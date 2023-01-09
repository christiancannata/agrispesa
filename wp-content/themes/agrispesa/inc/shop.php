<?php

/**
 * Trim zeros in price decimals
 **/
 add_filter( 'woocommerce_price_trim_zeros', '__return_true' );


// Change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single' );
function woocommerce_add_to_cart_button_text_single() {
    return __( 'Aggiungi alla box', 'woocommerce' );
}

//Prezzo prima del pulsante add to cart
add_action( 'woocommerce_before_add_to_cart_button', 'misha_before_add_to_cart_btn' );
function misha_before_add_to_cart_btn(){
  global $product;
  echo '<div class="btn-price">'.$product->get_price_html().'</div>';
}


//// Layout pagina negozio vuoto
add_action( 'woocommerce_no_products_found', 'shop_page_empty_layout' );

function shop_page_empty_layout() {
  $getIDbyNAME = get_term_by('name', 'shop', 'product_cat');
  $get_product_cat_ID = $getIDbyNAME->term_id;
   $args = array(
      'hide_empty' => true,
      'fields' => 'slugs',
      'hierarchical' => 1,
      'taxonomy' => 'product_cat',
      'parent' => $get_product_cat_ID,
      'hide_empty' => 0,
   );
   $categories = get_categories( $args );
   foreach ( $categories as $category_slug ) {
      $term_object = get_term_by( 'slug', $category_slug , 'product_cat' );
      $catID = $term_object->term_id ;
      echo '<div class="shop--list">';
      echo '<h2 class="shop--minititle">' . $term_object->name . '</h2>';
      echo do_shortcode( '[products limit="-1" columns="4" category="' . $category_slug . '"]' );
      echo '</div>';
   }
}
