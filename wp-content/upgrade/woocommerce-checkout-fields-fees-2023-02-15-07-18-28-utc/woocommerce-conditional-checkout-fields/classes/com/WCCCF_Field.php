<?php 
class WCCCF_Field
{
	var $field_data;
	var $core_field_data;
	var $field_order;
	var $checkout_options;
	public function __construct()
	{
		add_filter( 'woocommerce_checkout_fields' , array($this, 'manage_checkout_fields'), 999 );
		add_filter( 'woocommerce_checkout_get_value' , array($this, 'clear_checkout_fields'), 10,2 );
		
		
		//To create html 
		
		//Custom field type -> to manage checkout multiselect field
		add_filter( 'woocommerce_form_field_wccf_multiselect',  array($this, 'add_multiselect_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_wcccf_heading',  array($this, 'add_heading_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_wcccf_file',  array($this, 'add_file_field'), 10, 4 );
		
		add_action( 'woocommerce_after_order_notes', array($this, 'add_language_hidden_field'), 10, 1 );
		
		//To clear field checkout default value:
		//add_filter -> 'default_checkout_' . $input, $value, $input
	}
	//Order details page value
	public function manage_multiselect_value_on_order_meta_load($value, $order)
	{
		$value = is_array($value) ? implode(", ",$value) : $value;
		return $value;
	}
	public function manage_heading_on_order_meta_load($value, $order)
	{
		return "-------------------";
	}
	public function manage_checkbox_value_on_order_meta_load($value, $order)
	{
		$value = $value != '' ? esc_html__( 'Yes', 'woocommerce-conditional-checkout-fields' ) : esc_html__( 'No', 'woocommerce-conditional-checkout-fields' );
		return $value;
	}
	public function manage_date_value_on_order_meta_load($value, $order)
	{
		$date_format = get_option('date_format');
		$date = DateTime::createFromFormat("Y-m-d", $value );
		if(is_object($date))
			$value = $date->format($date_format);
		return $value;
	}
	public function manage_time_value_on_order_meta_load($value, $order)
	{
		$time_format = get_option('time_format');
		$date = DateTime::createFromFormat("H:i", $value );
		if(is_object($date))
			$value = $date->format($time_format ); 
		return $value;
	}
	public function manage_country_value_on_order_meta_load($value, $order)
	{
		global $wcccf_country_model;
		$value =  $wcccf_country_model->country_code_to_name($value);
		
		return $value;
	}
	public function manage_state_value_on_order_meta_load($value, $order)
	{
		global $wcccf_country_model;
		$value =  $wcccf_country_model->state_code_to_name($value);
		
		return $value;
	}
	public function manage_file_value_on_order_meta_load($value, $order)
	{
		global $wcccf_file_model;
		return $wcccf_file_model->get_file_download_link($value , $order);
	}
	// End order details
	
	public function is_booking_field($field)
	{
		if(isset($field["conditional_group_item"]))
		{
			foreach((array)$field["conditional_group_item"] as $condition)
				if(isset($condition["product_condition_type"]) && $condition["product_condition_type"] == "booking_person")
					return true;
		}	
		return false;
	}
	function add_language_hidden_field( $checkout ) 
	{
		global $wcccf_wpml_helper;
		echo '<div id="wcccf_current_lang_container">
				<input type="hidden" class="input-hidden" name="wcccf_current_lang" id="wcccf_current_lang_input" value="' . $wcccf_wpml_helper->get_current_locale() . '">
			</div>';
	}
	public function add_heading_field( $field, $key, $args, $value )
	{
		//wcccf_var_dump($args);
		$classes = is_array($args["input_class"]) ? implode(" ",$args["input_class"]) : "";
		$sort = $args['priority'] ? $args['priority'] : '';
		
		//****** NOTE ******
		//Cannot print directly an heading, it won't be sorted. Print instead a span that lately via 
		//javascript is trasformed in heading (see frontend-checkout-page.js)
		
		//data-priority
		$field = '<p class="form-row ' . implode( ' ', $args['class'] ) .'" id="' . $key . '_field" data-sort="' . esc_attr( $sort ) . '">';
		$field .= "<span data-head='{$args['options']}' data-class='{$classes}' class='wcccf_transform_to_heading'>{$args['label']}</span>";
		$field .= '</p>';
		return $field;
	}
	public function get_upload_area($args, $key, $ignore_required = false)
	{
		//$key : billing_wcccf_id_F2k4Lud072maLgS , where is the id 
		$classes = is_array($args["input_class"]) ? implode(" ",$args["input_class"]) : "";
		$sort = $args['priority'] ? $args['priority'] : '';
		$id = $args['wcccf_unique_id'];
		
		$field = "";
		$field .= '<span class="wcccf_file_uploader_container">';
		$field .= '<input class="'.$classes.' wcccf_input_file wcccf_field" ';
		$field .= '					type="file" ';
		$field .= '					value=""';  
		$field .= '					id="wcccf_file_upload_'.$id.'"';
		$field .= '					data-id="'.$id.'"';
		$field .= '					data-key="'.$key.'"';
		$field .= '					data-upload-button-id="#wcccf_file_upload_button_'.$id.'"';
		$field .=                   $args['required'] && !$ignore_required ? 'required="required"' : '';
		$field .= 					$args['options']['accept'] ?  'accept="'.$args['options']['accept'].'"' : ''; 
		$field .=					$args['options']['size'] != 0 ? 'data-size="'.($args['options']['size']*1048576).'"' : 'data-size="0"'; 
		$field .= 					$args['options']['size'] != 0 ? 'value="'.($args['options']['size']*1048576).'"' : 'value="0"';
		$field .= '				   </input>';
		if($args['options']['size'] != 0) 
			$field .= ' <strong>'.sprintf(esc_html__('( Max size: %s MB)', 'wp-user-extra-fields'), $args['options']['size']).'</strong>';
		//Metadata			
		$field .= '<input type="hidden" id="wcccf-filename-'.$id.'" name="wcccf_files['.$id.'][file_name]" value=""></input>';
		$field .= '<input type="hidden" id="wcccf-filenameprefix-'.$id.'" name="wcccf_files['.$id.'][file_name_tmp_prefix]" value=""></input>';
		$field .= '<input type="hidden" id="wcccf-complete-name-'.$id.'" name="'.$key.'" value=""></input>';
		$field .= '<input type="hidden" name="wcccf_files['.$id.'][form_type]" value="'.$args['form_type'].'"></input>';
					
		//File name		
		$field .= ' <span id="wcccf_file_tmp_name_'.$id.'" class="wcccf_temp_file_name"></span>';
		
		if($args['description'] != "")
			$field .= '<span class="description wcccf_block">'.$args['description'].'</span>';
		
		//Upload button
		$field .= '<button class="button wcccf_file_upload_button"';  
		$field .= '		id="wcccf_file_upload_button_'.$id.'"';
		$field .= '	   data-id="'.$id.'"';  
		$field .= '	   data-upload-field-id="#wcccf_file_upload_'.$id.'">'.esc_html__('Upload', 'woocommerce-conditional-checkout-fields').'</button>';
		//Delete button
		$field .= ' <button class="button wcccf_file_tmp_delete_button" '; 
		$field .= '		id="wcccf_file_tmp_delete_button_'.$id.'"';
		$field .= '	   data-id="'.$id.'" '; 
		$field .= '	   data-file-to-delete="">'.esc_html__('Delete', 'woocommerce-conditional-checkout-fields').'</button>';
		//Upload progress managment
		$field .= '<span id="wcccf_upload_progress_status_container_'.$id.'" class="wcccf_upload_progress_status_container">';
		$field .= '	<span class="wcccf_upload_progressbar" id="wcccf_upload_progressbar_'.$id.'"></span >';
		$field .= '	<span class="wcccf_upload_progressbar_percent" id="wcccf_upload_progressbar_percent_'.$id.'">0%</span>';
		$field .= '</span>';
		
		$field .= '</span>'; //wcccf_file_uploader_container
		
		return $field;
	}
	public function add_file_field( $field, $key, $args, $value )
	{
		//wcccf_var_dump($args);
		$classes = is_array($args["input_class"]) ? implode(" ",$args["input_class"]) : "";
		$sort = $args['priority'] ? $args['priority'] : '';
		$id = $args['wcccf_unique_id'];
		$required_text = $args['required'] ? ' <abbr class="required" title="required">*</abbr>' : "";
		
		$field = '<p class="form-row ' . implode( ' ', $args['class'] ) .'" id="' . $key . '_field" data-priority="' . esc_attr( $sort ) . '">';
		$field .= '<label  class="">'.$args['label'].$required_text.'</label>';
		$field .= $this->get_upload_area($args, $key);
		$field .= '</p>';
		return $field;
	}
	public function add_multiselect_field( $field, $key, $args, $value )
	{
		$options = '';
		$input_class = isset($args['input_class']) && is_array($args['input_class']) ? implode(" ",$args['input_class']) : "" ;
		$custom_attributes = isset($args['custom_attributes']) && is_array($args['custom_attributes']) ? implode(" ",$args['custom_attributes']) : "" ;
		$required = $args['required'] ? ' required="required" ' : "";
		$placeholder = isset($args['placeholder']) ? 'data-placeholder= "'.$args['placeholder'].'" placeholder="'.$args['placeholder'].'" ': ' placeholder="'.$args['placeholder'].'" ';
		$required_asterisk = $args['required'] ? ' <abbr class="required" title="required">*</abbr> ' : "";
		$sort = $args['priority'] ? $args['priority'] : '';
		
		if ( ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $option_key => $option_text )
			{
				$selected = (!is_array($value) && $value == $option_key) || (is_array($value) && in_array($option_key, $value)) ? ' selected="selected" ' : '';
				
				$options .= '<option value="' . $option_key . '" '. $selected . '>' . $option_text .' </option>';
			}

			$field = '<p class="form-row ' . implode( ' ', $args['class'] ) .'" id="' . $key . '_field" data-priority="' . esc_attr( $sort ) . '">
				<label for="' . $key . '" class="' . implode( ' ', $args['label_class'] ) .'" '.$required.'>' . $args['label'].$required_asterisk. '</label>
				<select name="' . $key . '[]" id="' . $key . '" class="select '.$input_class.' '.$custom_attributes.' " '.$placeholder.'  multiple="multiple">
					' . $options . '
				</select>
			</p>';
		}

		return $field;
	}
	public function clear_checkout_fields($null, $input)
	{
		if(strpos($input,'billing_wcccf_id_') !== false || strpos($input,'shipping_wcccf_id_') !== false)
			$null = '';
		return $null;
	}
	public function manage_checkout_fields( $fields ) 
	{
		global  $wcccf_field_model;
		
		$field_order =  $wcccf_field_model->get_field_order();
		$form_type_array = array('billing', 'shipping');
		
		 foreach($form_type_array as $form_type)
		 {
			 $fields = $this->get_form_field_data($form_type, true, $fields );
		 }
			
		//return $fields; 
		
		//SORT DOES NOT WORK ON WC > 3.0, javascript workaround has been implemented
		
		//wcccf_var_dump($fields);
		if($this->get_checkout_option('disable_checkout_sort_and_hide') != 'yes' && isset($field_order) && is_array($field_order))
		{
			$max_index = count($field_order);
			//wcccf_var_dump($field_order);
			foreach($form_type_array as $form_type)
			{
				//$sorted_field = $unsorted_fields = array();
				foreach($fields[$form_type] as $field_id => $field)
				{
					
					if(isset($field['priority']))
					{
						//Note: $field_order[$field_id]['sort_index']++ is to avoid the 0 value
						$new_sort_index = isset($field_order[$field_id]) ? (10*($field_order[$field_id]['sort_index']++)) : $field['priority'];
						$fields[$form_type][$field_id]['priority'] = !isset($field_order[$field_id]) ? /* $max_index++  */ $field['priority'] : $new_sort_index /* $field_order[$field_id]['sort_index']+$new_sort_index */;
					}
					if(!isset($field_order[$field_id]) && isset($field['priority']))
						$fields[$form_type][$field_id]['class'][] =  "wcccf_priority-".$field['priority'];
					elseif(wcccf_get_value_if_set($field_order, array( $field_id, 'sort_index'), false))
						$fields[$form_type][$field_id]['class'][] = "wcccf_priority-".($field_order[$field_id]['sort_index']+$new_sort_index);
					
					
					if(isset($field_order[$field_id]['is_hidden']))
						unset($fields[$form_type][$field_id]);
					
				} 
				
			}
		}
			return $fields; 
	}
	public function render_booking_product_additional_field($type, $product_item)
	{
		$data = $this->get_form_field_data($type, true, array(), true, $product_item['product_id']."-".$product_item['variation_id']);
		
		$main_product_id = $product_item['variation_id'] != 0 ? $product_item['variation_id'] :  $product_item['product_id'];
		$wc_product = wc_get_product($main_product_id);
		
		if(!is_object($wc_product) || get_class($wc_product) != 'WC_Product_Booking')
			return;
		
		if(!empty($data) && $wc_product->has_persons())
		{
			echo "<h3 class='wcccf_bookable_product_title'>".$product_item['data']->get_name()." (".wc_get_formatted_cart_item_data( $product_item, true ).")</h3>";
			
			$pers_num = array_sum($product_item['booking']['_persons']);
			for($i = 0; $i < $pers_num ; $i++)
			{
				if(count($data[$type]) > 0) echo "<div class='wcccf_group_fields'>";
				foreach($data[$type] as $form_field_id => $form_field_data)
				{
					$is_label_field = strpos($form_field_data['wcccf_unique_id'], '_label') !== false ;
					$is_state_field = strpos($form_field_data['wcccf_unique_id'], '_state') !== false ;
					$form_field_data['wcccf_unique_id'] = $is_label_field ?  str_replace('_label', "", $form_field_data['wcccf_unique_id']) : $form_field_data['wcccf_unique_id'];
					$form_field_data['wcccf_unique_id'] = $is_state_field ?  str_replace('_state', "", $form_field_data['wcccf_unique_id']) : $form_field_data['wcccf_unique_id'];
					$index_name = $is_label_field ? 'label' : 'value';
					$index_name = $is_state_field ?  'state' : $index_name ;
					
					if($form_field_data['wcccf_type'] == 'country' || $form_field_data['wcccf_type'] == 'state' || $form_field_data['wcccf_type'] == 'file')
					{
						continue;
					}
					
					woocommerce_form_field("wcccf_booking_item[".$product_item['key']."][".$i."][".$form_field_data['wcccf_unique_id']."][".$index_name."]", array(
									'type'       => $form_field_data["type"],
									'required'          => $form_field_data['required'],
									'class'      => $form_field_data['class'],
									'label'      => isset($form_field_data['label']) ? $form_field_data['label'] : "",
									'default'      => isset($form_field_data['default']) ? $form_field_data['default'] : "", 
									//'label_class' => array( 'wcmca_form_label' ),
									'input_class' => isset($form_field_data['input_class']) ? $form_field_data['input_class'] : array(),
									'placeholder'    => isset($form_field_data['placeholder']) ? $form_field_data['placeholder'] : "",
									'options'    => isset($form_field_data['options']) ? $form_field_data['options'] : array() ,
									'custom_attributes'  => isset($form_field_data['custom_attributes']) ? $form_field_data['custom_attributes'] : array()
									)
					);  
					
					//used when storing data on order product meta data
					if($is_label_field)
					{
						woocommerce_form_field("wcccf_booking_item[".$product_item['key']."][".$i."][".$form_field_data['wcccf_unique_id']."][form_type]", array(
								'type'       => "text",
								'default'      => $type, 
								'class'     => array('form-row-wide', "wcccf_hidden_field")
								));
						woocommerce_form_field("wcccf_booking_item[".$product_item['key']."][".$i."][".$form_field_data['wcccf_unique_id']."][field_type]", array(
								'type'       => "text",
								'default'      => $form_field_data['wcccf_type'], 
								'class'     => array('form-row-wide', "wcccf_hidden_field")
								));
					}
					
				} 
				if(count($data[$type]) > 0) echo "</div>";
			}
		}
	}
	public function get_field_name_by_data($field_data)
	{
		global $wcccf_wpml_helper;
		$field_name = isset($field_data['name'][$wcccf_wpml_helper->get_current_locale()]) ? $field_data['name'][$wcccf_wpml_helper->get_current_locale()] : "";
		$field_name = $field_name == "" && isset($field_data['name'][$wcccf_wpml_helper->get_default_locale()]) ? $field_data['name'][$wcccf_wpml_helper->get_default_locale()] : $field_name;
			  
		return $field_name;
	}
	public function get_form_field_data($form_type = 'billing', $is_checkout_page = true, $fields = array(), $get_product_bounded_fields = false, $product_id_to_use_as_filter = "", $force_return_heading = false)
	{
		global $wcccf_wpml_helper, $wcccf_country_model, $wcccf_country_model, $wcccf_product_model, $wcccf_datetime_model, $wcccf_option_model;
		$extra_field_data = $this->get_field_data($form_type);
		
		if($is_checkout_page && !$get_product_bounded_fields)
		{
			$options = $wcccf_option_model->get_options();
		
			wp_enqueue_script( 'van-picker', WCCCF_PLUGIN_PATH.'/js/vendor/datetimepicker/picker.js', array('jquery'));	
			wp_enqueue_script( 'van-datepicker', WCCCF_PLUGIN_PATH.'/js/vendor/datetimepicker/picker.date.js', array('jquery'));	
			wp_enqueue_script( 'van-timepicker', WCCCF_PLUGIN_PATH.'/js/vendor/datetimepicker/picker.time.js', array('jquery'));	
			$lang_code = str_replace("_formal", "",$wcccf_wpml_helper->get_current_locale());
			if(wcccf_file_exists(WCCCF_PLUGIN_ABS_PATH.'/js/vendor/datetimepicker/translations/'.$lang_code.'.js'))
				wp_enqueue_script('van-datepicker-localization', WCCCF_PLUGIN_PATH.'/js/vendor/datetimepicker/translations/'.$lang_code.'.js');
			//
			wp_register_script( 'wcccf-fields-js-managment', WCCCF_PLUGIN_PATH.'/js/form-fields-managment.js', array('jquery'));
			wp_localize_script( 'wcccf-fields-js-managment', 'wcccf_options', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 
																					 'is_checkout_page' => $is_checkout_page == true ? 'true' : 'false',
																					 'country_loader_path' => WCCCF_PLUGIN_PATH.'/img/horizontal-loader.gif',
																					 'date_format' => wcccf_get_value_if_set($options, 'date_format', 'yyyy-mm-dd'),
																					 'time_format' => wcccf_get_value_if_set($options, 'time_format', 'HH:i')) 
																					);
			wp_enqueue_script('wcccf-fields-js-managment');
			//
			wp_register_script( 'wcccf-checkout-page', WCCCF_PLUGIN_PATH.'/js/frontend-checkout-page.js', array('jquery'));	
			wp_localize_script( 'wcccf-checkout-page', 'wcccf_checkout_page', array( 'disable_checkout_sort_and_hide' => $this->get_checkout_option('disable_checkout_sort_and_hide')
																							));
			wp_enqueue_script('wcccf-checkout-page');
																									
			//
			wp_register_script( 'wcccf-file-upload-manager', WCCCF_PLUGIN_PATH.'/js/file-upload-manager.js', array('jquery'));
			wp_localize_script( 'wcccf-file-upload-manager', 'wcccf_file_uploader_manager', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
																									'delete_message' => esc_html__('deleting', 'woocommerce-conditional-checkout-fields'),
																									'size_error_message' => esc_html__('File too big. Max allowed size (MB): ', 'woocommerce-conditional-checkout-fields'),
																									'required_files_message' => esc_html__('Please upload all the required files', 'woocommerce-conditional-checkout-fields')
																									));
			wp_enqueue_script('wcccf-file-upload-manager');																		
			//
			wp_enqueue_script( 'wcccf-file-uploader', WCCCF_PLUGIN_PATH.'/js/file-uploader.js', array('jquery'));	
			
			wp_enqueue_style('van-datepicker-default', WCCCF_PLUGIN_PATH.'/css/vendor/datetimepicker/default.css'); 
			wp_enqueue_style('van-datepicker-date-default', WCCCF_PLUGIN_PATH.'/css/vendor/datetimepicker/default.date.css'); 
			wp_enqueue_style('van-datepicker-time-default', WCCCF_PLUGIN_PATH.'/css/vendor/datetimepicker/default.time.css'); 
			wp_enqueue_style('wcccf-form-ui-managment', WCCCF_PLUGIN_PATH.'/css/form-ui-managment.css'); 
			
		}
		//wcccf_var_dump($extra_field_data);
		$priority = 0;
		foreach((array)$extra_field_data as $field_data)
		 {
			 $priority++;
			//Logic check: check if the field can be rendered according the logic rules associated to the field
			if($is_checkout_page && isset($field_data['conditional_group_item']) && !$this->verify_logic_conditions($field_data['conditional_group_item'], $get_product_bounded_fields))
				continue;
			
			if($get_product_bounded_fields && (!isset($field_data['conditional_group_item']) || !$this->verify_logic_conditions($field_data['conditional_group_item'], $get_product_bounded_fields) || ($product_id_to_use_as_filter != "" && !in_array($product_id_to_use_as_filter, $wcccf_product_model->product_ids_to_which_was_bounded_last_field) )))
				continue;
			
			if(!$is_checkout_page && !$force_return_heading && $field_data['type'] == 'heading')
			{
				continue;
			}
			
			$state_id = $form_type.'_wcccf_id_'.$field_data['unique_id']."_state";
			
			$field_name = $this->get_field_name_by_data($field_data);
			
			$place_holder = isset($field_data['options']['place_holder'][$wcccf_wpml_helper->get_current_locale()]) ? $field_data['options']['place_holder'][$wcccf_wpml_helper->get_current_locale()] : "";
			$place_holder = $place_holder == "" && isset($field_data['options']['place_holder'][$wcccf_wpml_helper->get_default_locale()]) ? $field_data['options']['place_holder'][$wcccf_wpml_helper->get_default_locale()] : $place_holder;
			
			$field_type = $field_data['type'];
			$description =isset($field_data['options']['description'][$wcccf_wpml_helper->get_current_locale()]) ?  $field_data['options']['description'][$wcccf_wpml_helper->get_current_locale()] : '';
			$return = false;
			
			//custom attributes by field type
			$custom_attributes = $input_class = $validate = array();
			$additional_class =  "";
			$options = array();
			if($field_type == 'number' )
			{
				$custom_attributes = array('min' => $field_data['options']['number_min_value'], 'max' => $field_data['options']['number_max_value']);
				$validate[] = 'number';
				$input_class = array("wcccf_number_field");
			}
			if($field_type == 'email' )
			{
				$validate[] = 'email';
				$input_class = array("wcccf_email_field");
			}
			elseif($field_type == 'date')
			{
				$field_type = "text";
				$disabled_days = isset($field_data['options']['day_to_disable']) ? $field_data['options']['day_to_disable'] : array("none");
				$specific_dates_to_disable = $this->get_specific_dates_to_disable($field_data['options']['specific_dates_to_disable']);
				
				$min_limit_type = isset($field_data['options']['date_min_limit_type']) && $field_data['options']['date_min_limit_type'] == 'relative' ? 'relative' : 'absolute'; 
				$max_limit_type = isset($field_data['options']['date_max_limit_type']) && $field_data['options']['date_max_limit_type'] == 'relative' ? 'relative' : 'absolute'; 
	
				if($min_limit_type == 'absolute')
				{
					$min_value = $field_data['options']['date_min_value'] != "" ? str_replace("-",",",$field_data['options']['date_min_value']) : "";
				}
				else //relative (min)
				{
					if( $field_data['options']['date_min_offset'] == "")
						$min_value = date('Y,m,d');
					else
					 $min_value = $field_data['options']['date_min_offset'] != 0 ? str_replace("-",",", date('Y-m-d', strtotime($field_data['options']['date_min_offset']." ".$field_data['options']['date_min_offset_type']))) : ""; 
				}
				if($max_limit_type == 'absolute')
				{
					$max_value = $field_data['options']['date_max_value'] != "" ? str_replace("-",",",$field_data['options']['date_max_value']) : "";
				}
				else //relative (max)
				{
					if( $field_data['options']['date_max_offset'] == "")
						$max_value = date('Y,m,d');
					else
						$max_value = $field_data['options']['date_max_offset'] != 0 ? str_replace("-",",", date('Y-m-d', strtotime($field_data['options']['date_max_offset']." ".$field_data['options']['date_max_offset_type']))) : ""; 
				}
				
				$custom_attributes = array('data-min' => $min_value, 'data-max' => $max_value, 'data-disabled-days' => implode(",", $disabled_days), 'data-specific-dates-to-disable' => implode(";",$specific_dates_to_disable));
				$input_class = array("wcccf_date_field");
			}
			elseif($field_type == 'time')
			{
				$field_type = "text";
				$min_limit_type = isset($field_data['options']['time_min_limit_type']) && $field_data['options']['time_min_limit_type'] == 'relative' ? 'relative' : 'absolute'; 
				$max_limit_type = isset($field_data['options']['time_max_limit_type']) && $field_data['options']['time_max_limit_type'] == 'relative' ? 'relative' : 'absolute'; 
				$min_time_absolute_can_be_earlier_than_now = isset($field_data['options']['min_time_absolute_can_be_earlier_than_now']) && $field_data['options']['min_time_absolute_can_be_earlier_than_now'] == 'no' ? 'no' : 'yes'; 
				$time_interval_interval = isset($field_data['options']['time_interval']) ? $field_data['options']['time_interval'] : '30'; 

				if($min_limit_type == 'absolute')
				{
					if($min_time_absolute_can_be_earlier_than_now == 'no' && !$wcccf_datetime_model->time_is_greater_than($field_data['options']['time_min_value'], 'now'))
					{
						//wcccf_var_dump($wcccf_datetime_model->time_is_greater_than($field_data['options']['time_min_value'], 'now'));
						$field_data['options']['time_min_value'] = $wcccf_datetime_model->time_now();
					}
					$min_value = $field_data['options']['time_min_value'] != "" ? str_replace(":",",",$field_data['options']['time_min_value']) : "";
				}
				else 
				{
					$min_value = $field_data['options']['time_min_offset'] != "" ? str_replace(":",",", date('H:i', strtotime($field_data['options']['time_min_offset']." ".$field_data['options']['time_min_offset_type']))) : ""; 
				}
				if($max_limit_type == 'absolute')
				{
					$max_value = $field_data['options']['time_max_value'] != "" ? str_replace(":",",",$field_data['options']['time_max_value']) : "";
				}
				else 
				{
					$max_value = $field_data['options']['time_max_offset'] != "" ? str_replace(":",",", date('H:i', strtotime($field_data['options']['time_max_offset']." ".$field_data['options']['time_max_offset_type']))) : ""; 
				}
				
				
				$custom_attributes = array('data-min' => $min_value, 'data-max' => $max_value, 'data-interval' => $time_interval_interval);
				$input_class = array("wcccf_time_field");
			}
			elseif($field_type == 'country')
			{
				$field_type = $is_checkout_page ? "select" : "text";
				$options = $wcccf_country_model->get_countries($field_data['options']['country_selection_type'], true);
				$additional_class = " ";
				$input_class = array("wcccf_select2","wcccf_country_select");
				$custom_attributes = array("data-linked-state-field-id" =>$form_type.'_wcccf_id_'.$field_data['unique_id']."_state", 
										   "data-load-state" => $field_data['options']['country_hide_states'] == 'no',
										   "data-form-type" => $form_type,
										   "data-unique-id" => $field_data['unique_id'],
										   "data-state-selector-width" => $field_data['options']['row_type'] == "first" ? "last" : "wide",
										   "data-prev-state-value" => $is_checkout_page ? WC()->checkout()->get_value( $state_id ) : ""
										   );
			}
			elseif($field_type == 'state')
			{
				$field_type = $is_checkout_page ? "select" : "text";
				$input_class = array("wcccf_select2", "wcccf_state_select", 'not_empty');
				$options = $wcccf_country_model->get_state_by_country($field_data['options']['state_show_state_for_country']);
			}
			elseif($field_type == 'select')
			{
				$input_class = array("wcccf_select2");
				$field_type = "select";
				$is_multiselect = false;
				if($field_data['options']['select_multiple_selection'] == 'yes')
				{
					$is_multiselect = true;
					$custom_attributes['multiple'] = 'multiple';
					if($is_checkout_page)
						$field_type = $field_data['type'] = 'wccf_multiselect'; //custom implemented, see woocommerce_form_field_wccf_multiselect
				}
				$options = $this->get_select_labels_and_values($field_data['options']['select_labels_and_values'], !$is_multiselect && $place_holder != "");
			} 
			elseif($field_type == 'checkbox')
			{
				$field_data['options']['css_classes'] = "wcccf_checkbox_container";
			}
			elseif($field_type == 'heading')
			{
				$field_type = $field_data['type'] = 'wcccf_heading'; //custom implemented, see woocommerce_form_field_wcccf_heading
				$options = $field_data['options']['heading_tag'];
				//$return = true;
			}
			elseif($field_type == 'file')
			{
				$field_type = $field_data['type'] = 'wcccf_file'; //custom implemented, see woocommerce_form_field_wcccf_file
				$options['size'] = $field_data['options']['file_max_size'];
				$options['accept'] = $field_data['options']['file_accept'];
			}
			
			//From the classes, the 'address-field' attribute has been removed because was causing the ajax reload of the "your order" fragment
			$par_class = array('form-row-'.$field_data['options']['row_type'], $field_data['options']['css_classes'], $additional_class);
			$input_class = array_merge($input_class, explode(" ",$field_data['options']['css_input_classes']), array("wcccf_field")) ;
			
			$fields[$form_type][$form_type.'_wcccf_id_'.$field_data['unique_id']] = array(
					//type – type of field (text, textarea, password, select, country, state, email, tel, number, radio)
					'type' => $field_type,
					'wcccf_type' => $field_data['type'],
					'wcccf_unique_id' => $field_data['unique_id'],
					'wcccf_show_in_emails' => $field_data['options']['show_in_emails'] == 'yes' ? true : false,
					'wcccf_show_in_order_details_page' => $field_data['options']['show_in_order_details_page'] == 'yes'  ? true : false,
					'label'     => $field_name,
					'validate'     => $validate,
					'form_type'     => $form_type,
					/* 'label_class' => array(),*/
					'input_class' => $input_class, 
					'placeholder'   => $place_holder,
					'required'  => $field_data['options']['is_required'] == 'yes',
					'class'     => $is_checkout_page ? $par_class :  implode(" ", $input_class),
					'clear'     => true,
					'custom_attributes'    => $custom_attributes,
					'options' => $options,
					'description' => $description,
					//'value' => null,
					'priority' => 300+$priority, //field order on form
					'return' => $return
				 );
			
			if($is_checkout_page)
			{
				//What for? still needed? NO-> label are retrieved from current existing fields
				$fields[$form_type][$form_type.'_wcccf_label_'.$field_data['unique_id']] = array(
						'type' => 'text',
						'wcccf_unique_id' => $field_data['unique_id']."_label",
						'wcccf_type' => "hidden_label",
						'default' => str_replace('"', "", $field_data['name'][$wcccf_wpml_helper->get_default_locale()]),
						'class'     => array('form-row-wide', "wcccf_hidden_field"),
						'clear'     => true,
						'required' => false,
						'priority' => 500+$priority
					 );
			}
				 
			//State hidden field
			if($field_data['type'] == 'country' && isset($field_data['options']['country_hide_states']) && $field_data['options']['country_hide_states'] == 'no')
			{
				$priority++;
				$is_state_field_required  = $field_data['options']['is_required'];
				//Fields are generated while validating via ajax ("place order" click)
				if(defined('DOING_AJAX') && DOING_AJAX && isset($_POST[$form_type.'_wcccf_id_'.$field_data['unique_id']]))
				{
					$states_data = $wcccf_country_model->get_state_by_country($_POST[$form_type.'_wcccf_id_'.$field_data['unique_id']], true);
					$states = $states_data['states'];
					
					$is_state_field_required = is_array( $states ) && empty( $states ) ? false : $is_state_field_required ;
				}
		
				$css_state_class = $field_data['options']['row_type'] == "first" ? "last" : "wide"; //no need, is settev via ajax when loading the new field
				$fields[$form_type][$state_id] = array(
					'type' => 'text',
					'wcccf_unique_id' => $field_data['unique_id']."_state",
					'wcccf_show_in_emails' => $field_data['options']['show_in_emails'] == 'yes' ? true : false,
					'wcccf_show_in_order_details_page' => $field_data['options']['show_in_order_details_page'] == 'yes'  ? true : false,
					'wcccf_type' => 'state',
					'label'     => esc_html__('State/Province/County','woocommerce-conditional-checkout-fields'),
					'required'  => $is_state_field_required ,
					'class'     => $is_checkout_page ? array('form-row-'.$css_state_class, 'wcccf_state_country_state_field') : 'form-row-'.$css_state_class.' wcccf_state_country_state_field',
					'clear'     => true,
					'priority' => 300+$priority
				 );
			}
		 }
		
		return $fields;
	}
	private function verify_logic_conditions($field_data, $consider_only_product_bounded_fields = false)
	{
		global $wcccf_product_model, $wcccf_customer_model, $wcccf_cart_model;
		$and_item_results = array();
		$current_logic_result = true;
		$final_result = false;
		$exists_at_least_a_condition_bounded_to_product = false;
		$counter = 0;
		
		$wcccf_product_model->reset_internal_state();
		
		foreach($field_data as $condition)
		{
			if($counter > 0 && $condition['logic_operator'] == 'or') //New conditional rule
			{
				$and_item_results[] = $current_logic_result;
				$current_logic_result = true;
			}
			
			
			$counter++;
			if($condition['condition_type'] == 'product')
			{
				$exists_at_least_a_condition_bounded_to_product = true;
				$current_logic_result = $current_logic_result && $wcccf_product_model->products_satisfy_conditional_rule($condition, 'product', $consider_only_product_bounded_fields);
			}
			else if($condition['condition_type'] == 'category')
			{
				$exists_at_least_a_condition_bounded_to_product = true;
				$current_logic_result = $current_logic_result && $wcccf_product_model->taxonomy_satisfy_conditional_rule($condition, 'product_cat', $consider_only_product_bounded_fields);
			}
			else if($condition['condition_type'] == 'tag')
			{
				$exists_at_least_a_condition_bounded_to_product = true;
				$current_logic_result = $current_logic_result && $wcccf_product_model->taxonomy_satisfy_conditional_rule($condition, 'product_tag', $consider_only_product_bounded_fields);
			}
			else if($condition['condition_type'] == 'user' || $condition['condition_type'] == 'customer' )
			{
				$current_logic_result = $current_logic_result && $wcccf_customer_model->customer_satisfy_conditional_rule($condition);
			}
			else if($condition['condition_type'] == 'customer')
			{
				$current_logic_result = $current_logic_result && $wcccf_customer_model->customer_satisfy_conditional_rule($condition);
			}
			else if($condition['condition_type'] == 'cart')
			{
				$current_logic_result = $current_logic_result && $wcccf_cart_model->cart_satisfy_conditional_rule($condition);
			}
			
		}
		//Final loop iteration
		$and_item_results[] = $current_logic_result;
		
		//"Or" operation on the "And" results
		foreach($and_item_results as $and_result)
			$final_result = $and_result || $final_result;
		
		

		if($consider_only_product_bounded_fields  && !$exists_at_least_a_condition_bounded_to_product )
			return false;
		
		return $final_result;
	}
	private function get_last_field_name($fields , $field_type = "billing")
	{
		if(!isset($fields[$field_type]))
			return false;
		end($fields[$field_type]); 
		return key($fields);
	}
	public function get_next_free_id()
	{
		$result = $this->get_field_data();
		$max = 0;
		if(is_array($result) && !empty($result))
		{
			$keys = array_keys($result);
			foreach($keys as $key)
				$max = $key > $max ? $key : $max;
				
			$max++;
		}
		return $max;
	}
	public function duplicate_field($field_to_duplicate, $type, $next_id)
	{
		$fields = $this->get_field_data();
		if(isset($fields[$field_to_duplicate]))
		{
			$copy = $fields[$field_to_duplicate];
			foreach($copy['name'] as $lang => $content)
				$copy['name'][$lang] = $content." ".esc_html__('(Copy)','woocommerce-conditional-checkout-fields');
			
			$copy['unique_id'] = $this->generate_unique_id();
			$fields[$next_id] = $copy;
			//wcccf_var_dump($fields);			
			
			$this->save_field_data($fields);
		}
	}
	public function delete_field_data()
	{
		delete_option('wcccf_field_configuration_data');
		$this->field_data = null;
	}
	public function save_field_data($data_to_save)
	{
		update_option('wcccf_field_configuration_data', $this->stripslashes_deep($data_to_save));
		$this->field_data = null;
	}
	public function save_field_order($data_to_save)
	{
		update_option('wcccf_field_order_data', $data_to_save);
		$this->field_order = null;
	}
	public function save_checkout_options($data_to_save)
	{
		update_option('wcccf_checkout_options', $data_to_save);
		$this->checkout_options = null;
	}
	public function delete_field_order()
	{
		delete_option('wcccf_field_order_data');
		$this->field_order = null;
	}
	public function sort_field_arrays($global_checkout_field_array, $field_order)
	{
		
		$sorted_field_array = $unsorted_fields_array = array();
		foreach($global_checkout_field_array as $unique_id => $field_data)
		{
			if(isset($field_order[$unique_id]))
				$sorted_field_array[$field_order[$unique_id]["sort_index"]] = $field_data;
			else
				$unsorted_fields_array[] = $field_data;
		}
		
		if(count($unsorted_fields_array) > 0 )
			$sorted_field_array = array_merge($sorted_field_array, $unsorted_fields_array);
		
		ksort($sorted_field_array);
		
		return $sorted_field_array;
	}
	public function get_checkout_options()
	{
		$result = !isset($this->checkout_options) ? get_option('wcccf_checkout_options') : $this->checkout_options;
		$this->checkout_options = $result;
		return isset($result) && is_array($result) ? $result : array();
	}
	public function get_checkout_option($option_name)
	{
		$options = !isset($this->checkout_options) ? get_option('wcccf_checkout_options') : $this->checkout_options;
		$this->checkout_options = $options;
		$options = isset($options) && is_array($options) ? $options : array();
		$result = false;
		
		switch($option_name)
		{
			case 'disable_checkout_sort_and_hide': 
				$result = isset($options['disable_checkout_sort_and_hide']) ? $options['disable_checkout_sort_and_hide'] : 'no';
			break;
		}
		
		return $result;
	}
	public function get_field_order()
	{
		$result = !isset($this->field_order) ? get_option('wcccf_field_order_data') : $this->field_order;
		$this->field_order = $result;
		
		$index = 0;
		if(isset($result))
			foreach((array)$result as $result_index => $single_result)
			{
				$result[$result_index]["sort_index"] = $index++;
			}
		
		return isset($result) && is_array($result) ? $result : array();
	}
	private function stripslashes_deep($value)
	{
		$value = is_array($value) ?
					array_map('stripslashes_deep', $value) :
					stripslashes($value);

		return $value;
	}
	public function get_field_data($checkout_type = "all")
	{
		$data_from_db = !isset($this->field_data) ? get_option('wcccf_field_configuration_data') : $this->field_data;
		
		$this->field_data = $data_from_db;
		$data = array();
		if($checkout_type != 'all')
		{
			foreach((array)$data_from_db as $field_id => $field_data)
			{
				if(is_array($field_data) && $field_data['checkout_type'] == $checkout_type)
					$data[$field_id] = $field_data;
			}
		}
		else
		{
			$data = $data_from_db;
		}
		return is_array($data) ? $data : array();
	}
	public function get_core_field_data($type = "billing", $country ="")
	{
		$WC_Countries = new WC_Countries();
		$data_from_db = !isset($this->core_field_data) ? $WC_Countries->get_address_fields( $country, $type."_" ) : $this->core_field_data;
		return $data_from_db;
	}
	public function get_field_readable_value($meta_value, $form_field, $prev_country_id = "", $order = null)
	{
		global $wcccf_country_model,$wcccf_file_model;
		$time_format = get_option('time_format');
		$date_format = get_option('date_format');
		
		if(isset($form_field['options']) && isset($meta_value) && !is_array($meta_value)) //used for Country too
		{
			$meta_value = isset($form_field['options'][$meta_value]) ? $form_field['options'][$meta_value] : $meta_value ;
		}
		
		if($form_field['wcccf_type'] == 'state' && $prev_country_id != "")
		{
			$meta_value = $wcccf_country_model->state_code_to_name($meta_value, $prev_country_id );
			$prev_country_id = "";
		}
		elseif($form_field['wcccf_type'] == 'time')
		{
			$date = DateTime::createFromFormat("H:i", $meta_value );
			if(is_object($date))
				$meta_value = $date->format($time_format );
		}
		elseif($form_field['wcccf_type'] == 'date')
		{
			$date = DateTime::createFromFormat("Y-m-d", $meta_value );
			if(is_object($date))
				$meta_value = $date->format($date_format);
		}
		elseif($form_field['wcccf_type'] == 'wccf_multiselect' || $form_field['wcccf_type'] == 'select' && is_array($meta_value))
		{
			$selected_values = array();
			foreach($meta_value as $current_index)
				if(isset($form_field['options'][$current_index]))
					$selected_values[] = $form_field['options'][$current_index];
			
			//$meta_value = isset($meta_value) && is_array($meta_value) ? implode(", ", $meta_value) : $meta_value;
			$meta_value = !empty($selected_values) ? implode(", ", $selected_values) : "";
		}
		elseif($form_field['wcccf_type'] == 'checkbox')
		{
			$meta_value = isset($meta_value) && $meta_value != "" ? esc_html__('Accepted','woocommerce-conditional-checkout-fields') : esc_html__('Not accepted','woocommerce-conditional-checkout-fields');
		}
		elseif($form_field['wcccf_type'] == 'password')
		{
			$meta_value = isset($meta_value) ?  esc_html__('The one selected','woocommerce-conditional-checkout-fields') : esc_html__('N/A','woocommerce-conditional-checkout-fields');
		}
		else if($form_field['wcccf_type'] == 'wcccf_file')
		{
			$meta_value = $meta_value != "" ? $wcccf_file_model->get_file_download_link($meta_value , $order) : esc_html__('N/A','woocommerce-conditional-checkout-fields');
		}
		return $meta_value;
	}
	public function generate_unique_id()
	{
		$length = 15;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		$field_data = $this->get_field_data();
		$already_existing_ids = array();
		foreach($field_data as $tmp_field_data)
		{
			if(isset($tmp_field_data['unique_id']))
				$already_existing_ids[] = $tmp_field_data['unique_id'];
		}
		
		do
		{
			$randomString = "";
			for ($i = 0; $i < $length; $i++) 
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			
		}
		while(in_array($randomString, $already_existing_ids)); 

		return $randomString;
	}
	private function get_specific_dates_to_disable($data)
	{
		$options = isset($data) ? $data : "";
		$options = $options == "" && isset($data) ? $data:$options;
		$options = trim($options);
		
		$options = $options != "" ? explode(PHP_EOL, $options) : array();
		
		if(empty($options))
			return $options;
		
		try{
			$day_and_month = array(); 
			foreach((array)$options as $option)
			{
				$tmp_result = explode(":", $option);
				$day_and_month[] = date('Y').",".(trim($tmp_result[1])-1).",".trim($tmp_result[0]); //year,month, day
				$day_and_month[] = date("Y",strtotime("-1 year")).",".(trim($tmp_result[1])-1).",".trim($tmp_result[0]); //previous year
				$day_and_month[] = date("Y",strtotime("+1 year")).",".(trim($tmp_result[1])-1).",".trim($tmp_result[0]); //next year
			}
		}catch(Exception  $e){return array();}
		
		return $day_and_month;
		
	}
	
	private function get_select_labels_and_values($select_data, $empty_value = false)
	{
		global $wcccf_wpml_helper;
		
		$options = isset($select_data[$wcccf_wpml_helper->get_current_locale()]) ? $select_data[$wcccf_wpml_helper->get_current_locale()] : "";
		$options = $options == "" && isset($select_data[$wcccf_wpml_helper->get_default_locale()]) ? $select_data[$wcccf_wpml_helper->get_default_locale()] :$options;
		$options = trim($options);
		
		$options = $options != "" ? explode(PHP_EOL, $options) : array();
		if(empty($options))
			return $options;
		
		try{
			$label_and_values = $empty_value ? array("" => "") : array();
			foreach((array)$options as $option)
			{
				$tmp_result = explode(":", $option);
				$label_and_values[trim($tmp_result[0])] = trim($tmp_result[1]);
			}
		}catch(Exception  $e){return array();}
		return $label_and_values;
	}
}
?>