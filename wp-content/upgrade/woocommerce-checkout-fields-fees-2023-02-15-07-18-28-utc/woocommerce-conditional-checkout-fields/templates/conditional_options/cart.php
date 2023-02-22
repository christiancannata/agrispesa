<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Type','woocommerce-conditional-checkout-fields');  ?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][cart_condition_type]" >
		<option value="sub" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'sub' ); ?>><?php esc_html_e('Sub total','woocommerce-conditional-checkout-fields');?></option>
		<option value="sub_ex_tax" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'sub_ex_tax' ); ?>><?php esc_html_e('Sub total excluding taxes','woocommerce-conditional-checkout-fields');?></option>
		<option value="sub_tax" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'sub_tax' ); ?>><?php esc_html_e('Sub total taxes','woocommerce-conditional-checkout-fields');?></option>
		<option value="total" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'total' ); ?>><?php esc_html_e('Total','woocommerce-conditional-checkout-fields');?></option>
		<option value="total_ex_taxes" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'total_ex_taxes' ); ?>><?php esc_html_e('Total excluding taxes','woocommerce-conditional-checkout-fields');?></option>
		<option value="total_taxes" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'total_taxes' ); ?>><?php esc_html_e('Total taxes','woocommerce-conditional-checkout-fields');?></option>
		<option value="shipping" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'shipping' ); ?>><?php esc_html_e('Shipping','woocommerce-conditional-checkout-fields');?></option>
		<option value="shipping_ex_taxes" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'shipping_ex_taxes' ); ?>><?php esc_html_e('Shipping excluding taxes','woocommerce-conditional-checkout-fields');?></option>
		<option value="shipping_taxes" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'shipping_taxes' ); ?>><?php esc_html_e('Shipping taxes','woocommerce-conditional-checkout-fields');?></option>
		<option value="discount" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'discount' ); ?>><?php esc_html_e('Discount','woocommerce-conditional-checkout-fields');?></option>
		<option value="discount_ex_taxes" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'discount_ex_taxes' ); ?>><?php esc_html_e('Discount excluding taxes','woocommerce-conditional-checkout-fields');?></option>
		<option value="discount_taxes" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'discount_taxes' ); ?>><?php esc_html_e('Discount taxes','woocommerce-conditional-checkout-fields');?></option>
		<option value="item_quantity" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'item_quantity' ); ?>><?php esc_html_e('Sum of item quantities','woocommerce-conditional-checkout-fields');?></option>
		<option value="distinct_item" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'distinct_item' ); ?>><?php esc_html_e('Number of distinct items','woocommerce-conditional-checkout-fields');?></option>
		<option value="weight" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'weight' ); ?>><?php esc_html_e('Weight','woocommerce-conditional-checkout-fields');?></option>
		<option value="volume" <?php if(isset($coditional_item_data['cart_condition_type'])) selected( $coditional_item_data['cart_condition_type'], 'volume' ); ?>><?php esc_html_e('Volume','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Value','woocommerce-conditional-checkout-fields');?><span class="wcccf_required_label"> *</span></label>
	<input type="number" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][cart_value]" 
		   value="<?php if(isset($coditional_item_data['cart_value'])) echo $coditional_item_data['cart_value']; else echo 1; ?>">
	</input>
</div>
<div class="wcccf_option_box_container"> 
	<label><?php esc_html_e('Operator','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][cart_operator]" >
		<option value="lesser_equal" <?php if(isset($coditional_item_data['cart_operator'])) selected( $coditional_item_data['cart_operator'], 'lesser_equal' ); ?>><?php esc_html_e('Lesser or equal','woocommerce-conditional-checkout-fields');?></option>
		<option value="greater_equal" <?php if(isset($coditional_item_data['cart_operator'])) selected( $coditional_item_data['cart_operator'], 'greater_equal' ); ?>><?php esc_html_e('Greater or equal','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>