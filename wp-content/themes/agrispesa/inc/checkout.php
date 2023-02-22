<?php

//Remove payments from resume table
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
//Add payments methods after shipping address
add_action( 'woocommerce_checkout_payment_hook', 'woocommerce_checkout_payment', 10 );

//Rimuovi label shipping_method
add_filter('woocommerce_cart_shipping_method_full_label', 'bbloomer_remove_shipping_label', 9999, 2);
function bbloomer_remove_shipping_label($label, $method){
	$new_label = preg_replace('/^.+:/', '', $label);
	return $new_label;
}

//Minimo ordine checkout
add_action('woocommerce_checkout_process', 'wc_minimum_order_amount');
add_action('woocommerce_before_cart', 'wc_minimum_order_amount');

function wc_minimum_order_amount(){

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

//Free shipping label
add_filter('woocommerce_cart_shipping_method_full_label', 'add_free_shipping_label', 10, 2);
function add_free_shipping_label($label, $method){
	if ($method->cost == 0) {
		$label = 'Gratuita'; //not quite elegant hard coded string
	}
	return $label;
}

//coupon con consegna gratuita
add_filter('woocommerce_package_rates', 'coupon_free_shipping_customization', 20, 2);
function coupon_free_shipping_customization($rates, $package){
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
	function yith_ywgc_hide_on_cart($show_field){
		if (is_cart()) {
			$show_field = false;
		}

		return $show_field;
	}
}
add_filter('yith_gift_cards_show_field', 'yith_ywgc_hide_on_cart');

//sposta gift card nel checkout
if (!function_exists('ywgc_gift_card_code_form_checkout_hook')) {
	function ywgc_gift_card_code_form_checkout_hook($hook){
		$hook = 'woocommerce_review_order_before_payment';
		return $hook;
	}
}
add_filter('ywgc_gift_card_code_form_checkout_hook', 'ywgc_gift_card_code_form_checkout_hook', 10, 1);

// Modifica label note di consegna
function theme_override_checkout_notes_fields($fields){
	$fields['order']['order_comments']['placeholder'] = 'Dobbiamo sapere qualcosa in più? Ad esempio richieste particolari per la consegna. Dicci tutto!';
	$fields['order']['order_comments']['label'] = 'Note sulle consegna';
	return $fields;
}

//Cambia notifica codice sconto
add_filter('wc_add_to_cart_message_html', 'quadlayers_custom_add_to_cart_message');
function quadlayers_custom_add_to_cart_message(){
	$message = 'Questo prodotto è stato aggiunto alla tua scatola!';
	return $message;
}


//Sposta login al checkout
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
//add_action( 'woocommerce_checkout_billing', 'woocommerce_checkout_login_form' );



//Campi checkout
add_action('woocommerce_after_order_notes', 'personal_checkout_field');
function personal_checkout_field($checkout){
	echo '<div class="woocommerce-border-form">';
	echo '<h3 class="checkout--title">Su di te <span class="ec ec-sparkles"></span></h3>';
	woocommerce_form_field('compleanno', array(
		'type' => 'date',
		'class' => 'input-text ',
		'label' => __('Data di nascita'),
		'required' => false,
		'placeholder' => 'Sarà un buon motivo per festeggiare!',
	), $checkout->get_value('compleanno'));
	echo '</div>';
}

add_action('woocommerce_before_order_notes', 'delivery_checkout_field');
function delivery_checkout_field($checkout){
	echo '<div class="woocommerce-border-form w-bottom">';
	echo '<h3 class="checkout--title">Consegna a domicilio</h3>';
	echo '<p class="woocommerce-border-form--info">Hai qualche informazione utile per il nostro corriere?</p>';
	woocommerce_form_field('scala', array(
		'type' => 'text',
		'class' => 'input-text ',
		'label' => __('Scala'),
		'required' => false,
		'placeholder' => 'In quale scala abiti?',
	), $checkout->get_value('scala'));

	woocommerce_form_field('piano', array(
		'type' => 'text',
		'class' => 'input-text ',
		'label' => __('Piano'),
		'required' => true,
		'placeholder' => 'A che piano vivi?',
	), $checkout->get_value('piano'));
	echo '</div>';
}


/*** Update the order meta with field value ***/
add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta');
function custom_checkout_field_update_order_meta($order_id) {
	if ($_POST['compleanno']) update_post_meta($order_id, 'compleanno', esc_attr($_POST['compleanno']));
	if ($_POST['scala']) update_post_meta($order_id, 'scala', esc_attr($_POST['scala']));
	if ($_POST['piano']) update_post_meta($order_id, 'piano', esc_attr($_POST['piano']));
}

/** Display field value on the order edit page */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'scala_checkout_field_display_admin_order_meta', 10, 1 );
function scala_checkout_field_display_admin_order_meta($order_id){
    echo '<p><strong>'.__('Scala').':</strong> ' . get_post_meta( $order_id, 'scala', true ) . '</p>';
}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'piano_checkout_field_display_admin_order_meta', 10, 1 );
function piano_checkout_field_display_admin_order_meta($order_id){
    echo '<p><strong>'.__('Piano').':</strong> ' . get_post_meta( $order_id, 'piano', true ) . '</p>';
}


/*** Add the field to order emails ***/
add_filter('woocommerce_email_order_meta_keys', 'birthday_checkout_field_order_meta_keys');
function birthday_checkout_field_order_meta_keys($keys) {
	$keys[] = 'compleanno';
	return $keys;
}
add_filter('woocommerce_email_order_meta_keys', 'scala_checkout_field_order_meta_keys');
function scala_checkout_field_order_meta_keys($keys) {
	$keys[] = 'scala';
	return $keys;
}
add_filter('woocommerce_email_order_meta_keys', 'piano_checkout_field_order_meta_keys');
function piano_checkout_field_order_meta_keys($keys) {
	$keys[] = 'piano';
	return $keys;
}
