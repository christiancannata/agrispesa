<?php
/**
 * The repeat fields specific functionality for the plugin.
 *
 * @link       https://themelocation.com
 * @since      3.1.0
 *
 * @package    add-fields-to-checkout-page-woocommerce-premium
 * @subpackage add-fields-to-checkout-page-woocommerce-premium/utils
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWCFE_Utils_Repeat')):

class THWCFE_Utils_Repeat {
	private static function clean_suffix($text, $incl_parent, $suffix_type, $type){
		if($text && $incl_parent){
			if($suffix_type === 'alphabet'){
				$ch = $type === 'name' ? "_a" : " A";
				$text = rtrim($text, $ch);
			}else if($suffix_type === 'number'){
				$ch = $type === 'name' ? "_1" : " 1";
				$text = rtrim($text, "1");
			}
		}
		return $text;
	}

	private static function prepare_suffix($text, $incl_parent, $suffix_type, $index, $type){
		if($text && $suffix_type != 'none' && is_numeric($index)){
			$alphabet = range('A', 'Z');
			$index = $incl_parent ? $index+1 : $index;
			$text = self::clean_suffix($text, $incl_parent, $suffix_type, $type);

			$suffix = '';
			if($suffix_type === 'alphabet' && $index < 27){
				$suffix = $alphabet[$index-1];
				$suffix = $type === 'name' ? strtolower($suffix) : $suffix;
			}else{
				$suffix = $index;
			}

			$glue = $type === 'name' ? '_' : ' ';
			$text = $text.$glue.$suffix;

			/*if($incl_parent){
				$text = $text.$suffix;
			}else{
				$glue = $type === 'name' ? '_' : ' ';
				$text = $text.$glue.$suffix;
			}*/
		}
		return $text;
	}

	private static function prepare_new_repeat_field_arr($field, $index, $props){
		$new_field = $field;

		/*$name_suffix  = isset($field['rpt_name_suffix']) ? $field['rpt_name_suffix'] : 'number';
		$label_suffix = isset($field['rpt_label_suffix']) ? $field['rpt_label_suffix'] : 'number';
		$incl_parent  = isset($field['rpt_incl_parent']) ? $field['rpt_incl_parent'] : false;
		$incl_parent  = $incl_parent === 'yes' ? true : false;*/
		$name_suffix  = isset($props['name_suffix']) ? $props['name_suffix'] : 'number';
		$label_suffix = isset($props['label_suffix']) ? $props['label_suffix'] : 'number';
		$incl_parent  = isset($props['incl_parent']) ? $props['incl_parent'] : false;

		$name  = isset($field['name']) ? $field['name'] : '';
		$label = isset($field['label']) ? $field['label'] : '';

		$new_name  = self::prepare_suffix($name, $incl_parent, $name_suffix, $index, 'name');
		$new_label = self::prepare_suffix($label, $incl_parent, $label_suffix, $index, 'label');

		$new_field['name']  = $new_name;
		$new_field['label'] = $new_label;
		$new_field['title'] = $new_label;

		return $new_field;
	}

	private static function prepare_new_repeat_field_obj($field, $index, $props){
		$new_field = clone $field;

		/*$name_suffix  = $field->get_property('rpt_name_suffix');
		$label_suffix = $field->get_property('rpt_label_suffix');
		$incl_parent  = $field->get_property('rpt_incl_parent');*/
		$name_suffix  = isset($props['name_suffix']) ? $props['name_suffix'] : 'number';
		$label_suffix = isset($props['label_suffix']) ? $props['label_suffix'] : 'number';
		$incl_parent  = isset($props['incl_parent']) ? $props['incl_parent'] : false;

		$name = $field->get_property('name');
		$label = $field->get_property('title');

		$new_name  = self::prepare_suffix($name, $incl_parent, $name_suffix, $index, 'name');
		$new_label = self::prepare_suffix($label, $incl_parent, $label_suffix, $index, 'label');

		$new_field->set_property('id', $new_name);
		$new_field->set_property('name', $new_name);
		$new_field->set_property('name_old', $new_name);
		$new_field->set_property('title', $new_label);

		return $new_field;
	}

	private static function prepare_repeat_fields_arr($field, $rn=false, $name_only=false){
		$fields = array();
		$name   = $field['name'];
		$r_exp  = isset($field['repeat_rules']) ? $field['repeat_rules'] : false;

		if($r_exp){
			$rn = is_numeric($rn) ? $rn : self::get_repeat_times($r_exp);
			if($rn > 1){
				$rprops = self::prepare_repeat_props($field, false);

				for($i = 1 ; $i < $rn; $i++){ 
					$new_field = self::prepare_new_repeat_field_arr($field, $i, $rprops);
					$new_name = isset($new_field['name']) ? $new_field['name'] : '';

					if($new_name){
						if($name_only){
							$fields[] = $new_name;
						}else{
							$fields[$new_name] = $new_field;
						}
					}
				}
			}
		}		
		return $fields;
	}

	private static function prepare_repeat_fields_obj($field, $rn=false, $name_only=false){
		$fields = array();
		$key    = $field->get_property('name');
		$r_exp  = $field->get_property('repeat_rules');

		if($r_exp){
			$rn = is_numeric($rn) ? $rn : self::get_repeat_times($r_exp);
			if($rn > 1){
				$rprops = self::prepare_repeat_props($field, true);

				for($i = 1 ; $i < $rn; $i++){ 
					$new_field = self::prepare_new_repeat_field_obj($field, $i, $rprops);
					$new_name = $new_field->get_property('name');

					if($new_name){
						if($name_only){
							$fields[] = $new_name;
						}else{
							$fields[$new_name] = $new_field;
						}
					}
				}
			}
		}
		return $fields;
	}

	public static function prepare_repeat_fields_single($field, $rn=false, $name_only=false){
		$fields = false;

		if(THWCFE_Utils_Field::is_valid_field($field)){
			$fields = self::prepare_repeat_fields_obj($field, $rn, $name_only);

		}else if(is_array($field)){
			$fields = self::prepare_repeat_fields_arr($field, $rn, $name_only);
		}
		return empty($fields) ? false : $fields;
	}

	public static function prepare_repeat_fields_set($fieldset, $exclude=array()){
		if(is_array($fieldset)){
			$has_repeat_field = false;
			$new_fieldset = array();
			$exclude = is_array($exclude) ? $exclude : array();

			foreach($fieldset as $name => $field) {
				$new_fieldset[$name] = $field;

				//if(!in_array($name, $exclude)){
					$rfields = self::prepare_repeat_fields_single($field);
					if(is_array($rfields)){
						$has_repeat_field = true;
						$new_fieldset = array_merge($new_fieldset, $rfields);
					}
				//}
			}
			if($has_repeat_field){
				$fieldset = $new_fieldset;
			}
		}
		return $fieldset;
	}



	public static function get_repeat_section_names($order_id){
		$data = get_post_meta( $order_id, '_thwcfe_repeat_sections', true );
		$sections = self::prepare_rsection_names_array($data);
		return $sections;
	}

	public static function get_repeat_section_names_from_posted($posted){
		$data = isset( $_POST['thwcfe_repeat_sections'] ) ? wc_clean( $_POST['thwcfe_repeat_sections'] ) : '';
		$sections = self::prepare_rsection_names_array($data);
		return $sections;
	}

	private static function prepare_rsection_names_array($data){
		$sections = array();
		if($data){
			$rsections = $data ? explode(",", $data) : array();

			foreach ($rsections as $rsnames_str) {
				$snames = $rsnames_str ? explode(":", $rsnames_str) : array();

				if(count($snames) > 1){
					$osname = $snames[0];
					unset($snames[0]);
					$sections[$osname] = $snames;
				}
			}
		}
		return $sections;
	}

	public static function get_repeat_sections($order_id, $key, $section, $rsnames){
		$rsections = false;
		if(is_array($rsnames) && array_key_exists($key, $rsnames)){
			$rn = is_array($rsnames[$key]) ? count($rsnames[$key]) : false;
			if(is_numeric($rn)){
				$rsections = self::prepare_repeat_sections($section, $rn+1);
			}
		}
		return $rsections;
	}


	public static function prepare_repeat_sections_json() {
		$rsections = array(); 
		$sections = THWCFE_Utils::get_custom_sections();
		$cart_info = THWCFE_Utils::get_cart_summary();
		
		foreach($sections as $key => $section) {
			$show_section = true;
			if($key !== 'billing' && $key !== 'shipping'){
				$show_section = THWCFE_Utils_Section::is_show_section($section, $cart_info);
			}

			if($show_section){
				$rsnames = self::prepare_repeat_sections($section, false, true);
				$rsnames = is_array($rsnames) ? implode(':', $rsnames) : false;

				if($rsnames){
					$rsnames = $key.':'.$rsnames;
					$rsections[] = $rsnames;
				}
			}
		}
		return $rsections ? implode(',', $rsections) : '';
	}


	//Deprecating
	public static function prepare_repeat_fields_json() {
		$rfields = array(); 
		$sections = THWCFE_Utils::get_custom_sections();
		$cart_info = THWCFE_Utils::get_cart_summary();
		
		foreach($sections as $sname => $section) {
			$show_section = true;
			if($sname !== 'billing' && $sname !== 'shipping'){
				$show_section = THWCFE_Utils_Section::is_show_section($section, $cart_info);
			}

			if($show_section){
				$fieldset = THWCFE_Utils::get_fieldset_to_show($section);
				$fieldset = $fieldset ? $fieldset : array();
				
				if(is_array($fieldset)){
					foreach ($fieldset as $key => $field) {
						$rnames = self::prepare_repeat_fields_single($field, false, true);
						$rnames = is_array($rnames) ? implode(':', $rnames) : false;

						if($rnames){
							$rnames = $key.':'.$rnames;
							$rfields[] = $rnames;
						}
					}
				}
			}
		}
		return $rfields ? implode(',', $rfields) : '';
	}

	public static function get_repeat_field_names($order_id){
		$fields = array();

		$value = get_post_meta( $order_id, '_thwcfe_repeat_fields', true );
		if($value){
			$rfields = $value ? explode(",", $value) : array();

			foreach ($rfields as $rfnames_str) {
				$fnames = $rfnames_str ? explode(":", $rfnames_str) : array();

				if(count($fnames) > 1){
					$ofname = $fnames[0];
					unset($fnames[0]);
					$fields[$ofname] = $fnames;
				}
			}
		}
		return $fields;
	}

	public static function get_repeat_fields($order_id, $key, $field, $rfnames){
		$rfields = false;

		if(is_array($rfnames) && isset($rfnames[$key])){
			$rn = is_array($rfnames[$key]) ? count($rfnames[$key]) : false;
			if(is_numeric($rn)){
				$rfields = self::prepare_repeat_fields_obj($field, $rn+1);
			}
		}
		return $rfields;
	}

	private static function prepare_repeat_props($field, $isobj){
		$props = array();

		if($isobj){
			$incl_parent  = $field->get_property('rpt_incl_parent');
			$props['incl_parent']  = $incl_parent === 'yes' ? true : false;
			$props['name_suffix']  = $field->get_property('rpt_name_suffix');
			$props['label_suffix'] = $field->get_property('rpt_label_suffix');
		}else{
			$incl_parent = isset($field['rpt_incl_parent']) ? $field['rpt_incl_parent'] : false;
			$props['incl_parent']  = $incl_parent === 'yes' ? true : false;
			$props['name_suffix']  = isset($field['rpt_name_suffix']) ? $field['rpt_name_suffix'] : 'number';
			$props['label_suffix'] = isset($field['rpt_label_suffix']) ? $field['rpt_label_suffix'] : 'number';
		}

		return $props;
	}






	public static function prepare_new_repeat_section($section, $index, $props){
		$new_section = false;
		$fields = $section->get_property('fields');

		if(is_array($fields)){
			$rfields = array();
			foreach($fields as $fname => $field) {
				$rfield = self::prepare_new_repeat_field_obj($field, $index, $props);
				$rfname = $rfield->get_property('name');
				$rfields[$rfname] = $rfield;
			}

			$name_suffix  = isset($props['name_suffix']) ? $props['name_suffix'] : 'number';
			$label_suffix = isset($props['label_suffix']) ? $props['label_suffix'] : 'number';
			$incl_parent  = isset($props['incl_parent']) ? $props['incl_parent'] : false;

			$name = $section->get_property('name');
			$label = $section->get_property('title');

			$new_name  = self::prepare_suffix($name, $incl_parent, $name_suffix, $index, 'name');
			$new_label = self::prepare_suffix($label, $incl_parent, $label_suffix, $index, 'label');

			$new_section = clone $section;
			$new_section->set_property('id', $new_name);
			$new_section->set_property('name', $new_name);
			$new_section->set_property('title', $new_label);
			$new_section->set_property('fields', $rfields);
		}
		return $new_section;
	}

	public static function prepare_repeat_sections($section, $rn=false, $name_only=false){
		$rsections = array();
		$r_exp = $section->get_property('repeat_rules');

		if($r_exp){
			$rn = is_numeric($rn) ? $rn : self::get_repeat_times($r_exp);
			//$rn = is_numeric($rn) ? $rn : 3;

			$sname = $section->get_property('name');
			if($sname === 'billing' && $sname === 'shipping'){
				$rn = 0;
			}

			if($rn > 1){			
				$rprops = self::prepare_repeat_props($section, true);

				for($i = 1 ; $i < $rn; $i++){ 
					$new_section = self::prepare_new_repeat_section($section, $i, $rprops);
					
					if($new_section){
						$new_name = $new_section->get_property('name');
						if($new_name){
							if($name_only){
								$rsections[] = $new_name;
							}else{
								$rsections[$new_name] = $new_section;
							}
						}
					}
				}
			}
		}
		return $rsections;
	}


	











	/* Get Repeat number */
	public static function get_repeat_times($r_exp){
		$rt = 0;
		if($r_exp){
			$exp_arr = explode(":", $r_exp);
			if(count($exp_arr) == 2){
				$operator = $exp_arr[0];
				//$operand_type = $exp_arr[1];
				$operand = $exp_arr[1];

				if($operator === 'qty_product'){
					$rt = self::get_car_item_qty($operand);
				}
			}
		}
		//$rt = apply_filters('thwcfe_ignore_fields', $rt);
		return $rt;
	}

	public static function get_car_item_qty( $product_id ){
	    foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item){
	        if ( $product_id == $cart_item['product_id'] ){
	            return $cart_item['quantity'];
	            // break;
	        }
	    }
	    return 0;
	}
}

endif;