<?php
/**
 * YITH_WooCommerce_Gift_Cards class
 *
 * @package YITH\GiftCards\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WooCommerce_Gift_Cards' ) ) {
	/**
	 * YITH_WooCommerce_Gift_Cards class
	 *
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_WooCommerce_Gift_Cards {

		const YWGC_DB_VERSION_OPTION = 'yith_gift_cards_db_version';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Backend variable
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $backend;

		/**
		 * Frontend variable
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $frontend;

		/**
		 * Plugin emails array
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $emails = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return instance|YITH_WooCommerce_Gift_Cards
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
		protected function __construct() {
			$this->includes();
			$this->init_hooks();
			$this->start();
		}

		/**
		 * Includes
		 *
		 * @return void
		 */
		public function includes() {
			// Elementor Widgets integration.
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				require_once YITH_YWGC_DIR . 'includes/compatibilities/elementor/class-ywgc-elementor.php';
			}
		}

		/**
		 * Init_hooks
		 *
		 * @return void
		 */
		public function init_hooks() {
			/**
			 * Do some stuff on plugin init
			 */
			add_action( 'init', array( $this, 'on_plugin_init' ) );

			add_filter( 'yith_plugin_status_sections', array( $this, 'set_plugin_status' ) );

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

			$this->register_custom_post_statuses();
			$this->init_plugin_emails_array();

			/**
			 * Add an option to let the admin set the gift card as a physical good or digital goods
			 */
			add_filter( 'product_type_options', array( $this, 'add_type_option' ) );

			/**
			 * Append CSS for the email being sent to the customer
			 */
			add_action( 'yith_gift_cards_template_before_add_to_cart_form', array( $this, 'append_css_files' ) );

			/**
			 * Add taxonomy and assign it to gift card products
			 */
			add_action( 'woocommerce_after_register_taxonomy', array( $this, 'create_gift_cards_category' ) );

			/**
			 * Remove the view button in the gift card taxonomy
			 */
			add_filter( 'giftcard-category_row_actions', array( $this, 'ywgc_taxonomy_remove_view_row_actions' ), 10, 1 );

			add_filter( 'yith_ywgc_get_product_instance', array( $this, 'get_product_instance' ), 10, 2 );

			/**
			 * Select the date format option
			 */
			add_filter( 'yith_wcgc_date_format', array( $this, 'yith_ywgc_date_format_callback' ) );

			/**
			 * Display the YITH Product Addons in the gift card template
			 */
			if ( defined( 'YITH_WAPO_PREMIUM' ) ) {
				add_action( 'yith_wcgc_template_after_code', array( $this, 'ywgc_display_product_addons' ) );
			}
		}

		/**
		 * Start
		 *
		 * @return void
		 */
		public function start() {

			if ( ! defined( 'YITH_YWGC_EXTENDED' ) ) {
				$this->modules(); // Modules need to be the first thing loaded, to handle Premium version correctly. Loaded also in the free for the blocked features.
			}

			if ( is_admin() || WC()->is_rest_api_request() ) {
				YITH_YWGC_Admin();
			}

			// Init the backend.
			$this->backend = YITH_YWGC_Backend();

			// Init the frontend.
			$this->frontend = YITH_YWGC_Frontend();

			YITH_YWGC_Cart_Checkout();
			YITH_YWGC_Emails();
			YITH_YWGC_Shortcodes();
			YITH_YWGC_Category_Taxonomy();
		}

		/**
		 *  Execute all the operation need when the plugin init
		 */
		public function on_plugin_init() {
			$this->init_post_type();

			$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

			if ( is_admin() && ! $is_ajax ) {
				$this->init_metabox();
			}
		}

		/**
		 * Return the Modules class instance.
		 *
		 * @return YITH_YWGC_Modules
		 */
		public function modules() {
			return YITH_YWGC_Modules::get_instance();
		}

		/**
		 * Register the custom post type
		 */
		public function init_post_type() {
			/**
			 * APPLY_FILTERS: yith_wcgc_show_in_menu_cpt
			 *
			 * Filter to show or not the gift card dashboard in the WordPress menu.
			 *
			 * @param bool true to show it, false to hide it
			 *
			 * @return bool
			 */
			$args = array(
				'labels'          => array(
					'name'               => _x( 'All Gift Cards', 'post type general name', 'yith-woocommerce-gift-cards' ),
					'singular_name'      => _x( 'Gift Card', 'post type singular name', 'yith-woocommerce-gift-cards' ),
					'menu_name'          => _x( 'Gift Cards', 'admin menu', 'yith-woocommerce-gift-cards' ),
					'name_admin_bar'     => _x( 'Gift Card', 'add new on admin bar', 'yith-woocommerce-gift-cards' ),
					'add_new'            => _x( 'Create Code', 'admin menu item', 'yith-woocommerce-gift-cards' ),
					'add_new_item'       => esc_html__( 'Create Gift Card Code', 'yith-woocommerce-gift-cards' ),
					'new_item'           => esc_html__( 'New Gift Card', 'yith-woocommerce-gift-cards' ),
					'edit_item'          => esc_html__( 'Edit Gift Card', 'yith-woocommerce-gift-cards' ),
					'view_item'          => esc_html__( 'View Gift Card', 'yith-woocommerce-gift-cards' ),
					'all_items'          => esc_html__( 'All gift cards', 'yith-woocommerce-gift-cards' ),
					'search_items'       => esc_html__( 'Search gift cards', 'yith-woocommerce-gift-cards' ),
					'parent_item_colon'  => esc_html__( 'Parent gift cards:', 'yith-woocommerce-gift-cards' ),
					'not_found'          => esc_html__( 'No gift cards found.', 'yith-woocommerce-gift-cards' ),
					'not_found_in_trash' => esc_html__( 'No gift cards found in Trash.', 'yith-woocommerce-gift-cards' ),
				),
				'label'           => esc_html__( 'Gift Cards', 'yith-woocommerce-gift-cards' ),
				'description'     => esc_html__( 'Gift Cards', 'yith-woocommerce-gift-cards' ),
				'supports'        => array( 'title' ),
				'hierarchical'    => false,
				'capability_type' => 'product',
				'capabilities'    => array(
					'delete_post'  => 'edit_posts',
					'delete_posts' => 'edit_posts',
				),
				'public'          => false,
				'show_in_menu'    => apply_filters( 'yith_wcgc_show_in_menu_cpt', false ),
				'show_ui'         => true,
				'menu_position'   => 9,
				'can_export'      => true,
				'has_archive'     => false,
				'menu_icon'       => 'dashicons-clipboard',
				'query_var'       => false,
			);

			// Registering your Custom Post Type.
			register_post_type( YWGC_CUSTOM_POST_TYPE_NAME, $args );
		}

		/**
		 * Init_metabox
		 *
		 * @return void
		 */
		public function init_metabox() {

			$args = array(
				'label'    => esc_html__( 'Gift card detail', 'yith-woocommerce-gift-cards' ),
				'pages'    => YWGC_CUSTOM_POST_TYPE_NAME,
				'class'    => yith_set_wrapper_class(),
				'context'  => 'normal',
				'priority' => 'high',
				'tabs'     => array(
					'General' => array(
						'label'  => '',
						'fields' => apply_filters(
							'yith_ywgc_gift_card_instance_metabox_custom_fields',
							array(

								YITH_YWGC_Gift_Card::META_AMOUNT_TOTAL => array(
									'label'   => esc_html__( 'Amount', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The gift card amount', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
								),
								YITH_YWGC_Gift_Card::META_BALANCE_TOTAL => array(
									'label'   => esc_html__( 'Current balance', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The current amount available for the customer', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
								),
								'_ywgc_is_digital'     => array(
									'label'   => esc_html__( 'Virtual', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'Check if the gift card will be sent via email. Leave it unchecked to make this work as a physical gift card', 'yith-woocommerce-gift-cards' ),
									'type'    => 'onoff',
									'private' => false,
									'std'     => '',
								),
								'_ywgc_recipient'      => array(
									'label'   => esc_html__( 'Recipient\'s email', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The email address of the digital gift card recipient', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),
								'_ywgc_sender_name'    => array(
									'label'   => esc_html__( 'Sender\'s name', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The name of the digital gift card sender, if any', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
									'css'     => 'width: 80px;',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),
								'_ywgc_recipient_name' => array(
									'label'   => esc_html__( 'Recipient\'s name', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The name of the digital gift card recipient', 'yith-woocommerce-gift-cards' ),
									'type'    => 'text',
									'private' => false,
									'std'     => '',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),
								'_ywgc_message'        => array(
									'label'   => esc_html__( 'Message', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'The message attached to the gift card', 'yith-woocommerce-gift-cards' ),
									'type'    => 'textarea',
									'private' => false,
									'std'     => '',
									'deps'    => array(
										'ids'    => '_ywgc_is_digital',
										'values' => 'yes',
									),
								),

								'_ywgc_internal_notes' => array(
									'label'   => esc_html__( 'Internal notes', 'yith-woocommerce-gift-cards' ),
									'desc'    => esc_html__( 'Enter your notes here. This will only be visible to the admin', 'yith-woocommerce-gift-cards' ),
									'type'    => 'textarea',
									'private' => false,
									'std'     => '',
								),

							)
						),
					),
				),
			);

			$metabox = YIT_Metabox( 'yith-ywgc-gift-card-options-metabox' );
			$metabox->init( $args );

		}

		/**
		 * Declare support for WooCommerce features.
		 *
		 * @since 4.0.0
		 */
		public function declare_wc_features_support() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', YITH_YWGC_INIT, true );
			}
		}

		/**
		 * Current_user_can_create
		 *
		 * @return bool
		 */
		public function current_user_can_create() {
			/**
			 * APPLY_FILTERS: ywgc_can_create_gift_card
			 *
			 * Filter if current user can create a gift card.
			 *
			 * @param bool true if the current user can create a gift card, false if not
			 *
			 * @return bool
			 */
			return apply_filters( 'ywgc_can_create_gift_card', true );
		}

		/**
		 * Retrieve a gift card product instance from the gift card code
		 *
		 * @param string $code the gift card code to search for.
		 *
		 * @return YITH_YWGC_Gift_Card
		 * @since  1.0.0
		 */
		public function get_gift_card_by_code( $code ) {
			$args = array( 'gift_card_number' => $code );

			return new YITH_YWGC_Gift_Card( $args );
		}

		/**
		 * Generate a new gift card code
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function generate_gift_card_code() {

			// Create a new gift card number.
			$numeric_code     = (string) wp_rand( 99999999, mt_getrandmax() );
			$numeric_code_len = strlen( $numeric_code );

			/**
			 * APPLY_FILTERS: ywgc_random_generate_gift_card_code
			 *
			 * Filter the random generation of the gift card code.
			 *
			 * @param string the code randomly generated
			 *
			 * @return string
			 */
			$code        = apply_filters( 'ywgc_random_generate_gift_card_code', strtoupper( sha1( uniqid( wp_rand() ) ) ) );
			$code_len    = strlen( $code );
			$pattern     = get_option( 'ywgc_code_pattern', '****-****-****-****' );
			$pattern_len = strlen( $pattern );

			for ( $i = 0; $i < $pattern_len; $i++ ) {
				if ( '*' === $pattern[ $i ] ) {
					// replace all '*'s with one letter from the unique $code generated.
					$pattern[ $i ] = $code[ $i % $code_len ];
				} elseif ( 'D' === $pattern[ $i ] ) {
					// replace all 'D's with one digit from the unique integer $numeric_code generated.
					$pattern[ $i ] = $numeric_code[ $i % $numeric_code_len ];
				}
			}

			return $pattern;
		}

		/**
		 * Init an array of plugin emails
		 *
		 * @since  1.5.0
		 */
		public function init_plugin_emails_array() {
			/**
			 * APPLY_FILTERS: yith_ywgc_plugin_emails_array
			 *
			 * Filters list of allowed email types managed by the plugin
			 *
			 * @param array $emails List of email types
			 *
			 * @return array
			 */
			$this->emails = apply_filters(
				'yith_ywgc_plugin_emails_array',
				array(
					'ywgc-email-send-gift-card',
					'ywgc-email-delivered-gift-card',
					'ywgc-email-notify-customer',
				)
			);
		}

		/**
		 * Get plugin emails array
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_emails() {
			return $this->emails;
		}

		/**
		 * Load Plugin Framework
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Including the GDRP
		 */
		public function load_privacy() {

			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once YITH_YWGC_DIR . 'includes/class-yith-woocommerce-gift-cards-privacy.php';
			}

		}

		/**
		 * Register all the custom post statuses of gift cards
		 *
		 * @since  1.0.0
		 */
		public function register_custom_post_statuses() {

			register_post_status(
				YITH_YWGC_Gift_Card::STATUS_DISABLED,
				array(
					'label'                     => esc_html__( 'Disabled', 'yith-woocommerce-gift-cards' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'post_type'                 => array( 'gift_card' ),
					'label_count'               => _n_noop( esc_html__( 'Disabled', 'yith-woocommerce-gift-cards' ) . '<span class="count"> (%s)</span>', esc_html__( 'Disabled', 'yith-woocommerce-gift-cards' ) . ' <span class="count"> (%s)</span>' ),
				)
			);

			register_post_status(
				YITH_YWGC_Gift_Card::STATUS_DISMISSED,
				array(
					'label'                     => esc_html__( 'Dismissed', 'yith-woocommerce-gift-cards' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'post_type'                 => array( 'gift_card' ),
					'label_count'               => _n_noop( esc_html__( 'Dismissed', 'yith-woocommerce-gift-cards' ) . '<span class="count"> (%s)</span>', esc_html__( 'Dismissed', 'yith-woocommerce-gift-cards' ) . ' <span class="count"> (%s)</span>' ),
				)
			);

			register_post_status(
				YITH_YWGC_Gift_Card::STATUS_CODE_NOT_VALID,
				array(
					'label'                     => esc_html__( 'Code not valid', 'yith-woocommerce-gift-cards' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'post_type'                 => array( 'gift_card' ),
					'label_count'               => _n_noop( esc_html__( 'Code not valid', 'yith-woocommerce-gift-cards' ) . '<span class="count"> (%s)</span>', esc_html__( 'Code not valid', 'yith-woocommerce-gift-cards' ) . ' <span class="count"> (%s)</span>' ),
				)
			);

			register_post_status(
				YITH_YWGC_Gift_Card::STATUS_PRE_PRINTED,
				array(
					'label'                     => esc_html__( 'Pre-Printed', 'yith-woocommerce-gift-cards' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => false,
					'show_in_admin_status_list' => true,
					'post_type'                 => array( 'gift_card' ),
					'label_count'               => _n_noop( esc_html__( 'Pre-Printed', 'yith-woocommerce-gift-cards' ) . '<span class="count"> (%s)</span>', esc_html__( 'Pre-Printed', 'yith-woocommerce-gift-cards' ) . ' <span class="count"> (%s)</span>' ),
				)
			);
		}

		/**
		 * Add an option to let the admin set the gift card as a physical good or digital goods.
		 *
		 * @param array $array
		 *
		 * @return mixed
		 * @since  1.0.0
		 */
		public function add_type_option( $array ) {
			if ( isset( $array['virtual'] ) ) {
				$css_class     = $array['virtual']['wrapper_class'];
				$add_css_class = 'show_if_gift-card';
				$class         = empty( $css_class ) ? $add_css_class : $css_class .= ' ' . $add_css_class;

				$array['virtual']['wrapper_class'] = $class;
			}

			return $array;
		}

		/**
		 * Append CSS for the email being sent to the customer
		 */
		public function append_css_files() {
			YITH_YWGC()->frontend->enqueue_frontend_style();
		}

		/**
		 * Create_gift_cards_category
		 *
		 * Register new taxonomy which applies to attachments.
		 *
		 * @return void
		 */
		public function create_gift_cards_category() {

			$labels = array(
				'name'              => esc_html__( 'Gift card categories', 'yith-woocommerce-gift-cards' ),
				'singular_name'     => esc_html__( 'Gift card category', 'yith-woocommerce-gift-cards' ),
				'search_items'      => esc_html__( 'Search categories', 'yith-woocommerce-gift-cards' ),
				'all_items'         => esc_html__( 'All categories', 'yith-woocommerce-gift-cards' ),
				'parent_item'       => esc_html__( 'Parent category', 'yith-woocommerce-gift-cards' ),
				'parent_item_colon' => esc_html__( 'Parent category:', 'yith-woocommerce-gift-cards' ),
				'edit_item'         => esc_html__( 'Edit category', 'yith-woocommerce-gift-cards' ),
				'update_item'       => esc_html__( 'Update gift card category', 'yith-woocommerce-gift-cards' ),
				'add_new_item'      => esc_html__( 'Add new category', 'yith-woocommerce-gift-cards' ),
				'new_item_name'     => esc_html__( 'New category name', 'yith-woocommerce-gift-cards' ),
				'menu_name'         => esc_html__( 'Gift card category', 'yith-woocommerce-gift-cards' ),
			);

			$args = array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'query_var'         => true,
				'rewrite'           => true,
				'show_admin_column' => true,
				'show_in_menu'      => false, // hide in the WordPress dashboard.
				'show_ui'           => true,
				'public'            => true,
				'show_in_rest'      => true,
			);

			register_taxonomy( YWGC_CATEGORY_TAXONOMY, array( 'attachment', 'product' ), $args );

			if ( ! term_exists( 'none', YWGC_CATEGORY_TAXONOMY ) ) {
				wp_insert_term(
					__( 'None', 'yith-woocommerce-gift-cards' ),
					YWGC_CATEGORY_TAXONOMY,
					array(
						'description' => __( 'Select this category in your gift card product if you do not want to display images in your gift card gallery', 'yith-woocommerce-gift-cards' ),
						'slug'        => 'none',
					)
				);
			}

			if ( ! term_exists( 'all', YWGC_CATEGORY_TAXONOMY ) ) {
				wp_insert_term(
					__( 'All', 'yith-woocommerce-gift-cards' ),
					YWGC_CATEGORY_TAXONOMY,
					array(
						'description' => __( 'Select this category in your gift card product if you want to display all the images categories in your gift card gallery', 'yith-woocommerce-gift-cards' ),
						'slug'        => 'all',
					)
				);
			}
		}

		/**
		 * Ywgc_taxonomy_remove_view_row_actions
		 *
		 * Remove the view button in the gift card taxonomy
		 *
		 * @param  mixed $actions actions.
		 * @return actions
		 */
		public function ywgc_taxonomy_remove_view_row_actions( $actions ) {

			unset( $actions['view'] );
			return $actions;
		}

		/**
		 * Retrieve the product instance
		 *
		 * @param WC_Product_Gift_Card $product product.
		 *
		 * @return null|WC_Product
		 */
		public function get_product_instance( $product ) {

			global $sitepress;

			if ( $sitepress ) {
				$_wcml_settings = get_option( '_wcml_settings' );
				if ( isset( $_wcml_settings['trnsl_interface'] ) && '1' === $_wcml_settings['trnsl_interface'] ) {
					$product_id = $product->get_id();

					if ( $product_id ) {
						$id = yit_wpml_object_id( $product_id, 'product', true, $sitepress->get_default_language() );

						if ( $id !== $product_id ) {
							$product = wc_get_product( $id );
						}
					}
				}
			}

			return $product;
		}

		/**
		 * Add option select the date format
		 *
		 * @return string
		 * @since  2.0.5
		 */
		public function yith_ywgc_date_format_callback() {

			$date_format_in_js = get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' );

			$js_to_php_date_format = array(
				'd'  => 'j',
				'dd' => 'd',
				'o'  => 'z',
				'D'  => 'D',
				'DD' => 'l',
				'm'  => 'n',
				'mm' => 'm',
				'M'  => 'M',
				'MM' => 'F',
				'y'  => 'y',
				'yy' => 'Y',
			);

			$date_format_in_php = strtr( $date_format_in_js, $js_to_php_date_format );

			return $date_format_in_php;
		}

		/**
		 * Display the YITH Product Addons in the gift card template
		 *
		 * @param object $gift_card Gift card object.
		 */
		public function ywgc_display_product_addons( $gift_card ) {
			$gift_card->order_item_id = get_post_meta( $gift_card->ID, '_ywgc_order_item_id', true );

			if ( isset( $gift_card->order_item_id ) ) {
				$item_id = $gift_card->order_item_id;

				$addons_array = wc_get_order_item_meta( $item_id, '_ywapo_meta_data', true );

				if ( is_array( $addons_array ) && ! empty( $addons_array ) ) {
					foreach ( $addons_array as $addon ) {
						if ( isset( $addon['name'] ) && isset( $addon['value'] ) ) {
							?>
							<tr><td colspan="2" ><?php echo wp_kses_post( $addon['name'] . ': ' . $addon['value'] ); ?></td></tr>
							<?php
						} else {
							foreach ( $addon as $key => $value ) {
								$label       = $value['display_label'] ?? '';
								$addon_value = $value['display_value'] ?? '';

								?>
								<tr><td colspan="2" ><b><?php echo ( ! empty( $label ) ? wp_kses_post( $label ) . ': ' : '' ) ?></b><?php echo wp_kses_post( $addon_value ); ?></td></tr>
								<?php
							}
						}
					}
				}
			}
		}

		/**
		 * Getter option mandatory recipient
		 *
		 * @return bool
		 * @since  2.2.6
		 */
		public function mandatory_recipient() {

			return ( 'yes' === get_option( 'ywgc_recipient_mandatory', 'no' ) );
		}

		/**
		 * Retrieve if the gift cards should be updated on order refunded
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function change_status_on_refund() {
			return $this->disable_on_refund() || $this->dismiss_on_refund();
		}

		/**
		 * Retrieve if the gift cards should be updated on order cancelled
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function change_status_on_cancelled() {
			return $this->disable_on_cancelled() || $this->dismiss_on_cancelled();
		}

		/**
		 * Retrieve if a gift card should be set as dismissed if an order change its status
		 * to refunded
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function dismiss_on_refund() {
			return 'dismiss' === $this->order_refunded_action();
		}

		/**
		 * Retrieve if a gift card should be set as disabled if an order change its status
		 * to refunded
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function disable_on_refund() {
			return 'disable' === $this->order_refunded_action();
		}

		/**
		 * Retrieve if a gift card should be set as dismissed if an order change its status
		 * to cancelled
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function dismiss_on_cancelled() {
			return 'dismiss' === $this->order_cancelled_action();
		}

		/**
		 * Retrieve if a gift card should be set as disabled if an order change its status
		 * to cancelled
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function disable_on_cancelled() {
			return 'disable' === $this->order_cancelled_action();
		}

		/**
		 * Retrieve the image to be used as a main image for the gift card
		 *
		 * @param WC_product $product the product object.
		 *
		 * @return string
		 */
		public function get_header_image_for_product( $product ) {
			$header_image_url = '';

			if ( $product ) {

				$product_id = yit_get_product_id( $product );
				if ( $product instanceof WC_Product_Gift_Card ) {
					$header_image_url = $product->get_manual_header_image();
				}

				if ( ( empty( $header_image_url ) ) && has_post_thumbnail( $product_id ) ) {
					$image            = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), apply_filters( 'ywgc_email_image_size', 'full' ) );
					$header_image_url = $image[0];
				}
			}
			return $header_image_url;
		}

		/**
		 * Get_default_header_image
		 *
		 * @return string
		 */
		public function get_default_header_image() {

			$default_header_image_url = get_option( 'ywgc_gift_card_header_url', YITH_YWGC_ASSETS_IMAGES_URL . 'default-giftcard-main-image.jpg' );

			return $default_header_image_url ? $default_header_image_url : YITH_YWGC_ASSETS_IMAGES_URL . 'default-giftcard-main-image.jpg';
		}

		/**
		 * Retrieve the default image, configured from the plugin settings, to be used as gift card header image
		 *
		 * @param YITH_YWGC_Gift_Card|WC_Product $obj the product object.
		 *
		 * @return mixed|string|void
		 */
		public function get_header_image( $obj = null ) {

			$header_image_url = '';
			if ( $obj instanceof YITH_YWGC_Gift_Card ) {

				if ( $obj->has_custom_design ) {

					switch ( $obj->design_type ) {
						case 'custom-modal':
							$header_image_url = $obj->design;
							break;
						case 'custom':
							$header_image_url = YITH_YWGC_SAVE_URL . '/' . $obj->design;
							break;
						case 'template':
							$header_image_url = wp_get_attachment_url( $obj->design );
							break;
						case 'default':
							$product          = wc_get_product( $obj->product_id );
							$header_image_url = $this->get_header_image_for_product( $product );
							break;
					}
				}
			}

			if ( ! $header_image_url ) {
				$header_image_url = $this->get_default_header_image();
			}

			return $header_image_url;
		}

		/**
		 * Output a gift cards template filled with real data or with sample data to start editing it
		 * on product page
		 *
		 * @param WC_Product|YITH_YWGC_Gift_Card $object object.
		 * @param string                         $context context.
		 * @param  mixed                          $case case.
		 * @return void
		 */
		public function preview_digital_gift_cards( $object, $context = 'shop', $case = 'recipient' ) {

			if ( $object instanceof WC_Product ) {

				$header_image_url = $this->get_header_image( $object );

				// check if the admin set a default image for gift card.
				$amount = 0;
				if ( $object instanceof WC_Product_Simple || $object instanceof WC_Product_Variable || $object instanceof WC_Product_Yith_Bundle || $object instanceof WC_Product_Booking ) {
					$amount = yit_get_display_price( $object );
				}

				$amount          = wc_format_decimal( $amount );
				$formatted_price = wc_price( $amount );

				$gift_card_code = 'xxxx-xxxx-xxxx-xxxx';
				$message        = apply_filters( 'yith_ywgc_gift_card_template_message_text', esc_html__( 'Your message will show up here…', 'yith-woocommerce-gift-cards' ) );
			} elseif ( $object instanceof YITH_YWGC_Gift_Card ) {

				$header_image_url = $this->get_header_image( $object );
				$amount           = $object->total_amount;
				$formatted_price  = apply_filters( 'yith_ywgc_gift_card_template_amount', wc_price( $amount ), $object, $amount );
				$gift_card_code   = $object->gift_card_number;
				$message          = $object->message;
				$expiration_date  = ! is_numeric( $object->expiration ) ? strtotime( $object->expiration ) : $object->expiration;
			}

			// Checking if the image sent is a product image, if so then we set $header_image_url with correct url.
			if ( isset( $header_image_url ) ) {
				if ( strpos( $header_image_url, '-yith_wc_gift_card_premium_separator_ywgc_template_design-' ) !== false ) {
					$array_header_image_url = explode( '-yith_wc_gift_card_premium_separator_ywgc_template_design-', $header_image_url );
					$header_image_url       = $array_header_image_url['1'];
				}
			}

			$product_id = isset( $object->product_id ) ? $object->product_id : '';

			$args = array(
				'company_logo_url'         => ( 'yes' === get_option( 'ywgc_shop_logo_on_gift_card', 'no' ) ) ? get_option( 'ywgc_shop_logo_url', YITH_YWGC_ASSETS_IMAGES_URL . 'default-giftcard-main-image.png' ) : '',
				'header_image_url'         => $header_image_url,
				'default_header_image_url' => $this->get_default_header_image(),
				'formatted_price'          => $formatted_price,
				'gift_card_code'           => $gift_card_code,
				'message'                  => $message,
				'context'                  => $context,
				'object'                   => $object,
				'product_id'               => $product_id,
				'case'                     => $case,
				'date_format'              => apply_filters( 'yith_wcgc_date_format', 'Y-m-d' ),
				'expiration_date'          => $expiration_date,
			);

			wc_get_template( 'yith-gift-cards/ywgc-gift-card-template.php', $args, '', trailingslashit( YITH_YWGC_TEMPLATES_DIR ) );

		}

		/**
		 * Perform some check to a gift card that should be applied to the cart
		 * and retrieve a message code
		 *
		 * @param YITH_YWGC_Gift_Card $gift the gift card object.
		 * @param bool                $remove
		 *
		 * @return bool
		 */
		public function check_gift_card( $gift, $remove = false ) {

			$err_code = '';

			/**
			 * APPLY_FILTERS: yith_wcgc_deny_usage_of_gift_cards_to_purchase_gift_cards
			 *
			 * Filter the condition to deny or not the usage of gift cards codes to purchase gift card products.
			 *
			 * @param bool true to deny it, false for not. Default: false
			 *
			 * @return bool
			 */

			if ( ! is_object( $gift ) || ! $gift->exists() ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_NOT_EXIST;
			} elseif ( ! $gift->is_owner( get_current_user_id() ) ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_NOT_YOURS;
			} elseif ( isset( WC()->cart->applied_gift_cards[ $gift->get_code() ] ) ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_ALREADY_APPLIED;
			} elseif ( $gift->is_expired() ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_EXPIRED;
			} elseif ( $gift->is_disabled() ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_DISABLED;
			} elseif ( $gift->is_dismissed() ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_DISMISSED;
			} elseif ( apply_filters( 'yith_wcgc_deny_usage_of_gift_cards_to_purchase_gift_cards', false ) ) {

				$cart = WC()->cart->get_cart();

				foreach ( $cart as $cart_item_key => $cart_item ) {

					$product = $cart_item['data'];

					if ( $product instanceof WC_Product_Gift_Card ) {
						$err_code = YITH_YWGC_Gift_Card::GIFT_CARD_NOT_ALLOWED_FOR_PURCHASING_GIFT_CARD;
						break;
					}
				}
			}
			/**
			 * If the flag $remove is true and there is an error,
			 * the gift card will be removed from the cart, then we set the general
			 * error message here.
			 * */
			if ( $err_code && $remove ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_INVALID_REMOVED;
			}

			if ( ! is_object( $gift ) ) {
				wc_add_notice( esc_html__( 'This gift card code does not exist!', 'yith-woocommerce-gift-cards' ), 'error' );
				return false;
			}

			/**
			 * APPLY_FILTERS: yith_ywgc_check_gift_card
			 *
			 * Filter if the gift card have errors when applying it.
			 *
			 * @param string $err_code the error code
			 * @param object $gift the gift card object
			 *
			 * @return string
			 */
			$err_code = apply_filters( 'yith_ywgc_check_gift_card', $err_code, $gift );
			if ( $err_code ) {

				$err_msg = $gift->get_gift_card_error( $err_code );

				if ( $err_msg ) {
					wc_add_notice( $err_msg, 'error' );
				}

				return false;
			}

			if ( $gift->get_balance() <= 0 ) {
				$err_code = YITH_YWGC_Gift_Card::E_GIFT_CARD_ALREADY_APPLIED;
				$err_msg  = $gift->get_gift_card_error( $err_code );
				wc_add_notice( $err_msg, 'error' );

				return false;
			}

			if ( ! $remove ) {

				$ywgc_minimal_car_total = get_option( 'ywgc_minimal_car_total' );

				if ( WC()->cart->total < $ywgc_minimal_car_total ) {
					wc_add_notice( esc_html__( 'In order to use the gift card, the minimum total amount in the cart has to be ' . $ywgc_minimal_car_total . get_woocommerce_currency_symbol(), 'yith-woocommerce-gift-cards' ), 'error' );

					return false;
				}
			}

			$items = WC()->cart->get_cart();
			foreach ( $items as $cart_item_key => $values ) {
				$product = $values['data'];

				/**
				 * APPLY_FILTERS: yith_ywgc_check_subscription_product_on_cart
				 *
				 * Filter the condition to allow to apply the gift card codes to WC_Subscriptions_Product products in the cart.
				 *
				 * @param bool true to now allow it and display an error in the cart, false to allow it. Default: true
				 *
				 * @return bool
				 */
				if ( apply_filters( 'yith_ywgc_check_subscription_product_on_cart', true ) && class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product ) ) {
					wc_add_notice( esc_html__( 'It is not possible to add any gift card if the cart contains a subscription-based product', 'yith-woocommerce-gift-cards' ), 'error' );

					return false;
				}
			}

			$cart_coupons = WC()->cart->get_coupons();

			foreach ( $cart_coupons as $coupon ) {

				$coupon_code = strtoupper( $coupon->get_code() );
				$gift_code   = strtoupper( $gift->get_code() );

				if ( $gift_code === $coupon_code ) {
					wc_add_notice( esc_html__( 'This code is already applied', 'yith-woocommerce-gift-cards' ), 'error' );

					return false;
				}
			}

			/**
			 * APPLY_FILTERS: yith_ywgc_check_gift_card_return
			 *
			 * Filter the gift card code response when applied to the cart. It allows to add conditions based on the products in the cart.
			 *
			 * @param bool true to apply the gift card code, false to not
			 * @param object $gift the gift card applied object
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_ywgc_check_gift_card_return', true, $gift );
		}

	}
}
