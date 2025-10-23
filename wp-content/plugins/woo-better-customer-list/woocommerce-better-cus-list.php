<?php
/**
 * Plugin Name: Better Customer List for WooCommerce
 * Plugin URI:
 * Description: This plugin will no longer be maintained. This functionality can now be achieved by using the built-in WooCommerce Analytics.
 * Version: 1.2.3
 * Tested up to: 5.5.1
 * WC requires at least: 2.5.5
 * WC tested up to: 4.6.1
 * Author: Blaze Concepts
 * Author URI: https://www.blazeconcepts.co.uk/
 *
 * Text Domain: woo-better-customer-list
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'BLZ_BCL_ABSPATH', dirname( __FILE__ ) . '/' );

// Check WooCommerce is installed as thats pretty important with this plugin :)
if ( ! class_exists( 'BLZ_BCL_InstallCheck' ) ) {
  class BLZ_BCL_InstallCheck {
		static function install() {
			if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

				deactivate_plugins(__FILE__);

				$error_message = __('This plugin requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugin to be active!', 'woo-better-customer-list' );
				die($error_message);

			}
		}
	}
}

register_activation_hook( __FILE__, array('BLZ_BCL_InstallCheck', 'install') );
// END Check WooCommerce is installed


// Enable Languages
function blz_bcl_load_plugin_textdomain() {
	$domain = 'woo-better-customer-list';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	// wp-content/plugins/plugin-name/languages/plugin-name-de_DE.mo
	load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'blz_bcl_load_plugin_textdomain' );
// END Enable Languages


// Add BLZ_BCL settings link
function blz_bcl_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=wc-settings&tab=settings_tab_blz_bcl">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'blz_bcl_add_settings_link' );
// END Add BLZ_BCL settings link

// Include the main BLZBCL class
if ( ! class_exists( 'WooBLZBCL' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-better-cus-list.php';
}

// Add 'Customers' page to admin menu
add_action( 'admin_menu', 'blz_bcl_admin_menu' );

function blz_bcl_admin_menu() {
	add_menu_page(
        'WooCommerce Customers List',
        'Customers',
        'manage_woocommerce',
        'blz-bcl-list-customers',
        'blz_bcl_list_customers_admin',
        'dashicons-groups',
        58  );
}

function blz_bcl_list_customers_admin() {
    include_once( BLZ_BCL_ABSPATH.'/pages/list-customers-admin.php' );
}
// END Add 'Customers' page to admin menu

// BLZ BCL Settings Tab within WC Settings
class WC_Settings_Tab_BLZ_BCL {

    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_blz_bcl', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_tab_blz_bcl', __CLASS__ . '::update_settings' );
    }

    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_blz_bcl'] = __( 'Better Customer List', 'woo-better-customer-list' );
        return $settings_tabs;
    }

    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }

    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }

    public static function get_settings() {
        $settings = array(
            'general_section' => array(
                'name'     => __( 'General Settings', 'woo-better-customer-list' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'WC_Settings_Tab_BLZ_BCL_general_section'
            ),
            'cus_status' => array(
                'name' => __( 'Customer Activity Period (Days)', 'woo-better-customer-list' ),
                'type' => 'number',
                'desc' => __( 'days. This is the period for which the customer is assumed Active and becomes Inactive thereafter. Defaults to 31 days if left blank.', 'woo-better-customer-list' ),
                'id'   => 'WC_Settings_Tab_BLZ_BCL_cus_status'
            ),
            'general_end' => array(
                 'type' => 'sectionend',
                 'id' => 'WC_Settings_Tab_BLZ_BCL_general_end'
            )
        );
        return apply_filters( 'WC_Settings_Tab_BLZ_BCL_settings', $settings );
    }
}
WC_Settings_Tab_BLZ_BCL::init();
// END BLZ_BCL Settings Tab within WC Settings

// BLZ_BCL ajax calculations
add_action( 'admin_enqueue_scripts', 'BLZ_BCL_ajax_enqueue' );
function BLZ_BCL_ajax_enqueue($hook) {
	wp_enqueue_script( 'blz-bcl-ajax-script', plugins_url( '/js/ajax-load.js', __FILE__ ), array('jquery') );

	wp_localize_script( 'blz-bcl-ajax-script', 'blz_bcl_ajax_object',
    array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'wp_ajax_blz_bcl_caluot', 'blz_bcl_caluot' );
function blz_bcl_caluot() {
	global $wpdb;
		if (isset($_POST['blzbcluserid']) && !empty($_POST['blzbcluserid']) && is_numeric($_POST['blzbcluserid'])) {
			$userid = $_POST['blzbcluserid'];
			echo esc_html( __( wc_get_customer_order_count($userid), 'woo-better-customer-list' ) );
			wp_die();
		} else {
			echo esc_html( __( 'Error: No User ID', 'woo-better-customer-list' ) );
			wp_die();
		}
}

add_action( 'wp_ajax_blz_bcl_caluos', 'blz_bcl_caluos' );
function blz_bcl_caluos() {
	global $wpdb;
	if (isset($_POST['blzbcluserid']) && !empty($_POST['blzbcluserid']) && is_numeric($_POST['blzbcluserid'])) {
		$userid = $_POST['blzbcluserid'];
		echo esc_html( __( get_woocommerce_currency_symbol().''.wc_get_customer_total_spent($userid), 'woo-better-customer-list' ) );
		wp_die();
	} else {
		echo esc_html( __( 'Error: No User ID', 'woo-better-customer-list' ) );
		wp_die();
	}
}
// END BCL ajax calculations
