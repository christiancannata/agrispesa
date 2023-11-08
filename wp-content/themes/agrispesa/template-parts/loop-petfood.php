<?php
$product = wc_get_product( get_the_ID() );
$thumb_id = get_post_thumbnail_id();
$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
$thumb_url = $thumb_url_array[0];
// unità di misura personalizzata
$product_data = $product->get_meta('_woo_uom_input');

$title = get_the_title();
/*$title_without_weight = preg_replace(
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
*/
?>
<article class="petfood-box" style="background-image:url(<?php echo $thumb_url;?>);">
  <?php if (str_contains(get_the_title(), 'Puppy')) {
    echo '<span class="puppies"><span class="icon-heart"></span>Cuccioli</span>';

} ?>
  <div class="petfood-box--text">
    <div class="petfood-box--text--top">
      <h2 class="petfood-box--title"><a href="<?php the_permalink(); ?>" title="<?php echo strip_tags($title); ?>"><?php echo $title; ?></a></h2>
      <div class="petfood-box--price--flex">
        <?php // echo '<span class="product-info--quantity">' . $weigth_nav . '</span>'; ?>

        <div class="petfood-box--price">
          <?php echo $product->get_price_html(); ?>
        </div>
      </div>

      <?php echo do_shortcode('[add_to_cart id="'.get_the_ID().'" show_price="false" class="btn-fake" quantity="1" style="border:none;"]');?>
    </div>
  </div>
</article>
