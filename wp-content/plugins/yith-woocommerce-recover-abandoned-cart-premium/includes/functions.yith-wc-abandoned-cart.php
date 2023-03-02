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
 * Implements helper functions for YITH WooCommerce Recover Abandoned Cart
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */

global $yith_ywrac_db_version;

$yith_ywrac_db_version = '1.0.0';

if ( ! function_exists( 'yith_ywrac_db_install' ) ) {
	/**
	 * Install the table yith_ywrac_email_log
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function yith_ywrac_db_install() {
		global $wpdb;
		global $yith_ywrac_db_version;

		$installed_ver = get_option( 'yith_ywrac_db_version' );

		if ( $installed_ver !== $yith_ywrac_db_version ) {

			$table_name = $wpdb->prefix . 'yith_ywrac_email_log';

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`email_id` varchar(255) NOT NULL,
		`email_template_id` int(11) NOT NULL,
		`ywrac_cart_id` int(11) NOT NULL,
		`date_send` datetime NOT NULL,
		PRIMARY KEY (id)
		) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			add_option( 'yith_ywrac_db_version', $yith_ywrac_db_version );
		}
	}
}

if ( ! function_exists( 'yith_check_privacy_enabled' ) ) {

	/**
	 * Check if the tool for export and erase personal data are enabled
	 *
	 * @param bool $wc .
	 * @return bool
	 * @since 1.0.0
	 */
	function yith_check_privacy_enabled( $wc = false ) {
		global $wp_version;
		$enabled = $wc ? version_compare( WC()->version, '3.4.0', '>=' ) && version_compare( $wp_version, '4.9.5', '>' ) : version_compare( $wp_version, '4.9.5', '>' );
		return apply_filters( 'yith_check_privacy_enabled', $enabled, $wc );
	}
}


if ( ! function_exists( 'yith_ywrac_update_db_check' ) ) {
	/**
	 * Check if the function yith_ywrac_db_install must be installed or updated
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function yith_ywrac_update_db_check() {
		global $yith_ywrac_db_version;

		if ( get_site_option( 'yith_ywrac_db_version' ) !== $yith_ywrac_db_version ) {
			yith_ywrac_db_install();
		}
	}
}

if ( ! function_exists( 'yith_ywrac_check_sample_email_template_posts' ) ) {
	/**
	 * Check if sample email templates should be created or not
	 *
	 * @return void
	 * @since 1.4.6
	 */
	function yith_ywrac_check_sample_email_template_posts() {
		if ( 'no' === get_option( 'ywrac_sample_email_template_posts', 'no' ) ) {
			yith_ywrac_create_sample_email_template_posts();
		}
	}
}

if ( ! function_exists( 'yith_ywrac_create_sample_email_template_posts' ) ) {
	/**
	 * Create sample email templates
	 *
	 * @return void
	 * @since 1.4.6
	 */
	function yith_ywrac_create_sample_email_template_posts() {

		// Abandoned Cart email template.

		$cart_template_content  = '<h3 style="text-align: center;">' . __( 'We noticed you left something in your cart.', 'yith-woocommerce-recover-abandoned-cart' ) . '</h3>';
		$cart_template_content .= '<p style="text-align: center;">' . __( "Looks like you got interrupted. Don't worry, we saved these hot items for you. We've even got a special offer for you!", 'yith-woocommerce-recover-abandoned-cart' ) . '</p>';
		$cart_template_content .= '<p style="text-align: center;">' . __( 'Get <strong>10% off</strong> your next purchase by using this special discount code:', 'yith-woocommerce-recover-abandoned-cart' ) . '</p>';
		$cart_template_content .= '<h4 style="text-align: center;margin: 30px 0;">{{ywrac.coupon}}</h4>';
		$cart_template_content .= '<p style="text-align: center;">' . esc_html__( "The offer will be valid only for the next 24 hours so don't miss it.", 'yith-woocommerce-recover-abandoned-cart' ) . '</p>';
		$cart_template_content .= '<p style="text-align: center;"><strong>' . esc_html__( 'You added these items in your cart:', 'yith-woocommerce-recover-abandoned-cart' ) . '</strong></p>';
		$cart_template_content .= '<p style="text-align: center;">{{ywrac.cart}}</p>&nbsp;';
		$cart_template_content .= '<p style="text-align: center;margin-top: 30px;">{{ywrac.recoverbutton}}</p>';

		$cart_template = array(
			'post_title'   => esc_html__( 'There is something in your cart!', 'yith-woocommerce-recover-abandoned-cart' ),
			'post_content' => $cart_template_content,
			'post_status'  => 'publish',
			'post_type'    => 'ywrac_email',
			'post_author'  => get_current_user_id(),
		);

		$cart_template_metas = array(
			'_ywrac_email_active'    => 'no',
			'_ywrac_email_type'      => 'cart',
			'_ywrac_email_subject'   => esc_html__( 'Complete your purchase with 10% off', 'yith-woocommerce-recover-abandoned-cart' ),
			'_ywrac_email_auto'      => 'yes',
			'_ywrac_email_time'      => array(
				'time' => 1,
				'type' => 'hours',
			),
			'_ywrac_coupon_enabled'  => 'yes',
			'_ywrac_coupon_value'    => array(
				'amount' => '10',
				'type'   => 'percent',
			),
			'_ywrac_coupon_validity' => 1,
			'_ywrac_email_to_send'   => get_bloginfo( 'admin_email' ),
		);

		$cart_template_post_id = wp_insert_post( $cart_template, true );

		if ( $cart_template_post_id ) {
			foreach ( $cart_template_metas as $key => $meta ) {
				update_post_meta( $cart_template_post_id, $key, $meta );
			}
		}

		// Pending order email template.

		$pending_order_template_content  = '<h3 style="text-align: center;">' . esc_html__( 'We noticed you did not complete your order.', 'yith-woocommerce-recover-abandoned-cart' ) . '</h3>';
		$pending_order_template_content .= '<p style="text-align: center;">' . esc_html__( 'We would like to know if you had any difficulties completing the order or if we can help you with something.', 'yith-woocommerce-recover-abandoned-cart' ) . '</p>';
		$pending_order_template_content .= '<p style="text-align: center;">' . __( 'If you complete your order today, you can get <strong>10% off</strong> off with this special discount code:', 'yith-woocommerce-recover-abandoned-cart' ) . '</p>';
		$pending_order_template_content .= '<h4 style="text-align: center;margin: 30px 0;">{{ywrac.coupon}}</h4>';
		$pending_order_template_content .= '<p style="text-align: center;">' . esc_html__( "The offer will be valid only for the next 24 hours so don't miss it.", 'yith-woocommerce-recover-abandoned-cart' ) . '</p>';
		$pending_order_template_content .= '<p style="text-align: center;"><strong>' . esc_html__( 'This is your order:', 'yith-woocommerce-recover-abandoned-cart' ) . '</strong></p>';
		$pending_order_template_content .= '<p style="text-align: center;">{{ywrac.cart}}</p>&nbsp;';
		$pending_order_template_content .= '<p style="text-align: center;margin-top: 30px;">{{ywrac.recoverbutton}}</p>';

		$pending_order_template = array(
			'post_title'   => esc_html__( 'Complete your order with 10% off', 'yith-woocommerce-recover-abandoned-cart' ),
			'post_content' => $pending_order_template_content,
			'post_status'  => 'publish',
			'post_type'    => 'ywrac_email',
			'post_author'  => get_current_user_id(),
		);

		$pending_order_template_metas = array(
			'_ywrac_email_active'    => 'no',
			'_ywrac_email_type'      => 'order',
			'_ywrac_email_subject'   => esc_html__( 'Complete your order with 10% off', 'yith-woocommerce-recover-abandoned-cart' ),
			'_ywrac_email_auto'      => 'yes',
			'_ywrac_email_time'      => array(
				'time' => 1,
				'type' => 'hours',
			),
			'_ywrac_coupon_enabled'  => 'yes',
			'_ywrac_coupon_value'    => array(
				'amount' => '10',
				'type'   => 'percent',
			),
			'_ywrac_coupon_validity' => 1,
			'_ywrac_email_to_send'   => get_bloginfo( 'admin_email' ),
		);

		$pending_order_template_post_id = wp_insert_post( $pending_order_template, true );

		if ( $pending_order_template_post_id ) {
			foreach ( $pending_order_template_metas as $key => $meta ) {
				update_post_meta( $pending_order_template_post_id, $key, $meta );
			}
		}

		// Checks if both posts were created correctly and set the option.
		if ( $cart_template_post_id && $pending_order_template_post_id ) {
			update_option( 'ywrac_sample_email_template_posts', 'yes' );
		}

	}
}

if ( ! function_exists( 'yith_ywrac_locate_template' ) ) {
	/**
	 * Locate the templates and return the path of the file found
	 *
	 * @param string $path Path.
	 * @param array  $var Var.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	function yith_ywrac_locate_template( $path, $var = null ) {
		global $woocommerce;

		if ( function_exists( 'WC' ) ) {
			$woocommerce_base = WC()->template_path();
		} elseif ( defined( 'WC_TEMPLATE_PATH' ) ) {
			$woocommerce_base = WC_TEMPLATE_PATH;
		} else {
			$woocommerce_base = $woocommerce->plugin_path() . '/templates/';
		}

		$template_woocommerce_path = $woocommerce_base . $path;
		$template_path             = '/' . $path;
		$plugin_path               = YITH_YWRAC_DIR . 'templates/' . $path;

		$located = locate_template(
			array(
				$template_woocommerce_path,
				$template_path,
				$plugin_path,
			)
		);

		if ( ! $located && file_exists( $plugin_path ) ) {
			return apply_filters( 'yith_ywrac_locate_template', $plugin_path, $path );
		}

		return apply_filters( 'yith_ywrac_locate_template', $located, $path );
	}
}


if ( ! function_exists( 'yith_ywrac_get_excerpt' ) ) {
	/**
	 * Return the excerpt of template email
	 *
	 * @param int $post_id Post id.
	 * @return string
	 */
	function yith_ywrac_get_excerpt( $post_id ) {
		$post         = get_post( $post_id );
		$excerpt      = ! empty( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;
		$num_of_words = apply_filters( 'yith_ywrac_get_excerpt_num_words', 10 );
		return wp_trim_words( $excerpt, $num_of_words );
	}
}


if ( ! function_exists( 'yith_ywrac_get_roles' ) ) {
	/**
	 * Return the roles of users
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function yith_ywrac_get_roles() {
		global $wp_roles;
		$roles = array();

		foreach ( $wp_roles->get_names() as $key => $role ) {
			$roles[ $key ] = translate_user_role( $role );
		}
		return apply_filters( 'ywrac_get_roles', $roles );
	}
}


if ( ! function_exists( 'ywrac_get_cutoff' ) ) {
	/**
	 * Calculate the cutoff time
	 *
	 * @param int    $qty Quantity.
	 * @param string $type Type.
	 *
	 * @return float|int
	 */
	function ywrac_get_cutoff( $qty, $type ) {
		$cutoff = 0;
		if ( 'hours' === $type ) {
			$cutoff = 60 * 60 * $qty;
		} elseif ( 'days' === $type ) {
			$cutoff = 24 * 60 * 60 * $qty;
		} elseif ( 'minutes' === $type ) {
			$cutoff = 60 * $qty;
		}

		return $cutoff;
	}
}

if ( ! function_exists( 'ywrac_is_customer_unsubscribed' ) ) {
	/**
	 * Check if a customer is currently unsubscribed from email
	 *
	 * @param int | string $user User.
	 * @return bool
	 * @since 1.0.4
	 * @author Francesco Licandro
	 */
	function ywrac_is_customer_unsubscribed( $user = null ) {

		$blacklist = get_option( 'ywrac_mail_blacklist', '' );
		$blacklist = maybe_unserialize( $blacklist );

		if ( ! $blacklist ) {
			$blacklist = array();
		}

		if ( is_null( $user ) ) {
			$customer_id = get_current_user_id();
		} elseif ( is_email( $user ) ) {
			$customer = get_user_by( 'email', $user );
			if ( $customer ) {
				$customer_id = $customer->ID;
			} else {
				return in_array( $user, $blacklist, true );
			}
		} else {
			$customer_id = intval( $user );
		}

		if ( apply_filters( 'ywrac_allow_current_user', false ) ) {
			return false;
		}

		return get_user_meta( $customer_id, '_ywrac_is_unsubscribed', true ) === '1';
	}
}

if ( ! function_exists( 'ywrac_check_valid_admin_page' ) ) {
	/**
	 * Return if the current pagenow is valid for a post_type, useful if you want add metabox, scripts inside the editor of a particular post type
	 *
	 * @param string $post_type_name Post type name.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	function ywrac_check_valid_admin_page( $post_type_name ) : bool {
		global $pagenow;
		$request = $_REQUEST; //phpcs:ignore
		$post    = isset( $request['post'] ) ? sanitize_text_field( wp_unslash( $request['post'] ) ) : ( isset( $request['post_ID'] ) ? sanitize_text_field( wp_unslash( $request['post_ID'] ) ) : 0 );
		$post    = get_post( $post );

		if ( ( $post && $post->post_type === $post_type_name ) || ( 'post-new.php' === $pagenow && isset( $request['post_type'] ) && $request['post_type'] === $post_type_name ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'ywrac_get_product_price' ) ) {
	/**
	 * Get product price.
	 *
	 * @param WC_Product $_product Product.
	 * @param float      $price Price.
	 * @param string     $currency Currency.
	 * @return mixed|void
	 */
	function ywrac_get_product_price( $_product, $price = '', $currency = '' ) {

		$tax_display_cart = get_option( 'woocommerce_tax_display_cart' );

		if ( 'excl' === $tax_display_cart ) {
			$product_price = yit_get_price_excluding_tax( $_product, 1, $price );
		} else {
			$product_price = yit_get_price_including_tax( $_product, 1, $price );
		}

		return apply_filters( 'woocommerce_cart_product_price', wc_price( $product_price, array( 'currency' => $currency ) ), $_product );

	}
}

if ( ! function_exists( 'ywrac_get_product_subtotal' ) ) {
	/**
	 * Get product subtotal
	 *
	 * @param WC_Product $_product Product.
	 * @param int        $quantity Quantity.
	 * @param float      $price Price.
	 * @param string     $currency Currency.
	 * @return string
	 */
	function ywrac_get_product_subtotal( $_product, $quantity, $price = '', $currency = '' ) {

		$price             = empty( $price ) ? $_product->get_price() : $price;
		$taxable           = $_product->is_taxable();
		$tax_display_cart  = get_option( 'woocommerce_tax_display_cart' );
		$price_include_tax = wc_prices_include_tax();

		if ( $taxable ) {
			if ( 'excl' === $tax_display_cart ) {

				$row_price        = yit_get_price_excluding_tax( $_product, $quantity, $price );
				$product_subtotal = wc_price( $row_price, array( 'currency' => $currency ) );

				if ( $price_include_tax ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
			} else {

				$row_price        = yit_get_price_including_tax( $_product, $quantity, $price );
				$product_subtotal = wc_price( $row_price, array( 'currency' => $currency ) );

				if ( ! $price_include_tax ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
			}
		} else {
			$row_price        = $price * $quantity;
			$product_subtotal = wc_price( $row_price, array( 'currency' => $currency ) );
		}

		return $product_subtotal;
	}
}

if ( ! function_exists( 'ywrac_get_cron_interval' ) ) {
	/**
	 * Get cron interval
	 *
	 * @return mixed|void
	 */
	function ywrac_get_cron_interval() {
		$interval    = 0;
		$cron_config = get_option( 'ywrac_cron_config' );
		$cron_time   = empty( $cron_config['cron_time'] ) ? get_option( 'ywrac_cron_time', 10 ) : $cron_config['cron_time'];
		$cron_type   = empty( $cron_config['cron_type'] ) ? get_option( 'ywrac_cron_time_type', 'minutes' ) : $cron_config['cron_type'];

		if ( 'hours' === $cron_type ) {
			$interval = 60 * 60 * $cron_time;
		} elseif ( 'days' === $cron_type ) {
			$interval = 24 * 60 * 60 * $cron_time;
		} elseif ( 'minutes' === $cron_type ) {
			$interval = 60 * $cron_time;
		}

		return apply_filters( 'ywrac_cron_interval', $interval );
	}
}


if ( ! function_exists( 'ywrac_get_timestamp' ) ) {
	/**
	 * Return the timestamp
	 *
	 * @return int|string
	 */
	function ywrac_get_timestamp() {
		$gtm = apply_filters( 'ywrac_get_timestamp_with_gtm', 0 );

		return current_time( 'timestamp', $gtm ); //phpcs:ignore
	}
}

if ( ! function_exists( 'ywrac_get_customer_last_order' ) ) {
	/**
	 * Return the last order of a user
	 *
	 * @param int $user_id User id.
	 * @return bool|WC_Order
	 */
	function ywrac_get_customer_last_order( $user_id ) {
		return wc_get_customer_last_order( $user_id );
	}
}
