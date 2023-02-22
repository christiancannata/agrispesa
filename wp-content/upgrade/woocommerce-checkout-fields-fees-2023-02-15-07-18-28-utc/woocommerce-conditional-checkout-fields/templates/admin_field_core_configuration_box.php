<div class="dragbox" data-already-processed="false" id="field_configuration_box_<?php echo $field_id; ?>" >
	<h2>
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
		<div class="wcccf_display_as_block">
			<input type="hidden" name="wcccf_field_data[<?php echo $field_id; ?>][checkout_type]" value="<?php if(isset($field_data['checkout_type'])) echo $field_data['checkout_type']; else echo $field_checkout_type; ?>"></input>
			<div class="wcccf_loader" id="field_options_box_loader_<?php echo $field_id; ?>"></div>
		</div>
		
		<div class="field_options_box" id="field_options_box_<?php echo $field_id; ?>" >
			<?php 
					$this->render_field_options_box('core', $field_id);
				 ?>
		</div>
	</div>
</div>