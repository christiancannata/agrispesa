<?php
/**
 * Auto-loads the required dependencies for this plugin.
 *
 * @link       https://themelocation.com
 * @since      3.1.0
 *
 * @package    add-fields-to-checkout-page-woocommerce-premium
 * @subpackage add-fields-to-checkout-page-woocommerce-premium/includes
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWCFE_Autoloader')):

class THWCFE_Autoloader {
	private $include_path = '';

	private $compatibility_classes = array(
			'wcfe_checkout_fields_export_handler',
			'wcfe_wc_api_handler',
			'wcfe_wc_pdf_invoices_packing_slips_handler',
			'wcfe_wc_zapier_handler',
		);
	
	private $class_path = array(
		//'wcfe_settings' => 'classes/class-wcfe-settings.php',
		'wcfe_checkout_fields_utils' => 'classes/fe/class-wcfe-checkout-field-editor-utils.php',
	);
	
	
	public function __construct() {
		$this->include_path = untrailingslashit(THWCFE_PATH);
		
		if(function_exists("__autoload")){
			spl_autoload_register("__autoload");
		}
		spl_autoload_register(array($this, 'autoload'));
	}

	/** Include a class file. */
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			require_once( $path );
			return true;
		}
		return false;
	}
	
	/** Class name to file name. */
	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}
	
	public function autoload( $class ) {
		$class = strtolower( $class );
		$file  = $this->get_file_name_from_class( $class );
		$path  = '';
		$file_path  = '';

		if(isset($this->class_path[$class])){
			$file_path = $this->include_path . '/' . $this->class_path[$class];

		} elseif (in_array($class, $this->compatibility_classes)){
			$path = $this->include_path . '/includes/compatibility/';
			$file_path = $path . $file;

		} else {
			if (strpos($class, 'thwcfe_admin') === 0){
				$path = $this->include_path . '/admin/';

			} elseif (strpos($class, 'thwcfe_public') === 0){
				$path = $this->include_path . '/public/';

			} elseif (strpos($class, 'thwcfe_utils') === 0){
				$path = $this->include_path . '/includes/utils/';

			} elseif (strpos($class, 'wcfe_checkout_field') === 0){
				$path = $this->include_path . '/includes/model/fields/';

			} elseif (strpos($class, 'wcfe_condition') === 0){
				$path = $this->include_path . '/includes/model/rules/';

			} elseif (strpos($class, 'wcfe_checkout_section') === 0){
				$path = $this->include_path . '/includes/model/';

			} else{
				$path = $this->include_path . '/includes/';
			}
			$file_path = $path . $file;
		}
		
		if( empty($file_path) || (!$this->load_file($file_path) && strpos($class, 'thwcfe_') === 0) ) {
			$this->load_file( $this->include_path . $file );
		}
	}
}

endif;

new THWCFE_Autoloader();
