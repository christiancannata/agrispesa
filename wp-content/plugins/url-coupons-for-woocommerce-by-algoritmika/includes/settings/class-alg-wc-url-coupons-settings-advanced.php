<?php
/**
 * URL Coupons for WooCommerce - Advanced Section Settings.
 *
 * @version 1.6.4
 * @since   1.6.0
 *
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_URL_Coupons_Settings_Advanced' ) ) :

	class Alg_WC_URL_Coupons_Settings_Advanced extends Alg_WC_URL_Coupons_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.6.0
		 * @since   1.6.0
		 */
		function __construct() {
			$this->id   = 'advanced';
			$this->desc = __( 'Advanced', 'url-coupons-for-woocommerce-by-algoritmika' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 1.6.4
		 * @since   1.6.0
		 *
		 * @todo    [next] (feature) add "Action" option (defaults to `wp_loaded`)
		 * @todo    [next] (desc) `alg_wc_url_coupons_payment_request_product_data`: better naming and/or description
		 * @todo    [next] (desc) Force coupon redirect: better naming and/or description
		 */
		function get_settings() {
			$general      = array(
				array(
					'title' => __( 'Advanced', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'  => 'title',
					'id'    => 'alg_wc_url_coupons_advanced_options',
				),
				array(
					'title'    => __( 'Save on empty cart', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => __( 'Enable', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Save coupons when cart is emptied. Coupons will be reapplied when some product is added to the cart.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'       => 'alg_wc_url_coupons_save_empty_cart',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Remove "add to cart" key', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => __( 'Enable', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => sprintf( __( 'Will remove %s key on "%s" option.', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'<code>add-to-cart</code>',
						__( 'Redirect URL', 'url-coupons-for-woocommerce-by-algoritmika' ) . ' > ' . __( 'No redirect', 'url-coupons-for-woocommerce-by-algoritmika' ) ),
					'id'       => 'alg_wc_url_coupons_remove_add_to_cart_key',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Force coupon redirect', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => __( 'Enable', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => sprintf( __( 'Force coupon redirect after %s action.', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'<code>add-to-cart</code>' ),
					'id'       => 'alg_wc_url_coupons_add_to_cart_action_force_coupon_redirect',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'WP Rocket', 'url-coupons-for-woocommerce-by-algoritmika' ) . ': ' . __( 'Disable empty cart caching', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => __( 'Disable', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Check this if you have "WP Rocket" plugin installed, and having issues with cart being empty after you apply URL coupon and add a product.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'       => 'alg_wc_url_coupons_wp_rocket_disable_cache_wc_empty_cart',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'         => __( 'Payment request buttons: Apply coupons on single product pages', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'          => __( 'WooCommerce Stripe Gateway', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'            => 'alg_wc_url_coupons_payment_request_product_data[wc_stripe]',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),
				array(
					'desc'          => __( 'WooCommerce Payments', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'            => 'alg_wc_url_coupons_payment_request_product_data[wcpay]',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'end',
				),
				array(
					'title'    => __( 'Javascript reload', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => __( 'Reloads the page via javascript when the coupon is detected from the URL', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Enable if the coupon is not being applied without products in cart.', 'url-coupons-for-woocommerce-by-algoritmika' ) . '<br />' .
					              sprintf( __( 'Make sure that %s is set as %s so the %s cookie can be created.', 'url-coupons-for-woocommerce-by-algoritmika' ), '<strong>' . __( 'Data storage type', 'url-coupons-for-woocommerce-by-algoritmika' ) . '</strong>', '<strong>' . __( 'Cookie', 'url-coupons-for-woocommerce-by-algoritmika' ) . '</strong>', '<code>alg_wc_url_coupons</code>' ),
					'id'       => 'alg_wc_url_coupons_javascript_reload',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_url_coupons_advanced_options',
				),
			);
			$main_hook    = array(
				array(
					'title' => __( 'Main hook', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'  => __( 'The main hook used to detect and handle the coupon via URL.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'  => 'title',
					'id'    => 'alg_wc_url_coupons_main_hook_options',
				),
				array(
					'title'   => __( 'Hook', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'      => 'alg_wc_url_coupons_main_hook',
					'default' => 'wp_loaded',
					'type'    => 'select',
					'class'   => 'chosen_select',
					'options' => alg_wc_url_coupons()->core->get_possible_main_hooks(),
				),
				array(
					'title'    => __( 'Hook priority', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Priority for the main plugin hook.', 'url-coupons-for-woocommerce-by-algoritmika' ) . ' ' .
					              __( 'Leave empty for the default priority.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'       => 'alg_wc_url_coupons_priority',
					'default'  => '',
					'type'     => 'number',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_url_coupons_main_hook_options',
				),
			);
			$data_storage = array(
				array(
					'title' => __( 'Data management', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'  => 'title',
					'id'    => 'alg_wc_url_coupons_data_storage_options',
				),
				array(
					'title'   => __( 'Data storage type', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'    => __( 'The way the data from the plugin will be stored/retrieved.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'      => 'alg_wc_url_coupons_data_storage_type',
					'default' => 'session',
					'type'    => 'select',
					'class'   => 'chosen_select',
					'options' => array(
						'session' => __( 'Session', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'cookie'  => __( 'Cookie', 'url-coupons-for-woocommerce-by-algoritmika' ),
					),
				),
				array(
					'title'   => __( 'Extra cookie', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'    => sprintf( __( 'Set %s cookie when URL coupon has been applied', 'url-coupons-for-woocommerce-by-algoritmika' ), '<code>alg_wc_url_coupons</code>' ),
					'id'      => 'alg_wc_url_coupons_cookie_enabled',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'             => __( 'Cookie expiration', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'              => __( 'The time the cookie expires.', 'url-coupons-for-woocommerce-by-algoritmika' ) . ' ' .
					                       __( 'In seconds.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'                => 'alg_wc_url_coupons_cookie_sec',
					'default'           => 1209600,
					'type'              => 'number',
					'custom_attributes' => array( 'min' => 1 ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_url_coupons_data_storage_options',
				),
			);
			return array_merge( $general, $main_hook, $data_storage );
		}

	}

endif;

return new Alg_WC_URL_Coupons_Settings_Advanced();
