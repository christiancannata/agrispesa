"use strict";

var wcccf_next_id = 0;
var wcccf_conditional_row_id = 0;
jQuery(document).ready(function()
{
	wcccf_init_collapse_managment();
	
	//Drag managment: no longer used. Field have to be sorted using the new special menu
	jQuery('.column').sortable({
		connectWith: '.column',
		handle: 'h2',
		cursor: 'move',
		placeholder: 'placeholder',
		forcePlaceholderSize: true,
		opacity: 0.4,
		stop: function(event, ui)
		{
			
		}
	})
	.disableSelection(); 
	
	wcccf_next_id = parseInt(wcccf_configuration.next_id);
	jQuery(document).on('click', '.add_new_field_button', wcccf_add_new_field);
	jQuery(document).on('click', '.add_new_conditional_group_item', wcccf_add_new_conditional_group_item);
	jQuery(document).on('change', '.field_type_select', wcccf_load_field_options_box);
	jQuery(document).on('click', '.wcccuf_expand_conditional_options', wcccuf_expand_conditional_options);
	jQuery(document).on('change', '.wcccf_state_selector', wcccuf_load_states);
});
function wcccuf_load_states(event)
{
	event.preventDefault();
	event.stopPropagation();
	
	var country_code = jQuery(event.currentTarget).val();
	var target = '#state_selector_'+ jQuery(event.currentTarget).data('target-id');
	
	//console.log(jQuery(event.currentTarget).parent());
	if(country_code == 'any')
	{
		jQuery(target).empty();
		return false;
	}
	
	//UI
	jQuery(target+"_loader").show();
	jQuery(target).hide();
	
	//Ajax 
	var random = Math.floor((Math.random() * 1000000) + 999);
	var formData = new FormData();
	formData.append('action', 'wcccf_load_states_by_country_id');	
	formData.append('country_code', country_code); 
	formData.append('item_type', jQuery(event.currentTarget).data('item-type')); 
	formData.append('item_id', jQuery(event.currentTarget).data('item-id')); 
	formData.append('field_id', jQuery(event.currentTarget).data('field-id')); 
	
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			//UI
			jQuery(target+"_loader").hide();
			jQuery(target).show();
			jQuery(target).html(data);
		},
		error: function (data) 
		{
			
		},
		cache: false,
		contentType: false,
		processData: false
	}); 
	
	return false;
}
function wcccuf_expand_conditional_options(event)
{
	event.preventDefault();
	event.stopPropagation();
	
	var box_to_expand = jQuery(event.currentTarget).parent().siblings('.wcccuf_conditional_box_expandible');
	box_to_expand.toggle();
	
	return false;
}
function wcccf_init_collapse_managment(elem)
{
	elem = elem || ".dragbox";
	jQuery('.dragbox').each(function()
	{
		if(jQuery(this).data('already-processed') == true)
			return true;
		jQuery(this).data('already-processed', true);
		
		
		jQuery(this).hover(function()
		{
			jQuery(this).find('h2').addClass('collapse');
		}, function(){
			jQuery(this).find('h2').removeClass('collapse');
		})
		.find('h2').hover(function(){
			
		}, function(){
		})
		.click(function(event)
		{
			if(jQuery(event.target).hasClass('remove-field'))
			{
				wcccf_remove_configuration_box(event);
				return false;
			}
			if(jQuery(event.target).hasClass('duplicate-field'))
			{
				wcccf_duplicate_field(event);
				return false;
			}
			else if(jQuery(event.target).is('input'))
			{
				return false;
			}
			jQuery(this).siblings('.dragbox-content').toggle();
		})
		.end()
		;
	
	});
}
function wcccf_remove_configuration_box(event)
{
	event.stopPropagation();
	event.preventDefault();
	
	var elem = jQuery(event.target);
	var id = elem.data('id');
	jQuery("#field_configuration_box_"+id).fadeOut(function(){jQuery(this).remove()});
	
	return false;
}
function wcccf_duplicate_field(event)
{
	event.stopPropagation();
	event.preventDefault();
	
	var msg = wcccf_configuration.duplication_message.split("\\n").join("\n");
	
	if(!confirm(msg))
		return false;
	
	var elem = jQuery(event.target);
	var id = elem.data('id');
	var field_checkout_type = elem.data('checkout-type');
	
	
	var formData = new FormData();
	formData.append('action', 'wcccf_duplicate_'+wcccf_configuration.type); 
	formData.append('field_next_id', wcccf_next_id); 
	formData.append('field_to_duplicate', id); 
	formData.append('field_checkout_type', field_checkout_type); 
	
	wcccf_next_id++;
	
	//UI
	jQuery(".wcccf_loader").show();
	jQuery("#submit, .add_new_field_button, .wcccf-action-button").attr('disabled', true);
	
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			location.reload();
		},
		error: function (data) 
		{
			
		},
		cache: false,
		contentType: false,
		processData: false
	}); 
	
	
	return false;
}
function wcccf_add_new_field(event)
{
	event.stopPropagation();
	event.preventDefault();
	
	var loader = "#"+jQuery(event.currentTarget).data('loader');
	var container = "#"+jQuery(event.currentTarget).data('container');
	var button = event.currentTarget;
	var field_checkout_type = jQuery(event.currentTarget).data('checkout-type');
	
	//UI
	jQuery(loader).show();
	jQuery(event.currentTarget).attr('disabled', true);
	
	//Ajax 
	var random = Math.floor((Math.random() * 1000000) + 999);
	var formData = new FormData();
	formData.append('action', 'wcccf_add_new_field'); 
	formData.append('field_id', wcccf_next_id); 
	formData.append('field_checkout_type', field_checkout_type); 
	wcccf_next_id++;
	
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			//UI
			jQuery(loader).hide();
			jQuery(button).attr('disabled', false);
			jQuery(container).append(data);
			wcccf_init_collapse_managment();
		},
		error: function (data) 
		{
			//console.log(data);
			//alert("Error: "+data);
		},
		cache: false,
		contentType: false,
		processData: false
	}); 
	
	
	return false;
}

function wcccf_add_new_conditional_group_item(event)
{
	event.stopPropagation();
	event.preventDefault();
	
	var id = jQuery(event.currentTarget).data('id');
	var loader = "#group_conditional_options_loader_"+id;
	var conditional_item_id = jQuery(event.currentTarget).siblings('.conditional_options_box_content').find('.wcccf_conditional_option_item').length;
	var field_type = jQuery("#wcccf_field_type_select_"+id).val();
	
	//UI
	jQuery(loader).show();
	jQuery(event.currentTarget).hide();
	
	//Ajax 
	var random = Math.floor((Math.random() * 1000000) + 999);
	var formData = new FormData();
	formData.append('action', 'wcccf_add_new_conditional_group_item');  
	formData.append('field_id', id);
	formData.append('conditional_item_id', conditional_item_id);
	formData.append('field_type', field_type);
	
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			//UI
			jQuery(loader).hide();
			jQuery(event.currentTarget).show();
			jQuery("#conditional_options_box_content_"+id).append(data);
			
			wcccf_init_conditional_item_by_type('product');
			
		},
		error: function (data) 
		{
			
		},
		cache: false,
		contentType: false,
		processData: false
	}); 
	
	
	return false;
}
function wcccf_init_option_box_by_type(option_type)
{
	switch(option_type)
	{
		case 'time': wcccf_init_time_picker();
		break;
		case 'date': wcccf_init_date_picker();
		break;
	}
}
function wcccf_load_field_options_box(event)
{
	event.stopPropagation();
	event.preventDefault();
	
	var id = jQuery(event.currentTarget).data('id');
	var loader = "#field_options_box_loader_"+id;
	var type = jQuery(event.currentTarget).val();

	//UI
	jQuery(loader).show();
	jQuery(event.currentTarget).hide();
	
	//Ajax 
	var random = Math.floor((Math.random() * 1000000) + 999);
	var formData = new FormData();
	formData.append('action', 'wcccf_load_options_box_by_type'); 
	formData.append('field_type',  type); 
	formData.append('field_id', id);
	
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			//UI
			jQuery(loader).hide();
			jQuery(event.currentTarget).show();
			jQuery("#field_options_box_"+id).html(data);
			
			wcccf_init_option_box_by_type(type);
			
		},
		error: function (data) 
		{
			
		},
		cache: false,
		contentType: false,
		processData: false
	}); 
	
	return false;
}