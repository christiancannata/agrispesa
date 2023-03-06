<?php
/**
 * The admin settings page common utility functionalities.
 *
 * @link       https://themelocation.com
 * @since      3.1.0
 *
 * @package    add-fields-to-checkout-page-woocommerce-premium
 * @subpackage add-fields-to-checkout-page-woocommerce-premium/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWCFE_Admin_Utils')):

class THWCFE_Admin_Utils extends WCFE_Checkout_Fields_Utils {
	protected static $_instance = null;	
	
	public function __construct() {		
		
	}
	
	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function init() {	
		$this->prepare_sections_and_fields();
	}

	public static function get_checkout_section($section_name, $cart_info=false){
	 	if(isset($section_name) && !empty($section_name)){	
			$sections = THWCFE_Utils::get_custom_sections();
			if(is_array($sections) && isset($sections[$section_name])){
				$section = $sections[$section_name];	
				if(THWCFE_Utils_Section::is_valid_section($section)){
					return $section;
				} 
			}
		}
		return false;
	}
	
	public function update_section($section){
	 	if(THWCFE_Utils_Section::is_valid_section($section)){	
			$sections = THWCFE_Utils::get_custom_sections();
			$sections = is_array($sections) ? $sections : array();
			
			$sections[$section->name] = $section;
			
			$result1 = $this->save_sections($sections);
			$result2 = $this->update_section_hook_map($section);
	
			return $result1;
		}
		return false;
	}
	
	public function save_sections($sections){
		$result = update_option(THWCFE_Utils::OPTION_KEY_CUSTOM_SECTIONS, $sections);
		return $result;
	}
	
	public function update_section_hook_map($section){
		$section_name  = $section->name;
		$display_order = $section->get_property('order');
		$hook_name 	   = $section->position;
				
	 	if(isset($hook_name) && isset($section_name) && !empty($hook_name) && !empty($section_name)){	
			$hook_map = $this->get_section_hook_map();
			
			//Remove from hook if already hooked
			if($hook_map && is_array($hook_map)){
				foreach($hook_map as $hname => $hsections){
					if($hsections && is_array($hsections)){
						if(($key = array_search($section_name, $hsections)) !== false) {
							unset($hsections[$key]);
							$hook_map[$hname] = $hsections;
						}
					}
					
					if(empty($hsections)){
						unset($hook_map[$hname]);
					}
				}
			}
			
			if(isset($hook_map[$hook_name])){
				$hooked_sections = $hook_map[$hook_name];
				if(!in_array($section_name, $hooked_sections)){
					$hooked_sections[] = $section_name;
					$hooked_sections = $this->sort_hooked_sections($hooked_sections);
					
					$hook_map[$hook_name] = $hooked_sections;
					$this->save_section_hook_map($hook_map);
				}
			}else{
				$hooked_sections = array();
				$hooked_sections[] = $section_name;
				$hooked_sections = $this->sort_hooked_sections($hooked_sections);
				
				$hook_map[$hook_name] = $hooked_sections;
				$this->save_section_hook_map($hook_map);
			}					
		}
	}
	
	public function save_section_hook_map($section_hook_map){
		$result = update_option(THWCFE_Utils::OPTION_KEY_SECTION_HOOK_MAP, $section_hook_map);		
		return $result;
	}
	
	public function remove_section_from_hook($hook_name, $section_name){
		if(isset($hook_name) && isset($section_name) && !empty($hook_name) && !empty($section_name)){	
			$hook_map = $this->get_section_hook_map();
			if(isset($hook_map[$hook_name])){
				$hooked_sections = $hook_map[$hook_name];
				if(!in_array($section_name, $hooked_sections)){
					unset($hooked_sections[$section_name]);				
					$hook_map[$hook_name] = $hooked_sections;
					$this->save_section_hook_map($hook_map);
				}
			}				
		}
	}
	
	public function prepare_sections_and_fields(){
		$sections = $this->get_checkout_sections();
		if(empty($sections)){
			$sections = $this->get_default_sections();
			
			$old_custom_sections = get_option('thwcfd_custom_checkout_sections');
			$old_cfields = get_option('thwcfd_checkout_fields');
			
			if($sections && is_array($sections)){
				foreach($sections as $sname => $section){
					$old_sname = 'wcfd_fields_'.$sname;
					if($old_cfields && is_array($old_cfields) && isset($old_cfields[$old_sname])){
						$old_fields = $old_cfields[$old_sname];
						$fields = $this->prepare_fields_objects($old_fields);
						
						if(!empty($fields)){
							//$section->set_fields($fields);
							$section->set_property('fields', $fields);
						}
					}
				}
				
				$this->save_sections($sections);
				
				if($old_custom_sections && is_array($old_custom_sections)){
					foreach($old_custom_sections as $old_csname => $old_csection){
						$section = $this->prepare_section_object($old_csection, $old_cfields);
						if($section){
							//$sections[$old_csname] = $section;
							$this->update_section($section);
						}
					}
				}
			}
			$this->clear_old_settings();
		}
	}
	
	public function prepare_section_object($section_arr, $fields_arr){
		$section = false;
		if($section_arr && is_array($section_arr)){
			$sname = $section_arr['name'];
			
			$section = new WCFE_Checkout_Section();
			$section->set_property('id', $sname);
			$section->set_property('name', $sname);
			$section->set_property('title', $section_arr['label']);
			$section->set_property('position', $section_arr['position']);
			$section->set_property('custom_section', 1);
			$section->set_property('show_title', $section_arr['use_as_title']);
			/*$section->set_id($sname);
			$section->set_name($sname);
			$section->set_title($section_arr['label']);
			$section->set_position($section_arr['position']);
			$section->set_custom_section(1);
			$section->set_show_title($section_arr['use_as_title']);*/
			
			if($fields_arr && is_array($fields_arr) && isset($fields_arr['wcfd_fields_'.$sname])){
				$old_fields = $fields_arr['wcfd_fields_'.$sname];
				$fields = $this->prepare_fields_objects($old_fields);
				$section->set_property('fields', $fields);
			}
		}
		return $section;
	}
	
	public function prepare_fields_objects($fields){			
		$field_objects = array();
		if($fields && !empty($fields) && is_array($fields)){
			foreach($fields as $name => $field){
				if(!empty($name) && !empty($field) && is_array($field)){
					$field['type'] = isset($field['type']) ? $field['type'] : 'text';
					$field_object = THWCFE_Utils_Field::create_field($field['type'], $name, $field); 
				
					if($field_object){
						$field_objects[$name] = $field_object;
					}
				}
			}
		}
		
		return $field_objects;
	}
	
	public function get_default_sections(){
		$checkout_fields = $this->get_default_checkout_fields();

		$default_sections = array('billing' => 'Billing Fields', 'shipping' => 'Shipping Fields', 'additional' => 'Additional Fields');
		$default_sections = apply_filters('thwcfe_default_checkout_sections', $default_sections);

		$sections = array();
		$order = -3;
		foreach($checkout_fields as $fieldset => $fields){
			$fieldset = $fieldset && $fieldset === 'order' ? 'additional' : $fieldset;
			$title = isset($default_sections[$fieldset]) ? $default_sections[$fieldset] : '';

			$section = new WCFE_Checkout_Section();
			$section->set_property('id', $fieldset);
			$section->set_property('name', $fieldset);
			$section->set_property('order', $order);
			$section->set_property('title', $title);
			$section->set_property('custom_section', 0);
			$section->set_property('fields', $this->prepare_default_fields($fields));

			$sections[$fieldset] = $section;
			$order++;
		}
		
		return $sections;
	}

	public function prepare_default_fields($fields){
		$field_objects = array();

		if(is_array($fields)){
			foreach($fields as $name => $field){
				if(!empty($name) && !empty($field) && is_array($field)){
					$field['type'] = isset($field['type']) ? $field['type'] : 'text';
					$field_object = THWCFE_Utils_Field::create_field($field['type'], $name, $field); 
				
					if($field_object){
						$field_objects[$name] = $field_object;
					}
				}
			}
		}
		return $field_objects;
	}

	public function get_default_checkout_fields($fieldset = '') {
		// Fields are based on billing/shipping country. Grab those values but ensure they are valid for the store before using.
		$billing_country   = WC()->countries->get_base_country();
		$allowed_countries = WC()->countries->get_allowed_countries();

		if ( ! array_key_exists( $billing_country, $allowed_countries ) ) {
			$billing_country = current( array_keys( $allowed_countries ) );
		}

		$shipping_country  = WC()->countries->get_base_country();
		$allowed_countries = WC()->countries->get_shipping_countries();

		if ( ! array_key_exists( $shipping_country, $allowed_countries ) ) {
			$shipping_country = current( array_keys( $allowed_countries ) );
		}

		$checkout_fields = array(
			'billing'  => WC()->countries->get_address_fields(
				$billing_country,
				'billing_'
			),
			'shipping' => WC()->countries->get_address_fields(
				$shipping_country,
				'shipping_'
			),
			'order'    => array(
				'order_comments' => array(
					'type'        => 'textarea',
					'class'       => array( 'notes' ),
					'label'       => __( 'Order notes', 'woocommerce' ),
					'placeholder' => esc_attr__(
						'Notes about your order, e.g. special notes for delivery.',
						'woocommerce'
					),
				),
			),
		);

		$checkout_fields = apply_filters( 'woocommerce_checkout_fields', $checkout_fields );

		foreach ( $checkout_fields as $field_type => $fields ) {
			// Sort each of the checkout field sections based on priority.
			uasort( $checkout_fields[ $field_type ], 'wc_checkout_fields_uasort_comparison' );

			// Add accessibility labels to fields that have placeholders.
			foreach ( $fields as $single_field_type => $field ) {
				if ( empty( $field['label'] ) && ! empty( $field['placeholder'] ) ) {
					$checkout_fields[ $field_type ][ $single_field_type ]['label']       = $field['placeholder'];
					$checkout_fields[ $field_type ][ $single_field_type ]['label_class'] = 'screen-reader-text';
				}
			}
		}

		return $fieldset ? $checkout_fields[ $fieldset ] : $checkout_fields;
	}
	
	public function get_default_sections_(){
		//$default_sections = array('billing' => 'Billing Fields', 'shipping' => 'Shipping Fields', 'additional' => 'Additional Fields', 'address' => 'Address Fields');
		$default_sections = array('billing' => 'Billing Fields', 'shipping' => 'Shipping Fields', 'additional' => 'Additional Fields');
		$default_sections = apply_filters('thwcfe_default_checkout_sections', $default_sections);
		
		$sections = array();
		$order = -3;
		foreach($default_sections as $name => $title){
			$section = new WCFE_Checkout_Section();
			$section->set_property('id', $name);
			$section->set_property('name', $name);
			$section->set_property('order', $order);
			$section->set_property('title', $title);
			$section->set_property('custom_section', 0);
			$section->set_property('fields', $this->get_default_fields($name));
			
			$sections[$name] = $section;
			$order++;
		}
		
		return $sections;
	}
	
	public function get_default_fields_($section_name){
		$fields = false;
		
		if($section_name === 'billing' || $section_name === 'shipping'){
			$country = apply_filters('thwcfe_address_field_default_country', WC()->countries->get_base_country());
			$fields = WC()->countries->get_address_fields($country, $section_name . '_');	
		}else if($section_name === 'additional'){
			$fields = array(
				'order_comments' => array(
					'type'        => 'textarea',
					'class'       => array( 'notes' ),
					'label'       => __( 'Order notes', 'woocommerce' ),
					'placeholder' => esc_attr__(
						'Notes about your order, e.g. special notes for delivery.',
						'woocommerce'
					),
				),
			);
		}

		$field_objects = array();
		if(is_array($fields)){
			foreach($fields as $name => $field){
				if(!empty($name) && !empty($field) && is_array($field)){
					$field['type'] = isset($field['type']) ? $field['type'] : 'text';
					$field_object = THWCFE_Utils_Field::create_field($field['type'], $name, $field); 
				
					if($field_object){
						$field_objects[$name] = $field_object;
					}
				}
			}
		}
		
		return $field_objects;
	}
	
	public function postmeta_form_keys($keys, $post){
		if($post && $post->post_type === 'shop_order'){
			$custom_fields = self::get_all_custom_checkout_fields();
			$custom_field_keys = array();
			if(is_array($custom_fields)){
				foreach($custom_fields as $key => $field){
					$custom_field_keys[] = $key;
				}
			}
			
			if(!empty($custom_field_keys)){
				if(apply_filters('thwcfe_postmeta_form_keys_show_custom_fields_only', false)){
					return $custom_field_keys;
				}
			
				global $wpdb;
			
				if ( null === $keys ) {
					$limit = apply_filters( 'postmeta_form_limit', 30 );
					$sql = "SELECT DISTINCT meta_key
					FROM $wpdb->postmeta
					WHERE meta_key NOT BETWEEN '_' AND '_z'
					HAVING meta_key NOT LIKE %s
					ORDER BY meta_key
					LIMIT %d";
					$keys = $wpdb->get_col( $wpdb->prepare( $sql, $wpdb->esc_like( '_' ) . '%', $limit ) );
				}

				$keys = array_diff($keys, $custom_field_keys);
				$keys = array_merge($custom_field_keys, $keys);
			}			
		}
		return $keys;
	}
	
	public function stable_uasort(&$array, $cmp_function) {
		if(count($array) < 2) {
			return;
		}
		
		$halfway = count($array) / 2;
		$array1 = array_slice($array, 0, $halfway, TRUE);
		$array2 = array_slice($array, $halfway, NULL, TRUE);
	
		$this->stable_uasort($array1, $cmp_function);
		$this->stable_uasort($array2, $cmp_function);
		if(call_user_func_array($cmp_function, array(end($array1), reset($array2))) < 1) {
			$array = $array1 + $array2;
			return;
		}
		
		$array = array();
		reset($array1);
		reset($array2);
		while(current($array1) && current($array2)) {
			if(call_user_func_array($cmp_function, array(current($array1), current($array2))) < 1) {
				$array[key($array1)] = current($array1);
				next($array1);
			} else {
				$array[key($array2)] = current($array2);
				next($array2);
			}
		}
		while(current($array1)) {
			$array[key($array1)] = current($array1);
			next($array1);
		}
		while(current($array2)) {
			$array[key($array2)] = current($array2);
			next($array2);
		}
		return;
	}
	
	public function sort_sections(&$sections){
		if(is_array($sections) && !empty($sections)){
			$this->stable_uasort($sections, array($this, 'sort_sections_by_order'));
		}
	}
	
	public function sort_sections_by_order($a, $b){
		if(THWCFE_Utils_Section::is_valid_section($a) && THWCFE_Utils_Section::is_valid_section($b)){
			$order_a = is_numeric($a->get_property('order')) ? $a->get_property('order') : 0;
			$order_b = is_numeric($b->get_property('order')) ? $b->get_property('order') : 0;
			
			if($order_a == $order_b){
				return 0;
			}
			return ($order_a < $order_b) ? -1 : 1;
		}else{
			return 0;
		}
	}
	
	public function sort_hooked_sections($hsections){
		$sections = array();
		if(is_array($hsections) && !empty($hsections)){
			$custom_sections = $this->get_custom_sections();
			if(is_array($custom_sections) && !empty($custom_sections)){
				foreach($hsections as $sname){
					$temp = array();
					$temp['name'] = $sname;
						
					$section = isset($custom_sections[$sname]) ? $custom_sections[$sname] : false;
					if($section){
						$temp['order'] = $section->get_property('order');
					}else{
						$temp['order'] = 0;
					}
					
					$sections[] = $temp;
				}
			}
		}
	
		$this->stable_uasort($sections, array($this, 'sort_hooked_sections_by_order'));
		$result = array();
		foreach($sections as $section){
			$result[] = $section['name'];
		}
		
		return $result;
	}
	
	public function sort_hooked_sections_by_order($a, $b){
		if(is_array($a) && is_array($b)){
			$order_a = isset($a['order']) && is_numeric($a['order']) ? $a['order'] : 0;
			$order_b = isset($b['order']) && is_numeric($b['order']) ? $b['order'] : 0;
			
			if($order_a == $order_b){
				return 0;
			}
			return ($order_a < $order_b) ? -1 : 1;
		}else{
			return 0;
		}
	}
	
	/********************************************
	*-------- OLDER VERSION SUPPORT - START -----
	********************************************/
	public function clear_old_settings(){
		delete_option("thwcfd_custom_checkout_sections");
		delete_option("thwcfd_section_hook_map");
		delete_option('thwcfd_checkout_fields');
	}
	/********************************************
	*-------- OLDER VERSION SUPPORT - END -------
	********************************************/
}

endif;