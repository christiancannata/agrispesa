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
/**
 * Implements features of YITH WooCommerce Recover Abandoned Cart Counters
 *
 * @class   YITH_WC_Recover_Abandoned_Cart_Helper
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Recover Abandoned Cart
 */
if ( ! class_exists( 'YITH_WC_Recover_Abandoned_Cart_Helper' ) ) {
	/**
	 * Class YITH_WC_Recover_Abandoned_Cart_Helper
	 */
	class YITH_WC_Recover_Abandoned_Cart_Helper {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Recover_Abandoned_Cart_Helper
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Recover_Abandoned_Cart_Helper
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
		 * @since  1.0.0
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'set_cron' ), 20 );
			add_filter( 'cron_schedules', array( $this, 'cron_schedule' ), 50 ); //phpcs:ignore
			add_action( 'update_option_ywrac_cron_time', array( $this, 'destroy_schedule' ) );
			add_action( 'update_option_ywrac_cron_time_type', array( $this, 'destroy_schedule' ) );

		}

		/**
		 * Destroy the schedule
		 *
		 * Called when ywrac_cron_time and ywrac_cron_time_type are update from settings panel
		 *
		 * @since  1.0.0
		 */
		public function destroy_schedule() {
			wp_clear_scheduled_hook( 'ywrac_cron' );
			$this->set_cron();
		}

		/**
		 * Cron Schedule
		 *
		 * Add new schedules to WordPress.
		 *
		 * @param array $schedules Schedules.
		 *
		 * @since  1.0.0
		 */
		public function cron_schedule( $schedules ) {

			$interval = ywrac_get_cron_interval();

			$schedules['ywrac_gap'] = array(
				'interval' => $interval,
				'display'  => esc_html__( 'YITH WooCommerce Recover Abandoned Cart Cron', 'yith-woocommerce-recover-abandoned-cart' ),
			);

			return $schedules;
		}

		/**
		 * Set Cron
		 *
		 * Set ywrac_cron action each ywrac_gap schedule
		 *
		 * @since  1.0.0
		 */
		public function set_cron() {
			if ( ! wp_next_scheduled( 'ywrac_cron' ) ) {
				$recurrence = apply_filters( 'ywrac_recurrence', 'ywrac_gap' );
				wp_schedule_event( time(), $recurrence, 'ywrac_cron' );
			}
		}

		/**
		 * Update counter to statistic options:
		 *
		 * @param string $key Key.
		 * @param bool   $increase Increase or decrease.
		 * @since  1.0.0
		 */
		public function update_counter( $key, $increase = true ) {
			$suffix          = 'ywrac_';
			$current_counter = get_option( $suffix . $key, 0 );
			$counter         = $increase ? $current_counter + 1 : $current_counter - 1;
			$counter         = $counter < 0 ? 0 : $counter;
			update_option( $suffix . $key, $counter );
		}

		/**
		 * Update counter meta to statistic params
		 *
		 * @param int    $post_id Post id.
		 * @param string $key Key.
		 *
		 * @since  1.0.0
		 */
		public function update_counter_meta( $post_id, $key ) {
			$current_counter = get_post_meta( $post_id, $key, true );
			$current_counter = empty( $current_counter ) ? 0 : $current_counter;
			update_post_meta( $post_id, $key, $current_counter + 1 );
		}

		/**
		 * Update total amount when a cart is recovered
		 *
		 * @param float  $amount Amount.
		 * @param string $type String.
		 * @since  1.0.0
		 */
		public function update_amount_total( $amount, $type = '' ) {
			$key            = empty( $type ) ? 'ywrac_total_amount' : 'ywrac_total_' . $type . '_amount';
			$current_amount = get_option( $key );
			$current_amount = empty( $current_amount ) ? 0 : $current_amount;
			$total          = (float) $current_amount + (float) $amount;
			update_option( $key, $total );
		}

		/**
		 * Add to yith_ywrac_email_log a new entry
		 *
		 * @param string $user_email User email.
		 * @param int    $email_id email id.
		 * @param int    $cart_id Cart id.
		 * @param string $date Date.
		 *
		 * @since  1.0.0
		 */
		public function email_log( $user_email, $email_id, $cart_id, $date ) {
			global $wpdb;
			$table_name   = $wpdb->prefix . 'yith_ywrac_email_log';
			$insert_query = "INSERT INTO $table_name (email_id, email_template_id, ywrac_cart_id, date_send) VALUES ('" . $user_email . "', $email_id, $cart_id, '" . $date . "' )";
			$wpdb->query( $insert_query ); //phpcs:ignore
		}

		/**
		 * Clear coupons after use
		 *
		 * @since  1.0.0
		 */
		public function clear_coupons() {
			$delete_after_use = get_option( 'ywrac_coupon_delete_after_use' );
			$delete_expired   = get_option( 'ywrac_coupon_delete_expired' );

			if ( 'yes' !== $delete_after_use && 'yes' !== $delete_expired ) {
				return;
			}

			$args = array(
				'post_type'       => 'shop_coupon',
				'posts_per_pages' => -1,
				'meta_key'        => 'ywrac_coupon', //phpcs:ignore
				'meta_value'      => 'yes', //phpcs:ignore
			);

			$coupons = get_posts( $args );

			if ( ! empty( $coupons ) ) {
				foreach ( $coupons as $coupon ) {

					$coupon_code = wc_get_coupon_code_by_id( $coupon->ID );
					$wc_coupon   = new WC_Coupon( $coupon_code );

					if ( 'yes' === $delete_after_use ) {

						$usage_count = $wc_coupon->get_usage_count();
						if ( 1 === $usage_count ) {
							wp_trash_post( $coupon->ID );
						}
					}

					if ( 'yes' === $delete_expired ) {

						$date_expires = $wc_coupon->get_date_expires();

						if ( strtotime( $date_expires ) < strtotime( date( 'Y-m-d' ) ) ) { //phpcs:ignore
							wp_trash_post( $coupon->ID );
						}
					}
				}
			}
		}

	}
}

/**
 * Unique access to instance of YITH_WC_Recover_Abandoned_Cart_Helper class
 *
 * @return \YITH_WC_Recover_Abandoned_Cart_Helper
 */
function YITH_WC_Recover_Abandoned_Cart_Helper() { //phpcs:ignore
	return YITH_WC_Recover_Abandoned_Cart_Helper::get_instance();
}




