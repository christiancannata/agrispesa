<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWGC_Cart_Checkout' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Cart_Checkout
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Cart_Checkout {

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
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {

			/**
			 * set the price when a gift card product is added to the cart
			 */
			add_filter( 'woocommerce_add_cart_item', array( $this, 'set_price_in_cart' ), 10, 1 );

			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 2 );

			add_action( 'woocommerce_new_order_item', array( $this, 'append_gift_card_data_to_new_order_item' ), 10, 2 );

			/**
			 * Custom add_to_cart handler for gift card product type
			 */
			add_action( 'woocommerce_add_to_cart_handler_gift-card', array( $this, 'add_to_cart_handler' ) );

			/* Ajax action for applying a gift card to the cart */
			add_action( 'wp_ajax_ywgc_apply_gift_card_code', array( $this, 'apply_gift_card_code_callback' ) );
			add_action( 'wp_ajax_nopriv_ywgc_apply_gift_card_code', array( $this, 'apply_gift_card_code_callback' ) );

			/* Ajax action for applying a gift card to the cart */
			add_action( 'wp_ajax_ywgc_remove_gift_card_code', array( $this, 'remove_gift_card_code_callback' ) );
			add_action( 'wp_ajax_nopriv_ywgc_remove_gift_card_code', array( $this, 'remove_gift_card_code_callback' ) );

			/*
			 * Compatibility with TaxJar
			 */
			if ( class_exists( 'WC_Taxjar' ) ) {
				add_action( 'woocommerce_after_calculate_totals', array( $this, 'apply_gift_cards_discount' ), 50 );
			} else {
				/**
				 * Apply the discount to the cart using the gift cards submitted, is any exists.
				 */
				add_action( 'woocommerce_after_calculate_totals', array( $this, 'apply_gift_cards_discount' ), 20 );
			}

			/**
			 * Show gift card amount usage on cart totals - checkout page
			 */
			add_action( 'woocommerce_review_order_before_order_total', array( $this, 'show_gift_card_amount_on_cart_totals' ) );

			/**
			 * Show gift card amount usage on cart totals - cart page
			 */
			add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'show_gift_card_amount_on_cart_totals' ) );

			add_action( 'woocommerce_new_order', array( $this, 'register_gift_cards_usage' ) );

			add_filter( 'woocommerce_get_order_item_totals', array( $this, 'show_gift_cards_total_applied_to_order' ), 10, 2 );

			/**
			 * Show gift card details in cart page
			 */
			add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'ywgc_custom_cart_product_image' ), 10, 3 );

			if ( defined( 'YITH_YWGC_FREE_INIT' ) || get_option( 'ywgc_apply_gift_card_on_coupon_form', 'no' ) === 'yes' ) {
				add_action( 'init', array( $this, 'ywgc_apply_gift_card_on_coupon_form' ) );
			}

			add_action( 'woocommerce_mini_cart_contents', array( $this, 'calculate_cart_total_in_the_mini_cart' ) );

		}

		/**
		 * Ywgc_apply_gift_card_on_coupon_form
		 *
		 * @return void
		 */
		public function ywgc_apply_gift_card_on_coupon_form() {

			add_action( 'woocommerce_after_calculate_totals', array( $this, 'ywgc_allow_shipping_in_coupons' ) );

			/**
			 * Verify if a coupon code inserted on cart page or checkout page belong to a valid gift card.
			 * In this case, make the gift card working as a temporary coupon
			 */
			add_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'verify_coupon_code' ), 10, 2 );

			add_action( 'woocommerce_new_order_item', array( $this, 'deduct_amount_from_gift_card_wc_3_plus' ), 10, 3 );

		}

		/**
		 * Verify the gift card value
		 *
		 * @param array  $return_val the returning value
		 * @param string $code       the gift card code
		 *
		 * @return array
		 * @author Daniel Sanchez
		 * @since  2.0.4
		 */
		public function verify_coupon_code( $return_val, $code ) {

			$gift_card = YITH_YWGC()->get_gift_card_by_code( $code );

			if ( ! is_object( $gift_card ) ) {
				return $return_val;
			}

			/**
			 * APPLY_FILTERS: ywgc_verify_coupon_code_condition
			 *
			 * Filter the condition to verify the coupon code.
			 *
			 * @param bool true to add a condition, false for not. Default: false
			 * @param array $return_val the returning value of the coupon
			 * @param string $code the gift card code applied as coupon
			 *
			 * @return bool
			 */
			if ( apply_filters( 'ywgc_verify_coupon_code_condition', false, $return_val, $code ) ) {
				return $return_val;
			}

			if ( $gift_card->exists() && get_option( 'ywgc_apply_gc_code_on_gc_product', 'no' ) === 'yes' && is_cart() ) {

				$items = WC()->cart->get_cart();

				foreach ( $items as $cart_item_key => $values ) {
					$product = $values['data'];

					if ( $product->get_type() === 'gift-card' ) {
						wc_add_notice( esc_html__( 'It is not possible to add a gift card code when the cart contains a gift card product', 'yith-woocommerce-gift-cards' ), 'error' );

						return $return_val;
					}
				}
			}

			if ( ! $gift_card instanceof YITH_YWGC_Gift_Card ) {
				return $return_val;
			}

			/**
			 * APPLY_FILTERS: ywgc_get_gift_card_balance_amount_for_coupon
			 *
			 * Filter the gift card balance to apply it as a coupon.
			 *
			 * @param float the gift card balance
			 * @param object $gift_card the gift card object
			 *
			 * @return float
			 */
			$amount = apply_filters( 'ywgc_get_gift_card_balance_amount_for_coupon', $gift_card->get_balance(), $gift_card );

			global $woocommerce_wpml;

			if ( $woocommerce_wpml && $woocommerce_wpml->multi_currency ) {
				$amount = apply_filters( 'wcml_raw_price_amount', $amount );
			}

			if ( $gift_card->ID && $gift_card->get_balance() > 0 && $gift_card->is_enabled() & ! $gift_card->is_expired() ) {
				/**
				 * APPLY_FILTERS: ywgc_temp_coupon_array
				 *
				 * Filter the temporal coupon data, generated with the gift card data.
				 *
				 * @param array the temporal coupon data
				 * @param object $gift_card the gift card object
				 *
				 * @return array
				 */
				return apply_filters(
					'ywgc_temp_coupon_array',
					array(
						'discount_type' => 'fixed_cart',
						'coupon_amount' => $amount,
						'amount'        => $amount,
						'id'            => true,
					),
					$gift_card
				);
			}

			return $return_val;
		}

		/**
		 * Deduct_amount_from_gift_card
		 *
		 * @param  mixed $id id.
		 * @param  mixed $item_id item_id.
		 * @param  mixed $code code.
		 * @param  mixed $discount_amount discount_amount.
		 * @param  mixed $discount_amount_tax discount_amount_tax.
		 * @return void
		 */
		public function deduct_amount_from_gift_card( $order_id, $item_id, $code, $discount_amount, $discount_amount_tax ) {

			$gift = YITH_YWGC()->get_gift_card_by_code( $code );

			$total_discount_amount = $discount_amount + $discount_amount_tax;

			if ( $gift instanceof YITH_YWGC_Gift_Card ) {

				$gift->update_balance( $gift->get_balance() - $total_discount_amount );
				$gift->register_order( $order_id );
			}

		}

		/**
		 * @param $item_id
		 * @param $item
		 * @param $order_id
		 */
		public function deduct_amount_from_gift_card_wc_3_plus( $item_id, $item, $order_id ) {

			if ( $item instanceof WC_Order_Item_Coupon ) {
				$this->deduct_amount_from_gift_card( $order_id, $item_id, $item->get_code(), $item->get_discount(), $item->get_discount_tax() );
			}

		}

		/**
		 * Deduct an amount from the gift card balance
		 *
		 * @param WC_Cart $cart the WC cart object.
		 *
		 * @author Fran Mendoza
		 * @since  3.0.0
		 */
		public function ywgc_allow_shipping_in_coupons( $cart ) {

			$total_coupons_amount          = 0;
			$external_total_coupons_amount = 0;

			$cart_coupons = $cart->get_coupons();
			foreach ( $cart_coupons as $coupon ) {

				$coupon_code = $coupon->get_code();
				$gift        = YITH_YWGC()->get_gift_card_by_code( $coupon_code );

				if ( is_object( $gift ) && $gift->exists() ) {
					$coupon_data           = $coupon->get_data();
					$total_coupons_amount += $coupon_data['amount'];
				} else {
					$external_total_coupons_amount += $cart->get_coupon_discount_amount( $coupon_code );
				}
			}

			if ( $total_coupons_amount > 0 ) {

				$discount_total  = $cart->get_discount_total() + $cart->get_discount_tax();
				$total_to_cover  = $cart->get_total( 'value' );
				$coupons_balance = $total_coupons_amount + $external_total_coupons_amount - $discount_total;

				if ( $coupons_balance > 0 && $total_to_cover > 0 ) {

					if ( $coupons_balance < $total_to_cover ) {
						$remaining_amount = $coupons_balance;
					} else {
						$remaining_amount = $total_to_cover;
					}

					$cart->discount_cart += $remaining_amount;

					$new_cart_totals = $cart->get_totals();

					$this->ywgc_charge_other_amounts_on_coupons( $remaining_amount );

					$new_total      = $new_cart_totals['total'] - $remaining_amount;
					$cart->total    = $new_total;
					$new_tax_value  = 0;
					$cart_tax_total = $cart->get_tax_totals();

					foreach ( $cart_tax_total as $tax_object ) {

						$rate = WC_Tax::get_rate_percent( $tax_object->tax_rate_id );

						$rate_formatted = '1.' . str_replace( '%', '', $rate );

						$total_without_tax = (float) $new_total / (float) $rate_formatted;

						$new_tax_value += $new_total - $total_without_tax;
					}

					// Apply all the taxes to the total, and delete it from the shipping and the fees.

					$cart_contents = $cart->get_cart_contents();

					foreach ( $cart_contents as $cart_item_key => $values ) {

						$line_tax_data = $values['line_tax_data'];

						$line_tax_data_total = $line_tax_data['total'];

						foreach ( $line_tax_data_total as $line_tax_data_key => $line_tax_data_values ) {

							$cart->set_cart_contents_tax( $new_tax_value );
							$cart->set_cart_contents_taxes( array( $line_tax_data_key => $new_tax_value ) );

							$cart->set_total_tax( $new_tax_value );

							/**
							 * APPLY_FILTERS: ywgc_override_cart_item_taxes_allowing_shipping_in_coupons
							 *
							 * Filter the condition to override the cart item taxes when allowing the shipping in coupons.
							 *
							 * @param bool true to allow it, false for not. Default: true
							 *
							 * @return bool
							 */
							if ( apply_filters( 'ywgc_override_cart_item_taxes_allowing_shipping_in_coupons', true ) ) {
								// Necessary to display a zero tax in the order page.
								$cart->cart_contents[ $cart_item_key ]['line_tax_data']['total'][ $line_tax_data_key ]    = $new_tax_value;
								$cart->cart_contents[ $cart_item_key ]['line_tax_data']['subtotal'][ $line_tax_data_key ] = $new_tax_value;
							}
						}

						$cart_shipping_taxes = $cart->get_shipping_taxes();

						foreach ( $cart_shipping_taxes as $cart_shipping_taxes_key => $cart_shipping_taxes_value ) {

							$shipping_taxes_key[] = $cart_shipping_taxes_key;

							$shipping_total = $cart->get_shipping_total() + $cart_shipping_taxes_value;
							$cart->set_shipping_total( $shipping_total ); // set to zero to allow PayPal payment

							$cart->set_shipping_tax( 0 );
							$cart->set_shipping_taxes( array( $cart_shipping_taxes_key => 0 ) );

						}

						$cart_fees = $cart->get_fees();

						foreach ( $cart_fees as $key_fee => $value_fee ) {
							$cart->set_fee_taxes( array( $key_fee => 0 ) );
							$cart->set_fee_tax( 0 );
						}
					}
				}
			}

		}

		/**
		 * Add the remaining cart amount to the gift card added as coupon
		 *
		 * @author Fran Mendoza
		 * @since  3.0.0
		 */
		function ywgc_charge_other_amounts_on_coupons( $remaining_amount ) {

			$cart = WC()->cart;

			$cart_coupons = array_reverse( $cart->get_coupons() );

			foreach ( $cart_coupons as $coupon ) {

				$coupon_code = $coupon->get_code();
				$gift        = YITH_YWGC()->get_gift_card_by_code( $coupon_code );

				if ( ! is_object( $gift ) ) {
					continue;
				}

				$coupon_discount_amount = $cart->get_coupon_discount_amount( $coupon_code, false );

				$cart_discount_added_by_this_coupon = isset( $coupon_discount_amount ) ? $coupon_discount_amount : 0;

				$coupon_data = $coupon->get_data();

				if ( $cart_discount_added_by_this_coupon < $coupon_data['amount'] ) {

					$unused_coupon_amount = $coupon_data['amount'] - $cart_discount_added_by_this_coupon;

					if ( $remaining_amount <= $unused_coupon_amount ) {

						$cart->coupon_discount_amounts[ $coupon_code ] = $cart->coupon_discount_amounts[ $coupon_code ] + $remaining_amount;
						$remaining_amount                              = 0;
					} elseif ( $remaining_amount > $unused_coupon_amount ) {

						$remaining_amount = $remaining_amount - $unused_coupon_amount;

						$cart->coupon_discount_amounts[ $coupon_code ] += $unused_coupon_amount;
					}
				}

				if ( $remaining_amount == 0 ) {
					return;
				}
			}
		}

		/**
		 *
		 * Show the image chosen for a gift card
		 *
		 * @param string $product_image    the product title HTML
		 * @param array  $cart_item        the cart item array
		 * @param bool   $cart_item_key    The cart item key
		 *
		 * @since    2.0.1
		 * @author  Daniel Sanchez <daniel.sanchez@yithemes.com>
		 * @return  string  The product title HTML
		 * @use     woocommerce_cart_item_thumbnail hook
		 */
		public function ywgc_custom_cart_product_image( $product_image, $cart_item, $cart_item_key = false ) {

			if ( ! isset( $cart_item['ywgc_amount'] ) ) {
				return $product_image;
			}

			if ( ! empty( $cart_item['ywgc_has_custom_design'] ) ) {

				$design_type = $cart_item['ywgc_design_type'];

				if ( 'custom' === $design_type ) {

					$image = YITH_YWGC_SAVE_URL . '/' . $cart_item['ywgc_design'];

					$product_image = '<img width="300" height="300" src="' . $image . '" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
            alt="" srcset="' . $image . ' 300w, ' . $image . ' 600w, ' . $image . ' 100w, ' . $image . ' 150w, ' . $image . ' 768w, ' . $image . ' 1024w"
            sizes="(max-width: 300px) 100vw, 300px" />';

				} elseif ( 'template' === $design_type ) {
					$product_image = wp_get_attachment_image( $cart_item['ywgc_design'] );

				} elseif ( 'custom-modal' === $design_type ) {

					$image_url = $cart_item['ywgc_design'];

					$product_image = '<img width="300" height="300" src="' . $image_url . '" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
            alt="" srcset="' . $image_url . ' 300w, ' . $image_url . ' 600w, ' . $image_url . ' 100w, ' . $image_url . ' 150w, ' . $image_url . ' 768w, ' . $image_url . ' 1024w"
            sizes="(max-width: 300px) 100vw, 300px" />';

				}
			} else {

				if ( isset( $cart_item['ywgc_product_as_present'] ) && $cart_item['ywgc_product_as_present'] ) {

					$image = YITH_YWGC()->get_default_header_image();

					$array_product_image = explode( 'src=', $product_image );
					$array_product_image = explode( '"', $array_product_image[1] );

					$product_image = str_replace( $array_product_image[1], $image, $product_image );

				} else {

					$_product = wc_get_product( $cart_item['product_id'] );

					if ( get_class( $_product ) == 'WC_Product_Gift_Card' ) {

						$image_id         = get_post_thumbnail_id( $_product->get_id() );
						$header_image_url = wp_get_attachment_url( $image_id );

						$array_product_image = explode( 'src=', $product_image );
						$array_product_image = explode( '"', $array_product_image[1] );

						$product_image = str_replace( $array_product_image[1], $header_image_url, $product_image );

					}
				}
			}

			/**
			 * APPLY_FILTERS: ywgc_gift_card_product_image_in_cart
			 *
			 * Filter the gift card product image in the cart.
			 *
			 * @param string $product_image the product image
			 * @param array $cart_item the cart item
			 *
			 * @return string
			 */
			return apply_filters( 'ywgc_gift_card_product_image_in_cart', $product_image, $cart_item );
		}

		/**
		 * Show gift cards usage on order item totals
		 *
		 * @param array    $total_rows
		 * @param WC_Order $order
		 *
		 * @return array
		 */
		public function show_gift_cards_total_applied_to_order( $total_rows, $order ) {

			$gift_cards     = $order->get_meta( '_ywgc_applied_gift_cards' );
			$updated_as_fee = $order->get_meta( 'ywgc_gift_card_updated_as_fee' );

			if ( $gift_cards && $updated_as_fee == false ) {
				$row_totals = $total_rows['order_total'];
				unset( $total_rows['order_total'] );

				$gift_cards_message = '';
				foreach ( $gift_cards as $code => $amount ) {

					/**
					 * APPLY_FILTERS: yith_ywgc_gift_card_amount_thank_you_page
					 *
					 * Filter the gift card amount in the "Thank you" page.
					 *
					 * @param string $amount the gift card amount
					 * @param object the gift card object
					 *
					 * @return string
					 */
					$amount = apply_filters( 'yith_ywgc_gift_card_amount_thank_you_page', $amount, YITH_YWGC()->get_gift_card_by_code( $code ) );

					/**
					 * APPLY_FILTERS: yith_ywgc_gift_card_coupon_message
					 *
					 * Filter the gift card applied message in the order totals.
					 *
					 * @param string the applied gift card message
					 * @param string $amount the gift card amount
					 * @param string $code the gift card code
					 *
					 * @return string
					 */
					$gift_cards_message .= apply_filters( 'yith_ywgc_gift_card_coupon_message', '-' . wc_price( $amount ) . ' (' . $code . ')', $amount, $code );
				}

				$total_rows['gift_cards'] = array(
					'label' => esc_html__( 'Gift cards:', 'yith-woocommerce-gift-cards' ),
					'value' => $gift_cards_message,
				);

				/**
				 * APPLY_FILTERS: ywgc_gift_card_thankyou_table_total_rows
				 *
				 * Filter the gift card displayed on the totals.
				 *
				 * @param array $total_rows the gift card data displayed on the totals
				 * @param string $code the gift card code
				 *
				 * @return array
				 */
				$total_rows = apply_filters( 'ywgc_gift_card_thankyou_table_total_rows', $total_rows, $code );

				$total_rows['order_total'] = $row_totals;
			}

			return $total_rows;
		}

		/**
		 * Show gift card amount usage on cart totals
		 */
		public function show_gift_card_amount_on_cart_totals() {

			$applied_gift_cards = WC()->cart->applied_gift_cards;

			if ( ! empty( $applied_gift_cards ) ) {

				foreach ( $applied_gift_cards as $code ) :

					/**
					 * APPLY_FILTERS: yith_ywgc_cart_totals_gift_card_label
					 *
					 * Filter the gift card label in the totals on cart.
					 *
					 * @param string the gift card label
					 * @param string $code the gift card code
					 *
					 * @return string
					 */
					$label  = apply_filters( 'yith_ywgc_cart_totals_gift_card_label', esc_html( esc_html__( 'Gift card:', 'yith-woocommerce-gift-cards' ) . ' ' . $code ), $code );
					$amount = isset( WC()->cart->applied_gift_cards_amounts[ $code ] ) ? - WC()->cart->applied_gift_cards_amounts[ $code ] : 0;

					/**
					 * APPLY_FILTERS: yith_ywgc_cart_totals_gift_card_amount
					 *
					 * Filter the gift card total amount in the totals on cart.
					 *
					 * @param string $amount the gift card amount
					 * @param string $code the gift card code
					 *
					 * @return string
					 */
					$amount = apply_filters( 'yith_ywgc_cart_totals_gift_card_amount', $amount, $code );

					/**
					 * APPLY_FILTERS: ywgc_remove_gift_card_text
					 *
					 * Filter the "remove" text displayed on the cart totals, to remove the gift card from the cart.
					 *
					 * @param string the "remove" text
					 *
					 * @return string
					 */
					$value = wc_price( $amount ) . ' <a href="' . esc_url(
						add_query_arg(
							'remove_gift_card_code',
							urlencode( $code ),
							defined( 'WOOCOMMERCE_CHECKOUT' ) ? wc_get_checkout_url() : wc_get_cart_url()
						)
					) . '" class="ywgc-remove-gift-card " data-gift-card-code="' . esc_attr( $code ) . '">' . apply_filters( 'ywgc_remove_gift_card_text', esc_html__( '[Remove]', 'yith-woocommerce-gift-cards' ) ) . '</a>';
					?>
					<tr class="ywgc-gift-card-applied">
						<th><?php echo wp_kses( $label, 'post' ); ?></th>
						<td><?php echo wp_kses( $value, 'post' ); ?></td>
					</tr>

					<?php
					/**
					 * DO_ACTION: ywgc_gift_card_checkout_cart_table
					 *
					 * Allow to add extra information at the end of the gift card data on cart and checkout.
					 *
					 * @param string $code the gift card ocodebject
					 * @param float $amount the gift card amount
					 */
					do_action( 'ywgc_gift_card_checkout_cart_table', $code, $amount );

				endforeach;
			}
		}

		/**
		 * CMP
		 * Comparison function.
		 *
		 * @param  mixed $a a.
		 * @param  mixed $b b.
		 * @return int
		 */
		function cmp( $a, $b ) {
			if ( $a == $b ) {
				return 0;
			}

			return ( $a < $b ) ? - 1 : 1;
		}

		/**
		 * Apply a gift card discount to current cart
		 *
		 * @param string $code
		 */
		protected function add_gift_card_code_to_session( $code ) {
			$applied_gift_cards = $this->get_gift_cards_from_session();

			$code = strtoupper( $code );

			if ( ! in_array( $code, $applied_gift_cards ) ) {
				$applied_gift_cards[] = $code;
				WC()->session->set( 'applied_gift_cards', $applied_gift_cards );
			}
		}

		/**
		 * Remove a gift card discount from current cart
		 *
		 * @param string $code
		 */
		protected function remove_gift_card_code_from_session( $code ) {
			$applied_gift_cards = $this->get_gift_cards_from_session();

			if ( ( $key = array_search( $code, $applied_gift_cards ) ) !== false ) {
				unset( $applied_gift_cards[ $key ] );
			}

			WC()->session->set( 'applied_gift_cards', $applied_gift_cards );
		}

		/**
		 * Get_gift_cards_from_session
		 *
		 * @return array|string
		 */
		private function get_gift_cards_from_session() {
			$value = array();

			if ( isset( WC()->session ) ) {
				$value = WC()->session->get( 'applied_gift_cards', array() );
			}

			return $value;
		}

		/**
		 * Empty_gift_cards_session
		 *
		 * @return void
		 */
		private function empty_gift_cards_session() {
			if ( isset( WC()->session ) ) {
				WC()->session->__unset( 'applied_gift_cards' );
			}
		}

		/**
		 * Apply the gift cards discount to the cart
		 *
		 * @param WC_Cart $cart
		 */
		public function apply_gift_cards_discount( $cart ) {

			$cart->applied_gift_cards         = array();
			$cart->applied_gift_cards_amounts = array();

			$gift_card_codes = $this->get_gift_cards_from_session();
			if ( $gift_card_codes ) {

				$cart_total = $cart->get_total( 'edit' );

				$gift_card_amounts = array();
				foreach ( $gift_card_codes as $code ) {
					/** @var YITH_YWGC_Gift_Card $gift_card */
					$gift_card = YITH_YWGC()->get_gift_card_by_code( $code );

					if ( is_object( $gift_card ) && YITH_YWGC()->check_gift_card( $gift_card, true ) ) {
						/**
						 * APPLY_FILTERS: yith_ywgc_gift_card_coupon_amount
						 *
						 * Filter the gift card balance to be applied to the cart.
						 *
						 * @param string the gift card balance
						 * @param object $gift_card the gift card object
						 *
						 * @return string
						 */
						$gift_card_amounts[ $code ] = apply_filters( 'yith_ywgc_gift_card_coupon_amount', $gift_card->get_balance(), $gift_card );

					} else {
						$this->remove_gift_card_code_from_session( $code );
						wc_print_notices();
					}
				}

				uasort( $gift_card_amounts, array( $this, 'cmp' ) );

				foreach ( $gift_card_amounts as $code => $amount ) {

					$cart->applied_gift_cards[] = $code;

					if ( ( $cart_total + $cart->shipping_total > 0 ) && ( $amount > 0 ) ) {

						$discount = min( $amount, $cart_total );

						$residue = $cart_total - $discount;

						if ( $residue > 0 ) {
							if ( ( $cart->shipping_total - $residue ) >= 0 ) {
								/**
								 * APPLY_FILTERS: yith_ywgc_detract_residue_to_shipping_total
								 *
								 * Filter the condition to detract the gift card balance residue to the shipping total.
								 *
								 * @param bool true to allow it, false for not. Default: true
								 *
								 * @return bool
								 */
								if ( apply_filters( 'yith_ywgc_detract_residue_to_shipping_total', true ) ) {

									$cart->set_shipping_total( $residue );
								}
							} else {
								$residue = $residue - $cart->shipping_total;
							}
						}

						$cart->applied_gift_cards_amounts[ $code ] = $discount;
						$cart_total                               -= $discount;
					}
				}

				$discount = isset( $discount ) ? $discount : '';

				$cart->ywgc_original_cart_total = $cart->total;

				/**
				 * DO_ACTION: yith_ywgc_apply_gift_card_discount_before_cart_total
				 *
				 * Triggered before update the total with the gift card value substracted.
				 *
				 * @param object $cart the cart object
				 * @param float $discount the gift card discount amount
				 */
				do_action( 'yith_ywgc_apply_gift_card_discount_before_cart_total', $cart, $discount );

				$cart->total = abs( $cart_total );

				/**
				 * DO_ACTION: yith_ywgc_apply_gift_card_discount_after_cart_total
				 *
				 * Triggered after update the total with the gift card value substracted.
				 *
				 * @param object $cart the cart object
				 * @param float $discount the gift card discount amount
				 */
				do_action( 'yith_ywgc_apply_gift_card_discount_after_cart_total', $cart, $discount );

				/**
				 * APPLY_FILTERS: yith_ywgc_recalculate_taxes_after_cart_total
				 *
				 * Filter the condition to recalculate taxes after cart total.
				 *
				 * @param bool true to recalculate it, false for not. Default: false
				 *
				 * @return bool
				 */
				if ( apply_filters( 'yith_ywgc_recalculate_taxes_after_cart_total', false ) ) {

					if ( $cart->total == 0 ) {//phpcs:ignore
						$cart->set_total_tax( 0 );
						$cart->set_subtotal_tax( 0 );
						$cart->set_cart_contents_tax( 0 );
					} else {

						$cart_totals = $cart->get_totals();

						$cart_contents_total     = $cart_totals['cart_contents_total'];
						$cart_contents_total_tax = $cart_totals['cart_contents_tax'];

						$new_cart_total = $cart->total;

						$shiping_total     = $cart_totals['shipping_total'];
						$shiping_total_tax = $cart_totals['shipping_tax'];

						$cart_total_aux     = $cart_contents_total + $shiping_total;
						$cart_total_tax_aux = $cart_contents_total_tax + $shiping_total_tax;

						$tax_percentage = round( ( $cart_total_tax_aux * 100 ) / $cart_total_aux );

						$rate_formatted = '1.' . $tax_percentage;

						$amount_to_substract = ( $new_cart_total / $rate_formatted );

						$new_tax = $new_cart_total - $amount_to_substract;

						$cart_contents = $cart->get_cart_contents();

						foreach ( $cart_contents as $cart_item_key => $values ) {

							$line_tax_data       = $values['line_tax_data'];
							$line_tax_data_total = $line_tax_data['total'];
							foreach ( $line_tax_data_total as $line_tax_data_key => $line_tax_data_values ) {

								$cart->set_cart_contents_taxes( array( $line_tax_data_key => $new_tax ) );
							}

							$shipping_taxes = $cart->get_shipping_taxes();

							foreach ( $shipping_taxes as $cart_shipping_taxes_key => $cart_shipping_taxes_value ) {
								$cart->set_shipping_taxes( array( $cart_shipping_taxes_key => 0 ) );
							}
						}

						$cart->set_cart_contents_total( $new_cart_total );
						$cart->set_cart_contents_tax( $new_tax );
						$cart->set_total_tax( $new_tax );

					}
				}
			}

		}

		/**
		 * Check if the gift card code provided is valid and store the amount for
		 * applying the discount to the cart
		 */
		public function apply_gift_card_code_callback() {

			check_ajax_referer( 'apply-gift-card', 'security' );
			$code = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';

			if ( ! empty( $code ) ) {

				$gift = YITH_YWGC()->get_gift_card_by_code( $code );

				if ( YITH_YWGC()->check_gift_card( $gift ) ) {

					$this->add_gift_card_code_to_session( $code );

					wc_add_notice( $gift->get_gift_card_message( YITH_YWGC_Gift_Card::GIFT_CARD_SUCCESS ) );
				}
				wc_print_notices();
			}

			die();
		}

		/**
		 * Check if the gift card code provided is valid and store the amount for
		 * applying the discount to the cart
		 */
		public function remove_gift_card_code_callback() {

			check_ajax_referer( 'apply-gift-card', 'security' );
			$code = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';

			if ( ! empty( $code ) ) {

				$gift = YITH_YWGC()->get_gift_card_by_code( $code );

				if ( YITH_YWGC()->check_gift_card( $gift, true ) ) {

					$this->remove_gift_card_code_from_session( $code );

					wc_add_notice( $gift->get_gift_card_message( YITH_YWGC_Gift_Card::GIFT_CARD_REMOVED ) );
				}
				wc_print_notices();
			}

			die();
		}

		/**
		 * Update the balance for all gift cards applied to an order
		 *
		 * @throws Exception
		 *
		 * @param int $order_id
		 */
		public function register_gift_cards_usage( $order_id ) {

			/**
			 * Adding two race condition fields to the order
			 */
			$order = wc_get_order( $order_id );

			$order->update_meta_data( YWGC_RACE_CONDITION_BLOCKED, 'no' );
			$order->update_meta_data( YWGC_RACE_CONDITION_UNIQUID, 'none' );

			$applied_gift_cards = array();
			$applied_discount   = 0.00;

			$applied_gift_cards_amount = isset( WC()->cart ) ? WC()->cart->applied_gift_cards_amounts : array();
			$created_via               = get_post_meta( $order_id, '_created_via', true );

			if ( isset( $applied_gift_cards_amount ) && is_array( $applied_gift_cards_amount ) ) {
				foreach ( $applied_gift_cards_amount as $code => $amount ) {
					$gift = YITH_YWGC()->get_gift_card_by_code( $code );

					if ( $gift->exists() ) {
						/**
						 * APPLY_FILTERS: yith_ywgc_gift_card_amount_before_deduct
						 *
						 * Filter the gift card amount before deduct it from the gift card balance.
						 *
						 * @param string $amount the amount to be deducted
						 * @param object $gift the gift card object
						 *
						 * @return string
						 */
						$amount                      = apply_filters( 'yith_ywgc_gift_card_amount_before_deduct', $amount, $gift );
						$applied_gift_cards[ $code ] = $amount;
						$applied_discount           += $amount;

						// Avoid charging twice if there is a YITH Multi Vendor suborder
						if ( ! $created_via || 'yith_wcmv_vendor_suborder' != $created_via ) {
							/**
							 * APPLY_FILTERS: yith_ywgc_new_balance_before_update_balance
							 *
							 * Filter the gift card new balance before update it.
							 *
							 * @param string the new gift card balance
							 * @param object $gift the gift card object
							 * @param string $amount the amount to be deducted
							 *
							 * @return string
							 */
							$new_balance = apply_filters( 'yith_ywgc_new_balance_before_update_balance', max( 0.00, $gift->get_balance() - $amount ), $gift, $amount );

							$gift->update_balance( $new_balance );
							$gift->register_order( $order_id );
						}
					}
				}
			}

			if ( $applied_gift_cards && ( ! $created_via || 'yith_wcmv_vendor_suborder' != $created_via ) ) {
				$order       = wc_get_order( $order_id );
				$order_total = $order->get_total();

				$order->update_meta_data( '_ywgc_applied_gift_cards', $applied_gift_cards );
				$order->update_meta_data( '_ywgc_applied_gift_cards_totals', $applied_discount );
				$order->update_meta_data( '_ywgc_applied_gift_cards_order_total', $order_total );

				$applied_discount = apply_filters( 'ywgc_gift_card_amount_order_total_item', $applied_discount, $gift );

				$order->add_order_note( sprintf( esc_html__( 'Order paid with gift cards for a total amount of %s.', 'yith-woocommerce-gift-cards' ), wc_price( $applied_discount ) ) );

				$order->save();
			} elseif ( ! ! $created_via && 'yith_wcmv_vendor_suborder' == $created_via ) {
				$applied_discount = apply_filters( 'ywgc_gift_card_amount_order_total_item', $applied_discount, $gift );
				$order->add_order_note( sprintf( esc_html__( 'Order paid with gift cards for a total amount of %s.', 'yith-woocommerce-gift-cards' ), wc_price( $applied_discount ) ) );
				$order->save();
			}

			$this->empty_gift_cards_session();
		}

		/**
		 * Build cart item meta to pass to add_to_cart when adding a gift card to the cart
		 *
		 * @since 1.5.0
		 */
		public function build_cart_item_data() {

			$cart_item_data = array();

			/**
			 * Check if the current gift card has a prefixed amount set
			 */
			$ywgc_is_preset_amount = isset( $_REQUEST['gift_amounts'] ) && ( floatval( $_REQUEST['gift_amounts'] ) > 0 ); //phpcs:ignore WordPress.Security.NonceVerification
			$ywgc_is_preset_amount = wc_format_decimal( $ywgc_is_preset_amount );

			/**
			 * Neither manual or fixed? Something wrong happened!
			 */
			if ( ! $ywgc_is_preset_amount ) {
				wp_die( esc_html__( 'The gift card has an invalid amount', 'yith-woocommerce-gift-cards' ) );
			}

			/**
			 * Check if it is a physical gift card
			 */
			$ywgc_is_physical = isset( $_REQUEST['ywgc-is-physical'] ) && sanitize_text_field( wp_unslash( $_REQUEST['ywgc-is-physical'] ) );//phpcs:ignore WordPress.Security.NonceVerification
			if ( $ywgc_is_physical ) {

				/**
				 * Retrieve sender name
				 */
				$sender_name = isset( $_REQUEST['ywgc-sender-name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ywgc-sender-name'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification

				/**
				 * Recipient name
				 */
				$recipient_name = isset( $_REQUEST['ywgc-recipient-name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ywgc-recipient-name'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification

				/**
				 * Retrieve the sender message
				 */
				$sender_message = isset( $_REQUEST['ywgc-edit-message'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ywgc-edit-message'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification

			}

			/**
			 * Check if it is a digital gift card
			 */
			$ywgc_is_digital = isset( $_REQUEST['ywgc-is-digital'] ) && sanitize_text_field( wp_unslash( $_REQUEST['ywgc-is-digital'] ) );//phpcs:ignore WordPress.Security.NonceVerification
			if ( $ywgc_is_digital ) {

				/**
				 * Retrieve gift card recipient
				 */
				$recipients = apply_filters( 'ywgc-recipient-email', isset( $_REQUEST['ywgc-recipient-email'] ) ? $_REQUEST['ywgc-recipient-email'] : '');//phpcs:ignore

				/**
				 * Retrieve sender name
				 */
				$sender_name = isset( $_REQUEST['ywgc-sender-name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ywgc-sender-name'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification

				/**
				 * Recipient name
				 */
				$recipient_name = isset( $_REQUEST['ywgc-recipient-name'] ) ? $_REQUEST['ywgc-recipient-name'] : '';//phpcs:ignore

				/**
				 * Retrieve the sender message
				 */
				$sender_message = isset( $_REQUEST['ywgc-edit-message'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ywgc-edit-message'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification

				$gift_card_design = - 1;
				$design_type      = isset( $_POST['ywgc-design-type'] ) ? sanitize_text_field( wp_unslash( $_POST['ywgc-design-type'] ) ) : 'default'; //phpcs:ignore WordPress.Security.NonceVerification

				if ( 'template' === $design_type ) {
					if ( isset( $_POST['ywgc-template-design'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification
						$gift_card_design = sanitize_text_field( wp_unslash( $_POST['ywgc-template-design'] ) );//phpcs:ignore WordPress.Security.NonceVerification
					}
				}
			}

			if ( isset( $_POST['add-to-cart'] ) ) {//phpcs:ignore WordPress.Security.NonceVerification
				$cart_item_data['ywgc_product_id'] = absint( $_POST['add-to-cart'] );//phpcs:ignore WordPress.Security.NonceVerification
			} elseif ( isset( $_REQUEST['ywgc_product_id'] ) ) {//phpcs:ignore WordPress.Security.NonceVerification
				$cart_item_data['ywgc_product_id'] = sanitize_text_field( wp_unslash( $_POST['ywgc-template-design'] ) );//phpcs:ignore WordPress.Security.NonceVerification
			}

			/**
			 * Set the gift card amount
			 */
			$product     = wc_get_product( $cart_item_data['ywgc_product_id'] );
			$ywgc_amount = sanitize_text_field( wp_unslash( $_REQUEST['gift_amounts'] ) );//phpcs:ignore WordPress.Security.NonceVerification
			$ywgc_amount = apply_filters( 'yith_ywgc_submitting_select_amount', $ywgc_amount, $product );

			$cart_item_data['ywgc_amount']      = $ywgc_amount;
			$cart_item_data['ywgc_is_digital']  = $ywgc_is_digital;
			$cart_item_data['ywgc_is_physical'] = $ywgc_is_physical;

			/**
			 * Retrieve the gift card recipient, if digital
			 */
			if ( $ywgc_is_digital ) {
				$cart_item_data['ywgc_recipients']        = $recipients;
				$cart_item_data['ywgc_sender_name']       = $sender_name;
				$cart_item_data['ywgc_recipient_name']    = $recipient_name;
				$cart_item_data['ywgc_message']           = $sender_message;
				$cart_item_data['ywgc_design_type']       = $design_type;
				$cart_item_data['ywgc_has_custom_design'] = 'default' !== $design_type ? 1 : 0;
				if ( $gift_card_design ) {
					$cart_item_data['ywgc_design'] = $gift_card_design;
				}
			}

			if ( $ywgc_is_physical ) {
				$cart_item_data['ywgc_recipient_name'] = $recipient_name;
				$cart_item_data['ywgc_sender_name']    = $sender_name;
				$cart_item_data['ywgc_message']        = $sender_message;
			}

			return $cart_item_data;
		}

		/**
		 * Custom add_to_cart handler for gift card product type
		 */
		public function add_to_cart_handler() {

			$item_data  = $this->build_cart_item_data();
			$product_id = $item_data['ywgc_product_id'];

			if ( ! $product_id ) {
				wc_add_notice( esc_html__( 'An error occurred while adding the product to the cart.', 'yith-woocommerce-gift-cards' ), 'error' );

				return false;
			}

			$added_to_cart = false;

			if ( $item_data['ywgc_is_digital'] ) {

				$recipients = $item_data['ywgc_recipients'];
				/**
				 * Check if all mandatory fields are filled or throw an error
				 */
				if ( YITH_YWGC()->mandatory_recipient() && is_array( $recipients ) && ! count( $recipients ) ) {
					wc_add_notice( esc_html__( 'Add a valid email address for the recipient', 'yith-woocommerce-gift-cards' ), 'error' );

					return false;
				}

				/**
				 * Validate all email addresses submitted
				 */
				$email_error = '';
				if ( YITH_YWGC()->mandatory_recipient() && $recipients ) {
					foreach ( $recipients as $recipient ) {

						if ( YITH_YWGC()->mandatory_recipient() && empty( $recipient ) ) {
							wc_add_notice( esc_html__( 'The recipient(s) email address is mandatory', 'yith-woocommerce-gift-cards' ), 'error' );

							return false;
						}

						if ( $recipient && ! filter_var( $recipient, FILTER_VALIDATE_EMAIL ) ) {
							$email_error .= '<br>' . $recipient;
						}
					}

					if ( $email_error ) {
						wc_add_notice( esc_html__( 'Email address not valid, please check the following: ', 'yith-woocommerce-gift-cards' ) . $email_error, 'error' );

						return false;
					}
				}

				/** The user can purchase 1 gift card with multiple recipient emails or [quantity] gift card for the same user.
				 * It's not possible to mix both, purchasing multiple instance of gift card with multiple recipients
				 * */
				$recipient_count = is_array( $item_data['ywgc_recipients'] ) ? count( $item_data['ywgc_recipients'] ) : 0;
				$quantity        = ( $recipient_count > 1 ) ? $recipient_count : ( isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1 );//phpcs:ignore WordPress.Security.NonceVerification

				if ( $recipient_count > 1 ) {
					$item_data_to_card = $item_data;

					for ( $i = 0; $i < $recipient_count; $i++ ) {

						$item_data_to_card['ywgc_recipients']     = array( $item_data['ywgc_recipients'][ $i ] );
						$item_data_to_card['ywgc_recipient_name'] = $item_data['ywgc_recipient_name'][ $i ];

						$added_to_cart = WC()->cart->add_to_cart( $product_id, 1, 0, array(), $item_data_to_card );
					}
				} else {
					$item_data['ywgc_recipient_name'] = is_array( $item_data['ywgc_recipient_name'] ) ? $item_data['ywgc_recipient_name'][0] : $item_data['ywgc_recipient_name'];
					$added_to_cart                    = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $item_data );

				}
			} elseif ( $item_data['ywgc_is_physical'] ) {
				/** The user can purchase 1 gift card with multiple recipient names or [quantity] gift card for the same user.
				 * It's not possible to mix both, purchasing multiple instance of gift card with multiple recipients
				 * */

				$recipient_name_count = is_array( $item_data['ywgc_recipient_name'] ) ? count( $item_data['ywgc_recipient_name'] ) : 0;
				$quantity             = ( $recipient_name_count > 1 ) ? $recipient_name_count : ( isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1 );//phpcs:ignore WordPress.Security.NonceVerification

				if ( $recipient_name_count > 1 ) {
					$item_data_to_card = $item_data;

					for ( $i = 0; $i < $recipient_name_count; $i++ ) {

						$item_data_to_card['ywgc_recipient_name'] = $item_data['ywgc_recipient_name'][ $i ];

						$added_to_cart = WC()->cart->add_to_cart( $product_id, 1, 0, array(), $item_data_to_card );
					}
				} else {
					$item_data['ywgc_recipient_name'] = is_array( $item_data['ywgc_recipient_name'] ) ? $item_data['ywgc_recipient_name'][0] : $item_data['ywgc_recipient_name'];
					$added_to_cart                    = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $item_data );

				}
			} else {
				$quantity      = isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1;//phpcs:ignore WordPress.Security.NonceVerification
				$added_to_cart = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $item_data );
			}

			if ( $added_to_cart ) {

				if ( isset( $item_data['ywgc_product_id'] ) ) {
					$product_id = $item_data['ywgc_product_id'];
				}
				$this->show_cart_message_on_added_product( $product_id, $quantity );
			}

			// If we added the product to the cart we can now optionally do a redirect.
			if ( wc_notice_count( 'error' ) == 0 ) {//phpcs:ignore
				$adding_to_cart = wc_get_product( $product_id );
				$url            = '';
				// If has custom URL redirect there.
				$url = apply_filters( 'woocommerce_add_to_cart_redirect', $url, $adding_to_cart );
				if ( $url ) {
					wp_safe_redirect( $url );
					exit;
				} elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
					if ( function_exists( 'wc_get_cart_url' ) ) {
						wp_safe_redirect( wc_get_cart_url() );
					} else {
						wp_safe_redirect( WC()->cart->get_cart_url() );
					}
					exit;
				}
			}

		}

		/**
		 * Set the real amount for the gift card product
		 *
		 * @param array $cart_item
		 *
		 * @since 1.5.0
		 * @return mixed
		 */
		public function show_cart_message_on_added_product( $product_id, $quantity = 1 ) {
			$param = array( $product_id => $quantity );
			wc_add_to_cart_message( $param, true );
		}

		/**
		 * Set the real amount for the gift card product
		 *
		 * @param array $cart_item
		 *
		 * @since 1.5.0
		 * @return mixed
		 */
		public function set_price_in_cart( $cart_item ) {

			if ( isset( $cart_item['data'] ) ) {

				if ( $cart_item['data'] instanceof WC_Product_Gift_Card && isset( $cart_item['ywgc_amount'] ) ) {

					$cart_item['data']->update_meta_data( 'price', $cart_item['ywgc_amount'] );
					$cart_item['data']->save_meta_data();
					$cart_item['data']->set_price( $cart_item['ywgc_amount'] );
				}
			}

			return $cart_item;
		}

		/**
		 * Update cart item when retrieving cart from session
		 *
		 * @param $session_data mixed Session data to add to cart
		 * @param $values       mixed Values stored in session
		 *
		 * @return mixed Session data
		 * @since 1.5.0
		 */
		public function get_cart_item_from_session( $session_data, $values ) {

			if ( isset( $values['ywgc_product_id'] ) && $values['ywgc_product_id'] ) {

				$session_data['ywgc_product_id']              = isset( $values['ywgc_product_id'] ) ? $values['ywgc_product_id'] : '';
				$session_data['ywgc_amount']                  = isset( $values['ywgc_amount'] ) ? $values['ywgc_amount'] : '';
				$session_data['ywgc_amount_without_discount'] = isset( $values['ywgc_amount_without_discount'] ) ? $values['ywgc_amount_without_discount'] : '';
				$session_data['ywgc_is_manual_amount']        = isset( $values['ywgc_is_manual_amount'] ) ? $values['ywgc_is_manual_amount'] : false;
				$session_data['ywgc_is_digital']              = isset( $values['ywgc_is_digital'] ) ? $values['ywgc_is_digital'] : false;
				$session_data['ywgc_currency']                = isset( $values['ywgc_currency'] ) ? $values['ywgc_currency'] : false;
				$session_data['ywgc_default_currency_amount'] = isset( $values['ywgc_default_currency_amount'] ) ? $values['ywgc_default_currency_amount'] : false;
				$session_data['ywgc_amount_index']            = isset( $values['ywgc_amount_index'] ) ? $values['ywgc_amount_index'] : false;

				if ( $session_data['ywgc_is_digital'] ) {
					$session_data['ywgc_recipients']     = isset( $values['ywgc_recipients'] ) ? $values['ywgc_recipients'] : '';
					$session_data['ywgc_sender_name']    = isset( $values['ywgc_sender_name'] ) ? $values['ywgc_sender_name'] : '';
					$session_data['ywgc_recipient_name'] = isset( $values['ywgc_recipient_name'] ) ? $values['ywgc_recipient_name'] : '';
					$session_data['ywgc_message']        = isset( $values['ywgc_message'] ) ? $values['ywgc_message'] : '';

					$session_data['ywgc_has_custom_design'] = isset( $values['ywgc_has_custom_design'] ) ? $values['ywgc_has_custom_design'] : false;
					$session_data['ywgc_design_type']       = isset( $values['ywgc_design_type'] ) ? $values['ywgc_design_type'] : '';
					if ( $session_data['ywgc_has_custom_design'] ) {
						$session_data['ywgc_design'] = isset( $values['ywgc_design'] ) ? $values['ywgc_design'] : '';
					}

					$session_data['ywgc_postdated'] = isset( $values['ywgc_postdated'] ) ? $values['ywgc_postdated'] : false;
					if ( $session_data['ywgc_postdated'] ) {
						$session_data['ywgc_delivery_date'] = isset( $values['ywgc_delivery_date'] ) ? $values['ywgc_delivery_date'] : false;
					}

					$session_data['ywgc_delivery_notification_checkbox'] = isset( $values['ywgc_delivery_notification_checkbox'] ) ? $values['ywgc_delivery_notification_checkbox'] : 'off';

				}

				if ( isset( $values['ywgc_amount'] ) ) {

					/**
					 * APPLY_FILTERS: yith_ywgc_set_cart_item_price
					 *
					 * Filter the gift card item price in the cart session.
					 *
					 * @param float the gift card amount
					 * @param array $values array with the values
					 *
					 * @return float
					 */
					$product_price = apply_filters( 'yith_ywgc_set_cart_item_price', $values['ywgc_amount'], $values );

					yit_set_prop( $session_data['data'], 'price', $product_price );
				}
			}

			return $session_data;
		}

		/**
		 * @param                       $item_id
		 * @param WC_Order_Item_Product $item
		 *
		 * @throws Exception
		 */

		public function append_gift_card_data_to_new_order_item( $item_id, $item ) {

			if ( ! $item ) {
				return;
			}

			if ( 'line_item' === $item->get_type() ) {

				if ( isset( $item->legacy_values ) ) {
					$this->append_gift_card_data_to_order_item( $item_id, $item->legacy_values );
				}
			}
		}

		/**
		 * Append data to order item
		 *
		 * @param int   $item_id
		 * @param array $values
		 *
		 * @throws Exception
		 * @since  1.5.0
		 * @author Lorenzo Giuffrida
		 */
		public function append_gift_card_data_to_order_item( $item_id, $values ) {

			if ( ! isset( $values['ywgc_product_id'] ) ) {
				return;
			}

			/**
			 * Store all fields related to Gift Cards
			 */

			foreach ( $values as $key => $value ) {
				if ( strpos( $key, 'ywgc_' ) == 0 ) {//phpcs:ignore
					$meta_key = '_' . $key;
					wc_update_order_item_meta( $item_id, $meta_key, $value );
				}
			}

			/**
			 * Store subtotal and subtotal taxes applied to the gift card
			 */
			wc_update_order_item_meta( $item_id, '_ywgc_subtotal', $values['line_subtotal'] );
			wc_update_order_item_meta( $item_id, '_ywgc_subtotal_tax', $values['line_subtotal_tax'] );

			/**
			 * Store the plugin version for future use
			 */
			wc_update_order_item_meta( $item_id, '_ywgc_version', YITH_YWGC_VERSION );

		}

		/**
		 * Calculate the cart total for the mini cart - Fix calculation issues when adding a gift card to the cart
		 */
		public function calculate_cart_total_in_the_mini_cart() {

			foreach ( WC()->cart->get_cart() as $cart_item ) {
				$_product = wc_get_product( $cart_item['product_id'] );
				if ( $_product->is_type( 'gift-card' ) ) {
					WC()->cart->calculate_totals();
					break;
				}
			}

		}

	}
}

/**
 * Unique access to instance of YITH_YWGC_Cart_Checkout class
 *
 * @return YITH_YWGC_Cart_Checkout|YITH_YWGC_Cart_Checkout_Premium|YITH_YWGC_Cart_Checkout_Extended
 * @since 2.0.0
 */
function YITH_YWGC_Cart_Checkout() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	if ( defined( 'YITH_YWGC_PREMIUM' ) ) {
		$instance = YITH_YWGC_Cart_Checkout_Premium::get_instance();
	} elseif ( defined( 'YITH_YWGC_EXTENDED' ) ) {
		$instance = YITH_YWGC_Cart_Checkout_Extended::get_instance();
	} else {
		$instance = YITH_YWGC_Cart_Checkout::get_instance();
	}

	return $instance;
}
