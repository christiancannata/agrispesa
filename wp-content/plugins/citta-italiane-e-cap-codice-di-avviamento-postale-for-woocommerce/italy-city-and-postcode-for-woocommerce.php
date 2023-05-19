<?php
/*
 * Plugin Name: Città italiane e cap codice di avviamento postale for Woocommerce
 * Plugin URI: https://4wp.it
 * Description: Scelta della città e cap automatico in fase di checkout.
 * Version: 1.0.3
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Author: Roberto Bottalico
 * Author URI: https://4wp.it/roberto-bottalico
 * License: GPLv2 or later
 *
 * Text Domain: italy-city-and-postcode-for-woocommerce
 * Domain Path: /languages
 *
 * WC requires at least: 5
 * WC tested up to: 6.3.1
*/

function_exists('plugin_dir_url') or exit('No direct script access allowed');

define('ICAPFW_WOO_VERSION', '1.0.0');
define('ICAPFW_WOO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ICAPFW_WOO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ICAPFW_WOO_PLUGIN_BASENAME', plugin_basename(__FILE__));

require_once(ICAPFW_WOO_PLUGIN_DIR . 'class.icapfw-woo.settings.php');
require_once(ICAPFW_WOO_PLUGIN_DIR . 'class.icapfw-woo.main.php');
include( 'field_fraction.php' );


$icapfw_plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$icapfw_plugin", 'icapfw_plugin_settings_link' );
function icapfw_plugin_settings_link($links) { 
  $settings_link = '<a href="/wp-admin/admin.php?page=wc-settings&tab=icapfw">Impostazioni</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

function icapfw_warning_notice(){
    global $pagenow;
    if ( $pagenow == 'plugins.php' ) {
    $user = wp_get_current_user();
    if ( in_array( 'administrator', (array) $user->roles ) ) {
    echo '<div id="go-pro-icapfw" class="notice notice-info is-dismissible">
          <p>Attiva la versione PRO di CITTA\' ITALIANE E C.A.P per Woocommerce da <a href="https://4wp.it/product/citta-italiane-e-cap-automatico-per-woocommerce/" target="_blank">qui</a> per ottenere il c.a.p anche per le città che hanno più cap.</p>
         </div>';
    }
}
}
add_action('admin_notices', 'icapfw_warning_notice');

if ( in_array( 'italy-city-and-postcode-for-woocommerce-pro/italy-city-and-postcode-for-woocommerce-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
remove_action('admin_notices', 'icapfw_warning_notice');
}

register_activation_hook(__FILE__, ['ICAPFW_WOO', 'plugin_activation']);
register_deactivation_hook(__FILE__, ['ICAPFW_WOO', 'plugin_deactivation']);

ICAPFW_WOO::init();
