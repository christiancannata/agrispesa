<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWGC_Frontend' ) ) {
	/**
	 * @class   YITH_YWGC_Frontend
	 *
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_YWGC_Frontend {

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
		 * @since  1.0
		 */
		protected function __construct() {

			add_action( 'init', array( $this, 'frontend_init' ) );

			/**
			 * Enqueue frontend scripts
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_script' ) );

			/**
			 * Enqueue frontend styles
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_style' ) );

			/**
			 * Show the gift card product frontend template
			 */
			add_action( 'woocommerce_gift-card_add_to_cart', array( $this, 'show_gift_card_product_template' ) );

			/**
			 * Show element on gift card product template
			 */
			add_action( 'yith_gift_cards_template_after_gift_card_form', array( $this, 'show_gift_card_add_to_cart_button' ), 20 );

			add_action( 'yith_ywgc_show_gift_card_amount_selection', array( $this, 'show_amount_selection' ) );

			add_action( 'yith_ywgc_gift_card_design_section', array( $this, 'show_design_section' ) );

			add_action( 'yith_ywgc_gift_card_delivery_info_section', array( $this, 'show_gift_card_details' ), 15 );

			// Register new endpoint to use for My Account page.
			add_action( 'init', array( $this, 'yith_ywgc_add_endpoint' ) );

			// Add new query var.
			add_filter( 'query_vars', array( $this, 'yith_ywgc_gift_cards_query_vars' ) );

			// Insert the new endpoint into the My Account menu.
			add_filter( 'woocommerce_account_menu_items', array( $this, 'yith_ywgc_add_gift_cards_link_my_account' ) );

			// Add content to the new endpoint.
			add_action( 'woocommerce_account_gift-cards_endpoint', array( $this, 'yith_ywgc_gift_cards_content' ) );

			add_action( 'woocommerce_order_item_meta_start', array( $this, 'show_gift_card_code_on_order_item' ), 10, 3 );

			add_action( 'woocommerce_product_thumbnails', array( $this, 'yith_ywgc_display_gift_card_form_preview_below_image' ) );

			add_action( 'wp', array( $this, 'yith_ywgc_remove_image_zoom_support' ), 100 );

			add_action( 'ywgc_empty_table_state_action_customer', array( $this, 'display_empty_gift_cards_table_state_view_customer' ) );

			add_filter( 'woocommerce_get_price_html', array( $this, 'get_price_html_for_gift_cards' ), 10, 2 );

		}

		/**
		 * Initiate the frontend
		 *
		 * @since 2.0.2
		 */
		public function frontend_init() {

			/**
			 * Show the gift card section for entering the discount code in the cart page
			 */
			$ywgc_cart_hook = apply_filters( 'ywgc_gift_card_code_form_cart_hook', 'woocommerce_before_cart' );

			add_action( $ywgc_cart_hook, array( $this, 'show_field_for_gift_code' ) );

			/**
			 * Show the gift card section for entering the discount code in the cart page
			 */
			$ywgc_checkout_hook = apply_filters( 'ywgc_gift_card_code_form_checkout_hook', 'woocommerce_before_checkout_form' );

			add_action( $ywgc_checkout_hook, array( $this, 'show_field_for_gift_code' ) );

		}

		/**
		 * Show_amount_selection
		 *
		 * @param  mixed $product product.
		 * @return void
		 */
		public function show_amount_selection( $product ) {

			wc_get_template(
				'single-product/add-to-cart/gift-card-amount-selection.php',
				array(
					'product' => $product,
					'amounts' => $product->get_amounts_to_be_shown(),
				),
				'',
				trailingslashit( YITH_YWGC_TEMPLATES_DIR )
			);
		}

		/**
		 * Show custom design area for the product
		 *
		 * @param WC_Product $product product.
		 */
		public function show_design_section( $product ) {

			$args = apply_filters(
				'yith_wcgc_design_presets_args',
				array(
					'hide_empty' => 1,
				)
			);

			$categories = get_terms( YWGC_CATEGORY_TAXONOMY, $args );

			$item_categories = array();
			foreach ( $categories as $item ) {
				$object_ids = get_objects_in_term( $item->term_id, YWGC_CATEGORY_TAXONOMY );
				foreach ( $object_ids as $object_id ) {
					$item_categories[ $object_id ] = isset( $item_categories[ $object_id ] ) ? $item_categories[ $object_id ] . ' ywgc-category-' . $item->term_id : 'ywgc-category-' . $item->term_id;
				}
			}

			wc_get_template(
				'yith-gift-cards/gift-card-design.php',
				array(
					'categories'      => $categories,
					'item_categories' => $item_categories,
					'product'         => $product,
				),
				'',
				trailingslashit( YITH_YWGC_TEMPLATES_DIR )
			);
		}

		/**
		 * Show Gift Cards details
		 *
		 * @param WC_Product $product product.
		 */
		public function show_gift_card_details( $product ) {

			if ( ( $product instanceof WC_Product_Gift_Card ) && $product->is_virtual() ) { // load virtual gift cards template.
				wc_get_template(
					'yith-gift-cards/gift-card-details.php',
					array(
						'mandatory_recipient' => apply_filters( 'yith_wcgc_gift_card_details_mandatory_recipient', YITH_YWGC()->mandatory_recipient() ),
					),
					'',
					trailingslashit( YITH_YWGC_TEMPLATES_DIR )
				);
			} else {
				wc_get_template(
					'yith-gift-cards/physical-gift-card-details.php',
					array(
						'ywgc_physical_details_mandatory' => ( 'yes' === get_option( 'ywgc_physical_details_mandatory' ) ),
					),
					'',
					trailingslashit( YITH_YWGC_TEMPLATES_DIR )
				);
			}

		}

		/**
		 * Register new endpoint to use for My Account page.
		 */
		public function yith_ywgc_add_endpoint() {
			add_rewrite_endpoint( 'gift-cards', EP_ROOT | EP_PAGES );
		}

		/**
		 * Add new query var.
		 *
		 * @param $vars
		 *
		 * @return array
		 */
		public function yith_ywgc_gift_cards_query_vars( $vars ) {
			$vars[] = 'gift-cards';

			return $vars;
		}

		/**
		 * Insert the new endpoint into the My Account menu.
		 *
		 * @param $items
		 *
		 * @return array
		 */
		public function yith_ywgc_add_gift_cards_link_my_account( $items ) {

			$item_position = ( array_search( 'orders', array_keys( $items ), true ) );
			$items_part1   = array_slice( $items, 0, $item_position + 1 );
			$items_part2   = array_slice( $items, $item_position );

			/**
			 * APPLY_FILTERS: yith_wcgc_my_account_menu_item_title
			 *
			 * Filter the "Gift Cards" menu item title on "My Account".
			 *
			 * @param string the "Gift Cards" menu item title
			 *
			 * @return string
			 */
			$items_part1['gift-cards'] = apply_filters( 'yith_wcgc_my_account_menu_item_title', esc_html_x( 'Gift Cards', 'my account endpoint title', 'yith-woocommerce-gift-cards' ) );
			$items                     = array_merge( $items_part1, $items_part2 );

			return $items;
		}


		/**
		 *  Add content to the new endpoint.
		 */
		public function yith_ywgc_gift_cards_content() {
			echo do_shortcode( '[yith_wcgc_show_gift_card_list]' );
		}

		/**
		 * Show the gift card code under the order item, in the order admin page
		 *
		 * @param int        $item_id
		 * @param array      $item
		 * @param WC_product $_product
		 *
		 * @throws Exception
		 * @since  1.0.0
		 */
		public function show_gift_card_code_on_order_item( $item_id, $item, $_product ) {

			$gift_ids = ywgc_get_order_item_giftcards( $item_id );

			if ( empty( $gift_ids ) ) {
				return;
			}

			foreach ( $gift_ids as $gift_id ) {

				$gc = new YITH_YWGC_Gift_Card( array( 'ID' => $gift_id ) );

				if ( ! $gc->is_pre_printed() ) :
					?>
					<div>
						<?php
						/**
						 * APPLY_FILTERS: yith_ywgc_display_code_order_details
						 *
						 * Filter the condition to display the gift card code in the order details.
						 *
						 * @param bool true to display it, false to not. Default: true
						 *
						 * @return bool
						 */
						if ( apply_filters( 'yith_ywgc_display_code_order_details', true ) ) :
							?>
							<span class="ywgc-gift-code-label"><?php echo esc_html__( 'Gift card code: ', 'yith-woocommerce-gift-cards' ); ?></span>
							<span class="ywgc-card-code"><?php echo $gc->get_code(); ?></span>
							<?php
						endif;
						if ( $gc->is_virtual() ) {
							if ( $gc->delivery_send_date ) {
								$status_class = 'sent';
								$message      = sprintf( esc_html__( 'Sent on %s', 'yith-woocommerce-gift-cards' ), $gc->get_formatted_date( $gc->delivery_send_date ) );
							} elseif ( $gc->delivery_date >= current_time( 'timestamp' ) ) {//phpcs:ignore --timestamp is discouraged
								$status_class = 'scheduled';
								$message      = esc_html_x( 'Scheduled', 'gift card delivery status label', 'yith-woocommerce-gift-cards' );
							} elseif ( $gc->has_been_sent() === '' ) {
								$status_class = 'not-sent';
								$message      = esc_html_x( 'Not yet sent', 'gift card delivery status label', 'yith-woocommerce-gift-cards' );
							} else {
								$status_class = 'failed';
								$message      = esc_html_x( 'Failed', 'gift card delivery status label', 'yith-woocommerce-gift-cards' );
							}
							?>

							<div>
								<span><?php echo sprintf( esc_html__( 'Recipient: %s', 'yith-woocommerce-gift-cards' ), wp_kses( $gc->recipient, 'post' ) ); ?></span>
							</div>
							<div>
								<?php if ( '' !== $gc->delivery_date ) : ?>
									<span><?php echo sprintf( esc_html__( 'Delivery date: %s', 'yith-woocommerce-gift-cards' ), $gc->get_formatted_date( $item['ywgc_delivery_date'] ) ); ?></span>
									<br>
								<?php endif; ?>
								<span class="ywgc-delivery-status <?php echo esc_attr( $status_class ); ?>"><?php echo wp_kses( $message, 'post' ); ?></span>

							</div>
							<?php
						}
						?>
					</div>
					<?php
				endif;
			}
		}

		/**
		 * Display a preview of the form under the gift card image
		 */
		public function yith_ywgc_display_gift_card_form_preview_below_image() {

			if ( is_product() ) {

				$product = wc_get_product( get_the_ID() );

				if ( is_object( $product ) && $product->is_type( 'gift-card' ) ) {

					wc_get_template(
						'single-product/form-preview.php',
						array(
							'product' => $product,
						),
						'',
						trailingslashit( YITH_YWGC_TEMPLATES_DIR )
					);
				}
			}
		}

		/**
		 * Remove zoom in gift card product pages
		 */
		public function yith_ywgc_remove_image_zoom_support() {

			if ( is_product() ) {

				$product = wc_get_product( get_the_ID() );

				if ( is_object( $product ) && $product->is_type( 'gift-card' ) ) {
					remove_theme_support( 'wc-product-gallery-zoom' );
					remove_theme_support( 'wc-product-gallery-lightbox' );
				}
			}
		}

		/**
		 * Output the add to cart button for variations.
		 */
		public function show_gift_card_add_to_cart_button() {
			global $product;

			if ( ! $product->is_purchasable() ) {
				return;
			}

			if ( 'gift-card' === $product->get_type() ) {

				$product_id        = $product->get_id();
				$sold_individually = $product->is_sold_individually();
				$add_to_card_text  = $product->single_add_to_cart_text();

				wc_get_template(
					'single-product/add-to-cart/gift-card-add-to-cart.php',
					array(
						'product'           => $product,
						'product_id'        => $product_id,
						'sold_individually' => $sold_individually,
						'add_to_card_text'  => $add_to_card_text,
					),
					'',
					trailingslashit(
						YITH_YWGC_TEMPLATES_DIR
					)
				);
			}
		}

		/**
		 * Show the gift card product frontend template
		 */
		public function show_gift_card_product_template() {
			global $product;
			if ( 'gift-card' === $product->get_type() ) {

				wc_get_template(
					'single-product/add-to-cart/gift-card.php',
					'',
					'',
					trailingslashit( YITH_YWGC_TEMPLATES_DIR )
				);
			}
		}

		/**
		 * Add frontend style to gift card product page
		 *
		 * @since  1.0
		 */
		public function enqueue_frontend_script() {

			/**
			 * APPLY_FILTERS: yith_ywgc_do_eneuque_frontend_scripts
			 *
			 * Filter the condition to enqueue of the frontend scripts.
			 *
			 * @param bool true to enqueue the scripts everywhere. False to only enqueue them in the product, cart and checkout pages.
			 *
			 * @return bool
			 */
			if ( is_product() || is_cart() || is_checkout() || is_account_page() || apply_filters( 'yith_ywgc_do_eneuque_frontend_scripts', false ) ) {
				$frontend_deps = array(
					'jquery',
					'woocommerce',
					'jquery-ui-datepicker',
					\YIT_Assets::wc_script_handle( 'wc-accounting' ),
				);

				if ( is_cart() ) {
					$frontend_deps[] = 'wc-cart';
				}
				// register and enqueue ajax calls related script file.
				wp_register_script(
					'ywgc-frontend-script',
					apply_filters( 'yith_ywgc_enqueue_script_source_path', YITH_YWGC_SCRIPT_URL . yit_load_js_file( 'ywgc-frontend.js' ) ),
					$frontend_deps,
					YITH_YWGC_VERSION,
					true
				);

				$default_color     = defined( 'YITH_PROTEO_VERSION' ) ? get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' ) : '#000000';
				$plugin_main_color = get_option( 'ywgc_plugin_main_color', $default_color );

				global $post;

				$date_format = get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' );

				/**
				 * APPLY_FILTERS: yith_ywgc_gift_card_notice_target
				 *
				 * Filter the gift card notices target in the frontend script.
				 *
				 * @param string the notice target selector. Default: div.woocommerce
				 *
				 * @return string
				 */
				wp_localize_script(
					'ywgc-frontend-script',
					'ywgc_data',
					array(
						'loader'                       => apply_filters( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
						'ajax_url'                     => admin_url( 'admin-ajax.php' ),
						'currency'                     => get_woocommerce_currency_symbol(),
						'default_gift_card_image'      => YITH_YWGC()->get_header_image( is_product() ? wc_get_product( $post ) : null ),
						'wc_ajax_url'                  => WC_AJAX::get_endpoint( '%%endpoint%%' ),
						'gift_card_nonce'              => wp_create_nonce( 'apply-gift-card' ),
						// For accounting JS.
						'currency_format'              => esc_attr(
							str_replace(
								array( '%1$s', '%2$s' ),
								array(
									'%s',
									'%v',
								),
								get_woocommerce_price_format()
							)
						),
						'mon_decimal_point'            => wc_get_price_decimal_separator(),
						'currency_format_num_decimals' => apply_filters( 'yith_gift_cards_format_number_of_decimals', wc_get_price_decimals() ),
						'currency_format_symbol'       => get_woocommerce_currency_symbol(),
						'currency_format_decimal_sep'  => esc_attr( wc_get_price_decimal_separator() ),
						'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
						'email_bad_format'             => esc_html__( 'Please enter a valid email address', 'yith-woocommerce-gift-cards' ),
						'mandatory_email'              => YITH_YWGC()->mandatory_recipient(),
						'notice_target'                => apply_filters( 'yith_ywgc_gift_card_notice_target', 'div.ywgc_enter_code' ),
						'date_format'                  => $date_format,
						'plugin_main_color'            => $plugin_main_color,
					)
				);

				wp_enqueue_script( 'ywgc-frontend-script' );

			}
		}

		/**
		 * Add frontend style to gift card product page
		 *
		 * @since  1.0
		 */
		public function enqueue_frontend_style() {

			if ( is_product() || is_cart() || is_checkout() || is_account_page() || apply_filters( 'yith_ywgc_do_eneuque_frontend_scripts', false ) ) {
				wp_enqueue_style(
					'ywgc-frontend',
					YITH_YWGC_ASSETS_URL . '/css/ywgc-frontend.css',
					array(),
					YITH_YWGC_VERSION
				);

				if ( apply_filters( 'yith_ywgc_enqueue_jquery_ui_css', true ) ) {
					wp_enqueue_style(
						'jquery-ui-css',
						'//code.jquery.com/ui/1.13.1/themes/smoothness/jquery-ui.css',
						'1.0',
						true
					);
				}

				wp_add_inline_style( 'ywgc-frontend', $this->get_custom_css() );

			}

			if ( is_product() ) {
				wp_enqueue_style( 'dashicons' );
			}
		}

		/**
		 * Get_custom_css
		 *
		 * @return custom_css
		 */
		public function get_custom_css() {

			$custom_css        = '';
			$default_color     = defined( 'YITH_PROTEO_VERSION' ) ? get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' ) : '#000000';
			$plugin_main_color = get_option( 'ywgc_plugin_main_color', $default_color );

			list($r, $g, $b) = sscanf( $plugin_main_color, '#%02x%02x%02x' );

			$form_button_colors_default = array(
				'default'      => '#448a85',
				'hover'        => '#4ac4aa',
				'default_text' => '#ffffff',
				'hover_text'   => '#ffffff',
			);

			$form_colors_default = array(
				'default'      => '#ffffff',
				'hover'        => '#ffffff',
				'default_text' => '#000000',
				'hover_text'   => '#000000',
			);

			$form_button_colors_array = get_option( 'ywgc_apply_gift_cards_button_colors', $form_button_colors_default );
			$form_colors_array        = get_option( 'ywgc_apply_gift_cards_colors', $form_colors_default );

			$custom_css .= "
                    .ywgc_apply_gift_card_button{
                        background-color:{$form_button_colors_array['default']} !important;
                        color:{$form_button_colors_array['default_text']}!important;
                    }
                    .ywgc_apply_gift_card_button:hover{
                        background-color:{$form_button_colors_array['hover']}!important;
                        color:{$form_button_colors_array['hover_text']}!important;
                    }
                    .ywgc_enter_code{
                        background-color:{$form_colors_array['default']};
                        color:{$form_colors_array['default_text']};
                    }
                    .ywgc_enter_code:hover{
                        background-color:{$form_colors_array['default']};
                        color: {$form_colors_array['default_text']};
                    }
                    .gift-cards-list button{
                        border: 1px solid {$plugin_main_color};
                    }
                    .selected_image_parent{
                        border: 2px dashed {$plugin_main_color} !important;
                    }
                    .ywgc-preset-image.selected_image_parent:after{
                        background-color: {$plugin_main_color};
                    }
                    .ywgc-predefined-amount-button.selected_button{
                        background-color: {$plugin_main_color};
                    }
                    .ywgc-on-sale-text{
                        color:{$plugin_main_color};
                    }
                    .ywgc-choose-image.ywgc-choose-template:hover{
                        background: rgba({$r}, {$g}, {$b}, 0.9);
                    }
                    .ywgc-choose-image.ywgc-choose-template{
                        background: rgba({$r}, {$g}, {$b}, 0.8);
                    }
                    .ui-datepicker a.ui-state-active, .ui-datepicker a.ui-state-hover {
                        background:{$plugin_main_color} !important;
                        color: white;
                    }
                    .ywgc-form-preview-separator{
                        background-color: {$plugin_main_color};
                    }
                    .ywgc-form-preview-amount{
                        color: {$plugin_main_color};
                    }
                    #ywgc-manual-amount{
                        border: 1px solid {$plugin_main_color};
                    }
                    .ywgc-template-categories a:hover,
                    .ywgc-template-categories a.ywgc-category-selected{
                        color: {$plugin_main_color};
                    }
                    .ywgc-design-list-modal .ywgc-preset-image:before {
                        background-color: {$plugin_main_color};
                    }
                    .ywgc-custom-upload-container-modal .ywgc-custom-design-modal-preview-close {
                        background-color: {$plugin_main_color};
                    }
           ";

			if ( defined( 'YITH_YWGC_EXTENDED' ) ) {
				$custom_css .= '
                    .ywgc-amount-buttons{
                        background-color: #F2F2F2 !important;
                        color: #5B5B5B !important;
                        border: 1px solid #E1E1E1 !important;
                        margin-right: 7px !important;
                    }
                    .ywgc-amount-buttons.selected_button{
                        background-color: black !important;
                        color: white !important;
                    }
			 ';
			}

			return apply_filters( 'yith_ywgc_custom_css', $custom_css );
		}


		/**
		 * Show gift card field
		 */
		public function show_field_for_gift_code() {

			wc_get_template(
				'checkout/form-gift-cards.php',
				array(),
				'',
				YITH_YWGC_TEMPLATES_DIR
			);
		}

		/**
		 * Display empty table state view for customers
		 *
		 * @param array $args Field Arguments.
		 */
		public function display_empty_gift_cards_table_state_view_customer( $args = array() ) {

			yith_ywgc_get_view( 'empty-gift-cards-table-state-customer.php', compact( 'args' ) );

		}

		/**
		 * Show HTML prices for gift cards
		 *
		 * @param string     $price_html Price HTML.
		 * @param WC_Product $product    The product.
		 *
		 * @return string
		 * @since 2.1.9
		 */
		public function get_price_html_for_gift_cards( $price_html, $product ) {

			if ( 'gift-card' === $product->get_type() ) {

				$amounts       = $product->get_amounts_to_be_shown();
				$on_sale       = $product->get_add_discount_settings_status();
				$on_sale_value = get_post_meta( $product->get_id(), '_ywgc_sale_discount_value', true );
				$on_sale_value = str_replace( ',', '.', $on_sale_value );
				$on_sale_text  = get_post_meta( $product->get_id(), '_ywgc_sale_discount_text', true );

				// No price for current gift card.
				if ( ! count( $amounts ) ) {
					/**
					 * APPLY_FILTERS: yith_woocommerce_gift_cards_empty_price_html
					 *
					 * Filter the empty price HTML for the gift cards.
					 *
					 * @param string empty string
					 * @param object $this gift card product instance
					 *
					 * @return string
					 */
					$price_html = apply_filters( 'yith_woocommerce_gift_cards_empty_price_html', '', $this );
				} else {
					ksort( $amounts, SORT_NUMERIC );

					$min_price_array = current( $amounts );
					$min_price       = wc_price( $min_price_array['price'] );
					$max_price_array = end( $amounts );
					$max_price       = wc_price( $max_price_array['price'] );

					/**
					 * APPLY_FILTERS: yith_woocommerce_gift_cards_amount_range
					 *
					 * Filter the price range of a gift card product.
					 *
					 * @param string $price_html price range of the gift card
					 * @param object $this gift card product instance
					 * @param float $min_price minimum amount of the gift card
					 * @param float $max_price maximum amount of the gift card
					 *
					 * @return string
					 */
					$price_html = apply_filters( 'yith_woocommerce_gift_cards_amount_range', $min_price !== $max_price ? wc_format_price_range( $min_price, $max_price ) : $min_price, $this, $min_price, $max_price );

					if ( $on_sale && $on_sale_value && apply_filters( 'ywgc_show_discounted_gift_card_product_price', true ) ) {

						$min_price_discounted = $min_price_array['price'] - ( ( $min_price_array['price'] * (float) $on_sale_value ) / 100 );
						$max_price_discounted = $max_price_array['price'] - ( ( $max_price_array['price'] * (float) $on_sale_value ) / 100 );

						$price_html = wc_format_sale_price( wc_format_price_range( $min_price, $max_price ), wc_format_price_range( $min_price_discounted, $max_price_discounted ) );

						if ( '' !== $on_sale_text && is_product() ) {
							$price_html .= '<br><p class="ywgc-on-sale-text">' . $on_sale_text . '</p>';
						}
					}
				}
			}

			return $price_html;
		}
	}
}


/**
 * Unique access to instance of YITH_YWGC_Frontend_Premium class
 *
 * @return \YITH_YWGC_Frontend|\YITH_YWGC_Frontend_Premium
 * @since 2.0.0
 */
function YITH_YWGC_Frontend() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	if ( defined( 'YITH_YWGC_PREMIUM' ) ) {
		$instance = YITH_YWGC_Frontend_Premium::get_instance();
	} elseif ( defined( 'YITH_YWGC_EXTENDED' ) ) {
		$instance = YITH_YWGC_Frontend_Extended::get_instance();
	} else {
		$instance = YITH_YWGC_Frontend::get_instance();
	}

	return $instance;
}
