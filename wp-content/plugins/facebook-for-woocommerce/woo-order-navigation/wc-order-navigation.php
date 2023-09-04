<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.linkedin.com/in/stratos-vetsos-08262473/
 * @since             1.0.0
 * @package           Wc_Order_Navigation
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Order Navigation
 * Plugin URI:
 * Description:       Provides a simple interface for administrators and shop managers, help them to navigate through WooCommerce Orders inside order edit screen.
 * Version:           1.1
 * Author:            woosmartcod.com
 * Author URI:        https://woosmartcod.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-order-navigation
 * Domain Path:       /languages
 * WC requires at least: 2.2
 * WC tested up to: 6.3.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-order-navigation-activator.php
 */
function activate_wc_order_navigation() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-order-navigation-activator.php';
	Wc_Order_Navigation_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-order-navigation-deactivator.php
 */
function deactivate_wc_order_navigation() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-order-navigation-deactivator.php';
	Wc_Order_Navigation_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wc_order_navigation' );
register_deactivation_hook( __FILE__, 'deactivate_wc_order_navigation' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-order-navigation.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wc_order_navigation() {

	$plugin = new Wc_Order_Navigation();
	$plugin->run();

}
run_wc_order_navigation();
