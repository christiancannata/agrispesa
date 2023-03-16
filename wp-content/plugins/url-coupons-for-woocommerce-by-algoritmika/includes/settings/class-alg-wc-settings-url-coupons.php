<?php
/**
 * URL Coupons for WooCommerce - Settings
 *
 * @version 1.6.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_URL_Coupons' ) ) :

	class Alg_WC_Settings_URL_Coupons extends WC_Settings_Page {

		/**
		 * Constructor.
		 *
		 * @version 1.6.0
		 * @since   1.0.0
		 */
		function __construct() {
			$this->id    = 'alg_wc_url_coupons';
			$this->label = __( 'URL Coupons', 'url-coupons-for-woocommerce-by-algoritmika' );
			parent::__construct();
			add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'custom_sanitize' ), PHP_INT_MAX, 3 );
			// Sections
			require_once( 'class-alg-wc-url-coupons-settings-section.php' );
			require_once( 'class-alg-wc-url-coupons-settings-general.php' );
			require_once( 'class-alg-wc-url-coupons-settings-notices.php' );
			require_once( 'class-alg-wc-url-coupons-settings-advanced.php' );
		}

		/**
		 * custom_sanitize.
		 *
		 * @version 1.5.4
		 * @since   1.5.4
		 */
		function custom_sanitize( $value, $option, $raw_value ) {
			if ( ! empty( $option['alg_wc_uc_sanitize'] ) && function_exists( $option['alg_wc_uc_sanitize'] ) ) {
				$func  = $option['alg_wc_uc_sanitize'];
				$value = $func( $raw_value );
			}
			return $value;
		}

		/**
		 * get_settings.
		 *
		 * @version 1.2.0
		 * @since   1.0.0
		 */
		function get_settings() {
			global $current_section;
			return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
				array(
					'title' => __( 'Reset Settings', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'  => 'title',
					'id'    => $this->id . '_' . $current_section . '_reset_options',
				),
				array(
					'title'    => __( 'Reset section settings', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => '<strong>' . __( 'Reset', 'url-coupons-for-woocommerce-by-algoritmika' ) . '</strong>',
					'desc_tip' => __( 'Check the box and save changes to reset.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'       => $this->id . '_' . $current_section . '_reset',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => $this->id . '_' . $current_section . '_reset_options',
				),
			) );
		}

		/**
		 * maybe_reset_settings.
		 *
		 * @version 1.2.0
		 * @since   1.0.0
		 */
		function maybe_reset_settings() {
			global $current_section;
			if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
				foreach ( $this->get_settings() as $value ) {
					if ( isset( $value['id'] ) ) {
						$id = explode( '[', $value['id'] );
						delete_option( $id[0] );
					}
				}
				if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
					WC_Admin_Settings::add_message( __( 'Your settings have been reset.', 'url-coupons-for-woocommerce-by-algoritmika' ) );
				} else {
					add_action( 'admin_notices', array( $this, 'admin_notice_settings_reset' ) );
				}
			}
		}

		/**
		 * admin_notice_settings_reset.
		 *
		 * @version 1.1.0
		 * @since   1.1.0
		 */
		function admin_notice_settings_reset() {
			echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			     __( 'Your settings have been reset.', 'url-coupons-for-woocommerce-by-algoritmika' ) . '</strong></p></div>';
		}

		/**
		 * Save settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function save() {
			parent::save();
			$this->maybe_reset_settings();
		}

	}

endif;

return new Alg_WC_Settings_URL_Coupons();
