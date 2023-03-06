var thwcfe_settings_advanced = (function($, window, document) {
   /*------------------------------------
	*---- ON-LOAD FUNCTIONS - SATRT -----
	*------------------------------------*/
	$(function() {
		var advanced_settings_form = $('#advanced_settings_form');
		if(advanced_settings_form[0]) {
			thwcfeSetupEnhancedMultiSelectWithValue(advanced_settings_form);
		}
	});
   /*------------------------------------
	*---- ON-LOAD FUNCTIONS - END -----
	*------------------------------------*/
	
   /*------------------------------------
	*---- Custom Validations - SATRT -----
	*------------------------------------*/
	var VALIDATOR_ROW_HTML  = '<tr>';
        VALIDATOR_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_validator_name[]" placeholder="Validator Name" style="width:180px;"/></td>';
		VALIDATOR_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_validator_label[]" placeholder="Validator Label" style="width:180px;"/></td>';
		VALIDATOR_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_validator_pattern[]" placeholder="Validator Pattern" style="width:180px;"/></td>';
		VALIDATOR_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_validator_message[]" placeholder="Validator Message" style="width:180px;"/></td>';
		VALIDATOR_ROW_HTML += '<td class="action-cell">';
		VALIDATOR_ROW_HTML += '<a href="javascript:void(0)" onclick="thwcfeAddNewValidatorRow(this, 0)" class="dashicons dashicons-plus" title="Add new validator"></a></td>';
		VALIDATOR_ROW_HTML += '<td class="action-cell">';
		VALIDATOR_ROW_HTML += '<a href="javascript:void(0)" onclick="thwcfeRemoveValidatorRow(this, 0)" class="dashicons dashicons-no-alt" title="Remove validator"></a></td>';
		VALIDATOR_ROW_HTML += '</tr>';
		
	var CNF_VALIDATOR_ROW_HTML  = '<tr>';
        CNF_VALIDATOR_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_cnf_validator_name[]" placeholder="Validator Name" style="width:180px;"/></td>';
		CNF_VALIDATOR_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_cnf_validator_label[]" placeholder="Validator Label" style="width:180px;"/></td>';
		CNF_VALIDATOR_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_cnf_validator_pattern[]" placeholder="Field Name" style="width:180px;"/></td>';
		CNF_VALIDATOR_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_cnf_validator_message[]" placeholder="Validator Message" style="width:180px;"/></td>';
		CNF_VALIDATOR_ROW_HTML += '<td class="action-cell">';
		CNF_VALIDATOR_ROW_HTML += '<a href="javascript:void(0)" onclick="thwcfeAddNewValidatorRow(this, 1)" class="dashicons dashicons-plus" title="Add new validator"></a></td>';
		CNF_VALIDATOR_ROW_HTML += '<td class="action-cell">';
		CNF_VALIDATOR_ROW_HTML += '<a href="javascript:void(0)" onclick="thwcfeRemoveValidatorRow(this, 1)" class="dashicons dashicons-no-alt" title="Remove validator"></a></td>';
		CNF_VALIDATOR_ROW_HTML += '</tr>';
		
	addNewValidatorRow = function addNewValidatorRow(elm, prefix){
		var ptable = $(elm).closest('table');
		var rowsSize = ptable.find('tbody tr').size();
		
		var ROW_HTML = VALIDATOR_ROW_HTML;
		if(prefix == 1){
			ROW_HTML = CNF_VALIDATOR_ROW_HTML;
		}
			
		if(rowsSize > 0){
			ptable.find('tbody tr:last').after(ROW_HTML);
		}else{
			ptable.find('tbody').append(ROW_HTML);
		}
	}
	
	removeValidatorRow = function removeValidatorRow(elm, prefix){
		var ptable = $(elm).closest('table');
		$(elm).closest('tr').remove();
		var rowsSize = ptable.find('tbody tr').size();
		
		var ROW_HTML = VALIDATOR_ROW_HTML;
		if(prefix == 1){
			ROW_HTML = CNF_VALIDATOR_ROW_HTML;
		}
			
		if(rowsSize == 0){
			ptable.find('tbody').append(ROW_HTML);
		}
	}
   /*------------------------------------
	*---- Custom Validations - END -----
	*------------------------------------*/
				
	return {
		addNewValidatorRow : addNewValidatorRow,
		removeValidatorRow : removeValidatorRow,
   	};
}(window.jQuery, window, document));	

/* Advance Settings */
function thwcfeAddNewValidatorRow(elm, prefix){
	thwcfe_settings_advanced.addNewValidatorRow(elm, prefix);
}
function thwcfeRemoveValidatorRow(elm, prefix){
	thwcfe_settings_advanced.removeValidatorRow(elm, prefix);
}