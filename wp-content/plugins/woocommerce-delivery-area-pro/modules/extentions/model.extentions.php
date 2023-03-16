<?php
/**
 * Class: WDAP_Model_Extentions
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.0.9
 * @package Woocommerce Delivery Area Pro
 */

if ( ! class_exists( 'WDAP_Model_Extentions' ) ) {

	/**
	 * Display Extentions.
	 *
	 * @package Woocommerce Delivery Area Pro
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WDAP_Model_Extentions extends FlipperCode_Model_Base {
		/**
		 * Intialize constructor
		 */
		function __construct() {}
		
		/**
		 * Admin menu for Extention Display
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {		return array( 'wdap_manage_extentions' => esc_html__( 'Add-ons', 'woo-delivery-area-pro' ));	}
		
		function install() {}
		
	}
}
