<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://themelocation.com
 * @since      3.2.0
 *
 * @package    add-fields-to-checkout-page-woocommerce-premium
 * @subpackage add-fields-to-checkout-page-woocommerce-premium/public
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWCFE_Public_Checkout')):
 
class THWCFE_Public_Checkout extends THWCFE_Public {

	public function __construct( $plugin_name, $version ) {
		parent::__construct($plugin_name, $version);
		
		$force_wp_session = apply_filters('thwcfe_force_wp_session', false);
		if($force_wp_session && !isset($_SESSION) && !defined('DOING_CRON')){
			session_start();
		}
		
		add_action('after_setup_theme', array($this, 'define_public_hooks'));
	}

	public function enqueue_styles_and_scripts() {
		global $wp_scripts;
		
		if(is_checkout() || apply_filters('thwcfe_force_enqueue_checkout_public_scripts', false)){
			$debug_mode = apply_filters('thwcfe_debug_mode', false);
			$in_footer  = apply_filters('thwcfe_enqueue_script_in_footer', true);
			
			$suffix = $debug_mode ? '' : '.min';
			$jquery_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
			
			$this->enqueue_styles($suffix, $jquery_version, $in_footer);
			$this->enqueue_scripts($suffix, $jquery_version, $in_footer);
		}
	}
	
	private function enqueue_styles($suffix, $jquery_version, $in_footer) {
		wp_enqueue_style('thwcfe-timepicker-style', THWCFE_ASSETS_URL_PUBLIC.'js/timepicker/jquery.timepicker.css');
		wp_enqueue_style('jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/'. $jquery_version .'/themes/smoothness/jquery-ui.css');
		wp_enqueue_style('thwcfe-public-checkout-style', THWCFE_ASSETS_URL_PUBLIC . 'css/thwcfe-public'. $suffix .'.css', $this->version);
	}

	private function enqueue_scripts($suffix, $jquery_version, $in_footer) {
		wp_register_script('thwcfe-timepicker-script', THWCFE_ASSETS_URL_PUBLIC.'js/timepicker/jquery.timepicker.min.js', array('jquery'), '1.0.1', $in_footer);
		
		$deps = array();
		if( apply_filters( 'thwcfe_include_jquery_ui_i18n', TRUE ) ) {
			wp_register_script('jquery-ui-i18n', '//ajax.googleapis.com/ajax/libs/jqueryui/'.$jquery_version.'/i18n/jquery-ui-i18n.min.js',
			array('jquery','jquery-ui-datepicker'), $in_footer);
			
			$deps[] = 'jquery-ui-i18n';
		}else{
			$deps[] = 'jquery';
			$deps[] = 'jquery-ui-datepicker';
		}
		
		if(THWCFE_Utils::get_settings('disable_select2_for_select_fields') != 'yes'){
			$deps[] = 'select2';
			
			$select2_languages = apply_filters( 'thwcfe_select2_i18n_languages', false);
			if(is_array($select2_languages)){
				foreach($select2_languages as $lang){
					$handle = 'select2_i18n_'.$lang;
					wp_register_script($handle, '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/i18n/'.$lang.'.js', array('jquery','select2'));
					$deps[] = $handle;
				}
			}
		}

		$deps = apply_filters('thwcfe_public_script_deps', $deps);

		wp_register_script('thwcfe-public-checkout-script', THWCFE_ASSETS_URL_PUBLIC.'js/thwcfe-public-checkout'. $suffix .'.js', $deps, THWCFE_VERSION, $in_footer);
		
		
		if(apply_filters('thwcfe_force_register_date_picker_script', false)){
			wp_register_script('thwcfe-datepicker-script', 'https://code.jquery.com/ui/'.$jquery_version.'/jquery-ui.js', array('jquery'), '1.0.1', $in_footer);
			wp_enqueue_script('thwcfe-datepicker-script');
		}
		
		wp_enqueue_script('thwcfe-timepicker-script');
		wp_enqueue_script('thwcfe-public-checkout-script');
		
		$enable_conditions_payment_shipping = THWCFE_Utils::get_settings('enable_conditions_payment_shipping') ? true : false;
		$enable_conditions_review_panel = THWCFE_Utils::get_settings('enable_conditions_review_panel') ? true : false;
		$enable_country_based_conditions = THWCFE_Utils::get_settings('enable_country_based_conditions') ? true : false;
		$enable_conditions_review_panel = $enable_conditions_review_panel || $enable_conditions_payment_shipping || $enable_country_based_conditions;
			
		$wcfe_var = array(
			'lang' => array( 
						'am' => THWCFE_i18n::t('am'), 
						'pm' => THWCFE_i18n::t('pm'),  
						'AM' => THWCFE_i18n::t('AM'), 
						'PM' => THWCFE_i18n::t('PM'),
						'decimal' => THWCFE_i18n::t('.'), 
						'mins' => THWCFE_i18n::t('mins'), 
						'hr'   => THWCFE_i18n::t('hr'), 
						'hrs'  => THWCFE_i18n::t('hrs'),
					),
			'language' 	  => THWCFE_i18n::get_locale_code(),
			'date_format' => THWCFE_Utils::get_jquery_date_format(wc_date_format()),
			'dp_show_button_panel' => apply_filters('thwcfe_date_picker_show_button_panel', true),
			'dp_change_month' => apply_filters('thwcfe_date_picker_change_month', true),
			'dp_change_year' => apply_filters('thwcfe_date_picker_change_year', true),
			'readonly_date_field' => apply_filters('thwcfe_date_picker_field_readonly', true),
			'notranslate_dp' => apply_filters('thwcfe_date_picker_notranslate', true),
			'restrict_time_slots_for_same_day' => apply_filters( 'thwcfe_time_picker_restrict_slots_for_same_day', true ),
			'rebind_all_cfields' => apply_filters( 'thwcfe_enable_conditions_based_on_review_panel_fields', $enable_conditions_review_panel ),
			'change_event_disabled_fields' => apply_filters('thwcfe_change_event_disabled_fields', ''),
			'ajax_url'    => admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script('thwcfe-public-checkout-script', 'thwcfe_public_var', $wcfe_var);
	}
	
	public function define_public_hooks(){
		parent::define_public_hooks();

		$advanced_settings = $this->get_advanced_settings();
		$hp_cf = apply_filters('thwcfd_woocommerce_checkout_fields_hook_priority', 1000);
		
		//Show Custome Fields in Checkout Page
		add_action('woocommerce_checkout_before_customer_details', array($this, 'woo_checkout_before_customer_details'));
		add_action('woocommerce_checkout_after_customer_details', array($this, 'woo_checkout_after_customer_details'));
		
		add_action('woocommerce_before_checkout_billing_form', array($this, 'woo_before_checkout_billing_form'));
		add_action('woocommerce_after_checkout_billing_form', array($this, 'woo_after_checkout_billing_form'));
		
		add_action('woocommerce_before_checkout_shipping_form', array($this, 'woo_before_checkout_shipping_form'));
		add_action('woocommerce_after_checkout_shipping_form', array($this, 'woo_after_checkout_shipping_form'));

		if( get_option('woocommerce_enable_signup_and_login_from_checkout') == 'yes' ) {	
			add_action('woocommerce_before_checkout_registration_form', array($this, 'woo_before_checkout_registration_form'));
			add_action('woocommerce_after_checkout_registration_form', array($this, 'woo_after_checkout_registration_form'));
		}

		add_action('woocommerce_register_form_start', array($this, 'woo_registration_form_start'));
		add_action('woocommerce_register_form_end', array($this, 'woo_registration_form_end'));
		
		add_action('woocommerce_before_order_notes', array($this, 'woo_before_order_notes'));
		add_action('woocommerce_after_order_notes', array($this, 'woo_after_order_notes'));
		
		add_action('woocommerce_review_order_before_cart_contents', array($this, 'woo_review_order_before_cart_contents'));
		add_action('woocommerce_review_order_after_cart_contents', array($this, 'woo_review_order_after_cart_contents'));
		
		add_action('woocommerce_review_order_before_order_total', array($this, 'woo_review_order_before_order_total'));
		add_action('woocommerce_review_order_after_order_total', array($this, 'woo_review_order_after_order_total'));
		
		add_action('woocommerce_checkout_before_terms_and_conditions', array($this, 'woo_checkout_before_terms_and_conditions'));
		add_action('woocommerce_checkout_after_terms_and_conditions', array($this, 'woo_checkout_after_terms_and_conditions'));
		
		add_action('woocommerce_review_order_before_submit', array($this, 'woo_review_order_before_submit'));
		add_action('woocommerce_review_order_after_submit', array($this, 'woo_review_order_after_submit'));
		
		add_action('woocommerce_checkout_before_order_review_heading', array($this, 'woo_checkout_before_order_review_heading'));
		add_action('woocommerce_checkout_before_order_review', array($this, 'woo_checkout_before_order_review'));
		add_action('woocommerce_checkout_after_order_review', array($this, 'woo_checkout_after_order_review'));
		
		add_action('woocommerce_checkout_order_review', array($this, 'woo_checkout_order_review_0'), 0);
		add_action('woocommerce_checkout_order_review', array($this, 'woo_checkout_order_review_99'), 99);
		
		$this->render_sections_added_to_custom_positions();
		
		add_filter('woocommerce_enable_order_notes_field', array($this, 'woo_enable_order_notes_field'), 1000);

		//Themehigh's Multistep plugin Support
		if(THWCFE_Utils::is_thwmsc_enabled()){
			add_action('thwmsc_multi_step_tab_panels', array($this, 'output_checkout_form_hidden_fields'));
		}

		add_action('template_redirect', array($this, 'template_redirect'));
		add_action('woocommerce_remove_cart_item', array($this, 'woo_remove_cart_item'));
		add_filter('woocommerce_update_cart_action_cart_updated', array($this, 'woo_update_cart_action_cart_updated'));

		
		// Checkout init
		add_filter('woocommerce_checkout_fields', array($this, 'woo_checkout_fields'), $hp_cf);
		add_filter('woocommerce_billing_fields', array($this, 'woo_billing_fields'), $hp_cf, 2);
		add_filter('woocommerce_shipping_fields', array($this, 'woo_shipping_fields'), $hp_cf, 2);
		add_filter('woocommerce_default_address_fields', array($this, 'woo_default_address_fields'), $hp_cf);
		if(apply_filters('thwcfe_override_country_locale', true)){
			add_filter('woocommerce_get_country_locale', array($this, 'woo_get_country_locale'), $hp_cf);
			add_filter('woocommerce_get_country_locale_base', array($this, 'woo_prepare_country_locale'), $hp_cf);
			add_filter('woocommerce_get_country_locale_default', array($this, 'woo_prepare_country_locale'), $hp_cf);
		}
		

		//Checkout Process(Validate checkout fields, save user meta and save order meta
		add_action('woocommerce_checkout_process', array($this, 'woo_checkout_process'));
		add_action('woocommerce_after_checkout_validation', array($this, 'woo_checkout_fields_validation'), 10, 2); 
		add_action('woocommerce_checkout_update_user_meta', array($this, 'woo_checkout_update_user_meta'), 10, 2); 
		add_action('woocommerce_checkout_update_order_meta', array($this, 'woo_checkout_update_order_meta'), 10, 2);
		add_action('woocommerce_checkout_order_processed', array($this, 'woo_checkout_order_processed'), 10, 3);

		add_action('wp_ajax_thwcfe_calculate_extra_cost', array($this, 'thwcfe_calculate_extra_cost'), 10);
    	add_action('wp_ajax_nopriv_thwcfe_calculate_extra_cost', array($this, 'thwcfe_calculate_extra_cost'), 10);
		add_action('woocommerce_cart_calculate_fees', array($this, 'woo_cart_calculate_fees') );
		add_filter('woocommerce_cart_totals_fee_html', array($this, 'woo_cart_totals_fee_html'), 10, 2);

		//Custom user meta data
		add_filter( 'woocommerce_checkout_get_value', array($this, 'woo_checkout_get_value'), 10, 2 );
		add_filter( 'default_checkout_billing_country', array($this, 'woo_default_checkout_country'), 10, 2 );
		add_filter( 'default_checkout_shipping_country', array($this, 'woo_default_checkout_country'), 10, 2 );

		//Show in Order Details Page - Customer view (Thankyou Page & My Order Page)
		add_action('woocommerce_order_details_after_order_table', array($this, 'display_custom_fields_in_order_details_page_customer'), 20, 1);

		//Show in Email
		add_filter('woocommerce_email_customer_details_fields', array($this, 'woo_hide_default_customer_fields_in_emails'), 10, 3);

		add_action('woocommerce_email_customer_details', array($this, 'woo_email_customer_details'), 15, 4);
		add_filter('woocommerce_email_customer_details_fields', array($this, 'woo_email_customer_details_fields'), 10, 3);
		add_action('woocommerce_email_order_meta', array($this, 'woo_email_order_meta'), 20, 4);
		add_filter('woocommerce_email_order_meta_fields', array($this, 'woo_email_order_meta_fields'), 10, 3);


		/*if($this->get_setting_value($advanced_settings, 'custom_fields_position_email') === 'woocommerce_email_customer_details_fields'){
			if($this->get_setting_value($advanced_settings, 'enable_html_in_emails') === 'yes'){
				add_action('woocommerce_email_customer_details', array($this, 'display_custom_fields_in_emails'), 30, 4);
			}else{
				add_filter('woocommerce_email_customer_details_fields', array($this, 'woo_display_custom_fields_in_emails'), 10, 3);
			}
		}else{
			if($this->get_setting_value($advanced_settings, 'enable_html_in_emails') === 'yes'){
				add_action('woocommerce_email_order_meta', array($this, 'display_custom_fields_in_emails'), 20, 4);
			}else{
				add_filter('woocommerce_email_order_meta_fields', array($this, 'woo_display_custom_fields_in_emails'), 10, 3);
			}
		}*/

		//Supporting filters to use for other plugins
		//add_filter('thwcfe_custom_checkout_fields_and_values', array('THWCFE_Utils', 'get_custom_checkout_fields_and_values'), 10, 3);
		//add_filter('thwmsc_has_hooked_sections', array($this, 'has_hooked_sections'), 10, 2);
		add_filter('thwcfe_remove_disabled_fields_and_sections', array($this, 'filter_disabled_fields_and_sections'), 10, 2);
		add_filter('thwcfe_field_price_info', array($this, 'get_extra_cost_from_session'));
	}
	

	/********************************************************
	******** DISPLAY DEFAULT SECTIONS & FIELDS - START ******
	********************************************************/
	public function woo_checkout_fields( $checkout_fields ) {
		$sections = $this->get_checkout_sections();
		$cart_info = THWCFE_Utils::get_cart_summary();

		$sections = THWCFE_Utils::get_custom_sections();

		// Remove print Registration Forms
		$before_reg = $this->get_custom_sections_by_hook('before_registration_form');
		$before_reg_name = $before_reg[0];		

		$after_reg = $this->get_custom_sections_by_hook('after_registration_form');
		$after_reg_name = $after_reg[0];
		
		foreach($sections as $sname => $section) {
			if($sname !== 'billing' && $sname !== 'shipping' && $sname !== $before_reg_name && $sname !== $after_reg_name ){
				if(THWCFE_Utils_Section::is_show_section($section, $cart_info)){
					$fieldset = THWCFE_Utils::get_fieldset_to_show($section);
					$fieldset = $fieldset ? $fieldset : array();
					
					if(is_array($fieldset)){
						$sname = $sname === 'additional' ? 'order' : $sname;
						$fieldset = THWCFE_Utils_Repeat::prepare_repeat_fields_set($fieldset);
						$checkout_fields[$sname] = $fieldset; //TODO merge instead replacing existing fields to avoid losing any other non identified property
					}

					$rsections = THWCFE_Utils_Repeat::prepare_repeat_sections($section);
					if(is_array($rsections)){
						foreach($rsections as $rsname => $rsection){
							$rsfieldset = THWCFE_Utils::get_fieldset_to_show($rsection);
							$rsfieldset = $rsfieldset ? $rsfieldset : array();
							
							if(is_array($rsfieldset)){
								$checkout_fields[$rsname] = $rsfieldset;
							}
						}
					}
				}
			}
		}
		return $checkout_fields;
	}
	
	public function woo_billing_fields($fields, $country){
		$section_name = 'billing';
		$section = $this->get_checkout_section('billing');
		$use_default = apply_filters('thwcfe_use_default_fields_if_empty', false, $section_name);
		
		if(THWCFE_Utils_Section::is_valid_section($section)){
			if(is_wc_endpoint_url('edit-address')){
				$fieldset = THWCFE_Utils_Section::get_fieldset($section);
				if($fieldset || !$use_default){
					if(apply_filters('thwcfe_ignore_address_field_changes', false)) {
						$fieldset = $this->prepare_address_fields_my_account($fieldset, $fields, $section_name);
					}else{
						$fieldset = $this->prepare_address_fields($fieldset, $fields, $section_name, $country);
					}
					$fields = $fieldset;
				}
			}else{
				$fieldset = THWCFE_Utils::get_fieldset_to_show($section);
				if($fieldset || !$use_default){
					$fieldset = $this->prepare_address_fields($fieldset, $fields, $section_name, $country);
					$fields = $fieldset;
				}
			}

			$fields = THWCFE_Utils_Repeat::prepare_repeat_fields_set($fields);
		}
				
		return is_array($fields) ? $fields : array();
	}
	
	public function woo_shipping_fields($fields, $country){
		$section_name = 'shipping';
		$section = $this->get_checkout_section('shipping');
		$use_default = apply_filters('thwcfe_use_default_fields_if_empty', false, $section_name);
		
		if(THWCFE_Utils_Section::is_valid_section($section)){
			if(is_wc_endpoint_url('edit-address')){
				$fieldset = THWCFE_Utils_Section::get_fieldset($section);
				if($fieldset || !$use_default){
					if(apply_filters('thwcfe_ignore_address_field_changes', false)) {
						$fieldset = $this->prepare_address_fields_my_account($fieldset, $fields, $section_name);
					}else{
						$fieldset = $this->prepare_address_fields($fieldset, $fields, $section_name, $country);
					}
					$fields = $fieldset;
				}
			}else{
				$fieldset = THWCFE_Utils::get_fieldset_to_show($section);
				if($fieldset || !$use_default){
					$fieldset = $this->prepare_address_fields($fieldset, $fields, $section_name, $country);
					$fields = $fieldset;
				}
			}

			$fields = THWCFE_Utils_Repeat::prepare_repeat_fields_set($fields);
		}
				
		return is_array($fields) ? $fields : array();
	}
	
	public function woo_default_address_fields($fields){
		if(apply_filters('thwcfe_skip_default_address_fields_override', false)){
			return $fields;
		}
		
		$sname = apply_filters('thwcfe_address_field_override_with', 'billing');
		if($sname === 'billing' || $sname === 'shipping'){
			$section = $this->get_checkout_section($sname);
			
			if(THWCFE_Utils_Section::is_valid_section($section)){
				$address_fields = THWCFE_Utils::get_fieldset_to_show($section);
				
				foreach($fields as $name => $field) {
					if($this->is_default_address_field($name)){
						$custom_field = isset($address_fields[$sname.'_'.$name]) ? $address_fields[$sname.'_'.$name] : false;
						
						if($custom_field && !( isset($custom_field['enabled']) && $custom_field['enabled'] == false )){
							$fields[$name]['required'] = isset($custom_field['required']) && $custom_field['required'] ? true : false;
						}
					}
				}
			}
		}
		return $fields;
	}

	public function woo_get_country_locale($locale) {
		$countries_obj = new WC_Countries();
		$allowed_countries = $countries_obj->get_allowed_countries();
		$allowed_countries = array_keys($allowed_countries);
		
		if(is_array($locale) && is_array($allowed_countries)){
			foreach($allowed_countries as $country){
				if(isset($locale[$country])){
					$locale[$country] = $this->woo_prepare_country_locale($locale[$country], $country);
				}
			}
		}

		return $locale;
	}
	
	public function woo_prepare_country_locale($fields, $country=false) {
		if(is_array($fields)){
			$override_ph = apply_filters('thwcfe_address_field_override_placeholder', true, $country);
			$override_label = apply_filters('thwcfe_address_field_override_label', true, $country);
			$override_required = apply_filters('thwcfe_address_field_override_required', false, $country);
			$override_priority = apply_filters('thwcfe_address_field_override_priority', true, $country);
			
			$fieldset = false;
			$sname = apply_filters('thwcfe_country_locale_override_with', 'billing');
			if($sname === 'billing' || $sname === 'shipping'){
				$section = $this->get_checkout_section($sname);
				if(THWCFE_Utils_Section::is_valid_section($section)){
					$fieldset = THWCFE_Utils::get_fieldset_to_show($section);
				}
			}
				
			foreach($fields as $key => $props){
				if($override_ph && isset($props['placeholder'])){
					unset($fields[$key]['placeholder']);
				}
				if($override_label && isset($props['label'])){
					unset($fields[$key]['label']);
				}
				if($override_required && isset($props['required'])){
					if(is_array($fieldset)){
						if(isset($fieldset[$sname.'_'.$key]) && isset($fieldset[$sname.'_'.$key]['required'])){
							$fields[$key]['required'] = $fieldset[$sname.'_'.$key]['required'];
						}
					}else{
						unset($fields[$key]['required']);
					}
				}
				
				if($override_priority && isset($props['priority'])){
					unset($fields[$key]['priority']);
				}
			}
		}
		return $fields;
	}
	/********************************************************
	******** DISPLAY DEFAULT SECTIONS & FIELDS - END ********
	********************************************************/
	
	/********************************************************
	******** DISPLAY CUSTOM SECTIONS & FIELDS - START *******
	********************************************************/
	public function get_custom_sections_by_hook($hook_name){
		$section_hook_map = THWCFE_Utils::get_section_hook_map();
		
		$sections = false;
		if(is_array($section_hook_map) && isset($section_hook_map[$hook_name])){
			$sections = $section_hook_map[$hook_name];
		}	
						
		return empty($sections) ? false : $sections;
	}
	
	public function output_custom_section($sections, $checkout=false, $wrap_with=''){
		if($sections && is_array($sections)){
			$cart_info = THWCFE_Utils::get_cart_summary();

			foreach($sections as $sname){
				$section = THWCFE_Utils::get_checkout_section($sname, $cart_info);
				if(THWCFE_Utils_Section::is_valid_section($section)){
					$this->output_custom_section_single($section, $cart_info, $checkout, $wrap_with);

					$rsections = THWCFE_Utils_Repeat::prepare_repeat_sections($section);
					if(is_array($rsections)){
						foreach($rsections as $rsection){
							$this->output_custom_section_single($rsection, $cart_info, $checkout, $wrap_with);
						}
					}
				}
			}
		}
	}

	public function output_custom_section_single($section, $cart_info, $checkout=false, $wrap_with=''){
		$sname = $section->get_property('name');
		$fields = THWCFE_Utils_Section::get_fieldset($section, $cart_info);
		$fields = THWCFE_Utils_Repeat::prepare_repeat_fields_set($fields);
		
		do_action('thwcfe_before_section_'.$sname, $section);

		if(is_array($fields) && sizeof($fields) > 0){
			$wrap_with_div = THWCFE_Utils::get_settings('wrap_custom_sections_with_div');

			if($wrap_with === 'tr'){
				echo '<tr><td colspan="2">';
			}
			
			if($wrap_with_div === 'yes'){
				$css_class = $section->get_property('cssclass');
				$css_class = !empty($css_class) ? str_replace(" ", "", $css_class) : '';
				$css_class = !empty($css_class) ? str_replace(",", " ", $css_class) : '';
				
				$conditions_data = $this->prepare_ajax_conditions_data_section($section);
				if($conditions_data){
					$css_class .= empty($css_class) ? 'thwcfe-conditional-section' : ' thwcfe-conditional-section';
				}
				
				echo '<div class="thwcfe-checkout-section '. $css_class .' '. $section->get_property('name') .'" '.$conditions_data.'>';
			}						
			if($section->get_property('show_title')){
				echo THWCFE_Utils_Section::get_title_html($section);
			}
			
			do_action('thwcfe_before_section_fields_'.$sname, $section);

			foreach($fields as $name => $field){
				if(!(isset($field['enabled']) && $field['enabled'] == false)) {
					$value = null;
					if($checkout instanceof WC_Checkout){
						$value = $checkout->get_value($name);
					}else if(is_array($checkout) && isset($checkout['post_data'])){
						$value = THWCFE_Utils::get_value_from_query_string($checkout['post_data'], $name);
					}
					
					if(!$value && is_user_logged_in() && isset($field['user_meta']) && $field['user_meta']){
						$current_user = wp_get_current_user();
						if(metadata_exists('user', $current_user->ID, $field['name'])){
							$value = get_user_meta($current_user->ID, $field['name'], true);
						}
					}
					
					woocommerce_form_field($name, $field, $value);
				}
			}

			do_action('thwcfe_after_section_fields_'.$sname, $section);
			
			if($wrap_with_div === 'yes'){
				echo '</div>';
			}

			if($wrap_with === 'tr'){
				echo '</td></tr>';
			}
		}

		do_action('thwcfe_after_section_'.$sname, $section);
	}
		
	public function woo_before_checkout_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('before_checkout_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_after_checkout_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('after_checkout_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_checkout_before_customer_details() {
		$sections = $this->get_custom_sections_by_hook('before_customer_details');
		$this->output_custom_section($sections);	
	}
	public function woo_checkout_after_customer_details() {
		// Multistep plugin Support
		if(!THWCFE_Utils::is_thwmsc_enabled()){
			$this->output_checkout_form_hidden_fields();
		}
		
		$sections = $this->get_custom_sections_by_hook('after_customer_details');
		$this->output_custom_section($sections);	
	}
	public function woo_before_checkout_billing_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('before_checkout_billing_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_after_checkout_billing_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('after_checkout_billing_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_before_checkout_shipping_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('before_checkout_shipping_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_after_checkout_shipping_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('after_checkout_shipping_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_before_checkout_registration_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('before_checkout_registration_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_after_checkout_registration_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('after_checkout_registration_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_registration_form_start($checkout) {
		$sections = $this->get_custom_sections_by_hook('before_registration_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_registration_form_end($checkout) {
		$sections = $this->get_custom_sections_by_hook('after_registration_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_before_order_notes($checkout) {
		$sections = $this->get_custom_sections_by_hook('before_order_notes');
		$this->output_custom_section($sections, $checkout);	
	}		
	public function woo_after_order_notes($checkout) {	
		$sections = $this->get_custom_sections_by_hook('after_order_notes');
		$this->output_custom_section($sections, $checkout);	
	}	
	public function woo_review_order_before_cart_contents($checkout) {	
		$sections = $this->get_custom_sections_by_hook('before_cart_contents');
		$this->output_custom_section($sections, $checkout, 'tr');	
	}	
	public function woo_review_order_after_cart_contents($checkout) {	
		$sections = $this->get_custom_sections_by_hook('after_cart_contents');
		$this->output_custom_section($sections, $checkout, 'tr');	
	}	
	public function woo_review_order_before_order_total($checkout) {	
		$sections = $this->get_custom_sections_by_hook('before_order_total');
		$this->output_custom_section($sections, $checkout, 'tr');	
	}	
	public function woo_review_order_after_order_total($checkout) {	
		$sections = $this->get_custom_sections_by_hook('after_order_total');
		$this->output_custom_section($sections, $checkout, 'tr');	
	}	
	public function woo_checkout_before_terms_and_conditions($checkout) {	
		if(!is_wc_endpoint_url('order-pay')){
			$sections = $this->get_custom_sections_by_hook('before_terms_and_conditions');
			$this->output_custom_section($sections, $_POST);
		}
	}	
	public function woo_checkout_after_terms_and_conditions($checkout) {
		if(!is_wc_endpoint_url('order-pay')){	
			$sections = $this->get_custom_sections_by_hook('after_terms_and_conditions');
			$this->output_custom_section($sections, $_POST);
		}
	}	
	public function woo_review_order_before_submit($checkout) {	
		$sections = $this->get_custom_sections_by_hook('before_submit');
		$this->output_custom_section($sections, $_POST);	
	}	
	public function woo_review_order_after_submit($checkout) {	
		$sections = $this->get_custom_sections_by_hook('after_submit');
		$this->output_custom_section($sections, $_POST);	
	}
	public function woo_checkout_before_order_review_heading($checkout) {	
		$sections = $this->get_custom_sections_by_hook('before_order_review_heading');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_checkout_before_order_review($checkout) {	
		$sections = $this->get_custom_sections_by_hook('before_order_review');
		$this->output_custom_section($sections, $checkout);	
	}	
	public function woo_checkout_after_order_review($checkout) {	
		$sections = $this->get_custom_sections_by_hook('after_order_review');
		$this->output_custom_section($sections, $checkout);	
	}	
	public function woo_checkout_order_review_0($checkout) {	
		$sections = $this->get_custom_sections_by_hook('order_review_0');
		$this->output_custom_section($sections, $checkout);	
	}	
	public function woo_checkout_order_review_99($checkout) {	
		$sections = $this->get_custom_sections_by_hook('order_review_99');
		$this->output_custom_section($sections, $checkout);	
	}
	
	public function render_sections_added_to_custom_positions(){
		$positions = apply_filters('thwcfe_custom_section_positions', array());
		if(is_array($positions)){
			foreach($positions as $hook_name => $label){
				add_action($hook_name, array($this, 'woo_checkout_custom_hook'));
			}
		}
	}
	public function woo_checkout_custom_hook($hook_name, $checkout=false){
		$sections = $this->get_custom_sections_by_hook($hook_name);
		$this->output_custom_section($sections, $checkout);
	}

	/* Hide Additional Fields title if no fields available. */
	public function woo_enable_order_notes_field() {
		$section = $this->get_checkout_section('additional');
		if(THWCFE_Utils_Section::is_valid_section($section)){
			$fieldset = THWCFE_Utils::get_fieldset_to_show($section);
			if($fieldset){
				$enabled = 0;
				foreach($fieldset as $field){
					if($field['enabled']){
						$enabled = 1;
						break;
					}
				}
				return $enabled > 0 ? true : false;
			}else{
				return false;
			}
		}
		return true;
	}
   /*********************************************************
	******** DISPLAY CUSTOM SECTIONS & FIELDS - END *********
	*********************************************************/

	
	/*******************************************
	******** CHECKOUT PROCESS - START **********
	*******************************************/
	public function filter_disabled_fields_and_sections($checkout_fields, $posted){
		$disabled_fields = isset($posted['thwcfe_disabled_fields']) ? wc_clean($posted['thwcfe_disabled_fields']) : '';
		$disabled_sections = isset($posted['thwcfe_disabled_sections']) ? wc_clean($posted['thwcfe_disabled_sections']) : '';
		$dis_fields = $disabled_fields ? explode(",", $disabled_fields) : array();
		$dis_sections = $disabled_sections ? explode(",", $disabled_sections) : array();

		//$dis_sections = array();
		$dis_hooks = array();
		$ship_to_different_address = isset($posted['ship_to_different_address']) ? $posted['ship_to_different_address'] : false;
		
		if(($ship_to_different_address == false || ! WC()->cart->needs_shipping_address())){
			$dis_hooks = array_merge($dis_hooks, array('before_checkout_shipping_form','after_checkout_shipping_form'));
		}		
		if(is_user_logged_in()){
			$dis_hooks = array_merge($dis_hooks, array('before_checkout_registration_form','after_checkout_registration_form'));
		}		
		if(!(isset($posted['terms-field']) && $posted['terms-field'])){
			$dis_hooks = array_merge($dis_hooks, array('before_terms_and_conditions','after_terms_and_conditions'));
		}

		$dis_hooks = apply_filters('thwcfe_disabled_hooks', $dis_hooks);

		if(!empty($dis_hooks)){
			$rsnames = THWCFE_Utils_Repeat::get_repeat_section_names_from_posted($posted);

			foreach($dis_hooks as $hname){
				$hooked_sections = $this->get_custom_sections_by_hook($hname);
				if(is_array($hooked_sections)){
					foreach($hooked_sections as $sname){
						if(!in_array($sname, THWCFE_Utils_Section::$DEFAULT_SECTIONS)){
							$dis_sections[] = $sname;
							$rsections = isset($rsnames[$sname]) ? $rsnames[$sname] : false;
							if(is_array($rsections)){
								$dis_sections = array_merge($dis_sections, $rsections);
							}
						}
					}
				}
			}
		}

		$dis_sections = apply_filters('thwcfe_disabled_sections', $dis_sections);
		$dis_fields = apply_filters('thwcfe_disabled_fields', $dis_fields);

		if( (is_array($dis_fields) && !empty($dis_fields)) || (is_array($dis_sections) && !empty($dis_sections)) ){
			//$checkout_fields = WC()->checkout->checkout_fields;
			$modified = false;
			
			if(is_array($checkout_fields)){
				foreach($checkout_fields as $fieldset_key => $fieldset) {
					if(in_array($fieldset_key, $dis_sections)){
						unset($checkout_fields[$fieldset_key]);
						$modified = true;
						continue;
					}
					
					if(is_array($dis_fields)){
						foreach($dis_fields as $fname){
							if(isset($fieldset[$fname])){
								unset($checkout_fields[$fieldset_key][$fname]);
								$modified = true;
							}
						}
					}
				}
			}
			
			if(!$modified){
				//WC()->checkout->checkout_fields = $checkout_fields;
				$checkout_fields = false;
			}
		}

		return $checkout_fields;
	}

	// Prepare Checkout Fields
	public function woo_checkout_process(){
		$checkout_fields = WC()->checkout->checkout_fields;
		$checkout_fields = $this->filter_disabled_fields_and_sections($checkout_fields, $_POST);
		if($checkout_fields){
			WC()->checkout->checkout_fields = $checkout_fields;
		}
	}
	
	// Validate Checkout Fields
	public function woo_checkout_fields_validation($posted, $errors){
		$checkout_fields = WC()->checkout->checkout_fields;
		
		foreach($checkout_fields as $fieldset_key => $fieldset){
			if($this->maybe_skip_fieldset($fieldset_key, $posted)){
				continue;
			}
				
			foreach($fieldset as $key => $field) {
				if($field['type'] === 'file'){
					$this->validate_file($field, $posted, $errors);
				} else if(isset($posted[$key]) && !$this->is_blank($posted[$key])){
					$this->validate_custom_field($field, $posted, $errors);
				}
			}
		}
	}	
	
	// Save User Meta
	public function woo_checkout_update_user_meta($customer_id, $posted){
		$checkout_fields = WC()->checkout->checkout_fields;

		foreach($checkout_fields as $fieldset_key => $fieldset){
			if($fieldset_key === 'shipping' && !WC()->cart->needs_shipping()){
				continue;
			}
			
			foreach($fieldset as $key => $field) {
				if(isset($field['custom']) && $field['custom'] && isset($posted[$key])){	
					if(isset($field['user_meta']) && $field['user_meta']){
						$type = $field['type'];
						$value = false;
					
						if($type === 'file'){
							$value = $posted[$key];
							
						}else{
							$value  = $posted[$key];
							$value  = is_array($value) ? implode(",", $value) : $value;
							$fvalue = $field['default'];
							
							if($field['type'] === 'checkbox'){
								if($value == 1){
									$value = !empty($field['on_value']) ? $field['on_value'] : $value;
								}else{
									$value = !empty($field['off_value']) ? $field['off_value'] : $value;
								}
							}
						}
						
						$value = apply_filters( 'thwcfe_woocommerce_checkout_user_meta_posted_value_'.$key, $value, $customer_id, $posted );
						update_user_meta($customer_id, $key, $value );
					}
				}
			}
		}
	}
	
	// Save Order Meta
	public function woo_checkout_update_order_meta($order_id, $posted){
		$checkout_fields = WC()->checkout->checkout_fields;
		$ship_to_different_address = isset($posted['ship_to_different_address']) ? $posted['ship_to_different_address'] : false;

		if(!$ship_to_different_address || !WC()->cart->needs_shipping_address()){
			update_post_meta($order_id, 'thwcfe_ship_to_billing', 1);
		}else{
			update_post_meta($order_id, 'thwcfe_ship_to_billing', 0);
		}
		
		$disabled_fields = isset( $_POST['thwcfe_disabled_fields'] ) ? wc_clean( $_POST['thwcfe_disabled_fields'] ) : '';
		if($disabled_fields){
			$dis_fields = $disabled_fields ? explode(",", $disabled_fields) : false;
			if(is_array($dis_fields) && !empty($dis_fields)){
				$dis_fields = array_unique($dis_fields);
				$dis_fields = implode(",", $dis_fields);
				update_post_meta($order_id, '_thwcfe_disabled_fields', $dis_fields);
			}
		}

		$repeat_fields = isset( $_POST['thwcfe_repeat_fields'] ) ? wc_clean( $_POST['thwcfe_repeat_fields'] ) : '';
		if($repeat_fields){
			$r_fields = $repeat_fields ? explode(",", $repeat_fields) : false;
			if(is_array($r_fields) && !empty($r_fields)){
				$r_fields = array_unique($r_fields);
				$r_fields = implode(",", $r_fields);
				update_post_meta($order_id, '_thwcfe_repeat_fields', $r_fields);
			}
		}
		$repeat_sections = isset( $_POST['thwcfe_repeat_sections'] ) ? wc_clean( $_POST['thwcfe_repeat_sections'] ) : '';
		if($repeat_sections){
			$r_sections = $repeat_sections ? explode(",", $repeat_sections) : false;
			if(is_array($r_sections) && !empty($r_sections)){
				$r_sections = array_unique($r_sections);
				$r_sections = implode(",", $r_sections);
				update_post_meta($order_id, '_thwcfe_repeat_sections', $r_sections);
			}
		}
		
		foreach($checkout_fields as $fieldset_key => $fieldset){
			if($this->maybe_skip_fieldset($fieldset_key, $posted)){
				continue;
			}
			
			foreach($fieldset as $key => $field) {
				if(isset($field['custom']) && $field['custom'] && isset($field['order_meta']) && $field['order_meta']){
					$type = $field['type'];
					$value = false;
					
					if($type === 'file'){
						$value = isset($posted[$key]) && !empty($posted[$key]) ? $posted[$key] : false;
						
					}else{
						$value = isset($posted[$key]) && !empty($posted[$key]) ? $posted[$key] : false;
					
						if($field['type'] === 'checkbox'){
							if($value == 1){
								$value = !empty($field['on_value']) ? $field['on_value'] : $value;
							}else{
								$value = !empty($field['off_value']) ? $field['off_value'] : $value;
							}
						}
						
						if($value){
							$value  = is_array($value) ? implode(",", $value) : $value;
							$fvalue = $field['default'];
						}
					}
						
					if($value){
						$value = apply_filters( 'thwcfe_woocommerce_checkout_order_meta_posted_value_'.$key, $value, $order_id, $posted );
						update_post_meta($order_id, $key, $value);
					}
				}
			}
		}
	}


	private function maybe_skip_fieldset( $fieldset_key, $data ) {
		$ship_to_different_address = isset($data['ship_to_different_address']) ? $data['ship_to_different_address'] : false;

		if ( 'shipping' === $fieldset_key && ( ! $ship_to_different_address || ! WC()->cart->needs_shipping_address() ) ) {
			return true;
		}
		return false;
	}

	public function woo_checkout_order_processed($order_id, $posted_data, $order){
		$this->clear_extra_cost_info_from_session();
	}

	public function woo_remove_cart_item(){
		$this->clear_extra_cost_info_from_session();
	}

	public function woo_update_cart_action_cart_updated($cart_updated){
		$this->clear_extra_cost_info_from_session();
		return $cart_updated;
	}

	public function template_redirect(){
		$this->clear_extra_cost_info_from_session();
	}
	/*******************************************
	******** CHECKOUT PROCESS - END ************
	*******************************************/
	
	/*******************************************
	******** PRICE CALCULATION - START *********
	********************************************/
	public function validate_and_filter_fields($price_infos) {
		if($price_infos && is_array($price_infos)){
			$checkout_fields = $this->get_all_checkout_fields_map();
			
			if(!empty($checkout_fields)){
				$f_labels = array();

				$cfields = array();
				foreach($price_infos as $name => $price_info){
					$field = isset($checkout_fields[$name]) && is_array($checkout_fields[$name]) ? $checkout_fields[$name] : false;
					if($field){
						$cfields[$name] = $field;
						$rfields = THWCFE_Utils_Repeat::prepare_repeat_fields_single($field);
						if(is_array($rfields)){
							$cfields = array_merge($cfields, $rfields);
						}
					}
				}
				
				foreach($price_infos as $name => $price_info){
					//$field = isset($checkout_fields[$name]) && is_array($checkout_fields[$name]) ? $checkout_fields[$name] : false;
					$field = isset($cfields[$name]) && is_array($cfields[$name]) ? $cfields[$name] : false;

					if($field){
						$value = isset($price_info['value']) ? $price_info['value'] : '';
						if(is_array($value)){
							$value = implode(",", $price_info['value']);
						}
						$value = !empty($value) ? trim($value) : '';

						$valid = $this->validate_field($name, $value, $field);

						$label = $price_info['label'];
						$label = THWCFE_Utils::preare_fee_name($name, $label, $value, $f_labels);
						
						$f_labels[] = $label;
						$price_infos[$name]['label'] = $label;
						
						if(!$valid){
							unset($price_infos[$name]);
						}
					}
				}
			}
		}
		return $price_infos;
	}

	// Validate Checkout Fields
	public function validate_field($name, $value, $field){
		$valid = true;
		if($value && !$this->is_blank($value)){
			$validation = isset($field['validate']) ? $field['validate'] : '';
			
			if(is_array($validation) && !empty($validation)){
				foreach($validation as $rule){
					switch($rule) {
						case 'number' :
							if(!is_numeric($value)){
								$valid = false;
							}
							break;
						default:
							$custom_validators = $this->get_settings('custom_validators');
							$validator = is_array($custom_validators) && isset($custom_validators[$rule]) ? $custom_validators[$rule] : false;
							if(is_array($validator)){
								$pattern = $validator['pattern'];
								
								if(preg_match($pattern, $value) === 0) {
									$valid = false;
								}
								break;
							}
					}
				}
			}
		}
		return $valid;
	}

	public function save_extra_cost_in_session($price_info) {
		$force_wp_session = apply_filters('thwcfe_force_wp_session', false);

		if($force_wp_session){
			$this->save_extra_cost_in_wp_session($price_info);
		}else{
			$this->save_extra_cost_in_woo_session($price_info);
		}
	}
	
	public function get_extra_cost_from_session() {
		$force_wp_session = apply_filters('thwcfe_force_wp_session', false);
		$extra_cost = array();

		if($force_wp_session){
			$extra_cost = $this->get_extra_cost_from_wp_session();
		}else{
			$extra_cost = $this->get_extra_cost_from_woo_session();
		}
		return $extra_cost;
	}
	
	public function clear_extra_cost_info_from_session() {
		$force_wp_session = apply_filters('thwcfe_force_wp_session', false);

		if($force_wp_session){
			$this->clear_extra_cost_info_from_wp_session();
		}else{
			$this->clear_extra_cost_info_from_woo_session();
		}
	}

	// WP Session
	public function save_extra_cost_in_wp_session($price_info) {
		if(!isset($_SESSION) || apply_filters('thwcfe_force_start_session', false)){
			session_start();
		}
		$this->clear_extra_cost_info_from_wp_session();
		$_SESSION['thwcfe-extra-cost-info'] = $price_info;
	}
	
	public function get_extra_cost_from_wp_session() {
		if(!isset($_SESSION)){
			session_start();
		}
    	$extra_cost = isset($_SESSION['thwcfe-extra-cost-info']) ? $_SESSION['thwcfe-extra-cost-info'] : false;
		return is_array($extra_cost) ? $extra_cost : array();
	}
	
	public function clear_extra_cost_info_from_wp_session() {
		unset($_SESSION['thwcfe-extra-cost-info']);
	}
	
	// Woo Session
	public function save_extra_cost_in_woo_session($price_info) {
		if(WC()->session){
			$this->clear_extra_cost_info_from_woo_session();
			WC()->session->set('thwcfe-extra-cost-info', $price_info);
		}
	}
	
	public function get_extra_cost_from_woo_session() {
		$extra_cost = WC()->session->get('thwcfe-extra-cost-info');
		return is_array($extra_cost) ? $extra_cost : array();
	}
	
	public function clear_extra_cost_info_from_woo_session() {
		if(WC()->session){
			WC()->session->__unset('thwcfe-extra-cost-info');
		}
	}

	// Aborted Request in Session
	public function is_aborted_request($posted){
		$abort_req = isset($posted['abort_req']) ? stripslashes($posted['abort_req']) : '';
		$uid = isset($posted['uid']) ? stripslashes($posted['uid']) : '';
		$is_aborted = false;

		if($abort_req){
			$aborted_requests = $this->get_aborted_request_info_from_session();
			$is_aborted = in_array($uid, $aborted_requests);

			$this->save_aborted_request_info_in_session($abort_req);
		}else{
			$this->clear_aborted_request_info_from_session();
		}
		return $is_aborted;
	}

	public function save_aborted_request_info_in_session($aborted_request) {
		$force_wp_session = apply_filters('thwcfe_force_wp_session', false);

		if($force_wp_session){
			$this->save_aborted_request_info_in_wp_session($aborted_request);
		}else{
			$this->save_aborted_request_info_in_woo_session($aborted_request);
		}
	}
	public function get_aborted_request_info_from_session() {
		$force_wp_session = apply_filters('thwcfe_force_wp_session', false);
		$aborted_requests = array();

		if($force_wp_session){
			$aborted_requests = $this->get_aborted_request_info_from_wp_session();
		}else{
			$aborted_requests = $this->get_aborted_request_info_from_woo_session();
		}
		return $aborted_requests;
	}
	public function clear_aborted_request_info_from_session() {
		$force_wp_session = apply_filters('thwcfe_force_wp_session', false);

		if($force_wp_session){
			$this->clear_aborted_request_info_from_wp_session();
		}else{
			$this->clear_aborted_request_info_from_woo_session();
		}
	}
	public function save_aborted_request_info_in_wp_session($aborted_request) {
		if(!isset($_SESSION) || apply_filters('thwcfe_force_start_session', false)){
			session_start();
		}
		$aborted_requests = $this->get_aborted_request_info_from_wp_session();
		$aborted_requests[] = $aborted_request;
		$_SESSION['thwcfe-aborted-requests'] = $aborted_requests;
	}
	public function save_aborted_request_info_in_woo_session($aborted_request) {
		if($aborted_request && WC()->session){
			$aborted_requests = $this->get_aborted_request_info_from_woo_session();
			$aborted_requests[] = $aborted_request;
			WC()->session->set('thwcfe-aborted-requests', $aborted_requests);
		}
	}

	public function get_aborted_request_info_from_wp_session() {
		if(!isset($_SESSION)){
			session_start();
		}
    	$aborted_requests = isset($_SESSION['thwcfe-aborted-requests']) ? $_SESSION['thwcfe-aborted-requests'] : false;
		return is_array($aborted_requests) ? $aborted_requests : array();
	}
	public function get_aborted_request_info_from_woo_session() {
		$aborted_requests = WC()->session->get('thwcfe-aborted-requests');
		return is_array($aborted_requests) ? $aborted_requests : array();
	}

	public function clear_aborted_request_info_from_wp_session() {
		unset($_SESSION['thwcfe-aborted-requests']);
	}
	public function clear_aborted_request_info_from_woo_session() {
		if(WC()->session){
			WC()->session->__unset('thwcfe-aborted-requests');
		}
	}

	public function thwcfe_calculate_extra_cost() {
		$price_info_json = isset($_POST['price_info']) ? stripslashes($_POST['price_info']) : '';
		
		if($price_info_json) {
			$price_info = json_decode($price_info_json, true);
			$price_info = $this->validate_and_filter_fields($price_info);

			if(!$this->is_aborted_request($_POST)){
				$this->save_extra_cost_in_session($price_info);
			}
		}else{
			if(!$this->is_aborted_request($_POST)){
				$this->clear_extra_cost_info_from_session();
			}
		}
	}
	
	public function calculate_extra_cost($price_info){
		$fprice = 0;
		$price_type = isset($price_info['price_type']) ? $price_info['price_type'] : '';
		$price 		= isset($price_info['price']) ? $price_info['price'] : 0;
		$multiple   = isset($price_info['multiple']) ? $price_info['multiple'] : 0;
		$name 		= isset($price_info['name']) && !empty($price_info['name']) ? $price_info['name'] : false;
		$value 		= isset($price_info['value']) ? $price_info['value'] : false;
		
		if($name){
			$price = apply_filters('thwcfe_checkout_field_extra_price_'.$name, $price, $value);
		}
		
		global $woocommerce;
		$cart_total = $woocommerce->cart->cart_contents_total; //$woocommerce->cart->get_cart_total();
		if($price_type === 'percentage_subtotal'){
			$cart_total = $woocommerce->cart->subtotal;
		}else if($price_type === 'percentage_subtotal_ex_tax'){
			$cart_total = $woocommerce->cart->subtotal_ex_tax;
		}else if($price_type === 'percentage_total'){
			//$cart_total = $woocommerce->cart->subtotal_ex_tax;
		}else if($price_type === 'percentage_total_ex_tax'){
			//$cart_total = $woocommerce->cart->subtotal_ex_tax;
		}
		
		if($multiple == 1){
			$price_arr = explode(",", $price);
			$price_type_arr = explode(",", $price_type);
			
			foreach($price_arr as $index => $oprice){
				$oprice_type = isset($price_type_arr[$index]) ? $price_type_arr[$index] : 'normal';
				
				if($oprice_type === 'percentage' || $oprice_type === 'percentage_subtotal' || $oprice_type === 'percentage_subtotal_ex_tax'){
					if(is_numeric($oprice) && is_numeric($cart_total)){
						$fprice = $fprice + ($oprice/100)*$cart_total;
					}
				}else{
					if(is_numeric($oprice)){
						$fprice = $fprice + $oprice;
					}
				}	
			}
		}else{
			if($price_type === 'percentage' || $price_type === 'percentage_subtotal' || $price_type === 'percentage_subtotal_ex_tax'){
				if(is_numeric($price) && is_numeric($cart_total)){
					$fprice = ($price/100)*$cart_total;
				}
			}else if($price_type === 'dynamic'){
				$price_unit = isset($price_info['price_unit']) ? $price_info['price_unit'] : false;
				
				$qty   = isset($price_info['qty_field']) ? $price_info['qty_field'] : false;
				$qty   = apply_filters('thwcfe_dynamic_price_quantity', $qty, $name);
				$value = !empty($qty) && is_numeric($qty) ? $qty : $value;
				
				if(is_numeric($price) && is_numeric($value) && is_numeric($price_unit) && $price_unit > 0){
					$fprice = $price*($value/$price_unit);
				}
			}else if($price_type === 'custom'){
				if(is_numeric($value)){
					$fprice = $value;
				}
			}else{
				if(is_numeric($price)){
					$fprice = $price;
				}
			}
		}
		
		if($name){
			$fprice = apply_filters('thwcfe_checkout_field_extra_cost_'.$name, $fprice, $value);
		}

		return $fprice;
	}
	
	public function woo_cart_calculate_fees(){
		if(is_checkout()){
			global $woocommerce;
			$extra_cost = $this->get_extra_cost_from_session();
			
			foreach($extra_cost as $name => $price_info){
				$taxable = isset($price_info['taxable']) && $price_info['taxable'] === 'yes' ? true : false ;
				$tax_class = isset($price_info['tax_class']) && !empty($price_info['tax_class']) ? trim($price_info['taxable']) : '';
				
				$fee = $this->calculate_extra_cost($price_info);
				if($fee != 0){
					$woocommerce->cart->add_fee($price_info['label'], $fee, $taxable, $tax_class);
				}
			}
		}
	}
	
	public function woo_cart_totals_fee_html($cart_totals_fee_html, $fee){
		$cart_fee_names = $this->get_cart_fee_names();
		$show_tax_label = apply_filters('thwcfe_show_tax_label_in_cart_totals_fee_html', true);
		
		if($show_tax_label && $cart_fee_names && in_array($fee->name, $cart_fee_names)){
			if($fee && is_numeric($fee->total) && $fee->total != 0){
				if(wc_prices_include_tax()){
					if(!$this->display_prices_including_tax()){
						$cart_totals_fee_html .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
					}
				}else{
					if($this->display_prices_including_tax()){
						$cart_totals_fee_html .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
					}
				}
			}
		}
		return $cart_totals_fee_html;
	}
	
	public function display_prices_including_tax() {
		if($this->woo_version_check('3.3.0')){
			return WC()->cart->display_prices_including_tax();
		}
		return 'incl' === WC()->cart->tax_display_cart ? true : false;
	}
   	
	public function get_cart_fee_names(){
		$names = array();
		$extra_cost = $this->get_extra_cost_from_session();
		if(is_array($extra_cost)){
			foreach($extra_cost as $name => $price_info){
				if(isset($price_info['label'])){
					$names[] = $price_info['label'];
				}
			}
		}
		return !empty($names) ? $names : false;
	}
	/*******************************************
	******** PRICE CALCULATION - END ***********
	********************************************/

	/***********************************************************
	******** DISPLAY & SAVE CUSTOM USER META FIELDS - START ***
	***********************************************************/
	public function woo_checkout_get_value($value, $key){
		$user_fields = THWCFE_Utils_Section::get_user_fieldset_full();
		
		if(is_user_logged_in() && is_array( $user_fields ) && array_key_exists( $key, $user_fields )) {
			$current_user = wp_get_current_user();

			if($meta = get_user_meta( $current_user->ID, $key, true )){
				return $meta;
			}
		}
		
		return $value;
	}
	
	public function woo_default_checkout_country($value, $key){
		if($value && apply_filters('thwcfe_country_hidden_field_override_default_value', false, $key, $value)){
			$section_name = $key === 'shipping_country' ? 'shipping' : 'billing';
			$section = $this->get_checkout_section($section_name);
			$fieldset = THWCFE_Utils_Section::get_fieldset($section);
			
			if($fieldset && isset($fieldset[$key])){
				$field = $fieldset[$key];
				if(isset($field['type']) && $field['type'] === 'hidden'){
					$value = $field['default'] ? $field['default'] : $value;
				}
			}
		}
		return $value;
	}
	/***********************************************************
	******** DISPLAY & SAVE CUSTOM USER META FIELDS - START ***
	***********************************************************/

	/*******************************************************
	******** DISPLAY CUSTOM FIELDS VALUES - START *********
	*******************************************************/
	public function display_section_title_as_table_row($section){
		$html = '';
		$title = $section->get_property('title');
					
		if($title){
			$title = THWCFE_i18n::t($title);
			$subtitle = $section->get_property('subtitle') ? $section->get_property('subtitle') : false;
			$subtitle = $subtitle ? THWCFE_i18n::t($subtitle) : '';

			if($subtitle){
				$title .= '<br/><span style="font-size:80%">'.$subtitle.'</span>';
			}

			if(is_account_page()){
				$html .= '<tr><th colspan="2" class="thwcfe-section-title">'. $title .'</th></tr>';
			}else{
				$html .= '<tr><th colspan="2" class="thwcfe-section-title">'. $title .'</th></tr>';
			}
		}
		return $html;
	}

	public function display_custom_fields_as_table_row($order_id, $key, $field, $is_nl2br=true){
		$html = '';
		$type = $field->get_property('type');
					
		if($type === 'label' || $type === 'heading'){
			$label = $field->get_property('title') ? $field->get_property('title') : false;
			$subtitle = $field->get_property('subtitle') ? $field->get_property('subtitle') : false;

			if($label || $subtitle){
				if(apply_filters('thwcfe_esc_attr_custom_field_label_thankyou_page', false)){
					$label = $label ? THWCFE_i18n::esc_attr__t($label) : '';
					$subtitle = $subtitle ? THWCFE_i18n::esc_attr__t($subtitle) : '';
				}else{
					$label = $label ? THWCFE_i18n::t($label) : '';
					$subtitle = $subtitle ? THWCFE_i18n::t($subtitle) : '';
				}
				
				if($subtitle){
					$label .= '<br/><span style="font-size:80%">'.$subtitle.'</span>';
				}
				
				if(is_account_page()){
					$html .= '<tr><th colspan="2" class="thwcfe-html-'.$type.'">'. $label .'</th></tr>';
				}else{
					$html .= '<tr><th colspan="2" class="thwcfe-html-'.$type.'">'. $label .'</th></tr>';
				}
			}
		}else{
			$value = get_post_meta( $order_id, $key, true );

			if(!empty($value)){
				if($type === 'file'){
					$value = WCFE_Checkout_Fields_Utils::get_file_display_name_order($value, apply_filters('thwcfe_clickable_filename_in_order_view', true, $key));
				}else{
					$value = $this->get_option_text_from_value($field, $value);
					$value = is_array($value) ? implode(", ", $value) : $value;
				}
			
				if(($type === 'multiselect' || $type === 'checkboxgroup') && apply_filters('thwcfe_align_field_value_in_separate_lines', false)){
					$value = str_replace(",", ",<br/>", $value);
				}
				
				if($is_nl2br && $type === 'textarea'){
					$value = nl2br($value);
				}
				
				$label = $field->get_property('title') ? $field->get_property('title') : $key;
				$label = apply_filters('thwcfe_esc_attr_custom_field_label_thankyou_page', false) ? THWCFE_i18n::esc_attr__t($label) : THWCFE_i18n::t($label);
				
				if(apply_filters( 'thwcfe_view_order_customer_details_table_view', true )){
					$html .= '<tr><td>'. $label .':</td><td>'. wptexturize($value) .'</td></tr>';
				}else{
					$html .= '<br/><dt>'. $label .':</dt><dd>'. wptexturize($value) .'</dd>';
				}									
			}
		}
		return $html;
	}

	public function may_display_repeat_fields_value($order_id, $key, $field, $rfnames, $is_nl2br=true){
		$html = '';

		$rfields = THWCFE_Utils_Repeat::get_repeat_fields($order_id, $key, $field, $rfnames);
		if(is_array($rfields)){
			foreach($rfields as $rkey => $rfield) {
				$html .= $this->display_custom_fields_as_table_row($order_id, $rkey, $rfield, $is_nl2br);
			}
		}

		return $html;
	}

	/*
	 * Display custom fields in order details page for customers.
	 * - Thank You page, after customet details.
	 * - My Account order details page, after customer details.
	 */
	public function display_custom_fields_in_order_details_page_customer($order){		
		$sections = WCFE_Checkout_Fields_Utils::get_checkout_sections();	
		$sections = THWCFE_Utils::sort_sections($sections);

		$order_id = THWCFE_Utils::get_order_id($order);
		$rsnames = THWCFE_Utils_Repeat::get_repeat_section_names($order_id);
		$rfnames = THWCFE_Utils_Repeat::get_repeat_field_names($order_id);
		$dis_fields = WCFE_Checkout_Fields_Utils::get_disabled_fields($order_id);
		$is_nl2br = apply_filters('thwcfe_nl2br_custom_field_value', true);

		$html = '';
		foreach($sections as $sname => $section){
			$html .= $this->display_section_in_customer_order($order, $order_id, $section, $dis_fields, $rfnames, $is_nl2br);

			if($sname != 'billing' && $sname != 'shipping' && $sname != 'additional'){
				$html .= $this->may_display_repeat_sections_in_customer_order($order, $order_id, $sname, $section, $dis_fields, $rsnames, $rfnames, $is_nl2br);
			}			
		}

		if($html){
			do_action( 'thwcfe_order_details_before_custom_fields_table', $order ); 
			?>
			<table class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">
				<?php
					do_action( 'thwcfe_order_details_before_custom_fields', $order );
					echo $html;
					do_action( 'thwcfe_order_details_after_custom_fields', $order ); 
				?>
			</table>
			<?php
			do_action( 'thwcfe_order_details_after_custom_fields_table', $order ); 
		}
	}

	public function display_section_in_customer_order($order, $order_id, $section, $dis_fields, $rfnames, $is_nl2br=true){
		$html = '';
		$fields = THWCFE_Utils_Section::get_fields($section);
		if(is_array($fields)){
			$fields_html = $this->display_fields_in_customer_order($order, $order_id, $fields, $dis_fields, $rfnames, $is_nl2br);

			if($fields_html){
				$show_section_title = THWCFE_Utils_Section::is_show_section_title($section, 'customer_order');
				if($show_section_title){
					$html .= $this->display_section_title_as_table_row($section);
				}
				$html .= $fields_html;
			}
		}
		return $html;
	}

	public function may_display_repeat_sections_in_customer_order($order, $order_id, $key, $section, $dis_fields, $rsnames, $rfnames, $is_nl2br=true){
		$html = '';
		$rsections = THWCFE_Utils_Repeat::get_repeat_sections($order_id, $key, $section, $rsnames);
		if(is_array($rsections)){
			foreach($rsections as $rskey => $rsection) {
				$html .= $this->display_section_in_customer_order($order, $order_id, $rsection, $dis_fields, $rfnames, $is_nl2br);
			}
		}
		return $html;
	}

	public function display_fields_in_customer_order($order, $order_id, $fields, $dis_fields, $rfnames, $is_nl2br=true){
		$fields_html = '';
		if(is_array($fields) && !empty($fields)){
			foreach($fields as $key => $field) {
				if(THWCFE_Utils_Field::is_custom_enabled($field) && $field->get_property('show_in_thank_you_page')){
					if(!in_array($key, $dis_fields)){
						$fields_html .= $this->display_custom_fields_as_table_row($order_id, $key, $field, $is_nl2br);
					}

					$fields_html .= $this->may_display_repeat_fields_value($order_id, $key, $field, $rfnames, $is_nl2br);
				}
			}
		}
		return $fields_html;
	}
	/*******************************************************
	******** DISPLAY CUSTOM FIELDS VALUES - END ***********
	*******************************************************/
}

endif;