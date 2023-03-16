<?php
/**
 * URL Coupons for WooCommerce - Notices Section Settings
 *
 * @version 1.6.0
 * @since   1.6.0
 *
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_URL_Coupons_Settings_Notices' ) ) :

	class Alg_WC_URL_Coupons_Settings_Notices extends Alg_WC_URL_Coupons_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.6.0
		 * @since   1.6.0
		 */
		function __construct() {
			$this->id   = 'notices';
			$this->desc = __( 'Notices', 'url-coupons-for-woocommerce-by-algoritmika' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 1.6.0
		 * @since   1.6.0
		 *
		 * @todo    [maybe] (desc) Custom notice: better desc?
		 * @todo    [maybe] (desc) Notice method: better desc?
		 */
		function get_settings() {
			return array(
				array(
					'title' => __( 'Notice Options', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'  => apply_filters( 'alg_wc_url_coupons_settings',
						sprintf( 'Disabled (grayed-out) options are available in <a href="%s" target="_blank">URL Coupons for WooCommerce Pro</a> plugin version only.',
							'https://wpfactory.com/item/url-coupons-woocommerce/' ) ),
					'type'  => 'title',
					'id'    => 'alg_wc_url_coupons_notice_options',
				),
				array(
					'title'    => __( 'Delay notice', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => __( 'Delay', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Delay the "Coupon code applied successfully" notice if the cart is empty when applying a URL coupon.', 'url-coupons-for-woocommerce-by-algoritmika' ) . ' ' .
					              __( 'Notice will be delayed until there is at least one product in the cart.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'       => 'alg_wc_url_coupons_delay_notice',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'             => __( 'Custom notice', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'          => __( 'Custom notice to be displayed when coupon code is successfully applied.', 'url-coupons-for-woocommerce-by-algoritmika' ) . ' ' .
					                       __( 'Ignored if empty.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'                => 'alg_wc_url_coupons_notice',
					'default'           => '',
					'type'              => 'textarea',
					'css'               => 'width:100%;',
					'custom_attributes' => apply_filters( 'alg_wc_url_coupons_settings', array( 'readonly' => 'readonly' ) ),
				),
				array(
					'title'             => __( 'Notice per coupon', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'              => __( 'Enable', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'          => sprintf( __( 'Will add "%s" field to each coupon admin edit page.', 'url-coupons-for-woocommerce-by-algoritmika' ),
						__( 'URL Coupons', 'url-coupons-for-woocommerce-by-algoritmika' ) . ': ' . __( 'Notice', 'url-coupons-for-woocommerce-by-algoritmika' ) ),
					'id'                => 'alg_wc_url_coupons_notice_per_coupon',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'start',
					'custom_attributes' => apply_filters( 'alg_wc_url_coupons_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'              => __( 'Override global notice', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'          => __( 'This will remove the global "Custom notice" notice, in case if there are any "per coupon" notices to display.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'                => 'alg_wc_url_coupons_notice_per_coupon_override',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'end',
					'custom_attributes' => apply_filters( 'alg_wc_url_coupons_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Override default notice', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'              => __( 'Enable', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'          => __( 'This will remove the default "Coupon code applied successfully" notice, in case if there are any custom notices to display.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'                => 'alg_wc_url_coupons_notice_remove_default',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_url_coupons_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Notice method', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'          => __( 'Possible values: "Add" or "Append".', 'url-coupons-for-woocommerce-by-algoritmika' ) . ' ' .
					                       __( 'This makes a difference only if you have multiple notices displayed: "Add" option will add it as a new notice (i.e. will display it as a multiple notices), while "Append" option will merge it with the existing notice, for example, with the default "Coupon was successfully applied" notice (i.e. will display it as a single notice).', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'                => 'alg_wc_url_coupons_notice_method',
					'default'           => 'add',
					'type'              => 'select',
					'class'             => 'chosen_select',
					'options'           => array(
						'add'    => __( 'Add', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'append' => __( 'Append', 'url-coupons-for-woocommerce-by-algoritmika' ),
					),
					'custom_attributes' => apply_filters( 'alg_wc_url_coupons_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'              => __( 'Notice type', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'          => sprintf( __( 'Ignored, unless "%s" option is set to "%s".', 'url-coupons-for-woocommerce-by-algoritmika' ),
						__( 'Notice method', 'url-coupons-for-woocommerce-by-algoritmika' ), __( 'Add', 'url-coupons-for-woocommerce-by-algoritmika' ) ),
					'id'                => 'alg_wc_url_coupons_notice_type',
					'default'           => 'success',
					'type'              => 'select',
					'class'             => 'chosen_select',
					'options'           => array(
						'success' => __( 'Success', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'error'   => __( 'Error', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'notice'  => __( 'Notice', 'url-coupons-for-woocommerce-by-algoritmika' ),
					),
					'custom_attributes' => apply_filters( 'alg_wc_url_coupons_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'               => __( 'Notice glue', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'           => sprintf( __( 'Ignored, unless "%s" option is set to "%s".', 'url-coupons-for-woocommerce-by-algoritmika' ),
						__( 'Notice method', 'url-coupons-for-woocommerce-by-algoritmika' ), __( 'Append', 'url-coupons-for-woocommerce-by-algoritmika' ) ),
					'id'                 => 'alg_wc_url_coupons_notice_glue',
					'default'            => '<br>',
					'type'               => 'text',
					'custom_attributes'  => apply_filters( 'alg_wc_url_coupons_settings', array( 'readonly' => 'readonly' ) ),
					'alg_wc_uc_sanitize' => 'wp_kses_post',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_url_coupons_notice_options',
				),
			);
		}

	}

endif;

return new Alg_WC_URL_Coupons_Settings_Notices();
