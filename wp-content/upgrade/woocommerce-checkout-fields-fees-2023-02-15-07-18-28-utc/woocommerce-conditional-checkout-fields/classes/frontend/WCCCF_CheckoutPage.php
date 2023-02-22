<?php 
class WCCCF_CheckoutPage
{
	function __construct()
	{
		//Validation before placing the order
		add_action('woocommerce_checkout_process', array(&$this, 'process_posted_data'));
		//When the order si created
		add_action('woocommerce_checkout_order_processed', array( &$this, 'save_custom_posted_data' ));
		
	
		add_action('woocommerce_checkout_process', array(&$this,'validate_booking_fields_on_post'));
		
		add_action('woocommerce_before_checkout_form', array(&$this,'add_custom_code'));
	}
	public function process_posted_data()
	{
		global $wcccf_field_model;
		$form_types = array('billing', 'shipping');
		
		foreach($form_types as $form_type)
		{
			$fields = $wcccf_field_model->get_field_data($form_type);	
			foreach($fields as $field)
			{
				if(isset($_POST[$form_type.'_wcccf_id_'.$field['unique_id']]))
				{
					
					$field_name = $wcccf_field_model->get_field_name_by_data($field);
					$posted_field_value = $_POST[$form_type.'_wcccf_id_'.$field['unique_id']];
					switch($field['type'])
					{
						case 'tel': 
								if(!WC_Validation::is_phone($posted_field_value))
									wc_add_notice( wcccf_html_escape_allowing_special_tags(sprintf(__( '<strong>%s</strong> has an invalid format.' , 'woocommerce-conditional-checkout-fields') , $field_name), false), 'error' );
						break;
					}
					
				}
				
			}
			
		}
			
	}
	
	public function add_custom_code()
	{
		global $wcccf_file_model, $wcccf_code_model;
		$js_and_css_files = $wcccf_file_model->get_css_and_js_files();
		$i = 0;
		foreach($js_and_css_files['js'] as $js_path)
		{
			
			wp_enqueue_script('wcccf_custom_js_code_'.($i++), $js_path, array('jquery'));
		}
		foreach($js_and_css_files['css'] as $css_path)
		{
			
			wp_enqueue_style('wcccf_custom_css_code_'.($i++), $css_path);
		}
		
		//Hard code
		$javascript = $wcccf_code_model->get_code('javascript');
		$css = $wcccf_code_model->get_code('css');
		
		echo '<'.'script'.'>'.$javascript.'<'.'/'.'script'.'>';
		echo '<'.'style'.'>'.$css.'<'.'/'.'style'.'>';
	}
	public function save_custom_posted_data($order_id)
	{
		global $wcccf_order_model, $wcccf_file_model;
		$wcccf_order_model->set_ships_to_differt_address($order_id, !isset($_POST['ship_to_different_address']) ? 'no' : 'yes');
		
		//files
		if(isset($_POST['wcccf_files']))
		{
			$wcccf_file_model->process_files_after_checkout($order_id, $_POST['wcccf_files']);
		}
	}
	function validate_booking_fields_on_post() 
	{
		global $wcccf_field_model;
		$cart = WC()->cart->cart_contents ;
		$types = array('billing');
		if(isset($_POST['ship_to_different_address']))
			$types[] = 'shipping';
		
		foreach($cart as $cart_key => $cart_item)
			foreach($types as $type)
			{
				$data = $wcccf_field_model->get_form_field_data($type, true, array(), true, $cart_item['product_id']."-".$cart_item['variation_id']);
				if(isset($cart_item["booking"][esc_html__( 'Persons', 'woocommerce-bookings' )]))
					for($i = 0; $i < $cart_item["booking"][esc_html__( 'Persons', 'woocommerce-bookings' )]; $i++)
						foreach($data[$type] as $form_field_id => $form_field_data)
						{
							if($form_field_data['wcccf_type'] == 'country' ||
								$form_field_data['wcccf_type'] == "state" )
								continue;
								
							if ( (empty( $_POST["wcccf_booking_item"][$cart_item['key']][$i][$form_field_data['wcccf_unique_id']]['value'] ) || $_POST["wcccf_booking_item"][$cart_item['key']][$i][$form_field_data['wcccf_unique_id']]['value'] == "")
								
							&& isset($form_field_data['label']) && $form_field_data['required'])
								wc_add_notice( wcccf_html_escape_allowing_special_tags(sprintf(__('Field <strong>%s</strong> for product <strong>%s</strong> cannot be empty','woocommerce-conditional-checkout-fields'), $form_field_data['label'], $cart_item['data']->get_name()." (".WC()->cart->get_item_data( $cart_item, true ).")"), false), 'error' );
						}
			}
			
	}
}
?>