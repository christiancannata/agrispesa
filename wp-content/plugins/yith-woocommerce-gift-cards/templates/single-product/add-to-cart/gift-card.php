<?php
/**
 * Gift Card product add to cart
 *
 * @author  YITH <plugins@yithemes.com>
 * @package yith-woocommerce-gift-cards\templates\single-product\add-to-cart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

do_action( 'yith_gift_cards_template_before_add_to_cart_form' );
do_action( 'woocommerce_before_add_to_cart_form' );

?>


<form class="gift-cards_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( yit_get_prop( $product, 'id' ) ); ?>">

	<input type='hidden' name='ywgc_has_custom_design' value='1'>

	<?php do_action( 'yith_gift_cards_template_after_form_opening' ); ?>

	<?php if ( $product->is_virtual() ) : ?>
		<input type="hidden" name="ywgc-is-digital" value="1" />
	<?php endif; ?>

	<?php if ( ! $product->is_purchasable() ) : ?>
		<p class="gift-card-not-valid">
			<?php esc_html_e( 'This product cannot be purchased', 'yith-woocommerce-gift-cards' ); ?>
		</p>
	<?php else : ?>

		<?php if ( $product->is_virtual() ) : ?>
			<?php do_action( 'yith_ywgc_gift_card_design_section', $product ); ?>
		<?php endif; ?>


		<div class="gift-cards-list" 
		<?php
		if ( count( $product->get_amounts_to_be_shown() ) === 1 ) {
			echo 'style="display: none"';}
		?>
		>
			<?php do_action( 'yith_ywgc_show_gift_card_amount_selection', $product ); ?>
		</div>

		<?php do_action( 'yith_ywgc_gift_card_delivery_info_section', $product ); ?>

		<?php do_action( 'yith_gift_cards_template_before_add_to_cart_button' ); ?>

		<div class="ywgc-product-wrap" style="display:none;">
			<?php
			/**
			 * Yith_gift_cards_template_before_gift_card Hook
			 */
			do_action( 'yith_gift_cards_template_before_gift_card' );

			/**
			 * Yith_gift_cards_template_gift_card hook. Used to output the cart button and placeholder for variation data.
			 *
			 * @since  2.4.0
			 * @hooked yith_gift_cards_template_gift_card - 10 Empty div for variation data.
			 * @hooked yith_gift_cards_template_gift_card_add_to_cart_button - 20 Qty and cart button.
			 */
			do_action( 'yith_gift_cards_template_gift_card' );

			/**
			 * Yith_gift_cards_template_after_gift_card Hook
			 */
			do_action( 'yith_gift_cards_template_after_gift_card' );
			?>
		</div>

		<?php do_action( 'yith_gift_cards_template_after_add_to_cart_button' ); ?>

	<?php endif; ?>

	<?php do_action( 'yith_gift_cards_template_after_gift_card_form' ); ?>
</form>

<?php do_action( 'yith_gift_cards_template_after_add_to_cart_form' ); ?>

<?php do_action( 'yith_ywgc_gift_card_preview_end', $product ); ?>
