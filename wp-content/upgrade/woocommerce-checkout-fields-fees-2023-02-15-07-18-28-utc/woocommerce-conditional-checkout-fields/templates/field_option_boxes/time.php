<?php 
$min_limit_type = isset($field_data['options']['time_min_limit_type']) && $field_data['options']['time_min_limit_type'] == 'relative' ? 'relative' : 'absolute'; 
$max_limit_type = isset($field_data['options']['time_max_limit_type']) && $field_data['options']['time_max_limit_type'] == 'relative' ? 'relative' : 'absolute'; 
$random_id = rand(123, 28372394);
?>
<!-- min time -->
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Min time type','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][time_min_limit_type]" class="wcccf_min_datetime_type_selector" data-id="<?php echo $random_id ?>">
		<option value="absolute" <?php selected( $min_limit_type, 'absolute' ); ?>><?php esc_html_e('Absolute','woocommerce-conditional-checkout-fields');?></option>
		<option value="relative" <?php selected( $min_limit_type, 'relative' ); ?>><?php esc_html_e('Relative','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<!-- absolute -->
<div class="wcccf_option_box_container <?php if($min_limit_type == 'relative') echo 'wcccf_hide';?> wcccf_datetime_min_value_selector_<?php echo $random_id;?>" >
	<label><?php esc_html_e('Min time','woocommerce-conditional-checkout-fields');?></label>
	<input type="text" 
		   class="wcccf_min_time_value" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][time_min_value]" 
		   placeholder="<?php esc_html_e('Leave empty for no min time','woocommerce-conditional-checkout-fields');?>"
		   value="<?php if(isset($field_data['options']['time_min_value'])) echo $field_data['options']['time_min_value']; ?>"></input>
</div>
<div class="wcccf_option_box_container <?php if($min_limit_type == 'relative') echo 'wcccf_hide';?> wcccf_datetime_min_value_selector_<?php echo $random_id;?>" >
	<label><?php esc_html_e('Min time can be earlier than "now"?','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][min_time_absolute_can_be_earlier_than_now]" class="">
		<option value="yes" <?php if(isset($field_data['options']['min_time_absolute_can_be_earlier_than_now'])) selected( $field_data['options']['min_time_absolute_can_be_earlier_than_now'], 'yes' ); ?>><?php esc_html_e('Yes','woocommerce-conditional-checkout-fields');?></option>
		<option value="no" <?php if(isset($field_data['options']['min_time_absolute_can_be_earlier_than_now'])) selected( $field_data['options']['min_time_absolute_can_be_earlier_than_now'], 'no' ); ?>><?php esc_html_e('No','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<!-- relative -->
<div class="wcccf_option_box_container <?php if($min_limit_type == 'absolute') echo 'wcccf_hide';?> wcccf_datetime_min_value_selector_<?php echo $random_id;?>">
	<label><?php esc_html_e('Min relative time from now','woocommerce-conditional-checkout-fields');?></label>
	<input type="number" 
		   step="1"
		   class="" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][time_min_offset]" 
		   placeholder="<?php esc_html_e('Leave empty for no min time','woocommerce-conditional-checkout-fields');?>"
		   value="<?php if(isset($field_data['options']['time_min_offset'])) echo $field_data['options']['time_min_offset']; ?>"></input>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][time_min_offset_type]" class="wccc_datetime_offset_type">
		<option value="second" <?php if(isset($field_data['options']['time_min_offset_type'])) selected( $field_data['options']['time_min_offset_type'], 'second' ); ?>><?php esc_html_e('Second','woocommerce-conditional-checkout-fields');?></option>
		<option value="minute" <?php if(isset($field_data['options']['time_min_offset_type'])) selected( $field_data['options']['time_min_offset_type'], 'minute' ); ?>><?php esc_html_e('Minute','woocommerce-conditional-checkout-fields');?></option>
		<option value="hour" <?php if(isset($field_data['options']['time_min_offset_type'])) selected( $field_data['options']['time_min_offset_type'], 'hour' ); ?>><?php esc_html_e('Hour','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<!-- max time -->
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Max time type','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][time_max_limit_type]" class="wcccf_max_datetime_type_selector" data-id="<?php echo $random_id ?>">
		<option value="absolute" <?php selected( $max_limit_type, 'absolute' ); ?>><?php esc_html_e('Absolute','woocommerce-conditional-checkout-fields');?></option>
		<option value="relative" <?php selected( $max_limit_type, 'relative' ); ?>><?php esc_html_e('Relative','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<!-- absolute -->
<div class="wcccf_option_box_container <?php if($max_limit_type == 'relative') echo 'wcccf_hide';?> wcccf_datetime_max_value_selector_<?php echo $random_id;?>">
	<label><?php esc_html_e('Max time','woocommerce-conditional-checkout-fields');?></label>
	<input type="text" 
		   class="wcccf_max_time_value" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][time_max_value]" 
		   placeholder="<?php esc_html_e('Leave empty for no max time','woocommerce-conditional-checkout-fields');?>"
		   value="<?php if(isset($field_data['options']['time_max_value'])) echo $field_data['options']['time_max_value']; ?>"></input>
</div>
<!-- relative -->
<div class="wcccf_option_box_container <?php if($max_limit_type == 'absolute') echo 'wcccf_hide';?> wcccf_datetime_max_value_selector_<?php echo $random_id;?>">
	<label><?php esc_html_e('Max relative time from now','woocommerce-conditional-checkout-fields');?></label>
	<input type="number" 
		   step="1"
		   class="" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][time_max_offset]" 
		   placeholder="<?php esc_html_e('Leave empty for no max time','woocommerce-conditional-checkout-fields');?>"
		   value="<?php if(isset($field_data['options']['time_max_offset'])) echo $field_data['options']['time_max_offset']; ?>"></input>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][time_max_offset_type]" class="wccc_datetime_offset_type">
		<option value="second" <?php if(isset($field_data['options']['time_max_offset_type'])) selected( $field_data['options']['time_max_offset_type'], 'second' ); ?>><?php esc_html_e('Second','woocommerce-conditional-checkout-fields');?></option>
		<option value="minute" <?php if(isset($field_data['options']['time_max_offset_type'])) selected( $field_data['options']['time_max_offset_type'], 'minute' ); ?>><?php esc_html_e('Minute','woocommerce-conditional-checkout-fields');?></option>
		<option value="hour" <?php if(isset($field_data['options']['time_max_offset_type'])) selected( $field_data['options']['time_max_offset_type'], 'hour' ); ?>><?php esc_html_e('Hour','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
<!-- time span -->
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Choose the minutes interval between each time in the list','woocommerce-conditional-checkout-fields');?></label>
	<?php $time_interval = isset($field_data['options']['time_interval']) ? $field_data['options']['time_interval'] : 30; ?>
	<input type="number" required="required" step="1" min="1" value="<?php echo $time_interval; ?>" name="wcccf_field_data[<?php echo $field_id; ?>][options][time_interval]"></input>
</div>