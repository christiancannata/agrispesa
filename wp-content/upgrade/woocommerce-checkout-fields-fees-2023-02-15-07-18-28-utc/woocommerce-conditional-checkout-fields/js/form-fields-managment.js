"use strict";

jQuery(document).ready(function()
{
	jQuery(document).on('focusout', '.wcccf_number_field', wcccf_init_number_and_fields);
	jQuery(document).on('change', '.wcccf_country_select', wcccf_load_state_by_country);
	
});
function wcccf_init_fields()
{
	wcccf_init_select2_fields();
	wcccf_init_date_and_fields();
	wcccf_init_description_fields();
	
	jQuery('.wcccf_country_select').trigger('change');
	jQuery('.wcccf_number_field').trigger('focusout');
}
function wcccf_htmlDecode(input){
  var e = document.createElement('div');
  e.innerHTML = input;
  // handle case of empty input
  return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
}
function wcccf_init_description_fields()
{
	jQuery(".wcccf_checkbox_container span.description").each(function(index, elem)
	{
		var html_content = wcccf_htmlDecode(jQuery(elem).html());
		html_content = html_content.replace(/\\"/g, '');
		jQuery(elem).empty();
		
		jQuery(elem).append(html_content);
	});
}
function wcccf_init_number_and_fields(event)
{
	var min = parseInt(jQuery(event.currentTarget).attr('min'));
	var max = parseInt(jQuery(event.currentTarget).attr('max'));
	var curr_val = parseInt(jQuery(event.currentTarget).val());
	
	if((min == null && max == null) || curr_val == null)
		return;
	
	if((min != null && curr_val < min) || (max != null && curr_val > max) )
	{
		jQuery(event.currentTarget).val(min != null ? min : max);
	}
}
function wcccf_init_select2_fields()
{
	jQuery('.wcccf_select2').each(function(index,elem)
	{
		jQuery(elem).select2(
			{
				width: '100%',
				placeholder: jQuery(elem).prop('placeholder')
			});
	});
}
function wcccf_init_date_and_fields()
{
	jQuery('.wcccf_time_field').each(function(index, elem)
	{
		var min = jQuery(elem).data('min');
		var max = jQuery(elem).data('max');
		var time_interval = jQuery(elem).data('interval');
		min = min != null ? min.split(",") : "";
		max = max != null ? max.split(",") : "";
		jQuery(elem).pickatime({
		  format: wcccf_options.time_format,//'HH:i',//wcccf_datetime.time_format
		  formatSubmit: 'HH:i',
		   hiddenSuffix: '',
		   min: min,
		   max: max,
		   interval:time_interval
			
		});
	});
	
	jQuery('.wcccf_date_field').each(function(index, elem)
	{
		var min = jQuery(elem).data('min');
		var max = jQuery(elem).data('max');
		var days_to_disable = jQuery(elem).data('disabled-days')+"";
		var specific_dates_to_disable = jQuery(elem).data('specific-dates-to-disable')+"";
		min = min != null ? min.split(",") : "";
		max = max != null ? max.split(",") : "";
		if(max != "")
			max[1] = max[1] - 1 ;
		if(min != "")
			min[1] = min[1] - 1 ;
		
		if(days_to_disable == "none" || days_to_disable == null)
			days_to_disable = [];
		else 
		{
			var day_values = days_to_disable.split(",");
			days_to_disable = [];
			for(var i = 0; i < day_values.length; i++)
				days_to_disable.push(parseInt(day_values[i]));
		
		}
		
		if(specific_dates_to_disable != "" && specific_dates_to_disable != "undefined")
		{
			var dates = specific_dates_to_disable.split(";");
			for(var i = 0; i < dates.length; i++)
				days_to_disable.push(dates[i].split(","));
		}
		
		jQuery(elem).pickadate({
			 firstDay: 1,
			 format: wcccf_options.date_format,//'yyyy-mm-dd',  
			 formatSubmit: 'yyyy-mm-dd',
			 hiddenSuffix: '',
			 min: min,
			 max: max,
			 selectYears: 300,
			 selectMonths: true,
			 klass: {
				 selectMonth: 'wcccf_picker__select--month',
				 selectYear: 'wcccf_picker__select--year'
			 },
			 disable: days_to_disable
			
		});
	});
}
function wcccf_load_state_by_country(event)
{
	var current_country_field = jQuery(event.currentTarget);
	var country_code =  current_country_field.val();
	if(current_country_field.data('load-state') === 'false' || country_code == "")
		return false;
	
	var loader_html_random = Math.floor((Math.random() * 10000) + 1)
	var loader_html = '<img class="wcccf_country_loader" id="wcccf_country_loader_'+loader_html_random+'" src="'+wcccf_options.country_loader_path+'"></img>';
	var target_state_container = wcccf_options.is_checkout_page == 'true' ? "#"+jQuery(event.currentTarget).data('linked-state-field-id')+"_field" : "#_"+jQuery(event.currentTarget).data('linked-state-field-id');
	var prev_state_value = current_country_field.data('prev-state-value');
	current_country_field.data('prev-state-value', 'none');
	
	//Ajax 
	var random = Math.floor((Math.random() * 1000000) + 999);
	var formData = new FormData();
	formData.append('action', 'wcccf_load_state'); 
	formData.append('country_code', country_code); 
	formData.append('form_type', jQuery(event.currentTarget).data('form-type')); 
	formData.append('unique_id', jQuery(event.currentTarget).data('unique-id')); 
	formData.append('state_selector_width', jQuery(event.currentTarget).data('state-selector-width')); 
	formData.append('is_checkout_page', wcccf_options.is_checkout_page); 
	formData.append('prev_state_value', prev_state_value != null ? prev_state_value : 'none'); 
	
	//UI
	jQuery(target_state_container).hide();
	current_country_field.attr('disabled', true);
	current_country_field.parent().append(loader_html);
			
	if(current_country_field.val() == "")
		return;
	
	jQuery.ajax({
		url: wcccf_options.ajaxurl,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			//UI
			jQuery('#wcccf_country_loader_'+loader_html_random).remove();
			
			current_country_field.attr('disabled', false);
			if(data == 'none' || data == "")
				return;
			
			//UI
			jQuery(target_state_container).show();
			jQuery(target_state_container).replaceWith(data);
			if(wcccf_options.is_checkout_page != 'true') //admin order details page
			{
				jQuery("#"+jQuery(event.currentTarget).data('linked-state-field-id')+"_field").siblings('label').remove();
				
			}
			
			wcccf_init_select2_fields();
		},
		error: function (data) 
		{
			
		},
		cache: false,
		contentType: false,
		processData: false
	}); 
}