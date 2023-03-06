<?php
/**
 * The admin license settings page functionality of the plugin.
 *
 * @link       https://themelocation.com
 * @since      3.1.0
 *
 * @package    add-fields-to-checkout-page-woocommerce-premium
 * @subpackage add-fields-to-checkout-page-woocommerce-premium/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWCFE_Admin_Settings_License')):

class THWCFE_Admin_Settings_License extends THWCFE_Admin_Settings {
	protected static $_instance = null;
	
	public $ame_data_key;
	public $ame_deactivate_checkbox;
	public $ame_activation_tab_key;
	public $ame_deactivation_tab_key;

	public function __construct() {
		parent::__construct();
		
		$this->page_id = 'license_settings';
		$this->data_prefix = str_ireplace( array( ' ', '_', '&', '?' ), '_', strtolower( THWCFE_SOFTWARE_TITLE ) );
		$this->data_prefix = str_ireplace( 'woocommerce', 'th', $this->data_prefix );
		$this->ame_data_key             = $this->data_prefix . '_data';
		$this->ame_deactivate_checkbox  = $this->data_prefix . '_deactivate_checkbox';
		$this->ame_activation_tab_key   = $this->data_prefix . '_license_activate';
		$this->ame_deactivation_tab_key = $this->data_prefix . '_license_deactivate';
	}
	
	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	} 	
	
	public function render_page(){
		settings_errors();
		$this->output_tabs();
		$this->output_content();
	}

	private function output_content(){
		echo do_shortcode('[licensepage-woocommerce-checkout-field-editor]');
	}
	
	private function output_content_old(){
		?>            
        <div class="thpladmin-license-settings" style="padding-left: 30px;"> 
			<p class="info-text">
				<?php _e('You need to have a valid License in order to get upgrades or support for this plugin.', 'woocommerce-checkout-field-editor-pro') ?>
			</p>       
			       
		    <form action="options.php" method='post'>
				<div class="form-table thpladmin-form-table thpladmin-license-settings-grid">
					<?php
					settings_fields( $this->ame_data_key );
					do_settings_sections( $this->ame_activation_tab_key );
					submit_button( __('Save Changes', 'woocommerce-checkout-field-editor-pro') );
					?>
				</div>
            </form>
			
			<form action="options.php" method='post'>
				<div class="form-table thpladmin-form-table thpladmin-license-settings-grid">
					<?php
					settings_fields( $this->ame_deactivate_checkbox );
					do_settings_sections( $this->ame_deactivation_tab_key );
					submit_button( __('Save Changes', 'woocommerce-checkout-field-editor-pro') );
					?>
				</div>
			</form>
    	</div>       
    	<?php
	}
}

endif;