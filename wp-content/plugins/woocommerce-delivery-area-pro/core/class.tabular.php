<?php

/**
 * FlipperCode_List_Table_Helper Class File.
 *
 * @package Core
 * @author Flipper Code <hello@flippercode.com>
 */

if ( ! class_exists( 'FlipperCode_List_Table_Helper' ) ) {

	/**

	 * Include the main wp-list-table file.

	 */

	if ( ! class_exists( 'WP_List_Table' ) ) {

		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'; }

	/**
	 * Extend WP_LIST_TABLE to simplify table listing.
	 *
	 * @package Core
	 * @author Flipper Code <hello@flippercode.com>
	 */

	class FlipperCode_List_Table_Helper extends WP_List_Table {


		/**
		 * Plugin name.
		 * @var string
		 */
		var $plugin;
		
		
		/**
		 * Table name.
		 * @var string
		 */

		var $table;

		/**

		 * Custom SQL Query to fetch records.

		 *

		 * @var string

		 */

		var $sql;

		/**

		 * Action over records.

		 *

		 * @var array

		 */

		var $actions = array( 'edit', 'delete' );
		


		var $has_popup = array();

		/**

		 * Timestamp Column in table.

		 *

		 * @var string

		 */

		var $currenttimestamp_field;

		/**

		 * Text Domain for multilingual.

		 *

		 * @var string

		 */

		var $textdomain;

		/**

		 * Singular label.

		 *

		 * @var string

		 */

		var $singular_label;

		/**

		 * Plural label.

		 *

		 * @var string

		 */

		var $plural_label;

		/**

		 * Show add navigation at the top.

		 *

		 * @var boolean

		 */

		var $show_add_button = true;

		/**

		 * Ajax based listing

		 *

		 * @var boolean

		 */

		var $ajax = false;

		/**

		 * Columns to be displayed.

		 *

		 * @var array

		 */

		var $columns;

		/**

		 * Columns to be sortable.

		 *

		 * @var array

		 */

		var $sortable;

		/**

		 * Fields to be hide.

		 *

		 * @var  array

		 */

		var $hidden;

		/**

		 * Records per page.

		 *

		 * @var integer

		 */

		var $per_page = 10;

		/**

		 * Slug for the manage page.

		 *

		 * @var string

		 */

		var $admin_listing_page_name;

		/**

		 * Slug for the add or edit page.

		 *

		 * @var string

		 */

		var $admin_add_page_name;

		/**

		 * Response

		 *

		 * @var string

		 */

		var $response;

		/**

		 * Display string at the top of the table.

		 *

		 * @var string

		 */

		var $toptext;

		/**

		 * Display string at the bottom of the table.

		 *

		 * @var [type]

		 */

		var $bottomtext;

		/**

		 * Primary column of the table.

		 *

		 * @var string

		 */

		var $primary_col;

		/**

		 * Column where to display actions navigation.

		 *

		 * @var string

		 */

		var $col_showing_links;

		/**

		 * Call external function when actions executed.

		 *

		 * @var array

		 */

		var $extra_processing_on_actions;

		/**

		 * Current action name.

		 *

		 * @var string

		 */

		var $now_action;

		/**

		 * Table prefix.

		 *

		 * @var string

		 */

		var $prefix;

		/**

		 * Current page's records.

		 *

		 * @var array

		 */

		var $found_data;

		/**

		 * Total # of records.

		 *

		 * @var int

		 */

		var $items;

		/**

		 * All Records.

		 *

		 * @var array

		 */

		var $data;

		/**

		 * Columns to be excluded in search.

		 *

		 * @var array

		 */

		var $searchExclude;

		


		var $searchMapping;

		/**

		 * Actions executed in bulk action.

		 *

		 * @var array

		 */

		var $bulk_actions;

		/*

		Show header.

		* @var bool

		*/

		var $no_header = false;



		var $translation = array();


		
		var $form_id;
		
		

		var $form_class;
		


		var $plugin_dir;
		
		
		

		var $plugin_url;
				


		var $doing_bulk_action = false;



		var $modal_response = [];
		/**

		 * Constructer method

		 *

		 * @param array $tableinfo Listing configurations.

		 */

		public function __construct( $tableinfo ) {



			global $wpdb;

			$this->prefix = $wpdb->prefix;

			foreach ( $tableinfo as $key => $value ) {    // Initialise constuctor based provided values to class variables.

				$this->$key = $tableinfo[ $key ];

			}

			parent::__construct(

				array(

					'singular' => $this->singular_label,

					'plural'   => $this->plural_label,

					'ajax'     => $this->ajax,

				)

			);

			$this->init_listing();

		}

		private function fc_get_current_page_capability(){
			
			$capability_to_check = sanitize_text_field($_GET['page']);
			if( strpos( $capability_to_check, 'overview' ) !== false )
			$capability_to_check = str_replace('view', 'admin', $capability_to_check);
			return $capability_to_check;
				
			
		}
			
		public function authenticate_action_requests(){
			
			//Authentication
			if( !is_user_logged_in() )
			wp_die( 'You are not allowed to save changes!' );	
			
			//Authorization			
			if ( ! current_user_can( $this->fc_get_current_page_capability() ) )
			wp_die( 'You are not allowed to save changes!' );
			
			//Nonce Verification Bulk Action
			if( $this->doing_bulk_action && $this->current_action() == 'delete' ) {
				if(!isset( $_REQUEST['_wpnonce'] ) || empty($_REQUEST['_wpnonce']) )
				wp_die('You are not allowed to save changes!');
				if( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-'.$this->_args['plural'] ) )
				wp_die('You are not allowed to save changes!');
			}
			
			//Nonce Verification Custom Action
			if( isset($_GET['doaction']) && !empty($_GET['doaction']) && isset($_GET['ca']) && $_GET['ca']=='yes' ){
							
				if( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'action-'.sanitize_text_field( wp_unslash( $_GET['doaction'] ) ) ) )
				wp_die('You are not allowed to save changes!');
				
			}
						
		}
			
		/**

		 * Initialize table listing.

		 */

		public function init_listing() {

			if ( ! empty( $this->currenttimestamp_field ) ) {  // Load extra resources if we want to show time based filters in listing table.

				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_register_style('jquery-style', plugins_url($this->plugin).'/assets/css/smoothness/jquery-ui.css');

			}

			$this->prepare_items();
			
			if ( isset($_GET['doaction']) && !empty( $_GET['doaction'] ) && !empty( $_GET[ $this->primary_col ] ) ) {
				
				$this->authenticate_action_requests();
				
				$this->now_action = $function_name = sanitize_text_field( wp_unslash( $_GET['doaction'] ) );
				
				if ( false != strpos( sanitize_text_field( wp_unslash( $_GET['doaction'] ) ), '-' ) ) {
					$function_name = str_replace( '-','',$function_name ); }
					$this->$function_name();

			} else {
				$this->listing();
			}



		}

		

		/**

		 * Edit action.

		 */

		public function edit() {}

		

		/**

		 * Delete action.

		 */

		public function delete() {

			global $wpdb;

			if ( isset( $_GET[ $this->primary_col ] ) ) {

				$id    = intval( wp_unslash( $_GET[ $this->primary_col ] ) );

			    if( method_exists( $this, 'post_delete') ){
					
					$this->post_delete();
					
				}
				
				$wpdb->delete( $this->table, array( $this->primary_col => $id ), array( '%d' ) );

				$this->prepare_items();

				$this->response['success'] = $this->translation['delete_msg'];

			}

			$this->listing();

		}

		/**

		 * Display records listing.

		 */

		public function listing() {



			?>



		<div class="flippercode-ui">

			<div class="fc-main">

				<div class="fc-container">

					<div class="fc-divider">

					<div class="fc-back">

						<div class="fc-12">

							<div class="wpgmp_menu_title">

								<h4 class="fc-title-blue"><?php echo esc_html( $this->translation['manage_heading'] ); 

								$fc_add_record_url = esc_url( admin_url( 'admin.php?page=' . $this->admin_add_page_name ) );
								$fc_add_record_url = apply_filters('fc_manage_add_record_url', $fc_add_record_url);

								?>

								<span><a class="fc-new-link" target="_blank" href="<?php echo $fc_add_record_url; ?>"><?php echo esc_html( $this->translation['add_button'] ); ?></a>

								</span>

								</h4>

							</div>

						<div class="wpgmp-overview">

						    <?php $this->show_notification( $this->response ); ?>

							<fieldset>
							
							<?php
							
								$form_attr = '';
								$plugin_form_class = '';
								if(!empty($this->form_id)){
									$form_attr .= "id = {$this->form_id}";
								}
								if(!empty($this->form_class)){
									$plugin_form_class = $this->form_class;
								}
								$form_attr .= " class  = 'fc_manage_screen_form {$plugin_form_class}'";
								
							?>
							
							<form method="post" action="<?php echo admin_url( 'admin.php?page='.$this->admin_listing_page_name ); ?>" <?php echo $form_attr; ?> >
							
							<?php

							$this->search_box( 'search', 'search_id' );

							$this->display();

							?>

							<input type="hidden" name="row_id" value="" />

							<input type="hidden" name="operation" value="" />

							<?php //wp_nonce_field( 'wpgmp-nonce', '_wpnonce', true, true ); ?>

						</form>

						</fieldset>

							</div>

							</br>

						</div>

					</div>

</div></div></div></div>

			<?php

		}

		/**

		 * Reset primary column ID.

		 */

		public function unset_id_field() {



			if ( array_key_exists( $this->primary_col, $this->columns ) ) {

				unset( $this->columns[ $this->primary_col ] );  }

		}

		/**

		 * Get sortable columns.

		 *

		 * @return array Sortable columns names.

		 */

		function get_sortable_columns() {



			if ( empty( $this->sortable ) ) {



				$sortable_columns[ $this->primary_col ] = array( $this->primary_col, false );

			} else {



				foreach ( $this->sortable as $sortable ) {

					$sortable_columns[ $sortable ] = array( $sortable, false );

				}

			}

			return $sortable_columns;

		}

		/**

		 * Get columns to be displayed.

		 *

		 * @return array Columns names.

		 */

		function get_columns() {



			$columns = array( 'cb' => '<input type="checkbox" />' );



			if ( ! empty( $this->sql ) ) {

				global $wpdb;

				$results = $wpdb->get_results( $this->sql );

				if ( is_array( $results ) && ! empty( $results ) ) {

					foreach ( $results[0] as $column_name => $column_value ) {    // Get all columns by provided returned by sql query(Preparing Columns Array).

						if ( array_key_exists( $column_name, $this->columns ) ) {

							$this->columns[ $column_name ] = $this->columns[ $column_name ];

						} else {

							$this->columns[ $column_name ] = $column_name;

						}

					}

				}

			} else {

				if ( empty( $this->columns ) ) {

					global $wpdb;

					foreach ( $wpdb->get_col( 'DESC ' . $this->table, 0 ) as $column_name ) {  // Query all column name usind DESC (Preparing Columns Array).

						$this->columns[ $column_name ] = $column_name;

					}

				}

			}



			$this->unset_id_field(); // Preventing Id field to showup in Listing.



			// This is how we initialise all columns dynamically instead of statically (normally we write each column name here) in get_columns function definition :).

			foreach ( $this->columns as $dbcolname => $collabel ) {

				$columns[ $dbcolname ] = $collabel;

			}



			return $columns;

		}

		/**

		 * Column where to display actions.

		 *

		 * @param  array  $item        Record.

		 * @param  string $column_name Column name.

		 * @return string              Column output.

		 */

		function column_default( $item, $column_name ) {

			// Return Default values from db except current timestamp field. If currenttimestamp_field is encountered return formatted value.

			if ( ! empty( $this->currenttimestamp_field ) && $column_name == $this->currenttimestamp_field ) {

				$return = date( 'F j, Y', strtotime( $item->$column_name ) );

			} elseif ( $column_name == $this->col_showing_links ) {

				$actions = array();
				
				foreach ( $this->actions as $action ) {

					$action_slug  = sanitize_title( $action );
					$action_label = ucwords( $action );
					if ( 'delete' == $action_slug ) {
						$actions[ $action_slug ] = apply_filters('fc_manage_delete_link', sprintf( '<a href="javascript:void(0);" data-item-id="'.$item->{$this->primary_col}.'" data-page-slug="'.$this->admin_listing_page_name.'" data-unique-key="'.$this->primary_col.'" data-record-type="'.$this->singular_label.'" data-action="delete" data-toggle="modal" data-target="#delete_fc_record" data-current-page="'.admin_url('admin.php?page='.sanitize_text_field($_GET['page'])).'" data-target-tbl="'.$this->table.'">' . $action_label . '<span class="delete_icon"></span></a>', $item->{$this->primary_col} ));
					}
					elseif ( 'edit' == $action_slug ) {
						
						$actions[ $action_slug ] = apply_filters('fc_manage_edit_link',sprintf( '<a href="?page=%s&doaction=%s&' . $this->primary_col . '=%s">' . $action_label . '<span class="edit_icon"></span></a>', $this->admin_add_page_name, $action_slug, $item->{$this->primary_col}) );
					}else{
						
						$action_slug_class = apply_filters( 'fc_tabular_action_class',$action_slug,$item,$this->admin_listing_page_name );
						
						if(array_key_exists($action_slug, $this->has_popup) ){
							$data = $this->has_popup[$action_slug];
							$heading = $data['heading'];
							$icon = $this->plugin_url.'assets/images/'.$data['icon'];
							$content = $data['content'];
							$button_text = $data['button_text'];
							$action_peform_via = (!isset($data['perform_via'])) ? 'ajax' : $data['button_text'];
							$actions[ $action_slug ] = apply_filters('fc_manage_'.$action_slug.'_link', sprintf( '<a class="fc_backend_custom_action" href="javascript:void(0);" data-action-heading="'.$heading.'" data-action-icon="'.$icon.'" data-action-content="'.$content.'" data-button-text="'.$button_text.'" data-perform-via="'.$action_peform_via.'" data-item-id="'.$item->{$this->primary_col}.'" data-page-slug="'.$this->admin_listing_page_name.'" data-unique-key="'.$this->primary_col.'" data-record-type="'.$this->singular_label.'" data-action="'.$action_slug.'" data-toggle="modal" data-target="#modal_for_custom_action" data-current-page="'.admin_url('admin.php?page='.sanitize_text_field($_GET['page'])).'" data-target-tbl="'.$this->table.'">' . $action_label . '<span class="custom-action '.$action_slug.'_icon"></span></a>', $item->{$this->primary_col}) );

						}else{

							$custom_action_nonce = wp_create_nonce( 'action-' . $action_slug );
							$actions[ $action_slug ] = sprintf( '<a class="'.$action_slug_class.'" href="?page=%s&doaction=%s&' . $this->primary_col . '=%s&ca=yes&_wpnonce=%s">' . $action_label . '<span class="custom-action '.$action_slug.'_icon"></span></a>', $this->admin_listing_page_name, $action_slug, $item->{$this->primary_col}, $custom_action_nonce );
						}
						
						
						
					}
					
				}
				
				return sprintf( '%1$s %2$s', $item->{$this->col_showing_links}, $this->row_actions( $actions ) );



			} else {

				$return = $item->$column_name;

			}

			return $return;

		}



		/**

		 * Checkbox for each record.

		 *

		 * @param  array $item Record.

		 * @return string       Checkbox Element.

		 */

		function column_cb( $item ) {

			return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item->{$this->primary_col} ); }

		/**

		 * Sorting Order

		 *

		 * @param  string $a First element.

		 * @param  string $b Second element.

		 * @return string    Winner element.

		 */

		function usort_reorder( $a, $b ) {



			$orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';

			$order   = ( ! empty( $_GET['order'] ) ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'asc';

			$result  = strcmp( $a[ $orderby ], $b[ $orderby ] );

			return ( 'asc' == $order ) ? $result : -$result;

		}

		/**

		 * Get bulk actions.

		 *

		 * @return array Bulk action listing.

		 */

		function get_bulk_actions() {

			$actions = (array) $this->bulk_actions;

			return $actions;

		}

		/**

		 * Get records from ids.

		 *

		 * @return array Records ID.

		 */

		function get_user_selected_records() {



			$ids = isset( $_REQUEST['id'] ) ? wp_unslash( $_REQUEST['id'] ) : array();

			if ( is_array( $ids ) ) {

				$ids = implode( ',', $ids ); }

			if ( ! empty( $ids ) ) {

				return $ids; }

		}
		
		function no_items(){
			
			if(!empty($this->translation['no_records_found']))
			echo $this->translation['no_records_found'];
			else
			_e( 'No items found.' );
			
		}

		/**

		 * Process bulk actions.

		 */

		function process_bulk_action() {

			global $wpdb;

			$this->now_action = $this->current_action();

			$ids  = $this->get_user_selected_records();

			if(!empty($this->current_action())) {
				
				$this->doing_bulk_action = true;
				$this->authenticate_action_requests();
			
			}else{
				$this->doing_bulk_action = false;
			}
			
			if ( 'delete' === $this->current_action() && ! empty( $ids ) ) {

				//Prepared Query For Safe Execution
				$ids = explode( ',', $ids );
				$recordsCount = count( $ids );
				$recordsPlaceholders = array_fill( 0, $recordsCount, '%d' );
				$placeholdersForRecords = implode( ',', $recordsPlaceholders );
				$query = "DELETE FROM {$this->table} WHERE {$this->primary_col} IN ( $placeholdersForRecords )";
				$del = $wpdb->query( $wpdb->prepare( $query, $ids ) );
			
				if( method_exists( $this, 'post_bulk_delete') ){
						$this->post_bulk_delete();
				}

				if( isset($this->translation['bulk_delete_msg']) && !empty($this->translation['bulk_delete_msg']) ){
				   $this->response['success'] = $this->translation['bulk_delete_msg'];
				}else{
				   $this->response['success'] = $this->translation['delete_msg'];	
				}



			} else if ( 'export_location_csv' === $this->current_action() || 'export_csv' === $this->current_action() && ! empty( $ids ) ) {

				ob_clean();

				global $wpdb;

				$ids = $this->get_user_selected_records();
				$ids = explode(',',$ids);
				$exportCount = count($ids);
				$stringPlaceholders = array_fill(0, $exportCount, '%s');
				$placeholdersForIds = implode(', ', $stringPlaceholders);
				$prepared_query = ( ! empty( $ids )) ? " WHERE {$this->primary_col} IN ($placeholdersForIds) " : '';

				$columns      = array_keys( $this->columns );

				$columns      = ( count( $columns ) == 0 ) ? $columns[0] : implode( ',', $columns );

				if(empty( $this->sql )){
					
					//Sanitise the order and order by clause now using sanitize_sql_orderby
					$order_by_clause = sanitize_sql_orderby( $this->primary_col.' desc' );
					
					//Prepared Query
					$query = "SELECT $columns FROM ".$this->table.$prepared_query." Order By {$order_by_clause}";
					$data = $wpdb->get_results( $wpdb->prepare($query, $ids), ARRAY_A );
				
				}else{
					//Prepared query $this->sql
					$query =  $this->sql;
					$data = $wpdb->get_results( $query,ARRAY_A );
				}


				$tablerecords = array();

				if ( ! empty( $this->sql ) ) {

					$col_key_value = array();

					foreach ( $data[0] as $key => $val ) {  // Make csv's first row column heading according to columns selected in custom sql.

						$col_key_value[ $key ] = $key;

					}

					$tablerecords[] = $col_key_value;

				} else {

					$tablerecords[] = $this->columns;        // Make csv's first row column heading according automatic detected columns.

				}

				foreach ( $data as $entry ) {

					if ( array_key_exists( $this->primary_col, $entry ) ) {

						unset( $entry[ $this->primary_col ] ); }

					$tablerecords[] = $entry;



				}

				header( 'Content-Type: application/csv' );

				header( "Content-Disposition: attachment; filename=\"{$this->plural_label}-Records.csv\";" );

				header( 'Pragma: no-cache' );

				$fp = fopen( 'php://output', 'w' );

				foreach ( $tablerecords as $record ) {

					fputcsv( $fp, $record );

				}

				fclose( $fp );

				exit;



			}

		}

		/**

		 * Show notification message based on response.

		 *

		 * @param  array $response Response.

		 */

		public function show_notification( $response ) {



			if ( ! empty( $response['error'] ) ) {

				$this->show_message( $response['error'], true ); 
			} elseif ( ! empty( $response['success'] ) ) {

				$this->show_message( $response['success'] ); 
			}



		}

		/**

		 * Message html element.

		 *

		 * @param  string  $message  Message.

		 * @param  boolean $errormsg Error or not.

		 * @return string           Message element.

		 */

		public function show_message( $message, $errormsg = false ) {



			if ( empty( $message ) ) {

				return; 
			}

			if ( $errormsg ) {

				echo "<div class='fc-msg fc-msg-info'>".esc_html($message)."</div>";

			} else {

				echo "<div class='fc-msg fc-success'>".esc_html($message)."</div>";
			}



		}

		/**

		 * Prepare records before print.

		 */

		function prepare_items() {

			global $wpdb;
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->process_bulk_action();
			
			$query = ( empty( $this->sql ) ) ? 'SELECT * FROM ' . $this->table : $this->sql;
			
			if ( isset( $_GET['page'] ) && isset( $_REQUEST['s'] ) ) {
				$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
				$search = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
			} else {
				$page = '';
				$search = '';
			}

			if(!$this->noSql){ 

				if ( $this->admin_listing_page_name == $page && '' != $search ) {

					$s = $search;
					$first_column = '';
					$remaining_columns  = array();
					$prepare_query_with_placeholders = '';
					$prepare_args_values = array();
					
					foreach ( $this->columns as $column_name => $columnlabel ) {

						if ( "{$this->primary_col}" == $column_name ) {
							 continue;
						} else {
							
							if ( empty( $first_column ) ) {
								
								$first_column = $column_name;
								$prepare_args_values[] = $wpdb->esc_like($s);
								$prepare_query_with_placeholders = " WHERE {$column_name} LIKE '%%%s%%'";


							} else {
								
								$remaining_columns[] = $column_name;
								if ( isset($this->searchExclude) && !empty($this->searchExclude) && !in_array( $column_name, $this->searchExclude ) ) {
									$prepare_args_values[] = $wpdb->esc_like($s);
									$prepare_query_with_placeholders .= " or {$column_name} LIKE '%%%s%%'";

								}

								if(!isset($this->searchExclude) && empty($this->searchExclude) ){
									$prepare_args_values[] = $wpdb->esc_like($s);
									$prepare_query_with_placeholders .= " or {$column_name} LIKE '%%%s%%'";
								}
									
							}
						}
					}

					$prepared_query_data = array( 'query' => 'SELECT * FROM ' . $this->table , 'placeholders' => $prepare_query_with_placeholders, 'values' => $prepare_args_values );

					$prepared_query_data = apply_filters('fc_manage_search_query', $prepared_query_data , sanitize_text_field( wp_unslash( $_GET['page'] ) ) );

					$query_to_run = $prepared_query_data['query'];
					$prepare_query_with_placeholders = $prepared_query_data['placeholders'];
					$prepare_args_values = $prepared_query_data['values'];

					//Prepared Query Because It Has User Inputs
					$this->data = $wpdb->get_results(  $wpdb->prepare( $query_to_run. $prepare_query_with_placeholders. ' order by '.$this->primary_col.' desc', $prepare_args_values )  );


					if(is_array($this->data) && count($this->data) == 0 && is_array($this->searchMapping) && count($this->searchMapping) > 0) {
						$remaining_columns  = array();
						$search_item = preg_grep('~' . $s . '~i', $this->searchMapping);

						$search_data = array_keys($search_item);
						
						if(!empty($search_data)) {


							foreach ( $this->columns as $column_name => $columnlabel ) {

								if ( "{$this->primary_col}" == $column_name ) {
									 continue;
								} else {
									
									if ( empty( $first_column ) ) {
										
										$first_column = $column_name;
										$prepare_args_values[] = $wpdb->esc_like($search_data[0]);
										$prepare_query_with_placeholders = " WHERE {$column_name} LIKE '%%%s%%'";


									} else {
										
										$remaining_columns[] = $column_name;
										if ( isset($this->searchExclude) && !empty($this->searchExclude) && !in_array( $column_name, $this->searchExclude ) ) {
											$prepare_args_values[] = $wpdb->esc_like($search_data[0]);
											$prepare_query_with_placeholders .= " or {$column_name} LIKE '%%%s%%'";

										}

										if(!isset($this->searchExclude) && empty($this->searchExclude) ){

											$prepare_args_values[] = $wpdb->esc_like($search_data[0]);
											$prepare_query_with_placeholders .= " or {$column_name} LIKE '%%%s%%'";
										}
											
									}
								}
							}
						}

						$prepared_query_data = array( 'query' => 'SELECT * FROM ' . $this->table , 'placeholders' => $prepare_query_with_placeholders, 'values' => $prepare_args_values );

						$prepared_query_data = apply_filters('fc_manage_search_mapping_query', $prepared_query_data , sanitize_text_field( wp_unslash( $_GET['page'] ) ) );

						$query_to_run = $prepared_query_data['query'];
						$prepare_query_with_placeholders = $prepared_query_data['placeholders'];
						$prepare_args_values = $prepared_query_data['values'];

						//Prepared Query Because It Has User Inputs
						$this->data = $wpdb->get_results(  $wpdb->prepare( $query_to_run. $prepare_query_with_placeholders. ' order by '.$this->primary_col.' desc', $prepare_args_values )  );
					}
					
				}
				else if ( isset($_GET['orderby']) && ! empty( $_GET['orderby'] ) && isset($_GET['order']) && ! empty( $_GET['order'] ) ) {
									
					$_GET['orderby'] = sanitize_text_field( $_GET['orderby'] );
					$_GET['order'] = sanitize_text_field( $_GET['order'] );
					$orderby = ( !empty( $_GET['orderby'] ) ) ? wp_unslash( $_GET['orderby'] ) : $this->primary_col;
					$order   = ( !empty( $_GET['order'] ) ) ? wp_unslash( $_GET['order'] ) : 'asc';

						$query_to_run  = $query;
						$query_to_run .= " order by {$orderby} {$order}";
						//Basic static query with no user inputs
						$this->data = $wpdb->get_results( $query_to_run ); 
						
					}
				 else {

						
						$query_to_run = $query;
						if ( ! empty( $this->currenttimestamp_field ) ) {
						$query_to_run = $this->filter_query( $query_to_run ); }
						$query_to_run .= " order by {$this->primary_col} desc";
						//Basic static query with no user inputs
						$query_to_run = apply_filters('fc_manage_page_default_query', $query_to_run , sanitize_text_field( wp_unslash( $_GET['page'] ) ) );
						$this->data = $wpdb->get_results( $query_to_run );
						
					}

			}else{

				if(isset($this->external) && !empty($this->external)){
					$this->data   = $this->external;
				}

			}

			$current_page = apply_filters('fc_tabular_set_pagination_page',$this->get_pagenum()) ;
			$total_items  = count( $this->data );
			if ( is_array( $this->data ) && ! empty( $this->data ) ) {
				$this->found_data = @array_slice( $this->data, ( ( $current_page - 1 ) * $this->per_page ), $this->per_page );
			} else {
				$this->found_data = array();
			}

			$p_data = array( 'total_items' => $total_items,	'per_page' => $this->per_page );
			$this->set_pagination_args($p_data);
			$this->items = $this->found_data;
		}



	}

}

