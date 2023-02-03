<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
global $woocommerce;

?>
<div class="checkout--review-order woocommerce-checkout-review-order-table zig-zag-bottom">

	<div class="checkout--preview">
		<div class="checkout--preview--header">
			<div class="checkout--preview--cost">
				<span><?php wc_cart_totals_order_total_html(); ?></span>
			</div>
			<div class="checkout--preview--items product-number">
				<span><?php echo WC()->cart->get_cart_contents_count(); ?> <?php if(WC()->cart->get_cart_contents_count() == 1) {echo 'prodotto';} else { echo ' prodotti';}?></span>
			</div>
		</div>
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</div>

	<div class="sommair--totals ">

		<div class="sommair--totals--flex">
			<div class="sommair--totals--sx">
				<span class="small"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
			</div>
			<div class="sommair--totals--dx">
				<span><?php wc_cart_totals_subtotal_html(); ?></span>
			</div>
		</div>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="sommair--totals--flex cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<div class="sommair--totals--sx">
					<span class="small">Coupon</span>

					<?php
					 if(!$coupon->get_free_shipping()):?>
					 <br/>
					<span class="gift-car-number"><?php echo $coupon->code;?></span>
					<?php endif;?>
				</div>
				<div class="sommair--totals--dx">
					<?php
					 if($coupon->get_free_shipping()):?>
						<span><?php echo $coupon->code; ?></span><br/>
						<a class="woocommerce-remove-coupon" href="<?php echo WC()->cart->remove_coupon( $coupon->code ); ?>">[Elimina]</a>
					<?php else:?>
						<span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
					<?php endif;?>

				</div>
			</div>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="sommair--totals--flex fee">
				<div class="sommair--totals--sx">
					<span class="small"><?php echo esc_html( $fee->name ); ?></span>
				</div>
				<div class="sommair--totals--dx">
					<span><?php wc_cart_totals_fee_html( $fee ); ?></span>
				</div>
			</div>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<div class="sommair--totals--flex tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<div class="sommair--totals--sx">
							<span class="small"><?php echo esc_html( $tax->label ); ?></span>
						</div>
						<div class="sommair--totals--dx">
							<span><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="sommair--totals--flex tax-total">
					<div class="sommair--totals--sx">
						<span class="small"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
					</div>
					<div class="sommair--totals--dx">
						<span><?php wc_cart_totals_taxes_total_html(); ?></span>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( isset( WC()->cart->applied_gift_cards ) ) {

				foreach ( WC()->cart->applied_gift_cards as $code ) :

					$label  = apply_filters( 'yith_ywgc_cart_totals_gift_card_label', esc_html( __( 'Gift card ', 'yith-woocommerce-gift-cards' ) ), $code );
					$number = apply_filters( 'yith_ywgc_cart_totals_gift_card_label', esc_html( __( '', 'yith-woocommerce-gift-cards' ) . '' . $code ), $code );
					$amount = isset( WC()->cart->applied_gift_cards_amounts[ $code ] ) ? - WC()->cart->applied_gift_cards_amounts[ $code ] : 0;
					$value  = wc_price( $amount ) . ' <a href="' . esc_url(
						add_query_arg(
							'remove_gift_card_code',
							rawurlencode( $code ),
							defined( 'WOOCOMMERCE_CHECKOUT' ) ? wc_get_checkout_url() : wc_get_cart_url()
						)
					) .
							'" class="ywgc-remove-gift-card " data-gift-card-code="' . esc_attr( $code ) . '">' . apply_filters( 'ywgc_remove_gift_card_text', esc_html__( '[Remove]', 'yith-woocommerce-gift-cards' ) ) . '</a>';
					?>

					<div class="sommair--totals--flex">
						<div class="sommair--totals--sx">
							<span class="small"><?php echo wp_kses( $label, 'post' ); ?></span><br/>
							<span class="gift-car-number"><?php echo wp_kses( $number, 'post' ); ?></span>
						</div>
						<div class="sommair--totals--dx">
							<span><?php echo wp_kses( $value, 'post' ); ?></span>
						</div>
					</div>

					<?php do_action( 'ywgc_gift_card_checkout_cart_table', $code, $amount ); ?>

					<?php
				endforeach;
			}?>

		<?php //do_action( 'woocommerce_review_order_before_order_total' ); ?>

			<div class="sommair--totals--flex woocommerce-sommair-end">
				<div class="sommair--totals--sx">
					<span class="bold"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
				</div>
				<div class="sommair--totals--dx">
					<span><?php wc_cart_totals_order_total_html(); ?></span>
				</div>
			</div>
		<div class="woocommerce-after-sommair">
		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
		</div>

	</div>
	<div class="checkout--preview--bottom">
		<div class="checkout--preview--items mg-t">
			<span class="is-title"><span class="icon-check is-icon"></span>Spedizione e consegna</span>
			<span class="is-description">Ai nostri contadini diamo il tempo di raccogliere i prodotti che hai ordinato. Per questo non riceverai la scatola in 24 ore, ma lunedì o mercoledì prossimo; a seconda di dove vivi.</span>
		</div>
		<div class="checkout--preview--items mg-t">
			<span class="is-title"><span class="icon-check is-icon"></span>Pagamento e fattura</span>
			<span class="is-description">Oltre alla conferma d'ordine, provederemo a mandarti la fattura una volta confezionata la scatola. Garantiamo pagamenti sicuri.</span>
		</div>
	</div>
</div>
