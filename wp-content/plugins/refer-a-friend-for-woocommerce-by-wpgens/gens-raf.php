<?php

/**
 * Refer A Friend by WPGens Plugin
 *
 * @link              http://wpgens.com
 * @since             1.0.0
 * @package           Gens_RAF
 *
 * @wordpress-plugin
 * Plugin Name:       Refer A Friend for WooCommerce by WPGens
 * Plugin URI:        http://wpgens.com
 * Description:       Simple yet powerful referral system for WooCommerce. Each customer has referral link that rewards them with a coupon after someone makes a purchase through their link. Check premium version for more features.
 * Version:           1.2.3
 * Author:            Goran Jakovljevic
 * Author URI:        http://wpgens.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gens-raf
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gens-raf.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gens_raf() {

	$plugin = new Gens_RAF();
	$plugin->run();

}

// Need to run after Woo has been loaded
add_action( 'plugins_loaded', 'run_gens_raf' );
