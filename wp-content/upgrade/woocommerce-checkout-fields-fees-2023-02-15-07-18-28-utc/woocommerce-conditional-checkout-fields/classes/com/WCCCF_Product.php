<?php 
class WCCCF_Product
{
	var $product_ids_to_which_was_bounded_last_field = array();
	var $conditional_rule_additional_data = array();
	public function __construct()
	{
		add_action('wp_ajax_wcccf_get_product_list', array(&$this, 'ajax_load_product_list'));
		add_action('wp_ajax_wcccf_get_category_list', array(&$this, 'ajax_load_category_list'));
		add_action('wp_ajax_wcccf_get_tag_list', array(&$this, 'ajax_load_tag_list'));
		
	}
	public function reset_internal_state()
	{
		$this->product_ids_to_which_was_bounded_last_field = array();
		$this->conditional_rule_additional_data = array();
	}
	public function ajax_load_category_list()
	{
		$product_categories = $this->get_product_category_list($_GET['product_category']);
		echo json_encode( $product_categories);
		wp_die();
	}
	public function ajax_load_tag_list()
	{
		$product_tags = $this->get_product_category_list($_GET['product_tag'], 'product_tag');
		echo json_encode( $product_tags);
		wp_die();
	}
	function ajax_load_product_list()
	{
		$resultCount = 50;
		$search_string = isset($_GET['search_string']) ? $_GET['search_string'] : null;
		$page = isset($_GET['page']) ? $_GET['page'] : null;
		$offset = isset($page) ? ($page - 1) * $resultCount : null;
		$product_list = $this->get_product_list($search_string ,$offset, $resultCount);
		echo json_encode( $product_list); 
		wp_die();
	}
	function get_product_list($search_string ,$offset, $resultCount)
	{
		global $wpdb, $wcccf_wpml_helper;
		 $query_string = "SELECT products.ID as id, products.post_parent as product_parent, products.post_title as product_name, product_meta.meta_value as product_sku
							 FROM {$wpdb->posts} AS products
							 LEFT JOIN {$wpdb->postmeta} AS product_meta ON product_meta.post_id = products.ID AND product_meta.meta_key = '_sku'
							 WHERE  (products.post_type = 'product' OR products.post_type = 'product_variation')
							";
		if($search_string)
				$query_string .=  " AND ( products.post_title LIKE '%{$search_string}%' OR product_meta.meta_value LIKE '%{$search_string}%' OR products.ID LIKE '%{$search_string}%' ) 
								   AND (products.post_type = 'product' OR products.post_type = 'product_variation') ";
		
		$query_string .=  " GROUP BY products.ID ";
		
		$result = $wpdb->get_results($query_string ) ;
		
		if($wcccf_wpml_helper->wpml_is_active())
		{
			$product_ids = $variation_ids = array();
			foreach($result as $product)
			{
				if($product->product_parent == 0 )
					$product_ids[] = $product;
				else
					$variation_ids[] = $product;
			}
			//$result = $wcccf_wpml_helper->remove_translated_id($result, 'product', true);
			
			//Filter products
			if(!empty($product_ids))
				$product_ids = $wcccf_wpml_helper->remove_translated_id($product_ids, 'product', true);
			
			//Filter variations
			if(!empty($variation_ids))
				$variation_ids = $wcccf_wpml_helper->remove_translated_id($variation_ids, 'product', true);
			
			$result = array_merge($product_ids, $variation_ids);
		}
		
		if(isset($result) && !empty($result))
			foreach($result as $index => $product)
				{
					if($product->product_parent != 0 )
					{
						$readable_name = $this->get_variation_complete_name($product->id);
						$result[$index]->product_name = $readable_name != false ? "<i>".esc_html__('Variation','woocommerce-files-upload')."</i> ".$readable_name : $result[$index]->product_name;
					}
				}
		
		
		if(isset($offset) && isset($resultCount))
		{
			
			$num_order = $wpdb->get_col($query_string);
			$num_order = isset($num_order[0]) ? $num_order[0] : 0;
			$endCount = $offset + $resultCount;
			$morePages = $num_order > $endCount;
			$results = array(
				  "results" => $result,
				  "pagination" => array(
					  "more" => $morePages
				  )
			  );
		}
		else
			$results = array(
				  "results" => $result,
				  "pagination" => array(
					  "more" => false
				  )
			  );
		
		return $results;
	}
	public function get_product_category_list($search_string = null, $taxonomy = 'product_cat')
	 {
		 global $wpdb, $wcccf_wpml_helper;
		  $query_string = "SELECT product_categories.term_id as id, product_categories.name as category_name
							 FROM {$wpdb->terms} AS product_categories
							 LEFT JOIN {$wpdb->term_taxonomy} AS tax ON tax.term_id = product_categories.term_id 							 						 	 
							 WHERE tax.taxonomy = '{$taxonomy}' 
							 AND product_categories.slug <> 'uncategorized' 
							";
		 if($search_string)
					$query_string .=  " AND ( product_categories.name LIKE '%{$search_string}%' )";
			
		$query_string .=  " GROUP BY product_categories.term_id ";
		$result = $wpdb->get_results($query_string ) ;
		
		//WPML
		if($wcccf_wpml_helper->wpml_is_active())
		{
			$result = $wcccf_wpml_helper->remove_translated_id($result, 'product_cat', true);
		} 
		
		return $result;
	 }
	 public function get_product_tag_list($search_string = null)
	 {
		 return $terms = get_terms( array(
				'taxonomy' => 'product_tag',
				'hide_empty' => false,
				'name__like' => $search_string
			) );
	 }
	 public function get_variation_complete_name($variation_id)
	 {
		
		$error = false;
		$variation = wc_get_product($variation_id);
		if($variation == null || $variation == false)
			return "";
		if($variation->is_type('simple') || $variation->is_type('variable'))
			return $variation->get_title();
		
		
		$product_name = $variation->get_title()." - ";	
		if($product_name == " - ")
			return false;
		$attributes_counter = 0;
		foreach($variation->get_variation_attributes( ) as $attribute_name => $value)
		{
			
			if($attributes_counter > 0)
				$product_name .= ", ";
			$meta_key = urldecode( str_replace( 'attribute_', '', $attribute_name ) ); 
			
			$product_name .= " ".wc_attribute_label($meta_key).": ".$value;
			$attributes_counter++;
		}
		return $product_name;
	 }
	public function get_product_name($product_id, $default = false)
	{
		global $wcccf_wpml_helper;
		$product_id = $wcccf_wpml_helper->get_main_language_id($product_id, 'product');
		$readable_name  = $default;
		$product = wc_get_product($product_id);
		
		if(!isset($product) || $product == false)
			return "";
		
		if($product->get_type() == 'variation')
		{
			$readable_name = $this->get_variation_complete_name($product_id);
			$readable_name = isset($readable_name) && $readable_name != "" && $readable_name != " " ? "#".$product_id." - ".$readable_name  : $default;
		}
		else
		{
			try{
			   
			    $readable_name = isset($product) ? $product->get_formatted_name() : $default;
		    }catch (Exception $e){}
		}
		return $readable_name; //isset($product) ? $product->get_formatted_name() : $default;
	}
	public function get_product_category_name($category_id, $default = false)
	{
		global $wcccf_wpml_helper;
		$category_id = $wcccf_wpml_helper->get_main_language_id($category_id, 'product_cat');
		$category = get_term( $category_id, 'product_cat' );
		return isset($category) ? $category->name : $default;
	}
	public function get_product_tag_name($tag_id, $default = false)
	{
		global $wcccf_wpml_helper;
		$category_id = $wcccf_wpml_helper->get_main_language_id($tag_id, 'product_tag');
		$tag = get_term( $category_id, 'product_tag' );
		return isset($tag) ? $tag->name : $default;
	}
	public function get_product_id_by_category_id($cat_id)
	{
		
		$args = array(
			'fields'        		=> 'ids',
			'post_type'             => 'product',
			'post_status'           => 'publish',
			
			'posts_per_page'        => -1,
			'meta_query'            => array(
				array(
					'key'           => '_visibility',
					'value'         => array('catalog', 'visible'),
					'compare'       => 'IN'
				)
			),
			'tax_query'             => array(
				array(
					'taxonomy'      => 'product_cat',
					'field' => 'term_id', //This is optional, as it defaults to 'term_id'
					'terms'         => $cat_id,
					'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
				)
			)
		);
		$products = get_posts($args);
		
		return $products;
	}
	public function taxonomy_satisfy_conditional_rule($condition, $taxonomy = 'product_cat', $consider_only_product_bounded_fields = false)
	{
		global $woocommerce, $wcccf_wpml_helper;
		$condition['product_id'] = array();
		$results = array();
		$final_result = true;
		$cart_items = $woocommerce->cart->get_cart();
		$condition_type = $taxonomy == 'product_cat' ? 'category' : 'tag'; 
		$method_name = $taxonomy == 'product_cat' ? 'get_category_ids' : 'get_tag_ids'; 
		$num_of_category_present_on_cart = array();
		$condition['product_id'] = array();
		foreach($condition[$condition_type.'_id'] as $taxonomy_id)
		{
			//$condition['product_id'] = array();
			foreach($woocommerce->cart->get_cart() as $cart_item)
			{
				$product_id = $wcccf_wpml_helper->get_main_language_id($cart_item['data']->get_id());
				if($cart_item['data']->get_type() == 'variation')
					$product_id = $wcccf_wpml_helper->get_main_language_id($cart_item['data']->get_parent_id());
				
				$product =  wc_get_product( $product_id );
				$taxonomies_id = $product->$method_name(); //NOTE: It happens that 'get_category_ids' doesn't return the associted categories
				$taxonomies_id =  $wcccf_wpml_helper->get_main_language_ids($taxonomies_id, $taxonomy);
			
				if(in_array($taxonomy_id, $taxonomies_id ))
				{
					$num_of_category_present_on_cart[$taxonomy_id] = true;
					$condition['product_id'][] = $product_id;
				}
			}
			
		}
		$results[] = $this->products_satisfy_conditional_rule($condition,  $condition_type, $consider_only_product_bounded_fields);
		foreach($results as $result)
			$final_result = $result ? $final_result : false;
		
		
		//Cart
		if($condition[$condition_type.'_cart_presence_policy'] == 'selected_items_must_be_present' && count($num_of_category_present_on_cart) != count($condition[$condition_type.'_id'])) //selected_items_must_be_present || items_actually_present
				$final_result = false;
		
		return $final_result;
	}
	public function products_satisfy_conditional_rule($condition, $condition_type = 'product', $consider_only_product_bounded_fields = false)
	{
		
		global $woocommerce, $wcccf_wpml_helper;
		
		if($condition_type == 'product')
			$this->reset_internal_state();
		
		$cart_items = $woocommerce->cart->get_cart();
		$item_to_consider = array();
		$item_left = $condition['product_id'];
		$products_values_to_check = array('sum_of_values' => 0, 'min_value' => 0 ,'max_value' => 0);
		$result = false;
		foreach($woocommerce->cart->get_cart() as $cart_item)
		{
			$quantity = $cart_item["quantity"];
			$product_id = $wcccf_wpml_helper->get_main_language_id($cart_item['data']->get_id());
			$parent_id = $cart_item['variation_id'] != 0 ? $wcccf_wpml_helper->get_main_language_id($cart_item['product_id']) : 0;
			
			
			if(!in_array($product_id, $condition['product_id']) && ($parent_id == 0 || !in_array($parent_id, $condition['product_id'])))
				continue;
			
			//$item_left will be used lately to understand if all the condition item were present in cart
			$product_id_to_search = $product_id;
			if(!in_array($product_id, $condition['product_id']))	
				$product_id_to_search = $parent_id;
			if(($key = array_search($product_id_to_search, $item_left)) !== false) 
				unset($item_left[$key]);

			if($condition[$condition_type.'_condition_type'] == 'cart')
			{
				$products_values_to_check['sum_of_values'] += $quantity;
				$item_to_consider[] = array('product_id' => $product_id, 'value' => $quantity);
			}
			else 
			{
				$value = 0;
				$product =  wc_get_product( $product_id );
				switch($condition[$condition_type.'_condition_type'])
				{
					case 'amount_spent': $value = ($cart_item['line_subtotal'] + $cart_item['line_tax']); 
								  $products_values_to_check['min_value'] = $value < $products_values_to_check['min_value'] ? $value : 0;
								  $products_values_to_check['max_value'] = $value > $products_values_to_check['max_value'] ? $value : 0;
								  $products_values_to_check['sum_of_values'] += $value;
					break;
					case 'amount_spent_ex_taxes': $value = ($cart_item['line_subtotal'] ); 
								  $products_values_to_check['min_value'] = $value < $products_values_to_check['min_value'] ? $value : 0;
								  $products_values_to_check['max_value'] = $value > $products_values_to_check['max_value'] ? $value : 0;
								  $products_values_to_check['sum_of_values'] += $value;
					break;
					case 'stock': $value = $product->get_stock_quantity(); 
								  $value = $value == "" ? 0 : $value;
								  $products_values_to_check['min_value'] = $value < $products_values_to_check['min_value'] ? $value : 0;
								  $products_values_to_check['max_value'] = $value > $products_values_to_check['max_value'] ? $value : 0;
								  $products_values_to_check['sum_of_values'] += $value;
					break;
					case 'stock_status': $value = $product->get_stock_status(); 
										 $condition[$condition_type.'_value_considered'] = 'each_value'; //cannot sum the stock statuses, the value to be considered policed is forced to switch to each product value
					break;
					case 'weight': $value = $product->get_weight();
								   $value = $value == "" ? 0 : $value;
								   $products_values_to_check['sum_of_values'] += $value;
								   $products_values_to_check['min_value'] = $value < $products_values_to_check['min_value'] ? $value : 0;
								   $products_values_to_check['max_value'] = $value > $products_values_to_check['max_value'] ? $value : 0;
					break;
					case 'height': $value = $product->get_height();
								   $value = $value == "" ? 0 : $value;
								   $products_values_to_check['sum_of_values'] += $value;
								   $products_values_to_check['min_value'] = $value < $products_values_to_check['min_value'] ? $value : 0;
								   $products_values_to_check['max_value'] = $value > $products_values_to_check['max_value'] ? $value : 0;
					break;
					case 'length': $value = $product->get_length();
								   $value = $value == "" ? 0 : $value;
								   $products_values_to_check['sum_of_values'] += $value;
								   $products_values_to_check['min_value'] = $value < $products_values_to_check['min_value'] ? $value : 0;
								   $products_values_to_check['max_value'] = $value > $products_values_to_check['max_value'] ? $value : 0;
					break;
					case 'width': $value = $product->get_width();
								  $value = $value == "" ? 0 : $value;
								  $products_values_to_check['sum_of_values'] += $value;
								  $products_values_to_check['min_value'] = $value < $products_values_to_check['min_value'] ? $value : 0;
								  $products_values_to_check['max_value'] = $value > $products_values_to_check['max_value'] ? $value : 0;
					break;
					case 'volume': $width = $product->get_width() == "" ? 0 : $product->get_width();
								   $height = $product->get_height() == "" ? 0 : $product->get_height();
								   $length = $product->get_length() == "" ? 0 : $product->get_length();
								   $value = $width * $height * $length;
								   $products_values_to_check['sum_of_values'] += $value;
								   $products_values_to_check['min_value'] = $value < $products_values_to_check['min_value'] ? $value : 0;
								   $products_values_to_check['max_value'] = $value > $products_values_to_check['max_value'] ? $value : 0;
					break;
					case 'booking_person':  
									$value = isset($cart_item["booking"][esc_html__( 'Persons', 'woocommerce-bookings' )]) ? $cart_item["booking"][esc_html__( 'Persons', 'woocommerce-bookings' )] : 0;
								   $products_values_to_check['sum_of_values'] += $value;
								   $products_values_to_check['min_value'] = $value < $products_values_to_check['min_value'] ? $value : 0;
								   $products_values_to_check['max_value'] = $value > $products_values_to_check['max_value'] ? $value : 0;
					break;
				}
				
				$item_to_consider[] = array('product_id' => $product_id, 'value' => $value);
			}
			
			if($condition[$condition_type.'_value_considered'] == 'sum_of_values' ||
				$condition[$condition_type.'_value_considered'] == 'min_value' ||
				$condition[$condition_type.'_value_considered'] == 'max_value' ) //sum_of_values || each_value || min_value || max_value
			{
				$value_to_check = $products_values_to_check[$condition[$condition_type.'_value_considered']];
				
				if(($condition[$condition_type.'_operator'] == 'lesser_equal' && $value_to_check <= $condition[$condition_type.'_value'])||
					($condition[$condition_type.'_operator'] == 'greater_equal' && $value_to_check >= $condition[$condition_type.'_value']) ) //lesser_equal || greater_equal
				{
					$result = true;
				}
			}
			else if($condition[$condition_type.'_value_considered'] == 'each_value')
			{
				$result = true;
				
				
				
				foreach($item_to_consider as $tmp_item)
				{
					$result = ($condition[$condition_type.'_operator'] == 'lesser_equal' && $tmp_item['value'] <= $condition[$condition_type.'_value']) || ($condition[$condition_type.'_operator'] == 'greater_equal' && $tmp_item['value'] >= $condition[$condition_type.'_value']) ? $result : false;
				}
			}
			
		
			//Cart presence policy (Only for Product rules)
			if($condition_type == 'product' && $condition[$condition_type.'_cart_presence_policy'] == 'selected_items_must_be_present' && count($item_left) > 0) //selected_items_must_be_present || items_actually_present
				$result = false;
			
			if($result)
			{
				$this->product_ids_to_which_was_bounded_last_field[] = $cart_item['product_id']."-".$cart_item['variation_id'];
				$this->conditional_rule_additional_data = array('item_to_consider' => $item_to_consider);
			}
		}
		
		if(isset($condition[$condition_type.'_display_one_field_for_each_person']) && $condition[$condition_type.'_display_one_field_for_each_person'] == 'yes' && !$consider_only_product_bounded_fields)
		{
			$this->reset_internal_state();
			return false;
		} 
		else if($consider_only_product_bounded_fields && isset($condition[$condition_type.'_display_one_field_for_each_person']) && $condition[$condition_type.'_display_one_field_for_each_person'] == 'no')
		{
			
			$this->reset_internal_state();
			return false;
		}
		
			
		return $result;
	}
}
?>