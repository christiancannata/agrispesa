<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<div class="checkout--preview--header">
		<div class="checkout--preview--cost">
			<span><?php wc_cart_totals_order_total_html(); ?></span>
		</div>
		<div class="checkout--preview--items product-number">
			<span><?php echo WC()->cart->get_cart_contents_count(); ?> <?php if(WC()->cart->get_cart_contents_count() == 1) {echo 'prodotto';} else { echo ' prodotti';}?></span>
		</div>
	</div>

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2 style="display:none;">Il tuo scontrino</h2>

	<table cellspacing="0" class="shop_table shop_table_responsive">

		<tr class="cart-subtotal">
			<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th>
					<span class="small">Codice sconto</span>
					<?php
					 if(!$coupon->get_free_shipping()):?>
					 <br/>
					<span class="gift-car-number"><?php echo $coupon->get_code();?></span>
					<?php endif;?>
				</th>
				<td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>">
					<?php
					 if($coupon->get_free_shipping()):?>
						<span><?php echo $coupon->get_code(); ?></span><br/>
						<a class="woocommerce-remove-coupon" href="?remove_coupon=<?php echo $coupon->get_code(); ?>">[Elimina]</a>
					<?php else:?>
						<span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
					<?php endif;?>
				</td>
			</tr>
		<?php endforeach; ?>

		<?php if ( isset( WC()->cart->applied_gift_cards ) ) {

				foreach ( WC()->cart->applied_gift_cards as $code ) :

					$label  = apply_filters( 'yith_ywgc_cart_totals_gift_card_label', esc_html( __( 'Carta regalo ', 'yith-woocommerce-gift-cards' ) ), $code );
					$number = apply_filters( 'yith_ywgc_cart_totals_gift_card_label', esc_html( __( '', 'yith-woocommerce-gift-cards' ) . '' . $code ), $code );
					$amount = isset( WC()->cart->applied_gift_cards_amounts[ $code ] ) ? - WC()->cart->applied_gift_cards_amounts[ $code ] : 0;
					$value  = wc_price( $amount ) . ' <a href="' . esc_url(
						add_query_arg(
							'remove_gift_card_code',
							rawurlencode( $code ),
							defined( 'WOOCOMMERCE_CHECKOUT' ) ? wc_get_checkout_url() : wc_get_cart_url()
						)
					) .
							'" class="ywgc-remove-gift-card " data-gift-card-code="' . esc_attr( $code ) . '">' . apply_filters( 'ywgc_remove_gift_card_text', esc_html__( '[Elimina]', 'yith-woocommerce-gift-cards' ) ) . '</a>';
					?>

					<div class="sommair--totals--flex">
						<div class="sommair--totals--sx">
							<span class="small"><?php echo wp_kses( $label, 'post' ); ?></span><br/>
							<span class="gift-car-number"><?php echo wp_kses( $number, 'post' ); ?></span>
						</div>
						<div class="sommair--totals--dx">
							<span class="happy-price"><?php echo wp_kses( $value, 'post' ); ?></span>
						</div>
					</div>

					<?php do_action( 'ywgc_gift_card_checkout_cart_table', $code, $amount ); ?>

					<?php
				endforeach;

			} ?>





		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

		<?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>

			<tr class="shipping">
				<th><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>"><?php woocommerce_shipping_calculator(); ?></td>
			</tr>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php
		if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = '';

			if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
				/* translators: %s location. */
				$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
			}

			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( WC()->cart->get_tax_totals() as $code => $tax ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th><?php echo esc_html( $tax->label ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
						<td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
					<td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
				<?php
			}
		}
		?>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<tr class="order-total">
			<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	</table>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

	<div class="checkout--preview--bottom">
		<div class="checkout--preview--items mg-t">
			<span class="is-title"><span class="icon-check is-icon"></span>Consegna a domicilio</span>
			<span class="is-description">
				Diamo tempo ai contadini di preparare i prodotti che hai ordinato. 
				Per questo riceverai la scatola tra lunedì e mercoledì prossimo: te lo confermeremo in una mail.<br/>
				In 24 ore l’agricoltura contadina non può lavorare, è facile da capire.
			</span>
		</div>
	</div>

</div>
