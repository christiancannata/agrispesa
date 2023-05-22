<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://christiancannata.acom
 * @since      1.0.0
 *
 * @package    Fiscal_Code_Validator_Woocommerce
 * @subpackage Fiscal_Code_Validator_Woocommerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Fiscal_Code_Validator_Woocommerce
 * @subpackage Fiscal_Code_Validator_Woocommerce/includes
 * @author     Christian Cannata <christian@christiancannata.com>
 */
class Fiscal_Code_Validator_Woocommerce_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'fiscal-code-validator-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
