<div class="dragbox" data-already-processed="false" id="field_configuration_box_<?php echo $field_id; ?>" >
	<h2>
		<span class="configure">
			<a href="#" >
				<span class="dashicons dashicons-admin-page duplicate-field wcccf-action-button" data-id="<?php echo $field_id; ?>" data-checkout-type="<?php echo $field_checkout_type; ?>"></span>
			</a>
			<a href="#" >
				<span class="dashicons dashicons-menu expand-field wcccf-action-button"></span>
			</a>
			<a href="#" >
				<span class="dashicons dashicons-trash remove-field wcccf-action-button" data-id="<?php echo $field_id; ?>" ></span>
			</a>
		</span>
		<?php 
		$langs =  $wcccf_wpml_helper->get_langauges_list();
		foreach($langs as $language_code => $lang_data): ?>
				<?php if($lang_data['country_flag_url'] != "none"): ?>
					<img src=<?php echo $lang_data['country_flag_url']; ?> /> <?php echo $lang_data['default_locale']; ?><span class="wcccf_required_label"> *</span>:  
				<?php endif; ?>
				<input type="text" 
					   required="required" 
					   placeholder="<?php esc_html_e('Field label','woocommerce-conditional-checkout-fields');?>" 
					   name="wcccf_field_data[<?php echo $field_id; ?>][name][<?php echo $lang_data['default_locale']; ?>]" 
					   value="<?php if(isset($field_data['name'][$lang_data['default_locale']])) echo  $field_data['name'][$lang_data['default_locale']]; ?>"></input>
		<?php endforeach; ?>
	</h2>
	<div class="dragbox-content" <?php if($is_ajax) echo ' style="display:block;" '; ?>>
		<?php if(isset($field_data['unique_id'])): ?><span class="wcccf_id_label">(ID: <?php echo $field_data['unique_id']; ?>)</span><?php endif; ?>
		<h4 class="<?php echo "wcccf_label_type_select_".$field_checkout_type; ?> "><?php esc_html_e('Type','woocommerce-conditional-checkout-fields');?></h4>
		<div class="wcccf_display_as_block">
			<input type="hidden" name="wcccf_field_data[<?php echo $field_id; ?>][unique_id]" value="<?php if(isset($field_data['unique_id'])) echo $field_data['unique_id']; else echo $wcccf_field_model->generate_unique_id(); ?>"></input>
			<input type="hidden" name="wcccf_field_data[<?php echo $field_id; ?>][checkout_type]" value="<?php if(isset($field_data['checkout_type'])) echo $field_data['checkout_type']; else echo $field_checkout_type; ?>"></input>
			<select name="wcccf_field_data[<?php echo $field_id; ?>][type]" id="wcccf_field_type_select_<?php echo $field_id; ?>" class="field_type_select <?php echo "wcccf_type_select_".$field_checkout_type; ?>" data-id="<?php echo $field_id; ?>">
				<?php if($field_checkout_type != 'fee'): ?>
					<option value="text" <?php if(isset($field_data['type']) && $field_data['type'] == 'text') echo  ' selected="selected" '; ?>><?php esc_html_e('Text','woocommerce-conditional-checkout-fields');?></option>
					<option value="textarea" <?php if(isset($field_data['type']) && $field_data['type'] == 'textarea') echo  ' selected="selected" '; ?>><?php esc_html_e('Text area','woocommerce-conditional-checkout-fields');?></option>
					<option value="number" <?php if(isset($field_data['type']) && $field_data['type'] == 'number') echo  ' selected="selected" '; ?>><?php esc_html_e('Number','woocommerce-conditional-checkout-fields');?></option>
					<option value="tel" <?php if(isset($field_data['type']) && $field_data['type'] == 'tel' ) echo  ' selected="selected" '; ?>><?php esc_html_e('Telephone','woocommerce-conditional-checkout-fields');?></option>
					<option value="email" <?php if(isset($field_data['type']) && $field_data['type'] == 'email' ) echo  ' selected="selected" '; ?>><?php esc_html_e('Email','woocommerce-conditional-checkout-fields');?></option>
					<option value="password" <?php if(isset($field_data['type']) && $field_data['type'] == 'password' ) echo  ' selected="selected" '; ?>><?php esc_html_e('Password','woocommerce-conditional-checkout-fields');?></option>
					<option value="file" <?php if(isset($field_data['type']) && $field_data['type'] == 'file' ) echo  ' selected="selected" '; ?>><?php esc_html_e('File','woocommerce-conditional-checkout-fields');?></option>
					<option value="select" <?php if(isset($field_data['type']) && $field_data['type'] == 'select') echo  ' selected="selected" '; ?>><?php esc_html_e('Select/Multiselect','woocommerce-conditional-checkout-fields');?></option>
					<option value="checkbox" <?php if(isset($field_data['type']) && $field_data['type'] == 'checkbox') echo  ' selected="selected" '; ?>><?php esc_html_e('Checkbox','woocommerce-conditional-checkout-fields');?></option>
					<option value="date" <?php if(isset($field_data['type']) && $field_data['type'] == 'date') echo  ' selected="selected" '; ?>><?php esc_html_e('Date','woocommerce-conditional-checkout-fields');?></option>
					<option value="time"<?php if(isset($field_data['type']) && $field_data['type'] == 'time' ) echo  ' selected="selected" '; ?>><?php esc_html_e('Time','woocommerce-conditional-checkout-fields');?></option>
					<option value="country" <?php if(isset($field_data['type']) && $field_data['type'] == 'country') echo  ' selected="selected" '; ?> ><?php esc_html_e('Country','woocommerce-conditional-checkout-fields');?></option>
					<option value="state" <?php if(isset($field_data['type']) && $field_data['type'] == 'state') echo  ' selected="selected" '; ?>><?php esc_html_e('State','woocommerce-conditional-checkout-fields');?></option>
					<option value="heading" <?php if(isset($field_data['type']) && $field_data['type'] == 'heading') echo  ' selected="selected" '; ?>><?php esc_html_e('Heading','woocommerce-conditional-checkout-fields');?></option>
				<?php else: ?>
					<option value="fee" <?php if(isset($field_data['type']) && $field_data['type'] == 'fee') echo  ' selected="selected" '; ?>><?php esc_html_e('Fee','woocommerce-conditional-checkout-fields');?></option>
				<?php endif; ?>
			</select>
			<div class="wcccf_loader" id="field_options_box_loader_<?php echo $field_id; ?>"></div>
		</div>
		
		<div class="field_options_box" id="field_options_box_<?php echo $field_id; ?>" >
			<?php 
					if(!empty($field_data))
					{
						$this->render_field_options_box($field_data, $field_id);
					}
					else
					{
						if($field_checkout_type != 'fee')
							$this->render_field_options_box('text', $field_id);
						else 
							$this->render_field_options_box('fee', $field_id);
					}
				 ?>
		</div>
		<div class="field_conditional_options_box">
			<?php 
					if(!empty($field_data['conditional_group_item']))
					{
						$this->render_group_conditional_options($field_id, $field_data['conditional_group_item']);
					}
					else
						$this->render_group_conditional_options($field_id); 
				?>
		</div>
	</div>
</div>