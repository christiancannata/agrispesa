<?php
/**
 * Gift Card product add to cart
 *
 * @author  Yithemes
 * @package yith-woocommerce-gift-cards\templates\single-product\add-to-cart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
	<h3><?php echo wp_kses( get_option( 'ywgc_select_amount_title', esc_html__( 'Set an amount', 'yith-woocommerce-gift-cards' ) ), 'post' ); ?></h3>


	<?php if ( $amounts ) : ?>


		<?php do_action( 'yith_gift_card_amount_selection_first_option', $product ); ?>
		<?php foreach ( $amounts as $value => $item ) : ?>
		<button class="ywgc-predefined-amount-button ywgc-amount-buttons" value="<?php echo wp_kses( $item['amount'], 'post' ); ?>"
				data-price="<?php echo wp_kses( $item['price'], 'post' ); ?>"
				data-wc-price="<?php echo strip_tags( wc_price( $item['price'] ) ); //phpcs:ignore --wc function?>">
			<?php echo wp_kses( apply_filters( 'yith_gift_card_select_amount_values', $item['title'], $item ), 'post' ); ?>
		</button>

		<input type="hidden" class="ywgc-predefined-amount-button ywgc-amount-buttons" value="<?php echo wp_kses( $item['amount'], 'post' ); ?>"
			data-price="<?php echo wp_kses( $item['price'], 'post' ); ?>"
			data-wc-price="<?php echo strip_tags( wc_price( $item['price'] ) ); //phpcs:ignore --wc function?>" >

	<?php endforeach; ?>
		<?php
endif;

	do_action( 'yith_gift_card_amount_selection_last_option', $product );
	do_action( 'yith_gift_cards_template_after_amounts', $product );
