<?php
/*
 * Plugin Name: Italy City and Postcode For Woocommerce Pro
 * Plugin URI: https://4wp.it
 * Description: Selettore delle città italiane con rilascio automatico del c.a.p versione pro
 * Version: 1.0.0
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Author: Roberto Bottalico
 * Author URI: https://4wp.it/chi-sono
 * License: Commerciale
 *
 * Text Domain: italy-city-and-postcode-for-woocommerce-pro
 * Domain Path: /languages
 *
 * WC requires at least: 5
 * WC tested up to: 6.3.1
*/

function_exists('plugin_dir_url') or exit('No direct script access allowed');



define('ICAPFWPRO_WOO_VERSION', '1.0.0');
define('ICAPFWPRO_WOO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ICAPFWPRO_WOO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ICAPFWPRO_WOO_PLUGIN_BASENAME', plugin_basename(__FILE__));

require_once(ICAPFWPRO_WOO_PLUGIN_DIR . 'class.icapfwpro-woo.settings.php');
include( 'multi-postcode/multi-postcodeIT.php' );
include( 'multi-postcode/IT.php' );



