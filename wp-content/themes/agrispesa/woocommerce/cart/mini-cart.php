<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>

	<div class="minicart--layout">
	<div class="minicart--container">

		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		$i=1;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

				?>


					<div class="minicart-box">
					<?php
					echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'woocommerce_cart_item_remove_link',
						sprintf(
							'<a href="%s" class="icon-close" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"></a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							esc_attr__( 'Remove this item', 'woocommerce' ),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() )
						),
						$cart_item_key
					);
					?>

					<a class="minicart-box--link" href="<?php echo esc_url( $product_permalink );?>">
						<span class="minicart-box--quantity"><?php echo $cart_item['quantity']; ?></span>
						<?php echo $thumbnail; ?>
					</a>

					<div class="minicart-box--info">
						<div class="minicart-box--info--top">

						<?php if ($_product->is_type('variation')) {
								$product_data = $cart_item['data'];
								$titolo = $product_data->get_parent_data();
								$variazioni = $_product->get_attributes();
								echo '<h4 class="minicart-box--title"><a href="' . esc_url($product_permalink) . '" class="cart-product-var-title">' . $titolo['title'] . '</a></h4>';
								echo '<div class="new-cart--variations">';
								echo '<span class="cart-product-var-var">' . $variazioni['pa_dimensione'] . '</span>';
								echo '<span class="cart-product-var-var last">' . $variazioni['pa_tipologia'] . '</span>';
								echo '</div>';
							} else {
								echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<h4 class="minicart-box--title"><a href="%s">%s</a></h4>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
							}
						?>
					</div>
					<div class="minicart-box--info--bottom">
						<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					</div>

					</div>

				<?php
			}
			$i++;
			if($i==4) {
				break;
			}

		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</div>



	<div class="minicart--subtotal">
		<?php
		/**
		 * Hook: woocommerce_widget_shopping_cart_total.
		 *
		 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
		 */
		do_action( 'woocommerce_widget_shopping_cart_total' );
		?>
	</div>

	<div class="minicart--total-products">
		<span>Hai <?php echo WC()->cart->get_cart_contents_count(); ?> <?php if(WC()->cart->get_cart_contents_count() == 1) {echo 'prodotto';} else { echo ' prodotti';}?> nella tua scatola.</span>
	</div>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<div class="woocommerce-mini-cart__buttons buttons">
		<a href="<?php echo wc_get_cart_url(); ?>" class="btn btn-primary btn-small minicart--go-to">Guarda la tua scatola</a>
	</div>

	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
