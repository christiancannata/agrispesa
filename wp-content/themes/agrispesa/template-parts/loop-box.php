<?php
$product = wc_get_product( get_the_ID() );
$thumb_id = get_post_thumbnail_id();
$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
$thumb_url = $thumb_url_array[0];
?>
<article class="product-box">
  <a href="<?php the_permalink(); ?>" class="product-box--link" title="<?php echo the_title(); ?>">
    <?php if($thumb_id):?>
      <img src="<?php the_post_thumbnail_url(); ?>" class="product-box--thumb" alt="<?php echo esc_html( $title ); ?>" />
    <?php else: ?>
      <img src="https://agrispesa.it/wp-content/uploads/2023/02/default.png" class="product-box--thumb" alt="<?php echo esc_html( $title ); ?>" />
    <?php endif;?>
  </a>
  <div class="product-box--text">
    <div class="product-box--text--top">
      <h2 class="product-box--title"><?php echo the_title(); ?></h2>
      <p class="product-box--description"><?php echo the_excerpt(); ?></p>
      <div class="product-box--price">
        <?php echo $product->get_price_html(); ?>
      </div>
      <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-small product-box--button" title="<?php echo the_title(); ?>">Abbonati alla spesa</a>
        <div class="categories-list">

        </div>
    </div>
  </div>
</article>
