<?php
/**
 * Class KWED_Cartflows_CA_Email file.
 *
 * @package WooCommerce\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'WC_Email' ) && ! class_exists( 'KWED_Cartflows_CA_Email', false ) ) :

	/**
	 * Customer Completed Order Email.
	 *
	 * Order complete emails are sent to the customer when the order is marked complete and usual indicates that the order has been shipped.
	 *
	 * @class       WC_Email_Customer_Completed_Order
	 * @version     2.0.0
	 * @package     WooCommerce/Classes/Emails
	 * @extends     WC_Email
	 */
	class KWED_Cartflows_CA_Email extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'cartflows_ca_email_templates';
			$this->customer_email = true;
			$this->template_html  = 'emails/cartflows_ca_email.php';

			// Triggers for this email.
			add_filter( 'woo_cart_abandonment_recovery_email_override', array( $this, 'email_trigger' ), 10, 6 );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int            $order_id The order ID.
		 * @param WC_Order|false $order Order object.
		 */
		public function email_trigger( $response, $recipient, $subject, $content, $headers, $email_data ) {
			$this->setup_locale();
			$email_content = $this->get_html_content( $content, $subject );

			$this->send( $recipient, $subject, $email_content, $headers, $this->get_attachments() );

			$this->restore_locale();

			return true;
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_html_content( $message, $subject ) {
			return wc_get_template_html(
				$this->template_html,
				array(
					'email_heading'      => $subject,
					'message'            => $message,
					'additional_content' => apply_filters( 'kwed_cartflows_ca_additional_content', '' ),
					'sent_to_admin'      => false,
					'plain_text'         => false,
					'email'              => $this,
				)
			);
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Thanks for shopping with us', 'woocommerce' );
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => false,
					'email'              => $this,
				)
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => true,
					'email'              => $this,
				)
			);
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @since 3.7.0
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'Thanks for shopping with us.', 'woocommerce' );
		}
	}
	return new KWED_Cartflows_CA_Email();

endif;

