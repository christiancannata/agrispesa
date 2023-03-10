<?php
/**
 * Gift Card product add to cart
 *
 * @author  Yithemes
 * @package yith-woocommerce-gift-cards\templates\yith-gift-cards\
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$date_format = apply_filters( 'yith_wcgc_date_format', 'Y-m-d' );

?>

<input type="hidden" name="ywgc-is-physical" value="1" />

<div class="gift-card-content-editor step-content">

	<div class="ywgc-message">
		<label for="ywgc-edit-message"><?php echo wp_kses( apply_filters( 'ywgc_edit_message_label', esc_html__( 'Message: ', 'yith-woocommerce-gift-cards' ) ), 'post' ); ?></label>
		<textarea id="ywgc-edit-message" name="ywgc-edit-message" rows="5" placeholder="<?php echo wp_kses( get_option( 'ywgc_sender_message_placeholder', esc_html__( 'ENTER A MESSAGE FOR THE RECIPIENT', 'yith-woocommerce-gift-cards' ) ), 'post' ); ?>" ></textarea>
	</div>

</div>
