<?php 
$country_with_states = $wcccf_country_model->get_countries_with_states();
?>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Select which country states/provinces have to be showed','woocommerce-conditional-checkout-fields');?></label>
	<p><i><?php esc_html_e('Can be selected only the states/provinces for the allowed sell countries configured in the WooCommerce -> Settings -> General menu.','woocommerce-conditional-checkout-fields');?></i></p>
	 <select  name="wcccf_field_data[<?php echo $field_id; ?>][options][state_show_state_for_country]" placeholder="" required="required"> 
		<?php 
			foreach((array)$country_with_states as $country_code => $country_name)
			{
				$selected = isset($field_data['options']['state_show_state_for_country']) && $field_data['options']['state_show_state_for_country'] == $country_code ?  ' selected="selected" ' : "";
				echo "<option value='{$country_code}' {$selected} >{$country_name}</option>";
			}
		?>
	</select>
</div>