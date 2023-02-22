<?php 
class WCCCF_FeeConfiguratorPage
{
	var $page = "woocommerce-conditional-checkout-fees";
	public function __construct()
	{
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
	}
	public function add_page($cap )
	{
		
		$this->page = add_submenu_page( 'woocommerce-conditional-checkout-fields', esc_html__('Fees', 'woocommerce-conditional-checkout-fields'), esc_html__('Fees', 'woocommerce-conditional-checkout-fields'), $cap, 'woocommerce-conditional-checkout-fees', array($this, 'render_page'));
		
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
		global $pagenow,$wcccf_fee_model;
		
		//Save data 
		//wcccf_var_dump($_POST); 
		if(isset($_POST) && isset($_POST['wcccuf_nonce_configuration_data']) && wp_verify_nonce($_POST['wcccuf_nonce_configuration_data'], 'wcccuf_save_data') && isset($_POST['wcccf_field_data']))
			$wcccf_fee_model->save_fee_data($_POST['wcccf_field_data']);
		elseif(isset($_POST) && isset($_POST['wcccuf_nonce_configuration_data']) && wp_verify_nonce($_POST['wcccuf_nonce_configuration_data'], 'wcccuf_save_data') && !isset($_POST['wcccf_field_data']))
			$wcccf_fee_model->delete_fee_data();
			
			
		$time_format = get_option('time_format');
		$date_format = get_option('date_format');
		$next_id = $wcccf_fee_model->get_next_free_id();
		
		add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );
		
		wp_enqueue_script('postbox'); 
		wp_register_script( 'admin-fields-configurator-page', WCCCF_PLUGIN_PATH.'/js/admin-fields-configurator-page.js', array('jquery'));	
		wp_localize_script( 'admin-fields-configurator-page', 'wcccf_configuration', array( 'next_id' => $next_id ,
																							'type' => 'fee',
																							'duplication_message' => esc_html__('The last saved version of the field will be the one that will be duplicated. So make sure you have saved it before proceding.\n\nAt the end of the operation the page will be reloaded, Make sure you have saved you progress.\nProceed?','woocommerce-conditional-checkout-fields')) );
		wp_enqueue_script( 'admin-fields-configurator-page' );	
		wp_enqueue_script( 'admin-fields-configurator-page-conditional-options', WCCCF_PLUGIN_PATH.'/js/admin-fields-configurator-page-conditional-options.js', array('jquery'));	
		wp_enqueue_script( 'admin-load-category-list', WCCCF_PLUGIN_PATH.'/js/admin-load-category-list.js', array('jquery'));	
		wp_enqueue_script( 'admin-load-product-list', WCCCF_PLUGIN_PATH.'/js/admin-load-product-list.js', array('jquery'));	
		wp_enqueue_script( 'admin-load-tag-list', WCCCF_PLUGIN_PATH.'/js/admin-load-tag-list.js', array('jquery'));	
		wp_enqueue_script( 'admin-user-role-list', WCCCF_PLUGIN_PATH.'/js/admin-user-role-list.js', array('jquery'));	
		wp_enqueue_script( 'admin-customer-name-list', WCCCF_PLUGIN_PATH.'/js/admin-customer-list.js', array('jquery'));	
		wp_enqueue_script( 'select2', WCCCF_PLUGIN_PATH.'/js/vendor/select2.full.min.js', array('jquery'));	
		wp_enqueue_script( 'datetime-picker', WCCCF_PLUGIN_PATH.'/js/vendor/datetimepicker/picker.js', array('jquery'));	
		wp_enqueue_script( 'datetime-picker-date', WCCCF_PLUGIN_PATH.'/js/vendor/datetimepicker/picker.date.js', array('jquery'));	
		wp_enqueue_script( 'datetime-picker-time', WCCCF_PLUGIN_PATH.'/js/vendor/datetimepicker/picker.time.js', array('jquery'));	
		wp_register_script( 'admin-fields-configurator-page-date-time', WCCCF_PLUGIN_PATH.'/js/admin-fields-configurator-page-date-time.js', array('jquery'));	
		wp_localize_script( 'admin-fields-configurator-page-date-time', 'wcccf_datetime', array( 'time_format' => $time_format, 
																								 'date_format' => $date_format) );
		wp_enqueue_script('admin-fields-configurator-page-date-time');
		
		wp_enqueue_style('admin-fields-configurator-page', WCCCF_PLUGIN_PATH.'/css/admin-fields-configurator-page.css'); 
		wp_enqueue_style('select2', WCCCF_PLUGIN_PATH.'/css/vendor/select2.min.css'); 
		wp_enqueue_style('datetime-picker', WCCCF_PLUGIN_PATH.'/css/vendor/datetimepicker/default.css'); 
		wp_enqueue_style('datetime-picker-date', WCCCF_PLUGIN_PATH.'/css/vendor/datetimepicker/default.date.css'); 
		wp_enqueue_style('datetime-picker-time', WCCCF_PLUGIN_PATH.'/css/vendor/datetimepicker/default.time.css'); 
		?>
		<div class="wrap">
			 <?php //screen_icon(); ?>
 
			<h2><?php esc_html_e('WooCommerce Checkout Fees Configurator','woocommerce-conditional-checkout-fields'); ?></h2>
	
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
		if(!$screen || $screen->base != "woocommerce-checkout-fields-fees_page_woocommerce-conditional-checkout-fees")
			return;
		
		add_meta_box( 'fees', 
					esc_html__('Fees','woocommerce-conditional-checkout-fields'), 
					array($this, 'add_fees_meta_box'), 
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
	function add_fees_meta_box()
	{
		global $wcccf_html_helper, $wcccf_fee_model;
		
		$field_data = $wcccf_fee_model->get_fee_data();
		
		//wcccf_var_dump($field_data);
		?>
			<div class="column " id="billing_fields_container">
				<?php  $wcccf_html_helper->render_fee_configuration_meta_box($field_data, 'fee'); ?>
			</div>
			<div class="add_new_field_button_container">
				<button class="button button-primary add_new_field_button" data-loader="billing_fields_loader" data-checkout-type="fee" data-container="billing_fields_container" ><?php _e('Add new','woocommerce-conditional-checkout-fields'); ?></button>
				<div class="wcccf_loader" id="billing_fields_loader"></div>
			</div>
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