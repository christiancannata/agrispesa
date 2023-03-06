<?php
/**
 * Checkout Field - FIle
 *
 * @author    ThemeLocation
 * @category  Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Checkout_Field_File')):

class WCFE_Checkout_Field_File extends WCFE_Checkout_Field{
	public $maxsize = false;
	public $accept = false;
	public $upload_type = false;
	
	public function __construct() {
		$this->type = 'file';
	}	
}

endif;