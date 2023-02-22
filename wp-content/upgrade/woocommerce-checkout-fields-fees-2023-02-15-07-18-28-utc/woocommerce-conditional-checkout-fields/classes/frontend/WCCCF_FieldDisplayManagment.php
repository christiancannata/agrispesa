<?php 
class WCCCF_FieldDisplayManagment
{
	function __construct()
	{
		add_action('woocommerce_email_after_order_table',array(&$this, 'show_field_in_email'), 8 , 4);
		add_action('woocommerce_order_details_after_order_table',array(&$this, 'render_field_table'));
		
		//Checkout
		add_filter('woocommerce_after_checkout_billing_form', array(&$this,'woocommerce_after_checkout_billing_form'), 10, 3);
		add_filter('woocommerce_after_checkout_shipping_form', array(&$this,'woocommerce_after_checkout_shipping_form'), 10, 3);
	}
	function show_field_in_email($order, $sent_to_admin, $plain_text, $email)
	{
		$this->render_field_table($order, true);
	}
	function add_cart_table_item_extra_fields($text, $cart_item, $cart_item_key)
	{
		global $wcccf_field_model, $wcccf_is_woocommerce_booking_active;
	}
	function woocommerce_after_checkout_billing_form( $checkout  ) 
	{ 
		global $wcccf_field_model, $wcccf_is_woocommerce_booking_active;
		
		if(!$wcccf_is_woocommerce_booking_active)
			return;
		
		$cart = WC()->cart->cart_contents ;
		foreach($cart as $cart_key => $cart_item)
		{
			$wcccf_field_model->render_booking_product_additional_field('billing', $cart_item);
		}
	}
	
	function woocommerce_after_checkout_shipping_form( $checkout  ) 
	{ 
		global $wcccf_field_model, $wcccf_is_woocommerce_booking_active;
		
		if(!$wcccf_is_woocommerce_booking_active)
			return;
		
		$cart = WC()->cart->cart_contents ;
		foreach($cart as $cart_key => $cart_item)
		{
			$wcccf_field_model->render_booking_product_additional_field('shipping',$cart_item);
		}
	}
	
	function render_field_table($order, $is_email = false)
	{
		global $wcccf_field_model, $wcccf_country_model, $wcccf_order_model, $wcccf_file_model;
		$form_types = array('billing' => esc_html__('Billing','woocommerce-conditional-checkout-fields'), 'shipping' =>  esc_html__('Shipping','woocommerce-conditional-checkout-fields'));
		$form_data = array();
		$text_align = is_rtl() ? 'right' : 'left';
		$i = 0;
			
		if(!$is_email)
			wp_enqueue_style('wcccf-order-details-page', WCCCF_PLUGIN_PATH.'/css/frontend-order-details-page.css'); 
		
		
		foreach($form_types  as $form_type => $form_type_label)
		{
			$exists_at_least_one_field = false;
			$form_data = $wcccf_field_model->get_form_field_data($form_type, false, array(), false, "", true);
			$prev_country_id = "";
			$section_label_already_printed = false;
			
			if($form_type == 'shipping' && !$wcccf_order_model->ships_to_differt_address($order->get_id()))
				continue;
			
			ob_start();
			?>
				<?php if(!$is_email): ?>
					<table class="woocommerce-table woocommerce-table--order-details shop_table order_details wcccf_fields_table" >
				<?php else: ?>
					<table class="td" cellspacing="0" cellpadding="6" style="margin-top:20px; width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
				<?php endif; ?>
				<tfoot>
				<?php 
			
			if(isset($form_data[$form_type]))
				foreach($form_data[$form_type] as $field_id => $form_field)
				{
					if($form_field['wcccf_type'] == 'wcccf_heading')
					{
						if( (!$is_email && !$form_field['wcccf_show_in_order_details_page']) || ( $is_email && !$form_field['wcccf_show_in_emails']))
							continue;
						
						$meta_value = "";
						$prefix = "";
						$spacer = "";
					}
					else 
					{
						$prefix = $form_type_label;
						$spacer = " - ";
						$form_field['label'] .=":";
						$meta_value = $order->get_meta("_".$field_id);
						$is_meta_existing = $wcccf_order_model->is_meta_existing($order->get_id(), "_".$field_id);
						$field_id = str_replace("billing_", "", $field_id);
						
						if(($meta_value == '' && !$is_meta_existing) || (!$is_email && !$form_field['wcccf_show_in_order_details_page']) || ($is_email && !$form_field['wcccf_show_in_emails']))
							continue;
						
						
						$exists_at_least_one_field = true;
						if($form_field['wcccf_type'] == 'country')
						{
							$prev_country_id = $meta_value;
						}
						$meta_value = $wcccf_field_model->get_field_readable_value($meta_value, $form_field, $prev_country_id, $order);
					}
					?>
						<?php if(!$is_email):?>
						<tr>
							<th scope="row" class="wcccf_table_fields_th" ><?php echo $form_field['label'];  ?></th>
							<td class="wcccf_table_fields_td" ><?php echo  $meta_value //esc_html( $meta_value ); ?></td>
						</tr>
						<?php else:?>
						<tr>
							<th class="td" scope="row" colspan="2" style="text-align:<?php echo $text_align; ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo /* $prefix.$spacer. */$form_field['label'] ?></th>
							<td class="td" style="text-align:<?php echo $text_align; ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo $meta_value; ?></td>
						</tr>
						<?php $i++; endif;?>
					<?php 
					$i++;
					$section_label_already_printed = true;
				}
			?>
			</tfoot>
			</table>
			<?php
			$html = ob_get_contents();
			ob_end_clean();
		
			if($exists_at_least_one_field)
			{
				echo '<h2 class="woocommerce-order-details__title">'.$form_type_label.'</h2>';
				echo $html;
			}
		}
		
	}
}
?>