<?php
if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_WC_API_Handler')):

class WCFE_WC_API_Handler extends WCFE_Checkout_Fields_Utils{

	public function __construct() {
		add_filter("woocommerce_webhook_payload", array( $this, 'woo_webhook_payload' ), 10, 4);

		//WooCommerce - Add custom post_meta data to the order REST API response.
		add_filter('woocommerce_api_order_response', array( $this, 'woo_api_order_response'), 20, 4);
	}
	
	public function woo_webhook_payload($payload, $resource, $resource_id, $id) {
		$sections = $this->get_checkout_sections();
		
		if($resource === "order"){
			$order_id = $payload["id"];
			$user_id  = $payload["customer_id"];
			
			if($sections && is_array($sections)){
				foreach($sections as $sname => $section){					
					if(THWCFE_Utils_Section::is_valid_section($section)){
						$fields = THWCFE_Utils_Section::get_fields($section);
						if($fields){
							foreach($fields as $name => $field){	
								if(THWCFE_Utils_Field::is_enabled($field) && THWCFE_Utils_Field::is_custom_field($field)){
									$type = $field->get_property('type');
									$meta_value = false;

									if($field->get_property('order_meta')){
										$meta_value = get_post_meta( $order_id, $name, true );
									}else if($field->get_property('user_meta')){
										$meta_value = get_user_meta( $user_id, $name, true );
									}

									if($type === 'file' && apply_filters('thwcfe_api_display_only_the_name_of_uploaded_file', true, $name)){
										$meta_value = WCFE_Checkout_Fields_Utils::get_file_display_name_order($meta_value, false);
									}else{
										//$meta_value = $this->get_option_text_from_value($field, $meta_value);
										$meta_value = is_array($meta_value) ? implode(", ", $meta_value) : $meta_value;
									}

									$payload[$sname][$name] = $meta_value;
								}
							}
						}
					}
				}
			}	
								
		}else if($resource === "customer"){
			$user_id = $payload["id"];
			
			if($sections && is_array($sections)){
				foreach($sections as $sname => $section){
					if(THWCFE_Utils_Section::is_valid_section($section)){
						$fields = THWCFE_Utils_Section::get_fields($section);
						if($fields){
							foreach($fields as $name => $field){	
								if( THWCFE_Utils_Field::is_enabled($field) && THWCFE_Utils_Field::is_custom_field($field) && $field->get_property('user_meta') ){
									$type = $field->get_property('type');
									$value = get_user_meta( $user_id, $name, true );

									if($type === 'file' && apply_filters('thwcfe_api_display_only_the_name_of_uploaded_file', true, $name)){
										$value = WCFE_Checkout_Fields_Utils::get_file_display_name_order($value, false);
									}else{
										//$value = $this->get_option_text_from_value($field, $value);
										$value = is_array($value) ? implode(", ", $value) : $value;
									}

									$payload[$sname][$name] = $value;
								}
							}
						}
					}
				}
			}
		}
		return $payload;
	}

	public function woo_api_order_response($order_data, $order, $fields, $server) {
		$custom_fields = apply_filters('thwcfe_woo_api_order_response_fields', array());

		if(is_array($custom_fields)){
			foreach ($custom_fields as $key) {
				$order_data[$key] = get_post_meta( $order->id, $key, true );
			}
		}
		return $order_data;
	}
}

endif;