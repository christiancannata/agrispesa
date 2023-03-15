<?php
function sm_variable_parent_sync_price( $ids ) {

	if( empty( $ids ) ) {
		return;
	}

	foreach( $ids as $id ) {
		$parent_id = wp_get_post_parent_id( $id );

		if( $parent_id > 0 ) {
			if ( ( !empty( Smart_Manager::$sm_is_woo21 ) && Smart_Manager::$sm_is_woo21 == 'true' ) || ( !empty( Smart_Manager::$sm_is_woo22 ) && Smart_Manager::$sm_is_woo22 == 'true' ) || ( !empty( Smart_Manager::$sm_is_woo30 ) && Smart_Manager::$sm_is_woo30 == 'true' ) ) {
				if( class_exists( 'WC_Product_Variable' ) && is_callable( array('WC_Product_Variable', 'sync') ) ) {
					WC_Product_Variable::sync( $parent_id );
					delete_transient( 'wc_product_children_' . $parent_id ); //added in woo24
				}
			}
		}
	}

}

function sm_update_stock_status( $id, $stock ) {
  if ( ( ( !empty( Smart_Manager::$sm_is_woo21 ) && Smart_Manager::$sm_is_woo21 == 'true' ) || ( !empty( Smart_Manager::$sm_is_woo22 ) && Smart_Manager::$sm_is_woo22 == 'true' ) || ( !empty( Smart_Manager::$sm_is_woo30 ) && Smart_Manager::$sm_is_woo30 == 'true' ) ) && !empty( $id ) ) {

	  $parent_id = wp_get_post_parent_id( $id );

		$woo_version = ( ( defined( 'WOOCOMMERCE_VERSION' ) ) ? WOOCOMMERCE_VERSION : $woocommerce->version );
		$woo_prod_obj_stock_status = '';

		if( $parent_id > 0 && class_exists('WC_Product_Variation') ) {
		   $woo_prod_obj_stock_status = new WC_Product_Variation($id);
		} else if( class_exists('WC_Product') ) {
		   $woo_prod_obj_stock_status = new WC_Product($id);
		}

		if( !empty( $woo_prod_obj_stock_status ) ) {
		  if( !empty( Smart_Manager::$sm_is_woo30 ) && Smart_Manager::$sm_is_woo30 == 'true' && function_exists('wc_update_product_stock') ) {
		  		$prod = wc_get_product($id);
		  		$prod->set_stock_quantity( $stock );
				wc_update_product_stock( $prod, $stock );
		  } else if ( 'yes' === get_post_meta( $id, '_manage_stock', true ) ) { //check if manage stock is enabled or not  
			  if( version_compare( $woo_version, '2.4', ">=" ) ) {
				  if( $parent_id > 0 ) {
					  $stock_status_option = get_post_meta($id,'stock_status',true);
					  $stock_status = (!empty($stock_status_option)) ? $stock_status_option : '';
					  if( is_callable( array($woo_prod_obj_stock_status, 'set_stock_status') ) ) {
						$woo_prod_obj_stock_status->set_stock_status($stock_status);
					  }
				  } else if( is_callable( array($woo_prod_obj_stock_status, 'check_stock_status') ) ) {
					  $woo_prod_obj_stock_status->check_stock_status();
				  }
			  } else if( is_callable( array($woo_prod_obj_stock_status, 'set_stock') ) ) {
				  $woo_prod_obj_stock_status->set_stock($stock);
			  }
		  } 
		}
	}
}

function sm_array_recursive_diff($array1, $array2) {
	$array_diff = array();
	foreach ($array1 as $key => $value) {
		if (array_key_exists($key, $array2)) {
			if (is_array($value)) {
				$recursive_diff = sm_array_recursive_diff($value, $array2[$key]);
				if (count($recursive_diff)) { $array_diff[$key] = $recursive_diff; }
			} else {
				if ($value != $array2[$key]) {
			  		$array_diff[$key] = $value;
				}
			}
		} else {
			$array_diff[$key] = $value;
		}
	}
	return $array_diff;
} 

function sm_multidimesional_array_search($id, $index, $array) {
	if( empty( $array ) ){
		return null;
	}

   	foreach ($array as $key => $val) {
		if (empty($val[$index])) continue;

		if ($val[$index] == $id) {
		   return $key;
		}
  	}
   	return null;
}

//Function to sort multidimesnional array based on any given key
function sm_multidimensional_array_sort($array, $on, $order=SORT_ASC){

	$sorted_array = array();
	$sortable_array = array();

	if (count($array) > 0) {
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $key2 => $value2) {
					if ($key2 == $on) {
						$sortable_array[$key] = $value2;
					}
				}
			} else {
				$sortable_array[$key] = $value;
			}
		}

		switch ($order) {
			case SORT_ASC:
				asort($sortable_array);
				break;
			case SORT_DESC:
				arsort($sortable_array);
				break;
		}

		foreach ($sortable_array as $key => $value) {
			$sorted_array[$key] = $array[$key];
		}
	}

	return $sorted_array;
}

//Function to compare column position
function sm_position_compare( $a, $b ){
	if ( $a['position'] == $b['position'] )
		return 0;
	if ( $a['position'] < $b['position'] ) {
		return -1;
	}
	return 1;
}

function sm_woo_get_price($regular_price, $sale_price, $sale_price_dates_from, $sale_price_dates_to) {
	// Get price if on sale
	if ($sale_price && empty( $sale_price_dates_to ) && empty( $sale_price_dates_from ) ) {
		$price = $sale_price;
	} else { 
		$price = $regular_price;
	}

	$from_date = ( is_numeric( $sale_price_dates_from ) && ( int )$sale_price_dates_from == $sale_price_dates_from ) ? ( int )$sale_price_dates_from : strtotime( $sale_price_dates_from );
	$to_date = ( is_numeric( $sale_price_dates_to ) && ( int )$sale_price_dates_to == $sale_price_dates_to ) ? ( int )$sale_price_dates_to : strtotime( $sale_price_dates_to );
	
	if ( ! empty( $from_date ) && $from_date < strtotime('NOW') ) {
		$price = $sale_price;
	}
	
	if ( ! empty( $to_date ) && $to_date < strtotime('NOW') ) {
		$price = $regular_price;
	}

	return $price;
}

//function to fetch the variation current post title
function sm_get_current_variation_title( $pids = array() ) {

	$results = array();

	if( empty( $pids ) ) {
		return $results;
	}

	global $wpdb;

	$variable_taxonomy_ids = $wpdb->get_col( $wpdb->prepare( "SELECT taxonomy.term_taxonomy_id as term_taxonomy_id
														FROM {$wpdb->prefix}terms as terms
															JOIN {$wpdb->prefix}term_taxonomy as taxonomy 
															ON (taxonomy.term_id = terms.term_id
															  AND taxonomy.taxonomy = %s)
														WHERE terms.slug IN ('variable', 'variable-subscription')", 'product_type' ) );

	//query to get the parent ids old title
	$results = $wpdb->get_results( $wpdb->prepare( "SELECT id, post_title 
							FROM {$wpdb->posts} as p
							  JOIN {$wpdb->prefix}term_relationships as tp
								ON(tp.object_id = p.id
								  AND p.post_type = %s)
							  WHERE p.id IN (". implode(",",$pids) .")
								AND tp.term_taxonomy_id IN (". implode(",",$variable_taxonomy_ids) .")", 'product' ), ARRAY_A );

	return $results;
}

//function to sync the variations title when the parent product title is updated
function sm_sync_variation_title( $new_title_update_case, $ids ) {

	if( !empty( $new_title_update_case ) && !empty( $ids ) ) {

		global $wpdb;

		$wpdb->query( $wpdb->prepare(
						  "UPDATE {$wpdb->posts}
						  SET post_title = (CASE ". implode(" ",$new_title_update_case) ." END)
						  WHERE post_type = %s
						  AND post_parent IN (". implode(",",$ids) .")",
						  'product_variation'
					  )
				  );
	}
}

function sm_update_price_meta( $ids ) {

	if( !empty($ids) ) {

		global $wpdb;

		$query = "SELECT post_id,
					  GROUP_CONCAT( meta_key ORDER BY meta_id SEPARATOR '##' ) AS meta_keys, 
					  GROUP_CONCAT( meta_value ORDER BY meta_id SEPARATOR '##' ) AS meta_values 
				  FROM {$wpdb->prefix}postmeta 
				  WHERE meta_Key IN ( '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to' ) 
					AND post_id IN (".implode(",", $ids).")
				  GROUP BY post_id";
		$results = $wpdb->get_results( $query, 'ARRAY_A' );

		$update_cases = array();
		$ids_to_be_updated = array();

		foreach ( $results as $result ) {
			$meta_keys = explode( '##', $result['meta_keys'] );
			$meta_values = explode( '##', $result['meta_values'] );

			if ( count( $meta_keys ) == count( $meta_values ) ) {
				$keys_values = array_combine( $meta_keys, $meta_values );

				$from_date = (isset($keys_values['_sale_price_dates_from'])) ? $keys_values['_sale_price_dates_from'] : '';
				$to_date = (isset($keys_values['_sale_price_dates_to'])) ? $keys_values['_sale_price_dates_to'] : '';

				$regular_price = isset( $keys_values['_regular_price'] ) ? trim( $keys_values['_regular_price'] ) : '';
				$sale_price = isset( $keys_values['_sale_price'] ) ? trim( $keys_values['_sale_price'] ) : '';

				$price = sm_woo_get_price( $regular_price, $sale_price, $from_date, $to_date);

				$price = trim($price); // For handling when both price and sales price are null

				$meta_value = (!empty($price)) ? $price : '';

				update_post_meta($result['post_id'], '_price', $meta_value);
			}
		}
	}
}

//Function to detect whether a string is timestamp or not
function isTimestamp( $string ) { 
    try {
        new DateTime('@' . $string);
    } catch(Exception $e) {
        return false;
    }

    if( $string < strtotime('-30 years') || $string > strtotime('+30 years') ) {
       return false;
    }

	return true;
}

/**
 * This function will update the WC lookup table introduced in WC 3.6 for the edited product fields in SM
 * 
 * Since SM 4.2.3
 * For WC 3.6+
 */
function sm_update_product_lookup_table( $product_ids ) {

	if ( empty( $product_ids ) ) {
		return;
	}

	global $wpdb;

	$query = "SELECT post_id, meta_key, meta_value
				FROM {$wpdb->prefix}postmeta 
				WHERE meta_key IN ( '_sku', '_virtual', '_downloadable', '_regular_price', '_sale_price', '_price', '_manage_stock', '_stock', '_stock_status', '_wc_rating_count', '_wc_average_rating', 'total_sales'
				". ( ( !empty( Smart_Manager::$sm_is_woo40 ) ) ? ", '_tax_status', '_tax_class'" : '' ) ." ) 
				AND post_id IN (".implode(",", $product_ids).")
					GROUP BY post_id, meta_key";

	$results = $wpdb->get_results( $query, 'ARRAY_A' );

	$sm_cache_update = array();
	$sm_update_wc_lookup_table = array();
	$temp = array();

	// Preparing data
	foreach ( $results as $result ) {

		$meta_key = ( !empty( $result['meta_key'] ) ) ? $result['meta_key'] : '';
		if( empty( $meta_key ) ) {
			continue;
		}
		$meta_value = ( !empty( $result['meta_value'] ) ) ? $result['meta_value'] : '';

		$product_id = absint( $result['post_id'] );
		
		if( empty( $sm_cache_update[$product_id] ) ) {
			$sm_cache_update[$product_id] = array();
		}

		$price_meta = (array) ( $meta_key == '_price' ? $meta_value : false );

		$sm_cache_update[$product_id]['product_id'] 	= ( empty( $sm_cache_update[$product_id]['product_id'] ) ) ? $product_id : $sm_cache_update[$product_id]['product_id'];
		$sm_cache_update[$product_id]['sku'] 			= ( empty( $sm_cache_update[$product_id]['sku'] ) ) ? ( ( $meta_key == '_sku' ) ? $meta_value : '' ) : $sm_cache_update[$product_id]['sku'];
		$sm_cache_update[$product_id]['virtual'] 		= ( empty( $sm_cache_update[$product_id]['virtual'] ) ) ? ( ( $meta_key == '_virtual' && 'yes' === $meta_value ) ? 1 : 0 ) : $sm_cache_update[$product_id]['virtual'];
		$sm_cache_update[$product_id]['downloadable'] 	= ( empty( $sm_cache_update[$product_id]['downloadable'] ) ) ? ( ( $meta_key == '_downloadable' && 'yes' === $meta_value ) ? 1 : 0 ) : $sm_cache_update[$product_id]['downloadable'];
		$sm_cache_update[$product_id]['min_price'] 		= ( empty( $sm_cache_update[$product_id]['min_price'] ) ) ? ( reset( $price_meta ) ) : $sm_cache_update[$product_id]['min_price'];
		$sm_cache_update[$product_id]['max_price'] 		= ( empty( $sm_cache_update[$product_id]['max_price'] ) ) ? ( end( $price_meta ) ) : $sm_cache_update[$product_id]['max_price'];
		$sm_cache_update[$product_id]['onsale'] 		= ( empty( $sm_cache_update[$product_id]['onsale'] ) ) ? ( wc_format_decimal( ( $meta_key == '_sale_price' && !empty( $meta_value ) ) ? 1 : 0 ) ) : $sm_cache_update[$product_id]['onsale'];
		$sm_cache_update[$product_id]['stock_quantity'] = ( empty( $sm_cache_update[$product_id]['stock_quantity'] ) ) ? ( wc_stock_amount( ( $meta_key == '_stock' ) ? $meta_value : null ) ) : $sm_cache_update[$product_id]['stock_quantity'];
		$sm_cache_update[$product_id]['stock_status'] 	= ( empty( $sm_cache_update[$product_id]['stock_status'] ) ) ? ( ( $meta_key == '_stock_status' ) ? $meta_value : '' ) : $sm_cache_update[$product_id]['stock_status'];
		$sm_cache_update[$product_id]['rating_count'] 	= ( empty( $sm_cache_update[$product_id]['rating_count'] ) ) ? ( ( $meta_key == '_wc_rating_count' && is_array( maybe_unserialize( $meta_value ) ) ) ? array_sum( maybe_unserialize( $meta_value ) ) : 0 ) : $sm_cache_update[$product_id]['rating_count'];
		$sm_cache_update[$product_id]['average_rating'] = ( empty( $sm_cache_update[$product_id]['average_rating'] ) ) ? ( ( $meta_key == '_wc_average_rating' ) ? $meta_value : 0 ) : $sm_cache_update[$product_id]['average_rating'];
		$sm_cache_update[$product_id]['total_sales'] 	= ( empty( $sm_cache_update[$product_id]['total_sales'] ) ) ? ( ( $meta_key == 'total_sales' ) ? $meta_value : 0 ) : $sm_cache_update[$product_id]['total_sales'];
		$sm_cache_update[$product_id]['tax_status'] 	= ( empty( $sm_cache_update[$product_id]['tax_status'] ) ) ? ( ( $meta_key == '_tax_status' ) ? $meta_value : '' ) : $sm_cache_update[$product_id]['tax_status'];
		$sm_cache_update[$product_id]['tax_class'] 	= ( empty( $sm_cache_update[$product_id]['tax_class'] ) ) ? ( ( $meta_key == '_tax_class' ) ? $meta_value : '' ) : $sm_cache_update[$product_id]['tax_class'];
		$temp = $sm_cache_update;
		$temp[$product_id]['sku'] = (string) $temp[$product_id]['sku'];
		$temp[$product_id]['stock_status'] = (string) $temp[$product_id]['stock_status'];

		$sm_update_wc_lookup_table[$product_id] = "('".implode( "','", $temp[$product_id] )."')";

	}

	// Updating lookup table
	if ( ! empty( $sm_update_wc_lookup_table ) ) {
		$query = "REPLACE INTO {$wpdb->prefix}wc_product_meta_lookup
					VALUES ";
		$query .= implode( ",", $sm_update_wc_lookup_table );
		$wpdb->query( $query );
	}

	// wp_cache_set for lookup table
	if ( ! empty( $sm_cache_update ) ) {
		foreach ( $sm_cache_update as $update_data ) {
			wp_cache_set( 'lookup_table', $update_data, 'object_' . $update_data['product_id'] );
		}
	}
}

//Function to update product attribute lookup table
function sm_update_product_attribute_lookup_table( $product_ids = array() ) {

	if ( empty( $product_ids ) ) {
		return;
	}

	$insert_query_values = array();
	
	foreach ( $product_ids as $product_id ) {
		$product = wc_get_product( $product_id );
		if( empty( $product ) || ( ! empty( $product ) && ! $product instanceof WC_Product ) ) { 
			continue;
		}

		$product_attributes = ( is_callable( array( $product, 'get_attributes' ) ) ) ? $product->get_attributes() : array();
		if( empty( $product_attributes ) ) {
			continue;
		}

		$has_stock = ( ( is_callable( array( $product, 'get_stock_quantity' ) ) && ! empty( $product->get_stock_quantity() ) ) ||  ( is_callable( array( $product, 'is_in_stock' ) ) && ! empty( $product->is_in_stock() ) ) ) ? 1 : 0;

		foreach ( $product_attributes as $taxonomy_name => $attribute_data ) {
			if( empty( $attribute_data ) || ( ! empty( $attribute_data ) && is_callable( array( $attribute_data, 'get_id' ) ) && empty( $attribute_data->get_id() ) ) || empty( $taxonomy_name ) ) {
				continue;
			}
			$term_ids = ( is_callable( array( $attribute_data, 'get_options' ) ) ) ? $attribute_data->get_options() : array();
			if( empty( $term_ids ) ){
				continue;
			}
			$is_variation_attribute = ( is_callable( array( $attribute_data, 'get_variation' ) ) && ! empty( $attribute_data->get_variation() ) ) ? 1 : 0;
			foreach( $term_ids as $term_id ){	
				if( empty( $term_id ) ){
					continue;
				}
				if( empty( $is_variation_attribute ) ) {
					$insert_query_values[] = " ( ". $product_id .", ". $product_id .", '".$taxonomy_name."', ".$term_id.", ".$is_variation_attribute.", ".$has_stock." ) ";
				} else {
					$variation_ids = ( is_callable( array( $product, 'get_children' ) ) ) ? $product->get_children() : array();
					if( empty( $variation_ids ) ){
						continue;
					}
					foreach( $variation_ids as $variation_id ){
						$insert_query_values[] = " ( ". $variation_id .", ". $product_id .", '".$taxonomy_name."', ".$term_id.", ".$is_variation_attribute.", ".$has_stock." ) ";
					}
				}
			}		
		}
	}

	$delete_attribute_data = sa_sm_delete_attribute_lookup_data( $product_ids );
	if( ! empty( $insert_query_values ) ){
		sa_sm_update_attribute_lookup_data( $insert_query_values );	
	}
}

//Function to delete the attribute lookup table data
function sa_sm_delete_attribute_lookup_data( $product_ids = array() ) {
	if( empty( $product_ids ) ){
		return;
	}
	global $wpdb;
	$wpdb->query(
		"DELETE FROM {$wpdb->prefix}wc_product_attributes_lookup WHERE product_id IN (".implode(',', $product_ids).") OR product_or_parent_id IN (".implode( ',', $product_ids ).")"
	);
}

//Function for updating attribute lookup table
function sa_sm_update_attribute_lookup_data( $insert_query_values = array() ) {
	if( empty( $insert_query_values ) ){
		return;
	}
	global $wpdb;			
	$wpdb->query(
				"INSERT INTO {$wpdb->prefix}wc_product_attributes_lookup(
				product_id,
				product_or_parent_id,
				taxonomy,
				term_id,
				is_variation_attribute,
				in_stock)
				VALUES". implode( ", ", $insert_query_values ) ."" 		
			);			
}

//Function to generate the column state using store model
function sa_sm_generate_column_state( $store_model = array() ) {

	$column_model_transient = array( 'columns' => array(), 'sort_params' => array() );

	if( !empty( $store_model['columns'] ) ) {
		foreach( $store_model['columns'] as $key => $col ) {
			if( empty( $col['hidden'] ) && ! empty( $col['save_state'] ) && ! empty( $col['data'] ) ) {
				$column_model_transient['columns'][ $col['data'] ] = array( 'width' => ( !empty( $col['width'] ) ? $col['width'] : '' ),
																			'position' => ( !empty( $col['position'] ) ? $col['position'] : '' ) );
			}
		
		}
	}

	$column_model_transient['sort_params'] = ( !empty( $store_model['sort_params'] ) ) ? $store_model['sort_params'] : array();
	if ( ! empty( $store_model['search_params'] ) ) {
		$column_model_transient['search_params'] = $store_model['search_params'];
	}
	if( isset( $store_model['treegrid'] ) ) {
		$column_model_transient['treegrid'] = $store_model['treegrid'];
	}

	$column_model_transient = apply_filters( 'sm_generate_column_state', $column_model_transient, $store_model );

	return $column_model_transient;
}

//Function to update recent dashboards
function sa_sm_update_recent_dashboards( $meta_key = 'post_types', $slug = '' ) {
			
	if( empty( $meta_key ) || empty( $slug ) ) {
		return;
	}

	$recent_dashboards = get_user_meta( get_current_user_id(), 'sa_sm_recent_'.$meta_key, true );
	if( ! empty( $recent_dashboards ) ){
		$index = array_search( $slug, $recent_dashboards );
		if( false !== $index ) {
			array_splice( $recent_dashboards, $index, 1 );
		}
		array_unshift( $recent_dashboards, $slug );
		$recent_dashboards = array_slice( $recent_dashboards, 0, 3 );
	} else {
		$recent_dashboards = array( $slug );
	}

	if( is_array( $recent_dashboards ) ) {
		update_user_meta( get_current_user_id(), 'sa_sm_recent_'.$meta_key, $recent_dashboards );
	}
}

/**
 * Custom function to update post - Compat for WooCommerce Product Stock Alert plugins
 *
 * @param int $id post id for which is to be updated.
 * @return result of function call
 */
function sm_update_post( $id = 0 ) {
	$id = intval( $id );
	if( empty( $id ) ) {
		return;
	}
	$parent_id = wp_get_post_parent_id( $id );
	return ( empty( $parent_id ) ) ? wp_update_post( array( 'ID' => $id ) ) : wp_update_post( array( 'ID' => $parent_id ) );
}
