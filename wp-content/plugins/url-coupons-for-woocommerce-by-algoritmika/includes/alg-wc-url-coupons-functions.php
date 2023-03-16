<?php
/**
 * URL Coupons for WooCommerce - Functions.
 *
 * @version 1.6.5
 * @since   1.6.4
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'alg_wc_url_coupons_data_storage_get' ) ) {
	/**
	 * alg_wc_url_coupons_data_storage_get.
	 *
	 * @version 1.6.4
	 * @since   1.6.4
	 *
	 * @param $key
	 * @param string $default_value
	 *
	 * @return string
	 */
	function alg_wc_url_coupons_data_storage_get( $key, $default_value = '' ) {
		$storage_type = apply_filters( 'alg_wc_url_coupons_data_storage_type', 'session' );
		$data         = '';
		if ( 'session' === $storage_type && isset( WC()->session ) ) {
			$data = WC()->session->get( $key, $default_value );
		} elseif ( 'cookie' === $storage_type ) {
			$data = isset( $_COOKIE[ $key ] ) ? json_decode( stripslashes( $_COOKIE[ $key ] ) ) : $default_value;
		}
		return $data;
	}
}

if ( ! function_exists( 'alg_wc_url_coupons_data_storage_set' ) ) {
	/**
	 * alg_wc_url_coupons_data_storage_set.
	 *
	 * @version 1.6.5
	 * @since   1.6.4
	 *
	 * @param $key
	 *
	 * @param $value
	 */
	function alg_wc_url_coupons_data_storage_set( $key, $value ) {
		$storage_type = apply_filters( 'alg_wc_url_coupons_data_storage_type', 'session' );
		if ( 'session' === $storage_type && isset( WC()->session ) ) {
			WC()->session->set( $key, $value );
		} elseif ( 'cookie' === $storage_type ) {
			if ( is_null( $value ) ) {
				wc_setcookie( $key, json_encode( $value ), 1 );
			} else {
				wc_setcookie( $key, json_encode( $value ), ( time() + get_option( 'alg_wc_url_coupons_cookie_sec', 1209600 ) ) );
			}
		}
	}
}