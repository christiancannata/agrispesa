<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.linkedin.com/in/stratos-vetsos-08262473/
 * @since      1.0.0
 *
 * @package    Wc_Order_Navigation
 * @subpackage Wc_Order_Navigation/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wc_Order_Navigation
 * @subpackage Wc_Order_Navigation/includes
 * @author     FullStack <vetsos.s@gmail.com>
 */
class Wc_Order_Navigation_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wc-order-navigation',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
