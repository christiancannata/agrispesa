<?php

/**
 * Order section of the plugin
 *
 * @link            
 *
 * @package  Wt_Import_Export_For_Woo_Basic 
 */
if (!defined('ABSPATH')) {
    exit;
}

if(!class_exists('Wt_Import_Export_For_Woo_Basic_Order')){
class Wt_Import_Export_For_Woo_Basic_Order {

    public $module_id = '';
    public static $module_id_static = '';
    public $module_base = 'order';
    public $module_name = 'Order Import Export for WooCommerce';
    public $min_base_version= '1.0.0'; /* Minimum `Import export plugin` required to run this add on plugin */

    private $importer = null;
    private $exporter = null;
    private $all_meta_keys = array();
    private $exclude_hidden_meta_columns = array();
    private $found_meta = array();
    private $found_hidden_meta = array();
    private $selected_column_names = null;

    public function __construct()
    {      
        /**
        *   Checking the minimum required version of `Import export plugin` plugin available
        */
        if(!Wt_Import_Export_For_Woo_Basic_Common_Helper::check_base_version($this->module_base, $this->module_name, $this->min_base_version))
        {
            return;
        }
        if(!function_exists('is_plugin_active'))
        {
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
        }
        if(!is_plugin_active('woocommerce/woocommerce.php'))
        {
            return;
        }
       
        $this->module_id = Wt_Import_Export_For_Woo_Basic::get_module_id($this->module_base);
        self::$module_id_static = $this->module_id;
                
        add_filter('wt_iew_exporter_post_types_basic', array($this, 'wt_iew_exporter_post_types_basic'), 10, 1);
        add_filter('wt_iew_importer_post_types_basic', array($this, 'wt_iew_exporter_post_types_basic'), 10, 1);

        
        add_filter('wt_iew_exporter_alter_mapping_fields_basic', array($this, 'exporter_alter_mapping_fields'), 10, 3);        
        add_filter('wt_iew_importer_alter_mapping_fields_basic', array($this, 'get_importer_post_columns'), 10, 3);  
        
		add_filter('wt_iew_exporter_alter_filter_fields_basic', array($this, 'exporter_alter_filter_fields'), 10, 3);
		
        add_filter('wt_iew_exporter_alter_advanced_fields_basic', array($this, 'exporter_alter_advanced_fields'), 10, 3);        
        add_filter('wt_iew_importer_alter_advanced_fields_basic', array($this, 'importer_alter_advanced_fields'), 10, 3);
        
        add_filter('wt_iew_exporter_alter_meta_mapping_fields_basic', array($this, 'exporter_alter_meta_mapping_fields'), 10, 3);
        add_filter('wt_iew_importer_alter_meta_mapping_fields_basic', array($this, 'importer_alter_meta_mapping_fields'), 10, 3);

        add_filter('wt_iew_exporter_alter_mapping_enabled_fields_basic', array($this, 'exporter_alter_mapping_enabled_fields'), 10, 3);
        add_filter('wt_iew_importer_alter_mapping_enabled_fields_basic', array($this, 'exporter_alter_mapping_enabled_fields'), 10, 3);

        add_filter('wt_iew_exporter_do_export_basic', array($this, 'exporter_do_export'), 10, 7);
        add_filter('wt_iew_importer_do_import_basic', array($this, 'importer_do_import'), 10, 8);

        add_filter('wt_iew_importer_steps_basic', array($this, 'importer_steps'), 10, 2);
		
		
		add_action('admin_footer-edit.php', array($this, 'wt_add_order_bulk_actions'));
        add_action('load-edit.php', array($this, 'wt_process_order_bulk_actions'));
    }

	
	
	
    public function wt_add_order_bulk_actions() {
        global $post_type, $post_status;

        if ( 'shop_order' === $post_type && 'trash' !== $post_status && !is_plugin_active( 'wt-import-export-for-woo/wt-import-export-for-woo.php' )) {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    var $downloadOrders = $('<option>').val('wt_ier_download_orders').text('<?php _e('Export to CSV') ?>');
                    $('select[name^="action"]').append($downloadOrders);
                });
            </script>
            <?php
        }
    }
    
    /**
     * Order page bulk export action
     * 
     */
    public function wt_process_order_bulk_actions() {
        global $typenow;
        if ($typenow == 'shop_order') {
            // get the action list
            $wp_list_table = _get_list_table('WP_Posts_List_Table');
            $action = $wp_list_table->current_action();
            if (!in_array($action, array('wt_ier_download_orders'))) {
                return;
            }
            // security check
            check_admin_referer('bulk-posts');

            if (isset($_REQUEST['post'])) {
                $order_ids = array_map('absint', $_REQUEST['post']);
            }
            if (empty($order_ids)) {
                return;
            }
            // give an unlimited timeout if possible
            @set_time_limit(0);

            if ($action == 'wt_ier_download_orders') {
                
                
                include_once( 'export/class-wt-orderimpexpcsv-basic-exporter.php' );
                Wt_Import_Export_For_Woo_Basic_Order_Bulk_Export::do_export('shop_order', $order_ids);
            }
        }
    }
	
    /**
    *   Altering advanced step description
    */
    public function importer_steps($steps, $base)
    {
        if($this->module_base==$base)
        {
            $steps['advanced']['description']=__('Use advanced options from below to decide updates to existing orders, batch import count. You can also save the template file for future imports.');
        }
        return $steps;
    }
    
    public function importer_do_import($import_data, $base, $step, $form_data, $selected_template_data, $method_import, $batch_offset, $is_last_batch) {        
        if ($this->module_base != $base) {
            return $import_data;
        }             

        if(0 == $batch_offset){                        
            $memory = size_format(wt_let_to_num_basic(ini_get('memory_limit')));
            $wp_memory = size_format(wt_let_to_num_basic(WP_MEMORY_LIMIT));                      
            Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->module_base, 'import', '---[ New import started at '.date('Y-m-d H:i:s').' ] PHP Memory: ' . $memory . ', WP Memory: ' . $wp_memory);
        }
                
        include plugin_dir_path(__FILE__) . 'import/import.php';
        $import = new Wt_Import_Export_For_Woo_Basic_Order_Import($this);
        
        $response = $import->prepare_data_to_import($import_data,$form_data,$batch_offset,$is_last_batch);
        
        if($is_last_batch){
            Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->module_base, 'import', '---[ Import ended at '.date('Y-m-d H:i:s').']---');
        }
        
        return $response;
    }

    public function exporter_do_export($export_data, $base, $step, $form_data, $selected_template_data, $method_export, $batch_offset) {
        if ($this->module_base != $base) {
            return $export_data;
        }
        
        switch ($method_export) {
            case 'quick':
                $this->set_export_columns_for_quick_export($form_data);
                break;

            case 'template':
            case 'new':
                $this->set_selected_column_names($form_data);
                break;
            
            default:
                break;
        }

        include plugin_dir_path(__FILE__) . 'export/export.php';
        $export = new Wt_Import_Export_For_Woo_Basic_Order_Export($this);
                      
        $data_row = $export->prepare_data_to_export($form_data, $batch_offset);
        
        $header_row = $export->prepare_header(); 

        $export_data = array(
            'head_data' => $header_row,
            'body_data' => $data_row['data'],
            'total' => $data_row['total'],
        ); 

        return $export_data;
    }

    /**
     * Adding current post type to export list
     *
     */
    public function wt_iew_exporter_post_types_basic($arr) {
        $arr['order'] = __('Order', 'order-import-export-for-woocommerce');
		$arr['coupon'] = __('Coupon', 'order-import-export-for-woocommerce');
		$arr['product'] = __('Product', 'order-import-export-for-woocommerce');
		$arr['product_review'] = __('Product Review', 'order-import-export-for-woocommerce');
		$arr['product_categories'] = __('Product Categories', 'order-import-export-for-woocommerce');
		$arr['product_tags'] = __('Product Tags', 'order-import-export-for-woocommerce');
		$arr['user'] = __('User/Customer', 'order-import-export-for-woocommerce');
        return $arr;
    }

    /*
     * Setting default export columns for quick export
     */

    public function set_export_columns_for_quick_export($form_data) {

        $post_columns = self::get_order_post_columns();

        $this->selected_column_names = array_combine(array_keys($post_columns), array_keys($post_columns));

        if (isset($form_data['method_export_form_data']['mapping_enabled_fields']) && !empty($form_data['method_export_form_data']['mapping_enabled_fields'])) {
            foreach ($form_data['method_export_form_data']['mapping_enabled_fields'] as $value) {
                $additional_quick_export_fields[$value] = array('fields' => array());
            }

            $export_additional_columns = $this->exporter_alter_meta_mapping_fields($additional_quick_export_fields, $this->module_base, array());
            foreach ($export_additional_columns as $value) {
                $this->selected_column_names = array_merge($this->selected_column_names, $value['fields']);
            }
        }
    }

    public static function get_order_statuses() {
        $statuses = wc_get_order_statuses();
        return apply_filters('wt_iew_allowed_order_statuses',  $statuses);
    }

    public static function get_order_sort_columns() {
        $sort_columns = array('post_parent', 'ID', 'post_author', 'post_date', 'post_title', 'post_name', 'post_modified', 'menu_order', 'post_modified_gmt', 'rand', 'comment_count');
        return apply_filters('wt_iew_allowed_order_sort_columns', array_combine($sort_columns, $sort_columns));
    }

    public static function get_order_post_columns() {
        return include plugin_dir_path(__FILE__) . 'data/data-order-post-columns.php';
    }
    
    public function get_importer_post_columns($fields, $base, $step_page_form_data) {
        if ($base != $this->module_base) {
            return $fields;
        }
        $colunm = include plugin_dir_path(__FILE__) . 'data/data/data-wf-reserved-fields-pair.php';
//        $colunm = array_map(function($vl){ return array('title'=>$vl, 'description'=>$vl); }, $arr); 
        return $colunm;
    }

    public function exporter_alter_mapping_enabled_fields($mapping_enabled_fields, $base, $form_data_mapping_enabled_fields) {
        if ($base == $this->module_base) {
            $mapping_enabled_fields = array();
        }
        return $mapping_enabled_fields;
    }

    public function exporter_alter_meta_mapping_fields($fields, $base, $step_page_form_data) {
        if ($base != $this->module_base) {
            return $fields;
        }
		/*
        foreach ($fields as $key => $value) {
            switch ($key) {
                

                default:
                    break;
            }
        }
		 * 
		 */

        return $fields;
    }
    
    
    public function importer_alter_meta_mapping_fields($fields, $base, $step_page_form_data) {
        if ($base != $this->module_base) {
            return $fields;
        }
        $fields=$this->exporter_alter_meta_mapping_fields($fields, $base, $step_page_form_data);
        $out=array();
        foreach ($fields as $key => $value) 
        {
            $value['fields']=array_map(function($vl){ return array('title'=>$vl, 'description'=>$vl); }, $value['fields']);
            $out[$key]=$value;
        }
        return $out;
    }
    
    public function wt_get_found_meta() {   

        if (!empty($this->found_meta)) {
            return $this->found_meta;
        }

        // Loop products and load meta data
        $found_meta = array();
        // Some of the values may not be usable (e.g. arrays of arrays) but the worse
        // that can happen is we get an empty column.

        $all_meta_keys = $this->wt_get_all_meta_keys();

        $csv_columns = self::get_order_post_columns();

        $exclude_hidden_meta_columns = $this->wt_get_exclude_hidden_meta_columns();

        foreach ($all_meta_keys as $meta) {

            if (!$meta || (substr((string) $meta, 0, 1) == '_') || in_array($meta, $exclude_hidden_meta_columns) || in_array($meta, array_keys($csv_columns)) || in_array('meta:' . $meta, array_keys($csv_columns)))
                continue;

            $found_meta[] = $meta;
        }
        
        $found_meta = array_diff($found_meta, array_keys($csv_columns));
        $this->found_meta = $found_meta;
        return $this->found_meta;
    }

    public function wt_get_found_hidden_meta() {

        if (!empty($this->found_hidden_meta)) {
            return $this->found_hidden_meta;
        }

        // Loop products and load meta data
        $found_hidden_meta = array();
        // Some of the values may not be usable (e.g. arrays of arrays) but the worse
        // that can happen is we get an empty column.

        $all_meta_keys = $this->wt_get_all_meta_keys();
        $csv_columns = self::get_order_post_columns();
        $exclude_hidden_meta_columns = $this->wt_get_exclude_hidden_meta_columns();
        foreach ($all_meta_keys as $meta) {

            if (!$meta || (substr((string) $meta, 0, 1) != '_') || ((substr((string) $meta, 0, 1) == '_') && in_array(substr((string) $meta,1), array_keys($csv_columns)) ) || in_array($meta, $exclude_hidden_meta_columns) || in_array($meta, array_keys($csv_columns)) || in_array('meta:' . $meta, array_keys($csv_columns)))
                continue;

            $found_hidden_meta[] = $meta;
        }

        $found_hidden_meta = array_diff($found_hidden_meta, array_keys($csv_columns));

        $this->found_hidden_meta = $found_hidden_meta;
        return $this->found_hidden_meta;
    }

    public function wt_get_exclude_hidden_meta_columns() {

        if (!empty($this->exclude_hidden_meta_columns)) {
            return $this->exclude_hidden_meta_columns;
        }

        $exclude_hidden_meta_columns = include( plugin_dir_path(__FILE__) . 'data/data-wf-exclude-hidden-meta-columns.php' );

        $this->exclude_hidden_meta_columns = $exclude_hidden_meta_columns;
        return $this->exclude_hidden_meta_columns;
    }

    public function wt_get_all_meta_keys() {

        if (!empty($this->all_meta_keys)) {
            return $this->all_meta_keys;
        }

        $all_meta_keys = self::get_all_metakeys('shop_order');

        $this->all_meta_keys = $all_meta_keys;
        return $this->all_meta_keys;
    }

    /**
     * Get a list of all the meta keys for a post type. This includes all public, private,
     * used, no-longer used etc. They will be sorted once fetched.
     */
    public static function get_all_metakeys($post_type = 'shop_order') {
        global $wpdb;
        $meta = $wpdb->get_col($wpdb->prepare(
                        "SELECT DISTINCT pm.meta_key
            FROM {$wpdb->postmeta} AS pm
            LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id
            WHERE p.post_type = %s
            AND p.post_status IN ( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed' ) ORDER BY pm.meta_key", $post_type
        ));
        //sort($meta);
        return $meta;
    }

    public function set_selected_column_names($full_form_data) {
        if (is_null($this->selected_column_names)) {
            if (isset($full_form_data['mapping_form_data']['mapping_selected_fields']) && !empty($full_form_data['mapping_form_data']['mapping_selected_fields'])) {
                $this->selected_column_names = $full_form_data['mapping_form_data']['mapping_selected_fields'];
            }
            if (isset($full_form_data['meta_step_form_data']['mapping_selected_fields']) && !empty($full_form_data['meta_step_form_data']['mapping_selected_fields'])) {
                $export_additional_columns = $full_form_data['meta_step_form_data']['mapping_selected_fields'];
                foreach ($export_additional_columns as $value) {
                    $this->selected_column_names = array_merge($this->selected_column_names, $value);
                }
            }
        }

        return $full_form_data;
    }

    public function get_selected_column_names() {

        return $this->selected_column_names;
    }

    public function exporter_alter_mapping_fields($fields, $base, $mapping_form_data) {
        if ($base == $this->module_base) {
            $fields = self::get_order_post_columns();
        }
        return $fields;
    }

    public function exporter_alter_advanced_fields($fields, $base, $advanced_form_data) {
        if ($this->module_base != $base) {
            return $fields;
        }      
        unset($fields['export_shortcode_tohtml']);
        
        $out = array();
				
		$out['header_empty_row'] = array(
			'tr_html' => '<tr id="header_empty_row"><th></th><td></td></tr>'
		);
        $out['exclude_already_exported'] = array(
            'label' => __("Exclude already exported"),
            'type' => 'checkbox',
			'checkbox_fields' => array( 1 => __( 'Enable' ) ),
            'value' => 0,
            'field_name' => 'exclude_already_exported',
            'help_text' => __('Enable this to exclude the previously exported orders.'),
        );
        
        $out['export_to_separate'] = array(
            'label' => __("Export line items in"),
            'type' => 'radio',
            'radio_fields' => array(
                'default' => __('Default mode'),
                'column' => __('Separate columns'),
                'row' => __('Separate rows')    
            ),
            'value' => 'default',
            'merge_right'=>true,
            'field_name' => 'export_to_separate',
            //'help_text' => __("Option 'Yes' exports the line items within the order into separate columns in the exported file."),
            'help_text_conditional'=>array(
                array(
                    'help_text'=> __('The default option will export each line item details into a single column. This option is mainly used for the order migration purpose.'),
                    'condition'=>array(
                        array('field'=>'wt_iew_export_to_separate', 'value'=>'default')
                    )
                ),
                array(
                    'help_text'=> __('This option will export each line item details into a separate column.'),
                    'condition'=>array(
                        array('field'=>'wt_iew_export_to_separate', 'value'=>'column')
                    )
                ),
                array(
                    'help_text'=> __('This option will export each line item details into a separate row.'),
                    'condition'=>array(
                        array('field'=>'wt_iew_export_to_separate', 'value'=>'row')
                    )
                )
            )
        );

        foreach ($fields as $fieldk => $fieldv) {
            $out[$fieldk] = $fieldv;
        }
        return $out;
    }
    
    public function importer_alter_advanced_fields($fields, $base, $advanced_form_data) {
        if ($this->module_base != $base) {
            return $fields;
        }
        $out = array();
        

		$out['header_empty_row'] = array(
			'tr_html' => '<tr id="header_empty_row"><th></th><td></td></tr>'
		);
        $out['found_action_merge'] = array(
            'label' => __("If order exists in the store"),
            'type' => 'radio',
            'radio_fields' => array(
                'skip' => __('Skip'),
                'update' => __('Update'),                
            ),
            'value' => 'skip',
            'field_name' => 'found_action',
            'help_text' => __('Orders are matched by their order IDs.'),
            'help_text_conditional'=>array(
                array(
                    'help_text'=> __('This option will not update the existing orders and keeps the order as is.'),
                    'condition'=>array(
                        array('field'=>'wt_iew_found_action', 'value'=>'skip')
                    )
                ),
                array(
                    'help_text'=> __('This option will update the existing orders as per the data from the input file.'),
                    'condition'=>array(
                        array('field'=>'wt_iew_found_action', 'value'=>'update')
                    )
                )
            ),
            'form_toggler'=>array(
                'type'=>'parent',
                'target'=>'wt_iew_found_action'
            )
        );       
        
        $out['ord_link_using_sku'] = array(
            'label' => __('Link order items using'),
            'type' => 'radio',
            'radio_fields' => array(
                '0' => __('Product ID'),				
                '1' => __('Product SKU')
            ),
            'value' => '0',
            'field_name' => 'ord_link_using_sku',
			'merge_right' => true,
            'help_text_conditional'=>array(
                array(
                    'help_text'=> __('Links the products of the imported orders by SKU.'),
                    'condition'=>array(
                        array('field'=>'wt_iew_ord_link_using_sku', 'value'=>1)
                    )
                ),
                array(
                    'help_text'=> __('Links the products of the imported orders by Product ID. However, the products will not get linked if the Product ID conflicts with the IDs of an existing post type.'),
                    'condition'=>array(
                        array('field'=>'wt_iew_ord_link_using_sku', 'value'=>0)
                    )
                )
            ),
        );
		if( !is_plugin_active( 'product-import-export-for-woo/product-import-export-for-woo.php' ) ){
					$out['ord_link_using_sku']['help_text'] = sprintf(
						/* translators: %s: Product Import Export for WooCommerce plugin  URL */
						__( 'If you do not already have corresponding products added in your store, we recommend that you import them first using <a href="%s" target="_blank">Product Import Export for WooCommerce</a>.' ),
						admin_url('plugin-install.php?tab=plugin-information&plugin=product-import-export-for-woo')
					);
		 }
        
		$out['update_stock_details'] = array(
            'label' => __("Update stock details"),
            'type' => 'checkbox',
			'checkbox_fields' => array( 1 => __( 'Enable' ) ),
            'value' => 0,
            'field_name' => 'update_stock_details',
            'help_text' => __('Select to update the sale count and stock quantity of a product associated with the order.<br/>Note: Ensure the manage stock option is enabled. This feature is not meant to work for the refunded, cancelled or failed order statuses.'),
        );
        
        foreach ($fields as $fieldk => $fieldv) {
            $out[$fieldk] = $fieldv;
        }
        return $out;
    }
    
    /**
     *  Customize the items in filter export page
     */
    public function exporter_alter_filter_fields($fields, $base, $filter_form_data) {

        if ($base == $this->module_base)
        {
            /* altering help text of default fields */
            $fields['limit']['label']=__('Total number of orders to export'); 
            $fields['limit']['help_text']=__( 'Provide the number of orders you want to export. e.g. Entering 500 with a skip count of 10 will export orders from 11th to 510th position.' );
            $fields['offset']['label']=__('Skip first <i>n</i> orders');
            $fields['offset']['help_text']=__('Skips specified number of orders from the beginning of the database. e.g. Enter 10 to skip first 10 orders from export.');

            $fields['orders'] = array(
                'label' => __('Order IDs'),
                'placeholder' => __('Enter order IDs separated by ,'),
                'field_name' => 'orders',
                'sele_vals' => '',
                'help_text' => __('Enter order IDs separated by comma to export specific orders.'),
                'type' => 'text',
                'css_class' => '',
            );
            
            $fields['order_status'] = array(
                'label' => __('Order status'),
                'placeholder' => __('Any status'),
                'field_name' => 'order_status',
                'sele_vals' => self::get_order_statuses(),
                'help_text' => __( 'Filter orders on the basis of status. Multiple statuses can be selected.' ),
                'type' => 'multi_select',
                'css_class' => 'wc-enhanced-select',
                'validation_rule' => array('type'=>'text_arr')
            );
            $fields['products'] = array(
                'label' => __('Product'),
                'placeholder' => __('Search for a product&hellip;'),
                'field_name' => 'products',
                'sele_vals' => array(),
                'help_text' => __( 'Export orders containing specific products. Input name, ID or SKU of the products contained in the orders you want to export.' ),
                'type' => 'multi_select',
                'css_class' => 'wc-product-search',
                'validation_rule' => array('type'=>'text_arr')
            );
            $fields['email'] = array(
                'label' => __('Customer'),
                'placeholder' => __('Search for a customer&hellip;'),
                'field_name' => 'email',
                'sele_vals' => array(),
                'help_text' => __( 'Export orders of specific customers. Input the customer name or email to specify the customers.' ),
                'type' => 'multi_select',
                'css_class' => 'wc-customer-search',
                'validation_rule' => array('type'=>'text_arr')
            );
            $fields['coupons'] = array(
                'label' => __('Coupons'),
                'placeholder' => __('Search for a coupon&hellip;'),
                'field_name' => 'coupons',
                'sele_vals' => array(),
                'help_text' => __( 'Exports orders redeemed with specific coupon codes. Multiple coupon codes can be selected.' ),
                'type' => 'multi_select',
                'css_class' => 'wt-coupon-search',
				'validation_rule' => array('type'=>'text_arr')
            );

            $fields['date_from'] = array(
                'label' => __('Date from'),
                'placeholder' => __('Date from'),
                'field_name' => 'date_from',
                'sele_vals' => '',
                'help_text' => __( 'Export orders placed on and after the specified date.' ),
                'type' => 'text',
                'css_class' => 'wt_iew_datepicker',
//                'type' => 'field_html',
//                'field_html' => '<input type="text" name="date_from" class="wt_iew_datepicker" placeholder="'.__('From date').'" class="input-text" />',
            );

            $fields['date_to'] = array(
                'label' => __('Date to'),
                'placeholder' => __('Date to'),
                'field_name' => 'date_to',
                'sele_vals' => '',
                'help_text' => __( 'Export orders placed upto the specified date.' ),
                'type' => 'text',
                'css_class' => 'wt_iew_datepicker',
//                'type' => 'field_html',
//                'field_html' => '<input type="text" name="date_to" class="wt_iew_datepicker" placeholder="'.__('To date').'" class="input-text" />',
            );


        }
        return $fields;
    }

    public static function wt_get_product_id_by_sku($sku) {
        global $wpdb;
        $post_exists_sku = $wpdb->get_var($wpdb->prepare("
	    		SELECT $wpdb->posts.ID
	    		FROM $wpdb->posts
	    		LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
	    		WHERE $wpdb->posts.post_status IN ( 'publish', 'private', 'draft', 'pending', 'future' )
	    		AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = '%s'
	    		", $sku));
        if ($post_exists_sku) {
            return $post_exists_sku;
        }
        return false;
    }
    
    public function get_item_by_id($id) {
		$post = array();
        $post['edit_url'] = get_edit_post_link($id);
        $post['title'] = $id;
        return $post; 
    }
	public static function get_item_link_by_id($id) {
		$post = array();
        $post['edit_url'] = get_edit_post_link($id);
        $post['title'] = $id;
        return $post; 
    }
}
}

new Wt_Import_Export_For_Woo_Basic_Order();
