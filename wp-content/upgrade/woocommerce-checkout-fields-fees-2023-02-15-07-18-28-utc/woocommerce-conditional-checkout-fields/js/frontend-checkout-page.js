"use strict";

jQuery(document).ready(function()
{
	jQuery(document).on( 'change', 'input[name=payment_method]', wcccf_update_cart_table);
	jQuery(document).on( 'change', 'input[name=shipping_method]', wcccf_update_cart_table);
	jQuery(document).on( 'change', '#billing_postcode, #shipping_postcode, #shipping_city, #billing_city', wcccf_update_cart_table);
	jQuery(document).on('click', "#place_order", wcccf_additional_validation);
	if(wcccf_checkout_page.disable_checkout_sort_and_hide == 'no')
	{
		jQuery(document).on('updated_checkout', wcccf_custom_sort_checkout_forms);
	}
	
	jQuery( document.body ).trigger( 'update_checkout' );
	
});
wcccf_set_opacity_to_forms();

function wcccf_update_cart_table(event)
{
	jQuery( document.body ).trigger( 'update_checkout' );
}
function wcccf_transform_headings()
{
	jQuery('.wcccf_transform_to_heading').each(function(index, elem)
	{
		var tag = jQuery(elem).data('head');
		var classes = jQuery(elem).data('class');
		var text = jQuery(elem).html();
		var priority = jQuery(elem).parent().data('sort');
		
		var new_element = '<'+tag+' class="form-row '+classes+' wcccf_priority-'+priority+'" data-priority="'+priority+'" >'+text+'</'+tag+'>';
		jQuery(elem).parent().replaceWith(new_element);
	});
}
function wcccf_pre_init_done()
{
	wcccf_transform_headings();
	wcccf_init_fields(); 
}
function wcccf_set_opacity_to_forms()
{
	if(wcccf_checkout_page.disable_checkout_sort_and_hide == 'no')
	{
		var wcccf_loader = '<div class="wcccf_loader"></div>';
		jQuery("div.woocommerce-billing-fields__field-wrapper, div.woocommerce-shipping-fields__field-wrapper").css({'opacity':0});
		jQuery(wcccf_loader).insertBefore("div.woocommerce-shipping-fields__field-wrapper, div.woocommerce-billing-fields__field-wrapper");
		
	}
	else 
		wcccf_pre_init_done();
}
function wcccf_sort_ended(container)
{
	jQuery(".wcccf_loader").remove();
	jQuery(container).fadeTo(200, 1);
	
	//Force WooCommerce to reinit the form: jQuery(document.body).trigger('country_to_state_changed');

	wcccf_pre_init_done();
}
function wcccf_custom_sort_checkout_forms(event)
{
	wcccf_sort("div.woocommerce-billing-fields__field-wrapper");
	wcccf_sort("div.woocommerce-shipping-fields__field-wrapper");
	jQuery(document).unbind('updated_checkout', wcccf_custom_sort_checkout_forms);
}
function wcccf_sort(container)
{
	var sorted_array = {};
	jQuery(container).fadeTo(0, 0, function()
	{
		jQuery(container+" .form-row").each(function(index, elem)
		{
			var priority = wcccf_get_priority(elem);
			sorted_array[priority] = jQuery(elem);
		});
		
		
		for (var key in sorted_array)
		{
			 if (!sorted_array.hasOwnProperty(key)) continue;
			 
			 jQuery(container).append(sorted_array[key]);
		}
		
		//UI
		wcccf_sort_ended(container);
	});
	
}
function wcccf_get_priority(elem)
{
	var classes = jQuery(elem).attr('class').split(/\s+/);
	var priority = 24;
	jQuery.each(classes, function(index, item) {
		if (item.indexOf("wcccf_priority") !== -1) 
		{
			var result = item.split("-");
			priority = result[1];
		}
	});
	
	return priority;
}
function wcccf_sort_p(a, b) 
{
	return (jQuery(b).data('priority')) < (jQuery(a).data('priority')) ? 1 : -1;
}
function wcccf_additional_validation(event)
{
	var validation_error = false;
	
	
	jQuery(".wcccf_input_file").each(function(index,obj)
	{
		var has_reqired = jQuery(this).attr('required');
		var id = jQuery(this).data('id');
		validation_error = has_reqired && Query('#wcccf-filename-'+id).val() == "";
		
	});
	if(validation_error)
	{
		alert(wcccf_file_uploader_manager.required_files_message);
		event.preventDefault();
		event.stopImmediatePropagation();
		return false;
	}		
}