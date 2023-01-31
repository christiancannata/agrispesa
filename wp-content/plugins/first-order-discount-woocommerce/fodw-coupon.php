<?php
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

add_action( 'woocommerce_before_cart', 'fodw_apply_discount' );
//add_action( 'woocommerce_checkout_init', 'fodw_apply_discount' );

function fodw_apply_discount() {
    
    global $wpdb;

    if(is_null(WC()->session)) {
        return;
    }

    $strData = get_option('_fodw_configuration');
    $arrData = unserialize($strData);

    $isFreeProductAdded = WC()->session->__get('fodwp_free_product_added');

    // if disabled, then don't do anything
    if($arrData['type'] == 'disable' || (isset($arrData['autoApplyGuest']) && ($arrData['autoApplyGuest'] == 'no' || $arrData['autoApplyGuest'] == '') && !get_current_user_id()) || (isset($isFreeProductAdded) && $isFreeProductAdded)) {
        return;
    }

    if(fodw_has_bought()) {
    	return;
    }

    $productInCart = false;
    foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
        $_product = $values['data'];
    
        if( $arrData['freeProduct'] == $_product->get_id() ) {
            $productInCart = true;
        }
    }
    $couponId = get_option('_fodw_coupon_id');
    // Get coupon code
    $strCoupon = "SELECT post_title FROM {$wpdb->prefix}posts WHERE ID = '" . $couponId . "'";
    $arrCoupon = $wpdb->get_results($strCoupon);
    $coupon_code = $arrCoupon[0]->post_title; 
    $coupon = new WC_Coupon($couponId);

    $users = $coupon->get_used_by();
    $user = wp_get_current_user();

    // if coupon already applied
    if((isset(WC()->cart->applied_coupons) && !empty(WC()->cart->applied_coupons)) || !$coupon->is_valid() || (isset($user) && !empty($user) && in_array($user->user_email, $users)))  {
        return;
    }

    // Free shipping, fixed discount & % discount will be handled here 
    WC()->cart->add_discount( $coupon_code );
    WC()->session->__set('fodw_coupon_added', true);
 
}

/*
 * This function will check if customer has purchased any product.
 * Date: 17-08-2017
 * Author: Vidish Purohit
 */
function fodw_has_bought() {

    $count = 0;
    $bought = false;

    if(!get_current_user_id()) {
        return false;
    }

    // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => 'shop_order', // WC orders post type
        'post_status' => array('wc-completed', 'wc-in-progress', 'in-progress') // Only orders with status "completed" & "In  Progress"
    ) );

    // Going through each current customer orders
    foreach ( $customer_orders as $customer_order ) {
        $count++;
    }

    // return "true" when customer has already one order
    if ( $count > 0 ) {
        $bought = true;
    }
    return $bought;
}