var thwcfe_settings_base = (function($, window, document) {

	function setup_enhanced_multi_select_with_value(form){
		form.find('select.thwcfe-enhanced-multi-select').each(function(){
			if(!$(this).hasClass('enhanced')){
				$(this).select2({
					minimumResultsForSearch: 10,
					allowClear : true,
					placeholder: $(this).data('placeholder')
				}).addClass('enhanced');
				
				var value = $(this).data('value');
				value = value.split(",");
				
				$(this).val(value);
				$(this).trigger('change');
			}
		});
	}
	
	function setup_sortable_table(parent, elm, left){
		parent.find(elm+' tbody').sortable({
			items:'tr',
			cursor:'move',
			axis:'y',
			handle: 'td.sort',
			scrollSensitivity:40,
			helper:function(e,ui){
				ui.children().each(function(){
					$(this).width($(this).width());
				});
				ui.css('left', left);
				return ui;
			}		
		});	
	}
				
	return {
		setupEnhancedMultiSelectWithValue : setup_enhanced_multi_select_with_value,
		setupSortableTable : setup_sortable_table,
   	};
}(window.jQuery, window, document));	

/* Common Functions */
function thwcfeSetupEnhancedMultiSelectWithValue(elm){
	thwcfe_settings_base.setupEnhancedMultiSelectWithValue(elm);
}

function thwcfeSetupSortableTable(parent, elm, left){
	thwcfe_settings_base.setupSortableTable(parent, elm, left);
}
