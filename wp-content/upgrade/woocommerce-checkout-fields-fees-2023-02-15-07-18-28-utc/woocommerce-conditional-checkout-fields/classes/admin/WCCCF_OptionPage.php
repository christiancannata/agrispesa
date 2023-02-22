<?php 
class WCCCF_OptionPage 
{
	var $page = "woocommerce-conditional-checkout-option";
	
	public function __construct()
	{
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
	}
	public function add_page($cap )
	{
		
		$this->page = add_submenu_page( 'woocommerce-conditional-checkout-fields', esc_html__('Options', 'woocommerce-conditional-checkout-fields'), esc_html__('Options', 'woocommerce-conditional-checkout-fields'), $cap, 'woocommerce-conditional-checkout-option', array($this, 'render_page'));
		
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
		global $wcccf_option_model;
		wp_enqueue_script('postbox'); 
		
		wp_enqueue_style('admin-option', WCCCF_PLUGIN_PATH.'/css/admin-option-page.css'); 
		
		if(isset($_POST) && isset($_POST['wcccf_option']) && isset($_POST['wcccuf_nonce_option_data']) && wp_verify_nonce($_POST['wcccuf_nonce_option_data'], 'wcccuf_save_data'))
		{
			$wcccf_option_model->save_options($_POST['wcccf_option']);
		}
		
		?>
		<div class="wrap">
			<h2><?php esc_html_e('Options','woocommerce-conditional-checkout-fields'); ?></h2>
	
			<form id="post"  method="post">
				<?php wp_nonce_field( 'wcccuf_save_data', 'wcccuf_nonce_option_data' ); ?>
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
		if(!$screen || $screen->base != "woocommerce-checkout-fields-fees_page_woocommerce-conditional-checkout-option")
			return;
		
		add_meta_box( 'date_time_options', 
					esc_html__('Date & Time','woocommerce-conditional-checkout-fields'), 
					array($this, 'add_date_time_options'), 
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
	
	function add_date_time_options()
	{
		global $wcccf_option_model;
		$options = $wcccf_option_model->get_options();
		
		?>
		<div class="wcccf_option_container">
			<h3><?php esc_html_e('Date format','woocommerce-conditional-checkout-fields'); ?></h3>
			<p><?php wcccf_html_escape_allowing_special_tags(__('The date format displayed on checkout form can be customized. The following table explains which format can be used. <strong>Make sure to be using a valid format</strong>.','woocommerce-conditional-checkout-fields')); ?></p>
			<table class="table">
			  <thead>
				<tr>
				  <th><?php esc_html_e('Rule','woocommerce-conditional-checkout-fields'); ?></th>
				  <th><?php esc_html_e('Description','woocommerce-conditional-checkout-fields'); ?></th>
				  <th><?php esc_html_e('Result','woocommerce-conditional-checkout-fields'); ?></th>
				</tr>
			  </thead>
			  <tbody>
				<tr>
				  <td><code>d</code></td>
				  <td><?php esc_html_e('Date of the month','woocommerce-conditional-checkout-fields'); ?></td>
				  <td>1 – 31</td>
				</tr>
				<tr>
				  <td><code>dd</code></td>
				  <td><?php esc_html_e('Date of the month with a leading zero','woocommerce-conditional-checkout-fields'); ?></td>
				  <td>01 – 31</td>
				</tr>
				<tr>
				  <td><code>ddd</code></td>
				  <td><?php esc_html_e('Day of the week in short form','woocommerce-conditional-checkout-fields'); ?></td>
				  <td><?php esc_html_e('Sun – Sat','woocommerce-conditional-checkout-fields'); ?></td>
				</tr>
				<tr>
				  <td><code>dddd</code></td>
				  <td><?php esc_html_e('Day of the week in full form','woocommerce-conditional-checkout-fields'); ?></td>
				  <td><?php esc_html_e('Sunday – Saturday','woocommerce-conditional-checkout-fields'); ?></td>
				</tr>
			  </tbody>
			  <tbody>
				<tr>
				  <td><code>m</code></td>
				  <td><?php esc_html_e('Month of the year','woocommerce-conditional-checkout-fields'); ?></td>
				  <td>1 – 12</td>
				</tr>
				<tr>
				  <td><code>mm</code></td>
				  <td><?php esc_html_e('Month of the year with a leading zero','woocommerce-conditional-checkout-fields'); ?></td>
				  <td>01 – 12</td>
				</tr>
				<tr>
				  <td><code>mmm</code></td>
				  <td><?php esc_html_e('Month name in short form','woocommerce-conditional-checkout-fields'); ?></td>
				  <td>Jan – Dec</td>
				</tr>
				<tr>
				  <td><code>mmmm</code></td>
				  <td><?php esc_html_e('Month name in full form','woocommerce-conditional-checkout-fields'); ?></td>
				  <td><?php esc_html_e('January – December','woocommerce-conditional-checkout-fields'); ?></td>
				</tr>
			  </tbody>
			  <tbody>
				<!--<tr>
				  <td><code>yy</code></td>
				  <td>Year in short form <b>*</b></td>
				  <td>00 – 99</td>
				</tr> -->
				<tr>
				  <td><code>yyyy</code></td>
				  <td>Year in full form</td>
				  <td>2000 – 2999</td>
				</tr>
			  </tbody>
			</table>
			
			<label><?php esc_html_e('Date format','woocommerce-conditional-checkout-fields'); ?> <span class="label_required">*</span></label>	
			<input type="text" class="required" name="wcccf_option[date_format]" value="<?php echo wcccf_get_value_if_set($options, 'date_format', 'yyyy-mm-dd'); ?>" placeholder="<?php esc_html_e('Default value: yyyy-mm-dd','woocommerce-conditional-checkout-fields'); ?>" required="required"></input>
		</div>
		<div class="wcccf_option_container">
			<h3><?php esc_html_e('Time format','woocommerce-conditional-checkout-fields'); ?></h3>
			<p><?php wcccf_html_escape_allowing_special_tags(__('The time format displayed on checkout form can be customized. The following table explains which format can be used. <strong>Make sure to be using a valid format</strong>.','woocommerce-conditional-checkout-fields')); ?></p>
			
			<table class="table">
			  <thead>
				<tr>
				  <th>Rule</th>
				  <th>Description</th>
				  <th>Result</th>
				</tr>
			  </thead>
			  <tbody>
				<tr>
				  <td><code>h</code></td>
				  <td>Hour in 12-hour format</td>
				  <td>1 – 12</td>
				</tr>
				<tr>
				  <td><code>hh</code></td>
				  <td>Hour in 12-hour format with a leading zero</td>
				  <td>01 – 12</td>
				</tr>
				<tr>
				  <td><code>H</code></td>
				  <td>Hour in 24-hour format</td>
				  <td>0 – 23</td>
				</tr>
				<tr>
				  <td><code>HH</code></td>
				  <td>Hour in 24-hour format with a leading zero</td>
				  <td>00 – 23</td>
				</tr>
			  </tbody>
			  <tbody>
				<tr>
				  <td><code>i</code></td>
				  <td>Minutes</td>
				  <td>00 – 59</td>
				</tr>
			  </tbody>
			  <tbody>
				<tr>
				  <td><code>a</code></td>
				  <td>Day time period</td>
				  <td>a.m. / p.m.</td>
				</tr>
				<tr>
				  <td><code>A</code></td>
				  <td>Day time period in uppercase</td>
				  <td>AM / PM</td>
				</tr>
			  </tbody>
			</table>
			
			<label><?php esc_html_e('Time format','woocommerce-conditional-checkout-fields'); ?> <span class="label_required">*</span></label>	
			<input type="text" class="required" name="wcccf_option[time_format]" value="<?php echo wcccf_get_value_if_set($options, 'time_format', 'HH:i'); ?>" placeholder="<?php esc_html_e('Default value: HH:i','woocommerce-conditional-checkout-fields'); ?>" required="required"></input>
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