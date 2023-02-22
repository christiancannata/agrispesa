<?php 
	class WCCCF_OrderDetailsPage
	{
		public function __construct()
		{
			add_action( 'woocommerce_admin_order_data_after_billing_address',  array( &$this,'display_billing_downloads' ));
			add_action( 'woocommerce_admin_order_data_after_shipping_address',  array( &$this,'display_shipping_downloads' ));
			
			add_filter( 'woocommerce_admin_billing_fields',  array( &$this,'add_order_cccf_fields' )); //admin order details page
			add_filter( 'woocommerce_admin_shipping_fields',  array( &$this,'add_order_cccf_shipping_fields' )); //admin order details page
			add_action( 'woocommerce_process_shop_order_meta', array( &$this, 'woocommerce_process_shop_ordermeta' ), 10, 2 ); //99
		}
	
		public function display_custom_data( $order ) 
		{
			global $wcev_order_model, $wcev_text_helper, $wcccf_field_model;
			$texts = $wcev_text_helper->get_texts();
			$business_or_consumer = $wcev_order_model->get_client_type(WCEV_Order::get_id($order));
			$business_or_consumer_result =  $business_or_consumer && $business_or_consumer == 'consumer' ? $texts['consumer_option_label'] : $texts['business_option_label'];
			$was_invoice_requested = $invoice_was_requested = $wcev_order_model->was_invoice_requested(WCEV_Order::get_id($order)) ? esc_html__( 'Yes', 'woocommerce-conditional-checkout-fields' ) : esc_html__( 'No', 'woocommerce-conditional-checkout-fields' );
			
			if($business_or_consumer)
				echo "<p class='form-field form-field-wide' style='margint-top: 20px; display:block;'><strong>".esc_html__('Customer type', 'woocommerce-conditional-checkout-fields').": </strong><br/>" .$business_or_consumer_result . "</p>";
			
			if($was_invoice_requested )
				echo '<p class="form-field form-field-wide"><strong>'.esc_html__('Invoice requested', 'woocommerce-conditional-checkout-fields').': </strong><strong>' .$was_invoice_requested . '</strong></p>';
			
			if( $business_or_consumer || $was_invoice_requested)
				echo ' <small>('.esc_html__( 'To modify these values: edit the <strong>Billing Details</strong>', 'woocommerce-conditional-checkout-fields' ).')</small>';
		}
		public function add_order_cccf_shipping_fields($fields)
		{
			return $this->add_order_cccf_fields($fields, 'shipping');
		}
		public function add_order_cccf_fields($fields, $type = 'billing')
		{
			global $wcccf_field_model, $post, $wcccf_order_model;
			
			if(!isset($post))
				return $fields;
			
			$form_data = $wcccf_field_model->get_form_field_data($type, false, array(), false, "", true );
			
			
			$order = wc_get_order($post->ID);
			if(isset($form_data[$type]))
				foreach($form_data[$type] as $field_id => $form_field)
				{
					$meta_value = $order->get_meta("_".$field_id);
					$is_meta_existing = $wcccf_order_model->is_meta_existing($order->get_id(), "_".$field_id); 
					$field_id = str_replace("{$type}_", "", $field_id);
					
					if($form_field['type'] == 'wcccf_heading')
					{
						$field_data = array('label' =>  $form_field['label'], 'show' => true, 'type' => 'text');
						$fields[$field_id] = $field_data;
						add_filter( 'woocommerce_order_get_'.'_'.$type.'_'.$field_id, array(&$wcccf_field_model, 'manage_heading_on_order_meta_load' ), 1, 2);
					} 
					
					if($meta_value == '' && !$is_meta_existing)
						continue;
					
										
					unset($form_field['description']);
					if(is_array($meta_value)) //In case of select with multiple values, in order to properly display values before editing
					{
						//To display just the value: $form_field['value'] = implode(", ", $meta_value);
						$prev_country_id = $form_field['wcccf_type'] == 'country' ? $meta_value : "";
						$readable_meta_value = $wcccf_field_model->get_field_readable_value($meta_value, $form_field, "", $order);
						$form_field['value'] = is_array($readable_meta_value) ? implode(", ", $readable_meta_value) : $readable_meta_value;
						
						
						//Hook example
						//woocommerce_order_get__billing_wcccf_id_d2WjZ8kENlVzhM2 <-- ok
						//wcccf_var_dump('woocommerce_data_get_'.'_billing_'.$field_id);
						
						//In this state WooCommerce cannot manage Array values, so is used the following filter to hook when loading the multiselect content
						//that it is exploded as a string
						//**For preset edit input for multiselect: Is not possible. Unfortunately WooCommerce uses the select() function that takes just a string
						//	element as paramenter to decide which option is preselected. In case of multiple select field that value is an array
						$form_field['name'] = '_'.$type.'_'.$field_id.'[]';
						//Note: Field label is contained in $form_field['label']
						
						add_filter( 'woocommerce_order_get_'.'_'.$type.'_'.$field_id, array(&$wcccf_field_model, 'manage_multiselect_value_on_order_meta_load' ), 1, 2);
					}
					elseif($form_field['type'] == 'checkbox')
					{
						add_filter( 'woocommerce_order_get_'.'_'.$type.'_'.$field_id, array(&$wcccf_field_model, 'manage_checkbox_value_on_order_meta_load' ), 1, 2);
					}
					elseif($form_field['wcccf_type'] == 'date')
					{
						add_filter( 'woocommerce_order_get_'.'_'.$type.'_'.$field_id, array(&$wcccf_field_model, 'manage_date_value_on_order_meta_load' ), 1, 2);
					}
					elseif($form_field['wcccf_type'] == 'time')
					{
						add_filter( 'woocommerce_order_get_'.'_'.$type.'_'.$field_id, array(&$wcccf_field_model, 'manage_time_value_on_order_meta_load' ), 1, 2);
					}
					elseif($form_field['wcccf_type'] == 'country')
					{
						add_filter( 'woocommerce_order_get_'.'_'.$type.'_'.$field_id, array(&$wcccf_field_model, 'manage_country_value_on_order_meta_load' ), 1, 2);
					}
					elseif($form_field['wcccf_type'] == 'state')
					{
						add_filter( 'woocommerce_order_get_'.'_'.$type.'_'.$field_id, array(&$wcccf_field_model, 'manage_state_value_on_order_meta_load' ), 1, 2);
					}
					elseif($form_field['wcccf_type'] == 'wcccf_file')
					{
						//add_filter( 'woocommerce_order_get_'.'_'.$type.'_'.$field_id, array(&$wcccf_field_model, 'manage_file_value_on_order_meta_load' ), 1, 2);
						continue;
					}
					else //single value select field
					{
						$readable_meta_value = $wcccf_field_model->get_field_readable_value($meta_value, $form_field, "", $order);
						$form_field['value'] = is_array($readable_meta_value) ? implode(", ", $readable_meta_value) : $readable_meta_value;
					}
					
					$fields[$field_id] = $form_field;
				};
			
			
			return $fields;
		}
		
		function display_shipping_downloads($order)
		{
			$this->render_downloads_area('shipping');
		}
		function display_billing_downloads($order)
		{
			$this->render_downloads_area();
		}
		private function render_downloads_area($type = 'billing')
		{
			global $wcccf_field_model, $post, $wcccf_order_model, $wcccf_file_model;
			$form_data = $wcccf_field_model->get_form_field_data($type, false);
			$order = wc_get_order($post->ID);
			
			wp_register_script( 'wcccf-order-details-page', WCCCF_PLUGIN_PATH.'/js/admin-order-details-page.js', array('jquery'));
			wp_localize_script( 'wcccf-order-details-page', 'wcccf_order_details', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
																						  'delete_confirm_message' => esc_html__('Are you sure?', 'woocommerce-conditional-checkout-fields'),
																						  'deleted_message' => "<strong>".esc_html__('Deleted!', 'woocommerce-conditional-checkout-fields')."<strong>"));
			wp_enqueue_script('wcccf-order-details-page');	
			wp_enqueue_style('wcccf-order-details-page', WCCCF_PLUGIN_PATH.'/css/admin-order-details-page.css'); 			
			
			wp_enqueue_script( 'wcccf-file-uploader', WCCCF_PLUGIN_PATH.'/js/file-uploader.js', array('jquery'));
			//
			wp_register_script( 'wcccf-file-upload-manager', WCCCF_PLUGIN_PATH.'/js/file-upload-manager.js', array('jquery'));
			wp_localize_script( 'wcccf-file-upload-manager', 'wcccf_file_uploader_manager', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
																									'delete_message' => esc_html__('deleting', 'woocommerce-conditional-checkout-fields'),
																									'size_error_message' => esc_html__('File too big. Max allowed size (MB): ', 'woocommerce-conditional-checkout-fields'),
																									'required_files_message' => esc_html__('Please upload all the required files', 'woocommerce-conditional-checkout-fields')));
			wp_enqueue_script('wcccf-file-upload-manager');	
			
			if(isset($form_data[$type]))
				foreach($form_data[$type] as $field_id => $form_field)
				{
					if($form_field['wcccf_type'] != 'wcccf_file')
						continue;
					
					$meta_value = $order->get_meta("_".$field_id);
					$is_meta_existing = $wcccf_order_model->is_meta_existing($order->get_id(), "_".$field_id);
					$form_field['description'] = "";				
					//$field_id = str_replace("{$type}_", "", $field_id);
					$label_field_id = str_replace("_wcccf_id_", "_wcccf_label_", $field_id);
					$is_label_existing = $wcccf_order_model->is_meta_existing($order->get_id(), "_".$label_field_id); //If label metadata exists, it means that the field was deleted (if no field meta data existing)
					$id = $form_field['wcccf_unique_id'];
					
					//HTML
					if($meta_value == '' && !$is_meta_existing && $is_label_existing)
					{
						//continue;
						echo '<p>';	
						echo '<strong>'.$form_field['label'].'</strong><br/>';
						echo '<small>'.esc_html__('After the file has been uploaded, Save the order in order changes to take effect.', 'woocommerce-conditional-checkout-fields').'</small><br/>';
						echo $wcccf_field_model->get_upload_area($form_field, $field_id, true);
						echo '</p>';
					}	
					else if($meta_value != '' && $is_meta_existing)
					{
						echo '<p>';	
						echo '<strong>'.$form_field['label'].'</strong><br/>';
						echo '<small>'.esc_html__('To replace a file: first delete and then reload the page.', 'woocommerce-conditional-checkout-fields').'</small><br/>';
						echo $wcccf_file_model->get_file_download_link($meta_value , $order, "wccc_download_button_".$id);
						echo '<a data-id="'.$id.'" data-meta-key="_'.$field_id.'" data-order-id="'.$order->get_id().'" data-file-to-delete="'.$meta_value.'" class="button wcccf_delete_button">'.esc_html__('Delete', 'woocommerce-conditional-checkout-fields').'</a>';	
						echo '</p>';
					}
				
				}
		}
		public function woocommerce_process_shop_ordermeta($order_id, $post)
		{
			global $wcccf_file_model;
			if(isset($_POST['wcccf_files']))
			{
				$wcccf_file_model->process_files_on_order_save($order_id, $_POST['wcccf_files']);
			}
		}
		
	}	
?>