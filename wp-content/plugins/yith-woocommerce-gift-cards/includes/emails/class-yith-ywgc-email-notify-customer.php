<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists ( 'YITH_YWGC_Email_Notify_Customer' ) ) {
	/**
	 * Create and send a digital gift card to the specific recipient
	 *
	 * @since 0.1
	 * @extends \YITH_YWGC_Mail
	 */
	class YITH_YWGC_Email_Notify_Customer extends YITH_YWGC_Mail {

		/**
		 * Set email defaults
		 *
		 * @since 0.1
		 */
		public function __construct() {
			// set ID, this simply needs to be a unique name.
			$this->id = 'ywgc-email-notify-customer';

			// this is the title in WooCommerce Email settings.
			$this->title = __( 'YITH Gift Cards - Used Gift Card', 'yith-woocommerce-gift-cards' );

			parent::__construct();
		}
	}
}

return new YITH_YWGC_Email_Notify_Customer();
