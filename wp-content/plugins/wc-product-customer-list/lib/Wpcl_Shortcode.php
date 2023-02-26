<?php

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
class Wpcl_Shortcode
{
    public function __construct()
    {
    }
    
    public function init()
    {
        add_shortcode( 'customer_list', [ $this, 'display_shortcode' ] );
    }
    
    public function display_shortcode( $atts )
    {
        // Yoast was messing with these...1
        if ( !empty($_POST['action']) && 'wpseo_filter_shortcodes' == $_POST['action'] ) {
            return '';
        }
        $timing = Wpcl_Timing::getInstance();
        $timing->add_timing( 'Starting on the shortcode' );
        //		ob_start();
        
        if ( !empty($atts['product']) ) {
            // The product ID(s) in the shortcode attributes should always win
            $product_ids = explode( ',', $atts['product'] );
            // let's make sure we have a nice array of integers
            $product_ids = array_map( function ( $product_id ) {
                return intval( trim( $product_id ) );
            }, $product_ids );
        } else {
            
            if ( empty($atts['product']) && is_singular( 'product' ) ) {
                // if we're on a Product's single view page, let's grab the ID from the global $post var
                global  $post ;
                $product_ids = (array) $post->ID;
            } else {
                // we're not on a Product's page and the user hasn't supplied a product ID, not sure where to go
                return __( 'Please add the <code>product</code> shortcode attribute when not displaying on a Product\'s page', 'wc-product-customer-list' );
            }
        
        }
        
        $product_ids = array_unique( $product_ids );
        // check if it's an actual Product
        $timing->add_timing( 'Checking all the products' );
        foreach ( $product_ids as $product_id ) {
            // Let's check to make sure that it really IS an existing product
            
            if ( !Wpcl_Helpers::is_it_a_woo_product( $product_id ) ) {
                // ..and, if not, remove it from our array of product IDs
                if ( ($key = array_search( $product_id, $product_ids )) !== false ) {
                    unset( $product_ids[$key] );
                }
                continue;
            }
            
            $product = wc_get_product( $product_id );
            
            if ( empty($product) ) {
                printf( __( 'There does not seem to be a product with the ID %1$s', 'wc-product-customer-list' ), $atts['product'] );
                return false;
            }
        
        }
        $timing->add_timing( 'Parsing options and columns' );
        $params = Wpcl_Options::process_options( 'shortcode', $product_ids, $atts );
        $columns = Wpcl_Options::get_table_columns( $params, 'shortcode', $atts );
        // Check for order statuses
        
        if ( !empty($atts['order_status']) ) {
            $order_statuses = explode( ',', $atts['order_status'] );
            $order_statuses = array_map( function ( $order_statuse ) {
                return trim( $order_statuse );
            }, $order_statuses );
        } else {
            $order_statuses = [];
        }
        
        $sortable = false;
        $timing->add_timing( 'Starting to gather sales' );
        $order_items = Wpcl_Data_Compilation::gather_item_sales( $product_ids, $params, $order_statuses );
        $timing->add_timing( 'Done Gathering sales' );
        
        if ( count( $order_items ) > 0 ) {
            $output_html = Wpcl_Display::display_static_table(
                $order_items,
                $columns,
                $params,
                $sortable,
                'shortcode'
            );
        } else {
            $output_html == __( 'This product currently has no customers', 'wc-product-customer-list' );
        }
        
        //		$output_html = ob_get_contents();
        //		ob_end_clean();
        $timing->add_timing( 'All done. About to output.' );
        $timing->display_timings( true );
        return $output_html;
    }

}