<section class="all-categories">
  <?php
  $orderby = 'ID';
    $order = 'asc';
    $hide_empty = false;

    $getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
    $get_product_cat_ID = $getIDbyNAME->term_id;
    $cat_args = array(
        'orderby'    => $orderby,
        'order'      => $order,
        'hide_empty' => $hide_empty,
        'parent' => $get_product_cat_ID,
    );

$product_categories = get_terms( 'product_cat', $cat_args );

if( !empty($product_categories) ){
    echo '

<ul class="all-categories--list">';
    foreach ($product_categories as $key => $category) {
        echo '

<li>';
        echo '<a href="'.get_term_link($category).'" title="'.$category->name.'">';
        echo get_template_part( 'global-elements/icon', $category->slug );
        echo $category->name;
        echo '</a>';
        echo '</li>';
    }
    echo '</ul>


';
} ?>
</section>
