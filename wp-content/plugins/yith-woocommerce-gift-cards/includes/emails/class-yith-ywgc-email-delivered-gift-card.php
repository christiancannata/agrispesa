<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_YWGC_Email_Delivered_Gift_Card' ) ) {
	/**
	 * Create and send a digital gift card to the specific recipient
	 *
	 * @since 0.1
	 * @extends \YITH_YWGC_Mail
	 */
	class YITH_YWGC_Email_Delivered_Gift_Card extends YITH_YWGC_Mail {
		/**
		 * An introductional message from the shop owner
		 */
		public $introductory_text;

		/**
		 * Set email defaults
		 *
		 * @since 0.1
		 */
		public function __construct() {
			// set ID, this simply needs to be a unique name.
			$this->id = 'ywgc-email-delivered-gift-card';

			// this is the title in WooCommerce Email settings.
			$this->title = __( 'YITH Gift Cards - Delivered Gift Card Notification', 'yith-woocommerce-gift-cards' );

			parent::__construct();
		}
	}
}

return new YITH_YWGC_Email_Delivered_Gift_Card();
