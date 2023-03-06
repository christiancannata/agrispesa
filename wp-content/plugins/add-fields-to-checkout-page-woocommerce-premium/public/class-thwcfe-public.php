<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://themelocation.com
 * @since      3.2.0
 *
 * @package    add-fields-to-checkout-page-woocommerce-premium
 * @subpackage add-fields-to-checkout-page-woocommerce-premium/public
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWCFE_Public')):
 
class THWCFE_Public extends WCFE_Checkout_Fields_Utils{
	public $plugin_name;
	public $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	
	public function define_public_hooks(){
		$advanced_settings = $this->get_advanced_settings();

		add_filter('woocommerce_localisation_address_formats', array($this, 'woo_localisation_address_formats'), 20, 2); 
		add_filter('woocommerce_formatted_address_replacements', array($this, 'woo_formatted_address_replacements'), 20, 2); 
		add_filter('woocommerce_order_formatted_billing_address', array($this, 'woo_order_formatted_billing_address'), 20, 2);
		add_filter('woocommerce_order_formatted_shipping_address', array($this, 'woo_order_formatted_shipping_address'), 20, 2);
		
		add_filter('woocommerce_form_field_hidden', array($this, 'woo_form_field_hidden'), 10, 4);
		add_filter('woocommerce_form_field_heading', array($this, 'woo_form_field_heading'), 10, 4);
		add_filter('woocommerce_form_field_label', array($this, 'woo_form_field_label'), 10, 4);
		add_filter('woocommerce_form_field_textarea', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_checkbox', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_checkboxgroup', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_password', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_text', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_email', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_tel', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_number', array($this, 'woo_form_field'), 10, 4);		
		add_filter('woocommerce_form_field_select', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_multiselect', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_radio', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_datepicker', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_timepicker', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_file', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_file_default', array($this, 'woo_form_field'), 10, 4);
		
		if($this->get_setting_value($advanced_settings, 'enable_conditions_country') === 'yes'){
			add_filter('woocommerce_form_field_country', array($this, 'woo_form_field'), 10, 4);
		}
		if($this->get_setting_value($advanced_settings, 'enable_conditions_state') === 'yes'){
			add_filter('woocommerce_form_field_state', array($this, 'woo_form_field'), 10, 4);
		}

		add_action('wp_ajax_thwcfe_file_upload', array($this, 'ajax_file_upload'));
		add_action('wp_ajax_nopriv_thwcfe_file_upload', array($this, 'ajax_file_upload'));

		add_action('wp_ajax_thwcfe_remove_uploaded', array($this, 'ajax_remove_uploaded'));
		add_action('wp_ajax_nopriv_thwcfe_remove_uploaded', array($this, 'ajax_remove_uploaded'));

		add_filter('thwcfe_form_field_wrapper_attributes', array($this, 'form_field_wrapper_attributes'), 10, 3);
	}
	
	public function wcfe_add_error($msg, $errors=false){
		if($errors){
			$errors->add('validation', $msg);
		}else if(defined('WC_VERSION') && version_compare(WC_VERSION, '2.3.0', '>=')){
			wc_add_notice($msg, 'error');
		} else {
			WC()->add_error($msg);
		}
	}
	
	public function prepare_address_fields($fieldset, $original = false, $sname = 'billing', $country){
		if(apply_filters('thwcfe_skip_address_fields_override_with_locale', false)){
			return $fieldset;
		}

		$locale = WC()->countries->get_country_locale();
		if(isset($locale[ $country ]) && is_array($locale[ $country ])) {
			foreach($locale[ $country ] as $key => $value){
				if(is_array($value) && isset($fieldset[$sname.'_'.$key])){
					if(isset($value['required'])){
						$fieldset[$sname.'_'.$key]['required'] = $value['required'];
					}
				}
			}
		}
		return $fieldset;
	}
	
	public function prepare_address_fields_my_account($fieldset, $original_fieldset = false, $sname = 'billing'){
		if(!empty($fieldset) && !empty($original_fieldset) && is_array($fieldset) && is_array($original_fieldset)){
			$priority = 0;
			foreach($original_fieldset as $okey => $ofield) {
				$priority = isset($ofield['priority']) && is_numeric($ofield['priority']) && $ofield['priority'] > $priority ? $ofield['priority'] : $priority;
			}
			
			foreach($fieldset as $key => $field) {
				$show = apply_filters('thwcfe_show_edit_address_form_field_'.$key, true, $sname, $field);
				
				if(isset($field['custom']) && $field['custom'] && $show){
					$priority += 10;
					$required = isset($field['required']) && $field['required'] ? true : false;
					$ftype = isset($field['type']) ? $field['type'] : 'text';
					$ftype = $ftype === 'hidden' ? 'text' : $ftype;
									
					$custom_field = array();
					$custom_field['type'] = $ftype;
					$custom_field['label'] = THWCFE_i18n::t($field['label']);
					$custom_field['placeholder'] = THWCFE_i18n::t($field['placeholder']);
					$custom_field['class'] = $field['class'];
					$custom_field['description'] = THWCFE_i18n::t($field['description']);
					$custom_field['label_class'] = $field['label_class'];
					$custom_field['input_class'] = $field['input_class'];
					//$custom_field['default'] = $field['default'];
					$custom_field['validate'] = $field['validate'];
					//$custom_field['required'] = $field['required'];
					$custom_field['required'] = isset($field['rules']) && !empty($field['rules']) ? false : $required;
					$custom_field['priority'] = $priority;
					$custom_field['user_meta'] = $field['user_meta'];
					
					if($ftype === 'select' || $ftype === 'multiselect' || $ftype === 'radio' || $ftype === 'checkboxgroup'){
						$custom_field['options'] = $field['options'];
						$custom_field['options_object'] = $field['options_object'];
					}else if($ftype === 'checkbox'){
						$custom_field['on_value'] = $field['on_value'];
						$custom_field['off_value'] = $field['off_value'];
					}
					
					if(isset($field['rules']) && !empty($field['rules'])){
						$custom_field['required'] = false;
						$custom_field['validate'] = '';
					}
										
					$original_fieldset[$key] = $custom_field;
				}
			}
		}
		return $original_fieldset;
	}

	public function validate_custom_my_account_field($field, $posted, $errors=false){
		$type = is_array($field) && isset($field['type']) ? $field['type'] : 'text';

		if($type === 'file'){
			$key = is_array($field) && isset($field['name']) ? $field['name'] : '';

			if(isset($_FILES[$key])){
				$file = $_FILES[$key];
				$result = $this->validate_file($field, $file);

				if(is_array($result) && $result['status'] === "ERROR"){
					$this->wcfe_add_error($result['error'], $errors);
				}
			}
		}else{
			$this->validate_custom_field($field, $posted, $errors);
		}
	}

	public function validate_custom_field($field, $posted, $errors=false, $return=false){
		$err_msgs = array();
		$key = isset($field['name']) ? $field['name'] : false;
		
		if($key){
			$value = isset($posted[$key]) ? $posted[$key] : '';
			$validators = isset($field['validate']) ? $field['validate'] : '';

			if($value && is_array($validators) && !empty($validators)){					
				foreach($validators as $vname){
					$err_msg = '';
					$flabel = THWCFE_i18n::t($field['label']);

					if($vname === 'number'){
						if(!is_numeric($value)){
							$err_msg = '<strong>'. $flabel .'</strong> '. THWCFE_i18n::t('is not a valid number.');	
						}
					}else{
						$custom_validators = $this->get_settings('custom_validators');
						$validator = is_array($custom_validators) && isset($custom_validators[$vname]) ? $custom_validators[$vname] : false;

						if(is_array($validator)){
							$pattern = $validator['pattern'];

							if(preg_match($pattern, $value) === 0) {
								//$this->wcfe_add_error($value, $errors);
								$err_msg = sprintf(THWCFE_i18n::t($validator['message']), $flabel);
							}
						}else{
							$con_validators = $this->get_settings('confirm_validators');
							$cnf_validator = is_array($con_validators) && isset($con_validators[$vname]) ? $con_validators[$vname] : false;

							if(is_array($cnf_validator)){
								$cfield = $cnf_validator['pattern'];
								$cvalue = $posted[$cfield];
								
								if($value && $cvalue && $value != $cvalue) {
									$err_msg = sprintf(THWCFE_i18n::t($cnf_validator['message']), $flabel );
								}
							}
						}
					}

					if($err_msg){
						if($errors || !$return){
							$this->wcfe_add_error($err_msg, $errors);
						}
						$err_msgs[] = $err_msg;
					}
				}
			}
		}
		return !empty($err_msgs) ? $err_msgs : false;
	}

	public function validate_file($field, $file){
		$errors = array();
		$errors['status'] = 'SUCCESS';
					
		if($file){
			$file_type = THWCFE_Utils::get_posted_file_type($file);
			$file_size = isset($file['size']) ? $file['size'] : false;
			
			if($file_type && $file_size){
				$name  = isset($field['name']) ? $field['name'] : '';
				$title = isset($field['title']) ? $field['title'] : '';
				$title = THWCFE_i18n::t($title);
				$maxsize = isset($field['maxsize']) ? $field['maxsize'] : '';
				$accept = isset($field['accept']) ? $field['accept'] : '';
				$file_type = strtolower($file_type);

				$maxsize = apply_filters('thwcfe_file_upload_maxsize', $maxsize, $name);
				$maxsize_bytes = is_numeric($maxsize) ? $maxsize*1048576 : false;
				
				$accept = apply_filters('thwcfe_file_upload_accepted_file_types', $accept, $name);
				$allowed = $accept && !is_array($accept) ? array_map('trim', explode(",", $accept)) : $accept;
				
				if(is_array($allowed) && !empty($allowed) && !in_array($file_type, $allowed)){
					//$err_msg = '<strong>'. $title .':</strong> '. THWCFE_i18n::t( 'Invalid file type.' );
					$err_msg = sprintf(THWCFE_i18n::t('Invalid file type, allowed types are %s'), $accept);
					$errors['error'] = $err_msg;
					$errors['status'] = 'ERROR';							
					
				}else if($maxsize_bytes && is_numeric($maxsize_bytes) && $file_size >= $maxsize_bytes){
					$err_msg = sprintf(THWCFE_i18n::t('Uploaded file should not exceed %sMB.'), $maxsize);
					$errors['error'] = $err_msg;
					$errors['status'] = 'ERROR';
				}
			}
		}
		return $errors;
	}

	function ajax_file_upload(){
	  	/*$fileErrors = array(
				0 => "There is no error, the file uploaded with success",
				1 => "The uploaded file exceeds the upload_max_files in server settings",
				2 => "The uploaded file exceeds the MAX_FILE_SIZE from html form",
				3 => "The uploaded file uploaded only partially",
				4 => "No file was uploaded",
				6 => "Missing a temporary folder",
				7 => "Failed to write file to disk",
				8 => "A PHP extension stoped file to upload" 
			);*/

		$posted_data = isset($_POST) ? $_POST : array();
		$file_data = isset($_FILES) ? $_FILES : array();
		$data = array_merge($posted_data, $file_data);
		
		$response = array();

		$file = $data['file'];
		$fname = $data['field_name'];

		$fieldset = WCFE_Checkout_Fields_Utils::get_all_checkout_fields_map();
		$field = $fieldset[$fname];

		$uploaded = $this->validate_file($field, $file);
		
		if( $field['upload_type'] == 'multiple' ) {
			$upload_dir = wp_upload_dir();
			$upload_path = $upload_dir["basedir"]."/checkout_multiple_files/";
			$upload_url = $upload_dir["baseurl"]."/checkout_multiple_files/";

			if(!file_exists($upload_path)){
			    mkdir($upload_path);
			}

			$list_files = $data["thwcfe_file_upload"];
			$file_count = count($list_files['name']);
			$targetPath = $upload_path;

			if($uploaded && !isset($uploaded['error'])){
				for( $i = 0; $i < $file_count; $i++ ) {
					$fileName = $list_files["name"][$i];
					$fileNameChanged = str_replace(" ", "_", $fileName);

					$file_temp_name = $list_files["tmp_name"][$i];

					$response['response'] = "SUCCESS";
					$response['uploaded']['name'][$i] = $fileName;
					//$response['uploaded']['file'][$i] = $file_temp_name;
					$response['uploaded']['type'][$i] = $list_files["type"][$i];
					$response['uploaded']['size'][$i] = $list_files["size"][$i];

					if( move_uploaded_file( $file_temp_name, $targetPath . "/" . $fileNameChanged ) ) {
						$response['uploaded']['url'][$i] = $upload_url . $fileNameChanged;
					}
				}			
			} else {
				$response['response'] = "ERROR";
				$response['error'] = $uploaded['error'];
			}
		} else {
			if($uploaded && $uploaded['status'] === "SUCCESS"){
				$uploaded = $this->upload_file($file);
			}

			if($uploaded && !isset($uploaded['error'])){
				$file_size = isset($file['size']) ? $file['size'] : false;

				$response['response'] = "SUCCESS";
				$response['uploaded']['name'] = $file['name'];
				$response['uploaded']['url'] = $uploaded['url'];
				$response['uploaded']['file'] = $uploaded['file'];
				$response['uploaded']['type'] = $uploaded['type'];
				$response['uploaded']['size'] = $file_size;
			}else{
				$response['response'] = "ERROR";
				$response['error'] = $uploaded['error'];
			}
		}

		echo json_encode($response);
		die();
	}

	function ajax_remove_uploaded(){
		if(isset($_POST) && isset($_POST['file']) && $_POST['file']){
			$response = array();

			$file = $_POST['file'];
			$result = unlink($file);

			if($result){
				$response['response'] = "SUCCESS";
			}else{
				$response['response'] = "ERROR";
				$response['error'] = 'File does not exist';
			}

			echo json_encode($response);
		}else{
			$response['response'] = "SUCCESS";
			$response['error'] = '';

			echo json_encode($response);
		}
		die();
	}

	public function uploaded_file($file, $name='', $field=null){
		$result = false;
		
		if(is_array($file)){
			$uploaded = $this->validate_file($field, $file);

			if($uploaded && $uploaded['status'] === "SUCCESS"){
				$uploaded = $this->upload_file($file);
			}

			if($uploaded && !isset($uploaded['error'])){
				$file_size = isset($file['size']) ? $file['size'] : false;

				$result['response'] = "SUCCESS";
				$result['uploaded']['name'] = $file['name'];
				$result['uploaded']['url'] = $uploaded['url'];
				$result['uploaded']['file'] = $uploaded['file'];
				$result['uploaded']['type'] = $uploaded['type'];
				$result['uploaded']['size'] = $file_size;
			}else{
				$result['response'] = "ERROR";
				$result['error'] = $uploaded['error'];
			}
		}
		return $result;
	}

	public function upload_file($file, $name='', $field=null){
		$upload = false;
		
		if(is_array($file)){
			if(!function_exists('wp_handle_upload')){
				require_once(ABSPATH. 'wp-admin/includes/file.php');
				require_once(ABSPATH. 'wp-admin/includes/media.php');
			}
			
			add_filter('upload_dir', array('WCFE_Checkout_Fields_Utils', 'upload_dir'));
			//add_filter('upload_mimes', array('THWEPO_Utils', 'upload_mimes'));
			$upload = wp_handle_upload($file, array('test_form' => false));
			remove_filter('upload_dir', array('WCFE_Checkout_Fields_Utils', 'upload_dir'));
			//remove_filter('upload_mimes', array('THWEPO_Utils', 'upload_mimes'));
		}
		return $upload;
	}	

	
	/****************************************
	******** CUSTOM FIELD TYPES - START ****
	****************************************/
	public function skip_form_field_filter($name){
		$skip = false;
		$ignore_fields = apply_filters('thwcfe_ignore_fields', array());
		if(is_array($ignore_fields) && !empty($ignore_fields) && in_array($name, $ignore_fields)){
			$skip = true;
		}
		Return $skip;
	}

	public function output_checkout_form_hidden_fields(){
		$this->output_disabled_field_names_hidden_field();
		$this->output_repeat_field_names_hidden_field();
	}

	public function output_disabled_field_names_hidden_field(){
		echo '<input type="hidden" id="thwcfe_disabled_fields" name="thwcfe_disabled_fields" value=""/>';
		echo '<input type="hidden" id="thwcfe_disabled_sections" name="thwcfe_disabled_sections" value=""/>';
	}

	public function output_repeat_field_names_hidden_field(){
		$rfields = THWCFE_Utils_Repeat::prepare_repeat_fields_json();
		$rsections = THWCFE_Utils_Repeat::prepare_repeat_sections_json();
		echo '<input type="hidden" id="thwcfe_repeat_fields" name="thwcfe_repeat_fields" value="'.$rfields.'"/>';
		echo '<input type="hidden" id="thwcfe_repeat_sections" name="thwcfe_repeat_sections" value="'.$rsections.'"/>';
	}
	
	public function prepare_price_data_string($args){
		$price_info = '';
		if($this->is_price_field($args)){
			$label = !empty($args['title']) ? THWCFE_i18n::t($args['title']) : $args['name'];
			$taxable = isset($args['taxable']) ? $args['taxable'] : 'no';
			$tax_class = isset($args['tax_class']) ? $args['tax_class'] : '';
			
			$price_type = isset($args['price_type']) && !empty($args['price_type']) ? $args['price_type'] : 'normal';
			$price 		= isset($args['price']) && is_numeric($args['price']) ? $args['price'] : 0; 
			$price_unit = isset($args['price_unit']) && !empty($args['price_unit']) ? $args['price_unit'] : 0;
			
			$price_info  = 'data-price="'.$price.'" data-price-type="'.$price_type.'" data-price-label="'.esc_attr($label).'" ';
			$price_info .= 'data-price-unit="'.$price_unit.'" data-taxable="'.$taxable.'" data-tax-class="'.$tax_class.'"';
		}
		return $price_info;
	}
	
	public function prepare_price_data_option_field_string($args){
		$price_data = '';
		$label     = isset($args['title']) && !empty($args['title']) ? THWCFE_i18n::t($args['title']) : $args['name'];
		$taxable   = isset($args['taxable']) ? $args['taxable'] : 'no';
		$tax_class = isset($args['tax_class']) ? $args['tax_class'] : '';
		
		$price_data = 'data-price-label="'.esc_attr($label).'" data-taxable="'.$taxable.'" data-tax-class="'.$tax_class.'"';
				
		return $price_data;
	}
	
	public function prepare_price_data_option_string($args){
		$price_info = '';
		if( isset($args['price']) && !empty($args['price']) ){
			$price_info = 'data-price="'.$args['price'].'" data-price-type="'.$args['price_type'].'"';
		}
		return $price_info;
	}
	
	public function prepare_ajax_conditions_data_section($section){
		$data_str = false;
		if($section->get_property('conditional_rules_ajax_json')){
			$rules_action = $section->get_property('rules_action_ajax') ? $section->get_property('rules_action_ajax') : 'show';
			$rules = urldecode($section->get_property('conditional_rules_ajax_json'));
			$rules = esc_js($rules);
			
			$data_str = 'id="'.$section->name.'" data-rules="'. $rules .'" data-rules-action="'. $rules_action .'" data-rules-elm="section"';
		}
		return $data_str;
	}
	
	public function woo_form_field_heading($field = '', $key, $args, $value){
		if($this->skip_form_field_filter($key)){
    		return $ofield;
    	}

    	$args['class'][] = 'thwcfe-html-field-wrapper';

		//$field = '<h3 class="form-row '.esc_attr(implode(' ', $args['class'])).'" id="'.esc_attr($key).'_field">'. THWCFE_i18n::t($args['label']) .'</h3>';
		$rules = '';
		$rules_action = '';
		if(isset($args['rules']) && !empty($args['rules'])){
			$rules_action = isset($args['rules_action']) ? $args['rules_action'] : 'show';
			$rules = urldecode($args['rules']);
			$rules = esc_js($rules);
			$args['class'][] = 'thwcfe-conditional-field';
		}
		$data_rules = 'data-rules="'.$rules.'" data-rules-action="'.$rules_action.'"';
		
		$title_html = $this->get_title_html($args);
		$field  = '';
		if(!empty($title_html)){
			$field .= '<div class="form-row '.esc_attr(implode(' ', $args['class'])).'" id="'.esc_attr($key).'_field" data-name="'.esc_attr($key).'" '.$data_rules.' >'. $title_html .'</div>';
		}
		return $field;
		
		//$field = $this->get_title_html($args);
		//return $field;
	}
	
	public function woo_form_field_label($field = '', $key, $args, $value){
		if($this->skip_form_field_filter($key)){
    		return $ofield;
    	}

    	$args['class'][] = 'thwcfe-html-field-wrapper';

		$rules = '';
		$rules_action = '';
		if(isset($args['rules']) && !empty($args['rules'])){
			$rules_action = isset($args['rules_action']) ? $args['rules_action'] : 'show';
			$rules = urldecode($args['rules']);
			$rules = esc_js($rules);
			$args['class'][] = 'thwcfe-conditional-field';
		}
		$data_rules = 'data-rules="'.$rules.'" data-rules-action="'.$rules_action.'"';
		
		$title_html = $this->get_title_html($args);
		$field  = '';
		if(!empty($title_html)){
			$field .= '<div class="form-row '.esc_attr(implode(' ', $args['class'])).'" id="'.esc_attr($key).'_field" data-name="'.esc_attr($key).'" '.$data_rules.' >'. $title_html .'</div>';
		}
		return $field;
	}
	
	public function get_title_html($args){
		$title_html = '';
		if(isset($args['label']) && !empty($args['label'])){
			$title_type  = isset($args['title_type']) && !empty($args['title_type']) ? $args['title_type'] : 'label';
			$title_style = isset($args['title_color']) && !empty($args['title_color']) ? 'style="display:block; color:'.$args['title_color'].';"' : 'style="display:block;"';
			
			$title_html .= '<'. $title_type .' class="'. implode(' ', $args['label_class']) .'" '. $title_style .'>'. THWCFE_i18n::t($args['label']) .'</'. $title_type .'>';
		}
		
		$subtitle_html = '';
		if(isset($args['subtitle']) && !empty($args['subtitle'])){
			$subtitle_type  = isset($args['subtitle_type']) && !empty($args['subtitle_type']) ? $args['subtitle_type'] : 'span';
			$subtitle_style = isset($args['subtitle_color']) && !empty($args['subtitle_color']) ? 'style="color:'. $args['subtitle_color'] .';"' : '';
			$subtitle_class = isset($args['subtitle_class']) && is_array($args['subtitle_class']) ? implode(' ', $args['subtitle_class']) : $args['subtitle_class'];
			
			$subtitle_html .= '<'. $subtitle_type .' class="'. $subtitle_class .'" '. $subtitle_style .'>';
			$subtitle_html .= THWCFE_i18n::t($args['subtitle']) .'</'. $subtitle_type .'>';
		}
		
		$html = $title_html;
		if(!empty($subtitle_html)){
			$html .= $subtitle_html;
		}
	
		return $html;
	}

	public function file_remove_button_html($key, $value='', $args){
		$html = '';
		$type = isset($args['type']) ? $args['type'] : '';

		if($type === 'file'){
			$disp_name = '';

			if($value){
				$value = str_replace('\\','\\\\',$value);
				$value_arr = json_decode($value, true);
				$value = is_array($value_arr) && isset($value_arr['name']) ? $value_arr['name'] : '';

				$disp_name = WCFE_Checkout_Fields_Utils::get_file_display_name($value_arr); 
			}
			$display = $disp_name ? 'block' : 'none';

			$html .= '<span class="thwcfe-uloaded-files" style="display:'.$display.';">';
			$html .= '<span class="thwcfe-upload-preview" style="margin-right:15px;">'.$disp_name.'</span>';
			$html .= '</span>';
			$html .= '<span class="thwcfe-file-upload-status" style="display:none;"><img src="'.THWCFE_ASSETS_URL_PUBLIC.'css/loading.gif" style="width:32px;"/></span>';
			$html .= '<span class="thwcfe-file-upload-msg" style="display:none; color:red;"></span>';
		}
		return $html;
	}

	public function file_change_button_html($key, $disp_name='', $args){
		$display = $disp_name ? '' : 'none';

		$html  = '<span class="thwcfe-uloaded-files" style="display:'.$display.';">';
		$html .= '<span class="thwcfe-upload-preview" style="margin-right:15px;">'.$disp_name.'</span>';
		$html .= '<span onclick="thwcfeChangeUploaded(this, event)" class="thwcfe-remove-uploaded" title="Change uploaded" style="cursor:pointer; display:'.$display.'; color:red;">Change</span>';
		$html .= '</span>';

		return $html;
	}

	public function form_field_wrapper_attributes($attributes, $key, $args){
		$rules = '';
		$rules_action = '';
		if(isset($args['rules']) && !empty($args['rules'])){
			$rules_action = isset($args['rules_action']) ? $args['rules_action'] : 'show';
			$rules = urldecode($args['rules']);
			$rules = esc_js($rules);
			$args['class'][] = 'thwcfe-conditional-field';

			$attributes[] = 'data-rules="'. $rules .'"';
			$attributes[] = 'data-rules-action="'. $rules_action .'"';
		}

		return $attributes;
	}
	
	/**
     * Outputs a checkout/address form field.
     *
     * @subpackage  Forms
     * @param string $key
     * @param mixed $args
     * @param string $value (default: null)
     * @todo This function needs to be broken up in smaller pieces
     */
    public function woo_form_field($ofield = '', $key, $args, $value = null ) {
    	if($this->skip_form_field_filter($key)){
    		return $ofield;
    	}

        $defaults = array(
            'type'              => 'text',
            'label'             => '',
            'description'       => '',
            'placeholder'       => '',
            'maxlength'         => false,
            'required'          => false,
            'id'                => $key,
            'class'             => array(),
            'label_class'       => array(),
            'input_class'       => array(),
            'return'            => false,
            'options'           => array(),
            'custom_attributes' => array(),
            'validate'          => array(),
            'default'           => '',
			'autofocus'         => '',
			'priority'          => '',
        );
		
		$value = apply_filters( 'thwcfe_woocommerce_form_field_value_'.$key, $value ); //Deprecated
		$value = apply_filters( 'thwcfe_woocommerce_form_field_value', $value, $key );

        $args = wp_parse_args( $args, $defaults );
		$args['name'] = $key;
        $args = apply_filters( 'woocommerce_form_field_args', $args, $key, $value );
		
		if(isset($args['label'])){
			$args['label'] = THWCFE_i18n::t($args['label']);
			$args['label'] = stripslashes($args['label']);
		}
		if(isset($args['description'])){
			$args['description'] = THWCFE_i18n::t($args['description']);
		}
		if(isset($args['placeholder'])){
			$args['placeholder'] = THWCFE_i18n::t($args['placeholder']);
		}
		
		$args['input_class'][] = 'thwcfe-input-field';
		$args['class'][] = 'thwcfe-input-field-wrapper';
		$validations = array();
		
        if($args['required'] ) {
            $args['class'][] = 'validate-required';
			$validations[] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce' ) . '">*</abbr>';
        } else {
            $required = '';
        }

        if(is_string($args['label_class'])) {
            $args['label_class'] = array( $args['label_class'] );
        }

        if(is_null($value)){
            $value = $args['default'];
        }

        // Custom attribute handling
        $custom_attributes = array();
		$args['custom_attributes'] = array_filter( (array) $args['custom_attributes'] );

		if ( $args['maxlength'] && is_numeric($args['maxlength']) ) {
			if(isset($args['type']) && $args['type'] === 'multiselect'){
				$args['custom_attributes']['data-maxselections'] = absint( $args['maxlength'] );
			}else{
				$args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
			}
		}

		$disable_autocomplete = apply_filters('thwcfe_disable_checkout_field_autocomplete', false, $key);
		$args['autocomplete'] = $disable_autocomplete ? 'off' : $args['autocomplete'];
		if ( ! empty( $args['autocomplete'] ) ) {
			$args['custom_attributes']['autocomplete'] = $args['autocomplete'];
		}

		if ( true === $args['autofocus'] ) {
			$args['custom_attributes']['autofocus'] = 'autofocus';
		}
		
		if ( (isset($args['readonly']) && true === $args['readonly']) || true === apply_filters('thwcfe_is_readonly_field_'.$key, false)) {
			$args['custom_attributes']['readonly'] = 'readonly';
		}

        if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
            foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }

        if ( ! empty( $args['validate'] ) ) {
            foreach ( $args['validate'] as $validate ) {
                $args['class'][] = 'validate-' . $validate;
            }
        }
		
		$rules = '';
		$rules_action = '';
		if(isset($args['rules']) && !empty($args['rules'])){
			$rules_action = isset($args['rules_action']) ? $args['rules_action'] : 'show';
			$rules = urldecode($args['rules']);
			$rules = esc_js($rules);
			$args['class'][] = 'thwcfe-conditional-field';
		}

        $field = '';
        $label_id = $args['id'];
		$validations_str = implode(" ", $validations);
		$priority        = is_numeric($args['priority']) ? $args['priority'] : '';
        $field_container = '<p class="form-row %1$s" id="%2$s" data-sort="' . esc_attr( $priority ) . '" data-rules="'.$rules.'" data-rules-action="'.$rules_action.'" data-validations="'.$validations_str.'" data-priority="'. esc_attr( $priority ) .'">%3$s</p>';

        switch ( $args['type'] ) {
            case 'country' :
                $field .= $this->woo_form_field_fragment_country( $key, $args, $value, $custom_attributes );
                break;
				
            case 'state' :
				$field .= $this->woo_form_field_fragment_state( $key, $args, $value, $custom_attributes );
                break;
				
            case 'textarea' :
				$field .= $this->woo_form_field_fragment_textarea( $key, $args, $value, $custom_attributes );
                break;
				
            case 'checkbox' :
                $field = $this->woo_form_field_fragment_checkbox( $key, $args, $value, $custom_attributes, $required );
                break;
			
			case 'checkboxgroup' :
                $field = $this->woo_form_field_fragment_checkboxgroup( $key, $args, $value, $custom_attributes, $required );
                break;	
				
            case 'password' :
            case 'text' :
            case 'email' :
            case 'tel' :
            case 'number' :
                $field .= $this->woo_form_field_fragment_general( $key, $args, $value, $custom_attributes );
                break;
				
            case 'select' :
				$field .= $this->woo_form_field_fragment_select( $key, $args, $value, $custom_attributes );
                break;
				
			case 'multiselect' :
				$field .= $this->woo_form_field_fragment_multiselect( $key, $args, $value, $custom_attributes );
                break;	
				
            case 'radio' :
				$label_id = current( array_keys( $args['options'] ) );
				$field .= $this->woo_form_field_fragment_radio( $key, $args, $value);
                break;
				
			case 'datepicker' :
				$field .= $this->woo_form_field_fragment_datepicker( $key, $args, $value, $custom_attributes );
                break;
				
			case 'timepicker' :
				$field .= $this->woo_form_field_fragment_timepicker( $key, $args, $value, $custom_attributes );
                break;
				
			case 'file' :
				$field .= $this->woo_form_field_fragment_file( $key, $args, $value, $custom_attributes );
                break;

            case 'file_default' :
				$field .= $this->woo_form_field_fragment_file_default( $key, $args, $value, $custom_attributes );
                break;
        }

        if ( ! empty( $field ) ) {
            $field_html = '';

            if ( $args['label'] && 'checkbox' != $args['type'] ) {
                $field_html .= '<label for="'. esc_attr( $label_id ) .'" class="'. esc_attr(implode(' ', $args['label_class'])) .'">'. $args['label'] . $required .'</label>';
            }
			
			if(apply_filters('thwcfe_display_field_description_below_label', false, $key)){
				if ( $args['description'] ) {
					$field_html .= '<span class="description">' . $args['description'] . '</span>';
					if($args['type'] === 'radio' || $args['type'] === 'checkboxgroup' || $args['type'] === 'file'){
						$field_html .= '<br/>';
					}
				}
				
				$field_html .= $field;

				$field_html .= $this->prepare_file_preview_html($value);
				//$field_html .= $this->file_remove_button_html($key, $value, $args);
			}else{
				$field_html .= $field;

				$field_html .= $this->prepare_file_preview_html($value);
				//$field_html .= $this->file_remove_button_html($key, $value, $args);

				if ( $args['description'] ) {
					if($args['type'] === 'radio' || $args['type'] === 'checkboxgroup' || $args['type'] === 'file'){
						$field_html .= '<br/>';
					}
					$field_html .= '<span class="description">' . $args['description'] . '</span>';
				}
			}

            /*$field_html .= $field;

            if ( $args['description'] ) {
                $field_html .= '<span class="description">' . $args['description'] . '</span>';
            }*/
			
			if ( in_array("thwcfe-char-count", $args['input_class']) || in_array("thwcfe-char-left", $args['input_class']) ) {
                $field_html .= '<span id="'.$args['id'].'-char-count" class="thpl-char-count" style="float: right;"></span><div class="clear"></div>';
            }

            $container_class = esc_attr( implode( ' ', $args['class'] ) );
            $container_id = esc_attr( $args['id'] ) . '_field';

            $field = sprintf( $field_container, $container_class, $container_id, $field_html );
			
			return $field;
        }

		return $ofield;
    }
	
	public function woo_form_field_fragment_country( $key, $args, $value, $custom_attributes ) { 
		$field = '';

		$countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

		if ( 1 === count( $countries ) ) {

			$field .= '<strong>' . current( array_values( $countries ) ) . '</strong>';

			$field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys( $countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" readonly="readonly" />';

		} else {

			if (isset($args['placeholder'])) {
		    	$custom_attributes[] = 'data-placeholder="' . esc_attr($args['placeholder']) . '"';
		    }

			$field = '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="country_to_state country_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . '><option value="">' . esc_html__( 'Select a country&hellip;', 'woocommerce' ) . '</option>';

			foreach ( $countries as $ckey => $cvalue ) {
				$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . $cvalue . '</option>';
			}

			$field .= '</select>';

			$field .= '<noscript><button type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__( 'Update country', 'woocommerce' ) . '">' . esc_html__( 'Update country', 'woocommerce' ) . '</button></noscript>';

		}

		/*
		$countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();
		
		if ( 1 === sizeof( $countries ) ) {
			$field .= '<strong>' . current( array_values( $countries ) ) . '</strong>';
	
			$field .= '<input type="hidden" name="'. esc_attr( $key ) .'" id="'. esc_attr( $args['id'] ) .'" value="'. current( array_keys($countries ) ) .'" ';
			$field .= implode( ' ', $custom_attributes ) . ' class="country_to_state" />';
	
		} else {
			$field  = '<select name="'.esc_attr($key).'" id="'.esc_attr($args['id']).'" class="country_to_state country_select '.esc_attr(implode(' ', $args['input_class'])).'" ';
			$field .= implode( ' ', $custom_attributes ) . '><option value="">'.esc_html__( 'Select a country&hellip;', 'woocommerce' ) .'</option>';
	
			foreach ( $countries as $ckey => $cvalue ) {
				$field .= '<option value="' . esc_attr( $ckey ) . '" '. selected( $value, $ckey, false ) . '>' . $cvalue . '</option>';
			}
	
			$field .= '</select>';
			$field .= '<noscript><input type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__( 'Update country', 'woocommerce' ) . '" /></noscript>';
		}*/
		return $field;
	}
	
	public function woo_form_field_fragment_state( $key, $args, $value, $custom_attributes ) { 
		$field = '';

		/* Get country this state field is representing */
		$for_country = isset( $args['country'] ) ? $args['country'] : WC()->checkout->get_value( 'billing_state' === $key ? 'billing_country' : 'shipping_country' );
		$states      = WC()->countries->get_states( $for_country );

		if ( is_array( $states ) && empty( $states ) ) {

			$field_container = '<p class="form-row %1$s" id="%2$s" style="display: none">%3$s</p>';

			$field .= '<input type="hidden" class="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="" ' . implode( ' ', $custom_attributes ) . ' placeholder="' . esc_attr( $args['placeholder'] ) . '" readonly="readonly" />';

		} elseif ( ! is_null( $for_country ) && is_array( $states ) ) {

			$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="state_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ? $args['placeholder'] : esc_html__( 'Select an option&hellip;', 'woocommerce' ) ) . '">
				<option value="">' . esc_html__( 'Select an option&hellip;', 'woocommerce' ) . '</option>';

			foreach ( $states as $ckey => $cvalue ) {
				$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . $cvalue . '</option>';
			}

			$field .= '</select>';

		} else {

			$field .= '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $value ) . '"  placeholder="' . esc_attr( $args['placeholder'] ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

		}

		/*
		$country_key = 'billing_state' === $key ? 'billing_country' : 'shipping_country';
		$current_cc  = WC()->checkout->get_value( $country_key );
		$states      = WC()->countries->get_states( $current_cc );
		
		if ( is_array( $states ) && empty( $states ) ) {
			$field_container = '<p class="form-row %1$s" id="%2$s" style="display: none">%3$s</p>';

			$field .= '<input type="hidden" class="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="" ';
			$field .= implode( ' ', $custom_attributes ) . ' placeholder="' . esc_attr( $args['placeholder'] ) .'" />';

		} elseif ( is_array( $states ) ) {
			$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="state_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" ';
			$field .= implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">';
			$field .= '<option value="">' . esc_html__( 'Select a state&hellip;', 'woocommerce' ) . '</option>';

			foreach ( $states as $ckey => $cvalue ) {
				$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . $cvalue . '</option>';
			}

			$field .= '</select>';
		} else {
			$field .= '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $value ) . '" ';
			$field .= 'placeholder="'. esc_attr($args['placeholder']) .'" name="'. esc_attr($key) .'" id="'. esc_attr($args['id']) .'" '. implode(' ', $custom_attributes) .' />';
		}*/
		return $field;
	}
	
	public function woo_form_field_fragment_textarea( $key, $args, $value, $custom_attributes ) {
		$price_info = $this->prepare_price_data_string($args);
		if($this->is_price_field($args)){
			$args['input_class'][] = 'thwcfe-price-field';
		}
	
		$field  = '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" ';
		$field .= 'placeholder="' . esc_attr( $args['placeholder'] ) . '" '. $args['maxlength'] .' ';
		$field .= ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '');
		$field .= ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '');
		$field .= implode( ' ', $custom_attributes ) .' '.$price_info.'>'. esc_textarea( $value ) .'</textarea>';
		
		return $field;
	}
	
	public function woo_form_field_fragment_checkbox( $key, $args, $value, $custom_attributes, $required ) {  
		$price_info = $this->prepare_price_data_string($args);
		if($this->is_price_field($args)){
			$args['input_class'][] = 'thwcfe-price-field';
		}
		
		/*$args['default'] = !empty($args['default']) ? $args['default'] : 1;
		$checked = (isset($args['checked']) && $args['checked']) ? 'checked' : '';*/
		
		$on_value = !empty($args['on_value']) ? $args['on_value'] : 1;
		if(is_user_logged_in() && isset($args['user_meta']) && $args['user_meta']){
			$checked = checked( $value, $on_value, false );
		}else{
			$checked = checked( $value, $on_value, false );
			if(!$checked){
				$checked = (isset($args['checked']) && $args['checked']) ? 'checked="checked"' : '';
			}
		}
		
		$field  = '<label class="checkbox ' . implode( ' ', $args['label_class'] ) . '" ' . implode( ' ', $custom_attributes ) . '>';  
        $field .= '<input type="' . esc_attr( $args['type'] ) .'" class="input-checkbox '. esc_attr(implode(' ', $args['input_class'])) .'" name="' . esc_attr( $key ) . '" '; 
		$field .= 'id="' .esc_attr($args['id']). '" value="'. $on_value .'" '. $checked .' '.$price_info.' /> '. $args['label'] . $required . '</label>';
		
		return $field;
	}
	
	public function woo_form_field_fragment_checkboxgroup( $key, $args, $value, $custom_attributes, $required ) {  
		$field = '';
		if(!empty($args['options_object'])) {
			$options_list = apply_filters( 'thwcfe_input_field_options_'.$key, $args['options_object'] );
			$options_per_line = apply_filters('thwcfe_checkboxgroup_options_per_line', 1, $key);
			
			//$value = empty($value) ? $args['default'] : $value;
			$value = is_array($value) ? $value : explode(',', $value);
			$value = !empty($value) ? array_map('trim', $value) : $value;
			
			$index = 1;		
			foreach($options_list as $option) {
				$option_key = $option['key'];
				$option_text = THWCFE_i18n::t($option['text']);
				
				$price_info = $this->prepare_price_data_option_string($option);
				$price_data = '';
				$args['input_class'] = THWCFE_Utils::remove_by_value('thwcfe-price-field', $args['input_class']);

				if( isset($option['price']) && !empty($option['price']) ){
					$args['input_class'][] = 'thwcfe-price-field';
					
					//$label = !empty($args['title']) ? THWCFE_i18n::t($args['title']) : $args['name'];
					//$price_data = 'data-price-label="'.esc_attr($label).'"';
					$price_data = $this->prepare_price_data_option_field_string($args);
				}
				
				$checked = in_array($option_key, $value) ? 'checked="checked"' : '';
				
				$field .= '<label for="'. esc_attr($args['id']) .'_'. esc_attr($option_key) .'" style="display:inline; margin-right: 10px;" '; 
				$field .= 'class="checkbox ' . implode( ' ', $args['label_class'] ) .'" '. implode( ' ', $custom_attributes ) .'>';  
        		$field .= '<input type="checkbox" data-multiple="1" class="input-checkbox '. esc_attr(implode(' ', $args['input_class'])) .'" name="'. esc_attr($key) .'[]" '; 
				$field .= $price_info.' '.$price_data.' ';
				$field .= 'id="' .esc_attr($args['id']) .'_'. esc_attr($option_key). '" value="'. $option_key .'" '. $checked .' /> '. $option_text .'</label>';
				
				if(is_array($args['class']) && in_array("valign", $args['class'])){
					$breakline = (is_numeric($options_per_line) && $options_per_line > 0 && fmod($index, $options_per_line) == 0) ? true : false;
					$field .= $breakline ? '<br/>' : '';
				}
				
				$index++;
			}
		}
		return $field;
	}
		
	public function woo_form_field_fragment_general( $key, $args, $value, $custom_attributes ) {
		$price_info = $this->prepare_price_data_string($args);
		if($this->is_price_field($args)){
			$args['input_class'][] = 'thwcfe-price-field';
		}
		
		$field  = '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" name="' . esc_attr( $key ) . '" '; 
		$field .= 'id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . $args['maxlength'] . ' value="' . esc_attr( $value ) . '" ';
		$field .= implode( ' ', $custom_attributes ) . ' '.$price_info.' />';
		
		return $field;
	}
	
	public function woo_form_field_hidden($field = '', $key, $args, $value){
		if($this->skip_form_field_filter($key)){
    		return $ofield;
    	}

		$price_info = $this->prepare_price_data_string($args);
		
		$css_class = array();
		$css_class[] = 'thwcfe-input-field';
		if($this->is_price_field($args)){
			$css_class[] = 'thwcfe-price-field';
		}
		
		$value = apply_filters( 'thwcfe_woocommerce_form_field_value_'.$key, $value ); //Deprecated
		$value = apply_filters( 'thwcfe_woocommerce_form_field_value', $value, $key );
		if(is_null($value)){
            $value = $args['default'];
        }
		
		$rules = '';
		$rules_action = '';
		if(isset($args['rules']) && !empty($args['rules'])){
			$rules_action = isset($args['rules_action']) ? $args['rules_action'] : 'show';
			$rules = urldecode($args['rules']);
			$rules = esc_js($rules);
			$css_class[] = 'thwcfe-conditional-field';
		}

		$field  = '<input type="hidden" id="'. esc_attr($key) .'" name="'. esc_attr($key) .'" value="'. esc_attr( $value ) .'" ';
		$field .= 'class="'.esc_attr(implode(' ', $css_class)).'" '.$price_info.' data-rules="'.$rules.'" data-rules-action="'.$rules_action.'" />';
		return $field;
	}
	
	public function woo_form_field_fragment_select( $key, $args, $value, $custom_attributes ) { 
		$options = $field = '';
		
		if(!empty($args['options_object'])){
			$show_price = apply_filters('thwcfe_display_field_option_price', true, $key, 'select');
			//$options_list = apply_filters( 'thwcfe_input_field_options_'.$key, $args['options_object'] ); //DEPRECATED 26-03-2018
			$options_list = apply_filters( 'thwcfe_input_field_options', $args['options_object'], $key );
			$price_field = false;
			
			/*if(isset($args['placeholder']) && !empty( $args['placeholder'])){
				$options .= '<option disabled="">'. esc_attr( $args['placeholder'] ) .'</option>';
			}*/
			
			foreach($options_list as $option){
				$option_key = $option['key'];
				$option_text = THWCFE_i18n::t($option['text']);
				
				$price_info = $this->prepare_price_data_option_string($option);
				if( isset($option['price']) && !empty($option['price']) ){
					$price_field = true;

					if($show_price){
						$price_html = THWCFE_Utils_Price::get_price_html_option($key, $option);
						if(!empty($option_key) && !empty($option_text)){
							$option_text .= !empty($price_html) ? $price_html : '';
						}
					}
				}
				
				if('' === $option_key){ // If we have a blank option, select2 needs a placeholder
					if(empty( $args['placeholder'])) {
						$args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'woocommerce' );
					}
					$custom_attributes[] = 'data-allow_clear="true"';
				}
				$options .= '<option value="'. esc_attr($option_key) .'" '. selected($value, $option_key, false) .' '.$price_info.' >'. esc_attr( $option_text ) .'</option>';
			}
			
			$price_data = '';
			if($price_field){
				$args['input_class'][] = 'thwcfe-price-field';
				$args['input_class'][] = 'thwcfe-price-option-field';
				
				//$label = !empty($args['title']) ? THWCFE_i18n::t($args['title']) : $args['name'];
				//$price_data = 'data-price-label="'.esc_attr($label).'"';
				$price_data = $this->prepare_price_data_option_field_string($args);
			}
			
			if($this->get_settings('disable_select2_for_select_fields') != 'yes'){
				$args['input_class'][] = 'thwcfe-enhanced-select';
			}

			$field .= '<select name="'.esc_attr($key).'" id="'.esc_attr($args['id']).'" class="select '.esc_attr(implode(' ', $args['input_class'])).'" '; 
			$field .= implode(' ', $custom_attributes) .' data-placeholder="'. esc_attr($args['placeholder']) .'" '.$price_data.'>'. $options .'</select>';
		}
		return $field;
	}
	
	public function woo_form_field_fragment_multiselect( $key, $args, $value, $custom_attributes ) { 
		$options = $field = '';

		if(!empty($args['options_object'])){
			$options_list = apply_filters( 'thwcfe_input_field_options_'.$key, $args['options_object'] );
			
			$price_field = false;
			$value = is_array($value) ? $value : explode(',', $value);
			$value = !empty($value) ? array_map('trim', $value) : $value;
						
			foreach($options_list as $option){
				$option_key = $option['key'];
				$option_text = THWCFE_i18n::t($option['text']);
				
				$selected = in_array($option_key, $value) ? 'selected="selected"' : '';
				
				$price_info = $this->prepare_price_data_option_string($option);
				if( isset($option['price']) && !empty($option['price']) ){
					$price_field = true;
				}
				
				if('' === $option_key){  // If we have a blank option, select2 needs a placeholder
					if(empty( $args['placeholder'])) {
						$args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'woocommerce' );
					}
					$custom_attributes[] = 'data-allow_clear="true"';
				}
				$options .= '<option value="'. esc_attr($option_key) .'" '. $selected .' '.$price_info.' >'. esc_attr( $option_text ) .'</option>';
			}
			
			$price_data = '';
			if($price_field){
				$args['input_class'][] = 'thwcfe-price-field';
				$args['input_class'][] = 'thwcfe-price-option-field';
				
				//$label = !empty($args['title']) ? THWCFE_i18n::t($args['title']) : $args['name'];
				//$price_data = 'data-price-label="'.esc_attr($label).'"';
				$price_data = $this->prepare_price_data_option_field_string($args);
			}

			$field .= '<select multiple="multiple" name="'. esc_attr($key) .'[]" id="'. esc_attr($args['id']) .'" '; 
			$field .= 'class="thwcfe-enhanced-multi-select '. esc_attr(implode(' ', $args['input_class'])) .'" ';
			$field .= implode(' ', $custom_attributes) .' data-placeholder="'. esc_attr($args['placeholder']) .'" '.$price_data.'>'. $options .'</select>';
		}
		return $field;
	}
	
	public function woo_form_field_fragment_radio( $key, $args, $value) { 
		$field = '';
		if(!empty($args['options_object'])) {
			$options_list = apply_filters( 'thwcfe_input_field_options_'.$key, $args['options_object'] );
			
			$is_price_field = $this->is_price_option($options_list);
			
			foreach($options_list as $option) {
				$option_key = $option['key'];
				$option_text = THWCFE_i18n::t($option['text']);
				
				$price_info = $this->prepare_price_data_option_string($option);
				$price_data = '';
				//if( isset($option['price']) && !empty($option['price']) ){
				if($is_price_field){
					$args['input_class'][] = 'thwcfe-price-field';
					
					//$label = !empty($args['title']) ? THWCFE_i18n::t($args['title']) : $args['name'];
					//$price_data = 'data-price-label="'.esc_attr($label).'"';
					$price_data = $this->prepare_price_data_option_field_string($args);
				}
				
				$field .= '<input type="radio" class="input-radio '. esc_attr(implode(' ', $args['input_class'])) .'" value="'. esc_attr( $option_key ) .'" '; 
				$field .= $price_info.' '.$price_data.' ';
				$field .= 'name="'. esc_attr($key) .'" id="'. esc_attr($args['id']) .'_'. esc_attr($option_key) .'"'. checked($value, $option_key, false) .' />';
				$field .= '<label for="'. esc_attr($args['id']) .'_'. esc_attr($option_key) .'" '; 
				$field .= 'class="radio '. implode(' ', $args['label_class']) .'" style="display:inline; margin-right: 10px;"> '. $option_text .'</label>';
				
				if(in_array("valign", $args['class'])){
					$field .= '<br/>';
				}
			}
		}
		return $field;
	}
	
	public function woo_form_field_fragment_datepicker( $key, $args, $value, $custom_attributes ) { 
		$price_info = $this->prepare_price_data_string($args);
		if($this->is_price_field($args)){
			$args['input_class'][] = 'thwcfe-price-field';
		}
		
		$dateFormat = isset($args['date_format']) ? $args['date_format'] : $this->get_jquery_date_format(wc_date_format());	
		$defaultDate = isset($args['default_date']) ? $args['default_date'] : '';
		$maxDate = isset($args['max_date']) ? $args['max_date'] : '';
		$minDate = isset($args['min_date']) ? $args['min_date'] : '';
		$yearRange = isset($args['year_range']) ? $args['year_range'] : '-100:+1';
		$numberOfMonths = isset($args['number_months']) ? $args['number_months'] : 1; 
		$disabledDays = isset($args['disabled_days']) ? $args['disabled_days'] : '';
		$disabledDates = isset($args['disabled_dates']) ? $args['disabled_dates'] : '';
		
		$minDate = apply_filters( 'thwcfe_min_date_date_picker_'.$key, $minDate );
		$maxDate = apply_filters( 'thwcfe_max_date_date_picker_'.$key, $maxDate );
		$disabledDays = apply_filters( 'thwcfe_disabled_days_date_picker_'.$key, $disabledDays );
		$disabledDates = apply_filters( 'thwcfe_disabled_dates_date_picker_'.$key, $disabledDates );
		$firstDay = apply_filters( 'thwcfe_date_picker_first_day', 0, $key );
				
		$field  = '<input type="text" class="thwcfe-checkout-date-picker input-text '. esc_attr(implode(' ', $args['input_class'])) .'" name="'. esc_attr($key) .'" ';
		$field .= 'id="'. esc_attr($args['id']) .'" placeholder="'. esc_attr($args['placeholder']) .'" '. $args['maxlength'] .' value="'. esc_attr($value) .'" ';
		$field .= implode(' ', $custom_attributes) .' '.$price_info.' ';
		$field .= 'data-date-format="'. $dateFormat .'" data-default-date="'. $defaultDate .'" data-max-date="'. $maxDate .'" data-min-date="'. $minDate .'" ';
		$field .= 'data-year-range="'. $yearRange .'" data-number-months="'. $numberOfMonths .'" data-first-day="'. $firstDay .'" ';
		$field .= 'data-disabled-days="'. $disabledDays .'" data-disabled-dates="'. $disabledDates .'" />';
		
		return $field;
	}
	
	public function woo_form_field_fragment_timepicker( $key, $args, $value, $custom_attributes ) { 
		$price_info = $this->prepare_price_data_string($args);
		if($this->is_price_field($args)){
			$args['input_class'][] = 'thwcfe-price-field';
		}
		
		$args['min_time']  = isset($args['min_time']) ? $args['min_time'] : '';
		$args['max_time']  = isset($args['max_time']) ? $args['max_time'] : '';
		$args['start_time']  = isset($args['start_time']) ? $args['start_time'] : '';
		$args['time_step'] = isset($args['time_step']) ? $args['time_step'] : '';
		$args['time_format'] = isset($args['time_format']) ? $args['time_format'] : '';
		$args['linked_date'] = isset($args['linked_date']) ? $args['linked_date'] : '';
		
		$args['min_time'] = apply_filters( 'thwcfe_min_time_time_picker_'.$key, $args['min_time'] );
		$args['max_time'] = apply_filters( 'thwcfe_max_time_time_picker_'.$key, $args['max_time'] );
		$args['start_time'] = apply_filters( 'thwcfe_start_time_time_picker_'.$key, $args['start_time'] );
		$args['time_step'] = apply_filters( 'thwcfe_time_step_time_picker_'.$key, $args['time_step'] );
		$args['linked_date'] = apply_filters( 'thwcfe_linked_date_time_picker_'.$key, $args['linked_date'] );
		
		if(!empty($args['linked_date'])){
			$args['input_class'][] = 'thwcfe-linked-date-'.$args['linked_date'];
		}
		
		$field  = '<input type="text" class="thwcfe-checkout-time-picker input-text '. esc_attr(implode(' ', $args['input_class'])) .'" name="'. esc_attr($key) .'" '; 
		$field .= 'id="'. esc_attr($args['id']) .'" placeholder="'. esc_attr($args['placeholder']) .'" '. $args['maxlength'] .' value="'. esc_attr($value) .'" ';
		$field .= implode(' ', $custom_attributes) .' '.$price_info.' data-start-time="'.$args['start_time'].'" data-linked-date="'.$args['linked_date'].'" ';
		$field .= 'data-min-time="'.$args['min_time'].'" data-max-time="'.$args['max_time'].'" data-step="'.$args['time_step'].'" data-format="'.$args['time_format'].'" />';
		
		return $field;
	}
	
	public function woo_form_field_fragment_file( $key, $args, $value, $custom_attributes ) {
		$price_info = $this->prepare_price_data_string($args);
		$value_json = esc_attr($value);
		
		$hinput_class = array();
		$hinput_class[] = 'thwcfe-input-field';
		if($this->is_price_field($args)){
			$hinput_class[] = 'thwcfe-price-field';
		}

		$input_class = $args['input_class'];
		if(($ckey = array_search('thwcfe-input-field', $input_class)) !== false){
		    unset($input_class[$ckey]);
		}

		$field = '';
		$custom_file_field_attr = '';

		$muliple_attr = $args['upload_type'];
		if( $muliple_attr == 'multiple' ) {
			$multi_attr = 'multiple="multiple"';
		} else {
			$multi_attr = '';
		}

		if($value){
			$value = str_replace('\\','\\\\',$value);
			$value_arr = json_decode($value, true);
			if( is_array($value_arr) ) {
				if( isset($value_arr['name']) && is_array( $value_arr['name'] ) ) {
					$main_value = array();

					foreach ($value_arr['name'] as $keyvalue => $value) {
						$main_value[$keyvalue] = $value_arr['name'][$keyvalue];
					}
					$value = implode( ", ", $main_value );

				} elseif( isset($value_arr['name']) && isset($value_arr['name']) ) {
					$value = $value_arr['name'];
				} else {
					$value = '';
				}
			}

			if($value){
				$custom_file_field_attr = 'style="display:none;"';
			}
		}

		$field .= '<input type="hidden" class="thwcfe-checkout-file-value input-text '.esc_attr(implode(' ', $hinput_class)) .'" name="'.esc_attr($key).'" id="'.esc_attr($args['id']).'" '.$price_info.' value="'.$value_json.'" '; 
		$field .= implode(' ', $custom_attributes) . ' '. $multi_attr . ' />';
		
		$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="thwcfe-checkout-file '.esc_attr(implode(' ', $input_class)) .'" name="'. esc_attr($key) .'_file" ';
		
		$field .= 'id="'. esc_attr($args['id']) .'_file" placeholder="' . esc_attr($args['placeholder']) . '" value="' . esc_attr( $value ) . '" '.$custom_file_field_attr. ' ' . $multi_attr .'  />';

		return $field;
	}

	public function woo_form_field_fragment_file_default( $key, $args, $value, $custom_attributes ) {
		$field = '';
		$value_json = esc_attr($value);
		$args['type'] = 'file';
		$price_info = $this->prepare_price_data_string($args);
		if($this->is_price_field($args)){
			$args['input_class'][] = 'thwcfe-price-field';
		}

		$disp_name = '';
		if($value){
			$value = str_replace('\\','\\\\',$value);
			$value_arr = json_decode($value, true);
			$value = is_array($value_arr) && isset($value_arr['name']) ? $value_arr['name'] : '';

			if($value){
				$custom_attributes[] = 'style="display:none;"';
			}

			$disp_name = WCFE_Checkout_Fields_Utils::get_file_display_name($value_arr); 
			if($disp_name){
				$disp_name_prefix = apply_filters('thwcfe_my-account_file_name_prefix', '', $key);
				$disp_name = $disp_name_prefix.''.$disp_name;
			}
		}
		
		$field .= $this->file_change_button_html($key, $disp_name, $args);
		$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="thwcfe-input-file input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" name="' . esc_attr( $key ) . '" '; 
		$field .= 'id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . $args['maxlength'] . ' value="' . esc_attr( $value ) . '" ';
		$field .= implode( ' ', $custom_attributes ) . ' '.$price_info.' />';
		
		return $field;
	}

	private function prepare_file_preview_html($value, $hyperlink=true){
		$prev_html = '';
		$html = '';

		$uploaded = false;

		if(is_string($value) && !empty($value)){
			$value = str_replace('\\','\\\\',$value);
			$uploaded = json_decode($value, true);
		}

		if(is_array($uploaded)){

			$name = isset($uploaded['name']) ? $uploaded['name'] : '';
			$size = isset($uploaded['size']) ? $uploaded['size'] : '';
			$type = isset($uploaded['type']) ? $uploaded['type'] : '';
			$url  = isset($uploaded['url']) ? $uploaded['url'] : '';

			if(is_array( $uploaded['name'] ) ) {
				$prev_html .= '<span class="thwcfe-uloaded-file-list multiple-upload-content"><span class="thwcfe-uloaded-file-list-item">';
				foreach ($uploaded['name'] as $counts => $value) {

					$sized = THWCFE_Utils::convert_bytes_to_kb($size[$counts]);

					if( $name[$counts] ) {
						$prev_html .= '<span class="thwcfe-columns">';

						if(in_array($type[$counts], THWCFE_Utils::$IMG_FILE_TYPES)){
							$prev_html .= '<span class="thwcfe-column-thumbnail">';
							$prev_html .= '<img src="'. $url[$counts] .'" >';
							$prev_html .= '</span>';
						}

						$prev_html .= '<span class="thwcfe-column-title">';
						$prev_html .= '<span title="'.$name[$counts].'" class="title">'.$name[$counts].'</span>';

						if($sized){
							$prev_html .= '<span class="size">'.$sized.'</span>';
						}
						$prev_html .= '</span>';

						$prev_html .= '</span>';
					}
				}

				$prev_html .= '<span class="thwcfe-column-actions">';
				$prev_html .= '<a href="#" onclick="thwcfeRemoveUploaded(this, event); return false;" class="thwcfe-action-btn thwcfe-remove-uploaded" title="Remove All">X</a>';
				$prev_html .= '</span>';

				$prev_html .= '</span></span>';
			} else {

				$size = THWCFE_Utils::convert_bytes_to_kb($size);
				$disp_name = WCFE_Checkout_Fields_Utils::get_file_display_name($uploaded, $hyperlink); 
				
				if($disp_name){
					$prev_html .= '<span class="thwcfe-uloaded-file-list"><span class="thwcfe-uloaded-file-list-item">';
					$prev_html .= '<span class="thwcfe-columns">';
					
					if(in_array($type, THWCFE_Utils::$IMG_FILE_TYPES)){
						$prev_html .= '<span class="thwcfe-column-thumbnail">';
						$prev_html .= '<img src="'. $url .'" >';
						$prev_html .= '</span>';
					}

					$prev_html .= '<span class="thwcfe-column-title">';
					$prev_html .= '<span title="'.$name.'" class="title">'.$disp_name.'</span>';
					if($size){
						$prev_html .= '<span class="size">'.$size.'</span>';
					}
					$prev_html .= '</span>';

					$prev_html .= '<span class="thwcfe-column-actions">';
					$prev_html .= '<a href="#" onclick="thwcfeRemoveUploaded(this, event); return false;" class="thwcfe-action-btn thwcfe-remove-uploaded" title="Remove">X</a>';
					$prev_html .= '</span>';

					$prev_html .= '</span>';
					$prev_html .= '</span></span>';
				}
			}
		}

		$display = $prev_html ? 'block' : 'none';

		$html  = '<span class="thwcfe-uloaded-files" style="display:'.$display.';">';
		$html .= '<span class="thwcfe-upload-preview" style="margin-right:15px;">'.$prev_html.'</span>';
		$html .= '</span>';
		$html .= '<span class="thwcfe-file-upload-status" style="display:none;"><img src="'.THWCFE_ASSETS_URL_PUBLIC.'css/loading.gif" style="width:32px;"/></span>';
		$html .= '<span class="thwcfe-file-upload-msg" style="display:none; color:red;"></span>';

		return $html;
	}
   /****************************************
	******** CUSTOM FIELD TYPES - END ******
	****************************************/
}

endif;