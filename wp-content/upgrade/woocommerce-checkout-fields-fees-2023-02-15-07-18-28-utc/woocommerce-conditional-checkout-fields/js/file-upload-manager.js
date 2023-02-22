"use strict";

jQuery(document).ready(function()
{
	jQuery('.wcccf_view_download_file_button').on("click",wcccf_view_download_file);
	jQuery('.wcccf_file_upload_button').on("click",wcccf_upload_file);
	jQuery('.wcccf_file_tmp_delete_button').on("click",wcccf_delete_tmp_file);
	document.addEventListener('onWCCCFMultipleFileUploaderComplete', wcccf_on_file_uploaded);
	if (window.File && window.FileReader && window.FileList && window.Blob) 
	{
		jQuery('.wcccf_input_file').on('change' ,wcccf_encode_file);
	} 
	else {
		alert(file_check_popup_api);
	}
	
	//clear previous selected
    jQuery('.wcccf_input_file').val("");
});

function wcccf_view_download_file(evt)
{
	evt.stopPropagation();
	evt.preventDefault();
	var href =  jQuery(evt.currentTarget).data('href');
	var win = window.open(href, '_blank');
	
	return false;
}

function wcccf_check_file_size(files, max_size)
{
	if(max_size == "")
		return true;
	
	if (window.File && window.FileReader && window.FileList && window.Blob)
	{
		if(files != undefined)
		{
			var fsize =files[0].size;
			var ftype = files[0].type;
			var fname = files[0].name;
			if(fsize > max_size)
			{
				var size = fsize/1048576; // 1MB in bytes
				size = size.toFixed(2);
				alert(wcccf_file_uploader_manager.size_error_message+(max_size/1024/1024));
				return false;
			}
		}
	}
	else{
		alert(file_check_popup_browser);
		return false;
	}
	return true;
}
function wcccf_encode_file(evt) 
{
    var files = evt.target.files;
    var file = files[0];
	var id =  jQuery(evt.currentTarget).data('id');
	var max_size =  jQuery(evt.currentTarget).data('size');
	var sub_id = jQuery(evt.currentTarget).attr("data-extra-field-custom-form-id") ? "-"+jQuery(evt.currentTarget).data("extra-field-custom-form-id") : ""; //used in the extra field custom form in case of multiple forms
	var file_id = jQuery(evt.currentTarget).attr("data-extra-field-custom-form-id") ? jQuery(evt.currentTarget).data("file-id") : id;
	var upload_input_field = jQuery(evt.currentTarget);
	var upload_button = jQuery(evt.currentTarget).data('upload-button-id');
	
	//clear old one
	if(jQuery('#wcccf-encoded-file_'+id).length)
		jQuery('#wcccf-encoded-file_'+id).remove();
	

	if(wcccf_check_file_size(files, max_size))
	{	
			
		wcccf_reset_upload_field_metadata(id);
		jQuery('#wcccf_file_upload_button_'+id).show();
		
		if (files && file) 
		{
			
		 
		  jQuery(upload_button).trigger('click');
		}
		//ToDo: Show error message?
	}
};
function wcccf_reset_upload_field_metadata(id)
{
	jQuery('#wcccf_file_tmp_name_'+id).hide();
	jQuery('#wcccf_file_tmp_delete_button_'+id).hide();
	jQuery('#wcccf_file_tmp_delete_button_'+id).data('file-to-delete', "");
	jQuery('#wcccf-encoded-file_'+id).empty();
	jQuery('#wcccf-filename-'+id).val("");
	jQuery('#wcccf-complete-name-'+id).val("");
    jQuery('#wcccf-filenameprefix-'+id).val("");
}
function wcccf_upload_file(evt)
{
	evt.preventDefault();
	evt.stopPropagation();
	
	var id =  jQuery(evt.currentTarget).data('id');
	var file_id = jQuery(evt.currentTarget).attr("data-extra-field-custom-form-id") ? jQuery(evt.currentTarget).data("file-id") : id;
	var upload_input_field = jQuery(evt.currentTarget).data('upload-field-id');
	var files = jQuery(upload_input_field).prop('files');
	var max_size = jQuery(upload_input_field).data('size');
    var file = files[0];
	

   //UI
   jQuery(evt.currentTarget).hide();
   jQuery(upload_input_field).hide();
   jQuery('#wcccf_upload_progress_status_container_'+id).fadeIn();
   jQuery('#wcccf_file_tmp_delete_button_'+id).hide();
   jQuery('#wcccf_file_tmp_name_'+id).hide();
   jQuery('#wcccf_file_upload_button_'+id).hide();
   jQuery('#place_order').fadeOut();
   
   var current_upload_session_id = Math.floor((Math.random() * 10000000) + 1);
   var tempfile_name  = wcccf_replace_bad_char(file.name);
 
   var multiple_file_uploader = new WCCCFMultipleFileUploader({ field_id:id, 
																ajaxurl: wcccf_file_uploader_manager.ajaxurl, 
																upload_input_field: upload_input_field, 
																files: files, 
																file: file, 
																file_name:tempfile_name,
																current_upload_session_id: current_upload_session_id});
   document.addEventListener('onWCCCFMultipleFileUploaderComplete', function()
							{
								
							});
	multiple_file_uploader.continueUploading();						
	return false;
			
}
function wcccf_on_file_uploaded(event)
{
	var id = event.field_id;
	
	jQuery('#place_order').fadeIn();
	jQuery('#wcccf_upload_progress_status_container_'+id).fadeOut();
	jQuery('#wcccf_file_tmp_delete_button_'+id).fadeIn();
	jQuery('#wcccf_file_tmp_name_'+id).fadeIn();
	jQuery('#wcccf_file_tmp_name_'+id).css({'display': 'block'});
	
	
	jQuery('#wcccf-filename-'+id).val(event.file_name);
	jQuery('#wcccf-filenameprefix-'+id).val(event.current_upload_session_id);
	jQuery('#wcccf-complete-name-'+id).val(event.current_upload_session_id+"_"+event.file_name);
	jQuery('#wcccf_file_tmp_delete_button_'+id).data('file-to-delete', event.current_upload_session_id+"_"+event.file_name);
	jQuery('#wcccf_file_tmp_name_'+id).html(event.file_name);
}
function wcccf_delete_tmp_file(event)
{
	event.preventDefault();
	event.stopPropagation();

	var file_to_delete =  jQuery(event.currentTarget).data('file-to-delete');
	var id =  jQuery(event.currentTarget).data('id');
	
	jQuery(event.currentTarget).fadeOut();
	if(file_to_delete == "")
		return false;
	
	//UI
	jQuery("#wcccf_file_upload_"+id).val("");
	jQuery("#wcccf-filename-"+id).val("");
	jQuery("#wcccf-filenameprefix-"+id).val("");
	jQuery("#wcccf_file_upload_"+id).fadeIn();
	jQuery('#wcccf_file_tmp_name_'+id).fadeOut();
	
	var formData = new FormData();
	formData.append('action', 'wcccf_delete_tmp_uploaded_file');
	formData.append('file_to_delete', file_to_delete);
	
	jQuery.ajax({
		url: wcccf_file_uploader_manager.ajaxurl,
		type: 'POST',
		data: formData,
		async: true,
		success: function (data) 
		{
			
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
function wcccf_replace_bad_char(text)
{
	text = text.replace(/'/g,"");
	text = text.replace(/"/g,"");
	text = text.replace(/ /g,"_");
	
	//var remove_special_chars = wcccf.remove_special_chars_from_file_name == 'true' ? true : false;
	var remove_special_chars = true;
	var translate_re = /[öäüÖÄÜ]/g;
	var translate = {
		"ä": "a", "ö": "o", "ü": "u",
		"Ä": "A", "Ö": "O", "Ü": "U",
		"ß": "ss" // probably more to come
	  };
	text = text.replace(translate_re, function(match) { 
      return translate[match]; 
    });
	
	if(remove_special_chars)
	{
		text = text.replace(/[^0-9a-zA-Z_.]/g, '');
	}
	
	text = text == "" ? 'file' : text;
	return text;
}