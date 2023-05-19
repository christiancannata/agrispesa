<?php
/**
 * Modules list.
 *
 */

defined( 'ABSPATH' ) || exit;

return array(
	'gift-this-product'          => array(
		'name'         => _x( 'Gift this product', 'Module name', 'yyith-woocommerce-gift-cards' ),
		'description'  => __( 'Enable this module to be able to generate gift cards from your products. With the "Gift this product" feature, your customers can buy a gift card with the same value as the product he likes and suggest the product to the recipient.', 'yith-woocommerce-gift-cards' ),
		'needs_reload' => true,
	),
);
