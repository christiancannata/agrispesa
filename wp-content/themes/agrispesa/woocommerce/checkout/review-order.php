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
			$hasTest = false;

			if ( isset( $cart_item['test'] ) ) {
				$hasTest = true;

				$test       = $cart_item['test'];
				$testName   = get_user_name_from_test( $test );
				$poshNumber = $test['test_progressive_id'];
				$poshMood = $test['mood'];
				$poshColor = get_color_from_test( $test );
			}


			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<div class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">


					<?php if($woocommerce->cart->cart_contents_count == 1): ?>
						<div class="checkout--preview--image">
							<?php if($hasTest) {
								echo '<img src="' . get_template_directory_uri() . '/img/quiz/posh-'.$poshColor.'.jpg"
										 class="cart-color-posh" alt="Posh"/>';
							} else {
									echo $thumbnail; // PHPCS: XSS ok.
							}
							?>
						</div>
					<?php endif; ?>
					<div class="sommair--totals--flex">
						<div class="sommair--totals--sx">
							<?php if($woocommerce->cart->cart_contents_count > 1): ?>
							<div class="multi-product-images">
								<div class="checkout--preview--image">
									<?php if($hasTest) {
										echo '<img src="' . get_template_directory_uri() . '/img/quiz/posh-'.$poshColor.'.jpg"
				                 class="cart-color-posh" alt="Posh"/>';
									} else {
											echo $thumbnail; // PHPCS: XSS ok.
									}
									?>

								</div>
							<?php endif; ?>

								<span class="small">
									<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ) . '&nbsp;'; ?>
									<?php if($hasTest) {
												echo 'n°'.  $poshNumber;
												echo '<br/><div class="test-meta-cart">';
												echo '<span>' . $poshMood . '</span>';
												echo '<span>' . $testName . '</span>';
												echo '<span>' . $poshColor . '</span>';
												echo '</div>';
												echo '<div class="allergeni-lista">';
												echo '<p class="allergeni-lista--label open-allerg">Allergeni <span class="icon-arrow-down"></span></p>';
												echo '<div class="allergeni-lista--box">';
												echo '<p class="allergeni-lista--disclaimer">Potrebbero essere presenti alcuni dei seguenti allergeni:</p>';
												echo get_field('lista_allergeni', 'option');
												echo '</div>';
												echo '</div>';

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
						<!-- AIUTO CHRISTIAN -->
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
