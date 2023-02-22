"use strict";

jQuery(document).ready(function()
{
	jQuery(document).on('click', '.wcccf_delete_button',wcccf_delete_file);
});
function wcccf_delete_file(event)
{
	event.preventDefault();
	event.stopPropagation();

	var file_to_delete =  jQuery(event.currentTarget).data('file-to-delete');
	var order_id =  jQuery(event.currentTarget).data('order-id');
	var meta_key =  jQuery(event.currentTarget).data('meta-key');
	var id =  jQuery(event.currentTarget).data('id');
	
	if(file_to_delete == "" || order_id == "")
		return false;
	
	if (confirm(wcccf_order_details.delete_confirm_message))
	{
		//UI
		jQuery(event.currentTarget).fadeOut();
		jQuery("#wccc_download_button_"+id).fadeOut();
		
		var formData = new FormData();
		formData.append('action', 'wcccf_delete_uploaded_file');
		formData.append('file_to_delete', file_to_delete);
		formData.append('order_id', order_id);
		formData.append('meta_key', meta_key);
		
		jQuery.ajax({
			url: wcccf_order_details.ajaxurl,
			type: 'POST',
			data: formData,
			async: true,
			success: function (data) 
			{
				jQuery(event.currentTarget).parent().html(wcccf_order_details.deleted_message);
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
}