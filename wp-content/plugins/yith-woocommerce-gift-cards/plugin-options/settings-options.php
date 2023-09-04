<?php
/**
 * Settings options
 *
 * @package YITH\Booking\Options
 */

defined( 'ABSPATH' ) || exit();

$sub_tabs = array(
	'settings-general' => array(
		'title'              => _x( 'General', 'Tab title in plugin settings panel', 'yith-woocommerce-gift-cards' ),
		'yith-ywgc-priority' => 20,
		'description'        => __( 'Configure the plugin general settings.', 'yith-woocommerce-gift-cards' ),
	),
	'settings-gift-card-categories' => array(
		'title'              => _x( 'Gift card categories', 'Tab title in plugin settings panel', 'yith-woocommerce-gift-cards' ),
		'yith-ywgc-priority' => 20,
		'description'        => __( 'Configure the gift card categories.', 'yith-woocommerce-gift-cards' ),
	),
);

$sub_tabs = apply_filters( 'yith_ywgc_panel_settings_sub_tabs', $sub_tabs );

$options = array(
	'settings' => array(
		'settings-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => $sub_tabs,
		),
	),
);

return apply_filters( 'yith_ywgc_panel_settings_options', $options );
