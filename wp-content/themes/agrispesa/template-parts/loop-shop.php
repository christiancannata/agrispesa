<?php
$product = wc_get_product( get_the_ID() );
$thumb_id = get_post_thumbnail_id();
$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
$thumb_url = $thumb_url_array[0];
// unità di misura personalizzata
$product_data = $product->get_meta('_woo_uom_input');
?>
<article class="product-box">
  <a href="<?php the_permalink(); ?>" class="product-box--link" title="<?php echo the_title(); ?>">
    <?php if($thumb_id):?>
      <img src="<?php the_post_thumbnail_url(); ?>" class="product-box--thumb" alt="<?php echo esc_html( $title ); ?>" />
    <?php else: ?>
      <img src="https://staging.agrispesa.it/wp-content/uploads/2023/02/default.png" class="product-box--thumb" alt="<?php echo esc_html( $title ); ?>" />
    <?php endif;?>
  </a>
  <div class="product-box--text">
    <div class="product-box--text--top">
      <h2 class="product-box--title"><a href="<?php the_permalink(); ?>" title="<?php echo the_title(); ?>"><?php echo the_title(); ?></a></h2>
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

      <?php echo do_shortcode('[add_to_cart id="'.get_the_ID().'" show_price="false" class="btn-fake" quantity="1" style="border:none;"]');?>
    </div>
  </div>
</article>
