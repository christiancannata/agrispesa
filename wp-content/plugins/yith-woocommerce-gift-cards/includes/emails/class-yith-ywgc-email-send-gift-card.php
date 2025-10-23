<?php // phpcs:ignore WordPress.NamingConventions
/**
 * YITH_YWGC_Email_Send_Gift_Card class
 *
 * @package yith-woocommerce-gift-cards\lib\emails
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_Email' ) ) {
	require_once WC()->plugin_path() . '/includes/emails/class-wc-email.php';
}

if ( ! class_exists( 'YITH_YWGC_Email_Send_Gift_Card' ) ) {
	/**
	 * Create and send a digital gift card to the specific recipient
	 *
	 * @since 0.1
	 * @extends \WC_Email
	 */
	class YITH_YWGC_Email_Send_Gift_Card extends YITH_YWGC_Mail {

		/**
		 * An introductory message from the shop owner
		 *
		 * @var introductory_text introductory_text.
		 */
		public $introductory_text;

		/**
		 * Set email defaults
		 *
		 * @since 0.1
		 */
		public function __construct() {
			// set ID, this simply needs to be a unique name.
			$this->id = 'ywgc-email-send-gift-card';

			// this is the title in WooCommerce Email settings.
			$this->title = __( 'YITH Gift Cards - Gift Card Delivery', 'yith-woocommerce-gift-cards' );

			// this is the description in WooCommerce email settings.
			$this->description = __( 'Send the digital gift card to the email address selected during the purchase', 'yith-woocommerce-gift-cards' );

			// these are the default heading and subject lines that can be overridden using the settings.
			$this->heading = __( 'Your gift card', 'yith-woocommerce-gift-cards' );
			$this->subject = __( '[{site_title}] You have received a gift card', 'yith-woocommerce-gift-cards' );

			// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar.
			$this->template_html  = 'emails/send-gift-card.php';
			$this->template_plain = 'emails/plain/send-gift-card.php';

			$this->introductory_text = __( 'Hi {recipient_name}, you have received this gift card from {sender}, use it on our online shop.', 'yith-woocommerce-gift-cards' );

			// Trigger on specific action call.
			add_action( 'ywgc_email_send_gift_card_notification', array( $this, 'trigger', ), 10, 2 );

			parent::__construct();

			$this->email_type = 'html';
		}


		/**
		 * Get email subject.
		 *
		 * @return string
		 */
		public function get_subject() {
			$gift_card = $this->get_gift_card();

			$default_subject = $this->get_default_subject();
			$subject         = str_replace(
				array(
					'{recipient_name}',
					'{sender_name}',
					'{order_id}',
				),
				array(
					$gift_card->recipient_name ? $gift_card->recipient_name : '',
					$gift_card->sender_name ? $gift_card->sender_name : '',
					$gift_card->order_id ? $gift_card->order_id : '',
				),
				$this->format_string( $this->get_option( 'subject', $default_subject ) )
			);
			return apply_filters( 'woocommerce_email_subject_' . $this->id, $subject, $this->object );
		}

		/**
		 * Get email heading.
		 *
		 * @return string
		 */
		public function get_heading() {
			$gift_card = $this->get_gift_card();

			$default_heading = $this->get_default_heading();
			$heading         = str_replace(
				array(
					'{recipient_name}',
					'{sender_name}',
				),
				array(
					$gift_card->recipient_name ? $gift_card->recipient_name : '',
					$gift_card->sender_name ? $gift_card->sender_name : '',
				),
				$this->format_string( $this->get_option( 'heading', $default_heading ) )
			);
			return apply_filters( 'woocommerce_email_heading_' . $this->id, $heading, $this->object );
		}

		/**
		 * Send the digital gift card to the recipient
		 *
		 * @param int|YWGC_Gift_Card_Premium|YITH_YWGC_Gift_Card $object it's the order id or the gift card id or the gift card instance to be sent
		 *
		 * @return bool|void
		 */
		public function trigger( $object, $case = '' ) {
			$result = false;

			if ( is_numeric( $object ) ) {
				$post_type = get_post_type( $object );

				if ( 'shop_order' === $post_type ) {

					$gift_ids = array();
					// Check for order item that belong to a gift card and send them individually.
					$the_order = wc_get_order( $object );

					$line_items = $the_order->get_items( 'line_item' );

					foreach ( $line_items as $order_item_id => $order_item_data ) {

						$product_id = $order_item_data['product_id'];
						$product    = wc_get_product( $product_id );

						// skip all item that belong to product other than the gift card type.
						if ( ! $product instanceof WC_Product_Gift_Card ) {
							continue;
						}

						//  Check if current product, of type gift card, has a discount code associated to it.
						$gift_ids = array_merge( $gift_ids, ywgc_get_order_item_giftcards( $order_item_id ) );
					}

					if ( $gift_ids ) {
						// Trigger an email for every gift card associated.
						foreach ( $gift_ids as $gift_id ) {
							$this->trigger( $gift_id );
						}
						return true;
					}
				} elseif ( YWGC_CUSTOM_POST_TYPE_NAME === $post_type ) {

					$object = new YITH_YWGC_Gift_Card( array( 'ID' => $object ) );
				} else {
					return false;
				}
			}

			if ( ! ( $object instanceof YITH_YWGC_Gift_Card ) ) {
				return false;
			}

			if ( ! $object->exists() ) {
				return false;
			}

			$this->case        = $case;
			$this->object      = $object;
			$this->recipient   = $object->recipient;
			$recipient_name    = $this->object->recipient_name ? $this->object->recipient_name : '';
			$sender_name       = $this->object->sender_name ? $this->object->sender_name : __( 'a friend', 'yith-woocommerce-gift-cards' );
			$gifted_product_id = isset( $this->object->present_product_id ) && ! empty( $this->object->present_product_id ) ? $this->object->present_product_id : $this->object->product_id;
			$product_object    = wc_get_product( $gifted_product_id );
			$product_name      = isset( $product_object ) && ! empty( $product_object ) ? $product_object->get_name() : '';

			$this->introductory_text = $this->get_option( 'introductory_text', __( 'Hi {recipient_name}, you have received this gift card from {sender}, use it on our online shop.', 'yith-woocommerce-gift-cards' ) );

			$this->introductory_text = str_replace(
				array(
					'{sender}',
					'{recipient_name}',
					'{product_name}',
					'{gift_card_number}',
					'{total_amount}',
					'{message}',
				),
				array(
					$sender_name,
					$recipient_name,
					$product_name,
					$object->gift_card_number,
					wc_price( $object->total_amount ),
					$object->message,
				),
				$this->introductory_text
			);

			if ( $this->is_enabled() ) {
				$result = $this->send(
					$this->get_recipient(),
					$this->get_subject(),
					$this->get_content( $case ),
					$this->get_headers(),
					$this->get_attachments()
				);
			}

			/**
			 * APPLY_FILTERS: yith_wcgc_set_gift_card_as_sent
			 *
			 * Filter the condition to set the gift card as sent.
			 *
			 * @param bool true to set it as sent, false to not. Default: true
			 *
			 * @return bool
			 */
			if ( $result && ( apply_filters( 'yith_wcgc_set_gift_card_as_sent', true ) ) ) {
				$object->set_as_sent();
			}

			return $result;
		}

		/**
		 * get_content_html function.
		 *
		 * @since 0.1
		 * @return string
		 */
		public function get_content_html() {
			ob_start();
			wc_get_template(
				$this->template_html,
				array(
					'gift_card'         => $this->get_gift_card(),
					'introductory_text' => $this->get_introductory_text(),
					'email_heading'     => $this->get_heading(),
					'email_type'        => $this->email_type,
					'sent_to_admin'     => false,
					'plain_text'        => false,
					'email'             => $this,
					'case'              => $this->case,
				),
				'',
				YITH_YWGC_TEMPLATES_DIR
			);

			return ob_get_clean();
		}


		/**
		 * Initialize Settings Form Fields
		 *
		 * @since 0.1
		 */
		public function init_form_fields() {

			$this->form_fields = array(
				'enabled'           => array(
					'title'   => esc_html__( 'Enable/Disable', 'woocommerce' ),
					'type'                 => 'yith_ywgc_field',
					'yith_ywgc_field_type' => 'onoff',
					'label'   => esc_html__( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes',
				),
				'subject'           => array(
					'title'       => esc_html__( 'Subject', 'woocommerce' ),
					'type'                 => 'yith_ywgc_field',
					'yith_ywgc_field_type' => 'text',
					'description' => sprintf( esc_html__( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => '',
				),
				'heading'           => array(
					'title'       => esc_html__( 'Email Heading', 'woocommerce' ),
					'type'                 => 'yith_ywgc_field',
					'yith_ywgc_field_type' => 'text',
					'description' => sprintf( esc_html__( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->heading ),
					'placeholder' => '',
					'default'     => '',
				),
				'introductory_text' => array(
					'title'       => esc_html__( 'Introductive message', 'yith-woocommerce-gift-cards' ),
					'type'                 => 'yith_ywgc_field',
					'yith_ywgc_field_type' => 'textarea',
					'description' => sprintf( esc_html__( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->introductory_text ),
					'placeholder' => '',
					'default'     => '',
				),
			);
		}

		/**
		 * Get email main object
		 *
		 * @return object
		 */
		public function get_gift_card() {
			return has_filter( 'woocommerce_is_email_preview' ) ? $this->get_dummy_gift_card() : $this->object;
		}

		/**
		 * Get a dummy gift card.
		 *
		 * @return YITH_YWGC_Gift_Card
		 */
		public function get_dummy_gift_card() {
			$gift_card = new YITH_YWGC_Gift_Card();

			$gift_card->gift_card_number = 'J4KD-L8QZ-WX9M-R2NT';
			$gift_card->total_amount     = 25;
			$gift_card->message          = __( 'Happy birthday! Enjoy this gift on your most special day.', 'yith-woocommerce-gift-cards' );
			$gift_card->expiration       = strtotime( '+1 year' );

			return $gift_card;
		}

		/**
		 * Get email introductory text
		 *
		 * @return string
		 */
		public function get_introductory_text() {
			return has_filter( 'woocommerce_is_email_preview' ) ? $this->get_dummy_introductory_text() : $this->introductory_text;
		}

		/**
		 * Get dummy introductory text
		 *
		 * @return string
		 */
		public function get_dummy_introductory_text() {
			return __( 'Hi John, you have received this gift card from Mary, use it on our online shop.', 'yith-woocommerce-gift-cards' );
		}
	} // end \YITH_YWGC_Email_Send_Gift_Card class
}

return new YITH_YWGC_Email_Send_Gift_Card();
