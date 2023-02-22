<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Select country','woocommerce-conditional-checkout-fields');?><span class="wcccf_required_label"> *</span></label>
	<input type="hidden" name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][type]" value="billing" />
	<select class="wcccf_state_selector" 
			name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][billing_country]" 
			required="required" 
			data-target-id="<?php echo $conditional_item_id; ?>"
			data-field-id="<?php echo $field_id; ?>" 
			data-item-id="<?php echo $conditional_item_id; ?>" 
			data-item-type="billing_state">
		<option value="any" <?php if(isset($coditional_item_data['billing_country']) && 'any' == $coditional_item_data['billing_country']) echo ' selected="selected" ';?> ><?php esc_html_e('Any','woocommerce-conditional-checkout-fields');?></option>
			<?php 
			  foreach($wcccf_country_model->get_countries() as $country_code => $country_name): 
					$selected = isset($coditional_item_data['billing_country']) && $country_code == $coditional_item_data['billing_country'] ? ' selected="selected" ' : "";
					?>
			 <option value="<?php echo $country_code ;?>" <?php echo $selected  ?>><?php echo $country_name;?></option>
			 <?php endforeach; ?>
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Select state','woocommerce-conditional-checkout-fields');?></label>
	<div class="wcccf_loader" id="state_selector_<?php echo $conditional_item_id; ?>_loader"></div>
	<div id="state_selector_<?php echo $conditional_item_id; ?>" >
		<?php 
			if(isset($coditional_item_data['billing_country']))
			{
				$selected = isset($coditional_item_data['billing_state']) ? $coditional_item_data['billing_state'] : "";
				$wcccf_country_model->render_states_selector_by_country_id( $coditional_item_data['billing_country'] , "billing_state" , $field_id, $conditional_item_id, $selected); 
			}
		?>
	</div>
</div>
