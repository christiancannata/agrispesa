<h4><?php esc_html_e('Options','woocommerce-conditional-checkout-fields');?></h4>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Cart label','woocommerce-conditional-checkout-fields');?></label>
	<?php 
		$langs =  $wcccf_wpml_helper->get_langauges_list();
		foreach($langs as $language_code => $lang_data): ?>
			<div class="wcccf_display_as_block wccf_margin_bottom_10" >
				<?php if($lang_data['country_flag_url'] != "none"): ?>
					<img src=<?php echo $lang_data['country_flag_url']; ?> /> <?php echo $lang_data['default_locale']; ?><br/>
				<?php endif; ?>
				<input type="text" 
					   name="wcccf_field_data[<?php echo $field_id; ?>][options][fee_cart_label][<?php echo $lang_data['default_locale']; ?>]" 
					   placeholder="<?php esc_html_e('Cart label','woocommerce-conditional-checkout-fields');?>"
					   required="required" 
					   value="<?php if(isset($field_data['options']['fee_cart_label'][$lang_data['default_locale']])) echo  $field_data['options']['fee_cart_label'][$lang_data['default_locale']]; ?>"></input>
			</div>
		<?php endforeach; ?>
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Amount/Percentage','woocommerce-conditional-checkout-fields');?></label>
	<input type="number" 
		   step="0.01"
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][fee_amount]" 
		   required="required"
		   value="<?php if(isset($field_data['options']['fee_amount'])) echo $field_data['options']['fee_amount']; ?>"></input>
</div>
<div class="wcccf_option_box_container wcccf_fee_type_container"> 
	<label><?php esc_html_e('Type','woocommerce-conditional-checkout-fields');?></label>
	<p><?php wcccf_html_escape_allowing_special_tags(__('<strong>Note on Cart total:</strong> Total it is computed as sum of product totals and shipping costs and any eventual discount is not considered. Discounts will be applied by woocommerce only to the final total.','woocommerce-conditional-checkout-fields'));?></p>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][value_type]" >
		<option value="fixed" <?php if(isset($field_data['options']['value_type'])) selected( $field_data['options']['value_type'], 'fixed' ); ?>><?php esc_html_e('Fixed amount','woocommerce-conditional-checkout-fields');?></option>
		<option value="cart_total_percentage" <?php if(isset($field_data['options']['value_type'])) selected( $field_data['options']['value_type'], 'cart_total_percentage' ); ?>><?php esc_html_e('Cart total percentage','woocommerce-conditional-checkout-fields');?></option>
		<option value="cart_total_ex_tax_percentage" <?php if(isset($field_data['options']['value_type'])) selected( $field_data['options']['value_type'], 'cart_total_ex_tax_percentage' ); ?>><?php esc_html_e('Cart total (excluding taxes) percentage','woocommerce-conditional-checkout-fields');?></option>
		<option value="cart_subtotal_percentage" <?php if(isset($field_data['options']['value_type'])) selected( $field_data['options']['value_type'], 'cart_subtotal_percentage' ); ?>><?php esc_html_e('Cart subtotal percentage','woocommerce-conditional-checkout-fields');?></option>
		<option value="cart_subtotal_ex_tax_percentage" <?php if(isset($field_data['options']['value_type'])) selected( $field_data['options']['value_type'], 'cart_subtotal_ex_tax_percentage' ); ?>><?php esc_html_e('Cart subtotal (excluding taxes) percentage','woocommerce-conditional-checkout-fields');?></option>
		<option value="cart_shipping_total_percentage" <?php if(isset($field_data['options']['value_type'])) selected( $field_data['options']['value_type'], 'cart_shipping_total_percentage' ); ?>><?php esc_html_e('Shipping total percentage','woocommerce-conditional-checkout-fields');?></option>
		<option value="cart_shipping_total_ex_tax_percentage" <?php if(isset($field_data['options']['value_type'])) selected( $field_data['options']['value_type'], 'cart_shipping_total_ex_tax_percentage' ); ?>><?php esc_html_e('Shipping total (excluding taxes) percentage','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<div class="wcccf_option_box_container"> 
	<label><?php esc_html_e('Taxable','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][fee_is_taxable]" >
		<option value="no" <?php if(isset($field_data['options']['fee_is_taxable'])) selected( $field_data['options']['fee_is_taxable'], 'no' ); ?>><?php esc_html_e('No','woocommerce-conditional-checkout-fields');?></option>
		<option value="yes" <?php if(isset($field_data['options']['fee_is_taxable'])) selected( $field_data['options']['fee_is_taxable'], 'yes' ); ?>><?php esc_html_e('Yes','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<?php //wcccf_var_dump($field_data['options']); ?>
<div class="wcccf_option_box_container"> 
	<label><?php esc_html_e('Apply free shipping','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][apply_free_shipping]" >
		<option value="no" <?php if(isset($field_data['options']['apply_free_shipping'])) selected( $field_data['options']['apply_free_shipping'], 'no' ); ?>><?php esc_html_e('No','woocommerce-conditional-checkout-fields');?></option>
		<option value="yes" <?php if(isset($field_data['options']['apply_free_shipping'])) selected( $field_data['options']['apply_free_shipping'], 'yes' ); ?>><?php esc_html_e('Yes','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<div class="wcccf_option_box_container"> 
	<label><?php esc_html_e('Apply even if other fees have been applied','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][stack_with_other_fee]" >	
		<option value="yes" <?php if(isset($field_data['options']['stack_with_other_fee'])) selected( $field_data['options']['stack_with_other_fee'], 'yes' ); ?>><?php esc_html_e('Yes','woocommerce-conditional-checkout-fields');?></option>
		<option value="no" <?php if(isset($field_data['options']['stack_with_other_fee'])) selected( $field_data['options']['stack_with_other_fee'], 'no' ); ?>><?php esc_html_e('No','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
