<?php
/*
Plugin Name: URL Coupons for WooCommerce
Plugin URI: https://wpfactory.com/item/url-coupons-woocommerce/
Description: Let your customers apply standard WooCommerce discount coupons via URL.
Version: 1.6.7
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: url-coupons-for-woocommerce-by-algoritmika
Domain Path: /langs
WC tested up to: 7.4
*/

defined( 'ABSPATH' ) || exit;

if ( 'url-coupons-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	$plugin = 'url-coupons-woocommerce-pro/url-coupons-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		return;
	}
}

defined( 'ALG_WC_URL_COUPONS_VERSION' ) || define( 'ALG_WC_URL_COUPONS_VERSION', '1.6.7' );

defined( 'ALG_WC_URL_COUPONS_FILE' ) || define( 'ALG_WC_URL_COUPONS_FILE', __FILE__ );

require_once( 'includes/class-alg-wc-url-coupons.php' );

if ( ! function_exists( 'alg_wc_url_coupons' ) ) {
	/**
	 * Returns the main instance of Alg_WC_URL_Coupons to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_wc_url_coupons() {
		return Alg_WC_URL_Coupons::instance();
	}
}

add_action( 'plugins_loaded', 'alg_wc_url_coupons' );
