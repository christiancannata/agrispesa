<?php
/**
 * Checkout gift cards form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-gift-cards.php.
 *
 * @author  YIThemes
 * @package yith-woocommerce-gift-cards\templates\checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! apply_filters( 'yith_gift_cards_show_field', true ) ) {
	return;
}

$direct_display = get_option( 'ywgc_display_form', 'ywgc_display_form_hidden' ) === 'ywgc_display_form_visible' ? 'yes' : 'no';

if ( 'yes' !== $direct_display ) :

	?>
	<div class="ywgc_have_code">
		<?php
		if ( get_option( 'ywgc_icon_text_before_gc_form', 'no' ) === 'yes' ) {
			$icon = '<img src="' . YITH_YWGC_ASSETS_IMAGES_URL . 'card_giftcard_icon.svg "class="material-icons ywgc_woocommerce_message_icon"  style="margin-right: 6px; float: left;">';
		} else {
			$icon = ''; }

		wc_print_notice( $icon . get_option( 'ywgc_text_before_gc_form', esc_html__( '', 'yith-woocommerce-gift-cards' ) ) . '<span class="icon-heart gift-card-icon-heart"></span> Hai ricevuto una Gift Card? <a href="#" class="ywgc-show-giftcard">' . get_option( 'ywgc_link_text_before_gc_form', esc_html__( 'Inserisci il codice!', 'yith-woocommerce-gift-cards' ) ) . '</a>', 'notice' );
		?>

	</div>
	<?php

endif;

?>

<div class="ywgc_enter_code coupon-form" method="post" style="<?php echo ( 'yes' !== $direct_display ? 'display:none' : '' ); ?>">



	<?php

	if ( get_option( 'ywgc_minimal_cart_total_option', 'no' ) === 'yes' && WC()->cart->total < get_option( 'ywgc_minimal_cart_total_value', '0' ) ) :

		?>
		<p class="woocommerce-error" role="alert">

			<?php echo esc_html_x( 'In order to apply the gift card, the total amount in the cart has to be at least', 'Apply gift card', 'yith-woocommerce-gift-cards' ) . ' ' . wp_kses( get_option( 'ywgc_minimal_cart_total_value' ), 'post' ) . wp_kses( get_woocommerce_currency_symbol(), 'post' ); ?>

		</p>

		<?php

	endif;
	?>



		<div class="coupon-form--sx">
			<input type="text" name="gift_card_code" class="input-text"
				placeholder="<?php echo esc_attr( apply_filters( 'ywgc_checkout_box_placeholder', _x( 'Codice della Gift Card', 'Apply gift card', 'yith-woocommerce-gift-cards' ) ) ); ?>"
				id="giftcard_code" value="" />
		</div>
		<div class="coupon-form--dx">
			<button type="submit" class="btn btn-primary btn-small ywgc_apply_gift_card_button" name="ywgc_apply_gift_card" value="<?php echo wp_kses( get_option( 'ywgc_apply_gift_card_button_text', esc_html__( 'Applica', 'yith-woocommerce-gift-cards' ) ), 'post' ); ?>"><?php echo wp_kses( get_option( 'ywgc_apply_gift_card_button_text', esc_html__( 'Applica', 'yith-woocommerce-gift-cards' ) ), 'post' ); ?></button>
			<input type="hidden" name="is_gift_card" value="1" />
		</div>

		<div class="clear"></div>

		<?php

		if ( WC()->cart->total < get_option( 'ywgc_minimal_cart_total_value' ) ) :

			?>
			<div class="yith_wc_gift_card_blank_brightness"></div>
			<?php

		endif;

		?>



</div>
