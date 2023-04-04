(function($){
	
	// posts

	$('table.posts #the-list, table.pages #the-list').sortable({
		'items': 'tr',
		'axis': 'y',
		'helper': fixHelper,
		'update' : function(e, ui) {
			$.post( ajaxurl, {
				action: 'update-menu-order',
				order: $('#the-list').sortable('serialize'),
			});
		}
	});
	//$("#the-list").disableSelection();
	
	// tags
	
	$('table.tags #the-list').sortable({
		'items': 'tr',
		'axis': 'y',
		'helper': fixHelper,
		'update' : function(e, ui) {
			$.post( ajaxurl, {
				action: 'update-menu-order-tags',
				order: $('#the-list').sortable('serialize'),
			});
		}
	});
	
	$('table.users #the-list').sortable({
		'items': 'tr',
		'axis': 'y',
		'helper': fixHelper,
		'update' : function(e, ui) {
			var ref = $('input[name="_wp_http_referer"]').val().split('?');
			$.post( ajaxurl, {
				action: 'update-menu-order-users',
				order: $('#the-list').sortable('serialize'),
				referer_string: ref[1]
			});
		}
	});	
	
	$.each($('table.plugins:visible #the-list > tr'), function(i,j){
		var tid = 'plugin_'+$(this).find('input[name="checked[]"]').attr('id').replace('checkbox_', '');
		$(this).attr('id', tid);
	});
	
	$('table.plugins:visible #the-list').sortable({
		'items': 'tr',
		'axis': 'y',
		'helper': fixHelper,
		'refreshPositions': true,
		'update' : function(e, ui) {		
			$.post( ajaxurl, {
				action: 'update-menu-order-extras',
				order: $('#the-list').sortable('serialize')
			});
		}
	});		
	//$("#the-list").disableSelection();
	
	var fixHelper = function(e, ui) {
		ui.children().children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};
	

	
})(jQuery)
