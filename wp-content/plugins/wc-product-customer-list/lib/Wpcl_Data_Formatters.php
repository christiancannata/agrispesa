<?php

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
class Wpcl_Data_Formatters
{
    public function __construct()
    {
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function default_formatter(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $clean_column = str_replace( [ 'wpcl_custom_field__', 'wpcl_user_meta__' ], '', $column );
        $value = maybe_unserialize( $order->get_meta( $clean_column, true ) );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_order_number(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order_id;
        // if the user has permissions to edit orders, link the order number to its order.
        if ( is_admin() || is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
            $value = '<a href="' . admin_url( 'post.php' ) . '?post=' . $order_id . '&action=edit" target="_blank">' . $order->get_order_number() . '</a>';
        }
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_order_date(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = date_format( $order->get_date_created(), 'Y-m-d' );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_product_id(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $product_id;
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_product_sku(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $product->get_sku();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_billing_first_name(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_billing_first_name();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_billing_last_name(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_billing_last_name();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_billing_company(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_billing_company();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_billing_email(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_billing_email();
        $value = '<a href="mailto:' . sanitize_email( $value ) . '">' . $value . '</a>';
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_billing_phone(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_billing_phone();
        $value = '<a href="tel:' . $value . '">' . $value . '</a>';
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_billing_address_1(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_billing_address_1();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_billing_address_2(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_billing_address_2();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_billing_city(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_billing_city();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_billing_state(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_billing_state();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_billing_postalcode(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_billing_postcode();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_billing_country(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_billing_country();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_shipping_first_name(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_shipping_first_name();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_shipping_last_name(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_shipping_last_name();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_shipping_company(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_shipping_company();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_shipping_address_1(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_shipping_address_1();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_shipping_address_2(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_shipping_address_2();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_shipping_city(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_shipping_city();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_shipping_state(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_shipping_state();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_shipping_postalcode(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_shipping_postcode();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_shipping_country(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_shipping_country();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_customer_message(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_customer_note();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_customer_id(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $customer_id;
        if ( empty($value) ) {
            $value = '';
        }
        if ( $customer_id && is_user_logged_in() ) {
            $value = '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $value . '" target="_blank">' . $value . '</a>';
        }
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_customer_login(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        
        if ( empty($customer) == false && is_a( $customer, 'WP_User' ) ) {
            $value = get_admin_url() . 'user-edit.php?user_id=' . $customer_id;
        } else {
            $value = '';
        }
        
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_customer_username(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        
        if ( empty($customer) == false && is_a( $customer, 'WP_User' ) ) {
            $value = $customer->user_login;
            if ( !empty($params['wpcl_customer_username_link']) && is_user_logged_in() ) {
                $value = '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $customer_id . '" target="_blank">' . $value . '</a>';
            }
        } else {
            $value = '';
        }
        
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_customer_display_name(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        
        if ( empty($customer) == false && is_a( $customer, 'WP_User' ) ) {
            $value = $customer->display_name;
            if ( !empty($params['wpcl_customer_username_link']) && is_user_logged_in() ) {
                $value = '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $customer_id . '" target="_blank">' . $value . '</a>';
            }
        } else {
            $value = '';
        }
        
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_customer_avatar(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        
        if ( empty($customer) == false && is_a( $customer, 'WP_User' ) ) {
            $value = get_avatar( $customer_id, 96 );
        } else {
            $value = '';
        }
        
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_order_status(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = wc_get_order_status_name( $order->get_status() );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_order_payment(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_payment_method_title();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_order_shipping(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_shipping_method();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_order_coupon(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = implode( ', ', $order->get_coupon_codes() );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_order_variations(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $variations = $order->get_item( $item_id );
        $value = [];
        // data gathering BEGIN
        
        if ( method_exists( $variations, 'get_variation_id' ) ) {
            $variation_id = $variations->get_variation_id();
            $variation = new WC_Product_Variation( $variation_id );
            
            if ( is_a( $variation, 'WC_Product_Variation' ) ) {
                $variation_name = $variation->get_name();
                if ( !empty($variation_name) ) {
                    $value[] = [
                        'label' => __( 'Variation Name', 'wc-product-customer-list' ),
                        'value' => $variation->get_name(),
                    ];
                }
            }
        
        }
        
        $skipped_attributes = [ '_reduced_stock' ];
        foreach ( $variations->get_meta_data() as $itemvariation ) {
            if ( !is_array( $itemvariation->value ) && in_array( $itemvariation->key, $skipped_attributes ) === false ) {
                $value[] = [
                    'label' => wc_attribute_label( $itemvariation->key ),
                    'value' => wc_attribute_label( $itemvariation->value ),
                ];
            }
        }
        // data gathering END
        // data display BEGIN
        
        if ( !empty($value) ) {
            $value_out = '<span style="max-height: 50px; overflow-y: auto; display: block;">';
            foreach ( $value as $itemvariation ) {
                if ( !is_array( $itemvariation['value'] ) ) {
                    $value_out .= '<strong>' . $itemvariation['label'] . '</strong>: &nbsp;' . $itemvariation['value'] . '<br />';
                }
            }
            $value_out .= '</span>';
            $value = $value_out;
        } else {
            // the new format always expects a string for column data
            $value = '';
        }
        
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_order_total(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_formatted_order_total();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_order_tax_total(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $order->get_total_tax();
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_order_qty(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $value = $quantity;
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_user_meta_last_name(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        
        if ( is_a( $customer, 'WP_User' ) ) {
            $value = $customer->last_name;
        } else {
            $value = '';
        }
        
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_user_meta_rich_editing(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        
        if ( is_a( $customer, 'WP_User' ) ) {
            $value = $customer->rich_editing;
        } else {
            $value = '';
        }
        
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_customer_username_link(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        
        if ( !empty($customer_id) ) {
            $value = get_edit_user_link( $customer_id );
        } else {
            $value = '';
        }
        
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /*
     * The next section is for special data formatters / options
     */
    //     _____          _                    ______ _      _     _
    //    / ____|        | |                  |  ____(_)    | |   | |
    //   | |    _   _ ___| |_ ___  _ __ ___   | |__   _  ___| | __| |___
    //   | |   | | | / __| __/ _ \| '_ ` _ \  |  __| | |/ _ \ |/ _` / __|
    //   | |___| |_| \__ \ || (_) | | | | | | | |    | |  __/ | (_| \__ \
    //    \_____\__,_|___/\__\___/|_| |_| |_| |_|    |_|\___|_|\__,_|___/
    //
    //  replaced by smaller calls
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_custom_fields_all(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $custom_fields = ( !empty($params['wpcl_custom_fields']) ? $params['wpcl_custom_fields'] : false );
        if ( empty($custom_fields) ) {
            return [
                'current_row' => $current_row,
                'columns'     => $columns,
            ];
        }
        // coming from shortcodes, we might have arrays as strings
        
        if ( is_string( $custom_fields ) ) {
            $custom_fields = explode( ',', $custom_fields );
            $custom_fields = array_map( function ( $array_element ) {
                return trim( $array_element );
            }, $custom_fields );
        }
        
        foreach ( $custom_fields as $custom_field_key => $custom_field ) {
            $sanitized_column_name = 'wpcl_custom_field__' . sanitize_title( $custom_field );
            
            if ( isset( $columns[$sanitized_column_name] ) ) {
                $current_row[$sanitized_column_name] = maybe_unserialize( $order->get_meta( $custom_field, true ) );
                /*
                 * We've added the possibility to create your own data_formatters for custom columns.
                 *
                 * You'll need to adjust the table options with the 'wcpl_options' filter hook.
                 *
                 * Let's say you have activate a custom column field named '_my_custom_column',
                 * you'll need to create a function or static method inside a class that has that is called
                 * something like my_custom_columns_formatter($source, $params, $order_id, $order, $item_id, $product_id, $customer_id, $split_rows, $quantity )
                 * Yes, it must have all those parameters.
                 *
                 * Then hook into the 'wcpl_options' filter and add '_my_custom_column' as a key with the data_formatter property
                 * $table_options[ 'wpcl_custom_field__my_custom_column' ] => [ 'data_formatter' => 'my_custom_columns_formatter' ]
                 *
                 */
                
                if ( empty($table_options[$sanitized_column_name]['data_formatter']) == false ) {
                    $data_formatter = explode( '::', $table_options[$sanitized_column_name]['data_formatter'] );
                    // we can pass it a class' method
                    
                    if ( !empty($data_formatter[1]) ) {
                        $class = $data_formatter[0];
                        $method = $data_formatter[1];
                        if ( method_exists( $class, $method ) ) {
                            $current_row = $class::$method(
                                $column,
                                $source,
                                $params,
                                $columns,
                                $order_id,
                                $order,
                                $item_id,
                                $product,
                                $product_id,
                                $customer_id,
                                $customer,
                                $quantity,
                                $current_row
                            );
                        }
                    } else {
                        $function = $data_formatter[0];
                        if ( function_exists( $function ) ) {
                            $current_row = $function(
                                $column,
                                $source,
                                $params,
                                $columns,
                                $order_id,
                                $order,
                                $item_id,
                                $product,
                                $product_id,
                                $customer_id,
                                $customer,
                                $quantity,
                                $current_row
                            );
                        }
                    }
                
                } else {
                    
                    if ( is_array( $current_row[$sanitized_column_name] ) ) {
                        $string_version = '';
                        foreach ( $current_row[$sanitized_column_name] as $key => $value ) {
                            $string_version .= '<span class="key">' . $key . '</span> : <span class="value">' . $value . '</span><br>';
                        }
                        $current_row[$sanitized_column_name] = '<div style="max-height: 50px; overflow: auto;">' . $string_version . '</div>';
                    }
                
                }
            
            }
        
        }
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_custom_fields(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $cleaned_field_name = str_replace( 'wpcl_custom_field__', '', $column );
        $sanitized_column_name = 'wpcl_custom_field__' . sanitize_title( $cleaned_field_name );
        $current_row[$sanitized_column_name] = maybe_unserialize( $order->get_meta( $cleaned_field_name, true ) );
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    //    _    _                 __  __      _
    //   | |  | |               |  \/  |    | |
    //   | |  | |___  ___ _ __  | \  / | ___| |_ __ _
    //   | |  | / __|/ _ \ '__| | |\/| |/ _ \ __/ _` |
    //   | |__| \__ \  __/ |    | |  | |  __/ || (_| |
    //    \____/|___/\___|_|    |_|  |_|\___|\__\__,_|
    //
    // not really used. broken down into smaller calls
    //
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_user_meta_all(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $user_meta_keys = ( !empty($params['wpcl_user_meta']) ? $params['wpcl_user_meta'] : false );
        if ( empty($user_meta_keys) ) {
            return [
                'current_row' => $current_row,
                'columns'     => $columns,
            ];
        }
        // coming from shortcodes, we might have arrays as strings
        
        if ( is_string( $user_meta_keys ) ) {
            $user_meta_keys = explode( ',', $user_meta_keys );
            $user_meta_keys = array_map( function ( $array_element ) {
                return trim( $array_element );
            }, $user_meta_keys );
        }
        
        foreach ( $user_meta_keys as $user_key => $user_meta_key ) {
            $sanitized_column_name = 'wpcl_user_meta__' . sanitize_title( $user_meta_key );
            
            if ( isset( $columns[$sanitized_column_name] ) && $customer_id ) {
                $current_row[$sanitized_column_name] = get_user_meta( $customer_id, $user_meta_key, true );
                /*
                 * We've added the possibility to create your own data_formatters for custom columns.
                 *
                 * You'll need to adjust the table options with the 'wcpl_options' filter hook.
                 *
                 * Let's say you have activated a custom column field named '_my_custom_column',
                 * you'll need to create a function or static method inside a class that has that is called
                 * something like my_custom_columns_formatter($source, $params, $order_id, $order, $item_id, $product_id, $customer_id, $split_rows, $quantity )
                 * Yes, it must have all those parameters.
                 *
                 * Then hook into the 'wcpl_options' filter and add '_my_custom_column' as a key with the data_formatter property
                 * $table_options[ 'wpcl_user_meta__my_custom_column' ] => [ 'data_formatter' => 'my_custom_columns_formatter' ]
                 *
                 */
                
                if ( empty($table_options[$sanitized_column_name]['data_formatter']) == false ) {
                    $data_formatter = explode( '::', $table_options[$sanitized_column_name]['data_formatter'] );
                    // we can pass it a class' method
                    
                    if ( !empty($data_formatter[1]) ) {
                        $class = $data_formatter[0];
                        $method = $data_formatter[1];
                        if ( method_exists( $class, $method ) ) {
                            $current_row = $class::$method(
                                $column,
                                $source,
                                $params,
                                $columns,
                                $order_id,
                                $order,
                                $item_id,
                                $product,
                                $product_id,
                                $customer_id,
                                $customer,
                                $quantity,
                                $current_row
                            );
                        }
                    } else {
                        $function = $data_formatter[0];
                        if ( function_exists( $function ) ) {
                            $current_row = $function(
                                $column,
                                $source,
                                $params,
                                $columns,
                                $order_id,
                                $order,
                                $item_id,
                                $product,
                                $product_id,
                                $customer_id,
                                $customer,
                                $quantity,
                                $current_row
                            );
                        }
                    }
                
                }
            
            } else {
                // We need to add the column, even if empty, or DataTables gets angry.
                $current_row[$sanitized_column_name] = '';
            }
        
        }
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_user_meta(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $cleaned_field_name = str_replace( 'wpcl_user_meta__', '', $column );
        
        if ( !empty($customer_id) ) {
            $current_row[$column] = get_user_meta( $customer_id, $cleaned_field_name, true );
        } else {
            $current_row[$column] = '';
        }
        
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    //   __          __           _______
    //   \ \        / /          |__   __|
    //    \ \  /\  / /__   ___      | | ___  _   _ _ __ ___
    //     \ \/  \/ / _ \ / _ \     | |/ _ \| | | | '__/ __|
    //      \  /\  / (_) | (_) |    | | (_) | |_| | |  \__ \
    //       \/  \/ \___/ \___/     |_|\___/ \__,_|_|  |___/
    //
    //
    /**
     * /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_woo_tours(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $woo_tours_out = '<p>';
        $id = $product_id;
        $current_item = new WC_Order_Item_Product( $item_id );
        $order_items = $order->get_items();
        $n = 0;
        $find = 0;
        foreach ( $order_items as $items_key => $items_value ) {
            $n++;
            
            if ( $items_value->get_id() == $item_id ) {
                $find = 1;
                break;
            }
        
        }
        
        if ( $find == 0 ) {
        } else {
            $value_id = $id . '_' . $n;
            $value_id = apply_filters( 'we_attendee_key', $value_id, $current_item );
            $metadata = $order->get_meta( 'att_info-' . $value_id, true );
            if ( $metadata == '' ) {
                $metadata = $order->get_meta( 'att_info-' . $id, true );
            }
            
            if ( $metadata != '' ) {
                $metadata = explode( "][", $metadata );
                
                if ( !empty($metadata) ) {
                    $i = 0;
                    foreach ( $metadata as $item ) {
                        $i++;
                        $item = explode( "||", $item );
                        $f_name = ( isset( $item[1] ) && $item[1] != '' ? $item[1] : '' );
                        $l_name = ( isset( $item[2] ) && $item[2] != '' ? $item[2] : '' );
                        if ( $i > 0 ) {
                            $woo_tours_out .= ' ';
                        }
                        $woo_tours_out .= esc_html__( 'User ', 'wc-product-customer-list' ) . '(' . $i . ') <br>';
                        if ( $f_name != '' || $l_name != '' ) {
                            $woo_tours_out .= '<span><strong>' . esc_html__( 'Name: ', 'wc-product-customer-list' ) . '</strong>' . $f_name . ' ' . $l_name . '</span><br>';
                        }
                        if ( isset( $item[0] ) && $item[0] != '' ) {
                            $woo_tours_out .= '<span><strong>' . esc_html__( 'Email: ', 'wc-product-customer-list' ) . '</strong>' . $item[0] . '</span><br>';
                        }
                    }
                }
            
            }
        
        }
        
        $woo_tours_out .= '</p>';
        $current_row['wpcl_wootours'] = $woo_tours_out;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    //    _____  _       _     _   _____
    //   |  __ \(_)     | |   | | |  __ \
    //   | |__) |_  __ _| |__ | |_| |__) | __ ___  ___ ___
    //   |  _  /| |/ _` | '_ \| __|  ___/ '__/ _ \/ __/ __|
    //   | | \ \| | (_| | | | | |_| |   | | |  __/\__ \__ \
    //   |_|  \_\_|\__, |_| |_|\__|_|   |_|  \___||___/___/
    //              __/ |
    //             |___/
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_rightpress(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $rightpress_custom_fields = ( !empty($params["wpcl_rightpress_custom_fields"]) ? $params["wpcl_rightpress_custom_fields"] : false );
        if ( empty($rightpress_custom_fields) || !is_array( $rightpress_custom_fields ) ) {
            return [
                'current_row' => $current_row,
                'columns'     => $columns,
            ];
        }
        if ( class_exists( 'WCCF' ) == false || class_exists( 'WCCF_WC_Order_Item' ) == false || method_exists( 'WCCF_WC_Order_Item', 'get_instance' ) == false || class_exists( 'WCCF_Field_Controller' ) == false || method_exists( 'WCCF_Field_Controller', 'get_field_by_key' ) == false ) {
            return [
                'current_row' => $current_row,
                'columns'     => $columns,
            ];
        }
        $wccf_wc_order_item_controller = WCCF_WC_Order_Item::get_instance();
        $item = $order->get_item( $item_id );
        $display_values = $wccf_wc_order_item_controller->get_display_values_from_order_item_meta( $item['item_meta'], $item->get_product() );
        $right_press_info = [];
        $right_press_info_cleaned = [];
        $consolidate_rightpress_columns = isset( $params['wpcl_consolidate_rightpress_columns'] ) && $params['wpcl_consolidate_rightpress_columns'] == true;
        foreach ( $rightpress_custom_fields as $rightpress_custom_field_key ) {
            if ( $item['product_id'] == $product_id ) {
                
                if ( $field = WCCF_Field_Controller::get_field_by_key( 'product_field', $rightpress_custom_field_key, true ) ) {
                    $rightpress_field_label = $field->get_label();
                    $data_field_key = 'wccf_pf_' . $rightpress_custom_field_key;
                    
                    if ( $consolidate_rightpress_columns ) {
                        $column_key = 'rightpress_cf__' . sanitize_title( $rightpress_field_label );
                    } else {
                        $column_key = 'rightpress_cf__' . sanitize_title( $rightpress_custom_field_key ) . '_' . sanitize_title( $rightpress_field_label );
                    }
                    
                    if ( !isset( $columns[$column_key] ) ) {
                        $columns[$column_key] = $rightpress_field_label;
                    }
                    foreach ( $display_values as $display_value ) {
                        if ( $display_value['key'] === $data_field_key ) {
                            $current_row[$column_key][] = $display_value['value'];
                        }
                    }
                    if ( !isset( $current_row[$column_key] ) ) {
                        $current_row[$column_key] = [];
                    }
                }
            
            }
        }
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_rightpress_checkout(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $rightpress_custom_fields = ( !empty($params["wpcl_rightpress_checkout_fields"]) ? $params["wpcl_rightpress_checkout_fields"] : false );
        if ( empty($rightpress_custom_fields) || !is_array( $rightpress_custom_fields ) ) {
            return [
                'current_row' => $current_row,
                'columns'     => $columns,
            ];
        }
        if ( class_exists( 'WCCF' ) == false || class_exists( 'WCCF_WC_Order_Item' ) == false || method_exists( 'WCCF_WC_Order_Item', 'get_instance' ) == false || class_exists( 'WCCF_Field_Controller' ) == false || method_exists( 'WCCF_Field_Controller', 'get_field_by_key' ) == false || class_exists( 'WCCF_Checkout_Field_Controller' ) == false || method_exists( 'WCCF_Checkout_Field_Controller', 'get_all' ) == false || class_exists( 'WCCF_WC_Order' ) == false || method_exists( 'WCCF_WC_Order', 'get_instance' ) == false ) {
            return [
                'current_row' => $current_row,
                'columns'     => $columns,
            ];
        }
        // Get applicable fields (do not filter them when displaying field values)
        $all_fields = WCCF_Checkout_Field_Controller::get_all();
        $wccf_wc_order = WCCF_WC_Order::get_instance();
        $right_press_info = [];
        $right_press_info_cleaned = [];
        $consolidate_rightpress_columns = !empty($params['wpcl_consolidate_rightpress_columns']) && $params['wpcl_consolidate_rightpress_columns'] == true;
        foreach ( $rightpress_custom_fields as $rightpress_custom_field_key ) {
            
            if ( $field = WCCF_Field_Controller::get_field_by_key( 'checkout_field', $rightpress_custom_field_key, true ) ) {
                $rightpress_field_label = $field->get_label();
                $data_field_key = 'wccf_cf_' . $rightpress_custom_field_key;
                
                if ( $consolidate_rightpress_columns ) {
                    $column_key = 'rightpress_co__' . sanitize_title( $rightpress_field_label );
                } else {
                    $column_key = 'rightpress_co__' . sanitize_title( $rightpress_custom_field_key ) . '_' . sanitize_title( $rightpress_field_label );
                }
                
                if ( !isset( $columns[$column_key] ) ) {
                    $columns[$column_key] = $rightpress_field_label;
                }
                $display_values = $wccf_wc_order->get_field_values_from_order( $order, [ $field ] );
                foreach ( $display_values as $display_key => $display_value ) {
                    $current_row[$column_key][] = $display_value;
                }
                if ( !isset( $current_row[$column_key] ) ) {
                    $current_row[$column_key] = [];
                }
            }
        
        }
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_rightpress_order(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $rightpress_custom_fields = ( !empty($params["wpcl_rightpress_order_fields"]) ? $params["wpcl_rightpress_order_fields"] : false );
        if ( empty($rightpress_custom_fields) || !is_array( $rightpress_custom_fields ) ) {
            return [
                'current_row' => $current_row,
                'columns'     => $columns,
            ];
        }
        if ( class_exists( 'WCCF' ) == false || class_exists( 'WCCF_WC_Order_Item' ) == false || method_exists( 'WCCF_WC_Order_Item', 'get_instance' ) == false || class_exists( 'WCCF_Field_Controller' ) == false || method_exists( 'WCCF_Field_Controller', 'get_field_by_key' ) == false || class_exists( 'WCCF_Order_Field_Controller' ) == false || method_exists( 'WCCF_Order_Field_Controller', 'get_all' ) == false || class_exists( 'WCCF_WC_Order' ) == false || method_exists( 'WCCF_WC_Order', 'get_instance' ) == false ) {
            return [
                'current_row' => $current_row,
                'columns'     => $columns,
            ];
        }
        // Get applicable fields (do not filter them when displaying field values)
        $all_fields = WCCF_Order_Field_Controller::get_all();
        $wccf_wc_order = WCCF_WC_Order::get_instance();
        $right_press_info = [];
        $right_press_info_cleaned = [];
        $consolidate_rightpress_columns = !empty($params['wpcl_consolidate_rightpress_columns']) && $params['wpcl_consolidate_rightpress_columns'] == true;
        foreach ( $rightpress_custom_fields as $rightpress_custom_field_key ) {
            
            if ( $field = WCCF_Field_Controller::get_field_by_key( 'order_field', $rightpress_custom_field_key, true ) ) {
                $rightpress_field_label = $field->get_label();
                $data_field_key = 'wccf_of_' . $rightpress_custom_field_key;
                
                if ( $consolidate_rightpress_columns ) {
                    $column_key = 'rightpress_of__' . sanitize_title( $rightpress_field_label );
                } else {
                    $column_key = 'rightpress_of__' . sanitize_title( $rightpress_custom_field_key ) . '_' . sanitize_title( $rightpress_field_label );
                }
                
                if ( !isset( $columns[$column_key] ) ) {
                    $columns[$column_key] = $rightpress_field_label;
                }
                $display_values = $wccf_wc_order->get_field_values_from_order( $order, [ $field ] );
                foreach ( $display_values as $display_key => $display_value ) {
                    $current_row[$column_key][] = $display_value;
                }
                if ( !isset( $current_row[$column_key] ) ) {
                    $current_row[$column_key] = [];
                }
            }
        
        }
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    //   __          __          _____
    //   \ \        / /         / ____|
    //    \ \  /\  / /__   ___ | |     ___  _ __ ___  _ __ ___   ___ _ __ ___ ___
    //     \ \/  \/ / _ \ / _ \| |    / _ \| '_ ` _ \| '_ ` _ \ / _ \ '__/ __/ _ \
    //      \  /\  / (_) | (_) | |___| (_) | | | | | | | | | | |  __/ | | (_|  __/
    //     __\/_ \/ \___/ \___/ \_____\___/|_| |_| |_|_| |_| |_|\___|_|  \___\___|
    //    / ____|     | |                 (_)     | | (_)
    //   | (___  _   _| |__  ___  ___ _ __ _ _ __ | |_ _  ___  _ __  ___
    //    \___ \| | | | '_ \/ __|/ __| '__| | '_ \| __| |/ _ \| '_ \/ __|
    //    ____) | |_| | |_) \__ \ (__| |  | | |_) | |_| | (_) | | | \__ \
    //   |_____/ \__,_|_.__/|___/\___|_|  |_| .__/ \__|_|\___/|_| |_|___/
    //                                      | |
    //                                      |_|
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_subscription_id(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $subscriptions_ids = wcs_get_subscriptions_for_order( $order_id, array(
            'order_type' => 'any',
        ) );
        $ids = array();
        foreach ( $subscriptions_ids as $subscription_id => $subscription_obj ) {
            
            if ( is_admin() || is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
                $ids[] = '<a href="' . admin_url( 'post.php' ) . '?post=' . $subscription_obj->get_order_number() . '&action=edit" target="_blank">' . $subscription_obj->get_order_number() . '</a>';
            } else {
                $ids[] = $subscription_obj->get_order_number();
            }
        
        }
        $value = implode( '<br />', $ids );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_subscription_total(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $subscriptions_ids = wcs_get_subscriptions_for_order( $order_id, array(
            'order_type' => 'any',
        ) );
        $statuses = array();
        foreach ( $subscriptions_ids as $subscription_id => $subscription_obj ) {
            $statuses[] .= esc_html( strip_tags( $subscription_obj->get_formatted_order_total() ) );
        }
        $value = implode( '<br />', $statuses );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_subscription_status(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $subscriptions_ids = wcs_get_subscriptions_for_order( $order_id, array(
            'order_type' => 'any',
        ) );
        $statuses = array();
        foreach ( $subscriptions_ids as $subscription_id => $subscription_obj ) {
            $statuses[] = $subscription_obj->get_status();
        }
        $value = implode( '<br />', $statuses );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_subscription_start_date(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $subscriptions_ids = wcs_get_subscriptions_for_order( $order_id, array(
            'order_type' => 'any',
        ) );
        $start_dates = array();
        foreach ( $subscriptions_ids as $subscription_id => $subscription_obj ) {
            $start_date = $subscription_obj->get_time( 'start' );
            
            if ( $start_date !== 0 ) {
                $dt = new DateTime( "@{$start_date}" );
                $start_dates[] = $dt->format( get_option( 'date_format' ) );
            }
        
        }
        $value = implode( '<br />', $start_dates );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_subscription_trial_end(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $subscriptions_ids = wcs_get_subscriptions_for_order( $order_id, array(
            'order_type' => 'any',
        ) );
        $trial_ends = array();
        foreach ( $subscriptions_ids as $subscription_id => $subscription_obj ) {
            $trial_end = $subscription_obj->get_time( 'trial_end' );
            
            if ( $trial_end !== 0 ) {
                $dt = new DateTime( "@{$trial_end}" );
                $trial_ends[] = $dt->format( 'j F Y' );
            }
        
        }
        $value = implode( '<br />', $trial_ends );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_subscription_end_date(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $subscriptions_ids = wcs_get_subscriptions_for_order( $order_id, array(
            'order_type' => 'any',
        ) );
        $end_dates = array();
        foreach ( $subscriptions_ids as $subscription_id => $subscription_obj ) {
            $end_date = $subscription_obj->get_time( 'end' );
            
            if ( $end_date !== 0 ) {
                $dt = new DateTime( "@{$end_date}" );
                $end_dates[] = $dt->format( get_option( 'date_format' ) );
            }
        
        }
        $value = implode( '<br />', $end_dates );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_subscription_next_payment(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $subscriptions_ids = wcs_get_subscriptions_for_order( $order_id, array(
            'order_type' => 'any',
        ) );
        $next_payments = array();
        if ( $subscriptions_ids ) {
            foreach ( $subscriptions_ids as $subscription_id => $subscription_obj ) {
                $nextpayment = esc_attr( $subscription_obj->get_date_to_display( 'next_payment' ) );
                if ( $nextpayment !== 0 && $nextpayment !== '-' ) {
                    $next_payments[] = $nextpayment;
                }
            }
        }
        $value = implode( '<br />', $next_payments );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_subscription_last_order_date(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $subscriptions_ids = wcs_get_subscriptions_for_order( $order_id, array(
            'order_type' => 'any',
        ) );
        $last_payments = array();
        foreach ( $subscriptions_ids as $subscription_id => $subscription_obj ) {
            $date_type = 'last_payment_date';
            if ( $subscription_obj->get_time( $date_type, 'gmt' ) !== 0 ) {
                $last_payments[] = sprintf(
                    '<time class="%s" title="%s">%s</time>',
                    esc_attr( $column ),
                    esc_attr( date( __( 'Y/m/d g:i:s A', 'woocommerce-subscriptions' ), $subscription_obj->get_time( $date_type, 'site' ) ) ),
                    esc_html( $subscription_obj->get_date_to_display( $date_type ) )
                );
            }
        }
        $value = implode( '<br />', $last_payments );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_subscription_billing_interval(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $subscriptions_ids = wcs_get_subscriptions_for_order( $order_id, array(
            'order_type' => 'any',
        ) );
        $billing_intervals = array();
        foreach ( $subscriptions_ids as $subscription_id => $subscription_obj ) {
            $billing_intervals[] = $subscription_obj->get_billing_interval() . ' ' . $subscription_obj->get_billing_period();
        }
        $value = implode( '<br />', $billing_intervals );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }
    
    //   __          __          _____
    //   \ \        / /         / ____|
    //    \ \  /\  / /__   ___ | |     ___  _ __ ___  _ __ ___   ___ _ __ ___ ___
    //     \ \/  \/ / _ \ / _ \| |    / _ \| '_ ` _ \| '_ ` _ \ / _ \ '__/ __/ _ \
    //      \  /\  / (_) | (_) | |___| (_) | | | | | | | | | | |  __/ | | (_|  __/
    //    ___\/  \/ \___/ \___/ \_____\___/|_| |_| |_|_| |_| |_|\___|_|  \___\___|
    //   |  __ \             | |          | |
    //   | |__) | __ ___   __| |_   _  ___| |_
    //   |  ___/ '__/ _ \ / _` | | | |/ __| __|
    //   | |   | | | (_) | (_| | |_| | (__| |_
    //   |_|__ |_|  \___/ \__,_|\__,_|\___|\__|
    //   |  _ \                | | |
    //   | |_) |_   _ _ __   __| | | ___  ___
    //   |  _ <| | | | '_ \ / _` | |/ _ \/ __|
    //   | |_) | |_| | | | | (_| | |  __/\__ \
    //   |____/ \__,_|_| |_|\__,_|_|\___||___/
    //
    //
    /**
     * @param string       $column
     * @param string       $source
     * @param array        $params
     * @param array        $columns
     * @param int          $order_id
     * @param WC_Order     $order
     * @param int          $item_id
     * @param WC_Product   $product
     * @param int          $product_id
     * @param int          $customer_id
     * @param WP_User|bool $customer
     * @param int          $quantity
     * @param array        $current_row
     *
     * @return array
     */
    public static function data_bundled_items(
        $column,
        $source,
        $params,
        $columns,
        $order_id,
        $order,
        $item_id,
        $product,
        $product_id,
        $customer_id,
        $customer,
        $quantity,
        $current_row
    )
    {
        $order_item = new WC_Order_Item_Product( $item_id );
        $bundled_items = wc_pb_get_bundled_order_items( $order_item );
        $value = '<ul>';
        foreach ( $bundled_items as $bundled_item ) {
            $value .= '<li>- ' . $bundled_item['name'] . '</li>';
        }
        $value .= '</ul>';
        //echo 'test';
        //print_r($value);
        do_action( 'qm/debug', $value );
        $current_row[$column] = $value;
        return [
            'current_row' => $current_row,
            'columns'     => $columns,
        ];
    }

}