<?php
/**
 * Class: WDAP_Model_Settings
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package woo-delivery-area-pro
 */

if ( ! class_exists( 'WDAP_Model_Settings' ) ) {

	/**
	 * Setting model for Plugin Options.
	 *
	 * @package woo-delivery-area-pro
	 * @author Flipper Code <hello@flippercode.com>
	 */

	class WDAP_Model_Settings extends FlipperCode_Model_Base {

		function __construct() {}

		/**
		 * Admin menu for Settings Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wdap_form_settings' => esc_html__( 'Form Settings', 'woo-delivery-area-pro' ),
				'wdap_map_settings' => esc_html__( 'Maps Settings', 'woo-delivery-area-pro' ),
				'wdap_text_settings' => esc_html__( 'Text Settings', 'woo-delivery-area-pro' ),
				'wdap_restriction_settings' => esc_html__( 'Restrictions', 'woo-delivery-area-pro' ),
				'wdap_setting_settings' => esc_html__( 'Plugin Settings', 'woo-delivery-area-pro' ),
			);
		}

		/**
		 * Add or Edit Operation.
		 */
		function save() {

			$entityID = '';

			if ( isset( $_REQUEST['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
			}

			// Nonce Verification
			if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {
				die( 'Please reload page and submit the form again...' );
			}

			$toSave = array();
			$success_word = esc_html__( 'Plugin', 'woo-delivery-area-pro' );
			$get_option_data = maybe_unserialize(get_option( 'wp-delivery-area-pro'));
			// All Settings Form Submission
			

			if( isset( $_POST['text_form_submission'] ) && $_POST['text_form_submission'] == true){

				$fields_to_sanitize = array( 'wdap_shop_error_notavailable','wdap_shop_error_available','wdap_shop_error_invalid','wdap_category_error_notavailable','wdap_category_error_available','wdap_category_error_invalid','wdap_product_error_notavailable','wdap_product_error_available','wdap_product_error_invalid','wdap_cart_error_notavailable','wdap_cart_error_available','wdap_cart_error_invalid','wdap_cart_error_th','wdap_cart_error_summary','wdap_checkout_error_notavailable','wdap_checkout_error_available','wdap_checkout_error_invalid','wdap_checkout_error_th','wdap_checkout_error_summary','wdap_empty_zip_code','wdap_order_restrict_error' );

				$success_word = esc_html__( 'Text', 'woo-delivery-area-pro' );

			}else if(isset( $_POST['restrict_form_submission'] ) && $_POST['restrict_form_submission'] == true){

				$fields_to_sanitize = array('enable_auto_suggest_checkout','timeslot_error_message','timeslot_field_label');

				foreach ( $_POST['default_start_time'] as $i => $label ) {

					if(empty($_POST['default_start_time'][ $i ])){
						unset($_POST['default_start_time'][ $i ]);
					}else{
						if(strtotime($_POST['default_start_time'][ $i ])===false  ){
							$form_error[] = __('Please enter valid Default Start Time.', 'woo-delivery-area-pro');	
						}
					}	

				}

				foreach ( $_POST['default_end_time'] as $i => $label ) {

					if(empty($_POST['default_end_time'][ $i ])){
						unset($_POST['default_end_time'][ $i ]);
					}else{
						if(strtotime($_POST['default_end_time'][ $i ])===false){
							$form_error[] = __('Please enter valid Default End Time.', 'woo-delivery-area-pro');
						}
					}	

				}

				if ( !isset($_POST['enable_retrict_country']) ) {

					if( isset( $get_option_data['enable_retrict_country'] ) ){
						unset($get_option_data['enable_retrict_country']);
					}
				}

				if ( !isset($_POST['enable_places_to_retrict_country_only'])  ) {
					
					if( isset( $get_option_data['enable_places_to_retrict_country_only'] ) ){
						unset( $get_option_data['enable_places_to_retrict_country_only'] );
					}
				}

				if ( !isset($_POST['restrict_places_of_country_checkout']) ) {
					
					if( isset( $get_option_data['restrict_places_of_country_checkout'] ) ){
						unset( $get_option_data['restrict_places_of_country_checkout'] );
					}
				}
				
				if ( !isset($_POST['enable_order_restriction']) ) {
					
					if( isset( $get_option_data['enable_order_restriction'] ) ){
						unset( $get_option_data['enable_order_restriction'] );
					}
				}

				if ( !isset($_POST['enable_auto_suggest_checkout'])  ) {
					
					if( isset( $get_option_data['enable_auto_suggest_checkout'] ) ){
						unset( $get_option_data['enable_auto_suggest_checkout'] );
					}
				}

				if ( !isset($_POST['enable_timeslot_listing']) ) {
					
					if( isset( $get_option_data['enable_timeslot_listing'] ) ){
						unset( $get_option_data['enable_timeslot_listing'] );
					}
				}

				$success_word = esc_html__( 'Restriction', 'woo-delivery-area-pro' );

			}else if(isset( $_POST['map_form_submission'] ) && $_POST['map_form_submission'] == true){

				$fields_to_sanitize= array('wdap_map_width','wdap_map_height','wdap_map_zoom_level','wdap_map_center_lat','wdap_map_center_lng','shortcode_map_title', 'shortcode_map_description', 'shortcode_map_width', 'shortcode_map_height', 'shortcode_map_zoom_level', 'shortcode_map_center_lat', 'shortcode_map_center_lng', 'shortcode_map_style','wdap_map_style');

				if ( !isset($_POST['enable_map_bound']) ) {
					$_POST['enable_map_bound'] = 'no';
				}
				if ( !isset( $_POST['enable_polygon_on_map'] ) ) {
					$_POST['enable_polygon_on_map'] = 'no';
				}
				if ( !isset( $_POST['enable_markers_on_map'] ) ) {
					$_POST['enable_markers_on_map'] = 'no';
				}

				$success_word = esc_html__( 'Map', 'woo-delivery-area-pro' );

			}else if(isset( $_POST['form_form_submission'] ) && $_POST['form_form_submission'] == true){

				$fields_to_sanitize= array('wdap_check_buttonlbl','wdap_frontend_desc','avl_button_color','avl_button_bgcolor','success_msg_color','error_msg_color','shortcode_form_title','check_buttonPlaceholder','shortcode_form_description','wdap_address_empty','address_not_shipable','address_shipable','form_success_msg_color','form_error_msg_color','wdap_form_buttonlbl','form_button_color', 'form_button_bgcolor','product_listing_error','wdap_checkout_buttonlbl',);

				if ( isset( $_POST['can_be_delivered_redirect_url'] ) && !empty( $_POST['can_be_delivered_redirect_url'] ) ) {
					$_POST['can_be_delivered_redirect_url'] = esc_url( $_POST['can_be_delivered_redirect_url'] );
				}

				if ( isset( $temp_data['cannot_be_delivered_redirect_url'] ) && !empty( $temp_data['cannot_be_delivered_redirect_url'] ) ) {
					$_POST['can_be_delivered_redirect_url'] = esc_url( $_POST['cannot_be_delivered_redirect_url'] );
				}
				
				if ( isset( $_POST['hidden_zip_template'] ) ) {
					$toSave['default_templates']['zipcode'] = $_POST['hidden_zip_template'];
				}
					
				if ( isset( $_POST['hidden_shortcode_template'] ) ) {
					$toSave['default_templates']['shortcode'] = $_POST['hidden_shortcode_template'];
				}
				if ( !isset($_POST['enable_locate_me_btn']) ) {

					if( isset( $get_option_data['enable_locate_me_btn'] ) ){
						unset( $get_option_data['enable_locate_me_btn'] );
					}
				}

				if ( !isset($_POST['enable_product_listing']) ) {
					
					if( isset( $get_option_data['enable_product_listing'] ) ){
						unset( $get_option_data['enable_product_listing'] );
					}
				}

				if ( !isset($_POST['disable_availability_tab']) ) {

					if( isset( $get_option_data['disable_availability_tab'] ) ){
						unset( $get_option_data['disable_availability_tab'] );
					}
				}

				if ( !isset($_POST['disable_zipcode_listing']) ) {

					if( isset( $get_option_data['disable_zipcode_listing'] ) ){
						unset( $get_option_data['disable_zipcode_listing'] );
					}
				}

				if ( !isset($_POST['disable_availability_status']) ) {

					if( isset( $get_option_data['disable_availability_status'] ) ){
						unset( $get_option_data['disable_availability_status'] );
					}
				}

				if( ! isset( $_POST['apply_on'] ) ){
					$toSave['apply_on']['checkedvalue'] = array();
				}

				$success_word = esc_html__( 'Form', 'woo-delivery-area-pro' );

			}else if(isset( $_POST['plugin_form_submission'] ) && $_POST['plugin_form_submission'] == true){

				$fields_to_sanitize = array('wdap_custom_box_css');

			}	

			$this->verify( $_POST );
			if ( empty( $this->errors ) ) {
				if ( isset( $_POST['enable_retrict_country'] ) && $_POST['enable_retrict_country'] == true && empty( $_POST['wdap_country_restriction_listing'] ) ) {
					$this->errors[] = esc_html__( 'Please select at least one country.', 'woo-delivery-area-pro' );
				}
				if ( isset($_POST['wdap_map_height']) && empty( $_POST['wdap_map_height'] ) ) {
					$this->errors[] = esc_html__( 'Please enter map height.', 'woo-delivery-area-pro' );
				}
				if ( isset( $_POST['shortcode_map_height'] ) && empty( $_POST['shortcode_map_height'] ) ) {
					$this->errors[] = esc_html__( 'Please enter shortcode map height.', 'woo-delivery-area-pro' );
				}
				if(isset($_POST['enable_order_restriction']) && $_POST['enable_order_restriction'] == true && (!isset($get_option_data['wdap_order_restrict_error']) || empty($get_option_data['wdap_order_restrict_error']) ) ){
					$this->errors[] = esc_html__( 'Please enter order restriction error message (Text Settings page).', 'woo-delivery-area-pro' );
				}
			}	
			

			if ( is_array( $this->errors ) and ! empty( $this->errors ) ) {
				$this->throw_errors();
			}
			if ( isset( $_POST['entityID'] ) ) {
				$entityID = intval( wp_unslash( $_POST['entityID'] ) );
			}

			if ( $entityID > 0 ) {
				$where[ $this->unique ] = $entityID;
			} else {
				$where = '';
			}

			$temp_data = $_POST;				

			
			foreach ( $fields_to_sanitize as $field ) {
				if ( isset( $temp_data[ $field ] ) && ! empty( $temp_data[ $field ] ) ) {
					$toSave[ $field ] = sanitize_text_field( $temp_data[ $field ] );
					unset( $temp_data[ $field ] );
					unset($get_option_data[ $field ]);
				}
			}
			
			$merge_post = array_merge( $toSave, $temp_data );	
			
			$data = array_merge( $get_option_data, $merge_post );

			update_option( 'wp-delivery-area-pro', wp_unslash( $data ) );
			$response['success'] = $success_word.esc_html__( ' settings were saved successfully.', 'woo-delivery-area-pro' );
			return $response;
		}

			/**
			 * Delete rule object by id.
			 */
		public function delete() {
			if ( isset( $_GET['id'] ) ) {
				$id = intval( wp_unslash( $_GET['id'] ) );
				$connection = FlipperCode_Database::connect();
				$this->query = $connection->prepare( "DELETE FROM $this->table WHERE $this->unique='%d'", $id );
				return FlipperCode_Database::non_query( $this->query, $connection );
			}
		}
	}
}
