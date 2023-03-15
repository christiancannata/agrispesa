function Smart_Manager_Product() {
    if(typeof Smart_Manager_Pro != 'undefined') {
    	Smart_Manager_Pro.apply();
    }
    else {
    	Smart_Manager.apply();
    }
}
Smart_Manager_Product.prototype = (typeof Smart_Manager_Pro != 'undefined') ? Object.create(Smart_Manager_Pro.prototype) : Object.create(Smart_Manager.prototype);
Smart_Manager_Product.prototype.constructor = Smart_Manager_Product;
    
Smart_Manager_Product.prototype.refreshDashboardStates = function() {
    Smart_Manager.prototype.refreshDashboardStates.apply(this);
    if( window.smart_manager.dashboard_key == 'product' ) {
    	let tempDashModel = JSON.parse(JSON.stringify(window.smart_manager.currentDashboardModel));
    	window.smart_manager.dashboardStates[window.smart_manager.dashboard_key] = JSON.parse( window.smart_manager.dashboardStates[window.smart_manager.dashboard_key] );
        window.smart_manager.dashboardStates[window.smart_manager.dashboard_key]['treegrid'] = ( tempDashModel.hasOwnProperty('treegrid') ) ? tempDashModel.treegrid : false;
        window.smart_manager.dashboardStates[window.smart_manager.dashboard_key] = JSON.stringify( window.smart_manager.dashboardStates[window.smart_manager.dashboard_key] );
    }
}


if(typeof window.smart_manager_product === 'undefined'){
    window.smart_manager = new Smart_Manager_Product();
}


jQuery(document).on('sm_dashboard_change', '#sm_editor_grid', function() {
	
	//Code to hide the 'show variations' checkbox
	if( window.smart_manager.dashboard_key != 'product' ) {
		if( jQuery(".sm_top_bar_action_btns:nth-last-child(2)").find('#sm_products_show_variations_span').length > 0 ) {
			jQuery(".sm_top_bar_action_btns:nth-last-child(2) #sm_products_show_variations_span").remove();
		}
		return;
	}
})

.on('smart_manager_init', '#sm_editor_grid', function() { //For add row functionality

	if (typeof( window.smart_manager.defaultColumnsAddRow ) == 'undefined') {
		return;
	}

	window.smart_manager.defaultColumnsAddRow.push('terms_product_type');
})

.on('smart_manager_post_load_grid','#sm_editor_grid', function() {
	if( window.smart_manager.dashboard_key == 'product' ) {
		window.smart_manager.excludedEditedFieldKeys = ['postmeta/meta_key=_product_attributes/meta_value=_product_attributes'];
		//Call to function for 'show variations' checkbox
		window.smart_manager.showVariationsHtml();

		// ,'posts_post_status'
		let variationsDisabledColumns = new Array('posts_post_title','posts_post_date','posts_post_date_gmt','posts_post_modified','posts_post_modified_gmt','posts_post_content','posts_post_excerpt','posts_post_password','terms_product_cat','postmeta_meta_key__default_attributes_meta_value__default_attributes','custom_product_attributes','terms_product_type','terms_product_visibility','terms_product_visibility_featured','postmeta_meta_key__wc_mmax_prd_opt_enable_meta_value__wc_mmax_prd_opt_enable','postmeta_meta_key__wc_mmax_min_meta_value__wc_mmax_min','postmeta_meta_key__wc_mmax_max_meta_value__wc_mmax_max','postmeta_meta_key_allow_combination_meta_value_allow_combination','postmeta_meta_key_minimum_allowed_quantity_meta_value_minimum_allowed_quantity','postmeta_meta_key_maximum_allowed_quantity_meta_value_maximum_allowed_quantity','postmeta_meta_key_group_of_quantity_meta_value_group_of_quantity','postmeta_meta_key_min_quantity_meta_value_min_quantity','postmeta_meta_key_max_quantity_meta_value_max_quantity','postmeta_meta_key_minmax_do_not_count_meta_value_minmax_do_not_count','postmeta_meta_key_minmax_cart_exclude_meta_value_minmax_cart_exclude','postmeta_meta_key_minmax_category_group_of_exclude_meta_value_minmax_category_group_of_exclude');
		let parentDisabledColumns = new Array('postmeta_meta_key_min_max_rules_meta_value_min_max_rules','postmeta_meta_key_variation_minimum_allowed_quantity_meta_value_variation_minimum_allowed_quantity','postmeta_meta_key_variation_maximum_allowed_quantity_meta_value_variation_maximum_allowed_quantity','postmeta_meta_key_variation_group_of_quantity_meta_value_variation_group_of_quantity','postmeta_meta_key_min_quantity_var_meta_value_min_quantity_var','postmeta_meta_key_max_quantity_var_meta_value_max_quantity_var','postmeta_meta_key_variation_minmax_do_not_count_meta_value_variation_minmax_do_not_count','postmeta_meta_key_variation_minmax_cart_exclude_meta_value_variation_minmax_cart_exclude','postmeta_meta_key_variation_minmax_category_group_of_exclude_meta_value_variation_minmax_category_group_of_exclude');

		window.smart_manager.hot.updateSettings({
			cells: function(row, col, prop){
				
				let cellProperties = {},
					isLeaf = window.smart_manager.hot.getDataAtRowProp(row, 'isLeaf'),
					isVariation = window.smart_manager.hot.getDataAtRowProp(row, 'posts_post_parent'),
				 	colObj = ( ( window.smart_manager.currentVisibleColumns.indexOf(col) != -1 ) ? window.smart_manager.currentVisibleColumns[col] : {} ),
				 	nonNumericRenderCols = new Array('postmeta_meta_key__sale_price_meta_value__sale_price', 'postmeta_meta_key__regular_price_meta_value__regular_price');

				let customRenderer = window.smart_manager.getCustomRenderer( col );

				isVariation = ( isVariation ) ? parseInt(isVariation) : 0;

				if( customRenderer != '' ) {
					cellProperties.renderer = customRenderer;
				}

				if( colObj.hasOwnProperty('type') ) {
					if( nonNumericRenderCols.indexOf(prop) != -1 ) {
						cellProperties.renderer = 'customTextRenderer';
					}
				}

				if( isVariation > 0 && variationsDisabledColumns.indexOf(prop) != -1 ) {
					cellProperties.readOnly = 'true';
					
					if( prop === 'posts_post_title' && true === isLeaf ) {
						cellProperties.renderer = 'customHtmlRenderer';
					}

					if( 'terms_product_type' === prop || 'terms_product_visibility_featured' === prop ) {
						cellProperties.renderer = 'customTextRenderer';
						cellProperties.readOnly = 'true';
					}
				}

				if( 0 === isVariation && ( parentDisabledColumns.indexOf(prop) != -1 || (prop && prop.includes('postmeta_meta_key_attribute_pa_') ) ) ) {
					cellProperties.renderer = 'customTextRenderer';
					cellProperties.readOnly = 'true';
				}

				if( Object.entries(cellProperties).length !== 0 ) {
					return cellProperties;
				}
				
			}
		});
	}
})

.on('sm_grid_on_afterOnCellMouseUp','#sm_editor_grid', function(e, params) { //for handling attribute inline edit

	if( typeof( params.colObj.prop ) == 'undefined' || (typeof( params.colObj.prop ) != 'undefined' && params.colObj.prop != 'custom_product_attributes') || window.smart_manager.dashboard_key != 'product' ) {
		return;
	}


	window.smart_manager.defaultEditor = false;
	window.smart_manager.prodIsVariation = false;
	window.smart_manager.prodAttrDisplayIndex = 0;
	window.smart_manager.prodAttributeActualValues = ( typeof( params.colObj.values ) != 'undefined' ) ? params.colObj.values : '';

	let attributeList = '<option value="custom">'+_x('Custom product attribute', 'WooCommerce product attributes list', 'smart-manager-for-wp-e-commerce')+'</option>',
		attrSelectedList = '',
		dlgTitle = '',
		dlgContent = '',
		isVariation = false,
		selectedAttributes = new Array();

	//Code for setting is_variation flag
	if( typeof( window.smart_manager.currentColModel ) != 'undefined' ) {
		window.smart_manager.currentColModel.forEach(function(value) {
			if( value.hasOwnProperty('data') && value.hasOwnProperty('selectOptions') && value.data == 'terms_product_type' ) {
				window.smart_manager.productTypeValues = value.selectOptions;
			}	
		});

		let prodTypeId = window.smart_manager.hot.getDataAtRowProp( params.coords.row, 'terms_product_type' );
		if( prodTypeId != '' && window.smart_manager.productTypeValues.hasOwnProperty(prodTypeId) ) {
			if( window.smart_manager.productTypeValues[prodTypeId] == 'Variable' || window.smart_manager.productTypeValues[prodTypeId] == 'Variable Subscription' ) {
				isVariation = true;
			}
		}
	}

	let productAttributesSerialized = window.smart_manager.hot.getDataAtRowProp(params.coords.row, 'postmeta_meta_key__product_attributes_meta_value__product_attributes'); 
		productAttributesSerialized = ( window.smart_manager.isJSON( productAttributesSerialized ) ) ? JSON.parse( productAttributesSerialized ) : '';
	if( productAttributesSerialized && typeof( productAttributesSerialized ) === 'object' ) {

		Object.entries(productAttributesSerialized).forEach(([key, obj]) => {
			let isTaxonomy = ( obj.hasOwnProperty('is_taxonomy') ) ? obj.is_taxonomy : '';

			selectedAttributes.push(key);

			//Code for defined attributes
			if( isTaxonomy == 1 ) {
				attrLabel = ( typeof( window.smart_manager.prodAttributeActualValues[key] ) != 'undefined' && window.smart_manager.prodAttributeActualValues[key].hasOwnProperty('lbl') ) ? window.smart_manager.prodAttributeActualValues[key].lbl : '';
				attrType = ( typeof( window.smart_manager.prodAttributeActualValues[key] ) != 'undefined' && window.smart_manager.prodAttributeActualValues[key].hasOwnProperty('type') ) ? window.smart_manager.prodAttributeActualValues[key].type : '';
				attrValue = ( typeof( window.smart_manager.prodAttributeActualValues[key] ) != 'undefined' && window.smart_manager.prodAttributeActualValues[key].hasOwnProperty('val') ) ? window.smart_manager.prodAttributeActualValues[key].val : '';

				if( attrType == 'text' ) {
					if( obj.hasOwnProperty('value') ) {
						let values = Object.values(obj.value);
						if( values.length > 0 ) {
							attrValue = values.reduce(( acc, cur ) => { return acc + cur.trim() + ' | '; });
						}
					}
				}

			} else if (isTaxonomy == 0) {
				attrLabel = ( obj.hasOwnProperty('name') ) ? obj.name : '';
				attrType = 'text';
				attrValue = ( obj.hasOwnProperty('value') ) ? obj.value : '';
			}

			let attrVisibilityFlag = ( ( obj.hasOwnProperty('is_visible') && obj.is_visible == 1 ) ? 'checked' : '' ),
				attrVariationFlag = ( ( obj.hasOwnProperty('is_variation') && obj.is_variation == 1 ) ? 'checked' : '' ),
				attrPosition = ( ( obj.hasOwnProperty('position') ) ? obj.position : '' ),
				attrChkboxList = '';					

			attrChkboxList += '<tr> <td> <input type="checkbox" id="attribute_visibility_'+key+'" name="attribute_visibility['+window.smart_manager.prodAttrDisplayIndex+']" '+attrVisibilityFlag+'>'+_x('Visible on the product page', 'visibility option for WooCommerce product attributes', 'smart-manager-for-wp-e-commerce')+'</td> </tr>';
			attrChkboxList += '<tr> <td> <input type="checkbox" id="attribute_variation_'+key+'" name="attribute_variation['+window.smart_manager.prodAttrDisplayIndex+']" '+attrVariationFlag+'>'+_x('Used for variations', 'use for variations option for WooCommerce product attributes', 'smart-manager-for-wp-e-commerce')+'</td> </tr>';
			attrChkboxList += '<tr> <td> <label>'+_x('Position:', 'position checkbox for WooCommerce product attributes', 'smart-manager-for-wp-e-commerce')+'</label> <input type="number" style="width:23% !important;" name="attribute_position['+window.smart_manager.prodAttrDisplayIndex+']" value="'+attrPosition+'">';
			attrChkboxList += '<input type="hidden" name="attribute_taxonomy['+window.smart_manager.prodAttrDisplayIndex+']" value='+isTaxonomy+'> </td> </tr>';

			if (isTaxonomy == 1) {
				attrSelectedList += '<tr> <td> <label style="font-weight: bold;"> '+attrLabel+': </label> </td>';
				if( "text" === attrType ) {
					attrSelectedList += '<td rowspan="4"> <input type="text" id="'+attrLabel+'" name="attribute_values['+window.smart_manager.prodAttrDisplayIndex+']" value="'+attrValue+'" placeholder="'+_x('Pipe (|) separate terms', 'placeholder', 'smart-manager-for-wp-e-commerce')+'" /> </ td>';
				} else {
					attrSelectedList += '<td rowspan="4"> <select id="'+key+'" multiple="multiple" data-placeholder="'+_x('Select terms', 'placeholder', 'smart-manager-for-wp-e-commerce')+'" name="attribute_values['+window.smart_manager.prodAttrDisplayIndex+'][]" class="multiselect">';
					
					if( attrValue != '' ) {
						Object.entries(attrValue).forEach(([key, value]) => {
							attrSelectedList += ( obj.hasOwnProperty('value') && obj.value.hasOwnProperty(key) ) ? '<option value="'+ key +'" selected>'+ value +'</option>' : '<option value="'+ key +'">'+ value +'</option>';
						});
					}
					attrSelectedList += '</select> <br />';
					attrSelectedList += '<button class="button select_all_attributes" style="margin-right: 1em;">'+_x('Select all', 'button for selecting WooCommerce product attribute', 'smart-manager-for-wp-e-commerce')+'</button> ';
					attrSelectedList += '<button class="button select_no_attributes">'+_x('Select none', 'button for selecting WooCommerce product attribute', 'smart-manager-for-wp-e-commerce')+'</button> </td>';
				}
				attrSelectedList += '<td> <input type="hidden" name="attribute_names['+window.smart_manager.prodAttrDisplayIndex+']" index="'+window.smart_manager.prodAttrDisplayIndex+'" value="'+key+'" /></td>';
			} else if (isTaxonomy == 0) {
				attrSelectedList += '<tr> <td> <input type="text" name="attribute_names['+window.smart_manager.prodAttrDisplayIndex+']" index="'+window.smart_manager.prodAttrDisplayIndex+'" placeholder="'+_x('Name', 'placeholder', 'smart-manager-for-wp-e-commerce')+'" value="'+attrLabel+'"> </td>';
				attrSelectedList += '<td rowspan="4"> <input type="text" id="'+attrLabel+'" name="attribute_values['+window.smart_manager.prodAttrDisplayIndex+']" value="'+attrValue+'" placeholder="'+_x('Pipe (|) separate terms', 'placeholder', 'smart-manager-for-wp-e-commerce')+'" /> </td>';
			}
			attrSelectedList += '</tr>';
			attrSelectedList += attrChkboxList;

			window.smart_manager.prodAttrDisplayIndex++;
		});
	}

	Object.entries(params.colObj.values).forEach(([key, value]) => {
		let disabled = ( selectedAttributes.indexOf( key ) != -1 ) ? 'disabled' : '';
	  	attributeList += '<option value="'+key+'" '+ disabled +' >'+value.lbl+'</option>';
	});

	dlgContent += '<div id="edit_product_attributes">'+
						'<input type="hidden" name="isVariation" value="'+ ( ( isVariation ) ? 1 : 0 ) +'">'+
						'<table id= "table_edit_attributes" width="102%">'+
							attrSelectedList +
						'</table>'+
						'<div id="edit_attributes_toolbar">'+
							'<button type="button" class= "button button-primary" id="edit_attributes_add" style="float:right;">'+_x('Add', 'add attribute button for WooCommerce products', 'smart-manager-for-wp-e-commerce')+'</button>'+
							'<select id="edit_attributes_taxonomy_list" style="float: right; margin-right: 1em;">'+attributeList+'</select>'+
						'</div>'+
					'</div>';

		let initializeChosen = function() {
			jQuery("select.multiselect").chosen();
			jQuery(".chosen-container-multi").css({'width': '250px !important', 'margin-bottom': '7px'});

			//Code for select all and none attributes
			jQuery(document)
			.off('click', 'button.select_all_attributes').on('click', 'button.select_all_attributes', function(){
				jQuery(this).closest('td').find('select option').attr("selected","selected");
				jQuery(this).closest('td').find('select').trigger("chosen:updated");
				return false;
			})

			.off('click', 'button.select_no_attributes').on('click', 'button.select_no_attributes', function(){
				jQuery(this).closest('td').find('select option').removeAttr("selected");
				jQuery(this).closest('td').find('select').trigger("chosen:updated");
				return false;
			});
		}

		window.smart_manager.modal = {
			title: _x('Attribute', 'modal title', 'smart-manager-for-wp-e-commerce'),
			content: dlgContent,
			autoHide: false,
			width: 'w-2/6',
			cta: {
				title: _x('Ok', 'button', 'smart-manager-for-wp-e-commerce'),
				callback: function() {
					if( typeof window.smart_manager.prodAttributeInlineEdit === "function" ) {
						window.smart_manager.prodAttributeInlineEdit(params);
					}
				}
			},
			onCreate: initializeChosen,
			onUpdate: initializeChosen
		}
		window.smart_manager.showModal()

	
})

.off('click', '#edit_attributes_add').on('click', '#edit_attributes_add', function(){
	let taxonomySelected = jQuery("#edit_attributes_taxonomy_list").val(),
		isVariation = jQuery("#edit_product_attributes [name=isVariation]").val(),
		newAttribute = '',
		attrType = 'text',
		attrVal = '',
		isTaxonomy = 0,
		attrChkboxList = '';

	//Code to reset the taxonomy list
	jQuery('#edit_attributes_taxonomy_list').find('option[value="custom"]').prop('selected', true);

	jQuery('#edit_attributes_taxonomy_list').find('option[value="'+ taxonomySelected +'"]').prop('disabled', true);

	if( taxonomySelected !== "custom" ) {
		attrType = ( typeof(window.smart_manager.prodAttributeActualValues) != 'undefined' && typeof(window.smart_manager.prodAttributeActualValues[taxonomySelected]) != 'undefined' && window.smart_manager.prodAttributeActualValues[taxonomySelected].hasOwnProperty('type') ) ? window.smart_manager.prodAttributeActualValues[taxonomySelected].type : '';
		attrVal = ( typeof(window.smart_manager.prodAttributeActualValues) != 'undefined' && typeof(window.smart_manager.prodAttributeActualValues[taxonomySelected]) != 'undefined' && window.smart_manager.prodAttributeActualValues[taxonomySelected].hasOwnProperty('val') ) ? window.smart_manager.prodAttributeActualValues[taxonomySelected].val : '';
		isTaxonomy = 1;
	}

	attrChkboxList += '<tr> <td> <input type="checkbox" id="attribute_visibility_'+taxonomySelected+'" name="attribute_visibility['+window.smart_manager.prodAttrDisplayIndex+']">'+_x('Visible on the product page', 'visibility option for WooCommerce product attributes', 'smart-manager-for-wp-e-commerce')+'</td> </tr>';
	attrChkboxList += '<tr> <td> <input type="checkbox" id="attribute_variation_'+taxonomySelected+'" name="attribute_variation['+window.smart_manager.prodAttrDisplayIndex+']">'+_x('Used for variations', 'use for variations option for WooCommerce product attributes', 'smart-manager-for-wp-e-commerce')+'</td> </tr>';
	attrChkboxList += '<tr> <td> <label>'+_x('Position:', 'position checkbox for WooCommerce product attributes', 'smart-manager-for-wp-e-commerce')+'</label> <input type="number" style="width:23% !important;" name="attribute_position['+window.smart_manager.prodAttrDisplayIndex+']" value="'+window.smart_manager.prodAttrDisplayIndex+'">';
	attrChkboxList += '<input type="hidden" name="attribute_taxonomy['+window.smart_manager.prodAttrDisplayIndex+']" value="'+isTaxonomy+'"> </td> </tr>';

	if (isTaxonomy == 1) {
		newAttribute += '<tr> <td> <label style="font-weight: bold;">'+window.smart_manager.prodAttributeActualValues[taxonomySelected].lbl+':</label> </td>';
		if( "text" === attrType ) {
			newAttribute += '<td rowspan="4"> <input type="text" id="'+attrLabel+'" name="attribute_values['+window.smart_manager.prodAttrDisplayIndex+']" value="'+attrValue+'" placeholder="'+_x('Pipe (|) separate terms', 'placeholder', 'smart-manager-for-wp-e-commerce')+'" /> </ td>';
		} else {
			newAttribute += '<td rowspan="4"> <select multiple="multiple" data-placeholder="'+_x('Select terms', 'placeholder', 'smart-manager-for-wp-e-commerce')+'" name="attribute_values['+window.smart_manager.prodAttrDisplayIndex+'][]" class="multiselect" style="width:100% !important">';

			if( attrVal != '' ) {
				Object.entries(attrVal).forEach(([key, value]) => {
					newAttribute += '<option value="'+ key +'">'+ value +'</option>';
				});
			}
			newAttribute += '</select> <br />';
			newAttribute += '<button class="button select_all_attributes" style="margin-right: 1em;">'+_x('Select all', 'button for selecting WooCommerce product attribute', 'smart-manager-for-wp-e-commerce')+'</button> ';
			newAttribute += '<button class="button select_no_attributes">'+_x('Select none', 'button for selecting WooCommerce product attribute', 'smart-manager-for-wp-e-commerce')+'</button> </td>';
		}
		newAttribute += '<td> <input type="hidden" name="attribute_names['+window.smart_manager.prodAttrDisplayIndex+']" index="'+window.smart_manager.prodAttrDisplayIndex+'" value="'+ taxonomySelected +'"/></td>';
	} else if (isTaxonomy == 0) {
		newAttribute += '<tr> <td> <input type="text" name="attribute_names['+window.smart_manager.prodAttrDisplayIndex+']" index="'+window.smart_manager.prodAttrDisplayIndex+'" placeholder="'+_x('Name', 'placeholder', 'smart-manager-for-wp-e-commerce')+'"> </td>';
		newAttribute += '<td rowspan="4"> <input type="text" name="attribute_values['+window.smart_manager.prodAttrDisplayIndex+']" value="" placeholder="'+_x('Pipe (|) separate terms', 'placeholder', 'smart-manager-for-wp-e-commerce')+'" /> </td>';
	}
	
	newAttribute += '</tr>';
	newAttribute += attrChkboxList;

	jQuery('#table_edit_attributes').append(newAttribute);
	jQuery("select.multiselect").chosen();

	window.smart_manager.prodAttrDisplayIndex++;
})

//Function to handle Product Attribute Inline Edit
Smart_Manager.prototype.prodAttributeInlineEdit = function(params){
	if('undefined' === typeof(window.smart_manager.editedAttribueSlugs)){
		return;
	}
	let attributesEditedText = '',
		productAttributesPostmeta = {};
	window.smart_manager.editedAttribueSlugs = '';
	jQuery('#edit_product_attributes input[name^="attribute_names"]').each( function(){
		let index = jQuery(this).attr('index'),
			attrNm = jQuery(this).val(),
			isTaxonomy = parseInt(jQuery("input[name='attribute_taxonomy["+index+"]']" ).val()),
			editedValue = '',
			editedText = '',
			selectedText = '',
			selectedVal = '';
		if( attributesEditedText.length > 0 && (window.smart_manager.editedAttribueSlugs).length > 0) {
			attributesEditedText += ', <br>';
			window.smart_manager.editedAttribueSlugs += ', <br>';
		}
		if( jQuery( "input[name='attribute_values["+index+"]']" ).attr('type') !== undefined && jQuery( "input[name='attribute_values["+index+"]']" ).attr('type') == "text" ) {
			editedValue = jQuery( "input[name='attribute_values["+index+"]']" ).val();

			if (editedValue == '') {
				return;
			}

			editedText = editedValue.split("|");
			editedText = editedText.map(text => text.trim());
			editedValue = editedText.join(" | ");

			if (isTaxonomy == 1) {
				attributesEditedText += attributesEditedText[attrNm] + ': [' + editedText + ']';
				window.smart_manager.editedAttribueSlugs += window.smart_manager.editedAttribueSlugs[attrNm] + ': [' + editedText + ']';
				editedValue = editedText;
			} else if (isTaxonomy == 0) {
				attributesEditedText += attrNm + ': [' + editedValue + ']';
				window.smart_manager.editedAttribueSlugs += attrNm + ': [' + editedValue + ']';
				attrNm = attrNm.replace(/( )/g,"-").replace(/([^a-z A-Z 0-9][^\w\s])/gi,'').toLowerCase();
			}

		} else {

			selectedText = jQuery( "select[name='attribute_values["+index+"][]'] option:selected" ).map(function () {
							return jQuery(this).text();
						}).get().join(' | ');

			if (selectedText == '') {
				return;
			}

			selectedVal = jQuery( "select[name='attribute_values["+index+"][]'] option:selected" ).map(function () {
							return jQuery(this).val();
						}).get();

			editedValue = {};

			if( window.smart_manager.prodAttributeActualValues.hasOwnProperty(attrNm) && window.smart_manager.prodAttributeActualValues[attrNm].hasOwnProperty('val') ) {
				selectedVal.forEach((index) => {
					editedValue[index] = window.smart_manager.prodAttributeActualValues[attrNm].val[index];
				});
			}
			let attributeName = (attrNm) ? attrNm.substr(3) : '';
			window.smart_manager.editedAttribueSlugs += ((attributeName) ? attributeName : '') + ': [' + selectedText + ']';
			attributesEditedText += ( ( window.smart_manager.prodAttributeActualValues.hasOwnProperty(attrNm) && window.smart_manager.prodAttributeActualValues[attrNm].hasOwnProperty('lbl') ) ? window.smart_manager.prodAttributeActualValues[attrNm].lbl : '' ) + ': [' + selectedText + ']';
		}
		productAttributesPostmeta [attrNm] = {};
		productAttributesPostmeta [attrNm]['name'] = attrNm;
		productAttributesPostmeta [attrNm]['value'] = editedValue;
		productAttributesPostmeta [attrNm]['position'] = jQuery( "input[name='attribute_position["+index+"]']" ).val();

		if (jQuery( "input[name='attribute_visibility["+index+"]']" ).is(":checked")) {
			productAttributesPostmeta [attrNm]['is_visible'] = 1;
		} else {
			productAttributesPostmeta [attrNm]['is_visible'] = 0;
		}

		if (jQuery( "input[name='attribute_variation["+index+"]']" ).is(":checked")) {
			productAttributesPostmeta [attrNm]['is_variation'] = 1;
		} else {
			productAttributesPostmeta [attrNm]['is_variation'] = 0;
		}
		productAttributesPostmeta [attrNm]['is_taxonomy'] = isTaxonomy;
	});


	window.smart_manager.hot.setDataAtRowProp(params.coords.row, 'postmeta_meta_key__product_attributes_meta_value__product_attributes', ((Object.keys(productAttributesPostmeta).length > 0) ? JSON.stringify(productAttributesPostmeta) : ''), 'sm.longstring_product_attributes_inline_update');
	window.smart_manager.hot.setDataAtCell(params.coords.row, params.coords.col, attributesEditedText, 'sm.longstring_product_attributes_inline_update');
}

//Function to handle 'show variations' checkbox
Smart_Manager.prototype.showVariationsHtml = function() {
	let show_variations_checked = '';

	if( window.smart_manager.currentDashboardModel.hasOwnProperty('treegrid') && window.smart_manager.currentDashboardModel.treegrid == 'true' ) {
		show_variations_checked = 'checked';
	}

	if( jQuery(".sm_top_bar_action_btns:nth-last-child(2)").find('#sm_products_show_variations_span').length == 0 ) {
		jQuery(".sm_top_bar_action_btns:nth-last-child(2)").append("<label id='sm_products_show_variations_span' style='font-weight:400 !important;float: right;padding: 0.5em;'> <input type='checkbox' name='sm_products_show_variations' id='sm_products_show_variations' value='sm_products_show_variations' "+ show_variations_checked +">"+_x('Show Variations', 'checkbox for displaying WooCommerce product variations', 'smart-manager-for-wp-e-commerce')+"</label>");
	}

	jQuery('.sm_top_bar_action_btns:nth-last-child(2) #sm_products_show_variations').off('change').on('change',function() {

		if( jQuery('#sm_products_show_variations').is(":checked") ) {
			window.smart_manager.currentDashboardModel.tables.posts.where.post_type = ['product', 'product_variation'];
			window.smart_manager.currentDashboardModel.treegrid = 'true';
		} else {
			window.smart_manager.currentDashboardModel.tables.posts.where.post_type = 'product';
			window.smart_manager.currentDashboardModel.treegrid = 'false';
		}

		if ( typeof (window.smart_manager.updateState) !== "undefined" && typeof (window.smart_manager.updateState) === "function" ) {
			window.smart_manager.updateState(); //refreshing the dashboard states
		}

		window.smart_manager.refresh();
	});
}
