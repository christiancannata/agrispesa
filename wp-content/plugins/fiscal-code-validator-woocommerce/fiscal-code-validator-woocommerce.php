<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://christiancannata.acom
 * @since             1.0.0
 * @package           Fiscal_Code_Validator_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Validazione del codice fiscale per WooCommerce
 * Plugin URI:        https://christiancannata.acom
 * Description:       Valida il codice fiscale in fase di checkout per gli acquisti con WooCommerce
 * Version:           1.0.0
 * Author:            Christian Cannata
 * Author URI:        https://christiancannata.acom
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fiscal-code-validator-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('FISCAL_CODE_VALIDATOR_WOOCOMMERCE_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fiscal-code-validator-woocommerce-activator.php
 */
function activate_fiscal_code_validator_woocommerce()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-fiscal-code-validator-woocommerce-activator.php';
    Fiscal_Code_Validator_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fiscal-code-validator-woocommerce-deactivator.php
 */
function deactivate_fiscal_code_validator_woocommerce()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-fiscal-code-validator-woocommerce-deactivator.php';
    Fiscal_Code_Validator_Woocommerce_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_fiscal_code_validator_woocommerce');
register_deactivation_hook(__FILE__, 'deactivate_fiscal_code_validator_woocommerce');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-fiscal-code-validator-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fiscal_code_validator_woocommerce()
{

    $plugin = new Fiscal_Code_Validator_Woocommerce();
    $plugin->run();

}

run_fiscal_code_validator_woocommerce();
