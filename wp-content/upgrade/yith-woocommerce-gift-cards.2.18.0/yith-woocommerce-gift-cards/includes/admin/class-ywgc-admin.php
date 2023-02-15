<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWGC_Admin' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Admin
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Admin {

		/**
		 * Plugin panel page
		 */
		const PANEL_PAGE = 'yith_woocommerce_gift_cards_panel';

		/**
		 * @var $panel Panel Object
		 */
		protected $panel;

		/**
		 * @var string Premium version landing link
		 */
		protected $premium_landing = '//yithemes.com/themes/plugins/yith-woocommerce-gift-cards/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $official_documentation = 'https://docs.yithemes.com/yith-woocommerce-gift-cards/';

		/**
		 * @var string Official plugin landing page
		 */
		protected $premium_live = 'https://plugins.yithemes.com/yith-woocommerce-gift-cards/';

		/**
		 * @var string Official plugin support page
		 */
		protected $support = 'https://yithemes.com/my-account/support/dashboard/';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @param  array $args the arguments
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function __construct() {

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs['gift-cards']          = esc_html__( 'Dashboard', 'yith-woocommerce-gift-cards' );
			$admin_tabs['general']             = esc_html__( 'General', 'yith-woocommerce-gift-cards' );
			$admin_tabs['gift-cards-category'] = esc_html__( 'Image categories', 'yith-woocommerce-gift-cards' );

			$capability = apply_filters( 'yith_wcgc_plugin_settings_capability', 'manage_options' );

			$premium_tab = array(
				'landing_page_url' => $this->get_premium_landing_uri(),
				'premium_features' => array(
					// Put here all the premium Features.
					__( 'Set an <strong>expiration date for the gift card</strong> (a specific date, like January 01, or after a specific time after the purchase, like 3 months after)', 'yith-woocommerce-gift-cards' ),
					__( 'Manage stock of each gift card product', 'yith-woocommerce-gift-cards' ),
					__( 'Enable an optional <strong>QR code</strong> in gift cards', 'yith-woocommerce-gift-cards' ),
					__( 'Import and export gift cards into a <strong>CSV file</strong>', 'yith-woocommerce-gift-cards' ),
					__( 'Allow users to <strong>choose a delivery date</strong> for the gift card', 'yith-woocommerce-gift-cards' ),
					__( 'Allow users to <strong>enter a custom amount</strong> (and set the minimum and/or maximum amount)', 'yith-woocommerce-gift-cards' ),
					__( 'Allow users to <strong>upload a custom image or photo</strong> to customize the gift card', 'yith-woocommerce-gift-cards' ),
					__( 'Option to attach a PDF to gift card email', 'yith-woocommerce-gift-cards' ),
					__( 'Notify the sender via email when the gift card is delivered to recipient', 'yith-woocommerce-gift-cards' ),
					__( 'Allow users to enter the gift card code into the standard coupon code field (instead of showing two different forms in cart and checkout)', 'yith-woocommerce-gift-cards' ),
					__( 'Enable the “Gift this product” option in product pages to sell gift cards linked to specific products', 'yith-woocommerce-gift-cards' ),
					'<b>' . __( 'Regular updates, Translations and Premium Support', 'yith-woocommerce-gift-cards' ) . '</b>',
				),
				'main_image_url'   => YITH_YWGC_ASSETS_URL . '/images/gift-cards-get-premium.jpeg', // Plugin main image should be in your plugin assets folder.
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'plugin_slug'      => YITH_YWGC_SLUG,
				'page_title'       => 'Gift Cards',
				'menu_title'       => 'Gift Cards',
				'capability'       => $capability,
				'parent'           => '',
				'class'            => yith_set_wrapper_class(),
				'parent_page'      => 'yit_plugin_panel',
				'page'             => self::PANEL_PAGE,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YWGC_DIR . 'plugin-options',
				'premium_tab'      => $premium_tab,
				'is_premium'       => defined( 'YITH_YWGC_PREMIUM' ),
				'is_extended'      => defined( 'YITH_YWGC_EXTENDED' ),
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {

				require_once YITH_YWGC_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_YWGC_TEMPLATES_DIR . 'admin/' . $this->premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}


		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return apply_filters( 'yith_plugin_fw_premium_landing_uri', $this->premium_landing, YITH_YWGC_SLUG );
		}
	}
}

/**
 * Unique access to instance of YITH_YWGC_Admin class
 *
 * @return YITH_YWGC_Admin|YITH_YWGC_Admin_Premium|YITH_YWGC_Admin_Extended
 * @since 2.0.0
 */
function YITH_YWGC_Admin() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	if ( defined( 'YITH_YWGC_PREMIUM' ) ) {
		$instance = YITH_YWGC_Admin_Premium::get_instance();
	} elseif ( defined( 'YITH_YWGC_EXTENDED' ) ) {
		$instance = YITH_YWGC_Admin_Extended::get_instance();
	} else {
		$instance = YITH_YWGC_Admin::get_instance();
	}

	return $instance;
}
