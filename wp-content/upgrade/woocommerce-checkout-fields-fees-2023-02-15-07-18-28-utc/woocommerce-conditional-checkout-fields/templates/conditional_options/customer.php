<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Select customers','woocommerce-conditional-checkout-fields');?><span class="wcccf_required_label"> *</span></label>
	<select class="js-data-customers-ajax" name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][customer_id][]" 
		multiple="multiple" 
		required="required"
		data-init-value ="<?php if(isset($coditional_item_data['customer_id'])) echo implode(",",$coditional_item_data['customer_id']); ?>" >
		<?php if(isset($coditional_item_data['customer_id']))
				foreach($coditional_item_data['customer_id'] as $user_id ): 
				if($user_id != 'guest'):
				
					$user_name = $wcccf_customer_model->get_gustomer_name_by_id($user_id);
					if($user_name != false):
			?>
						<option value="<?php echo $user_id; ?>" selected="selected" ><?php echo $user_name; ?></option>
		<?php endif;  endif; endforeach; ?>
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Belonging policy','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][customer_operator]" >
		<option value="at_least_one" <?php if(isset($coditional_item_data['customer_operator']) && $coditional_item_data['customer_operator'] == 'at_least_one') echo ' selected="selected" '; ?>><?php esc_html_e('Is one of the selected','woocommerce-conditional-checkout-fields');?></option>
		<option value="is_none" <?php if(isset($coditional_item_data['customer_operator']) && $coditional_item_data['customer_operator'] == 'is_none') echo ' selected="selected" '; ?>><?php esc_html_e('Is none of the selected','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
