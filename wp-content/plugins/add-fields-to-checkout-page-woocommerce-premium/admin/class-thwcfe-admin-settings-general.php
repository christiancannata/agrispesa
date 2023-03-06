<?php
/**
 * The admin general settings page functionality of the plugin.
 *
 * @link       https://themelocation.com
 * @since      3.1.0
 *
 * @package    add-fields-to-checkout-page-woocommerce-premium
 * @subpackage add-fields-to-checkout-page-woocommerce-premium/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWCFE_Admin_Settings_General')):

class THWCFE_Admin_Settings_General extends THWCFE_Admin_Settings {
	protected static $_instance = null;
	public $field_factory = NULL;
	
	private $cell_props_L = array();
	private $cell_props_R = array();
	private $cell_props_CB = array();
	private $cell_props_CBS = array();
	private $cell_props_CBL = array();
	private $cell_props_CP = array();

	public function __construct() {
		parent::__construct();
		$this->page_id    = 'fields';
		$this->section_id = 'billing';
		
		//$this->move_fields_from_one_to_another();
	}
	
	//Example function to move fields from one section to another.
	private function move_fields_from_one_to_another(){
		$section_from = $this->get_checkout_section('billing');
		$section_to = $this->get_checkout_section('activities');
		
		$field_set = THWCFE_Utils_Section::get_fields($section_from);
		
		$fields = array('fall_sports', 'winter_sports', 'spring_sport', 'middle_school', 'orange_community_ed');
		foreach($fields as $fname){
			$field = $field_set[$fname];
			$section_to = THWCFE_Utils_Section::add_field($section_to, $field);
		}
		$result = $this->update_section($section_to);
	}
	
	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}	

	public function init(){
		$this->init_constants();
		$this->wpml_register_address_strings();

		$this->locale_fields = array(
			'billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode', 'billing_city',
			'shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city',
			'order_comments'
		);

		$this->render_page();
	}

	public function define_admin_hooks(){
		/*if(THWCFE_Utils::get_settings('lazy_load_products') != 'yes'){
			add_filter('thpladmin_load_products', array('WCFE_Checkout_Fields_Utils', 'load_products'));
		}
		
		if(THWCFE_Utils::get_settings('lazy_load_categories') != 'yes'){
			add_filter('thpladmin_load_products_cat', array('WCFE_Checkout_Fields_Utils', 'load_products_cat'));
		}*/
		
		add_filter('thpladmin_load_user_roles', array('WCFE_Checkout_Fields_Utils', 'load_user_roles'));		
		
		// Show in order details page
		add_action('woocommerce_admin_order_data_after_order_details', array($this, 'woo_admin_order_data_after_order_details'), 20, 1);
		add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'woo_admin_order_data_after_billing_address'), 20, 1);
		add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'woo_admin_order_data_after_shipping_address'), 20, 1);
		add_filter('postmeta_form_keys', array($this, 'postmeta_form_keys'), 10, 2);
		
		add_filter("woocommerce_customer_meta_fields", array($this, 'woo_customer_meta_fields'), 11, 1 );
		
		// Shop order columns
		add_action('manage_edit-shop_order_columns', array($this, 'manage_edit_shop_order_columns'), 11, 1);
		add_action('manage_shop_order_posts_custom_column', array($this, 'manage_shop_order_posts_custom_column'), 11, 2 );
		add_filter("manage_edit-shop_order_sortable_columns", array($this, 'manage_edit_shop_order_sortable_columns'), 11, 1 );
		add_filter('posts_clauses', array($this, 'posts_clauses_sort_shop_orders'), 10, 2);
		//add_action('pre_get_posts', array($this, 'pre_get_posts'));
		//add_filter('posts_orderby', array($this, 'posts_orderby'), 10, 2);
		
		// Formatted addresses
		add_filter('woocommerce_localisation_address_formats', array($this, 'woo_localisation_address_formats'), 20, 2 ); 
		add_filter('woocommerce_formatted_address_replacements', array($this, 'woo_formatted_address_replacements'), 20, 2 ); 
		add_filter('woocommerce_order_formatted_billing_address', array($this, 'woo_order_formatted_billing_address'), 20, 2 );
		add_filter('woocommerce_order_formatted_shipping_address', array($this, 'woo_order_formatted_shipping_address'), 20, 2 );
		//add_filter('woocommerce_my_account_my_address_formatted_address', array($this, 'woo_my_account_my_address_formatted_address'), 20, 3 );
		//add_filter('woocommerce_formatted_address_force_country_display', '__return_true' );
		
		// Show in Email
		/*add_filter('woocommerce_email_customer_details_fields', array($this, 'woo_hide_default_customer_fields_in_emails'), 10, 3);

		add_action('woocommerce_email_customer_details', array($this, 'woo_email_customer_details'), 15, 4);
		add_filter('woocommerce_email_customer_details_fields', array($this, 'woo_email_customer_details_fields'), 10, 3);
		add_action('woocommerce_email_order_meta', array($this, 'woo_email_order_meta'), 20, 4);
		add_filter('woocommerce_email_order_meta_fields', array($this, 'woo_email_order_meta_fields'), 10, 3);*/

		/*if(THWCFE_Utils::get_settings('custom_fields_position_email') === 'woocommerce_email_customer_details_fields' ){
			if(THWCFE_Utils::get_settings('enable_html_in_emails') === 'yes'){
				add_action('woocommerce_email_order_meta', array($this, 'display_custom_fields_in_emails'), 20, 4);
			}else{
				add_filter('woocommerce_email_customer_details_fields', array($this, 'woo_display_custom_fields_in_emails'), 10, 3);
			}
		}else{
			if(THWCFE_Utils::get_settings('enable_html_in_emails') === 'yes'){
				add_action('woocommerce_email_customer_details', array($this, 'display_custom_fields_in_emails'), 20, 4);
			}else{
				add_filter('woocommerce_email_order_meta_fields', array($this, 'woo_display_custom_fields_in_emails'), 10, 3);
			}
		}*/
		
		//add_filter('woocommerce_attribute_label', array($this, 'woo_attribute_label'), 10, 2 );
		
		//To get checkout fields & values outside the plugin
		//add_filter('thwcfe_custom_checkout_fields_and_values', array('THWCFE_Utils', 'get_custom_checkout_fields_and_values'), 10, 3);
	}
	
	public function init_constants(){
		$this->cell_props_L = array( 
			'label_cell_props' => 'width="13%"', 
			'input_cell_props' => 'width="34%"', 
			'input_width' => '250px',  
		);
		
		$this->cell_props_R = array( 
			'label_cell_props' => 'width="13%"', 
			'input_cell_props' => 'width="34%"', 
			'input_width' => '250px', 
		);
		
		$this->cell_props_CB = array( 
			'label_props' => 'style="margin-right: 40px;"', 
		);
		$this->cell_props_CBS = array( 
			'label_props' => 'style="margin-right: 15px;"', 
		);
		$this->cell_props_CBL = array( 
			'label_props' => 'style="margin-right: 52px;"', 
		);
		
		$this->cell_props_CP = array(
			'label_cell_props' => 'width="13%"', 
			'input_cell_props' => 'width="34%"', 
			'input_width' => '225px',
		);
		
		$this->section_form_props = $this->get_section_form_props();
		
		$this->field_form_props = $this->get_field_form_props();
		$this->field_form_props_display = $this->get_field_form_props_display();
	}
	
	public function wpml_register_address_strings(){
		THWCFE_i18n::wpml_register_string('Field Title - '.'Canton', 'Canton' );
		THWCFE_i18n::wpml_register_string('Field Title - '.'County', 'County' );
		THWCFE_i18n::wpml_register_string('Field Title - '.'District', 'District' );		
		THWCFE_i18n::wpml_register_string('Field Title - '.'Municipality', 'Municipality' );
		THWCFE_i18n::wpml_register_string('Field Title - '.'Prefecture', 'Prefecture' );
		THWCFE_i18n::wpml_register_string('Field Title - '.'Province', 'Province' );
		THWCFE_i18n::wpml_register_string('Field Title - '.'Region', 'Region' );
		THWCFE_i18n::wpml_register_string('Field Title - '.'State', 'State' );
		THWCFE_i18n::wpml_register_string('Field Title - '.'State / Zone', 'State / Zone' );
		
		THWCFE_i18n::wpml_register_string('Field Title - '.'Suburb', 'Suburb' );	
		THWCFE_i18n::wpml_register_string('Field Title - '.'Town / District', 'Town / District' );
		
		THWCFE_i18n::wpml_register_string('Field Title - '.'Postcode', 'Postcode' );
		THWCFE_i18n::wpml_register_string('Field Title - '.'ZIP', 'ZIP' );
	}
	
	public function get_section_form_props(){
		$positions = $this->get_available_positions();
		$html_text_tags = $this->get_label_types();

		$suffix_types = array(
			'number' => 'Number',
			'alphabet' => 'Alphabet',
			'none' => 'None',
		);

		$suffix_types_1 = array(
			'number' => 'Number',
			'alphabet' => 'Alphabet',
		);
		
		return array(
			'name' 		 => array('name'=>'name', 'label'=>'Name/ID', 'type'=>'text', 'required'=>1),
			'position' 	 => array('name'=>'position', 'label'=>'Display Position', 'type'=>'select', 'options'=>$positions, 'required'=>1),
			//'box_type' 	 => array('name'=>'box_type', 'label'=>'Box Type', 'type'=>'select', 'options'=>$box_types),
			'cssclass' 	 => array('name'=>'cssclass', 'label'=>'CSS Class', 'type'=>'text'),
			'show_title' => array('name'=>'show_title', 'label'=>'Show section title in checkout page.', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>1),
			'show_title_my_account' => array('name'=>'show_title_my_account', 'label'=>'Show section title in my account page.', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>1),
			
			'title' 		   => array('name'=>'title', 'label'=>'Title', 'type'=>'text'),
			//'title_position' => array('name'=>'title_position', 'label'=>'Title Position', 'type'=>'select', 'options'=>$title_positions),
			'title_type' 	   => array('name'=>'title_type', 'label'=>'Title Type', 'type'=>'select', 'value'=>'h3', 'options'=>$html_text_tags),
			'title_color' 	   => array('name'=>'title_color', 'label'=>'Title Color', 'type'=>'colorpicker'),
			'title_class' 	   => array('name'=>'title_class', 'label'=>'Title Class', 'type'=>'text'),
			
			'subtitle' 			  => array('name'=>'subtitle', 'label'=>'Subtitle', 'type'=>'text'),
			//'subtitle_position' => array('name'=>'subtitle_position', 'label'=>'Subtitle Position', 'type'=>'select', 'options'=>$title_positions),
			'subtitle_type' 	  => array('name'=>'subtitle_type', 'label'=>'Subtitle Type', 'type'=>'select', 'value'=>'h3', 'options'=>$html_text_tags),
			'subtitle_color' 	  => array('name'=>'subtitle_color', 'label'=>'Subtitle Color', 'type'=>'colorpicker'),
			'subtitle_class' 	  => array('name'=>'subtitle_class', 'label'=>'Subtitle Class', 'type'=>'text'),

			'rpt_name_suffix' => array('type'=>'select', 'name'=>'rpt_name_suffix', 'label'=>'Name Suffix', 'options'=>$suffix_types_1),
			'rpt_label_suffix' => array('type'=>'select', 'name'=>'rpt_label_suffix', 'label'=>'Label Suffix', 'options'=>$suffix_types),
			'rpt_incl_parent' => array('type'=>'checkbox', 'name'=>'rpt_incl_parent', 'label'=>'Start indexing from parent', 'value'=>'yes', 'checked'=>0),
		);
	}
	
	public function get_field_form_props(){
		$field_types = $this->get_field_types();
		
		$validations = array(
			'email' => 'Email',
			'phone' => 'Phone',
			'postcode' => 'Postcode',
			'state' => 'State',
			'number' => 'Number',
		);
		$custom_validators = THWCFE_Utils::get_settings('custom_validators');
		if(is_array($custom_validators)){
			foreach( $custom_validators as $vname => $validator ) {
				$validations[$vname] = $validator['label'];
			}
		}
		
		$confirm_validators = THWCFE_Utils::get_settings('confirm_validators');
		if(is_array($confirm_validators)){
			foreach( $confirm_validators as $vname => $validator ) {
				$validations[$vname] = $validator['label'];
			}
		}
		
		$price_types = array(
			'normal' => 'Fixed',
			'custom' => 'Custom',
			'percentage' => 'Percentage of Cart Contents Total',
			'percentage_subtotal' => 'Percentage of Subtotal',
			'percentage_subtotal_ex_tax' => 'Percentage of Subtotal Ex Tax',
			'dynamic' => 'Dynamic',
		);
		
		$week_days = array(
			'sun' => 'Sunday',
			'mon' => 'Monday',
			'tue' => 'Tuesday',
			'wed' => 'Wednesday',
			'thu' => 'Thursday',
			'fri' => 'Friday',
			'sat' => 'Saturday',
		);


		$html_text_tags = $this->get_label_types();
		//$title_positions = array( 'left' => 'Left of the field', 'above' => 'Above field', );
		
		$time_formats = array(
			'h:i A' => '12-hour format',
			'H:i' => '24-hour format',
		);

		$suffix_types = array(
			'number' => 'Number',
			'alphabet' => 'Alphabet',
			'none' => 'None',
		);

		$suffix_types_1 = array(
			'number' => 'Number',
			'alphabet' => 'Alphabet',
		);
		
		$hint_accept = "Specify allowed file types separated by comma (e.g. png,jpg,docx,pdf).";
		
		$hint_price = "If taxable, always enter price exclusive of tax.";
		$hint_default_date = "Specify a date in the current dateFormat, or number of days from today (e.g. +7) or a string of values and periods ('y' for years, 'm' for months, 'w' for weeks, 'd' for days, e.g. '+1m +7d'), or leave empty for today.";
		$hint_date_format = "The format for parsed and displayed dates.";
		$hint_min_date = "The minimum selectable date. Specify a date in yyyy-mm-dd format, or number of days from today (e.g. -7) or a string of values and periods ('y' for years, 'm' for months, 'w' for weeks, 'd' for days, e.g. '-1m -7d'), or leave empty for no minimum limit.";
		$hint_max_date = "The maximum selectable date. Specify a date in yyyy-mm-dd format, or number of days from today (e.g. +7) or a string of values and periods ('y' for years, 'm' for months, 'w' for weeks, 'd' for days, e.g. '+1m +7d'), or leave empty for no maximum limit.";
		$hint_year_range = "The range of years displayed in the year drop-down: either relative to today's year ('-nn:+nn' e.g. -5:+3), relative to the currently selected year ('c-nn:c+nn' e.g. c-10:c+10), absolute ('nnnn:nnnn' e.g. 2002:2012), or combinations of these formats ('nnnn:+nn' e.g. 2002:+3). Note that this option only affects what appears in the drop-down, to restrict which dates may be selected use the minDate and/or maxDate options.";
		$hint_number_of_months = "The number of months to show at once.";
		$hint_disabled_dates = "Specify dates in yyyy-mm-dd format separated by comma.";
		
		return array(
			'name' 		  => array('type'=>'text', 'name'=>'name', 'label'=>'Name', 'required'=>1),
			'type' 		  => array('type'=>'select', 'name'=>'type', 'label'=>'Field Type', 'required'=>1, 'options'=>$field_types, 
								'onchange'=>'thwcfeFieldTypeChangeListner(this)'),
			'value' 	  => array('type'=>'text', 'name'=>'value', 'label'=>'Default Value'),
			'placeholder' => array('type'=>'text', 'name'=>'placeholder', 'label'=>'Placeholder'),
			'description' => array('type'=>'text', 'name'=>'description', 'label'=>'Description'),
			'validate'    => array('type'=>'multiselect', 'name'=>'validate', 'label'=>'Validations', 'placeholder'=>'Select validations', 'options'=>$validations),
			'cssclass'    => array('type'=>'text', 'name'=>'cssclass', 'label'=>'Wrapper Class', 'placeholder'=>'Seperate classes with comma', 'value'=>'form-row-wide'),
			'input_class' => array('type'=>'text', 'name'=>'input_class', 'label'=>'Input Class', 'placeholder'=>'Seperate classes with comma'),
			
			'price'        => array('type'=>'text', 'name'=>'price', 'label'=>'Price', 'placeholder'=>'Price', 'hint_text'=>$hint_price),
			'price_unit'   => array('type'=>'text', 'name'=>'price_unit', 'label'=>'Unit', 'placeholder'=>'Unit'),
			'price_type'   => array('type'=>'select', 'name'=>'price_type', 'label'=>'Price Type', 'options'=>$price_types, 'onchange'=>'thwcfePriceTypeChangeListener(this)'),
			'taxable'      => array('type'=>'select', 'name'=>'taxable', 'label'=>'Taxable', 'options'=>array('no' => 'No', 'yes' => 'Yes')),
			'tax_class'    => array('type'=>'select', 'name'=>'tax_class', 'label'=>'Tax Class', 'options'=>THWCFE_Utils::get_product_tax_class_options()),
			
			'order_meta' => array('type'=>'checkbox', 'name'=>'order_meta', 'label'=>'Order Meta Data', 'value'=>'yes', 'checked'=>1),
			'user_meta'  => array('type'=>'checkbox', 'name'=>'user_meta', 'label'=>'User Meta Data', 'value'=>'yes', 'checked'=>0),
			
			'checked'   => array('type'=>'checkbox', 'name'=>'checked', 'label'=>'Checked by default', 'value'=>'yes', 'checked'=>1),
			'required'  => array('type'=>'checkbox', 'name'=>'required', 'label'=>'Required', 'value'=>'yes', 'checked'=>0, 'status'=>1),
			'clear' 	=> array('type'=>'checkbox', 'name'=>'clear', 'label'=>'Clear Row', 'value'=>'yes', 'checked'=>0, 'status'=>1),
			'enabled'   => array('type'=>'checkbox', 'name'=>'enabled', 'label'=>'Enabled', 'value'=>'yes', 'checked'=>1, 'status'=>1),
			
			'show_in_email' => array('type'=>'checkbox', 'name'=>'show_in_email', 'label'=>'Display in Admin Emails', 'value'=>'yes', 'checked'=>1),
			'show_in_email_customer' => array('type'=>'checkbox', 'name'=>'show_in_email_customer', 'label'=>'Display in Customer Emails', 'value'=>'yes', 'checked'=>1),
			'show_in_order' => array('type'=>'checkbox', 'name'=>'show_in_order', 'label'=>'Display in Order Detail Pages', 'value'=>'yes', 'checked'=>1),
			'show_in_thank_you_page' => array('type'=>'checkbox', 'name'=>'show_in_thank_you_page', 'label'=>'Display in Thank You Page', 'value'=>'yes', 'checked'=>1),
			'show_in_my_account_page' => array('type'=>'checkbox', 'name'=>'show_in_my_account_page', 'label'=>'Display in My Account Page', 'value'=>'yes', 'checked'=>0),
			
			'title'          => array('type'=>'text', 'name'=>'title', 'label'=>'Label'),
			'title_type'     => array('type'=>'select', 'name'=>'title_type', 'label'=>'Title Type', 'value'=>'h3', 'options'=>$html_text_tags),
			'title_color'    => array('type'=>'colorpicker', 'name'=>'title_color', 'label'=>'Title Color'),
			'title_class'    => array('type'=>'text', 'name'=>'title_class', 'label'=>'Label Class', 'placeholder'=>'Seperate classes with comma'),
			
			'subtitle'       => array('type'=>'text', 'name'=>'subtitle', 'label'=>'Subtitle'),
			'subtitle_type'  => array('type'=>'select', 'name'=>'subtitle_type', 'label'=>'Subtitle Type', 'value'=>'label', 'options'=>$html_text_tags),
			'subtitle_color' => array('type'=>'colorpicker', 'name'=>'subtitle_color', 'label'=>'Subtitle Color'),
			'subtitle_class' => array('type'=>'text', 'name'=>'subtitle_class', 'label'=>'Subtitle Class', 'placeholder'=>'Seperate classes with comma'),
			
			'minlength'   => array('type'=>'text', 'name'=>'minlength', 'label'=>'Min. Length', 'hint_text'=>'The minimum number of characters allowed'),
			'maxlength'   => array('type'=>'text', 'name'=>'maxlength', 'label'=>'Max. Length', 'hint_text'=>'The maximum number of characters allowed'),
			//'repeat_x'    => array('type'=>'text', 'name'=>'repeat_x', 'label'=>'Repeat X'),
			
			'maxsize' => array('type'=>'text', 'name'=>'maxsize', 'label'=>'Maxsize(in MB)'),
			'accept'  => array('type'=>'text', 'name'=>'accept', 'label'=>'Accepted File Types', 'placeholder'=>'eg: png,jpg,docx,pdf', 'hint_text'=>$hint_accept),

			'upload_type'      => array('type'=>'select', 'name'=>'upload_type', 'label'=>'Upload Type', 'options'=>array('single' => 'Single Upload', 'multiple' => 'Multiple Upload')),

			'autocomplete' 	=> array('type'=>'text', 'name'=>'autocomplete', 'label'=>'Autocomplete'),
			'country_field' => array('type'=>'text', 'name'=>'country_field', 'label'=>'Country Field Name'),
			'country' 		=> array('type'=>'text', 'name'=>'country', 'label'=>'Country'),
						
			'default_date' => array('type'=>'text','name'=>'default_date', 'label'=>'Default Date','placeholder'=>"Leave empty for today's date",'hint_text'=>$hint_default_date),
			'date_format'  => array('type'=>'text', 'name'=>'date_format', 'label'=>'Date Format', 'value'=>'dd/mm/yy', 'hint_text'=>$hint_date_format),
			'min_date'     => array('type'=>'text', 'name'=>'min_date', 'label'=>'Min. Date', 'placeholder'=>'The minimum selectable date', 'hint_text'=>$hint_min_date),
			'max_date'     => array('type'=>'text', 'name'=>'max_date', 'label'=>'Max. Date', 'placeholder'=>'The maximum selectable date', 'hint_text'=>$hint_max_date),
			'year_range'   => array('type'=>'text', 'name'=>'year_range', 'label'=>'Year Range', 'value'=>'-100:+1', 'hint_text'=>$hint_year_range),
			'number_of_months' => array('type'=>'text', 'name'=>'number_of_months', 'label'=>'Number Of Months', 'value'=>'1', 'hint_text'=>$hint_number_of_months),
			'disabled_days'  => array('type'=>'multiselect', 'name'=>'disabled_days', 'label'=>'Disabled Days', 'placeholder'=>'Select days to disable', 'options'=>$week_days),
			'disabled_dates' => array('type'=>'text', 'name'=>'disabled_dates', 'label'=>'Disabled Dates', 'placeholder'=>'Seperate dates with comma', 
			'hint_text'=>$hint_disabled_dates),
			
			'min_time'    => array('type'=>'text', 'name'=>'min_time', 'label'=>'Min. Time', 'value'=>'12:00am', 'sub_label'=>'ex: 12:30am'),
			'max_time'    => array('type'=>'text', 'name'=>'max_time', 'label'=>'Max. Time', 'value'=>'11:30pm', 'sub_label'=>'ex: 11:30pm'),
			'start_time'  => array('type'=>'text', 'name'=>'start_time', 'label'=>'Start Time', 'value'=>'', 'sub_label'=>'ex: 2h 30m'),
			'time_step'   => array('type'=>'text', 'name'=>'time_step', 'label'=>'Time Step', 'value'=>'30', 'sub_label'=>'In minutes, ex: 30'),
			'time_format' => array('type'=>'select', 'name'=>'time_format', 'label'=>'Time Format', 'value'=>'h:i A', 'options'=>$time_formats),
			'linked_date' => array('type'=>'text', 'name'=>'linked_date', 'label'=>'Linked Date'),

			'rpt_name_suffix' => array('type'=>'select', 'name'=>'rpt_name_suffix', 'label'=>'Name Suffix', 'options'=>$suffix_types_1),
			'rpt_label_suffix' => array('type'=>'select', 'name'=>'rpt_label_suffix', 'label'=>'Label Suffix', 'options'=>$suffix_types),
			'rpt_incl_parent' => array('type'=>'checkbox', 'name'=>'rpt_incl_parent', 'label'=>'Start indexing from parent', 'value'=>'yes', 'checked'=>0),
		);
	}
	
	public function get_field_form_props_display(){
		return array('name', 'type', 'title', 'placeholder', 'validate', 'required', 'enabled');
	}
	
	public function get_field_types(){
		return array(
			'text' => 'Text', 'hidden' => 'Hidden', 'password' => 'Password', 
			'tel' => 'Telephone', 'email' => 'Email', 'number' => 'Number',  
			'textarea' => 'Textarea', 'select' => 'Select', 'multiselect' => 'Multiselect', 
			'radio' => 'Radio', 'checkbox' => 'Checkbox', 'checkboxgroup' => 'Checkbox Group', 
			'datepicker' => 'Date Picker', 'timepicker' => 'Time Picker', 
			'file' => 'File Upload',
			'heading' => 'Heading', 'label' => 'Label'
		);
	}
	
	public function get_label_types(){
		return array('h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'p' => 'p', 'div' => 'div', 'span' => 'span', 'label' => 'label');
	}
	
	public function is_reserved_field_name( $field_name ){
		if($field_name && in_array($field_name, array(
			'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 
			'billing_country', 'billing_postcode', 'billing_phone', 'billing_email',
			'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 
			'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments'
		))){
			return true;
		}
		return false;
	}
	
	public function is_default_field_name($field_name){
		if($field_name && in_array($field_name, array(
			'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 
			'billing_country', 'billing_postcode', 'billing_phone', 'billing_email',
			'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 
			'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments'
		))){
			return true;
		}
		return false;
	}
	
	
	
	public function get_available_positions(){
		$positions = array(
			//'before_checkout_form' => 'Before checkout form',
			//'after_checkout_form' => 'After checkout form',
			'before_customer_details' => 'Before customer details',
			'after_customer_details' => 'After customer details',
			'before_checkout_billing_form' => 'Before billing form',
			'after_checkout_billing_form' => 'After billing form',
			'before_checkout_shipping_form' => 'Before shipping form',
			'after_checkout_shipping_form' => 'After shipping form',
			'before_checkout_registration_form' => 'Before checkout registration form',
			'after_checkout_registration_form' => 'After checkout registration form',		
			'before_registration_form' => 'Before registration form',
			'after_registration_form' => 'After registration form',
			'before_order_notes' => 'Before order_notes',
			'after_order_notes' => 'After order notes',
			'before_terms_and_conditions' => 'Before terms and conditions',
			'after_terms_and_conditions' => 'After terms and conditions',
			'before_submit' => 'Before submit button',
			'after_submit' => 'After submit button',
			/*
			'before_cart_contents' => 'Review Order - Before cart contents',
			'after_cart_contents' => 'Review Order - After cart contents',
			'before_order_total' => 'Review Order - Before order total',
			'after_order_total' => 'Review Order - After order total',
			'before_order_review' => 'Before order review wrapper',
			'after_order_review' => 'After order review wrapper',
			'order_review_0' => 'Before order review content',
			'order_review_99' => 'After order review content',*/
		);

		if(apply_filters('thwcfe_enable_review_order_section_positions', false)){
			$positions['before_cart_contents'] = 'Review Order - Before cart contents';
			$positions['after_cart_contents'] = 'Review Order - After cart contents';
			$positions['before_order_total'] = 'Review Order - Before order total';
			$positions['after_order_total'] = 'Review Order - After order total';
			$positions['before_order_review_heading'] = 'Before order review heading';
			$positions['before_order_review'] = 'Before order review wrapper';
			$positions['after_order_review'] = 'After order review wrapper';
			$positions['order_review_0'] = 'Before order review content';
			$positions['order_review_99'] = 'After order review content';
		}
		
		$custom_positions = apply_filters('thwcfe_custom_section_positions', array());
		if(is_array($custom_positions)){
			$positions = array_merge($positions, $custom_positions);
		}
		
		return $positions;
	}
	
	public function reset_to_default() {
		delete_option(self::OPTION_KEY_CUSTOM_SECTIONS);
		delete_option(self::OPTION_KEY_SECTION_HOOK_MAP);
		delete_option('thwepo_options_name_title_map');
		
		$this->prepare_sections_and_fields();
		
		echo '<div class="updated"><p>'. THWCFE_i18n::t('Checkout fields successfully reset') .'</p></div>';
	}
   /*-----------------------------------
	----- UTILITY FUNCTIONS - END ------
	------------------------------------*/
   
   /*-----------------------------------
	----- SECTION FUNCTIONS - START ----
	------------------------------------*/
	/* Override */
	public function output_sections() {
		$result = false;
		if(isset($_POST['s_action']) && $_POST['s_action'] == 'new')
			$result = $this->create_section();	
			
		if(isset($_POST['s_action']) && $_POST['s_action'] == 'edit')
			$result = $this->edit_section();	
			
		if(isset($_POST['s_action']) && $_POST['s_action'] == 'remove')
			$result = $this->remove_section();
			
		$current_section = $this->get_current_section();
		$sections = THWCFE_Utils::get_custom_sections();
					
		if(empty($sections)){
			return;
		}
		
		$this->sort_sections($sections);
		
		$array_keys = array_keys( $sections );
				
		echo '<ul class="thpladmin-sections">';
		$i=0;
		foreach( $sections as $name => $section ){
			if(!THWCFE_Utils_Section::is_valid_section($section)){
				continue;
			}
			$url = $this->get_admin_url($this->page_id, sanitize_title($name));	
			$rules_json = htmlspecialchars($section->get_property('conditional_rules_json'));
			$rules_json_ajax = htmlspecialchars($section->get_property('conditional_rules_ajax_json'));
			
			echo '<li><a href="'. $url .'" class="'. ($current_section == $name ? 'current' : '') .'">'. THWCFE_i18n::t($section->get_property('title')) .'</a></li>';
			if(THWCFE_Utils_Section::is_custom_section($section)){
				?>
                <li>
                	<form id="section_prop_form_<?php echo $name; ?>" method="post" action="">
                        <input type="hidden" name="f_rules[<?php echo $i; ?>]" class="f_rules" value="<?php echo $rules_json; ?>" />
                        <input type="hidden" name="f_rules_ajax[<?php echo $i; ?>]" class="f_rules_ajax" value="<?php echo $rules_json_ajax; ?>" />
                    </form>
                    <span class='s_edit_btn dashicons dashicons-edit tips' data-tip='<?php THWCFE_i18n::et('Edit Section'); ?>'  
					onclick='thwcfeOpenEditSectionForm(<?php echo THWCFE_Utils_Section::get_property_json($section); ?>)'></span>
                </li>
                <li>
					<span class="s_copy_btn dashicons dashicons-admin-page tips" data-tip="<?php THWCFE_i18n::et('Duplicate Section'); ?>" onclick='thwcfeOpenCopySectionForm(<?php echo THWCFE_Utils_Section::get_property_json($section); ?>)'></span>
				</li>
				<li>
                    <form method="post" action="">
                        <input type="hidden" name="s_action" value="remove" />
                        <input type="hidden" name="i_name" value="<?php echo $name; ?>" />
                        <span class='s_delete_btn dashicons dashicons-no tips color-red' data-tip='<?php THWCFE_i18n::et('Delete Section'); ?>' onclick='thwcfeRemoveSection(this)'></span>
					</form>
                </li>
                <?php
			}
		}
		echo '<li><a href="javascript:void(0)" onclick="thwcfeOpenNewSectionForm()" class="add_link">+ '. THWCFE_i18n::t( 'Add new section' ) .'</a></li>';
		echo '<li style="position: absolute; right: 15px; top: 15px;"><a href="https://demo-themelocation.co/custom-fields/docs/" target="_blan" class="add_link">'. THWCFE_i18n::t( 'Documentation' ) .'</a></li>';
		echo '</ul>';		
		
		if($result){
			echo $result;
		}
	}
	
	public function prepare_copy_section($section, $posted){
		$s_name_copy = isset($posted['s_name_copy']) ? $posted['s_name_copy'] : '';
		if($s_name_copy){
			$section_copy = WCFE_Checkout_Fields_Utils::get_checkout_section($s_name_copy);
			if(THWCFE_Utils_Section::is_valid_section($section_copy)){
				$field_set = $section_copy->get_property('fields');
				if(is_array($field_set) && !empty($field_set)){
					$section->set_property('fields', $field_set);
				}
			}
		}
		return $section;
	}
					
	public function create_section(){
		$section = THWCFE_Utils_Section::prepare_section_from_posted_data($_POST);
		$section = $this->prepare_copy_section($section, $_POST);
		$result = $this->update_section($section);
						
		if($result == true){			
			return '<div class="updated"><p>'. THWCFE_i18n::t('New section added successfully.') .'</p></div>';
		}else{
			return '<div class="error"><p> '. THWCFE_i18n::t('New section not added due to an error.') .'</p></div>';
		}		
	}
	
	public function edit_section(){
		$result = false;
		$section  = THWCFE_Utils_Section::prepare_section_from_posted_data($_POST, 'edit');
		if($section){
			$name 	  = $section->get_property('name');
			$position = $section->get_property('position');
			$old_position = !empty($_POST['i_position_old']) ? $_POST['i_position_old'] : '';
			
			if($old_position && $position && ($old_position != $position)){			
				$this->remove_section_from_hook($position_old, $name);
			}
			
			$result = $this->update_section($section);
		}
		if($result == true){			
			return '<div class="updated"><p>'. THWCFE_i18n::t('Section details updated successfully.') .'</p></div>';
		}else{
			return '<div class="error"><p> '. THWCFE_i18n::t('Section details not updated due to an error.') .'</p></div>';
		}		
	}
			
	public function remove_section(){
		$section_name = !empty($_POST['i_name']) ? $_POST['i_name'] : false;		
		if($section_name){	
			$result = $this->delete_section($section_name);			
										
			if ($result == true) {
				return '<div class="updated"><p>'. THWCFE_i18n::t('Section removed successfully.') .'</p></div>';
			} else {
				return '<div class="error"><p> '. THWCFE_i18n::t('Section not removed due to an error.') .'</p></div>';
			}
		}
	}
	 
	public function delete_section($section_name){
		if(isset($section_name) && !empty($section_name)){	
			$sections = $this->get_checkout_sections();
			if(is_array($sections) && isset($sections[$section_name])){
				$section   = $sections[$section_name];
				$hook_name = $section->get_property('position');
				
				$this->remove_section_from_hook($hook_name, $section_name);
				unset($sections[$section_name]);
							
				$result = $this->save_sections($sections);		
				return $result;
			}
		}
		return false;
	}
	
   /*-----------------------------------
	*----- SECTION FUNCTIONS - END -----
	*-----------------------------------*/
	
   /*-----------------------------------
	*------ SECTION FORMS - START ------
	*-----------------------------------*/
	private function output_add_section_form_pp(){		
		?>
		<div id="thwcfe_new_section_form_pp" title="Create New Section" class="thwcfe_popup_wrapper">
			<?php $this->output_popup_form_section('new'); ?>
		</div>
        <?php
	}
	
	private function output_edit_section_form_pp(){		
		?>
		<div id="thwcfe_edit_section_form_pp" title="Edit Section" class="thwcfe_popup_wrapper">
			<?php $this->output_popup_form_section('edit'); ?>
		</div>
        <?php
	}
	
	private function output_popup_form_section($form_type){
		?>
        <form method="post" id="thwcfe_<?php echo $form_type ?>_section_form" action="">
        	<input type="hidden" name="s_action" value="<?php echo $form_type ?>" />
            <div id="thwcfe-tabs-container_<?php echo $form_type ?>">
                <ul class="thpladmin-tabs-menu">
                    <li class="first current"><a class="thwcfe_tab_general_link" href="javascript:void(0)" 
                    onclick="thwcfeOpenFormTab(this, 'thwcfe-section-tab-general', '<?php echo $form_type ?>')">General Option</a></li>
                    <li><a class="thwcfe_tab_rules_link" href="javascript:void(0)" 
                    onclick="thwcfeOpenFormTab(this, 'thwcfe-section-tab-rules', '<?php echo $form_type ?>')">Conditional Display</a></li>
                    <li><a class="thwcfe_tab_rules_link" href="javascript:void(0)" 
                    onclick="thwcfeOpenFormTab(this, 'thwcfe-section-tab-repeat-rules', '<?php echo $form_type ?>')">Repeat Condition</a></li>
                </ul>
                <div id="thwcfe_section_editor_form_<?php echo $form_type ?>" class="thpladmin-tab thwcfe_popup_wrapper">
                    <div id="thwcfe-section-tab-general_<?php echo $form_type ?>" class="thpladmin-tab-content">
                    	<?php if($form_type === 'edit'){ ?>
                            <input type="hidden" name="s_name" value="" />
            				<input type="hidden" name="i_position_old" value="" />
                        <?php }else{ ?>
                            <input type="hidden" name="s_name_copy" value="" />
                        <?php } ?>
                        <input type="hidden" name="i_rules" value="" />
						<input type="hidden" name="i_rules_ajax" value="" />
						<input type="hidden" name="i_repeat_rules" value="" />
                        
                    	<table width="100%" border="0">
							<?php
                            $this->output_section_info_form();
                            $this->output_h_separator();
                            $this->output_title_form(true);
                            $this->output_h_separator();
                            //$this->output_rule_form(true);
                            ?> 
                        </table>
                    </div>
                    <div id="thwcfe-section-tab-rules_<?php echo $form_type ?>" class="thpladmin-tab-content">
                        <table class="thwcfe_section_form_tab_rules_placeholder" width="100%" style="margin-top: 10px;">
                        <?php 
                        $this->render_field_form_fragment_rules('section'); 
                        $this->render_field_form_fragment_rules_ajax('section');
                        ?>
                        </table>
                    </div>
                    <div id="thwcfe-section-tab-repeat-rules_<?php echo $form_type ?>" class="thpladmin-tab-content">
                    	<table class="thwcfe_section_form_tab_repeat_rules_placeholder" width="100%" style="margin-top: 10px;">
                    	<?php 
						$this->render_field_form_fragment_repeat('section');
						?>
                        </table>
                    </div>
                </div>
            </div>
        </form>
        <?php
	}
		
	private function output_section_info_form(){
		$available_positions = $this->get_available_positions();
		?>
        <tr>                
            <td colspan="4" class="err_msgs"></td>
        </tr>            	
        <tr>                
            <td width="15%"><?php THWCFE_i18n::et('Name/ID'); ?><abbr class="required" title="required">*</abbr></td>
            <td width="35%"><input type="text" name="i_name" style="width:250px;"/></td>
            
            <td width="15%"><?php THWCFE_i18n::et('Display Position'); ?><abbr class="required" title="required">*</abbr></td>
            <td>
                <select name="i_position" style="width:250px;">
                	<?php foreach($available_positions as $value=>$label){ ?>
                    <option value="<?php echo trim($value); ?>"><?php THWCFE_i18n::et($label); ?></option>
                	<?php } ?>
                </select>
            </td>
        </tr>  
        <tr>
            <td><?php THWCFE_i18n::et('CSS Class'); ?></td>
            <td>
                <input type="text" name="i_cssclass" style="width:250px;"/>
            </td>
            
            <td><?php THWCFE_i18n::et('Display Order'); ?></td>
            <td>
                <input type="text" name="i_order" style="width:250px;"/>
            </td>           
        </tr> 
        <?php
	}
	
	private function output_title_form($show_subtitle = false){
		$this->output_h_separator(false);
		?>
        <tr>                
            <td>&nbsp;</td>
            <td colspan="3">
                <input type="checkbox" id="a_fshow_title" name="i_show_title" value="yes" checked />
        		<label for="a_fshow_title" style="margin-right: 20px;"><?php THWCFE_i18n::et('Show section title in checkout page'); ?></label>
                <input type="checkbox" id="a_fshow_title_my_account" name="i_show_title_my_account" value="yes" checked/>
        		<label for="a_fshow_title_my_account" ><?php THWCFE_i18n::et('Show section title in my account page'); ?></label>
            </td>
        </tr> 
        <?php $this->output_h_separator(false); ?>
        <tr>                
            <td><?php THWCFE_i18n::et('Title'); ?><abbr class="required" title="required">*</abbr></td>
            <td><input type="text" name="i_title" style="width:250px;"/></td>
            
            <td><?php THWCFE_i18n::et('Title Type'); ?></td>
            <td>
            	<select name="i_title_type" value="h3" style="width:250px;">
                	<?php foreach($this->get_label_types() as $value => $label){ ?>
                    <option value="<?php echo trim($value); ?>"><?php THWCFE_i18n::et($label); ?></option>
                	<?php } ?>
                </select>
            </td>
        </tr>  
        <tr>              
            <td><?php THWCFE_i18n::et('Title Color'); ?></td>
            <td>
            	<span class="thpladmin-colorpickpreview title_preview" style=""></span>
            	<input type="text" name="i_title_color" class="thpladmin-colorpick" style="width:225px;"/>                
            </td>
            
            <td><?php THWCFE_i18n::et('Title Class'); ?></td>
            <td><input type="text" name="i_title_class" style="width:250px;"/></td>
        </tr>
        
        <?php
		if($show_subtitle){
			$this->output_h_separator(false);
		?>
        <tr>                
            <td><?php THWCFE_i18n::et('Subtitle'); ?></td>
            <td><input type="text" name="i_subtitle" style="width:250px;"/></td>
            
            <td><?php THWCFE_i18n::et('Subtitle Type'); ?></td>
            <td>
            	<select name="i_subtitle_type" value="p" style="width:250px;">
                	<?php foreach($this->get_label_types() as $value => $label){ ?>
                    <option value="<?php echo trim($value); ?>"><?php THWCFE_i18n::et($label); ?></option>
                	<?php } ?>
                </select>            
            </td>
        </tr>  
        <tr>                         
            <td><?php THWCFE_i18n::et('Subtitle Color'); ?></td>
            <td>
            	<span class="thpladmin-colorpickpreview subtitle_preview" style=""></span>
            	<input type="text" name="i_subtitle_color" class="thpladmin-colorpick" style="width:225px;"/>
            </td>
            
            <td><?php THWCFE_i18n::et('Subtitle Class'); ?></td>
            <td><input type="text" name="i_subtitle_class" style="width:250px;"/></td>
        </tr>
        <?php
		}
	}
	
	private function output_h_separator($show_line = true){
		$style = $show_line ? 'style="height:5px; border-bottom: 1px dashed #ccc;"' : 'style="height: 5px;"';
		?>
		<tr><td colspan="4" <?php echo $style; ?> ></td></tr> 
        <?php
	}
	
	private function output_rule_form(){
	
	}
   /*-----------------------------------
	*------ SECTION FORMS - END --------
	*-----------------------------------*/
	
	
	public function render_page(){
		if(isset($_POST['reset_fields']))
			echo $this->reset_to_default();	
		
		$memory_limit_current = ini_get('memory_limit');	
		$memory_limit = THWCFE_Utils::get_settings('wp_memory_limit');
		
		if(!empty($memory_limit)){
			ini_set('memory_limit', $memory_limit);
		}
			
		$this->output_tabs();
		$this->output_sections();
		$this->output_content();
		
		if(!empty($memory_limit)){
			ini_set('memory_limit', $memory_limit_current);
		}
	}
	
	
   /*---------------------------------------------
	*------ CHECKOUT FIELDS FUNCTIONS - START ----
	*---------------------------------------------*/
	private function output_actions_row($section){
		if(THWCFE_Utils_Section::is_valid_section($section)){
		?>
            <th colspan="6">
                <button type="button" class="button button-primary" onclick="thwcfeOpenNewFieldForm('<?php echo $section->get_property('name'); ?>')">
                    <?php THWCFE_i18n::et('+ Add field'); ?>
                </button>
                <button type="button" class="button" onclick="thwcfeRemoveSelectedFields()"><?php  THWCFE_i18n::et('Remove'); ?></button>
                <button type="button" class="button" onclick="thwcfeEnableSelectedFields()"><?php  THWCFE_i18n::et('Enable'); ?></button>
                <button type="button" class="button" onclick="thwcfeDisableSelectedFields()"><?php THWCFE_i18n::et('Disable'); ?></button>
            </th>
            <th colspan="5">
                <input type="submit" name="save_fields" class="button-primary" value="<?php THWCFE_i18n::et('Save changes') ?>" style="float:right" />
                <input type="submit" name="reset_fields" class="button" value="<?php THWCFE_i18n::et('Reset to default fields') ?>" style="float:right; margin-right: 5px;" 
                onclick="return confirm('Are you sure you want to reset to default fields? all your changes will be deleted.');"/>
            </th>  
    	<?php 
		}else{
		?>
			<th colspan="6">
                <button type="button" class="button" disabled ><?php THWCFE_i18n::et('+ Add field'); ?></button>
                <button type="button" class="button" disabled ><?php THWCFE_i18n::et('Remove'); ?></button>
                <button type="button" class="button" disabled ><?php THWCFE_i18n::et('Enable'); ?></button>
                <button type="button" class="button" disabled ><?php THWCFE_i18n::et('Disable'); ?></button>
            </th>
            <th colspan="5">
                <input type="submit" name="save_fields" class="button" disabled value="<?php THWCFE_i18n::et('Save changes') ?>" style="float:right" />
                <input type="submit" name="reset_fields" class="button" value="<?php THWCFE_i18n::et('Reset to default fields') ?>" style="float:right; margin-right: 5px;" 
                onclick="return confirm('Are you sure you want to reset to default fields? all your changes will be deleted.');"/>
            </th> 
		<?php
		}
	}
	
	private function output_fields_table_heading(){
		?>
		<th class="sort"></th>
		<th class="check-column"><input type="checkbox" style="margin:0px 4px -1px -1px;" onclick="thwcfeSelectAllCheckoutFields(this)"/></th>
		<th class="name"><?php THWCFE_i18n::et('Name'); ?></th>
		<th class="type"><?php THWCFE_i18n::et('Type'); ?></th>
		<th class="label"><?php THWCFE_i18n::et('Label'); ?></th>
		<th class="placeholder"><?php THWCFE_i18n::et('Placeholder'); ?></th>
		<th class="validate"><?php THWCFE_i18n::et('Validation Rules'); ?></th>
        <th class="status"><?php THWCFE_i18n::et('Required'); ?></th>
		<th class="status"><?php THWCFE_i18n::et('Enabled'); ?></th>	
        <th class="actions align-center"><?php THWCFE_i18n::et('Actions'); ?></th>	
        <?php
	}
	
	private function output_content(){
		$section_name = $this->get_current_section();
		$section = $this->get_checkout_section($section_name);
		$action = isset($_POST['f_action']) ? $_POST['f_action'] : false;
		
		if($action === 'new')
			echo $this->save_or_update_field($section, $action);	
			
		if($action === 'edit')
			echo $this->save_or_update_field($section, $action);
		
		if(isset($_POST['save_fields']))
			echo $this->save_fields($section);
			
		$section = $this->get_checkout_section($section_name);
		$ignore_fields = apply_filters('thwcfe_ignore_fields', array());
		
		?>            
        <div class="wrap woocommerce"><div class="icon32 icon32-attributes" id="icon-woocommerce"><br /></div>                
		    <form method="post" id="thwcfe_checkout_fields_form" action="">
            <table id="thwcfe_checkout_fields" class="wc_gateways widefat thpladmin_fields_table" cellspacing="0">
                <thead>
                    <tr><?php $this->output_actions_row($section); ?></tr>
                    <tr><?php $this->output_fields_table_heading(); ?></tr>						
                </thead>
                <tfoot>
                    <tr><?php $this->output_fields_table_heading(); ?></tr>
                    <tr><?php $this->output_actions_row($section); ?></tr>
                </tfoot>
                <tbody class="ui-sortable">
                <?php 
				if(THWCFE_Utils_Section::is_valid_section($section)):
				
				$i=0;												
				foreach( $section->fields as $field ) :	
					$name = $field->get_property('name');
					$type = $field->get_property('type');
					$is_enabled = $field->get_property('enabled') ? 1 : 0;
					$props_json = htmlspecialchars($this->get_property_set_json($field));
					
					$options_json = htmlspecialchars($field->get_property('options_json'));
					$rules_json = htmlspecialchars($field->get_property('conditional_rules_json'));
					$rules_json_ajax = htmlspecialchars($field->get_property('conditional_rules_ajax_json'));
					$repeat_rule_json = htmlspecialchars($field->get_property('repeat_rules'));
					
					//$disabled_actions = $is_enabled ? in_array($type, THWCFE_Utils_Field::$SPECIAL_FIELD_TYPES) : 1;
					$disable_actions = in_array($name, $ignore_fields) ? true : false;
					$disable_edit = $disable_actions || !$is_enabled ? true : false;
					$disable_copy = $disable_actions || in_array($type, THWCFE_Utils_Field::$SPECIAL_FIELD_TYPES) ? true : false;
					$disabled_cb = $disable_actions ? 'disabled' : '';
				?>
					<tr class="row_<?php echo $i; echo($is_enabled === 1 ? '' : ' thpladmin-disabled') ?>">
						<td width="1%" class="sort ui-sortable-handle">
							<input type="hidden" name="f_name[<?php echo $i; ?>]" class="f_name" value="<?php echo $name; ?>" />
							<input type="hidden" name="f_order[<?php echo $i; ?>]" class="f_order" value="<?php echo $i; ?>" />
							<input type="hidden" name="f_deleted[<?php echo $i; ?>]" class="f_deleted" value="0" />
							<input type="hidden" name="f_enabled[<?php echo $i; ?>]" class="f_enabled" value="<?php echo $is_enabled; ?>" />
							
							<input type="hidden" name="f_props[<?php echo $i; ?>]" class="f_props" value='<?php echo $props_json; ?>' />
							<input type="hidden" name="f_options[<?php echo $i; ?>]" class="f_options" value="<?php echo $options_json; ?>" />
							<input type="hidden" name="f_rules[<?php echo $i; ?>]" class="f_rules" value="<?php echo $rules_json; ?>" />
							<input type="hidden" name="f_rules_ajax[<?php echo $i; ?>]" class="f_rules_ajax" value="<?php echo $rules_json_ajax; ?>" />

							<input type="hidden" name="f_repeat_rules[<?php echo $i; ?>]" class="f_repeat_rules" value="<?php echo $repeat_rule_json; ?>" />
						</td>
						<td class="td_select"><input type="checkbox" name="select_field" <?php echo $disabled_cb; ?>/></td>
						
						<?php
						foreach( $this->field_form_props_display as $pname ){
							$property = $this->field_form_props[$pname];
						
							$pvalue = $field->get_property($pname);
							$pvalue = is_array($pvalue) ? implode(',', $pvalue) : $pvalue;
							$pvalue = esc_attr($pvalue);
							
							if($property['type'] == 'checkbox'){
								$pvalue = $pvalue ? 1 : 0;
							}
							
							if(isset($property['status']) && $property['status'] == 1){
								//$statusHtml = $pvalue == 1 ? '<span class="status-enabled tips" data-tip="'.THWCFE_i18n::t('Yes').'">'.THWCFE_i18n::t('Yes').'</span>' : '-';
								$statusHtml = $pvalue == 1 ? '<span class="dashicons dashicons-yes tips" data-tip="'.THWCFE_i18n::t('Yes').'"></span>' : '-';
								?>
								<td class="td_<?php echo $pname; ?> status"><?php echo $statusHtml; ?></td>
								<?php
							}else{
								?>
								<td class="td_<?php echo $pname; ?>"><?php echo stripslashes($pvalue); ?></td>
								<?php
							}
						}
						?>
						
						<td class="td_actions" align="center">
							<?php if($disable_edit){ ?>
								<span class="f_edit_btn dashicons dashicons-edit disabled"></span>
							<?php }else{ ?>
								<span class="f_edit_btn dashicons dashicons-edit tips" data-tip="<?php THWCFE_i18n::et('Edit Field'); ?>"  
								onclick="thwcfeOpenEditFieldForm(this, <?php echo $i; ?>)"></span>
							<?php } ?>
							
							<?php if($disable_copy){ ?>
								<span class="f_copy_btn dashicons dashicons-admin-page disabled"></span>
							<?php }else{ ?>
								<span class="f_copy_btn dashicons dashicons-admin-page tips" data-tip="<?php THWCFE_i18n::et('Duplicate Field'); ?>"  
								onclick="thwcfeOpenCopyFieldForm(this, <?php echo $i; ?>)"></span>
							<?php } ?>
						</td>
					</tr>						
                <?php $i++; endforeach; endif; ?>
                </tbody>
            </table> 
            </form>
            <?php
            $this->output_add_field_form_pp();
			$this->output_edit_field_form_pp();
			$this->output_add_section_form_pp();
			$this->output_edit_section_form_pp();
			$this->output_popup_form_field_fragments();
			?>
    	</div>
    <?php
    }
	
	public function get_property_set_json($field){
		if(THWCFE_Utils_Field::is_valid_field($field)){
			$props_set = array();
			
			foreach( $this->field_form_props as $pname => $property ){
				$pvalue = $field->get_property($pname);
				$pvalue = is_array($pvalue) ? implode(',', $pvalue) : $pvalue;
				$pvalue = esc_attr($pvalue);
				
				if($property['type'] == 'checkbox'){
					$pvalue = $pvalue ? 1 : 0;
				}
				$props_set[$pname] = $pvalue;
			}
						
			$props_set['custom'] = THWCFE_Utils_Field::is_custom_field($field) ? 1 : 0;
			$props_set['order'] = $field->get_property('order');
			$props_set['priority'] = $field->get_property('priority');
			$props_set['price_field'] = $field->get_property('price_field') ? 1 : 0;
			$props_set['rules_action'] = $field->get_property('rules_action');
			$props_set['rules_action_ajax'] = $field->get_property('rules_action_ajax');
						
			return json_encode($props_set);
		}else{
			return '';
		}
	}
	
	private function save_or_update_field($section, $action) {
		try {
			$field = THWCFE_Utils_Field::prepare_field_from_posted_data($_POST, $this->field_form_props);
					
			if($action === 'edit'){
				$section = THWCFE_Utils_Section::update_field($section, $field);
			}else{
				$section = THWCFE_Utils_Section::add_field($section, $field);
			}
			
			$result = $this->update_section($section);
			
			if($result == true) {
				echo '<div class="updated"><p>'. THWCFE_i18n::t('Your changes were saved.') .'</p></div>';
				do_action('thwcfe-checkout-fields-updated');
			}else {
				echo '<div class="error"><p>'. THWCFE_i18n::t('Your changes were not saved due to an error (or you made none!).') .'</p></div>';
			}
		} catch (Exception $e) {
			echo '<div class="error"><p>'. THWCFE_i18n::t('Your changes were not saved due to an error.') .'</p></div>';
			//echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
	
	private function save_fields($section) {
		try {
			$f_names = !empty( $_POST['f_name'] ) ? $_POST['f_name'] : array();	
			if(empty($f_names)){
				echo '<div class="error"><p> '. THWCFE_i18n::t('Your changes were not saved due to no fields found.') .'</p></div>';
				return;
			}
			
			$f_order   = !empty( $_POST['f_order'] ) ? $_POST['f_order'] : array();	
			$f_deleted = !empty( $_POST['f_deleted'] ) ? $_POST['f_deleted'] : array();
			$f_enabled = !empty( $_POST['f_enabled'] ) ? $_POST['f_enabled'] : array();
						
			$sname = $section->get_property('name');
			$field_set = THWCFE_Utils_Section::get_fields($section);
			
			$max = max( array_map( 'absint', array_keys( $f_names ) ) );
			for($i = 0; $i <= $max; $i++) {
				$name = $f_names[$i];
				
				if(isset($field_set[$name])){
					if(isset($f_deleted[$i]) && $f_deleted[$i] == 1){
						unset($field_set[$name]);
						continue;
					}
					
					$field = $field_set[$name];
					$field->set_property('order', isset($f_order[$i]) ? trim(stripslashes($f_order[$i])) : 0);
					$field->set_property('enabled', isset($f_enabled[$i]) ? trim(stripslashes($f_enabled[$i])) : 0);
					
					$field_set[$name] = $field;
				}
			}
			$section->set_property('fields', $field_set);
			$section = THWCFE_Utils_Section::sort_fields($section);
			
			$result1 = $this->update_section($section);
			//$result2 = $this->update_options_name_title_map();
			
			if ($result1 == true) {
				echo '<div class="updated"><p>'. THWCFE_i18n::t('Your changes were saved.') .'</p></div>';
				do_action('thwcfe-checkout-fields-updated');
			} else {
				echo '<div class="error"><p>'. THWCFE_i18n::t('Your changes were not saved due to an error (or you made none!).') .'</p></div>';
			}
			
		} catch (Exception $e) {
			echo '<div class="error"><p>'. THWCFE_i18n::t('Your changes were not saved due to an error.') .'</p></div>';
		}
	}
	
	private function output_add_field_form_pp(){
		?>
        <div id="thwcfe_new_field_form_pp" title="New Checkout Field" class="thpladmin-popup-wrapper">
          <?php $this->output_popup_form_fields('new'); ?>
        </div>
        <?php
	}
		
	private function output_edit_field_form_pp(){		
		?>
        <div id="thwcfe_edit_field_form_pp" title="Edit Checkout Field" class="thpladmin-popup-wrapper">
          <?php $this->output_popup_form_fields('edit'); ?>
        </div>
        <?php
	}
   /*---------------------------------------------
	*------ CHECKOUT FIELDS FUNCTIONS - END ------
	*---------------------------------------------*/
			
	
   /*******************************************************************************
	******** DISPLAY CUSTOM FIELDS & VALUES in ADMIN ORDER DETAILS PAGE - START ***
	*******************************************************************************/	
	public function woo_admin_order_data_after_order_details($order){	
		$fields = array();
		
		$sections = WCFE_Checkout_Fields_Utils::get_checkout_sections();	
		$sections = THWCFE_Utils::sort_sections($sections);

		$order_id = THWCFE_Utils::get_order_id($order);
		$rsnames = THWCFE_Utils_Repeat::get_repeat_section_names($order_id);

		foreach($sections as $sname => $section){	
			if($sname != 'billing' && $sname != 'shipping' && $sname != 'additional'){
				/*$temp_fields = THWCFE_Utils_Section::get_fields($section);
				if($temp_fields && is_array($temp_fields)){
					$fields = array_merge($fields, $temp_fields);
				}*/
				$this->display_section_in_admin_order($order, $order_id, $sname, $section, '<p>&nbsp;</p>');
				$this->may_display_repeat_sections_in_admin_order($order, $order_id, $sname, $section, $rsnames);
			}			
		}
		
		//$this->display_fields_in_admin_order($order, $fields, '', '<p>&nbsp;</p>');
	}
	
	public function woo_admin_order_data_after_billing_address($order){	
		$section = WCFE_Checkout_Fields_Utils::get_checkout_section('billing');
		if(THWCFE_Utils_Section::is_valid_section($section)){
			$fields = THWCFE_Utils_Section::get_fields($section);
			$html = $this->display_fields_in_admin_order($order, $fields, '');
			
			if($html){
				echo $html;
			}
		}
	}
	
	public function woo_admin_order_data_after_shipping_address($order){	
		$section = WCFE_Checkout_Fields_Utils::get_checkout_section('shipping');
		if(THWCFE_Utils_Section::is_valid_section($section)){
			$fields = THWCFE_Utils_Section::get_fields($section);
			$html = $this->display_fields_in_admin_order($order, $fields, '');

			if($html){
				echo $html;
			}
		}
	}

	public function display_section_in_admin_order($order, $order_id, $key, $section, $html_prefix=''){
		$fields = THWCFE_Utils_Section::get_fields($section);
		if(is_array($fields)){
			$html = $this->display_fields_in_admin_order($order, $fields, '');

			if($html){
				$show_section_title = THWCFE_Utils_Section::is_show_section_title($section, 'admin_order');

				if($show_section_title){
					$title_html = $this->display_section_title_in_admin_order($section);
					$html = $title_html.$html;
				}else{
					$html = '<p>&nbsp;</p>'.$html;
				}

				echo $html_prefix.$html;
			}
		}
	}

	public function may_display_repeat_sections_in_admin_order($order, $order_id, $key, $section, $rsnames){
		$rsections = THWCFE_Utils_Repeat::get_repeat_sections($order_id, $key, $section, $rsnames);
		if(is_array($rsections)){
			foreach($rsections as $rskey => $rsection) {
				$this->display_section_in_admin_order($order, $order_id, $rskey, $rsection);
			}
		}
	}

	public function display_section_title_in_admin_order($section){
		$html = '';
		$title = $section->get_property('title');
					
		if($title){
			$title = THWCFE_i18n::t($title);
			$subtitle = $section->get_property('subtitle') ? $section->get_property('subtitle') : false;
			$subtitle = $subtitle ? THWCFE_i18n::t($subtitle) : '';

			if($subtitle){
				$title .= '<br/><span style="font-size:80%">'.$subtitle.'</span>';
			}

			$html .= '<h3>'. $title .'</h3>';
		}
		return $html;
	}

	public function display_single_field_in_admin_order($order_id, $key, $field, $field_name_prefix, $is_nl2br=true, $esc_attr_label=false){
		$html = '';
		$type = $field->get_property('type');
					
		if($type === 'label' || $type === 'heading'){
			$label = $field->get_property('title') ? $field->get_property('title') : false;
			$subtitle = $field->get_property('subtitle') ? $field->get_property('subtitle') : false;

			if($label || $subtitle){
				if($esc_attr_label){
					$label = $label ? THWCFE_i18n::esc_attr__t($label) : '';
					$subtitle = $subtitle ? THWCFE_i18n::esc_attr__t($subtitle) : '';
				}else{
					$label = $label ? THWCFE_i18n::t($label) : '';
					$subtitle = $subtitle ? THWCFE_i18n::t($subtitle) : '';
				}
				
				if($subtitle){
					$label .= '<br/><span style="font-size:80%">'.$subtitle.'</span>';
				}
				
				$found = true;
				if($type === 'heading'){
					$html .= '<h3>'. $label .'</h3>';
				}else{
					$html .= '<p><strong>'. $label .'</strong></p>';
				}
			}
		}else{
			$value = get_post_meta( $order_id, $field_name_prefix.$key, true );
			if(!empty($value)){
				if($type === 'file'){
					$value = WCFE_Checkout_Fields_Utils::get_file_display_name_order($value, apply_filters('thwcfe_clickable_filename_in_order_admin_view', true, $key));
				}else{
					$value = $this->get_option_text_from_value($field, $value);
					$value = is_array($value) ? implode(",", $value) : $value;
				}

				$label = $field->get_property('title') ? $field->get_property('title') : $key;
				if($esc_attr_label){
					$label = THWCFE_i18n::esc_attr__t($label);
				}else{
					$label = THWCFE_i18n::t($label);
				}
				
				if($is_nl2br && $type === 'textarea'){
					$value = nl2br($value);
				}else if($type !== 'file'){
					$value = esc_html($value);
				}
				
				$found = true;
				//$html .= '<p><strong>'. $label .':</strong><br/> '. $value .'</p>';
				$html .= '<p><strong>'. $label .':</strong> '. $value .'</p>';							
			}
		}
		return $html;
	}

	public function may_display_repeat_fields_in_admin_order($order_id, $key, $field, $rfnames, $field_name_prefix, $is_nl2br=true, $esc_attr_label=false){
		$html = '';

		$rfields = THWCFE_Utils_Repeat::get_repeat_fields($order_id, $key, $field, $rfnames);
		if(is_array($rfields)){
			foreach($rfields as $rkey => $rfield) {
				$html .= $this->display_single_field_in_admin_order($order_id, $rkey, $rfield, $field_name_prefix, $is_nl2br, $esc_attr_label);
			}
		}

		return $html;
	}
	
	public function display_fields_in_admin_order($order, $fields, $field_name_prefix = ''){
		$html = '';

		if($fields){			
			$is_nl2br = apply_filters('thwcfe_nl2br_custom_field_value', true);
			$esc_attr_label = apply_filters('thwcfe_esc_attr_custom_field_label_admin_order', false);
			
			$order_id = THWCFE_Utils::get_order_id($order);
			$dis_fields = WCFE_Checkout_Fields_Utils::get_disabled_fields($order_id);
			$rfnames = THWCFE_Utils_Repeat::get_repeat_field_names($order_id);
		
			foreach($fields as $name => $field){	
				if(THWCFE_Utils_Field::is_valid_field($field) && THWCFE_Utils_Field::is_custom_field($field) && 
						THWCFE_Utils_Field::is_enabled($field) && $field->get_property('show_in_order')){	
					
					if(!in_array($name, $dis_fields)){
						$html .= $this->display_single_field_in_admin_order($order_id, $name, $field, $field_name_prefix, $is_nl2br, $esc_attr_label);
					}
					$html .= $this->may_display_repeat_fields_in_admin_order($order_id, $name, $field, $rfnames, $field_name_prefix, $is_nl2br, $esc_attr_label);				
				}
			}
		}
		return $html;
	} 
	
	public function woo_customer_meta_fields($fields){
		$sections = $this->get_checkout_sections();
		if($sections && is_array($sections)){
			foreach($sections as $sname => $section) {
				$fieldset = THWCFE_Utils_Section::get_fields($section);
					
				if($fieldset && is_array($fieldset) && !empty($fieldset)){
					if($sname === 'billing' || $sname === 'shipping'){
						foreach($fieldset as $key => $field) {
							if(THWCFE_Utils_Field::is_custom_field($field) && $field->get_property('user_meta')){	
								$fields[$sname]['fields'][$key] = array(
									'label'       => THWCFE_i18n::t($field->get_property('title')),
									'description' => THWCFE_i18n::t($field->get_property('description')),
									'type'        => $field->get_property('type'),
									'class'       => '',
									'options'     => THWCFE_Utils_Field::get_option_array($field)
								);
							}
						}
					}else{
						$cfields = array();
						
						foreach($fieldset as $key => $field) {
							if(THWCFE_Utils_Field::is_custom_field($field) && $field->get_property('user_meta')){	
								$cfields[$key] = array(
									'label'       => THWCFE_i18n::t($field->get_property('title')),
									'description' => THWCFE_i18n::t($field->get_property('description')),
									'type'        => $field->get_property('type'),
									'class'       => '',
									'options'     => THWCFE_Utils_Field::get_option_array($field)
								);
							}
						}
						
						if(!empty($cfields)){
							$fields[$sname]['title'] = THWCFE_i18n::t($section->get_property('title'));
							$fields[$sname]['fields'] = $cfields;
						}
					}
				}
			}
		}
		
		return $fields;
	}
	
   /*******************************************************************************
	******** DISPLAY CUSTOM FIELDS & VALUES in ADMIN ORDER DETAILS PAGE - END *****
	*******************************************************************************/
	
	
   /*******************************************************************************
	******** DISPLAY CUSTOM FIELDS & VALUES in ADMIN ORDER LIST TABLE - START *****
	*******************************************************************************/
	
	public function get_custom_shop_order_columns(){
		$custom_columns_str = $this->get_settings('custom_shop_order_columns');
		$custom_columns = array();
		
		if(!empty($custom_columns_str)){
			$col_arr = explode(",", $custom_columns_str);
			
			if($col_arr){
				foreach($col_arr as $col_str){
					$col = explode(":", $col_str);
					
					if(is_array($col) && !empty($col)){
						$name = isset($col[0]) ? $col[0] : false;
						if($name){
							$title = isset($col[1]) ? $col[1] : $name;
							$custom_columns[$name] = $title;
						}
					}
				}
			}
		}
		
		return is_array($custom_columns) ? $custom_columns : array();
	}
	
	public function manage_edit_shop_order_columns($columns){
		$custom_columns = $this->get_custom_shop_order_columns();

		if(!empty($custom_columns)){
			$new_columns = (is_array($columns)) ? $columns : array();
			if(isset($new_columns['order_actions'])){
				unset($new_columns['order_actions']);
			}
			
			foreach($custom_columns as $name => $title){
				$new_columns[$name] = $title;
			}
			
			if(isset($columns['order_actions'])){
				$new_columns['order_actions'] = $columns['order_actions'];
			}		
			return $new_columns;
		}
		return $columns;
	}
	
	public function manage_shop_order_posts_custom_column($column){
		$custom_columns = $this->get_custom_shop_order_columns();
		
		if(!empty($custom_columns)){
			global $post;
			$data = get_post_meta( $post->ID );
			
			if(array_key_exists($column, $custom_columns)){
				echo (isset($data[$column]) ? $data[$column][0] : '');
			}
		}
	}
	
	public function manage_edit_shop_order_sortable_columns( $columns ) {
		$custom_columns = $this->get_custom_shop_order_columns();
		$custom = array();
		
		if(!empty($custom_columns)){
			foreach($custom_columns as $name => $title){
				//$custom[$name] = $name.'_POST_META_ID';
				$custom[$name] = $name;
			}
		}

		return wp_parse_args( $custom, $columns );
	}
	
	public function pre_get_posts($query) { 
		//if ($query->is_post_type_archive('shop_order') && $query->is_main_query()) {
		if($query->query['post_type']  == 'shop_order' && $query->is_main_query() && isset($query->query['orderby'])) {	
			$custom_columns = $this->get_custom_shop_order_columns();
			$orderby = $query->query['orderby'];
	
			if(!empty($custom_columns) && array_key_exists($orderby, $custom_columns)){
				//$query->set('meta_key', $orderby);
				//$query->set('orderby', 'meta_value');
			}
		}
		//return $query;
	}

	public function posts_clauses_sort_shop_orders($pieces, $query) {
		global $wpdb;

		if(isset($query->query['post_type']) && $query->query['post_type'] == 'shop_order' && $query->is_main_query() && isset($query->query['orderby'])) {	
			$custom_columns = $this->get_custom_shop_order_columns();
			$orderby = $query->query['orderby'];
	
			if(!empty($custom_columns) && array_key_exists($orderby, $custom_columns)){
				$fieldset = self::get_all_checkout_fields();
				$cfield = is_array($fieldset) && isset($fieldset[$orderby]) ? $fieldset[$orderby] : false;

				if($cfield){
					$orderby_str = 'wp_rd.meta_value';

					if($cfield->get_property('type') === 'datepicker'){
						$date_format = $cfield->get_property('date_format');
						if($date_format){
							$date_format = str_replace("dd", "%d", $date_format);
							$date_format = str_replace("mm", "%m", $date_format);
							$date_format = str_replace("yy", "%Y", $date_format);
						}else{
							$date_format = '%d/%m/%Y';
						}

						$orderby_str = "STR_TO_DATE( wp_rd.meta_value,'".$date_format."' )";
					}

					$order = strtoupper($query->get('order'));
			    	$order = in_array($order, array('ASC', 'DESC')) ? $order : 'ASC';

					$pieces['join'] .= " LEFT JOIN $wpdb->postmeta wp_rd ON wp_rd.post_id = {$wpdb->posts}.ID AND wp_rd.meta_key = '".$orderby."'";
					
		            $pieces['orderby'] = $orderby_str." $order, ". $pieces['orderby'];
				}
			}
		}
		return $pieces;
	}
	
	
   /*******************************************************************************
	******** DISPLAY CUSTOM FIELDS & VALUES in ADMIN ORDER LIST TABLE - START *****
	*******************************************************************************/
	 
	 
   /*------------------------------------------
	*-------- HTML FORM FRAGMENTS - START -----
	*------------------------------------------*/
	private function output_popup_form_fields($form_type){
		?>
		<form method="post" id="thwcfe_<?php echo $form_type ?>_field_form" action="">
          	<input type="hidden" name="f_action" value="<?php echo $form_type ?>" />
        	<div id="thwcfe-tabs-container_<?php echo $form_type ?>">
                <ul class="thpladmin-tabs-menu">
                    <li class="first current"><a class="thwcfe_tab_general_link" href="javascript:void(0)" 
                    onclick="thwcfeOpenFormTab(this, 'thwcfe-tab-general', '<?php echo $form_type ?>')">General Option</a></li>
                    <li><a class="thwcfe_tab_rules_link" href="javascript:void(0)" 
                    onclick="thwcfeOpenFormTab(this, 'thwcfe-tab-display-rules', '<?php echo $form_type ?>')">Conditional Display</a></li>
                    <li><a class="thwcfe_tab_rules_link" href="javascript:void(0)" 
                    onclick="thwcfeOpenFormTab(this, 'thwcfe-tab-repeat-rules', '<?php echo $form_type ?>')">Repeat Condition</a></li>
                </ul>
                <div id="thwcfe_field_editor_form_<?php echo $form_type ?>" class="thpladmin-tab thwcfe_popup_wrapper">
                    <div id="thwcfe-tab-general_<?php echo $form_type ?>" class="thpladmin-tab-content">
						<input type="hidden" name="i_name_old" value="" />
						<input type="hidden" name="i_order" value="" />
						<input type="hidden" name="i_priority" value="" />
                        <input type="hidden" name="i_options" value="" />
						<input type="hidden" name="i_rules" value="" />
						<input type="hidden" name="i_rules_ajax" value="" />
						<input type="hidden" name="i_repeat_rules" value="" />
						<input type="hidden" name="i_country_field" value="" />
						<input type="hidden" name="i_country" value="" />
						<input type="hidden" name="i_autocomplete" value="" />
						
						<?php $this->render_field_form_fragment_general($form_type); ?>
                        <table class="thwcfe_field_form_tab_general_placeholder" width="100%"></table>
                    </div>
                    <div id="thwcfe-tab-display-rules_<?php echo $form_type ?>" class="thpladmin-tab-content">
                    	<table class="thwcfe_field_form_tab_rules_placeholder" width="100%" style="margin-top: 10px;">
                    	<?php 
						$this->render_field_form_fragment_rules(); 
						$this->render_field_form_fragment_rules_ajax();
						?>
                        </table>
                    </div>
                    <div id="thwcfe-tab-repeat-rules_<?php echo $form_type ?>" class="thpladmin-tab-content">
                    	<table class="thwcfe_field_form_tab_repeat_rules_placeholder" width="100%" style="margin-top: 10px;">
                    	<?php 
						$this->render_field_form_fragment_repeat(); 
						?>
                        </table>
                    </div>
                </div>
        	</div>
        </form>
        <?php
	}	
	
	private function output_popup_form_field_fragments(){
		$this->render_form_field_inputtext();
		$this->render_form_field_hidden();
		$this->render_form_field_password();
		$this->render_form_field_tel();
		$this->render_form_field_email();
		$this->render_form_field_number();		
		$this->render_form_field_textarea();
		$this->render_form_field_select();
		$this->render_form_field_multiselect();		
		$this->render_form_field_radio();
		$this->render_form_field_checkbox();
		$this->render_form_field_checkboxgroup();
		$this->render_form_field_datepicker();
		$this->render_form_field_timepicker();	
		$this->render_form_field_file();
		//$this->render_form_field_country();
		//$this->render_form_field_state();	
		$this->render_form_field_heading();
		$this->render_form_field_label();
		$this->render_form_field_default();
		
		$this->render_field_form_fragment_product_list();
		$this->render_field_form_fragment_category_list();
		$this->render_field_form_fragment_tag_list();
		$this->render_field_form_fragment_user_role_list();
		$this->render_field_form_fragment_fields_wrapper();
	}
	
	private function render_form_field_inputtext(){
		?>
        <table id="thwcfe_field_form_id_text" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['maxlength'], $this->cell_props_L);
				$this->render_form_element_empty_cell();
            	//$this->render_form_field_element($this->field_form_props['repeat_x'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('inputtext');
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>     
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}
	
	private function render_form_field_hidden(){
		$field = $this->field_form_props['title'];
		$field['placeholder'] = 'For order details page & email';
		?>
        <table id="thwcfe_field_form_id_hidden" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
				$this->render_form_field_element($field, $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_R);
				?>
            </tr>
			<tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_L);
            	$this->render_form_element_empty_cell();
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('hidden');
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
        </table>
        <?php   
	}
	
	private function render_form_field_password(){
		?>
        <table id="thwcfe_field_form_id_password" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['maxlength'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('password');
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>    
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
        </table>
        <?php   
	}

	private function render_form_field_tel(){
		?>
        <table id="thwcfe_field_form_id_tel" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['maxlength'], $this->cell_props_L);
				$this->render_form_element_empty_cell();
            	//$this->render_form_field_element($this->field_form_props['repeat_x'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('inputtext');
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>     
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}

	private function render_form_field_email(){
		?>
        <table id="thwcfe_field_form_id_email" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['maxlength'], $this->cell_props_L);
				$this->render_form_element_empty_cell();
            	//$this->render_form_field_element($this->field_form_props['repeat_x'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('inputtext');
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>     
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}

	private function render_form_field_number(){
		?>
        <table id="thwcfe_field_form_id_number" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['maxlength'], $this->cell_props_L);
				$this->render_form_element_empty_cell();
            	//$this->render_form_field_element($this->field_form_props['repeat_x'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('inputtext');
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>     
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}
	
	private function render_form_field_textarea(){
		$value_field_props = $this->field_form_props['value'];
		$value_field_props['type'] = 'textarea';
		?>
        <table id="thwcfe_field_form_id_textarea" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($value_field_props, $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['maxlength'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('textarea');
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}
	
	private function render_form_field_select(){
		?>
        <table id="thwcfe_field_form_id_select" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
			<tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <?php $this->render_form_element_h_spacing(); ?>
            <?php $this->render_field_form_fragment_options(); ?>
            <?php $this->render_form_element_h_spacing(); ?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
        </table>
        <?php   
	}
	
	private function render_form_field_multiselect(){
		$field_props_maxlength = $this->field_form_props['maxlength'];
		$field_props_maxlength['label'] = 'Max. Selections';
		$field_props_maxlength['hint_text'] = 'The maximum number of options that can be selected';
		?>
        <table id="thwcfe_field_form_id_multiselect" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($field_props_maxlength, $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <?php $this->render_form_element_h_spacing(); ?>
            <?php $this->render_field_form_fragment_options(); ?>
            <?php $this->render_form_element_h_spacing(); ?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>    
        </table>
        <?php   
	}
	
	private function render_form_field_radio(){
		?>
        <table id="thwcfe_field_form_id_radio" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
			<tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_L);
            	$this->render_form_element_empty_cell();
				?>
            </tr>
            <?php $this->render_form_element_h_spacing(); ?>
            <?php $this->render_field_form_fragment_options(); ?>
            <?php $this->render_form_element_h_spacing(); ?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
        </table>
        <?php   
	}
	
	private function render_form_field_checkbox(){
		$field_value_props = $this->field_form_props['value'];
		$field_value_props['label'] = THWCFE_i18n::t('Value');
		
		?>
        <table id="thwcfe_field_form_id_checkbox" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
            	$this->render_form_field_element($field_value_props, $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
			<tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_L);
            	$this->render_form_element_empty_cell();
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('checkbox');
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['checked'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>    
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
        </table>
        <?php   
	}
	
	private function render_form_field_checkboxgroup(){
		$field_value_props = $this->field_form_props['value'];
		$field_value_props['label'] = THWCFE_i18n::t('Default Values');
		
		?>
        <table id="thwcfe_field_form_id_checkboxgroup" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
            	$this->render_form_field_element($field_value_props, $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <?php $this->render_form_element_h_spacing(); ?>
            <?php $this->render_field_form_fragment_options(); ?>
            <?php $this->render_form_element_h_spacing(); ?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
        </table>
        <?php   
	}
	
	private function render_form_field_datepicker(){
		?>
        <table id="thwcfe_field_form_id_datepicker" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('datepicker');
			?>
            <?php 
				$this->render_form_element_h_separator();
				$this->render_form_element_h_spacing(); 
				$this->render_field_form_fragment_datepicker();
				$this->render_form_element_h_separator();
				$this->render_form_element_h_spacing(); 
			?>  
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>      
        </table>
        <?php   
	}
	
	private function render_form_field_timepicker(){
		?>
        <table id="thwcfe_field_form_id_timepicker" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['linked_date'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				 $this->render_field_form_fragment_price('timepicker');
			?>
            <?php 
				$this->render_form_element_h_separator();
				$this->render_form_element_h_spacing(); 
				$this->render_field_form_fragment_timepicker();
				$this->render_form_element_h_separator();
				$this->render_form_element_h_spacing(); 
			?>   
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}
	
	private function render_form_field_file(){
		?>
        <table id="thwcfe_field_form_id_file" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['maxsize'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['accept'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['upload_type'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price('file');
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>     
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
        </table>
        <?php   
	}

	private function render_form_field_country(){
		?>
        <table id="thwcfe_field_form_id_country" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
        </table>
        <?php   
	}

	private function render_form_field_state(){
		?>
        <table id="thwcfe_field_form_id_state" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['country_field'], $this->cell_props_L);
            	$this->render_form_element_empty_cell();
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
        </table>
        <?php   
	}
	
	private function render_form_field_heading(){
		?>
        <table id="thwcfe_field_form_id_heading" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_element_empty_cell();
				?>
            </tr>
            <?php 
				$this->render_form_element_h_spacing(); 
				$this->render_field_form_fragment_title(true);
			?>   
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$show_in_order = $this->field_form_props['show_in_order'];
				$show_in_thank_you_page = $this->field_form_props['show_in_thank_you_page'];
				$show_in_order['checked'] = 0;
				$show_in_thank_you_page['checked'] = 0;
				
            	$this->render_form_field_element($show_in_order, $this->cell_props_CBS, false);
				$this->render_form_field_element($show_in_thank_you_page, $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
				<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_my_account_page'], $this->cell_props_CBS, false);
				?>
				</td>
            </tr>
        </table>
        <?php   
	}
	
	private function render_form_field_label(){
		?>
        <table id="thwcfe_field_form_id_label" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_element_empty_cell();
				?>
            </tr>
            <?php 
				$this->render_form_element_h_spacing();  
				$this->render_field_form_fragment_title(true);
			?>    
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$show_in_email = $this->field_form_props['show_in_email'];
				$show_in_email_customer = $this->field_form_props['show_in_email_customer'];
				$show_in_email['checked'] = 0;
				$show_in_email_customer['checked'] = 0;
				
            	$this->render_form_field_element($show_in_email, $this->cell_props_CBL, false);
				$this->render_form_field_element($show_in_email_customer, $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
				$show_in_order = $this->field_form_props['show_in_order'];
				$show_in_thank_you_page = $this->field_form_props['show_in_thank_you_page'];
				$show_in_order['checked'] = 0;
				$show_in_thank_you_page['checked'] = 0;
				
            	$this->render_form_field_element($show_in_order, $this->cell_props_CBS, false);
				$this->render_form_field_element($show_in_thank_you_page, $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
				<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_my_account_page'], $this->cell_props_CBS, false);
				?>
				</td>
            </tr>
        </table>
        <?php   
	}
	
	private function render_form_field_default(){
		?>
        <table id="thwcfe_field_form_id_default" class="thpladmin_field_info_tbl" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['title'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['description'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['value'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['placeholder'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->field_form_props['input_class'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['title_class'], $this->cell_props_R);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_form_field_element($this->field_form_props['cssclass'], $this->cell_props_L);
            	$this->render_form_field_element($this->field_form_props['validate'], $this->cell_props_R);
				?>
            </tr>
            <?php 
				$this->render_form_element_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['required'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['clear'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['enabled'], $this->cell_props_CB, false);
				?>
                </td>
            </tr> 
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_email'], $this->cell_props_CBL, false);
				$this->render_form_field_element($this->field_form_props['show_in_email_customer'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>
			<tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['show_in_order'], $this->cell_props_CBS, false);
				$this->render_form_field_element($this->field_form_props['show_in_thank_you_page'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>   
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_form_field_element($this->field_form_props['order_meta'], $this->cell_props_CB, false);
				$this->render_form_field_element($this->field_form_props['user_meta'], $this->cell_props_CB, false);
				?>
                </td>
            </tr>  
        </table>
        <?php   
	}
	
	private function render_field_form_fragment_general($form_type, $input_field = true){
		//$field_name_label = $input_field ? THWCFE_i18n::t('Name') : THWCFE_i18n::t('ID');
		?>
        <table width="100%">
            <tr>                
                <td colspan="6" class="err_msgs"></td>
            </tr> 
            
            <?php if($form_type === 'edit'){ ?> 
            <tr>
            	<td colspan="6">
                    <input type="hidden" name="i_rowid" value="" />
                    <input type="hidden" name="i_original_type" value="" />
                </td>
            </tr>    
            <?php } ?>  
                	         
            <tr>  
            <?php 
				$this->render_form_field_element($this->field_form_props['name'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['type'], $this->cell_props_R); 
			?>         
            </tr>  
		</table> 
		<?php 
	}

    private function render_field_form_fragment_options(){
		?>
		<tr>  
		<?php
			$this->render_form_field_element($this->field_form_props['taxable'], $this->cell_props_L);
			$this->render_form_field_element($this->field_form_props['tax_class'], $this->cell_props_R);
		?>
        </tr>
		<tr>
			<td width="13%" valign="top"><?php THWCFE_i18n::et('Options'); ?></td>
			<?php $this->render_form_element_tooltip(false); ?>
			<td colspan="4">
				<table border="0" cellpadding="0" cellspacing="0" class="thwcfe-option-list thpladmin-dynamic-row-table"><tbody>
					<tr>
						<td style="width:190px;"><input type="text" name="i_options_key[]" placeholder="Option Value" style="width:180px;"/></td>
						<td style="width:190px;"><input type="text" name="i_options_text[]" placeholder="Option Text" style="width:180px;"/></td>
						<td style="width:80px;"><input type="text" name="i_options_price[]" placeholder="Price" style="width:70px;"/></td>
						<td style="width:130px;">    
							<select name="i_options_price_type[]" style="width:120px;">
								<option selected="selected" value="">Normal</option>
								<option value="percentage">Percentage of Cart Contents Total</option>
								<option value="percentage_subtotal">Percentage of Subtotal</option>
								<option value="percentage_subtotal_ex_tax">Percentage of Subtotal Ex Tax</option>
							</select>
						</td>
						<td class="action-cell"><a href="javascript:void(0)" onclick="thwcfeAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>
						<td class="action-cell"><a href="javascript:void(0)" onclick="thwcfeRemoveOptionRow(this)" class="btn btn-red" title="Remove option">x</a></td>
						<td class="action-cell sort ui-sortable-handle"></td>
					</tr>
				</tbody></table>            	
			</td>
		</tr>
        <?php
	}
	
	private function render_field_form_fragment_title($show_subtitle = false){
		?>
        <tr>                
        <?php
			$title_props = $this->field_form_props['title'];
			if($title_props['label']){
				$title_props['label'] = 'Title';
			}
			
			$title_class_props = $this->field_form_props['title_class'];
			if($title_class_props['label']){
				$title_class_props['label'] = 'Title Class';
			}
			
			$this->render_form_field_element($title_props, $this->cell_props_L);
			$this->render_form_field_element($this->field_form_props['title_type'], $this->cell_props_R);
		?>
        </tr>  
        <tr>
        <?php	
			$this->render_form_field_element($this->field_form_props['title_color'], $this->cell_props_CP);
			$this->render_form_field_element($title_class_props, $this->cell_props_R);
		?>
        </tr>
        <?php
		if($show_subtitle){
			$this->output_h_separator(false);
			?>
			<tr class="thwcfe_subtitle_row">              
			<?php
				$this->render_form_field_element($this->field_form_props['subtitle'], $this->cell_props_L);
				$this->render_form_field_element($this->field_form_props['subtitle_type'], $this->cell_props_R);
			?>
			</tr>
			<tr class="thwcfe_subtitle_row"> 
			<?php
				$this->render_form_field_element($this->field_form_props['subtitle_color'], $this->cell_props_CP);
				$this->render_form_field_element($this->field_form_props['subtitle_class'], $this->cell_props_R);
			?>
            </tr>
            <?php
		}
	}
	
	private function render_field_form_fragment_datepicker(){
		?>
        <tr>  
		<?php
            $this->render_form_field_element($this->field_form_props['date_format'], $this->cell_props_L);
            $this->render_form_field_element($this->field_form_props['default_date'], $this->cell_props_R);
        ?>
        </tr>
        <tr>  
		<?php
            $this->render_form_field_element($this->field_form_props['min_date'], $this->cell_props_L);
            $this->render_form_field_element($this->field_form_props['max_date'], $this->cell_props_R);
        ?>
        </tr>
        <tr>  
		<?php
            $this->render_form_field_element($this->field_form_props['year_range'], $this->cell_props_L);
            $this->render_form_field_element($this->field_form_props['number_of_months'], $this->cell_props_R);
        ?>
        </tr>
        <tr>  
		<?php
            $this->render_form_field_element($this->field_form_props['disabled_days'], $this->cell_props_L);
            $this->render_form_field_element($this->field_form_props['disabled_dates'], $this->cell_props_R);
        ?>
        </tr>
        <?php 
    }
	
	private function render_field_form_fragment_timepicker(){
		?>
        <tr>  
		<?php
            $this->render_form_field_element($this->field_form_props['min_time'], $this->cell_props_L);
            $this->render_form_field_element($this->field_form_props['max_time'], $this->cell_props_R);
        ?>
        </tr>
        <tr>  
		<?php
            $this->render_form_field_element($this->field_form_props['start_time'], $this->cell_props_L);
            $this->render_form_field_element($this->field_form_props['time_step'], $this->cell_props_R);
        ?>
        </tr>
        <tr>  
		<?php
            $this->render_form_field_element($this->field_form_props['time_format'], $this->cell_props_L);
            $this->render_form_element_empty_cell();
        ?>
        </tr>
        <?php
    }
	
	private function render_field_form_fragment_price($type = false){
		?>
        <tr>                
            <td width="13%"><?php THWCFE_i18n::et('Price'); ?></td>
            <?php 
			$price_field = $this->field_form_props['price'];
			$tooltip = ( isset($price_field['hint_text']) && !empty($price_field['hint_text']) ) ? $price_field['hint_text'] : false;
			$this->render_form_element_tooltip($tooltip);
			?>
            <td width="34%">
            	<input type="text" name="i_price" placeholder="Price" style="width:250px;" class="thpl-price-field"/>
                <label class="thpl-dynamic-price-field" style="display:none">per</label>
                <input type="text" name="i_price_unit" placeholder="Unit" style="width:80px; display:none" class="thpl-dynamic-price-field"/>
                <label class="thpl-dynamic-price-field" style="display:none">unit</label>
            </td>
		<?php 
			$field_props = $this->field_form_props['price_type'];
			$options = isset($field_props['options']) ? $field_props['options'] : array();
			
			if($type === 'datepicker' || $type === 'timepicker'){
				unset($options['custom']);
				unset($options['dynamic']);
			}
			
			$field_props['options'] = $options;
			$this->render_form_field_element($field_props, $this->cell_props_R); 
		?>
        </tr>  
		<tr>  
		<?php
			$this->render_form_field_element($this->field_form_props['taxable'], $this->cell_props_L);
			$this->render_form_field_element($this->field_form_props['tax_class'], $this->cell_props_R);
		?>
        </tr>
        <?php
	}

	private function render_field_form_fragment_repeat($type="field"){
		$form_props = $type === "field" ? $this->field_form_props : $this->section_form_props;

		?>
		<tr><td style="padding-left: 0; color: red; padding-bottom: 20px;" colspan="12">Note: For Account registration area this field repeated are not available.</td></tr>
        <tr class="thwepo_repeat_rule">
        	<td colspan="2">Repeat <?php echo $type; ?> for</td>
        	<td colspan="4">
        		<table border="0" width="100%"><tbody>
					<tr>
						<td width="25%">
				            <select name="i_repeat_operator" style="width:200px;" onchange="thwcfeRepeatOperatorChangeListner(this)">
				            	<option value="">...</option>
				                <option value="qty_product">Product Qty</option>
				            </select>
				        </td>
				        <td colspan="2" class="thpladmin_repeat_operand"><input type="text" name="i_repeat_operand" style="width:200px;"/></td>
					</tr>
				</tbody></table>
        		<!--<table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody>
					<tr>
						<td width="25%">
				            <select name="i_repeat_operator" style="width:200px;" onchange="thwcfeRepeatOperatorChangeListner(this)">
				                <option value="cart_contains">Cart quantity of</option>
				            </select>
				        </td>
				        <td width="25%">
				            <select name="i_repeat_operand_type" style="width:200px;" onchange="thwcfeRepeatOperandTypeChangeListner(this)">
				                <option value="">...</option>
				                <option value="product">Product</option>
				            </select>
				        </td>
				        <td colspan="2" class="thpladmin_repeat_operand"><input type="text" name="i_repeat_operand" style="width:200px;"/></td>
					</tr>
				</tbody></table>-->
        	</td>
        </tr>
        <?php 
			$this->render_form_element_h_spacing(); 
		?>
        <tr>
        	<td colspan="2">&nbsp;</td>
        	<td colspan="4">
        	<?php
        	$this->render_form_field_element($form_props['rpt_incl_parent'], $this->cell_props_CBS, false);
			?>
            </td>
        </tr>
        <tr>
        <?php
            $this->render_form_field_element($form_props['rpt_name_suffix'], $this->cell_props_L);
            $this->render_form_field_element($form_props['rpt_label_suffix'], $this->cell_props_R);
        ?>
        </tr>
        <?php
	}
	
	private function render_field_form_fragment_rules($type="field"){
		?>
		<tr><td style="padding-left: 12px; color: red; padding-bottom: 20px;">Note: For Account registration area this conditional display are not available.</td></tr>
        <tr>
        	
        	<td style="padding-left: 12px;">
                <select name="i_rules_action" style="width:80px;">
                    <option value="show">Show</option>
                    <option value="hide">Hide</option>
                </select>
                <?php echo $type; ?> if all below conditions are met.
            </td>
        </tr>
        <tr>                
            <td>
            	<table class="thwepo_conditional_rules" width="100%"><tbody>
                    <tr class="thwepo_rule_set_row">                
                        <td>
                            <table class="thwepo_rule_set" width="100%"><tbody>
                                <tr class="thwepo_rule_row">
                                    <td>
                                        <table class="thwepo_rule" width="100%" style=""><tbody>
                                            <tr class="thwepo_condition_set_row">
                                                <td>
                                                    <table class="thwepo_condition_set" width="100%" style=""><tbody>
                                                        <tr class="thwepo_condition">
                                                            <td width="25%">
                                                                <select name="i_rule_operator" style="width:200px;" onchange="thwcfeRuleOperatorChangeListner(this)">
                                                                    <option value=""></option>
                                                                    <option value="cart_contains">Cart contains</option>
                                                                    <option value="cart_not_contains">Cart not contains</option>
                                                                    <option value="cart_only_contains">Cart only contains</option>
                                                                    
																	<option value="cart_subtotal_eq">Cart subtotal equals to</option>
                                                                    <option value="cart_subtotal_gt">Cart subtotal greater than</option>
                                                                    <option value="cart_subtotal_lt">Cart subtotal less than</option>
                                                                    <option value="cart_total_eq">Cart total equals to</option>
                                                                    <option value="cart_total_gt">Cart total greater than</option>
                                                                    <option value="cart_total_lt">Cart total less than</option>
																	
																	<option value="user_role_eq">User role equals to</option>
                                                                    <option value="user_role_ne">User role not equals to</option>
                                                                    
                                                                    <?php /*?><option value="count_eq">Product count equals to</option>
                                                                    <option value="count_gt">Product count greater than</option>
                                                                    <option value="count_lt">Product count less than</option><?php */?>
                                                                </select>
                                                            </td>
                                                            <td width="25%">
                                                                <select name="i_rule_operand_type" style="width:200px;" onchange="thwcfeRuleOperandTypeChangeListner(this)">
                                                                    <option value=""></option>
                                                                    <option value="product">Product</option>
																	<option value="product_variation">Product Variation</option>
                                                                    <option value="category">Category</option>
                                                                    <option value="tag">Tag</option>
                                                                </select>
                                                            </td>
                                                            <td width="25%" class="thpladmin_rule_operand"><input type="text" name="i_rule_operand" style="width:200px;"/></td>
                                                            <td class="actions">
                                                                <a href="javascript:void(0)" class="thpl_logic_link" onclick="thwcfeAddNewConditionRow(this, 1)" title="">AND</a>
                                                                <a href="javascript:void(0)" class="thpl_logic_link" onclick="thwcfeAddNewConditionRow(this, 2)" title="">OR</a>
                                                                <a href="javascript:void(0)" class="thpl_delete_icon dashicons dashicons-no" onclick="thwcfeRemoveRuleRow(this)" title="Remove"></a>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>            	
                        </td>            
                    </tr> 
        		</tbody></table>
        	</td>
        </tr>
        <?php
	}
	
	private function render_field_form_fragment_rules_ajax($type="field"){
		?>
        <tr><td style="border-top: 1px dashed #e6e6e6;">&nbsp;</td></tr>
        <tr>
        	<td style="padding-left: 12px;">
                <select name="i_rules_action_ajax" style="width:80px;">
                    <option value="show">Show</option>
                    <option value="hide">Hide</option>
                </select>
                <?php echo $type; ?> if all below conditions are met.
            </td>
        </tr>
        <tr>                
            <td>
            	<table class="thwepo_conditional_rules_ajax" width="100%"><tbody>
                    <tr class="thwepo_rule_set_row">                
                        <td>
                            <table class="thwepo_rule_set" width="100%"><tbody>
                                <tr class="thwepo_rule_row">
                                    <td>
                                        <table class="thwepo_rule" width="100%" style=""><tbody>
                                            <tr class="thwepo_condition_set_row">
                                                <td>
                                                    <table class="thwepo_condition_set" width="100%" style=""><tbody>
                                                        <tr class="thwepo_condition">
                                                        	<td width="25%" class="thpladmin_rule_operand">
                                                            	<input type="hidden" name="i_rule_operand_type" value="field" />
                                                            	<?php $this->render_field_form_fragment_fields_select(); ?>
                                                            </td>
                                                            <td width="25%">
                                                                <select name="i_rule_operator" style="width:200px;" onchange="thwcfeRuleOperatorChangeListnerAjax(this)">
                                                                    <option value="">Please select an operator</option>
                                                                    <option value="empty">Is empty</option>
                                                                    <option value="not_empty">Is not empty</option>
                                                                    <option value="value_eq">Value equals to</option>
                                                                    <option value="value_ne">Value not equals to</option>
                                                                    <option value="value_in">Value in</option>
                                                                    <option value="value_cn">Contains</option>
                                                                    <option value="value_nc">Not contains</option>
                                                                    <option value="value_gt">Value greater than</option>
                                                                    <option value="value_le">Value less than</option>
                                                                    <option value="value_sw">Value starts with</option>
                                                                    <option value="value_nsw">Value not starts with</option>
																	<option value="date_eq">Date equals to</option>
                                                                    <option value="date_ne">Date not equals to</option>
                                                                    <option value="date_gt">Date after</option>
                                                                    <option value="date_lt">Date before</option>
																	<option value="day_eq">Day equals to</option>
                                                                    <option value="day_ne">Day not equals to</option>
                                                                    <option value="checked">Is checked</option>
                                                                    <option value="not_checked">Is not checked</option>
                                                                    <option value="regex">Match expression</option>
                                                                </select>
                                                            </td>
                                                            <td width="25%"><input type="text" name="i_rule_value" style="width:200px;"/></td>
                                                            <td class="actions">
                                                              <a href="javascript:void(0)" class="thpl_logic_link" onclick="thwcfeAddNewConditionRowAjax(this, 1)" title="">AND</a>
                                                              <a href="javascript:void(0)" class="thpl_logic_link" onclick="thwcfeAddNewConditionRowAjax(this, 2)" title="">OR</a>
                                                              <a href="javascript:void(0)" class="thpl_delete_icon dashicons dashicons-no" onclick="thwcfeRemoveRuleRowAjax(this)" title="Remove"></a>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>            	
                        </td>            
                    </tr> 
        		</tbody></table>
        	</td>
        </tr>
        <?php
	}
	
	/*private function render_field_form_fragment_product_list(){
		//$products = apply_filters( "thpladmin_load_products", array() );
		$products = WCFE_Checkout_Fields_Utils::load_products();
		if(!empty($products)){
			array_unshift( $products , array( "id" => "-1", "title" => "All Products" ));
			?>
	        <div id="thwcfe_product_select" style="display:none;">
	        <select multiple="multiple" name="i_rule_operand" data-placeholder="Click to select products" class="thwcfe-enhanced-multi-select thwcfe-operand" style="width:200px;" value="">
				<?php 	
	                foreach($products as $product){
	                    echo '<option value="'. $product["id"] .'" >'. $product["title"] .'</option>';
	                }
	            ?>
	        </select>
	        </div>
	        <?php
	    }else{
	    	?>
	        <div id="thwcfe_product_select" style="display:none;">
	        <input type="text" name="i_rule_operand" class="thwcfe-operand" style="width:200px;" value="">
	        </div>
	        <?php
	    }
	}*/

	private function render_field_form_fragment_product_list(){
		?>
        <div id="thwcfe_product_select" style="display:none;">
        <select multiple="multiple" name="i_rule_operand" data-placeholder="Click to select products" class="thwcfe-enhanced-multi-select1 thwcfe-operand thwcfe-product-select" style="width:200px;" value="">
        </select>
        </div>
        <?php
	}
	
	private function render_field_form_fragment_category_list(){		
		//$categories = apply_filters( "thpladmin_load_products_cat", array() );
		$categories = WCFE_Checkout_Fields_Utils::load_products_cat();
		array_unshift( $categories , array( "id" => "-1", "title" => "All Categories" ));
		?>
        <div id="thwcfe_product_cat_select" style="display:none;">
        <select multiple="multiple" name="i_rule_operand" data-placeholder="Click to select categories" class="thwcfe-enhanced-multi-select" style="width:200px;" value="">
			<?php 	
                foreach($categories as $category){
                    echo '<option value="'. $category["id"] .'" >'. $category["title"] .'</option>';
                }
            ?>
        </select>
        </div>
        <?php
	}

	private function render_field_form_fragment_tag_list(){		
		//$categories = apply_filters( "thpladmin_load_products_cat", array() );
		$tags = WCFE_Checkout_Fields_Utils::load_product_tags();
		array_unshift( $tags , array( "id" => "-1", "title" => "All Tags" ));
		?>
        <div id="thwcfe_product_tag_select" style="display:none;">
        <select multiple="multiple" name="i_rule_operand" data-placeholder="Click to select tags" class="thwcfe-enhanced-multi-select" style="width:200px;" value="">
			<?php 	
                foreach($tags as $tag){
                    echo '<option value="'. $tag["id"] .'" >'. $tag["title"] .'</option>';
                }
            ?>
        </select>
        </div>
        <?php
	}
	
	private function render_field_form_fragment_user_role_list(){		
		$user_roles = apply_filters( "thpladmin_load_user_roles", array() );
		//array_unshift( $user_roles , array( "id" => "-1", "title" => "All User Roles" ));
		?>
        <div id="thwcfe_user_role_select" style="display:none;">
        <select multiple="multiple" name="i_rule_operand" data-placeholder="Click to select user roles" class="thwcfe-enhanced-multi-select" style="width:200px;" value="">
			<?php 	
                foreach($user_roles as $role){
                    echo '<option value="'. $role["id"] .'" >'. $role["title"] .'</option>';
                }
            ?>
        </select>
        </div>
        <?php
	}
	
	private function render_field_form_fragment_fields_wrapper(){		
		?>
        <div id="thwcfe_checkout_fields_select" style="display:none;">
			<?php $this->render_field_form_fragment_fields_select(); ?>
        </div>
        <?php
	}
	
	private function render_field_form_fragment_fields_select(){		
		$sections = THWCFE_Utils::get_custom_sections();	
		$show_name = apply_filters('thwcfe_show_filed_name_for_field_list_in_conditions_tab', true);
		
		$other_fields = array('ship-to-different-address-checkbox' => 'Ship to a different address');
		if(THWCFE_Utils::get_settings('enable_conditions_payment_shipping')){
			$other_fields['shipping_method[0]'] = 'Shipping Method';
			$other_fields['payment_method'] = 'Payment Method';
		}
		$other_fields = apply_filters('thwcfe_extra_fields_for_diaplay_rules', $other_fields); //Deprecated
		$other_fields = apply_filters('thwcfe_extra_fields_for_display_rules', $other_fields);
		
		?>
        <select multiple="multiple" name="i_rule_operand" data-placeholder="Click to select field(s)" class="thwcfe-enhanced-multi-select" style="width:200px;" value="">
			<?php 
			if($sections && is_array($sections)){	
				foreach($sections as $sname => $section){	
					if($section && THWCFE_Utils_Section::is_valid_section($section)){
						$fields = THWCFE_Utils_Section::get_fields($section);
						if($fields && is_array($fields)){	
							echo '<optgroup label="'. $section->get_property('title') .'">';
							foreach($fields as $name => $field){
								if($field && THWCFE_Utils_Field::is_valid_field($field) && THWCFE_Utils_Field::is_enabled($field)){
									$label = $field->get_property('title');
									$label = empty($label) ? $name : $label;
									if($show_name){
										$label .= ' ('. $name .')';
									}
									echo '<option value="'. $name .'" >'. $label .'</option>';
								}
							}
							echo '</optgroup>';
						}
					}
				}
				echo '<optgroup label="Other Fields">';
				foreach($other_fields as $name => $label){
					if($name && $label){
						echo '<option value="'. $name .'" >'. THWCFE_i18n::t($label) .'</option>';
					}
				}
				echo '</optgroup>';
			}
            ?>
        </select>
        <?php 
	}
	
   /*******************************************
 	*-------- HTML FORM FRAGMENTS - END ------- 
 	*******************************************/
}

endif;