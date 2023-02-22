"use strict";

function wcccf_init_product_select2()
{
	
	jQuery(".js-data-product-ajax").each(function(index, elem)
	{
		
		jQuery(elem).select2(
		{
		  width: 300,
		 ajax: {
			url: ajaxurl,
			dataType: 'json',
			delay: 250,
			tags: "true",
			multiple: true,
			
			data: function (params) {
			  return {
				search_string: params.term, // search term
				page: params.page || 1,
				action: 'wcccf_get_product_list'
			  };
			},
			processResults: function (data, params) 
			{
			  //console.log(params);
			 
			   return {
				results: jQuery.map(data.results, function(obj) 
				{
					return { id: obj.id, text: "(SKU: "+obj.product_sku+" ID: "+obj.id+") "+obj.product_name };
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
		});
	
	});
	
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