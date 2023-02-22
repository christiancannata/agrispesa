<?php 
$min_limit_type = isset($field_data['options']['date_min_limit_type']) && $field_data['options']['date_min_limit_type'] == 'relative' ? 'relative' : 'absolute'; 
$max_limit_type = isset($field_data['options']['date_max_limit_type']) && $field_data['options']['date_max_limit_type'] == 'relative' ? 'relative' : 'absolute'; 
$random_id = rand(123, 28372394);
?>
<!-- min date -->
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Min date type','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][date_min_limit_type]" class="wcccf_min_datetime_type_selector" data-id="<?php echo $random_id ?>">
		<option value="absolute" <?php selected( $min_limit_type, 'absolute' ); ?>><?php esc_html_e('Absolute','woocommerce-conditional-checkout-fields');?></option>
		<option value="relative" <?php selected( $min_limit_type, 'relative' ); ?>><?php esc_html_e('Relative','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
  <!-- absolute -->
<div class="wcccf_option_box_container <?php if($min_limit_type == 'relative') echo 'wcccf_hide';?> wcccf_datetime_min_value_selector_<?php echo $random_id;?>">
	<label><?php esc_html_e('Min date','woocommerce-conditional-checkout-fields');?></label>
	<input type="text" 
		   class="wcccf_min_date_value" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][date_min_value]" 
		   placeholder="<?php esc_html_e('Min date','woocommerce-conditional-checkout-fields');?>"
		   value="<?php if(isset($field_data['options']['date_min_value'])) echo $field_data['options']['date_min_value']; ?>"></input>
	<p class="wcccf_date_description"><?php wcccf_html_escape_allowing_special_tags(__('Leave <strong>empty</strong> for no limit','woocommerce-conditional-checkout-fields'));?></p>
</div>
  <!-- relative -->
<div class="wcccf_option_box_container <?php if($min_limit_type == 'absolute') echo 'wcccf_hide';?> wcccf_datetime_min_value_selector_<?php echo $random_id;?>">
	<label><?php esc_html_e('Min relative date from now','woocommerce-conditional-checkout-fields');?></label>
	<input type="number" 
		   step="1"
		   class="wcccf_absolute_min_date_value" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][date_min_offset]" 
		   placeholder="<?php esc_html_e('Min date','woocommerce-conditional-checkout-fields');?>"
		   value="<?php if(isset($field_data['options']['date_min_offset'])) echo $field_data['options']['date_min_offset']; ?>"></input>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][date_min_offset_type]" class="wccc_datetime_offset_type">
		<option value="day" <?php if(isset($field_data['options']['date_min_offset_type'])) selected( $field_data['options']['date_min_offset_type'], 'day' ); ?>><?php esc_html_e('Day','woocommerce-conditional-checkout-fields');?></option>
		<option value="month" <?php if(isset($field_data['options']['date_min_offset_type'])) selected( $field_data['options']['date_min_offset_type'], 'month' ); ?>><?php esc_html_e('Month','woocommerce-conditional-checkout-fields');?></option>
		<option value="year" <?php if(isset($field_data['options']['date_min_offset_type'])) selected( $field_data['options']['date_min_offset_type'], 'year' ); ?>><?php esc_html_e('Year','woocommerce-conditional-checkout-fields');?></option>
	</select>
	<p class="wcccf_date_description"><?php wcccf_html_escape_allowing_special_tags(__('Leave <strong>empty</strong> to set today as min date and <strong>0</strong> for no limit.','woocommerce-conditional-checkout-fields'));?></p>	
</div>
<!-- max date -->
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Max date type','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][date_max_limit_type]" class="wcccf_max_datetime_type_selector" data-id="<?php echo $random_id ?>">
		<option value="absolute" <?php selected( $max_limit_type, 'absolute' ); ?>><?php esc_html_e('Absolute','woocommerce-conditional-checkout-fields');?></option>
		<option value="relative" <?php selected( $max_limit_type, 'relative' ); ?>><?php esc_html_e('Relative','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div>
  <!-- absolute -->
<div class="wcccf_option_box_container <?php if($max_limit_type == 'relative') echo 'wcccf_hide';?> wcccf_datetime_max_value_selector_<?php echo $random_id;?>">
	<label><?php esc_html_e('Max date','woocommerce-conditional-checkout-fields');?></label>
	<input type="text" 
		   class="wcccf_max_date_value" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][date_max_value]" 
		   placeholder="<?php esc_html_e('Max date','woocommerce-conditional-checkout-fields');?>"
		   value="<?php if(isset($field_data['options']['date_max_value'])) echo $field_data['options']['date_max_value']; ?>"></input>
	<p class="wcccf_date_description"><?php wcccf_html_escape_allowing_special_tags(__('Leave <strong>empty</strong> for no limit.','woocommerce-conditional-checkout-fields'));?></p>	
</div>
  <!-- relative -->
<div class="wcccf_option_box_container <?php if($max_limit_type == 'absolute') echo 'wcccf_hide';?> wcccf_datetime_max_value_selector_<?php echo $random_id;?>">
	<label><?php esc_html_e('Max relative date from now','woocommerce-conditional-checkout-fields');?></label>
	<input type="number" 
		   step="1"
		   class="wcccf_absolute_max_date_value" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][date_max_offset]" 
		   placeholder="<?php esc_html_e('Max date','woocommerce-conditional-checkout-fields');?>"
		   value="<?php if(isset($field_data['options']['date_max_offset'])) echo $field_data['options']['date_max_offset']; ?>"></input>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][options][date_max_offset_type]" class="wccc_datetime_offset_type">
		<option value="day" <?php if(isset($field_data['options']['date_max_offset_type'])) selected( $field_data['options']['date_max_offset_type'], 'day' ); ?>><?php esc_html_e('Day','woocommerce-conditional-checkout-fields');?></option>
		<option value="month" <?php if(isset($field_data['options']['date_max_offset_type'])) selected( $field_data['options']['date_max_offset_type'], 'month' ); ?>><?php esc_html_e('Month','woocommerce-conditional-checkout-fields');?></option>
		<option value="year" <?php if(isset($field_data['options']['date_max_offset_type'])) selected( $field_data['options']['date_max_offset_type'], 'year' ); ?>><?php esc_html_e('Year','woocommerce-conditional-checkout-fields');?></option>
	</select>
	<p class="wcccf_date_description"><?php esc_html_e('Leave <strong>empty</strong> to set today as max date and <strong>0</strong> for no limit.','woocommerce-conditional-checkout-fields');?></p>	
</div>
<!-- end min/max date -->
<!-- day to disable -->
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Days of the week to disable','woocommerce-conditional-checkout-fields');?></label>
	<input type="checkbox" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][day_to_disable][1]" 
		   value="1"
		   <?php if(isset($field_data['options']['day_to_disable'][1])) echo 'checked="checked"'; ?> ><?php esc_html_e('Monday','woocommerce-conditional-checkout-fields');?></input>
	<input type="checkbox" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][day_to_disable][2]" 
		   value="2"
		   <?php if(isset($field_data['options']['day_to_disable'][2])) echo 'checked="checked"'; ?> ><?php esc_html_e('Tuesday','woocommerce-conditional-checkout-fields');?></input>
	<input type="checkbox" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][day_to_disable][3]" 
		   value="3"
		   <?php if(isset($field_data['options']['day_to_disable'][3])) echo 'checked="checked"'; ?> ><?php esc_html_e('Wednesday','woocommerce-conditional-checkout-fields');?></input>
		   
	<input type="checkbox" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][day_to_disable][4]" 
		   value="4"
		   <?php if(isset($field_data['options']['day_to_disable'][4])) echo 'checked="checked"'; ?> ><?php esc_html_e('Thursday','woocommerce-conditional-checkout-fields');?></input>
	
	<input type="checkbox" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][day_to_disable][5]" 
		   value="5"
		   <?php if(isset($field_data['options']['day_to_disable'][5])) echo 'checked="checked"'; ?> ><?php esc_html_e('Friday','woocommerce-conditional-checkout-fields');?></input>
		 
	<input type="checkbox" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][day_to_disable][6]" 
		   value="6"
		   <?php if(isset($field_data['options']['day_to_disable'][6])) echo 'checked="checked"'; ?> ><?php esc_html_e('Saturday','woocommerce-conditional-checkout-fields');?></input>
		   
	<input type="checkbox" 
		   name="wcccf_field_data[<?php echo $field_id; ?>][options][day_to_disable][7]" 
		   value="7"
		   <?php if(isset($field_data['options']['day_to_disable'][7])) echo 'checked="checked"'; ?> ><?php esc_html_e('Sunday','woocommerce-conditional-checkout-fields');?></input>
			  
</div>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Specifc dates to exclude','woocommerce-conditional-checkout-fields');?><span class="wcccf_required_label"> *</span></label>
		<span class="description wcccf_display_as_block wccf_margin_bottom_10"><?php wcccf_html_escape_allowing_special_tags(__('Enter each date on a new line.<br>Each line rapresent a date and must be expessed in the following format <i>d:m</i>, where <i>d</i> is the day number (1-31) and <i>m</i> is the month number (1-12). Example: <br><br>24:12<br>1:3<br>15:2','woocommerce-conditional-checkout-field')); ?>
		<?php if($wcccf_wpml_helper->wpml_is_active()) :?>
		<?php wcccf_html_escape_allowing_special_tags(__('<br><br>Where the first is the 24th of December, the second is the 1st of March and the last is the 15th of February. <strong>NOTE: </strong>This options overrides the previous "Days of the week to disable" option. ','woocommerce-conditional-checkout-field')); ?>
		<?php endif; ?>
		</span>
		<div class="wcccf_float_left wccf_margin_bottom_10 wccf_right_bottom_10" >
				<textarea  rows="6" cols="50"
							name="wcccf_field_data[<?php echo $field_id; ?>][options][specific_dates_to_disable]" 
							placeholder=""><?php if(isset($field_data['options']['specific_dates_to_disable'])) echo $field_data['options']['specific_dates_to_disable']; ?></textarea>
			</div>
</div>