<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked wc_empty_cart_message - 10
 */
do_action( 'woocommerce_cart_is_empty' );

if ( wc_get_page_id( 'shop' ) > 0 ) :

?>
<div class="emptycart">
	<div class="emptycart--intro">
		<div class="emptycart--top">

			<h1 class="emptycart--title">Ops, la tua scatola <br class="only-mobile" />è vuota.</h1>

			<p class="emptycart--subtitle"><a href="/negozio">Vai al negozio</a> per fare la spesa.</p>
	</div>
		<!--
	<div class="emptycart--loop">
			<?php
			$args = array(
        'limit'     => '8',
        'orderby'   => array( 'meta_value_num' => 'DESC', 'title' => 'ASC' ),
				'product_cat' => 'Negozio',
        'meta_key'  => 'total_sales',
    );

    $query    = new WC_Product_Query( $args );
    $products = $query->get_products();
    if ( $products ): ?>
        <?php foreach ( $products as $product ):
					$product = wc_get_product( $product->get_id() );
					//print_r($product);
					$thumb_id = get_post_thumbnail_id( $product->get_id() );
					$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
					$thumb_url = $thumb_url_array[0];
					// unità di misura personalizzata
					$product_data = $product->get_meta('_woo_uom_input');
					?>
						<article class="product-box">
						  <a href="<?php the_permalink($product->get_id()); ?>" class="product-box--link" title="<?php echo $product->get_name(); ?>">
						    <?php if($thumb_id):?>
						      <img src="<?php echo $thumb_url; ?>" class="product-box--thumb" alt="<?php echo esc_html( $product->get_name() ); ?>" />
						    <?php else: ?>
						      <img src="https://agrispesa.it/wp-content/uploads/2023/02/default.png" class="product-box--thumb" alt="<?php echo esc_html( $product->get_name() ); ?>" />
						    <?php endif;?>
						  </a>
						  <div class="product-box--text">
						    <div class="product-box--text--top">
						      <h2 class="product-box--title"><a href="<?php the_permalink($product->get_id()); ?>" title="<?php echo $product->get_name(); ?>"><?php echo $product->get_name(); ?></a></h2>
						      <div class="product-box--price--flex">

										<?php if ( $product->has_weight() ) {
						        	if($product_data && $product_data != 'gr') {
						        		echo '<span class="product-info--quantity">' . $product->get_weight() . ' '.$product_data.'</span>';
						        	} else {
						            if($product->get_weight() == 1000) {
						        			echo '<span class="product-info--quantity">1 kg</span>';
						        		} else {
						        			echo '<span class="product-info--quantity">' . $product->get_weight() . ' gr</span>';
						        		}
						        	}
						        } ?>
						        <div class="product-box--price">
						          <?php echo $product->get_price_html(); ?>
						        </div>
						      </div>

						      <?php echo do_shortcode('[add_to_cart id="'.$product->get_id().'" show_price="false" class="btn-fake" quantity="1" style="border:none;"]');?>
						    </div>
						  </div>
						</article>

        <?php endforeach; ?>
    <?php endif; ?>
		</div>
 -->
	</div>
	<div class="emptycart--image">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/images/elements/empty-box.svg" />
	</div>
</div>

<?php endif; ?>
