<?php
$product = wc_get_product( get_the_ID() );
$thumb_id = get_post_thumbnail_id();
$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
$thumb_url = $thumb_url_array[0];
// unità di misura personalizzata
$product_data = $product->get_meta('_woo_uom_input');

$title = get_the_title();
$title_without_weight = preg_replace(
	 array('/(kg\s\d+|ml\s\d+|cl\s\d+|g\s\d+|pz\s\d+|l\s\d+)/'),
	 array(''),
	 $title
);
$the_weight_array = getNumbersFromString($title);
$i = 1;
$weigth_nav = "";
if(isset($the_weight_array) && !empty($the_weight_array)) {
	foreach ($the_weight_array as $the_weight) {
		if(isset($the_weight[0])) {
			$weigth_nav = $the_weight[0];
		} else {
			$weigth_nav = "";
		}

		if($i === 1) {
			break;
		}
	}
}
die("nnn");
?>
<article class="product-box">
  <a href="<?php the_permalink(); ?>" class="product-box--link" title="<?php echo the_title(); ?>">
    <?php if( has_term( 'Petfood', 'product_cat' ) ) {
      echo '<div class="pawer-logo-badge">';
    	echo get_template_part('global-elements/logo', 'pawer');
      echo '</div>';
      } ?>
    <?php if($thumb_id):?>
      <img src="<?php the_post_thumbnail_url(); ?>" class="product-box--thumb" alt="<?php echo esc_html( $title_without_weight ); ?>" />
    <?php else: ?>
      <img src="https://agrispesa.it/wp-content/uploads/2023/02/default.png" class="product-box--thumb" alt="<?php echo esc_html( $title_without_weight ); ?>" />
    <?php endif;?>
  </a>
  <div class="product-box--text">
    <div class="product-box--text--top">
      <h2 class="product-box--title"><a href="<?php the_permalink(); ?>" title="<?php echo $title_without_weight; ?>"><?php echo $title_without_weight; ?></a></h2>
      <div class="product-box--price--flex">
        <?php echo '<span class="product-info--quantity">' . $weigth_nav . '</span>'; ?>

        <div class="product-box--price">
          <?php echo $product->get_price_html(); ?>
        </div>
      </div>

      <?php echo do_shortcode('[add_to_cart id="'.get_the_ID().'" show_price="false" class="btn-fake" quantity="1" style="border:none;"]');?>
    </div>
  </div>
</article>
