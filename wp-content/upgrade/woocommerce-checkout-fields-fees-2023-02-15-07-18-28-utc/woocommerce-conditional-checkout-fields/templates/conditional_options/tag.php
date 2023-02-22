<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Select tags','woocommerce-conditional-checkout-fields');?><span class="wcccf_required_label"> *</span></label>
	<select class="js-data-tag-ajax" 
			name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][tag_id][]" 
			multiple="multiple" 
			required="required">
			<?php 
			if(!empty($coditional_item_data))
			 foreach( $coditional_item_data['tag_id'] as $tag_id)
				{
					echo '<option value="'.$tag_id.'" selected="selected" >'.$wcccf_product_model->get_product_tag_name($tag_id).'</option>';
				} 
			?>
				
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Type','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][tag_condition_type]" >
		<option value="cart" <?php if(isset($coditional_item_data['tag_condition_type']) && $coditional_item_data['tag_condition_type'] == 'cart') echo ' selected="selected" '; ?>><?php esc_html_e('Cart quantity','woocommerce-conditional-checkout-fields');?></option>
		<option value="amount_spent" <?php if(isset($coditional_item_data['tag_condition_type']) && $coditional_item_data['tag_condition_type'] == 'amount_spent') echo ' selected="selected" '; ?>><?php esc_html_e('Amount spent','woocommerce-conditional-checkout-fields');?></option>
		<option value="amount_spent_ex_taxes" <?php if(isset($coditional_item_data['tag_condition_type']) && $coditional_item_data['tag_condition_type'] == 'amount_spent_ex_taxes') echo ' selected="selected" '; ?>><?php esc_html_e('Amount spent excluding taxes','woocommerce-conditional-checkout-fields');?></option>
		<option value="stock" <?php if(isset($coditional_item_data['tag_condition_type']) && $coditional_item_data['tag_condition_type'] == 'stock') echo ' selected="selected" '; ?>><?php esc_html_e('Stock quantity','woocommerce-conditional-checkout-fields');?></option>
		<option value="stock_status" <?php if(isset($coditional_item_data['tag_condition_type']) && $coditional_item_data['tag_condition_type'] == 'stock_status') echo ' selected="selected" '; ?>><?php esc_html_e('Stock status (values: instock, outofstock)','woocommerce-conditional-checkout-fields');?></option>
		<option value="weight" <?php if(isset($coditional_item_data['tag_condition_type']) && $coditional_item_data['tag_condition_type'] == 'weight') echo ' selected="selected" '; ?>><?php esc_html_e('Weight','woocommerce-conditional-checkout-fields');?></option>
		<option value="height" <?php if(isset($coditional_item_data['tag_condition_type']) && $coditional_item_data['tag_condition_type'] == 'height') echo ' selected="selected" '; ?>><?php esc_html_e('Height','woocommerce-conditional-checkout-fields');?></option>
		<option value="length" <?php if(isset($coditional_item_data['tag_condition_type']) && $coditional_item_data['tag_condition_type'] == 'length') echo ' selected="selected" '; ?>><?php esc_html_e('Lenght','woocommerce-conditional-checkout-fields');?></option>
		<option value="width" <?php if(isset($coditional_item_data['tag_condition_type']) && $coditional_item_data['tag_condition_type'] == 'width') echo ' selected="selected" '; ?>><?php esc_html_e('Width','woocommerce-conditional-checkout-fields');?></option>
		<option value="volume" <?php if(isset($coditional_item_data['tag_condition_type']) && $coditional_item_data['tag_condition_type'] == 'volume') echo ' selected="selected" '; ?>><?php esc_html_e('Volume','woocommerce-conditional-checkout-fields');?></option>
		<?php if($wcccf_is_woocommerce_booking_active): ?>
			<option value="booking_person" <?php if(isset($coditional_item_data['product_condition_type']) && $coditional_item_data['product_condition_type'] == 'booking_person') echo ' selected="selected" '; ?>><?php esc_html_e('Booking - Person','woocommerce-conditional-checkout-fields');?></option>
		<?php endif; ?>
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Value','woocommerce-conditional-checkout-fields');?><span class="wcccf_required_label"> *</span></label>
	<input type="text" 
		   class="wcccf_bigger_input" 
		   min="1" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][tag_value]" 
		   value="<?php if(isset($coditional_item_data['tag_value'])) echo $coditional_item_data['tag_value']; else echo 1; ?>" 
		   placeholder="" 
		   required="required"></input>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Operator','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][tag_operator]" >
			<option value="greater_equal" <?php if(isset($coditional_item_data['tag_operator']) && $coditional_item_data['tag_operator'] == 'greater_equal') echo ' selected="selected" '; ?>><?php esc_html_e('Greater or equal','woocommerce-conditional-checkout-fields');?></option>
			<option value="lesser_equal" <?php if(isset($coditional_item_data['tag_operator']) && $coditional_item_data['tag_operator'] == 'lesser_equal') echo ' selected="selected" '; ?>><?php esc_html_e('Lesser or equal','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Value has to be considered as','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][tag_value_considered]" >
		<option value="each_value" <?php if(isset($coditional_item_data['tag_value_considered']) && $coditional_item_data['tag_value_considered'] == 'each_value') echo ' selected="selected" '; ?>><?php esc_html_e('Each product value','woocommerce-conditional-checkout-fields');?></option>
		<option value="sum_of_values" <?php if(isset($coditional_item_data['tag_value_considered']) && $coditional_item_data['tag_value_considered'] == 'sum_of_values') echo ' selected="selected" '; ?>><?php esc_html_e('The sum of product values','woocommerce-conditional-checkout-fields');?></option>
		<option value="max_value" <?php if(isset($coditional_item_data['tag_value_considered']) && $coditional_item_data['tag_value_considered'] == 'max_value') echo  ' selected="selected" '; ?>><?php esc_html_e('Max value of the selected products','woocommerce-conditional-checkout-fields');?></option>
		<option value="min_value" <?php if(isset($coditional_item_data['tag_value_considered']) && $coditional_item_data['tag_value_considered'] == 'min_value') echo  ' selected="selected" '; ?>><?php esc_html_e('Min value of the selecte products','woocommerce-conditional-checkout-fields');?></option>
		<!-- <option value="at_least_one" <?php if(isset($coditional_item_data['tag_value_considered']) && $coditional_item_data['tag_value_considered'] == 'at_least_one') echo ' selected="selected" '; ?>><?php esc_html_e('At least one product','woocommerce-conditional-checkout-fields');?></option> -->
	</select>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Cart presence policy. The field is showed if','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][tag_cart_presence_policy]" >
		<option value="items_actually_present" <?php if(isset($coditional_item_data['tag_cart_presence_policy']) && $coditional_item_data['tag_cart_presence_policy'] == 'items_actually_present') echo ' selected="selected" '; ?>><?php esc_html_e('Any product belonging to any of the selected tags is in cart','woocommerce-conditional-checkout-fields');?></option>
		<option value="selected_items_must_be_present" <?php if(isset($coditional_item_data['tag_cart_presence_policy']) && $coditional_item_data['tag_cart_presence_policy'] == 'selected_items_must_be_present') echo ' selected="selected" '; ?>><?php esc_html_e('At least one product per each selected tag is in cart','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<!-- <div class="wcccf_option_box_container">
	<label><?php esc_html_e('Apply the fee','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][application_policy]" >
		<option value="items_actually_present" <?php if(isset($coditional_item_data['application_policy']) && $coditional_item_data['application_policy'] == 'once') echo ' selected="selected" '; ?>><?php esc_html_e('Only once','woocommerce-conditional-checkout-fields');?></option>
		<option value="selected_items_must_be_present" <?php if(isset($coditional_item_data['application_policy']) && $coditional_item_data['application_policy'] == 'for_each_detected_item') echo ' selected="selected" '; ?>><?php esc_html_e('For each detected item','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div> -->
<?php if($wcccf_is_woocommerce_booking_active): ?>
	<div class="wcccf_option_box_container">
		<label><?php esc_html_e('Display one field for each person','woocommerce-conditional-checkout-fields');?></label>
		<p><?php esc_html_e('For each Bookable product, the plugin will show N fields where N is the number of the persons the user selected. For other product type the field will not be showed. The field value will be lately always showed on both Order page and Emails (ignoring the <i>Show in emails</i> and <i>Show in order details page</i> settings). <strong>NOTE:</strong> note this option is unavailable for Contry and State fields.','woocommerce-conditional-checkout-fields');?></p>
		<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][tag_display_one_field_for_each_person]">
			<option value="no" <?php if(isset($coditional_item_data['tag_display_one_field_for_each_person'])) selected( $coditional_item_data['tag_display_one_field_for_each_person'], 'no' ); ?>><?php esc_html_e('No','woocommerce-conditional-checkout-fields');?></option>
			<option value="yes" <?php if(isset($coditional_item_data['tag_display_one_field_for_each_person'])) selected( $coditional_item_data['tag_display_one_field_for_each_person'], 'yes' ); ?>><?php esc_html_e('Yes','woocommerce-conditional-checkout-fields');?></option>
		</select>
	</div>
<?php endif; ?>