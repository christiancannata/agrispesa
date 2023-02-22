<?php 
class WCCCF_Cart
{
	var $fee_shipping = false;
	function __construct()
	{
		//add_action('update_order_review', array(&$this, 'compute_fee'))
		add_action('woocommerce_cart_calculate_fees', array(&$this, 'compute_fee'));
		add_filter( 'woocommerce_product_needs_shipping', array( $this, 'apply_free_shipping' ), 10, 2 );
		
		//Used also after the checkout is placed
		add_filter('woocommerce_get_cart_item_from_session', array( &$this, 'update_cart_item_meta' ),10,3); 
	}
	function update_cart_item_meta($session_data, $values, $key)
	{
		if(!isset($_POST) || empty($_POST))
			return $session_data;
		
		global $wcccf_field_model;
		
		$types = array('billing');
		if(isset($_POST['ship_to_different_address']))
			$types[] = 'shipping';
		
		if(isset($_POST['wcccf_booking_item']))
		{
			foreach($_POST['wcccf_booking_item'] as $cart_item_key => $data_array)
			{
				if($cart_item_key == $key)
					foreach($data_array as $person_index => $field_data)
					{
						if(!isset($session_data['wcccf_booking_item']))
							 $session_data['wcccf_booking_item'] = array();
						 
						 if(!isset($session_data['wcccf_booking_item'][$person_index]))
							 $session_data['wcccf_booking_item'][$person_index] = array();
						
						foreach($field_data as $field_unique_id => $field_content)
							if(in_array($field_content['form_type'], $types))
							{
								$session_data['wcccf_booking_item'][$person_index][$field_unique_id] = array('field_id' =>$field_unique_id, 
																											  'label' => $field_content['label'], 
																											  'value' => $field_content['value'], 
																											  'form_type' => $field_content['form_type'], 
																											  'field_type' => $field_content['field_type']);
							}
						
					}
			} 
		}
		
		return $session_data; 
	}
	public function cart_satisfy_conditional_rule($condition)
	{
		global $woocommerce, $wcccf_wpml_helper;
		$cart = $woocommerce->cart;
		$result = false;
		$cart_value = 0;
		switch($condition['cart_condition_type'])
		{
			case 'sub': $cart_value = $cart->subtotal;
			break;
			case 'sub_ex_tax': $cart_value = $cart->subtotal_ex_tax;
			break;
			case 'sub_tax': $cart_value = $cart->subtotal - $cart->subtotal_ex_tax;
			break;
			case 'total':  $cart_value = $cart->total;
			break;
			case 'total_ex_taxes': $cart_value = $cart->total - $cart->tax_total;
			break;
			case 'total_taxes': $cart_value = $cart->tax_total;
			break;
			case 'shipping':  $cart_value = $cart->shipping_total;
			break;
			case 'shipping_ex_taxes': $cart_value = $cart->shipping_total - $cart->shipping_tax_total;
			break;
			case 'shipping_taxes': $cart_value = $cart->shipping_tax_total;
			break;
			case 'discount':  $cart_value = $cart->discount_total;
			break;
			case 'discount_ex_taxes': $cart_value = $cart->shipping_total - $cart->discount_cart_tax;
			break;
			case 'discount_taxes': $cart_value = $cart->get_cart_discount_tax_total;
			break;
			case 'item_quantity': $cart_value = $this->get_cart_product_sum_of_quantities();
			break;
			case 'distinct_item': $cart_value = count($woocommerce->cart->get_cart());
			break;
			case 'weight': $cart_value = $this->get_cart_product_characteristic_value_sum('weight');
			break;
			case 'volume': $cart_value = $this->get_cart_product_characteristic_value_sum('volume');
			break;
		}
		
		if(($condition['cart_operator'] == 'lesser_equal' && $cart_value <= $condition['cart_value'])||
					($condition['cart_operator'] == 'greater_equal' && $cart_value >= $condition['cart_value']) ) //lesser_equal || greater_equal
				{
					$result = true;
				}
				
		return $result;
	}
	
	public function get_cart_product_characteristic_value_sum($value = 'weight')
	{
		global $woocommerce, $wcccf_wpml_helper;
		$cart_items = $woocommerce->cart->get_cart();
		$sum = 0;
		
		foreach($cart_items as $cart_item)
		{
			if($value == 'quantity')
				$sum += $cart_item["quantity"];
			else 
			{
				$product_id = $wcccf_wpml_helper->get_main_language_id($cart_item['data']->get_id());
				$product =  wc_get_product( $product_id );
				switch($value)
				{
					case 'weight': $sum += $product->get_weight();
					break;
					case 'volume': $width = $product->get_width() == "" ? 0 : $product->get_width();
								   $height = $product->get_height() == "" ? 0 : $product->get_height();
								   $lenght = $product->get_length() == "" ? 0 : $product->get_length();
								   $sum += $width * $height * $lenght;
					break;
				}
			}
		}
		
		return $sum;
	}
	public function get_cart_product_sum_of_quantities()
	{
		global $woocommerce, $wcccf_wpml_helper;
		$cart_items = $woocommerce->cart->get_cart();
		$quantity =0;
		foreach($cart_items as $cart_item)
			$quantity += $cart_item["quantity"];
			
		return $quantity;
	}
	public function compute_fee()
	{
		global $woocommerce, $wcccf_fee_model, $wcccf_wpml_helper;
		$parameters = array();
		
		if(function_exists('is_cart') && is_cart())
            return;
			
		$as_at_least_one_fee_has_been_applied = false;
		if (!defined( 'DOING_AJAX' ) ) //Fees added on Checkout, while "update_order_review"
			return;
			
		$fees_data = $wcccf_fee_model->get_fee_data();
		
		$totals = $woocommerce->cart->get_totals();	
		$total_ex_taxes = $woocommerce->cart->get_cart_contents_total() + $totals['shipping_total'];
		$total = $total_ex_taxes + $totals['shipping_tax'] + $totals['subtotal_tax'];
			
		foreach((array)$fees_data as $fee_data)
		{
			$fee_application_data = $wcccf_fee_model->verify_logic_conditions($fee_data['conditional_group_item'],$_POST);
			if(isset($fee_data['conditional_group_item']) && !$fee_application_data['can_be_applied'])
				continue;
			
			$stack_with_other_fee = wcccf_get_value_if_set($fee_data, array('options', 'stack_with_other_fee'), 'no') == 'yes'  ? true : false;
					 	
			if($as_at_least_one_fee_has_been_applied && !$stack_with_other_fee)
				continue;
				
			$lang_code = $wcccf_wpml_helper->get_current_locale();
			if(isset($_POST['post_data']))
			{
				parse_str($_POST['post_data'], $parameters);
				$lang_code = $parameters['wcccf_current_lang'];
			}
			
			/* fee computation */
			$amount = $fee_data['options']['fee_amount'];
			$fee_data['options']['value_type'] = !isset($fee_data['options']['value_type']) ? "fixed" : $fee_data['options']['value_type'];
			switch($fee_data['options']['value_type'])
			{
				case 'cart_total_percentage': $amount = ($total*$amount)/100;
					break;
				case 'cart_total_ex_tax_percentage': $amount = ($total_ex_taxes*$amount)/100;
					break; 
				case 'cart_subtotal_percentage': $amount = (($woocommerce->cart->get_subtotal()+$woocommerce->cart->get_subtotal_tax( ))*$amount)/100;
					break;
				case 'cart_subtotal_ex_tax_percentage': $amount = ($woocommerce->cart->get_subtotal()*$amount)/100;
					break;
				case 'cart_shipping_total_percentage': $amount = (($totals['shipping_total']+$totals['shipping_tax'])*$amount)/100;
					break;
				case 'cart_shipping_total_ex_tax_percentage': $amount = ($totals['shipping_total']*$amount)/100;
					break;
			}
			
			$this->fee_shipping = wcccf_get_value_if_set($fee_data, array('options', 'apply_free_shipping'), 'no') == 'yes' ? true : $this->fee_shipping ;
			$amount = $fee_application_data['apply_fee_to_each_detected_item'] ? $amount * $fee_application_data['number_of_detected_items'] : $amount;
			$woocommerce->cart->add_fee( $fee_data['options']['fee_cart_label'][$lang_code], $amount, $fee_data['options']['fee_is_taxable'] == 'yes' );
			$as_at_least_one_fee_has_been_applied = true;
		}
		
	}
	
	//Free shipping
	public function apply_free_shipping( $needs_shipping, $product )
	{
		return $this->fee_shipping ? false : $needs_shipping;
	}
}
?>