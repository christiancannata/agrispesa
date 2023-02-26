<?php

class Wpcl_Settings
{
    public function __construct()
    {
    }
    
    public function init()
    {
        add_filter( 'woocommerce_get_sections_products', [ $this, 'add_woo_section' ] );
        add_filter(
            'woocommerce_get_settings_products',
            [ $this, 'all_settings' ],
            10,
            2
        );
        add_action( 'admin_menu', [ $this, 'maybe_add_settings_item_under_woo_menu' ] );
    }
    
    function add_woo_section( $sections )
    {
        $sections['wpcl'] = __( 'Product Customer List', 'wc-product-customer-list' );
        return $sections;
    }
    
    function maybe_add_settings_item_under_woo_menu()
    {
        $add_link = get_option( 'wpcl_add_admin_shortcut', true ) == 'yes';
        
        if ( $add_link ) {
            global  $submenu ;
            $url = admin_url( 'admin.php?page=wc-settings&tab=products&section=wpcl' );
            $submenu['woocommerce'][] = [ __( 'Product Customer List', 'wc-product-customer-list' ), 'manage_options', $url ];
        }
    
    }
    
    public function all_settings( $settings, $current_section )
    {
        // Enqueue admin stylesheet
        wp_register_style(
            'wpcl-settings-css',
            WPCL_PLUGIN_URL . '/assets/settings.css',
            false,
            '2.7.4'
        );
        wp_enqueue_style( 'wpcl-settings-css' );
        // Get all available statuses
        $statuses = [];
        foreach ( get_post_stati( [
            'show_in_admin_status_list' => true,
        ], 'objects' ) as $status ) {
            if ( !in_array( $status->name, [
                'publish',
                'draft',
                'pending',
                'trash',
                'future',
                'private',
                'auto-draft'
            ] ) ) {
                $statuses[$status->name] = $status->label;
            }
        }
        
        if ( $current_section == 'wpcl' ) {
            //			Wpcl_Options::test_options();;
            $settings_wpcl = [];
            $settings_wpcl[] = [
                'name' => __( 'Product Customer List for WooCommerce', 'wc-product-customer-list' ),
                'type' => 'title',
                'desc' => __( 'The following options are used to configure Product Customer List for WooCommerce', 'wc-product-customer-list' ),
                'id'   => 'wpcl-settings',
            ];
            $all_fields = Wpcl_Options::get_table_options();
            // we want to order them by  'settings_order'
            $normal_fields_ordered = [];
            foreach ( $all_fields as $setting_code => $setting ) {
                if ( empty($setting['is_setting']) || empty($setting['setting']) || !empty($setting['is_premium']) ) {
                    continue;
                }
                $current_order = $setting['setting']['settings_order'];
                // in case they overlap
                if ( !empty($normal_fields_ordered[$current_order]) ) {
                    while ( !empty($normal_fields_ordered[$current_order]) ) {
                        $current_order++;
                    }
                }
                $normal_fields_ordered[$current_order] = $setting['setting'];
            }
            foreach ( $normal_fields_ordered as $normal_field ) {
                $settings_wpcl[] = $normal_field;
            }
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Add shortcut under WooCommerce Menu', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_add_admin_shortcut',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'This link would let you jump directly to the settings tab', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'     => __( 'Order status', 'wc-product-customer-list' ),
            //				'desc'     => __( 'Select one or multiple order statuses for which you will display the customers.', 'wc-product-customer-list' ),
            //				'id'       => 'wpcl_order_status_select',
            //				'css'      => 'min-width:300px;',
            //				'default'  => [ 'wc-completed' ],
            //				'type'     => 'multiselect',
            //				'options'  => $statuses,
            //				'desc_tip' => true,
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Partial refunds', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_order_partial_refunds',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Hide partially refunded orders', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Order number column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_order_number',
            //				'default' => 'yes',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable order number column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Order date column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_order_date',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable order date column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Order status column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_order_status',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable order status column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Order quantity column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_order_qty',
            //				'default' => 'yes',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable order quantity column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Order total column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_order_total',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable order total column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Payment method column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_order_payment',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable payment method column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Shipping method column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_order_shipping',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable shipping method column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Coupons used column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_order_coupon',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable coupons used column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Variations column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_variations',
            //				'default' => 'yes',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable variations column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Customer message column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_customer_message',
            //				'default' => 'yes',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable customer message column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Customer ID', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_customer_ID',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable customer ID column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Customer username', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_customer_username',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable customer username column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Customer display name', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_customer_display_name',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable customer display name column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Billing first name column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_billing_first_name',
            //				'default' => 'yes',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable billing first name column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Billing last name column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_billing_last_name',
            //				'default' => 'yes',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable billing last name column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Billing company column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_billing_company',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable billing company column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Billing e-mail column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_billing_email',
            //				'default' => 'yes',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable billing e-mail column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Billing phone column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_billing_phone',
            //				'default' => 'yes',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable billing phone column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Billing address 1 column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_billing_address_1',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable billing address 1 column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Billing address 2 column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_billing_address_2',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable billing address 2 column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Billing city column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_billing_city',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable billing city column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Billing state column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_billing_state',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable billing state column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Billing Postal Code / Zip column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_billing_postalcode',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable billing postal code / Zip column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Billing country column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_billing_country',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable billing country column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Shipping first name column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_shipping_first_name',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable shipping first name column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Shipping last name column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_shipping_last_name',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable shipping last name column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Shipping company column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_shipping_company',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable shipping company column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Shipping address 1 column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_shipping_address_1',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable shipping address 1 column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Shipping address 2 column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_shipping_address_2',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable shipping address 2 column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Shipping city column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_shipping_city',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable shipping city column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Shipping state column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_shipping_state',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable shipping state column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Shipping Postal Code / Zip column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_shipping_postalcode',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable shipping postal code / Zip column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Shipping country column', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_shipping_country',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable shipping country column', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'    => __( 'Add SKU to PDF title', 'wc-product-customer-list' ),
            //				'id'      => 'wpcl_export_pdf_sku',
            //				'default' => 'no',
            //				'type'    => 'checkbox',
            //				'css'     => 'min-width:300px;',
            //				'desc'    => __( 'Enable SKU in PDF title', 'wc-product-customer-list' ),
            //			];
            //			$settings_wpcl[] = [
            //				'name'     => __( 'PDF orientation', 'wc-product-customer-list' ),
            //				'id'       => 'wpcl_export_pdf_orientation',
            //				'css'      => 'min-width:300px;',
            //				'default'  => [ 'portrait' ],
            //				'type'     => 'select',
            //				'options'  => [
            //					'portrait'  => __( 'Portrait', 'wc-product-customer-list' ),
            //					'landscape' => __( 'Landscape', 'wc-product-customer-list' ),
            //				],
            //				'desc_tip' => false,
            //			];
            //			$settings_wpcl[] = [
            //				'name'     => __( 'PDF page size', 'wc-product-customer-list' ),
            //				'id'       => 'wpcl_export_pdf_pagesize',
            //				'css'      => 'min-width:300px;',
            //				'default'  => [ 'letter' ],
            //				'type'     => 'select',
            //				'options'  => [
            //					'LETTER' => __( 'US Letter', 'wc-product-customer-list' ),
            //					'LEGAL'  => __( 'US Legal', 'wc-product-customer-list' ),
            //					'A3'     => __( 'A3', 'wc-product-customer-list' ),
            //					'A4'     => __( 'A4', 'wc-product-customer-list' ),
            //					'A5'     => __( 'A5', 'wc-product-customer-list' ),
            //				],
            //				'desc_tip' => false,
            //			];
            // SETTINGS ORDER
            $settings_wpcl[] = [
                'type' => 'sectionend',
                'id'   => 'wpcl-settings',
            ];
            $settings_wpcl[] = [
                'name' => __( 'Looking for other options?', 'wc-product-customer-list' ),
                'id'   => 'wpcl_upgrade',
                'type' => 'title',
                'desc' => sprintf( __( 'For more options, please upgrade to the <a href="$1%s" target="_blank">premium version</a>.', 'wc-product-customer-list' ), WPCL_PRO_URL ),
            ];
            return $settings_wpcl;
        } else {
            return $settings;
        }
    
    }
    
    // Get order custom fields
    public static function gather_order_meta_keys()
    {
        global  $wpdb ;
        $post_type = 'shop_order';
        $query = "\n\t        SELECT DISTINCT({$wpdb->postmeta}.meta_key)\n\t        FROM {$wpdb->posts}\n\t        LEFT JOIN {$wpdb->postmeta}\n\t        ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id\n\t        WHERE {$wpdb->posts}.post_type = '%s'\n\t        AND {$wpdb->postmeta}.meta_key != ''\n\t        ORDER BY {$wpdb->postmeta}.meta_key ASC;\n\t    ";
        $meta_keys = $wpdb->get_col( $wpdb->prepare( $query, $post_type ) );
        $custom_fields = [];
        foreach ( $meta_keys as $meta_key ) {
            $custom_fields[$meta_key] = $meta_key;
        }
        return $custom_fields;
    }
    
    // Get user meta keys
    public static function gather_user_meta()
    {
        global  $wpdb ;
        $select = "\n    \t\tSELECT distinct {$wpdb->usermeta}.meta_key\n    \t\tFROM {$wpdb->usermeta}\n\t\t\tORDER BY {$wpdb->usermeta}.meta_key ASC;\n    \t";
        $user_meta_keys = $wpdb->get_results( $select );
        $user_fields = [];
        foreach ( $user_meta_keys as $user_meta_key ) {
            $user_fields[$user_meta_key->meta_key] = $user_meta_key->meta_key;
        }
        return $user_fields;
    }
    
    // Get order product fields (Rightpress)
    public static function gather_rightpress_product_fields()
    {
        $rightpress_product_fields = [];
        // Query Arguments
        $args = [
            'post_type'      => [ 'wccf_product_field' ],
            'post_status'    => [ 'publish' ],
            'posts_per_page' => -1,
            'nopaging'       => true,
            'order'          => 'DESC',
            'orderby'        => 'none',
        ];
        // The Query
        $query = new WP_Query( $args );
        // The Loop
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $rightpress_label = get_post_meta( get_the_ID(), 'label', true );
                //$rightpres_label = $rightpres_label[0];
                $rightpress_key = get_post_meta( get_the_ID(), 'key', true );
                //$rightpres_key = $rightpres_key[0];
                $rightpress_product_fields[$rightpress_key] = $rightpress_label;
                //$rightpress_product_fields[] = get_the_title();
                //$rightpress_product_fields[] = get_post_custom($post->ID);
            }
        }
        /* Restore original Post Data */
        wp_reset_postdata();
        return $rightpress_product_fields;
    }
    
    // Get order checkout fields (Rightpress)
    public static function gather_rightpress_checkout_fields()
    {
        $rightpress_checkout_fields = [];
        // Query Arguments
        $args = [
            'post_type'      => [ 'wccf_checkout_field' ],
            'post_status'    => [ 'publish' ],
            'posts_per_page' => -1,
            'nopaging'       => true,
            'order'          => 'DESC',
            'orderby'        => 'none',
        ];
        // The Query
        $query = new WP_Query( $args );
        // The Loop
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $rightpress_checkout_label = get_post_meta( get_the_ID(), 'label', true );
                //$rightpres_label = $rightpres_label[0];
                $rightpress_checkout_key = get_post_meta( get_the_ID(), 'key', true );
                //$rightpres_key = $rightpres_key[0];
                $rightpress_checkout_fields[$rightpress_checkout_key] = $rightpress_checkout_label;
                //$rightpress_product_fields[] = get_the_title();
                //$rightpress_product_fields[] = get_post_custom($post->ID);
            }
        }
        /* Restore original Post Data */
        wp_reset_postdata();
        return $rightpress_checkout_fields;
    }
    
    // Get order checkout fields (Rightpress)
    public static function gather_rightpress_order_fields()
    {
        $rightpress_order_fields = [];
        // Query Arguments
        $args = [
            'post_type'      => [ 'wccf_order_field' ],
            'post_status'    => [ 'publish' ],
            'posts_per_page' => -1,
            'nopaging'       => true,
            'order'          => 'DESC',
            'orderby'        => 'none',
        ];
        // The Query
        $query = new WP_Query( $args );
        // The Loop
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $rightpress_order_label = get_post_meta( get_the_ID(), 'label', true );
                //$rightpres_label = $rightpres_label[0];
                $rightpress_order_key = get_post_meta( get_the_ID(), 'key', true );
                //$rightpres_key = $rightpres_key[0];
                $rightpress_order_fields[$rightpress_order_key] = $rightpress_order_label;
                //$rightpress_product_fields[] = get_the_title();
                //$rightpress_product_fields[] = get_post_custom($post->ID);
            }
        }
        /* Restore original Post Data */
        wp_reset_postdata();
        return $rightpress_order_fields;
    }

}