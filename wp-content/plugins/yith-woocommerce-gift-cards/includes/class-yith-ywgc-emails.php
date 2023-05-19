<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWGC_Emails' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Emails
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Emails {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var instance instance.
		 */
		public static $instance;

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
		public function __construct() {

			/**
			 * Add an email action for sending the digital gift card
			 */
			add_filter( 'woocommerce_email_actions', array( $this, 'add_gift_cards_trigger_action' ) );

			/**
			 * Locate the plugin email templates
			 */
			add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_core_template' ), 10, 3 );

			/**
			 * Add the email used to send digital gift card to woocommerce email tab
			 */
			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_email_classes' ) );

			/**
			 * Add entry on resend order email list
			 */
			add_filter( 'woocommerce_resend_order_emails_available', array( $this, 'resend_gift_card_code' ) );

			/**
			 * Add information to the email footer
			 */
			add_action( 'woocommerce_email_footer', array( $this, 'add_footer_information' ) );

			/**
			 * Add CSS style to gift card emails header
			 */
			add_action( 'woocommerce_email_header', array( $this, 'include_css_for_emails' ), 10, 2 );

			/**
			 * Show an introductory text before the gift cards editor
			 */
			add_action( 'ywgc_gift_cards_email_before_preview', array( $this, 'show_introductory_text' ), 10, 2 );

			/**
			 * Show the link for cart discount on the gift card email
			 */
			add_action( 'ywgc_gift_card_email_after_preview', array( $this, 'show_link_for_cart_discount' ), 10 );

			add_action( 'yith_ywgc_send_gift_card_email', array( $this, 'send_gift_card_email' ) );
		}

		/**
		 * send the gift card code email
		 *
		 * @param YITH_YWGC_Gift_Card|int $gift_card the gift card.
		 * @param bool                            $only_new  choose if only never sent gift card should be used.
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function send_gift_card_email( $gift_card, $only_new = true ) {

			if ( is_numeric( $gift_card ) ) {
				$gift_card = new YITH_YWGC_Gift_Card( array( 'ID' => $gift_card ) );
			}

			if ( ! $gift_card->exists() ) {
				return;
			}

			/**
			 * APPLY_FILTERS: yith_wcgc_deny_gift_card_email
			 *
			 * Filter the condition to deny to send the gift card email.
			 *
			 * @param bool true to deny it, false to allow it. Default: false
			 * @param object $gift_card the gift card object
			 *
			 * @return bool
			 */
			if ( ( ! $gift_card->is_virtual() || empty( $gift_card->recipient ) ) || apply_filters( 'yith_wcgc_deny_gift_card_email', false, $gift_card ) ) {
				// not a digital gift card or missing recipient.
				return;
			}

			if ( $only_new && $gift_card->has_been_sent() ) {
				// avoid sending emails more than one time.
				return;
			}

			/**
			 * APPLY_FILTERS: ywgc_recipient_email_before_sent_email
			 *
			 * Filter the recipient email before sending the gift card email.
			 *
			 * @param string the recipient email
			 * @param object $gift_card the gift card object
			 *
			 * @return string
			 */
			$gift_card->recipient = apply_filters( 'ywgc_recipient_email_before_sent_email', $gift_card->recipient, $gift_card );

			/**
			 * DO_ACTION: ywgc_before_sent_email_gift_card_notification
			 *
			 * Before send the gift card notification via email.
			 *
			 * @param object $gift_card the gift card object
			 */
			do_action( 'ywgc_before_sent_email_gift_card_notification', $gift_card );

			WC()->mailer();

			/**
			 * DO_ACTION: ywgc_email_send_gift_card_notification
			 *
			 * Trigger the gift card notification email.
			 *
			 * @param object $gift_card the gift card object
			 * @param string the recipient case
			 */
			do_action( 'ywgc_email_send_gift_card_notification', $gift_card, 'recipient' );

			/**
			 * DO_ACTION: yith_ywgc_gift_card_email_sent
			 *
			 * After send the gift card notification via email.
			 *
			 * @param object $gift_card the gift card object
			 */
			do_action( 'yith_ywgc_gift_card_email_sent', $gift_card );

			$old_file = get_post_meta( $gift_card->ID, 'ywgc_pdf_file', true );

			if ( file_exists( $old_file ) ) {
				unlink( $old_file );
			}
		}

		/**
		 * Show a link that let the customer to go to the website, adding the discount to the cart
		 *
		 * @param YITH_YWGC_Gift_Card $gift_card gift_card.
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_link_for_cart_discount( $gift_card ) {

			if ( 'no' !== get_option( 'ywgc_auto_discount_button_activation', 'yes' ) ) {

				$shop_page_url = apply_filters( 'yith_ywgc_shop_page_url', get_permalink( wc_get_page_id( 'shop' ) ) ? get_permalink( wc_get_page_id( 'shop' ) ) : site_url() );

				wc_get_template(
					'emails/automatic-discount.php',
					array(
						'apply_discount_url' => apply_filters( 'yith_ywgc_email_automatic_cart_discount_url', $shop_page_url, $gift_card ),
						'gift_card'          => $gift_card,
					),
					'',
					YITH_YWGC_TEMPLATES_DIR
				);

			}
		}

		/**
		 * Show the introductory message on the email being sent
		 *
		 * @param string              $text
		 * @param YITH_YWGC_Gift_Card $gift_card
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_introductory_text( $text, $gift_card ) {

			/**
			 * APPLY_FILTERS: ywgc_gift_cards_email_before_preview_text
			 *
			 * Filter the introductory text in the gift card email.
			 *
			 * @param string $text introductory text
			 * @param object $gift_card the gift card object
			 *
			 * @return string
			 */
			?>
			<p class="center-email"><?php echo apply_filters( 'ywgc_gift_cards_email_before_preview_text', $text, $gift_card ); ?></p>
			<?php
		}


		/**
		 * Include_css_for_emails
		 * Add CSS style to gift card emails header
		 *
		 * @param  mixed $email_heading email_heading.
		 * @param  mixed $email email.
		 * @return void
		 */
		public function include_css_for_emails( $email_heading, $email = null ) {
			if ( null === $email ) {
				return;
			}

			if ( ! isset( $email->object ) ) {
				return;
			}

			if ( ! $email->object instanceof YITH_YWGC_Gift_Card ) {
				return;
			}

			echo '<style type="text/css">';

			include YITH_YWGC_ASSETS_DIR . '/css/ywgc-frontend.css';

			if ( is_rtl() ) {
				wc_get_template(
					'emails/style-rtl.css',
					'',
					'',
					YITH_YWGC_TEMPLATES_DIR
				);
			} else {
				wc_get_template(
					'emails/style.css',
					'',
					'',
					YITH_YWGC_TEMPLATES_DIR
				);
			}

			echo '</style>';
		}

		/**
		 * Add gift card email to the available email on resend order email feature
		 *
		 * @param array $emails current emails.
		 *
		 * @return array
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function resend_gift_card_code( $emails ) {
			$emails[] = 'ywgc-email-send-gift-card';

			return $emails;
		}


		/**
		 * Append CSS for the email being sent to the customer
		 *
		 * @param WC_Email $email the email content.
		 */
		public function add_footer_information( $email = null ) {
			if ( is_null( $email ) ) {
				return;
			}

			if ( ! isset( $email->object ) ) {
				return;
			}

			if ( ! $email->object instanceof YITH_YWGC_Gift_Card ) {
				return;
			}

			/**
			 * APPLY_FILTERS: yith_ywgc_email_shop_name
			 *
			 * Filter the shop name displayed on the gift card email footer.
			 *
			 * @param string the shop name
			 *
			 * @return string
			 */
			$shop_name = apply_filters( 'yith_ywgc_email_shop_name', get_option( 'ywgc_shop_name', '' ) );
			/**
			 * APPLY_FILTERS: yith_ywgc_email_shop_link
			 *
			 * Filter the shop link displayed on the gift card email footer.
			 *
			 * @param string the shop link. Default: shop page
			 *
			 * @return string
			 */
			$shop_link = apply_filters( 'yith_ywgc_email_shop_link', get_permalink( wc_get_page_id( 'shop' ) ) );

			if ( ! $shop_name || ! $shop_link ) {
				return;
			}

			wc_get_template(
				'emails/gift-card-footer.php',
				array(
					'email'      => $email,
					'shop_name'  => $shop_name,
					'shop_link' => $shop_link,
				),
				'',
				YITH_YWGC_TEMPLATES_DIR
			);
		}

		/**
		 * Add an email action for sending the digital gift card
		 *
		 * @param array $actions list of current actions.
		 *
		 * @return array
		 */
		public function add_gift_cards_trigger_action( $actions ) {
			// Add trigger action for sending digital gift card.
			$actions[] = 'ywgc-email-send-gift-card';

			return $actions;
		}

		/**
		 * Locate the plugin email templates
		 *
		 * @param $core_file
		 * @param $template
		 * @param $template_base
		 *
		 * @return string
		 */
		public function locate_core_template( $core_file, $template, $template_base ) {
			$custom_template = array(
				'emails/send-gift-card.php',
			);

			if ( in_array( $template, $custom_template, true ) ) {
				$core_file = YITH_YWGC_TEMPLATES_DIR . $template;
			}

			return $core_file;
		}


		/**
		 * Add the email used to send digital gift card to woocommerce email tab
		 *
		 * @param string $email_classes current email classes.
		 *
		 * @return mixed
		 */
		public function add_woocommerce_email_classes( $email_classes ) {

			include 'emails/class.yith-ywgc-mail.php';

			if ( isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] ) {
				// add the email class to the list of email classes that WooCommerce loads.
				$email_classes['ywgc-email-send-gift-card']      = include 'emails/class-yith-ywgc-email-send-gift-card.php';
			} else{
				// add the email class to the list of email classes that WooCommerce loads.
				$email_classes['ywgc-email-send-gift-card']      = include 'emails/class-yith-ywgc-email-send-gift-card.php';
				$email_classes['ywgc-email-notify-customer']     = include 'emails/class-yith-ywgc-email-notify-customer.php';
				$email_classes['ywgc-email-delivered-gift-card'] = include 'emails/class-yith-ywgc-email-delivered-gift-card.php';
			}


			return $email_classes;
		}
	}
}

/**
 * Unique access to instance of YITH_YWGC_Emails class
 *
 * @return YITH_YWGC_Emails|YITH_YWGC_Emails_Premium|YITH_YWGC_Emails_Extended
 * @since 2.0.0
 */
function YITH_YWGC_Emails() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	if ( defined( 'YITH_YWGC_PREMIUM' ) ) {
		$instance = YITH_YWGC_Emails_Premium::get_instance();
	} elseif ( defined( 'YITH_YWGC_EXTENDED' ) ) {
		$instance = YITH_YWGC_Emails_Extended::get_instance();
	} else {
		$instance = YITH_YWGC_Emails::get_instance();
	}

	return $instance;
}
