<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWGC_Shortcodes' ) ) {
	/**
	 * @class   YITH_YWGC_Shortcodes
	 */
	class YITH_YWGC_Shortcodes {

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

			add_shortcode( 'yith_ywgc_display_gift_card_form', array( $this, 'yith_ywgc_display_gift_card_form' ) );

		}

		/**
		 * Shortcode to include the necessary hook to display the gift card form
		 *
		 * @param $atts
		 * @param $content
		 *
		 * @return false|string
		 */
		function yith_ywgc_display_gift_card_form( $atts, $content ) {

			global $product;

			if ( is_object( $product ) && $product instanceof WC_Product_Gift_Card && 'gift-card' === $product->get_type() ) {

				ob_start();

				wc_get_template(
					'single-product/add-to-cart/gift-card.php',
					'',
					'',
					trailingslashit( YITH_YWGC_TEMPLATES_DIR )
				);

				$content = ob_get_clean();

			}
			return $content;
		}
	}
}

/**
 * Unique access to instance of YITH_YWGC_Shortcodes class
 *
 * @return YITH_YWGC_Shortcodes|YITH_YWGC_Shortcodes_Premium|YITH_YWGC_Shortcodes_Extended
 * @since 2.0.0
 */
function YITH_YWGC_Shortcodes() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	if ( defined( 'YITH_YWGC_PREMIUM' ) ) {
		$instance = YITH_YWGC_Shortcodes_Premium::get_instance();
	} elseif ( defined( 'YITH_YWGC_EXTENDED' ) ) {
		$instance = YITH_YWGC_Shortcodes_Extended::get_instance();
	} else {
		$instance = YITH_YWGC_Shortcodes::get_instance();
	}

	return $instance;
}
