<?php
$product = wc_get_product( get_the_ID() );
$thumb_id = get_post_thumbnail_id();
$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
$thumb_url = $thumb_url_array[0];
?>
<article class="product-box">
  <a href="<?php the_permalink(); ?>" class="product-box--link" title="<?php echo the_title(); ?>">
    <img src="<?php the_post_thumbnail_url(); ?>" class="product-box--thumb" alt="<?php echo the_title(); ?>" />
  </a>
  <div class="product-box--text">
    <div class="product-box--text--top">
      <h2 class="product-box--title"><?php echo the_title(); ?></h2>
      <div class="product-box--price--flex">
        <?php if ( $product->has_weight() ) {
        	echo '<p class="product-box--description product-info--quantity">' .  $product->get_weight() . ' kg — </p>';
        } ?>
        <div class="product-box--price">
          <?php echo $product->get_price_html(); ?>
        </div>
      </div>
      <p class="product-box--excerpt">
        <?php echo the_excerpt(); ?>
      </p>

      <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-small product-box--button" title="<?php echo the_title(); ?>">Scopri di più</a>
    </div>
  </div>
</article>
