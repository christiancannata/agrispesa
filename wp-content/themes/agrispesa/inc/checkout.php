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
	$fields['order']['order_comments']['placeholder'] = 'Dobbiamo sapere qualcosa in pi??? Ad esempio richieste particolari per la consegna. Dicci tutto!';
	$fields['order']['order_comments']['label'] = 'Note sulle consegna';
	return $fields;
}

//Cambia notifica codice sconto
add_filter('wc_add_to_cart_message_html', 'quadlayers_custom_add_to_cart_message');
function quadlayers_custom_add_to_cart_message(){
	$message = 'Questo prodotto ?? stato aggiunto alla tua scatola!';
	return $message;
}

//Sposta login al checkout
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
//add_action( 'woocommerce_checkout_billing', 'woocommerce_checkout_login_form' );

//Metti in sospeso l'ordine di default
add_action( 'woocommerce_payment_complete', 'webappick_set_completed_for_paid_orders' );
function webappick_set_completed_for_paid_orders( $order_id ) {
	$order = wc_get_order( $order_id );
	$order->update_status( 'on_hold' );
}

//Cambia label stato ordini nella lista e dettaglio
add_filter( 'wc_order_statuses', 'rename_order_statuses', 20, 1 );
function rename_order_statuses( $order_statuses ) {
    //$order_statuses['wc-completed']  = _x( 'Order Received', 'Order status', 'woocommerce' );
    //$order_statuses['wc-processing'] = _x( 'Paid', 'Order status', 'woocommerce' );
    $order_statuses['wc-on-hold']    = _x( 'Confezionamento', 'Order status', 'woocommerce' );
    //$order_statuses['wc-pending']    = _x( 'Waiting', 'Order status', 'woocommerce' );
    return $order_statuses;
}
//Cambia label stato ordini nel bulk
add_filter( 'bulk_actions-edit-shop_order', 'custom_dropdown_bulk_actions_shop_order', 20, 1 );
function custom_dropdown_bulk_actions_shop_order( $actions ) {
    //$actions['mark_processing'] = __( 'Mark paid', 'woocommerce' );
    $actions['mark_on-hold']    = __( 'Modifica lo stato in "Confezionamento"', 'woocommerce' );
    //$actions['mark_completed']  = __( 'Mark order received', 'woocommerce' );
    return $actions;
}


//Add custom fields to checkout
function cloudways_custom_checkout_fields($fields){
    $fields['cloudways_extra_fields'] = array(
      	'cloudways_scala_field' => array(
          'type' => 'text',
          'required'      => true,
					'label'         => __('Scala'),
					'placeholder'   => __('In quale scala abiti?'),
        ),
				'cloudways_piano_field' => array(
          'type' => 'text',
          'required'      => true,
					'label'         => __('Piano'),
					'placeholder'   => __('A che piano vivi?'),
        ),
    );
    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'cloudways_custom_checkout_fields' );
function cloudways_extra_checkout_fields(){
    $checkout = WC()->checkout(); ?>
		<div id="shipping-custom-fields" class="woocommerce-border-form w-bottom">
			<h3 class="checkout--title">Consegna a domicilio <span class="ec ec-sparkles"></span></h3>
			<p class="woocommerce-border-form--info">Hai qualche informazione utile per il nostro corriere?</p>
	    <?php
	       foreach ( $checkout->checkout_fields['cloudways_extra_fields'] as $key => $field ) : ?>
	            <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
	        <?php endforeach; ?>
	    </div>
<?php }
add_action( 'woocommerce_checkout_after_customer_details' ,'cloudways_extra_checkout_fields' );

//Save data of WooCommerce Custom Checkout Fields
function cloudways_save_extra_checkout_fields( $order_id, $posted ){
    // don't forget appropriate sanitization if you are using a different field type
    if( isset( $posted['cloudways_scala_field'] ) ) {
        update_post_meta( $order_id, '_cloudways_scala_field', sanitize_text_field( $posted['cloudways_scala_field'] ) );
    }
    if( isset( $posted['cloudways_piano_field'] ) ) {
        update_post_meta( $order_id, '_cloudways_piano_field', sanitize_text_field( $posted['cloudways_piano_field'] ) );
    }

}
add_action( 'woocommerce_checkout_update_order_meta', 'cloudways_save_extra_checkout_fields', 10, 2 );

//Display WooCommerce Admin Custom Order Fields
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'admin_order_after_billing_address_callback', 10, 1 );
function admin_order_after_billing_address_callback( $order ){
    if ( $tiva1  = $order->get_meta('_cloudways_scala_field') ) {
        echo '<p><strong>'. __("Scala") . ':</strong> ' . $tiva1 . '</p>';
    } else {
			  echo '<p><strong>'. __("Scala") . ':</strong>-</p>';
		}
    if ( $tfcarr = $order->get_meta('_cloudways_piano_field') ) {
        echo '<p><strong>'. __("Piano") . ':</strong> ' . $tfcarr . '</p>';
    } else {
			  echo '<p><strong>'. __("Piano") . ':</strong>-</p>';
		}
}

function cloudways_save_extra_details( $post_id, $post ){
    update_post_meta( $post_id, '_cloudways_text_field', wc_clean( $_POST[ '_cloudways_text_field' ] ) );
    update_post_meta( $post_id, '_cloudways_dropdown', wc_clean( $_POST[ '_cloudways_dropdown' ] ) );
}
add_action( 'woocommerce_process_shop_order_meta', 'cloudways_save_extra_details', 45, 2 );


// disable double add to cart of a product
add_filter('woocommerce_add_to_cart_validation', 'my_validation_handler', 10, 2);
function my_validation_handler($is_valid, $product_id) {
	foreach(WC()->cart->get_cart() as $cart_item_key => $values) {
		if ($values['data']->get_id() == $product_id) {
			return false;
		}
	}
	return $is_valid;
}
