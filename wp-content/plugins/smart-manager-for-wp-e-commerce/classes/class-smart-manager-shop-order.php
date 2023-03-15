<?php

if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Smart_Manager_Shop_Order' ) ) {
	class Smart_Manager_Shop_Order extends Smart_Manager_Base {
		public $dashboard_key = '',
			$default_store_model = array(),
			$order_old_statuses = array();
		public static $kpi_query_results = array();

		function __construct($dashboard_key) {
			parent::__construct($dashboard_key);

			$this->dashboard_key = $dashboard_key;
			$this->post_type = $dashboard_key;
			$this->req_params  	= (!empty($_REQUEST)) ? $_REQUEST : array();
			
			add_filter( 'sm_dashboard_model',array( &$this,'orders_dashboard_model' ), 10, 2 );
			add_filter( 'sm_data_model', array( &$this, 'orders_data_model' ), 10, 2 );

			add_filter('posts_where',array(&$this,'sm_query_orders_where_cond'),100,2);
			add_filter('posts_join_paged',array(&$this,'sm_query_join'),100,2);
			add_filter('posts_orderby',array(&$this,'sm_query_order_by'),100,2);

			add_filter( 'sm_batch_update_copy_from_ids_select',array( &$this,'sm_batch_update_copy_from_ids_select' ), 10, 2 );

			add_filter('found_posts',array( 'Smart_Manager_Shop_Order' ,'kpi_data_query'),100,2);

			add_filter( 'sm_inline_update_pre', array( &$this, 'pre_inline_update' ), 10, 1 );
		}

		//Fucntion for overriding the select clause for fetching the ids for batch update 'copy from' functionality
		public function sm_batch_update_copy_from_ids_select( $select, $args ) {

			$select = " SELECT ID AS id, CONCAT('Order #', ID) AS title ";
			return $select;
		}

		//Function to generate the column model fr orders custom columns
		public static function generate_orders_custom_column_model( $column_model ) {

			global $wpdb;

			$custom_columns = array( 'shipping_method', 'coupons_used', 'line_items', 'details', 'order_sub_total' );
			$order_items_table_searchable_cols = array( 'shipping_method', 'coupons_used' );
			$index = sizeof($column_model);

			foreach( $custom_columns as $col ) {

				$src = ( in_array( $col, $order_items_table_searchable_cols ) ? 'woocommerce_order_items/' : 'custom/' ). $col;

				$col_index = sm_multidimesional_array_search ($src, 'src', $column_model);

				if( empty( $col_index ) ) {
					$column_model [$index] = array();
					$column_model [$index]['src'] = $src;
					$column_model [$index]['data'] = sanitize_title(str_replace('/', '_', $column_model [$index]['src'])); // generate slug using the wordpress function if not given 
					$column_model [$index]['name'] = __(ucwords(str_replace('_', ' ', $col)), 'smart-manager-for-wp-e-commerce');
					$column_model [$index]['key'] = $column_model [$index]['name'];
					$column_model [$index]['type'] = 'text';
					$column_model [$index]['hidden']	= false;
					$column_model [$index]['editable']	= false;
					$column_model [$index]['editor']	= false;
					$column_model [$index]['batch_editable'] = false;
					$column_model [$index]['sortable']	= true;
					$column_model [$index]['resizable']	= true;
					$column_model [$index]['allow_showhide'] = true;
					$column_model [$index]['exportable']	= true;
					$column_model [$index]['searchable']	= in_array( $col, $order_items_table_searchable_cols ) ? true : false;
					$column_model [$index]['save_state'] = true;
					$column_model [$index]['values'] = array();
					$column_model [$index]['search_values'] = array();

					if( in_array( $col, $order_items_table_searchable_cols ) ) {
						$column_model [$index]['table_name'] = $wpdb->prefix.'woocommerce_order_items';
						$column_model [$index]['col_name'] = $col;
					}
					$index++;
				}
			}

			return $column_model;
		}

		public function orders_dashboard_model ($dashboard_model, $dashboard_model_saved) {
			global $wpdb, $current_user;

			$dashboard_model['tables']['posts']['where']['post_type'] = 'shop_order';

			$visible_columns = array('ID', 'post_date', '_billing_first_name', '_billing_last_name', '_billing_email', 'post_status', '_order_total', 'details', '_payment_method_title', 'shipping_method', 'coupons_used', 'line_items');

			$numeric_columns = array('_billing_phone', '_cart_discount', '_cart_discount_tax', '_customer_user');

			$string_columns = array('_billing_postcode', '_shipping_postcode');

			$post_status_col_index = sm_multidimesional_array_search('posts_post_status', 'data', $dashboard_model['columns']);
			
			if( function_exists('wc_get_order_statuses') ) {
				$order_statuses = wc_get_order_statuses();
			}
			
			$order_statuses_keys = ( !empty( $order_statuses ) ) ? array_keys($order_statuses) : array();

			$dashboard_model['columns'][$post_status_col_index]['defaultValue'] = ( !empty( $order_statuses_keys[0] ) ) ? $order_statuses_keys[0] : 'wc-pending';

			$dashboard_model['columns'][$post_status_col_index]['save_state'] = true;
			
			$dashboard_model['columns'][$post_status_col_index]['values'] = $order_statuses;
			$dashboard_model['columns'][$post_status_col_index]['selectOptions'] = $order_statuses; //for inline editing

			$color_codes = array( 'green' => array( 'wc-completed', 'wc-processing' ),
									'red' => array( 'wc-cancelled', 'wc-failed', 'wc-refunded' ),
									'orange' => array( 'wc-on-hold', 'wc-pending' ) );

			$dashboard_model['columns'][$post_status_col_index]['colorCodes'] = apply_filters( 'sm_'.$this->dashboard_key.'_status_color_codes', $color_codes );

			$dashboard_model['columns'][$post_status_col_index]['search_values'] = array();
			foreach ($order_statuses as $key => $value) {
				$dashboard_model['columns'][$post_status_col_index]['search_values'][] = array('key' => $key, 'value' => $value);
			}

			if( is_callable( array( 'Smart_Manager_Shop_Order', 'generate_orders_custom_column_model' ) ) ) {
				$dashboard_model['columns'] = self::generate_orders_custom_column_model( $dashboard_model['columns'] );
			}

			$column_model = &$dashboard_model['columns'];

			//Code for unsetting the position for hidden columns

			foreach( $column_model as &$column ) {
				
				if (empty($column['src'])) continue;

				$src_exploded = explode("/",$column['src']);

				if (empty($src_exploded)) {
					$src = $column['src'];
				}

				if ( sizeof($src_exploded) > 2) {
					$col_table = $src_exploded[0];
					$cond = explode("=",$src_exploded[1]);

					if (sizeof($cond) == 2) {
						$src = $cond[1];
					}
				} else {
					$src = $src_exploded[1];
					$col_table = $src_exploded[0];
				}


				if( empty($dashboard_model_saved) ) {
					if (!empty($column['position'])) {
						unset($column['position']);
					}

					$position = array_search($src, $visible_columns);

					if ($position !== false) {
						$column['position'] = $position + 1;
						$column['hidden'] = false;
					} else {
						$column['hidden'] = true;
					}
				}

				if ($src == 'post_date') {
					$column ['name'] = $column ['key'] = __('Date', 'smart-manager-for-wp-e-commerce');
				} else if ($src == 'post_status') {
					$column ['name'] = $column ['key'] = __('Status', 'smart-manager-for-wp-e-commerce');
				} else if ($src == 'post_excerpt') {
					$column ['name'] = $column ['key'] = __('Customer provided note', 'smart-manager-for-wp-e-commerce');
				} else if( !empty( $numeric_columns ) && in_array( $src, $numeric_columns ) ) {
					$column ['type'] = $column ['editor'] = 'numeric';
				} else if( !empty( $string_columns ) && in_array( $src, $string_columns ) ) {
					$column ['type'] = $column ['editor'] = 'text';
				}
			}

			if (!empty($dashboard_model_saved)) {
				$col_model_diff = sm_array_recursive_diff($dashboard_model_saved,$dashboard_model);	
			}

			//clearing the transients before return
			if (!empty($col_model_diff)) {
				delete_transient( 'sa_sm_'.$this->dashboard_key );	
			}

			return $dashboard_model;

		}


		public static function process_custom_search( $where, $params ) {

			global $wpdb;

			//Code for handling simple search
			if( empty( $params['search_text'] ) || strpos( $where, 'posts.ID IN' ) === true ) {
				return $where;
			}

			$search_text = $wpdb->_real_escape( $params['search_text'] );
			$skuOrderIds = $userOrderIds = array();
			$dashboard = ( !empty( $params['active_module'] ) ) ? $params['active_module'] : 'shop_order';

			//Query to get the post_id of the products whose sku code matches with the one type in the search text box of the Orders Module
			$pIds  = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT(post_id) FROM {$wpdb->prefix}postmeta
			              									WHERE meta_key = %s
			                 								AND meta_value LIKE %s", '_sku', '%' . $wpdb->esc_like($search_text) . '%') );
			if( count( $pIds ) > 0 ) {
				$skuOrderIds = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT(order_id)
							                                    FROM {$wpdb->prefix}woocommerce_order_items AS woocommerce_order_items
							                                    	LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woocommerce_order_itemmeta USING ( order_item_id )
							                                    WHERE woocommerce_order_itemmeta.meta_key IN ( %s, %s )
							                                    	AND woocommerce_order_itemmeta.meta_value IN ( ". implode( ',', $pIds ) ." )", '_product_id', '_variation_id') );
			}
			
			//Query to perform simple search in either of item names i.e. product_name, shipping_title, coupon_code
			$itemNameskuOrderIds = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT(order_id)
									                                FROM {$wpdb->prefix}woocommerce_order_items
									                                WHERE order_item_name LIKE %s", '%' . $wpdb->esc_like($search_text) . '%') );

			//Query for getting the user_id based on the email entered in the Search Box
            $userIds = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT(id)
														FROM {$wpdb->users} 
                    									WHERE user_email LIKE %s", '%' . $wpdb->esc_like($search_text) . '%' ) );

            if( count( $userIds ) > 0 ) {
            	$userOrderIds = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT(p.ID)
            														FROM {$wpdb->prefix}posts AS p
            															JOIN {$wpdb->prefix}postmeta AS pm
            																ON( pm.post_id = p.ID
            																	AND p.post_type = %s
            																	AND pm.meta_key = %s )
            														WHERE pm.meta_value IN( ". implode( ',', $userIds ) ." )", $dashboard, '_customer_user' ) );
            }

            if( !empty( $skuOrderIds ) || !empty( $itemNameskuOrderIds ) || !empty( $userOrderIds ) ) {
            	$orderIds = array_unique( array_merge( $skuOrderIds, $itemNameskuOrderIds, $userOrderIds ) );
            	$where = " AND {$wpdb->prefix}posts.ID IN(". implode( ',', $orderIds ) .") AND {$wpdb->prefix}posts.post_type = '". $dashboard ."' ";
            }

			return $where;
		}

		public function sm_query_orders_where_cond ($where, $wp_query_obj) {

			if( is_callable( array( 'Smart_Manager_Shop_Order', 'process_custom_search' ) ) ) {
				$where = self::process_custom_search( $where, $this->req_params );
			}

			return $where;
		}


		public static function generate_orders_custom_column_data( $data_model, $params ) {
			
			global $wpdb, $current_user;

			$order_ids = $order_coupons = array();
			$order_id_cond = '';
			$dashboard = ( !empty( $params['active_module'] ) ) ? $params['active_module'] : 'shop_order';

			if( !empty( $data_model['items'] ) ) {
				foreach( $data_model['items'] as $data ) {
					if( !empty( $data['posts_id'] ) ) {
						$order_ids[] = $data['posts_id'];
					}
				}	
			}

			if( !empty( $order_ids ) ) {
				if( count( $order_ids ) > 100 ) {
					$order_ids_imploded = implode(",",$order_ids);
					update_option( 'sa_sm_export_'.$dashboard.'_ids', $order_ids_imploded, 'no' );
					$order_id_cond = "SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'sa_sm_export_".$dashboard."_ids'";
					$csv_export = true;
				} else {
					$order_id_cond = implode(",",$order_ids);
					$csv_export = false;
				}	
			}

			if( !empty( $order_id_cond ) ) {

				$results_order_coupons = $wpdb->get_results( $wpdb->prepare( "SELECT order_id,
									                                        GROUP_CONCAT(order_item_name
									                                                            ORDER BY order_item_id 
									                                                            SEPARATOR ', ' ) AS coupon_used
									                                    FROM {$wpdb->prefix}woocommerce_order_items
									                                    WHERE order_item_type = %s
									                                    	".( ( !empty( $csv_export ) ) ? " AND FIND_IN_SET ( order_id, ( ".$order_id_cond." ) ) " : " AND order_id IN ( ".$order_id_cond." ) "  )."
									                                    GROUP BY order_id", 'coupon'), 'ARRAY_A' );

				if( !empty( $results_order_coupons ) ) {
					foreach( $results_order_coupons as $result ) {
	                    $order_coupons[$result['order_id']] = $result['coupon_used'];
	                } 
				}

				$variation_ids = $wpdb->get_col( $wpdb->prepare( "SELECT order_itemmeta.meta_value 
							                                        FROM {$wpdb->prefix}woocommerce_order_items AS order_items 
							                                           LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta 
							                                               ON (order_items.order_item_id = order_itemmeta.order_item_id)
							                                        WHERE order_itemmeta.meta_key = %s
							                                               AND order_itemmeta.meta_value > %d
							                                               ".( ( !empty( $csv_export ) ) ? " AND FIND_IN_SET ( order_items.order_id, ( ".$order_id_cond." ) ) " : " AND order_items.order_id IN ( ".$order_id_cond." ) "  ), '_variation_id', 0 ) );
	            
	            if ( count( $variation_ids ) > 0 ) {

	            	if( count( $variation_ids ) > 100 ) {
						$variation_ids_imploded = implode(",",$variation_ids);
						update_option( 'sa_sm_export_'.$dashboard.'_variation_ids', $variation_ids_imploded, 'no' );
						$variation_id_cond = " AND FIND_IN_SET ( postmeta.post_id, ( SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'sa_sm_export_".$dashboard."_variation_ids' ) ) ";
					} else {
						$variation_id_cond = " AND postmeta.post_id IN (". implode(",",$variation_ids) .") ";
					}

	                $results_variation_att = $wpdb->get_results( $wpdb->prepare( "SELECT postmeta.post_id AS post_id,
										                                                    GROUP_CONCAT(postmeta.meta_value
										                                                        ORDER BY postmeta.meta_id 
										                                                        SEPARATOR ',' ) AS meta_value
										                                            FROM {$wpdb->prefix}postmeta AS postmeta
										                                            WHERE postmeta.meta_key LIKE %s
										                                                ". $variation_id_cond ."
										                                            GROUP BY postmeta.post_id", 'attribute_%' ), 'ARRAY_A') ;
	            }
			}
			

			//Code to get the variation Attributes
			$attributes_terms = $wpdb->get_results( $wpdb->prepare( "SELECT terms.slug as slug, terms.name as term_name
										                          FROM {$wpdb->prefix}terms AS terms
										                            JOIN {$wpdb->prefix}postmeta AS postmeta 
										                                ON ( postmeta.meta_value = terms.slug 
										                                        AND postmeta.meta_key LIKE %s )
										                          GROUP BY terms.slug", 'attribute_%' ), 'ARRAY_A' );
            $attributes = array();
            foreach ( $attributes_terms as $attributes_term ) {
                $attributes[$attributes_term['slug']] = $attributes_term['term_name'];
            }
            
            $variation_att_all = array();

            if ( !empty($results_variation_att) && is_array( $results_variation_att ) && count( $results_variation_att ) > 0 ) {
                
                for ($i=0;$i<sizeof($results_variation_att);$i++) {
                    $variation_attributes = explode(", ",$results_variation_att [$i]['meta_value']);
                    
                    $attributes_final = array();
                    foreach ($variation_attributes as $variation_attribute) {
                        $attributes_final[] = (isset($attributes[$variation_attribute]) ? $attributes[$variation_attribute] : ucfirst($variation_attribute) );
                    }
                    
                    $results_variation_att [$i]['meta_value'] = implode(", ",$attributes_final);
                    $variation_att_all [$results_variation_att [$i]['post_id']] = $results_variation_att [$i]['meta_value'];
                }

            }

            //Code for handling search
            $order_id_join = '';
			if( !empty($params) && !empty($params['search_query']) && !empty($params['search_query'][0]) ) {
				$order_id_join = " JOIN {$wpdb->base_prefix}sm_advanced_search_temp as temp ON (temp.product_id = order_items.order_id)";
				$order_id_cond = ''; 
			} else if( !empty( $order_id_cond ) ) {
				$order_id_cond = ( ( !empty( $csv_export ) ) ? " AND FIND_IN_SET ( order_id, ( ".$order_id_cond." ) ) " : " AND order_id IN ( ".$order_id_cond." ) "  );
			}

			$order_items = array();
            $order_shipping_method = array();

            $results = $wpdb->get_results( $wpdb->prepare( "SELECT order_items.order_item_id,
				                            order_items.order_id    ,
				                            order_items.order_item_name AS order_prod,
				                            order_items.order_item_type,
				                            GROUP_CONCAT(order_itemmeta.meta_key
				                                                ORDER BY order_itemmeta.meta_id 
				                                                SEPARATOR '###' ) AS meta_key,
				                            GROUP_CONCAT(order_itemmeta.meta_value
				                                                ORDER BY order_itemmeta.meta_id 
				                                                SEPARATOR '###' ) AS meta_value
				                        FROM {$wpdb->prefix}woocommerce_order_items AS order_items 
				                            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta 
				                                ON (order_items.order_item_id = order_itemmeta.order_item_id
				                                AND order_items.order_item_type IN ('line_item', 'shipping') )
				                            ". $order_id_join ."
				                        WHERE 1 = %d
				                        	AND order_items.order_item_type IN ('line_item', 'shipping')
				                            ". $order_id_cond ."
				                        GROUP BY order_items.order_item_id", 1 ), 'ARRAY_A' );

            if ( !empty( $results ) ) {

                foreach ( $results as $result ) {

                    if ( !isset($order_items [$result['order_id']]) ) {
                        $order_items [$result['order_id']] = array();
                    }

                    if ($result['order_item_type'] == 'shipping') {
                        $order_shipping_method [$result['order_id']] = $result['order_prod'];
                    } else {
                        $order_items [$result['order_id']] [] = $result;
                    }

                }    
            }


            if( !empty( $data_model['items'] ) ) {
            	foreach( $data_model['items'] as $key => $order_data ) {

            		$order_id = ( !empty( $order_data['posts_id'] ) ) ? $order_data['posts_id'] : 0;

            		if( !empty( $order_items[$order_id] ) ) {

            			foreach( $order_items[$order_id] as $order_item ) {
            				$order_meta_values = explode('###', $order_item ['meta_value'] );
	                        $order_meta_key = explode('###', $order_item ['meta_key'] );

	                        if (count($order_meta_values) != count($order_meta_key)) {
	                            continue;
	                        }

	                        $order_meta_key_values = array_combine($order_meta_key, $order_meta_values);

	                        $data_model['items'][$key]['custom_details'] = intval ( (!empty($data_model['items'][$key]['custom_details'])) ? $data_model['items'][$key]['custom_details'] : 0 );
	                        $data_model['items'][$key]['custom_details'] += intval( ( !empty( $order_meta_key_values['_qty'] ) ) ? $order_meta_key_values['_qty'] : 0 );

	                        $product_id = ( $order_meta_key_values['_variation_id'] > 0 ) ? $order_meta_key_values['_variation_id'] : $order_meta_key_values['_product_id'];
		                    $sm_sku = get_post_meta( $product_id, '_sku', true );
		                    if ( ! empty( $sm_sku ) ) {
		                            $sku_detail = '[SKU: ' . $sm_sku . ']';
		                    } else {
		                            $sku_detail = '';
		                    }
		                    
		                    $variation_att = ( isset( $variation_att_all [$order_meta_key_values['_variation_id']] ) && !empty( $variation_att_all [$order_meta_key_values['_variation_id']] ) ) ? $variation_att_all [$order_meta_key_values['_variation_id']] : '';

		                    $product_full_name = ( !empty( $variation_att ) ) ? $order_item['order_prod'] . ' (' . $variation_att . ')' : $order_item['order_prod'];

		                    $data_model['items'][$key]['custom_line_items'] = (!empty($data_model['items'][$key]['custom_line_items'])) ? $data_model['items'][$key]['custom_line_items'] : '';
		                    $data_model['items'][$key]['custom_line_items'] .= $product_full_name.' '.$sku_detail.'['.__('Qty','smart-manager-for-wp-e-commerce').': '.$order_meta_key_values['_qty'].']['.__('Price','smart-manager-for-wp-e-commerce').': '.($order_meta_key_values['_line_total']/$order_meta_key_values['_qty']).']';

		                    if( !empty( $order_meta_key_values['_wc_cog_item_total_cost'] ) ) {
		                    	$data_model['items'][$key]['custom_line_items'] .= '['.__('Cost of Good','smart-manager-for-wp-e-commerce').': '.wc_format_decimal($order_meta_key_values['_wc_cog_item_total_cost']).']';
		                    }

		                    $data_model['items'][$key]['custom_line_items'] .= ', ';

		                    $data_model['items'][$key]['custom_order_sub_total'] = floatval ( (!empty($data_model['items'][$key]['custom_order_sub_total'])) ? $data_model['items'][$key]['custom_order_sub_total'] : 0 );
	                        $data_model['items'][$key]['custom_order_sub_total'] += floatval( ( !empty( $order_meta_key_values['_line_subtotal'] ) ) ? $order_meta_key_values['_line_subtotal'] : 0 );
            			}

            			if( !empty( $data_model['items'][$key]['custom_line_items'] ) ) {
            				$data_model['items'][$key]['custom_line_items'] = substr( $data_model['items'][$key]['custom_line_items'], 0, -2 ); //To remove extra comma ', ' from returned 
            			}

            			$data_model['items'][$key]['custom_details'] = !empty( $data_model['items'][$key]['custom_details'] ) ? ( ( $data_model['items'][$key]['custom_details'] == 1) ? $data_model['items'][$key]['custom_details'] . ' item' : $data_model['items'][$key]['custom_details'] . ' items' ) : ''; 

            		}

                    $data_model['items'][$key]['woocommerce_order_items_coupons_used'] = ( !empty( $order_coupons[$order_id] ) ) ? $order_coupons[$order_id] : "";
                    $data_model['items'][$key]['woocommerce_order_items_shipping_method'] = ( !empty( $order_shipping_method[$order_id] ) ) ? $order_shipping_method[$order_id] : "";
            	}
            }

			return $data_model;
		}

		//Function to query for the dashboard KPI data
		public static function kpi_data_query( $found_rows, $wp_query_obj ) {

			$query = ( !empty( $wp_query_obj->request ) ) ? $wp_query_obj->request : '';

			if( !empty( $query ) ) {

				global $wpdb;

				$status_counts = array();
				$from_strpos = strpos( $query, 'FROM' );
				$from_pos = ( !empty( $from_strpos ) ) ? $from_strpos : 0;

				if( $from_pos > 0 ) {
					$query = substr( $query, $from_pos );
					$groupby_strpos = strpos( $query, 'GROUP' );
					$group_pos = ( !empty( $groupby_strpos ) ) ? $groupby_strpos : 0;
					$query = substr( $query, 0, $group_pos );

					if( !empty( $query ) ) {
						$final_query = 'SELECT '.$wpdb->prefix.'posts.post_status, COUNT( DISTINCT( '.$wpdb->prefix.'posts.id ) ) AS count '. $query .' GROUP BY '.$wpdb->prefix.'posts.post_status';

						self::$kpi_query_results = $wpdb->get_results( 'SELECT '.$wpdb->prefix.'posts.post_status, COUNT( DISTINCT( '.$wpdb->prefix.'posts.id ) ) AS count '. $query .' GROUP BY '.$wpdb->prefix.'posts.post_status', 'ARRAY_A' );
					}
				}
			}

			return $found_rows;
		}

		//Function for getting the KPI data
		public static function generate_orders_kpi_data( $params, $statuses = array() ) {
			
			global $wpdb;

			$kpi_data = array();
			$dashboard = ( !empty( $params['active_module'] ) ) ? $params['active_module'] : 'shop_order';
			$status_counts = ( !empty( self::$kpi_query_results ) ) ? self::$kpi_query_results : array();

			if( count($status_counts) > 0 ) {

				$dashboard_model = get_transient('sa_sm_'.$dashboard);
				if( ! empty( $dashboard_model ) ) {
					$dashboard_model = json_decode( $dashboard_model, true );
				}

				if( !empty( $dashboard_model['columns'] ) ) {
					foreach( $dashboard_model['columns'] as $colObj ) {
						if( !empty( $colObj['data'] ) && !empty( $colObj['colorCodes'] ) && $colObj['data'] == 'posts_post_status' ) {

							foreach( $colObj['colorCodes'] as $key => $obj ) {
								foreach( $obj as $col ) {
									$color_codes[ $col ] = $key;
								}
							}
						}
					}
				}

				foreach( $status_counts as $value ) {
					$key = ( !empty( $statuses[$value['post_status']] ) ) ? $statuses[$value['post_status']] : ucwords($value['post_status']);
					$kpi_data[ $key ] = array( 'count' => $value['count'], 
												'color' => ( ( !empty( $color_codes[$value['post_status']] ) ) ? $color_codes[$value['post_status']] : '' ) );
				}
			}

			return $kpi_data;
		}

		//function to modify the data_model only for Export CSV & fetch data for custom columns
		public function orders_data_model( $data_model, $data_col_params ) {

			if( is_callable( array( 'Smart_Manager_Shop_Order', 'generate_orders_custom_column_data' ) ) ) {
				$data_model = self::generate_orders_custom_column_data( $data_model, $this->req_params );
			}

			if( !empty( $this->req_params['sm_page'] ) && $this->req_params['sm_page'] == 1 ) {
				if( is_callable( array( 'Smart_Manager_Shop_Order', 'generate_orders_kpi_data' ) ) ) {

					$order_statuses = array();

					if( function_exists('wc_get_order_statuses') ) {
						$order_statuses = wc_get_order_statuses();
					}

					$data_model['kpi_data'] = self::generate_orders_kpi_data( $this->req_params, $order_statuses );

				}
			}

			return $data_model;
			
		}

		//function for additional things pre inline update
		public function pre_inline_update( $edited_data = array() ) {

			if ( empty( $edited_data ) ) {
				return $edited_data;
			}
			foreach( $edited_data as $id => $edited_row ) {
				if( empty( $id ) ) {
					continue;
				}

				if( ! empty( $edited_row['posts/post_status'] ) && class_exists( 'WC_Order' ) ) {
					$order = new WC_Order( $id );
					$order->update_status( $edited_row['posts/post_status'], '', true );
					unset( $edited_data[$id]['posts/post_status'] );
				}
			}
			return $edited_data;
		}
	}
}
