<?php 
class WCCCF_DisplayManagerPage 
{
	var $page = "woocommerce-conditional-checkout-display-manager";
	
	public function __construct()
	{
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
	}
	public function add_page($cap )
	{
		
		$this->page = add_submenu_page( 'woocommerce-conditional-checkout-fields', esc_html__('Sort & hide', 'woocommerce-conditional-checkout-fields'), esc_html__('Sort & hide', 'woocommerce-conditional-checkout-fields'), $cap, 'woocommerce-conditional-checkout-display-manager', array($this, 'render_page'));
		
		add_action('load-'.$this->page,  array($this,'page_actions'),9);
		add_action('admin_footer-'.$this->page,array($this,'footer_scripts'));
	}
	function footer_scripts(){
		?>
		<script> postboxes.add_postbox_toggles(pagenow);</script>
		<?php
	}
	
	function page_actions()
	{
		do_action('add_meta_boxes_'.$this->page, null);
		do_action('add_meta_boxes', $this->page, null);
	}
	public function render_page()
	{
		global $pagenow,$wcccf_field_model;
		wp_enqueue_style('wcccf-display-manager-page', WCCCF_PLUGIN_PATH.'/css/admin-display-manager-page.css'); 
		
		//Save data 
		if(isset($_POST) && isset($_POST['wcccf_field_display_reset']) && isset($_POST['wcccuf_nonce_configuration_data']) && wp_verify_nonce($_POST['wcccuf_nonce_configuration_data'], 'wcccuf_save_data'))
		{
			$wcccf_field_model->delete_field_order();
		}
		else if(isset($_POST) && isset($_POST['wcccuf_nonce_configuration_data']) && wp_verify_nonce($_POST['wcccuf_nonce_configuration_data'], 'wcccuf_save_data') && isset($_POST['wcccf_sort']))
			$wcccf_field_model->save_field_order($_POST['wcccf_sort']);
			

		//Options
		if(isset($_POST) && isset($_POST['wcccf_checkout_options']) && isset($_POST['wcccuf_nonce_configuration_data']) && wp_verify_nonce($_POST['wcccuf_nonce_configuration_data'], 'wcccuf_save_data'))
		{
			$wcccf_field_model->save_checkout_options($_POST['wcccf_checkout_options']);
		}
		add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );
		
		wp_enqueue_script('postbox'); 
		wp_enqueue_script( 'admin-fields-display-manager-page', WCCCF_PLUGIN_PATH.'/js/admin-fields-display-manager-page.js', array('jquery'));	
		wp_enqueue_style('admin-fields-display-manager', WCCCF_PLUGIN_PATH.'/css/admin-fields-display-manager.css'); 
		
		?>
		<div class="wrap">
			<h2><?php esc_html_e('Sort & Hide Checkout Fields','woocommerce-conditional-checkout-fields'); ?></h2>
	
			<form id="post"  method="post">
				<?php wp_nonce_field( 'wcccuf_save_data', 'wcccuf_nonce_configuration_data' ); ?>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
						<div id="post-body-content">
						</div>
						
						<div id="postbox-container-1" class="postbox-container">
							<?php do_meta_boxes('woocommerce-conditional-checkout-fields','side',null); ?>
						</div>
						
						<div id="postbox-container-2" class="postbox-container">
							  <?php do_meta_boxes('woocommerce-conditional-checkout-fields','normal',null); ?>
							  <?php do_meta_boxes('woocommerce-conditional-checkout-fields','advanced',null); ?>
							  
						</div> 
					</div> <!-- #post-body -->
				</div> <!-- #poststuff -->
				
			</form>
		</div> <!-- .wrap -->
		<?php 
	}
	
	function add_meta_boxes()
	{
		$screen = get_current_screen();
		//wcccf_var_dump($screen->base);
		if(!$screen || $screen->base != "woocommerce-checkout-fields-fees_page_woocommerce-conditional-checkout-display-manager")
			return;
		
		add_meta_box( 'billing_fields', 
					esc_html__('Billing fields','woocommerce-conditional-checkout-fields'), 
					array($this, 'add_billing_fields_meta_box'), 
					'woocommerce-conditional-checkout-fields', 
					'normal' 
			);
		
		add_meta_box( 'shipping_fields', 
					esc_html__('Shipping fields','woocommerce-conditional-checkout-fields'), 
					array($this, 'add_shipping_fields_meta_box'), 
					'woocommerce-conditional-checkout-fields', 
					'normal' 
			);
		
		add_meta_box('additional_options', 
			esc_html__('Additional options','woocommerce-conditional-checkout-fields'), 
			array($this, 'add_additional_options_meta_box'), 
			'woocommerce-conditional-checkout-fields',
			'side' 
		);
			
		add_meta_box('save_button', 
				esc_html__('Save fields','woocommerce-conditional-checkout-fields'), 
				array($this, 'add_save_button_meta_box'), 
				'woocommerce-conditional-checkout-fields',
				'side' 
			);
	}
	
	function add_billing_fields_meta_box()
	{
		global $wcccf_html_helper, $wcccf_field_model, $wcccf_wpml_helper;
		
		$countries = new WC_Countries();
		$billing_checkout_fields = $countries->get_address_fields( $countries->get_base_country(),'billing_');
		
		$conditional_checkout_fields = $wcccf_field_model->get_field_data('billing');
		$field_order =  $wcccf_field_model->get_field_order();
		$global_checkout_field_array = array();
		
		?>
			<div class="column " id="billing_fields_container">
				<?php 
				//init
				foreach((array)$billing_checkout_fields as $chckout_field_id => $checkout_field )
				{
					if(!is_array($checkout_field) || !isset($checkout_field['class']) || !isset($checkout_field['label']))
						continue;
					
					
						$width = "wide";
						if( in_array('form-row-first', $checkout_field['class'] ))
							$width = 'first';
						else if( in_array('form-row-last', $checkout_field['class'] ))
						   $width = 'last';
					   
						$field_data = array('label' => isset($checkout_field['label']) ? $checkout_field['label'] : "" , 'id' => $chckout_field_id, 'width' => $width, "can_be_hidden" => false);
						$field_data['is_hidden'] = isset($field_order[$field_data['id']]) && isset($field_order[$field_data['id']]['is_hidden']) ? true : false;
						$global_checkout_field_array[$field_data['id']] = $field_data;
					
				}
				foreach((array)$conditional_checkout_fields  as $checkout_field )	
				{
					if(!is_array($checkout_field) || !isset($checkout_field['name']) || !isset($checkout_field['unique_id']) || !isset($checkout_field['options']))
						continue;
					
					if($wcccf_field_model->is_booking_field($checkout_field))
					{
						continue;
					}
					
					
					
					$field_data = array('label' => $checkout_field['name'][$wcccf_wpml_helper->get_default_locale()] , 'id' => 'billing_wcccf_id_'.$checkout_field['unique_id'], 'width' => $checkout_field["options"]['row_type'], "can_be_hidden" => true);
					$field_data['is_hidden'] = isset($field_order[$field_data['id']]) && isset($field_order[$field_data['id']]['is_hidden']) ? true : false;
					$global_checkout_field_array[$field_data['id']] = $field_data;
				}
				
				$global_checkout_field_array = $wcccf_field_model->sort_field_arrays($global_checkout_field_array, $field_order);
				
				//rendering
				foreach($global_checkout_field_array as $field_data)
				{
					$wcccf_html_helper->get_sort_element_template($field_data, $field_data['is_hidden']);
				}
				?>
			</div>

		<?php
	}
	function add_shipping_fields_meta_box()
	{
		global $wcccf_html_helper, $wcccf_field_model, $wcccf_wpml_helper;
		
		$countries = new WC_Countries();
		$shipping_checkout_fields = $countries->get_address_fields( $countries->get_base_country(),'shipping_');
		
		$conditional_checkout_fields = $wcccf_field_model->get_field_data('shipping');
		$field_order =  $wcccf_field_model->get_field_order();
		$global_checkout_field_array = array();
		?>
			<div class="column " id="shipping_fields_container">
				<?php 
				//init
				foreach((array)$shipping_checkout_fields as $chckout_field_id => $checkout_field )
				{
					if(!is_array($checkout_field) || !isset($checkout_field['class']) || !isset($checkout_field['label']))
						continue;
					
					$width = "wide";
					if( in_array('form-row-first', $checkout_field['class'] ))
						$width = 'first';
					else if( in_array('form-row-last', $checkout_field['class'] ))
					   $width = 'last';
				   
					$field_data = array('label' => isset($checkout_field['label']) ? $checkout_field['label'] : "" , 'id' => $chckout_field_id, 'width' =>$width, "can_be_hidden" => false);
					$field_data['is_hidden'] = isset($field_order[$field_data['id']]) && isset($field_order[$field_data['id']]['is_hidden']) ? true : false;
					$global_checkout_field_array[$field_data['id']] = $field_data;
				}
				foreach((array)$conditional_checkout_fields  as $checkout_field )	
				{
					if(!is_array($checkout_field) || !isset($checkout_field['name']) || !isset($checkout_field['unique_id']) || !isset($checkout_field['options']))
						continue;
					
					if($wcccf_field_model->is_booking_field($checkout_field))
						continue;
					
					$field_data = array('label' => $checkout_field['name'][$wcccf_wpml_helper->get_default_locale()] , 'id' => 'shipping_wcccf_id_'.$checkout_field['unique_id'], 'width' => $checkout_field["options"]['row_type'], "can_be_hidden" => true);
					$field_data['is_hidden'] = isset($field_order[$field_data['id']]) && isset($field_order[$field_data['id']]['is_hidden']) ? true : false;
					$global_checkout_field_array[$field_data['id']] = $field_data;
				}
				$global_checkout_field_array = $wcccf_field_model->sort_field_arrays($global_checkout_field_array, $field_order);
				
				//rendering
				foreach($global_checkout_field_array as $field_data)
				{
					$wcccf_html_helper->get_sort_element_template($field_data, $field_data['is_hidden']);
					
				}
				?>
			</div>
		
		<?php
	}
	function add_additional_options_meta_box()
	{
		global $wcccf_field_model;
		$disable_checkout_sort_and_hide = $wcccf_field_model->get_checkout_option('disable_checkout_sort_and_hide');
		
		?>
		<p class="wcccf_option_container">
			<label><?php esc_html_e('Restore field default sorting?','woocommerce-conditional-checkout-fields'); ?> 
			<input type="checkbox" name="wcccf_field_display_reset" value="true"></input></label>
		</p>
		<p class="wcccf_option_container">
			<label><?php esc_html_e('Disable checkout sort & hide feature','woocommerce-conditional-checkout-fields'); ?> </label>	
			<select name="wcccf_checkout_options[disable_checkout_sort_and_hide]">
				<option value="no" <?php selected( $disable_checkout_sort_and_hide, 'no'); ?>><?php esc_html_e('No','woocommerce-conditional-checkout-fields'); ?></option>
				<option value="yes" <?php selected( $disable_checkout_sort_and_hide, 'yes'); ?>><?php esc_html_e('Yes','woocommerce-conditional-checkout-fields'); ?></option>
			</select>
			<small><?php esc_html_e('This option will completely disable Sort & Hide feature. Enable if you are experiencing any issue with 3rd party plugin that are managing checkout process.','woocommerce-conditional-checkout-fields'); ?></small>
		</p>
		<?php 
	}
	function add_save_button_meta_box()
	{
		
		submit_button( esc_html__( 'Save', 'woocommerce-conditional-checkout-fields' ),
						'primary',
						'submit'
					);
	}
}
?>