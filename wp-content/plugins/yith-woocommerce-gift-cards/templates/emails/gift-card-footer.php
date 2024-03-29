<?php
/**
 * Add a footer for the gift card email
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\GiftCards\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$shop_name = apply_filters( 'yith_ywgc_email_shop_name', $shop_name );
$shop_link = apply_filters( 'yith_ywgc_email_shop_link', get_permalink( wc_get_page_id( 'shop' ) ) );
if ( ! $shop_name || ! $shop_link ) {
	return;
}
?>

<div class="ywgc-footer">
	<a target="_blank" class="center-email" href="<?php echo esc_url( $shop_link ); ?>"><?php echo wp_kses_post( $shop_name ); ?></a>
</div>
