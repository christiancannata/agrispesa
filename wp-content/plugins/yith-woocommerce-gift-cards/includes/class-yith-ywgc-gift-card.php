<?php
/**
 * Class to handle the gift card object
 *
 * @package YITH\GiftCards\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWGC_Gift_Card' ) ) {
	/**
	 * YITH_YWGC_Gift_Card class
	 *
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_YWGC_Gift_Card {

		const E_GIFT_CARD_NOT_EXIST       = 100;
		const E_GIFT_CARD_NOT_YOURS       = 101;
		const E_GIFT_CARD_ALREADY_APPLIED = 102;
		const E_GIFT_CARD_EXPIRED         = 103;
		const E_GIFT_CARD_DISABLED        = 104;
		const E_GIFT_CARD_DISMISSED       = 105;
		const E_GIFT_CARD_INVALID_REMOVED = 106;

		const GIFT_CARD_SUCCESS                              = 200;
		const GIFT_CARD_REMOVED                              = 201;
		const GIFT_CARD_NOT_ALLOWED_FOR_PURCHASING_GIFT_CARD = 202;

		const META_ORDER_ID      = '_ywgc_order_id';
		const META_AMOUNT_TOTAL  = '_ywgc_amount_total';
		const META_BALANCE_TOTAL = '_ywgc_balance_total';

		const META_CUSTOMER_ID = '_ywgc_customer_id'; // Refers to id of the customer that purchase the gift card.

		const STATUS_ENABLED   = 'publish';
		const STATUS_DISMISSED = 'ywgc-dismissed';

		const META_SENDER_NAME       = '_ywgc_sender_name';
		const META_RECIPIENT_NAME    = '_ywgc_recipient_name';
		const META_RECIPIENT_EMAIL   = '_ywgc_recipient';
		const META_MESSAGE           = '_ywgc_message';
		const META_CURRENCY          = '_ywgc_currency';
		const META_VERSION           = '_ywgc_version';
		const META_DELIVERY_DATE     = '_ywgc_delivery_date';
		const META_SEND_DATE         = '_ywgc_delivery_send_date';
		const META_IS_DIGITAL        = '_ywgc_is_digital';
		const META_HAS_CUSTOM_DESIGN = '_ywgc_has_custom_design';
		const META_DESIGN_TYPE       = '_ywgc_design_type';
		const META_DESIGN            = '_ywgc_design';
		const META_EXPIRATION        = '_ywgc_expiration';
		const META_INTERNAL_NOTES    = '_ywgc_internal_notes';

		const STATUS_PRE_PRINTED    = 'ywgc-pre-printed';
		const STATUS_DISABLED       = 'ywgc-disabled';
		const STATUS_CODE_NOT_VALID = 'ywgc-code-not-valid';

		/**
		 * ID
		 *
		 * @var int the gift card id
		 */
		public $ID = 0;

		/**
		 * Product_id
		 *
		 * @var int  the product id
		 */
		public $product_id = 0;

		/**
		 * Order_id
		 *
		 * @var int the order id
		 */
		public $order_id = 0;

		/**
		 * Gift_card_number
		 *
		 * @var string the gift card code
		 */
		public $gift_card_number = '';

		/**
		 * Total_amount
		 *
		 * @var float the gift card amount
		 */
		public $total_amount = 0.00;

		/**
		 * Total_balance
		 *
		 * @var float the gift card current balance
		 */
		protected $total_balance = 0.00;

		/**
		 * Status
		 *
		 * @var string the gift card post status
		 */
		public $status = 'publish';

		/**
		 * Recipient
		 *
		 * @var string the recipient for digital gift cards
		 */
		public $recipient = '';

		/**
		 * Customer_id
		 *
		 * @var int
		 */
		public $customer_id = 0;

		/**
		 * Delivery_date
		 *
		 * @var string the expected delivery date
		 */
		public $delivery_date = '';

		/**
		 * Delivery_send_date
		 *
		 * @var string the real delivery date
		 */
		public $delivery_send_date = '';

		/**
		 * Sender_name
		 *
		 * @var string the sender for digital gift cards
		 */
		public $sender_name = '';

		/**
		 * Recipient_name
		 *
		 * @var string the sender for digital gift cards
		 */
		public $recipient_name = '';

		/**
		 * Message
		 *
		 * @var string the message for digital gift cards
		 */
		public $message = '';

		/**
		 * Has_custom_design
		 *
		 * @var bool the digital gift cards use the default image
		 */
		public $has_custom_design = true;

		/**
		 * Design_type
		 *
		 * @var string the type of design chosen by the user. Could be :
		 *             'default' for standard image
		 *             'custom' for image uploaded by the user
		 *             'template' for template chosen from the desing list
		 */
		public $design_type = 'default';

		/**
		 * Design
		 *
		 * @var string the custom image for digital gift cards
		 */
		public $design = null;

		/**
		 * Currency
		 *
		 * @var string the currency used when the gift card is created
		 */
		public $currency = '';

		/**
		 * Version
		 *
		 * Plugin version that created the gift card
		 *
		 * @var version version.
		 */
		public $version = '';

		/**
		 * Is_digital
		 *
		 * @var bool the gift card is digital
		 */
		public $is_digital = false;

		/**
		 * Expiration
		 *
		 * @var int the timestamp for gift card valid use
		 */
		public $expiration = 0;

		/**
		 * Internal_notes
		 *
		 * @var string internal note
		 */
		public $internal_notes = '';

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @param array $args The arguments.
		 *
		 * @since  1.0
		 */
		public function __construct( $args = array() ) {
			$post = false;

			if ( isset( $args['ID'] ) ) {
				$post = get_post( $args['ID'] );
			} elseif ( isset( $args['gift_card_number'] ) ) {
				$this->gift_card_number = $args['gift_card_number'];

				$post = get_posts(
					array(
						'title'     => $args['gift_card_number'],
						'post_type' => YWGC_CUSTOM_POST_TYPE_NAME,
					)
				);

				if ( is_array( $post ) && isset( $post[0] ) ) {
					$post = $post[0];
				}
			}

			if ( $post && is_object( $post ) ) {
				$this->ID = $post->ID;

				/**
				 * APPLY_FILTERS: yith_ywgc_gift_card_number
				 *
				 * Filter the gift card post title.
				 *
				 * @param string the post title
				 * @param object $post the gift card post object
				 *
				 * @return object
				 */
				$this->gift_card_number = apply_filters( 'yith_ywgc_gift_card_number', $post->post_title, $post );
				$this->product_id       = $post->post_parent;

				// Backward compatibility check with gift cards created with free version.
				$old_order_id = get_post_meta( $post->ID, '_gift_card_order_id', true );

				if ( ! empty( $old_order_id ) ) {
					$this->order_id = $old_order_id;
				} else {
					$this->order_id = get_post_meta( $post->ID, self::META_ORDER_ID, true );
				}

				$total_amount = get_post_meta( $post->ID, self::META_AMOUNT_TOTAL, true );

				if ( ! empty( $total_amount ) ) {
					$this->total_amount = $total_amount;
				} else {
					$amount     = get_post_meta( $post->ID, '_ywgc_amount', true );
					$amount_tax = get_post_meta( $post->ID, '_ywgc_amount_tax', true );
					$this->update_amount( (float) $amount + (float) $amount_tax );
				}

				/**
				 * APPLY_FILTERS: yith_ywgc_gift_card_total_balance
				 *
				 * Filter the gift card total balance on post creation.
				 *
				 * @param float the total balance
				 * @param object $post the gift card post object
				 * @param string the gift card code
				 *
				 * @return float
				 */
				$total_balance = apply_filters( 'yith_ywgc_gift_card_total_balance', get_post_meta( $post->ID, self::META_BALANCE_TOTAL, true ), $post, $this->gift_card_number );

				if ( ! empty( $total_balance ) ) {
					$this->total_balance = $total_balance;
				} else {
					$balance     = get_post_meta( $post->ID, '_ywgc_amount_balance', true );
					$balance_tax = get_post_meta( $post->ID, '_ywgc_amount_balance_tax', true );
					$balance     = empty( $balance ) ? 0 : $balance;
					$balance_tax = empty( $balance_tax ) ? 0 : $balance_tax;
					$this->update_balance( (float) $balance + (float) $balance_tax );
				}

				$this->customer_id = get_post_meta( $post->ID, self::META_CUSTOMER_ID, true );

				$this->status = $post->post_status;
			}

			// If $args is related to an existent gift card, load their data.
			if ( $this->ID ) {
				$this->sender_name        = get_post_meta( $this->ID, self::META_SENDER_NAME, true );
				$this->recipient_name     = get_post_meta( $this->ID, self::META_RECIPIENT_NAME, true );
				$this->recipient          = get_post_meta( $this->ID, self::META_RECIPIENT_EMAIL, true );
				$this->message            = get_post_meta( $this->ID, self::META_MESSAGE, true );
				$this->currency           = get_post_meta( $this->ID, self::META_CURRENCY, true );
				$this->version            = get_post_meta( $this->ID, self::META_VERSION, true );
				$this->delivery_date      = get_post_meta( $this->ID, self::META_DELIVERY_DATE, true );
				$this->delivery_send_date = get_post_meta( $this->ID, self::META_SEND_DATE, true );
				$this->is_digital         = get_post_meta( $this->ID, self::META_IS_DIGITAL, true );
				$this->has_custom_design  = get_post_meta( $this->ID, self::META_HAS_CUSTOM_DESIGN, true );
				$this->design_type        = get_post_meta( $this->ID, self::META_DESIGN_TYPE, true );
				$this->design             = get_post_meta( $this->ID, self::META_DESIGN, true );
				$this->expiration         = get_post_meta( $this->ID, self::META_EXPIRATION, true );
				$this->internal_notes     = get_post_meta( $this->ID, self::META_INTERNAL_NOTES, true );
			}
		}

		/**
		 * Register the order in the list of orders where the gift card was used
		 *
		 * @param int $order_id the order ID.
		 *
		 * @since  1.0.0
		 */
		public function register_order( $order_id ) {
			if ( $this->ID ) {
				// assign the order to this gift cards...
				$orders   = $this->get_registered_orders();
				$orders[] = $order_id;
				update_post_meta( $this->ID, YWGC_META_GIFT_CARD_ORDERS, $orders );

				// assign the customer to this gift cards...
				$order         = wc_get_order( $order_id );
				$customer_user = $order->get_meta( 'customer_user' );
				$this->register_user( $customer_user );
			}
		}

		/**
		 * Check if the user is registered as the gift card owner
		 *
		 * @param int $user_id user_id.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_registered_user( $user_id ) {
			$customer_users = get_post_meta( $this->ID, YWGC_META_GIFT_CARD_CUSTOMER_USER );

			return in_array( $user_id, $customer_users, true );
		}

		/**
		 * Register an user as the gift card owner(may be one or more)
		 *
		 * @param int $user_id user_id.
		 *
		 * @since  1.0.0
		 */
		public function register_user( $user_id ) {
			if ( 0 == $user_id ) {
				return;
			}

			if ( $this->is_registered_user( $user_id ) ) {
				// the user is a register user.
				return;
			}

			update_post_meta( $this->ID, YWGC_META_GIFT_CARD_CUSTOMER_USER, $user_id );
		}

		/**
		 * Retrieve the list of orders where the gift cards was used
		 *
		 * @return array|mixed
		 * @since  1.0.0
		 */
		public function get_registered_orders() {
			$orders = array();

			if ( $this->ID ) {
				$orders = get_post_meta( $this->ID, YWGC_META_GIFT_CARD_ORDERS, true );

				if ( ! $orders ) {
					$orders = array();
				}
			}

			return array_unique( $orders );
		}

		/**
		 * Retrieve the history of the gift card redemption using the shortcode
		 *
		 * @return array|mixed
		 * @since  1.0.0
		 */
		public function get_redemption_history() {
			$redemptions = array();

			if ( $this->ID ) {
				$redemptions = get_post_meta( $this->ID, 'ywgc_redemption_history', true );

				if ( ! $redemptions ) {
					$redemptions = array();
				}
			}

			return array_unique( $redemptions );
		}

		/**
		 * Check if the gift card has enough balance to cover the amount requested
		 *
		 * @param mixed $amount int the amount to be deducted from current gift card balance.
		 *
		 * @return bool the gift card has enough credit
		 */
		public function has_sufficient_credit( $amount ) {
			return $this->total_balance >= $amount;
		}

		/**
		 * Retrieve the gift card code
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function get_code() {
			return $this->gift_card_number;
		}

		/**
		 * The gift card exists
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function exists() {
			return $this->ID > 0;
		}

		/**
		 * Retrieve if a gift card is enabled
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_enabled() {
			return self::STATUS_ENABLED === $this->status;
		}

		/**
		 * Retrieve if a gift card is disabled
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_disabled() {
			return self::STATUS_DISABLED === $this->status;
		}

		/**
		 * Check the gift card ownership
		 *
		 * @param int|string $user user id or user email.
		 *
		 * @return bool
		 */
		public function is_owner( $user ) {
			// todo perform a real check for gift card ownership.
			return true;
		}

		/**
		 * Check if the gift card can be used
		 *
		 * @return bool
		 */
		public function can_be_used() {
			$can_use = $this->exists() && $this->is_enabled() && ! $this->is_expired();

			/**
			 * APPLY_FILTERS: yith_ywgc_gift_card_can_be_used
			 *
			 * Filter the condition to check if the gift card can be used.
			 *
			 * @param bool $can_use true if it can be used, false if not
			 * @param object the gift card object
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_ywgc_gift_card_can_be_used', $can_use, $this );
		}

		/**
		 * Update and store the new balance
		 *
		 * @param float $new_amount new_amount.
		 */
		public function update_balance( $new_amount ) {
			$this->total_balance = $new_amount;

			if ( $this->ID ) {
				update_post_meta( $this->ID, self::META_BALANCE_TOTAL, $this->total_balance );
			}
		}

		/**
		 * Update and store the new amount
		 *
		 * @param float $new_amount new_amount.
		 */
		public function update_amount( $new_amount ) {
			$this->total_amount = $new_amount;

			if ( $this->ID ) {
				update_post_meta( $this->ID, self::META_AMOUNT_TOTAL, $this->total_amount );
			}
		}

		/**
		 * Retrieve the current gift card balance
		 *
		 * @return float|mixed
		 */
		public function get_balance() {
			/**
			 * APPLY_FILTERS: ywgc_get_total_balance
			 *
			 * Filter the gift card total balance getter.
			 *
			 * @param float the total balance rounded
			 * @param float the total balance
			 * @param object the gift card post object
			 *
			 * @return float
			 */
			return apply_filters( 'ywgc_get_total_balance', round( (float) $this->total_balance, wc_get_price_decimals() ), $this->total_balance, $this );
		}

		/**
		 * The gift card product is virtual
		 */
		public function is_virtual() {
			return $this->is_digital;
		}

		/**
		 * Get_gift_card_error
		 *
		 * @param  mixed $err_code err_code.
		 * @return filter
		 */
		public function get_gift_card_error( $err_code ) {
			$err = '';

			switch ( $err_code ) {
				case self::E_GIFT_CARD_NOT_EXIST:
					// translators: %s is the gift card code.
					$err = sprintf( esc_html__( 'The gift card code %s does not exist!', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
					break;

				case self::E_GIFT_CARD_NOT_YOURS:
					// translators: %s is the gift card code.
					$err = sprintf( esc_html__( 'Sorry, it seems that the gift card code "%s" is not yours and cannot be used for this order.', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
					break;

				case self::E_GIFT_CARD_ALREADY_APPLIED:
					// translators: %s is the gift card code.
					$err = sprintf( esc_html__( 'The gift card code %s has already been applied!', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
					break;

				case self::E_GIFT_CARD_EXPIRED:
					// translators: %s is the gift card code.
					$err = sprintf( esc_html__( 'Sorry, the gift card code %s is expired and cannot be used.', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
					break;

				case self::E_GIFT_CARD_DISABLED:
					// translators: %s is the gift card code.
					$err = sprintf( esc_html__( 'Sorry, the gift card code %s is currently disabled and cannot be used.', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
					break;

				case self::E_GIFT_CARD_DISMISSED:
					// translators: %s is the gift card code.
					$err = sprintf( esc_html__( 'Sorry, the gift card code %s is no longer valid!', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
					break;

				case self::E_GIFT_CARD_INVALID_REMOVED:
					// translators: %s is the gift card code.
					$err = sprintf( esc_html__( 'Sorry, it seems that the gift card code %s is invalid - it has been removed from your cart.', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
					break;

				case self::GIFT_CARD_NOT_ALLOWED_FOR_PURCHASING_GIFT_CARD:
					$err = esc_html__( 'Gift card codes cannot be used to purchase other gift cards', 'yith-woocommerce-gift-cards' );
					break;
			}

			/**
			 * APPLY_FILTERS: yith_ywgc_get_gift_card_error
			 *
			 * Filter the gift card error message.
			 *
			 * @param string $err the error message
			 * @param string $err_code the error code
			 * @param object the gift card object
			 *
			 * @return string
			 */
			return apply_filters( 'yith_ywgc_get_gift_card_error', $err, $err_code, $this );
		}

		/**
		 * Retrieve a message for a successful gift card status
		 *
		 * @param string $err_code error code.
		 *
		 * @return string
		 */
		public function get_gift_card_message( $err_code ) {
			$err = '';

			switch ( $err_code ) {
				case self::GIFT_CARD_SUCCESS:
					$err = esc_html__( 'Gift card code successfully applied.', 'yith-woocommerce-gift-cards' );
					break;

				case self::GIFT_CARD_REMOVED:
					$err = esc_html__( 'Gift card code successfully removed.', 'yith-woocommerce-gift-cards' );
					break;

				case self::GIFT_CARD_NOT_ALLOWED_FOR_PURCHASING_GIFT_CARD:
					$err = esc_html__( 'Gift card codes cannot be used to purchase other gift cards', 'yith-woocommerce-gift-cards' );
					break;
			}

			/**
			 * APPLY_FILTERS: yith_ywgc_get_gift_card_message
			 *
			 * Filter the error message when a gift card is applied to the cart.
			 *
			 * @param string $err the error message
			 * @param string $err_code the error code
			 * @param object the gift card object
			 *
			 * @return string
			 */
			return apply_filters( 'yith_ywgc_get_gift_card_message', $err, $err_code, $this );
		}

		/**
		 * Check if the gift card has been sent
		 */
		public function has_been_sent() {
			return $this->delivery_send_date;
		}

		/**
		 * Set the gift card as sent
		 */
		public function set_as_sent() {
			$this->delivery_send_date = current_time( 'timestamp' ); //phpcs:ignore --timestamp is discouraged
			update_post_meta( $this->ID, self::META_SEND_DATE, $this->delivery_send_date );
		}

		/**
		 * Set_as_code_not_valid
		 *
		 * @return void
		 */
		public function set_as_code_not_valid() {
			$this->gift_card_number = 'NOT VALID';
			$this->set_status( self::STATUS_CODE_NOT_VALID );
		}

		/**
		 * Set the gift card as pre-printed i.e. the code is manually entered instead of being auto generated
		 */
		public function set_as_pre_printed() {
			$this->set_status( self::STATUS_PRE_PRINTED );
		}

		/**
		 * Check if the gift card is pre-printed
		 */
		public function is_pre_printed() {
			return self::STATUS_PRE_PRINTED === $this->status;
		}

		/**
		 * Check if the gift card is expired
		 */
		public function is_expired() {
			if ( ! $this->expiration ) {
				return false;
			}

			return time() > $this->expiration;
		}

		/**
		 * Set the gift card enabled status
		 *
		 * @param bool|false $enabled enabled.
		 *
		 * @since  1.0.0
		 */
		public function set_enabled_status( $enabled = false ) {
			$current_status = $this->is_enabled();

			if ( $current_status === $enabled ) {
				return;
			}

			// If the gift card is dismissed, stop now.
			if ( $this->is_dismissed() ) {
				return;
			}

			$this->set_status( $enabled ? 'publish' : self::STATUS_DISABLED );
		}

		/**
		 * Set the gift card status
		 *
		 * @param string $status status.
		 *
		 * @since  1.0.0
		 */
		public function set_status( $status ) {
			$this->status = $status;

			if ( $this->ID ) {
				$args = array(
					'ID'          => $this->ID,
					'post_status' => $status,
				);

				wp_update_post( $args );
			}
		}

		/**
		 * Save the current object
		 */
		public function save() {
			// Create post object args.
			$args = array(
				'post_title'  => $this->gift_card_number,
				'post_status' => $this->status,
				'post_type'   => YWGC_CUSTOM_POST_TYPE_NAME,
				'post_parent' => $this->product_id,
			);

			if ( 0 == $this->ID ) {
				// Insert the post into the database.
				$this->ID = wp_insert_post( $args );
			} else {
				$args['ID'] = $this->ID;
				$this->ID   = wp_update_post( $args );
			}

			/**
			 * APPLY_FILTERS: ywgc_save_total_balance
			 *
			 * Filter the gift card total balance saved.
			 *
			 * @param float the total balance rounded
			 * @param float the total balance
			 * @param object the gift card post object
			 *
			 * @return float
			 */
			$total_balance_rounded = apply_filters( 'ywgc_save_total_balance', round( (float) $this->total_balance, wc_get_price_decimals() ), $this->total_balance, $this );

			/**
			 * APPLY_FILTERS: ywgc_save_total_amount
			 *
			 * Filter the gift card total amount saved.
			 *
			 * @param float the total amount rounded
			 * @param float the total amount
			 * @param object the gift card post object
			 *
			 * @return float
			 */
			$total_amount_rounded = apply_filters( 'ywgc_save_total_amount', round( (float) $this->total_amount, wc_get_price_decimals() ), $this->total_amount, $this );

			// Save Gift Card post_meta.
			update_post_meta( $this->ID, self::META_ORDER_ID, $this->order_id );
			update_post_meta( $this->ID, self::META_CUSTOMER_ID, $this->customer_id );
			update_post_meta( $this->ID, self::META_BALANCE_TOTAL, $total_balance_rounded );
			update_post_meta( $this->ID, self::META_AMOUNT_TOTAL, $total_amount_rounded );

			$date_format = apply_filters( 'yith_wcgc_date_format', 'Y-m-d' );

			update_post_meta( $this->ID, self::META_SENDER_NAME, $this->sender_name );
			update_post_meta( $this->ID, self::META_RECIPIENT_NAME, $this->recipient_name );
			update_post_meta( $this->ID, self::META_RECIPIENT_EMAIL, $this->recipient );
			update_post_meta( $this->ID, self::META_MESSAGE, str_replace( '\\', '', $this->message ) );
			update_post_meta( $this->ID, self::META_CURRENCY, $this->currency );
			update_post_meta( $this->ID, self::META_VERSION, $this->version );

			$delivery_date_format = date_i18n( $date_format, time() );
			update_post_meta( $this->ID, '_ywgc_delivery_date_formatted', $delivery_date_format );

			update_post_meta( $this->ID, self::META_HAS_CUSTOM_DESIGN, $this->has_custom_design );

			$expiration_date_format = ( '0' !== $this->expiration ) ? date_i18n( $date_format, $this->expiration ) : '';

			update_post_meta( $this->ID, self::META_EXPIRATION, $this->expiration );
			update_post_meta( $this->ID, '_ywgc_expiration_date_formatted', $expiration_date_format );

			update_post_meta( $this->ID, self::META_DESIGN_TYPE, $this->design_type );
			update_post_meta( $this->ID, self::META_DESIGN, $this->design );

			update_post_meta( $this->ID, self::META_IS_DIGITAL, $this->is_digital );
			update_post_meta( $this->ID, self::META_INTERNAL_NOTES, $this->internal_notes );

			return $this->ID;
		}

		/**
		 * Retrieve the status label for every gift card status
		 *
		 * @return string
		 */
		public function get_status_label() {
			$label = '';

			switch ( $this->status ) {
				case self::STATUS_DISABLED:
					$label = esc_html__( 'The gift card has been disabled', 'yith-woocommerce-gift-cards' );
					break;

				case self::STATUS_ENABLED:
					$label = esc_html__( 'Valid', 'yith-woocommerce-gift-cards' );
					break;

				case self::STATUS_DISMISSED:
					$label = esc_html__( 'No longer valid, replaced by another code', 'yith-woocommerce-gift-cards' );
					break;
			}

			return $label;
		}

		/**
		 * The gift card is nulled and no more usable
		 *
		 * @since  1.0.0
		 */
		public function set_dismissed_status() {
			$this->set_status( self::STATUS_DISMISSED );
		}

		/**
		 * The gift card code is duplicate and the gift card is not usable until a new, valid, code is set
		 *
		 * @since  1.0.0
		 */
		public function set_duplicated_status() {
			$this->set_status( self::STATUS_DISMISSED );
		}

		/**
		 * Check if the gift card is dismissed
		 *
		 * @since  1.0.0
		 */
		public function is_dismissed() {
			return self::STATUS_DISMISSED === $this->status;
		}

		/**
		 * Get_formatted_date
		 *
		 * @param  mixed $date date.
		 * @return string
		 */
		public function get_formatted_date( $date ) {
			$date_format = apply_filters( 'yith_wcgc_date_format', 'Y-m-d' );

			$date = ! is_numeric( $date ) ? strtotime( $date ) : $date;

			$formatted_date = date_i18n( $date_format, $date );

			return $formatted_date;
		}
	}
}
