<?php

/**
 * Trim zeros in price decimals
 **/
 add_filter( 'woocommerce_price_trim_zeros', '__return_true' );


// Change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single' );
function woocommerce_add_to_cart_button_text_single() {
    return __( 'Acquista', 'woocommerce' );
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
  $getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
  $get_product_cat_ID = $getIDbyNAME->term_id;
   $args = array(
      'hide_empty' => true,
      'fields' => 'slugs',
      'taxonomy' => 'product_cat',
      'parent' => $get_product_cat_ID,
      'hide_empty' => true,
   );
   $categories = get_categories( $args );
   foreach ( $categories as $category_slug ) {
      $term_object = get_term_by( 'slug', $category_slug , 'product_cat' );
      $catID = $term_object->term_id ;
      echo '<div class="shop--list">';
      echo '<div class="shop--list--header">';
      echo '<h2 class="shop--minititle">' . $term_object->name . '</h2>';
			echo '<a href="' . $term_object->slug . '" title="Vedi tutto ' . $term_object->name . '" class="arrow-link">Vedi tutto<span class="icon-arrow-right"></span></a>';
			echo '</div>';
      echo do_shortcode( '[products limit="-1" columns="1" category="' . $category_slug . '"]' );
      echo '</div>';
   }
}

//Cambio testo bollino sconti
add_filter('woocommerce_sale_flash', 'woocommerce_custom_sale_text', 10, 3);
function woocommerce_custom_sale_text($text, $post, $_product)
{
return '<span class="onsale"><span class="small">HEY,</span><span>COSTA</span><span>MENO!</span></span>';
}

//Limita la ricerca ai prodotti
// Only show products in the front-end search results
add_filter('pre_get_posts','lw_search_filter_pages');
function lw_search_filter_pages($query) {
    // Frontend search only
    if ( ! is_admin() && $query->is_search() ) {
        $query->set('post_type', 'product');
        $query->set( 'wc_query', 'product_query' );
    }
    return $query;
}


/**
 * Change number of related products output
 */
function woo_related_products_limit() {
  global $product;

	$args['posts_per_page'] = 6;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args', 20 );
  function jk_related_products_args( $args ) {
	$args['posts_per_page'] = 6; // 4 related products
	$args['columns'] = 2; // arranged in 2 columns
	return $args;
}


//Rimuovi label shipping_method
add_filter( 'woocommerce_cart_shipping_method_full_label', 'bbloomer_remove_shipping_label', 9999, 2 );

function bbloomer_remove_shipping_label( $label, $method ) {
    $new_label = preg_replace( '/^.+:/', '', $label );
    return $new_label;
}


//Minimo ordine 43 euro
/**
 * Set a minimum order amount for checkout
 */
// add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
// add_action( 'woocommerce_before_cart' , 'wc_minimum_order_amount' );

function wc_minimum_order_amount() {
    // $facciamoNoi = 50; //ID prodotto Facciamo noi
    // $facciamoNoiID = WC()->cart->generate_cart_id( $facciamoNoi );
    // $box_in_cart = WC()->cart->find_product_in_cart( $facciamoNoiID );
    //
     $minimum = 43;
    // if ( $box_in_cart ) {
    //     $minimum = 35;
    // }

    $product_id = 50;

   $product_cart_id = WC()->cart->generate_cart_id( $product_id );
   $in_cart = WC()->cart->find_product_in_cart( $product_cart_id );



    if ( WC()->cart->total < $minimum ) {

        if( is_cart() ) {

print_r($product_cart_id );
          if ( $in_cart ) {

             $notice = 'Product ID ' . $product_id . ' is in the Cart!';
             wc_print_notice( $notice, 'notice' );



          }

            wc_print_notice(
                sprintf( 'Beh Your current order total is %s — you must have an order with a minimum of %s to place your order ' ,
                    wc_price( WC()->cart->total ),
                    wc_price( $minimum )
                ), 'error'
            );

        } else {

            wc_add_notice(
                sprintf( 'Ehi Your current order total is %s — you must have an order with a minimum of %s to place your order' ,
                    wc_price( WC()->cart->total ),
                    wc_price( $minimum )
                ), 'error'
            );

        }
    }
}
