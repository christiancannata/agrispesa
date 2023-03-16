<?php
/**
 * Controller class
 *
 * @author Flipper Code<hello@flippercode.com>
 * @version 3.0.0
 * @package woo-delivery-area-pro
 */

if ( ! class_exists( 'WDAP_Model' ) ) {

	/**
	 * Controller class to display views.
	 *
	 * @author: Flipper Code<hello@flippercode.com>
	 * @version: 3.0.0
	 * @package: woo-delivery-area-pro
	 */

	class WDAP_Model extends Flippercode_Factory_Model {


		function __construct() {
						
			$page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : '';
			$module_path = WDAP_MODEL;
			$module_path = apply_filters('fc_modal_load_module', $module_path, $page);
			parent::__construct( $module_path, 'WDAP_Model_' );
			
		}

	}

}
