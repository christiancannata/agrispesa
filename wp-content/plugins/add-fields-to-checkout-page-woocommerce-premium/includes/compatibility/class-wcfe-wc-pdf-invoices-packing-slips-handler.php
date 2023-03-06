<?php
if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_WC_PDF_Invoices_Packing_Slips_Handler')):

class WCFE_WC_PDF_Invoices_Packing_Slips_Handler extends WCFE_Checkout_Fields_Utils{
	public function __construct() {
		add_action( 'wpo_wcpdf_after_order_data', array($this, 'wcpdf_after_order_data'), 10, 2 );
	}

	public function wcpdf_after_order_data($template_type, $order) {
		if ($template_type == 'invoice') {
			$fields = $this->get_invoice_fields();
			$this->display_wcpdfips_custom_fields($order, $fields);
			
		}else if ($template_type == 'packing-slip') {
			$fields = $this->get_packing_slip_fields();
			$this->display_wcpdfips_custom_fields($order, $fields);
		}
	}
	
	public function display_wcpdfips_custom_fields($order, $fields) {
		if(is_array($fields) && !empty($fields)){
			$user_id = $order->get_user_id();
			
			$order_id = false;
			if($this->woo_version_check()){
				$order_id = $order->get_id();
			}else{
				$order_id = $order->id;
			}
			$is_nl2br = apply_filters('thwcfe_nl2br_custom_field_value', true);
			
			foreach($fields as $key => $field) {
				$type = isset($field['type']) && $field['type'] ? $field['type'] : 'text';
				$value = '';
				if($user_id && isset($field['user_meta']) && $field['user_meta']){
					$value = get_user_meta( $user_id, $key, true );
				} else if(isset($field['order_meta']) && $field['order_meta']){
					$value = get_post_meta( $order_id, $key, true );
				}

				if($type === 'file'){
					$value = WCFE_Checkout_Fields_Utils::get_file_display_name_order($value, false);
				} else{
					//$value = $this->get_option_text_from_value($field, $value);
					$value = is_array($value) ? implode(", ", $value) : $value;
				}
				
				//if($is_nl2br && $type === 'textarea'){
				if($is_nl2br){
					$value = nl2br($value);
				}

				if($value){
					echo '<tr><th>'. THWCFE_i18n::t($field['title']) .':</th><td>'. $value .'</td></tr>';
				}
			}
		}
	}
	
	public function get_invoice_fields(){
		return $this->get_fields('pdf_invoice_fields');
	}
	
	public function get_packing_slip_fields(){
		return $this->get_fields('pdf_packing_slip_fields');
	}
	
	public function get_fields($settings_name){
		$fields = array();
		$fields_str = $this->get_settings($settings_name);
		
		if(!empty($fields_str)){
			$fields_arr = explode(",", $fields_str);
			
			if(is_array($fields_arr) && !empty($fields_arr)){
				$sections = $this->get_checkout_sections();	
				
				if($sections){
					foreach($sections as $sname => $section){	
						$fieldset = THWCFE_Utils_Section::get_fields($section);
						
						if($fieldset && is_array($fieldset)){
							foreach($fieldset as $key => $field){
								if(THWCFE_Utils_Field::is_custom_field($field) && THWCFE_Utils_Field::is_enabled($field) && in_array($key, $fields_arr)){
									$nfield = array();
									$nfield['name'] = $field->get_property('name');
									$nfield['type'] = $field->get_property('type');
									$nfield['title'] = $field->get_property('title');
									$nfield['order_meta'] = $field->get_property('order_meta');
									$nfield['user_meta'] = $field->get_property('user_meta');
									
									$fields[$key] = $nfield;
								}
							}
						}
					}
				}
			}
		}
		return $fields;
	}

}

endif;