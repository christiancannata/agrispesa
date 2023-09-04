<?php
/**
 * Class YITH_YWGC_Shortcodes
 *
 * @package YITH\GiftCards\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWGC_Shortcodes' ) ) {
	/**
	 * YITH_YWGC_Shortcodes class.
	 */
	class YITH_YWGC_Shortcodes {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_YWGC_Shortcodes
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
		 * @author YITH <plugins@yithemes.com>
		 */
		protected function __construct() {
			add_shortcode( 'yith_ywgc_display_gift_card_form', array( $this, 'yith_ywgc_display_gift_card_form' ) );

			add_shortcode( 'yith_wcgc_show_gift_card_list', array( $this, 'yith_wcgc_show_gift_card_list' ) );
		}

		/**
		 * Shortcode to include the necessary hook to display the gift card form
		 *
		 * @param array  $atts    Shortcode atts.
		 * @param string $content Content.
		 *
		 * @return false|string
		 */
		public function yith_ywgc_display_gift_card_form( $atts, $content ) {
			global $product;

			if ( is_object( $product ) && $product instanceof WC_Product_Gift_Card && 'gift-card' === $product->get_type() ) {
				$on_sale       = $product->get_add_discount_settings_status();
				$on_sale_value = get_post_meta( $product->get_id(), '_ywgc_sale_discount_value', true );
				$on_sale_text  = get_post_meta( $product->get_id(), '_ywgc_sale_discount_text', true );

				ob_start();

				wc_get_template(
					'single-product/add-to-cart/gift-card.php',
					array(
						'product'       => $product,
						'on_sale'       => $on_sale,
						'on_sale_value' => $on_sale_value,
						'on_sale_text'  => $on_sale_text,
					),
					'',
					trailingslashit( YITH_YWGC_TEMPLATES_DIR )
				);

				$content = ob_get_clean();
			}

			return $content;
		}

		/**
		 * Shortcode to print gift card list
		 *
		 * @param array $atts Shortcode atts.
		 *
		 * @return string
		 */
		public function yith_wcgc_show_gift_card_list( $atts ) {
			ob_start();

			wc_get_template(
				'myaccount/my-giftcards.php',
				array(),
				'',
				trailingslashit( YITH_YWGC_TEMPLATES_DIR )
			);

			return ob_get_clean();
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
