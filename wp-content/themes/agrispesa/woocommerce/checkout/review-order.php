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
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$thumbnail = $_product->get_image();




			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<div class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">


					<?php if($woocommerce->cart->cart_contents_count == 1 && $_product->is_type('variation')): ?>
						<div class="checkout--preview--image">
							<?php echo $thumbnail; // PHPCS: XSS ok.
							?>
						</div>
					<?php else: ?>

						quanti prodotti? <?php echo WC()->cart->get_cart_contents_count(); ?>
					<?php endif; ?>
					<div class="sommair--totals--flex <?php if ($_product->is_type('variation')) { echo 'flex-start'; }?>">
						<div class="sommair--totals--sx">
							<?php if($woocommerce->cart->cart_contents_count > 1): ?>
							<div class="multi-product-images">
								<div class="checkout--preview--image">
									<?php echo $thumbnail; ?>
								</div>
							<?php endif; ?>
								<span class="small">
									<?php if ($_product->is_type('variation')) {
										$titolo = $_product->get_parent_data();
										$variazioni = $_product->get_attributes();
										echo $titolo['title'];
										echo '<div class="checkout-sommair--variations">';
										echo '<span>' . $variazioni['pa_dimensione'] . '</span>';
										echo '<span class="last">' . $variazioni['pa_tipologia'] . '</span>';
										echo '</div>';
									} else {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ) . '&nbsp;';
									} ?>

								</span>
							<?php if($woocommerce->cart->cart_contents_count > 1): ?>
							</div>
							<?php endif; ?>
						</div>
						<div class="sommair--totals--dx">
							<span><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</div>
					</div>

				</div>
				<?php
			}
		}

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
					<span class="small"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				</div>
				<div class="sommair--totals--dx">
					<span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
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

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

			<div class="sommair--totals--flex">
				<div class="sommair--totals--sx">
					<span class="bold"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
				</div>
				<div class="sommair--totals--dx">
					<span><?php wc_cart_totals_order_total_html(); ?></span>
				</div>
			</div>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</div>
</div>
