<?php
/**
 * Dashboard options
 *
 */

defined( 'ABSPATH' ) || exit(); // Exit if accessed directly.

$sub_tabs = array(
	'configuration-gift-card-categories' => array(
		'title'       => _x( 'Gift card categories', 'Tab title in plugin settings panel', 'yith-woocommerce-gift-cards' ),
		'description' => implode(
			'<br />',
			array(
				esc_html__( 'Create categories with designs for your different gift cards.', 'yith-woocommerce-gift-cards' ),
				esc_html__( 'These categories allow you to upload and organize images for your gift cards.', 'yith-woocommerce-gift-cards' ),
			)
		),
	),

);

$options = array(
	'configuration' => array(
		'configuration-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => apply_filters( 'yith_ywgc_panel_configuration_sub_tabs', $sub_tabs ),
		),
	),
);

return apply_filters( 'yith_ywgc_panel_configuration_options', $options );
