<?php

/**
 * Trim zeros in price decimals
 **/
//add_filter( 'woocommerce_price_trim_zeros', '__return_true' );


// Change add to cart text on single product page
add_filter('woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single');
function woocommerce_add_to_cart_button_text_single()
{
	return __('Acquista', 'woocommerce');
}

//Prezzo prima del pulsante add to cart
add_action('woocommerce_before_add_to_cart_button', 'misha_before_add_to_cart_btn');
function misha_before_add_to_cart_btn()
{
	global $product;
	echo '<div class="btn-price">' . $product->get_price_html() . '</div>';
}


//// Layout pagina negozio vuoto
add_action('woocommerce_no_products_found', 'shop_page_empty_layout');

function shop_page_empty_layout()
{
	$getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
	$get_product_cat_ID = $getIDbyNAME->term_id;
	$args = array(
		'hide_empty' => true,
		'fields' => 'slugs',
		'taxonomy' => 'product_cat',
		'parent' => $get_product_cat_ID,
		'hide_empty' => true,
	);
	$categories = get_categories($args);
	foreach ($categories as $category_slug) {
		$term_object = get_term_by('slug', $category_slug, 'product_cat');
		$catID = $term_object->term_id;
		echo '<div class="shop--list">';
		echo '<div class="shop--list--header">';
		echo '<h2 class="shop--minititle">' . $term_object->name . '</h2>';
		echo '<a href="' . $term_object->slug . '" title="Vedi tutto ' . $term_object->name . '" class="arrow-link">Vedi tutto<span class="icon-arrow-right"></span></a>';
		echo '</div>';
		echo do_shortcode('[products limit="-1" columns="1" category="' . $category_slug . '"]');
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
add_filter('pre_get_posts', 'lw_search_filter_pages');
function lw_search_filter_pages($query)
{
	// Frontend search only
	if (!is_admin() && $query->is_search()) {
		$query->set('post_type', 'product');
		$query->set('wc_query', 'product_query');
	}
	return $query;
}


/**
 * Change number of related products output
 */
function woo_related_products_limit()
{
	global $product;

	$args['posts_per_page'] = 6;
	return $args;
}

add_filter('woocommerce_output_related_products_args', 'jk_related_products_args', 20);
function jk_related_products_args($args)
{
	$args['posts_per_page'] = 6; // 4 related products
	$args['columns'] = 2; // arranged in 2 columns
	return $args;
}


//Rimuovi label shipping_method
add_filter('woocommerce_cart_shipping_method_full_label', 'bbloomer_remove_shipping_label', 9999, 2);

function bbloomer_remove_shipping_label($label, $method)
{
	$new_label = preg_replace('/^.+:/', '', $label);
	return $new_label;
}


//Minimo ordine checkout
/**
 * Set a minimum order amount for checkout
 */
add_action('woocommerce_checkout_process', 'wc_minimum_order_amount');
add_action('woocommerce_before_cart', 'wc_minimum_order_amount');

function wc_minimum_order_amount()
{

	$category = 'box';
	$minimum = get_field('agr_minimun_amount', 'option');

	$loggedUser = is_user_logged_in();
	$allowedClients = get_field('agr_clients_no_limits', 'option');


	if ($loggedUser && $allowedClients && in_array($loggedUser, $allowedClients)) {
		$minimum = 10;
	}

	// Loop through cart items
	foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
		// Product id
		$product_id = $cart_item['product_id'];
		// Has category box
		if (has_term($category, 'product_cat', $product_id)) {
			$minimum = 10;
		}
	}

	if (WC()->cart->total < $minimum) {
		$cartTotal = WC()->cart->total;
		$addPrice = $minimum - $cartTotal;

		if (is_cart()) {

			echo '<div class="minimum-amount-advice"><div class="checkout--preview--items mg-t"><span class="is-title"><span class="icon-ics is-icon red"></span>Non hai abbastanza prodotti</span><span class="is-description">Per preparare la tua scatola, abbiamo bisogno di un ordine di almeno ' . wc_price($minimum) . '. Scegli altri prodotti!<br/>Ti mancano ' . wc_price($addPrice) . '.</span></div></div>';
			// Remove proceed to checkout button
			remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);

		} else {

			echo '<div class="minimum-amount-advice"><div class="checkout--preview--items mg-t"><span class="is-title"><span class="icon-ics is-icon red"></span>Non hai abbastanza prodotti</span><span class="is-description">Per preparare la tua scatola, abbiamo bisogno di un ordine di almeno ' . wc_price($minimum) . '. Scegli altri prodotti!<br/>Ti mancano ' . wc_price($addPrice) . '.</span></div></div>';
			// Remove proceed to checkout button
			remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);

		}
	}
}

//Cambia h2 a lista prodotti e aggiungi peso
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
add_action('woocommerce_shop_loop_item_title', 'soChangeProductsTitle', 10);
function soChangeProductsTitle()
{
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


//Free shipping label
add_filter('woocommerce_cart_shipping_method_full_label', 'add_free_shipping_label', 10, 2);
function add_free_shipping_label($label, $method)
{
	if ($method->cost == 0) {
		$label = 'Gratuita'; //not quite elegant hard coded string
	}
	return $label;
}

//coupon con consegna gratuita
add_filter('woocommerce_package_rates', 'coupon_free_shipping_customization', 20, 2);
function coupon_free_shipping_customization($rates, $package)
{
	$has_free_shipping = false;

	$applied_coupons = WC()->cart->get_applied_coupons();
	foreach ($applied_coupons as $coupon_code) {
		$coupon = new WC_Coupon($coupon_code);
		if ($coupon->get_free_shipping()) {
			$has_free_shipping = true;

			break;
		}
	}

	foreach ($rates as $rate_key => $rate) {
		if ($has_free_shipping) {
			// For "free shipping" method (enabled), remove it
			if ($rate->method_id == 'free_shipping') {
				unset($rates[$rate_key]);
			} // For other shipping methods
			else {
				// Append rate label titles (free)
				$rates[$rate_key]->label .= ' ' . __('Gratuita', 'woocommerce');

				// Set rate cost
				$rates[$rate_key]->cost = 0;

				// Set taxes rate cost (if enabled)
				$taxes = array();
				foreach ($rates[$rate_key]->taxes as $key => $tax) {
					if ($rates[$rate_key]->taxes[$key] > 0)
						$taxes[$key] = 0;
				}
				$rates[$rate_key]->taxes = $taxes;
			}
		}
	}
	return $rates;
}

//Sposta bottoni di pagamento prima del bottone di default
//add_action('init', 'change_payments_buttons_position', 11);
// function change_payments_buttons_position() {
//   $payementGateway = WC_Stripe_Payment_Request::instance();
//
//   if ($payementGateway) {
//     remove_action('woocommerce_proceed_to_checkout', array($payementGateway, 'display_payment_request_button_html'), 1);
//   	remove_action('woocommerce_proceed_to_checkout', array($payementGateway, 'display_payment_request_button_separator_html'), 2);
//
//   	add_action('woocommerce_review_order_before_submit', array($payementGateway, 'display_payment_request_button_html'), 2);
//   	add_action('woocommerce_review_order_before_submit', array($payementGateway, 'display_payment_request_button_separator_html'), 1);
//   }
// }

//sposta coupon nel checkout
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
add_action('woocommerce_review_order_before_payment', 'woocommerce_checkout_coupon_form', 5);

/* YITH Gift Cards - hide the section for gift card code submission on cart page */
if (!function_exists('yith_ywgc_hide_on_cart')) {
	function yith_ywgc_hide_on_cart($show_field)
	{
		if (is_cart()) {
			$show_field = false;
		}

		return $show_field;
	}
}
add_filter('yith_gift_cards_show_field', 'yith_ywgc_hide_on_cart');

//sposta gift card nel checkout
if (!function_exists('ywgc_gift_card_code_form_checkout_hook')) {
	function ywgc_gift_card_code_form_checkout_hook($hook)
	{
		$hook = 'woocommerce_review_order_before_payment';
		return $hook;
	}
}
add_filter('ywgc_gift_card_code_form_checkout_hook', 'ywgc_gift_card_code_form_checkout_hook', 10, 1);


//Bottone minicart
add_action('wp_footer', 'trigger_for_ajax_add_to_cart');
function trigger_for_ajax_add_to_cart()
{
	?>
	<div class="notify-product-cart-added" style="display: none">
		<span>Il prodotto è stato aggiunto al tuo carrello.</span>
		<a href="/carrello/" title="Visualizza carrello">Visualizza carrello</a>
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

function iconic_cart_count_fragments($fragments)
{

	$fragments['.cart-number-elements'] = '<span class="cart-number-elements">' . WC()->cart->get_cart_contents_count() . '</span>';

	return $fragments;

}

/**
 * Redirect users after add to cart.
 */
function my_custom_add_to_cart_redirect($url)
{

	if (!isset($_REQUEST['add-to-cart']) || !is_numeric($_REQUEST['add-to-cart'])) {
		return $url;
	}

	$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_REQUEST['add-to-cart']));

	// Only redirect products that have the 'box' category
	if (has_term('box', 'product_cat', $product_id)) {
		$url = WC()->cart->get_cart_url();
	}

	return $url;

}

add_filter('woocommerce_add_to_cart_redirect', 'my_custom_add_to_cart_redirect');

add_filter('woocommerce_checkout_fields', 'theme_override_checkout_notes_fields');

// Modifica label note di consegna
function theme_override_checkout_notes_fields($fields)
{
	$fields['order']['order_comments']['placeholder'] = 'Dobbiamo sapere qualcosa in più? Ad esempio richieste particolari per la consegna. Dicci tutto!';
	$fields['order']['order_comments']['label'] = 'Note personali';
	return $fields;
}


//Cambia notifica codice sconto
add_filter('wc_add_to_cart_message_html', 'quadlayers_custom_add_to_cart_message');
function quadlayers_custom_add_to_cart_message()
{
	$message = 'Questo prodotto è stato aggiunto alla tua scatola!';
	return $message;
}


/**
 * Override loop template and show quantities next to add to cart buttons
 */
add_filter('woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2);
function quantity_inputs_for_woocommerce_loop_add_to_cart_link($html, $product)
{

	if ($product && $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock() && !$product->is_sold_individually()) {
		$html = '<div class="shop-buttons-flex"><form action="' . esc_url($product->add_to_cart_url()) . '" class="cart" method="post" enctype="multipart/form-data"><div class="product-quantity--change"><button type="button" id="minus" class="product-quantity--minus disabled" field="quantity">-</button>';
		$html .= woocommerce_quantity_input(array(), $product, false);
		$html .= '<button type="button" id="plus" class="product-quantity--plus" field="quantity">+</button></div><button type="submit" data-product_id="' . $product->get_id() . '" data-quantity="1" data-tip="Ciao" data-product_sku="' . esc_attr($product->get_sku()) . '" class="btn btn-primary btn-small ajax_add_to_cart add_to_cart_button">' . esc_html($product->add_to_cart_text()) . '</button>';
		$html .= '</form></div>';
	}
	return $html;
}


add_filter('woocommerce_billing_fields', 'custom_woocommerce_billing_fields');


function custom_woocommerce_billing_fields($fields)
{

	$fields['_billing_scala'] = array(
		'type' => 'text',
		'class' => array('my-field-class orm-row-wide'),
		'label' => __('Scala'),
		'required' => true,
		'placeholder' => '',
	);

	$fields['_billing_piano'] = array(
		'type' => 'text',
		'class' => array('my-field-class orm-row-wide'),
		'label' => __('Piano'),
		'required' => true,
		'placeholder' => '',
	);

	return $fields;
}


add_filter('woocommerce_shipping_fields', 'custom_woocommerce_shipping_fields');

function custom_woocommerce_shipping_fields($fields)
{

	$fields['_shipping_scala'] = array(
		'type' => 'text',
		'class' => array('my-field-class orm-row-wide'),
		'label' => __('Scala'),
		'required' => true,
		'placeholder' => '',
	);

	$fields['_shipping_piano'] = array(
		'type' => 'text',
		'class' => array('my-field-class orm-row-wide'),
		'label' => __('Piano'),
		'required' => true,
		'placeholder' => '',
	);

	return $fields;
}


add_action('woocommerce_after_order_notes', 'my_custom_checkout_field');

function my_custom_checkout_field($checkout)
{

	woocommerce_form_field('compleanno', array(
		'type' => 'date',
		'class' => 'input-text ',
		'label' => __('Compleanno'),
		'required' => false,
		'placeholder' => '',
	), $checkout->get_value('compleanno'));

}


/**
 * Update the order meta with field value
 **/
add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');

function my_custom_checkout_field_update_order_meta($order_id)
{
	if ($_POST['compleanno']) update_post_meta($order_id, 'compleanno', esc_attr($_POST['compleanno']));

}

/**
 * Add the field to order emails
 *
 * */
add_filter('woocommerce_email_order_meta_keys', 'my_custom_checkout_field_order_meta_keys');

function my_custom_checkout_field_order_meta_keys($keys)
{
	$keys[] = 'compleanno';
	return $keys;
}
