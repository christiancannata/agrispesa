<?php
/**
 * Functions file
 *
 * @package YITH\GiftCards\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** Define constant values */
defined( 'YWGC_CUSTOM_POST_TYPE_NAME' ) || define( 'YWGC_CUSTOM_POST_TYPE_NAME', 'gift_card' );
defined( 'YWGC_GIFT_CARD_PRODUCT_TYPE' ) || define( 'YWGC_GIFT_CARD_PRODUCT_TYPE', 'gift-card' );
defined( 'YWGC_PRODUCT_PLACEHOLDER' ) || define( 'YWGC_PRODUCT_PLACEHOLDER', '_ywgc_placeholder' );
defined( 'YWGC_CATEGORY_TAXONOMY' ) || define( 'YWGC_CATEGORY_TAXONOMY', 'giftcard-category' );

/** Race conditions - Gift cards duplicates */
defined( 'YWGC_RACE_CONDITION_BLOCKED' ) || define( 'YWGC_RACE_CONDITION_BLOCKED', '_ywgc_race_condition_blocked' );
defined( 'YWGC_RACE_CONDITION_UNIQUID' ) || define( 'YWGC_RACE_CONDITION_UNIQUID', '_ywgc_race_condition_uniqid' );

/*  plugin actions */
defined( 'YWGC_ACTION_RETRY_SENDING' ) || define( 'YWGC_ACTION_RETRY_SENDING', 'retry-sending' );
defined( 'YWGC_ACTION_DOWNLOAD_PDF' ) || define( 'YWGC_ACTION_DOWNLOAD_PDF', 'download-gift-pdf' );
defined( 'YWGC_ACTION_ENABLE_CARD' ) || define( 'YWGC_ACTION_ENABLE_CARD', 'enable-gift-card' );
defined( 'YWGC_ACTION_DISABLE_CARD' ) || define( 'YWGC_ACTION_DISABLE_CARD', 'disable-gift-card' );
defined( 'YWGC_ACTION_ADD_DISCOUNT_TO_CART' ) || define( 'YWGC_ACTION_ADD_DISCOUNT_TO_CART', 'ywcgc-add-discount' );
defined( 'YWGC_ACTION_VERIFY_CODE' ) || define( 'YWGC_ACTION_VERIFY_CODE', 'ywcgc-verify-code' );
defined( 'YWGC_ACTION_PRODUCT_ID' ) || define( 'YWGC_ACTION_PRODUCT_ID', 'ywcgc-product-id' );
defined( 'YWGC_ACTION_GIFT_THIS_PRODUCT' ) || define( 'YWGC_ACTION_GIFT_THIS_PRODUCT', 'ywcgc-gift-this-product' );

/*  gift card post_metas */
defined( 'YWGC_META_GIFT_CARD_ORDERS' ) || define( 'YWGC_META_GIFT_CARD_ORDERS', '_ywgc_orders' );
defined( 'YWGC_META_GIFT_CARD_CUSTOMER_USER' ) || define( 'YWGC_META_GIFT_CARD_CUSTOMER_USER', '_ywgc_customer_user' ); // Refer to user that use the gift card.
defined( 'YWGC_ORDER_ITEM_DATA' ) || define( 'YWGC_ORDER_ITEM_DATA', '_ywgc_order_item_data' );

/*  order item metas    */
defined( 'YWGC_META_GIFT_CARD_POST_ID' ) || define( 'YWGC_META_GIFT_CARD_POST_ID', '_ywgc_gift_card_post_id' );
defined( 'YWGC_META_GIFT_CARD_CODE' ) || define( 'YWGC_META_GIFT_CARD_CODE', '_ywgc_gift_card_code' );
defined( 'YWGC_META_GIFT_CARD_STATUS' ) || define( 'YWGC_META_GIFT_CARD_STATUS', '_ywgc_gift_card_status' );

if ( ! function_exists( 'ywgc_get_status_label' ) ) {
	/**
	 * Retrieve the status label for every gift card status
	 *
	 * @param YITH_YWGC_Gift_Card $gift_card Gift card object.
	 *
	 * @return string
	 */
	function ywgc_get_status_label( $gift_card ) {
		return $gift_card->get_status_label();
	}
}

if ( ! function_exists( 'ywgc_get_order_item_giftcards' ) ) {
	/**
	 * Retrieve the gift card ids associated to an order item
	 *
	 * @param int $order_item_id Order item ID.
	 *
	 * @return string|void
	 * @author YITH <plugins@yithemes.com>
	 * @since  1.0.0
	 */
	function ywgc_get_order_item_giftcards( $order_item_id ) {
		/*
		 * Let third party plugin to change the $order_item_id
		 */
		$order_item_id = apply_filters( 'yith_get_order_item_gift_cards', $order_item_id );
		$gift_ids      = wc_get_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_POST_ID );

		if ( is_numeric( $gift_ids ) ) {
			$gift_ids = array( $gift_ids );
		}

		if ( ! is_array( $gift_ids ) ) {
			$gift_ids = array();
		}

		return $gift_ids;
	}
}

if ( ! function_exists( 'ywgc_set_order_item_giftcards' ) ) {
	/**
	 * Retrieve the gift card ids associated to an order item
	 *
	 * @param int   $order_item_id the order item.
	 * @param array $ids           the array of gift card ids associated to the order item.
	 *
	 * @return string|void
	 * @since  1.0.0
	 */
	function ywgc_set_order_item_giftcards( $order_item_id, $ids ) {
		$ids = apply_filters( 'yith_ywgc_set_order_item_meta_gift_card_ids', $ids, $order_item_id );

		wc_update_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_POST_ID, $ids );

		$gift_card_codes = array();

		foreach ( $ids as $gc_id ) {
			$gc      = new YITH_YWGC_Gift_Card( array( 'ID' => $gc_id ) );
			$gc_code = $gc->get_code();
			$gift_card_codes[] = $gc_code;
		}

		wc_update_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_CODE, $gift_card_codes );

		do_action( 'yith_ywgc_set_order_item_meta_gift_card_ids_updated', $order_item_id, $ids );
	}
}

if ( ! function_exists( 'yith_get_attachment_image_url' ) ) {
	/**
	 * Get the attachment URL
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $size          Image size.
	 *
	 * @return string
	 */
	function yith_get_attachment_image_url( $attachment_id, $size = 'thumbnail' ) {
		if ( function_exists( 'wp_get_attachment_image_url' ) ) {
			$header_image_url = wp_get_attachment_image_url( $attachment_id, $size );
		} else {
			$header_image     = wp_get_attachment_image_src( $attachment_id, $size );
			$header_image_url = $header_image['url'];
		}
		return apply_filters( 'yith_ywcgc_attachment_image_url', $header_image_url );
	}
}

add_filter( 'yit_fw_metaboxes_type_args', 'ywgc_filter_balance_display' );

if ( ! function_exists( 'ywgc_filter_balance_display' ) ) {
	/**
	 * Fix the current balance display to match WooCommerce settings
	 *
	 * @param mixed $args args.
	 *
	 * @return mixed
	 */
	function ywgc_filter_balance_display( $args ) {
		if ( '_ywgc_balance_total' === $args['args']['args']['id'] ) {
			$args['args']['args']['value'] = round( $args['args']['args']['value'], wc_get_price_decimals() );
		}

		return $args;
	}
}

if ( ! function_exists( 'ywgc_disallow_gift_cards_with_same_title' ) ) {
	/**
	 * Avoid new gift cards with the same name
	 *
	 * @param mixed $messages messages.
	 */
	function ywgc_disallow_gift_cards_with_same_title( $messages ) {
		global $post;
		global $wpdb;
		$title   = $post->post_title;
		$post_id = $post->ID;

		do_action( 'yith_ywgc_before_disallow_gift_cards_with_same_title_query', $post_id, $messages );

		if ( get_post_type( $post_id ) !== 'gift_card' || ( get_post_type( $post_id ) === 'gift_card' && '' === $title ) ) {
			return $messages;
		}

		$wtitlequery = "SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'gift_card' AND post_title = %s AND ID != %d ";
		$wresults    = $wpdb->get_results( $wpdb->prepare( $wtitlequery, $title, $post_id ) ); //phpcs:ignore --Direct Database call is discouraged.

		if ( $wresults ) {
			$error_message = __( 'This title is already used. Please choose another one', 'yith-woocommerce-gift-cards' );
			add_settings_error( 'post_has_links', '', $error_message, 'error' );
			settings_errors( 'post_has_links' );
			$post->post_status = 'draft';
			wp_update_post( $post );

			return;
		}

		return $messages;

	}

	add_action( 'post_updated_messages', 'ywgc_disallow_gift_cards_with_same_title' );
}


/**
 * Ywgc_get_attachment_id_from_url
 *
 * @param  mixed $attachment_url attachment_url.
 * @return int
 */
function ywgc_get_attachment_id_from_url( $attachment_url = '' ) {
	global $wpdb;

	$attachment_id = false;

	if ( '' === $attachment_url ) {
		return;
	}

	$upload_dir_paths = wp_upload_dir();

	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

		$attachment_id = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
                WHERE wposts.ID = wpostmeta.post_id
            	AND wpostmeta.meta_key = '_wp_attached_file'
                AND wpostmeta.meta_value = %s
            	AND wposts.post_type = 'attachment'",
				$attachment_url
			)
		);
	}

	return $attachment_id;
}

if ( ! function_exists( 'yith_ywgc_get_view' ) ) {
	/**
	 * Get the view
	 *
	 * @param string $file_name Name of the file to get in views.
	 * @param array  $args      Arguments.
	 */
	function yith_ywgc_get_view( $file_name, $args = array() ) {
		$file_path = trailingslashit( YITH_YWGC_VIEWS_PATH ) . $file_name;

		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		if ( file_exists( $file_path ) ) {
			include $file_path;
		}
	}
}

if ( ! function_exists( 'yith_ywgc_get_view_html' ) ) {
	/**
	 * Get a view HTML
	 *
	 * @param string $view The view.
	 * @param array  $args Arguments.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	function yith_ywgc_get_view_html( $view, $args = array() ): string {
		ob_start();

		yith_ywgc_get_view( $view, $args );

		return ob_get_clean();
	}
}

function ywgc_string_to_float( $num ) {
	$dotPos   = strrpos( $num, '.' );
	$commaPos = strrpos( $num, ',' );
	$sep      = ( ( $dotPos > $commaPos ) && $dotPos ) ? $dotPos : ( ( ( $commaPos > $dotPos ) && $commaPos ) ? $commaPos : false );

	if ( ! $sep ) {
		return floatval( preg_replace( '/[^0-9]/', '', $num ) );
	}

	return floatval( preg_replace( '/[^0-9]/', '', substr( $num, 0, $sep ) ) . '.' . preg_replace( '/[^0-9]/', '', substr( $num, $sep + 1, strlen( $num ) ) ) );
}

if ( ! function_exists( 'yith_ywgc_current_screen_is' ) ) {
	/**
	 * Return true if current screen is one of the $ids.
	 *
	 * @param string|string[] $ids The screen ID(s).
	 *
	 * @return bool
	 * @since 3.0.0
	 */
	function yith_ywgc_current_screen_is( $ids ) {
		$ids       = (array) $ids;
		$screen_id = yith_ywgc_get_current_screen_id();

		return $screen_id && in_array( $screen_id, $ids, true );
	}
}

if ( ! function_exists( 'yith_ywgc_get_current_screen_id' ) ) {
	/**
	 * Retrieve the current screen ID.
	 *
	 * @return string|false
	 * @since 3.0.0
	 */
	function yith_ywgc_get_current_screen_id() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

		return ! ! $screen && is_a( $screen, 'WP_Screen' ) ? $screen->id : false;
	}
}

if ( ! function_exists( 'yith_ywgc_admin_screen_ids' ) ) {
	/**
	 * Return gift card admin screen ids.
	 * Useful to enqueue correct styles/scripts in Booking's pages.
	 *
	 * @return array
	 */
	function yith_ywgc_admin_screen_ids(): array {
		$screen_ids = array(
			'product',
			'edit-product',
		);

		return $screen_ids;
	}
}

if ( ! function_exists( 'yith_ywgc_is_module_active' ) ) {
	/**
	 * Is this module active?
	 *
	 * @param string $module_key The module key.
	 *
	 * @return bool
	 */
	function yith_ywgc_is_module_active( string $module_key ): bool {
		$modules_class = YITH_YWGC_Modules::get_instance();

		return $modules_class->is_module_active( $module_key );
	}
}
