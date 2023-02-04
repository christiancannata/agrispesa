<?php

if (!defined('ABSPATH')) {
	exit;
}

class Wt_Import_Export_For_Woo_Basic_Coupon_Bulk_Export {

	public static function do_export($post_type = 'shop_coupon', $coupon_ids = array()) {
		global $wpdb;

		$delimiter = !empty($_POST['delimiter']) ? $_POST['delimiter'] : ','; // WPCS: CSRF ok, input var ok.

		$csv_columns = include_once( __DIR__ . '/../data/data-coupon-post-columns.php' );
		$csv_columns = array_combine(array_keys($csv_columns), array_keys($csv_columns));
		$user_columns_name = !empty($_POST['columns_name']) ? wc_clean($_POST['columns_name']) : $csv_columns;
		$export_columns = !empty($_POST['columns']) ? wc_clean($_POST['columns']) : '';
		$include_hidden_meta = true;

		$wpdb->hide_errors();
		@set_time_limit(0);
		if (function_exists('apache_setenv'))
			@apache_setenv('no-gzip', 1);
		@ini_set('zlib.output_compression', 0);
		@ob_end_clean();

		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename=coupon_export_' . date('Y_m_d_H_i_s', current_time('timestamp')) . '.csv');
		header('Pragma: no-cache');
		header('Expires: 0');

		$fp = fopen('php://output', 'w');

		$row = array();
		foreach ($csv_columns as $column => $value) {
			$temp_head = esc_attr($user_columns_name[$column]);
			if (!$export_columns || in_array($column, $export_columns))
				$row[] = $temp_head;
		}

		$row = apply_filters('wt_ier_alter_coupon_csv_header', $row); //Alter Coupon CSV Header

		$row = array_map('Wt_Import_Export_For_Woo_Basic_Coupon_Bulk_Export::wrap_column', $row);
		fwrite($fp, implode($delimiter, $row) . "\n");
		unset($row);

		$coupon_args = array(
			'post_status' => array('publish', 'pending', 'private', 'draft'),
			'post_type' => 'shop_coupon',
			'numberposts' => 9999
		);

		$coupon_args['post__in'] = $coupon_ids;

		$coupons = get_posts($coupon_args);

		foreach ($coupons as $coupon) {
			foreach ($csv_columns as $column => $value) {
				if (!$export_columns || in_array($column, $export_columns)) {
					if (isset($coupon->$column)) {
						if (is_array($coupon->$column)) {
							$coupon->$column = implode(",", $coupon->$column);
						}
						if ($column == 'product_ids') {
							$hf_val = self::format_data($coupon->$column);
							$sku = self::get_sku_from_id($hf_val);
							$row[] = str_replace(',', '|', $hf_val);
							continue;
						}
						if ($column == 'exclude_product_ids') {
							$ex_val = self::format_data($coupon->$column);
							$exsku = self::get_sku_from_id($ex_val);
							$row[] = str_replace(',', '|', $ex_val);
							continue;
						}
						$row[] = self::format_data($coupon->$column);
					} elseif (isset($coupon->$column) && !is_array($coupon->$column)) {
						if ($column === 'post_title') {
							$row[] = sanitize_text_field($coupon->$column);
						} else {
							$row[] = self::format_data($coupon->$column);
						}
					} elseif ($column === 'product_SKUs') {
						$row[] = !empty($sku) ? $sku : '';
						unset($sku);
					} elseif ($column === 'exclude_product_SKUs') {
						$row[] = !empty($exsku) ? $exsku : '';
						unset($exsku);
					} elseif ($column === 'expiry_date') {
						$exp_date = get_post_meta($coupon->ID, 'date_expires');
						$row[] = !empty($exp_date[0]) ? date("Y-m-d", $exp_date[0]) : '';
					} else {
						$row[] = '';
					}
				}
			}

			$row = apply_filters('wt_ier_alter_coupon_csv_data', $row); // Alter Coupon CSV data if needed
			$row = array_map('Wt_Import_Export_For_Woo_Basic_Coupon_Bulk_Export::wrap_column', $row);
			fwrite($fp, implode($delimiter, $row) . "\n");
			unset($row);
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

	/**
	 * Get a list of all the meta keys for a post type. This includes all public, private,
	 * used, no-longer used etc. They will be sorted once fetched.
	 */
	public static function get_all_metakeys($post_type = 'shop_coupon') {
		global $wpdb;

		$meta = $wpdb->get_col($wpdb->prepare(
						"SELECT DISTINCT pm.meta_key
            FROM {$wpdb->postmeta} AS pm
            LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id
            WHERE p.post_type = %s
            AND p.post_status IN ( 'publish', 'pending', 'private', 'draft' )", $post_type
		));

		sort($meta);

		return $meta;
	}

	public static function get_sku_from_id($val) {
		$pro_id = explode(",", $val);
		$sku_arr = array();
		if ($pro_id) {
			foreach ($pro_id as $value) {
				$product_exist = get_post_type($value);
				if ($product_exist == 'product' || $product_exist == 'product_variation') {
					$psku = get_post_meta($value, '_sku', TRUE);
					if (!empty($psku)) {
						$sku_arr[] = $psku;
					}
				}
			}
		}
		$new_sku = implode("|", $sku_arr);
		return $new_sku;
	}

}
