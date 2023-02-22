<?php 
class WCCCF_FieldCoreConfiguratorPage
{
	var $page = "woocommerce-conditional-checkout-core-fields";
	public function __construct()
	{
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
	}
	public function add_page($cap )
	{
		$this->page = add_submenu_page( 'woocommerce-conditional-checkout-fields', esc_html__('Core Fields', 'woocommerce-conditional-checkout-fields'),esc_html__('Core Fields', 'woocommerce-conditional-checkout-fields'), $cap, 'woocommerce-conditional-checkout-core-fields', array($this, 'render_page'));
		
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
		
		
		
		//Save data 
		//wcccf_var_dump($_POST); 
		if(isset($_POST) && isset($_POST['wcccuf_nonce_configuration_data']) && wp_verify_nonce($_POST['wcccuf_nonce_configuration_data'], 'wcccuf_save_data') && isset($_POST['wcccf_field_data']))
			$wcccf_field_model->save_field_data($_POST['wcccf_field_data']);
		elseif(isset($_POST) && isset($_POST['wcccuf_nonce_configuration_data']) && wp_verify_nonce($_POST['wcccuf_nonce_configuration_data'], 'wcccuf_save_data') && !isset($_POST['wcccf_field_data']))
			$wcccf_field_model->delete_field_data();
			
			
		$time_format = get_option('time_format');
		$date_format = get_option('date_format');
		$next_id = $wcccf_field_model->get_next_free_id();
		
		add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );
		
		wp_enqueue_script('postbox'); 
		wp_enqueue_style('admin-fields-configurator-page', WCCCF_PLUGIN_PATH.'/css/admin-fields-configurator-page.css'); 
		
		wp_enqueue_script('postbox'); 
		wp_register_script( 'admin-fields-configurator-page', WCCCF_PLUGIN_PATH.'/js/admin-fields-configurator-page.js', array('jquery'));	
		wp_localize_script( 'admin-fields-configurator-page', 'wcccf_configuration', array( 'next_id' => $next_id,
																							'type' => 'field',
																							'duplication_message' => esc_html__('The last saved version of the field will be the one that will be duplicated. So make sure you have saved it before proceding.\n\nAt the end of the operation the page will be reloaded, Make sure you have saved you progress.\nProceed?','woocommerce-conditional-checkout-fields')) );
		wp_enqueue_script( 'admin-fields-configurator-page' );	
		
		
		?>
		<div class="wrap">
			<h2><?php esc_html_e('Core Fields Configurator','woocommerce-conditional-checkout-fields'); ?></h2>
	
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
		
		if(!$screen || $screen->base != "woocommerce-checkout-fields-fees_page_woocommerce-conditional-checkout-core-fields")
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
		add_meta_box('save_button', 
				esc_html__('Save fields','woocommerce-conditional-checkout-fields'), 
				array($this, 'add_save_button_meta_box'), 
				'woocommerce-conditional-checkout-fields',
				'side' 
			);
	}
	function add_billing_fields_meta_box()
	{
		global $wcccf_html_helper, $wcccf_field_model;
		
		$field_data = $wcccf_field_model->get_core_field_data('billing');
		$screen = get_current_screen();
		
		?>
			<div class="column " id="billing_fields_container">
				<?php  $wcccf_html_helper->render_field_core_configuration_meta_box($field_data); ?>
			</div>
		<?php
	}
	function add_shipping_fields_meta_box()
	{
		global $wcccf_html_helper, $wcccf_field_model;
		$field_data = $wcccf_field_model->get_core_field_data('shipping');
		
		?>
			<div class="column " id="shipping_fields_container">
				<?php  $wcccf_html_helper->render_field_core_configuration_meta_box($field_data); ?>
			</div>			
		<?php
	}
	function add_save_button_meta_box()
	{
		$screen = get_current_screen();
		if(!$screen || $screen->base != "woocommerce-checkout-fields-fees_page_woocommerce-conditional-checkout-core-fields")
			return;
		
		submit_button( esc_html__( 'Save', 'woocommerce-conditional-checkout-fields' ),
						'primary',
						'submit'
					);
	}
}
?>