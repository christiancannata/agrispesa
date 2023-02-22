<?php 
	$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
	
 ?>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Method','woocommerce-conditional-checkout-fields');  ?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][payment_gatway_id]" >
		<?php foreach($available_gateways as $payment_id => $payment_gateway): ?>
			<option value="<?php echo $payment_id; ?>" <?php if(isset($coditional_item_data['payment_gatway_id'])) selected( $coditional_item_data['payment_gatway_id'], $payment_id ); ?>><?php echo $payment_gateway->title ?></option>
		<?php endforeach; ?>
	</select>
</div>
<div class="wcccf_option_box_container"> 
	<label><?php esc_html_e('Operator','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][payment_operator]" >
		<option value="equal" <?php if(isset($coditional_item_data['payment_operator'])) selected( $coditional_item_data['payment_operator'], 'equal' ); ?>><?php esc_html_e('Equal','woocommerce-conditional-checkout-fields');?></option>
		<option value="not_equal" <?php if(isset($coditional_item_data['payment_operator'])) selected( $coditional_item_data['payment_operator'], 'not_equal' ); ?>><?php esc_html_e('Not equal','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>