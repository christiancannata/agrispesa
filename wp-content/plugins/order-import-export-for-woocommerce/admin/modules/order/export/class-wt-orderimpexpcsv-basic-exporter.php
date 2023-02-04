<?php

if (!defined('ABSPATH')) {
    exit;
}

class Wt_Import_Export_For_Woo_Basic_Order_Bulk_Export {

    public static $temp_order_metadata;
    public static $line_item_meta;
    public static $include_hidden_meta;
	public static $is_wt_invoice_active;
	public static $shipment_tracking_active;
	public static $wpo_wcpdf;
	public static $is_yith_tracking_active;

    /**
     * Order Exporter
     */
    public static function do_export($post_type = 'shop_order', $order_IDS = array(), $xmldata = '0') {
        global $wpdb;

		
		if (is_plugin_active('print-invoices-packing-slip-labels-for-woocommerce/print-invoices-packing-slip-labels-for-woocommerce.php')):
            self::$is_wt_invoice_active = true;
        endif;
        if (class_exists('Zorem_Woocommerce_Advanced_Shipment_Tracking') || class_exists('WC_Shipment_Tracking')):
            self::$shipment_tracking_active = true;
        endif;
        if (class_exists('WPO_WCPDF')):
            self::$wpo_wcpdf = true;
        endif; 		
		if (is_plugin_active('yith-woocommerce-order-tracking-premium/init.php')):
            self::$is_yith_tracking_active = true;
        endif;  
		
        $csv_columns = include_once( __DIR__ . '/../data/data-order-post-columns.php' );
		$csv_columns = array_combine(array_keys($csv_columns), array_keys($csv_columns));

        $exclude_hidden_meta_columns = array();
        $user_columns_name = !empty($_POST['columns_name']) ? wc_clean($_POST['columns_name']) : $csv_columns;
        $export_columns = !empty($_POST['columns']) ? wc_clean($_POST['columns']) : array();

        
        $delimiter = !empty($_POST['delimiter']) ? $_POST['delimiter'] : ','; // WPCS: CSRF ok, input var ok. 
        $exclude_already_exported =  false;
        $export_to_separate_columns = false;
        self::$include_hidden_meta = false;

        if (self::$include_hidden_meta) {
            self::$temp_order_metadata = apply_filters('wt_hidden_meta_columns', self::get_all_metakeys('shop_order'));
        }
        //ord_auto_export_ftp_file_name


        $wpdb->hide_errors();

        if (function_exists('apache_setenv'))
            @apache_setenv('no-gzip', 1);
        @ini_set('zlib.output_compression', 0);
        @ob_end_clean();

        $order_ids = $order_IDS;


        $file_name = apply_filters('wt_iew_product_bulk_export_order_filename', 'order_export_' . date('Y-m-d-h-i-s') . '.csv');
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $file_name);
        header('Pragma: no-cache');
        header('Expires: 0');

        $fp = fopen('php://output', 'w');
        
        $order_ids = apply_filters('wt_iew_product_bulk_export_order_ids', $order_ids);
        // Variable to hold the CSV data we're exporting
        $row = array();

        // Export header rows
        foreach ($csv_columns as $column => $value) {
            if (!isset($user_columns_name[$column]))
                continue;
            $temp_head = esc_attr($user_columns_name[$column]);
            if (!$export_columns || in_array($column, $export_columns))
                $row[] = self::format_data($temp_head);
        }

        if (self::$include_hidden_meta) {
            $found_order_meta = array();
            // Some of the values may not be usable (e.g. arrays of arrays) but the worse
            // that can happen is we get an empty column.
            foreach (self::$temp_order_metadata as $meta) {
                if (!$meta)
                    continue;
                if (in_array(substr($meta, 1), array_keys($csv_columns)))
                    continue;
                if (in_array($meta, $exclude_hidden_meta_columns) || in_array($meta, array_keys($csv_columns)))
                    continue;
                $found_order_meta[] = $meta;
            }
            $found_order_meta = array_diff($found_order_meta, array_keys($csv_columns));
            $export_column_count = count($export_columns);
            $csv_column_count = count($row);
            $rows1 = $row;
            foreach ($found_order_meta as $key => $val) {
                $rows1[] = 'meta:' . self::format_data($val);
            }
            $row = $rows1;
        }

        $max_line_items = self::get_max_line_items($order_ids);
        for ($i = 1; $i <= $max_line_items; $i++) {
            $row[] = "line_item_{$i}";
        }

        if ($export_to_separate_columns) {
            self::$line_item_meta = self::get_all_line_item_metakeys();
            for ($i = 1; $i <= $max_line_items; $i++) {
                foreach (self::$line_item_meta as $meta_value) {
                    $new_val = str_replace("_", " ", $meta_value);
                    $row["line_item_{$i}_name"] = "Product Item {$i} Name";
                    $row["line_item_{$i}_product_id"] = "Product Item {$i} id";
                    $row["line_item_{$i}_sku"] = "Product Item {$i} SKU";
                    $row["line_item_{$i}_quantity"] = "Product Item {$i} Quantity";
                    $row["line_item_{$i}_total"] = "Product Item {$i} Total";
                    $row["line_item_{$i}_subtotal"] = "Product Item {$i} Subtotal";
                    if (in_array($meta_value, array("_product_id", "_qty", "_variation_id", "_line_total", "_line_subtotal", "_tax_class", "_line_tax", "_line_tax_data", "_line_subtotal_tax"))) {
                        continue;
                    } else {
                        $row["line_item_{$i}_$meta_value"] = "Product Item {$i} $new_val";
                    }
                }
            }
        }
        $filter_args = array('export_columns' => $export_columns, 'csv_columns' => $csv_columns, 'max_line_items' => $max_line_items, 'order_ids' => $order_ids, 'file_pointer' => $fp);
        $row = apply_filters('wt_iew_product_bulk_export_order_csv_header', $row, $filter_args); //Alter CSV Header

        if (!empty($row)) {
            $row = array_map('Wt_Import_Export_For_Woo_Basic_Order_Bulk_Export::wrap_column', $row);
            fwrite($fp, implode($delimiter, $row) . "\n");
        }
        $filter_args['header_row'] = $row;
        unset($row);
        // Loop orders
        foreach ($order_ids as $order_id) {
            //$row = array();   
            $data = self::get_orders_csv_row($order_id, $export_columns, $max_line_items, $user_columns_name);
            // Add to csv

            $data = apply_filters('wt_iew_product_bulk_export_order_csv_data', $data, $filter_args); //Alter CSV Data
            if (!empty($data)) {
                $row = array_map('Wt_Import_Export_For_Woo_Basic_Order_Bulk_Export::wrap_column', $data);
                fwrite($fp, implode($delimiter, $row) . "\n");
            }
            unset($row);
            unset($data);
            // updating records with expoted status 
            update_post_meta($order_id, 'wf_order_exported_status', TRUE);
        }


        fclose($fp);
        exit;
    }

    public static function format_data($data) {
        if (!is_array($data))
            ;
        $data = (string) urldecode($data);
        $enc = mb_detect_encoding($data, 'UTF-8, ISO-8859-1', true);
        $data = ( $enc == 'UTF-8' ) ? $data : utf8_encode($data);
        return $data;
    }

    /**
     * Wrap a column in quotes for the CSV
     * @param  string data to wrap
     * @return string wrapped data
     */
    public static function wrap_column($data) {
        return '"' . str_replace('"', '""', $data) . '"';
    }

    public static function get_max_line_items($order_ids) {
        $max_line_items = 0;
        foreach ($order_ids as $order_id) {
            $order = wc_get_order($order_id);
            $line_items_count = count($order->get_items());
            if ($line_items_count >= $max_line_items) {
                $max_line_items = $line_items_count;
            }
        }
        return $max_line_items;
    }

    public static function get_orders_csv_row($order_id, $export_columns, $max_line_items, $user_columns_name = array()) {
        $csv_columns = include( __DIR__ . '/../data/data-order-post-columns.php' );

        // Get an instance of the WC_Order object
        $order = wc_get_order($order_id);
        $line_items = $shipping_items = $fee_items = $tax_items = $coupon_items = $refund_items = array();

        // get line items
        foreach ($order->get_items() as $item_id => $item) {
            $product = (WC()->version < '4.4.0') ? $order->get_product_from_item($item) : $item->get_product(); // $order->get_product_from_item($item) deprecated since version 4.4.0 
            if (!is_object($product)) {
                $product = new WC_Product(0);
            }
            //$item_meta = function_exists('wc_get_order_item_meta') ? wc_get_order_item_meta($item_id, '', false) : $order->get_item_meta($item_id);
            $item_meta = self::get_order_line_item_meta($item_id);
            $prod_type = (WC()->version < '3.0.0') ? $product->product_type : $product->get_type();
            $line_item = array(
                'name' => html_entity_decode(!empty($item['name']) ? $item['name'] : $product->get_title(), ENT_NOQUOTES, 'UTF-8'),
                'product_id' => (WC()->version < '2.7.0') ? $product->id : (($prod_type == 'variable' || $prod_type == 'variation' || $prod_type == 'subscription_variation') ? $product->get_parent_id() : $product->get_id()),
                'sku' => $product->get_sku(),
                'quantity' => $item['qty'],
                'total' => wc_format_decimal($order->get_line_total($item), 2),
                'sub_total' => wc_format_decimal($order->get_line_subtotal($item), 2),
                    //'meta' => html_entity_decode($meta, ENT_NOQUOTES, 'UTF-8'),
            );

            //add line item tax
            $line_tax_data = isset($item['line_tax_data']) ? $item['line_tax_data'] : array();
            $tax_data = maybe_unserialize($line_tax_data);
            $tax_detail = isset($tax_data['total']) ? wc_format_decimal(wc_round_tax_total(array_sum((array) $tax_data['total'])), 2) : '';
            if ($tax_detail != '0.00' && !empty($tax_detail)) {
                $line_item['tax'] = $tax_detail;
                $line_tax_ser = json_encode($tax_data);
                $line_item['tax_data'] = $line_tax_ser;
            }

            foreach ($item_meta as $key => $value) {
                switch ($key) {
                    case '_qty':
                    case '_variation_id':
                    case '_product_id':
                    case '_line_total':
                    case '_line_subtotal':
                    case '_tax_class':
                    case '_line_tax':
                    case '_line_tax_data':
                    case '_line_subtotal_tax':
                        break;

                    default:
                        if (is_object($value))
                            $value = $value->meta_value;
                        if (is_array($value))
                            $value = implode(',', $value);
                        $line_item[$key] = $value;
                        break;
                }
            }

            $refunded = wc_format_decimal($order->get_total_refunded_for_item($item_id), 2);
            if ($refunded != '0.00') {
                $line_item['refunded'] = $refunded;
            }

            if ($prod_type === 'variable' || $prod_type === 'variation' || $prod_type === 'subscription_variation') {
                $line_item['_variation_id'] = (WC()->version > '2.7') ? $product->get_id() : $product->variation_id;
            }
            $line_items[] = $line_item;
        }

        /*
          foreach ($order->get_shipping_methods() as $_ => $shipping_item) {

          $shipping_items[] = implode('|', array(
          'method:' . $shipping_item['name'],
          'total:' . wc_format_decimal($shipping_item['cost'], 2),
          ));
          }
         * 
         */
        //shipping items is just product x qty under shipping method
        $line_items_shipping = $order->get_items('shipping');
        foreach ($line_items_shipping as $item_id => $item) {
            $item_meta = self::get_order_line_item_meta($item_id);
            foreach ($item_meta as $key => $value) {
                switch ($key) {
                    case 'Items':
                    case 'method_id':
                        if (is_object($value))
                            $value = $value->meta_value;
                        if (is_array($value))
                            $value = implode(',', $value);

                        $meta[$key] = $value;
                        break;
                    case 'taxes':
                        if (is_object($value))
                            $value = $value->meta_value;
                        if (is_array($value))
                            $value = implode(',', $value);

                        $value = maybe_unserialize($value);
                        $meta[$key] = json_encode($value);
                        break;
                }
            }
            foreach (array('Items', 'method_id', 'taxes') as $value) {
                if (!isset($meta[$value])) {
                    $meta[$value] = '';
                }
            }
            $shipping_items[] = trim(implode('|', array('items:' . $meta['Items'], 'method_id:' . $meta['method_id'], 'taxes:' . $meta['taxes'])));
        }

        //get fee and total
        $fee_total = 0;
        $fee_tax_total = 0;
        foreach ($order->get_fees() as $fee_id => $fee) {
            $fee_items[] = implode('|', array(
                'name:' . html_entity_decode($fee['name'], ENT_NOQUOTES, 'UTF-8'),
                'total:' . wc_format_decimal($fee['line_total'], 2),
                'tax:' . wc_format_decimal($fee['line_tax'], 2),
                'tax_data:' . json_encode($fee['line_tax_data'])
            ));
            $fee_total += $fee['line_total'];
            $fee_tax_total += $fee['line_tax'];
        }

        $order_taxes = $order->get_taxes();
        if (!empty($order_taxes)) {
            foreach ($order_taxes as $tax_id => $tax_item) {

                if (!empty($tax_item->get_shipping_tax_total())) {
                    $total = $tax_item->get_tax_total() + $tax_item->get_shipping_tax_total();
                } else {
                    $total = $tax_item->get_tax_total();
                }
                $tax_items[] = implode('|', array(
                    'rate_id:' . $tax_item->get_rate_id(),
                    'code:' . $tax_item->get_rate_code(),
                    'total:' . wc_format_decimal($tax_item->get_tax_total(), 2),
                    'label:' . $tax_item->get_label(),
                    'tax_rate_compound:' . $tax_item->get_compound(),
                ));
            }
        }

        // add coupons
		if ( (WC()->version < '4.4.0' ) ) {
			foreach ( $order->get_items('coupon') as $_ => $coupon_item ) {
				$discount_amount = !empty( $coupon_item[ 'discount_amount' ] ) ? $coupon_item[ 'discount_amount' ] : 0;
				$coupon_items[]	 = implode( '|', array(
					'code:' . $coupon_item[ 'name' ],
					'amount:' . wc_format_decimal( $discount_amount, 2 ),
				) );
			}
		} else {
			foreach ( $order->get_coupon_codes() as $_ => $coupon_code ) {
				$coupon_obj = new WC_Coupon($coupon_code);
				$discount_amount = !empty( $coupon_obj->get_amount() ) ? $coupon_obj->get_amount() : 0;
				$coupon_items[]	 = implode( '|', array(
					'code:' . $coupon_code,
					'amount:' . wc_format_decimal( $discount_amount, 2 ),
				) );
			}
		}

        foreach ($order->get_refunds() as $refunded_items) {

            if ((WC()->version < '2.7.0')) {
                $refund_items[] = implode('|', array(
                    'amount:' . $refunded_items->get_refund_amount(),
                    'reason:' . $refunded_items->reason,
                    'date:' . date('Y-m-d H:i:s', strtotime($refunded_items->date_created)),
                ));
            } else {
                $refund_items[] = implode('|', array(
                    'amount:' . $refunded_items->get_amount(),
                    'reason:' . $refunded_items->get_reason(),
                    'date:' . date('Y-m-d H:i:s', strtotime($refunded_items->get_date_created())),
                ));
            }
        }


            $order_data = array(
                'order_id' => $order->get_id(),
                'order_number' => $order->get_order_number(),
                'order_date' => date('Y-m-d H:i:s', strtotime(get_post($order->get_id())->post_date)),
                'paid_date' => $order->get_date_paid(),
                'status' => $order->get_status(),
                'shipping_total' => $order->get_total_shipping(),
                'shipping_tax_total' => wc_format_decimal($order->get_shipping_tax(), 2),
                'fee_total' => wc_format_decimal($fee_total, 2),
                'fee_tax_total' => wc_format_decimal($fee_tax_total, 2),
                'tax_total' => wc_format_decimal($order->get_total_tax(), 2),
                'cart_discount' => (defined('WC_VERSION') && (WC_VERSION >= 2.3)) ? wc_format_decimal($order->get_total_discount(), 2) : wc_format_decimal($order->get_cart_discount(), 2),
                'order_discount' => (defined('WC_VERSION') && (WC_VERSION >= 2.3)) ? wc_format_decimal($order->get_total_discount(), 2) : wc_format_decimal($order->get_order_discount(), 2),
                'discount_total' => wc_format_decimal($order->get_total_discount(), 2),
                'order_total' => wc_format_decimal($order->get_total(), 2),                
                'order_key' => $order->get_order_key(),
                'order_currency' => $order->get_currency(),
                'payment_method' => $order->get_payment_method(),
                'payment_method_title' => $order->get_payment_method_title(),
                'transaction_id' => $order->get_transaction_id(),
                'customer_ip_address' => $order->get_customer_ip_address(),
                'customer_user_agent' => $order->get_customer_user_agent(),                
                'shipping_method' => $order->get_shipping_method(),
                'customer_id' => $order->get_user_id(),
                'customer_user' => $order->get_user_id(),
                'customer_email' => ($a = get_userdata($order->get_user_id())) ? $a->user_email : '',
                'billing_first_name' => $order->get_billing_first_name(),
                'billing_last_name' => $order->get_billing_last_name(),
                'billing_company' => $order->get_billing_company(),
                'billing_email' => $order->get_billing_email(),
                'billing_phone' => $order->get_billing_phone(),
                'billing_address_1' => $order->get_billing_address_1(),
                'billing_address_2' => $order->get_billing_address_2(),
                'billing_postcode' => $order->get_billing_postcode(),
                'billing_city' => $order->get_billing_city(),
                'billing_state' => $order->get_billing_state(),
                'billing_country' => $order->get_billing_country(),
                'shipping_first_name' => $order->get_shipping_first_name(),
                'shipping_last_name' => $order->get_shipping_last_name(),
                'shipping_company' => $order->get_shipping_company(),
                'shipping_phone' => (version_compare(WC_VERSION, '5.6', '<')) ? '' : $order->get_shipping_phone(),                
                'shipping_address_1' => $order->get_shipping_address_1(),
                'shipping_address_2' => $order->get_shipping_address_2(),
                'shipping_postcode' => $order->get_shipping_postcode(),
                'shipping_city' => $order->get_shipping_city(),
                'shipping_state' => $order->get_shipping_state(),
                'shipping_country' => $order->get_shipping_country(),
                'customer_note' => $order->get_customer_note(),
                'wt_import_key' => $order->get_order_number(),
                'shipping_items' => self::format_data(implode(';', $shipping_items)),
                'fee_items' => implode('||', $fee_items),
                'tax_items' => implode(';', $tax_items),
                'coupon_items' => implode(';', $coupon_items),
                'refund_items' => implode(';', $refund_items),
                'order_notes' => implode('||', (defined('WC_VERSION') && (WC_VERSION >= 3.2)) ? self::get_order_notes_new($order) : self::get_order_notes($order)),
                'download_permissions' => $order->is_download_permitted() ? $order->is_download_permitted() : 0,
            );
        
        foreach ($order_data as $key => $value) {
            if (!$export_columns || in_array($key, $export_columns)) {
                // need to modify code
            } else {
                unset($order_data[$key]);
            }
        }

        if (self::$is_wt_invoice_active):
            $invoice_date = get_post_meta($order_data['order_id'], '_wf_invoice_date', true);
            $invoice_number = get_post_meta($order_data['order_id'], 'wf_invoice_number', true);
            $order_data['meta:wf_invoice_number'] = empty($invoice_number) ? '' : $invoice_number;
            $order_data['meta:_wf_invoice_date'] = empty($invoice_date) ? '' : date_i18n(get_option( 'date_format' ), $invoice_date);
        endif;
        if (self::$is_yith_tracking_active):
            $ywot_tracking_code = get_post_meta($order_data['order_id'], 'ywot_tracking_code', true);
            $ywot_tracking_postcode = get_post_meta($order_data['order_id'], 'ywot_tracking_postcode', true);
            $ywot_carrier_id = get_post_meta($order_data['order_id'], 'ywot_carrier_id', true);
            $ywot_pick_up_date = get_post_meta($order_data['order_id'], 'ywot_pick_up_date', true);
            $ywot_picked_up = get_post_meta($order_data['order_id'], 'ywot_picked_up', true);            
            $order_data['meta:ywot_tracking_code'] = empty($ywot_tracking_code) ? '' : $ywot_tracking_code;
            $order_data['meta:ywot_tracking_postcode'] = empty($ywot_tracking_postcode) ? '' : $ywot_tracking_postcode;
            $order_data['meta:ywot_carrier_id'] = empty($ywot_carrier_id) ? '' : $ywot_carrier_id;
            $order_data['meta:ywot_pick_up_date'] = empty($ywot_pick_up_date) ? '' : $ywot_pick_up_date;
            $order_data['meta:ywot_picked_up'] = empty($ywot_picked_up) ? '' : $ywot_picked_up;            
        endif; 
		if (self::$shipment_tracking_active):
            $advanced_shipment_tracking = get_post_meta($order_data['order_id'], '_wc_shipment_tracking_items', true);
            $order_data['meta:_wc_shipment_tracking_items'] = empty($advanced_shipment_tracking) ? '' : json_encode($advanced_shipment_tracking);
        endif;
		if (self::$wpo_wcpdf):
            $_wcpdf_invoice_number = get_post_meta($order_data['order_id'], '_wcpdf_invoice_number', true);
            $_wcpdf_invoice_date = get_post_meta($order_data['order_id'], '_wcpdf_invoice_date', true);
			$_wcpdf_invoice_number_data = get_post_meta($order_data['order_id'], '_wcpdf_invoice_number_data', true);
            $_wcpdf_invoice_date_formatted = get_post_meta($order_data['order_id'], '_wcpdf_invoice_date_formatted', true);
            $_wcpdf_invoice_settings = get_post_meta($order_data['order_id'], '_wcpdf_invoice_settings', true);
                     
            $order_data['meta:_wcpdf_invoice_number'] = empty($_wcpdf_invoice_number) ? '' : $_wcpdf_invoice_number;
            $order_data['meta:_wcpdf_invoice_date'] = empty($_wcpdf_invoice_date) ? '' : $_wcpdf_invoice_date;
            $order_data['meta:_wcpdf_invoice_number_data'] = empty($_wcpdf_invoice_number_data) ? '' : json_encode($_wcpdf_invoice_number_data);
			$order_data['meta:_wcpdf_invoice_date_formatted'] = empty($_wcpdf_invoice_date_formatted) ? '' : $_wcpdf_invoice_date_formatted;
			$order_data['meta:_wcpdf_invoice_settings'] = empty($_wcpdf_invoice_settings) ? '' : json_encode($_wcpdf_invoice_settings);
        endif;


        $li = 1;
        foreach ($line_items as $line_item) {
            foreach ($line_item as $name => $value) {
                $line_item[$name] = $name . ':' . $value;
            }
            $line_item = implode(apply_filters('wt_change_item_separator', '|'), $line_item);
            $order_data["line_item_{$li}"] = $line_item;
            $li++;
        }

        for ($i = 1; $i <= $max_line_items; $i++) {
            $order_data["line_item_{$i}"] = !empty($order_data["line_item_{$i}"]) ? self::format_data($order_data["line_item_{$i}"]) : '';
        }
        $export_to_separate_columns = !empty($_POST['export_to_separate_columns']) ? true : false;
        if ($export_to_separate_columns) {
            $line_item_values = self::get_all_metakeys_and_values($order);
            for ($i = 1; $i <= $max_line_items; $i++) {
                $line_item_array = explode('|', $order_data["line_item_{$i}"]);
                foreach (self::$line_item_meta as $meta_val) {
                    $order_data["line_item_{$i}_name"] = !empty($line_item_array[0]) ? substr($line_item_array[0], strpos($line_item_array[0], ':') + 1) : '';
                    $order_data["line_item_{$i}_product_id"] = !empty($line_item_array[1]) ? substr($line_item_array[1], strpos($line_item_array[1], ':') + 1) : '';
                    $order_data["line_item_{$i}_sku"] = !empty($line_item_array[2]) ? substr($line_item_array[2], strpos($line_item_array[2], ':') + 1) : '';
                    $order_data["line_item_{$i}_quantity"] = !empty($line_item_array[3]) ? substr($line_item_array[3], strpos($line_item_array[3], ':') + 1) : '';
                    $order_data["line_item_{$i}_total"] = !empty($line_item_array[4]) ? substr($line_item_array[4], strpos($line_item_array[4], ':') + 1) : '';
                    $order_data["line_item_{$i}_subtotal"] = !empty($line_item_array[5]) ? substr($line_item_array[5], strpos($line_item_array[5], ':') + 1) : '';
                    if (in_array($meta_val, array("_product_id", "_qty", "_variation_id", "_line_total", "_line_subtotal", "_tax_class", "_line_tax", "_line_tax_data", "_line_subtotal_tax"))) {
                        continue;
                    } else {
                        $order_data["line_item_{$i}_$meta_val"] = !empty($line_item_values[$i][$meta_val]) ? $line_item_values[$i][$meta_val] : '';
                    }
                }
            }
        }
        $order_data_filter_args = array('export_columns' => $export_columns, 'user_columns_name' => $user_columns_name, 'max_line_items' => $max_line_items);
        return apply_filters('wt_iew_product_bulk_export_order_data', $order_data, $order_data_filter_args);
    }

    public static function get_order_notes($order) {
        $callback = array('WC_Comments', 'exclude_order_comments');
        $args = array(
            'post_id' => (WC()->version < '2.7.0') ? $order->id : $order->get_id(),
            'approve' => 'approve',
            'type' => 'order_note'
        );
        remove_filter('comments_clauses', $callback);
        $notes = get_comments($args);
        add_filter('comments_clauses', $callback);
        $notes = array_reverse($notes);
        $order_notes = array();
        foreach ($notes as $note) {
            $date = $note->comment_date;
            $customer_note = 0;
            if (get_comment_meta($note->comment_ID, 'is_customer_note', '1')) {
                $customer_note = 1;
            }
            $order_notes[] = implode('|', array(
                'content:' . str_replace(array("\r", "\n"), ' ', $note->comment_content),
                'date:' . (!empty($date) ? $date : current_time('mysql')),
                'customer:' . $customer_note,
                'added_by:' . $note->added_by
            ));
        }
        return $order_notes;
    }

    public static function get_order_notes_new($order) {
        $notes = wc_get_order_notes(array('order_id' => $order->get_id(), 'order_by' => 'date_created', 'order' => 'ASC'));
        $order_notes = array();
        foreach ($notes as $note) {
            $order_notes[] = implode('|', array(
                'content:' . str_replace(array("\r", "\n"), ' ', $note->content),
                'date:' . $note->date_created->date('Y-m-d H:i:s'),
                'customer:' . $note->customer_note,
                'added_by:' . $note->added_by
            ));
        }
        return $order_notes;
    }

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

    public static function get_all_line_item_metakeys() {
        global $wpdb;
        $filter_meta = apply_filters('wt_order_export_select_line_item_meta', array());
        $filter_meta = !empty($filter_meta) ? implode("','", $filter_meta) : '';
        $query = "SELECT DISTINCT om.meta_key
            FROM {$wpdb->prefix}woocommerce_order_itemmeta AS om 
            INNER JOIN {$wpdb->prefix}woocommerce_order_items AS oi ON om.order_item_id = oi.order_item_id
            WHERE oi.order_item_type = 'line_item'";
        if (!empty($filter_meta)) {
            $query .= " AND om.meta_key IN ('" . $filter_meta . "')";
        }
        $meta_keys = $wpdb->get_col($query);
        return $meta_keys;
    }

    public static function get_order_line_item_meta($item_id) {
        global $wpdb;
        $filtered_meta = apply_filters('wt_order_export_select_line_item_meta', array());
        $filtered_meta = !empty($filtered_meta) ? implode("','", $filtered_meta) : '';
        $query = "SELECT meta_key,meta_value
            FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = '$item_id'";
        if (!empty($filtered_meta)) {
            $query .= " AND meta_key IN ('" . $filtered_meta . "')";
        }
        $meta_keys = $wpdb->get_results($query, OBJECT_K);
        return $meta_keys;
    }


    public static function get_all_metakeys_and_values($order = null) {
        $line_item_values = array();
        $in = 1;
        foreach ($order->get_items() as $item_id => $item) {
            //$item_meta = function_exists('wc_get_order_item_meta') ? wc_get_order_item_meta($item_id, '', false) : $order->get_item_meta($item_id);
            $item_meta = self::get_order_line_item_meta($item_id);
            foreach ($item_meta as $key => $value) {
                switch ($key) {
                    case '_qty':
                    case '_product_id':
                    case '_line_total':
                    case '_line_subtotal':
                    case '_tax_class':
                    case '_line_tax':
                    case '_line_tax_data':
                    case '_line_subtotal_tax':
                        break;

                    default:
                        if (is_object($value))
                            $value = $value->meta_value;
                        if (is_array($value))
                            $value = implode(',', $value);
                        $line_item_value[$key] = $value;
                        break;
                }
            }
            $line_item_values[$in] = !empty($line_item_value) ? $line_item_value : '';
            $in++;
        }
        return $line_item_values;
    }

}
