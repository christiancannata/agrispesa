<div class="wcccf_conditional_option_item">
	<input type="hidden" name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][logic_operator]" value="<?php echo $operator; ?>"></input>
	<div class="wcccf_option_box_container">
		<label><?php esc_html_e('Select an option','woocommerce-conditional-checkout-fields');?></label>
		<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][condition_type]" 
				class="conditional_group_item_select"
				data-id="<?php echo $field_id; ?>" 
				data-conditional-id="<?php echo $conditional_item_id; ?>">
			<option value="product" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'product' );; ?>><?php esc_html_e('Product','woocommerce-conditional-checkout-fields');?></option>
			<option value="category" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'category' ); ?>><?php esc_html_e('Category','woocommerce-conditional-checkout-fields');?></option>
			<option value="tag" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'tag' ); ?>><?php esc_html_e('Tag','woocommerce-conditional-checkout-fields');?></option>
			<option value="cart" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'cart' ); ?>><?php esc_html_e('Cart','woocommerce-conditional-checkout-fields');?></option>
			<option value="user" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'user' ); ?>><?php esc_html_e('User role','woocommerce-conditional-checkout-fields');?></option>
			<option value="customer" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'customer' ); ?>><?php esc_html_e('Customer','woocommerce-conditional-checkout-fields');?></option>
			<?php if($this->current_field_type == 'fee'): ?>
				<option value="payment" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'payment' ); ?>><?php esc_html_e('Payment method','woocommerce-conditional-checkout-fields');?></option>
				<option value="shipping_method" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'shipping_method' ); ?>><?php esc_html_e('Shipping method','woocommerce-conditional-checkout-fields');?></option>
				<option value="billing_state_country" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'billing_state_country' ); ?>><?php esc_html_e('Billing country & state','woocommerce-conditional-checkout-fields');?></option>
				<option value="billing_postcode" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'billing_postcode' ); ?>><?php esc_html_e('Billing postcode / ZIP','woocommerce-conditional-checkout-fields');?></option>
				<option value="billing_city" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'billing_city' ); ?>><?php esc_html_e('Billing Town / City','woocommerce-conditional-checkout-fields');?></option>
				<option value="shipping_state_country" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'shipping_state_country' ); ?>><?php esc_html_e('Shipping country & state','woocommerce-conditional-checkout-fields');?></option>
				<option value="shipping_postcode" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'shipping_postcode' ); ?>><?php esc_html_e('Shipping postcode / ZIP','woocommerce-conditional-checkout-fields');?></option>
				<option value="shipping_city" <?php if(isset($coditional_item_data['condition_type'])) selected( $coditional_item_data['condition_type'], 'shipping_city' ); ?>><?php esc_html_e('Shipping Town / City','woocommerce-conditional-checkout-fields');?></option>
				
			<?php endif; ?>
		</select>
		<div class="wcccf_loader" ></div>
	</div>
	<div class="condition_sub_options_box">
		<?php 
				if(!empty($coditional_item_data))
				{
					$this->condition_sub_options_box($coditional_item_data['condition_type'], $field_id, $conditional_item_id, $coditional_item_data);
				}
				else
					$this->condition_sub_options_box('product', $field_id, $conditional_item_id ); ?>
	</div>
	<div class="wcccf_logic_operator_container">
		<div class="wcccf_loader" ></div>
		<button class="button button-secondary wcccf_add_conditional_option" data-id="<?php echo $field_id; ?>"><?php esc_html_e('And','woocommerce-conditional-checkout-fields');?></button>
		<button class="button button-secondary delete wcccf_remove_conditional_option" ><?php esc_html_e('Remove','woocommerce-conditional-checkout-fields');?></button>
	</div>
</div>