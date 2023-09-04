<?php
/**
 * Gift Card product add to cart
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\GiftCards\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$date_format = apply_filters( 'yith_wcgc_date_format', 'Y-m-d' );

?>

<input type="hidden" name="ywgc-is-physical" value="1" />

<h3><?php echo wp_kses( get_option( 'ywgc_delivery_info_title', esc_html__( 'Delivery info', 'yith-woocommerce-gift-cards' ) ), 'post' ); ?></h3>

<div class="gift-card-content-editor step-content">

	<div class="ywgc-message clearfix">
		<label for="ywgc-edit-message"><?php echo wp_kses( apply_filters( 'ywgc_edit_message_label', esc_html__( 'Message: ', 'yith-woocommerce-gift-cards' ) ), 'post' ); ?></label>
		<textarea id="ywgc-edit-message" name="ywgc-edit-message" rows="5"></textarea>
	</div>

</div>
