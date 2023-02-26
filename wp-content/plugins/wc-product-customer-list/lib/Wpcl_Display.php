<?php

class Wpcl_Display
{
    public function __construct()
    {
    }
    
    public function init()
    {
        add_action( 'load-post.php', [ $this, 'add_post_meta_box' ] );
    }
    
    public function add_post_meta_box()
    {
        add_meta_box(
            'wc-product-customer-list-meta-box',
            esc_html__( 'Customers who bought this product', 'wc-product-customer-list' ),
            [ $this, 'render_meta_box' ],
            'product',
            'normal',
            'default'
        );
    }
    
    public function render_meta_box()
    {
        global  $post ;
        Wpcl_Assets::enqueue_scripts();
        Wpcl_Assets::enqueue_table_css();
        // Get product ID
        
        if ( !function_exists( 'yith_wcp_premium_init' ) ) {
            $post_id = $post->ID;
        } else {
            // Fix for YITH Composite Products Premium Bug
            $post_id = intval( $_GET['post'] );
        }
        
        $params = Wpcl_Options::process_options( 'options', (array) $post_id );
        $columns = Wpcl_Options::get_table_columns( $params, 'options' );
        $order_statuses = $params['wpcl_order_status_select'];
        $sortable = false;
        $order_items = Wpcl_Data_Compilation::gather_item_sales( (array) $post_id, $params, $order_statuses );
        // No use in repeating the product's name when we're ON the product's editing page
        $params['wpcl_table_title'] = '';
        
        if ( count( $order_items ) > 0 ) {
            echo  self::display_ajax_table(
                $order_items,
                $columns,
                $params,
                'options'
            ) ;
        } else {
            $settings_url = admin_url( 'admin.php?page=wc-settings&tab=products&section=wpcl' );
            echo  sprintf( wp_kses( __( 'This product currently has no customers. By default, the plugin only displays orders with "complete" and "processing" statuses. You can change this in the <a href="%s" target="_blank">plugin settings</a>.', 'wc-product-customer-list' ), array(
                'a' => array(
                'href'   => array(),
                'target' => '_blank',
            ),
            ) ), esc_url( $settings_url ) ) ;
        }
    
    }
    
    public static function display_ajax_table(
        $order_items,
        $columns,
        $params,
        $source = 'shortcode'
    )
    {
        $html_out = '';
        $reorder_class = ( empty($params['wpcl_col_reorder']) ? ' no-col-reorder ' : ' can-col-reorder ' );
        // we're going to need our scripts and css
        Wpcl_Assets::enqueue_table_css();
        Wpcl_Assets::enqueue_scripts();
        do_action( 'wpcl_shortcode_before_table' );
        $html_out .= self::display_table_title( $params );
        $rand = rand( 0, 10000 );
        $html_out .= '<script>WPCL_ORDERS_' . $rand . ' = ' . json_encode( $order_items ) . ';</script>';
        $html_out .= '<script>WPCL_ORDERS_' . $rand . '_columns = ' . json_encode( $columns ) . ';</script>';
        $html_out .= '<script>WPCL_ORDERS_' . $rand . '_options = ' . json_encode( $params ) . ';</script>';
        $html_out .= '<div class="wpcl wpcl-list-table-container ' . $reorder_class . '">';
        //do_action( 'wpcl_shortcode_before_table', $post_id, $columns, $atts );
        $html_out .= '<table class="wpcl-list-table" style="width:100%" data-orders="WPCL_ORDERS_' . $rand . '"
			       data-call-source="' . esc_attr( sanitize_title( $source ) ) . '"
			       data-ajax-mode="true"></table>';
        $html_out .= '<div class="wpcl-extra-action wpcl-total-quantity">';
        
        if ( $params['wpcl_order_qty_total'] ) {
            $html_out .= '<p class="total">';
            $html_out .= '<strong>' . __( 'Total quantity sold', 'wc-product-customer-list' ) . ' : </strong> <span class="product-count"></span>';
            $html_out .= '</p>';
        }
        
        $html_out .= '</div>';
        
        if ( !empty($params['wpcl_email_all']) ) {
            $html_out .= '<div class="wpcl-btn-mail-to-all-group">
					<a class="button wpcl-btn-mail-to-all"
					   href="mailto:?bcc=">' . __( 'Email all customers', 'wc-product-customer-list' ) . '</a>';
            $html_out .= '</div>';
            $html_out .= '<a href="#" class="button wpcl-btn-email-selected" id="email-selected"
				   disabled>' . __( 'Email selected customers', 'wc-product-customer-list' ) . '</a>';
            do_action( 'wpcl_after_email_button' );
            //, $order_items['email_list'] );
        }
        
        $html_out .= '</div>';
        return $html_out;
    }
    
    public static function display_static_table(
        $order_items,
        $columns,
        $params,
        $sortable,
        $source = 'shortcode'
    )
    {
        $html_out = '';
        global  $post ;
        $current_page_id = $post->ID;
        
        if ( $sortable ) {
            // we're going to need our scripts and css
            $table_extra_class = ' table-dynamic ';
        } else {
            $table_extra_class = ' table-static ';
        }
        
        Wpcl_Assets::enqueue_scripts();
        Wpcl_Assets::enqueue_table_css();
        $reorder_class = ( empty($params['wpcl_col_reorder']) ? ' no-col-reorder ' : ' can-col-reorder ' );
        // The users can opt to hide the titles row
        $show_titles_row = $params['wpcl_show_titles_row'];
        $show_titles_row_style = ( $show_titles_row ? '' : ' style="display: none" ' );
        $rand = rand( 0, 10000 );
        $html_out .= '<script>WPCL_ORDERS_' . $rand . ' = ' . json_encode( $order_items ) . ';</script>';
        $html_out .= '<script>WPCL_ORDERS_' . $rand . '_columns = ' . json_encode( $columns ) . ';</script>';
        $html_out .= '<script>WPCL_ORDERS_' . $rand . '_options = ' . json_encode( $params ) . ';</script>';
        $html_out .= '<div class="wpcl wpcl-list-table-container ' . $table_extra_class . $reorder_class . '">';
        $html_out .= self::display_table_title( $params );
        $timing = Wpcl_Timing::getInstance();
        $timing->add_timing( 'Before getting the item info' );
        $all_order_information = Wpcl_Data_Compilation::get_order_item_information(
            'shortcode',
            $order_items,
            $params,
            $columns
        );
        $html_out .= '<table class="wpcl-list-table wpcl-shortcode" style="width:100%" 
				   data-orders="WPCL_ORDERS_' . $rand . '"
			       data-call-source="' . esc_attr( sanitize_title( $source ) ) . '"
			       data-use-datatables="true" data-sortable="' . esc_attr( $sortable ) . '" data-ajax-mode="false">';
        $html_out .= '<thead ' . $show_titles_row_style . '>';
        $html_out .= '<tr>';
        /*
         * Important not to take the $columns var we got in this method, but, rather, the ones returned by our data info
         */
        foreach ( $all_order_information['columns'] as $column_key => $column_pretty_name ) {
            $html_out .= '<th class="wpcl-table-heading wpcl-table-heading_' . esc_attr( sanitize_title( $column_key ) ) . '">';
            $html_out .= '	<strong>' . $column_pretty_name . '</strong>';
            $html_out .= '</th>';
        }
        $html_out .= '</tr>';
        $html_out .= '</thead>';
        $html_out .= '<tbody>';
        foreach ( $all_order_information['data'] as $data_row ) {
            $html_out .= '<tr>';
            foreach ( $all_order_information['columns'] as $column_key => $column_pretty ) {
                // a few columns of data shouldn't actually be output
                if ( in_array( $column_key, [ 'wpcl_billing_email_raw' ] ) ) {
                    continue;
                }
                
                if ( isset( $data_row[$column_key] ) ) {
                    $html_out .= '<td class="wpcl-table-cell wpcl-table-cell_' . esc_attr( sanitize_title( $column_key ) ) . '">';
                    $html_out .= $data_row[$column_key];
                    $html_out .= '</td>';
                } else {
                    // we don't want to mess up the cells count even if we don't have the needed data
                    $html_out .= '<td></td>';
                }
            
            }
            do_action( 'wpcl_shortcode_add_row_cell', $data_row );
            $html_out .= '</tr>';
        }
        $html_out .= '</tbody>';
        $html_out .= '</table>';
        $html_out .= '<div class="wpcl-extra-action wpcl-total-quantity">';
        
        if ( $params['wpcl_order_qty_total'] ) {
            $html_out .= '<p class="total">';
            $html_out .= '<strong>' . __( 'Total quantity sold', 'wc-product-customer-list' ) . ' : </strong> <span class="product-count">' . $all_order_information['product_count'] . '</span>';
            $html_out .= '</p>';
        }
        
        $html_out .= '</div>';
        
        if ( !empty($params['wpcl_email_all']) ) {
            $html_out .= '<div class="wpcl-btn-mail-to-all-group">
					<a class="button wpcl-btn-mail-to-all"
					   href="mailto:?bcc=">' . __( 'Email all customers', 'wc-product-customer-list' ) . '</a>';
            $html_out .= '</div>';
            $html_out .= '<a href="#" class="button wpcl-btn-email-selected" id="email-selected"
				   disabled>' . __( 'Email selected customers', 'wc-product-customer-list' ) . '</a>';
            do_action( 'wpcl_after_email_button' );
            //, $order_items['email_list'] );
        }
        
        $html_out .= '</div>';
        //		if ( defined( 'WPCL_DEBUG' ) && WPCL_DEBUG == true ) {
        //			$html_out .= '<pre>';
        //			$html_out .= print_r( $all_order_information, true );
        //			$html_out .= '</pre>';
        //		}
        return $html_out;
    }
    
    public static function display_table_title( $params )
    {
        $table_title = $params['wpcl_table_title'];
        if ( !empty($table_title) ) {
            return '<h3>' . $table_title . '</h3>';
        }
        return '';
    }

}