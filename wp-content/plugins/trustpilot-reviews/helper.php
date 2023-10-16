<?php

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

use Trustpilot\Review\TrustpilotHttpClient;
use Trustpilot\Review\TrustpilotLogger;

function trustpilot_get_default_settings() {
	return '{"general":{"key":"","invitationTrigger":"orderConfirmed", "mappedInvitationTrigger":[]},"trustbox":{"trustboxes":[]},
     "skuSelector": "none", "mpnSelector": "none", "gtinSelector": "none"}';
}

function trustpilot_get_default_past_orders() {
	return '0';
}

function trustpilot_get_default_failed_orders() {
	return '{}';
}

function trustpilot_get_default_plugin_status() {
	return '{"pluginStatus": 200, "blockedDomains": []}';
}

function trustpilot_get_field( $field ) {
	return json_decode( stripslashes( get_option( $field, '{}' ) ) );
}

function trustpilot_set_field( $field, $new_field ) {
	if (current_user_can('manage_options')) {
		$json = json_encode( $new_field );
		update_option( $field, $json );
	}
}

function trustpilot_compatible() {
	return version_compare( phpversion(), '5.2.0' ) >= 0 && function_exists( 'curl_init' );
}

/**
 * Get values from master settings field by key
 */

function trustpilot_get_settings( $key = null, $settings = null ) {
	if ( null == $settings ) {
		$settings = get_option( 'trustpilot_settings', trustpilot_get_default_settings() );
	}
	if ( null == $key ) {
		return $settings;
	}
	return json_decode( stripslashes( $settings ) )->{$key};
}

function trustpilot_get_page_url( $page ) {
	try {
		$value = ( isset( $page ) ) ? $page : trustpilot_get_settings( 'trustbox' )->page;
		switch ( $value ) {
			case 'landing':
				$url = trustpilot_get_landing_url();
				break;
			case 'category':
				$url = trustpilot_get_category_url();
				break;
			case 'product':
				$url = trustpilot_get_product_url();
				break;
			default:
				$url = trustpilot_get_landing_url();
		}
		if ( is_wp_error( $url ) ) {
			throw new \Exception( $url->get_error_message() );
		}
		return str_replace( array( 'http:', 'https:' ), '', $url );
	} catch ( \Throwable $e ) {
		$message = 'Unable to find URL for a page ' . $page;
		TrustpilotLogger::error(
			$e,
			$message,
			array(
				'page' => $page,
			)
		);
		return str_replace( array( 'http:', 'https:' ), '', get_home_url() );
	} catch ( \Exception $e ) {
		$message = 'Unable to find URL for a page ' . $page;
		TrustpilotLogger::error(
			$e,
			$message,
			array(
				'page' => $page,
			)
		);
		return str_replace( array( 'http:', 'https:' ), '', get_home_url() );
	}
}

function trustpilot_get_landing_url() {
	return get_home_url();
}

function trustpilot_get_first_category() {
	$category_args = array(
		'taxonomy'   => 'product_cat',
		'childless'  => true,
		'orderby'    => 'id',
		'number'     => 1,
		'empty'      => 0,
		'hide_empty' => 1,
	);
	$categories    = (array) get_categories( $category_args );
	if ( is_array( $categories ) && ! empty( $categories ) ) {
		return (object) array_values( $categories )[0];
	} else {
		return false;
	}
}

function trustpilot_get_category_url() {
	$category = trustpilot_get_first_category();
	if ( $category ) {
		return get_term_link( $category->term_id, 'product_cat' );
	} else {
		return get_permalink( trustpilot_get_page_id( 'shop' ) );
	}
}

function trustpilot_get_first_product() {
	if ( function_exists( 'wc_get_products' ) ) {
		$product_args = array(
			'visibility' => 'visible',
			'status'     => 'publish',
			'limit'      => 1,
			'orderby'    => 'id',
		);

		$products = wc_get_products( $product_args );

		if ( ! empty( $products ) ) {
			return $products[0];
		} else {
			return '';
		}
	} else {
		$product_args = array(
			'posts_per_page' => 1,
			'orderby'        => 'published_at',
			'order'          => 'DESC',
			'post_type'      => 'product',
		);

		$posts = get_posts( $product_args );

		if ( ! empty( $posts ) && function_exists( 'wc_get_product' ) ) {
			return wc_get_product( $posts[0] );
		} else {
			return '';
		}
	}
}

function trustpilot_get_product_url() {
	$product = trustpilot_get_first_product();
	if ( ! empty( $product ) ) {
		if ( method_exists( $product, 'get_id' ) ) {
			return get_permalink( $product->get_id() );
		} else {
			return get_permalink( $product->id );
		}
	} else {
		return get_permalink( trustpilot_get_page_id( 'shop' ) );
	}
}

function trustpilot_get_page_id( $page ) {
	if ( function_exists( 'wc_get_page_id' ) ) {
		return wc_get_page_id( $page );
	} else {
		return woocommerce_get_page_id( $page );
	}
}

function trustpilot_get_product_sku() {
	$skus = array();
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		$product = trustpilot_get_first_product();
		array_push( $skus, TRUSTPILOT_PRODUCT_ID_PREFIX . trustpilot_get_inventory_attribute( 'id', $product ) );

		if ( ! empty( $product ) ) {
			array_push( $skus, trustpilot_get_inventory_attribute( 'sku', $product ) );
		}
	}
	return implode( ',', $skus );
}

function trustpilot_get_product_name() {
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		$product = trustpilot_get_first_product();
		if ( ! empty( $product ) ) {
			if ( method_exists( $product, 'get_name' ) ) {
				return $product->get_name();
			} else {
				return $product->get_title();
			}
		}
	}
	return '';
}

/**
 * WooCommerce get version number
 */
function trustpilot_get_woo_version_number() {
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		$plugin_folder = get_plugins( '/woocommerce' );
		$plugin_file   = 'woocommerce.php';

		if ( isset( $plugin_folder[ $plugin_file ]['Version'] ) ) {
			return $plugin_folder[ $plugin_file ]['Version'];
		} else {
			return null;
		}
	} else {
		return get_bloginfo( 'version' );
	}
}

/**
 * Get product attr based on product review settings
 */
function trustpilot_get_inventory_attribute_field( $attr ) {
	switch ( $attr ) {
		case 'sku':
			$field = trustpilot_get_settings( 'skuSelector' );
			// treat 'none' as 'sku'
			if ( 'none'  == $field || '' == $field ) {
				$field = 'sku';
			}
			return $field;
		case 'gtin':
			return trustpilot_get_settings( 'gtinSelector' );
		case 'mpn':
			return trustpilot_get_settings( 'mpnSelector' );
		default:
			return $attr;
	}
}

/**
 * Get product sku based on product reviews settings
 */
function trustpilot_get_inventory_attribute( $attr, $product, $useDbAttribute = true ) {
	$attr_field = $useDbAttribute ? trustpilot_get_inventory_attribute_field( $attr ) : $attr;
	switch ( $attr_field ) {
		case 'sku':
			$value = (string) ( method_exists( $product, 'get_sku' ) ?
				$product->get_sku() : '' );
			return $value ? $value : '';
		case 'id':
			if (method_exists( $product, 'get_id' )) {
				return (string) $product->get_id();
			}
			return is_object($product) ? (string) $product->id : '';
		default:
			$value = $product->get_attribute( $attr_field );
			$value = apply_filters( 'trustpilot_inventory_attribute_value', $value, $attr_field, $product );
			return $value ? $value : '';
	}
}
