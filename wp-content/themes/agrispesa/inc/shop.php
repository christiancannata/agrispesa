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
add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
add_action( 'woocommerce_before_cart' , 'wc_minimum_order_amount' );

function wc_minimum_order_amount() {

  $minimum = 43;
  $category = 'box';

  // Loop through cart items
  foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
      // Product id
      $product_id = $cart_item['product_id'];
      // Has category box
      if ( has_term( $category, 'product_cat', $product_id ) ) {
          $minimum = 26;
      }
  }

    if ( WC()->cart->total < $minimum ) {
      $cartTotal = WC()->cart->total;
      $addPrice = $minimum - $cartTotal;

        if( is_cart() ) {

            echo '<div class="minimum-amount-advice"><div class="checkout--preview--items mg-t"><span class="is-title"><span class="icon-ics is-icon red"></span>Non hai abbastanza prodotti</span><span class="is-description">Per preparare la tua scatola, abbiamo bisogno di un ordine di almeno ' .wc_price($minimum) .'. Scegli altri prodotti!<br/>Ti mancano ' .wc_price($addPrice) .'.</span></div></div>';
            // Remove proceed to checkout button
            remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );

        } else {

          echo '<div class="minimum-amount-advice"><div class="checkout--preview--items mg-t"><span class="is-title"><span class="icon-ics is-icon red"></span>Non hai abbastanza prodotti</span><span class="is-description">Per preparare la tua scatola, abbiamo bisogno di un ordine di almeno ' .wc_price($minimum) .'. Scegli altri prodotti!<br/>Ti mancano ' .wc_price($addPrice) .'.</span></div></div>';
          // Remove proceed to checkout button
          remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );

        }
    }
}

//Cambia h2 a lista prodotti e aggiungi peso
remove_action( 'woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title', 10 );
add_action('woocommerce_shop_loop_item_title', 'soChangeProductsTitle', 10 );
function soChangeProductsTitle() {
  global $product;
  $product_data = $product->get_meta('_woo_uom_input');

  if ( $product->has_weight() ) {
  	if($product_data) {
      echo '<div class="product-loop-title-meta"><h6 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h6>';
  		echo '<span class="product-info--quantity">' . $product->get_weight() . ' '.$product_data.'</span></div>';
  	} else {
      echo '<div class="product-loop-title-meta"><h6 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h6>';
  		echo '<span class="product-info--quantity">' . $product->get_weight() . ' g</span></div>';
  	}
  } else {
    echo '<h6 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h6>';
  }
}


//Free shipping label
//add_filter( 'woocommerce_cart_shipping_method_full_label', 'add_free_shipping_label', 10, 2 );
function add_free_shipping_label( $label, $method ) {
    if ( $method->cost == 0 ) {
        $label = 'Spedizione gratuita'; //not quite elegant hard coded string
    }
    return $label;
}

//coupon con spedizione gratuita
//add_filter( 'woocommerce_package_rates', 'coupon_free_shipping_customization', 20, 2 );
function coupon_free_shipping_customization( $rates, $package ) {
    $has_free_shipping = false;

    $applied_coupons = WC()->cart->get_applied_coupons();
    foreach( $applied_coupons as $coupon_code ){
        $coupon = new WC_Coupon($coupon_code);
        if($coupon->get_free_shipping()){
            $has_free_shipping = true;
            break;
        }
    }

    foreach( $rates as $rate_key => $rate ){
        if( $has_free_shipping ){
            // For "free shipping" method (enabled), remove it
            if( $rate->method_id == 'free_shipping'){
                unset($rates[$rate_key]);
            }
            // AIUTO CHRISTIAN: un altro if se è applicatanuna gift card


            // For other shipping methods
            else {
                // Append rate label titles (free)
                $rates[$rate_key]->label .= ' ' . __('(free)', 'woocommerce');

                // Set rate cost
                $rates[$rate_key]->cost = 0;

                // Set taxes rate cost (if enabled)
                $taxes = array();
                foreach ($rates[$rate_key]->taxes as $key => $tax){
                    if( $rates[$rate_key]->taxes[$key] > 0 )
                        $taxes[$key] = 0;
                }
                $rates[$rate_key]->taxes = $taxes;
            }
        }
    }
    return $rates;
}

//Sposta bottoni di pagamento prima del bottone di default
// add_action('init', 'change_payments_buttons_position', 11);
// function change_payments_buttons_position() {
// 	remove_action('woocommerce_proceed_to_checkout', array(WC_Stripe_Payment_Request::instance(), 'display_payment_request_button_html'), 1);
// 	remove_action('woocommerce_proceed_to_checkout', array(WC_Stripe_Payment_Request::instance(), 'display_payment_request_button_separator_html'), 2);
//
// 	add_action('woocommerce_review_order_before_submit', array(WC_Stripe_Payment_Request::instance(), 'display_payment_request_button_html'), 2);
// 	add_action('woocommerce_review_order_before_submit', array(WC_Stripe_Payment_Request::instance(), 'display_payment_request_button_separator_html'), 1);
//
// }

//sposta coupon nel checkout
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
add_action('woocommerce_review_order_before_payment', 'woocommerce_checkout_coupon_form');


//sposta gift card nel checkout
if ( ! function_exists('ywgc_gift_card_code_form_checkout_hook' ) ){
  function ywgc_gift_card_code_form_checkout_hook( $hook ){
    $hook = 'woocommerce_review_order_before_payment';
    return $hook;
  }
}
add_filter( 'ywgc_gift_card_code_form_checkout_hook', 'ywgc_gift_card_code_form_checkout_hook', 10, 1 );
