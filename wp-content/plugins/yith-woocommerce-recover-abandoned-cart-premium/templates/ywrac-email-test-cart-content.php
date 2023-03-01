<?php
/**
 * YITH WooCommerce Recover Abandoned Cart Content metabox template
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author  YITH
 *
 * @var bool   $has_translation
 * @var string $lang
 * @var array  $cart_content
 */

$icl_t               = function_exists( 'icl_t' );
$item_label          = ( $icl_t ) ? icl_t( 'yith-woocommerce-recover-abandoned-cart', 'ywrac_cart_template_thumbnail', 'Item', $has_translation, false, $lang ) : __( 'Item', 'yith-woocommerce-recover-abandoned-cart' );
$price_label         = ( $icl_t ) ? icl_t( 'yith-woocommerce-recover-abandoned-cart', 'ywrac_cart_template_product', 'Product', $has_translation, false, $lang ) : __( 'Price', 'yith-woocommerce-recover-abandoned-cart' );
$cart_subtotal_label = ( $icl_t ) ? icl_t( 'yith-woocommerce-recover-abandoned-cart', 'ywrac_cart_template_cart_subtotal', 'Cart subtotal', $has_translation, false, $lang ) : __( 'Cart subtotal', 'yith-woocommerce-recover-abandoned-cart' );


// Get random products.
$args     = array(
	'status'         => 'publish',
	'posts_per_page' => 2,
	'orderby'        => 'rand',
);
$products = wc_get_products( $args );

$product_price1 = 15;
$qty1           = 3;
$product_price2 = 10;
$qty2           = 2;
$subtotal       = 0;
?>

<table class="shop_table cart" id="yith-ywrac-table-list" cellspacing="0" style="width: 100%;">
	<thead>
	<tr>
		<th class="product-thumbnail"><?php echo esc_html( $item_label ); ?></th>
		<th class="product-name" style="width: 50%;"></th>
		<th class="product-subtotal"><?php echo esc_html( $price_label ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if ( $products ) : ?>
		<?php foreach ( $products as $product ) : ?>
			<tr class="cart_item">
				<td class="product-thumbnail">
					<?php $src = ( $product->get_image_id() ) ? current( wp_get_attachment_image_src( $product->get_image_id(), 'shop_thumbnail' ) ) : wc_placeholder_img_src(); ?>
					<a style="width:100px;height:auto; display: inline-block;" class="product-image" href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<img style=" width: 100%; height: auto;" src="<?php echo esc_url( $src ); ?>"/>
					</a>
				</td>
				<?php
				$qty              = random_int( 1, 10 );
				$product_subtotal = $product->get_price() * $qty;
				$subtotal        += $product_subtotal;
				?>
				<td class="product-name"><?php echo wp_kses_post( $product->get_name() ); ?> <small>x<?php echo esc_html( $qty ); ?></small></td>
				<td class="product-subtotal"><?php echo wp_kses_post( wc_price( $product_subtotal ) ); ?></td>
			</tr>
			<?php endforeach; ?>
	<?php else : ?>
		<?php
		$product_price1 = 15;
		$qty1           = 3;
		$product_price2 = 10;
		$qty2           = 2;
		$src            = wc_placeholder_img_src();
		?>
		<tr class="cart_item">
			<td class="product-thumbnail">
				<img style="max-width: none; width: 100px; height: auto;" src="<?php echo esc_url( $src ); ?>"/>
			</td>
			<td class="product-name">Product 1 <small>x<?php echo $qty1; ?></small></td>
			<td class="product-subtotal">
				<?php
				$product_subtotal1 = (float) $product_price1 * $qty1;
				echo wp_kses_post( wc_price( $product_subtotal1 ) );
				?>
			</td>
		</tr>
		<tr class="cart_item">
			<td class="product-thumbnail">
				<img style="max-width: none; width: 100px; height: auto;" src="<?php echo esc_url( $src ); ?>"/>
			</td>
			<td class="product-name">Product 2 <small>x<?php echo $qty2; ?></small></td>
			<td class="product-subtotal">
				<?php
				$product_subtotal2 = (float) $product_price2 * $qty2;
				echo wp_kses_post( wc_price( $product_subtotal2 ) );
				?>
			</td>
		</tr>
		<?php
		$subtotal = $product_subtotal1 + $product_subtotal2;
		?>
	<?php endif; ?>
	<tr>
		<td scope="col" colspan="2"><strong><?php echo esc_html( $cart_subtotal_label ); ?></strong></td>
		<td scope="col"><strong><?php echo wp_kses_post( wc_price( $subtotal ) ); ?></strong></td>
	</tr>
	</tbody>
</table>

<style type="text/css">
	#yith-ywrac-table-list tr th {
		border-bottom: 2px solid #eee;
	}
	#yith-ywrac-table-list tr td {
		border-bottom: 1px solid #eee;
	}
</style>
