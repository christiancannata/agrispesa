<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Tag','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][heading_tag]">
		<option value="h3" <?php if(isset($field_data['options']['heading_tag'])) selected( $field_data['options']['heading_tag'], 'h3'); ?>><?php esc_html_e('H3','woocommerce-conditional-checkout-fields');?></option>
		<option value="h1" <?php if(isset($field_data['options']['heading_tag'])) selected( $field_data['options']['heading_tag'], 'h1'); ?>><?php esc_html_e('H1','woocommerce-conditional-checkout-fields');?></option>
		<option value="h2" <?php if(isset($field_data['options']['heading_tag'])) selected( $field_data['options']['heading_tag'], 'h2'); ?>><?php esc_html_e('H2','woocommerce-conditional-checkout-fields');?></option>
		<option value="h4" <?php if(isset($field_data['options']['heading_tag'])) selected( $field_data['options']['heading_tag'], 'h4'); ?>><?php esc_html_e('H4','woocommerce-conditional-checkout-fields');?></option>
		<option value="h5" <?php if(isset($field_data['options']['heading_tag'])) selected( $field_data['options']['heading_tag'], 'h5'); ?>><?php esc_html_e('H5','woocommerce-conditional-checkout-fields');?></option>
	</select>
	<!-- Special -->
	<input type="hidden" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][row_type]" 
		   value="wide"></input>
		   
	<input type="hidden" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][show_in_order_details_page]" 
		   value="no"></input>
		   
	<input type="hidden" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][show_in_emails]" 
		   value="no"></input>
		   
	<input type="hidden" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][is_required]" 
		   value="no"></input>
		   
	<input type="hidden" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][css_classes]" 
		   value=""></input>
</div>

<div class="wcccf_option_box_container">
	<label><?php esc_html_e('CSS head classes','woocommerce-conditional-checkout-fields');?></label>
	<input type="text" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][css_input_classes]" 
		   placeholder="class1 class2 class 3"
		   value="<?php if(isset($field_data['options']['css_input_classes'])) echo $field_data['options']['css_input_classes']; ?>"></input>
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
