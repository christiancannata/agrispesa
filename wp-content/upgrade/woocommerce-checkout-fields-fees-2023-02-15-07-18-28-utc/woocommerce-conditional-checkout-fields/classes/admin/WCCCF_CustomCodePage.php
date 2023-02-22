<?php 
class WCCCF_CustomCodePage
{
	var $page = "woocommerce-conditional-checkout-custom-code";
	
	public function __construct()
	{
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
	}
	public function add_page($cap )
	{
		
		$this->page = add_submenu_page( 'woocommerce-conditional-checkout-fields', esc_html__('Custom JS & CSS', 'woocommerce-conditional-checkout-fields'), esc_html__('Custom JS & CSS', 'woocommerce-conditional-checkout-fields'), $cap, 'woocommerce-conditional-checkout-custom-code', array($this, 'render_page'));
		
		add_action('load-'.$this->page,  array($this,'page_actions'),9);
		add_action('admin_footer-'.$this->page,array($this,'footer_scripts'));
	}
	function page_actions()
	{
		do_action('add_meta_boxes_'.$this->page, null);
		do_action('add_meta_boxes', $this->page, null);
	}
	function footer_scripts(){
		
		?>
			< script > postboxes.add_postbox_toggles(pagenow);</ script >
		<?php
	}
	public function render_page()
	{
		global $pagenow, $wcccf_code_model;
		
		add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );
	    if(isset($_POST) && isset($_POST['wcccuf_nonce_configuration_data']) && wp_verify_nonce($_POST['wcccuf_nonce_configuration_data'], 'wcccuf_save_data') && isset($_POST['wcccf_code']))
			$wcccf_code_model->save_code($_POST['wcccf_code']);
	
		wp_enqueue_script('postbox'); 
		wp_enqueue_style('admin-custom-code', WCCCF_PLUGIN_PATH.'/css/admin-custom-code-page.css'); 	
		
		?>
		<div class="wrap">
			
			<h2><?php esc_html__('WooCommerce Checkout Custom JS & CSS','woocommerce-conditional-checkout-fields'); ?></h2>
	
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
		if(!$screen || $screen->base != "woocommerce-checkout-fields-fees_page_woocommerce-conditional-checkout-custom-code")
			return;
		
		add_meta_box( 'code_fields', 
					esc_html__('Javascript & CSS code - Checkout page','woocommerce-conditional-checkout-fields'), 
					array($this, 'add_custom_code_fields_meta_box'), 
					'woocommerce-conditional-checkout-fields', 
					'normal' 
			);
		
			
		add_meta_box('save_button', 
				esc_html__('Save','woocommerce-conditional-checkout-fields'), 
				array($this, 'add_save_button_meta_box'), 
				'woocommerce-conditional-checkout-fields',
				'side' 
			);
	}
	function add_custom_code_fields_meta_box()
	{
		global $wcccf_code_model;
		$javascript = $wcccf_code_model->get_code('javascript');
		$css = $wcccf_code_model->get_code('css');
		//WP_CONTENT_DIR
		?>
		<p class="wcccf_option_container">
			<label><?php wcccf_html_escape_allowing_special_tags(__('Javscript - The following custom code will be included in the Checkout page','woocommerce-conditional-checkout-fields')); ?> </label>
			<span class="wcccf_description"><?php wcccf_html_escape_allowing_special_tags(__('<strong><i>NOTE:</i></strong> You can also copy in the <strong>wp-content/wcccf_custom_code/js</strong> directory (create it if not existing) all the JavaScript files you need. Those will be automatically loaded in the Checkout page.','woocommerce-conditional-checkout-fields')); ?></span>
			<textarea name="wcccf_code[javascript]"><?php echo $javascript; ?></textarea>
		</p>
		<p class="wcccf_option_container">
			<label><?php esc_html_e('CSS - The following custom code will be included in the Checkout page','woocommerce-conditional-checkout-fields'); ?></label>
			<span class="wcccf_description"><?php wcccf_html_escape_allowing_special_tags(__('<strong><i>NOTE:</i></strong> You can also copy in the <strong>wp-content/wcccf_custom_code/css</strong> directory (create it if not existing) all the CSS files you need. Those will be automatically loaded in the Checkout page.','woocommerce-conditional-checkout-fields')); ?></span>
			<textarea name="wcccf_code[css]"><?php echo $css; ?></textarea>
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