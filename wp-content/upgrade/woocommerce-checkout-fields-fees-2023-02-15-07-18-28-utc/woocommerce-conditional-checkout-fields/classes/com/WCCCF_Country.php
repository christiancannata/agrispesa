<?php 
class WCCCF_Country 
{
	function __construct()
	{
		add_action('wp_ajax_wcccf_load_state', array(&$this, 'ajax_render_state_field'));
		add_action('wp_ajax_nopriv_wcccf_load_state', array(&$this, 'ajax_render_state_field'));
		add_action('wp_ajax_wcccf_load_states_by_country_id', array(&$this, 'ajax_render_states_selector_by_country_id'));
	}
	public function render_states_selector_by_country_id($country_id = 'none' , $item_type = "billing_state" , $field_id = "", $conditional_item_id = "", $selected = "")
	{
		$states_data = $this->get_state_by_country($country_id, true);
		$states = $states_data['states'];
		$label_data = $states_data['label'];
		
		
		if($field_id === "" || $conditional_item_id === "" )
		{
	
		}	
		else if ( is_array( $states ) && empty( $states ) ) //Like Germany, it doesn't have a states/provinces
		{
			?>
			<input type="hidden" name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][<?php echo $item_type; ?>]" value="<?php echo $selected;?>" />
			<?php 
		}
		elseif(is_array($states)) //Ex.: Italy, Brazil
		{
			?>
			<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][<?php echo $item_type; ?>]" >
				<option value="any" <?php selected($selected, 'any'); ?>><?php esc_html_e('Any','woocommerce-conditional-checkout-fields');?></option>
				<?php foreach($states as $state_code => $state_name): ?> 
					<option value="<?php echo $state_code;?>" <?php selected($selected, $state_code); ?>><?php echo $state_name;?></option>
				<?php endforeach; ?>
			</select>
			<?php 
		}
		else //$states is false. Ex.: UK
		{
			?>
			<input type="text" value="<?php echo $selected;?>" name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][<?php echo $item_type; ?>]" />
			<small class="wcccf_state_description"><?php esc_html_e('Leave empty to apply to all or type the name of the state/county/province.', 'woocommerce-conditional-checkout-fields'); ?></small>
			<?php 
		}
		
		
	}
	public function ajax_render_states_selector_by_country_id()
	{
		$country_id = isset($_POST['country_code']) ? $_POST['country_code'] : "";
		$item_type = isset($_POST['item_type']) ? $_POST['item_type'] : "";
		$field_id = isset($_POST['field_id']) ? $_POST['field_id'] : "";
		$conditional_item_id = isset($_POST['item_id']) ? $_POST['item_id'] : "";
		$this->render_states_selector_by_country_id($country_id, $item_type, $field_id , $conditional_item_id);
		wp_die();
	}
	public function ajax_render_state_field()
	{
		$country_id = isset($_POST['country_code']) ? $_POST['country_code'] : 'none';
		$form_type = isset($_POST['form_type']) ? $_POST['form_type'] : 'billing';
		$unique_id = isset($_POST['unique_id']) ? $_POST['unique_id'] : 'none';
		$is_checkout_page = isset($_POST['is_checkout_page']) ? $_POST['is_checkout_page'] == 'true' : true;
		$state_selector_width = isset($_POST['state_selector_width']) ? $_POST['state_selector_width'] : 'wide';
		$prev_state_value = isset($_POST['prev_state_value']) && $_POST['prev_state_value'] != 'none' ? $_POST['prev_state_value'] : null;
		if($country_id == 'none' || $unique_id  == 'none')
		{
			echo "none";
			wp_die();
		}
		$states_data = $this->get_state_by_country($country_id, true);
		$states = $states_data['states'];
		$label_data = $states_data['label'];
		
		if ( is_array( $states ) && empty( $states ) ) //Like Germany, it doesn't have a states/provinces
		{
			woocommerce_form_field($form_type.'_wcccf_id_'.$unique_id."_state", array(
							'type'       => 'hidden',
							'class'      => array( 'form-row-'.$state_selector_width ),
							//'label_class' => array( 'wcmca_form_label' ),
							'value'    => $states,
							'required' => false,
							'label'      => !isset($label_data[$country_id]['state']['label']) ? "&nbsp;": $label_data[$country_id]['state']['label'],
							'custom_attributes'  => array('required' => 'required')
							), $prev_state_value
					);
		}
		elseif(is_array($states)) //Ex.: Italy, Brazil
		{
			$reordered_states = array();
			$reordered_states[""] = esc_html__('Select one','woocommerce-conditional-checkout-fields');
			foreach($states as $state_code => $state_name)
				$reordered_states[$state_code] = $state_name;
			
			$required = isset($label_data[$country_id]['state']['required']) ? $label_data[$country_id]['state']['required'] : false;
			$custom_attributes = $required ? array('required' => 'required') : array();
			woocommerce_form_field($form_type.'_wcccf_id_'.$unique_id."_state", array(
							'type'       => 'select',
							'required'          => $required,
							'class'      => array( 'form-row-'.$state_selector_width ),
							'label'      => !isset($label_data[$country_id]['state']['label']) ? esc_html__('State / County','woocommerce-conditional-checkout-fields') : $label_data[$country_id]['state']['label'],
							//'label_class' => array( 'wcmca_form_label' ),
							'input_class' => array('wcccf_select2','wcccf_state_select', 'not_empty'),
							'options'    => $reordered_states,
							'custom_attributes'  => $custom_attributes
							), $prev_state_value 
					);
		}
		else //$states is false. Ex.: UK
		{
			$required = isset($label_data[$country_id]['state']['required']) ? $label_data[$country_id]['state']['required'] : false;
			$custom_attributes = $required ? array('required' => 'required') : array();
			woocommerce_form_field($form_type.'_wcccf_id_'.$unique_id."_state", array(
						'type'       => 'text',
						'class'      => array( 'form-row-'.$state_selector_width ),
						'required'          => $required,
						'label'      => !isset($label_data[$country_id]['state']['label']) ? esc_html__('State / County','woocommerce-conditional-checkout-fields') : $label_data[$country_id]['state']['label'],//esc_html__('State / Province','woocommerce-conditional-checkout-fields'),
						'custom_attributes'  => $custom_attributes
						), $prev_state_value
					);
		}
		
		wp_die();
	}
	public function get_state_by_country($country_id, $return_label_data = false, $type = 'billing')
	{
		$countries_obj   = new WC_Countries();
		
		$states = $countries_obj->get_states( $country_id ); //paramenter -> GB, IT ... is the "value" selected in the $countries select box
		$label_data =  $countries_obj->get_country_locale();
		
		return !$return_label_data ? $states : array('label' =>$label_data, 'states' => $states);
	}
	
	function get_countries($type = 'all', $empty_selection = false)
	{
		$countries_obj  = new WC_Countries();
		
		switch($type)
		{
			default:
			case 'all': $countries = $countries_obj->get_countries();
			break;
			case 'allowed': $countries   = $countries_obj->get_allowed_countries();
			break;
			case 'shipping_countries': $countries  = $countries_obj->get_shipping_countries();
			break;
		}
		
		if(count($countries) > 1)
		{
			$reordered_states = array();
			if( $empty_selection)
				$reordered_states[""] = esc_html__('Select one','woocommerce-conditional-checkout-fields');
			foreach($countries as $country_code => $country_name)
				$reordered_states[$country_code] = $country_name;
		}
		else
			$reordered_states = $countries;
		return $reordered_states;
	}
	function get_countries_with_states()
	{
		$country = $this->get_countries();
		$result = array();
		
		foreach((array)$country as $country_code => $country_name)
		{
			$states = $this->get_state_by_country($country_code);
			if(is_array($states) && !empty($states))
				$result[$country_code] = $country_name;
		}
		return $result;
	}
	function country_code_to_name($code)
	{
		$countries_obj   = new WC_Countries();
		return  isset($countries_obj->countries[ $code ])  ? $countries_obj->countries[ $code ]  : $code;
	}
	function state_code_to_name($state_code, $country_code = null )
	{
		$countries_obj   = new WC_Countries();
		$result = $countries_obj->get_states($country_code );
	
		if($result)
		{
			if($country_code == null)
			{
				foreach($result as $country)
					if(isset($country[$state_code]))
						return $country[$state_code];
			}
			else if(isset($result[$state_code]))
				return $result[$state_code];
			else
				return isset($result[$country_code][$state_code]) ? $result[$country_code][$state_code] : $state_code;
		}
		return $state_code;
	}
}
?>