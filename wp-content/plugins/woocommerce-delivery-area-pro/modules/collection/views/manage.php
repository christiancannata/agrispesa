<?php
/**
 * Class FlipperCode_List_Table_Helper File
 *
 * @author Flipper Code <hello@flippercode.com>
 * @package woo-delivery-area-pro
 */

global $wpdb;

$dboptions = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );

$table_name = $wpdb->prefix . 'wdap_geocode_details'; 
$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

if ( $wpdb->get_var( $query ) == $table_name ) { 
	
	$objects = $wpdb->get_results("select * from ".$table_name." where geocode_details IS NULL OR geocode_details = '' AND NOT geocode_details = '0' ");

	$geo_locations = array();
	if( is_array($objects) ) {
		foreach($objects as $object) {
			$geo_locations[$object->id] = $object->zipcode;
		}
	}
	$json = json_encode($geo_locations);
	
}


$form  = new WDAP_FORM();
if( isset($objects) && count($objects) > 0 && isset($dboptions['wdap_googleapikey']) && !empty($dboptions['wdap_googleapikey']) ) {
	$modalArgs = array( 'fc_modal_header' => __('Start Geocoding Process'),
				  'fc_modal_content' => '<div class="fc-msg fc-danger">Total '.count($objects).' zipcodes don\'t have latitude & longitude.</div><p>You can start geocoding process by clicking below link. and whole process may takes few minutes. Please don\'t close or refresh the window meanwhile.</p> <p><input type="button" name="fc-geocoding" class="fc-btn fc-btn-green fc-geocoding" value="Start Geocoding" /><div class="fcdoc-loader">
						   <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
						   <span class="sr-only">Loading...</span>
						  </div><textarea class="fc-location-data-set">'.$json.'</textarea><form enctype="multipart/form-data" action="" name="wdap-new-loc" method="post">'.wp_nonce_field('wdap-nonce').'<input type="hidden" value="update_loc" name="operation" /><textarea name="fc-location-new-set" class="fc-location-new-set"></textarea><span class="wdap-status"></span><input type="submit" name="fc-geocoding-updates" class="fc-btn fc-btn-green fc-geocoding-updates" value="Update Zipcodes" /></form></p>',
				  'fc_modal_initiator' => '.fc-open-modal',
				  'default_value' => '',
				  'class' => 'fc-modal fc-modal-show fc-12' );

echo WDAP_FORM::field_fc_modal('fc_import_modal', $modalArgs);  	
}


$form  = new WDAP_FORM();
echo wp_kses_post( $form->show_header() );

if ( class_exists( 'FlipperCode_List_Table_Helper' ) && ! class_exists( 'WDAP_Collection_Listing' ) ) {

	/**
	 * Class wpp_Rule_Table to display rules for manage.
	 *
	 * @author Flipper Code <hello@flippercode.com>
	 * @package woo-delivery-area-pro
	 */
	class WDAP_Collection_Listing extends FlipperCode_List_Table_Helper {
		/**
		 * Intialize class constructor.
		 *
		 * @param array $tableinfo Rules Table Informaiton.
		 */
		public function __construct( $tableinfo ) {

			parent::__construct( $tableinfo );
		}

		function column_chooseproducts( $record ) {

			if ( $record->applyon == 'All Products' ) {
				$html = '-';
				return $html;
			} else if ( $record->applyon == 'Selected Products' ) {
				$record = unserialize( $record->chooseproducts );
				if ( is_array( $record ) ) {
					 $html = '';
					foreach ( $record as $key => $value ) {
						$html .= '<div class="thumbanil_listing">';
						if ( get_the_post_thumbnail( $value, 'thumbnail' ) ) {
							$html .= get_the_post_thumbnail( $value, 'thumbnail' );
						} else {
							$html .= wc_placeholder_img( 'thumbnail' );
						}
						$html .= '<a href="' . get_the_permalink( $value ) . '">';
						$html .= get_the_title( $value );
						$html .= '</a>';
						$html .= '</div>';
					}
				}
				  return $html;
			} else {
				return '-';
			}
		}
		function column_wdap_map_region( $record ) {

			$map_region_value = maybe_unserialize( $record->wdap_map_region_value );
			$map_Value_exist = ! empty( $map_region_value ) ? true : false;
			$map_assign_value = json_decode( $record->assignploygons );
			$is_polygon_exist = ! empty( $map_assign_value ) ? true : false;

			$delivery_type = '';
			$map_region_type = '';
			$by_polygon_type = esc_html__( 'By Map Drawing', 'woo-delivery-area-pro' );

			if ( $map_Value_exist && $is_polygon_exist ) {

				$delivery_type = sprintf( esc_html__( 'By %s', 'woo-delivery-area-pro' ), ucfirst( $record->wdap_map_region ) );
				if ( $record->wdap_map_region == 'by_distance' ) {
					$delivery_type = esc_html__( 'By Distance', 'woo-delivery-area-pro' );
				}
				$map_region_type = $delivery_type . ' + ' . $by_polygon_type;

			} else if ( $map_Value_exist ) {

				$delivery_type = sprintf( esc_html__( 'By %s', 'woo-delivery-area-pro' ), ucfirst( $record->wdap_map_region ) );
				if ( $record->wdap_map_region == 'by_distance' ) {
					$delivery_type = esc_html__( 'By Distance', 'woo-delivery-area-pro' );
				}
				$map_region_type = $delivery_type;

			} else if ( $is_polygon_exist ) {

				$map_region_type = $by_polygon_type;
			}

			return $map_region_type;
		}

		function column_applyon( $record ) {

			if ( $record->applyon == 'selected_categories' ) {
				return esc_html__( 'Selected Categories', 'woo-delivery-area-pro' );
			}
			if ( $record->applyon == 'all_products_excluding_some' ) {
				return esc_html__( 'All Products With Exclude Products', 'woo-delivery-area-pro' );
			}

			return $record->applyon;
		}

	}

	 global $wpdb;
	 $columns = array(
		 'title'         => esc_html__( 'Title', 'woo-delivery-area-pro' ),
		 'applyon'        => esc_html__( 'Apply On', 'woo-delivery-area-pro' ),
		 'chooseproducts' => esc_html__( 'Selected Products', 'woo-delivery-area-pro' ),
		 'wdap_map_region' => esc_html__( 'Applied Delivery Area Rule', 'woo-delivery-area-pro' ),
	 );
	 $sortable  = array( 'title', 'applyon' );
	 $tableinfo = array(
		 'table'                   => WDAP_TBL_FORM,
		 'textdomain'              => 'woo-delivery-area-pro',
		 'singular_label'          => esc_html__( 'Delivery area', 'woo-delivery-area-pro' ),
		 'plural_label'            => esc_html__( 'Delivery areas', 'woo-delivery-area-pro' ),
		 'admin_listing_page_name' => 'wdap_manage_collection',
		 'admin_add_page_name'     => 'wdap_add_collection',
		 'primary_col'             => 'id',
		 'columns'                 => $columns,
		 'sortable'                => $sortable,
		 'per_page'                => 200,
		 'actions'                 => array( 'edit', 'delete'),
		 'bulk_actions'            => array(
			 'delete' => esc_html__( 'Delete', 'woo-delivery-area-pro' ),
		 ),
		 'searchMapping' => array('all_products_excluding_some' => 'All Products With Exclude Products', 'selected_categories' => 'Selected Categories', 'zipcode' => 'By Zipcode', 'country' => 'By Country', 'continents' => 'By Continents', 'by_distance' => 'By Distance', 'sub-continents' => 'By Sub-continents', 'zipcode' => 'By Map Drawing' ),
		 'col_showing_links'       => 'title',
		 'translation' => array(
			 'manage_heading'      => esc_html__( 'Manage Delivery Areas', 'woo-delivery-area-pro' ),
			 'add_button'          => esc_html__( 'Add Delivery Area', 'woo-delivery-area-pro' ),
			 'delete_msg'          => esc_html__( 'Delivery area was deleted successfully.', 'woo-delivery-area-pro' ),
			 'bulk_delete_msg'     => esc_html__( 'Delivery areas were deleted successfully.', 'woo-delivery-area-pro' ),
			 'insert_msg'          => esc_html__( 'Delivery area was added successfully', 'woo-delivery-area-pro' ),
			 'update_msg'          => esc_html__( 'Delivery area was updated successfully', 'woo-delivery-area-pro' ),
			 'no_records_found'    => esc_html__( 'No delivery areas were found.', 'wp-google-map-plugin' ),
		 ),
	 );

	 return new WDAP_Collection_Listing( $tableinfo );
}
