( function ( $ ) {

	wpclUtils = {
		chunk       : function ( _array, _chunkMaxSize ) {

			var arrayOut  = [],
				i         = 0,
				arraySize = _array.length;

			if ( _chunkMaxSize < 1 ) {
				console.error( '[ObcUtils.chunk] Array chunk must be at leats 1' );
				return arrayOut;
			}

			while ( i < arraySize ) {
				arrayOut.push( _array.slice( i, i += _chunkMaxSize ) );
			}

			return arrayOut;
		},
		arrayUnique : function ( _array ) {
			var unique = [];
			for ( var i = 0; i < _array.length; i++ ) {
				if ( unique.indexOf( _array[ i ] ) === -1 ) {
					unique.push( _array[ i ] );
				}
			}
			return unique;
		}
	};


	wpclOrdersTable = {
		init : function ( $tableContainer ) {
			var _this = wpclOrdersTable;

			_this.orderChunkSize = 50; // how many orders per AJAX call

			_this.$tableContainer = $( '.wpcl-list-table-container' );

			// Do we even have the meta box on this page?
			if ( _this.$tableContainer.length < 0 ) {
				console.log( 'no tables container' );
				return false;
			}


			_this.$dataTable    = _this.$tableContainer.find( '.wpcl-list-table' );
			_this.$extraActions = _this.$tableContainer.find( '.wpcl-extra-action' );
			_this.$extraActions.hide();
			_this.sortable = _this.$dataTable.data( 'sortable' );

			// _this.useDatables = _this.$dataTable.data( 'use-datatables' );
			_this.ajaxMode = _this.$dataTable.data( 'ajax-mode' );


			_this.$extraButtons = $( '.wpcl-btn-mail-to-all-group' ).add( '.wpcl-btn-email-selected' );

			if ( _this.$extraButtons.length > 0 ) {
				_this.$extraButtons.hide();
			}

			_this.$emailNote = $( '.wpcl-email-all-technical-note' );
			if ( _this.$emailNote.length > 0 ) {
				_this.$emailNote
					.hide()
					.attr( 'aria-hidden', true );
			}


			// Check if we have some the necessary config data supplied wpcl_enqueue_scripts()
			if ( typeof wpcl_script_vars === 'undefined' ) {
				console.error( '[wc-product-customer-list-pro] an error occured while trying to get the variables for the DataTables' );
				return false;
			}


			_this.ordersVariable = _this.$dataTable.data( 'orders' );
			_this.callSource     = _this.$dataTable.data( 'call-source' );


			// No related order items, nothing else to do
			if ( typeof window[ _this.ordersVariable ] === 'undefined' ) {
				console.log( 'no orders ' + _this.ordersVariable );
				return false;
			}

			if ( typeof window[ _this.ordersVariable + '_columns' ] !== 'undefined' ) {
				_this.customColumns = window[ _this.ordersVariable + '_columns' ];
			} else {
				_this.customColumns = false;
			}

			if ( typeof window[ _this.ordersVariable + '_options' ] !== 'undefined' ) {
				_this.customOptions = window[ _this.ordersVariable + '_options' ];
			} else {
				_this.customOptions = wpcl_script_vars;
			}


			if ( _this.ajaxMode ) {

				// We want to only launch this once the metabox comes into view
				var tableSection = document.querySelector( '.wpcl-list-table-container' ),
					observer     = new IntersectionObserver( function ( entries ) {
						$.each( entries, function ( i, entry ) {
							if ( entry.intersectionRatio > 0 ) {

								// Our main thing
								_this.processAllOrderItems();

								// no use in watching if it's in view anymore, after the first time
								observer.disconnect()
							}
						} );
					} );
				observer.observe( tableSection );

			} else {
				_this.setupDataTables( true );

			}

			// else ... well, there's nothing else to do :)


		},

		processAllOrderItems : function () {
			var _this = wpclOrdersTable;

			// console.log( 'About to process orders' );


			_this.needColumns = true;
			// _this.currentProductId = _this.customOptions.productId;

			// We're taking all our orders and making groups to send to the REST API
			// One at a time is wayyy too slow
			_this.orderIdBatches    = wpclUtils.chunk( window[ _this.ordersVariable ], _this.orderChunkSize );
			_this.currentChunkIndex = 0;
			_this.numChunks         = _this.orderIdBatches.length;

			_this.data          = [];
			_this.columns       = [];
			_this.emails        = [];
			_this.totalQuantity = 0;

			_this.$progressContainer = $( '<div class="progress-bar">\n' +
				'<span class="result">' + wpcl_script_vars.trans.processing_orders + '0%' + '</span>' +
				'  <span class="bar">' +
				'    <span class="progress"></span>' +
				'  </span>' +
				'</div>' ).insertBefore( _this.$dataTable );

			_this.$progressBar = _this.$progressContainer.find( '.progress' );
			_this.$result      = _this.$progressContainer.find( '.result' );


			_this.startTime = new Date();

			_this.getOrderItemInfo( _this.currentOrderIndex );


			_this.rowCounter = 0;

		},

		displayAjaxError : function ( error ) {
			var _this = wpclOrdersTable;

			_this.$progressContainer.html( '<span class="result alert error">' + error + '</span>' );
		},

		getOrderItemInfo : function () {
			var _this = wpclOrdersTable;

			console.log('[processAllOrderItems::getOrderItemInfo] for these orders',  _this.orderIdBatches[ _this.currentChunkIndex ]);

			$.ajax( {
					url    : wpcl_script_vars.ajax_path,
					method : 'POST',
					cache  : false,
					data   : {
						action         : 'process_order_items',
						nonce          : wpcl_script_vars.ajax_nonce,
						batch_orders   : _this.orderIdBatches[ _this.currentChunkIndex ],
						need_columns   : _this.needColumns,
						call_source    : _this.callSource,
						custom_columns : _this.customColumns,
						custom_options : _this.customOptions,
					}
				} )
				.fail( function ( jqXHR, textStatus, errorThrown ) {
					_this.displayAjaxError( wpcl_script_vars.trans.ajax_error );
					console.error( '[wc-product-customer-list-pro] ' + wpcl_script_vars.trans.ajax_error, jqXHR, textStatus, errorThrown );
				} )

				.done( function ( _data ) {
					// console.log( _data );

					if ( typeof _data.success === 'undefined' || _data.success !== true ) {
						if ( typeof _data.message !== 'undefined' ) {
							_this.displayAjaxError( _data.message );
						} else if ( typeof _data.data !== 'undefined' && typeof _data.data.message !== 'undefined' ) {
							_this.displayAjaxError( _data.data.message );
						} else if ( typeof _data.data !== 'undefined' ) {
							_this.displayAjaxError( _data.data );
						} else {
							_this.displayAjaxError( _data );
						}

						return;
					}


					// Yay! It worked

					if ( typeof _data.data !== 'undefined' && typeof _data.data.timing !== 'undefined' ) {
						console.table( _data.data.timing );
					}


					// Compile the data
					if ( typeof _data.data !== 'undefined' && typeof _data.data.order_rows !== 'undefined' && _data.data.order_rows.length > 0 ) {

						for ( var i = 0; i < _data.data.order_rows.length; i++ ) {
							_this.data.push( _data.data.order_rows[ i ] );
						}
console.log('_data', _data);
					}

					// Compile the columns during the first reception of data
					if ( _this.needColumns && typeof _data.data.columns !== 'undefined' ) {

						$.each( _data.data.columns, function ( data, title ) {
							_this.columns.push( {
								'data'  : data,
								'title' : title
							} )
						} );

						_this.needColumns = false;
					}


					// Add the emails to our global email array
					if ( _data.data.email_list != null && typeof _data.data.email_list !== 'undefined' && _data.data.email_list.length > 0 ) {
						_this.emails = _this.emails.concat( _data.data.email_list );


					}

					if ( typeof _data.data.product_count !== 'undefined' ) {
						_this.totalQuantity += _data.data.product_count;
					}


					_this.currentChunkIndex++;
					if ( _this.currentChunkIndex < _this.numChunks ) {


						// call itself with the next item in the index
						_this.getOrderItemInfo();

						// Adjust
						var percentage = ( ( _this.currentChunkIndex ) / _this.numChunks * 100 );
						_this.$progressBar.css( {
							width : percentage + '%'
						} );

						_this.$result.text(
							wpcl_script_vars.trans.processing_orders +
							( Math.round( percentage ) ) + '%'
						);
					} else {

						// Adjust the progress bar
						_this.$progressBar.css( { width : '100%' } );
						_this.$result.text( '100%' );


						// Compile the list of unique emails
						if ( _this.emails.length > 0 ) {
							_this.emails = wpclUtils.arrayUnique( _this.emails );


							// Some browsers don't like mailto: links longer than about 2000 characters
							// https://stackoverflow.com/a/417184/636709

							var emailSeperator = _this.customOptions.wpcl_email_seperator;
							var emailGroups = [],
								mailto      = 'mailto:?bcc=',
								limitChars  = 1900;

							for ( var i = 0; i < _this.emails.length; i++ ) {
								var email      = _this.emails[ i ];
								var testMailto;
								if(emailSeperator == 'comma') {
									testMailto = mailto + email + ',';
								} else {
									testMailto = mailto + email + ';';
								}

								if ( testMailto.length > limitChars ) {
									// adding this one would make it too long,
									// store it in our groups and reset the mailto
									emailGroups.push( mailto );
									if(emailSeperator == 'semicolon') {
										mailto = 'mailto:?bcc=' + email + ';';
									} else  {
										mailto = 'mailto:?bcc=' + email + ',';
									}
								} else {
									// still some place? Let's add it to the end
									mailto = testMailto;
								}
							}

							// for the last one
							emailGroups.push( mailto );


							// in case we already had more than one "Email all" button, let's clean everything up
							var $btnEmailAllSection = _this.$tableContainer.find( '.wpcl-btn-mail-to-all-group' ),
								$btnEmailAll        = $btnEmailAllSection.find( '.wpcl-btn-mail-to-all' );

							if ( $btnEmailAll.length > 1 ) {
								$btnEmailAll.slice( 1 ).remove();
							}


							// let's see how many buttons we need
							if ( emailGroups.length > 1 ) {
								// need more than one button? OK.

								var $firstBtn = $btnEmailAll.eq( 0 ),
									numGroups = emailGroups.length;

								for ( var i = 0; i < numGroups; i++ ) {
									var currMailto = emailGroups[ i ];

									if ( i === 0 ) {
										// first one? doing the same as usual
										$firstBtn
											.attr( 'href', currMailto )
											.text( wpcl_script_vars.trans.email_multiple_button_text + ' (' + ( i + 1 ) + '/' + numGroups + ')' );
									} else {
										// need another button? let's create one
										$firstBtn
											.clone()
											.appendTo( $btnEmailAllSection )
											.attr( 'href', currMailto )
											.text( wpcl_script_vars.trans.email_multiple_button_text + ' (' + ( i + 1 ) + '/' + numGroups + ')' );
									}

								}

								_this.$emailNote
									.show()
									.attr( 'aria-hidden', false );

							} else {
								// Only need one button? awesome!
								_this.$tableContainer.find( '.wpcl-btn-mail-to-all' ).attr( 'href', emailGroups[ 0 ] );

							}
						}

						_this.$extraActions.find( '.total' ).find( '.product-count' ).text( _this.totalQuantity );


						// adjust the totalQuantity column because it may have been split "per" ajax call
						for ( var i = 0; i < _this.data.length; i++ ) {
							if ( typeof _this.data[ i ].wpcl_order_qty_total_column !== 'undefined' ) {
								_this.data[ i ].wpcl_order_qty_total_column = _this.totalQuantity;
							}
						}


						var endTime = new Date();
						var seconds = ( endTime.getTime() - _this.startTime.getTime() ) / 1000;

						console.log(
							' [wc-product-customer-list-pro] %cCompiling ' + _this.data.length + ' customers took took about ' +
							'%c' + seconds + ' seconds. ',
							'background: #222; color: #fff',
							'background: #222; color: #bada55'
						);


						_this.$progressContainer.slideUp();

						// we have all our info. Time to setup the datables
						_this.setupDataTables();
					}
				} );

		},

		setupDataTables : function ( staticTableMode ) {
			var _this = wpclOrdersTable;


			var productSKU           = _this.customOptions.productSku,
				productTitle         = _this.customOptions.productTitle,
				pdfOrientation       = _this.customOptions.wpcl_export_pdf_orientation,
				pdfPageSize          = _this.customOptions.wpcl_export_pdf_pagesize,
				fileName             = productTitle.replace( /[^a-z0-9\s]/gi, '' ).replace( /[_\s]/g, '-' ),
				productSKUfilename   = productSKU.replace( /[^a-z0-9\s]/gi, '' ).replace( /[_\s]/g, '-' ),
				columnOrderIndex     = parseInt( _this.customOptions.wpcl_column_order_index ),
				columnOrderDirection = _this.customOptions.wpcl_column_order_direction,
				optionStateSave      = _this.customOptions.wpcl_state_save === true,
				pdfTitle             = productTitle,
				search               = _this.customOptions.wpcl_search,
				showInfoUnderTable   = _this.customOptions.wpcl_info,
				doPaging             = _this.customOptions.wpcl_paging,
				domSearch            = search ? 'Blfrtip' : 'Blrtip';
				emailSeperator		 = _this.customOptions.wpcl_email_seperator;


			if ( typeof columnOrderIndex === 'undefined' || isNaN( columnOrderIndex ) ) {
				columnOrderIndex = 0;
			}

			if ( typeof columnOrderDirection === 'undefined'
				|| ( [ 'asc', 'ASC', 'desc', 'DESC' ].indexOf( columnOrderDirection ) < 0 )
			) {
				columnOrderDirection = "ASC";
			}


			if ( _this.customOptions.wpcl_export_pdf_sku === true && _this.customOptions.productSku !== '' ) {
				pdfTitle = _this.customOptions.productTitle + ' (' + _this.customOptions.productSku + ')';
			}


			var dataTablesOptions = {

				data    : _this.data,
				columns : _this.columns,

				stateSave : optionStateSave,
				//stateLoadParams: function (settings, data) {  data.columns['0'].visible = false; },
				info       : showInfoUnderTable,
				paging     : doPaging,
				order      : [ [ columnOrderIndex, columnOrderDirection ] ],
				ordering   : _this.customOptions.wpcl_sortable,
				select     : true,
				lengthMenu : [ [ 10, 25, 50, -1 ], [ 10, 25, 50, wpcl_script_vars.trans.lengthMenuAll ] ],
				dom        : domSearch,
				searching  : search,
				colReorder : {
					enable : _this.customOptions.wpcl_col_reorder
				},
				buttons    : [
					// Copy functionality
					{
						extend    : 'copy',
						text      : wpcl_script_vars.trans.copybtn,
						available : function ( dt, config ) {
							return typeof _this.customOptions.wpcl_copy !== 'undefined' && _this.customOptions.wpcl_copy === true;
						}
					},


				],

				pagingType  : 'full',
				scrollX     : _this.customOptions.wpcl_scrollx,
				fixedHeader : _this.customOptions.wpcl_fixed_header,
				language    : {
					'search'      : wpcl_script_vars.trans.search,
					'emptyTable'  : wpcl_script_vars.trans.emptyTable,
					'zeroRecords' : wpcl_script_vars.trans.zeroRecords,
					'tableinfo'   : wpcl_script_vars.trans.tableinfo,
					'lengthMenu'  : wpcl_script_vars.trans.lengthMenu,
					'info'        : wpcl_script_vars.trans.info,
					paginate      : {
						first    : '«',
						previous : '‹',
						next     : '›',
						last     : '»'
					},
					buttons       : {
						copyTitle   : wpcl_script_vars.trans.copyTitle,
						copySuccess : {
							_ : wpcl_script_vars.trans.copySuccessMultiple,
							1 : wpcl_script_vars.trans.copySuccessSingle,
						}
					},
					aria          : {
						paginate : {
							first    : wpcl_script_vars.trans.paginateFirst,
							previous : wpcl_script_vars.trans.paginatePrevious,
							next     : wpcl_script_vars.trans.paginateNext,
							last     : wpcl_script_vars.trans.paginateLast,
						}
					}
				},

				// add a data-email attribute with the order email
				createdRow : function ( row, data, dataIndex ) {
					$( row ).attr( 'data-email', data.billing_email_raw );
				},

				drawCallback : function ( settings ) {

					// Hiding the header row
					if ( typeof _this.customOptions.wpcl_show_titles_row !== 'undefined' && _this.customOptions.wpcl_show_titles_row === false ) {
						var $headersRow = _this.$tableContainer.find( '.dataTables_scrollHead' );

						if ( $headersRow.length > 0 ) {
							$headersRow.remove();
						}
					}
				}
			};


			/*
			* Only add the following buttons if the related features are active
			*/

			//Column reordering button
			if ( _this.customOptions.wpcl_col_reorder ) {
				dataTablesOptions.buttons.push( {
					text   : wpcl_script_vars.trans.resetColumn,
					action : function ( e, dt, node, config ) {
						_this.table.colReorder.reset();
						_this.table.state.clear();
						window.location.reload();
					}
				} );
			}


			// 		// Printing Functionality
			if ( _this.customOptions.wpcl_print ) {
				dataTablesOptions.buttons.push( {
						extend : 'print',
						title  : productTitle,
						text   : wpcl_script_vars.trans.printbtn,

						available : function ( dt, config ) {
							return typeof _this.customOptions.wpcl_print !== 'undefined' && _this.customOptions.wpcl_print === true;
						},

						customize : function ( win ) {
							$( win.document.body )
								.css( 'background-color', '#fff' )
								.css( 'padding', '1px' );

							$( win.document.body ).find( 'table' )
								.addClass( 'compact' )
								.css( 'font-size', 'inherit' )
								.css( 'border', '0px' )
								.css( 'border-collapse', 'collapse' );

							$( win.document.body ).find( 'table th' )
								.css( 'padding', '5px 8px 8px' )
								.css( 'background-color', '#f1f1f1' )
								.css( 'border-bottom', '0px' );

							$( win.document.body ).find( 'table td' )
								.css( 'border', '1px solid #dfdfdf' )
								.css( 'padding', '8px' );

							$( win.document.body ).find( 'table tr:nth-child(even)' )
								.css( 'background-color', '#f9f9f9' );
						}
					},
				);
			}

			if ( _this.customOptions.wpcl_export_excel ) {

				// Excel export
				dataTablesOptions.buttons.push( {
					extend    : 'excelHtml5',
					title     : fileName,
					available : function ( dt, config ) {
						return typeof _this.customOptions.wpcl_export_excel !== 'undefined' && _this.customOptions.wpcl_export_excel === true;
					}
				} );
			}


			// CSV export
			if ( _this.customOptions.wpcl_export_csv ) {
				dataTablesOptions.buttons.push( {
					extend    : 'csvHtml5',
					title     : fileName,
					available : function ( dt, config ) {
						return typeof _this.customOptions.wpcl_export_csv !== 'undefined' && _this.customOptions.wpcl_export_csv === true;
					}
				} );
			}


			if ( _this.customOptions.wpcl_export_pdf ) {

				// PDF export
				dataTablesOptions.buttons.push( {
					extend      : 'pdfHtml5',
					title       : pdfTitle,
					orientation : pdfOrientation,
					pageSize    : pdfPageSize,
					filename    : fileName,

					available : function ( dt, config ) {
						return typeof _this.customOptions.wpcl_export_pdf !== 'undefined' && _this.customOptions.wpcl_export_pdf === true;
					},
					customize : function ( doc ) {
						doc.styles.tableHeader.fillColor   = '#f1f1f1';
						doc.styles.tableHeader.color       = '#000';
						doc.styles.tableBodyEven.fillColor = '#f9f9f9';
						doc.styles.tableBodyOdd.fillColor  = '#fff';
					}
				} );
			}


			if ( staticTableMode ) {
				delete dataTablesOptions.data;
				delete dataTablesOptions.columns;
			}


			_this.table = _this.$dataTable.DataTable( dataTablesOptions );


			_this.$extraActions.show();
			if ( _this.$extraButtons.length > 0 ) {
				_this.$extraButtons.show();
			}


			if ( _this.customOptions.wpcl_index ) {
				_this.table.on( 'order.dt search.dt', function () {
					_this.table.column( 0, {
						search : 'applied',
						order  : 'applied'
					} ).nodes().each( function ( cell, i ) {
						var indexNum   = i + 1;
						cell.innerHTML = '<p>' + indexNum + '</p>';
					} );
				} ).draw();
			}

			// Update email list on row selection

			var $emailSelected = $( '.wpcl-btn-email-selected' );

			// It might be hidden through PHP when it's not needed
			if ( $emailSelected.length > 0 ) {
				$emailSelected.hide();

				$emailSelected.on( 'click', function ( event ) {

					var href = $( event.currentTarget ).attr( 'href' );

					if ( href.indexOf( "mailto" ) === -1 ) {
						console.log( 'No rows seem to be selected' );
						return false;
					} else {
						return true;
					}


				} );


				_this.table.on( 'select', function ( e, dt, type, indexes ) {
					var emails   = $.map( _this.table.rows( '.selected' ).nodes(), function ( item ) {
						return $( item ).data( 'email' );
					} );
					var emailBcc;
					if(emailSeperator == 'semicolon') {
						emailBcc = emails.join( ";" );
					} else {
						emailBcc = emails.join( "," );
					}
					$emailSelected.attr( 'href', 'mailto:?bcc=' + emailBcc );
					if ( emailBcc ) {
						$emailSelected
							.removeAttr( 'disabled' )
							.show();
					}
				} );

				// Update email list on row deselection
				_this.table.on( 'deselect', function ( e, dt, type, indexes ) {
					var emails = $.map( _this.table.rows( '.selected' ).nodes(), function ( item ) {
						return $( item ).data( 'email' );
					} );
					console.log( emails );
					var emailBcc = emails.join( "," );
					$emailSelected.attr( 'href', 'mailto:?bcc=' + emailBcc );
					if ( emailBcc ) {
						$emailSelected.removeAttr( 'disabled' );
					} else {
						$emailSelected
							.attr( 'disabled', 'true' )
							.attr( 'href', '#' )
							.hide();
					}
				} );


			}

			console.log( 'Setup dataTables', _this.customColumns );
		},


	};


	// doc.ready
	$( function () {
		wpclOrdersTable.init();
	} );


} )( jQuery );
