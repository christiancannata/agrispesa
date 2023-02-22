<?php 
class WCCCF_Order
{
	function __construct()
	{
		//save after checkout
		add_action('woocommerce_new_order_item', array( &$this, 'update_order_item_meta' ),10,3);
		
		//on delete 
		add_action( 'before_delete_post', array( &$this, 'before_order_is_deleted' ), 10 );
	}
	public function is_meta_existing($order_id, $meta_name)
	{
		$check = wp_cache_get($order_id, 'post_meta');
		return !isset($check[$meta_name]) ? false : true;
	}
	public function save_meta($order_id, $meta_key, $meta_value)
	{
		$order = wc_get_order($order_id);
		if(is_bool($order))
			return;
		$order->update_meta_data($meta_key, $meta_value);
		$order->save();
	}
	public function delete_meta($order_id, $meta_key)
	{
		$order = wc_get_order($order_id);
		if(is_bool($order))
			return;
		$order->delete_meta_data($meta_key);
		$order->save();
	}
	public function ships_to_differt_address($order_id)
	{
		$order = new WC_Order($order_id);
		$result = $order->get_meta('_ship_to_different_address');
		
		return $result == 'yes' ? true : false;
	}
	public function set_ships_to_differt_address($order_id, $value)
	{
		$this->save_meta($order_id, '_ship_to_different_address', $value);
	}
	
	
	function update_order_item_meta($item_id, $item, $order_id )
	{
		/* DATA RETRIEVED FROM CART ITEM META SAVED ON woocommerce_get_cart_item_from_session DEFINED ON CART MODEL */
		global $wcccf_field_model;
		if(!isset($item->legacy_values))
			return;
		$values = $item->legacy_values;
		$form_fields = $wcccf_field_model->get_form_field_data("billing", false);
		$form_fields = $wcccf_field_model->get_form_field_data("shipping", false, $form_fields);
		
		
		if(isset($values['wcccf_booking_item']))
		{
			foreach($values['wcccf_booking_item'] as  $person_index => $field_data)
			{
				foreach($field_data as $field_content)
				{
					if($field_content['field_type'] == 'state' || $field_content['field_type'] == 'country')
						continue;
					
					
					$field_array_index = $field_content['form_type']."_wcccf_id_".$field_content['field_id'];
					if(isset($form_fields[$field_content['form_type']]) && isset($form_fields[$field_content['form_type']][$field_array_index]) && $field_content['value']  != "")
					{
						$field_content['value'] = $wcccf_field_model->get_field_readable_value($field_content['value'], $form_fields[$field_content['form_type']][$field_array_index]);
					}
					
					wc_add_order_item_meta($item_id, $field_content['label'], $field_content['value'] == "" ? esc_html__('N/A','woocommerce-conditional-checkout-fields') : $field_content['value']);
				}
			}
		}
	}
	
	public function before_order_is_deleted($order_id)
	{
		global $wcccf_file_model;
		$post = get_post($order_id);
		if ($post->post_type == 'shop_order')
		{
			$wcccf_file_model->delete_all_order_files($order_id);
		}
	}
}
?>