<?php


add_action('woocommerce_payment_complete', 'wpdesk_set_completed_for_paid_orders');

function wpdesk_set_completed_for_paid_orders($order_id)
{

	$order = wc_get_order($order_id);
	$order->update_status('completed');

}


//Remove payments from resume table
remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
//Add payments methods after shipping address
add_action('woocommerce_checkout_payment_hook', 'woocommerce_checkout_payment', 10);

//Rimuovi label shipping_method
add_filter('woocommerce_cart_shipping_method_full_label', 'bbloomer_remove_shipping_label', 9999, 2);
function bbloomer_remove_shipping_label($label, $method)
{
	$new_label = preg_replace('/^.+:/', '', $label);
	return $new_label;
}

//Minimo ordine checkout
add_action('woocommerce_checkout_process', 'wc_minimum_order_amount_checkout');
add_action('woocommerce_before_cart', 'wc_minimum_order_amount');


function getMinimumOrder()
{
	$current_user = wp_get_current_user();

	$category = 'box';
	$minimum = get_option('options_agr_minimun_amount');
	$usersToEsclude = get_option('options_agr_clients_no_limits');

	$delivery = 15;

	/*
	 * FATTO. € 43 per chi fa acquisti unicamente dal negozio (ST)

FATTO. Non c'è minimo ordine per ST che fa ulteriori ordini oltre al primo per quella settimana.
a. Per questi secondi ordini ST, se l'indirizzo di consegna è uguale a quello del primo ordine, non c'è costo di spedizione.
b. Per questi secondi ordini ST, se l'indirizzo di consegna è diverso a quello del primo ordine, c'è costo di spedizione

FATTO. Non c'è minimo ordine per clienti FN che acquistano prodotti aggiuntivi.
a. Per questi ordini aggiuntivi FN, se l'indirizzo di consegna è uguale a quello del primo ordine, non c'è costo di spedizione.

	 */

	if (!$minimum) {
		$minimum = 86;
	}


	//$allowedClients = get_option('agr_clients_no_limits');

	$has_sub = false;

	$orderLastWeek = 0;

	if ($current_user) {

		$has_sub = wcs_user_has_subscription($current_user->ID, '', 'active');

		$lastDayCreatedOrdersFN = getLastDeliveryDay();
		$args = array(
			'customer_id' => get_current_user_id(),
			'limit' => 1,
			'status' => "completed",
			"meta_key" => "_date_completed",
			"meta_value" => $lastDayCreatedOrdersFN->getTimestamp(),
			"meta_compare" => ">",
		);
		$orders = wc_get_orders($args);
		$orderLastWeek = count($orders);

	}


	// NO MINIMO ORDINE SE HA FN ATTIVA
	if ($current_user && $has_sub) {
		//tolgo il limite se l'utente ha un abbonamento attivo
		$minimum = 0;
		$delivery = 0;
	}

	if ($current_user && !$has_sub && $orderLastWeek > 0) {
		//tolgo il limite se l'utente ha un abbonamento attivo
		$minimum = 0;
		$delivery = 0;
	}


	if ($usersToEsclude && is_array($usersToEsclude) && $current_user && in_array($current_user->ID, $usersToEsclude)) {
		$minimum = 0;
	}

	// Loop through cart items
	foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
		// Product id
		$product_id = $cart_item['product_id'];
		// Has category box
		/*	if (has_term($category, 'product_cat', $product_id)) {
				$minimum = 10;
				continue;
			} */

		//ACQUISTO CREDITO
		if ($product_id == 17647) {
			$minimum = 0;
			$delivery = 0;
			continue;
		}

	}


	$totalCart = WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() + WC()->cart->get_shipping_total();

	return ['minimum' => $minimum, 'diff' => $minimum - $totalCart];

}

function wc_minimum_order_amount()
{

	$minOrder = getMinimumOrder();

	if ($minOrder['diff'] > 0) {
		if (is_cart()) {

			//echo '<div class="minimum-amount-advice"><div class="checkout--preview--items mg-t"><span class="is-title"><span class="icon-ics is-icon red"></span>Non hai abbastanza prodotti</span><span class="is-description">Per preparare la tua scatola, abbiamo bisogno di un ordine di almeno ' . wc_price($minOrder['minimum']) . '. scegli altri prodotti!</span></div></div>';
			// Remove proceed to checkout button
			remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);

		} else {
			//echo '<div class="minimum-amount-advice"><div class="checkout--preview--items mg-t"><span class="is-title"><span class="icon-ics is-icon red"></span>Non hai abbastanza prodotti</span><span class="is-description">Per preparare la tua scatola, abbiamo bisogno di un ordine di almeno ' . wc_price($minOrder['minimum']) . '. scegli altri prodotti!.</span></div></div>';
			// Remove proceed to checkout button
			remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);

		}
	}

}

add_action('woocommerce_check_cart_items', 'required_min_cart_subtotal_amount');
function required_min_cart_subtotal_amount()
{

	$minOrder = getMinimumOrder();

	if ($minOrder['diff'] > 0) {
		wc_add_notice('Per preparare la tua scatola, abbiamo bisogno di un ordine di almeno ' . wc_price($minOrder['minimum']) . '. scegli altri prodotti! Ti mancano ' . wc_price($minOrder['diff']), 'error');
	}
}


function wc_minimum_order_amount_checkout()
{

	$minOrder = getMinimumOrder();

	if ($minOrder['diff'] > 0) {
		wc_add_notice('Per preparare la tua scatola, abbiamo bisogno di un ordine di almeno ' . wc_price($minOrder['minimum']) . '. scegli altri prodotti! Ti mancano ' . wc_price($minOrder['diff']), 'error');
	}
}

//Free shipping label
//add_filter('woocommerce_cart_shipping_method_full_label', 'add_free_shipping_label', 10, 2);
/*function add_free_shipping_label($label, $method)
{
	if ($method->cost == 0) {
		$label = 'Gratuita'; //not quite elegant hard coded string
	}
	return $label;
}*/


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

// Modifica label note di consegna
function theme_override_checkout_notes_fields($fields)
{
	$fields['order']['order_comments']['placeholder'] = 'Dobbiamo sapere qualcosa di più? Dicci tutto!';
	$fields['order']['order_comments']['label'] = 'Note per il confezionamento';
	return $fields;
}

//Cambia notifica codice sconto
add_filter('wc_add_to_cart_message_html', 'quadlayers_custom_add_to_cart_message');
function quadlayers_custom_add_to_cart_message()
{
	$message = 'Questo prodotto è stato aggiunto alla tua scatola!';
	return $message;
}

//Sposta login al checkout
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);
//add_action( 'woocommerce_checkout_billing', 'woocommerce_checkout_login_form' );

//Cambia label stato ordini nella lista e dettaglio
add_filter('wc_order_statuses', 'rename_order_statuses', 20, 1);
function rename_order_statuses($order_statuses)
{
	//$order_statuses['wc-completed']  = _x( 'Order Received', 'Order status', 'woocommerce' );
	//$order_statuses['wc-processing'] = _x( 'Paid', 'Order status', 'woocommerce' );
	$order_statuses['wc-on-hold'] = _x('Confezionamento', 'Order status', 'woocommerce');
	//$order_statuses['wc-pending']    = _x( 'Waiting', 'Order status', 'woocommerce' );
	return $order_statuses;
}

//Cambia label stato ordini nel bulk
add_filter('bulk_actions-edit-shop_order', 'custom_dropdown_bulk_actions_shop_order', 20, 1);
function custom_dropdown_bulk_actions_shop_order($actions)
{
	//$actions['mark_processing'] = __( 'Mark paid', 'woocommerce' );
	$actions['mark_on-hold'] = __('Modifica lo stato in "Confezionamento"', 'woocommerce');
	//$actions['mark_completed']  = __( 'Mark order received', 'woocommerce' );
	return $actions;
}


//Add DOGS custom fields to checkout
function cloudways_dog_custom_checkout_fields($fields)
{
	$fields['cloudways_dog_extra_fields'] = array(
		'cloudways_dog_name_field' => array(
			'type' => 'text',
			'required' => false,
			'label' => __('Nome del cane'),
			'placeholder' => __('Come si chiama il tuo cane?'),
		),
	);
	return $fields;
}

add_filter('woocommerce_checkout_fields', 'cloudways_dog_custom_checkout_fields');
function cloudways_dog_extra_checkout_fields()
{
	$checkout = WC()->checkout();

	$cat_in_cart = false;
	// Loop through all products in the Cart
	foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
		// If Cart has category "petfood", set $cat_in_cart to true
		if (has_term('petfood', 'product_cat', $cart_item['product_id'])) {
			$cat_in_cart = true;
			break;
		}
	}
	if ($cat_in_cart) {
		?>

		<div id="dog-custom-fields" class="woocommerce-border-form">
			<h3 class="checkout--title">Amiamo gli animali <span class="ec ec-dog"></span></h3>
			<p class="woocommerce-border-form--info">Vogliamo sapere di più sul tuo amico a quattro zampe.</p>
			<?php
			foreach ($checkout->checkout_fields['cloudways_dog_extra_fields'] as $key => $field) : ?>
				<?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
			<?php endforeach; ?>
		</div>
	<?php }
}

add_action('woocommerce_checkout_after_customer_details', 'cloudways_dog_extra_checkout_fields');

//Save data of WooCommerce Custom Checkout Fields
function cloudways_save_extra_checkout_fields($order_id, $posted)
{
	if (isset($posted['cloudways_dog_name_field'])) {
		update_post_meta($order_id, '_cloudways_dog_name_field', sanitize_text_field($posted['cloudways_dog_name_field']));
	}

}

add_action('woocommerce_checkout_update_order_meta', 'cloudways_save_extra_checkout_fields', 10, 2);


function cloudways_save_extra_details($post_id, $post)
{
	if (isset($_POST['_cloudways_dog_name_field'])) {
		update_post_meta($post_id, '_cloudways_dog_name_field', wc_clean($_POST['_cloudways_dog_name_field']));
	}
}

add_action('woocommerce_process_shop_order_meta', 'cloudways_save_extra_details', 45, 2);


// disable double add to cart of a product
add_filter('woocommerce_add_to_cart_validation', 'my_validation_handler', 10, 2);
function my_validation_handler($is_valid, $product_id)
{
	foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
		if ($values['data']->get_id() == $product_id) {
			return false;
		}
	}
	return $is_valid;
}


//Checkout: Remove proceed button if shipping not available
// function prevent_checkout_access_no_shipping() {
//     // Check that WC is enabled and loaded
//     if( function_exists( 'is_checkout' ) && is_checkout() ) {
//
//         // get shipping packages and their rate counts
//         $packages = WC()->cart->get_shipping_packages();
//         foreach( $packages as $key => $pkg ) {
//             $calculate_shipping = WC()->shipping->calculate_shipping_for_package( $pkg );
//             if( empty( $calculate_shipping['rates'] ) ) {
//                 wp_redirect( esc_url( WC()->cart->get_cart_url() ) );
//                 exit;
//             }
//         }
//     }
// }
// add_action( 'wp', 'prevent_checkout_access_no_shipping' );
//
// //Checkout: Remove checkout button if shipping not available
// function disable_checkout_button_no_shipping() {
//     $package_counts = array();
//
//     // get shipping packages and their rate counts
//     $packages = WC()->shipping->get_packages();
//     foreach( $packages as $key => $pkg )
//         $package_counts[ $key ] = count( $pkg[ 'rates' ] );
//
//     // remove button if any packages are missing shipping options
//     if( in_array( 0, $package_counts ) )
//         remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
// }
// add_action( 'woocommerce_proceed_to_checkout', 'disable_checkout_button_no_shipping', 1 );

//Cambia testo nessuna spedizione disponibile
class WPDeskNoShippingMessage
{
	/**
	 * Register hooks.
	 */
	public function add_hooks()
	{
		add_filter('woocommerce_no_shipping_available_html', [$this, 'change_message']);
		add_filter('woocommerce_cart_no_shipping_available_html', [$this, 'change_message']);
	}

	/**
	 * Change message.
	 *
	 * @param string $message
	 *
	 * @return string
	 */
	public function change_message($message)
	{
		return __('<span class="cart-no-shipping-available">Non consegniamo nella tua zona</span>');
	}
}

(new WPDeskNoShippingMessage())->add_hooks();


// Display shipping_scala field to checkout and My account addresses
add_filter('woocommerce_shipping_fields', 'display_shipping_scala_field', 20, 1);
function display_shipping_scala_field($shipping_fields)
{

	$shipping_fields['shipping_scala'] = array(
		'type' => 'text',
		'label' => __('Scala'),
		'class' => array('form-row-wide'),
		'priority' => 25,
		'required' => false,
		'clear' => true,
		'priority' => 101
	);
	return $shipping_fields;
}

// Save shipping_scala field value as user meta data
add_action('woocommerce_checkout_update_customer', 'save_account_shipping_scala_field', 10, 2);
function save_account_shipping_scala_field($customer, $data)
{
	if (isset($_POST['shipping_scala']) && !empty($_POST['shipping_scala'])) {
		$customer->update_meta_data('shipping_scala', sanitize_text_field($_POST['shipping_scala']));
	}
}

// Admin orders shipping_scala editable field and display
add_filter('woocommerce_admin_shipping_fields', 'admin_order_shipping_scala_editable_field');
function admin_order_shipping_scala_editable_field($fields)
{
	$fields['shipping_scala'] = array('label' => __('Scala', 'woocommerce'));

	return $fields;
}

// WordPress User: Add shipping_scala editable field
add_filter('woocommerce_customer_meta_fields', 'wordpress_user_account_shipping_scala_field');
function wordpress_user_account_shipping_scala_field($fields)
{
	$fields['shipping']['fields']['shipping_scala'] = array(
		'label' => __('Scala', 'woocommerce'),
		'description' => __('', 'woocommerce')
	);
	return $fields;
}


// Display shipping_scala field to checkout and My account addresses
add_filter('woocommerce_shipping_fields', 'display_shipping_piano_field', 20, 1);
function display_shipping_piano_field($shipping_fields)
{

	if (!WC()->cart) {
		return $shipping_fields;
	}

	$default = '';
	if (in_array('WELOVEDENSO', WC()->cart->get_applied_coupons()) || in_array('WELOVEDENSO10', WC()->cart->get_applied_coupons())) {
		$default = 'TERRA';
	}

	$shipping_fields['shipping_piano'] = array(
		'type' => 'text',
		'label' => __('Piano'),
		'class' => array('form-row-wide'),
		'priority' => 25,
		'required' => true,
		'clear' => true,
		'default' => $default,
		'priority' => 101
	);
	return $shipping_fields;
}

// Save shipping_piano field value as user meta data
add_action('woocommerce_checkout_update_customer', 'save_account_shipping_piano_field', 10, 2);
function save_account_shipping_piano_field($customer, $data)
{
	if (isset($_POST['shipping_piano']) && !empty($_POST['shipping_piano'])) {
		$customer->update_meta_data('shipping_piano', sanitize_text_field($_POST['shipping_piano']));
	}
}

// Admin orders shipping_piano editable field and display
add_filter('woocommerce_admin_shipping_fields', 'admin_order_shipping_piano_editable_field');
function admin_order_shipping_piano_editable_field($fields)
{
	$fields['shipping_scala'] = array('label' => __('Piano', 'woocommerce'));

	return $fields;
}

// WordPress User: Add shipping_piano editable field
add_filter('woocommerce_customer_meta_fields', 'wordpress_user_account_shipping_piano_field');
function wordpress_user_account_shipping_piano_field($fields)
{
	$fields['shipping']['fields']['shipping_piano'] = array(
		'label' => __('Piano', 'woocommerce'),
		'description' => __('', 'woocommerce')
	);
	return $fields;
}


// Display shipping_citofono field to checkout and My account addresses
add_filter('woocommerce_shipping_fields', 'display_shipping_citofono_field', 20, 1);
function display_shipping_citofono_field($shipping_fields)
{

	$shipping_fields['shipping_citofono'] = array(
		'type' => 'textarea',
		'label' => __('Citofono e indicazioni per il corriere'),
		'class' => array('form-row-wide'),
		'priority' => 25,
		'required' => false,
		'clear' => true,
		'priority' => 101
	);
	return $shipping_fields;
}

// Save shipping_citofono field value as user meta data
add_action('woocommerce_checkout_update_customer', 'save_account_shipping_citofono_field', 10, 2);
function save_account_shipping_citofono_field($customer, $data)
{
	if (isset($_POST['shipping_citofono']) && !empty($_POST['shipping_citofono'])) {
		$customer->update_meta_data('shipping_citofono', sanitize_text_field($_POST['shipping_citofono']));
	}
}

// Admin orders shipping_citofono editable field and display
add_filter('woocommerce_admin_shipping_fields', 'admin_order_shipping_citofono_editable_field');
function admin_order_shipping_citofono_editable_field($fields)
{
	$fields['shipping_citofono'] = array('label' => __('Citofono e indicazioni', 'woocommerce'));

	return $fields;
}

// WordPress User: Add shipping_piano editable field
add_filter('woocommerce_customer_meta_fields', 'wordpress_user_account_shipping_citofono_field');
function wordpress_user_account_shipping_citofono_field($fields)
{
	$fields['shipping']['fields']['shipping_citofono'] = array(
		'label' => __('Citofono e indicazioni', 'woocommerce'),
		'description' => __('', 'woocommerce')
	);
	return $fields;
}


// Display Billing cellulare field to checkout and My account addresses
add_filter('woocommerce_billing_fields', 'display_cellulare_billing_field', 20, 1);
function display_cellulare_billing_field($billing_fields)
{

	$billing_fields['billing_cellulare'] = array(
		'type' => 'text',
		'label' => __('Cellulare'),
		'class' => array('form-row-wide'),
		'priority' => 25,
		'required' => false,
		'clear' => true,
		'priority' => 101
	);
	return $billing_fields;
}

// Save Billing cellulare field value as user meta data
add_action('woocommerce_checkout_update_customer', 'save_account_billing_cellulare_field', 10, 2);
function save_account_billing_cellulare_field($customer, $data)
{
	if (isset($_POST['billing_cellulare']) && !empty($_POST['billing_cellulare'])) {
		$customer->update_meta_data('billing_cellulare', sanitize_text_field($_POST['billing_cellulare']));
	}
}

// Admin orders Billing cellulare editable field and display
add_filter('woocommerce_admin_billing_fields', 'admin_order_billing_cellulare_editable_field');
function admin_order_billing_cellulare_editable_field($fields)
{
	$fields['cellulare'] = array('label' => __('Cellulare', 'woocommerce'));

	return $fields;
}

// WordPress User: Add Billing cellulare editable field
add_filter('woocommerce_customer_meta_fields', 'wordpress_user_account_billing_cellulare_field');
function wordpress_user_account_billing_cellulare_field($fields)
{
	$fields['billing']['fields']['billing_cellulare'] = array(
		'label' => __('Cellulare', 'woocommerce'),
		'description' => __('', 'woocommerce')
	);
	return $fields;
}


//creo il campo codice fiscale
add_filter('woocommerce_checkout_fields', 'codice_fiscale');

function codice_fiscale($fields)
{
	$fields['billing']['codice_fiscale'] = array(
		'label' => __('Codice Fiscale', 'woocommerce'),
		'placeholder' => _x('Codice Fiscale', 'placeholder', 'woocommerce'),
		'required' => true,
		'class' => array('form-row'),
		'clear' => true,
		'priority' => 20
	);

	return $fields;
}

add_action('woocommerce_checkout_update_order_meta', 'codice_fiscale_order_meta');

function codice_fiscale_order_meta($order_id)
{
	if (!empty($_POST['codice_fiscale'])) {
		update_post_meta($order_id, 'Codice Fiscale', strtoupper(sanitize_text_field($_POST['codice_fiscale'])));
	}
}


// Save Billing codice_fiscale field value as user meta data
add_action('woocommerce_checkout_update_customer', 'save_account_codice_fiscale_field', 10, 2);
function save_account_codice_fiscale_field($customer, $data)
{
	if (isset($_POST['codice_fiscale']) && !empty($_POST['codice_fiscale'])) {
		$customer->update_meta_data('codice_fiscale', sanitize_text_field($_POST['codice_fiscale']));
	}
}

// Admin orders Billing codice_fiscale editable field and display
add_filter('woocommerce_admin_billing_fields', 'admin_order_codice_fiscale_editable_field');
function admin_order_codice_fiscale_editable_field($fields)
{
	$fields['codice_fiscale'] = array('label' => __('Codice Fiscale', 'woocommerce'));

	return $fields;
}

// WordPress User: Add Billing codice fiscale editable field
add_filter('woocommerce_customer_meta_fields', 'wordpress_user_account_codice_fiscale_field');
function wordpress_user_account_codice_fiscale_field($fields)
{
	$fields['billing']['fields']['codice_fiscale'] = array(
		'label' => __('Codice Fiscale', 'woocommerce'),
		'description' => __('', 'woocommerce')
	);
	return $fields;
}


// creo il campo partita iva
add_filter('woocommerce_checkout_fields', 'partita_iva');

function partita_iva($fields)
{
	$fields['billing']['partita_iva'] = array(
		'label' => __('Partita Iva', 'woocommerce'),
		'placeholder' => _x('Partita Iva', 'placeholder', 'woocommerce'),
		'required' => false,
		'class' => array('form-row'),
		'clear' => true,
		'priority' => 30
	);

	return $fields;
}

add_action('woocommerce_checkout_update_order_meta', 'partita_iva_order_meta');

function partita_iva_order_meta($order_id)
{
	if (!empty($_POST['partita_iva'])) {
		update_post_meta($order_id, 'Partita Iva', sanitize_text_field($_POST['partita_iva']));
	}
}


// Save Billing partita_iva field value as user meta data
add_action('woocommerce_checkout_update_customer', 'save_account_partita_iva_field', 10, 2);
function save_account_partita_iva_field($customer, $data)
{
	if (isset($_POST['partita_iva']) && !empty($_POST['partita_iva'])) {
		$customer->update_meta_data('partita_iva', sanitize_text_field($_POST['partita_iva']));
	}
}

// Admin orders Billing partita_iva editable field and display
add_filter('woocommerce_admin_billing_fields', 'admin_order_partita_iva_editable_field');
function admin_order_partita_iva_editable_field($fields)
{
	$fields['partita_iva'] = array('label' => __('P.IVA', 'woocommerce'));

	return $fields;
}

// WordPress User: Add Billing partita_iva editable field
add_filter('woocommerce_customer_meta_fields', 'wordpress_user_account_partita_iva_field');
function wordpress_user_account_partita_iva_field($fields)
{
	$fields['billing']['fields']['partita_iva'] = array(
		'label' => __('P.IVA', 'woocommerce'),
		'description' => __('', 'woocommerce')
	);
	return $fields;
}

//inserisco il codice fiscale nel back end
add_action('woocommerce_admin_order_data_after_billing_address', 'codice_fiscale_order_meta_admin', 10, 1);

function codice_fiscale_order_meta_admin($order)
{
	echo '<p><strong>' . __('Codice Fiscale') . ':</strong> ' . get_post_meta($order->get_id(), 'Codice Fiscale', true) . '</p>';
}

//inserisco il codice fiscale nella mail dell'ordine
add_filter('woocommerce_email_order_meta_keys', 'my_custom_fiscale_order_meta_keys');

function my_custom_fiscale_order_meta_keys($keys)
{
	$keys[] = 'Codice Fiscale';
	return $keys;
}

//inserisco la partita iva nel back end
add_action('woocommerce_admin_order_data_after_billing_address', 'partita_iva_order_meta_admin', 10, 1);

function partita_iva_order_meta_admin($order)
{
	echo '<p><strong>' . __('Partita Iva') . ':</strong> ' . get_post_meta($order->get_id(), 'Partita Iva', true) . '</p>';
}

//inserisco la partita iva nella mail dell'ordine
add_filter('woocommerce_email_order_meta_keys', 'my_custom_partita_iva_order_meta_keys');

function my_custom_partita_iva_order_meta_keys($keys)
{
	$keys[] = 'Partita Iva';
	return $keys;
}


//Display WooCommerce Admin Custom Order Fields
add_action('woocommerce_admin_order_data_after_shipping_address', 'admin_order_after_billing_address_callback', 10, 1);
function admin_order_after_billing_address_callback($order)
{
	if ($tiva1 = $order->get_meta('_shipping_scala')) {
		echo '<p><strong>' . __("Scala") . ':</strong> ' . $tiva1 . '</p>';
	} else {
		echo '<p><strong>' . __("Scala") . ':</strong>-</p>';
	}
	if ($tfcarr = $order->get_meta('_shipping_piano')) {
		echo '<p><strong>' . __("Piano") . ':</strong> ' . $tfcarr . '</p>';
	} else {
		echo '<p><strong>' . __("Piano") . ':</strong>-</p>';
	}
	if ($tfcitofono = $order->get_meta('_shipping_citofono')) {
		echo '<p><strong>' . __("Citofono e note") . ':</strong><br/> ' . $tfcitofono . '</p>';
	} else {
		echo '<p><strong>' . __("Citofono e note") . ':</strong>-</p>';
	}
	if ($tfdogname = $order->get_meta('_cloudways_dog_name_field')) {
		echo '<p><strong>' . __("Nome del cane") . ':</strong> ' . $tfdogname . '</p>';
	} else {
		echo '<p><strong>' . __("Nome del cane") . ':</strong>-</p>';
	}
}


// Imposto gli attributi in modo che vengano salvati nel profilo utente:
function cf_checkout_update_user_meta($customer_id, $posted)
{
	if (isset($posted['partita_iva'])) {
		$partita_iva = sanitize_text_field($posted['partita_iva']);
		update_user_meta($customer_id, 'partita_iva', $partita_iva);
	}
	if (isset($posted['codice_fiscale'])) {
		$codice_fiscale = sanitize_text_field($posted['codice_fiscale']);
		update_user_meta($customer_id, 'codice_fiscale', $codice_fiscale);
	}

}

add_action('woocommerce_checkout_update_user_meta', 'cf_checkout_update_user_meta', 10, 2);

//validazione del codice fiscale
function required_cf_checkout_field_process()
{
	if ($_POST['codice_fiscale'] && !codiceFiscale($_POST['codice_fiscale']))
		wc_add_notice(__('Devi inserire un codice fiscale valido per inoltrare l\'ordine.'), 'error');
}

/** controllo del codice fiscale **/
function codiceFiscale($cf)
{

	if ($cf == '')
		return false;

	if (strlen($cf) != 16)
		return false;

	$cf = strtoupper($cf);
	if (!preg_match("/[A-Z0-9]+$/", $cf))
		return false;

	$s = 0;

	for ($i = 1; $i <= 13; $i += 2) {
		$c = $cf[$i];
		if ('0' <= $c and $c <= '9')
			$s += ord($c) - ord('0');
		else
			$s += ord($c) - ord('A');
	}

	for ($i = 0; $i <= 14; $i += 2) {
		$c = $cf[$i];
		switch ($c) {
			case '0':
				$s += 1;
				break;
			case '1':
				$s += 0;
				break;
			case '2':
				$s += 5;
				break;
			case '3':
				$s += 7;
				break;
			case '4':
				$s += 9;
				break;
			case '5':
				$s += 13;
				break;
			case '6':
				$s += 15;
				break;
			case '7':
				$s += 17;
				break;
			case '8':
				$s += 19;
				break;
			case '9':
				$s += 21;
				break;
			case 'A':
				$s += 1;
				break;
			case 'B':
				$s += 0;
				break;
			case 'C':
				$s += 5;
				break;
			case 'D':
				$s += 7;
				break;
			case 'E':
				$s += 9;
				break;
			case 'F':
				$s += 13;
				break;
			case 'G':
				$s += 15;
				break;
			case 'H':
				$s += 17;
				break;
			case 'I':
				$s += 19;
				break;
			case 'J':
				$s += 21;
				break;
			case 'K':
				$s += 2;
				break;
			case 'L':
				$s += 4;
				break;
			case 'M':
				$s += 18;
				break;
			case 'N':
				$s += 20;
				break;
			case 'O':
				$s += 11;
				break;
			case 'P':
				$s += 3;
				break;
			case 'Q':
				$s += 6;
				break;
			case 'R':
				$s += 8;
				break;
			case 'S':
				$s += 12;
				break;
			case 'T':
				$s += 14;
				break;
			case 'U':
				$s += 16;
				break;
			case 'V':
				$s += 10;
				break;
			case 'W':
				$s += 22;
				break;
			case 'X':
				$s += 25;
				break;
			case 'Y':
				$s += 24;
				break;
			case 'Z':
				$s += 23;
				break;
		}
	}

	if (chr($s % 26 + ord('A')) != $cf[15])
		return false;

	return true;
}


//Sconto primo ordine
//add_filter('woocommerce_package_rates', 'free_first_order_shipping', 20, 2);
function free_first_order_shipping($rates, $package)
{

	$lastDayCreatedOrdersFN = getLastDeliveryDay();
	// New shipping cost (can be calculated)
	$has_free_shipping = false;

	$tax_rate = 0;
	$new_cost = 0;
	$label = 'Gratuita';

	$loggedUser = is_user_logged_in();
	$orders = [];

	$totalOrders = 0;
	$orderData = $_POST;

	$has_sub = null;

	if ($loggedUser) {
		$current_user = wp_get_current_user();

		$has_sub = wcs_get_subscriptions([
			"subscriptions_per_page" => 1,
			"orderby" => "ID",
			"order" => "DESC",
			"subscription_status" => ["active"],
			"customer_id" => $current_user->ID,
		]);
		$has_sub = reset($has_sub);

		if ($has_sub) {
			//check if calendar enabled
			$lastOrderWeek = get_option("current_order_week", true);
			$lastOrderWeek = explode("_", $lastOrderWeek);
			$lastOrderWeek = end($lastOrderWeek);

			$disabledWeeks = get_post_meta($has_sub->get_id(), "disable_weeks_" . (new \DateTime())->format("Y"), true);

			if (is_array($disabledWeeks) && in_array($lastOrderWeek, $disabledWeeks)) {
				$has_sub = null;
			}

		}

		$user_id = get_current_user_id();
		$args = array(
			'customer_id' => $user_id,
			'limit' => 1,
			'status' => "completed",
			"meta_key" => "_date_completed",
			"meta_value" => $lastDayCreatedOrdersFN->getTimestamp(),
			"meta_compare" => ">",
		);
		$orders = wc_get_orders($args);


		$args = array(
			'customer_id' => $user_id,
			'limit' => 1,
			'status' => "completed",
		);
		$totalOrders = wc_get_orders($args);
		$totalOrders = count($totalOrders);

	} else {
		//check old orders

		if (isset($orderData['s_postcode'])) {

			$capOrdersQuery = wc_get_orders([
				"limit" => -1,
				"status" => "completed",
				"meta_key" => "_shipping_postcode",
				"meta_value" => $orderData['s_postcode'],
				"meta_compare" => "=",
			]);

			foreach ($capOrdersQuery as $order) {

				$newOrderAddress = trim(str_replace(['via', 'piazza'], "", strtolower($orderData['s_address'])));
				$oldOrderAddress = trim(str_replace(['via', 'piazza'], "", strtolower($order->get_shipping_address_1())));


				if ($newOrderAddress == $oldOrderAddress) {

					$totalOrders++;

					if ($order->get_date_completed() >= $lastDayCreatedOrdersFN) {
						$orders[] = $order;

					}
				}

			}
		}

	}

	$buying_sub = false;


	if (WC()->cart) {
		$productsCart = array_column(WC()->cart->get_cart(), 'product_id');

		foreach ($productsCart as $item) {
			$categories = get_the_terms($item, "product_cat");
			foreach ($categories as $term) {
				if (in_array($term->slug, ["box"])) {
					$buying_sub = true;
				}
			}
		}
	}


	if ($loggedUser && $has_sub && !$buying_sub) {
		$has_free_shipping = true;
	}

	if (empty($orders) && $totalOrders == 0) {
		$has_free_shipping = true;
		$label = 'Gratuita per il primo ordine';

		if ($buying_sub && isset($orderData['s_state']) && !in_array($orderData['s_state'], ['AT', 'CN'])) {
			$label .= ', poi 5€';
		}

	}


	if (WC()->cart && (in_array('WELOVEDENSO', WC()->cart->get_applied_coupons()) || in_array('WELOVEDENSO10', WC()->cart->get_applied_coupons()))) {
		$has_free_shipping = true;
	}


	if ($has_free_shipping) {

		foreach ($rates as $rate_key => $rate) {

			// Excluding free shipping methods
			if ($rate->method_id != 'free_shipping') {

				// Set rate cost
				$rates[$rate_key]->cost = $new_cost;
				//Set shipping label
				$rates[$rate_key]->label = $label;

				// Set taxes rate cost (if enabled)
				$taxes = array();
				foreach ($rates[$rate_key]->taxes as $key => $tax) {
					if ($rates[$rate_key]->taxes[$key] > 0)
						$taxes[$key] = $new_cost * $tax_rate;
				}
				$rates[$rate_key]->taxes = $taxes;

			}
		}

	}


	return $rates;
}

//Aggiungi Spedizione solo se è recurrent
add_filter('woocommerce_subscriptions_is_recurring_fee', '__return_true');
add_filter('woocommerce_cart_calculate_fees', 'add_recurring_postage_fees', 10, 1);

function add_recurring_postage_fees($cart)
{

	if (!empty($cart->recurring_cart_key)) {
		if (WC()->cart && in_array('WELOVEDENSO', WC()->cart->get_applied_coupons()) || WC()->cart && in_array('WELOVEDENSO10', WC()->cart->get_applied_coupons())) {
			//$intervals = explode( '_', $cart->recurring_cart_key );
			$cart->add_fee('Consegna', 0, false, '');

			// $coupon_code = 'SCONTODENSO';
			// WC()->cart->apply_coupon( $coupon_code );

		} else {
			//$intervals = explode( '_', $cart->recurring_cart_key );
			$cart->add_fee('Consegna', 5, false, '');
		}


	}
}

/*
function my_woocommerce_apply_cart_coupon_in_url()
{
	// Return early if WooCommerce or sessions aren't available.
	if (!function_exists('WC') || !WC()->session) {
		return;
	}

	// Return if there is no coupon in the URL, otherwise set the variable.
	if (empty($_REQUEST['coupon'])) {
		return;
	} else {
		$coupon_code = esc_attr($_REQUEST['coupon']);
	}

	// Set a session cookie to remember the coupon if they continue shopping.
	WC()->session->set_customer_session_cookie(true);

	foreach (WC()->cart->get_coupons() as $code => $coupon) {
		WC()->cart->remove_coupon($code);
	}


	// Apply the coupon to the cart if necessary.
	WC()->cart->add_discount($coupon_code);

	$buying_sub = false;
	$box_product_id = 50;
	if (in_array($box_product_id, array_column(WC()->cart->get_cart(), 'product_id'))) {
		$buying_sub = true;
	}

	if (!$buying_sub) {
		WC()->cart->add_discount($coupon_code . '10');
	}

}*/

//add_action('wp_loaded', 'my_woocommerce_apply_cart_coupon_in_url', 30);
//add_action('woocommerce_add_to_cart', 'my_woocommerce_apply_cart_coupon_in_url');


function storeapps_wc_sc_max_url_coupons_limit($limit = 5)
{
	$limit = 10;
	return $limit;
}

function storeapps_handle_smart_coupons_hooks()
{
	add_filter('wc_sc_max_url_coupons_limit', 'storeapps_wc_sc_max_url_coupons_limit', 11);
}

add_action('wp_loaded', 'storeapps_handle_smart_coupons_hooks');


add_filter('woocommerce_available_payment_gateways', 'bbloomer_unset_gateway_by_category');

function bbloomer_unset_gateway_by_category($available_gateways)
{
	if (is_admin()) return $available_gateways;
	if (!is_checkout()) return $available_gateways;

	if (get_site_url() != 'https://www.agrispesa.it') {
		return $available_gateways;
	}

	/*
	$isBonificoEnabled = false;

	foreach (WC()->cart->get_cart_contents() as $values) {
		$categories = get_the_terms($values['product_id'], 'product_cat');
		foreach ($categories as $term) {
			if (in_array($term->slug, ["box"])) {
				$isBonificoEnabled = true;
			}
		}
	}

	if (!$isBonificoEnabled) unset($available_gateways['bacs']); // DISABLE COD IF CATEGORY IS IN THE CART
	*/
	return $available_gateways;
}


add_filter('woocommerce_add_to_cart_validation', 'filter_wc_add_to_cart_validation', 10, 3);
function filter_wc_add_to_cart_validation($passed, $product_id, $quantity)
{
	$is_virtual = $is_physical = false;
	$product = wc_get_product($product_id);

	if ($product->is_virtual()) {
		$is_virtual = true;
	} else {
		$is_physical = true;
	}

	// Loop though cart items
	foreach (WC()->cart->get_cart() as $cart_item) {
		// Check for specific product categories
		if (($cart_item['data']->is_virtual() && $is_physical)
			|| (!$cart_item['data']->is_virtual() && $is_virtual)) {
			wc_add_notice(__("You can't combine physical and virtual products together.", "woocommerce"), 'error');
			return false;
		}
	}

	return $passed;
}

// For security: check cart items and avoid checkout
add_action('woocommerce_check_cart_items', 'filter_wc_check_cart_items');
function filter_wc_check_cart_items()
{
	$canBuySubscription = true;

	$skipSubscriptionCheck = WC()->session->get('skip_check_subscription');

	if (!$skipSubscriptionCheck) {
		$current_user = wp_get_current_user();


		if ($current_user) {
			$has_sub = wcs_user_has_subscription($current_user->ID, '', 'active');
			if (!$has_sub) {
				$has_sub = wcs_user_has_subscription($current_user->ID, '', 'on-hold');
			}

			if ($has_sub) {
				foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

					$categories = get_the_terms($cart_item['product_id'], "product_cat");
					foreach ($categories as $term) {
						if (in_array($term->slug, ["box"]) && $has_sub) {
							$canBuySubscription = false;
							WC()->cart->remove_cart_item($cart_item_key);
						}
					}

				}
			}


			if (!$canBuySubscription) {
				wc_add_notice('Hai già un abbonamento, elimina il vecchio abbonamento dalla tua area riservata per poterne effettuare uno nuovo.', 'error');
			}
		}
	}


}


add_action('woocommerce_checkout_subscription_created', function ($subscription) {

	//suspend all old subscription of the customer
	$subscriptions = wcs_get_subscriptions([
		"subscriptions_per_page" => -1,
		"orderby" => "ID",
		"order" => "ASC",
		'customer_id' => $subscription->get_customer_id(),
		"subscription_status" => "any"]);

	if (!empty($subscriptions)) {
		foreach ($subscriptions as $oldSubscription) {
			if ($oldSubscription->get_id() != $subscription->get_id()) {
				$oldSubscription->set_status('cancelled');
				$oldSubscription->save();

				$years = [date('Y'), date('Y') + 1];

				foreach ($years as $year) {
					$disableWeeks = get_post_meta($oldSubscription->get_id(), 'disable_weeks_' . $year, true);
					if ($disableWeeks) {
						update_post_meta($subscription->get_id(), 'disable_weeks_' . $year, $disableWeeks);
					}
				}

			}
		}
	}

	$paymentMethod = $subscription->get_payment_method();

	//change reneval date
	// get next $consegneNameDay from today


	if ($paymentMethod == 'bacs' || $paymentMethod == 'satispay') {
		$subscription->set_status('active');
		$subscription->set_billing_period('year');
		$subscription->set_billing_interval(100);
		$subscription->update_dates([
			'next_payment' => '2100-01-01 00:00:00'
		]);

		$subscription->set_requires_manual_renewal(true);
	} else {

		$nextRenew = new DateTime("next Wednesday");
		$now = new DateTime();
		if ($now->format('D') != 'Wed') {
			$nextRenew->add(new DateInterval('P1W'));
		}

		$nextRenew->setTimezone(new DateTimeZone('Europe/Rome'));
		$nextRenew->setTime(12, 0, 0);

		$nextPayment = $nextRenew->format('Y-m-d H:i:s');
		$paymentInterval = 1;
		$paymentPeriod = 'week';

		$subscription->set_billing_period($paymentPeriod);
		$subscription->set_billing_interval($paymentInterval);
		$subscription->update_dates([
			'next_payment' => $nextPayment
		]);
	}
	$subscription->save();

}, 100);


add_action('woocommerce_email_after_order_table', 'mm_email_after_order_table', 10, 4);
function mm_email_after_order_table($order, $sent_to_admin, $plain_text, $email)
{
	$orderType = get_order_type($order->get_id());
	if ($orderType != 'CREDITO') {
		echo "<p>Ricorda che questa mail e il documento che trovi all'interno della tua scatola non sono la Fattura ma la Conferma d'ordine.<br>
<u>Potrai quindi riscontrare differenze tra gli importi previsti dalla Conferma d'ordine e quelli della Fattura.</u><br>
Questo perchè, quando riempiremo la scatola, peseremo ad uno ad uno i diversi prodotti e verificheremo il peso esatto di ciascuno di essi.<br>
Poi perfezioneremo l’ordine ed emetteremo la Fattura in modo che tu possa pagare esattamente quanto ricevuto.<br>
<u>È dunque probabile che l’importo effettivo della Fattura si possa discostare un po' da quanto ti propone la Conferma d'ordine.</u> </p>";
	}

}
