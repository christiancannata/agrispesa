<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Min value','woocommerce-conditional-checkout-fields');?></label>
	<input type="number" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][number_min_value]" 
		   placeholder="<?php esc_html_e('leave empty for no limit','woocommerce-conditional-checkout-fields'); ?>"
		   value="<?php if(isset($field_data['options']['number_min_value'])) echo $field_data['options']['number_min_value']; ?>"></input>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Max value','woocommerce-conditional-checkout-fields');?></label>
	<input type="number" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][number_max_value]" 
		   placeholder="<?php esc_html_e('leave empty for no limit','woocommerce-conditional-checkout-fields'); ?>"
		   value="<?php if(isset($field_data['options']['number_max_value'])) echo $field_data['options']['number_max_value']; ?>"></input>
</div>