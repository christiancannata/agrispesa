<?php

// Change add to cart text on single product page
add_filter('woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single');
function woocommerce_add_to_cart_button_text_single() {
	return __('Acquista', 'woocommerce');
}

//Prezzo prima del pulsante add to cart
add_action('woocommerce_before_add_to_cart_button', 'misha_before_add_to_cart_btn');
function misha_before_add_to_cart_btn(){
	global $product;
	echo '<div class="btn-price">' . $product->get_price_html() . '</div>';
}


//// Layout pagina negozio vuoto
add_action('woocommerce_no_products_found', 'shop_page_empty_layout');

function shop_page_empty_layout(){
	$getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
	$get_product_cat_ID = $getIDbyNAME->term_id;
	$getSpeciali = get_term_by('name', 'speciali', 'product_cat');
	$specialiID = $getSpeciali->term_id;
	$args = array(
		'hide_empty' => true,
		'fields' => 'slugs',
		'taxonomy' => 'product_cat',
		'parent' => $get_product_cat_ID,
		'hide_empty' => true,
	);
	$categories = get_categories($args);
		echo '<div class="negozio--flex">';
		echo '<div class="products-list-agr">';

		echo '<div class="products-navigation" style="display:block;">';
		echo "<span class='products-navigation--title alert'><span class='ec ec-pensive'></span> Non abbiamo trovato niente. Prova a dare un'occhiata a questi prodotti.</span>";

		echo '</div>';

	foreach ($categories as $category_slug) {
		$term_object = get_term_by('slug', $category_slug, 'product_cat');
		$catID = $term_object->term_id;
		$url = get_term_link( $catID );
		echo '<div class="shop--list">';
		echo '<div class="shop--list--header">';
		echo '<h2 class="shop--minititle">' . $term_object->name . '</h2>';
		echo '<a href="' . $url . '" title="Vedi tutto ' . $term_object->name . '" class="arrow-link">Vedi tutto<span class="icon-arrow-right"></span></a>';
		echo '</div>';
		echo do_shortcode('[products limit="3" columns="1" category="' . $category_slug . '"]');
		echo '</div>';
	}
	echo '</div>';
	echo '<div class="negozio-sidebar">';
	echo '<ul class="negozio-sidebar--list">';

	$my_walker= new Walker_Category_Custom();
	$special_category = get_field('agr_special_category', 'option');
	$excludeSpecial = '';
	if($special_category) {
		$excludeSpecial = '';
	} else {
		$excludeSpecial = $specialiID;
	}
	$sidebar = array(
			 'taxonomy'     => 'product_cat',
			 'orderby'  => 'meta_value',
			 'meta_key' => 'categories_order_agr',
			 'show_count'   => 0,
			 'hierarchical' => 1,
			 'title_li'     => '',
			 'hide_empty'   => 1,
			 'walker' => $my_walker,
			 'child_of' => $get_product_cat_ID,
			 'exclude' => $excludeSpecial,
		);
		wp_list_categories($sidebar);
		echo '</ul>';
	echo '</div>';

	echo '</div>';
}

//Cambio testo bollino sconti
add_filter('woocommerce_sale_flash', 'woocommerce_custom_sale_text', 10, 3);
function woocommerce_custom_sale_text($text, $post, $_product){
	return '<span class="onsale"><span class="small">HEY,</span><span>COSTA</span><span>MENO!</span></span>';
}

//Limita la ricerca ai prodotti
// Only show products in the front-end search results
add_filter('pre_get_posts', 'lw_search_filter_pages');
function lw_search_filter_pages($query) {
	// Frontend search only
	if (!is_admin() && $query->is_search()) {
		$query->set('post_type', 'product');
		$query->set('wc_query', 'product_query');
	}
	return $query;
}


/*** Change number of related products output*/
function woo_related_products_limit(){
	global $product;

	$args['posts_per_page'] = 6;
	return $args;
}

add_filter('woocommerce_output_related_products_args', 'jk_related_products_args', 20);
function jk_related_products_args($args){
	$args['posts_per_page'] = 6; // 4 related products
	$args['columns'] = 2; // arranged in 2 columns
	return $args;
}

//Cambia h2 a lista prodotti e aggiungi peso
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
add_action('woocommerce_shop_loop_item_title', 'soChangeProductsTitle', 10);
function soChangeProductsTitle(){
	global $product;
	$product_data = $product->get_meta('_woo_uom_input');

	if ($product->has_weight()) {
		if ($product_data && $product_data != 'gr') {
			echo '<div class="product-loop-title-meta"><h6 class="' . esc_attr(apply_filters('woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title')) . '">' . get_the_title() . '</h6>';
			echo '<span class="product-info--quantity">' . $product->get_weight() . ' ' . $product_data . '</span></div>';
		} else {
			if ($product->get_weight() == 1000) {
				echo '<div class="product-loop-title-meta"><h6 class="' . esc_attr(apply_filters('woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title')) . '">' . get_the_title() . '</h6>';
				echo '<span class="product-info--quantity">1 kg</span></div>';
			} else {
				echo '<div class="product-loop-title-meta"><h6 class="' . esc_attr(apply_filters('woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title')) . '">' . get_the_title() . '</h6>';
				echo '<span class="product-info--quantity">' . $product->get_weight() . ' gr</span></div>';
			}
		}
	} else {
		echo '<h6 class="' . esc_attr(apply_filters('woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title')) . '">' . get_the_title() . '</h6>';
	}
}

//Bottone minicart
add_action('wp_footer', 'trigger_for_ajax_add_to_cart');
function trigger_for_ajax_add_to_cart(){
	?>
	<div class="notify-product-cart-added" style="display: none">
		<span>Questo prodotto è stato aggiunto alla tua scatola!</span>
	</div>
	<script type="text/javascript">
		(function ($) {
			$('body').on('added_to_cart', function () {
				// Testing output on browser JS console
				$('.cart--link').removeClass('is-empty-cart');
				$('.cart--link').addClass('is-full-cart');

				$(".notify-product-cart-added").fadeIn()

				setTimeout(function () {
					$(".notify-product-cart-added").fadeOut()
				}, 3000);
			});
		})(jQuery);
	</script>
	<?php
}

//Aggiorna numero carrello
add_filter('woocommerce_add_to_cart_fragments', 'iconic_cart_count_fragments', 10, 1);
function iconic_cart_count_fragments($fragments){
	$fragments['.cart-number-elements'] = '<span class="cart-number-elements">' . WC()->cart->get_cart_contents_count() . '</span>';
	return $fragments;
}

/** Redirect users after add to cart. */
function my_custom_add_to_cart_redirect($url){
	if (!isset($_REQUEST['add-to-cart']) || !is_numeric($_REQUEST['add-to-cart'])) {
		return $url;
	}
	$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_REQUEST['add-to-cart']));

	// Only redirect products that have the 'box' category
	if (has_term('box', 'product_cat', $product_id)) {
		$url = wc_get_cart_url();
	}
	return $url;
}

add_filter('woocommerce_add_to_cart_redirect', 'my_custom_add_to_cart_redirect');
add_filter('woocommerce_checkout_fields', 'theme_override_checkout_notes_fields');

/*** Override loop template and show quantities next to add to cart buttons*/
add_filter('woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2);
function quantity_inputs_for_woocommerce_loop_add_to_cart_link($html, $product){
	if ($product && $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock() && !$product->is_sold_individually()) {
		$html = '<div class="shop-buttons-flex"><form action="' . esc_url($product->add_to_cart_url()) . '" class="cart" method="post" enctype="multipart/form-data"><div class="product-quantity--change"><button type="button" id="minus" class="product-quantity--minus disabled" field="quantity">-</button>';
		$html .= woocommerce_quantity_input(array(), $product, false);
		$html .= '<button type="button" id="plus" class="product-quantity--plus" field="quantity">+</button></div><button type="submit" data-product_id="' . $product->get_id() . '" data-quantity="1" data-tip="Ciao" data-product_sku="' . esc_attr($product->get_sku()) . '" class="btn btn-primary btn-small ajax_add_to_cart add_to_cart_button">' . esc_html($product->add_to_cart_text()) . '</button>';
		$html .= '</form></div>';
	}
	return $html;
}

//Aggiungi classe wp_list_categories
class Walker_Category_Custom extends Walker_Category {

	public $tree_type = 'category';
	public $db_fields = array(
		'parent' => 'parent',
		'id'     => 'term_id',
	);
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children'>\n";
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	public function start_el( &$output, $data_object, $depth = 0, $args = array(), $current_object_id = 0 ) {
		// Restores the more descriptive, specific name for use within this method.
		$category = $data_object;
		/** This filter is documented in wp-includes/category-template.php */
		$cat_name = apply_filters( 'list_cats', esc_attr( $category->name ), $category );
		// Don't generate an element if the category name is empty.
		if ( '' === $cat_name ) {
			return;
		}

		$atts         = array();
		$atts['href'] = get_term_link( $category );

		if ( $args['use_desc_for_title'] && ! empty( $category->description ) ) {

			$atts['title'] = strip_tags( apply_filters( 'category_description', $category->description, $category ) );
		}


		$atts = apply_filters( 'category_list_link_attributes', $atts, $category, $depth, $args, $current_object_id );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$link = sprintf(
			'<a%s>%s</a>',
			$attributes,
			$cat_name
		);

		if ( ! empty( $args['show_count'] ) ) {
			$link .= ' (' . number_format_i18n( $category->count ) . ')';
		}
		if ( 'list' === $args['style'] ) {
			$output     .= "\t<li";
			if($category->count == 0) {
				$cat_empty = 'cat-empty';
			} else {
				$cat_empty = '';
			}
			$css_classes = array(
				'cat-item',
				'cat-item-' . $category->term_id,
				$cat_empty
			);

			if ( ! empty( $args['current_category'] ) ) {
				// 'current_category' can be an array, so we use `get_terms()`.
				$_current_terms = get_terms(
					array(
						'taxonomy'   => $category->taxonomy,
						'include'    => $args['current_category'],
						'hide_empty' => true
					)
				);

				foreach ( $_current_terms as $_current_term ) {
					if ( $category->term_id == $_current_term->term_id ) {
						$css_classes[] = 'current-cat';
						$link          = str_replace( '<a', '<a aria-current="page"', $link );
					} elseif ( $category->term_id == $_current_term->parent ) {
						$css_classes[] = 'current-cat-parent';
					}
					while ( $_current_term->parent ) {
						if ( $category->term_id == $_current_term->parent ) {
							$css_classes[] = 'current-cat-ancestor';
							break;
						}
						$_current_term = get_term( $_current_term->parent, $category->taxonomy );
					}
				}
			}
			$css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );
			$css_classes = $css_classes ? ' class="' . esc_attr( $css_classes ) . '"' : '';

			$output .= $css_classes;
			$output .= ">$link\n";
		} elseif ( isset( $args['separator'] ) ) {
			$output .= "\t$link" . $args['separator'] . "\n";
		} else {
			$output .= "\t$link<br />\n";
		}

	}
	public function end_el( &$output, $data_object, $depth = 0, $args = array() ) {
		$category = $data_object;

		if ( 'list' !== $args['style'] ) {
			return;
		}
		$output .= "</li>\n";


		if($category->count > 0 && category_has_children( $category->term_id )) {

			$output .= "<li class='cat-item view-all'><a href='".get_term_link( $category->term_id )."' title='Tutto " . $category->name . "' class='view-all--link'>Tutto " . $category->name . "</a></li>";

		}

	}
}

function category_has_children( $term_id = 0, $taxonomy = 'product_cat' ) {
    $children = get_categories( array(
        'child_of'      => $term_id,
        'taxonomy'      => $taxonomy,
        'hide_empty'    => false,
        'fields'        => 'ids',
    ) );
    return ( $children );
}

/*** Change number of products that are displayed per page (shop page)*/
add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

function new_loop_shop_per_page( $cols ) {
  // $cols contains the current number of products per page based on the value stored on Options –> Reading
  // Return the number of products you wanna show per page.
  $cols = 20;
  return $cols;
}

//Modifica loop negozio per avere solo prodotti con check _is_product_active
function custom_meta_query( $meta_query ){
    $meta_query[] = array(
			'key'     => '_is_active_shop',
			'value' => '1',
			'compare' => '=='
    );
    return $meta_query;
}

// The main shop and archives meta query
add_filter( 'woocommerce_product_query_meta_query', 'custom_product_query_meta_query', 10, 2 );
function custom_product_query_meta_query( $meta_query, $query ) {
    //if( ! is_admin() )
        return custom_meta_query( $meta_query );
}

// The shortcode products query
add_filter( 'woocommerce_shortcode_products_query', 'custom__shortcode_products_query', 10, 3 );
function custom__shortcode_products_query( $query_args, $atts, $loop_name ) {
    if( ! is_admin() )
        $query_args['meta_query'] = custom_meta_query( $query_args['meta_query'] );
    return $query_args;
}

// The widget products query
add_filter( 'woocommerce_products_widget_query_args', 'custom_products_widget_query_arg', 10, 1 );
function custom_products_widget_query_arg( $query_args ) {
    if( ! is_admin() )
        $query_args['meta_query'] = custom_meta_query( $query_args['meta_query'] );
    return $query_args;
}
