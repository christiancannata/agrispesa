"use strict";

function wcccf_init_customer_select2()
{
	jQuery(".js-data-customers-ajax").select2(
	{
	   width: 600,
	  ajax: {
		url: ajaxurl,
		dataType: 'json',
		delay: 250,
		multiple: false,
		data: function (params) {
		  return {
			search_string: params.term, // search term
			page: params.page || 1,
			action: 'wcccf_get_usernames_list'
		  };
		},
		processResults: function (data, params) 
		{
		  //console.log(params);
		 
		   return {
			results: jQuery.map(data.results, function(obj) 
			{
				var user = (obj.first_name+obj.last_name).length != 0 ? "<b>User: </b>"+obj.first_name+" "+obj.last_name+"<br>" : "<b>User</b>: N/A <br>";
				
				 return { id: obj.ID, text: "<b>User ID: </b>"+obj.ID+"<br>"+  
											  "<b>Email: </b>"+obj.email+"<br>"+
											   user +
											  "<b>Billing: </b> "+obj.billing_name+" "+obj.billing_last_name+" - "+obj.billing_email+"<br><br>"
											 
											  }; 
			}),
			pagination: {
						  'more': typeof data.pagination === 'undefined' ? false : data.pagination.more
						}
			};
		},
		cache: true
	  },
	  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
	  minimumInputLength: 0,
	  templateResult: wcccf_formatRepo, 
	  templateSelection: wcccf_formatRepoSelection  
	}
	);
	
	
}
function wcccf_formatRepo (repo) 
{
	if (repo.loading) return repo.text;
	
	var markup = '<div class="clearfix">' +
			'<div class="col-sm-12">' + repo.text + '</div>';
    markup += '</div>'; 
	
    return markup;
  }

  function wcccf_formatRepoSelection (repo) 
  {
	  return repo.full_name || repo.text;
  }