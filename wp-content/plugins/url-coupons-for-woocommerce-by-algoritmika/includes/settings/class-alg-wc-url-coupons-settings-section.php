<?php
/**
 * URL Coupons for WooCommerce - Section Settings
 *
 * @version 1.1.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_URL_Coupons_Settings_Section' ) ) :

	class Alg_WC_URL_Coupons_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.1.0
		 * @since   1.0.0
		 */
		function __construct() {
			add_filter( 'woocommerce_get_sections_alg_wc_url_coupons', array( $this, 'settings_section' ) );
			add_filter( 'woocommerce_get_settings_alg_wc_url_coupons_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
		}

		/**
		 * settings_section.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function settings_section( $sections ) {
			$sections[ $this->id ] = $this->desc;
			return $sections;
		}

	}

endif;
