<?php
/**
 * Plugin Name: Flexible Shipping
 * Plugin URI: https://wordpress.org/plugins/flexible-shipping/
 * Description: Create additional shipment methods in WooCommerce and enable pricing based on cart weight or total.
 * Version: 4.25.1
 * Author: Octolize
 * Author URI: https://octol.io/fs-author
 * Text Domain: flexible-shipping
 * Domain Path: /lang/
 * Requires at least: 5.8
 * Tested up to: 6.5
 * WC requires at least: 8.6
 * WC tested up to: 9.0
 * Requires PHP: 7.4
 * ​
 * Copyright 2017 WP Desk Ltd.
 * ​
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * ​
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * ​
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

defined( 'ABSPATH' ) || exit;

/* THIS VARIABLE CAN BE CHANGED AUTOMATICALLY */
$plugin_version = '4.25.1';

$plugin_name        = 'Flexible Shipping';
$plugin_class_name  = Flexible_Shipping_Plugin::class;
$plugin_text_domain = 'flexible-shipping';
$product_id         = 'Flexible Shipping';
$plugin_file        = __FILE__;
$plugin_dir         = __DIR__;
$plugin_shops       = [
	'default' => 'https://octolize.com/',
];

define( 'FLEXIBLE_SHIPPING_VERSION', $plugin_version );
define( $plugin_class_name, $plugin_version );

$requirements = [
	'php'          => '7.4',
	'wp'           => '5.8',
	'repo_plugins' => [
		[
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
			'version'   => '6.6',
		],
	],
];

if ( interface_exists( \Psr\Log\LoggerInterface::class ) || interface_exists( \Psr\Log\LoggerAwareInterface::class ) ) {
	interface_exists( \Psr\Log\LoggerAwareInterface::class );
	class_exists( \Psr\Log\AbstractLogger::class );
	class_exists( \Psr\Log\NullLogger::class );
	trait_exists( \Psr\Log\LoggerAwareTrait::class );
	trait_exists( \Psr\Log\LoggerTrait::class );
}

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
	}
} );

require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow-common/src/plugin-init-php52-free.php';
