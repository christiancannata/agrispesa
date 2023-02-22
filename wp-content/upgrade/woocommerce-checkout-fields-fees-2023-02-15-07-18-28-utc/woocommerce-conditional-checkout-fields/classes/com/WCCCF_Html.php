<?php 
class WCCCF_Html
{
	var $current_field_type = 'text';
	public function __construct()
	{
		add_action('wp_ajax_wcccf_add_new_field', array($this, 'ajax_add_new_field_configuration_meta_box'));
		add_action('wp_ajax_wcccf_duplicate_field', array($this, 'ajax_add_duplicate_field'));
		add_action('wp_ajax_wcccf_duplicate_fee', array($this, 'ajax_add_duplicate_fee'));
		add_action('wp_ajax_wcccf_load_options_box_by_type', array($this, 'ajax_load_options_box_by_type'));
		//conditional
		add_action('wp_ajax_wcccf_add_new_conditional_group_item', array($this, 'ajax_add_new_conditional_group_item'));
		add_action('wp_ajax_wcccf_add_new_conditional_item', array($this, 'ajax_add_new_conditional_item'));
		add_action('wp_ajax_wcccf_add_new_condition_sub_options_box', array($this, 'ajax_add_new_condition_sub_options_box'));
	}
	public function get_sort_element_template($field_data, $is_hidden = false)
	{
		$class = "wcccf-row-wide";
		if($field_data['width'] == 'first')
			$class = "wcccf-row-first";
		else if($field_data['width'] == 'last')
			$class = "wcccf-row-last";
		?>
		<div class="dragbox <?php echo $class; ?>" data-already-processed="false"  >
		<input type="hidden" name="wcccf_sort[<?php echo $field_data['id']?>][sort_index]" value="<?php $field_data['id']; ?>"/>
				<h2 class="">
					<?php if($field_data['can_be_hidden']): ?>
					<span class="configure">
						<label><?php _e('Hide','woocommerce-conditional-checkout-fields'); ?> <input type="checkbox" name="wcccf_sort[<?php echo $field_data['id']?>][is_hidden]" value="true" <?php if($is_hidden) echo 'checked="checked"';?>></input></label>
					</span>
					<?php endif; 
						echo $field_data['id']; if($field_data['label'] != "") echo ": <span class='wcccf_field_label'>".$field_data['label'].'</span>'; ?>	
				</h2>
		</div>
		<?php
	}
	public function ajax_add_new_field_configuration_meta_box()
	{
		$field_id = isset($_POST['field_id']) ?  $_POST['field_id'] : 0;
		$field_checkout_type = isset($_POST['field_checkout_type']) ?  $_POST['field_checkout_type'] : 0;
		$this->render_field_custom_configuration_meta_box($field_id, $field_checkout_type, true );
		wp_die();
	}
	public function ajax_add_duplicate_field()
	{
		global $wcccf_field_model;
		$field_next_id = isset($_POST['field_next_id']) ?  $_POST['field_next_id'] : -1;
		$field_to_duplicate = isset($_POST['field_to_duplicate']) ?  $_POST['field_to_duplicate'] : -1;
		$field_checkout_type = isset($_POST['field_checkout_type']) ?  $_POST['field_checkout_type'] : "none";
		if($field_to_duplicate > -1 && $field_next_id > -1 && $field_checkout_type != "none")
			$wcccf_field_model->duplicate_field($field_to_duplicate, $field_checkout_type, $field_next_id );
		wp_die();
	}
	public function ajax_add_duplicate_fee()
	{
		global $wcccf_fee_model;
		$field_next_id = isset($_POST['field_next_id']) ?  $_POST['field_next_id'] : -1;
		$field_to_duplicate = isset($_POST['field_to_duplicate']) ?  $_POST['field_to_duplicate'] : -1;
		$field_checkout_type = isset($_POST['field_checkout_type']) ?  $_POST['field_checkout_type'] : "none";
		if($field_to_duplicate > -1 && $field_next_id > -1)
			$wcccf_fee_model->duplicate_fee($field_to_duplicate, $field_next_id );
		wp_die();
	}
	public function ajax_load_options_box_by_type()
	{
		$field_type = isset($_POST['field_type']) ?  $_POST['field_type'] : 'text';
		$field_id = isset($_POST['field_id']) ?  $_POST['field_id'] : 0;
		$this->render_field_options_box($field_type, $field_id);
		wp_die();
	}
	public function ajax_add_new_conditional_group_item()
	{
		$field_id = isset($_POST['field_id']) ?  $_POST['field_id'] : 0;
		$conditional_item_id = isset($_POST['conditional_item_id']) ?  $_POST['conditional_item_id'] : 0;
		$this->current_field_type = isset($_POST['field_type']) ? $_POST['field_type'] : 'text';
		$this->render_conditional_group_item( $field_id, $conditional_item_id);
		wp_die();
	}
	public function ajax_add_new_conditional_item()
	{
		$field_id = isset($_POST['field_id']) ?  $_POST['field_id'] : 0;
		$conditional_item_id = isset($_POST['conditional_item_id']) ?  $_POST['conditional_item_id'] : 0;
		$this->current_field_type = isset($_POST['field_type']) ? $_POST['field_type'] : 'text';
		$this->render_conditional_item( $field_id, $conditional_item_id);
		wp_die();
	}
	public function ajax_add_new_condition_sub_options_box()
	{
		$options_type = isset($_POST['option_type']) ?  $_POST['option_type'] : 0;
		$field_id = isset($_POST['field_id']) ?  $_POST['field_id'] : 0;
		$conditional_item_id = isset($_POST['conditional_item_id']) ?  $_POST['conditional_item_id'] : 0;
		$this->current_field_type = isset($_POST['field_type']) ? $_POST['field_type'] : 'text';
		$this->condition_sub_options_box( $options_type, $field_id, $conditional_item_id);
		wp_die();
	}
	//if $field_id_or_data contains and integer, will be creted an empty field with the passed id 
	public function render_field_custom_configuration_meta_box($field_id_or_data = 0, $field_checkout_type = 'billing', $is_ajax = false)
	{
		global $wcccf_wpml_helper, $wcccf_field_model;
		$field_id_or_data = !is_array($field_id_or_data) ? array($field_id_or_data => array()) : $field_id_or_data;
		foreach($field_id_or_data as $field_id => $field_data)
		{
			$this->current_field_type = isset($field_data['type']) ? $field_data['type'] : "text";
			include WCCCF_PLUGIN_ABS_PATH.'/templates/admin_field_custom_configuration_box.php';
		}
	}
	public function render_fee_configuration_meta_box($field_id_or_data = 0, $field_checkout_type = 'billing', $is_ajax = false)
	{
		$this->render_field_custom_configuration_meta_box($field_id_or_data, $field_checkout_type, $is_ajax);
	}
	public function render_field_core_configuration_meta_box($field_id_or_data = 0, $field_checkout_type = 'billing', $is_ajax = false)
	{
		global $wcccf_wpml_helper, $wcccf_field_model;
		$field_id_or_data = !is_array($field_id_or_data) ? array($field_id_or_data => array()) : $field_id_or_data;
		wcccf_var_dump($field_id_or_data);
		foreach($field_id_or_data as $field_id => $field_data)
		{
			$this->current_field_type = isset($field_data['type']) ? $field_data['type'] : "text";
			include WCCCF_PLUGIN_ABS_PATH.'/templates/admin_field_core_configuration_box.php';
		}
	}
	public function render_field_options_box($field_type_or_data = 'text', $field_id = 0)
	{
		global $wcccf_field_model, $wcccf_wpml_helper, $wcccf_country_model, $wcccf_is_woocommerce_booking_active;
		
		$field_data = is_array($field_type_or_data) ? $field_type_or_data : array();
		$field_type = !is_array($field_type_or_data) ? $field_type_or_data: $field_type_or_data['type'];
		
		
		switch($field_type)
		{
			case 'core':
				break;
			case 'number':
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/common.php';
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/number.php';
				break;
			case 'select':
			//case 'dropdown':
			case 'radio':
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/common.php';
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/select.php';
				break;
			case 'checkbox':
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/common.php';
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/checkbox.php';
				break;
			case 'date':
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/common.php';
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/date.php';
				break;
			case 'time':
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/common.php';
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/time.php';
				break;
			case 'country':
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/common.php';
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/country.php';
				break;
			case 'state':
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/common.php';
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/state.php';
				break;
			case 'file':
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/common.php';
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/file.php';
				break;
			case 'heading':
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/heading.php';
				break;
			case 'fee':
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/fee.php';
				break;
			default:
				include WCCCF_PLUGIN_ABS_PATH.'/templates/field_option_boxes/common.php';
			break;
		}
	}
	public function render_group_conditional_options($field_id = 0, $conditional_options = array())
	{
		global $wcccf_field_model;
		include WCCCF_PLUGIN_ABS_PATH.'/templates/admin_conditional_options_box.php';
		
	}
	public function render_conditional_group_item($field_id = 0, $conditional_item_id = 0, $field_data = array()) 
	{
		global $wcccf_field_model;
		$counter_open = $counter_closed = 0;
		if(!empty($field_data))
		{
			$row_counter = 0;
			foreach($field_data as $conditional_item_id => $coditional_item_data)
			{
				$operator = $coditional_item_data['logic_operator'];
				if($operator == 'or')
				{
					//include WCCCF_PLUGIN_ABS_PATH.'/templates/admin_conditional_group_item.php';
					if($row_counter > 0)
					{
						$counter_closed++;
						echo '</div>';
					}
					echo '<div class="conditional_row">';
					$counter_open++;
					include WCCCF_PLUGIN_ABS_PATH.'/templates/admin_conditional_item.php';
				}
				else
				{
					if($counter_open == 0)
						echo '<div class="conditional_row">';
					$this->render_conditional_item($field_id, $conditional_item_id, $coditional_item_data);
					if($counter_open == 0)
						echo '</div>';
				}
				$row_counter++;
			}
			if($counter_open != $counter_closed)
				echo '</div>';
			
		}
		else 
		{
			$operator = 'or';
			include WCCCF_PLUGIN_ABS_PATH.'/templates/admin_conditional_group_item.php';
		}
	}
	public function render_conditional_item($field_id = 0, $conditional_item_id = 0, $coditional_item_data = array())
	{
		global $wcccf_field_model;
		$operator = 'and';
		include WCCCF_PLUGIN_ABS_PATH.'/templates/admin_conditional_item.php';
	}
	public function condition_sub_options_box($options_type = 'product', $field_id = 0, $conditional_item_id = 0, $coditional_item_data = array())
	{
		global $wcccf_customer_model, $wcccf_product_model, $wcccf_is_woocommerce_booking_active, $wcccf_country_model;
		switch($options_type)
		{
			case 'product': 
				include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/product.php';
				break;
			case 'category': 
				include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/category.php';
				break;
			case 'tag': 
				include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/tag.php';
				break;
			case 'cart': 
				include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/cart.php';
				break;
			case 'user': 
				include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/user.php';
				break;
			case 'customer': 
				include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/customer.php';
				break;
			case 'payment': 
				include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/payment_method.php';
				break;
			case 'shipping_method': 
				include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/shipping_method.php';
				break;
			case 'billing_first_name':
			case 'billing_last_name':
			case 'billing_company':
			case 'billing_address_1':
			case 'billing_address_2':
			case 'billing_city':
			case 'billing_email':
			case 'shipping_first_name':
			case 'shipping_last_name':
			case 'shipping_company':
			case 'shipping_address_1':
			case 'shipping_address_2':
			case 'shipping_city':
			include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/checkout_generic_input_text.php';
				break;
			case 'billing_postcode':
			case 'shipping_postcode':
			include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/checkout_generic_input_numeric.php';
				break;
			case 'billing_state_country': 
				include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/billing_country_state.php';
				break;
			case 'shipping_state_country': 
				include WCCCF_PLUGIN_ABS_PATH.'/templates/conditional_options/shipping_country_state.php'; 
				break;
		}
		
	}
}
?>