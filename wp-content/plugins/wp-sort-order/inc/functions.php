<?php if ( ! defined( 'ABSPATH' ) ) exit; 
		
	function sanitize_wpso_data( $input ) {
		if(is_array($input)){		
			$new_input = array();	
			foreach ( $input as $key => $val ) {
				$new_input[ $key ] = (is_array($val)?sanitize_wpso_data($val):sanitize_text_field( $val ));
			}			
		}else{
			$new_input = sanitize_text_field($input);			
			if(stripos($new_input, '@') && is_email($new_input)){
				$new_input = sanitize_email($new_input);
			}
			if(stripos($new_input, 'http') || wp_http_validate_url($new_input)){
				$new_input = esc_url($new_input);
			}			
		}	
		return $new_input;
	}	
	function wp_wpso_enqueue_scripts() {
		
		global $wpdb;
		
		wp_enqueue_script(
			'wpso-front-scripts',
			plugins_url('js/front-scripts.js', dirname(__FILE__)),
			array('jquery'),
			date('Ymhi')
		);
		
		$expected_terms = $wpdb->get_results("SELECT t.term_id FROM ".$wpdb->prefix."term_taxonomy t WHERE t.taxonomy='usercategories'");		
		
		$wp_wpso_array = array(
								'ajaxurl' => admin_url( 'admin-ajax.php' ),	
								'nonce'=> wp_create_nonce('wpso_nonce_ensured'),
						);
						
		$expected_items = array();				
						
		if(!empty($expected_terms)){
			foreach($expected_terms as $eterms){
				//pree($eterms);
				$tquery = "SELECT t.taxonomy, t.term_id FROM $wpdb->term_taxonomy t, $wpdb->terms tr WHERE ".("tr.term_id='".$eterms->term_id."'")." AND tr.term_id=t.term_id LIMIT 1";	
				//pree($tquery);
				$tax_term = $wpdb->get_row($tquery);
				$taxonomy = $tax_term->taxonomy;
				$term_id = $tax_term->term_id;
				$term_items = get_term_children($term_id, $taxonomy);
				
				if(empty($term_items)){
					$term_items_direct = get_objects_in_term( $term_id, $taxonomy); //07-10-2018
					if(!empty($term_items_direct)){
						$term_items = array($term_id);
					}
				}
				
				if(!empty($term_items)){
					
					if($items>0)
					$expected_items[$items] = array();
					
					foreach($term_items as $items){
						//pree($items);
						$term = get_term_by('id', $items, $taxonomy);
						
						
						$squery = "
										SELECT 							
											um.user_id
											
										FROM 
											$wpdb->usermeta um
										WHERE										
											um.meta_key=CONCAT('user_order_', $items)
										ORDER BY
											CAST(um.meta_value AS unsigned)
										ASC
											
									";
									
						$teamMembers = $wpdb->get_results($squery);
						
						
						if(!empty($teamMembers)){							
							foreach ( $teamMembers as $teamMember ){
									
								//$teamMember_info = get_userdata($teamMember->user_id);	
								//pree(get_author_posts_url($teamMember->user_id));
								$expected_items[$items][] = array('user_id'=>$teamMember->user_id, 'user_link'=>get_author_posts_url($teamMember->user_id));
							} 
							
						}
					}
				}

			}
		}		
		
		$wp_wpso_array['user_items'] = $expected_items;
		
		wp_localize_script( 'wpso-front-scripts', 'wpso', $wp_wpso_array );
	}
	
	add_action( 'wp_enqueue_scripts', 'wp_wpso_enqueue_scripts', 99 );	
	
	
	function wp_wpso_admin_enqueue_scripts() {
		
		global $wpdb;
		
		if(isset($_GET['page']) && $_GET['page']=='wpso-settings'){
			
			wp_enqueue_script( 'wpso-fontawesome', plugin_dir_url( dirname(__FILE__) ) . 'js/fontawesome.min.js' );
			wp_enqueue_style( 'wpso-fontawesome', plugins_url('css/fontawesome.min.css', dirname(__FILE__)), array());	
			wp_enqueue_script( 'wpso-bootstrap', plugin_dir_url( dirname(__FILE__) ) . 'js/bootstrap.min.js' );
			wp_enqueue_style( 'wpso-bootstrap', plugins_url('css/bootstrap.min.css', dirname(__FILE__)), array());				
		}
		
	}
			
	add_action( 'admin_enqueue_scripts', 'wp_wpso_admin_enqueue_scripts', 99 );		
	
	if(!function_exists('pre')){
		function pre($data){
			if(isset($_GET['debug'])){
				pree($data);
			}
		}	 
	} 
		
	if(!function_exists('pree')){
	function pree($data){
				echo '<pre>';
				print_r($data);
				echo '</pre>';	
		
		}	 
	} 	

	if ( ! function_exists("wpso_plugin_links"))
	{
		function wpso_plugin_links($links) { 
			global $wpso_premium_link, $wpso_pro;
			
			$settings_link = '<a href="options-general.php?page=wpso-settings">'.__('Settings', 'wpso-sort-order').'</a>';
			
			if($wpso_pro){
				array_unshift($links, $settings_link); 
			}else{
				 
				$wpso_premium_link = '<a href="'.esc_url($wpso_premium_link).'" title="'.__('Go Premium', 'wpso-sort-order').'" target=_blank>'.__('Go Premium', 'wpso-sort-order').'</a>'; 
				array_unshift($links, $settings_link, $wpso_premium_link); 
			
			}
			
			
			return $links; 
		}
	}
	
/**
* Uninstall hook
*/
	
	register_uninstall_hook( __FILE__, 'wpso_uninstall' );
	
	function wpso_uninstall()
	{
		global $wpdb;
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$curr_blog = $wpdb->blogid;
			$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				wpso_uninstall_db();
			}
			switch_to_blog( $curr_blog );
		} else {
			wpso_uninstall_db();
		}
	}
	function wpso_uninstall_db()
	{
		global $wpdb;
		$result = $wpdb->query( "DESCRIBE $wpdb->terms `term_order`" );
		if ( $result ){
			$query = "ALTER TABLE $wpdb->terms DROP `term_order`";
			$result = $wpdb->query( $query );
		}
		
		$result = $wpdb->query( "DESCRIBE $wpdb->users `user_order`" );
		if ( $result ){
			$query = "ALTER TABLE $wpdb->users DROP `user_order`";
			$result = $wpdb->query( $query );
		}
	
		delete_option( 'wpso_activation' );	
	}
