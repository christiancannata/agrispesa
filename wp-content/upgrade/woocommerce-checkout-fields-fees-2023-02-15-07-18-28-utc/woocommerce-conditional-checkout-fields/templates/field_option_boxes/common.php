<h4><?php esc_html_e('Options','woocommerce-conditional-checkout-fields');?></h4>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Placeholder','woocommerce-conditional-checkout-fields');?></label>
	<?php 
		$langs =  $wcccf_wpml_helper->get_langauges_list();
		foreach($langs as $language_code => $lang_data): ?>
			<div class="wcccf_display_as_block wccf_margin_bottom_10" >
				<?php if($lang_data['country_flag_url'] != "none"): ?>
					<img src=<?php echo $lang_data['country_flag_url']; ?> /> <?php echo $lang_data['default_locale']; ?><br/>
				<?php endif; ?>
				<input type="text" 
					   name="wcccf_field_data[<?php echo $field_id; ?>][options][place_holder][<?php echo $lang_data['default_locale']; ?>]" 
					   placeholder="<?php esc_html_e('Placeholder text','woocommerce-conditional-checkout-fields');?>"
					   value="<?php if(isset($field_data['options']['place_holder'][$lang_data['default_locale']])) echo  $field_data['options']['place_holder'][$lang_data['default_locale']]; ?>"></input>
			</div>
		<?php endforeach; ?>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Description','woocommerce-conditional-checkout-fields');?></label>
	<?php 
		$langs =  $wcccf_wpml_helper->get_langauges_list();
		foreach($langs as $language_code => $lang_data): ?>
			<div class="wcccf_display_as_block wccf_margin_bottom_10" >
				<?php if($lang_data['country_flag_url'] != "none"): ?>
					<img src=<?php echo $lang_data['country_flag_url']; ?> /> <?php echo $lang_data['default_locale']; ?><br/>
				<?php endif; ?>
				<textarea rows="6" cols="50"
					   name="wcccf_field_data[<?php echo $field_id; ?>][options][description][<?php echo $lang_data['default_locale']; ?>]" 
					   placeholder="<?php esc_html_e('Description text','woocommerce-conditional-checkout-fields');?>"><?php if(isset($field_data['options']['description'][$lang_data['default_locale']])) echo  stripcslashes($field_data['options']['description'][$lang_data['default_locale']]); ?></textarea>
			</div>
		<?php endforeach; ?>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('CSS row classes','woocommerce-conditional-checkout-fields');?></label>
	<input type="text" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][css_classes]" 
		   placeholder="class1 class2 class 3"
		   value="<?php if(isset($field_data['options']['css_classes'])) echo $field_data['options']['css_classes']; ?>"></input>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('CSS input classes','woocommerce-conditional-checkout-fields');?></label>
	<input type="text" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][css_input_classes]" 
		   placeholder="class1 class2 class 3"
		   value="<?php if(isset($field_data['options']['css_input_classes'])) echo $field_data['options']['css_input_classes']; ?>"></input>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Required','woocommerce-conditional-checkout-fields'); ?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][is_required]">
		<option value="no" <?php if(isset($field_data['options']['is_required'])) selected( $field_data['options']['is_required'], 'no' ); ?>><?php esc_html_e('No','woocommerce-conditional-checkout-fields');?></option>
		<option value="yes" <?php if(isset($field_data['options']['is_required'])) selected( $field_data['options']['is_required'], 'yes' ); ?>><?php esc_html_e('Yes','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Show in emails','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][show_in_emails]">	
		<option value="yes" <?php if(isset($field_data['options']['show_in_emails'])) selected( $field_data['options']['show_in_emails'], 'yes' ); ?>><?php esc_html_e('Yes','woocommerce-conditional-checkout-fields');?></option>
		<option value="no" <?php if(isset($field_data['options']['show_in_emails'])) selected( $field_data['options']['show_in_emails'], 'no' ); ?>><?php esc_html_e('No','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Show in order details page','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][show_in_order_details_page]">	
		<option value="yes" <?php if(isset($field_data['options']['show_in_order_details_page'])) selected( $field_data['options']['show_in_order_details_page'], 'yes' ); ?>><?php esc_html_e('Yes','woocommerce-conditional-checkout-fields');?></option>
		<option value="no" <?php if(isset($field_data['options']['show_in_order_details_page'])) selected( $field_data['options']['show_in_order_details_page'], 'no' ); ?>><?php esc_html_e('No','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Row width','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][row_type]">
		<option value="wide" <?php if(isset($field_data['options']['row_type'])) selected( $field_data['options']['row_type'], 'wide' ); ?>><?php esc_html_e('Full width','woocommerce-conditional-checkout-fields');?></option>
		<option value="first" <?php if(isset($field_data['options']['row_type'])) selected( $field_data['options']['row_type'], 'first' ); ?>><?php esc_html_e('Half left','woocommerce-conditional-checkout-fields');?></option>
		<option value="last" <?php if(isset($field_data['options']['row_type'])) selected( $field_data['options']['row_type'], 'last' ); ?>><?php esc_html_e('Half right','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>