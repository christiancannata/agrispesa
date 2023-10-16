<?php

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
class Wpcl_Options
{
    public function __construct()
    {
    }
    
    public static function get_table_options()
    {
        $columns = [
            'wpcl_index'                  => [
            'is_premium'             => false,
            'is_column'              => true,
            'default_value'          => 'false',
            'column_custom_function' => '',
            'shortcode_field'        => 'index',
            'shortcode_default'      => 'false',
            'column_pretty_name'     => __( 'Index', 'wc-product-customer-list' ),
            'default_premium_value'  => 'false',
            'is_setting'             => true,
            'setting'                => [
            'settings_order' => 1015,
            'name'           => __( 'Add an Index column', 'wc-product-customer-list' ),
            'desc'           => __( 'This will simply add a numbered column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_index',
            'default'        => 'no',
            'css'            => 'min-width:300px;',
            'type'           => 'checkbox',
        ],
        ],
            'wpcl_add_admin_shortcut'     => [
            'is_column'       => false,
            'is_option'       => false,
            'is_setting'      => true,
            'default_value'   => false,
            'shortcode_value' => false,
            'setting'         => [
            'settings_order' => 1,
            'name'           => __( 'Add shortcut under WooCommerce Menu', 'wc-product-customer-list' ),
            'id'             => 'wpcl_add_admin_shortcut',
            'default'        => 'yes',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'This link would let you jump directly to the settings tab', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_customer_avatar'        => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Customer avatar', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_customer_avatar',
            'shortcode_field'    => 'customer_avatar',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 230,
            'name'           => __( 'Customer avatar', 'wc-product-customer-list' ),
            'id'             => 'wpcl_customer_avatar',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable customer avatar column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_order_number'           => [
            'is_column'          => true,
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Order number', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_order_number',
            'shortcode_field'    => 'order_number',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 30,
            'name'           => __( 'Order number column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_number',
            'default'        => 'yes',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable order number column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_order_date'             => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Date', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_order_date',
            'shortcode_field'    => 'order_date',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 40,
            'name'           => __( 'Order date column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_date',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable order date column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_product_id'             => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Product ID', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_product_id',
            'shortcode_field'    => 'show_product_id',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 42,
            'name'           => __( 'Product ID', 'wc-product-customer-list' ),
            'id'             => 'wpcl_product_id',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable product ID column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_product_sku'            => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Product SKU', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_product_sku',
            'shortcode_field'    => 'show_product_sku',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 42,
            'name'           => __( 'Product SKU', 'wc-product-customer-list' ),
            'id'             => 'wpcl_product_sku',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable product SKU column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_billing_first_name'     => [
            'is_column'          => true,
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Billing First name', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_billing_first_name',
            'shortcode_field'    => 'billing_first_name',
            'shortcode_default'  => 'true',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 300,
            'name'           => __( 'Billing first name column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_billing_first_name',
            'default'        => 'yes',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable billing first name column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_billing_last_name'      => [
            'is_column'          => true,
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Billing Last name', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_billing_last_name',
            'shortcode_field'    => 'billing_last_name',
            'shortcode_default'  => 'true',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 310,
            'name'           => __( 'Billing last name column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_billing_last_name',
            'default'        => 'yes',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable billing last name column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_billing_company'        => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing Company', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_billing_company',
            'shortcode_field'    => 'billing_company',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 320,
            'name'           => __( 'Billing company column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_billing_company',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable billing company column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_billing_email'          => [
            'is_column'          => true,
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Billing E-mail', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_billing_email',
            'shortcode_field'    => 'billing_email',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 330,
            'name'           => __( 'Billing e-mail column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_billing_email',
            'default'        => 'yes',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable billing e-mail column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_billing_phone'          => [
            'is_column'          => true,
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Billing Phone', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_billing_phone',
            'shortcode_field'    => 'billing_phone',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 340,
            'name'           => __( 'Billing phone column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_billing_phone',
            'default'        => 'yes',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable billing phone column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_billing_address_1'      => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing Address 1', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_billing_address_1',
            'shortcode_field'    => 'billing_address_1',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 350,
            'name'           => __( 'Billing address 1 column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_billing_address_1',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable billing address 1 column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_billing_address_2'      => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing Address 2', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_billing_address_2',
            'shortcode_field'    => 'billing_address_2',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 360,
            'name'           => __( 'Billing address 2 column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_billing_address_2',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable billing address 2 column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_billing_city'           => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing City', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_billing_city',
            'shortcode_field'    => 'billing_city',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 370,
            'name'           => __( 'Billing city column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_billing_city',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable billing city column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_billing_state'          => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing State', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_billing_state',
            'shortcode_field'    => 'billing_state',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 380,
            'name'           => __( 'Billing state column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_billing_state',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable billing state column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_billing_postalcode'     => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing Postal Code / Zip', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_billing_postalcode',
            'shortcode_field'    => 'billing_postalcode',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 390,
            'name'           => __( 'Billing Postal Code / Zip column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_billing_postalcode',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable billing postal code / Zip column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_billing_country'        => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing Country', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_billing_country',
            'shortcode_field'    => 'billing_country',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 400,
            'name'           => __( 'Billing country column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_billing_country',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable billing country column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_shipping_first_name'    => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping First name', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_shipping_first_name',
            'shortcode_field'    => 'shipping_first_name',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 500,
            'name'           => __( 'Shipping first name column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_shipping_first_name',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable shipping first name column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_shipping_last_name'     => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Last name', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_shipping_last_name',
            'shortcode_field'    => 'shipping_last_name',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 510,
            'name'           => __( 'Shipping last name column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_shipping_last_name',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable shipping last name column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_shipping_company'       => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Company', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_shipping_company',
            'shortcode_field'    => 'shipping_company',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 520,
            'name'           => __( 'Shipping company column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_shipping_company',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable shipping company column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_shipping_address_1'     => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Address 1', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_shipping_address_1',
            'shortcode_field'    => 'shipping_address_1',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 530,
            'name'           => __( 'Shipping address 1 column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_shipping_address_1',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable shipping address 1 column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_shipping_address_2'     => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Address 2', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_shipping_address_2',
            'shortcode_field'    => 'shipping_address_2',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 540,
            'name'           => __( 'Shipping address 2 column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_shipping_address_2',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable shipping address 2 column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_shipping_city'          => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping City', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_shipping_city',
            'shortcode_field'    => 'shipping_city',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 550,
            'name'           => __( 'Shipping city column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_shipping_city',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable shipping city column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_shipping_state'         => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping State', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_shipping_state',
            'shortcode_field'    => 'shipping_state',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 560,
            'name'           => __( 'Shipping state column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_shipping_state',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable shipping state column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_shipping_postalcode'    => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Postal Code / Zip', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_shipping_postalcode',
            'shortcode_field'    => 'shipping_postalcode',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 570,
            'name'           => __( 'Shipping Postal Code / Zip column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_shipping_postalcode',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable shipping postal code / Zip column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_shipping_country'       => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Country', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_shipping_country',
            'shortcode_field'    => 'shipping_country',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 580,
            'name'           => __( 'Shipping country column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_shipping_country',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable shipping country column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_customer_message'       => [
            'is_column'          => true,
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Customer Message', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_customer_message',
            'shortcode_field'    => 'customer_message',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 110,
            'name'           => __( 'Customer message column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_customer_message',
            'default'        => 'yes',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable customer message column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_customer_id'            => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Customer ID', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_customer_id',
            'shortcode_field'    => 'customer_id',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 200,
            'name'           => __( 'Customer ID', 'wc-product-customer-list' ),
            'id'             => 'wpcl_customer_id',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable customer ID column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_customer_username'      => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Customer username', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_customer_username',
            'shortcode_field'    => 'customer_username',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 210,
            'name'           => __( 'Customer username', 'wc-product-customer-list' ),
            'id'             => 'wpcl_customer_username',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable customer username column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_customer_username_link' => [
            'is_column'          => false,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Customer username link', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_customer_username_link',
            'shortcode_field'    => 'customer_username_link',
            'shortcode_default'  => 'false',
        ],
            'wpcl_customer_display_name'  => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Customer display name', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_customer_display_name',
            'shortcode_field'    => 'customer_display_name',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 220,
            'name'           => __( 'Customer display name', 'wc-product-customer-list' ),
            'id'             => 'wpcl_customer_display_name',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable customer display name column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_order_status'           => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Order Status', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_order_status',
            'shortcode_field'    => 'order_status_column',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 15,
            'name'           => __( 'Order status column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_status',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable order status column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_order_status_select'    => [
            'is_column'          => false,
            'default_value'      => array( 'wc-completed', 'wc-processing' ),
            'column_pretty_name' => __( 'Order Status', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_order_status',
            'shortcode_field'    => 'order_status_column',
            'shortcode_default'  => 'wc-completed,wc-processing',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 10,
            'name'           => __( 'Order status', 'wc-product-customer-list' ),
            'desc'           => __( 'Select one or multiple order statuses for which you will display the customers. Hold ctrl (pc) or cmd (mac) + click to select multiple statuses.', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_status_select',
            'css'            => 'min-width:300px;',
            'default'        => array( 'wc-completed', 'wc-processing' ),
            'type'           => 'multiselect',
            'options'        => self::get_order_statuses_options(),
            'desc_tip'       => true,
        ],
        ],
            'wpcl_order_payment'          => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Payment method', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_order_payment',
            'shortcode_field'    => 'order_payment',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 70,
            'name'           => __( 'Payment method column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_payment',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable payment method column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_order_shipping'         => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping method', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_order_shipping',
            'shortcode_field'    => 'order_shipping',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 80,
            'name'           => __( 'Shipping method column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_shipping',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable shipping method column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_order_coupon'           => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Coupons used', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_order_coupon',
            'shortcode_field'    => 'order_coupon',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 90,
            'name'           => __( 'Coupons used column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_coupon',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable coupons used column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_variations'             => [
            'is_column'          => true,
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Variation', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_order_variations',
            'shortcode_field'    => 'order_variations',
            'shortcode_default'  => 'true',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 100,
            'name'           => __( 'Variations column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_variations',
            'default'        => 'yes',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable variations column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_order_tax_total'        => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Order tax total', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_order_tax_total',
            'shortcode_field'    => 'order_tax_total',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 69,
            'name'           => __( 'Order total column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_tax_total',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable order tax total column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_order_total'            => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Order total', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_order_total',
            'shortcode_field'    => 'order_total',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 70,
            'name'           => __( 'Order total column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_total',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable order total column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_order_qty'              => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Order Qty Total', 'wc-product-customer-list' ),
            'data_formatter'     => 'Wpcl_Data_Formatters::data_order_qty',
            'shortcode_field'    => 'order_qty',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 50,
            'name'           => __( 'Order quantity column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_qty',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable order quantity column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_order_qty_total'        => [
            'is_column'          => false,
            'is_option'          => true,
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Order Qty Total', 'wc-product-customer-list' ),
            'shortcode_field'    => 'order_qty_total',
            'shortcode_default'  => 'false',
        ],
            'wpcl_order_qty_total_column' => [
            'is_column'          => true,
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Order Qty Total column', 'wc-product-customer-list' ),
            'shortcode_field'    => 'order_qty_total_column',
            'shortcode_default'  => 'false',
            'is_setting'         => true,
            'setting'            => [
            'settings_order' => 50,
            'name'           => __( 'Order quantity total column', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_qty_total_column',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Enable order quantity total column', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_show_titles_row'        => [
            'is_column'         => false,
            'is_option'         => true,
            'default_value'     => 'yes',
            'shortcode_field'   => 'show_titles',
            'shortcode_default' => true,
        ],
            'wpcl_table_title'            => [
            'is_column'         => false,
            'default_value'     => '',
            'shortcode_field'   => 'table_title',
            'shortcode_default' => false,
        ],
            'wpcl_order_partial_refunds'  => [
            'is_column'         => false,
            'default_value'     => 'no',
            'shortcode_field'   => 'display_partial_refunds',
            'shortcode_default' => 'false',
            'is_setting'        => true,
            'setting'           => [
            'settings_order' => 20,
            'name'           => __( 'Partial refunds', 'wc-product-customer-list' ),
            'id'             => 'wpcl_order_partial_refunds',
            'default'        => 'no',
            'type'           => 'checkbox',
            'css'            => 'min-width:300px;',
            'desc'           => __( 'Hide partially refunded orders', 'wc-product-customer-list' ),
        ],
        ],
            'wpcl_limit'                  => [
            'is_column'         => false,
            'is_option'         => true,
            'default_value'     => 99999,
            'shortcode_field'   => 'limit',
            'shortcode_default' => 99999,
        ],
            'wpcl_copy'                   => [
            'is_premium'             => false,
            'is_premium_shortcode'   => true,
            'is_column'              => false,
            'default_value'          => 'true',
            'default_premium_value'  => 'true',
            'column_custom_function' => '',
            'shortcode_field'        => 'copy',
            'shortcode_default'      => 'false',
        ],
            'wpcl_export_pdf'             => [
            'is_premium'             => false,
            'is_premium_shortcode'   => true,
            'is_column'              => false,
            'default_value'          => 'true',
            'default_premium_value'  => 'true',
            'column_custom_function' => '',
            'shortcode_field'        => 'export_pdf',
            'shortcode_default'      => 'false',
        ],
            'wpcl_export_csv'             => [
            'is_premium'             => false,
            'is_premium_shortcode'   => true,
            'is_column'              => false,
            'default_value'          => 'true',
            'default_premium_value'  => 'true',
            'column_custom_function' => '',
            'shortcode_field'        => 'export_csv',
            'shortcode_default'      => 'false',
        ],
            'wpcl_export_excel'           => [
            'is_premium'             => false,
            'is_premium_shortcode'   => true,
            'is_column'              => false,
            'default_value'          => 'true',
            'default_premium_value'  => 'true',
            'column_custom_function' => '',
            'shortcode_field'        => 'export_excel',
            'shortcode_default'      => 'false',
        ],
            'wpcl_email_all'              => [
            'is_premium'             => false,
            'is_premium_shortcode'   => true,
            'is_column'              => false,
            'default_value'          => 'true',
            'default_premium_value'  => 'true',
            'column_custom_function' => '',
            'shortcode_field'        => 'email_all',
            'shortcode_default'      => 'false',
        ],
            'wpcl_print'                  => [
            'is_premium'             => false,
            'is_premium_shortcode'   => true,
            'is_column'              => false,
            'default_value'          => 'true',
            'default_premium_value'  => 'true',
            'column_custom_function' => '',
            'shortcode_field'        => 'print',
            'shortcode_default'      => 'false',
        ],
            'wpcl_search'                 => [
            'is_premium'             => false,
            'is_premium_shortcode'   => true,
            'is_column'              => false,
            'default_value'          => true,
            'default_premium_value'  => true,
            'column_custom_function' => '',
            'shortcode_field'        => 'search',
            'shortcode_default'      => 'false',
        ],
            'wpcl_paging'                 => [
            'is_premium'             => false,
            'is_premium_shortcode'   => false,
            'is_column'              => false,
            'default_value'          => 'true',
            'default_premium_value'  => 'true',
            'column_custom_function' => '',
            'shortcode_field'        => 'paging',
            'shortcode_default'      => 'false',
        ],
            'wpcl_info'                   => [
            'is_premium'             => false,
            'is_premium_shortcode'   => false,
            'is_column'              => false,
            'default_value'          => 'true',
            'default_premium_value'  => 'true',
            'column_custom_function' => '',
            'shortcode_field'        => 'info',
            'shortcode_default'      => 'false',
        ],
        ];
        // Plugin specific settings
        // WooCommerce Subscriptions
        
        if ( class_exists( 'WC_Subscriptions' ) && wpcl_activation()->is__premium_only() ) {
            $premium_columns['wpcl_subscription_status'] = [
                'is_premium'         => true,
                'is_column'          => true,
                'default_value'      => 'no',
                'column_pretty_name' => __( 'Subscription status', 'wc-product-customer-list' ),
                'data_formatter'     => 'Wpcl_Data_Formatters::data_subscription_status',
                'shortcode_field'    => 'subscription_status',
                'shortcode_default'  => 'false',
                'is_setting'         => true,
                'setting'            => [
                'settings_order' => 2001,
                'name'           => __( 'Subscription status', 'wc-product-customer-list' ),
                'id'             => 'wpcl_subscription_status',
                'default'        => 'no',
                'type'           => 'checkbox',
                'css'            => 'min-width:300px;',
                'desc'           => __( 'Enable subscription status column', 'wc-product-customer-list' ),
            ],
            ];
            $premium_columns['wpcl_subscription_id'] = [
                'is_premium'         => true,
                'is_column'          => true,
                'default_value'      => 'no',
                'column_pretty_name' => __( 'Subscription id', 'wc-product-customer-list' ),
                'data_formatter'     => 'Wpcl_Data_Formatters::data_subscription_id',
                'shortcode_field'    => 'subscription_id',
                'shortcode_default'  => 'false',
                'is_setting'         => true,
                'setting'            => [
                'settings_order' => 2002,
                'name'           => __( 'Subscription id', 'wc-product-customer-list' ),
                'id'             => 'wpcl_subscription_id',
                'default'        => 'no',
                'type'           => 'checkbox',
                'css'            => 'min-width:300px;',
                'desc'           => __( 'Enable subscription id column', 'wc-product-customer-list' ),
            ],
            ];
            $premium_columns['wpcl_subscription_total'] = [
                'is_premium'         => true,
                'is_column'          => true,
                'default_value'      => 'no',
                'column_pretty_name' => __( 'Subscription total', 'wc-product-customer-list' ),
                'data_formatter'     => 'Wpcl_Data_Formatters::data_subscription_total',
                'shortcode_field'    => 'subscription_total',
                'shortcode_default'  => 'false',
                'is_setting'         => true,
                'setting'            => [
                'settings_order' => 2003,
                'name'           => __( 'Subscription total', 'wc-product-customer-list' ),
                'id'             => 'wpcl_subscription_total',
                'default'        => 'no',
                'type'           => 'checkbox',
                'css'            => 'min-width:300px;',
                'desc'           => __( 'Enable subscription total column', 'wc-product-customer-list' ),
            ],
            ];
            $premium_columns['wpcl_subscription_start_date'] = [
                'is_premium'         => true,
                'is_column'          => true,
                'default_value'      => 'no',
                'column_pretty_name' => __( 'Subscription start date', 'wc-product-customer-list' ),
                'data_formatter'     => 'Wpcl_Data_Formatters::data_subscription_start_date',
                'shortcode_field'    => 'subscription_start_date',
                'shortcode_default'  => 'false',
                'is_setting'         => true,
                'setting'            => [
                'settings_order' => 2004,
                'name'           => __( 'Subscription start date', 'wc-product-customer-list' ),
                'id'             => 'wpcl_subscription_start_date',
                'default'        => 'no',
                'type'           => 'checkbox',
                'css'            => 'min-width:300px;',
                'desc'           => __( 'Enable subscription start date column', 'wc-product-customer-list' ),
            ],
            ];
            $premium_columns['wpcl_subscription_trial_end'] = [
                'is_premium'         => true,
                'is_column'          => true,
                'default_value'      => 'no',
                'column_pretty_name' => __( 'Subscription trial end', 'wc-product-customer-list' ),
                'data_formatter'     => 'Wpcl_Data_Formatters::data_subscription_trial_end',
                'shortcode_field'    => 'subscription_trial_end',
                'shortcode_default'  => 'false',
                'is_setting'         => true,
                'setting'            => [
                'settings_order' => 2005,
                'name'           => __( 'Subscription trial end', 'wc-product-customer-list' ),
                'id'             => 'wpcl_subscription_trial_end',
                'default'        => 'no',
                'type'           => 'checkbox',
                'css'            => 'min-width:300px;',
                'desc'           => __( 'Enable subscription trial end column', 'wc-product-customer-list' ),
            ],
            ];
            $premium_columns['wpcl_subscription_end_date'] = [
                'is_premium'         => true,
                'is_column'          => true,
                'default_value'      => 'no',
                'column_pretty_name' => __( 'Subscription end date', 'wc-product-customer-list' ),
                'data_formatter'     => 'Wpcl_Data_Formatters::data_subscription_end_date',
                'shortcode_field'    => 'subscription_end_date',
                'shortcode_default'  => 'false',
                'is_setting'         => true,
                'setting'            => [
                'settings_order' => 2006,
                'name'           => __( 'Subscription end date', 'wc-product-customer-list' ),
                'id'             => 'wpcl_subscription_end_date',
                'default'        => 'no',
                'type'           => 'checkbox',
                'css'            => 'min-width:300px;',
                'desc'           => __( 'Enable subscription end date column', 'wc-product-customer-list' ),
            ],
            ];
            $premium_columns['wpcl_subscription_next_payment'] = [
                'is_premium'         => true,
                'is_column'          => true,
                'default_value'      => 'no',
                'column_pretty_name' => __( 'Subscription next payment', 'wc-product-customer-list' ),
                'data_formatter'     => 'Wpcl_Data_Formatters::data_subscription_next_payment',
                'shortcode_field'    => 'subscription_next_payment',
                'shortcode_default'  => 'false',
                'is_setting'         => true,
                'setting'            => [
                'settings_order' => 2007,
                'name'           => __( 'Subscription next payment', 'wc-product-customer-list' ),
                'id'             => 'wpcl_subscription_next_payment',
                'default'        => 'no',
                'type'           => 'checkbox',
                'css'            => 'min-width:300px;',
                'desc'           => __( 'Enable subscription next payment column', 'wc-product-customer-list' ),
            ],
            ];
            $premium_columns['wpcl_subscription_last_order_date'] = [
                'is_premium'         => true,
                'is_column'          => true,
                'default_value'      => 'no',
                'column_pretty_name' => __( 'Subscription last order date', 'wc-product-customer-list' ),
                'data_formatter'     => 'Wpcl_Data_Formatters::data_subscription_last_order_date',
                'shortcode_field'    => 'subscription_last_order_date',
                'shortcode_default'  => 'false',
                'is_setting'         => true,
                'setting'            => [
                'settings_order' => 2008,
                'name'           => __( 'Subscription last order date', 'wc-product-customer-list' ),
                'id'             => 'wpcl_subscription_last_order_date',
                'default'        => 'no',
                'type'           => 'checkbox',
                'css'            => 'min-width:300px;',
                'desc'           => __( 'Enable subscription last order date column', 'wc-product-customer-list' ),
            ],
            ];
            $premium_columns['wpcl_subscription_billing_interval'] = [
                'is_premium'         => true,
                'is_column'          => true,
                'default_value'      => 'no',
                'column_pretty_name' => __( 'Subscription billing interval', 'wc-product-customer-list' ),
                'data_formatter'     => 'Wpcl_Data_Formatters::data_subscription_billing_interval',
                'shortcode_field'    => 'subscription_billing_interval',
                'shortcode_default'  => 'false',
                'is_setting'         => true,
                'setting'            => [
                'settings_order' => 2010,
                'name'           => __( 'Subscription billing interval', 'wc-product-customer-list' ),
                'id'             => 'wpcl_subscription_billing_interval',
                'default'        => 'no',
                'type'           => 'checkbox',
                'css'            => 'min-width:300px;',
                'desc'           => __( 'Enable subscription billing interval column', 'wc-product-customer-list' ),
            ],
            ];
        }
        
        if ( class_exists( 'WC_Bundles' ) && wpcl_activation()->is__premium_only() ) {
            $premium_columns['wpcl_bundled_items'] = [
                'is_premium'         => true,
                'is_column'          => true,
                'default_value'      => 'no',
                'column_pretty_name' => __( 'Bundled items', 'wc-product-customer-list' ),
                'data_formatter'     => 'Wpcl_Data_Formatters::data_bundled_items',
                'shortcode_field'    => 'bundled_items',
                'shortcode_default'  => 'false',
                'is_setting'         => true,
                'setting'            => [
                'settings_order' => 2001,
                'name'           => __( 'Bundled items (WooCommerce Product Bundles)', 'wc-product-customer-list' ),
                'id'             => 'wpcl_bundled_items',
                'default'        => 'no',
                'type'           => 'checkbox',
                'css'            => 'min-width:300px;',
                'desc'           => __( 'Enable bundled items column', 'wc-product-customer-list' ),
            ],
            ];
        }
        // WooCommerce Custom Fields (Rightpress) - Product Fields
        
        if ( class_exists( 'WCCF' ) && wpcl_activation()->is__premium_only() ) {
            $premium_columns['wpcl_rightpress_custom_fields'] = [
                'is_premium'             => true,
                'is_column'              => true,
                'is_option'              => true,
                'default_value'          => [],
                'column_custom_function' => 'Wpcl_Options::process_column_rightpress_custom_columns',
                'shortcode_field'        => 'rightpress_custom_fields',
                'shortcode_default'      => [],
                'is_setting'             => true,
                'setting'                => [
                'settings_order' => 2000,
                'name'           => __( 'Product fields (Rightpress)', 'woocommerce' ),
                'desc'           => __( 'Select one or multiple custom fields to display in the table.', 'wc-product-customer-list' ),
                'id'             => 'wpcl_rightpress_custom_fields',
                'css'            => 'min-width:300px;',
                'type'           => 'multiselect',
                'options'        => Wpcl_Settings::gather_rightpress_product_fields(),
                'desc_tip'       => true,
            ],
            ];
            $premium_columns['wpcl_rightpress_checkout_fields'] = [
                'is_premium'             => true,
                'is_column'              => true,
                'is_option'              => true,
                'default_value'          => [],
                'column_custom_function' => 'Wpcl_Options::process_column_rightpress_checkout_columns',
                'shortcode_field'        => 'rightpress_checkout_fields',
                'shortcode_default'      => [],
                'is_setting'             => true,
                'setting'                => [
                'settings_order' => 2010,
                'name'           => __( 'Checkout fields (Rightpress)', 'woocommerce' ),
                'desc'           => __( 'Select one or multiple custom fields to display in the table.', 'wc-product-customer-list' ),
                'id'             => 'wpcl_rightpress_checkout_fields',
                'css'            => 'min-width:300px;',
                'type'           => 'multiselect',
                'options'        => Wpcl_Settings::gather_rightpress_checkout_fields(),
                'desc_tip'       => true,
            ],
            ];
            $premium_columns['wpcl_rightpress_order_fields'] = [
                'is_premium'             => true,
                'is_column'              => true,
                'is_option'              => true,
                'default_value'          => [],
                'column_custom_function' => 'Wpcl_Options::process_column_rightpress_order_columns',
                'shortcode_field'        => 'rightpress_order_fields',
                'shortcode_default'      => [],
                'is_setting'             => true,
                'setting'                => [
                'settings_order' => 2015,
                'name'           => __( 'Order fields (Rightpress)', 'woocommerce' ),
                'desc'           => __( 'Select one or multiple custom fields to display in the table.', 'wc-product-customer-list' ),
                'id'             => 'wpcl_rightpress_order_fields',
                'css'            => 'min-width:300px;',
                'type'           => 'multiselect',
                'options'        => Wpcl_Settings::gather_rightpress_order_fields(),
                'desc_tip'       => true,
            ],
            ];
            $premium_columns['wpcl_consolidate_rightpress_columns'] = [
                'is_premium'             => true,
                'is_column'              => false,
                'default_value'          => 'true',
                'column_custom_function' => '',
                'shortcode_field'        => 'consolidate_rightpress_columns',
                'shortcode_default'      => 'true',
                'is_setting'             => true,
                'setting'                => [
                'settings_order' => 2030,
                'name'           => __( 'Consolidate columns with the same label', 'wc-product-customer-list' ),
                'id'             => 'wpcl_consolidate_rightpress_columns',
                'default'        => 'yes',
                'type'           => 'checkbox',
                'css'            => 'min-width:300px;',
                'desc'           => __( 'Even if they are different fields, if they are labelled the same, they will be in the same column', 'wc-product-customer-list' ),
            ],
            ];
        }
        
        return $columns;
    }
    
    public static function get_table_columns( $params, $source = 'options', $shortcode_attributes = false )
    {
        $is_premium = wpcl_activation()->is__premium_only();
        $fields = self::get_table_options();
        $columns = [];
        foreach ( $fields as $option_name => $option_values ) {
            // The same table options are used to dictate other things than columns.
            // We only want the ones with 'is_column' => true
            if ( empty($option_values['is_column']) ) {
                continue;
            }
            // Some columns are only for paying customers
            if ( $is_premium == false && !empty($option_values['is_premium']) && $option_values['is_premium'] == true ) {
                continue;
            }
            // from the options table
            
            if ( 'options' == $source ) {
                $default_value = ( isset( $option_values['default_value'] ) ? $option_values['default_value'] : '' );
                $option = get_option( $option_name, $default_value );
                
                if ( !empty($option_values['column_custom_function']) ) {
                    $data_formatter = explode( '::', $option_values['column_custom_function'] );
                    $new_columns = [];
                    // we can pass it a class' method
                    
                    if ( !empty($data_formatter[1]) ) {
                        $class = $data_formatter[0];
                        $method = $data_formatter[1];
                        if ( method_exists( $class, $method ) ) {
                            $new_columns = $class::$method( $option, $params );
                        }
                    } else {
                        // or a function
                        $function = $data_formatter[0];
                        if ( function_exists( $function ) ) {
                            $new_columns = ${$function}( $option, $params );
                        }
                    }
                    
                    // Some new columns? Let's add them to the lot
                    if ( !empty($new_columns) ) {
                        $columns = array_merge( $columns, $new_columns );
                    }
                } else {
                    $default_value = ( isset( $option_values['default_value'] ) ? $option_values['default_value'] : '' );
                    if ( get_option( $option_name, $default_value ) == 'yes' ) {
                        $columns[$option_name] = $option_values['column_pretty_name'];
                    }
                }
            
            } else {
                
                if ( 'shortcode' == $source ) {
                    if ( !isset( $option_values['shortcode_field'] ) && !isset( $option_values['shortcode_default'] ) ) {
                        continue;
                    }
                    $shortcode_field = ( isset( $option_values['shortcode_field'] ) ? $option_values['shortcode_field'] : false );
                    $shortcode_default = ( isset( $option_values['shortcode_default'] ) ? $option_values['shortcode_default'] : false );
                    $shortcode_attributes_present = isset( $shortcode_attributes[$shortcode_field] );
                    // Some columns are actually more than one column and the option needs to be specially processed
                    // if the shortcode is there, use its value, else get the default from the lookup table
                    $option = ( $shortcode_attributes_present ? $shortcode_attributes[$shortcode_field] : $shortcode_default );
                    
                    if ( !empty($option_values['column_custom_function']) ) {
                        $data_formatter = explode( '::', $option_values['column_custom_function'] );
                        $new_columns = [];
                        // we can pass it a class' method
                        
                        if ( !empty($data_formatter[1]) ) {
                            $class = $data_formatter[0];
                            $method = $data_formatter[1];
                            if ( method_exists( $class, $method ) ) {
                                $new_columns = $class::$method( $option, $params );
                            }
                        } else {
                            // or a function
                            $function = $data_formatter[0];
                            if ( function_exists( $function ) ) {
                                $new_columns = ${$function}( $option, $params );
                            }
                        }
                        
                        // Some new columns? Let's add them to the lot
                        if ( !empty($new_columns) ) {
                            $columns = array_merge( $columns, $new_columns );
                        }
                    } else {
                        $shortcode_default_show = $shortcode_default == 'true';
                        $shortcode_attributes_show = ( $shortcode_attributes_present ? $shortcode_attributes[$shortcode_field] == 'true' : false );
                        
                        if ( $shortcode_attributes_present ) {
                            // they've specified it in the shortcode
                            if ( $shortcode_attributes_show ) {
                                // they set the shortcode attribute to true
                                $columns[$option_name] = $option_values['column_pretty_name'];
                            }
                            // they specified it but set it to false -> nothing to do
                        } else {
                            // they have NOT specified it in the shortcode
                            if ( $shortcode_default_show ) {
                                // the shortcode default = show
                                $columns[$option_name] = $option_values['column_pretty_name'];
                            }
                        }
                    
                    }
                
                }
            
            }
        
        }
        return $columns;
    }
    
    public static function process_options( $source, $product_ids, $shortcode_attributes = array() )
    {
        $timing = Wpcl_Timing::getInstance();
        $timing->add_timing( 'Starting to get the options' );
        $fields = self::get_table_options();
        $product_skus = [];
        $product_titles = [];
        foreach ( $product_ids as $product_id ) {
            $product = wc_get_product( $product_id );
            
            if ( is_a( $product, 'WC_Product' ) ) {
                $product_titles[] = $product->get_title();
                $product_skus[] = $product->get_sku();
            } else {
                $product_titles[] = sprintf( __( 'Invalid product ID [%1$s]', 'wc-product-customer-list' ), $product_id );
                $product_skus[] = sprintf( __( 'Invalid product ID [%1$s]', 'wc-product-customer-list' ), $product_id );
            }
        
        }
        $options = [
            'productSku'   => implode( ', ', $product_skus ),
            'productTitle' => implode( ', ', $product_titles ),
            'productIds'   => $product_ids,
        ];
        $timing->add_timing( 'Processing the option fields' );
        foreach ( $fields as $option_name => $option_values ) {
            $is_option = isset( $option_values['is_option'] ) && $option_values['is_option'] == true;
            $is_column = isset( $option_values['is_column'] ) && $option_values['is_column'] == true;
            // we're only looking for options here
            if ( !$is_option && $is_column ) {
                continue;
            }
            $is_premium_field = empty($option_values['is_premium']) == false && in_array( $option_values['is_premium'], [ true, 'true', 0 ], true );
            
            if ( 'shortcode' == $source ) {
                $is_premium_shortcode = empty($option_values['is_premium_shortcode']) == false && in_array( $option_values['is_premium_shortcode'], [ true, 'true', 0 ], true );
                // get the default
                
                if ( isset( $option_values['shortcode_default'] ) ) {
                    $options[$option_name] = $option_values['shortcode_default'];
                } else {
                    
                    if ( isset( $option_values['default_value'] ) ) {
                        $options[$option_name] = $option_values['default_value'];
                    } else {
                        continue;
                    }
                
                }
                
                // maybe override it with the shortcode atts
                
                if ( isset( $option_values['shortcode_field'] ) && isset( $shortcode_attributes[$option_values['shortcode_field']] ) ) {
                    $shortcode_attribute = $shortcode_attributes[$option_values['shortcode_field']];
                    // Array options (that might come in as imploded arrays= > strings)
                    
                    if ( in_array( $option_name, [
                        'wpcl_order_status_select',
                        'wpcl_rightpress_custom_fields',
                        'wpcl_rightpress_checkout_fields',
                        'wpcl_rightpress_order_fields',
                        'wpcl_custom_fields',
                        'wpcl_user_meta',
                        'user_meta',
                        'usermeta'
                    ] ) && is_string( $shortcode_attribute ) ) {
                        $shortcode_attribute = explode( ',', $shortcode_attribute );
                        $shortcode_attribute = array_map( function ( $array_element ) {
                            return trim( $array_element );
                        }, $shortcode_attribute );
                    }
                    
                    // some might be integers but coming in as strings
                    if ( in_array( $option_name, [ 'wpcl_limit' ] ) && is_string( $shortcode_attribute ) ) {
                        $shortcode_attribute = intval( $shortcode_attribute );
                    }
                    // override if not a premium field
                    if ( !$is_premium_shortcode ) {
                        $options[$option_name] = $shortcode_attribute;
                    }
                    // override if it's a premium field, but only IF they're premium
                    // users (should be 'else' but not sure how the Freemium plugin compiles
                    if ( wpcl_activation()->is__premium_only() && $is_premium_shortcode ) {
                        $options[$option_name] = $shortcode_attribute;
                    }
                    if ( !wpcl_activation()->is__premium_only() && $is_premium_shortcode ) {
                        $options[$option_name] = false;
                    }
                }
            
            } else {
                // get the default
                $default_value = $option_values['default_value'];
                $options[$option_name] = $default_value;
                // if it's not a premium field or it is && they're premium users...
                if ( !$is_premium_field || wpcl_activation()->is__premium_only() && $is_premium_field ) {
                    $options[$option_name] = get_option( $option_name, $default_value );
                }
            }
            
            // cleaning up binary values
            
            if ( in_array( $options[$option_name], [ 'true', true, 'yes' ], true ) ) {
                $options[$option_name] = true;
            } else {
                if ( in_array( $options[$option_name], [ 'false', false, 'no' ], true ) ) {
                    $options[$option_name] = false;
                }
            }
        
        }
        if ( 'options' == $source ) {
            $options['wpcl_table_title'] = implode( ', ', $product_titles );
        }
        $timing->add_timing( 'Done processing the options' );
        return $options;
    }
    
    public static function process_column_wpcl_custom_fields( $fields, $params )
    {
        $columns = [];
        $custom_fields = [];
        // shortcodes will be sending us arrays as strings.
        // Let's convert and clean them
        
        if ( !empty($fields) && is_string( $fields ) ) {
            $fields = explode( ',', $fields );
            $fields = array_map( function ( $array_element ) {
                return trim( $array_element );
            }, $fields );
        }
        
        if ( !empty($fields) && is_array( $fields ) ) {
            foreach ( $fields as $custom_field ) {
                $column_key = 'wpcl_custom_field__' . sanitize_title( $custom_field );
                $columns[$column_key] = $custom_field;
                $custom_fields[] = $column_key;
            }
        }
        //		if ( ! empty( $custom_fields ) ) {
        //			$columns['wpcl_custom_fields'] = $custom_fields;
        //		}
        return $columns;
    }
    
    public static function process_column_wpcl_user_meta( $user_meta_keys, $params )
    {
        $columns = [];
        $user_meta_columns = [];
        // shortcodes will be sending us arrays as strings.
        // Let's convert and clean them
        
        if ( !empty($user_meta_keys) && is_string( $user_meta_keys ) ) {
            $user_meta_keys = explode( ',', $user_meta_keys );
            $user_meta_keys = array_map( function ( $array_element ) {
                return trim( $array_element );
            }, $user_meta_keys );
        }
        
        if ( !empty($user_meta_keys) && is_array( $user_meta_keys ) ) {
            foreach ( $user_meta_keys as $user_meta_key ) {
                $column_key = 'wpcl_user_meta__' . sanitize_title( $user_meta_key );
                $columns[$column_key] = $user_meta_key;
                //'[User Meta] ' . $user_meta_key;
                $user_meta_columns[] = $user_meta_key;
            }
        }
        //		if ( ! empty( $user_meta_columns ) ) {
        //			$columns['wpcl_user_meta'] = $user_meta_columns;
        //		}
        return $columns;
    }
    
    public static function process_column_wpcl_wootours( $option, $params )
    {
        
        if ( !empty($option) && 'yes' == $option ) {
            return $columns['we_attendee'] = __( 'Attendee information', 'wc-product-customer-list' );
        } else {
            return false;
        }
    
    }
    
    public static function process_column_rightpress_custom_columns( $wanted_rightpress_columns, $params )
    {
        // shortcodes will be sending us arrays as strings.
        // Let's convert and clean them
        
        if ( !empty($wanted_rightpress_columns) && is_string( $wanted_rightpress_columns ) ) {
            $wanted_rightpress_columns = explode( ',', $wanted_rightpress_columns );
            $wanted_rightpress_columns = array_map( function ( $array_element ) {
                return trim( $array_element );
            }, $wanted_rightpress_columns );
        }
        
        $consolidate_rightpress_columns = !empty($params['wpcl_consolidate_rightpress_columns']) && $params['wpcl_consolidate_rightpress_columns'] == true;
        $rightpress_columns = $columns = [];
        if ( !empty($wanted_rightpress_columns) && class_exists( 'WCCF_Field_Controller' ) ) {
            foreach ( $wanted_rightpress_columns as $rightpress_custom_field_key ) {
                
                if ( $field = WCCF_Field_Controller::get_field_by_key( 'product_field', $rightpress_custom_field_key, true ) ) {
                    $rightpress_field_label = $field->get_label();
                    
                    if ( $consolidate_rightpress_columns ) {
                        $column_key = 'rightpress_cf__' . sanitize_title( $rightpress_field_label );
                    } else {
                        $column_key = 'rightpress_cf__' . sanitize_title( $rightpress_custom_field_key ) . '_' . sanitize_title( $rightpress_field_label );
                    }
                    
                    if ( !in_array( $column_key, $rightpress_columns ) ) {
                        $rightpress_columns[] = $column_key;
                    }
                    if ( !in_array( $column_key, $columns ) ) {
                        $columns[$column_key] = $rightpress_field_label;
                    }
                }
            
            }
        }
        if ( !empty($rightpress_columns) ) {
            $columns['wpcl_rightpress_custom_fieldkeys'] = $rightpress_columns;
        }
        return $columns;
    }
    
    public static function process_column_rightpress_checkout_columns( $wanted_rightpress_columns, $params )
    {
        // shortcodes will be sending us arrays as strings.
        // Let's convert and clean them
        
        if ( !empty($wanted_rightpress_columns) && is_string( $wanted_rightpress_columns ) ) {
            $wanted_rightpress_columns = explode( ',', $wanted_rightpress_columns );
            $wanted_rightpress_columns = array_map( function ( $array_element ) {
                return trim( $array_element );
            }, $wanted_rightpress_columns );
        }
        
        $consolidate_rightpress_columns = !empty($params['wpcl_consolidate_rightpress_columns']) && $params['wpcl_consolidate_rightpress_columns'] == true;
        $rightpress_columns = $columns = [];
        if ( !empty($wanted_rightpress_columns) && class_exists( 'WCCF_Field_Controller' ) ) {
            foreach ( $wanted_rightpress_columns as $rightpress_custom_field_key ) {
                
                if ( $field = WCCF_Field_Controller::get_field_by_key( 'checkout_field', $rightpress_custom_field_key, true ) ) {
                    $rightpress_field_label = $field->get_label();
                    
                    if ( $consolidate_rightpress_columns ) {
                        $column_key = 'rightpress_co__' . sanitize_title( $rightpress_field_label );
                    } else {
                        $column_key = 'rightpress_co__' . sanitize_title( $rightpress_custom_field_key ) . '_' . sanitize_title( $rightpress_field_label );
                    }
                    
                    if ( !in_array( $column_key, $rightpress_columns ) ) {
                        $rightpress_columns[] = $column_key;
                    }
                    if ( !in_array( $column_key, $columns ) ) {
                        $columns[$column_key] = $rightpress_field_label;
                    }
                }
            
            }
        }
        if ( !empty($rightpress_columns) ) {
            $columns['wpcl_rightpress_checkout_fieldkeys'] = $rightpress_columns;
        }
        return $columns;
    }
    
    public static function process_column_rightpress_order_columns( $wanted_rightpress_columns, $params )
    {
        // shortcodes will be sending us arrays as strings.
        // Let's convert and clean them
        
        if ( !empty($wanted_rightpress_columns) && is_string( $wanted_rightpress_columns ) ) {
            $wanted_rightpress_columns = explode( ',', $wanted_rightpress_columns );
            $wanted_rightpress_columns = array_map( function ( $array_element ) {
                return trim( $array_element );
            }, $wanted_rightpress_columns );
        }
        
        $consolidate_rightpress_columns = !empty($params['wpcl_consolidate_rightpress_columns']) && $params['wpcl_consolidate_rightpress_columns'] == true;
        $rightpress_columns = $columns = [];
        if ( !empty($wanted_rightpress_columns) && class_exists( 'WCCF_Field_Controller' ) ) {
            foreach ( $wanted_rightpress_columns as $rightpress_custom_field_key ) {
                
                if ( $field = WCCF_Field_Controller::get_field_by_key( 'order_field', $rightpress_custom_field_key, true ) ) {
                    $rightpress_field_label = $field->get_label();
                    
                    if ( $consolidate_rightpress_columns ) {
                        $column_key = 'rightpress_of__' . sanitize_title( $rightpress_field_label );
                    } else {
                        $column_key = 'rightpress_of__' . sanitize_title( $rightpress_custom_field_key ) . '_' . sanitize_title( $rightpress_field_label );
                    }
                    
                    if ( !in_array( $column_key, $rightpress_columns ) ) {
                        $rightpress_columns[] = $column_key;
                    }
                    if ( !in_array( $column_key, $columns ) ) {
                        $columns[$column_key] = $rightpress_field_label;
                    }
                }
            
            }
        }
        if ( !empty($rightpress_columns) ) {
            $columns['wpcl_rightpress_order_fieldkeys'] = $rightpress_columns;
        }
        return $columns;
    }
    
    public static function get_order_statuses_options()
    {
        // Get all available statuses
        $statuses = [];
        foreach ( get_post_stati( [
            'show_in_admin_status_list' => true,
        ], 'objects' ) as $status ) {
            if ( !in_array( $status->name, [
                'publish',
                'draft',
                'pending',
                'trash',
                'future',
                'private',
                'auto-draft',
                'acf-disabled',
                'wc-checkout-draft'
            ] ) ) {
                $statuses[$status->name] = $status->label;
            }
        }
        return $statuses;
    }
    
    public static function test_options()
    {
        $fields = self::get_table_options();
        foreach ( $fields as $field_name => $field_info ) {
            echo  '<div style="height: 10px;"></div>' ;
            
            if ( !empty($field_info['column_pretty_name']) ) {
                echo  $field_info['column_pretty_name'] . '<br>' ;
            } else {
                echo  $field_name . '<br>' ;
            }
            
            
            if ( empty($field_info['shortcode_field']) ) {
                echo  '<strong style="color: red; font-weight: bold;">NO SHORTCODE ENTERED</strong>' . '<br>' ;
            } else {
                echo  '<code>' . $field_name . ' => ' . $field_info['shortcode_field'] . '</code>' . '<br>' ;
            }
            
            if ( !isset( $field_info['default_value'] ) ) {
                echo  '<strong style="color: red; font-weight: bold;">Missing Default value</strong>' . '<br>' ;
            }
            
            if ( !empty($field_info['is_setting']) ) {
                if ( empty($field_info['setting']) ) {
                    echo  '<strong style="color: red; font-weight: bold;">Missing setting</strong>' . '<br>' ;
                }
                if ( empty($field_info['setting']['settings_order']) ) {
                    echo  '<strong style="color: red; font-weight: bold;">Missing Settings Order</strong>' . '<br>' ;
                }
                if ( !empty($field_info['setting']) && $field_info['setting']['id'] != $field_name ) {
                    echo  '<strong style="color: red; font-weight: bold;">Mismatch between ID and Setting ID</strong>' . '<br>' ;
                }
            }
        
        }
    }

}