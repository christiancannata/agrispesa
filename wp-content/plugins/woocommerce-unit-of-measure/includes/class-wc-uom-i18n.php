<?php
/**
 * The includes are included for WooCommerce RRP.
 *
 * @author     Bradley Davis
 * @package    WooCommerce_RRP
 * @subpackage WooCommerce_RRP/includes
 * @since      3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly.
endif;

/**
 * Includes parent class that pulls everything together.
 *
 * @since 3.0.0
 */
class Wc_Uom_I18n {
	/**
	 * The Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->wc_uom_textdomain_activate();
	}

	/**
	 * Add all filter type actions.
	 *
	 * @since 3.0.0
	 */
	public function wc_uom_textdomain_activate() {
		add_action( 'init', array( $this, 'wc_uom_textdomain' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function wc_uom_textdomain() {
		load_plugin_textdomain(
			'woocommerce-uom',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}

$wc_uom_i18n = new Wc_Uom_I18n();
