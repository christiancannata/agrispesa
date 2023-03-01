<?php
/**
 * YITH WooCommerce Recover Abandoned Cart Content metabox template
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */

if ( empty( $cart_content['cart'] ) ) {
	return;
}

$tax_display_cart = get_option( 'woocommerce_tax_display_cart' );

$tax_total = 0;
$total     = 0;
?>
<table class="shop_table cart" id="yith-ywrac-table-list" cellspacing="0">
	<thead>
	<tr>
		<th class="product-thumbnail"><?php esc_html_e( 'Thumbnail', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
		<th class="product-name"><?php esc_html_e( 'Product', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
		<th class="product-single"><?php esc_html_e( 'Product Price', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
		<th class="product-quantity"><?php esc_html_e( 'Quantity', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
		<th class="product-subtotal"><?php esc_html_e( 'Total', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ( $cart_content['cart'] as $cart_item_key => $cart_item ) :

		$product_id = ( isset( $cart_item['variation_id'] ) && ! empty( $cart_item['variation_id'] ) ) ? $cart_item['variation_id'] : $cart_item['product_id'];
		$_product   = wc_get_product( $product_id );

		if ( ! $_product instanceof WC_Product ) {
			continue;
		}

		$qty = $cart_item['quantity'];

		$price = $cart_item['line_subtotal'] / $qty;
		$tax   = $cart_item['line_subtotal_tax'] / $qty;

		$total     += $cart_item['line_subtotal'];
		$tax_total += $cart_item['line_subtotal_tax'];

		if ( 'incl' == $tax_display_cart ) {
			$price         += $tax;
			$total         += $cart_item['line_subtotal_tax'];
			$tax_incl_html  = '<small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
			$price_subtotal = wc_price( $price, array( 'currency' => $currency ) ) . $tax_incl_html;
			$price_total    = wc_price( $price * $qty, array( 'currency' => $currency ) ) . $tax_incl_html;
		} else {
			$price_subtotal = wc_price(
				$price,
				array(
					'currency'     => $currency,
					'ex_tax_label' => true,
				)
			);
			$price_total    = wc_price(
				$price * $qty,
				array(
					'currency'     => $currency,
					'ex_tax_label' => true,
				)
			);
		}
		?>
		<tr class="cart_item">
			<td class="product-thumbnail">
				<?php
				$thumbnail = $_product->get_image();

				if ( ! $_product->is_visible() ) {
					echo $thumbnail;
				} else {
					printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
				}
				?>
			</td>

			<td class="product-name">
				<a href="<?php echo $_product->get_permalink(); ?>"><?php echo $_product->get_title(); ?></a>
				<?php
				// Meta data
				$item_data = array();

				// Variation data
				if ( isset( $cart_item['variation_id'] ) && isset( $cart_item['variation'] ) && ! empty( $cart_item['variation_id'] ) && is_array( $cart_item['variation'] ) ) {
					foreach ( $cart_item['variation'] as $name => $value ) {
						$label = '';
						if ( '' === $value ) {
							continue;
						}
						$taxonomy = wc_attribute_taxonomy_name( str_replace( 'pa_', '', urldecode( $name ) ) );

						// If this is a term slug, get the term's nice name
						if ( taxonomy_exists( $taxonomy ) ) {
							$term = get_term_by( 'slug', $value, $taxonomy );
							if ( ! is_wp_error( $term ) && $term && $term->name ) {
								$value = $term->name;
							}
							$label = wc_attribute_label( $taxonomy );

						} else {

							if ( strpos( $name, 'attribute_' ) !== false ) {
								$custom_att = str_replace( 'attribute_', '', $name );

								if ( $custom_att != '' ) {
									$label = wc_attribute_label( $custom_att );
								} else {
									$label = $name;
								}
							}
						}

						$item_data[] = array(
							'key'   => $label,
							'value' => $value,
						);
					}
				}

				// Output flat or in list format
				if ( sizeof( $item_data ) > 0 ) {
					foreach ( $item_data as $data ) {
						echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "\n";
					}
				}
				?>
			</td>
			<td class="product-price">
				<?php
				$cart_item['data'] = $_product;
				echo apply_filters( 'woocommerce_cart_item_price', $price_subtotal, $cart_item, $cart_item_key );
				?>
			</td>

			<td class="product-quantity">
				<?php echo $cart_item['quantity']; ?>
			</td>

			<td class="product-subtotal">
				<?php
				echo apply_filters( 'woocommerce_cart_item_subtotal', $price_total, $cart_item, $cart_item_key );
				?>
			</td>
		</tr>

	<?php endforeach ?>
	<?php if ( $tax_display_cart == 'excl' ) : ?>
	<tr>
		<td scope="col" colspan="4" style="text-align: right">
			<strong><?php esc_html_e( 'Cart Subtotal', 'yith-woocommerce-recover-abandoned-cart' ); ?></strong></td>

		<?php
		$product_subtotal = wc_price( $total, array( 'currency' => $currency ) ) . ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
		?>
		<td scope="col"><?php echo $product_subtotal; ?></td>
		<?php endif; ?>
	</tr>


	<?php
	if ( $tax_total ) :
		if ( $tax_display_cart == 'excl' ) :
			?>
			<tr>
				<td scope="col" colspan="4" style="text-align: right"><strong><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></strong></td>
				<td scope="col"><?php echo wc_price( $tax_total, array( 'currency' => $currency ) ); ?></td>
			</tr>
		<?php endif; ?>
	<?php endif; ?>
	<?php
	if ( $total ) :
		if ( $tax_display_cart == 'excl' ) {
			$total += $tax_total;
		}
		?>
		<tr>
			<td scope="col" colspan="4" style="text-align: right"><strong><?php esc_html_e( 'Cart Total', 'yith-woocommerce-recover-abandoned-cart' ); ?></strong></td>
			<td scope="col"><?php echo wc_price( $total, array( 'currency' => $currency ) ); ?></td>
		</tr>
	<?php endif ?>
	</tbody>
</table>
