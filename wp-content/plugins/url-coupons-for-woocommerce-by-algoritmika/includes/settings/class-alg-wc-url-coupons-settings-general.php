<?php
/**
 * URL Coupons for WooCommerce - General Section Settings
 *
 * @version 1.6.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_URL_Coupons_Settings_General' ) ) :

	class Alg_WC_URL_Coupons_Settings_General extends Alg_WC_URL_Coupons_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.1.0
		 * @since   1.0.0
		 */
		function __construct() {
			$this->id   = '';
			$this->desc = __( 'General', 'url-coupons-for-woocommerce-by-algoritmika' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 1.6.0
		 * @since   1.0.0
		 *
		 * @todo    [now] (dev) `alg_wc_url_coupons_delay_coupon_non_empty_cart`: default to `no`?
		 * @todo    [now] (desc) `alg_wc_url_coupons_delay_coupon_non_empty_cart`: better `check_product` option name
		 * @todo    [next] (dev) split into sections, e.g. "General", "Delay Coupon", "Hide Coupon", "Redirect", etc.
		 * @todo    [now] (dev) move "Notes" to "Notices"?
		 * @todo    [next] (desc) notes: "... all *available* WooCommerce notices..."
		 * @todo    [maybe] (desc) `[alg_wc_url_coupons_print_notices]` shortcode: better desc?
		 * @todo    [now] (dev) `alg_wc_url_coupons_key`: default to `apply_coupon`
		 * @todo    [next] (desc) translations: notices: "You can also use shortcodes here."
		 * @todo    [next] (dev) translations (i.e. `do_shortcode()`): `alg_wc_url_coupons_key`
		 * @todo    [next] (dev) translations (i.e. `do_shortcode()`): "Redirect URL" (including "per coupon")
		 * @todo    [next] [!] (dev) Delay coupon: Success notice: better default value
		 * @todo    [next] (desc): `alg_wc_url_coupons_delay_coupon_check_product`: better desc!
		 * @todo    [next] (dev): `alg_wc_url_coupons_delay_coupon_check_product`: default to `yes`?
		 * @todo    [next] (dev) Delay coupon notice, Custom notice, Notice glue: raw?
		 * @todo    [maybe] (desc) Delay coupon notice: better desc?
		 * @todo    [maybe] (desc) Redirect URL per coupon: better desc?
		 * @todo    [maybe] (dev) add "Force print notices" option (i.e. use e.g. `wp_head` action (i.e. alternative for the `[alg_wc_url_coupons_print_notices]` shortcode))?
		 */
		function get_settings() {

			$main_settings = array(
				array(
					'title' => __( 'URL Coupons Options', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'  => 'title',
					'id'    => 'alg_wc_url_coupons_options',
				),
				array(
					'title'   => __( 'URL Coupons', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'    => '<strong>' . __( 'Enable plugin', 'url-coupons-for-woocommerce-by-algoritmika' ) . '</strong>',
					'id'      => 'alg_wc_url_coupons_enabled',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_url_coupons_options',
				),
			);

			$general_settings = array(
				array(
					'title' => __( 'General Options', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'  => 'title',
					'id'    => 'alg_wc_url_coupons_general_options',
				),
				array(
					'title'    => __( 'URL coupons key', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'URL key. If you change this, make sure it\'s unique and is not used anywhere on your site (e.g. by another plugin).', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => '<p>' . sprintf( __( 'Your customers can apply shop\'s standard coupons by visiting URL. E.g.: %s.', 'url-coupons-for-woocommerce-by-algoritmika' ),
							'<code>' . site_url() . '/?' . '<strong>' . get_option( 'alg_wc_url_coupons_key', 'alg_apply_coupon' ) . '</strong>' . '=couponcode' . '</code>' ) . '</p>',
					'id'       => 'alg_wc_url_coupons_key',
					'default'  => 'alg_apply_coupon',
					'type'     => 'text',
				),
				array(
					'title'         => __( 'Session', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'          => __( 'Force session start when a URL with the coupon key is accessed', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'      => __( 'Enable this if URL coupons are not being applied to the guests (i.e. not logged users).', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'            => 'alg_wc_url_coupons_force_start_session',
					'default'       => 'yes',
					'checkboxgroup' => 'start',
					'type'          => 'checkbox',
				),
				array(
					'desc'          => __( 'Force session start earlier and everywhere', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'      => __( 'Enable this if URL coupons are still not being applied to the guests users, most probably on cached sites.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'            => 'alg_wc_url_coupons_force_start_session_earlier',
					'default'       => 'no',
					'checkboxgroup' => 'end',
					'type'          => 'checkbox',
				),
				array(
					'title'             => __( 'Add products to cart', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'              => __( 'Enable', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'          => __( 'Automatically adds coupon\'s products to the cart for "Fixed product discount" type coupons.', 'url-coupons-for-woocommerce-by-algoritmika' ) .
					                       apply_filters( 'alg_wc_url_coupons_settings',
						                       '<br>' . sprintf( 'This option is available in <a href="%s" target="_blank">URL Coupons for WooCommerce Pro</a> plugin version only.',
							                       'https://wpfactory.com/item/url-coupons-woocommerce/' ) ),
					'id'                => 'alg_wc_url_coupons_fixed_product_discount_add_products',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'start',
					'custom_attributes' => apply_filters( 'alg_wc_url_coupons_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'              => __( 'Empty cart', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'          => __( 'Clear the cart before adding the coupon\'s products.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'                => 'alg_wc_url_coupons_fixed_product_discount_add_products_emptycart',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'end',
					'custom_attributes' => apply_filters( 'alg_wc_url_coupons_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_url_coupons_general_options',
				),
			);

			$delay_coupon_settings = array(
				array(
					'title' => __( 'Delay Coupon Options', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'  => 'title',
					'id'    => 'alg_wc_url_coupons_delay_coupon_options',
				),
				array(
					'title'    => __( 'Delay coupon', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => __( 'Enable section', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Delay applying the coupon until some product is added to the cart.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'       => 'alg_wc_url_coupons_delay_coupon',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'desc'     => __( 'Delay on non-empty cart', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'What to do if there are already products in cart when applying coupon.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'       => 'alg_wc_url_coupons_delay_coupon_non_empty_cart',
					'default'  => 'yes',
					'type'     => 'select',
					'class'    => 'chosen_select',
					'options'  => array(
						'yes'           => __( 'Yes', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'no'            => __( 'No', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'check_product' => __( 'No, if there is correct product already in the cart (for "Fixed product discount" type coupons)', 'url-coupons-for-woocommerce-by-algoritmika' ),
					),
				),
				array(
					'desc'     => __( 'Check product', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'For "Fixed product discount" type coupons - additionally check if correct product is added to the cart (i.e. if coupon is valid for product).', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'       => 'alg_wc_url_coupons_delay_coupon_check_product',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'desc'     => __( 'Success notice', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Ignored if empty.', 'url-coupons-for-woocommerce-by-algoritmika' ) . ' ' .
					              sprintf( __( 'Available placeholder(s): %s.', 'url-coupons-for-woocommerce-by-algoritmika' ), '%coupon_code%' ),
					'id'       => 'alg_wc_url_coupons_delay_coupon_notice[success]',
					'default'  => __( 'Coupon code applied successfully.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'     => 'textarea',
					'css'      => 'width:100%;',
				),
				array(
					'desc'     => __( 'Error notice', 'url-coupons-for-woocommerce-by-algoritmika' ) . ': ' .
					              __( 'Coupon already applied', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Ignored if empty.', 'url-coupons-for-woocommerce-by-algoritmika' ) . ' ' .
					              sprintf( __( 'Available placeholder(s): %s.', 'url-coupons-for-woocommerce-by-algoritmika' ), '%coupon_code%' ),
					'id'       => 'alg_wc_url_coupons_delay_coupon_notice[error_applied]',
					'default'  => __( 'Coupon code already applied!', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'     => 'textarea',
					'css'      => 'width:100%;',
				),
				array(
					'desc'     => __( 'Error notice', 'url-coupons-for-woocommerce-by-algoritmika' ) . ': ' .
					              __( 'Coupon does not exist', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Ignored if empty.', 'url-coupons-for-woocommerce-by-algoritmika' ) . ' ' .
					              sprintf( __( 'Available placeholder(s): %s.', 'url-coupons-for-woocommerce-by-algoritmika' ), '%coupon_code%' ),
					'id'       => 'alg_wc_url_coupons_delay_coupon_notice[error_not_found]',
					'default'  => __( 'Coupon "%coupon_code%" does not exist!', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'     => 'textarea',
					'css'      => 'width:100%;',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_url_coupons_delay_coupon_options',
				),
			);

			$hide_coupon_settings = array(
				array(
					'title' => __( 'Hide Coupon Options', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'  => 'title',
					'id'    => 'alg_wc_url_coupons_hide_coupon_options',
				),
				array(
					'title'    => __( 'Cart page', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => __( 'Hide', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Enable this if you want to hide standard coupon input field on the cart page.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'       => 'alg_wc_url_coupons_cart_hide_coupon',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Checkout page', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => __( 'Hide', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Enable this if you want to hide standard coupon input field on the checkout page.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'       => 'alg_wc_url_coupons_checkout_hide_coupon',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Hide condition', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'     => __( 'Select a hide condition for the coupon input field', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip' => __( 'Select "Hide if coupon applied via URL" if you want to hide the standard coupon input field if a coupon is applied via URL, select "Always hide" if you want to always hide the standard coupon input field.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'       => 'alg_wc_url_coupons_hide_coupon_condition',
					'default'  => 'always',
					'type'     => 'select',
					'options'  => array(
						'url'    => __( 'Hide if coupon applied via URL', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'always' => __( 'Always hide', 'url-coupons-for-woocommerce-by-algoritmika' ),
					),
				),


				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_url_coupons_hide_coupon_options',
				),
			);

			$redirect_settings = array(
				array(
					'title' => __( 'Redirect Options', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'  => apply_filters( 'alg_wc_url_coupons_settings',
						sprintf( 'Redirect options are available in <a href="%s" target="_blank">URL Coupons for WooCommerce Pro</a> plugin version only.',
							'https://wpfactory.com/item/url-coupons-woocommerce/' ) ),
					'type'  => 'title',
					'id'    => 'alg_wc_url_coupons_redirect_options',
				),
				array(
					'title'             => __( 'Redirect URL', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'          => __( 'Redirect when coupon code is successfully applied.', 'url-coupons-for-woocommerce-by-algoritmika' ) . ' ' .
					                       __( 'Possible values: No redirect; redirect to cart; redirect to checkout; redirect to custom local URL.', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'                => 'alg_wc_url_coupons_redirect',
					'default'           => 'no',
					'type'              => 'select',
					'class'             => 'chosen_select',
					'options'           => array(
						'no'       => __( 'No redirect', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'cart'     => __( 'Redirect to cart', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'checkout' => __( 'Redirect to checkout', 'url-coupons-for-woocommerce-by-algoritmika' ),
						'custom'   => __( 'Redirect to custom local URL', 'url-coupons-for-woocommerce-by-algoritmika' ),
					),
					'custom_attributes' => apply_filters( 'alg_wc_url_coupons_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'              => __( 'Custom local URL', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'id'                => 'alg_wc_url_coupons_redirect_custom_url',
					'default'           => '',
					'type'              => 'text',
					'css'               => 'width:100%;',
					'custom_attributes' => apply_filters( 'alg_wc_url_coupons_settings', array( 'readonly' => 'readonly' ) ),
				),
				array(
					'title'             => __( 'Redirect URL per coupon', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'              => __( 'Enable', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc_tip'          => sprintf( __( 'Will add "%s" field to each coupon admin edit page.', 'url-coupons-for-woocommerce-by-algoritmika' ),
						__( 'URL Coupons', 'url-coupons-for-woocommerce-by-algoritmika' ) . ': ' . __( 'Redirect URL', 'url-coupons-for-woocommerce-by-algoritmika' ) ),
					'id'                => 'alg_wc_url_coupons_redirect_per_coupon',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_url_coupons_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_url_coupons_redirect_options',
				),
			);

			$notes = array(
				array(
					'title' => __( 'Notes', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'desc'  => '<span class="dashicons dashicons-info"></span> ' .
					           sprintf( __( 'If you are using URL to a page where no WooCommerce notices are displayed, try adding our %s shortcode to the content.', 'url-coupons-for-woocommerce-by-algoritmika' ),
						           '<code>[alg_wc_url_coupons_print_notices]</code>' ) . ' ' .
					           __( 'Please note that this shortcode will print all WooCommerce notices (i.e. not only from our plugin, or notices related to the coupons).', 'url-coupons-for-woocommerce-by-algoritmika' ),
					'type'  => 'title',
					'id'    => 'alg_wc_url_coupons_notes',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_url_coupons_notes',
				),
			);

			return array_merge( $main_settings, $general_settings, $delay_coupon_settings, $hide_coupon_settings, $redirect_settings, $notes );
		}

	}

endif;

return new Alg_WC_URL_Coupons_Settings_General();
