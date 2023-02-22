<?php 
class WCCCF_Customer
{
	public function __construct()
	{
		add_action('wp_ajax_wcccf_get_usernames_list', array(&$this, 'ajax_get_usernames_partial_list'));
	}
	public function get_user_roles()
	{
		global $wp_roles;
		return $wp_roles->roles;
	}
	public function customer_satisfy_conditional_rule($condition)
	{
		$user = wp_get_current_user();
		
		if($condition['condition_type'] == 'user' )
		{
			
			$user_roles = array();
			
			if($user->ID == 0)
				$user_roles[] = 'not_logged';
			else 
			{
				$user_roles = $user->roles;
			}
			
			//user_operator -> at_least_one || has_all || has_none
			if($condition['user_operator'] == 'at_least_one')
				return count(array_intersect($user_roles, $condition['user_role'])) > 0;
			else if($condition['user_operator'] == 'has_all')
				return count(array_intersect($user_roles, $condition['user_role'])) == count($user_roles);
			else if($condition['user_operator'] == 'has_none')
				return count(array_intersect($user_roles, $condition['user_role'])) == 0;
			
			return true;
		}
		else if($condition['condition_type'] == 'customer')
		{
			if($condition['customer_operator'] == 'at_least_one')
				return $user->ID != 0 && count(array_intersect(array($user->ID), $condition['customer_id'])) > 0;
			else if($condition['customer_operator'] == 'is_none')
				return $user->ID == 0 || count(array_intersect(array($user->ID), $condition['customer_id'])) == 0;
		}
		
		return false;
	}
	public function get_gustomer_name_by_id($user_id)
	{
		$customer = new WC_Customer( $user_id );
		if(!$customer)
			return false;
			
		$to_return =   "<b>User ID: </b>".$customer->get_id()."<br> ".  
											  "<b>Email: </b>".$customer->get_email()."<br> ".
											  "<b>User: </b>".$customer->get_first_name()." ".$customer->get_last_name()."<br> ".
											  "<b>Billing: </b> ".$customer->get_billing_first_name()." ".$customer->get_billing_last_name()." - ".$customer->get_billing_email()."<br><br> ";
		return $to_return;
	}
	public function ajax_get_usernames_partial_list()
	{
		$resultCount = 15;
		$search_string = isset($_GET['search_string']) ? $_GET['search_string'] : null;
		$page = isset($_GET['page']) ? $_GET['page'] : null;
		$offset = isset($page) ? ($page - 1) * $resultCount : null;
		$customers = $this->get_users_list($search_string ,$offset, $resultCount);
		echo json_encode( $customers); 
		wp_die();
	}
	public function get_users_list($search_string ,$offset, $resultCount)
	{
		global $wpdb; 
		$join_manager_roles_additional_string = "";
		
		//Returns only customers
		$manager_roles = array('customer');
		$join_manager_roles_additional_string = $where_manager_roles_additional_string = "";
		
		if(count($manager_roles) > 0 )
		{
			$counter = 0;
			foreach((array)$manager_roles as $manager_role)
			{
				$where_manager_roles_additional_string .= $counter++ == 0 ? " ( " : " OR ";
				$where_manager_roles_additional_string .= " user_capabilities.meta_value LIKE '%".serialize($manager_role).serialize(true)."%' ";
			}
			$where_manager_roles_additional_string .= " ) ";
			
		}
		//	
		
		$join_manager_roles_additional_string = " LEFT JOIN {$wpdb->usermeta} AS user_capabilities ON users.ID = user_capabilities.user_id AND user_capabilities.meta_key = '{$wpdb->prefix}capabilities'";
				
		$limit_query = isset($offset) && isset($resultCount) ? " LIMIT {$resultCount} OFFSET {$offset}": "";
		$additional_select = $additional_join = $additional_where = "";
		
		{
			
			
			$additional_join = " LEFT JOIN {$wpdb->usermeta} AS first_name_meta  ON first_name_meta.user_id = users.ID AND first_name_meta.meta_key = 'first_name'
								 LEFT JOIN {$wpdb->usermeta} AS last_name_meta  ON last_name_meta.user_id = users.ID AND last_name_meta.meta_key = 'last_name' 
								 LEFT JOIN {$wpdb->usermeta} AS billing_name_meta  ON billing_name_meta.user_id = users.ID  AND billing_name_meta.meta_key = 'billing_first_name' 
								 LEFT JOIN {$wpdb->usermeta} AS billing_last_name_meta  ON billing_last_name_meta.user_id = users.ID  AND billing_last_name_meta.meta_key = 'billing_last_name'
								 LEFT JOIN {$wpdb->usermeta} AS billing_email_meta  ON billing_email_meta.user_id = users.ID AND billing_email_meta.meta_key = 'billing_email'
								 ";
								 
			
		}
		 $query_string = "SELECT users.ID as ID, users.user_email as email, users.user_login as user_login, first_name_meta.meta_value as first_name, last_name_meta.meta_value as last_name, 
								 billing_name_meta.meta_value as billing_name, billing_last_name_meta.meta_value as billing_last_name, billing_email_meta.meta_value as billing_email, user_capabilities.meta_value as capabilies
							 FROM {$wpdb->users} AS users {$additional_join} {$join_manager_roles_additional_string} ";
							
		if($where_manager_roles_additional_string != "" || $additional_where != "")					
							 $query_string .=" WHERE {$where_manager_roles_additional_string} {$additional_where} ";
		if($search_string)
		{
			$offset = null;
			$limit_query = "";
			if($where_manager_roles_additional_string != "" || $additional_where != "")
				$query_string .= " AND ";
			else 
				$query_string .= " WHERE ";
			
			$query_string .=  " ( users.ID LIKE '%{$search_string}%' OR  
										  users.user_email LIKE '%{$search_string}%' OR 
										  users.user_login LIKE '%{$search_string}%' OR 
										  first_name_meta.meta_value LIKE '%{$search_string}%' OR
										  last_name_meta.meta_value LIKE '%{$search_string}%' OR
										  billing_name_meta.meta_value LIKE '%{$search_string}%' OR 
										  billing_last_name_meta.meta_value LIKE '%{$search_string}%' OR 
										  billing_email_meta.meta_value LIKE '%{$search_string}%'  
									  )";
		}
		//wcsts_var_dump($query_string);
		
		$order_by =  " GROUP BY users.ID ORDER BY  users.ID ASC ".$limit_query ;
		$wpdb->query('SET SQL_BIG_SELECTS=1');
		$wpdb->query('SET MAX_JOIN_SIZE=99999999999999999');
		$results = $wpdb->get_results($query_string.$order_by );
		$bad_char = array('"', "'");
		
		if(isset($offset) && isset($resultCount))
		{
			$num_order = $wpdb->get_results($query_string );
			$num_order = isset($num_order) ? count($num_order) : 0;
			$endCount = $offset + $resultCount;
			$morePages = $num_order > $endCount;
			$results = array(
				  "results" => $results,
				  "pagination" => array(
					  "more" => $morePages
				  )
			  );
		}
		else
			$results = array(
				  "results" => $results,
				  "pagination" => array(
					  "more" => false
				  )
			  );
		
		return $results;
	}
}
?>