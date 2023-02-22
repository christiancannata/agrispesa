<?php 
class WCCCF_Fee
{
	var $fee_data;
	public function __construct()
	{
	}
	public function get_fee_data()
	{
		$data_from_db = !isset($this->fee_data) ? get_option('wcccf_fee_configuration_data') : $this->fee_data;
		
		$this->fee_data = $data_from_db;
		
		return is_array($this->fee_data) ? $this->fee_data : array();
	}
	public function delete_fee_data()
	{
		delete_option('wcccf_fee_configuration_data');
		$this->fee_data = null;
	}
	public function save_fee_data($data_to_save)
	{
		update_option('wcccf_fee_configuration_data', $this->stripslashes_deep($data_to_save));
		$this->fee_data = null;
	}
	private function stripslashes_deep($value)
	{
		$value = is_array($value) ?
					array_map('stripslashes_deep', $value) :
					stripslashes($value);

		return $value;
	}
	public function get_next_free_id()
	{
		$result = $this->get_fee_data();
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
	public function duplicate_fee($field_to_duplicate, $next_id)
	{
		global $wcccf_field_model;
		$fields = $this->get_fee_data();
		
		if(isset($fields[$field_to_duplicate]))
		{
			$copy = $fields[$field_to_duplicate];
			foreach($copy['name'] as $lang => $content)
				$copy['name'][$lang] = $content." ".esc_html__('(Copy)','woocommerce-conditional-checkout-fields');
			
			foreach($copy['options']['fee_cart_label'] as $lang => $content)
				$copy['options']['fee_cart_label'][$lang] = $content." ".esc_html__('(Copy)','woocommerce-conditional-checkout-fields');
				
			$copy['unique_id'] = $wcccf_field_model->generate_unique_id();
			$fields[$next_id] = $copy;
			
			$this->save_fee_data($fields);
		}
	}
	public function verify_logic_conditions($fee_data, $posted_data)
	{
		global $wcccf_product_model, $wcccf_customer_model, $wcccf_cart_model;
		$and_item_results = array();
		$current_logic_result = true;
		$final_result = false;
		$parameters_array = array();
		$counter = 0;
		$apply_fee_to_each_detected_item = false;
		$number_of_detected_items = 1;
		
		if(isset($posted_data['post_data']))
			parse_str($posted_data['post_data'], $parameters_array);
		
		$parameters_array = empty($parameters_array) ? $_POST : $parameters_array; //when submitting data on checkout, $_POST holds  all the required data
		
		foreach($fee_data as $condition)
		{
			if($counter > 0 && $condition['logic_operator'] == 'or') //New conditional rule
			{
				$and_item_results[] = $current_logic_result;
				$current_logic_result = true;
			}
			
			$counter++;
			if($condition['condition_type'] == 'product')
			{
				$current_logic_result =  $current_logic_result && $wcccf_product_model->products_satisfy_conditional_rule($condition);
				//Cannot be used due to a WooCommerce bug on 'get_category_ids' method: $apply_fee_to_each_detected_item = wcccf_get_value_if_set($condition, 'application_policy', 'once') != 'once';
				
			}
			else if($condition['condition_type'] == 'category')
			{
				$current_logic_result =  $current_logic_result && $wcccf_product_model->taxonomy_satisfy_conditional_rule($condition, 'product_cat');
				//Cannot be used due to a WooCommerce bug on 'get_category_ids' method: $apply_fee_to_each_detected_item = wcccf_get_value_if_set($condition, 'application_policy', 'once') != 'once';
			}
			else if($condition['condition_type'] == 'tag')
			{
				$current_logic_result =  $current_logic_result && $wcccf_product_model->taxonomy_satisfy_conditional_rule($condition, 'product_tag');
				//Cannot be used due to a WooCommerce bug on 'get_category_ids' method: $apply_fee_to_each_detected_item = wcccf_get_value_if_set($condition, 'application_policy', 'once') != 'once';
			}
			else if($condition['condition_type'] == 'user')
			{
				$current_logic_result =  $current_logic_result && $wcccf_customer_model->customer_satisfy_conditional_rule($condition);
			}
			else if($condition['condition_type'] == 'cart')
			{
				$current_logic_result =  $current_logic_result && $wcccf_cart_model->cart_satisfy_conditional_rule($condition);
			}
			else if($condition['condition_type'] == 'payment' && isset($_POST['payment_method']))
			{
				
				
				$current_logic_result =  $current_logic_result && (
											($condition['payment_operator'] == 'equal' && $_POST['payment_method'] == $condition['payment_gatway_id']) || 
											($condition['payment_operator'] == 'not_equal' && $_POST['payment_method'] != $condition['payment_gatway_id']) 
										);
			}
			else if($condition['condition_type'] == 'shipping_method' && isset($_POST['shipping_method']))
			{
				$shipping_method_id = $_POST['shipping_method'][0];
				$current_logic_result =  $current_logic_result && (
											($condition['shipping_method_operator'] == 'equal' && $shipping_method_id == $condition['shipping_method_id']) || 
											($condition['shipping_method_operator'] == 'not_equal' && $shipping_method_id != $condition['shipping_method_id']) 
										);
			}
			else if(($condition['condition_type'] == 'billing_postcode' || 
					 $condition['condition_type'] == 'shipping_postcode' || 
					 $condition['condition_type'] == 'billing_city' || 
					 $condition['condition_type'] == 'shipping_city'))
			{
				$condition_type_data = explode("_", $condition['condition_type']);
				if(!isset($parameters_array[$condition_type_data[0].'_'.$condition_type_data[1]]))
					$current_logic_result = false;
				else
				{
					$field_value = trim($parameters_array[$condition_type_data[0].'_'.$condition_type_data[1]]);
					$field_value = strtolower($field_value);
					$condition['value'] = strtolower($condition['value']);
					$current_logic_result =  $current_logic_result && (
												($condition['operator'] == 'equal' && $field_value == $condition['value']) || 
												($condition['operator'] == 'not_equal' && $field_value != $condition['value']) || 
												($condition['operator'] == 'greater_or_equal' && $field_value >= $condition['value']) || 
												($condition['operator'] == 'lesser_or_equal' && $field_value <= $condition['value']) 
											);
					
				}
			}
			else if(($condition['condition_type'] == 'billing_state_country' || $condition['condition_type'] == 'shipping_state_country') && !empty($parameters_array))
			{
				$type = $condition['type'];
				if( 	$condition[$type."_country"] == 'any' || 
					   (isset($condition[$type."_country"]) && isset($parameters_array[$type."_country"]) &&  strtolower($condition[$type."_country"]) == strtolower($parameters_array[$type."_country"]) && ($condition[$type."_state"] == 'any' || $condition[$type."_state"] == "")) ||
					   (isset($condition[$type."_country"]) && isset($parameters_array[$type."_country"]) &&  strtolower($condition[$type."_country"]) == strtolower($parameters_array[$type."_country"]) && strtolower($condition[$type."_state"]) == strtolower($parameters_array[$type."_state"]))
				   )
				   {
					   $current_logic_result = $current_logic_result && true;
				   }
				else 
					$current_logic_result = $current_logic_result && false;
			}
			
		}
		
		//Final loop iteration
		$and_item_results[] = $current_logic_result;
		
		//"Or" operation on the "And" results
		foreach($and_item_results as $and_result)
			$final_result = $and_result || $final_result;
			
			
		if($apply_fee_to_each_detected_item)
		{
			$number_of_detected_items = 0;
			foreach($wcccf_product_model->conditional_rule_additional_data['item_to_consider'] as $item_to_consider)
				$number_of_detected_items += $item_to_consider['value'];
		}
		
		return array('can_be_applied' => $final_result, 'apply_fee_to_each_detected_item' => $apply_fee_to_each_detected_item , 'number_of_detected_items' => $number_of_detected_items);
	}
}