<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWRAC_Privacy_DPA' ) ) {
	/**
	 * Class YITH_YWRAC_Privacy_DPA
	 * Privacy Class
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_YWRAC_Privacy_DPA extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_YWRAC_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH WooCommerce Recover Abandoned Cart Premium', 'Privacy Policy Content', 'yith-woocommerce-recover-abandoned-cart' ) );
		}

		/**
		 * Return the message
		 *
		 * @param string $section Section.
		 *
		 * @return string
		 */
		public function get_privacy_message( $section ) {
			$message = '';

			switch ( $section ) {
				case 'collect_and_store':
					$message = sprintf( '<p>%s</p><ul><li>%s</li><li>%s</li></ul><p>%s</p><p class="privacy-policy-tutorial">%s</p>', esc_html__( 'While you visit our site, we\'ll track:', 'yith-woocommerce-recover-abandoned-cart' ), esc_html__( 'Products added to cart: these will be used to send you marketing messages and invite you to complete the order.', 'yith-woocommerce-recover-abandoned-cart' ), esc_html__( 'Name, Last name, Email and Phone number: data used with the purpose to contact you.', 'yith-woocommerce-recover-abandoned-cart' ), esc_html__( 'We\'ll also use cookies to keep track of your cart ID while you\'re browsing our site.', 'yith-woocommerce-recover-abandoned-cart' ), esc_html__( 'Note: you may want to provide further details about your cookie policy and add a link to that section from here.', 'yith-woocommerce-recover-abandoned-cart' ) );
					break;
				case 'has_access':
					$message = sprintf( '<p>%s</p><p>%s</p>', esc_html__( 'Members of our team have access to the information you provide. For example, both Administrators and Shop Managers.', 'yith-woocommerce-recover-abandoned-cart' ), esc_html__( 'Our team members have access to this information to help you fulfill orders and provide support.', 'yith-woocommerce-recover-abandoned-cart' ) );
					break;
			}

			return apply_filters( 'ywrac_privacy_policy_content', $message, $section );

		}
	}
}

new YITH_YWRAC_Privacy_DPA();
