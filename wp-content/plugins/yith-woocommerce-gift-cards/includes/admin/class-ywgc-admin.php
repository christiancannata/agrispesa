<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Class YITH_YWGC_Admin
 *
 * @package YITH\GiftCards\Includes\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWGC_Admin' ) ) {
	/**
	 * YITH_YWGC_Admin class
	 *
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_YWGC_Admin {

		/**
		 * The panel
		 *
		 * @var YIT_Plugin_Panel_WooCommerce $panel
		 */
		protected $panel;

		/**
		 * Gift Cards panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_woocommerce_gift_cards_panel';

		/**
		 * Single instance of the class
		 *
		 * @var YITH_YWGC_Admin
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
		 * @since  1.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWGC_DIR . 'init.php' ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

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
					'dashboard' => array(
						'title' => _x( 'Dashboard', 'Settings tab name', 'yith-woocommerce-gift-cards' ),
						'icon'  => 'dashboard',
					),
					'settings'  => array(
						'title' => _x( 'Settings', 'Settings tab name', 'yith-woocommerce-gift-cards' ),
						'icon'  => 'settings',
					),
					'email'     => array(
						'title' => __( 'Email Settings', 'yith-woocommerce-gift-cards' ),
						'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>',
					),
					'modules'   => array(
						'title'       => _x( 'Modules', 'Modules tab name', 'yith-woocommerce-gift-cards' ),
						'description' => __( 'Enable the following modules to unlock additional features for your gift cards.', 'yith-woocommerce-gift-cards' ),
						'icon'        => 'add-ons',
					),
				)
			);
		}

		/**
		 * Retrieve the premium tab content.
		 *
		 * @return array
		 */
		protected function get_premium_tab(): array {
			return array(
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
			);
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {
			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = $this->get_admin_panel_tabs();
			$capability = get_option( 'ywgc_allow_shop_manager', 'no' ) === 'yes' ? 'manage_woocommerce' : apply_filters( 'yith_wcgc_plugin_settings_capability', 'manage_options' );

			$args = array(
				'ui_version'       => 2,
				'create_menu_page' => true,
				'parent_slug'      => '',
				'plugin_slug'      => YITH_YWGC_SLUG,
				'page_title'       => 'YITH WooCommerce Gift Cards',
				'menu_title'       => 'Gift Cards',
				'capability'       => $capability,
				'parent'           => '',
				'class'            => yith_set_wrapper_class(),
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YWGC_DIR . 'plugin-options',
				'is_free'          => defined( 'YITH_YWGC_FREE' ),
				'is_extended'      => defined( 'YITH_YWGC_EXTENDED' ),
				'is_premium'       => defined( 'YITH_YWGC_PREMIUM' ),
				'plugin_version'   => YITH_YWGC_VERSION,
				'plugin_icon'      => YITH_YWGC_ASSETS_URL . '/images/gift-card.svg',
			);

			// registers premium tab.
			if ( ! defined( 'YITH_YWGC_PREMIUM' ) ) {
				$args['premium_tab'] = $this->get_premium_tab();
			}

			// registers help tab.
			if ( ! defined( 'YITH_YWGC_FREE' ) ) {
				$args['help_tab'] = $this->get_help_tab();
			}

			// registers welcome modals.
			if ( ! defined( 'YITH_YWGC_FREE' ) ) {
				$args['welcome_modals'] = $this->get_welcome_modals();
			}

			if ( defined( 'YITH_YWGC_PREMIUM' ) ) {
				$args['your_store_tools'] = array(
					'items' => array(
						'wishlist'               => array(
							'name'           => 'Wishlist',
							'icon_url'       => YITH_YWGC_ASSETS_URL . '/images/plugins/wishlist.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-wishlist/',
							'description'    => _x(
								'Allow your customers to create lists of products they want and share them with family and friends.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Wishlist',
								'yith-woocommerce-gift-cards'
							),
							'is_active'      => defined( 'YITH_WCWL_PREMIUM' ),
							'is_recommended' => true,
						),
						'ajax-product-filter'    => array(
							'name'           => 'Ajax Product Filter',
							'icon_url'       => YITH_YWGC_ASSETS_URL . '/images/plugins/ajax-product-filter.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-ajax-product-filter/',
							'description'    => _x(
								'Help your customers to easily find the products they are looking for and improve the user experience of your shop.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Ajax Product Filter',
								'yith-woocommerce-gift-cards'
							),
							'is_active'      => defined( 'YITH_WCAN_PREMIUM' ),
							'is_recommended' => true,
						),
						'booking'                => array(
							'name'           => 'Booking and Appointment',
							'icon_url'       => YITH_YWGC_ASSETS_URL . '/images/plugins/booking.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-booking/',
							'description'    => _x(
								'Enable a booking/appointment system to manage renting or booking of services, rooms, houses, cars, accommodation facilities and so on.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH Bookings',
								'yith-woocommerce-gift-cards'
							),
							'is_active'      => defined( 'YITH_WCBK_PREMIUM' ),
							'is_recommended' => false,

						),
						'request-a-quote'        => array(
							'name'           => 'Request a Quote',
							'icon_url'       => YITH_YWGC_ASSETS_URL . '/images/plugins/request-a-quote.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-request-a-quote/',
							'description'    => _x(
								'Hide prices and/or the "Add to cart" button and let your customers request a custom quote for every product.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Request a Quote',
								'yith-woocommerce-gift-cards'
							),
							'is_active'      => defined( 'YITH_YWRAQ_PREMIUM' ),
							'is_recommended' => false,
						),
						'product-addons'         => array(
							'name'           => 'Product Add-Ons & Extra Options',
							'icon_url'       => YITH_YWGC_ASSETS_URL . '/images/plugins/product-add-ons.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-product-add-ons/',
							'description'    => _x(
								'Add paid or free advanced options to your product pages using fields like radio buttons, checkboxes, drop-downs, custom text inputs, and more.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Product Add-Ons',
								'yith-woocommerce-gift-cards'
							),
							'is_active'      => defined( 'YITH_WAPO_PREMIUM' ),
							'is_recommended' => false,
						),
						'dynamic-pricing'        => array(
							'name'           => 'Dynamic Pricing and Discounts',
							'icon_url'       => YITH_YWGC_ASSETS_URL . '/images/plugins/dynamic-pricing-and-discounts.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-dynamic-pricing-and-discounts/',
							'description'    => _x(
								'Increase conversions through dynamic discounts and price rules, and build powerful and targeted offers.',
								'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Dynamic Pricing and Discounts',
								'yith-woocommerce-gift-cards'
							),
							'is_active'      => defined( 'YITH_YWDPD_PREMIUM' ),
							'is_recommended' => false,
						),
						'customize-my-account'   => array(
							'name'           => 'Customize My Account Page',
							'icon_url'       => YITH_YWGC_ASSETS_URL . '/images/plugins/customize-myaccount-page.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-customize-my-account-page/',
							'description'    => _x( 'Customize the My Account page of your customers by creating custom sections with promotions and ad-hoc content based on your needs.', '[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Customize My Account', 'yith-woocommerce-gift-cards' ),
							'is_active'      => defined( 'YITH_WCMAP_PREMIUM' ),
							'is_recommended' => false,
						),
						'recover-abandoned-cart' => array(
							'name'           => 'Recover Abandoned Cart',
							'icon_url'       => YITH_YWGC_ASSETS_URL . '/images/plugins/recover-abandoned-cart.svg',
							'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-recover-abandoned-cart/',
							'description'    => _x(
								'Contact users who have added products to the cart without completing the order and try to recover lost sales.',
								'[YOUR STORE TOOLS TAB] Description for plugin Recover Abandoned Cart',
								'yith-woocommerce-gift-cards'
							),
							'is_active'      => defined( 'YITH_YWRAC_PREMIUM' ),
							'is_recommended' => false,
						),
					),
				);
			}

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_YWGC_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Action links
		 *
		 * @param mixed $links links.
		 *
		 * @return array|mixed
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->panel_page, defined( 'YITH_YWGC_PREMIUM' ), YITH_YWGC_SLUG );

			return $links;
		}

		/**
		 * Plugin Row Meta
		 *
		 * @param  mixed $new_row_meta_args new_row_meta_args.
		 * @param  mixed $plugin_meta plugin_meta.
		 * @param  mixed $plugin_file plugin_file.
		 * @param  mixed $plugin_data plugin_data.
		 * @param  mixed $status status.
		 * @param  mixed $init_file init_file.
		 *
		 * @return mixed
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWGC_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_YWGC_SLUG;
			}

			if ( defined( 'YITH_YWGC_FREE' ) ) {
				$new_row_meta_args['is_free'] = true;
			}

			if ( defined( 'YITH_YWGC_PREMIUM' ) ) {
				$new_row_meta_args['is_premium'] = true;
			}

			if ( defined( 'YITH_YWGC_EXTENDED' ) ) {
				$new_row_meta_args['is_extended'] = true;
			}

			return $new_row_meta_args;
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
		 * Handle email settings tab
		 * This method based on query string load single email options or the general table
		 *
		 * @since  1.5.0
		 */
		public function email_settings() {
			$emails = YITH_YWGC()->get_emails();

			// is a single email view?
			$active = '';

			if ( isset( $_GET['section'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				foreach ( $emails as $email ) {
					if ( strtolower( $email ) === sanitize_text_field( wp_unslash( $_GET['section'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
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

			if ( ! empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
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
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( isset( $_POST['security'], $_POST['params'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'yith_ywgc_save_email_settings' ) && current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
				parse_str( $_POST['params'], $params ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				unset( $_POST['params'] );

				foreach ( $params as $key => $value ) {
					$_POST[ $key ] = $value;
				}

				global $current_section;

				$email_key       = isset( $_POST['email_key'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['email_key'] ) ) ) : '';
				$current_section = $email_key;

				$mailer = WC()->mailer();
				$class  = $mailer->emails[ $email_key ];
				$class->process_admin_options();

				$current_section = null;

				wp_send_json_success( array( 'msg' => 'Email updated' ) );
				die();
			}
			// phpcs:enable WordPress.Security.NonceVerification.Missing
		}

		/**
		 * Save email status in ajax.
		 *
		 * @return void
		 */
		public function save_mail_status() {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( isset( $_POST['security'], $_POST['email_key'], $_POST['enabled'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'yith_ywgc_save_email_status' ) && current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
				$email_key      = sanitize_text_field( wp_unslash( $_POST['email_key'] ) );
				$email_settings = get_option( 'woocommerce_' . $email_key . '_settings' );

				if ( is_array( $email_settings ) && ! empty( $email_key ) ) {
					$email_settings['enabled'] = sanitize_text_field( wp_unslash( $_POST['enabled'] ) );
					update_option( 'woocommerce_' . $email_key . '_settings', $email_settings );
				}
			}

			die();
			// phpcs:enable WordPress.Security.NonceVerification.Missing
		}

		/**
		 * Build single email settings page
		 *
		 * @param string $email_key The email key.
		 *
		 * @return string
		 * @since  1.5.0
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
function YITH_YWGC_Admin() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	if ( defined( 'YITH_YWGC_PREMIUM' ) ) {
		$instance = YITH_YWGC_Admin_Premium::get_instance();
	} elseif ( defined( 'YITH_YWGC_EXTENDED' ) ) {
		$instance = YITH_YWGC_Admin_Extended::get_instance();
	} else {
		$instance = YITH_YWGC_Admin::get_instance();
	}

	return $instance;
}
