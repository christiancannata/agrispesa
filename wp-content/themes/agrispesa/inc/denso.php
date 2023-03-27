<?php

//Denso: apri form shipping e compila i dati
add_filter( 'woocommerce_shipping_fields', 'custom_checkout_billing_city_field', 10, 1 );
function custom_checkout_billing_city_field( $shipping_fields ) {
	if( in_array( 'welovedenso', WC()->cart->get_applied_coupons() ) ) {
		add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
		global $woocommerce;

		$company = 'Denso Thermal Systems S.p.A.';
    $city = 'Poirino';
		$postcode = '10046';
		$address = 'Frazione Masio, 24';
		$state = 'TO';

    // Set values (to be sure)
    $woocommerce->customer->set_shipping_address( $address );
		$woocommerce->customer->set_shipping_postcode( $postcode );
    $woocommerce->customer->set_shipping_city( $city );
    $woocommerce->customer->set_shipping_company( $company );
		$woocommerce->customer->set_shipping_state( $state );

    // Change fields
    $shipping_fields['shipping_address_1']['default'] = $address;
    $shipping_fields['shipping_postcode']['default'] = $postcode;
    $shipping_fields['shipping_city']['default'] = $city;
    $shipping_fields['shipping_company']['default'] = $company;
    $shipping_fields['shipping_state']['default'] = $state;

    $shipping_fields['shipping_address_1']['custom_attributes']['readonly'] = 'readonly';
    $shipping_fields['shipping_postcode']['custom_attributes']['readonly'] = 'readonly';
    $shipping_fields['shipping_city']['custom_attributes']['readonly'] = 'readonly';
    $shipping_fields['shipping_company']['custom_attributes']['readonly'] = 'readonly';
    //$shipping_fields['shipping_state']['custom_attributes']['readonly'] = 'readonly';

		// $shipping_fields['shipping_state']['type'] = 'select';
    // $shipping_fields['shipping_state']['options'] = array( $state => $state );
    // $shipping_fields['shipping_state']['default'] = $state;
    //$shipping_fields['shipping_state']['custom_attributes']['disabled'] = 'disabled';

    return $shipping_fields;
	}
}

//Se il cliente usa il codice, salva il dato
class Custom_class{
	public function __construct(){
		add_action('woocommerce_checkout_order_processed', array($this, 'checkout_processed'), 10, 3);
	}

	public function checkout_processed($order_id, $posted_data, $order){

		// get all coupons in cart
		$cc = $order->get_items('coupon');

		// if no coupons used return
		if( !$cc ) return;

		// run through for each coupons used in cart
		foreach($cc  as $cc1){

			if($cc1->get_code() == 'welovedenso'){

				// save to user meta
				update_user_meta( $this->current_user->ID , 'welovedenso_coupon_used', 'welovedenso');
			}
		}
	}
}
new Custom_class();
