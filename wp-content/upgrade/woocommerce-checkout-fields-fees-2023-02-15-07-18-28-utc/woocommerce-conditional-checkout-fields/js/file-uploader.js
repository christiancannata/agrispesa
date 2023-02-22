"use strict";

function WCCCFMultipleFileUploader (params) 
{
	var myself = this;
	
    this.current_uploaded_file_index = 0;
	this.number_of_files_to_upload = params.files.length;
	this.files = params.files;
	this.file = params.file;
	this.file_name = params.file_name;
	this.upload_input_field = params.upload_input_field;
	this.xhr = new XMLHttpRequest(); //params.xhr;
	this.sum_all_file_sizes = 0;
	this.already_loaded_globally_bytes = 0;
	this.bytes_loaded_until_latest_chunk_upload = 0;
	this.field_id = params.field_id;
	this.isUploadingFileChunk = false;
	this.uploadcounter=0;
	this.uploadfilearray = [];		
	this.current_file;
	this.current_file_name;
	this.current_upload_session_id = params.current_upload_session_id;
	this.formData;
	this.BYTES_PER_CHUNK = 10485760; //1MB
	this.ajaxurl = params.ajaxurl;
	
	for(var i = 0; i < this.files.length; i++)
		this.sum_all_file_sizes += this.files[i].size;
	
	
	this.xhr.onreadystatechange = function(e) 
	{
		if (myself.xhr.readyState == 4) 
		{
			//1.
			if(myself.xhr.responseText === '0' || myself.xhr.responseText === '1')
			{
				
			}
			else if(myself.xhr.status == 200)
			{
				//3
				myself.continueUploading();
			}
		}
	}
}
 
WCCCFMultipleFileUploader.prototype.continueUploading = function() 
{
	//Used to display progress bar
	
	if(this.current_uploaded_file_index == this.number_of_files_to_upload)
	{
		var event = new Event('onWCCCFMultipleFileUploaderComplete');
		event.file_name = this.file_name;
		event.field_id = this.field_id;
		event.upload_input_field = this.upload_input_field;
		event.current_upload_session_id = this.current_upload_session_id;
		document.dispatchEvent(event);
		return false;
	}
	var i = this.current_uploaded_file_index; 
	this.current_file = this.number_of_files_to_upload == 1 ? this.file : this.files[i] ;
	this.current_file_name  = wcccf_replace_bad_char(this.current_file.name);
	var quantity = typeof this.files[i].quantity !== 'undefined' ? this.current_file.quantity : 1;
	
	this.current_uploaded_file_index++;
	this.startUploadingFileChunk();
	return true;
};

WCCCFMultipleFileUploader.prototype.startUploadingFileChunk = function()
{
	var blob = this.current_file;
	var SIZE = blob.size;
	var start = 0;
	var end = this.BYTES_PER_CHUNK;
	this.uploadcounter=0;
	this.uploadfilearray = [];

	while( start < SIZE ) 
	{

		var chunk = blob.slice(start, end);  //blob.webkitSlice(start, end); 
		this.uploadfilearray[this.uploadcounter] = chunk;
		this.uploadcounter = this.uploadcounter+1;
		start = end;
		end = start + this.BYTES_PER_CHUNK;
	}
	this.uploadcounter = 0;
	this.continueUploadingFileChunk(this.uploadfilearray[this.uploadcounter]);
}

WCCCFMultipleFileUploader.prototype.continueUploadingFileChunk = function(blobFile) 
{
	var chunkFormData = new FormData();
	var chunk_xhr = new XMLHttpRequest();
	var myself = this;
	this.bytes_loaded_until_latest_chunk_upload = 0;
	
	chunkFormData.append("action", "wcccf_file_chunk_upload");
	chunkFormData.append("wcccf_file_chunk", blobFile);
	chunkFormData.append("wcccf_file_name", this.current_file_name);
	chunkFormData.append("wcccf_upload_field_name",  this.file_name);
	chunkFormData.append("wcccf_current_chunk_num",  this.uploadcounter);
	chunkFormData.append("wcccf_current_upload_session_id",  this.current_upload_session_id);
	chunkFormData.append("wcccf_is_last_chunk",  this.uploadfilearray.length - 1 == this.uploadcounter ? "true" : "false");
	
	chunk_xhr.open("POST", this.ajaxurl);
	chunk_xhr.upload.addEventListener("progress",  function(event){myself.onSingleFileUploadProgress(myself,event)}, false);
	chunk_xhr.onreadystatechange = function(e) 
	{
		if (chunk_xhr.readyState == XMLHttpRequest.DONE && chunk_xhr.responseText == '0') 
		{
			myself.onSingleFileUploadError(myself, e);
			return;
		}			
		
		if (chunk_xhr.readyState == 4 && chunk_xhr.status == 200) 
		{
			myself.uploadcounter++;
			if (myself.uploadfilearray.length > myself.uploadcounter )
			{
				myself.continueUploadingFileChunk(myself.uploadfilearray[myself.uploadcounter]); 			                             
			}
			else
			{
				myself.continueUploading();
			}
		}
	};
	myself.resetFileUploadProgressBar(myself);
	chunk_xhr.send(chunkFormData);

}

WCCCFMultipleFileUploader.prototype.onSingleFileUploadLoaded = function(myself, event, chunk_xhr) 
{
	console.log(this.responseJSON());	
}
WCCCFMultipleFileUploader.prototype.onSingleFileUploadError = function(myself, event) 
{
	myself.already_loaded_globally_bytes -= myself.BYTES_PER_CHUNK;
	
	myself.BYTES_PER_CHUNK = myself.BYTES_PER_CHUNK/2;
	if(myself.BYTES_PER_CHUNK <= 0)
		myself.BYTES_PER_CHUNK = 1048576;
	
	myself.startUploadingFileChunk(myself.uploadfilearray[myself.uploadcounter]); 	
}

WCCCFMultipleFileUploader.prototype.getCurrentUploadedFileIndex = function() 
{
     return this.current_uploaded_file_index;
};
WCCCFMultipleFileUploader.prototype.setGloballyLoadedBytes = function(already_loaded, myself) 
{
	myself.already_loaded_globally_bytes += already_loaded - myself.bytes_loaded_until_latest_chunk_upload;
	myself.bytes_loaded_until_latest_chunk_upload = already_loaded; 
}; 

WCCCFMultipleFileUploader.prototype.getNumberOfFilesToUpload = function() 
{
     return this.number_of_files_to_upload;
};
WCCCFMultipleFileUploader.prototype.onSingleFileUploadProgress = function(myself, event)
{
	myself.setGloballyLoadedBytes(event.loaded, myself);
	
	var pc = parseInt((myself.already_loaded_globally_bytes / myself.sum_all_file_sizes * 100));
	pc = pc > 100 ? 100 : pc;
	jQuery('#wcccf_upload_progressbar_'+myself.field_id).css('width', pc+"%");
	jQuery('#wcccf_upload_progressbar_percent_'+myself.field_id).html(pc + "%");

}
WCCCFMultipleFileUploader.prototype.resetFileUploadProgressBar = function(myself)
{
	jQuery('#wcccf_upload_progressbar_'+myself.field_id).css('width', "0%");
	jQuery('#wcccf_upload_progressbar_percent_'+myself.field_id).html("0%");
}
WCCCFMultipleFileUploader.prototype.cloneObject = function( original )  
{
    var clone = Object.create( Object.getPrototypeOf( original ) ) ;
    var i , keys = Object.getOwnPropertyNames( original ) ;

    for ( i = 0 ; i < keys.length ; i ++ )
    {
        Object.defineProperty( clone , keys[ i ] ,
            Object.getOwnPropertyDescriptor( original , keys[ i ] )
        ) ;
    }

    return clone;
}