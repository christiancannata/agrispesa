<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Class YITH_YWGC_Backend
 *
 * @package YITH\GiftCards\Includes\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YITH_YWGC_Backend' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Backend
	 *
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_YWGC_Backend {

		const YWGC_GIFT_CARD_LAST_VIEWED_ID = 'ywgc_last_viewed';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var instance instance.
		 */
		protected static $instance;

		/**
		 * Rc_active
		 * Race condition active.
		 *
		 * @since 2.0.3
		 * @var rc_active rc_active.
		 */
		protected static $rc_active;

		/**
		 * An array of processing orders for current instance. Used for avoid duplicate
		 *
		 * @var array
		 */
		protected $processing_order = array();

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
		protected function __construct() {

			/**
			 * Enqueue scripts and styles
			 */
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_files' ) );

			/**
			 * Add the "Gift card" type to product type list
			 */
			add_filter( 'product_type_selector', array( $this, 'add_gift_card_product_type' ) );

			/**
			 * * Save gift card data when a product of type "gift card" is saved
			 */
			add_action( 'save_post', array( $this, 'save_gift_card' ), 1, 2 );

			/**
			 * Hide some item meta from product edit page
			 */
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_item_meta' ) );

			/**
			 * Append gift card amount generation controls to general tab on product page
			 */
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'show_gift_card_product_settings' ) );

			/**
			 * Generate a valid card number for every gift card product in the order
			 */
			add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 10, 3 );

			add_action( 'woocommerce_before_order_itemmeta', array( $this, 'show_gift_card_code_on_order_item' ), 10, 3 );

			/**
			 * Set the CSS class 'show_if_gift-card in tax section
			 */
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'show_tax_class_for_gift_cards' ) );

			/**
			 * Custom condition to create gift card on cash on delivery only on complete status
			 */
			add_filter( 'ywgc_custom_condition_to_create_gift_card', array( $this, 'ywgc_custom_condition_to_create_gift_card_call_back' ), 10, 2 );

			add_action( 'save_post_product', array( $this, 'set_gift_card_category_to_product' ) );

			/**
			 * Set the CSS class 'show_if_gift-card in 'sold indidually' section
			 */
			add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'show_sold_individually_for_gift_cards' ) );

			/**
			 * Manage CSS class for the gift cards table rows
			 */
			add_filter( 'post_class', array( $this, 'add_cpt_table_class' ), 10, 3 );

			add_action( 'init', array( $this, 'redirect_gift_cards_link' ) );

			add_action( 'load-upload.php', array( $this, 'set_gift_card_category_to_media' ) );

			add_action( 'edited_term_taxonomy', array( $this, 'update_taxonomy_count' ), 10, 2 );

			/**
			 * Show inventory tab in product tabs
			 */
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'show_inventory_tab' ) );

			/**
			 * Show gift cards code and amount in order's totals section, in edit order page
			 */
			add_action( 'woocommerce_admin_order_totals_after_tax', array( $this, 'show_gift_cards_total_before_order_totals' ) );

			/**
			 * Add filters on the Gift Card Post Type page
			 */
			add_action( 'pre_get_posts', array( $this, 'filter_gift_card_page_query' ) );

			/**
			 * Filter display order item meta key to show
			 */
			add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'show_as_string_order_item_meta_key' ), 10, 1 );

			/**
			 * Filter display order item meta value to show
			 */
			add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'show_formatted_date' ), 10, 3 );

			add_action( 'woocommerce_order_status_changed', array( $this, 'update_gift_card_amount_on_order_status_change' ), 10, 4 );

			/**
			 * Recalculate order totals on save order items (in order to show always the correct total for the order)
			 */
			add_action( 'woocommerce_order_before_calculate_totals', array( $this, 'update_totals_on_save_order_items' ), 10, 2 );

			/**
			 * Show/Hide product metaboxes for not Gift Card products
			 */
			add_filter( 'postbox_classes_product_giftcard-categorydiv', array( $this, 'show_postbox_only_for_gift_card_products' ) );
			add_filter( 'postbox_classes_product_woocommerce-product-images', array( $this, 'hide_postbox_only_for_gift_card_products' ) );

			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
			add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

			// Remove Yoast SEO metabox from gift card categories.
			add_action( 'admin_init', array( $this, 'disable_yoast_seo_metabox_in_gift_card_categories' ) );
		}

		/**
		 * Show the gift card code under the order item, in the order admin page
		 *
		 * @param int        $item_id item_id.
		 * @param array      $item item.
		 * @param WC_product $_product _product.
		 *
		 * @since  1.0.0
		 */
		public function show_gift_card_code_on_order_item( $item_id, $item, $_product ) {

			$gift_ids = ywgc_get_order_item_giftcards( $item_id );

			if ( empty( $gift_ids ) ) {
				return;
			}

			foreach ( $gift_ids as $gift_id ) {

				$gc = new YITH_YWGC_Gift_Card( array( 'ID' => $gift_id ) );

				?>
				<div>
					<span class="ywgc-gift-code-label"><?php esc_html_e( 'Gift card code: ', 'yith-woocommerce-gift-cards' ); ?></span>
					<a href="<?php echo esc_url( admin_url( 'edit.php?s=' . $gc->get_code() . '&post_type=gift_card&mode=list' ) ); ?>" class="ywgc-card-code"><?php echo wp_kses( $gc->get_code(), 'post' ); ?></a>
				</div>
				<?php
			}
		}

		/**
		 * Enqueue_backend_files
		 * Enqueue scripts on administration comment page
		 *
		 * @param  mixed $hook hook.
		 * @return void
		 */
		public function enqueue_backend_files( $hook ) {

			$screen = get_current_screen();

			// Enqueue style and script for the edit-gift_card screen id.
			if ( 'edit-gift_card' === $screen->id ) {

				// When viewing the gift card page, store the max id so all new gift cards will be notified next time.
				global $wpdb;
				$last_id = $wpdb->get_var( $wpdb->prepare( "SELECT max(id) FROM {$wpdb->prefix}posts WHERE post_type = %s", YWGC_CUSTOM_POST_TYPE_NAME ) );//phpcs:ignore --Direct call to Database is discouraged.
				update_option( self::YWGC_GIFT_CARD_LAST_VIEWED_ID, $last_id );
			}

			if ( 'product' === $screen->id ) {
				wp_enqueue_style( 'yith-plugin-fw-fields' );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
			}

			$if_shop_order = function_exists( 'wc_get_page_screen_id' ) ? wc_get_page_screen_id( 'shop-order' ) === $screen->id : 'shop-order' === $screen->id;

			if ( is_admin() && ( in_array( $screen->id, array( 'product', 'edit-product' ), true ) ) || ( 'gift_card' === $screen->id ) || $if_shop_order || isset( $_REQUEST['page'] ) && 'yith_woocommerce_gift_cards_panel' === $_REQUEST['page'] || 'edit-gift_card' === $screen->id ) {//phpcs:ignore WordPress.Security.NonceVerification

				// Add style and scripts.
				wp_enqueue_style(
					'ywgc-backend-css',
					YITH_YWGC_ASSETS_URL . '/css/ywgc-backend.css',
					array(),
					YITH_YWGC_VERSION
				);

				wp_register_script(
					'ywgc-backend',
					YITH_YWGC_SCRIPT_URL . yit_load_js_file( 'ywgc-backend.js' ),
					array(
						'jquery',
						\YIT_Assets::wc_script_handle( 'wc-jquery-blockui' ),
					),
					YITH_YWGC_VERSION,
					true
				);

				$date_format = get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' );

				wp_localize_script(
					'ywgc-backend',
					'ywgc_data',
					array(
						'loader'                    => apply_filters( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
						'ajax_url'                  => admin_url( 'admin-ajax.php' ),
						'choose_image_text'         => esc_html__( 'Choose Image', 'yith-woocommerce-gift-cards' ),
						'date_format'               => $date_format,
						'save_email_settings_nonce' => wp_create_nonce( 'yith_ywgc_save_email_settings' ),
						'save_email_status_nonce'   => wp_create_nonce( 'yith_ywgc_save_email_status' ),
					)
				);

				wp_enqueue_script( 'ywgc-backend' );
			}

			if ( ( isset( $_REQUEST['page'] ) && 'yith_woocommerce_gift_cards_panel' === $_REQUEST['page'] ) || 'edit-giftcard-category' === $screen->id || 'product' === $screen->id || 'edit-gift_card' === $screen->id ) {//phpcs:ignore WordPress.Security.NonceVerification
				wp_enqueue_style(
					'ywgc_gift_cards_admin_panel_css',
					YITH_YWGC_ASSETS_URL . '/css/ywgc-gift-cards-admin-panel.css',
					array(),
					YITH_YWGC_VERSION
				);
			}

			if ( 'upload' === $screen->id ) {

				wp_register_script(
					'ywgc-categories',
					YITH_YWGC_SCRIPT_URL . yit_load_js_file( 'ywgc-categories.js' ),
					array(
						'jquery',
						\YIT_Assets::wc_script_handle( 'wc-jquery-blockui' ),
					),
					YITH_YWGC_VERSION,
					true
				);

				$categories1_id = 'categories1_id';
				$categories2_id = 'categories2_id';

				wp_localize_script(
					'ywgc-categories',
					'ywgc_data',
					array(
						'loader'                => apply_filters( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
						'ajax_url'              => admin_url( 'admin-ajax.php' ),
						'set_category_action'   => esc_html__( 'Set gift card category', 'yith-woocommerce-gift-cards' ),
						'unset_category_action' => esc_html__( 'Unset gift card category', 'yith-woocommerce-gift-cards' ),
						'categories1'           => $this->get_category_select( $categories1_id ),
						'categories1_id'        => $categories1_id,
						'categories2'           => $this->get_category_select( $categories2_id ),
						'categories2_id'        => $categories2_id,
					)
				);

				wp_enqueue_script( 'ywgc-categories' );
			}

			if ( 'edit-giftcard-category' === $screen->id ) {

				wp_enqueue_media();
				wp_register_script(
					'ywgc-media-button',
					YITH_YWGC_SCRIPT_URL . yit_load_js_file( 'ywgc-media-button.js' ),
					array(
						'jquery',
					),
					YITH_YWGC_VERSION,
					true
				);

				wp_localize_script(
					'ywgc-media-button',
					'ywgc_data',
					array(
						'upload_file_frame_title'  => esc_html__( 'Manage the Media library', 'yith-woocommerce-gift-cards' ),
						'upload_file_frame_button' => esc_html__( 'Done', 'yith-woocommerce-gift-cards' ),
					)
				);

				wp_enqueue_script( 'ywgc-media-button' );
			}

		}

		/**
		 * Get the gift card category select
		 *
		 * @param int $select_id the category select ID.
		 *
		 * @return string
		 */
		public function get_category_select( $select_id ) {
			$media_terms = get_terms( YWGC_CATEGORY_TAXONOMY, 'hide_empty=0' );

			$select = '<select id="' . $select_id . '" name="' . $select_id . '">';
			foreach ( $media_terms as $entry ) {
				$select .= '<option value="' . $entry->term_id . '">' . $entry->name . '</option>';
			}
			$select .= '</select>';

			return $select;

		}

		/**
		 * Add the "Gift card" type to product type list
		 *
		 * @param array $types current type array.
		 *
		 * @return mixed
		 * @since  1.0.0
		 */
		public function add_gift_card_product_type( $types ) {
			if ( YITH_YWGC()->current_user_can_create() ) {
				$types[ YWGC_GIFT_CARD_PRODUCT_TYPE ] = esc_html__( 'Gift card', 'yith-woocommerce-gift-cards' );
			}

			return $types;
		}

		/**
		 * Save gift card additional data
		 *
		 * @param int $product_id the product ID.
		 */
		public function save_gift_card_data( $product_id ) {

			$product = new WC_Product_Gift_Card( $product_id );

			/**
			 * Save custom gift card header image, if exists
			 */
			if ( isset( $_REQUEST['ywgc_product_image_id'] ) ) {//phpcs:ignore WordPress.Security.NonceVerification
				if ( intval( $_REQUEST['ywgc_product_image_id'] ) ) {//phpcs:ignore WordPress.Security.NonceVerification

					$product->set_header_image( $_REQUEST['ywgc_product_image_id'] );//phpcs:ignore
				} else {

					$product->unset_header_image();
				}
			}

			/**
			 * Save gift card settings about template design
			 */
			if ( isset( $_POST['template-design-mode'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification
				$product->set_design_status( $_POST['template-design-mode'] );//phpcs:ignore
			}

			/**
			 * Save gift card amounts
			 */
			$amounts = $_POST['gift-card-amounts'] ?? array();
			$amount  = $_POST['gift_card-amount'] ?? '';

			if ( '' !== $amount ) {
				$amounts[] = $amount;
			}

			$product->save_amounts( $amounts, '' );

		}


		/**
		 * Save gift card amount when a product is saved
		 *
		 * @param int    $post_id
		 * @param object $post
		 *
		 * @return mixed|void
		 */
		public function save_gift_card( $post_id, $post ) {

			$product = wc_get_product( $post_id );

			if ( ! is_object( $product ) ) {
				return;
			}

			if ( ! isset( $_POST['product-type'] ) || ( YWGC_GIFT_CARD_PRODUCT_TYPE !== $_POST['product-type'] ) ) {//phpcs:ignore WordPress.Security.NonceVerification
				return;
			}

			// verify this is not an auto save routine.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			/**
			 * Update gift card amounts
			 */
			$this->save_gift_card_data( $post_id );

			/**
			 * DO_ACTION: yith_gift_cards_after_product_save
			 *
			 * Allow actions after the gift card product is save.
			 *
			 * @param int $post_id the gift card ID
			 * @param object $post the post object
			 * @param object $product the product object
			 */
			do_action( 'yith_gift_cards_after_product_save', $post_id, $post, $product );
		}

		/**
		 * Hide some item meta from order edit page
		 *
		 * @param array $args args.
		 *
		 * @return array
		 */
		public function hide_item_meta( $args ) {
			$args[] = YWGC_META_GIFT_CARD_POST_ID;

			return $args;
		}

		/**
		 * Show controls on backend product page to let create the gift card price
		 */
		public function show_gift_card_product_settings() {

			if ( ! YITH_YWGC()->current_user_can_create() ) {
				return;
			}

			yith_ywgc_get_view( 'gift-cards-product-settings.php' );
		}

		/**
		 * Show the gift card amounts list
		 *
		 * @param int $product_id the product ID.
		 */
		public function show_gift_card_amount_list( $product_id ) {

			$gift_card = new WC_Product_Gift_Card( $product_id );

			if ( ! $gift_card->exists() ) {
				return;
			}

			$amounts = $gift_card->get_product_amounts();

			yith_ywgc_get_view( 'gift-cards-show-amount-list.php', compact( 'amounts' ) );

		}


		/**
		 * When the order is completed, generate a card number for every gift card product
		 *
		 * @param int|WC_Order $order The order which status is changing.
		 * @param string       $old_status Current order status.
		 * @param string       $new_status New order status.
		 *
		 * @throws Exception
		 */
		public function order_status_changed( $order, $old_status, $new_status ) {

			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}

			if ( ! $order || in_array( $order->get_id(), $this->processing_order, true ) ) {
				return;
			}

			$this->processing_order[] = $order->get_id();
			/**
			 * APPLY_FILTERS: yith_ywgc_generate_gift_card_on_order_status
			 *
			 * Filter the order statuses where the gift card will be generated.
			 *
			 * @param array the order statuses. Default: 'completed', 'processing'
			 *
			 * @return array
			 */
			$allowed_status = apply_filters(
				'yith_ywgc_generate_gift_card_on_order_status',
				array( 'completed', 'processing' )
			);

			if ( in_array( $new_status, $allowed_status, true ) ) {
				$this->generate_gift_card_for_order( $order );

				$used_gift_cards = $order->get_meta( '_ywgc_applied_gift_cards' );

				if ( isset( $used_gift_cards ) && ! empty( $used_gift_cards ) ) {
					$checkout_instance = YITH_YWGC_Cart_Checkout::get_instance();
					foreach ( $used_gift_cards as $gift_card_code => $value ) {
						$gift_card = YITH_YWGC()->get_gift_card_by_code( $gift_card_code );
					}
				}
			}
		}

		/**
		 * Generate the gift card code, if not yet generated
		 *
		 * @param WC_Order $order order.
		 *
		 * @throws Exception
		 * @since  1.0.0
		 */
		public function generate_gift_card_for_order( $order ) {
			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}

			/**
			 * APPLY_FILTERS: yith_gift_cards_generate_on_order_completed
			 *
			 * Filter the condition to generate the gift card when the order is completed.
			 *
			 * @param bool true to generate it when the order is completed, false for not
			 * @param object $order the order object
			 *
			 * @return bool
			 */
			if ( apply_filters( 'yith_gift_cards_generate_on_order_completed', true, $order ) ) {
				$this->create_gift_cards_for_order( $order );
			}
		}

		/**
		 * Custom condition
		 *
		 * @param WC_Order $order order.
		 * @return boolean
		 *
		 * @since  2.0.6
		 */
		public function ywgc_custom_condition_to_create_gift_card_call_back( $cond, $order ) {

			$gateway = wc_get_payment_gateway_by_order( $order );
			if ( $order->get_status() === 'processing' && is_object( $gateway ) && $gateway instanceof WC_Gateway_COD ) {
				return false;
			}

			return true;

		}

		/**
		 * Create_gift_cards_for_order
		 * Create the gift cards for the order
		 *
		 * @param WC_Order $order order.
		 *
		 * @return void
		 * @throws Exception
		 */
		public function create_gift_cards_for_order( $order ) {

			if ( ! apply_filters( 'ywgc_custom_condition_to_create_gift_card', true, $order ) ) {
				return;
			}

			foreach ( $order->get_items( 'line_item' ) as $order_item_id => $order_item_data ) {

				$product_id = $order_item_data['product_id'];
				$product    = wc_get_product( $product_id );

				// Skip all item that belong to product other than the gift card type.
				if ( ! $product instanceof WC_Product_Gift_Card ) {
					continue;
				}

				// Check if current product, of type gift card, has a previous gift card.
				// Code before creating another.
				$gift_ids = ywgc_get_order_item_giftcards( $order_item_id );
				if ( $gift_ids ) {
					continue;
				}

				if ( ! apply_filters( 'yith_ywgc_create_gift_card_for_order_item', true, $order, $order_item_id, $order_item_data ) ) {
					continue;
				}

				$order_id = $order->get_id();

				$line_subtotal     = apply_filters( 'yith_ywgc_line_subtotal', $order_item_data['line_subtotal'], $order_item_data, $order_id, $order_item_id );
				$line_subtotal_tax = apply_filters( 'yith_ywgc_line_subtotal_tax', $order_item_data['line_subtotal_tax'], $order_item_data, $order_id, $order_item_id );

				// Generate as many gift card code as the quantity bought.
				$quantity      = $order_item_data['qty'];
				$single_amount = (float) ( $line_subtotal / $quantity );
				$single_tax    = (float) ( $line_subtotal_tax / $quantity );

				$new_ids = array();

				$order_currency = version_compare( WC()->version, '3.0', '<' ) ? $order->get_order_currency() : $order->get_currency();

				$product_id = wc_get_order_item_meta( $order_item_id, '_ywgc_product_id' );
				$amount     = wc_get_order_item_meta( $order_item_id, '_ywgc_amount' );
				$is_digital = wc_get_order_item_meta( $order_item_id, '_ywgc_is_digital' );

				if ( $is_digital ) {
					$recipients        = wc_get_order_item_meta( $order_item_id, '_ywgc_recipients' );
					$recipient_count   = count( (array) $recipients );
					$sender            = wc_get_order_item_meta( $order_item_id, '_ywgc_sender_name' );
					$recipient_name    = wc_get_order_item_meta( $order_item_id, '_ywgc_recipient_name' );
					$message           = wc_get_order_item_meta( $order_item_id, '_ywgc_message' );
					$has_custom_design = wc_get_order_item_meta( $order_item_id, '_ywgc_has_custom_design' );
					$design_type       = wc_get_order_item_meta( $order_item_id, '_ywgc_design_type' );
				}

				for ( $i = 0; $i < $quantity; $i ++ ) {

					// Generate a gift card post type and save it.
					$gift_card = new YITH_YWGC_Gift_Card();

					$gift_card->product_id = $product_id;
					$gift_card->order_id   = $order_id;
					$gift_card->is_digital = $is_digital;

					if ( $gift_card->is_digital ) {
						$gift_card->sender_name       = $sender;
						$gift_card->recipient_name    = $recipient_name;
						$gift_card->message           = $message;
						$gift_card->has_custom_design = $has_custom_design;
						$gift_card->design_type       = $design_type;

						if ( $has_custom_design ) {
							$gift_card->design = wc_get_order_item_meta( $order_item_id, '_ywgc_design' );
						}

						/**
						 * If the user entered several recipient email addresses, one gift card
						 * for every recipient will be created and it will be the unique recipient for
						 * that email. If only one, or none if allowed, recipient email address was entered
						 * then create '$quantity' specular gift cards
						 */
						if ( ( 1 == $recipient_count ) && ! empty( $recipients[0] ) ) {//phpcs:ignore
							$gift_card->recipient = $recipients[0];
						} elseif ( ( $recipient_count > 1 ) && ! empty( $recipients[ $i ] ) ) {
							$gift_card->recipient = $recipients[ $i ];
						} else {
							/**
							 * Set the customer as the recipient of the gift card
							 */
							$gift_card->recipient = apply_filters( 'yith_ywgc_set_default_gift_card_recipient', $order->get_billing_email() );
						}
					}

					$attempts = 100;
					do {
						$code       = apply_filters( 'yith_wcgc_generated_code', YITH_YWGC()->generate_gift_card_code(), $order, $gift_card );
						$check_code = YITH_YWGC()->get_gift_card_by_code( $code );

						if ( is_object( $check_code ) && ! $check_code->ID ) {
							$gift_card->gift_card_number = $code;
							break;
						}
						$attempts --;
					} while ( $attempts > 0 );

					if ( ! $attempts ) {
						// Unable to find a unique code, the gift card need a manual code entered.
						$gift_card->set_as_code_not_valid();
					}

					$gift_card->total_amount = $single_amount + $single_tax;

					$gift_card->update_balance( $gift_card->total_amount );
					$gift_card->version  = YITH_YWGC_VERSION;
					$gift_card->currency = $order_currency;

					$gift_card->expiration = 0;

					do_action( 'yith_ywgc_before_gift_card_generation_save', $gift_card );

					$gift_card->save();

					do_action( 'yith_ywgc_after_gift_card_generation_save', $gift_card );

					update_post_meta( $gift_card->ID, '_ywgc_order_item_id', $order_item_id );

					// Save the gift card id.
					$new_ids[] = $gift_card->ID;

					// ...and send it now if it's not postdated
					if ( ( apply_filters( 'ywgc_send_gift_card_code_by_default', true, $gift_card ) ) || apply_filters( 'yith_wcgc_send_now_gift_card_to_custom_recipient', false, $gift_card ) ) {

						YITH_YWGC_Emails::get_instance()->send_gift_card_email( $gift_card );
					}
				}

				// save gift card Post ids on order item.
				ywgc_set_order_item_giftcards( $order_item_id, $new_ids );

			}
			if ( apply_filters( 'ywgc_apply_race_condition', false ) ) {
				$this->end_race_condition( $order->get_id() );
			}

		}

		/**
		 * Start race condition
		 *
		 * @param int $order_id
		 *
		 * @since  2.0.3
		 * @return bool
		 */
		public function start_race_condition( $order_id ) {

			global $wpdb;

			$ywgc_race_condition_uniqid = uniqid();

			$sql = "UPDATE {$wpdb->postmeta} pm1, {$wpdb->postmeta} pm2
                SET pm1.meta_value = 'yes',
                    pm2.meta_value = %s
                WHERE pm1.post_id = %d
                    AND pm1.meta_key = %s
                    AND pm1.meta_value != 'yes'
                    AND pm2.post_id = %d
                    AND pm2.meta_key = %s
                ";

			$this->rc_active = $wpdb->query(//phpcs:ignore --Direct call to Database is discouraged.
				$wpdb->prepare(
					$sql,
					$ywgc_race_condition_uniqid,
					$order_id,
					YWGC_RACE_CONDITION_BLOCKED,
					$order_id,
					YWGC_RACE_CONDITION_UNIQUID
				)
			);

			if ( $this->rc_active ) {

				$sub_sql = "SELECT meta_value FROM {$wpdb->postmeta}
                    WHERE post_id = %d
                    AND meta_key = %s
                ";

				$uniqid_result = $wpdb->get_results(//phpcs:ignore --Direct call to Database is discouraged.
					$wpdb->prepare(
						$sub_sql,
						$order_id,
						YWGC_RACE_CONDITION_UNIQUID
					)
				);

				if ( is_array( $uniqid_result ) && isset( $uniqid_result[0] ) && $uniqid_result[0]->meta_value !== $ywgc_race_condition_uniqid ) {
					return 0;
				}
			}

			return 1;
		}

		/**
		 * end race condition
		 *
		 * @param int order_id
		 *
		 * @since  2.0.3
		 */
		public function end_race_condition( $order_id ) {

			global $wpdb;

			if ( $this->rc_active ) {

				$sql = "UPDATE {$wpdb->postmeta}
                SET meta_value = 'no'
                WHERE post_id = %d
                    AND meta_key = %s
                ";

				$result = $wpdb->query(//phpcs:ignore --Direct call to Database is discouraged.
					$wpdb->prepare(
						$sql,
						$order_id,
						YWGC_RACE_CONDITION_BLOCKED
					)
				);

			}

		}

		/**
		 * Show_tax_class_for_gift_cards
		 *
		 * @return void
		 */
		public function show_tax_class_for_gift_cards() {

			echo '<script>
                jQuery("select#_tax_status").closest(".options_group").addClass("show_if_gift-card");
            </script>';

		}

		/**
		 * Set_gift_card_category_to_product
		 *
		 * @param  mixed $post_id post_id.
		 * @return void
		 */
		public function set_gift_card_category_to_product( $post_id ) {

			if ( isset( $_REQUEST['product-type'] ) && 'gift-card' !== $_REQUEST['product-type'] ) {
				return;
			}

			if ( ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['action2'] ) ) {//phpcs:ignore WordPress.Security.NonceVerification
				return;
			}

			if ( ( '-1' === $_REQUEST['action'] ) && ( '-1' === $_REQUEST['action2'] ) ) {//phpcs:ignore WordPress.Security.NonceVerification
				return;
			}

			$selected_catergories            = isset( $_REQUEST['tax_input']['giftcard-category'] ) ? $_REQUEST['tax_input']['giftcard-category'] : array();//phpcs:ignore
			$selected_catergories_serialized = wp_json_encode( $selected_catergories );

			update_post_meta( $post_id, 'selected_images_categories', $selected_catergories_serialized );
		}

		/**
		 * Show_sold_individually_for_gift_cards
		 *
		 * @return void
		 */
		public function show_sold_individually_for_gift_cards() {
			?>
			<script>
				jQuery("#_sold_individually").closest(".options_group").addClass("show_if_gift-card");
				jQuery("#_sold_individually").closest(".form-field").addClass("show_if_gift-card");
			</script>
			<?php
		}

		/**
		 * manage CSS class for the gift cards table rows
		 *
		 * @param array  $classes
		 * @param string $class
		 * @param int    $post_id
		 *
		 * @return array|mixed|void
		 * @since  1.0.0
		 */
		public function add_cpt_table_class( $classes, $class, $post_id ) {

			if ( YWGC_CUSTOM_POST_TYPE_NAME !== get_post_type( $post_id ) ) {
				return $classes;
			}

			$gift_card = new YITH_YWGC_Gift_Card( array( 'ID' => $post_id ) );

			if ( ! $gift_card->exists() ) {
				return $class;
			}

			$classes[] = $gift_card->status;

			/**
			 * APPLY_FILTERS: yith_gift_cards_table_class
			 *
			 * Filter the gift card table classes.
			 *
			 * @param array $classes the table classes
			 * @param int $post_id the post ID
			 *
			 * @return array
			 */
			return apply_filters( 'yith_gift_cards_table_class', $classes, $post_id );
		}

		/**
		 * Make some redirect based on the current action being performed
		 *
		 * @since  1.0.0
		 */
		public function redirect_gift_cards_link() {

			/**
			 * Check if the user ask for retrying sending the gift card email that are not shipped yet
			 */
			if ( isset( $_GET[ YWGC_ACTION_RETRY_SENDING ] ) ) {//phpcs:ignore WordPress.Security.NonceVerification

				$gift_card_id = isset( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification

				YITH_YWGC_Emails::get_instance()->send_gift_card_email( $gift_card_id, false );
				$redirect_url = remove_query_arg( array( YWGC_ACTION_RETRY_SENDING, 'id' ) );

				wp_safe_redirect( $redirect_url );
				exit;
			}

			if ( ! isset( $_GET['post_type'] ) || ! isset( $_GET['s'] ) ) {//phpcs:ignore WordPress.Security.NonceVerification
				return;
			}

			if ( 'shop_coupon' !== ( sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) ) ) {//phpcs:ignore WordPress.Security.NonceVerification
				return;
			}

			if ( preg_match( '/(\w{4}-\w{4}-\w{4}-\w{4})(.*)/i', sanitize_text_field( wp_unslash( $_GET['s'] ) ), $matches ) ) { //phpcs:ignore WordPress.Security.NonceVerification
				wp_safe_redirect( admin_url( 'edit.php?s=' . $matches[1] . '&post_type=gift_card' ) );
				die();
			}
		}

		/**
		 * Set_gift_card_category_to_media
		 *
		 * @return mixed|void
		 */
		public function set_gift_card_category_to_media() {
			// Skip all request without an action.
			if ( ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['action2'] ) ) {
				return;
			}

			// Skip all request without a valid action.
			if ( ( '-1' === $_REQUEST['action'] ) && ( '-1' === $_REQUEST['action2'] ) ) {
				return;
			}

			$action = '-1' !== $_REQUEST['action'] ? $_REQUEST['action'] : $_REQUEST['action2'];

			// Skip all request that do not belong to gift card categories.
			if ( ( 'ywgc-set-category' !== $action ) && ( 'ywgc-unset-category' !== $action ) ) {
				return;
			}

			// Skip all request without a media list.
			if ( ! isset( $_REQUEST['media'] ) ) {
				return;
			}

			$media_ids = $_REQUEST['media'];

			// Check if the request if for set or unset the selected category to the selected media.
			$action_set_category = ( 'ywgc-set-category' === $action ) ? true : false;

			// Retrieve the category to be applied to the selected media.
			$category_id = '-1' !== $_REQUEST['action'] ? intval( $_REQUEST['categories1_id'] ) : intval( $_REQUEST['categories2_id'] );

			foreach ( $media_ids as $media_id ) {

				if ( $action_set_category ) {
					$result = wp_set_object_terms( $media_id, $category_id, YWGC_CATEGORY_TAXONOMY, true );
				} else {
					$result = wp_remove_object_terms( $media_id, $category_id, YWGC_CATEGORY_TAXONOMY );
				}

				if ( is_wp_error( $result ) ) {
					return $result;
				}
			}
		}

		/**
		 * Fix the taxonomy count of items
		 *
		 * @param $term_id
		 * @param $taxonomy_name
		 *
		 * @since  1.0.0
		 */
		public function update_taxonomy_count( $term_id, $taxonomy_name ) {
			// Update the count of terms for attachment taxonomy.
			if ( YWGC_CATEGORY_TAXONOMY !== $taxonomy_name ) {
				return;
			}

			global $wpdb;
			$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts p1 WHERE p1.ID = $wpdb->term_relationships.object_id AND ( post_status = 'publish' OR ( post_status = 'inherit' AND (post_parent = 0 OR (post_parent > 0 AND ( SELECT post_status FROM $wpdb->posts WHERE ID = p1.post_parent ) = 'publish' ) ) ) ) AND post_type = 'attachment' AND term_taxonomy_id = %d", $term_id ) );//phpcs:ignore --Direct call to Database is discouraged.

			$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term_id ) );//phpcs:ignore --Direct call to Database is discouraged.
		}

		/**
		 * Show inventory section for gift card products
		 *
		 * @param array $tabs
		 *
		 * @return mixed
		 */
		public function show_inventory_tab( $tabs ) {
			if ( isset( $tabs['inventory'] ) ) {

				array_push( $tabs['inventory']['class'], 'show_if_gift-card' );
			}

			return $tabs;

		}

		/**
		 * Show gift cards code and amount in order's totals section, in edit order page
		 *
		 * @param int $order_id
		 */
		public function show_gift_cards_total_before_order_totals( $order_id ) {

			$order            = wc_get_order( $order_id );
			$order_gift_cards = $order->get_meta( '_ywgc_applied_gift_cards' );
			$currency         = $order->get_currency();

			if ( $order_gift_cards ) :
				foreach ( $order_gift_cards as $code => $amount ) :
					/**
					 * APPLY_FILTERS: ywgc_gift_card_amount_order_total_item
					 *
					 * Filter the gift card amount applied to the order.
					 *
					 * @param string the amount applied to the order
					 * @param object the gift card object
					 *
					 * @return string
					 */
					$amount = apply_filters( 'ywgc_gift_card_amount_order_total_item', $amount, YITH_YWGC()->get_gift_card_by_code( $code ) );
					?>
					<tr>
						<td class="label"><?php echo esc_html__( 'Gift card: ', 'yith-woocommerce-gift-cards' ) . esc_html( $code ) . ''; ?></td>
						<td width="1%"></td>
						<td class="total"><?php echo wp_kses_post( wc_price( $amount, array( 'currency' => $currency ) ) ); ?>
						</td>
					</tr>
					<?php
				endforeach;
			endif;
		}

		/**
		 * Add filters on the Gift Card Post Type page
		 *
		 * @param $query
		 */
		public function filter_gift_card_page_query( $query ) {
			global $pagenow, $post_type;

			if ( 'edit.php' === $pagenow && 'gift_card' === $post_type && isset( $_GET['balance'] ) && in_array( $_GET['balance'], array( 'used', 'active' ), true ) ) {//phpcs:ignore WordPress.Security.NonceVerification
				if ( 'active' === $_GET['balance'] ) {//phpcs:ignore WordPress.Security.NonceVerification
					$meta_query = array(
						array(
							'key'     => '_ywgc_balance_total',
							'value'   => 0,
							'compare' => '>',
						),
					);
				} else {
					$meta_query = array(
						array(
							'key'     => '_ywgc_balance_total',
							'value'   => pow( 10, - wc_get_price_decimals() ),
							'compare' => '<',
						),
					);
				}

				$query->set( 'meta_query', $meta_query );
			}
		}

		/**
		 * Localize order item meta and show theme as strings
		 *
		 * @param $display_key
		 * @param $meta
		 * @param $order_item
		 * @return string|void
		 */
		public function show_as_string_order_item_meta_key( $display_key ) {
			if ( strpos( $display_key, 'ywgc' ) !== false ) {
				if ( '_ywgc_product_id' === $display_key ) {
					$display_key = esc_html__( 'Product ID', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_product_as_present' === $display_key ) {
					$display_key = esc_html__( 'Product as a present', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_present_product_id' === $display_key ) {
					$display_key = esc_html__( 'Present product ID', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_present_variation_id' === $display_key ) {
					$display_key = esc_html__( 'Present variation ID', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_amount' === $display_key ) {
					$display_key = esc_html__( 'Amount', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_is_digital' === $display_key ) {
					$display_key = esc_html__( 'Digital', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_sender_name' === $display_key ) {
					$display_key = esc_html__( 'Sender\'s name', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_recipient_name' === $display_key ) {
					$display_key = esc_html__( 'Recipient\'s name', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_message' === $display_key ) {
					$display_key = esc_html__( 'Message', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_design_type' === $display_key ) {
					$display_key = esc_html__( 'Design type', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_design' === $display_key ) {
					$display_key = esc_html__( 'Design', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_subtotal' === $display_key ) {
					$display_key = esc_html__( 'Subtotal', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_subtotal_tax' === $display_key ) {
					$display_key = esc_html__( 'Subtotal tax', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_version' === $display_key ) {
					$display_key = esc_html__( 'Version', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_delivery_date' === $display_key ) {
					$display_key = esc_html__( 'Delivery date', 'yith-woocommerce-gift-cards' );
				} elseif ( '_ywgc_postdated' === $display_key ) {
					$display_key = esc_html__( 'Postdated', 'yith-woocommerce-gift-cards' );
				}
			}
			return $display_key;
		}

		/**
		 * Format date to show as meta value in order page
		 *
		 * @param $meta_value
		 * @param $meta
		 * @return mixed
		 */
		public function show_formatted_date( $meta_value, $meta = '', $item = '' ) {

			if ( '_ywgc_delivery_date' === $meta->key ) {
				$date_format = apply_filters( 'yith_wcgc_date_format', 'Y-m-d' );
				$meta_value  = apply_filters( 'yith_ywgc_delivery_date_meta_value', date_i18n( $date_format, $meta_value ) . ' (' . $date_format . ')', $meta_value, $date_format );
			}

			return $meta_value;

		}

		/**
		 * Update gift card amount in case the order is cancelled or refunded
		 *
		 * @param $order_id
		 * @param $from_status
		 * @param $to_status
		 * @param bool        $order
		 */
		public function update_gift_card_amount_on_order_status_change( $order_id, $from_status, $to_status, $order = false ) {
			if ( $order && $order instanceof WC_Order ) {
				$created_via                  = get_post_meta( $order_id, '_created_via', true );
				$is_gift_card_amount_refunded = $order->get_meta( '_ywgc_is_gift_card_amount_refunded' );
				if ( ( 'cancelled' === $to_status || ( 'refunded' === $to_status ) || ( 'failed' === $to_status ) ) && 'yes' !== $is_gift_card_amount_refunded ) {
					$gift_card_applied = $order->get_meta( '_ywgc_applied_gift_cards' );
					if ( empty( $gift_card_applied ) ) {
						return;
					}

					foreach ( $gift_card_applied as $gift_card_code => $gift_card_value ) {
						$args       = array(
							'gift_card_number' => $gift_card_code,
						);
						$gift_card  = new YITH_YWGC_Gift_Card( $args );
						$new_amount = $gift_card->get_balance() + $gift_card_value;

						if ( $new_amount > $gift_card->total_amount ) {
							$new_amount = $gift_card->total_amount;
						}
						/**
						 * APPLY_FILTERS: yith_ywgc_restore_gift_card_balance
						 *
						 * Filter the condition to restore the gift card amount if the order is refunded or cancelled.
						 *
						 * @param bool true to restore the amount, false for not. Default: true
						 * @param object $gift_card the gift card object
						 *
						 * @return bool
						 */
						if ( apply_filters( 'yith_ywgc_restore_gift_card_balance', true, $gift_card ) && ( ! $created_via || 'yith_wcmv_vendor_suborder' != $created_via ) ) {
							$gift_card->update_balance( $new_amount );
						}
					}

					$order->update_meta_data( '_ywgc_is_gift_card_amount_refunded', 'yes' );
					$order->save_meta_data();
				}
			}
		}

		/**
		 * Update_totals_on_save_order_items
		 *
		 * @param  WC_Order $order order.
		 * @param  mixed    $data_store data_store.
		 * @return void
		 */
		public function update_totals_on_save_order_items( $data_store, $order ) {

			if ( 'wc-refunded' === $order->get_status() ) {
				return;
			}

			$used_gift_cards       = $order->get_meta( '_ywgc_applied_gift_cards' );
			$used_gift_cards_total = $order->get_meta( '_ywgc_applied_gift_cards_totals' );

			if ( ! $used_gift_cards ) {
				return;
			}

			$applied_codes = array();
			foreach ( $used_gift_cards as $code => $amount ) {
				$applied_codes[] = $code;
			}

			$applied_codes_string = implode( ', ', $applied_codes );

			$order_total     = $order->get_total();
			$order_aux_total = $order->get_meta( '_ywgc_applied_gift_cards_order_total' );
			$updated_as_fee  = $order->get_meta( 'ywgc_gift_card_updated_as_fee' );

			// When the order status changes, WC avoid the gift card value, so we create here a negative fee with the gift card value, to apply it to the order.
			if ( ! $updated_as_fee && ! empty( $order_aux_total ) && $order_total !== $order_aux_total ) {

				$item = new WC_Order_Item_Fee();

				$amount = round( - 1 * ( (float) $used_gift_cards_total ), 2 );

				// add coupons as fees.
				$item->set_props(
					array(
						'id'       => '_ywgc_fee',
						'name'     => 'Gift Card (' . $applied_codes_string . ')',
						'total'    => floatval( $amount ),
						'order_id' => $order->get_id(),
					)
				);

				$order->add_item( $item );
				$order_total = $order_total + $amount;
				$order->set_total( $order_total );
				$order->update_meta_data( 'ywgc_gift_card_updated_as_fee', true );
			}

		}

		/**
		 * Change messages when a gift card is updated.
		 *
		 * @param  array $messages Array of messages.
		 * @return array|void
		 */
		public function post_updated_messages( $messages ) {
			global $post;
			global $wpdb;
			$title   = $post->post_title;
			$post_id = $post->ID;

			$messages['gift_card'] = array(
				0  => '', // Unused. Messages start at index 1.
				1  => esc_html__( 'Gift card updated', 'yith-woocommerce-gift-cards' ),
				2  => esc_html__( 'Custom field updated', 'yith-woocommerce-gift-cards' ),
				3  => esc_html__( 'Custom field deleted', 'yith-woocommerce-gift-cards' ),
				4  => esc_html__( 'Gift card updated', 'yith-woocommerce-gift-cards' ),
				5  => esc_html__( 'Gift card restored', 'yith-woocommerce-gift-cards' ),
				6  => esc_html__( 'Gift card published', 'yith-woocommerce-gift-cards' ),
				7  => esc_html__( 'Gift card saved', 'yith-woocommerce-gift-cards' ),
				8  => esc_html__( 'Gift card submitted', 'yith-woocommerce-gift-cards' ),
				9  => sprintf(
				/* translators: %s is the date */
					__( 'Gift card scheduled for: %s.', 'yith-woocommerce-gift-cards' ),
					'<strong>' . date_i18n( wc_date_format() . ' @ ' . wc_time_format(), strtotime( $post->post_date ) )
				),
				10 => __( 'Gift card draft updated.', 'yith-woocommerce-gift-cards' ),
			);

			if ( 'gift_card' !== get_post_type( $post_id ) || ( 'gift_card' === get_post_type( $post_id ) && '' === $title ) ) {

				if ( 'gift_card' === get_post_type( $post_id ) && '' === $title ) {
					$post->post_status = 'draft';
					wp_update_post( $post );
				}

				return $messages;
			}

			do_action( 'yith_ywgc_before_disallow_gift_cards_with_same_title_query', $post_id, $messages );

			$wtitlequery = "SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'gift_card' AND post_title = %s AND ID != %d ";
			$wresults    = $wpdb->get_results( $wpdb->prepare( $wtitlequery, $title, $post_id ) );//phpcs:ignore --Direct call to Database is discouraged.

			if ( $wresults ) {
				$error_message = __( 'This code is already used. Please choose another one', 'yith-woocommerce-gift-cards' );
				add_settings_error( 'post_has_links', '', $error_message, 'error' );
				settings_errors( 'post_has_links' );
				$post->post_status = 'draft';
				wp_update_post( $post );

				return;
			}

			return $messages;
		}

		/**
		 * Specify custom bulk actions messages for gift card post type.
		 *
		 * @param  array $bulk_messages Array of messages.
		 * @param  array $bulk_counts Array of how many objects were updated.
		 * @return array
		 */
		public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
			$bulk_messages['gift_card'] = array(
				/* translators: %s: gift card count */
				'updated'   => _n( '%s gift card updated.', '%s gift cards updated.', $bulk_counts['updated'], 'yith-woocommerce-gift-cards' ),
				/* translators: %s: gift card count */
				'locked'    => _n( '%s gift card not updated, somebody is editing it.', '%s gift cards not updated, somebody is editing them.', $bulk_counts['locked'], 'yith-woocommerce-gift-cards' ),
				/* translators: %s: gift count */
				'deleted'   => _n( '%s gift card permanently deleted.', '%s gift cards permanently deleted.', $bulk_counts['deleted'], 'yith-woocommerce-gift-cards' ),
				/* translators: %s: gift card count */
				'trashed'   => _n( '%s gift card moved to the Trash.', '%s gift cards moved to the Trash.', $bulk_counts['trashed'], 'yith-woocommerce-gift-cards' ),
				/* translators: %s: gift card count */
				'untrashed' => _n( '%s gift card restored from the Trash.', '%s gift cards restored from the Trash.', $bulk_counts['untrashed'], 'yith-woocommerce-gift-cards' ),
			);

			return $bulk_messages;
		}

		/**
		 * Add a special class to force show specific postbox only for Gift Card products.
		 *
		 * @since 3.15.0
		 * @param $classes array postbox classes
		 *
		 * @return array filtered postbox classes
		 */
		public function show_postbox_only_for_gift_card_products( $classes ) {
			$classes[] = 'show_if_gift-card';
			return $classes;
		}

		/**
		 * Add a special class to hide specific postbox only for Gift Card products.
		 *
		 * @since 3.15.0
		 * @param $classes array postbox classes
		 *
		 * @return array filtered postbox classes
		 */
		public function hide_postbox_only_for_gift_card_products( $classes ) {
			$classes[] = 'hide_if_gift-card';
			return $classes;
		}

		/**
		 * Remove Yoast SEO metabox in gift card categories
		 */
		public function disable_yoast_seo_metabox_in_gift_card_categories() {
			if ( class_exists( 'WPSEO_Options' ) ) {
				WPSEO_Options::set( 'display-metabox-tax-' . YWGC_CATEGORY_TAXONOMY, false );
			}
		}
	}
}

/**
 * Unique access to instance of YITH_YWGC_Frontend_Premium class
 *
 * @return \YITH_YWGC_Backend|\YITH_YWGC_Backend_Premium
 * @since 2.0.0
 */
function YITH_YWGC_Backend() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	if ( defined( 'YITH_YWGC_PREMIUM' ) ) {
		$instance = YITH_YWGC_Backend_Premium::get_instance();
	} elseif ( defined( 'YITH_YWGC_EXTENDED' ) ) {
		$instance = YITH_YWGC_Backend_Extended::get_instance();
	} else {
		$instance = YITH_YWGC_Backend::get_instance();
	}

	return $instance;
}
