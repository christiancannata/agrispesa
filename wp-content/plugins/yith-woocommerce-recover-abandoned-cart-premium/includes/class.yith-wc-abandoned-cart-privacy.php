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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements admin features of YITH WooCommerce Recover Abandoned Cart
 *
 * @class   YITH_WC_Recover_Abandoned_Cart_Privacy
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.2.3
 * @author YITH
 */
if ( ! class_exists( 'YITH_WC_Recover_Abandoned_Cart_Privacy' ) ) {
	/**
	 * Class YITH_WC_Recover_Abandoned_Cart_Privacy
	 */
	class YITH_WC_Recover_Abandoned_Cart_Privacy {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Recover_Abandoned_Cart_Privacy
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Recover_Abandoned_Cart_Privacy
		 * @since  1.2.3
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
		 * @since  1.0.0
		 */
		public function __construct() {
			add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporters' ), 5 );
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_erasers' ), 4 );
		}

		/**
		 * Register the exporter for YITH WooCommerce Recover Abandoned Cart.
		 *
		 * @param array $exporters Exporters.
		 *
		 * @return array
		 */
		public function register_exporters( $exporters = array() ) {
			$exporters['ywrac-customer-recover-abandoned-cart'] = array(
				'exporter_friendly_name' => __( 'Customer Abandoned Cart', 'yith-woocommerce-recover-abandoned-cart' ),
				'callback'               => array( 'YITH_WC_Recover_Abandoned_Cart_Privacy', 'data_exporter' ),
			);

			return $exporters;
		}

		/**
		 * Register the eraser for YITH WooCommerce Recover Abandoned Cart.
		 *
		 * @param array $erasers Erasers.
		 *
		 * @return array
		 */
		public function register_erasers( $erasers = array() ) {
			$erasers['ywrac-customer-recover-abandoned-cart'] = array(
				'eraser_friendly_name' => __( 'Customer Abandoned Cart', 'yith-woocommerce-recover-abandoned-cart' ),
				'callback'             => array( 'YITH_WC_Recover_Abandoned_Cart_Privacy', 'data_eraser' ),
			);

			return $erasers;
		}

		/**
		 * Data exporter callback to export abandoned cart.
		 *
		 * @param string $email_address Email Address.
		 * @param int    $page Page.
		 *
		 * @return array
		 */
		public static function data_exporter( $email_address, $page ) {
			$done           = false;
			$data_to_export = array();

			$query = array(
				'post_type'      => YITH_WC_Recover_Abandoned_Cart()->post_type_name,
				'posts_per_page' => 10,
				'paged'          => $page,
				'meta_key'       => '_user_email', //phpcs:ignore
				'meta_value'     => $email_address, //phpcs:ignore
			);

			$carts = get_posts( $query );

			if ( $carts ) {
				foreach ( $carts as $cart ) {
					$data_to_export[] = array(
						'group_id'    => 'ywrac_recover_abandoned_cart',
						'group_label' => __( 'Recover Abandoned Cart', 'yith-woocommerce-recover-abandoned-cart' ),
						'item_id'     => 'ywrac_cart -' . $cart->ID,
						'data'        => self::get_cart_personal_data( $cart ),
					);
				}
				$done = 10 > count( $carts );
			} else {
				$done = true;
			}

			return array(
				'data' => $data_to_export,
				'done' => $done,
			);
		}

		/**
		 * Data eraser callback to erase personal data registered in abandoned cart.
		 *
		 * @param string $email_address Email Address.
		 * @param int    $page Page.
		 *
		 * @return array
		 */
		public static function data_eraser( $email_address, $page ) {

			$response = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);

			$query = array(
				'post_type'      => YITH_WC_Recover_Abandoned_Cart()->post_type_name,
				'posts_per_page' => 10,
				'paged'          => $page,
				'meta_key'       => '_user_email', //phpcs:ignore
				'meta_value'     => $email_address, //phpcs:ignore
			);

			$carts = get_posts( $query );

			if ( $carts ) {
				foreach ( $carts as $cart ) {
					if ( apply_filters( 'ywrac_privacy_erase_personal_data', true, $cart ) ) {
						self::remove_personal_data( $cart );

						/* Translators: %s Order number. */
						$response['messages'][]    = sprintf( __( 'Removed abandoned cart %s.', 'yith-woocommerce-recover-abandoned-cart' ), $cart->ID );
						$response['items_removed'] = true;
					} else {
						/* Translators: %s Order number. */
						$response['messages'][]     = sprintf( __( 'Personal data within subscription %s has been retained.', 'yith-woocommerce-recover-abandoned-cart' ), $cart->ID );
						$response['items_retained'] = true;
					}
				}
				$response['done'] = 10 > count( $carts );
			} else {
				$response['done'] = true;
			}

			return $response;
		}

		/**
		 * Get personal data registered on cart.
		 *
		 * @param WP_Post $cart_post WP_Post.
		 *
		 * @return array
		 */
		protected static function get_cart_personal_data( $cart_post ) {
			$personal_data   = array();
			$props_to_export = apply_filters(
				'ywrac_privacy_export_personal_data_props',
				array(
					'id'               => __( 'Cart Id', 'yith-woocommerce-recover-abandoned-cart' ),
					'date_created'     => __( 'Cart Creation Date', 'yith-woocommerce-recover-abandoned-cart' ),
					'_cart_subtotal'   => __( 'Cart Total', 'yith-woocommerce-recover-abandoned-cart' ),
					'_user_first_name' => __( 'First Name', 'yith-woocommerce-recover-abandoned-cart' ),
					'_user_last_name'  => __( 'Last Name', 'yith-woocommerce-recover-abandoned-cart' ),
					'_user_email'      => __( 'Email Address', 'yith-woocommerce-recover-abandoned-cart' ),
					'_user_phone'      => __( 'Phone', 'yith-woocommerce-recover-abandoned-cart' ),
				),
				$cart_post
			);

			foreach ( $props_to_export as $prop => $name ) {
				switch ( $prop ) {
					case 'id':
						$value = $cart_post->ID;
						break;
					case 'date_created':
						$value = mysql2date( get_option( 'date_format' ), $cart_post->post_date );
						break;
					default:
						$value = get_post_meta( $cart_post->ID, $prop, true );

				}

				$value = apply_filters( 'ywrac_privacy_export_personal_data_prop', $value, $prop, $cart_post );

				if ( $value ) {
					$personal_data[] = array(
						'name'  => $name,
						'value' => $value,
					);
				}
			}

			return $personal_data;
		}


		/**
		 * Remove personal data
		 *
		 * @param WP_Post $cart_post WP_Post.
		 */
		protected static function remove_personal_data( $cart_post ) {
			wp_delete_post( $cart_post->ID );
		}
	}
}

/**
 * Unique access to instance of YITH_WC_Recover_Abandoned_Cart_Privacy class
 *
 * @return \YITH_WC_Recover_Abandoned_Cart_Privacy
 */
function YITH_WC_Recover_Abandoned_Cart_Privacy() { //phpcs:ignore
	return YITH_WC_Recover_Abandoned_Cart_Privacy::get_instance();
}
