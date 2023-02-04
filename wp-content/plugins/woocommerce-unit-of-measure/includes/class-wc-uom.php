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
class Wc_Uom {

	/**
	 * The Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->wc_uom_requires();
	}

	/**
	 * Include any files from includes and parent files from admin and public.
	 *
	 * @since 3.0.0
	 */
	public function wc_uom_requires() {
		require_once plugin_dir_path( trailingslashit( dirname( __FILE__ ) ) ) . 'admin/class-wc-uom-admin.php';
		require_once plugin_dir_path( trailingslashit( dirname( __FILE__ ) ) ) . 'public/class-wc-uom-public.php';
	}

}

/**
 * Instantiate the class
 *
 * @since 2.0.0
 */
$wc_uom = new Wc_Uom();
