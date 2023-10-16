<?php

use  Automattic\WooCommerce\Utilities\OrderUtil ;
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
            $settings_wpcl[] = [
                'type' => 'sectionend',
                'id'   => 'wpcl_upgrade',
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
        
        if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
            $meta_keys = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->prefix}wc_orders_meta" );
            $custom_fields = [];
        } else {
            $query = "\n\t\t        SELECT DISTINCT({$wpdb->postmeta}.meta_key)\n\t\t        FROM {$wpdb->posts}\n\t\t        LEFT JOIN {$wpdb->postmeta}\n\t\t        ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id\n\t\t        WHERE {$wpdb->posts}.post_type = '%s'\n\t\t        AND {$wpdb->postmeta}.meta_key != ''\n\t\t        ORDER BY {$wpdb->postmeta}.meta_key ASC;\n\t\t    ";
            $meta_keys = $wpdb->get_col( $wpdb->prepare( $query, $post_type ) );
            $custom_fields = [];
        }
        
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