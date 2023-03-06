<?php
/**
 * The application scope class to retreive data.
 *
 * @link       https://themelocation.com
 * @since      3.1.0
 *
 * @package    add-fields-to-checkout-page-woocommerce-premium
 * @subpackage add-fields-to-checkout-page-woocommerce-premium/includes
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWCFE_Data')):

class THWCFE_Data {
	protected static $_instance = null;
	private $products = array();
	private $categories = array();
	
	public function __construct() {
		
	}
	
	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function load_products_ajax(){
		$productsList = array();
		$value = isset($_POST['value']) ? stripslashes($_POST['value']) : '';
		$count = 0;

		$limit = apply_filters('thwcfe_load_products_per_page', 100);

		if(!empty($value)){
			$value_arr = $value ? explode(',', $value) : false;

			$args = array(
			    'include' => $value_arr,
				'orderby' => 'name', 
				'order' => 'ASC', 
				'return' => 'ids'
			);
			$products = wc_get_products($args);

			if(!empty($products)){
				foreach($products as $pid){
					$productsList[] = array("id" => $pid, "text" => get_the_title($pid), "selected" => true);
				}
			}

			$count = count($products);

		}else{
			$term = isset($_POST['term']) ? stripslashes($_POST['term']) : '';
			$page = isset($_POST['page']) ? stripslashes($_POST['page']) : 1;

		    $status = apply_filters('thwcfe_load_products_status', 'publish');

		    $args = array(
				's' => $term,
			    'limit' => $limit,
			    'page'  => $page,
			    'status' => $status, 
				'orderby' => 'name', 
				'order' => 'ASC', 
				'return' => 'ids'
			);
			$products = wc_get_products( $args );

			
			if(!empty($products)){
				foreach($products as $pid){
					$productsList[] = array("id" => $pid, "text" => get_the_title($pid));
					//$productsList[] = array("id" => $product->ID, "title" => $product->post_title);
				}
			}

			$count = count($products);
		}

		$morePages = $count < $limit ? false : true;

		$results = array(
			"results" => $productsList,
			"pagination" => array( "more" => $morePages )
		);

		wp_send_json_success($results);
  		die();
	}
}

endif;