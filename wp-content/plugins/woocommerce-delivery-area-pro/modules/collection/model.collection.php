<?php
/**
 * Class: WDAP_Model_Collection
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.0.0
 * @package woo-delivery-area-pro
 */


if ( ! class_exists( 'WDAP_Model_Collection' ) ) {

	/**
	 * Setting model for Plugin Options.
	 *
	 * @package woo-delivery-area-pro
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WDAP_Model_Collection extends FlipperCode_Model_Base {


		/**
		 * Intialize Backup object.
		 */
		public $validations = array(
			'wdap_collection_title' => array(
				'req' => 'Please Enter Delivery Area Title',
			)
		);
		private $dboptions;


		function __construct() {
			$this->table = WDAP_TBL_FORM;
			$this->unique = 'id';
			$this->dboptions = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );

		}

			/**
			 * Admin menu for Settings Operation
			 *
			 * @return array Admin menu navigation(s).
			 */
		function navigation() {

			return array(
				'wdap_add_collection' => esc_html__( 'Add Delivery Area', 'woo-delivery-area-pro' ),
				'wdap_manage_collection' => esc_html__( 'Manage Delivery Areas', 'woo-delivery-area-pro' ),
			);

		}

			/**
			 * Install table associated with Rule entity.
			 *
			 * @return string SQL query to install post_widget_rules table.
			 */
		public function install() {

			global $wpdb;
			$sql = 'CREATE TABLE ' . $wpdb->prefix . 'wdap_collection (
				 id int(10) unsigned AUTO_INCREMENT ,
				 title varchar(200) NOT NULL,
				 applyon varchar(100) NOT NULL,
				 chooseproducts LONGTEXT NOT NULL,
				 selectedcategories LONGTEXT NOT NULL,
				 exclude_products LONGTEXT NOT NULL,
				 assignploygons LONGTEXT NOT NULL,
				 wdap_map_region varchar(100) NOT NULL,
				 wdap_map_region_value LONGTEXT NOT NULL,
				 extra_settings LONGTEXT NULL,
				PRIMARY KEY  (id)
				)';

			return $sql;
		}

		/**
		 * Get Rule(s)
		 *
		 * @param  array $where  Conditional statement.
		 * @return array         Array of Rule object(s).
		 */
		public function fetch( $where = array() ) {

			$objects = $this->get( $this->table, $where );
			if ( isset( $objects ) ) {
				return $objects;
			}
		}

		function save_polygon_cordinates() {
			
			if ( ! empty( $_POST['store_address_json'] ) ) {
				$_POST['store_address_json'] = sanitize_text_field( $_POST['store_address_json'] );
				$_POST['store_address_json'] = str_replace( "'", '"', $_POST['store_address_json'] );
				$_POST['store_address_json'] = stripcslashes( $_POST['store_address_json'] );
			}
			$entityID = '';
			if ( isset( $_REQUEST['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
			}

			// Nonce Verification
			if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {
				die( 'Please reload page and submit the form again...' );
			}

			$form_errors = array();
			$mandatory_fields = array( 'wdap_collection_title' => 'Delivery Area Title' );
			foreach ( $mandatory_fields as $key => $field ) {
				if ( empty( $_POST[ $key ] ) ) {
					$form_errors[] = sprintf( esc_html__( '%s is a required field', 'woo-delivery-area-pro' ), $field );
				}
			}
			if ( $_POST['wdap_applyonRadio'] == 'Selected Products' && empty( $_POST['wdap_select_product'] ) ) {
				$form_errors[] = esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' );
			}

			if ( $_POST['wdap_applyonRadio'] == 'all_products_excluding_some' && empty( $_POST['exclude_products'] ) ) {
				$form_errors[] = esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' );
			}

			if ( $_POST['wdap_applyonRadio'] == 'selected_categories' && empty( $_POST['selectedcategories'] ) ) {
				$form_errors[] = esc_html__( 'Please select at least one category.', 'woo-delivery-area-pro' );
			}
			$polygonlen = strlen( $_POST['polygons_json'] );

			if ( $_POST['wdap_map_region'] == 'zipcode' && ( $polygonlen == 2 || $polygonlen == 0 ) && empty( $_POST['wdap_zip_codearea'] ) ) {
				if ( empty( $_POST['hasGoogleAPI'] ) ) {
					$form_errors[] = esc_html__( 'Please enter comma seperated zipcodes in textarea.', 'woo-delivery-area-pro' );
				} else {
					$form_errors[] = esc_html__( 'Please enter either zipcodes or draw a polygon on google map that represents your delivery area.', 'woo-delivery-area-pro' );
				}
			}

			if ( $_POST['wdap_map_region'] == 'country' && ( $polygonlen == 2 || $polygonlen == 0 ) && empty( $_POST['wdap_map_region_setting'] ) ) {
				$form_errors[] = esc_html__( 'Please select any country or draw any polygon shape.', 'woo-delivery-area-pro' );
			}
			if ( $_POST['wdap_map_region'] == 'sub-continents' && ( $polygonlen == 2 || $polygonlen == 0 ) && empty( $_POST['wdap_map_region_setting']['sub_continent'] ) ) {
				$form_errors[] = esc_html__( 'Please select any sub-continent or draw any polygon.', 'woo-delivery-area-pro' );
			}
			if ( isset($_POST['wdap_map_region']) && ($_POST['wdap_map_region'] == 'continents') && ( $polygonlen == 2 || $polygonlen == 0 ) && empty( $_POST['wdap_map_region_setting']['continent'] ) ) {
				$form_errors[] = esc_html__( 'Please select any continent or draw any polygon.', 'woo-delivery-area-pro' );
			}
			if ( $_POST['wdap_map_region'] == 'by_distance' ) {

				if ( empty( $_POST['wdap_store_address'] ) || empty( $_POST['store_address_json'] ) ) {
					$form_errors[] = esc_html__( 'Please specify nearest location to your store.', 'woo-delivery-area-pro' );
				}
				if ( empty( $_POST['wdap_store_address_range'] ) ) {
					$form_errors[] = esc_html__( 'Please specify distance range in kilometers where you allow / do delivery for orders.', 'woo-delivery-area-pro' );
				}
			}

			if ( count( $form_errors ) == 0 ) {
				if ( isset( $_POST['entityID'] ) ) {
					$entityID = intval( wp_unslash( $_POST['entityID'] ) );
				}
				if ( $entityID > 0 ) {
					$where['id'] = $entityID;
				} else {
					$where = '';
				}
				$data = array();
				$data['title']   = sanitize_text_field( wp_unslash( $_POST['wdap_collection_title'] ) );
				$data['applyon'] = sanitize_text_field( wp_unslash( $_POST['wdap_applyonRadio'] ) );
				if ( $_POST['wdap_applyonRadio'] == 'Selected Products' ) {
					if ( $_POST['wdap_select_product'] ) {
						$data['chooseproducts'] = serialize( $_POST['wdap_select_product'] );
					}
				}
				if ( $_POST['wdap_map_region'] == 'zipcode' ) {
					$allzipcodes      = sanitize_text_field( wp_unslash( $_POST['wdap_zip_codearea'] ) );
					$allzipcodesarray = array_map('trim', explode( ',', $allzipcodes ));
					$filteredallzipcodes = array_filter( $allzipcodesarray );
					$data['wdap_map_region_value'] = serialize( $filteredallzipcodes );
				} else if ( $_POST['wdap_map_region'] == 'by_distance' ) {
					$address = serialize(
						array(
							'range' => sanitize_text_field( wp_unslash( $_POST['wdap_store_address_range'] ) ),
							'address' => sanitize_text_field( wp_unslash( $_POST['store_address_json'] ) ),
						)
					);
					$data['wdap_map_region_value'] = $address;
				} else {
					$data['wdap_map_region_value'] = isset($_POST['wdap_map_region_setting']) ? serialize( $_POST['wdap_map_region_setting'] ):'';
				}
				if ( ! empty( $_POST['selectedcategories'] ) ) {
					$data['selectedcategories'] = serialize( $_POST['selectedcategories'] );
				}
				if ( ! empty( $_POST['exclude_products'] ) ) {
					$data['exclude_products'] = serialize( $_POST['exclude_products'] );
				}
				$data['assignploygons']  = wp_unslash( $_POST['polygons_json'] );
				$data['wdap_map_region'] = sanitize_text_field( wp_unslash( $_POST['wdap_map_region'] ) );

				$data = apply_filters('wdap_collection_info', $data, $where);

				$result = FlipperCode_Database::insert_or_update( WDAP_TBL_FORM, $data, $where );

				global $wpdb;
				$table_name = $wpdb->prefix . 'wdap_geocode_details'; 
				$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
				
				

				if(!empty($this->dboptions['wdap_googleapikey'])) {
					
					if ( $wpdb->get_var( $query ) == $table_name ) {
						if( $result != false && isset($_POST['wdap_map_region']) && $_POST['wdap_map_region'] == 'zipcode' && !empty($_POST['wdap_zip_codearea']) ){
							$this->save_collection_geocode_details($allzipcodesarray);
						}elseif ( $entityID > 0 && isset($_POST['wdap_map_region']) && $_POST['wdap_map_region'] == 'zipcode' && !empty($_POST['wdap_zip_codearea']) ) {
							$this->save_collection_geocode_details($allzipcodesarray);
						}
					}
				}
				

				if ( false === $result ) {
					 $response['error'] = esc_html__( 'Something went wrong. Please try again.', 'woo-delivery-area-pro' );
				} elseif ( $entityID > 0 ) {
					$response['success'] = esc_html__( 'Delivery area was updated successfully.', 'woo-delivery-area-pro' );
				} else {
					$response['success'] = esc_html__( 'Delivery area was saved successfully.', 'woo-delivery-area-pro' );
				}
				unset( $_POST );
				if ( $entityID > 0 ) {
					$_POST['entityID'] = $entityID;
				}
				$_POST['operation'] = 'save';
				return $response;
			} else {
				$_POST['polygon_submission_error'] = $form_errors;
			}
		}

		function save_collection_geocode_details($data){

			global $wpdb;
			$table_name = $wpdb->prefix.'wdap_geocode_details';
			$objects = $wpdb->get_results("select * from ".$table_name);
			$count = 0;
			$db_zipcode = array();
			foreach($objects as $obj){
				$db_zipcode[$count] = $obj->zipcode;
				$count++;
			}
			
			$final_data = array_intersect($db_zipcode,$data);
			$result = array_diff($data,$final_data);
			
			$where = '';
			
			foreach($result as $zipcod){
				if (strpos($zipcod, '*') !== false || empty($zipcod) ) {
					continue;
				}
				$table_data['zipcode'] = $zipcod;
				
				
				$result = FlipperCode_Database::insert_or_update( $table_name, $table_data, $where );
			}
			
		}

		function wdap_change_poly_coordinates_in_Js_Object( $data ) {

			$final_all_polygons = str_replace( '', '', $data );
			$final_all_polygons = json_decode( $final_all_polygons );
			$final_all_polygons = (array) $final_all_polygons;
			$onepolyset = array();
			$requirepolyset = array();
			if ( is_array( $final_all_polygons ) && count( $final_all_polygons ) > 0 ) {
				foreach ( $final_all_polygons as $key => $onepolygonsettings ) {

					if ( is_array( $onepolygonsettings ) && count( $onepolygonsettings ) > 0 ) {

						foreach ( $onepolygonsettings as $key1 => $onepolygonvalues ) {
							$onepolyset['id'] = isset( $onepolygonvalues->id ) ? $onepolygonvalues->id : '';
							$removequote = isset( $onepolygonvalues->coordinate ) ? $onepolygonvalues->coordinate : array();
							$onepolyset['coordinate'] = $removequote;

							if ( is_array( $removequote ) && count( $removequote ) > 0 ) {
								foreach ( $removequote as $key2 => $obj ) {
									$temp_obj = array();
									$temp_obj['lat'] = isset( $obj->lat ) ? (double) $obj->lat : '';
									$temp_obj['lng'] = isset( $obj->lng ) ? (double) $obj->lng : '';
									$onepolyset['coordinate'][ $key2 ] = (object) $temp_obj;
								}
							}
							$onepolyset['format'] = $onepolygonvalues->popygon_all_properties;
						}

					}

					if(!empty($onepolyset['coordinate'])){
						$requirepolyset[] = $onepolyset;
					}

				}
			}
			return $requirepolyset;
		}

		function create_update_collection($isDokan) {
			$response = array();
			if ( (isset( $_GET['page'] ) && ( sanitize_text_field( $_GET['page'] ) == 'wdap_add_collection' )) || ($isDokan && current_user_can('seller')) ) {

				if ( isset( $_POST['deliverypro_submission'] ) && ! empty( $_POST['deliverypro_submission'] ) ) {
			
					$response = $this->save_polygon_cordinates();
				}
					$modelFactory = new WDAP_Model();
					$ques_obj = $modelFactory->create_object( 'collection' );
					$wdap_js_lang['ajax_url'] = admin_url( 'admin-ajax.php' );
					$wdap_js_lang['nonce'] = wp_create_nonce( 'wdap-call-nonce' );
				if ( isset( $_GET['doaction'] ) && 'edit' == sanitize_text_field( $_GET['doaction'] ) && isset( $_GET['id'] ) ) {
					$ques_obj = $ques_obj->fetch( array( array( 'id', '=', intval( wp_unslash( sanitize_text_field( $_GET['id'] ) ) ) ) ) );
					$data = isset($ques_obj[0]) ? (array) $ques_obj[0] : array();
					$save_polygons = isset($data['assignploygons']) ? $data['assignploygons'] : array();
					$final_all_poligons = $this->wdap_change_poly_coordinates_in_Js_Object($save_polygons);
					$wdap_js_lang['polygons'] = $final_all_poligons;

					if(!empty($data['wdap_map_region']) && ($data['wdap_map_region']=='by_distance')){

						$zipcode_of_store_address = maybe_unserialize( $data['wdap_map_region_value'] );
						$zipaddress = json_decode( $zipcode_of_store_address['address'] );

						$range_circl_ui = array(
							'strokeColor'=>'#FF0000',
							'strokeOpacity'=>1,
							'strokeWeight'=>1,
							'fillColor'=>'#FF0000',
							'fillOpacity'=>0.5,
						);

						$range_circl_ui = apply_filters('range_circl_ui',$range_circl_ui);

						$location = array(
							'lat' => isset( $zipaddress->lat ) ? $zipaddress->lat : '',
							'lng' => isset( $zipaddress->lng ) ? $zipaddress->lng : '',
							'placezipcode' => isset( $zipaddress->placezipcode ) ? $zipaddress->placezipcode : '',
							'place_country_name' => isset( $zipaddress->place_country_name ) ? $zipaddress->place_country_name : '',
							'range' => isset( $zipcode_of_store_address['range'] ) ? $zipcode_of_store_address['range'] : '',
							'format'=>$range_circl_ui 

						);
						$wdap_js_lang['store_information'] = $location;
					}

				}
				if ( $this->dboptions ) {
					$wdap_js_lang['mapsettings']['zoom']      = ! empty( $this->dboptions['wdap_map_zoom_level'] ) ? $this->dboptions['wdap_map_zoom_level'] : '';
					$wdap_js_lang['mapsettings']['centerlat'] = ! empty( $this->dboptions['wdap_map_center_lat'] ) ? $this->dboptions['wdap_map_center_lat'] : '';
					$wdap_js_lang['mapsettings']['centerlng'] = ! empty( $this->dboptions['wdap_map_center_lng'] ) ? $this->dboptions['wdap_map_center_lng'] : '';
					$wdap_js_lang['mapsettings']['style']     = ! empty( $this->dboptions['wdap_map_style'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_map_style'] ) ) : '';
				}
					$icon_url = WDAP_IMAGES . '/pin_blue.png';
					$icon_url = apply_filters( 'wdap_map_icon', $icon_url );
					$wdap_js_lang['icon_url'] = esc_url( $icon_url );
					wp_enqueue_script( 'jquery' );
					wp_enqueue_script( 'polygonsdraw', esc_url( WDAP_JS . 'polygonsdraw.js' ), array( 'jquery' ) );
					wp_localize_script( 'polygonsdraw', 'wdap_backend_obj', $wdap_js_lang );
			}

			return $response;

		}



		/**
		 * Add or Edit Operation.
		 */
		function save() {

			 // Check for validation errors
			 $this->errors = isset( $_POST['polygon_submission_error'] ) ? $_POST['polygon_submission_error'] : '';
			if ( is_array( $this->errors ) && ! empty( $this->errors ) ) {
				$this->throw_errors();
			}

			$entityID = isset( $_POST['entityID'] ) ? $_POST['entityID'] : '';
			if ( $entityID ) {
				$entityID = intval( wp_unslash( $_POST['entityID'] ) );
			}
			if ( $entityID > 0 ) {
				$response['success'] = esc_html__( 'Delivery area was updated successfully.', 'woo-delivery-area-pro' );
			} else {
				$response['success'] = esc_html__( 'Delivery area was saved successfully.', 'woo-delivery-area-pro' );
			}
			return $response;
		}

		/**
		 * Delete rule object by id.
		 */
		public function delete() {
			if ( isset( $_GET['id'] ) ) {
				$id = intval( wp_unslash( sanitize_text_field( $_GET['id'] ) ) );
				$connection = FlipperCode_Database::connect();
				$this->query = $connection->prepare( "DELETE FROM $this->table WHERE $this->unique='%d'", $id );
				return FlipperCode_Database::non_query( $this->query, $connection );
			}
		}

		public function update_loc() {
			
			global $_POST;
			
			$result = '';
			$entityID = '';

			global $wpdb;
			$table_name = $wpdb->prefix.'wdap_geocode_details';

			if ( isset( $_REQUEST['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ); }

			if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'wdap-nonce' ) ) {

				die( 'Cheating...' );

			}
			$all_new_locations = json_decode(wp_unslash($_POST['fc-location-new-set'] ));
			
			if( is_array($all_new_locations) and !empty( $all_new_locations) )
			{
				foreach($all_new_locations as $location) {
					if(isset($location->result) && $location->result == 0){
						$data['geocode_details'] =  0;
					}else{
						$all_geocode_data = json_encode($location);
						$data['geocode_details'] =  $all_geocode_data;
					}
					
					if ( $location->id > 0 ) {
						$where[ $this->unique ] = $location->id;
						$result = FlipperCode_Database::insert_or_update( $table_name, $data, $where );
					} 
				}
			}
		
			if ( false === $result ) {
				$response['error'] = __( 'Something went wrong. Please try again.','woo-delivery-area-pro' );
			} elseif ( $entityID > 0 ) {
				$response['success'] = __( 'Location updated successfully','woo-delivery-area-pro' );
			} else {
				$response['success'] = __( 'Location added successfully.','woo-delivery-area-pro' );
			}

			return $response;
		}

	}
}
