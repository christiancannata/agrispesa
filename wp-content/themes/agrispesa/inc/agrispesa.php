<?php

//Aggiungi funzionalità Woocommerce
add_theme_support( 'woocommerce' );

/**
 * Trim zeros in price decimals
 **/
 add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

///Prezzo bundle
 function wc_ninja_custom_variable_price( $price, $product ) {
    // Main Price
    $prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
    $price = $prices[0] !== $prices[1] ? sprintf( __( 'A partire da %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

    // Sale Price
    $prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
    sort( $prices );
    $saleprice = $prices[0] !== $prices[1] ? sprintf( __( '', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

    if ( $price !== $saleprice ) {
        $price = ' <ins class="highlight"> '. $price.' </ins> <del class="strike"> '.$saleprice .' </del> ';
    }

    return $price;
}
add_filter( 'woocommerce_variable_sale_price_html', 'wc_ninja_custom_variable_price', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_ninja_custom_variable_price', 10, 2 );


/**
 * Exclude products from a particular category on the shop page
 */
function custom_pre_get_posts_query( $q ) {

    $tax_query = (array) $q->get( 'tax_query' );

    $tax_query[] = array(
           'taxonomy' => 'product_cat',
           'field' => 'slug',
           'terms' => array( 'box' ), // Don't display products in the box on the shop page.
           'operator' => 'NOT IN'
    );


    $q->set( 'tax_query', $tax_query );

}
add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );


// Change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single' );
function woocommerce_add_to_cart_button_text_single() {
    return __( 'Aggiungi alla box', 'woocommerce' );
}

// Change add to cart text on product archives page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives' );
function woocommerce_add_to_cart_button_text_archives() {
    return __( 'Aggiungi alla box', 'woocommerce' );
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


//Trasforma select variabili in bottoni
function variation_radio_buttons($html, $args) {
      $args = wp_parse_args(apply_filters('woocommerce_dropdown_variation_attribute_options_args', $args), array(
        'options'          => false,
        'attribute'        => false,
        'product'          => false,
        'selected'         => false,
        'name'             => '',
        'id'               => '',
        'class'            => '',
        'show_option_none' => __('Choose an option', 'woocommerce'),
      ));

      if(false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product) {
        $selected_key     = 'attribute_'.sanitize_title($args['attribute']);
        $args['selected'] = isset($_REQUEST[$selected_key]) ? wc_clean(wp_unslash($_REQUEST[$selected_key])) : $args['product']->get_variation_default_attribute($args['attribute']);
      }

      $options               = $args['options'];
      $product               = $args['product'];
      $attribute             = $args['attribute'];
      $name                  = $args['name'] ? $args['name'] : 'attribute_'.sanitize_title($attribute);
      $id                    = $args['id'] ? $args['id'] : sanitize_title($attribute);
      $class                 = $args['class'];
      $show_option_none      = (bool)$args['show_option_none'];
      $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __('Choose an option', 'woocommerce');

      if(empty($options) && !empty($product) && !empty($attribute)) {
        $attributes = $product->get_variation_attributes();
        $options    = $attributes[$attribute];
      }

      $radios = '<div class="variation-radios '.$name.'">';

      if(!empty($options)) {
        if($product && taxonomy_exists($attribute)) {
          $terms = wc_get_product_terms($product->get_id(), $attribute, array(
            'fields' => 'all',
          ));

          foreach($terms as $term) {
            if(in_array($term->slug, $options, true)) {
              $id = $name.'-'.$term->slug;
              $radios .= '<label for="'.esc_attr($id).'"><input type="radio" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr($term->slug).'" '.checked(sanitize_title($args['selected']), $term->slug, false).'><div class="box '.esc_attr($term->slug).'"><span>'.esc_html(apply_filters('woocommerce_variation_option_name', $term->name)).'</span></div></label>';
            }
          }
        } else {
          foreach($options as $option) {
            $id = $name.'-'.$option;
            $checked    = sanitize_title($args['selected']) === $args['selected'] ? checked($args['selected'], sanitize_title($option), false) : checked($args['selected'], $option, false);
            $radios    .= '<label for="'.esc_attr($id).'"><input type="radio" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr($option).'" id="'.sanitize_title($option).'" '.$checked.'><div class="box '.esc_attr($option).'"><span>'.esc_html(apply_filters('woocommerce_variation_option_name', $option)).'</span></div></label>';
          }
        }
      }

      $radios .= '</div>';

      return $html.$radios;
    }
    add_filter('woocommerce_dropdown_variation_attribute_options_html', 'variation_radio_buttons', 20, 2);

    function variation_check($active, $variation) {
      if(!$variation->is_in_stock() && !$variation->backorders_allowed()) {
        return false;
      }
      return $active;
    }
    add_filter('woocommerce_variation_is_active', 'variation_check', 10, 2);


// Lunghezza Riassunto
function my_excerpt_length($length){
return 15;
}
add_filter('excerpt_length', 'my_excerpt_length');


// Titoli pagine di categoria
add_filter( 'get_the_archive_title', function ($title) {
    if ( is_category() ) {
            $title = single_cat_title( '', false );
        } elseif ( is_tag() ) {
            $title = single_tag_title( '', false );
        } elseif ( is_author() ) {
            $title = '<span class="vcard">' . get_the_author() . '</span>' ;
        } elseif ( is_tax() ) { //for custom post types
            $title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
        }
    return $title;
});


// Mostra 9 risultati in archivio
function archive_order_classes( $query ) {
    if ( !is_admin() && $query->is_archive() && $query->is_main_query() ) {
        $query->set( 'posts_per_page', '9' );
    }
    if (is_search()) {
      if( $_SESSION['havesearch'] == true ) {
    		$query->set('posts_per_page', '6');
    	} else {
        $query->set('posts_per_page', '3');
      }

    }
}
add_action( 'pre_get_posts', 'archive_order_classes' );


//Custom menu
function footer_menu() {
	register_nav_menus(
		array(
			'footer_menu_one' => __( 'Footer Menu 1' ),
			'footer_menu_two' => __( 'Footer Menu 2' ),
			'footer_menu_three' => __( 'Footer Menu 3' ),
			'user_menu' => __( 'User menu' )
		)
	);
}

add_action( 'init', 'footer_menu' );


/**
 * Remove product page tabs
 */
add_filter( 'woocommerce_product_tabs', 'my_remove_all_product_tabs', 98 );

function my_remove_all_product_tabs( $tabs ) {
  unset( $tabs['description'] );        // Remove the description tab
  unset( $tabs['reviews'] );       // Remove the reviews tab
  unset( $tabs['additional_information'] );    // Remove the additional information tab
  return $tabs;
}


//Remove downloads from account
function custom_my_account_menu_items( $items ) {
    unset($items['downloads']);
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'custom_my_account_menu_items' );
