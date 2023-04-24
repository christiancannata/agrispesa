<section class="all-categories">
  <?php
    $orderby = 'ID';
    $order = 'asc';
    $hide_empty = false;

    $getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
    $get_product_cat_ID = $getIDbyNAME->term_id;

    $getSpeciali = get_term_by('name', 'speciali', 'product_cat');
    $specialiID = $getSpeciali->term_id;

    $cat_args = array(
      'orderby' => 'meta_value',
      'meta_key' => 'categories_order_agr',
      'order' => 'ASC',
      'hide_empty' => false,
        'parent' => $get_product_cat_ID,
        'exclude' => $specialiID
    );


$product_categories = get_terms( 'product_cat', $cat_args );

$special_category = get_field('agr_special_category', 'option');
$special_icon = get_field('agr_special_icon', 'option');
  $special_name = null;
  $special_slug = null;
if($special_category){
	$link = get_term_link( $special_category, 'product_cat' );
	$special = get_term_by('term_id', $special_category, 'product_cat');
	if($special){
		$special_name = $special->name;
		$special_slug = $special->slug;
	}

}


if( !empty($product_categories) ){
  $categoriesNumber = count($product_categories);

  if($special_category) {
    $allCategoriesNr = $categoriesNumber + 1;
  } else {
    $allCategoriesNr = $categoriesNumber;
  }
  $calcWidth = 100 / $allCategoriesNr;


    echo '<ul class="all-categories--list">';

    if($special_category) {
      echo '<li style="min-width:'.$calcWidth.'%;">';
      echo '<a href="'.$link.'" title="'.$special_name.'">';
      if($special_icon == 'heart') {
        echo get_template_part( 'global-elements/icon', 'heart' );
      } else {
        echo get_template_part( 'global-elements/icon', 'star' );
      }
      echo $special_name;
      echo '</a>';
      echo '</li>';
    }

    foreach ($product_categories as $key => $category) {
        echo '<li style="min-width:'.$calcWidth.'%;">';
        echo '<a href="'.get_term_link($category).'" title="'.$category->name.'">';
        echo get_template_part( 'global-elements/icon', $category->slug );
        echo $category->name;
        echo '</a>';
        echo '</li>';
    }

    echo '</ul>';
}


?>

</section>
