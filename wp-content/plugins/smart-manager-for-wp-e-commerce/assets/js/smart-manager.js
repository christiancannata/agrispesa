/**
 * Smart Manager JS class
 * Initialize and load data in grid
 * Public interface
 **/

function Smart_Manager() { 
	var currentDashboardModel='', dashboard_key= '', dashboardName= '', dashboard_select_options= '',sm_nonce= '', column_names= new Array(), simpleSearchText = '', advancedSearchQuery= new Array(), post_data_params = '', 
		month_names_short = '', search_count, state_apply, dashboard_states = {}, skip_default_action, current_selected_dashboard = '';
}
const { __, _x, _n, _nx, sprintf } = wp.i18n;
Smart_Manager.prototype.init = function() {

	this.firstLoad = true
	this.currentDashboardModel='';
	this.dashboard_key= '';
	this.dashboardName= '';
	this.dashboard_select_options= '';
	this.sm_nonce= '';
	this.column_names= new Array();
	this.advancedSearchQuery= new Array();
	this.simpleSearchText = '';
	this.advancedSearchRuleCount = 0;
	this.post_data_params = '';
	this.month_names_short = '';
	this.dashboardStates = {};
	this.current_selected_dashboard = '';
	this.currentDashboardData = [];
	this.currentVisibleColumns = new Array('');
	this.editedData = {};
	this.editedCellIds = [];
	this.selectedRows = [];
	this.duplicateStore = false;
	this.selectAll = false;
	this.batch_update_action_options_default = '';
	this.batch_update_actions = {};
	this.sm_beta_smart_date_filter = '';
	this.addRecords_count = 0;
	this.defaultColumnsAddRow = new Array('posts_post_status', 'posts_post_title', 'posts_post_content');
	this.columnsVisibilityUsed = false; // flag for handling column visibility
	this.totalRecords = 0;
	this.displayTotalRecords = 0;
	this.loadedTotalRecords = 0;
	this.hotPlugin = {}; //object containing all Handsontable plugins
	this.gettingData = 0;
	this.searchType = sm_beta_params.search_type;
	this.advancedSearchContent = '';
	this.simpleSearchContent = '';
	this.searchTimeoutId = 0;
	this.columnSort = false;
	this.defaultEditor = true;
	this.currentGetDataParams = {};
	this.modifiedRows = [];
	this.dirtyRowColIds = {};
	this.wpToolsPanelWidth = 0;
	this.kpiData = {};
	this.defaultSortParams = { orderby: 'ID', order: 'DESC', default: true };
	this.isColumnModelUpdated = false
	this.state_apply = false;
	this.skip_default_action = false;
	this.search_count = 0;
	this.page = 1;
	this.hideDialog = '';
	this.multiselect_chkbox_list = '';
	this.limit = sm_beta_params.record_per_page;
	this.sm_dashboards_combo = '', // variable to store the dashboard name;
	this.column_names_batch_update = new Array(), // array for storing the batch update field;
	this.sm_store_table_model = new Array(), // array for storing store table mode;
	this.lastrow = '1';
	this.lastcell = '1';
	this.grid_width = '750';
	this.grid_height = '600';
	this.sm_ajax_url = (ajaxurl.indexOf('?') !== -1) ? ajaxurl + '&action=sm_beta_include_file' : ajaxurl + '?action=sm_beta_include_file';

	this.sm_qtags_btn_init = 1;
	this.sm_grid_nm = 'sm_editor_grid'; //name of div containing jqgrid
	this.sm_wp_editor_html = ''; //variable for storing the html of the wp editor
	this.sm_last_edited_row_id = '';
	this.sm_last_edited_col = '';
	this.colModelSearch = {};
	this.advancedSearchRoute = "advancedSearch";
	this.bulkEditRoute = "bulkEdit";
	this.columnManagerRoute = "columnManager";
	this.currentColModel = '';

	this.notification = {} //object for handling all notification messages
	this.notificationHideDelayInMs = 16000

	this.modal = {} //object for handling all modal dialogs

	// defining operators for diff datatype for advanced search

	let intOperators = {
		'eq': '==',
		'neq': '!=',
		'lt': '<',
		'gt': '>',
		'lte': '<=',
		'gte': '>='
	}

	this.possibleOperators = {
			'numeric': intOperators,
			'date': intOperators,
			'datetime': intOperators,
			'date': intOperators,
			'dropdown': {'is': _x('is', "select options - operator for 'dropdown' data type fields", 'smart-manager-for-wp-e-commerce'), 
						'is not': _x('is not', "select options - operator for 'dropdown' data type fields", 'smart-manager-for-wp-e-commerce')},
			'text': {'is': _x('is', "select options - operator for 'text' data type fields", 'smart-manager-for-wp-e-commerce'), 
					'like': _x('contains', "select options - operator for 'text' data type fields", 'smart-manager-for-wp-e-commerce'),
					'is not': _x('is not', "select options - operator for 'text' data type fields", 'smart-manager-for-wp-e-commerce'), 
					'not like': _x('not contains', "select options - operator for 'text' data type fields", 'smart-manager-for-wp-e-commerce')
				}
	}

	this.savedSearch = []
	this.savedBulkEditConditions = []
	this.batch_background_process = sm_beta_params.batch_background_process;
	this.sm_success_msg = sm_beta_params.success_msg;
	this.background_process_name = sm_beta_params.background_process_name;
	this.sm_updated_successful = parseInt(sm_beta_params.updated_successful);
	this.sm_updated_msg = sm_beta_params.updated_msg;
	this.sm_dashboards = sm_beta_params.sm_dashboards;
	this.sm_views = (sm_beta_params.hasOwnProperty('sm_views')) ? JSON.parse(sm_beta_params.sm_views) : {};
	this.ownedViews = (sm_beta_params.hasOwnProperty('sm_owned_views')) ? JSON.parse(sm_beta_params.sm_owned_views) : [];
	this.publicViews = (sm_beta_params.hasOwnProperty('sm_public_views')) ? JSON.parse(sm_beta_params.sm_public_views) : []
	this.viewPostTypes = (sm_beta_params.hasOwnProperty('sm_view_post_types')) ? JSON.parse(sm_beta_params.sm_view_post_types) : {}
	this.recentDashboards = (sm_beta_params.hasOwnProperty('recent_dashboards')) ? JSON.parse(sm_beta_params.recent_dashboards) : [];
	this.recentViews = (sm_beta_params.hasOwnProperty('recent_views')) ? JSON.parse(sm_beta_params.recent_views) : [];
	this.recentDashboardType = (sm_beta_params.hasOwnProperty('recent_dashboard_type')) ? sm_beta_params.recent_dashboard_type : 'post_type';
	this.sm_dashboards_public = sm_beta_params.sm_dashboards_public;
	this.taxonomyDashboards = (sm_beta_params.hasOwnProperty('taxonomy_dashboards')) ? JSON.parse(sm_beta_params.taxonomy_dashboards) : {};
	this.allTaxonomyDashboards = (sm_beta_params.hasOwnProperty('all_taxonomy_dashboards')) ? JSON.parse(sm_beta_params.all_taxonomy_dashboards) : {};
	this.recentTaxonomyDashboards = (sm_beta_params.hasOwnProperty('recent_taxonomy_dashboards')) ? JSON.parse(sm_beta_params.recent_taxonomy_dashboards) : [];
	this.sm_lite_dashboards = sm_beta_params.lite_dashboards;
	this.sm_admin_email = sm_beta_params.sm_admin_email;
	this.sm_deleted_successful = parseInt(sm_beta_params.deleted_successful);
	this.trashEnabled = sm_beta_params.trashEnabled;

	this.clearSearchOnSwitch = true;

	this.sm_is_woo30 = sm_beta_params.SM_IS_WOO30;
	this.sm_id_woo22 = sm_beta_params.SM_IS_WOO22;
	this.sm_is_woo21 = sm_beta_params.SM_IS_WOO21;
	this.sm_beta_pro = sm_beta_params.SM_BETA_PRO;
	this.smAppAdminURL = sm_beta_params.SM_APP_ADMIN_URL;

	this.wooPriceDecimalPlaces = ( typeof sm_beta_params.woo_price_decimal_places != 'undefined' ) ? sm_beta_params.woo_price_decimal_places : 2;
	this.wooPriceDecimalSeparator = ( typeof sm_beta_params.woo_price_decimal_separator != 'undefined' ) ? sm_beta_params.woo_price_decimal_separator : '.';

	this.wpDbPrefix = sm_beta_params.wpdb_prefix;

	this.backgroundProcessRunningMessage = sm_beta_params.background_process_running_message

	this.trashAndDeletePermanently = sm_beta_params.trashAndDeletePermanently

	this.window_width = jQuery(window).width();
	this.window_height = jQuery(window).height();

	this.pricingPageURL = ((this.smAppAdminURL) ? this.smAppAdminURL : location.href) + '-pricing';
	
	this.month_names_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

	this.isSettingsPage = sm_beta_params.is_settings_page;

	this.forceCollapseAdminMenu = (sm_beta_params.hasOwnProperty('forceCollapseAdminMenu')) ? parseInt(sm_beta_params.forceCollapseAdminMenu) : 0
	this.defaultImagePlaceholder = (sm_beta_params.hasOwnProperty('defaultImagePlaceholder')) ? sm_beta_params.defaultImagePlaceholder : ''
	this.rowHeight = (sm_beta_params.hasOwnProperty('rowHeight')) ? sm_beta_params.rowHeight : '50px'

	//Code for setting the default dashboard
	if( typeof this.sm_dashboards != 'undefined' && this.sm_dashboards != '' ) {
		this.sm_dashboards_combo = this.sm_dashboards = JSON.parse(this.sm_dashboards);
		this.sm_lite_dashboards = JSON.parse(this.sm_lite_dashboards);

		let defaultDashboardslug = (Array.isArray(this.recentDashboards) && this.recentDashboards.length > 0) ? this.recentDashboards[0] : '';
		this.dashboardName = (defaultDashboardslug) ? this.sm_dashboards[defaultDashboardslug] : ''
		if(this.sm_beta_pro == 1){
			if((this.recentDashboardType == 'view' || defaultDashboardslug == '') && this.recentViews.length > 0){
				defaultDashboardslug = (this.viewPostTypes.hasOwnProperty(this.recentViews[0])) ? this.recentViews[0] : '';
				this.dashboardName = (this.sm_views[defaultDashboardslug]) ? this.sm_views[defaultDashboardslug] : this.dashboardName
			}

			if((this.recentDashboardType == 'taxonomy' || defaultDashboardslug == '') && this.recentTaxonomyDashboards.length > 0){
				defaultDashboardslug = (this.recentTaxonomyDashboards[0]) ? this.recentTaxonomyDashboards[0] : defaultDashboardslug;
				this.dashboardName = (this.taxonomyDashboards[defaultDashboardslug]) ? this.taxonomyDashboards[defaultDashboardslug] : this.dashboardName
			}
		}

		this.current_selected_dashboard = defaultDashboardslug;
		this.dashboard_key = defaultDashboardslug;

		this.sm_nonce = this.sm_dashboards['sm_nonce'];
		delete this.sm_dashboards['sm_nonce'];
	}
	
	window.smart_manager.setDashboardDisplayName();

	this.loadMoreBtnHtml = "<button id='sm_editor_grid_load_items' style='height:2em;border: 1px solid #5850ec;background-color: white;border-radius: 3px;cursor: pointer;line-height: 17px;color: #5850ec;'>"+ sprintf(_x('Load More %s', 'bottom bar button', 'smart-manager-for-wp-e-commerce'), "<span>"+window.smart_manager.dashboardDisplayName+"</span>")+"</button>"

	this.container = document.getElementById('sm_editor_grid');

	this.body_font_size = jQuery("body").css('font-size');
	this.body_font_family = jQuery("body").css('font-family');
	this.editedAttribueSlugs = '';
	this.excludeFieldKeys = [];

	//Function to set all the states on unload
	window.onbeforeunload = function (evt) { 
		if ( typeof (window.smart_manager.updateState) !== "undefined" && typeof (window.smart_manager.updateState) === "function" ) {
			window.smart_manager.updateState({'async': false}); //refreshing the dashboard states
		}
	}

	if ( !jQuery(document.body).hasClass('folded') && window.smart_manager.sm_beta_pro == 1 && !window.smart_manager.isSettingsPage && window.smart_manager.forceCollapseAdminMenu == 1) {
		jQuery(document.body).addClass('folded');
	}

	let contentwidth = jQuery('#wpbody-content').width() - 20,
		contentheight = 910;

	let grid_height = contentheight - ( contentheight * 0.20 ); 

	window.smart_manager.grid_width = contentwidth - (contentwidth * 0.01);
	window.smart_manager.grid_height = ( grid_height < document.body.clientHeight - 400 ) ? document.body.clientHeight - 400 : grid_height;

	jQuery('#sm_editor_grid').trigger( 'smart_manager_init' ); //custom trigger

	window.smart_manager.load_dashboard();
	window.smart_manager.event_handler();
	window.smart_manager.loadNavBar();

	//Code for setting rowHeight CSS variable
	let r = document.querySelector(':root');
	if(r){
		r.style.setProperty('--row-height', window.smart_manager.rowHeight);
	}
}


Smart_Manager.prototype.convert_to_slug = function(text) {
	return text
		.toLowerCase()
		.replace(/ /g,'-')
		.replace(/[^\w-]+/g,'');
}

Smart_Manager.prototype.convert_to_pretty_text = function(text) {
	return text
		.replace(/_/g,' ')
		.split(' ')
	    .map((s) => s.charAt(0).toUpperCase() + s.substring(1))
	    .join(' ');
}

Smart_Manager.prototype.setDashboardDisplayName = function(){
	window.smart_manager.dashboardDisplayName = window.smart_manager.dashboardName;
	let viewSlug = window.smart_manager.getViewSlug(window.smart_manager.dashboardName);
	if(viewSlug){
		window.smart_manager.dashboardDisplayName = (window.smart_manager.sm_dashboards[window.smart_manager.viewPostTypes[viewSlug]]) ? window.smart_manager.sm_dashboards[window.smart_manager.viewPostTypes[viewSlug]] : 'records';
		window.smart_manager.dashboardDisplayName = (window.smart_manager.dashboardDisplayName === 'records' && window.smart_manager.allTaxonomyDashboards[window.smart_manager.viewPostTypes[viewSlug]]) ? window.smart_manager.allTaxonomyDashboards[window.smart_manager.viewPostTypes[viewSlug]] : window.smart_manager.dashboardDisplayName;
	}
}

Smart_Manager.prototype.load_dashboard = function() {
	jQuery('#sm_editor_grid').trigger( 'smart_manager_pre_load_dashboard' ); //custom trigger

	window.smart_manager.page = 1;

	if( typeof(window.smart_manager.currentDashboardModel) == 'undefined' || window.smart_manager.currentDashboardModel == '' ) {
		window.smart_manager.column_names = new Array('');
		window.smart_manager.column_names_batch_update = new Array();
		
		var sm_dashboard_valid = 0;
		if( window.smart_manager.sm_beta_pro == 0 ) {
			sm_dashboard_valid = 0;
			if( window.smart_manager.sm_lite_dashboards.indexOf(window.smart_manager.dashboard_key) >= 0 ) {
				sm_dashboard_valid = 1;    
			}
		} else {
			sm_dashboard_valid = 1;
		}

		if(typeof window.smart_manager.hot == 'undefined'){
			window.smart_manager.loadGrid();
		}
		
		if( sm_dashboard_valid == 1 ) {			
			window.smart_manager.getDashboardModel();
			window.smart_manager.getData();
		} else {
			jQuery("#sm_dashboard_select").val(window.smart_manager.current_selected_dashboard);
			window.smart_manager.notification = {message: sprintf(_x('For managing %s, %s %s version', 'modal content', 'smart-manager-for-wp-e-commerce'), window.smart_manager.dashboardDisplayName, window.smart_manager.sm_success_msg, '<a href="' + window.smart_manager.pricingPageURL + '" target="_blank">'+_x('Pro', 'modal content', 'smart-manager-for-wp-e-commerce')+'</a>'),hideDelay: window.smart_manager.notificationHideDelayInMs}
			window.smart_manager.showNotification()
		}
	} else {
		window.smart_manager.getData();
	}

}

// Function to create optgroups for dashboards
Smart_Manager.prototype.createOptGroups = function(args={}) {

	if(Object.keys(args).length == 0){
		return;
	}

	if(!args.parent || !args.child){
		return
	}

	let parent = (!Array.isArray(args.parent)) ? Object.keys(args.parent) : args.parent,
		child = (!Array.isArray(args.child)) ? Object.keys(args.child) : args.child,
		options = '';

	child.map((key) => {
		if((parent.includes(key) && args['is_recently_accessed']) || (!parent.includes(key) && !args['is_recently_accessed']) || args['isParentChildSame']){
			options += '<option value="'+key+'" '+ ((key == window.smart_manager.dashboard_key) ? "selected" : "") +'>'+((args['is_recently_accessed']) ? args.parent[key] : args.child[key]) +'</option>';
		}
	});

	window.smart_manager.dashboard_select_options += (options != '') ? '<optgroup label="'+args.label+'">'+options+'</optgroup>' : '';
}

// Function to load top right bar on the page
Smart_Manager.prototype.loadNavBar = function() {

	//Code for simple & advanced search
	let selected = '',
	switchSearchType = ( window.smart_manager.searchType == 'simple' ) ? _x('Advanced', 'search type', 'smart-manager-for-wp-e-commerce') : _x('Simple', 'search type', 'smart-manager-for-wp-e-commerce');

	window.smart_manager.simpleSearchContent = "<input type='text' id='sm_simple_search_box' placeholder='"+_x('Type to search...', 'placeholder', 'smart-manager-for-wp-e-commerce')+"'value='"+window.smart_manager.simpleSearchText+"'>";
	window.smart_manager.advancedSearchContent = '<div id="sm_advanced_search" title="'+_x('Click to add/edit condition', 'tooltip', 'smart-manager-for-wp-e-commerce')+'">'+
													'<div id="sm_advanced_search_content">'+ sprintf(_x('%d condition%s', 'search conditions', 'smart-manager-for-wp-e-commerce'), window.smart_manager.advancedSearchRuleCount, ((window.smart_manager.advancedSearchRuleCount > 1) ? _x('s', 'search conditions', 'smart-manager-for-wp-e-commerce') : ''))+'</div>'+
													'<div id="sm_advanced_search_icon">'+
														'<span class="dashicons dashicons-edit-large"></span>'+
													'</div>'+
												'</div>';

	//Code for dashboards select2
	window.smart_manager.dashboard_select_options = '';
	
	if( window.smart_manager.sm_beta_pro == 1 ) {
		
		let recentDashboards = (!Array.isArray(window.smart_manager.recentDashboards)) ? window.smart_manager.recentDashboards.values() : window.smart_manager.recentDashboards,
			recentTaxonomyDashboards = (!Array.isArray(window.smart_manager.recentTaxonomyDashboards)) ? window.smart_manager.recentTaxonomyDashboards.values() : window.smart_manager.recentTaxonomyDashboards;

		// Code for rendering recently accessed dashboards
		if(recentDashboards.length > 0){
			window.smart_manager.createOptGroups({'parent': window.smart_manager.sm_dashboards,
				'child': recentDashboards,
				'label': _x('Common post types', 'dashboard option groups', 'smart-manager-for-wp-e-commerce'),
				'is_recently_accessed': true
			});
		}

		// Code for rendering recently accessed taxonomy dashboards
		if(recentTaxonomyDashboards.length > 0){
			window.smart_manager.createOptGroups({'parent': window.smart_manager.taxonomyDashboards,
				'child': recentTaxonomyDashboards,
				'label': _x('Common taxonomies', 'dashboard option groups', 'smart-manager-for-wp-e-commerce'),
				'is_recently_accessed': true
			});
		}

		// Code for rendering recently accessed views
		if(window.smart_manager.recentViews.length > 0 && Object.keys(window.smart_manager.sm_views).length > 0){
			options = '';
			window.smart_manager.recentViews.map((key) => {
				if(window.smart_manager.sm_views.hasOwnProperty(key) && window.smart_manager.viewPostTypes.hasOwnProperty(key)){
					options += '<option value="'+window.smart_manager.viewPostTypes[key]+'" '+ ((key == window.smart_manager.dashboard_key) ? "selected" : "") +'>'+window.smart_manager.sm_views[key]+'</option>';
				}
			});
			window.smart_manager.dashboard_select_options += (options != '') ? '<optgroup label="'+_x('Recently used views', 'dashboard option groups', 'smart-manager-for-wp-e-commerce')+'">'+options+'</optgroup>' : '';
		}

		// Code for rendering all remmaining dashboards
		if(Object.keys(window.smart_manager.sm_dashboards).length > 0){
				window.smart_manager.createOptGroups({'parent': recentDashboards,
					'child': window.smart_manager.sm_dashboards,
					'label': _x('All post types', 'dashboard option groups', 'smart-manager-for-wp-e-commerce'),
					'is_recently_accessed': false
				});
		}

		// Code for rendering all remmaining taxonomy dashboards
		if(Object.keys(window.smart_manager.taxonomyDashboards).length > 0){
			window.smart_manager.createOptGroups({'parent': recentTaxonomyDashboards,
				'child': window.smart_manager.taxonomyDashboards,
				'label': _x('All taxonomies', 'dashboard option groups', 'smart-manager-for-wp-e-commerce'),
				'is_recently_accessed': false
			});
		}

		// Code for rendering all remmaining views
		if(Object.keys(window.smart_manager.sm_views).length > 0){
			window.smart_manager.dashboard_select_options += '<optgroup label="'+_x('All saved views', 'dashboard option groups', 'smart-manager-for-wp-e-commerce')+'">';
			Object.keys(window.smart_manager.sm_views).map((key) => {
				if(!window.smart_manager.recentViews.includes(key) && window.smart_manager.viewPostTypes.hasOwnProperty(key)){
					window.smart_manager.dashboard_select_options += '<option value="'+window.smart_manager.viewPostTypes[key]+'" '+ ((key == window.smart_manager.dashboard_key) ? "selected" : "") +'>'+window.smart_manager.sm_views[key]+'</option>';
				}
			});
			window.smart_manager.dashboard_select_options += '</optgroup>';
		}

		// Code to change the dashboard key to view post type
		let viewSlug = window.smart_manager.getViewSlug(window.smart_manager.dashboardName);
		if(viewSlug){
			window.smart_manager.dashboard_key = window.smart_manager.viewPostTypes[viewSlug];
		}
	} else {
		if(Object.keys(window.smart_manager.sm_dashboards).length > 0){
			window.smart_manager.createOptGroups({'parent': window.smart_manager.sm_dashboards,
				'child': window.smart_manager.sm_dashboards,
				'label': _x('All post types', 'dashboard option groups', 'smart-manager-for-wp-e-commerce'),
				'is_recently_accessed': false,
				'isParentChildSame': true
			});
		}

		if(Object.keys(window.smart_manager.taxonomyDashboards).length > 0){
			window.smart_manager.createOptGroups({'parent': window.smart_manager.taxonomyDashboards,
				'child': window.smart_manager.taxonomyDashboards,
				'label': _x('All taxonomies', 'dashboard option groups', 'smart-manager-for-wp-e-commerce'),
				'is_recently_accessed': false,
				'isParentChildSame': true
			});
		}
	}

	let navBar = "<select id='sm_dashboard_select'> </select>"+
				"<div id='sm_nav_bar_search'>"+
					"<div id='search_content_parent'>"+
						"<div id='search_content' style='width:98%;'>"+
							( ( window.smart_manager.searchType == 'simple' ) ? window.smart_manager.simpleSearchContent : window.smart_manager.advancedSearchContent )+
						"</div>"+
					"</div>"+
					"<div id='sm_top_bar_advanced_search'>"+
						"<div id='search_switch_container'> <input type='checkbox' id='search_switch' switchSearchType='"+ switchSearchType.toLowerCase() +"' /><label title='"+ sprintf(_x('Switch to %s', 'tooltip', 'smart-manager-for-wp-e-commerce'), switchSearchType) +"' for='search_switch'> "+ sprintf(_x('%s Search', 'search type', 'smart-manager-for-wp-e-commerce'), switchSearchType)+"</label></div>"+
						// "<div id='search_switch_lbl'> "+ sprintf(_x('%s Search', 'search type', 'smart-manager-for-wp-e-commerce'), String(switchSearchType).capitalize())+"</div>"+
					"</div>"+
				"</div>";

	jQuery('#sm_nav_bar .sm_beta_left').append(navBar);
	jQuery('#sm_dashboard_select').empty().append(window.smart_manager.dashboard_select_options);
	jQuery('#sm_dashboard_select').select2({ width: '15em', dropdownCssClass: 'sm_beta_dashboard_select', dropdownParent: jQuery('#sm_nav_bar') });


	let sm_top_bar = '<div id="sm_top_bar" style="font-weight:400 !important;width:100%;">'+
						'<div id="sm_top_bar_left" class="sm_beta_left" style="width:'+ window.smart_manager.grid_width +'px;background-color: white;padding: 0.5em 0em 1em 0em;">'+
							'<div class="sm_top_bar_action_btns">'+
								'<div id="batch_update_sm_editor_grid" title="'+_x('Bulk Edit', 'tooltip', 'smart-manager-for-wp-e-commerce')+'">'+
									'<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">'+
										'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>'+
									'</svg>'+
									'<span>'+_x('Bulk Edit', 'button', 'smart-manager-for-wp-e-commerce')+'</span>'+
								'</div>'+
							'</div>'+
							'<div class="sm_top_bar_action_btns">'+
								'<div id="save_sm_editor_grid_btn" title="'+_x('Save', 'tooltip', 'smart-manager-for-wp-e-commerce')+'">'+
									'<svg class="sm-ui-state-disabled" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">'+
										'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>'+
									'</svg>'+
									'<span>'+_x('Save', 'button', 'smart-manager-for-wp-e-commerce')+'</span>'+
								'</div>'+
								'<div id="add_sm_editor_grid" title="'+_x('Add Row', 'tooltip', 'smart-manager-for-wp-e-commerce')+'">'+
									'<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">'+
										'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>'+
									'</svg>'+
									'<span>'+_x('Add Row', 'button', 'smart-manager-for-wp-e-commerce')+'</span>'+
								'</div>'+
								'<div id="dup_sm_editor_grid" class="sm_beta_dropdown">'+
									'<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">'+
										'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>'+
									'</svg>'+
									'<span title="'+_x('Duplicate', 'tooltip', 'smart-manager-for-wp-e-commerce')+'">'+_x('Duplicate', 'button', 'smart-manager-for-wp-e-commerce')+'</span>'+
									'<div class="sm_beta_dropdown_content">'+
										'<a id="sm_beta_dup_selected" href="#">'+_x('Selected Records', 'duplicate button', 'smart-manager-for-wp-e-commerce')+'</a>'+
										'<a id="sm_beta_dup_entire_store" href="#">'+_x('Entire Store', 'duplicate button', 'smart-manager-for-wp-e-commerce')+'</a>'+
									'</div>'+
								'</div>'+
								'<div id="del_sm_editor_grid" class="sm_beta_dropdown">'+
									'<svg class="sm-error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">'+
										'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>'+
									'</svg>'+
									'<span title="'+_x('Delete', 'tooltip', 'smart-manager-for-wp-e-commerce')+'">'+_x('Delete', 'button', 'smart-manager-for-wp-e-commerce')+'</span>'+
									'<div class="sm_beta_dropdown_content">'+
										'<a id="sm_beta_move_to_trash" href="#">'+_x('Move to Trash', 'delete button', 'smart-manager-for-wp-e-commerce')+'</a>'+
										'<a id="sm_beta_delete_permanently" href="#" class="sm-error-icon">'+_x('Delete Permanently', 'delete button', 'smart-manager-for-wp-e-commerce')+'</a>'+
									'</div>'+
								'</div>'+
							'</div>'+
							'<div class="sm_top_bar_action_btns">'+
								'<div id="sm_custom_views" class="sm_beta_dropdown">'+
									'<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">'+
										'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"></path>'+
									'</svg>'+
									'<span title="'+_x("Custom Views", 'tooltip', 'smart-manager-for-wp-e-commerce')+'">'+_x('Custom Views', 'button', 'smart-manager-for-wp-e-commerce')+'</span>'+
									'<div class="sm_beta_dropdown_content">'+
										'<a id="sm_custom_views_create" href="#">'+_x('Create', 'custom view button', 'smart-manager-for-wp-e-commerce')+'</a>'+
										'<a id="sm_custom_views_update" href="#">'+_x('Update', 'custom view button', 'smart-manager-for-wp-e-commerce')+'</a>'+
										'<a id="sm_custom_views_delete" href="#" class="sm-error-icon">'+_x('Delete', 'custom view button', 'smart-manager-for-wp-e-commerce')+'</a>'+
									'</div>'+
								'</div>'+
								'<div id="export_csv_sm_editor_grid" title="'+_x('Export CSV', 'tooltip', 'smart-manager-for-wp-e-commerce')+'">'+
									'<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">'+
										'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'+
									'</svg>'+
									'<span>'+_x('Export CSV', 'button', 'smart-manager-for-wp-e-commerce')+'</span>'+
								'</div>'+
							'</div>'+
							'<div class="sm_top_bar_action_btns">'+
								'<div id="print_invoice_sm_editor_grid_btn" title="'+_x('Print Invoice', 'tooltip', 'smart-manager-for-wp-e-commerce')+'">'+
									'<svg class="sm-ui-state-disabled" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">'+
										'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>'+
									'</svg>'+
									'<span>'+_x('Print Invoice', 'button', 'smart-manager-for-wp-e-commerce')+'</span>'+
								'</div>'+
							'</div>'+
							'<div class="sm_top_bar_action_btns">'+
								'<div id="show_hide_cols_sm_editor_grid" title="'+_x('Show / Hide Columns', 'tooltip', 'smart-manager-for-wp-e-commerce')+'">'+
									'<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">'+
										'<path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />'+
									'</svg>'+
									'<span>'+_x('Columns', 'button', 'smart-manager-for-wp-e-commerce')+'</span>'+
								'</div>'+
							'</div>'+
						'</div>'+
					'</div>';

	let sm_bottom_bar = "<div id='sm_bottom_bar' style='font-weight:500 !important;color:#5850ec;width:"+window.smart_manager.grid_width+"px;'>"+
							"<div id='sm_bottom_bar_left' class='sm_beta_left'></div>"+
							"<div id='sm_bottom_bar_right' class='sm_beta_right'>"+
								"<div id='sm_beta_load_more_records' class='sm_beta_right' style='cursor: pointer;' title='"+_x('Load more records', 'tooltip', 'smart-manager-for-wp-e-commerce')+"'>"+window.smart_manager.loadMoreBtnHtml+"</div>"+
								"<div id='sm_beta_display_records' class='sm_beta_select_blue sm_beta_right'></div>"+
							"</div>"+
						"</div>";

	let sm_msg = jQuery('.sm_design_notice').prop('outerHTML');
	if(sm_msg){
		jQuery(sm_msg).insertAfter("#sm_nav_bar");
		jQuery('.wrap > .sm_design_notice').show()
	}

	jQuery(sm_top_bar).insertBefore("#sm_editor_grid");
	jQuery(sm_bottom_bar).insertAfter("#sm_editor_grid");

	if ( window.smart_manager.dashboard_key == 'shop_order' ) {
		jQuery('#print_invoice_sm_editor_grid_btn').show();
	} else {
		jQuery('#print_invoice_sm_editor_grid_btn').hide();
	}

	(window.smart_manager.isTaxonomyDashboard()) ? jQuery('#sm_beta_move_to_trash').hide() : jQuery('#sm_beta_move_to_trash').show();

	window.smart_manager.displayShowHideColumnSettings(true);

	//Code for Dashboard KPI
	jQuery('#sm_dashboard_kpi').remove();

	if( window.smart_manager.searchType != 'simple' ) {
		window.smart_manager.initialize_advanced_search(); //initialize advanced search control
	}

	jQuery('#sm_top_bar').trigger('sm_top_bar_loaded');
}

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

Smart_Manager.prototype.getCheckboxValues = function( colObj ) {
	
	if(!colObj){
		return [];
	}
	
	if( !(colObj.hasOwnProperty('checkedTemplate') && colObj.hasOwnProperty('uncheckedTemplate')) ) {
		colObj.checkedTemplate = 'true';
		colObj.uncheckedTemplate = 'false';
	}
	
	return new Array({'key': colObj.checkedTemplate, 'value':  String(colObj.checkedTemplate).capitalize()},
					{'key': colObj.uncheckedTemplate, 'value':  String(colObj.uncheckedTemplate).capitalize()});
}

Smart_Manager.prototype.initialize_advanced_search = function() {

	if( typeof(window.smart_manager.currentColModel) == 'undefined' ) {
		return;
	}

	let colModel = JSON.parse( JSON.stringify( window.smart_manager.currentColModel ) );
	window.smart_manager.colModelSearch = {}

	Object.entries(colModel).map(([key, obj]) => {

		if( obj.hasOwnProperty('searchable') && obj.searchable == 1 ) { 

			if( obj.type == 'checkbox' ) {
				obj.type = 'dropdown';
				obj.search_values = window.smart_manager.getCheckboxValues(obj);		
			}

			if( obj.type == 'sm.multilist' ) {
				obj.type = 'dropdown';
			}

			if( obj.type == 'text' ) {
				if( obj.hasOwnProperty('validator') ) {
					if( obj.validator == 'customNumericTextEditor' ) {
						obj.type = 'numeric';
					}
				}
			}

			if (obj.type == "number") {
				obj.type = 'numeric'
			}

			window.smart_manager.colModelSearch[obj.table_name +'.'+ obj.col_name] = {
																							'title': obj.name_display,
																							'type': (obj.hasOwnProperty('search_type')) ? obj.search_type : obj.type,
																							'values': (obj.search_values) ? obj.search_values : {}
																						};
		}
	});
	jQuery('#sm_advanced_search_content').html( sprintf(_x('%d condition%s', 'search conditions', 'smart-manager-for-wp-e-commerce'), window.smart_manager.advancedSearchRuleCount, ((window.smart_manager.advancedSearchRuleCount > 1) ? _x('s', 'search conditions', 'smart-manager-for-wp-e-commerce') : '')) )
}

Smart_Manager.prototype.showLoader = function( is_show = true ) {
	if ( is_show ) {
		jQuery('.sm-loader-container').hide().show();
	} else {
		jQuery('.sm-loader-container').hide();
	}
}

Smart_Manager.prototype.send_request = function(params, callback, callbackParams) {

	if( typeof params.showLoader == 'undefined' || (typeof params.showLoader != 'undefined' && params.showLoader !== false ) ) {
		window.smart_manager.showLoader();
	}

	if( window.smart_manager.sm_beta_pro == 1 ) {
		// Flag for handling taxonomy dashboards
		params.data['is_taxonomy'] = window.smart_manager.isTaxonomyDashboard();
	}

	jQuery.ajax({
		type : ( ( typeof(params.call_type) != 'undefined' ) ? params.call_type : 'POST' ),
		url : ( (typeof(params.call_url) != 'undefined' ) ? params.call_url : window.smart_manager.sm_ajax_url ),
		dataType: ( ( typeof(params.data_type) != 'undefined' ) ? params.data_type : 'text' ),
		async: ( ( typeof(params.async) != 'undefined' ) ? params.async : true ),
		data: params.data,
		success: function(resp) {
			if( typeof params.showLoader == 'undefined' || (typeof params.showLoader != 'undefined' && params.showLoader !== false ) ) {
				if(false == params.hasOwnProperty('hideLoader') || (params.hasOwnProperty('hideLoader') && false != params.hideLoader) ){
					window.smart_manager.showLoader(false);
				}
			}
			return ( ( typeof(callbackParams) != 'undefined' ) ? callback(callbackParams, resp) : callback(resp) );
		},
		error: function(error) {
			console.log('Smart Manager AJAX failed::', error);
		}
	});

}

//function to format the column model
Smart_Manager.prototype.format_dashboard_column_model = function( column_model ) {

	if( window.smart_manager.currentColModel == '' || typeof(window.smart_manager.currentColModel) == 'undefined' ) {
		return;
	}

	index = 0;

	if ( typeof (window.smart_manager.sortColumns) !== "undefined" && typeof (window.smart_manager.sortColumns) === "function" ) {
		window.smart_manager.sortColumns();
	}

	window.smart_manager.column_names = [];
	window.smart_manager.currentVisibleColumns = [];

	for (i = 0; i < window.smart_manager.currentColModel.length; i++) {

			if( typeof(window.smart_manager.currentColModel[i]) == 'undefined' ) {
				continue;
			}

			hidden = ( typeof(window.smart_manager.currentColModel[i].hidden) != 'undefined' ) ? window.smart_manager.currentColModel[i].hidden : true;

			column_values = (typeof(window.smart_manager.currentColModel[i].values) != 'undefined') ? window.smart_manager.currentColModel[i].values : {};

			type = (typeof(window.smart_manager.currentColModel[i].type) != 'undefined') ? window.smart_manager.currentColModel[i].type : '';
			editor = (typeof(window.smart_manager.currentColModel[i].editor) != 'undefined') ? window.smart_manager.currentColModel[i].editor : '';
			selectOptions = (typeof(window.smart_manager.currentColModel[i].selectOptions) != 'undefined') ? window.smart_manager.currentColModel[i].selectOptions : '';
			multiSelectSeparator = (typeof(window.smart_manager.currentColModel[i].separator) != 'undefined') ? window.smart_manager.currentColModel[i].separator : '';
			allowMultiSelect = false;

			if( type == 'dropdown' && editor == 'select2' ) {
				if( window.smart_manager.currentColModel[i].hasOwnProperty('select2Options') ) {
					if( window.smart_manager.currentColModel[i].select2Options.hasOwnProperty('data') ) {

						column_values = {};
						allowMultiSelect = ( window.smart_manager.currentColModel[i].select2Options.hasOwnProperty('multiple') ) ? window.smart_manager.currentColModel[i].select2Options.multiple : false;

						window.smart_manager.currentColModel[i].select2Options.data.forEach(function(obj) {
							column_values[obj.id] = obj.text;
						});
					}
				}
			}

			let bu_values = []
			if( Object.keys(column_values).length > 0 ){
				Object.keys(column_values).forEach(key => {
					bu_values.push({'key':key, 'value':column_values[key]})
				});
			}

			let name = '';

			if( typeof( window.smart_manager.currentColModel[i].name ) != 'undefined' ) {
				name = ( window.smart_manager.currentColModel[i].name ) ? window.smart_manager.currentColModel[i].name.trim() : '';
			}

			if(window.smart_manager.currentColModel[i].hasOwnProperty('name_display') === false) {// added for state management
				window.smart_manager.currentColModel[i].name_display = name;
			}

			if( hidden === false ) {
				window.smart_manager.column_names[index] = window.smart_manager.currentColModel[i].name_display; //Array for column headers
				window.smart_manager.currentVisibleColumns[index] = window.smart_manager.currentColModel[i];
				index++;
			}

			var batch_enabled_flag = false;

			if (window.smart_manager.currentColModel[i].hasOwnProperty('batch_editable')) {
				batch_enabled_flag = window.smart_manager.currentColModel[i].batch_editable;
			}

			if (batch_enabled_flag === true) {

				let type = window.smart_manager.currentColModel[i].type;

				if(window.smart_manager.currentColModel[i].hasOwnProperty('validator')) {
					if('customNumericTextEditor' == window.smart_manager.currentColModel[i].validator){
						type = 'numeric';	
					}
				}

				window.smart_manager.column_names_batch_update[window.smart_manager.currentColModel[i].src] = {title: window.smart_manager.currentColModel[i].name_display, type:type, editor:window.smart_manager.currentColModel[i].editor, values:bu_values, src:window.smart_manager.currentColModel[i].data, allowMultiSelect:allowMultiSelect, multiSelectSeparator:multiSelectSeparator};

				if( window.smart_manager.currentColModel[i].type == 'checkbox' ) {
					window.smart_manager.column_names_batch_update[window.smart_manager.currentColModel[i].src].type = 'dropdown';
				 	window.smart_manager.column_names_batch_update[window.smart_manager.currentColModel[i].src].values = window.smart_manager.getCheckboxValues(window.smart_manager.currentColModel[i]);
				}

				if( window.smart_manager.currentColModel[i].type == 'sm.multilist' ) {
					window.smart_manager.column_names_batch_update[window.smart_manager.currentColModel[i].src].type = 'multilist';

					//Code for setting the values
					let multilistValues = window.smart_manager.column_names_batch_update[window.smart_manager.currentColModel[i].src].values
					let multilistBulkEditValues = []
					multilistValues.forEach((obj) => {
						let val = (obj.hasOwnProperty('value')) ? obj.value : {}
						let title = (val.hasOwnProperty('title')) ? val.title : ((val.hasOwnProperty('term')) ? val.term : '') 
						multilistBulkEditValues.push({'key': obj.key, 'value': title});
					})
					window.smart_manager.column_names_batch_update[window.smart_manager.currentColModel[i].src].values = multilistBulkEditValues
				}
			}

			if ( typeof(window.smart_manager.currentColModel[i].allow_showhide) != 'undefined' && window.smart_manager.currentColModel[i].allow_showhide === false ) {
				window.smart_manager.currentColModel[i].hidedlg = true;
			}
			
			window.smart_manager.currentColModel[i].name = window.smart_manager.currentColModel[i].index;

			// setting the default width
			if (typeof(window.smart_manager.currentColModel[i].width) == 'undefined') {
				// window.smart_manager.currentColModel[i].width = 80;
			}

			//Code for formatting the values
			var formatted_values = '';
			window.smart_manager.currentColModel[i].wordWrap = true;
	}

	jQuery('#sm_editor_grid').trigger( 'smart_manager_post_format_columns' ); //custom trigger
}

Smart_Manager.prototype.setDashboardModel = function (response) {
	if( typeof response != 'undefined' && response != '' ) {
		window.smart_manager.sm_store_table_model = response.tables;
		window.smart_manager.currentColModel = response.columns;

		//call to function for formatting the column model
		if( typeof( window.smart_manager.format_dashboard_column_model ) !== "undefined" && typeof( window.smart_manager.format_dashboard_column_model ) === "function" ) {
			window.smart_manager.format_dashboard_column_model();
		}
		response.columns = window.smart_manager.currentColModel;
		window.smart_manager.currentDashboardModel = response;

		//Code for rendering the columns in grid
		window.smart_manager.formatGridColumns();

		if(window.smart_manager.hotPlugin.manualColumnResizePlugin){
			window.smart_manager.hotPlugin.manualColumnResizePlugin.manualColumnWidths = []
		}
		

		window.smart_manager.hot.updateSettings({
			data: window.smart_manager.currentDashboardData,
			columns: window.smart_manager.currentVisibleColumns,
			colHeaders: window.smart_manager.column_names,
			forceRender: window.smart_manager.firstLoad
		})

		//Code for handling sort state management
		if( window.smart_manager.currentDashboardModel.hasOwnProperty('sort_params') ) {
			if( window.smart_manager.currentDashboardModel.sort_params ) {
				if( window.smart_manager.currentDashboardModel.sort_params.hasOwnProperty('default') ) {
					window.smart_manager.hotPlugin.columnSortPlugin.sort();
				} else {
					if( window.smart_manager.currentVisibleColumns.length > 0 ) {
						for( let index in window.smart_manager.currentVisibleColumns ) {
							if( window.smart_manager.currentVisibleColumns[index].src == window.smart_manager.currentDashboardModel.sort_params.column ) {
								let sort_params = Object.assign({}, window.smart_manager.currentDashboardModel.sort_params);
								sort_params.column = parseInt(index);
								window.smart_manager.hotPlugin.columnSortPlugin.setSortConfig([sort_params]);
								break;
							}
						}
					}
				}
			}
		}

		if(window.smart_manager.firstLoad){
			window.smart_manager.firstLoad = false
		}

		if(window.smart_manager.sm_beta_pro == 1){

			jQuery('#sm_custom_views_update, #sm_custom_views_delete').hide();

			let viewSlug = window.smart_manager.getViewSlug(window.smart_manager.dashboardName);
			if(viewSlug){
				if(window.smart_manager.ownedViews.includes(viewSlug)) {
					jQuery('#sm_custom_views_update, #sm_custom_views_delete').show();
				}
			}

			if(response.hasOwnProperty('search_params')){
				let searchType = 'simple';
				
				if(response.search_params.hasOwnProperty('isAdvanceSearch')){
					if(response.search_params.isAdvanceSearch == 'true'){
						searchType = 'advanced'
					}
				}

				if(response.search_params.hasOwnProperty('params')){
					if( searchType == 'simple' ) {
						window.smart_manager.simpleSearchText = response.search_params.params;
						window.smart_manager.advancedSearchQuery = new Array();
						jQuery('#search_switch').prop('checked', false);
					} else {
						window.smart_manager.simpleSearchText = '';
						window.smart_manager.advancedSearchQuery = response.search_params.params;

						// code to update the advanced seach rule count
						window.smart_manager.advancedSearchRuleCount = 0;
						if(window.smart_manager.advancedSearchQuery.length > 0){
							if(Object.keys(window.smart_manager.advancedSearchQuery[0]).length > 0){
								let rules = (window.smart_manager.advancedSearchQuery[0].hasOwnProperty('rules')) ? window.smart_manager.advancedSearchQuery[0].rules : []
								if(rules.length > 0){
									rules.map((s)=>{
										window.smart_manager.advancedSearchRuleCount += s.rules.length
									})
								}
							}
						}
						jQuery('#search_switch').prop('checked', true);
					}
				}
				window.smart_manager.clearSearchOnSwitch = false;
				window.smart_manager.searchType = ( searchType == 'simple' ) ? 'advanced' : 'simple';
				let el = '#search_switch';
				jQuery(el).attr('switchSearchType', searchType);
				jQuery(el).trigger('change'); //Code to re-draw the search content based on search type
				if( searchType == 'simple' ) {
					jQuery('#sm_simple_search_box').val(window.smart_manager.simpleSearchText);
				}
				window.smart_manager.clearSearchOnSwitch = true;
			}
		}

		if( window.smart_manager.searchType != 'simple' && !response.hasOwnProperty('search_params') ) {
			window.smart_manager.initialize_advanced_search(); //initialize advanced search control
		}

		jQuery('#sm_editor_grid').trigger( 'smart_manager_post_load_grid' ); //custom trigger
	}
}

Smart_Manager.prototype.getViewSlug = function(title = '') {
	return Object.keys(window.smart_manager.sm_views).find(key => window.smart_manager.sm_views[key] === title);
}

Smart_Manager.prototype.displayShowHideColumnSettings = function (isShow = true) {
    (isShow) ? jQuery('#show_hide_cols_sm_editor_grid').show() : jQuery('#show_hide_cols_sm_editor_grid').hide();
}

Smart_Manager.prototype.getDashboardModel = function () {
	window.smart_manager.currentDashboardModel = '';

	// Ajax request to get the dashboard model
	let params = {};
		params.data_type = 'json';
		params.hideLoader = false;
		params.data = {
						cmd: 'get_dashboard_model',
						security: window.smart_manager.sm_nonce,
						active_module: window.smart_manager.dashboard_key,
						is_public: ( window.smart_manager.sm_dashboards_public.indexOf(window.smart_manager.dashboard_key) != -1 ) ? 1 : 0,
						active_module_title: window.smart_manager.dashboardName
					};

		// Code for passing extra param for taxonomy & view handling
		if( window.smart_manager.sm_beta_pro == 1 ) {
			let viewSlug = window.smart_manager.getViewSlug(window.smart_manager.dashboardName);
			params.data['is_view'] = 0;

			if(viewSlug) {
				params.data['is_view'] = 1;
				params.data['active_view'] = viewSlug;
				params.data['active_module'] = (window.smart_manager.viewPostTypes.hasOwnProperty(viewSlug)) ? window.smart_manager.viewPostTypes[viewSlug] : window.smart_manager.dashboard_key;	
				window.smart_manager.isViewAuthor(viewSlug);
			}

			// Flag for handling taxonomy dashboards
			params.data['is_taxonomy'] = window.smart_manager.isTaxonomyDashboard();
		}

	window.smart_manager.send_request(params, window.smart_manager.setDashboardModel);
}

Smart_Manager.prototype.set_data = function(response) {
	if( typeof response != 'undefined' && response != '' ) {
		let res = {};

		if( response != 'null' && window.smart_manager.isJSON( response ) ) {
			res = JSON.parse(response);

			window.smart_manager.totalRecords = parseInt(res.total_count);
			window.smart_manager.displayTotalRecords = ( res.hasOwnProperty('display_total_count') ) ? res.display_total_count : res.total_count;

			// re-initialize the loadedTotalRecords
			if( window.smart_manager.page == 1 ) {
				window.smart_manager.loadedTotalRecords = 0
			}

			let loadedRecordCount = (res.hasOwnProperty('loaded_total_count')) ? parseInt(res.loaded_total_count) : res.items.length
			window.smart_manager.loadedTotalRecords += loadedRecordCount
			
			if( window.smart_manager.page > 1 ) {
			
				window.smart_manager.showLoader(false);

				let lastRowIndex = window.smart_manager.currentDashboardData.length;

				let idsIndex = {};
				let idKey = window.smart_manager.getKeyID()
				window.smart_manager.currentDashboardData.map((obj, key) => {
					let id = (obj[idKey]) ? obj[idKey] : ''
					if(id != ''){
						idsIndex[id] = key
					}
				})

				//if no matchingids then replace else push/concat
				if(Object.keys(idsIndex).length > 0) {
					res.items.map((data, key) => {
						let id = (data[idKey]) ? data[idKey] : ''
						if(idsIndex[id]){
							window.smart_manager.currentDashboardData[idsIndex[id]] = data;
						} else {
							window.smart_manager.currentDashboardData.push(data)
						}
					})
				} else {
					window.smart_manager.currentDashboardData = window.smart_manager.currentDashboardData.concat(res.items)
				}
				
				window.smart_manager.hot.forceFullRender = false
				window.smart_manager.hot.loadData(window.smart_manager.currentDashboardData, false);
				
				if( window.smart_manager.sm_beta_pro == 0 ) {
					if( typeof( window.smart_manager.modifiedRows ) != 'undefined' ) {
						if( window.smart_manager.modifiedRows.length >= window.smart_manager.sm_updated_successful ) {
							//call to function for highlighting selected row ids
							if( typeof( window.smart_manager.disableSelectedRows ) !== "undefined" && typeof( window.smart_manager.disableSelectedRows ) === "function" ) {
								window.smart_manager.disableSelectedRows(true);
							}
						}
					}
				}
			} else {
				window.smart_manager.currentDashboardData = ( window.smart_manager.totalRecords > 0 ) ? res.items : [];
			}
		} else {
			window.smart_manager.currentDashboardData = [];
		}

		if( window.smart_manager.page == 1 ) {
			if( window.smart_manager.columnSort ) {
				window.smart_manager.hot.loadData(window.smart_manager.currentDashboardData);
				window.smart_manager.hot.scrollViewportTo(0, 0);
			} else {

				jQuery('#sm_dashboard_kpi').remove();

				if( res.hasOwnProperty('kpi_data') ) {
					window.smart_manager.kpiData = res.kpi_data;
					if( Object.entries(window.smart_manager.kpiData).length > 0 ) {
						let kpi_html = new Array();
				
						Object.entries(window.smart_manager.kpiData).forEach(([kpiTitle, kpiObj]) => {
							kpi_html.push('<span class="sm_beta_select_'+ ( ( kpiObj.hasOwnProperty('color') !== false && kpiObj['color'] != '' ) ? kpiObj['color'] : 'grey' ) +'"> '+ kpiTitle +'('+ ( ( kpiObj.hasOwnProperty('count') !== false ) ? kpiObj['count'] : 0 ) +') </span>');
						});

						if( kpi_html.length > 0 ) {
							jQuery('#sm_bottom_bar_left').append('<div id="sm_dashboard_kpi">'+ kpi_html.join("<span class='sm_separator'> | </span>") +'</div>' );
						}
					}
				} else {
					window.smart_manager.kpiData = {};
				}

				if(window.smart_manager.currentVisibleColumns.length > 0){
					if(window.smart_manager.isColumnModelUpdated){
						window.smart_manager.formatGridColumns();
						
						window.smart_manager.hot.updateSettings({
							data: window.smart_manager.currentDashboardData,
							columns: window.smart_manager.currentVisibleColumns,
							colHeaders: window.smart_manager.column_names,
							// forceRender: window.smart_manager.firstLoad
						})	
					} else {
						window.smart_manager.hot.updateSettings({
							data: window.smart_manager.currentDashboardData,
							forceRender: window.smart_manager.firstLoad
						})
					}
					if(window.smart_manager.firstLoad){
						window.smart_manager.firstLoad = false
					}
				}
				window.smart_manager.showLoader(false);
			}
		}

		window.smart_manager.refreshBottomBar();

		if(window.smart_manager.totalRecords == 0){
			jQuery('#sm_editor_grid_load_items').attr('disabled','disabled');
				jQuery('#sm_editor_grid_load_items').addClass('sm-ui-state-disabled');
	
				jQuery('#sm_bottom_bar_right #sm_beta_display_records').hide();
				jQuery('#sm_bottom_bar_right #sm_beta_load_more_records').text(sprintf(_x('No %s Found', 'bottom bar status', 'smart-manager-for-wp-e-commerce'), window.smart_manager.dashboardDisplayName));
		} else {
			if( window.smart_manager.currentDashboardData.length >= window.smart_manager.totalRecords ) {
				jQuery('#sm_editor_grid_load_items').attr('disabled','disabled');
				jQuery('#sm_editor_grid_load_items').addClass('sm-ui-state-disabled');
	
				jQuery('#sm_bottom_bar_right #sm_beta_display_records').hide();
				jQuery('#sm_bottom_bar_right #sm_beta_load_more_records').text(sprintf(_x('%d %s loaded', 'bottom bar status', 'smart-manager-for-wp-e-commerce'), window.smart_manager.displayTotalRecords, window.smart_manager.dashboardDisplayName));
	
			} else {
				jQuery('#sm_bottom_bar_right #sm_beta_display_records').show();
				jQuery('#sm_editor_grid_load_items').removeAttr('disabled');
				jQuery('#sm_editor_grid_load_items').removeClass('sm-ui-state-disabled');
				jQuery('#sm_bottom_bar_right #sm_beta_load_more_records').html(window.smart_manager.loadMoreBtnHtml);
				jQuery('#sm_bottom_bar_right #sm_editor_grid_load_items span').html(window.smart_manager.dashboardDisplayName);
			}
			jQuery('#sm_bottom_bar_right').show();
		}

		window.smart_manager.gettingData = 0;
	}
}

//Function to refresh the bottom bar of grid
Smart_Manager.prototype.refreshBottomBar = function() {
	let msg = ( window.smart_manager.currentDashboardData.length > 0 ) ? sprintf(_x('%d of %d %s loaded', 'bottom bar status', 'smart-manager-for-wp-e-commerce'), window.smart_manager.loadedTotalRecords, window.smart_manager.displayTotalRecords, window.smart_manager.dashboardDisplayName) : sprintf(_x('No %s Found', 'bottom bar status', 'smart-manager-for-wp-e-commerce'), window.smart_manager.dashboardDisplayName);
	jQuery('#sm_bottom_bar_right #sm_beta_display_records').html(msg);
}


Smart_Manager.prototype.getDataDefaultParams = function(params) {

	let defaultParams = {};
		defaultParams.data = {
						  cmd: 'get_data_model',
						  active_module: window.smart_manager.dashboard_key,
						  security: window.smart_manager.sm_nonce,
						  is_public: ( window.smart_manager.sm_dashboards_public.indexOf(window.smart_manager.dashboard_key) != -1 ) ? 1 : 0,
						  sm_page: window.smart_manager.page,
						  sm_limit: window.smart_manager.limit,
						  SM_IS_WOO30: window.smart_manager.sm_is_woo30,
						  sort_params: (window.smart_manager.currentDashboardModel.hasOwnProperty('sort_params') ) ? window.smart_manager.currentDashboardModel.sort_params : '',
						  table_model: (window.smart_manager.currentDashboardModel.hasOwnProperty('tables') ) ? window.smart_manager.currentDashboardModel.tables : '',
						  search_text: (window.smart_manager.searchType == 'simple') ? window.smart_manager.simpleSearchText : '',
						  advanced_search_query: JSON.stringify((window.smart_manager.searchType != 'simple') ? window.smart_manager.advancedSearchQuery : [])
					  };

	// Code for passing extra param for view handling
	if( window.smart_manager.sm_beta_pro == 1 ) {
		let viewSlug = window.smart_manager.getViewSlug(window.smart_manager.dashboardName);
		defaultParams.data['is_view'] = 0;

		if(viewSlug){
			defaultParams.data['is_view'] = 1;
			defaultParams.data['active_view'] = viewSlug;
			defaultParams.data['active_module'] = (window.smart_manager.viewPostTypes.hasOwnProperty(viewSlug)) ? window.smart_manager.viewPostTypes[viewSlug] : window.smart_manager.dashboard_key;
		}
	}

	if( typeof params != 'undefined' ) {
		if( Object.getOwnPropertyNames(params).length > 0 ) {
			let paramsData = (params.hasOwnProperty('data')) ? params.data : {}
			if( Object.getOwnPropertyNames(paramsData).length > 0 ) {
				defaultParams = Object.assign(paramsData, defaultParams.data);
			}
			defaultParams = Object.assign(params, defaultParams);    
		}    
	}

	window.smart_manager.currentGetDataParams = defaultParams;
}

Smart_Manager.prototype.getData = function(params = {}) {

	window.smart_manager.gettingData = 1;

	if( window.smart_manager.page == 1 ) {
		if ( typeof (window.smart_manager.getDataDefaultParams) !== "undefined" && typeof (window.smart_manager.getDataDefaultParams) === "function" ) {
			window.smart_manager.getDataDefaultParams(params);
			window.smart_manager.currentGetDataParams.hideLoader = false
		}
	} else {
		if( typeof(window.smart_manager.currentGetDataParams.data) != 'undefined' && typeof(window.smart_manager.currentGetDataParams.data.sm_page) != 'undefined' ) {
			
			if(params.hasOwnProperty('refreshPage')){
				window.smart_manager.currentGetDataParams.data.sm_page = params.refreshPage;
				window.smart_manager.currentGetDataParams.async = false;
			} else {
				window.smart_manager.currentGetDataParams.data.sm_page = window.smart_manager.page;
			}
			window.smart_manager.currentGetDataParams.data.sort_params = window.smart_manager.currentDashboardModel.sort_params;
		}
	}

	window.smart_manager.send_request(window.smart_manager.currentGetDataParams, window.smart_manager.set_data);
}

Smart_Manager.prototype.inline_edit_dlg = function(params) {
		if (params.dlg_width == '' || typeof (params.dlg_width) == 'undefined') {
			modal_width = 350;
		} else {
			modal_width = params.dlg_width;
		}

		if (params.dlg_height == '' || typeof (params.dlg_height) == 'undefined') {
			modal_height = 390;
		} else {
			modal_height = params.dlg_height;
		}

		let ok_btn = [{
					  text: _x("OK", 'button', 'smart-manager-for-wp-e-commerce'),
					  class: 'sm_inline_dialog_ok sm-dlg-btn-yes',
					  click: function() {
						jQuery( this ).dialog( "close" );
					  }
					}];

		jQuery( "#sm_inline_dialog" ).html(params.content);

		let dialog_params = {
								closeOnEscape: true,
								draggable: false,
								dialogClass: 'sm_ui_dialog_class',
								height: modal_height,
								width: modal_width,
								modal: (params.hasOwnProperty('modal')) ? params.modal : false,
								position: {my: ( params.hasOwnProperty('position_my') ) ? params.position_my : 'left center+250px',
											at: ( params.hasOwnProperty('position_my') ) ? params.position_at : 'left center', 
											of: params.target},
								create: function (event, ui) {
									if( !(params.hasOwnProperty('title') && params.title != '') ) {
										jQuery(".ui-widget-header").hide();
									}
								},
								open: function() {

									if( params.hasOwnProperty('show_close_icon') && params.show_close_icon === false ) {
										jQuery(this).find('.ui-dialog-titlebar-close').hide();
									}

									jQuery('.ui-widget-overlay').bind('click', function() { 
									    jQuery('#sm_inline_dialog').dialog('close'); 
									});

									if( !(params.hasOwnProperty('title') && params.title != '') ) {
										jQuery(".ui-widget-header").hide();
									} else if( (params.hasOwnProperty('title') && params.title != '') ) {
										jQuery(".ui-widget-header").show();
									}

									if( params.hasOwnProperty('customDataAttributes') ) {
										Object.entries(params.customDataAttributes).forEach(([key, value]) => {
											jQuery(this).attr(key, value);											
										});
									}

									jQuery(this).html(params.content);
								},
								close: function(event, ui) { 
									jQuery(this).dialog('close');
								},
							  buttons: ( params.hasOwnProperty('display_buttons') && params.display_buttons === false ) ? [] : ( params.hasOwnProperty('buttons_model') ? params.buttons_model : ok_btn )
							}

		if( params.hasOwnProperty('title') ) {
			dialog_params.title = params.title;
		}

		if( params.hasOwnProperty('titleIsHtml') ) {
			dialog_params.titleIsHtml = params.titleIsHtml;
		}
		
		jQuery( "#sm_inline_dialog" ).dialog(dialog_params);
		jQuery('.sm_ui_dialog_class, .ui-widget-overlay').show();
}

Smart_Manager.prototype.getTextWidth = function (text, font) {
    // re-use canvas object for better performance
    let canvas = window.smart_manager.getTextWidthCanvas || (window.smart_manager.getTextWidthCanvas = document.createElement("canvas"));
    let context = canvas.getContext("2d");
    context.font = font;
    let metrics = context.measureText(text);
    return metrics.width;
}

Smart_Manager.prototype.formatGridColumns = function () {
	if(Array.isArray(window.smart_manager.currentVisibleColumns) && window.smart_manager.currentVisibleColumns.length > 0){
		window.smart_manager.currentVisibleColumns.map((c, i) => {
			let colWidth = c.width || 0;
			let header_text = window.smart_manager.column_names[i],
				font = '30px Arial';
				// font = '26px ' + window.smart_manager.body_font_family;

			let newWidth = window.smart_manager.getTextWidth(header_text,font);

			if( newWidth > colWidth && !c.width ) {
				c.width = ( newWidth < 250 ) ? newWidth : 250;
			}
			c.width = Math.round(parseInt(c.width))
			window.smart_manager.currentVisibleColumns[i] = c
		})
	}
}

Smart_Manager.prototype.enableDisableButtons = function() {
	//enabling the action buttons
	if( window.smart_manager.selectedRows.length > 0 || window.smart_manager.selectAll ) {
		if( jQuery('.sm_top_bar_action_btns #del_sm_editor_grid svg').hasClass('sm-ui-state-disabled') ) {
			jQuery('.sm_top_bar_action_btns #del_sm_editor_grid svg').removeClass('sm-ui-state-disabled');
		}

		if( jQuery('.sm_top_bar_action_btns #print_invoice_sm_editor_grid_btn svg').hasClass('sm-ui-state-disabled') ) {
			jQuery('.sm_top_bar_action_btns #print_invoice_sm_editor_grid_btn svg').removeClass('sm-ui-state-disabled');
		}

	} else {
		if( !jQuery('.sm_top_bar_action_btns #del_sm_editor_grid svg').hasClass('sm-ui-state-disabled') ) {
			jQuery('.sm_top_bar_action_btns #del_sm_editor_grid svg').addClass('sm-ui-state-disabled');
		}

		if( !jQuery('.sm_top_bar_action_btns #print_invoice_sm_editor_grid_btn svg').hasClass('sm-ui-state-disabled') ) {
			jQuery('.sm_top_bar_action_btns #print_invoice_sm_editor_grid_btn svg').addClass('sm-ui-state-disabled');
		}
	}
}


Smart_Manager.prototype.disableSelectedRows = function( readonly ) {

	for (let i = 0; i < window.smart_manager.hot.countRows(); i++) {

		if( window.smart_manager.modifiedRows.indexOf(i) != -1 ) {
			continue;
		}

		for (let j = 0; j < window.smart_manager.hot.countCols(); j++) {
			window.smart_manager.hot.setCellMeta( i, j, 'readOnly', readonly );
		}
	}

}

//Function to highlight the edited cells
Smart_Manager.prototype.highlightEditedCells = function() {

	if( typeof window.smart_manager.dirtyRowColIds == 'undefined' || Object.getOwnPropertyNames(window.smart_manager.dirtyRowColIds).length == 0 ) {
		return;
	}

	for( let row in window.smart_manager.dirtyRowColIds ) {

		window.smart_manager.dirtyRowColIds[row].forEach(function(colIndex) {
			
			cellProp = window.smart_manager.hot.getCellMeta(row, colIndex);
			prevClassName = cellProp.className;

			if( prevClassName == '' || typeof prevClassName == 'undefined' || ( typeof(prevClassName) != 'undefined' && prevClassName.indexOf('sm-grid-dirty-cell') == -1 ) ) {
				window.smart_manager.hot.setCellMeta(row, colIndex, 'className', (prevClassName + ' ' + 'sm-grid-dirty-cell'));
				jQuery('.smCheckboxColumnModel input[data-row='+row+']').parents('tr').removeClass('sm_edited').addClass('sm_edited');
			}
		});
	}
}

Smart_Manager.prototype.isHTML = RegExp.prototype.test.bind(/(<([^>]+)>)/i);

Smart_Manager.prototype.isJSON = function(str) {
    try {
        return (JSON.parse(str) && !!str);
    } catch (e) {
        return false;
    }
}


Smart_Manager.prototype.getCustomRenderer = function ( col ) {
  
	let customRenderer = '';

	let colObj = window.smart_manager.currentVisibleColumns[col];

	if( typeof( colObj ) != 'undefined' ) {

		let renderer = ( colObj.hasOwnProperty('renderer') ) ? colObj.renderer : '';

		if( colObj.hasOwnProperty('type') ) {
			if( colObj.type == 'numeric' ) {
				customRenderer = 'numericRenderer';
			} else if( colObj.type == 'text' && renderer != 'html' ) {
				customRenderer = 'customTextRenderer';
			} else if( colObj.type == 'html' || renderer == 'html' ) {
				customRenderer = 'customHtmlRenderer';
			} else if( colObj.type == 'checkbox' ) {
				// customRenderer = 'customCheckboxRenderer';
			} else if( colObj.type == 'password' ) {
				customRenderer = 'customPasswordRenderer';
			}
		}
	}

	return customRenderer;
}


Smart_Manager.prototype.generateImageGalleryDlgHtml = function( imageObj ) {
let html = '';

	if( typeof( imageObj ) !== "undefined" ) {
		Object.entries(imageObj).forEach(([id, imageUrl]) => {
			html += '<div class="sm_beta_left sm_gallery_image">'+
						'<img data-id="'+ imageUrl.id +'" src="'+ imageUrl.val +'" width="150" height="150"></img>'+
						'<div style="text-align:center;"> <span class="dashicons dashicons-trash sm_beta_select_red sm_gallery_image_delete" title="'+_x('Remove gallery image', 'tooltip', 'smart-manager-for-wp-e-commerce')+'"> </div>'+
					'</div>';

		});	
	}

	return html;
}

Smart_Manager.prototype.handleMediaUpdate = function( params ) {
	
	let file_frame;
					
	// If the media frame already exists, reopen it.
	if ( file_frame ) {
	  file_frame.open();
	  return;
	}

	let allowMultiple = ( params.hasOwnProperty('allowMultiple') ) ? params.allowMultiple : false;
	
	// Code for attaching media to the posts
	wp.media.model.settings.post.id = 0
	if('posts_id' === window.smart_manager.getKeyID() && params.hasOwnProperty('row_data_id')){
		wp.media.model.settings.post.id = params.row_data_id
	}

	// Create the media frame.
	file_frame = wp.media.frames.file_frame = wp.media({
	  title: ( params.hasOwnProperty('uploaderTitle') ) ? params.uploaderTitle : jQuery( this ).data( 'uploader_title' ),
	  button: {
		text: ( params.hasOwnProperty('uploader_button_text') ) ? params.uploaderButtonText : jQuery( this ).data( 'uploader_button_text' ),
	  },
	  library: {
	    type: 'image'
	  },
	  multiple: allowMultiple  // Set to true to allow multiple files to be selected
	});

	if( params.hasOwnProperty('callback') ) {
		file_frame.on( 'select', function() {

			let attachments = ( allowMultiple ) ? file_frame.state().get('selection').toJSON() : file_frame.state().get('selection').first().toJSON();
			params.callback( attachments )
		});
	}

	file_frame.open();

}

Smart_Manager.prototype.inlineUpdateMultipleImages = function( galleryImages ) {
	let params = {};
	params.data = {
					cmd: 'inline_update',
					active_module: window.smart_manager.dashboard_key,
					edited_data: JSON.stringify(galleryImages),
					security: window.smart_manager.sm_nonce,
					pro: ( ( typeof(window.smart_manager.sm_beta_pro) != 'undefined' ) ? window.smart_manager.sm_beta_pro : 0 ),
					table_model: (window.smart_manager.currentDashboardModel.hasOwnProperty('tables') ) ? window.smart_manager.currentDashboardModel.tables : ''
				};

	window.smart_manager.send_request(params, function(response) {
		window.smart_manager.refresh();
	});
};



Smart_Manager.prototype.showImagePreview = function(params) {
	let xOffset = 150,
		yOffset = 30;

	if( jQuery('#sm_img_preview').length == 0 ) {
		jQuery("body").append("<div id='sm_img_preview' style='z-index:100199;'><div style='margin: 1em; padding: 1em; border-radius: 0.1em; border: 0.1em solid #ece0e0;'><img src='" + params.current_cell_value + "' width='300' /></div><div id='sm_img_preview_text'>"+ params.title +"</div></div>");
	}

	jQuery("#sm_img_preview")
    	.css("top", (params.event.pageY - xOffset) + "px")
    	.css("left", (params.event.pageX + yOffset) + "px")
    	.fadeIn("fast")
    	.show();
}

Smart_Manager.prototype.loadGrid = function() {
	jQuery('#sm_editor_grid').html('');
	window.smart_manager.formatGridColumns();
	window.smart_manager.hot = new Handsontable(window.smart_manager.container, {
																				  data: window.smart_manager.currentDashboardData,
																				  height: window.smart_manager.grid_height,
																				  width: window.smart_manager.grid_width,
																				//   allowEmpty: true, // default is true
																				  rowHeaders: function(index) {
																					return '<input type="checkbox" />';
																				  }, // for row headings (like numbering)
																				  colHeaders: true, // for col headings
																				//   renderAllRows: true,
																				//   viewportRowRenderingOffset: 100, // -- problem no. of rows outside the visible part of table. Default: auto
																				  stretchH: 'all', // strech 
																				  autoColumnSize: {useHeaders: true},
																				//   wordWrap: true, //default is true
																				//   autoRowSize: false, // by default its undefined which is also same
																				  rowHeights: window.smart_manager.rowHeight,
																				  colWidths: 100,
																				  bindRowsWithHeaders: true,
																				  manualColumnResize: true,
																				//   manualRowResize: true,
																				  manualColumnMove: false,
																				  columnSorting: true,
																				//   columnSorting: { sortEmptyCells: false }, //--problem
																				//   fillHandle: 'vertical', //for excel like filling of cells
																				fillHandle: { //for excel like filling of cells
																					direction: 'vertical',
																					autoInsertRow: false // For restricting to add new rows automatically when dragging the cells
																				}, 
																				  persistentState: true,
																				  customBorders: true,
																				//   disableVisualSelection: true,
																				  columns: window.smart_manager.currentVisibleColumns,
																				  colHeaders: window.smart_manager.column_names, 
																				});

	window.smart_manager.hotPlugin.columnSortPlugin = window.smart_manager.hot.getPlugin('columnSorting');
	window.smart_manager.hotPlugin.manualColumnResizePlugin = window.smart_manager.hot.getPlugin('manualColumnResize')

	//Code to have title for each of the column headers
	jQuery('table.htCore').find('.colHeader').each(function() {
		jQuery(this).attr('title',jQuery(this).text()+' '+_x('(Click to sort)', 'tooltip', 'smart-manager-for-wp-e-commerce'));
	});
	
	window.smart_manager.hot.updateSettings({

		cells: function(row, col, prop) {
			
			let customRenderer = window.smart_manager.getCustomRenderer( col );

			if( customRenderer != '' ) {
				let cellProperties = {};
				cellProperties.renderer = customRenderer;
				return cellProperties;						
			}
			
		},

		afterOnCellMouseOver: function(e, coords, td) {
			if( coords.row < 0 || coords.col < 0 ) {
				return;
			}

			let col = this.getCellMeta(coords.row, coords.col),
				current_cell_value = this.getDataAtCell(coords.row, coords.col);
			if( typeof(col.type) != 'undefined' && current_cell_value ) {
				if( col.type == 'sm.image' ) {
					let row_title = '';
					if( window.smart_manager.dashboard_key == 'product' ) {
						row_title = this.getDataAtRowProp(coords.row, 'posts_post_title');
						row_title = ( window.smart_manager.isHTML(row_title) == true ) ? jQuery(row_title).text() : row_title;
						row_title = row_title;
					}
					let params = {
									'current_cell_value': current_cell_value,
									'event': e,
									'title': row_title
								};

					if( typeof( window.smart_manager.showImagePreview ) !== "undefined" && typeof( window.smart_manager.showImagePreview ) === "function" ) {
						window.smart_manager.showImagePreview(params);
					}					
				}
			}
		},

		afterOnCellMouseOut: function(e, coords, td) {
			if( jQuery('#sm_img_preview').length > 0 ) {
				jQuery('#sm_img_preview').remove();
			}
		},

		afterRender: function( isForced ) { //TODO: check
			if( isForced === true ) {
				window.smart_manager.showLoader(false);
			}
		},

		beforeColumnSort: function(currentSortConfig, destinationSortConfigs) {
		  	window.smart_manager.hotPlugin.columnSortPlugin.setSortConfig(destinationSortConfigs);
		  	if( typeof(destinationSortConfigs) != 'undefined' ) {
		  		if( destinationSortConfigs.length > 0 ) {
		  			if( destinationSortConfigs[0].hasOwnProperty('column') ) {
			  			if( window.smart_manager.currentVisibleColumns.length > 0 ) {
			  				let colObj = window.smart_manager.currentVisibleColumns[destinationSortConfigs[0].column];

			  				window.smart_manager.currentDashboardModel.sort_params = { 'column': colObj.src,
											'sortOrder': destinationSortConfigs[0].sortOrder };

			  				window.smart_manager.columnSort = true;
			  			}
			  		}	
		  		} else {
		  			if( window.smart_manager.currentDashboardModel.hasOwnProperty('sort_params') ) {
		  				window.smart_manager.currentDashboardModel.sort_params = Object.assign({}, window.smart_manager.defaultSortParams);
		  			}
		  			window.smart_manager.columnSort = false;
		  		}

		  		window.smart_manager.page = 1;
		  		window.smart_manager.getData();
		  	}
		  	return false; // The blockade for the default sort action.
		},

		afterCreateRow: function (row, amount) {
			
			while( amount > 0 ) {
				// setTimeout( function() { //added for handling dirty class for edited cells

					let idKey = window.smart_manager.getKeyID();
					let row_data_id = window.smart_manager.hot.getDataAtRowProp(row, idKey);

					if( typeof(row_data_id) != 'undefined' && row_data_id ) {
						return;
					}

					window.smart_manager.addRecords_count++;
					window.smart_manager.hot.setDataAtRowProp(row,idKey,'sm_temp_'+window.smart_manager.addRecords_count);

					let val = '',
						colObj = {};

					for( let key in window.smart_manager.currentColModel ) {

						colObj = window.smart_manager.currentColModel[key];

						if( colObj.hasOwnProperty('data') ) {
							if( jQuery.inArray(colObj.data, window.smart_manager.defaultColumnsAddRow) >= 0 ) {

								if( typeof colObj.defaultValue != 'undefined' ) {
									val = colObj.defaultValue;
								} else {
									if( typeof colObj.selectOptions != 'undefined' ) {
										val = Object.keys(colObj.selectOptions)[0]
									} else {
										val = 'test';
									}
								}

								window.smart_manager.hot.setDataAtRowProp(row, colObj.data, val);
							}
						}
					}
				// }, 1 );
				row++;
				amount--;
			}
		},

		afterChange: function(changes, source) {

			if( window.smart_manager.selectAll === true || changes === null ) {
				return;
			}

			let col = {},
				cellProp = {},
				colIndex = '',
				idKey = window.smart_manager.getKeyID(),
				colTypesDisabledHiglight = new Array('sm.image');

			changes.forEach(([row, prop, oldValue, newValue]) => {
				if( ( row < 0 && prop == 0 ) || (oldValue == newValue && String(oldValue).length == String(newValue).length) ) {
					return;
				}

				if( window.smart_manager.modifiedRows.indexOf(row) == -1 ) {
					window.smart_manager.modifiedRows.push(row);
				}
				
				colIndex = window.smart_manager.hot.propToCol(prop);
				if( typeof(colIndex) == 'number' ) {
					col = window.smart_manager.hot.getCellMeta(row, colIndex);
				}

				let id = window.smart_manager.hot.getDataAtRowProp(row, idKey);

				if( (oldValue != newValue || String(oldValue).length != String(newValue).length) && prop != idKey && colTypesDisabledHiglight.indexOf(col.type) == -1 ) { //for inline edit
					cellProp = window.smart_manager.hot.getCellMeta(row, prop);
					prevClassName = ( typeof(cellProp.className) != 'undefined' ) ? cellProp.className : '';

					//dirty cells variable
					if( window.smart_manager.dirtyRowColIds.hasOwnProperty(row) === false ) {
						window.smart_manager.dirtyRowColIds[row] = new Array();
					}

					if( window.smart_manager.dirtyRowColIds[row].indexOf(colIndex) == -1 ) {
						window.smart_manager.dirtyRowColIds[row].push(colIndex);
					}

					if( jQuery('.sm_top_bar_action_btns #save_sm_editor_grid_btn svg').hasClass('sm-ui-state-disabled') ) {
						jQuery('.sm_top_bar_action_btns #save_sm_editor_grid_btn svg').removeClass('sm-ui-state-disabled');
					}

					if( prevClassName == '' || ( typeof(prevClassName) != 'undefined' && prevClassName.indexOf('sm-grid-dirty-cell') == -1 ) ) {

						//creating the edited json string

						if( window.smart_manager.editedData.hasOwnProperty(id) === false ) {
							window.smart_manager.editedData[id] = {};
						}

						if( Object.entries(col).length === 0 ) {
							if( typeof( window.smart_manager.currentColModel ) != 'undefined' ) {
								window.smart_manager.currentColModel.forEach(function(value) {
									if( value.hasOwnProperty('data') && value.data == prop ) {
										col.src = value.src;
									}	
								});
							}
						}
						window.smart_manager.editedData[id][col.src] = (window.smart_manager.editedAttribueSlugs && (false === (window.smart_manager.excludedEditedFieldKeys).includes(col.src))) ? window.smart_manager.editedAttribueSlugs : newValue;
						window.smart_manager.editedCellIds.push({'row': row, 'col':colIndex});
					}

					if( window.smart_manager.sm_beta_pro == 0 ) {
						if( typeof( window.smart_manager.modifiedRows ) != 'undefined' ) {
							if( window.smart_manager.modifiedRows.length >= window.smart_manager.sm_updated_successful ) {
								//call to function for highlighting selected row ids
								if( typeof( window.smart_manager.disableSelectedRows ) !== "undefined" && typeof( window.smart_manager.disableSelectedRows ) === "function" ) {
									window.smart_manager.disableSelectedRows(true);
								}
							}
						}
					}
				}
			});

			//call to function for highlighting edited cell ids
			if( typeof( window.smart_manager.highlightEditedCells ) !== "undefined" && typeof( window.smart_manager.highlightEditedCells ) === "function" ) {
				window.smart_manager.highlightEditedCells();
			}

			window.smart_manager.hot.render();
		},

		afterOnCellMouseUp: function (e, coords, td) {
			window.smart_manager.editedAttribueSlugs = '';
			window.smart_manager.selectAll = false
			
			//Code for having checkbox column selection
			if(coords.col === -1){

				//code for handling header checkbox selection
				if(window.smart_manager.hot){
					if(window.smart_manager.hot.selection){
						if(window.smart_manager.hot.selection.highlight){
							if(window.smart_manager.hot.selection.highlight.selectAll){
								window.smart_manager.selectAll = true			
							}
							if(window.smart_manager.hot.selection.highlight.selectedRows){
								window.smart_manager.selectedRows = window.smart_manager.hot.selection.highlight.selectedRows
							}
						}
					}	
				}

				if( typeof( window.smart_manager.enableDisableButtons ) !== "undefined" && typeof( window.smart_manager.enableDisableButtons ) === "function" ) {
					window.smart_manager.enableDisableButtons();
				}
				return;
			}

			let col = this.getCellMeta(coords.row, coords.col);
			if( typeof(col.readOnly) != 'undefined' && col.readOnly == 'true' ) {
				return;
			}
			
			let id_key = window.smart_manager.getKeyID(),
				row_data_id = this.getDataAtRowProp(coords.row, id_key),
				current_cell_value = this.getDataAtCell(coords.row, coords.col),
				params = {'coords': coords,
						'td': td, 
						'colObj': col, 
						'row_data_id': row_data_id, 
						'current_cell_value': current_cell_value};
			
			window.smart_manager.defaultEditor = true;
			jQuery('#sm_editor_grid').trigger('sm_grid_on_afterOnCellMouseUp',[params]);	
			if( window.smart_manager.hasOwnProperty('defaultEditor') && window.smart_manager.defaultEditor === false ) {
				return;
			}

			if( typeof (col.type) != 'undefined' && col.type == 'sm.multipleImage' ) { // code to handle the functionality to handle editing of 'image' data types
			let galleryImages = current_cell_value,
				imageGalleryHtml = `<div class="sm_gallery_image_parent" data-id="${row_data_id}" data-col="${col.src || ''}">`;

			if( Object.keys( galleryImages ).length > 0 ) {
				if ( typeof (window.smart_manager.generateImageGalleryDlgHtml) !== "undefined" && typeof (window.smart_manager.generateImageGalleryDlgHtml) === "function" ) {
					imageGalleryHtml += window.smart_manager.generateImageGalleryDlgHtml( galleryImages );
				}
			}

			imageGalleryHtml += '</div>';

			if( Object.entries(col).length === 0 ) {
				if( typeof( window.smart_manager.currentColModel ) != 'undefined' ) {
						window.smart_manager.currentColModel.forEach(function(value) {
							if( value.hasOwnProperty('data') && value.data == col.prop ) {
								col.src = value.src;
							}	
						});
					}
				}

				window.smart_manager.modal = {
					title: _x('Gallery Images', 'gallery modal title', 'smart-manager-for-wp-e-commerce'),
					content: imageGalleryHtml,
					autoHide: false,
					cta: {
						title: _x('Add', 'button', 'smart-manager-for-wp-e-commerce'),
						closeModalOnClick: false,
						callback: function() {
							if ( typeof (window.smart_manager.handleMediaUpdate) !== "undefined" && typeof (window.smart_manager.handleMediaUpdate) === "function" ) {

								jQuery('.sm_ui_dialog_class, .ui-widget-overlay').hide();

								let params = {	
												UploaderText: _x('Add images to product gallery', 'button', 'smart-manager-for-wp-e-commerce'),
												UploaderButtonText: _x('Add to gallery', 'button', 'smart-manager-for-wp-e-commerce'),
												allowMultiple: true,
												row_data_id: row_data_id
											};

								
									params.callback = function( attachments ) {

										jQuery('.sm_ui_dialog_class , .ui-widget-overlay').show();

										if( typeof( attachments ) == 'undefined' ) {
											return;
										}

										let imageGalleryHtml = `<div class="sm_gallery_image_parent" data-id="${row_data_id}" data-col="${col.src || ''}">`,
											modifiedGalleryImages = [],
											imageIds = new Set();

										jQuery('.sm_gallery_image').find('img').each( function(){
											modifiedGalleryImages.push({
												id:jQuery(this).data('id'), 
												val:jQuery(this).attr('src')
											  });
											imageIds.add(jQuery(this).data('id'));
										});	
										
										attachments.forEach( function( attachmentObj ) {
											modifiedGalleryImages.push({
												id:attachmentObj.id, 
												val:attachmentObj.sizes.full.url
											  });
											imageIds.add(attachmentObj.id);
										});

										if ( typeof (window.smart_manager.generateImageGalleryDlgHtml) !== "undefined" && typeof (window.smart_manager.generateImageGalleryDlgHtml) === "function" ) {
											imageGalleryHtml += window.smart_manager.generateImageGalleryDlgHtml( modifiedGalleryImages );
										}

										imageGalleryHtml += '</div>';

										jQuery('div.modal-body').html(imageGalleryHtml);

										if ( typeof (window.smart_manager.inlineUpdateMultipleImages) !== "undefined" && typeof (window.smart_manager.inlineUpdateMultipleImages) === "function" ) {
											window.smart_manager.inlineUpdateMultipleImages({[row_data_id]: {[col.src]: [...imageIds].join(',')}});
										}
									}

								window.smart_manager.handleMediaUpdate( params );
							}
						}
					},
				}
				window.smart_manager.showModal()
			}

			if( typeof (col.type) != 'undefined' && col.type == 'sm.image' && coords.row >= 0 ) { // code to handle the functionality to handle editing of 'image' data types

				if ( typeof (window.smart_manager.handleMediaUpdate) !== "undefined" && typeof (window.smart_manager.handleMediaUpdate) === "function" ) {

					let params = {row_data_id: row_data_id};

					// When an image is selected, run a callback.
					params.callback = function( attachment ) {

						if( typeof( attachment ) == 'undefined' ) {
					  		return;
					  	}
						
						let src = col.src;

						let params = {};
							params.data = {
											cmd: 'inline_update',
											active_module: window.smart_manager.dashboard_key,
											edited_data: JSON.stringify({[row_data_id] : {[src]: attachment['id']}}),
											security: window.smart_manager.sm_nonce,
											pro: ( ( typeof(window.smart_manager.sm_beta_pro) != 'undefined' ) ? window.smart_manager.sm_beta_pro : 0 ),
											table_model: (window.smart_manager.currentDashboardModel.hasOwnProperty('tables') ) ? window.smart_manager.currentDashboardModel.tables : ''
										};

						window.smart_manager.send_request(params, function(response) {
							if ( 'failed' !== response ) {
								window.smart_manager.hot.setDataAtCell(coords.row, coords.col, attachment['url'], 'image_inline_update');

								if( window.smart_manager.isJSON( response ) && ( typeof(window.smart_manager.sm_beta_pro) == 'undefined' || ( typeof(window.smart_manager.sm_beta_pro) != 'undefined' && window.smart_manager.sm_beta_pro != 1 ) ) ) {
									response = JSON.parse( response );
									msg = response.msg;

									if( typeof( response.sm_inline_update_count ) != 'undefined' ) {
										if ( typeof (window.smart_manager.updateLitePromoMessage) !== "undefined" && typeof (window.smart_manager.updateLitePromoMessage) === "function" ) {
											window.smart_manager.updateLitePromoMessage( response.sm_inline_update_count );
										}
									}
								} else {
									msg = response;
								}
							}
						});

					};

					window.smart_manager.handleMediaUpdate( params );
				}
			}

			if( typeof (col.type) != 'undefined' && col.type == 'sm.longstring' ) {

				if( typeof(wp.editor.getDefaultSettings) == 'undefined' ) {
					return;
				}

				let unformatted_val = current_cell_value; //Code for unformatting the 'longstring' type values
				let initializeWPEditor = function(){
					wp.editor.remove('sm_beta_lonstring_input');
					wp.editor.initialize('sm_beta_lonstring_input', {tinymce:  { height: 200,
									wpautop:true, 
									plugins : 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview', 
									toolbar1: 'formatselect bold,italic,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,wp_more,|,spellchecker,fullscreen,wp_adv',
									toolbar2: 'underline,justifyfull,forecolor,|,pastetext,pasteword,removeformat,|,media,charmap,|,outdent,indent,|,undo,redo,wp_help'},
					quicktags:  { buttons: 'strong,em,link,block,del,img,ul,ol,li,code,more,spell,close,fullscreen' },
					mediaButtons: true });
				}
				window.smart_manager.modal = {
					title: col.key || '',
					content: '<textarea style="width:100%;height:100%;z-index:100;" id="sm_beta_lonstring_input">'+ unformatted_val +'</textarea>',
					autoHide: false,
					cta: {
						title: _x('Ok', 'button', 'smart-manager-for-wp-e-commerce'),
						callback: function() {
							let content = wp.editor.getContent('sm_beta_lonstring_input');
							window.smart_manager.hot.setDataAtCell(coords.row, coords.col, content, 'sm.longstring_inline_update');
							wp.editor.remove('sm_beta_lonstring_input');
						}
					},
					onCreate: initializeWPEditor,
					onUpdate: initializeWPEditor
				}
				window.smart_manager.showModal()
			}

			if( col.editor == 'sm.serialized' ) { //Code for handling serialized complex data handling

				window.smart_manager.JSONEditorObj = {} // hold JSONEditor instance

				let initializeJSONEditor = function() {
					let container = document.getElementById("sm_beta_json_editor");
					jQuery(container).html('');
					let options = {
									"mode": 'tree',
									"search": true
								};
					window.smart_manager.JSONEditorObj = new JSONEditor(container, options);
					let val = ( window.smart_manager.isJSON(current_cell_value) ) ? JSON.parse(current_cell_value) : current_cell_value;

					if ( col.editor_schema && window.smart_manager.isJSON( col.editor_schema ) ) {
						window.smart_manager.JSONEditorObj.setSchema( JSON.parse( col.editor_schema ) );
					}

					window.smart_manager.JSONEditorObj.set(val);
					window.smart_manager.JSONEditorObj.expandAll();
				}

				window.smart_manager.modal = {
					title: col.key || '',
					content: '<div id="sm_beta_json_editor"></div>',
					autoHide: false,
					cta: {
						title: _x('Ok', 'button', 'smart-manager-for-wp-e-commerce'),
						callback: function() {
							let content = (window.smart_manager.JSONEditorObj) ? JSON.stringify(window.smart_manager.JSONEditorObj.get()) : '';
							window.smart_manager.hot.setDataAtCell(coords.row, coords.col, content, 'sm.serialized_inline_update');
							window.smart_manager.JSONEditorObj = {}
							wp.editor.remove('sm_beta_json_editor')
						}
					},
					onCreate: initializeJSONEditor,
					onUpdate: initializeJSONEditor
				}
				window.smart_manager.showModal()
			}

			if( typeof (col.type) != 'undefined' && col.type == 'sm.multilist' ) { // code to handle the functionality to handle editing of 'multilist' data types
					var actual_value = col.values,
						multiselect_data = new Array(),
						multiselect_chkbox_list = '',
						current_value = new Array();

					if( current_cell_value != '' && typeof(current_cell_value) != 'undefined' && current_cell_value !== null ) {
						current_value = (typeof(current_cell_value) == 'string') ? current_cell_value.split(', ') : new Array(String(current_cell_value));
					}
					for (var index in actual_value) {
						let title = (actual_value[index].hasOwnProperty('title')) ? actual_value[index].title : actual_value[index].term;
						if(0 === parseInt(actual_value[index]['parent'])) {
							if(undefined !== multiselect_data[index]) {
								if(false !== multiselect_data[index].hasOwnProperty('child')) {
									multiselect_data[index].id = index
									multiselect_data[index].term = actual_value[index].term;
									multiselect_data[index].title = title; 
								}
							} else {
								multiselect_data[index] = {'id': index, 
															'term' : actual_value[index].term,
															'title': title
														};    
							}			
						} else {

							if(!actual_value[actual_value[index]['parent']]){
								continue;
							}

							if(undefined === multiselect_data[actual_value[index]['parent']]) {
								//For hierarchical categories
								for (var mindex in multiselect_data) {
									if (false === multiselect_data[mindex].hasOwnProperty('child')) {
										continue;
									}
									for (var cindex in multiselect_data[mindex].child) {
									}
								}
								multiselect_data[actual_value[index]['parent']] = {};
							}
							if(false === multiselect_data[actual_value[index]['parent']].hasOwnProperty('child')) {
								multiselect_data[actual_value[index]['parent']].child = {};
							}
							multiselect_data[actual_value[index]['parent']].term = actual_value[actual_value[index]['parent']].term;
							multiselect_data[actual_value[index]['parent']].child[index] = {term: actual_value[index].term,
																							title: title,
																						};
						}
					}
					multiselect_data.sort(function(a,b){
						return a.term.localeCompare(b.term);
					})
					multiselect_chkbox_list += '<ul>';
					for (let index in multiselect_data) {
						let idStr = (multiselect_data[index].id) ? multiselect_data[index].id.toString() : ''
						let checked = (current_value != '' && (current_value.includes(multiselect_data[index].title) || current_value.includes(idStr))) ? 'checked' : '';
						multiselect_chkbox_list += '<li> <input type="checkbox" name="chk_multiselect" value="'+ multiselect_data[index].id +'" '+ checked +'>  '+ multiselect_data[index].term +'</li>';
						
						if ( false === multiselect_data[index].hasOwnProperty('child') ) continue;
						let child_val = multiselect_data[index].child;
						multiselect_chkbox_list += '<ul class="children">';
						let childValKeys = Object.keys(multiselect_data[index].child);
						childValKeys.sort(function(a,b){
							return child_val[a].term.localeCompare(child_val[b].term);
						})
						childValKeys.map(function(key) {
							let term = (child_val[key].hasOwnProperty('term')) ? child_val[key].term : ''
							let title = (child_val[key].hasOwnProperty('title')) ? child_val[key].title : term
							let child_checked = (current_value != '' && (current_value.includes(title) || current_value.includes(key.toString()))) ? 'checked' : '';
							multiselect_chkbox_list += '<li> <input type="checkbox" name="chk_multiselect" value="'+ key +'" '+ child_checked +'>  '+ term +'</li>';
						});
						multiselect_chkbox_list += '</ul>';
					}               
					multiselect_chkbox_list += '</ul>';

				window.smart_manager.modal = {
					title: _x('Category', 'modal title', 'smart-manager-for-wp-e-commerce'),
					content: multiselect_chkbox_list,
					autoHide: false,
					cta: {
						title: _x('Ok', 'button', 'smart-manager-for-wp-e-commerce'),
						callback: function() {
							let mutiselect_edited_text = '';
							let selected_val = jQuery("input[name='chk_multiselect']:checked" ).map(function () {
													return jQuery(this).val();
												}).get();
							if( selected_val.length > 0 ) {
								for (var index in selected_val) {
									if( actual_value.hasOwnProperty(selected_val[index]) ) {
										if (mutiselect_edited_text != '') {
											mutiselect_edited_text += ', ';
										}
										mutiselect_edited_text += selected_val[index];
									}
								}
							}
							window.smart_manager.hot.setDataAtCell(coords.row, coords.col, mutiselect_edited_text, 'sm.multilist_inline_update');
						}
					},
				}
				window.smart_manager.showModal()
			}
		},
		// to handle updating the state on column resize
		afterColumnResize: function (currentColumn, newSize, isDoubleClick) {
			if(window.smart_manager.currentVisibleColumns[currentColumn]){
				for(let index in window.smart_manager.currentColModel){
					if(window.smart_manager.currentColModel[index].src == window.smart_manager.currentVisibleColumns[currentColumn].src){
						window.smart_manager.currentColModel[index].width = newSize
					}
				}
			}
		}
	});
}

Smart_Manager.prototype.reset = function( fullReset = false ){
	
	if(fullReset){
		window.smart_manager.currentDashboardModel = '';
		window.smart_manager.currentVisibleColumns = [];
		window.smart_manager.column_names = [];
		window.smart_manager.simpleSearchText = '';
		window.smart_manager.advancedSearchQuery = new Array();
		window.smart_manager.advancedSearchRuleCount = 0;
		window.smart_manager.colModelSearch = {}
		window.smart_manager.savedBulkEditConditions = []
	}

	window.smart_manager.currentDashboardData = [];
	
	window.smart_manager.selectedRows = [];
	window.smart_manager.selectAll = false;
	window.smart_manager.addRecords_count = 0;
	window.smart_manager.page = 1;
	window.smart_manager.dirtyRowColIds = {};
	window.smart_manager.editedData = {};

	if(window.smart_manager.hot){
		if(window.smart_manager.hot.selection){
			if(window.smart_manager.hot.selection.highlight){
				if(window.smart_manager.hot.selection.highlight.selectAll){
					delete window.smart_manager.hot.selection.highlight.selectAll
				}
				window.smart_manager.hot.selection.highlight.selectedRows = []
			}
		}
	}
}

Smart_Manager.prototype.refresh = function( dataParams ) {
	window.smart_manager.reset();

	if( window.smart_manager.sm_beta_pro == 0 ) {
		if( typeof( window.smart_manager.disableSelectedRows ) !== "undefined" && typeof( window.smart_manager.disableSelectedRows ) === "function" ) {
			window.smart_manager.disableSelectedRows(false);
		}
	}

	window.smart_manager.getData(dataParams);
}

// Function to show the pannel dialog
Smart_Manager.prototype.showPannelDialog = function(route = '', currentRoute = '') {
	if(!route && !currentRoute){
		return
	}

	let url = ''
	let currentURL = window.location.href

	if(!route){
		url = currentURL.replace(new RegExp(currentRoute, "g"), "/")
	} else {
		route = ("#!/" === route) ? route : "#!/"+route
		if(currentURL.includes(route)){
			url = currentURL.replace(new RegExp(route, "g"), route)
		} else if(currentURL.includes("#!/")){
			url = currentURL.replace(new RegExp("#!/", "g"), route)
		} else {
			url = currentURL + route
		}
	}

	if(url){
		window.location.href = url
	}
}

Smart_Manager.prototype.event_handler = function() {

	// Code to handle width of the grid based on the WP collapsable menu
	jQuery(document).on('click', '#collapse-menu', function() {
		let current_url = document.URL;

		if ( current_url.indexOf("page=smart-manager") == -1 ) {
			return;
		}

		if ( !jQuery(document.body).hasClass('folded') ) {
			window.smart_manager.grid_width = document.documentElement.offsetWidth - (document.documentElement.offsetWidth * 0.10);
		} else {
			window.smart_manager.grid_width = document.documentElement.offsetWidth - (document.documentElement.offsetWidth * 0.04);
		}
		
		window.smart_manager.hot.updateSettings({'width':window.smart_manager.grid_width});
		window.smart_manager.hot.render();

		jQuery('#sm_top_bar, #sm_bottom_bar').css('width',window.smart_manager.grid_width+'px');
		jQuery('#sm_top_bar_actions').css('width',window.smart_manager.grid_width+'px');
		jQuery('#sm_top_bar_left').css('width','calc('+ window.smart_manager.grid_width +'px - 2em');
	});

	//Code to handle dashboard change in grid
	jQuery(document).off('change', '#sm_dashboard_select').on('change', '#sm_dashboard_select',function(){

		var sm_dashboard_valid = 0,
			sm_selected_dashboard_key = jQuery(this).val(),
			sm_selected_dashboard_title = jQuery( "#sm_dashboard_select option:selected" ).text();

		if( window.smart_manager.sm_beta_pro == 0 ) {
			sm_dashboard_valid = 0;
			if( window.smart_manager.sm_lite_dashboards.indexOf(sm_selected_dashboard_key) >= 0 ) {
				sm_dashboard_valid = 1;    
			}
		} else {
			sm_dashboard_valid = 1;
		}

		if( sm_dashboard_valid == 1 ) {

			window.smart_manager.state_apply = true;
			// window.smart_manager.refreshDashboardStates(); //function to save the state
			
			if ( typeof (window.smart_manager.updateState) !== "undefined" && typeof (window.smart_manager.updateState) === "function" ) {
				window.smart_manager.updateState(); //refreshing the dashboard states
			}
			
			window.smart_manager.reset(true);

			window.smart_manager.dashboard_key = sm_selected_dashboard_key;
			window.smart_manager.dashboardName = sm_selected_dashboard_title;
			window.smart_manager.current_selected_dashboard = sm_selected_dashboard_key;
		
			window.smart_manager.setDashboardDisplayName();

			if( window.smart_manager.searchType === 'advanced' ){
				jQuery('#search_switch').prop('checked', false).trigger('change'); //Code to re-draw the search content based on search type
			} else {
				jQuery('#sm_simple_search_box').val('');
			}

			if ( typeof (window.smart_manager.initialize_advanced_search) !== "undefined" && typeof (window.smart_manager.initialize_advanced_search) === "function" && window.smart_manager.searchType != 'simple' ) {
				window.smart_manager.initialize_advanced_search();
			}

			if ( window.smart_manager.dashboard_key == 'shop_order' ) {
				jQuery('#print_invoice_sm_editor_grid_btn').show();
			} else {
				jQuery('#print_invoice_sm_editor_grid_btn').hide();
			}

			(window.smart_manager.isTaxonomyDashboard()) ? jQuery('#sm_beta_move_to_trash').hide() : jQuery('#sm_beta_move_to_trash').show();

			window.smart_manager.displayShowHideColumnSettings(true);

			jQuery('#sm_editor_grid').trigger( 'sm_dashboard_change' ); //custom trigger

			window.smart_manager.load_dashboard(); 
		} else {
			jQuery(this).val(window.smart_manager.current_selected_dashboard);
			window.smart_manager.notification = {message: sprintf(_x('For managing %s, %s %s version', 'modal content', 'smart-manager-for-wp-e-commerce'), sm_selected_dashboard_title, window.smart_manager.sm_success_msg, '<a href="' + window.smart_manager.pricingPageURL + '" target="_blank">'+_x('Pro', 'modal content', 'smart-manager-for-wp-e-commerce')+'</a>'), hideDelay: window.smart_manager.notificationHideDelayInMs}
			window.smart_manager.showNotification()
		}
		
	})
	
	.off( 'click', '#sm_advanced_search' ).on( 'click', '#sm_advanced_search' ,function(e){
		e.preventDefault();

		if ( typeof (window.smart_manager.showPannelDialog) !== "undefined" && typeof (window.smart_manager.showPannelDialog) === "function" ) {
			window.smart_manager.showPannelDialog(window.smart_manager.advancedSearchRoute)
		}
	})

	.off( 'click', '#show_hide_cols_sm_editor_grid' ).on( 'click', '#show_hide_cols_sm_editor_grid' ,function(e){
		e.preventDefault();
		if ( "undefined" !== typeof (window.smart_manager.showPannelDialog) && "function" === typeof (window.smart_manager.showPannelDialog) ) {
			window.smart_manager.showPannelDialog(window.smart_manager.columnManagerRoute);
		}
	})

	//code for handling resetting column state to default state
	.off('click', 'a#sm_reset_state').on('click', 'a#sm_reset_state', function (e){
		e.preventDefault();

		let params = {},
			viewSlug = window.smart_manager.getViewSlug(window.smart_manager.dashboardName);
		params.data_type = 'json';
		params.data = {
						cmd: 'reset_state',
						security: window.smart_manager.sm_nonce,
						active_module: window.smart_manager.dashboard_key
					};

		params.data['dashboard_key'] = window.smart_manager.dashboard_key
		// Code for passing extra param for view handling
		if( 1 == window.smart_manager.sm_beta_pro ) {
			params.data['is_view'] = 0;

			if(viewSlug){
				params.data['is_view'] = 1;
				params.data['active_module'] = viewSlug;
			}
		}

		window.smart_manager.send_request(params, function(response) {
			let dashboardURLParams = (viewSlug) ? viewSlug+"&is_view=1" : window.smart_manager.dashboard_key;
			window.location.href = (window.smart_manager.smAppAdminURL || window.location.href) + ((window.location.href.indexOf("?")===-1)?"?":"&") + "dashboard="+dashboardURLParams;
		})			
	})

	.off( 'change', '#search_switch').on( 'change', '#search_switch' ,function(){ //request for handling switch search types

		let switchSearchType = jQuery(this).attr('switchSearchType'),
			title = jQuery("label[for='"+ jQuery(this).attr("id") +"']").attr('title'),
			content = '';

		// if(window.smart_manager.clearSearchOnSwitch){
		// 	window.smart_manager.advancedSearchQuery = new Array();
		// 	window.smart_manager.simpleSearchText = '';
		// }

		jQuery(this).attr('switchSearchType', window.smart_manager.searchType);
		jQuery("label[for='"+ jQuery(this).attr("id") +"']").attr('title', title.replace(String(switchSearchType).capitalize(), String(window.smart_manager.searchType).capitalize()));

		window.smart_manager.searchType = switchSearchType;
		content = ( window.smart_manager.searchType == 'simple' ) ? window.smart_manager.simpleSearchContent : window.smart_manager.advancedSearchContent;
		jQuery('#sm_nav_bar_search #search_content').html(content);

		if( window.smart_manager.searchType == 'simple' ) {
			jQuery('#sm_simple_search_box').val(window.smart_manager.simpleSearchText);
		} else {
			// Code to initialize search col model
			if ( typeof (window.smart_manager.initialize_advanced_search) !== "undefined" && typeof (window.smart_manager.initialize_advanced_search) === "function" ) {
				window.smart_manager.initialize_advanced_search();
			}

			// Code to show the advanced search dialog in case of no conditions
			if ( window.smart_manager.advancedSearchRuleCount == 0 && typeof (window.smart_manager.showPannelDialog) !== "undefined" && typeof (window.smart_manager.showPannelDialog) === "function" ) {
				window.smart_manager.showPannelDialog(window.smart_manager.advancedSearchRoute)
			}
		}

		// code for refreshing the dashboard based on the search
		if ( (window.smart_manager.simpleSearchText != '' || window.smart_manager.advancedSearchRuleCount > 0) && typeof (window.smart_manager.load_dashboard) !== "undefined" && typeof (window.smart_manager.load_dashboard) === "function" ) {
			window.smart_manager.load_dashboard()
		}

	})

	.off( 'keyup', '#sm_simple_search_box').on( 'keyup', '#sm_simple_search_box' ,function(){ //request for handling simple search
		clearTimeout(window.smart_manager.searchTimeoutId);
		window.smart_manager.searchTimeoutId = setTimeout(function () {
			window.smart_manager.simpleSearchText = jQuery('#sm_simple_search_box').val();
			window.smart_manager.refresh();
		}, 1000);
	})

	//Code to handle the inline save functionality
	.off( 'click', '.sm_top_bar_action_btns #save_sm_editor_grid_btn').on( 'click', '.sm_top_bar_action_btns #save_sm_editor_grid_btn' ,function(){
		
		if(Object.keys(window.smart_manager.editedData).length == 0){
			window.smart_manager.notification = {message: _x('Please edit a record', 'notification', 'smart-manager-for-wp-e-commerce')}
			window.smart_manager.showNotification()
			return;
		}

		if(window.smart_manager.dashboard_key == 'user' && Object.keys(window.smart_manager.dirtyRowColIds).length > 0){
			for(let row in window.smart_manager.dirtyRowColIds){
				let userEmail = window.smart_manager.hot.getDataAtRowProp(row, 'users_user_email');
				if(!userEmail){
					window.smart_manager.notification = {message: _x('Please enter user email', 'notification', 'smart-manager-for-wp-e-commerce') + '<div style="font-size:0.9em;font-style: italic;margin:1em;">'+_x('Enable', 'notification', 'smart-manager-for-wp-e-commerce')+'<code>'+_x('User Email', 'notification', 'smart-manager-for-wp-e-commerce')+'</code>'+_x('column if not enabled using', 'notification', 'smart-manager-for-wp-e-commerce')+'<a href="https://www.storeapps.org/docs/sm-how-to-show-hide-columns-in-dashboard/?utm_source=sm&utm_medium=in_app&utm_campaign=view_docs" target="_blank"> '+_x('column show/hide functionality', 'notification', 'smart-manager-for-wp-e-commerce')+'</a>.</div>',hideDelay: window.smart_manager.notificationHideDelayInMs}
					window.smart_manager.showNotification()
					return;
				}			
			}		
		}

		if( typeof (window.smart_manager.saveData) !== "undefined" && typeof (window.smart_manager.saveData) === "function" ) {
			window.smart_manager.saveData();    
		}
	})

	//Code to handle the delete records functionality
	.off( 'click', '.sm_top_bar_action_btns #sm_beta_move_to_trash, .sm_top_bar_action_btns #sm_beta_delete_permanently').on( 'click', '.sm_top_bar_action_btns #sm_beta_move_to_trash, .sm_top_bar_action_btns #sm_beta_delete_permanently' ,function(){

		let id = jQuery(this).attr('id');

		let deletePermanently = ( 'sm_beta_delete_permanently' == id ) ? 1 : 0;
		let moveToTrash = ( 'sm_beta_move_to_trash' == id ) ? 1 : 0;

		let isBackgroundProcessRunning = window.smart_manager.backgroundProcessRunningNotification(false);

		if( 0 == window.smart_manager.sm_beta_pro && deletePermanently ) {
			window.smart_manager.notification = {status:'error',message: _x('To permanently delete records', 'notification', 'smart-manager-for-wp-e-commerce')+', <a href="' + window.smart_manager.pricingPageURL + '" target="_blank">'+_x('upgrade to Pro', 'notification', 'smart-manager-for-wp-e-commerce')+'</a>',hideDelay: window.smart_manager.notificationHideDelayInMs}
			window.smart_manager.showNotification()
			return false;
		}

		if( ( deletePermanently || moveToTrash ) && window.smart_manager.trashAndDeletePermanently.disable ) {
			window.smart_manager.notification = {status:'error',message: window.smart_manager.trashAndDeletePermanently.error_message,hideDelay: window.smart_manager.notificationHideDelayInMs}
			window.smart_manager.showNotification()
			return false;
		}
			
		if( window.smart_manager.selectedRows.length == 0 && !window.smart_manager.selectAll ) {
			window.smart_manager.notification = {message: _x('Please select a record', 'notification', 'smart-manager-for-wp-e-commerce')}
			window.smart_manager.showNotification()
			return false;
		}

		if ( window.smart_manager.sm_beta_pro == 0 && window.smart_manager.selectedRows.length > window.smart_manager.sm_deleted_successful ) {
			window.smart_manager.notification = {message: _x('To delete more than', 'notification', 'smart-manager-for-wp-e-commerce')+' '+window.smart_manager.sm_deleted_successful+' '+_x('records at a time', 'notification', 'smart-manager-for-wp-e-commerce')+', <a href="' + window.smart_manager.pricingPageURL + '" target="_blank">'+_x('upgrade to Pro', 'notification', 'smart-manager-for-wp-e-commerce')+'</a>',hideDelay:window.smart_manager.notificationHideDelayInMs}
			window.smart_manager.showNotification()
		} else {	

			let params = {};

			params.title       = '<span class="sm-error-icon"><span class="dashicons dashicons-warning" style="vertical-align: text-bottom;"></span>&nbsp;'+_x('Attention!!!', 'modal title', 'smart-manager-for-wp-e-commerce')+'</span>';
			params.titleIsHtml = true;
			params.btnParams   = {};

			let actionText = ( !window.smart_manager.trashEnabled || deletePermanently ) ? '<span class="sm-error-icon">'+_x('permanently delete', 'modal content', 'smart-manager-for-wp-e-commerce')+'</span>' : _x('trash', 'modal content', 'smart-manager-for-wp-e-commerce'); 

			if( !window.smart_manager.trashEnabled || deletePermanently ) {
				params.height = 170;
			}

			let selected_text = '<span style="font-size: 1.2em;">'+sprintf(_x('Are you sure you want to %s', 'modal content', 'smart-manager-for-wp-e-commerce'), '<strong>'+ actionText +' '+_x('the selected','modal content', 'smart-manager-for-wp-e-commerce')+'</strong>'+' ') + ( ( window.smart_manager.selectedRows.length > 1 ) ? _x('records', 'modal content', 'smart-manager-for-wp-e-commerce') : _x('record', 'modal content', 'smart-manager-for-wp-e-commerce') ) + '?</span>';
			let all_text      = '<span style="font-size: 1.2em;">'+sprintf(_x('Are you sure you want to %s the %s?', 'modal content', 'smart-manager-for-wp-e-commerce'),'<strong>'+ actionText +' '+_x('all', 'modal content', 'smart-manager-for-wp-e-commerce')+'</strong>', window.smart_manager.dashboardDisplayName)+ '</span>';

			params.btnParams.yesCallbackParams = {};

			if ( window.smart_manager.sm_beta_pro == 1 ) {
				params.btnParams.yesCallbackParams = { 'deletePermanently': deletePermanently };

				if ( true === window.smart_manager.selectAll ) {
					params.content = all_text;
				} else {
					params.content = selected_text;
				}

				if ( typeof (window.smart_manager.deleteAllRecords) !== "undefined" && typeof (window.smart_manager.deleteAllRecords) === "function" ) {
					params.btnParams.yesCallback = window.smart_manager.deleteAllRecords;
				}
			} else {
				if ( typeof (window.smart_manager.deleteRecords) !== "undefined" && typeof (window.smart_manager.deleteRecords) === "function" ) {
					params.content = selected_text;
					if ( true === window.smart_manager.selectAll ) {
						params.content += '<br><br><br><span style="font-size: 1.2em;"><small><i>'+_x('Note: Looking to', 'modal content', 'smart-manager-for-wp-e-commerce')+' <strong>'+_x('delete all', 'modal content', 'smart-manager-for-wp-e-commerce')+'</strong> '+_x('the records?', 'modal content', 'smart-manager-for-wp-e-commerce')+' <a href="' + window.smart_manager.pricingPageURL + '" target="_blank">'+_x('Upgrade to Pro', 'modal content', 'smart-manager-for-wp-e-commerce')+'</a></i></small></span>';
						params.height = 225;
					}
					params.btnParams.yesCallback = window.smart_manager.deleteRecords;
				}
			}
			if( !isBackgroundProcessRunning ) {
				window.smart_manager.showConfirmDialog(params);
			}
		}
		return false;    
	})

	//Code for handling refresh event
	.off( 'click', ".sm_gallery_image .sm_gallery_image_delete").on( 'click', ".sm_gallery_image .sm_gallery_image_delete", function(){
		let colSrc = jQuery(this).parents('div.sm_gallery_image_parent').data('col') || '',
		updateId = jQuery(this).parents('div.sm_gallery_image_parent').data('id') || 0;

		jQuery(this).parents('.sm_gallery_image').remove();

		let imageIds = new Array();

		jQuery('.sm_gallery_image').find('img').each( function(){
			imageIds.push( jQuery(this).data('id') );
		});

		let updatedGalleryImages = {};

		updatedGalleryImages[updateId] = {};
		updatedGalleryImages[updateId][colSrc] = imageIds.join(',');

	  	if ( typeof (window.smart_manager.inlineUpdateMultipleImages) !== "undefined" && typeof (window.smart_manager.inlineUpdateMultipleImages) === "function" ) {
			window.smart_manager.inlineUpdateMultipleImages( updatedGalleryImages );
		}
	})
	

	//Code for handling refresh event
	.off( 'click', "#refresh_sm_editor_grid").on( 'click', "#refresh_sm_editor_grid", function(){
		window.smart_manager.refresh();
	})

	.off( 'click', "#sm_editor_grid_distraction_free_mode").on( 'click', "#sm_editor_grid_distraction_free_mode", function(){

		if ( window.smart_manager.sm_beta_pro == 1 ) {
			if ( typeof (window.smart_manager.smToggleFullScreen) !== "undefined" && typeof (window.smart_manager.smToggleFullScreen) === "function" ) {
				let element = document.documentElement;
				window.smart_manager.smToggleFullScreen( element );    
			}
		} else {
			window.smart_manager.notification = {message: sprintf(_x('This feature is available only in the %s version', 'modal content', 'smart-manager-for-wp-e-commerce'), '<a href="' + window.smart_manager.pricingPageURL + '" target="_blank">'+_x('Pro', 'modal content', 'smart-manager-for-wp-e-commerce')+'</a>'),hideDelay: window.smart_manager.notificationHideDelayInMs}
			window.smart_manager.showNotification()
		}
		
/*Review*/
		window.smart_manager.hot.updateSettings({'width':window.smart_manager.grid_width});
		window.smart_manager.hot.render();

		jQuery('#sm_top_bar, #sm_bottom_bar').css('width',window.smart_manager.grid_width+'px');
	})

	//Code for load more items
	.off( 'click', "#sm_editor_grid_load_items").on( 'click', "#sm_editor_grid_load_items", function(){

		if( window.smart_manager.currentDashboardData.length >= window.smart_manager.totalRecords ) {
			return;
		}

		window.smart_manager.page++;
		window.smart_manager.getData();
	})

	.off( 'click', 'td.htDimmed' ).on( 'click', 'td.htDimmed' , function() {
		if( window.smart_manager.sm_beta_pro == 0 ) {
			if( typeof( window.smart_manager.modifiedRows ) != 'undefined' ) {
				if( window.smart_manager.modifiedRows.length >= window.smart_manager.sm_updated_successful ) {
					alert(_x('For editing more records upgrade to Pro', 'notification', 'smart-manager-for-wp-e-commerce'));
				}
			}
		}
	})

	//Code for add record functionality
	.off( 'click', "#add_sm_editor_grid").on( 'click', "#add_sm_editor_grid", function(){
		window.smart_manager.modal = {
			title:  sprintf(_x('Add %s(s)', 'modal title', 'smart-manager-for-wp-e-commerce'), window.smart_manager.dashboardDisplayName),
			content: '<div style="font-size:1.2em;margin:1em;"> <div style="margin-bottom:1em;">'+sprintf(_x('Enter how many new %s(s) to create!', 'modal content', 'smart-manager-for-wp-e-commerce'), window.smart_manager.dashboardDisplayName)+'</div> <input type="number" id="sm_beta_add_record_count" min="1" value="1" style="width:5em;"></div>',
			autoHide: false,
			cta: {
				title: _x('Create', 'button', 'smart-manager-for-wp-e-commerce'),
				callback: function() {
					// setTimeout((window.smart_manager.modal = {}),2000) // code to hide the modal
					let count = jQuery('#sm_beta_add_record_count').val();
					if( count > 0 ) {
						window.smart_manager.hot.alter('insert_row', 0, count);
					}
				}
			},
			closeCTA: { title: _x('Cancel', 'button', 'smart-manager-for-wp-e-commerce')}
		}
		window.smart_manager.showModal()
	})

	.off('click', "#sm_custom_views_create, #sm_custom_views_update").on('click', "#sm_custom_views_create, #sm_custom_views_update", function(e){
		e.preventDefault();
		if( window.smart_manager.sm_beta_pro == 1 ) {
			if ( typeof (window.smart_manager.createUpdateViewDialog) !== "undefined" && typeof (window.smart_manager.createUpdateViewDialog) === "function" ) {
				let id = jQuery(this).attr('id');
				let action = (id == 'sm_custom_views_update') ? 'update' : 'create';
				window.smart_manager.createUpdateViewDialog(action);
			}
		}  else {
			window.smart_manager.notification = {message: sprintf(_x('Custom Views avialable (Only in %s)', 'notification', 'smart-manager-for-wp-e-commerce'), '<a href="'+ window.smart_manager.pricingPageURL +'" target="_blank">'+_x('Pro', 'notification', 'smart-manager-for-wp-e-commerce')+'</a>'),hideDelay: window.smart_manager.notificationHideDelayInMs}
			window.smart_manager.showNotification()
		}
		
	})

	.off('click', "#sm_custom_views_delete").on('click', "#sm_custom_views_delete", function(e){
		e.preventDefault();
		if( window.smart_manager.sm_beta_pro == 1 ) {
			let params = {};

			params.btnParams = {}
			params.title = '<span class="sm-error-icon"><span class="dashicons dashicons-warning" style="vertical-align: text-bottom;"></span>&nbsp;'+_x('Attention!!!', 'modal title', 'smart-manager-for-wp-e-commerce')+'</span>';
			params.content = '<span style="font-size: 1.2em;">'+_x('This will', 'modal content', 'smart-manager-for-wp-e-commerce')+' <span class="sm-error-icon"><strong>'+_x('delete', 'modal content', 'smart-manager-for-wp-e-commerce')+'</strong></span> '+_x('the current view. Are you sure you want to continue?', 'modal content', 'smart-manager-for-wp-e-commerce')+'</span>';
			params.titleIsHtml = true;
			params.height = 200;

			if ( typeof (window.smart_manager.deleteView) !== "undefined" && typeof (window.smart_manager.deleteView) === "function" ) {
				params.btnParams.yesCallback = window.smart_manager.deleteView;
			}
			
			window.smart_manager.showConfirmDialog(params);
		}  else {
			window.smart_manager.notification = {message: sprintf(_x('Custom Views avialable (Only in %s)', 'notification', 'smart-manager-for-wp-e-commerce'), '<a href="'+ window.smart_manager.pricingPageURL +'" target="_blank">'+_x('Pro', 'notification', 'smart-manager-for-wp-e-commerce')+'</a>'),hideDelay: window.smart_manager.notificationHideDelayInMs}
			window.smart_manager.showNotification()
		}
	})

	// Code for handling the batch update & duplicate records functionality
	.off( 'click', "#batch_update_sm_editor_grid, .sm_top_bar_action_btns .sm_beta_dropdown_content a, #export_csv_sm_editor_grid, #print_invoice_sm_editor_grid_btn").on( 'click', "#batch_update_sm_editor_grid, .sm_top_bar_action_btns .sm_beta_dropdown_content a, #export_csv_sm_editor_grid, #print_invoice_sm_editor_grid_btn", function(){

		let id = jQuery(this).attr('id'),
			btnText = jQuery(this).text();

		if( jQuery(this).parents('div#del_sm_editor_grid').length > 0 || jQuery(this).parents('div#sm_custom_views').length > 0 ) {
			return;
		}

		if( window.smart_manager.sm_beta_pro == 1 ) {

			if( typeof( id ) != 'undefined' ) {
				if( id == 'export_csv_sm_editor_grid' ) { //code for handling export CSV functionality
					if ( typeof (window.smart_manager.generateCsvExport) !== "undefined" && typeof (window.smart_manager.generateCsvExport) === "function" ) {
						window.smart_manager.generateCsvExport();    
					}
				} else {
					if( window.smart_manager.selectedRows.length > 0 || window.smart_manager.selectAll ) {

						let isBackgroundProcessRunning = window.smart_manager.backgroundProcessRunningNotification(false);

						if( id == 'batch_update_sm_editor_grid' && !isBackgroundProcessRunning ) { //code for handling batch update functionality
							// window.smart_manager.createBatchUpdateDialog();
							if ( typeof (window.smart_manager.showPannelDialog) !== "undefined" && typeof (window.smart_manager.showPannelDialog) === "function" ) {
								window.smart_manager.showPannelDialog(window.smart_manager.bulkEditRoute)
							}
						} else if( ( id == 'sm_beta_dup_entire_store' || id == 'sm_beta_dup_selected' ) && !isBackgroundProcessRunning ) { //code for handling duplicate records functionality
							if(window.smart_manager.isTaxonomyDashboard()){
								window.smart_manager.notification = {message: _x('Comming soon', 'notification', 'smart-manager-for-wp-e-commerce')}
								window.smart_manager.showNotification()
							} else {
								let params = {};

								params.btnParams = {}
								params.title = _x('Attention!!!', 'modal title', 'smart-manager-for-wp-e-commerce');
								params.content = (window.smart_manager.dashboard_key != 'product') ? '<p>'+_x('This will duplicate only the records in posts, postmeta and related taxonomies.', 'modal content', 'smart-manager-for-wp-e-commerce')+'</p>' : '';
								params.content += _x('Are you sure you want to duplicate the ', 'modal content', 'smart-manager-for-wp-e-commerce') + btnText + '?';

								if ( typeof (window.smart_manager.duplicateRecords) !== "undefined" && typeof (window.smart_manager.duplicateRecords) === "function" ) {
									params.btnParams.yesCallback = window.smart_manager.duplicateRecords;
								}
								
								window.smart_manager.duplicateStore = ( id == 'sm_beta_dup_entire_store' ) ? true : false;

								window.smart_manager.showConfirmDialog(params);
							}
						} else if( id == 'print_invoice_sm_editor_grid_btn' ) { //code for handling Print Invoice functionality
							if ( typeof (window.smart_manager.printInvoice) !== "undefined" && typeof (window.smart_manager.printInvoice) === "function" ) {
								window.smart_manager.printInvoice();
							}
						}

					} else {
						window.smart_manager.notification = {message: _x('Please select a record', 'notification', 'smart-manager-for-wp-e-commerce')}
						window.smart_manager.showNotification()
					}
				}
			}
			
		} else {

			if( typeof(id) != 'undefined' ) {


				if( id != 'sm_beta_dup_entire_store' && id != 'sm_beta_dup_selected' ) {
					
					let description = sprintf(_x('You can change/update multiple fields of the entire store OR for selected items using the Bulk Edit feature. Refer to this doc on %s or watch the video below.', 'modal description', 'smart-manager-for-wp-e-commerce'), '<a href="https://www.storeapps.org/docs/sm-how-to-use-batch-update/?utm_source=sm&utm_medium=in_app&utm_campaign=view_docs" target="_blank">'+_x('how to do bulk edit', 'modal description', 'smart-manager-for-wp-e-commerce')+'</a>');

					if( id == 'export_csv_sm_editor_grid' ) {
						description = _x('You can export all the records OR filtered records (using Simple Search or Advanced Search) by simply clicking on the Export CSV button at the bottom right of the grid.', 'modal description', 'smart-manager-for-wp-e-commerce');
					}

					content = '<div>'+
									'<p style="font-size:1.2em;margin:1em;">'+description+'</p>'+
									'<div style="height:17rem;"><iframe width="100%" height="100%" src="https://www.youtube.com/embed/'+ ( ( id == 'batch_update_sm_editor_grid' ) ? 'COXCuX2rFrk' : 'GMgysSQw7_g' ) +'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>'+
								'</div>'

					title = ( ( id == 'batch_update_sm_editor_grid' ) ? btnText + ' - <span style="color: red;">'+_x('Biggest Time Saver', 'modal title', 'smart-manager-for-wp-e-commerce')+' </span>' : btnText ) + sprintf(_x('(Only in %s)', 'modal title', 'smart-manager-for-wp-e-commerce'), '<a href="'+ window.smart_manager.pricingPageURL +'" target="_blank">'+_x('Pro', 'modal title', 'smart-manager-for-wp-e-commerce')+'</a>');

					window.smart_manager.modal = {
						title: title,
						content: content,
						width: 'w-2/6',
						autoHide: false,
						isFooterItemsCenterAligned: true,
						cta: {
							title: _x('Get Pro at 25% off', 'button', 'smart-manager-for-wp-e-commerce'),
							callback: function() {
								window.open(window.smart_manager.pricingPageURL, "_blank");
								jQuery( this ).dialog( "close" );
							}
						}
					}
					window.smart_manager.showModal()

				} else {
					window.smart_manager.notification = {message: sprintf(_x('Duplicate Records (Only in %s)', 'notification', 'smart-manager-for-wp-e-commerce'), '<a href="'+ window.smart_manager.pricingPageURL +'" target="_blank">'+_x('Pro', 'notification', 'smart-manager-for-wp-e-commerce')+'</a>'),hideDelay: window.smart_manager.notificationHideDelayInMs}
					window.smart_manager.showNotification()
				}
			} else {
				window.smart_manager.notification = {message: sprintf(_x('This feature is available only in the %s version', 'modal content', 'smart-manager-for-wp-e-commerce'), '<a href="' + window.smart_manager.pricingPageURL + '" target="_blank">'+_x('Pro', 'modal content', 'smart-manager-for-wp-e-commerce')+'</a>'),hideDelay: window.smart_manager.notificationHideDelayInMs}
		    	window.smart_manager.showNotification()
			}
		}
	})

	.off('mouseover', '.sm_gallery_image > img').on('mouseover','.sm_gallery_image > img', function(e){
		
		let params = {
						'current_cell_value': jQuery(this).attr('src'),
						'event': e,
						'title': ''
					};

		if( typeof( window.smart_manager.showImagePreview ) !== "undefined" && typeof( window.smart_manager.showImagePreview ) === "function" ) {
			window.smart_manager.showImagePreview(params);
		}

	})

	.off('mouseout', '.sm_gallery_image > img').on('mouseout','.sm_gallery_image > img', function(e){
		
		if( jQuery('#sm_img_preview').length > 0 ) {
			jQuery('#sm_img_preview').remove();
		}

	})

	//Code for handling the dropdown menu for the duplicate button
	.off('mouseenter', '.sm_beta_dropdown').on('mouseenter','.sm_beta_dropdown', function(){
		jQuery(this).find('.sm_beta_dropdown_content').show();
	})

	//Code for handling the dropdown menu for the duplicate button
	.off('mouseleave', '.sm_beta_dropdown').on('mouseleave','.sm_beta_dropdown', function(){
		jQuery(this).find('.sm_beta_dropdown_content').hide();
	})

	.off("click", ".sm_click_to_copy").on("click", ".sm_click_to_copy", function() {
		let temp = jQuery("<input>");
		  jQuery("body").append(temp);
		  temp.val(jQuery(this).html()).select();
		  document.execCommand("copy");
		  temp.remove();
	});

	jQuery(document).trigger('sm_event_handler');

}

//Function to equalize the enabled and disabled section height in column visibility dialog
Smart_Manager.prototype.columnVisibilityEqualizeHeight = function() {
	let enabledHeight = jQuery('#sm-columns-enabled').height(),
		disabledHeight = jQuery('#sm-columns-disabled').height(),
		maxHeight = enabledHeight > disabledHeight ? enabledHeight : disabledHeight;

	if( maxHeight > 0 ) {
		jQuery('#sm-columns-enabled, #sm-columns-disabled').height(maxHeight);
	}
}

//Function to process Column Visibility Enabled & Disabled Columns Search
Smart_Manager.prototype.processColumnVisibilitySearch = function(eventObj) {
	
	let searchString = jQuery(eventObj).val(),
		ulId = jQuery(eventObj).attr('data-ul-id');
	
	if( ulId != '' ) {
		jQuery("#"+ulId).find('li').each( function() {
			let txtValue = jQuery(this).text();
			if (txtValue.toUpperCase().indexOf(searchString.toUpperCase()) > -1) {
		      jQuery(this).show();
		    } else {
		      jQuery(this).hide();
		    }
		});
	}
}

//Function to create column Visibility dialog
Smart_Manager.prototype.createColumnVisibilityDialog = function() {
	if( 'undefined' === typeof (window.smart_manager.currentColModel)  ) {
		return;
	}
	let enabledColumnsArray = new Array(),
		hiddenColumnsArray = new Array(),
		colText = '',
		colVal = '',
		temp = '',
		panelContent = '';

	for( let key in window.smart_manager.currentColModel ) {

		colObj = window.smart_manager.currentColModel[key];

		if( ! colObj.hasOwnProperty('data') ) {
			continue;
		}

		if( colObj.hasOwnProperty('allow_showhide') && true === colObj.allow_showhide ) {

			colText = ( colObj.hasOwnProperty('name_display') ) ? colObj.name_display : '';
			colVal = ( colObj.hasOwnProperty('data') ) ? colObj.data : '';
			colPosition = ( colObj.hasOwnProperty('position') ) ? ( ( colObj.position != '' ) ? colObj.position - 1 : '' ) : '';


			temp = '<li><span class="handle">::</span> '+ colText + ' ' +
						'<input type="hidden" name="columns[]" class="js-column-key" value="'+ colVal +'"> '+
						'<input type="hidden" name="columns_names[]" class="js-column-title" value="'+ colText +'"> '+
					'</li>';

			if( colObj.hasOwnProperty('hidden') && false === colObj.hidden ) {
				enabledColumnsArray.push(temp);
			} else if( colObj.hasOwnProperty('hidden') && true === colObj.hidden ) {
				hiddenColumnsArray.push(temp);
			}
		} 
	}


	panelContent = '<form id="sm-column-visibility"> '+
					'<ul class="unstyled-list"> '+
						'<li> '+_x('Drag the enabled columns to the right to disable them and vise-versa. Drag the columns top or bottom to rearrange their position in the grid.', 'columns settings description', 'smart-manager-for-wp-e-commerce')+
						'</li> '+
						'<li style="margin-top: 1em;"> '+sprintf(_x('Click %s to reset the Columns order to default.', 'columns settings description', 'smart-manager-for-wp-e-commerce'), '<a href="#" id="sm_reset_state" style="cursor:pointer;">'+_x('here', 'columns settings description', 'smart-manager-for-wp-e-commerce')+'</a>')+
						'</li> '+
						'<li> '+
							'<div class="sm-sorter-section"> '+
								'<h3>'+_x('Enabled', 'columns settings searchbox heading', 'smart-manager-for-wp-e-commerce')+'</h3> '+
								'<input type="text" id="searchEnabledColumns" data-ul-id="sm-columns-enabled" class="sm-search-box" onkeyup="window.smart_manager.processColumnVisibilitySearch(this)" placeholder="'+_x('Search For Enabled Columns...', 'placeholder', 'smart-manager-for-wp-e-commerce')+'"> '+
								'<ul class="sm-sorter columns-enabled" id="sm-columns-enabled"> '+
									enabledColumnsArray.join("") +
								'</ul> '+
							'</div> '+
							'<div class="sm-sorter-section"> '+
								'<h3>'+_x('Disabled', 'columns settings searchbox heading', 'smart-manager-for-wp-e-commerce')+'</h3> '+
								'<input type="text" id="searchDisabledColumns" data-ul-id="sm-columns-disabled" class="sm-search-box" onkeyup="window.smart_manager.processColumnVisibilitySearch(this)" placeholder="'+_x('Search For Disabled Columns...', 'placeholder', 'smart-manager-for-wp-e-commerce')+'"> '+
								'<ul class="sm-sorter columns-disabled" id="sm-columns-disabled"> '+
									hiddenColumnsArray.join("") +
								'</ul> '+
							'</div> '+
						'</li> '+
					'</ul> '+
					'<input type="hidden" value="" id="sm-all-enabled-columns"> '+
				'</form> ';


	document.getElementById('column-settings').innerHTML = panelContent;

	if ( "undefined" !== typeof (window.smart_manager.processColumnVisibility) && "function" === typeof (window.smart_manager.processColumnVisibility) ) {
		window.smart_manager.processColumnVisibility;
	}

	if ( "undefined" !== typeof (window.smart_manager.columnVisibilityEqualizeHeight) && "function" === typeof (window.smart_manager.columnVisibilityEqualizeHeight) ) {
		window.smart_manager.columnVisibilityEqualizeHeight();
	}

	let $columns = document.getElementById('sm-columns-enabled'),
		$columnsDisabled = document.getElementById('sm-columns-disabled');

	window.smart_manager.enabledSortable = Sortable.create($columns, {
		group: 'smartManagerColumns',
		animation: 100,
		onSort: function (evt) {
			if ( "undefined" !== typeof (window.smart_manager.columnsMoved) && "function" === typeof (window.smart_manager.columnsMoved) ) {
				window.smart_manager.columnsMoved();
			}
		}
	});
	window.smart_manager.disabledSortable = Sortable.create($columnsDisabled, {
		group: 'smartManagerColumns',
		animation: 100
	});
}


//Function to block Bulk Edit/Duplicate Records/Delete Records functionality when background process is running
Smart_Manager.prototype.backgroundProcessRunningNotification = function( isBackgroundProcessRunning = false ) {
		isBackgroundProcessRunning = ( "undefined" !== typeof (window.smart_manager.isBackgroundProcessRunning) && "function" === typeof (window.smart_manager.isBackgroundProcessRunning) ) ? window.smart_manager.isBackgroundProcessRunning() : false;
		if( isBackgroundProcessRunning ) {
			window.smart_manager.notification = {message: window.smart_manager.backgroundProcessRunningMessage,hideDelay: window.smart_manager.notificationHideDelayInMs}
		    window.smart_manager.showNotification()
		}
		return isBackgroundProcessRunning;
}

//Function to update the list of enabled columns on column move event
Smart_Manager.prototype.columnsMoved = function() {
	let enabled = jQuery('#sm-column-visibility').find('.columns-enabled .js-column-key');
	let allEnabled = enabled.map(function () {
		return jQuery(this).val();
	}).get().join(',');
	jQuery('#sm-column-visibility').find('#sm-all-enabled-columns').val(allEnabled);
	window.smart_manager.columnsVisibilityUsed = true;
}

//Function to load the updated list of enabled columns in the grid
Smart_Manager.prototype.processColumnVisibility = function() {
	if( false === window.smart_manager.columnsVisibilityUsed ) {
		return false;
	}

	let enabledColumns = jQuery('#sm-column-visibility').find('#sm-all-enabled-columns').val();

	if( 'undefined' === typeof (enabledColumns) || 'undefined' === typeof (window.smart_manager.currentColModel) ) {
		return;
	}

	if( enabledColumns.length > 0 ) {

		// let idKey = ( window.smart_manager.dashboard_key == 'user' ) ? 'users_id' : 'posts_id';
        	// enabledColumns = idKey + ',' + enabledColumns;

		let enabledColumnsArray = enabledColumns.split(','),
			colVal = '',
			position = 0,
			index = 0;

		window.smart_manager.column_names = [];
		window.smart_manager.currentVisibleColumns = [];

		for( let key in window.smart_manager.currentColModel ) {

			colObj = window.smart_manager.currentColModel[key];

			if( colObj.hasOwnProperty('allow_showhide') && true === colObj.allow_showhide ) {
				colVal = ( colObj.hasOwnProperty('data') ) ? colObj.data : '';

				if( enabledColumnsArray.indexOf(colVal) != -1 ) {

					position = enabledColumnsArray.indexOf(colVal)+1;

					window.smart_manager.currentColModel[key].hidden = false; //Code for refreshing the column visibility
					window.smart_manager.currentColModel[key].position = position; //Code for refreshing the column position

				} else {
					window.smart_manager.currentColModel[key].hidden = true;
				}
			}
		}

		if ( "undefined" !== typeof (window.smart_manager.sortColumns) && "function" === typeof (window.smart_manager.sortColumns) ) {
			window.smart_manager.sortColumns();
		}

		window.smart_manager.currentColModel.forEach(function(colObj){

			let hidden = ( 'undefined' !== typeof(colObj.hidden) ) ? colObj.hidden : true;

			if( false === hidden ) {
				if( false === colObj.hasOwnProperty('name_display') ) {// added for state management
					colObj.name_display = name;
				}
				let name = ( 'undefined' !== typeof(colObj.name) ) ? colObj.name.trim() : '';

				window.smart_manager.column_names[index] = colObj.name_display; //Array for column headers
				window.smart_manager.currentVisibleColumns[index] = colObj;

				index++;
			}
		});

		if ( "undefined" !== typeof (window.smart_manager.updateState) && "function" === typeof (window.smart_manager.updateState) ) {
			let params = { refreshDataModel : true, async: false };
			window.smart_manager.isColumnModelUpdated = true
			window.smart_manager.updateState(params); //refreshing the dashboard states
		}
	}
}

//Function to sort the columns in the current_col_model based on the 'position' key
Smart_Manager.prototype.sortColumns = function() {

	if( typeof window.smart_manager.currentColModel == 'undefined' ) {
		return;
	}

	window.smart_manager.indexPointer = 0;

	let enabledColumns = new Array(),
		disabledColumns = new Array();
		enabledColumnsFinal = new Array();

	window.smart_manager.currentColModel.forEach(function(colObj){
		enabled = 0;

		if( colObj.hasOwnProperty('position') != false && colObj.hasOwnProperty('hidden') != false ) {
			if( colObj.position != '' && colObj.hidden === false ) {
				enabledColumns[ colObj.position ] = colObj;
				enabled = 1;
			}
		}

		if( enabled == 0 ) {
			disabledColumns.push(colObj);
		}
	});

	enabledColumns.forEach(function(colObj){ //done this to re-index the array for proper array length
		enabledColumnsFinal.push(colObj);
	});

	enabledColumnsFinal.sort(function(a, b) {
		return parseInt(a.position) - parseInt(b.position);
	});

	window.smart_manager.currentColModel = enabledColumnsFinal.concat(disabledColumns);
}

//Function to get the seleted IDs
Smart_Manager.prototype.getSelectedKeyIds = function() {
	let idKey = window.smart_manager.getKeyID(),
		selectedIds = [];	
	window.smart_manager.selectedRows.forEach((rowId) => {
		selectedIds.push(window.smart_manager.currentDashboardData[rowId][idKey]);
	})

	return selectedIds;
}

//Function to show columns menu

Smart_Manager.prototype.isViewAuthor = function(viewSlug) {
				let params = {};
				params.data_type = 'json';
				params.data = {
								cmd: 'is_view_author',
								module: 'custom_views',
								active_module: viewSlug,
								security: window.smart_manager.sm_nonce,
								slug: viewSlug,
							};
				window.smart_manager.send_request(params, function(response){
					window.smart_manager.displayShowHideColumnSettings(response);
				});
}

//Function to delete records
Smart_Manager.prototype.deleteRecords = function() {

	if( window.smart_manager.selectedRows.length == 0 && !window.smart_manager.selectAll ) {
		return;
	}

	let params = {};
		params.data = {
						cmd: 'delete',
						active_module: window.smart_manager.dashboard_key,
						security: window.smart_manager.sm_nonce,
						ids: JSON.stringify(window.smart_manager.getSelectedKeyIds())
					};

	window.smart_manager.send_request(params, function(response) {
		if ( 'failed' !== response ) {
			if( jQuery('.sm_top_bar_action_btns #del_sm_editor_grid svg').hasClass('sm-ui-state-disabled') === false ) {
				jQuery('.sm_top_bar_action_btns #del_sm_editor_grid svg').addClass('sm-ui-state-disabled');
			}
			window.smart_manager.refresh();
			window.smart_manager.notification = {status:'success', message: response}
			window.smart_manager.showNotification()
		}
	});
}


Smart_Manager.prototype.updateLitePromoMessage = function( countRows ) {
	let count = parseInt( countRows );
	if( count >= 2 ) {
		jQuery('.sm_design_notice .sm_sub_headline.action').hide();
		jQuery('.sm_design_notice .sm_sub_headline.response').show();
	}
}

//Function to save inline edited data
Smart_Manager.prototype.saveData = function() {

	if( Object.getOwnPropertyNames(window.smart_manager.editedData).length <= 0 ) {
		return;
	}

	let params = {};
		params.data = {
						cmd: 'inline_update',
						active_module: window.smart_manager.dashboard_key,
						edited_data: JSON.stringify(window.smart_manager.editedData),
						security: window.smart_manager.sm_nonce,
						pro: ( ( typeof(window.smart_manager.sm_beta_pro) != 'undefined' ) ? window.smart_manager.sm_beta_pro : 0 ),
						table_model: (window.smart_manager.currentDashboardModel.hasOwnProperty('tables') ) ? window.smart_manager.currentDashboardModel.tables : ''
					};

	let hasInvalidClass = jQuery('.sm-grid-dirty-cell').hasClass('htInvalid');
	if ( hasInvalidClass == false ) {

		window.smart_manager.send_request(params, function(response) {

			if('failed' !== response){

				let title = 'success'
				if (window.smart_manager.isJSON( response )){
					// title = 'note'
					response = JSON.parse( response );
					msg = response.msg;
				} else{
					msg = response;
				}

				if( ( typeof(window.smart_manager.sm_beta_pro) == 'undefined' || ( typeof(window.smart_manager.sm_beta_pro) != 'undefined' && window.smart_manager.sm_beta_pro != 1 ) ) ) {
					
					if( typeof( response.sm_inline_update_count ) != 'undefined' ) {
						if ( typeof (window.smart_manager.updateLitePromoMessage) !== "undefined" && typeof (window.smart_manager.updateLitePromoMessage) === "function" ) {
							window.smart_manager.updateLitePromoMessage( response.sm_inline_update_count );
						}
					}
				}

				if( window.smart_manager.editedCellIds.length > 0 ) {
					for( let i=0; i<window.smart_manager.editedCellIds.length; i++ ) {
						
						colProp = window.smart_manager.hot.getCellMeta(window.smart_manager.editedCellIds[i].row, window.smart_manager.editedCellIds[i].col);
						currentClassName = ( colProp.hasOwnProperty('className') ) ? colProp.className : '';

						if( currentClassName.indexOf('sm-grid-dirty-cell') != -1 ) {
							currentClassName = currentClassName.substr(0, currentClassName.indexOf('sm-grid-dirty-cell'));
						}

						window.smart_manager.hot.setCellMeta(window.smart_manager.editedCellIds[i].row, window.smart_manager.editedCellIds[i].col, 'className', currentClassName);
						jQuery('.smCheckboxColumnModel input[data-row='+window.smart_manager.editedCellIds[i].row+']').parents('tr').removeClass('sm_edited');
					}


					// Code to get modified page nos.
					let modifiedPageNumbers = new Set()
					window.smart_manager.modifiedRows.map((rowNo) => {
						modifiedPageNumbers.add(Math.ceil((rowNo/window.smart_manager.limit)))
					})

					window.smart_manager.dirtyRowColIds = {};
					window.smart_manager.editedData = {};
					window.smart_manager.modifiedRows = new Array();

					modifiedPageNumbers.forEach(r => window.smart_manager.getData({refreshPage: r}));
				}
				window.smart_manager.hot.render();
				window.smart_manager.notification = {message: msg}
				if('success' === title){
					window.smart_manager.notification.status = title	
				}
				window.smart_manager.showNotification()
			}

		});
		
	} else {
		window.smart_manager.notification = {status:'error', message: _x('You have entered incorrect data in the highlighted cells.', 'notification', 'smart-manager-for-wp-e-commerce')}
		window.smart_manager.showNotification()
	}

}

// Function to handle all modal dialog
Smart_Manager.prototype.showModal = function(){
	if(window.smart_manager.modal.hasOwnProperty('title') && '' !== window.smart_manager.modal.title && window.smart_manager.modal.hasOwnProperty('content') && '' !== window.smart_manager.modal.content){
		window.smart_manager.showPannelDialog('#!/')
	}
}

// Function to handle all notification alerts
Smart_Manager.prototype.showNotification = function(){
	if(window.smart_manager.notification.hasOwnProperty('message') && '' !== window.smart_manager.notification.message){
		window.smart_manager.showPannelDialog('#!/')
	}
}

Smart_Manager.prototype.hideNotificationDialog = function() {
	jQuery( "#sm_inline_dialog" ).dialog("close");
}

//Function to show notification messages
Smart_Manager.prototype.showNotificationDialog = function( title = '', content = '', dlgparams = {} ) {

	window.smart_manager.modal = {
									title: ( title ) ? title : _x('Note', 'modal title', 'smart-manager-for-wp-e-commerce'),
									content: ( content ) ? content : sprintf(_x('This feature is available only in the %s version', 'modal content', 'smart-manager-for-wp-e-commerce'), '<a href="' + window.smart_manager.pricingPageURL + '" target="_blank">'+_x('Pro', 'modal content', 'smart-manager-for-wp-e-commerce')+'</a>'),
									autoHide: ( dlgparams.hasOwnProperty('autoHide') ) ? dlgparams.autoHide : false
								}
	window.smart_manager.showModal()	
}

//Function to show progress dialog
Smart_Manager.prototype.showProgressDialog = function( title = '' ) {

	window.smart_manager.modal = {
		title: ( title != '' ) ? title : _x('Please Wait', 'progressbar modal title', 'smart-manager-for-wp-e-commerce'),
		content: '<div class="sm_beta_background_update_progressbar"> <span class="sm_beta_background_update_progressbar_text" style="" >'+_x('Initializing...', 'progressbar modal content', 'smart-manager-for-wp-e-commerce')+'</span></div><div class="sm_beta_batch_update_background_link" >'+_x('Continue in background', 'progressbar modal content', 'smart-manager-for-wp-e-commerce')+'</div>',
		autoHide: false,
		showCloseIcon: false,
		cta: {}
	}
	window.smart_manager.showModal()
}

//Function to show confirm dialog
Smart_Manager.prototype.showConfirmDialog = function( params ) {

		window.smart_manager.modal = {
			title: ( params.hasOwnProperty('title') !== false && params.title != '' ) ? params.title : _x('Warning', 'modal title', 'smart-manager-for-wp-e-commerce'),
			content: ( params.hasOwnProperty('content') !== false && params.content != '' ) ? params.content : _x('Are you sure?', 'modal content', 'smart-manager-for-wp-e-commerce'),
			autoHide: false,
			cta: {
				title: ( (params.btnParams.hasOwnProperty('yesText')) ? params.btnParams.yesText : _x('Yes', 'button', 'smart-manager-for-wp-e-commerce') ),
				closeModalOnClick: (params.btnParams.hasOwnProperty('hideOnYes')) ? params.btnParams.hideOnYes : true,
				callback: function() {
					if( params.btnParams.hasOwnProperty('yesCallback') && typeof params.btnParams.yesCallback === "function" ) {
						if( params.btnParams.hasOwnProperty('yesCallbackParams') ) {
							params.btnParams.yesCallback( params.btnParams.yesCallbackParams );
						} else {
							params.btnParams.yesCallback();
						}
					}
				}
			},
			closeCTA: {
				title: ( (params.btnParams.hasOwnProperty('noText')) ? params.btnParams.noText : _x('No', 'button', 'smart-manager-for-wp-e-commerce') ),
				callback: function() {
					if( params.btnParams.hasOwnProperty('noCallback') && typeof params.btnParams.noCallback === "function" ) {
						params.btnParams.noCallback();
					}
				}
			},
		}
		window.smart_manager.showModal()
}

Smart_Manager.prototype.getCurrentDashboardState = function() {
	let tempDashModel = JSON.parse(JSON.stringify(window.smart_manager.currentDashboardModel));
	let tempColModel = JSON.parse(JSON.stringify(window.smart_manager.currentColModel));
	
	if(!Array.isArray(tempColModel)) {
		tempColModel = []
	}

	tempDashModel.columns = new Array();
	tempColModel.forEach(function(colObj) {
		if( typeof(colObj.hidden) != 'undefined' && colObj.hidden === false ) {
			tempDashModel.columns.push(colObj);
		}
	});
	let dashboardState = {'columns': tempDashModel.columns, 'sort_params': tempDashModel.sort_params};
	let viewSlug = window.smart_manager.getViewSlug(window.smart_manager.dashboardName);
	if(viewSlug){
		dashboardState['search_params'] = {
			'isAdvanceSearch': ((window.smart_manager.advancedSearchQuery.length > 0) ? 'true' : 'false'),
			'params': ((window.smart_manager.advancedSearchQuery.length > 0) ? window.smart_manager.advancedSearchQuery : window.smart_manager.simpleSearchText)
		}
	}
	return JSON.stringify(dashboardState);
}

Smart_Manager.prototype.refreshDashboardStates = function() {
	window.smart_manager.dashboardStates[window.smart_manager.dashboard_key] = window.smart_manager.getCurrentDashboardState();
}

//Function to handle the state apply at regular intervals
Smart_Manager.prototype.updateState = function(refreshParams) {

	let viewSlug = window.smart_manager.getViewSlug(window.smart_manager.dashboardName);

	// do not refresh the states if view
	if ( typeof (window.smart_manager.refreshDashboardStates) !== "undefined" && typeof (window.smart_manager.refreshDashboardStates) === "function" ) {
		window.smart_manager.refreshDashboardStates(); //refreshing the dashboard states
	}

	if( Object.getOwnPropertyNames(window.smart_manager.dashboardStates).length <= 0 ) {
		return;
	}

	//Ajax request to update the dashboard states
	let params = {};
		params.data_type = 'json';
		params.data = {
						cmd: 'save_state',
						security: window.smart_manager.sm_nonce,
						active_module: window.smart_manager.dashboard_key,
						dashboard_states: window.smart_manager.dashboardStates
					};
		// Code for passing extra param for view handling
		if( window.smart_manager.sm_beta_pro == 1 ) {
			params.data['is_view'] = 0;

			if(viewSlug){
				params.data['is_view'] = 1;
				params.data['active_module'] = viewSlug;
			}

			// Flag for handling taxonomy dashboards
			params.data['is_taxonomy'] = window.smart_manager.isTaxonomyDashboard();
		}
	
		params.showLoader = false;

		if( refreshParams ) {
			if( typeof refreshParams.async != 'undefined' ) {
				params.async = refreshParams.async;
			}
		}

	window.smart_manager.send_request(params, function(refreshParams, response) {
			window.smart_manager.dashboardStates = {};
			if( refreshParams ) {
				if( typeof refreshParams.refreshDataModel != 'undefined' ) {
					window.smart_manager.refresh();
				}
			}
	}, refreshParams);
}

// Function to determine if the selected dashhboard is a taxonomy dashboard or not
Smart_Manager.prototype.isTaxonomyDashboard = function() {
	let viewSlug = window.smart_manager.getViewSlug(window.smart_manager.dashboardName);
	return (window.smart_manager.allTaxonomyDashboards[(window.smart_manager.viewPostTypes.hasOwnProperty(viewSlug)) ? window.smart_manager.viewPostTypes[viewSlug] : window.smart_manager.dashboard_key]) ? 1: 0
}

// Function to get keyId for the dashboard
Smart_Manager.prototype.getKeyID = function() {
	switch (true){
        case ('undefined' !== typeof window.smart_manager.taxonomyDashboards[window.smart_manager.dashboard_key]):
			return 'terms_term_id'
		case ('user' === window.smart_manager.dashboard_key):
			return 'users_id' 
		default:
			return 'posts_id';
	}
}

if(typeof window.smart_manager === 'undefined'){
	window.smart_manager = new Smart_Manager();
}

//Events to be handled on document ready
jQuery(document).ready(function() {
		window.smart_manager.init();
});

jQuery.widget('ui.dialog', jQuery.extend({}, jQuery.ui.dialog.prototype, { 
        _title: function(title) { 
            let $title = this.options.title || '&nbsp;' 
            if( ('titleIsHtml' in this.options) && this.options.titleIsHtml == true ) 
                title.html($title); 
            else title.text($title); 
        } 
}));

//Code for custom rendrers and extending Handsontable
(function(Handsontable){
	  let defaultTextEditor = Handsontable.editors.TextEditor.prototype.extend();

	//Function to override the SelectEditor function to handle color codes
    Handsontable.editors.SelectEditor.prototype.prepare = function () {
      	
      	// Call the original prepare method
      	Handsontable.editors.BaseEditor.prototype.prepare.apply(this, arguments);

      	let _this2 = this,
      		selectOptions = this.cellProperties.selectOptions,
      		colorCodes = ( typeof(this.cellProperties.colorCodes) != 'undefined' ) ? this.cellProperties.colorCodes : '',
      		options = '';
		
			if (typeof selectOptions === 'function') {
				options = this.prepareOptions(selectOptions(this.row, this.col, this.prop));
			} else {
		    	options = this.prepareOptions(selectOptions);
		  	}

	      	this.select.innerHTML = '';

	      	Object.entries(options).forEach(([key, value]) => {
				let optionElement = document.createElement('OPTION');
					optionElement.value = key;

				if( colorCodes != ''  ) {
					for( let color in colorCodes ) {
						if( colorCodes[color].indexOf(key) != -1 ) {
							optionElement.className = 'sm_beta_select_'+color;
							break;		
						}
					}
				}

				optionElement.innerHTML = value;
				_this2.select.appendChild(optionElement);	
			});
	};

	Smart_Manager.prototype.dateEditor = function( currObj, arguments, format = 'Y-m-d H:i:s', placeholder = 'YYYY-MM-DD HH:MM:SS' ) {
      // Call the original createElements method
      Handsontable.editors.TextEditor.prototype.createElements.apply(currObj, arguments);

      // Create datepicker input and update relevant properties
      currObj.TEXTAREA = document.createElement('input');
      currObj.TEXTAREA.setAttribute('type', 'text');
      currObj.TEXTAREA.className = 'htDateTimeEditor';
      currObj.textareaStyle = currObj.TEXTAREA.style;
      currObj.textareaStyle.width = 0;
      currObj.textareaStyle.height = 0;

      // Replace textarea with datepicker
      Handsontable.dom.empty(currObj.TEXTAREA_PARENT);
      currObj.TEXTAREA_PARENT.appendChild(currObj.TEXTAREA);

        jQuery('.htDateTimeEditor')
			.Zebra_DatePicker({ format: format,
                                    show_icon: false,
                                    show_select_today: false,
                                    default_position: 'below',
									readonly_element: false,
                                })
			.attr('placeholder',placeholder);
    };

	function customNumericTextEditor(query, callback) {
	    // ...your custom logic of the validator

	    RegExp.escape= function(s) {
		    return s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
		}; 

	    let regx = new RegExp("^[0-9]*"+ RegExp.escape(window.smart_manager.wooPriceDecimalSeparator) +"?[0-9]*$");
	    
	    if (regx.test(query)) {
	      callback(true);
	    } else {
	      callback(false);
	    }
	  }

	  function customPhoneTextEditor(value, callback) {
	    // ...your custom logic of the validator
	   	if (/^(\d|\-|\+|\.|\(|\)|\ )*$/.test(value)) { 
        	callback(true);
      	} else {
        	callback(false);
      	}
	  }

	  // Register an alias
	  Handsontable.validators.registerValidator('customNumericTextEditor', customNumericTextEditor);
	  Handsontable.validators.registerValidator('customPhoneTextEditor', customPhoneTextEditor);


	  let dateTimeEditor = Handsontable.editors.TextEditor.prototype.extend(),
	  		dateEditor = Handsontable.editors.TextEditor.prototype.extend(),
	  		timeEditor = Handsontable.editors.TextEditor.prototype.extend();



        dateTimeEditor.prototype.createElements = function() { window.smart_manager.dateEditor( this, arguments ) };
        dateEditor.prototype.createElements = function() { window.smart_manager.dateEditor( this, arguments, 'Y-m-d', 'YYYY-MM-DD' ) };
        timeEditor.prototype.createElements = function() { window.smart_manager.dateEditor( this, arguments, 'H:i', 'HH:MM' ) };

        function numericRenderer(hotInstance, td, row, col, prop, value, cellProperties) {
		    Handsontable.renderers.NumericRenderer.apply(this, arguments);

		    let colObj = ( window.smart_manager.currentVisibleColumns.indexOf(col) != -1 ) ? window.smart_manager.currentVisibleColumns[col] : {};

		    if( !value && '' === value && null === value ) {
		    	value = parseFloat(value);
		    	value = ( colObj.hasOwnProperty('decimalPlaces') ) ? value.toFixed( parseInt( colObj.decimalPlaces ) ) : value;
		    }

		    if(!value || value === '' || value == null || value === 0 || value === 0.00 || value === '0' || value === '0.00' ) {
		        td.innerHTML = '<div class="wrapper htRight htNumeric htNoWrap">' + value + '</div>';
		    } else {
		    	td.innerHTML = '<div title="'+ td.innerHTML +'" class="wrapper">' + td.innerHTML + '</div>';
		    }

			// Code for handling colorCodes highlighting for the cells
			let colorCodes = ( typeof(cellProperties.colorCodes) != 'undefined' ) ? cellProperties.colorCodes : '';

			if( value !== '' && value != null ) {

				if( colorCodes != '' ) {					
					for( let color in colorCodes ) {
						
						let min = (colorCodes[color].hasOwnProperty('min')) ? colorCodes[color]['min'] : -1,
							max = (colorCodes[color].hasOwnProperty('max')) ? colorCodes[color]['max'] : -1
						
						if(min < 0 && max < 0){
							continue;
						}

						let v = parseFloat(value);

						if(isNaN(v)){
							continue;
						}

						if( ((min < 0 || max < 0) && ((min >= 0 && v >= min) || (max >= 0 && v <= max)))
							|| ((min >= 0 && max >= 0) && (v >= min) && (v <= max)) ){
							td.classList.add(...['sm_beta_select_'+color, 'sm_font_bold'])
							break;
						}
					}
				}
			}

		    return td;
		}
	  	Handsontable.renderers.registerRenderer('numericRenderer', numericRenderer);

	  	function customTextRenderer(hotInstance, td, row, col, prop, value, cellProperties) {
		    Handsontable.renderers.TextRenderer.apply(this, arguments);
		    td.innerHTML = '<div title="'+ td.innerHTML +'" class="wrapper">' + td.innerHTML + '</div>';

		    return td;
		}
	  	Handsontable.renderers.registerRenderer('customTextRenderer', customTextRenderer);

	  	function customHtmlRenderer(hotInstance, td, row, col, prop, value, cellProperties) {
			Handsontable.renderers.HtmlRenderer.apply(this, arguments);
			td.innerHTML = '<div title="'+ td.innerText +'" class="wrapper">' + td.innerHTML + '</div>';
		    
		    return td;
		}
	  	Handsontable.renderers.registerRenderer('customHtmlRenderer', customHtmlRenderer);

	  	function customCheckboxRenderer(hotInstance, td, row, col, prop, value, cellProperties) {

		    Handsontable.renderers.CheckboxRenderer.apply(this, arguments);
		    td.innerHTML = '<div class="wrapper">' + td.innerHTML + '</div>';
		    
		    return td;
		}
	  	Handsontable.renderers.registerRenderer('customCheckboxRenderer', customCheckboxRenderer);

	  	function customPasswordRenderer(hotInstance, td, row, col, prop, value, cellProperties) {

		    Handsontable.renderers.PasswordRenderer.apply(this, arguments);
		    td.innerHTML = '<div class="wrapper">' + td.innerHTML + '</div>';
		    
		    return td;
		}
	  	Handsontable.renderers.registerRenderer('customPasswordRenderer', customPasswordRenderer);

      function datetimeRenderer(hotInstance, td, row, column, prop, value, cellProperties) {
        if( typeof(cellProperties.className) != 'undefined' ) { //code to higlight the cell on selection
            td.setAttribute('class',cellProperties.className);
        }

        td.innerHTML = value;

        td.innerHTML = '<div class="wrapper">' + td.innerHTML + '</div>';

        return td;
      }

		function longstringRenderer(hotInstance, td, row, column, prop, value, cellProperties) {
			Handsontable.renderers.HtmlRenderer.apply(this, arguments);
			if( typeof(cellProperties.className) != 'undefined' ) { //code to higlight the cell on selection
				td.setAttribute('class',cellProperties.className);
			}

			td.innerHTML = '<div title="'+ td.innerText +'" class="wrapper">' + td.innerHTML + '</div>';

			return td;
		}

		function selectValueRenderer(hotInstance, td, row, column, prop, value, cellProperties) {
			let source = cellProperties.selectOptions || {},
				className = ( typeof(cellProperties.className) != 'undefined' ) ? cellProperties.className : '',
				colorCodes = ( typeof(cellProperties.colorCodes) != 'undefined' ) ? cellProperties.colorCodes : '';

			// if( className != '' ) { //code to higlight the cell on selection
			// 	td.setAttribute('class',className);
			// }

			if( typeof source != 'undefined' && typeof value != 'undefined' && source.hasOwnProperty(value) ) {
				td.setAttribute('data-value',value);

				if( colorCodes != '' ) {					
					for( let color in colorCodes ) {
						if( colorCodes[color].indexOf(value) != -1 ) {
							// className = (( className != '' ) ? className + ' ' : '') + 'sm_beta_select_'+color;
							// td.setAttribute('class',className);
							td.classList.add('sm_beta_select_'+color)
							break;		
						}
					}
				}
				td.innerHTML = source[value];
			}

			td.innerHTML = '<div title="'+ td.innerText +'" class="wrapper">' + td.innerHTML + '</div>';

			return td;
		}

		function multilistRenderer(hotInstance, td, row, column, prop, value, cellProperties) {
		// ...renderer logic
			Handsontable.renderers.TextRenderer.apply(this, arguments);
			if( typeof(cellProperties.className) != 'undefined' ) { //code to higlight the cell on selection
				td.setAttribute('class',cellProperties.className);
			}
			
			td.innerHTML = '<div class="wrapper" style="line-height:30px;">' + td.innerHTML + '</div>';

			return td;
		}
		
	  Handsontable.renderers.registerRenderer('selectValueRenderer', selectValueRenderer);

	  	function select2Renderer(instance, td, row, col, prop, value, cellProperties) {

		    let selectedId;
		    let optionsList = (cellProperties.select2Options.data) ? cellProperties.select2Options.data : [];
		    let dynamicSelect2 = ( cellProperties.select2Options.hasOwnProperty('loadDataDynamically') ) ? true : false;
	  		
		    if( (typeof optionsList === "undefined" || typeof optionsList.length === "undefined" || !optionsList.length) && !dynamicSelect2 ) {
		        Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
		        return td;
		    }

		    if( dynamicSelect2 && typeof(value) == 'object' ) {
				jQuery(td).attr('data-value', JSON.stringify(value));

		    	let values = ( value ) ? value : [];

		    	value = [];
		    	var text = '';
		    	values.forEach(function(obj) {
		    		if( obj.text ) {
						value.push(obj.text.trim());
		    		}
				});

		    } else {
		    	let values = (value + "").split(",");

			    value = [];
			    for (let index = 0; index < optionsList.length; index++) {

			        if (values.indexOf(optionsList[index].id + "") > -1) {
			            selectedId = optionsList[index].id;
			            value.push(optionsList[index].text);
			        }
			    }	
		    }
		    
		    value = value.join(", ");

		    Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);

			td.innerHTML = '<div class="wrapper">' + td.innerHTML + '</div>';
		    return td;
		}
	  	Handsontable.renderers.registerRenderer('select2Renderer', select2Renderer);

		function generateImageHtml(params = {}){
			if(!params.td || !params.value || !params.cellProperties || !params.currentInstance){
				return
			}

			let escaped = Handsontable.helper.stringify(params.value),
				img,
				className = ((params.className) ? (params.className + ' ') : '') + 'sm_image_thumbnail';
			if (escaped.indexOf('http') === 0) {
				img = document.createElement('IMG');
				img.src = params.value;
				img.width = 30;
				img.height = 30;

				img.setAttribute('class',className);

				Handsontable.dom.addEvent(img, 'mousedown', function (e){
					e.preventDefault(); // prevent selection quirk
				});

				// Handsontable.dom.empty(td);
				params.td.appendChild(img);
			}
			else {
				// render as text
				Handsontable.renderers.TextRenderer.apply(params.currentInstance, arguments);
			}

			if( typeof(params.cellProperties.className) != 'undefined' ) {
					className += ' '+ params.cellProperties.className;
					params.td.setAttribute('class',params.cellProperties.className);
			}

			return params.td
		}

	  function imageRenderer(hotInstance, td, row, column, prop, value, cellProperties) {
			try{
				value = (!value || value == 0) ? window.smart_manager.defaultImagePlaceholder : value
				Handsontable.dom.empty(td);
				td = generateImageHtml({
					td: td,
					value: value,
					cellProperties: cellProperties,
					currentInstance: this
				})
				td.innerHTML = '<div class="wrapper">' + td.innerHTML + '</div>';
			}catch(e){
				console.log('imageRenderer:: ', e)
			}
		  	return td;
	  }

	  function multipleImageRenderer(hotInstance, td, row, column, prop, value, cellProperties) {
		try{
			value = (!Array.isArray(value)) ? [] : value;
			value = (value.length === 0) ? [{id:0, val:window.smart_manager.defaultImagePlaceholder}] : value
			Handsontable.dom.empty(td);
			value.map((obj) => {
				td = generateImageHtml({
					td: td,
					value: obj.val || '',
					cellProperties: cellProperties,
					currentInstance: this
				})
			})
			td.innerHTML = '<div class="wrapper">' + td.innerHTML + '</div>';
		}catch(e){
			console.log('multipleImageRenderer:: ', e)
		}
		return td;
  }

	  // Register an alias for datetime
	  Handsontable.cellTypes.registerCellType('sm.datetime', {
        editor: dateTimeEditor,
        renderer: datetimeRenderer,
        allowInvalid: true,
      });

	  // Register an alias for date
      Handsontable.cellTypes.registerCellType('sm.date', {
        editor: dateEditor,
        renderer: datetimeRenderer,
        allowInvalid: true,
      });

      // Register an alias for time
      Handsontable.cellTypes.registerCellType('sm.time', {
        editor: timeEditor,
        renderer: datetimeRenderer,
        allowInvalid: true,
      });

	  // Register an alias for image
	  Handsontable.cellTypes.registerCellType('sm.image', {
		renderer: imageRenderer,
		allowInvalid: true,
	  });

	  // Register an alias for multiple gallery images
	  Handsontable.cellTypes.registerCellType('sm.multipleImage', {
		// renderer: Handsontable.renderers.HtmlRenderer,
		renderer: multipleImageRenderer,
		allowInvalid: true,
	  });

	  // Register an alias for longstrings
	  Handsontable.cellTypes.registerCellType('sm.longstring', {
		editor: defaultTextEditor,
		renderer: multilistRenderer,
		allowInvalid: true,
	  });

	  // Register an alias for serialized
	  Handsontable.cellTypes.registerCellType('sm.serialized', {
		editor: defaultTextEditor,
		renderer: multilistRenderer,
		allowInvalid: true,
	  });

	// Register an alias for multilist
	  Handsontable.cellTypes.registerCellType('sm.multilist', {
		editor: defaultTextEditor,
		renderer: multilistRenderer,
		allowInvalid: true,
	  });

})(Handsontable);
