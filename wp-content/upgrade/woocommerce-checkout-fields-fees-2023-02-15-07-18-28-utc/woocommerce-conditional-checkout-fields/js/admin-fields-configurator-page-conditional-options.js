"use strict";

jQuery(document).ready(function()
{
	jQuery(document).on('change', '.conditional_group_item_select', wcccf_load_condition_sub_options);
	jQuery(document).on('click', '.wcccf_add_conditional_option', wcccf_add_condition_item);
	jQuery(document).on('click', '.wcccf_remove_conditional_option', wcccf_remove_condition_item);
	wcccf_init_all_select2();
});
function wcccf_add_condition_item(event)
{
	event.stopPropagation();
	event.preventDefault();
	
	var id = jQuery(event.currentTarget).data('id');
	var conditional_item = jQuery(event.currentTarget).closest('.conditional_row');
	var remove_button = jQuery(event.currentTarget).siblings('.wcccf_remove_conditional_option');
	var loader = jQuery(event.currentTarget).siblings('.wcccf_loader');
	var conditional_item_id = jQuery(event.currentTarget).closest('.conditional_options_box_content').find('.wcccf_conditional_option_item').length;
	var field_type = jQuery("#wcccf_field_type_select_"+id).val();
	
	//UI
	jQuery(loader).show();
	jQuery(event.currentTarget).hide();
	remove_button.hide();
	
	//Ajax 
	var random = Math.floor((Math.random() * 1000000) + 999);
	var formData = new FormData();
	formData.append('action', 'wcccf_add_new_conditional_item');  
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
			remove_button.show();
			conditional_item.append(data);
			
			wcccf_init_conditional_item_by_type('product');
			
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
function wcccf_remove_condition_item(event)
{
	event.stopPropagation();
	event.preventDefault();
	
	var conditional_item = jQuery(event.currentTarget).closest('.wcccf_conditional_option_item');
	var conditional_items_container = conditional_item.closest('.conditional_row');
	
	conditional_item.remove();
	if(conditional_items_container.children().length == 0)
		conditional_items_container.remove();
	
	return false;
}
function wcccf_init_all_select2()
{
	wcccf_init_conditional_item_by_type('all');
}
function wcccf_init_conditional_item_by_type(option_type)
{
	switch(option_type)
	{
		case 'product': wcccf_init_product_select2();
		break;
		case 'category': wcccf_init_category_select2();
		break;
		case 'tag': wcccf_init_tag_select2();
		break;
		case 'user': wcccf_init_user_role_select2();
		break;
		case 'customer': wcccf_init_customer_select2();
		break;
		case 'all': wcccf_init_product_select2(); wcccf_init_category_select2(); wcccf_init_user_role_select2(); wcccf_init_customer_select2(); wcccf_init_tag_select2();
		break;
	}
}	
function wcccf_load_condition_sub_options(event)
{
	event.stopPropagation();
	event.preventDefault();
	
	var sub_options_box = jQuery(event.currentTarget).parent().siblings('.condition_sub_options_box');
	var loader = jQuery(event.currentTarget).siblings('.wcccf_loader');
	var elem = event.currentTarget;
	var option_type = jQuery(elem).val();
	var field_id = jQuery(elem).data('id');
	var conditional_item_id = jQuery(elem).data('conditional-id');
	var field_type = jQuery("#wcccf_field_type_select_"+field_id).val();
	
	//UI
	jQuery(loader).show();
	jQuery(sub_options_box).hide();
	jQuery(elem).attr('disabled', true);
			
	//Ajax 
	var random = Math.floor((Math.random() * 1000000) + 999);
	var formData = new FormData();
	formData.append('action', 'wcccf_add_new_condition_sub_options_box'); 
	formData.append('option_type', option_type); 
	formData.append('field_id', field_id); 
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
			jQuery(elem).attr('disabled', false);
			jQuery(sub_options_box).html(data);
			jQuery(sub_options_box).show();
			
			wcccf_init_conditional_item_by_type(option_type);
			
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
}