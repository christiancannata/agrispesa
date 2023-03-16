<?php
/**
 * URL Coupons for WooCommerce - Core Class.
 *
 * @version 1.6.7
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_URL_Coupons_Core' ) ) :

	class Alg_WC_URL_Coupons_Core {

		/**
		 * Constructor.
		 *
		 * @version 1.6.4
		 * @since   1.0.0
		 *
		 * @todo    [next] (feature) multiple keys, e.g. `apply_coupon,coupon`
		 * @todo    [maybe] (dev) hide coupons: maybe it's safer to hide coupons with CSS instead of using filter
		 */
		function __construct() {

			if ( 'yes' === get_option( 'alg_wc_url_coupons_enabled', 'yes' ) ) {
				// Apply URL coupon.
				foreach ( array_keys( $this->get_possible_main_hooks() ) as $main_hook ) {
					add_action( $main_hook, array( $this, 'apply_url_coupon_on_main_hook_triggered' ), ( '' !== ( $priority = get_option( 'alg_wc_url_coupons_priority', '' ) ) ? $priority : PHP_INT_MAX ) );
				}
				// Force session.
				add_action( 'alg_wc_url_coupons_before_coupon_applied', array( $this, 'maybe_force_start_session' ), 10 );
				add_action( 'init', array( $this, 'maybe_force_start_session_everywhere' ), 10 );
				// Set additional cookie.
				add_action( 'alg_wc_url_coupons_before_coupon_applied', array( $this, 'maybe_set_additional_cookie' ), 11 );
				// Delay coupon.
				if ( 'yes' === get_option( 'alg_wc_url_coupons_delay_coupon', 'no' ) ) {
					add_action( 'woocommerce_add_to_cart', array( $this, 'apply_delayed_coupon' ), PHP_INT_MAX, 6 );
				}
				// Delay notice.
				if ( 'yes' === get_option( 'alg_wc_url_coupons_delay_notice', 'no' ) ) {
					add_action( 'alg_wc_url_coupons_coupon_applied', array( $this, 'delay_notice' ), 10, 3 );
					add_action( 'wp_head', array( $this, 'display_delayed_notice' ) );
				}
				add_action( 'alg_wc_url_coupons_after_coupon_applied', array( $this, 'redirect' ), PHP_INT_MAX, 3 );
				// Hide coupons.
				if ( 'yes' === get_option( 'alg_wc_url_coupons_cart_hide_coupon', 'no' ) ) {
					add_filter( 'woocommerce_coupons_enabled', array( $this, 'hide_coupon_field_on_cart' ), PHP_INT_MAX );
				}
				if ( 'yes' === get_option( 'alg_wc_url_coupons_checkout_hide_coupon', 'no' ) ) {
					add_filter( 'woocommerce_coupons_enabled', array( $this, 'hide_coupon_field_on_checkout' ), PHP_INT_MAX );
				}
				add_action( 'alg_wc_url_coupons_coupon_applied', array( $this, 'save_applied_coupon' ), 10, 3 );
				add_action( 'woocommerce_removed_coupon', array( $this, 'remove_coupon' ), 10, 3 );

				// Force coupon redirect.
				if ( 'yes' === get_option( 'alg_wc_url_coupons_add_to_cart_action_force_coupon_redirect', 'no' ) ) {
					add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'add_to_cart_action_force_coupon_redirect' ), PHP_INT_MAX, 2 );
				}
				// WP Rocket: Disable empty cart caching.
				if ( 'yes' === get_option( 'alg_wc_url_coupons_wp_rocket_disable_cache_wc_empty_cart', 'no' ) ) {
					add_filter( 'rocket_cache_wc_empty_cart', '__return_false', PHP_INT_MAX );
				}
				// Save coupons on empty cart.
				if ( 'yes' === get_option( 'alg_wc_url_coupons_save_empty_cart', 'no' ) ) {
					add_action( 'woocommerce_before_cart_emptied', array( $this, 'save_empty_cart_coupons' ) );
					add_action( 'woocommerce_add_to_cart', array( $this, 'apply_empty_cart_coupons' ), PHP_INT_MAX, 6 );
				}
				// Payment request product data: WooCommerce Stripe Gateway, WooCommerce Payments.
				$payment_request_product_data_options = get_option( 'alg_wc_url_coupons_payment_request_product_data', array() );
				foreach ( array( 'wc_stripe', 'wcpay' ) as $gateway ) {
					if ( isset( $payment_request_product_data_options[ $gateway ] ) && 'yes' === $payment_request_product_data_options[ $gateway ] ) {
						add_filter( $gateway . '_payment_request_product_data', array( $this, 'payment_request_product_data' ), PHP_INT_MAX, 2 );
					}
				}
				// Shortcodes.
				add_shortcode( 'alg_wc_url_coupons_translate', array( $this, 'translate_shortcode' ) );
				// Data storage.
				add_filter( 'alg_wc_url_coupons_data_storage_type', array( $this, 'set_data_storage_type' ) );
				// Javascript reload.
				add_action( 'wp_footer', array( $this, 'reload_page_via_js' ) );
				add_filter( 'alg_wc_url_coupons_apply_url_coupon_validation', array( $this, 'do_not_apply_url_coupon_until_js_reload' ) );
				add_filter( 'alg_wc_url_coupons_keys_to_remove_on_redirect', array( $this, 'remove_reloaded_param_via_js_on_redirect' ) );
			}
		}

		/**
		 * set_data_storage_type.
		 *
		 * @version 1.6.4
		 * @since   1.6.4
		 *
		 * @param $type
		 *
		 * @return string
		 */
		function set_data_storage_type( $type ) {
			$type = get_option( 'alg_wc_url_coupons_data_storage_type', 'session' );
			return $type;
		}

		/**
		 * payment_request_product_data.
		 *
		 * @version 1.6.2
		 * @since   1.6.2
		 *
		 * @see     https://github.com/woocommerce/woocommerce-gateway-stripe/blob/5.8.1/includes/payment-methods/class-wc-stripe-payment-request.php#L451
		 * @see     https://github.com/Automattic/woocommerce-payments/blob/3.4.0/includes/class-wc-payments-payment-request-button-handler.php#L289
		 */
		function payment_request_product_data( $data, $product ) {
			if ( ! empty( $data['total']['amount'] ) && $data['total']['amount'] > 0 ) {
				$applied_coupons = WC()->cart->get_applied_coupons();
				if ( ! empty( $applied_coupons ) ) {
					$total_discounts = 0;
					foreach ( $applied_coupons as $coupon_code ) {
						$coupon = new WC_Coupon( $coupon_code );
						if ( $coupon && $coupon->is_valid_for_product( $product ) ) {
							$total_discounts += $coupon->get_discount_amount( $product->get_price() * 100 );
						}
					}
					if ( 0 != $total_discounts ) {
						$data['total']['amount'] -= $total_discounts;
						$data['displayItems'][]   = array(
							'label'  => esc_html(
								( 'wc_stripe_payment_request_product_data' === current_filter() ?
									__( 'Discount', 'woocommerce-gateway-stripe' ) :
									__( 'Discount', 'woocommerce-payments' ) )
							),
							'amount' => $total_discounts,
						);
					}
				}
			}
			return $data;
		}

		/**
		 * save_empty_cart_coupons.
		 *
		 * @version 1.6.4
		 * @since   1.6.1
		 *
		 * @todo    [next] (dev) merge this with `WC()->session->set( 'alg_wc_url_coupons', ... )`?
		 */
		function save_empty_cart_coupons( $clear_persistent_cart ) {
			$coupons = alg_wc_url_coupons_data_storage_get( 'alg_wc_url_coupons_empty_cart', array() );
			alg_wc_url_coupons_data_storage_set( 'alg_wc_url_coupons_empty_cart', array_unique( array_merge( $coupons, WC()->cart->applied_coupons ) ) );
		}

		/**
		 * apply_empty_cart_coupons.
		 *
		 * @version 1.6.4
		 * @since   1.6.1
		 *
		 * @todo    [next] (dev) use `$this->apply_coupon()`?
		 * @todo    [next] (feature) apply only applicable coupons, e.g. `fixed_product`, etc.
		 */
		function apply_empty_cart_coupons( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
			$coupons = alg_wc_url_coupons_data_storage_get( 'alg_wc_url_coupons_empty_cart', array() );
			if ( ! empty( $coupons ) ) {
				alg_wc_url_coupons_data_storage_set( 'alg_wc_url_coupons_empty_cart', null );
				foreach ( $coupons as $coupon ) {
					WC()->cart->add_discount( $coupon );
				}
			}
		}

		/**
		 * translate_shortcode.
		 *
		 * @version 1.5.4
		 * @since   1.5.4
		 */
		function translate_shortcode( $atts, $content = '' ) {
			// E.g.: `[alg_wc_url_coupons_translate lang="EN,DE" lang_text="Text for EN & DE" not_lang_text="Text for other languages"]`
			if ( isset( $atts['lang_text'] ) && isset( $atts['not_lang_text'] ) && ! empty( $atts['lang'] ) ) {
				return ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ?
					$atts['not_lang_text'] : $atts['lang_text'];
			}
			// E.g.: `[alg_wc_url_coupons_translate lang="EN,DE"]Text for EN & DE[/alg_wc_url_coupons_translate][alg_wc_url_coupons_translate not_lang="EN,DE"]Text for other languages[/alg_wc_url_coupons_translate]`
			return (
				( ! empty( $atts['lang'] ) && ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ) ||
				( ! empty( $atts['not_lang'] ) && defined( 'ICL_LANGUAGE_CODE' ) && in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ) ) )
			) ? '' : $content;
		}

		/**
		 * apply_delayed_coupon.
		 *
		 * @version 1.6.6
		 * @since   1.5.0
		 *
		 * @todo    [now] (dev) `$skip_coupons`: `fixed_cart` type?
		 * @todo    [next] (dev) `$skip_coupons`: `percent` type?
		 * @todo    [maybe] (dev) `$skip_coupons`: `$coupon->is_valid_for_product()`: 2nd param?
		 * @todo    [now] (dev) `$skip_coupons`: `$coupon->is_valid_for_cart()`?
		 */
		function apply_delayed_coupon( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
			$coupons = alg_wc_url_coupons_data_storage_get( 'alg_wc_url_coupons', array() );
			if ( ! empty( $coupons ) ) {
				$skip_coupons = array();
				$key          = get_option( 'alg_wc_url_coupons_key', 'alg_apply_coupon' );
				foreach ( $coupons as $coupon_code ) {
					if (
						'yes' === get_option( 'alg_wc_url_coupons_delay_coupon_check_product', 'no' ) &&
						( $product = wc_get_product( $variation_id ? $variation_id : $product_id ) ) &&
						( $coupon_id = wc_get_coupon_id_by_code( $coupon_code ) ) && ( $coupon = new WC_Coupon( $coupon_id ) ) &&
						$coupon->is_type( 'fixed_product' ) && ! $coupon->is_valid_for_product( $product )
					) {
						$skip_coupons[] = $coupon_code;
					} else {
						$result = $this->apply_coupon( $coupon_code, $key );
						if ( true === $result ) {
							alg_wc_url_coupons_data_storage_set( 'alg_wc_url_coupons', null );
						}
					}
				}
				if ( ! empty( $skip_coupons ) ) {
					alg_wc_url_coupons_data_storage_set( 'alg_wc_url_coupons', $skip_coupons );
				}
			}
		}

		/**
		 * Check if the condition to hide coupons is met.
		 *
		 * @return bool Whether the condition to hide coupons is met.
		 *
		 * @since 1.6.7
		 * @version 1.6.7
		 */
		function is_hide_condition_met() {
			// Get the hide condition from the plugin settings.
			$hide_condition = get_option( 'alg_wc_url_coupons_hide_coupon_condition', 'always' );

			// Check if the hide condition is 'always'.
			if ( 'always' === $hide_condition ) {
				return true;
			} elseif ( 'url' === $hide_condition ) {
				// Get the applied coupon codes from the data storage.
				$applied_coupons = alg_wc_url_coupons_data_storage_get( 'alg_wc_url_coupons_applied_coupons', array() );
				// Check if there are any applied coupons.
				if ( ! empty( $applied_coupons ) ) {
					// Get the last applied coupon.
					$last_applied_coupon = end( $applied_coupons );
					// Check if the last applied coupon is still in the cart.
					$coupon_is_valid = in_array( $last_applied_coupon, WC()->cart->get_applied_coupons() );
					// Only hide the coupon field if the coupon is still valid.
					if ( $coupon_is_valid ) {
						return true;
					}
				}
			}

			// Return false if none of the conditions are met.
			return false;
		}

		/**
		 * Save the applied coupon to the data storage.
		 *
		 * @param string $coupon_code The code of the applied coupon.
		 *
		 * @since 1.6.7
		 * @version 1.6.7
		 */
		function save_applied_coupon( $coupon_code ) {
			$applied_coupons = alg_wc_url_coupons_data_storage_get( 'alg_wc_url_coupons_applied_coupons', array() );
			if ( ! in_array( $coupon_code, $applied_coupons ) ) {
				$applied_coupons[] = $coupon_code;
				// Set the applied coupons data to the data storage.
				alg_wc_url_coupons_data_storage_set( 'alg_wc_url_coupons_applied_coupons', $applied_coupons );
			}
		}

		/**
		 * Remove the coupon from the data storage.
		 *
		 * @param string $removed_coupon_code The code of the removed coupon.
		 *
		 * @since   1.6.7
		 * @version 1.6.7
		 */
		function remove_coupon( $removed_coupon_code ) {
			$applied_coupons = alg_wc_url_coupons_data_storage_get( 'alg_wc_url_coupons_applied_coupons', array() );
			if ( ( $key = array_search( $removed_coupon_code, $applied_coupons ) ) !== false ) {
				unset( $applied_coupons[ $key ] );
				// Set the updated applied coupons data to the data storage.
				alg_wc_url_coupons_data_storage_set( 'alg_wc_url_coupons_applied_coupons', $applied_coupons );
			}
		}

		/**
		 * add_to_cart_action_force_coupon_redirect.
		 *
		 * @version 1.3.2
		 * @since   1.3.2
		 */
		function add_to_cart_action_force_coupon_redirect( $url, $adding_to_cart ) {
			$key = get_option( 'alg_wc_url_coupons_key', 'alg_apply_coupon' );
			return ( isset( $_GET[ $key ] ) ? remove_query_arg( 'add-to-cart' ) : $url );
		}

		/**
		 * hide the coupon field on the checkout page if the condition to hide the coupon field is met.
		 *
		 * @version 1.0.1
		 * @since   1.0.1
		 */
		function hide_coupon_field_on_checkout( $enabled ) {
			// Check if the condition to hide the coupon field is met.
			$hide = $this->is_hide_condition_met();
			// If the condition is met and the user is on the checkout page, return false to hide the coupon field.
			if ( $hide && is_checkout() ) {
				return false;
			} else {
				// Otherwise, return the value of $enabled to show the coupon field.
				return $enabled;
			}
		}

		/**
		 * hide the coupon field on the cart page if the condition to hide the coupon field is met.
		 *
		 * @version 1.0.1
		 * @since   1.0.1
		 */
		function hide_coupon_field_on_cart( $enabled ) {
			// Check if the condition to hide the coupon field is met.
			$hide = $this->is_hide_condition_met();
			// If the condition is met and the user is on the cart page, return false to hide the coupon field.
			if ( $hide && is_cart() ) {
				return false;
			} else {
				// Otherwise, return the value of $enabled to show the coupon field.
				return $enabled;
			}
		}

		/**
		 * maybe_force_start_session.
		 *
		 * @version 1.6.4
		 * @since   1.3.0
		 */
		function maybe_force_start_session( $coupon_code = null ) {
			if (
				'yes' === get_option( 'alg_wc_url_coupons_force_start_session', 'yes' ) &&
				WC()->session && ! WC()->session->has_session()
			) {
				WC()->session->set_customer_session_cookie( true );
			}
		}

		/**
		 * maybe_force_start_session_everywhere.
		 *
		 * @version 1.6.4
		 * @since   1.6.4
		 */
		function maybe_force_start_session_everywhere() {
			if (
				'yes' === get_option( 'alg_wc_url_coupons_force_start_session', 'yes' ) &&
				'yes' === get_option( 'alg_wc_url_coupons_force_start_session_earlier', 'no' ) &&
				WC()->session && ! WC()->session->has_session()
			) {
				WC()->session->set_customer_session_cookie( true );
			}
		}

		/**
		 * maybe_set_additional_cookie.
		 *
		 * @version 1.3.0
		 * @since   1.3.0
		 */
		function maybe_set_additional_cookie( $coupon_code ) {
			if ( 'yes' === get_option( 'alg_wc_url_coupons_cookie_enabled', 'no' ) ) {
				setcookie( 'alg_wc_url_coupons', $coupon_code, ( time() + get_option( 'alg_wc_url_coupons_cookie_sec', 1209600 ) ), '/', $_SERVER['SERVER_NAME'], false );
			}
		}

		/**
		 * delay_notice.
		 *
		 * @version 1.6.4
		 * @since   1.3.0
		 *
		 * @todo    [maybe] (dev) still delay notice on `! $result`?
		 */
		function delay_notice( $coupon_code, $key, $result ) {
			if ( ! $result ) {
				return;
			}
			if ( WC()->cart->is_empty() ) {
				$all_notices = alg_wc_url_coupons_data_storage_get( 'wc_notices', array() );
				wc_clear_notices();
				alg_wc_url_coupons_data_storage_set( 'alg_wc_url_coupons_notices', $all_notices );
			}
		}

		/**
		 * display_delayed_notice.
		 *
		 * @version 1.6.4
		 * @since   1.2.5
		 */
		function display_delayed_notice() {
			if ( function_exists( 'WC' ) && isset( WC()->cart ) && ! WC()->cart->is_empty() && ( $notices = alg_wc_url_coupons_data_storage_get( 'alg_wc_url_coupons_notices', array() ) ) && ! empty( $notices ) ) {
				alg_wc_url_coupons_data_storage_set( 'alg_wc_url_coupons_notices', null );
				alg_wc_url_coupons_data_storage_set( 'wc_notices', $notices );
			}
		}

		/**
		 * redirect.
		 *
		 * @version 1.6.4
		 * @since   1.3.0
		 *
		 * @todo    [now] [!] (dev) different/same redirect on `! $result` (e.g. when coupon is applied twice)?
		 */
		function redirect( $coupon_code, $key, $result ) {
			if ( ! $result ) {
				return;
			}
			$keys_to_remove = array( $key );
			if ( 'yes' === get_option( 'alg_wc_url_coupons_remove_add_to_cart_key', 'yes' ) ) {
				$keys_to_remove[] = 'add-to-cart';
			}
			$keys_to_remove = apply_filters( 'alg_wc_url_coupons_keys_to_remove_on_redirect', $keys_to_remove );
			$redirect_url   = apply_filters( 'alg_wc_url_coupons_redirect_url', remove_query_arg( $keys_to_remove ), $coupon_code, $key );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		/**
		 * do_delay_coupon.
		 *
		 * @version 1.6.0
		 * @since   1.6.0
		 *
		 * @todo    [now] (dev) `fixed_cart`
		 * @todo    [maybe] (dev) `$coupon->is_valid_for_product()`: 2nd param?
		 */
		function do_delay_coupon( $coupon_code ) {
			if ( 'yes' === get_option( 'alg_wc_url_coupons_delay_coupon', 'no' ) ) {
				if ( 'yes' !== ( $delay_on_non_empty_cart = get_option( 'alg_wc_url_coupons_delay_coupon_non_empty_cart', 'yes' ) ) && ! WC()->cart->is_empty() ) {
					if ( 'no' === $delay_on_non_empty_cart ) {
						return false;
					} elseif ( 'check_product' === $delay_on_non_empty_cart ) {
						if ( ( $coupon_id = wc_get_coupon_id_by_code( $coupon_code ) ) && ( $coupon = new WC_Coupon( $coupon_id ) ) && $coupon->is_type( 'fixed_product' ) ) {
							foreach ( WC()->cart->get_cart() as $item ) {
								if ( ( $product = wc_get_product( $item['variation_id'] ? $item['variation_id'] : $item['product_id'] ) ) && $coupon->is_valid_for_product( $product ) ) {
									return false;
								}
							}
						}
					}
				}
				return true;
			}
			return false;
		}

		/**
		 * delay_coupon.
		 *
		 * @version 1.6.4
		 * @since   1.6.0
		 *
		 * @todo    [next] (dev) `alg_wc_url_coupons_delay_coupon`: require force session start?
		 * @todo    [maybe] (feature) `alg_wc_url_coupons_delay_coupon`: notices: customizable notice types?
		 */
		function delay_coupon( $coupon_code, $key ) {
			$result  = false;
			$notices = get_option( 'alg_wc_url_coupons_delay_coupon_notice', array() );
			$notices = array_map( 'do_shortcode', $notices );
			if ( ! WC()->cart->has_discount( $coupon_code ) ) {
				if ( wc_get_coupon_id_by_code( $coupon_code ) ) {
					$coupons   = alg_wc_url_coupons_data_storage_get( 'alg_wc_url_coupons', array() );
					$coupons[] = $coupon_code;
					alg_wc_url_coupons_data_storage_set( 'alg_wc_url_coupons', array_unique( $coupons ) );
					$notice = ( isset( $notices['success'] ) ? $notices['success'] : __( 'Coupon code applied successfully.', 'url-coupons-for-woocommerce-by-algoritmika' ) );
					if ( '' != $notice ) {
						wc_add_notice( str_replace( '%coupon_code%', $coupon_code, $notice ) );
					}
					$result = true;
					do_action( 'alg_wc_url_coupons_coupon_delayed', $coupon_code, $key, $result );
				} else {
					$notice = ( isset( $notices['error_not_found'] ) ? $notices['error_not_found'] : __( 'Coupon "%coupon_code%" does not exist!', 'url-coupons-for-woocommerce-by-algoritmika' ) );
					if ( '' != $notice ) {
						wc_add_notice( str_replace( '%coupon_code%', $coupon_code, $notice ), 'error' );
					}
				}
			} else {
				$notice = ( isset( $notices['error_applied'] ) ? $notices['error_applied'] : __( 'Coupon code already applied!', 'url-coupons-for-woocommerce-by-algoritmika' ) );
				if ( '' != $notice ) {
					wc_add_notice( str_replace( '%coupon_code%', $coupon_code, $notice ), 'error' );
				}
			}
			do_action( 'alg_wc_url_coupons_after_coupon_delayed', $coupon_code, $key, $result );
			return $result;
		}

		/**
		 * apply_url_coupon.
		 *
		 * e.g. http://example.com/?alg_apply_coupon=test
		 *
		 * @version 1.6.4
		 * @since   1.0.0
		 *
		 * @todo    [maybe] (feature) options to add products to cart with query arg?
		 * @todo    [maybe] (dev) `if ( ! WC()->cart->has_discount( $coupon_code ) ) {}`?
		 *
		 * @param null $args
		 */
		function apply_url_coupon( $args = null ) {
			$args        = wp_parse_args(
				$args,
				array(
					'key'         => get_option( 'alg_wc_url_coupons_key', 'alg_apply_coupon' ),
					'coupon_code' => '',
				)
			);
			$key         = $args['key'];
			$coupon_code = ! empty( $args['coupon_code'] ) ? sanitize_text_field( $args['coupon_code'] ) : ( isset( $_GET[ $key ] ) ? sanitize_text_field( $_GET[ $key ] ) : '' );
			if (
				! empty( $coupon_code ) &&
				function_exists( 'WC' ) &&
				apply_filters( 'alg_wc_url_coupons_apply_url_coupon_validation', true, $args )
			) {
				do_action( 'alg_wc_url_coupons_before_coupon_applied', $coupon_code, $key );
				if ( $this->do_delay_coupon( $coupon_code ) ) {
					// Delay coupon
					$result = $this->delay_coupon( $coupon_code, $key );
				} else {
					// Apply coupon
					$result = $this->apply_coupon( $coupon_code, $key );
				}
				do_action( 'alg_wc_url_coupons_after_coupon_applied', $coupon_code, $key, $result );
			}
		}

		/**
		 * apply_url_coupon_on_main_hook_triggered.
		 *
		 * @version 1.6.4
		 * @since   1.6.4
		 */
		function apply_url_coupon_on_main_hook_triggered() {
			if ( get_option( 'alg_wc_url_coupons_main_hook', 'wp_loaded' ) === current_filter() ) {
				$this->apply_url_coupon();
			}
		}

		/**
		 * get_possible_main_hooks.
		 *
		 * @version 1.6.4
		 * @since   1.6.4
		 */
		function get_possible_main_hooks() {
			return array(
				'wp_loaded' => __( 'WP Loaded', 'url-coupons-for-woocommerce-by-algoritmika' ),
				'init'      => __( 'Init', 'url-coupons-for-woocommerce-by-algoritmika' ),
			);
		}

		/**
		 * apply_coupon.
		 *
		 * @version 1.5.2
		 * @since   1.5.2
		 *
		 * @todo    [next] (dev) use `WC()->cart->apply_coupon()` instead of `WC()->cart->add_discount()`
		 */
		function apply_coupon( $coupon_code, $key ) {
			$result = WC()->cart->add_discount( $coupon_code );
			do_action( 'alg_wc_url_coupons_coupon_applied', $coupon_code, $key, $result );
			return $result;
		}

		/**
		 * reload_page_via_js.
		 *
		 * @version 1.6.4
		 * @since   1.6.4
		 */
		function reload_page_via_js() {
			if ( 'yes' !== get_option( 'alg_wc_url_coupons_javascript_reload', 'no' ) ) {
				return;
			}
			$js_data = array(
				'key'            => get_option( 'alg_wc_url_coupons_key', 'alg_apply_coupon' ),
				'create_cookie'  => 'cookie' === get_option( 'alg_wc_url_coupons_data_storage_type', 'session' ),
				'reloaded_param' => $this->get_reloaded_param_via_js(),
				'cookie_path'    => COOKIEPATH ? COOKIEPATH : '/',
				'cookie_domain'  => COOKIE_DOMAIN,
				'cookie_max_age' => get_option( 'alg_wc_url_coupons_cookie_sec', 1209600 ),
			);
			?>
			<script>
				jQuery(function ($) {
					let data = JSON.parse('<?php echo json_encode( $js_data ); ?>');
					let script = {
						data: null,
						init: function () {
							const params = new Proxy(new URLSearchParams(window.location.search), {
								get: (searchParams, prop) => searchParams.get(prop)
							});
							if (params[this.data.key] && params[this.data.key].length && (!params[this.data.reloaded_param])) {
								if (this.data.create_cookie) {
									script.setCookie('alg_wc_url_coupons', JSON.stringify([params[this.data.key]]));
								}
								if ('URLSearchParams' in window) {
									var searchParams = new URLSearchParams(window.location.search);
									searchParams.set(this.data.reloaded_param, "1");
									window.location.search = searchParams.toString();
								}
							}
						},
						setCookie: function (name, value) {
							let max_age = script.data.cookie_max_age;
							let path = script.data.cookie_path;
							let domain = script.data.cookie_domain;
							document.cookie = name + '=' + encodeURIComponent(value) + (max_age ? '; max-age=' + max_age : '') + (path ? '; path=' + path : '') + (domain ? '; domain=' + domain : '');
						}
					};
					script.data = data;
					script.init();
				});
			</script>
			<?php
		}

		/**
		 * do_not_apply_url_coupon_until_js_reload.
		 *
		 * @version 1.6.4
		 * @since   1.6.4
		 *
		 * @param $validation
		 *
		 * @return boolean
		 */
		function do_not_apply_url_coupon_until_js_reload( $validation ) {
			if (
				'yes' === get_option( 'alg_wc_url_coupons_javascript_reload', 'no' ) &&
				! isset( $_GET[ $this->get_reloaded_param_via_js() ] )
			) {
				$validation = false;
			}
			return $validation;
		}

		/**
		 * remove_reloaded_param_via_js_on_redirect.
		 *
		 * @version 1.6.4
		 * @since   1.6.4
		 *
		 * @param $keys_to_remove
		 *
		 * @return array
		 */
		function remove_reloaded_param_via_js_on_redirect( $keys_to_remove ) {
			if ( 'yes' === get_option( 'alg_wc_url_coupons_javascript_reload', 'no' ) ) {
				$keys_to_remove[] = $this->get_reloaded_param_via_js();
			}
			return $keys_to_remove;
		}

		/**
		 * get_reloaded_param_via_js.
		 *
		 * @version 1.6.4
		 * @since   1.6.4
		 *
		 * @return string
		 */
		function get_reloaded_param_via_js() {
			return apply_filters( 'alg_wc_url_coupons_javascript_reloaded_param', 'reloaded' );
		}

	}

endif;

return new Alg_WC_URL_Coupons_Core();
