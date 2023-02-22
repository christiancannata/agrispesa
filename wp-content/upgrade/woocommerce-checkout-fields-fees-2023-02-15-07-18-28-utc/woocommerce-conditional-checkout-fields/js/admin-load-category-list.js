"use strict";

function wcccf_init_category_select2() 
{
	
	jQuery(".js-data-category-ajax").select2(
		{
			width:300,
			ajax: {
			url: ajaxurl,
			dataType: 'json',
			delay: 250,
			multiple: true,
			data: function (params) {
			  return {
				product_category: params.term, // search term
				page: params.page,
				action: 'wcccf_get_category_list'
			  };
			},
			processResults: function (data, page) 
			{
		   
			   return {
				results: jQuery.map(data, function(obj) {
					return { id: obj.id, text: obj.category_name };
					}),
				pagination: {
							  'more': typeof data.pagination === 'undefined' ? false : data.pagination.more
							}
				
				};
			},
			cache: true
		  },
		  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		  minimumInputLength: 1,
		  templateResult: wcccf_formatRepo, 
		  templateSelection: wcccf_formatRepoSelection  
		});
}