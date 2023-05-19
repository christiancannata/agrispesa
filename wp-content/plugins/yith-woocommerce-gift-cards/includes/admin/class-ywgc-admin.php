<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Class YITH_YWGC_Admin
 *
 * @package YITH\GiftCards\Includes\Admin
 */

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
		 * @var $panel Panel Object
		 */
		protected $panel;

		/**
		 * Gift Cards panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_woocommerce_gift_cards_panel';

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

			add_action( 'plugins_loaded', array( __CLASS__, 'include_admin_handlers' ), 20 );

			add_action( 'before_woocommerce_init', array( $this, 'declare_wc_features_support' ) );

			add_action( 'yith_ywgc_email_settings', array( $this, 'email_settings' ) );
			add_action( 'yith_ywgc_print_email_settings', array( $this, 'print_email_settings' ) );

			add_action( 'wp_ajax_yith_ywgc_save_email_settings', array( $this, 'save_email_settings' ) );
			add_action( 'wp_ajax_nopriv_yith_ywgc_save_email_settings', array( $this, 'save_email_settings' ) );

			add_action( 'wp_ajax_yith_ywgc_save_mail_status', array( $this, 'save_mail_status' ) );
			add_action( 'wp_ajax_nopriv_yith_ywgc_save_mail_status', array( $this, 'save_mail_status' ) );

		}

		/**
		 * Retrieve the admin panel tabs.
		 *
		 * @return array
		 */
		protected function get_admin_panel_tabs(): array {
			return apply_filters(
				'yith_ywgc_admin_panel_tabs',
				array(
					'dashboard'     => array(
						'title' => _x( 'Dashboard', 'Settings tab name', 'yith-woocommerce-gift-cards' ),
						'icon'  => 'dashboard',
					),
					'settings'      => array(
						'title' => _x( 'Settings', 'Settings tab name', 'yith-woocommerce-gift-cards' ),
						'icon'  => 'settings',
					),
					'configuration' => array(
						'title' => _x( 'Configuration', 'Settings tab name', 'yith-woocommerce-gift-cards' ),
						'icon'  => 'configuration',
					),
					'email'         => array(
						'title' => __( 'Email Settings', 'yith-woocommerce-gift-cards' ),
						'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>',
					),
					'modules'       => array(
						'title' => _x( 'Modules', 'Modules tab name', 'yith-woocommerce-gift-cards' ),
						'description' => __( 'Enable the following modules to unlock additional features for your gift cards.', 'yith-woocommerce-gift-cards' ),
						'icon'  => 'add-ons',
					),
				)
			);
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

			$admin_tabs = $this->get_admin_panel_tabs();

			$capability = apply_filters( 'yith_wcgc_plugin_settings_capability', 'manage_options' );

			$args = array(
				'ui_version'       => 2,
				'create_menu_page' => true,
				'parent_slug'      => '',
				'plugin_slug'      => YITH_YWGC_SLUG,
				'page_title'       => 'Gift Cards',
				'menu_title'       => 'Gift Cards',
				'capability'       => $capability,
				'parent'           => '',
				'class'            => yith_set_wrapper_class(),
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YWGC_DIR . 'plugin-options',
				'premium_tab' => array(
					'features' => array(
						array(
							'title'       => __( 'Sell physical gift cards to ship to customers', 'yith-woocommerce-gift-cards' ),
							'description' => __( 'Create unlimited physical gift cards with one or multiple fixed amounts. Physical gift cards can be printed and shipped to customers.', 'yith-woocommerce-gift-cards' ),
						),
						array(
							'title'       => __( 'Advanced user options', 'yith-woocommerce-gift-cards' ),
							'description' => __( 'Allow users to enter a custom card amount and choose a delivery date and time for the gift card.', 'yith-woocommerce-gift-cards' ),
						),
						array(
							'title'       => __( 'Custom images support', 'yith-woocommerce-gift-cards' ),
							'description' => __( 'Users can upload or drag and drop a custom image or photo to customize the gift card and make it more special.', 'yith-woocommerce-gift-cards' ),
						),
						array(
							'title'       => __( 'Notifications to improve user experience ', 'yith-woocommerce-gift-cards' ),
							'description' => __( 'Notify the sender via e-mail when the gift card is delivered to the recipient and when it is used to purchase in your shop.', 'yith-woocommerce-gift-cards' ),
						),
						array(
							'title'       => __( 'Gift card redemption options', 'yith-woocommerce-gift-cards' ),
							'description' => __( 'Choose where to show the gift card field on the Cart and Checkout pages, set a minimum requested amount in the cart to allow users to apply a gift card code, exclude specific product categories from redemption, etc.', 'yith-woocommerce-gift-cards' ),
						),
						array(
							'title'       => __( 'Advanced admin options', 'yith-woocommerce-gift-cards' ),
							'description' => __( 'Generate and send in bulk multiple gift cards, disable cards, check and manually update their balance, and other options for advanced management.', 'yith-woocommerce-gift-cards' ),
						),
						array(
							'title'       => __( '"Gift this product” options to sell more gift cards ', 'yith-woocommerce-gift-cards' ),
							'description' => __( 'Suggest the purchase of a gift card on the product pages: the gift cards will be automatically generated with the same value as the products and they will be highlighted in the e-mail. ', 'yith-woocommerce-gift-cards' ),
						),
					),
				),
				'is_premium'       => defined( 'YITH_YWGC_PREMIUM' ),
				'is_extended'      => defined( 'YITH_YWGC_EXTENDED' ),
			);

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_YWGC_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );


		}

		/**
		 * Include Admin Post Type and Taxonomy handlers.
		 */
		public static function include_admin_handlers() {

			require_once trailingslashit( YITH_YWGC_DIR ) . 'includes/admin/post-types/class-yith-ywgc-gift-card-post-type-admin.php';

			do_action( 'yith_ywgc_admin_post_type_handlers_loaded' );
		}

		/**
		 * Declare support for WooCommerce features.
		 */
		public function declare_wc_features_support() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', YITH_YWGC_INIT, true );
			}
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
				include_once $premium_tab_template;
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

		/**
		 * Handle email settings tab
		 * This method based on query string load single email options or the general table
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 */
		public function email_settings() {

			$emails = YITH_YWGC()->get_emails();
			// is a single email view?
			$active = '';
			if ( isset( $_GET['section'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification
				foreach ( $emails as $email ) {
					if ( strtolower( $email ) === sanitize_text_field( wp_unslash( $_GET['section'] ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification
						$active = $email;
						break;
					}
				}
			}

			// load mailer.
			$mailer = WC()->mailer();

			$emails_table = array();
			foreach ( $emails as $email ) {
				$email_class            = $mailer->emails[ $email ];
				$emails_table[ $email ] = array(
					'title'       => $email_class->get_title(),
					'description' => $email_class->get_description(),
					'recipient'   => $email_class->is_customer_email() ? __( 'Customer', 'yith-woocommerce-gift-cards' ) : $email_class->get_recipient(),
					'enable'      => $email_class->is_enabled(),
					'content'     => $email_class->get_content_type(),
				);
			}

			include_once YITH_YWGC_DIR . '/templates/admin/email-settings-tab.php';
		}

		/**
		 * Outout emal settings section
		 *
		 * @param string $email_key Email ID.
		 *
		 * @return void
		 */
		public function print_email_settings( $email_key ) {
			global $current_section;
			$current_section = strtolower( $email_key );
			$mailer          = WC()->mailer();
			$class           = $mailer->emails[ $email_key ];
			WC_Admin_Settings::get_settings_pages();

			if ( ! empty( $_POST ) ) {//phpcs:ignore WordPress.Security.NonceVerification
				$class->process_admin_options();
			}

			include YITH_YWGC_DIR . '/templates/admin/email-settings-single.php';

			$current_section = null;
		}

		/**
		 * Save email settings in ajax.
		 *
		 * @return void
		 */
		public function save_email_settings() {
			parse_str( $_POST['params'], $params );
			unset( $_POST['params'] );

			foreach ( $params as $key => $value ) {
				$_POST[ $key ] = $value;
			}

			global $current_section;
			$current_section = strtolower( $_POST['email_key'] );

			$mailer = WC()->mailer();
			$class  = $mailer->emails[ $_POST['email_key'] ];
			$class->process_admin_options();

			$current_section = null;

			wp_send_json_success( array( 'msg' => 'Email updated' ) );
			die();
		}

		/**
		 * Save email status in ajax.
		 *
		 * @return void
		 */
		public function save_mail_status() {

			if ( isset( $_POST['email_key'] ) && $_POST['enabled'] ) {
				$email_key      = $_POST['email_key'];
				$email_settings = get_option( 'woocommerce_' . $email_key . '_settings' );
				if ( is_array( $email_settings ) && ! empty( $email_key ) ) {
					$email_settings['enabled'] = $_POST['enabled'];
					update_option( 'woocommerce_' . $email_key . '_settings', $email_settings );
				}
			}
			die();
		}

		/**
		 * Build single email settings page
		 *
		 * @param string $email_key The email key.
		 *
		 * @return string
		 * @since  1.5.0
		 * @author Francesco Licandro
		 */
		public function build_single_email_settings_url( $email_key ) {
			return admin_url( "admin.php?page={$this->panel_page}&tab=email&section=" . strtolower( $email_key ) );
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
