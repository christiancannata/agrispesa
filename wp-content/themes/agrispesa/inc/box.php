<?php

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
