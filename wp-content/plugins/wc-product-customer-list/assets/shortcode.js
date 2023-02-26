jQuery(document).ready(function($) {
	var productTitle = wpcl_script_vars.productTitle;
	var pdfOrientation = wpcl_script_vars.pdfOrientation;
	var pdfPageSize = wpcl_script_vars.pdfPagesize;
	var fileName = productTitle.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');

	// Pro
	var wpclInfoState = (wpcl_script_vars.infoState === 'true');
	var wpclPagingState = (wpcl_script_vars.pagingState === 'true');
	var wpclCSV = (wpcl_script_vars.exportCsv === 'true');
	var wpclPDF = (wpcl_script_vars.exportPdf === 'true');
	var wpclCopy = (wpcl_script_vars.copy === 'true');
	var wpclPrint = (wpcl_script_vars.print === 'true');
	var wpclSortable = (wpcl_script_vars.sortable === 'true');
	var wpclScrollX = (wpcl_script_vars.scrollX === 'true');
	var wpclSearchState = (wpcl_script_vars.searchState === 'true');
	var wpclIndex = (wpcl_script_vars.indexColumn === 'true');

	if (wpclSearchState == true) {
		var wpclSearch = 'Blfrtip';
	} else {
		var wpclSearch = 'Blrtip';
	}


	var table = $('.wpcl-shortcode#list-table').DataTable( {

		columnDefs: [ {
            searchable: false,
            orderable: false,
            targets: 'wpcl-index',
            available: function ( dt, config ) {
            	return wpclIndex;
            }
        } ],
        order: [[ 1, 'asc' ]],

		/*columnDefs: [
	        { targets: [0], visible: false},
	        { targets: '_all', visible: true }
    	],
    	*/
		colReorder: false,
		info: wpclInfoState,
		//stateSave:  true,
  		//stateLoadParams: function (settings, data) {  data.columns['0'].visible = false; },

		//select: true,
		lengthMenu: [[10, 25, 50, -1], [10, 25, 50, wpcl_script_vars.lengthMenuAll]],
		//dom: 'Blfrtip',
		dom: wpclSearch,
		buttons: [

			{
				extend: 'copy',
				text: wpcl_script_vars.copybtn,
				available: function ( dt, config ) {
                    return wpclCopy;
                }
			},
			{
				extend: 'print',
				title: productTitle,
				text: wpcl_script_vars.printbtn,
				customize: function ( win ) {
					$(win.document.body)
						.css( 'background-color', '#fff' )
						.css( 'padding', '1px' );

					$(win.document.body).find( 'table' )
						.addClass( 'compact' )
						.css( 'font-size', 'inherit' )
						.css( 'border', '0px' )
						.css( 'border-collapse', 'collapse' );

					$(win.document.body).find( 'table th' )
						.css( 'padding', '5px 8px 8px' )
						.css( 'background-color', '#f1f1f1' )
						.css( 'border-bottom', '0px' );

					$(win.document.body).find( 'table td' )
						.css( 'border', '1px solid #dfdfdf' )
						.css( 'padding', '8px' );

					$(win.document.body).find( 'table tr:nth-child(even)' )
						.css( 'background-color', '#f9f9f9' );
				},
				available: function ( dt, config ) {
                    return wpclPrint;
                }
			},
			{
				extend: 'csvHtml5',
				title: fileName,
				available: function ( dt, config ) {
                    return wpclCSV;
                },
			},
			{
				extend: 'pdfHtml5',
				title: productTitle,
				orientation: pdfOrientation,
				pageSize: pdfPageSize,
				filename: fileName,
				customize: function(doc)
				{
					doc.styles.tableHeader.fillColor = '#f1f1f1';
					doc.styles.tableHeader.color = '#000';
					doc.styles.tableBodyEven.fillColor = '#f9f9f9';
					doc.styles.tableBodyOdd.fillColor = '#fff';
				},
				available: function ( dt, config ) {
                    return wpclPDF;
                }
			},
			/*
			{
				text: wpcl_script_vars.resetColumn,
				action: function ( e, dt, node, config ) {
					table.colReorder.reset();
					table.state.clear();
					window.location.reload();
				}
			}
			*/
		],

		paging: wpclPagingState,
		pagingType: 'full',
		scrollX: wpclScrollX,
		language: {
			'search': wpcl_script_vars.search,
			'emptyTable': wpcl_script_vars.emptyTable,
			'zeroRecords': wpcl_script_vars.zeroRecords,
			'tableinfo': wpcl_script_vars.tableinfo,
			'lengthMenu': wpcl_script_vars.lengthMenu,
			'info': wpcl_script_vars.info,
			paginate: {
				first:    '«',
				previous: '‹',
				next:     '›',
				last:     '»'
			},
			buttons: {
			copyTitle: wpcl_script_vars.copyTitle,
			copySuccess: {
				_: wpcl_script_vars.copySuccessMultiple,
				1: wpcl_script_vars.copySuccessSingle,
			}
		},
		aria: {
			paginate: {
				first:    wpcl_script_vars.paginateFirst,
				previous: wpcl_script_vars.paginatePrevious,
				next:     wpcl_script_vars.paginateNext,
				last:     wpcl_script_vars.paginateLast,
			}
		}
	}

	} );

	if (wpclIndex == true) {
		table.on( 'order.dt search.dt', function () {
	        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	var indexNum = i+1;
	            cell.innerHTML = '<p>' + indexNum + '</p>';
	        } );
	    } ).draw();
	}
	/*
	// Update email list on row selection
	$('#email-selected').click(function( event ) {
		//event.preventDefault();
	});
	table.on( 'select', function ( e, dt, type, indexes ) {
		var emails = $.map(table.rows('.selected').data(), function (item) {
			return item[0];
		});
		var emailBcc = emails.join(",");
		$('#email-selected').attr('href', 'mailto:?bcc=' + emailBcc);
		if(emailBcc) {
			$('#email-selected').removeAttr('disabled');
		}
	});

	// Update email list on row deselection
	table.on( 'deselect', function ( e, dt, type, indexes ) {
		var emails = $.map(table.rows('.selected').data(), function (item) {
			return item[0];
		});
		var emailBcc = emails.join(",");
		$('#email-selected').attr('href', 'mailto:?bcc=' + emailBcc);
		if(emailBcc) {
			$('#email-selected').removeAttr('disabled');
		} else {
			$('#email-selected').attr('disabled', 'true');
		}
	});
	*/

} );