<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Multiple value selection','woocommerce-conditional-checkout-fields'); ?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][select_multiple_selection]">
		<option value="no" <?php if(isset($field_data['options']['select_multiple_selection'])) selected( $field_data['options']['select_multiple_selection'], 'no'); ?>><?php esc_html_e('No','woocommerce-conditional-checkout-fields');?></option>
		<option value="yes" <?php if(isset($field_data['options']['select_multiple_selection'])) selected( $field_data['options']['select_multiple_selection'], 'yes'); ?>><?php esc_html_e('Yes','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Labels and values','woocommerce-conditional-checkout-fields');?><span class="wcccf_required_label"> *</span></label>
		<span class="description wcccf_display_as_block wccf_margin_bottom_10"><?php wcccf_html_escape_allowing_special_tags(__('Enter each choice on a new line.<br>For more control, you may specify both a value and label like this:<br><br>red : Red<br>blue : Blue<br>green : Green.','woocommerce-conditional-checkout-field')); ?>
		<?php if($wcccf_wpml_helper->wpml_is_active()) :?>
		<?php wcccf_html_escape_allowing_special_tags(__('<br><br><strong>Note: </strong> you can use different labels for each language but you <strong>must use</strong> same values. For example:  " value_1 : Green" for English and "value_1 : Verde" for Italian.','woocommerce-conditional-checkout-field')); ?>
		<?php endif; ?>
		</span>
	<?php 
		$langs =  $wcccf_wpml_helper->get_langauges_list();
		foreach($langs as $language_code => $lang_data): ?>
			<div class="wcccf_float_left wccf_margin_bottom_10 wccf_right_bottom_10" >
				<?php if($lang_data['country_flag_url'] != "none"): ?>
					<img src=<?php echo $lang_data['country_flag_url']; ?> /> <?php echo $lang_data['default_locale']; ?><br/>
				<?php endif; ?>
				<textarea  rows="6" cols="50"
							required="required" 
							name="wcccf_field_data[<?php echo $field_id; ?>][options][select_labels_and_values][<?php echo $lang_data['default_locale']; ?>]" 
							placeholder=""><?php if(isset($field_data['options']['select_labels_and_values'][$lang_data['default_locale']])) echo $field_data['options']['select_labels_and_values'][$lang_data['default_locale']]; ?></textarea>
			</div>
		<?php endforeach; ?>
</div>