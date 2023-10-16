<?php

/**
 * @package WC_Product_Customer_List
 * @version 3.1.6
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
use  Automattic\WooCommerce\Utilities\OrderUtil ;
class Wpcl_Data_Compilation
{
    private static  $necessary_capability = 'edit_shop_orders' ;
    // We're taking care of everything in the init() method
    public function __construct()
    {
    }
    
    public function init()
    {
        // prepare wp_ajax
        add_action( 'wp_ajax_process_order_items', [ $this, 'process_order_items_ajax' ] );
    }
    
    public function process_order_items_ajax()
    {
        // check permissions
        
        if ( !is_user_logged_in() || !current_user_can( self::$necessary_capability ) ) {
            wp_send_json( [
                'success' => false,
                'message' => __( 'You do not have the permissions to access this information', 'wc-product-customer-list' ),
            ] );
            return;
        }
        
        // check nonce
        
        if ( !wp_verify_nonce( $_REQUEST['nonce'], 'wc-product-customer-list-pro' ) ) {
            wp_send_json( [
                'success' => false,
                'message' => __( 'You do not have the permissions to access this information', 'wc-product-customer-list' ),
            ] );
            return;
        }
        
        $orders = ( !empty($_POST['batch_orders']) ? $_POST['batch_orders'] : false );
        $need_columns = ( !empty($_POST['need_columns']) ? $_POST['need_columns'] : false );
        $call_source = ( !empty($_POST['call_source']) ? $_POST['call_source'] : false );
        if ( in_array( $call_source, [ 'shortcode', 'admin' ] ) == false ) {
            $call_source = 'admin';
        }
        
        if ( empty($orders) ) {
            wp_send_json( [
                'success' => false,
                'message' => __( 'Invalid order IDs', 'wc-product-customer-list' ),
            ] );
            return;
        }
        
        // Any of the WordPress data sanitization functions can be used here
        $custom_columns = ( !empty($_POST['custom_columns']) && $_POST['custom_columns'] != 'false' ? (array) $_POST['custom_columns'] : false );
        if ( $custom_columns ) {
            $custom_columns = Wpcl_Helpers::array_map_recursive( 'esc_attr', $custom_columns );
        }
        $custom_options = ( !empty($_POST['custom_options']) && $_POST['custom_options'] != 'false' ? (array) $_POST['custom_options'] : false );
        
        if ( $custom_options ) {
            $custom_options = Wpcl_Helpers::array_map_recursive( 'esc_attr', $custom_options );
            foreach ( $custom_options as $custom_option_key => $custom_option_value ) {
                // cleaning up binary values
                
                if ( in_array( $custom_option_value, [ 'true', true, 'yes' ], true ) ) {
                    $custom_options[$custom_option_key] = true;
                } else {
                    if ( in_array( $custom_option_value, [ 'false', false, 'no' ], true ) ) {
                        $custom_options[$custom_option_key] = false;
                    }
                }
            
            }
        }
        
        $item_data = self::get_order_item_information(
            'options',
            $orders,
            $custom_options,
            $custom_columns
        );
        // there was a problem with the data. for example: refunded order
        
        if ( $item_data['success'] === false ) {
            wp_send_json( [
                'success' => false,
                'message' => $item_data['reason'],
            ] );
            return;
        }
        
        $response = [
            'success'       => true,
            'order_rows'    => $item_data['data'],
            'product_count' => $item_data['product_count'],
            'email_list'    => $item_data['email_list'],
            'columns'       => $item_data['columns'],
        ];
        $timing = Wpcl_Timing::getInstance();
        $timing_data = $timing->display_timings( false );
        if ( !empty($timing_data) ) {
            $response['timing'] = $timing_data;
        }
        // To not send through data needlessly
        if ( !$need_columns ) {
            unset( $response['columns'] );
        }
        wp_send_json_success( $response );
    }
    
    public static function get_order_item_information(
        $source,
        $orders,
        $params,
        $columns = array()
    )
    {
        $timing = Wpcl_Timing::getInstance();
        
        if ( empty($columns) == true ) {
            $timing->add_timing( '[get_order_item_information] BEFORE compiling table columns.' );
            $columns = Wpcl_Options::get_table_columns( $params, 'options' );
            $timing->add_timing( '[get_order_item_information] AFTER compiling table columns.' );
        }
        
        $table_options = Wpcl_Options::get_table_options();
        $data = [
            'data'       => [],
            'email_list' => [],
        ];
        $productcount = [];
        $first_record = true;
        $timing->add_timing( '[get_order_item_information] Note: only tracking first order\'s timing.' );
        $prev_product_id = $previous_product = $previous_order_id = $previous_order = false;
        $order_counter = 0;
        foreach ( $orders as $order_info ) {
            $order_id = $order_info['order_id'];
            $item_id = $order_info['order_item_id'];
            $product_id = $order_info['product_id'];
            $timing->add_timing( '[get_order_item_information] Getting order #' . $order_id );
            // let's cache the WC_Order in case it's the same
            
            if ( $previous_order_id && $previous_order_id == $order_id ) {
                $order = $previous_order;
            } else {
                $order = wc_get_order( $order_id );
            }
            
            /*
            +			 * Let's make sure this is a valid order ID in the system.
            +			 * If not, move on to the next ID
            +			 */
            if ( !$order || is_wp_error( $order ) || !is_a( $order, 'WC_Order' ) ) {
                continue;
            }
            $timing->add_timing( '[get_order_item_information] Getting product #' . $product_id );
            
            if ( $prev_product_id && $prev_product_id == $product_id ) {
                $product = $previous_product;
            } else {
                $product = wc_get_product( $product_id );
            }
            
            $previous_order = $order;
            $previous_product = $product;
            $current_item = new WC_Order_Item_Product( $item_id );
            // The product ID
            $current_product_id = $current_item->get_product_id();
            if ( $product->get_type() !== 'variation' && $current_product_id != $product_id ) {
                continue;
            }
            $customer_id = $order->get_customer_id();
            $customer = get_userdata( $customer_id );
            $quantity = 0;
            //			$formatted_total = $order->get_formatted_order_total();
            $timing->add_timing( '[get_order_item_information] Calculating quantities for Order #' . $order_id );
            // Get quantity
            $refunded_qty = 0;
            $order_items = $order->get_items();
            foreach ( $order_items as $order_item_id => $order_item ) {
                if ( $order_item['product_id'] == $product_id ) {
                    $refunded_qty += $order->get_qty_refunded_for_item( $order_item_id );
                }
            }
            // Only one product per line if rows are split
            try {
                $quantity = wc_get_order_item_meta( $item_id, '_qty', true );
            } catch ( Exception $ex ) {
                $quantity = 0;
            }
            $quantity += $refunded_qty;
            $timing->add_timing( '[get_order_item_information] Quantity found = ' . $quantity );
            // Check for partially refunded orders
            if ( $quantity == 0 && !empty($params['wpcl_order_partial_refunds']) && $params['wpcl_order_partial_refunds'] == true ) {
                // Order has been partially refunded
                //				return array(
                //					'success' => false,
                //					'reason'  => 'refunded order',
                //				);
                continue;
            }
            $current_row = [];
            $current_row['billing_email'] = $order->get_billing_email();
            $current_row['billing_email_raw'] = sanitize_email( $current_row['billing_email'] );
            // setting aside to not be processed by the $columns loop later
            $current_row['order'] = $order;
            $current_row['order_id'] = $order_id;
            $current_row['product'] = $product;
            $current_row['product_id'] = $product_id;
            $current_row['item_id'] = $item_id;
            $current_row['wpcl_order_item_id'] = $item_id;
            $timing->add_timing( '[get_order_item_information] Starting to process individual columns for order #' . $order_id );
            foreach ( $columns as $column => $column_value ) {
                $timing->add_timing( '[get_order_item_information] Beginning ' . $column );
                /*
                 * Data formatters can be a class and a static method or a function
                 *
                 * Even if they don't use all the data, all methods/functions should exepct to receive these arguments:
                 * $source
                 * $params
                 * $order_id
                 * $order
                 * $item_id
                 * $product
                 * $product_id
                 * $customer_id
                 * $quantity
                 * $current_row
                 *
                 * Premium users can use the 'wcpl_options' filter hook to alter the options dictating the
                 *  data display, overloading the class::static_method it uses or even simply hooking it up
                 *  to a custom function
                 *
                 * By default all the data formatters use static methods in the Wpcl_Data_Formatters class.
                 *  Each one should also have a unique filter hook to adjust the display of the data
                 *
                 */
                
                if ( isset( $table_options[$column] ) && empty($table_options[$column]['data_formatter']) == false ) {
                    $data_formatter = explode( '::', $table_options[$column]['data_formatter'] );
                    // we can pass it a class' method
                    
                    if ( !empty($data_formatter[1]) ) {
                        $class = $data_formatter[0];
                        $method = $data_formatter[1];
                        
                        if ( method_exists( $class, $method ) ) {
                            $adjusted_info = $class::$method(
                                $column,
                                $source,
                                $params,
                                $columns,
                                $order_id,
                                $order,
                                $item_id,
                                $product,
                                $product_id,
                                $customer_id,
                                $customer,
                                $quantity,
                                $current_row
                            );
                            $current_row = $adjusted_info['current_row'];
                            $columns = $adjusted_info['columns'];
                        }
                    
                    } else {
                        $function = $data_formatter[0];
                        
                        if ( function_exists( $function ) ) {
                            $adjusted_info = ${$function}(
                                $column,
                                $source,
                                $params,
                                $columns,
                                $order_id,
                                $order,
                                $item_id,
                                $product,
                                $product_id,
                                $customer_id,
                                $customer,
                                $quantity,
                                $current_row
                            );
                            $current_row = $adjusted_info['current_row'];
                            $columns = $adjusted_info['columns'];
                        }
                    
                    }
                
                } else {
                    // if it wasn't overriden by a more specific method in Wpcl_Data_Formatters,
                    // we need a few exceptions for some of the special fields
                    // custom fields
                    
                    if ( strpos( $column, 'wpcl_custom_field__' ) > -1 ) {
                        $adjusted_info = Wpcl_Data_Formatters::data_custom_fields(
                            $column_value,
                            $source,
                            $params,
                            $columns,
                            $order_id,
                            $order,
                            $item_id,
                            $product,
                            $product_id,
                            $customer_id,
                            $customer,
                            $quantity,
                            $current_row
                        );
                    } else {
                        
                        if ( strpos( $column, 'wpcl_user_meta__' ) > -1 ) {
                            // user meta
                            $adjusted_info = Wpcl_Data_Formatters::data_user_meta(
                                $column,
                                $source,
                                $params,
                                $columns,
                                $order_id,
                                $order,
                                $item_id,
                                $product,
                                $product_id,
                                $customer_id,
                                $customer,
                                $quantity,
                                $current_row
                            );
                        } else {
                            
                            if ( strpos( $column, 'rightpress_' ) > -1 ) {
                                // rightpress info is a bit different and has to be processed differently
                                continue;
                            } else {
                                // default data processor
                                $adjusted_info = Wpcl_Data_Formatters::default_formatter(
                                    $column,
                                    $source,
                                    $params,
                                    $columns,
                                    $order_id,
                                    $order,
                                    $item_id,
                                    $product,
                                    $product_id,
                                    $customer_id,
                                    $customer,
                                    $quantity,
                                    $current_row
                                );
                            }
                        
                        }
                    
                    }
                    
                    $current_row = $adjusted_info['current_row'];
                    $columns = $adjusted_info['columns'];
                }
                
                $timing->add_timing( '[get_order_item_information] Done with ' . $column );
            }
            $productcount[] = $quantity;
            if ( $order->get_billing_email() ) {
                $data['email_list'][] = $order->get_billing_email();
            }
            // A little $columns / $options kludge because we need to save slightly different keys for our columns
            // So we're saving the options we need through the columns gathering process
            // @todo : replace with 'options_overload' method of some sort
            
            if ( !empty($columns['wpcl_rightpress_custom_fieldkeys']) ) {
                $params['wpcl_rightpress_custom_fieldkeys'] = $columns['wpcl_rightpress_custom_fieldkeys'];
                unset( $columns['wpcl_rightpress_custom_fieldkeys'] );
            }
            
            
            if ( !empty($columns['wpcl_rightpress_checkout_fieldkeys']) ) {
                $params['wpcl_rightpress_checkout_fieldkeys'] = $columns['wpcl_rightpress_checkout_fieldkeys'];
                unset( $columns['wpcl_rightpress_checkout_fieldkeys'] );
            }
            
            
            if ( !empty($columns['wpcl_rightpress_order_fieldkeys']) ) {
                $params['wpcl_rightpress_order_fieldkeys'] = $columns['wpcl_rightpress_order_fieldkeys'];
                unset( $columns['wpcl_rightpress_order_fieldkeys'] );
            }
            
            // NOT rightpress
            // maybe split the rows
            
            if ( !empty($params['wpcl_split_rows']) && $quantity > 1 ) {
                // set the quantity to 1
                if ( isset( $current_row['wpcl_order_qty'] ) ) {
                    $current_row['wpcl_order_qty'] = 1;
                }
                // and add it as many times as the quantity so they're each on one row
                for ( $i = 0 ;  $i < $quantity ;  $i++ ) {
                    $data['data'][] = $current_row;
                }
            } else {
                $data['data'][] = $current_row;
            }
            
            $timing->add_timing( '[get_order_item_information] Done with Order #' . $order_id );
            $first_record = false;
        }
        if ( !empty($data['email_list']) ) {
            // 2019-05-02 Added array_values because array_unique preserves keys and made JavaScript it was an object instead of an array
            $data['email_list'] = array_values( array_unique( $data['email_list'] ) );
        }
        // add an index columns
        
        if ( isset( $columns['wpcl_index'] ) ) {
            $index_counter = 1;
            foreach ( $data['data'] as $data_row_key => $data_row ) {
                $data['data'][$data_row_key]['wpcl_index'] = $index_counter;
                $index_counter++;
            }
        }
        
        // Count all the products
        $final_product_count = array_sum( $productcount );
        // wpcl_order_qty_total_column
        if ( isset( $columns['wpcl_order_qty_total_column'] ) ) {
            foreach ( $data['data'] as $data_row_key => $data_row ) {
                $data['data'][$data_row_key]['wpcl_order_qty_total_column'] = $final_product_count;
            }
        }
        return [
            'success'       => true,
            'data'          => $data['data'],
            'columns'       => $columns,
            'product_count' => $final_product_count,
            'email_list'    => $data['email_list'],
        ];
    }
    
    /**
     *
     * Will get us all the Orders containing specific product IDs
     *
     * @param int[]    $product_ids
     * @param string[] $product_ids
     *
     * @return array|object|null
     */
    public static function gather_item_sales( $product_ids, $params, $statuses = array() )
    {
        global  $sitepress, $wpdb ;
        $timing = Wpcl_Timing::getInstance();
        $timing->add_timing( '[gather_item_sales] Getting all orders with this product' );
        $all_product_ids = $product_ids;
        // get the adjusted post IDs if WPML is active
        foreach ( $product_ids as $product_id ) {
            
            if ( isset( $sitepress ) && method_exists( $sitepress, 'get_element_trid' ) && method_exists( $sitepress, 'get_element_translations' ) ) {
                $trid = $sitepress->get_element_trid( $product_id, 'post_product' );
                $translations = $sitepress->get_element_translations( $trid, 'product' );
                foreach ( $translations as $lang => $translation ) {
                    $all_product_ids[] = $translation->element_id;
                }
            }
        
        }
        // Prepare the desired statuses
        
        if ( !empty($statuses) ) {
            $order_statuses = $statuses;
        } else {
            $order_statuses = get_option( 'wpcl_order_status_select', [ 'wc-completed', 'wc-processing' ] );
        }
        
        // Query the orders related to the product
        $order_statuses = array_map( 'esc_sql', (array) $order_statuses );
        $order_statuses_string = "'" . implode( "', '", $order_statuses ) . "'";
        $post_ids = array_map( 'esc_sql', (array) $all_product_ids );
        $post_ids_string = "'" . implode( "', '", $post_ids ) . "'";
        
        if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
            $item_sales = $wpdb->get_results( $wpdb->prepare( "SELECT p.order_id, p.order_item_id, p.product_id \n\t\t        FROM {$wpdb->prefix}wc_order_product_lookup p\n\t\t        INNER JOIN {$wpdb->prefix}wc_orders o ON p.order_id = o.id\n\t\t        WHERE (p.product_id IN ({$post_ids_string}) OR p.variation_id IN ({$post_ids_string})) \n\t\t        AND o.type != 'shop_order_refund' \n\t\t        AND o.status IN ({$order_statuses_string})\n\t\t        ORDER BY p.order_id DESC\n\t\t        LIMIT %d", $params['wpcl_limit'] ), ARRAY_A );
        } else {
            $item_sales = $wpdb->get_results( $wpdb->prepare( "SELECT o.ID as order_id, oi.order_item_id,  oim.meta_value AS product_id FROM\n\t\t\t\t{$wpdb->prefix}woocommerce_order_itemmeta oim\n\t\t\t\tINNER JOIN {$wpdb->prefix}woocommerce_order_items oi\n\t\t\t\tON oim.order_item_id = oi.order_item_id\n\t\t\t\tINNER JOIN {$wpdb->posts} o\n\t\t\t\tON oi.order_id = o.ID\n\t\t\t\tWHERE (oim.meta_key = '_product_id' or oim.meta_key = '_variation_id')\n\t\t\t\tAND oim.meta_value IN ( {$post_ids_string} )\n\t\t\t\tAND o.post_status IN ( {$order_statuses_string} )\n\t\t\t\tAND o.post_type NOT IN ('shop_order_refund')\n\t\t\t\tORDER BY o.ID DESC\n\t\t\t\tLIMIT %d", $params['wpcl_limit'] ), ARRAY_A );
        }
        
        $timing->add_timing( '[gather_item_sales] Done getting all orders for this product' );
        return $item_sales;
    }

}