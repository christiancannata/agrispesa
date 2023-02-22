<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Select roles','woocommerce-conditional-checkout-fields');?><span class="wcccf_required_label"> *</span></label>
	<select class="js-data-user-role" name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][user_role][]" multiple="multiple" required="required">
		<option value="not_logged" <?php if(isset($coditional_item_data['user_role']) && in_array('not_logged',$coditional_item_data['user_role'])) echo ' selected="selected" ';?> ><?php esc_html_e('Not logged','woocommerce-conditional-checkout-fields');?></option>
		<?php foreach($wcccf_customer_model->get_user_roles() as $role_code => $role_name): 
				$selected = isset($coditional_item_data['user_role']) && in_array($role_code,$coditional_item_data['user_role']) ? ' selected="selected" ' : "";
		?>
			<option value="<?php echo $role_code; ?>" <?php echo $selected; ?>><?php echo $role_name["name"]; ?></option>
		<?php endforeach; ?>
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Belonging policy','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][user_operator]" >
		<option value="at_least_one" <?php if(isset($coditional_item_data['user_operator']) && $coditional_item_data['user_operator'] == 'at_least_one') echo ' selected="selected" '; ?>><?php esc_html_e('User has one of the selected roles','woocommerce-conditional-checkout-fields');?></option>
		<option value="has_all" <?php if(isset($coditional_item_data['user_operator']) && $coditional_item_data['user_operator'] == 'has_all') echo ' selected="selected" '; ?>><?php esc_html_e('User has all the selected roles','woocommerce-conditional-checkout-fields');?></option>
		<option value="has_none" <?php if(isset($coditional_item_data['user_operator']) && $coditional_item_data['user_operator'] == 'has_none') echo ' selected="selected" '; ?>><?php esc_html_e('User has none of the selected roles','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
