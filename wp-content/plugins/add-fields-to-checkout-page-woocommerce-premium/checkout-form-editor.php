<?php

/**
 * Plugin Name: 	WooCommerce Checkout & Account Field Editor (Premium)
 * Plugin URI:  	https://www.themelocation.com/woocommerce-checkout-register-form-editor/
 * Description: 	Customize WooCommerce checkout and my account page (Add, Edit, Delete and re-arrange fields).
 * Version:     	3.2.0
 * Author:      	ThemeLocation
 * Author URI:  	https://themelocation.com
 *
 * Text Domain: 	wcfe
 * Domain Path: 	/languages
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 5.2.4
 */
if ( !defined( 'WPINC' ) ) {
    die;
}
if ( !function_exists( 'is_woocommerce_active' ) ) {
    function is_woocommerce_active()
    {
        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }
        
        if ( in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) || class_exists( 'WooCommerce' ) ) {
            return true;
        } else {
            return false;
        }
    
    }

}

if ( is_woocommerce_active() ) {
    define( 'THWCFE_VERSION', '3.2.0' );
    !defined( 'THWCFE_SOFTWARE_TITLE' ) && define( 'THWCFE_SOFTWARE_TITLE', 'WooCommerce Checkout Field Editor' );
    !defined( 'THWCFE_FILE_' ) && define( 'THWCFE_FILE_', __FILE__ );
    !defined( 'THWCFE_PATH' ) && define( 'THWCFE_PATH', plugin_dir_path( __FILE__ ) );
    !defined( 'THWCFE_URL' ) && define( 'THWCFE_URL', plugins_url( '/', __FILE__ ) );
    !defined( 'THWCFE_BASE_NAME' ) && define( 'THWCFE_BASE_NAME', plugin_basename( __FILE__ ) );
    /**
     * The code that runs during plugin activation.
     */
    function activate_thwcfe()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-thwcfe-activator.php';
        THWCFE_Activator::activate();
    }
    
    /**
     * The code that runs during plugin deactivation.
     */
    function deactivate_thwcfe()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-thwcfe-deactivator.php';
        THWCFE_Deactivator::deactivate();
    }
    
    register_activation_hook( __FILE__, 'activate_thwcfe' );
    register_deactivation_hook( __FILE__, 'deactivate_thwcfe' );
    function init_auto_updater_thwcfe()
    {
        global  $tl_fields ;
        
        if ( !isset( $tl_fields ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $tl_fields = fs_dynamic_init( array(
                'id'             => '1707',
                'slug'           => 'add-fields-to-checkout-page-woocommerce',
                'type'           => 'plugin',
                'public_key'     => 'pk_3abcc29391266d676ac7996328bce',
                'is_premium'     => true,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'    => 'themelocation_checkout_field_editor_pro',
                'support' => false,
                'parent'  => array(
                'slug' => 'woocommerce',
            ),
            ),
                'is_live'        => true,
            ) );
        }
        
        return $tl_fields;
    }
    
    init_auto_updater_thwcfe();
    // Signal that SDK was initiated.
    do_action( 'tl_fields_loaded' );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-thwcfe.php';
    /**
     * Begins execution of the plugin.
     */
    function run_theme_location_thwcfe()
    {
        $plugin = new THWCFE();
        $plugin->run();
    }
    
    run_theme_location_thwcfe();
    /**
     * Returns helper class instance.
     */
    function get_thwcfe_helper()
    {
        return new THWCFE_Functions();
    }

}
