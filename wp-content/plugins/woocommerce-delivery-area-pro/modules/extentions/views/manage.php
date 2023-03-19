<?php
/**
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.0.9
 * @package WooCommerce Delivery Area Pro
 */

	$form = new WDAP_FORM();
	$form->set_header( esc_html__( 'Premium Add-Ons / Extentions For WooCommerce Delivery Area Pro', 'woo-delivery-area-pro' ), array() );
	
	$extentions = array();

	$products_by_delivery_area = array( 'url' => 'https://codecanyon.net/item/woocommerce-products-by-delivery-area/24635017',
	 'thumb' => 'products-by-da.jpg');
	 
	$minimum_order_by_da = array('url' => 'http://shop.woodeliveryarea.com/product/minimum-order-by-delivery-area/',
	'demo_url' => 'http://shop.woodeliveryarea.com/minimum-order-delivery-area/',
	 'thumb' => 'minimum-order-by-da.jpg');
	 
	$display_matched_collections = array('url' => 'http://shop.woodeliveryarea.com/product/woocommerce-display-matched-collections/',
	'demo_url' => 'https://www.woodeliveryarea.com/matched-collection-by-delivery-area/',
	 'thumb' => 'display-matched-collections.jpg');
	 
	$delivery_area_notification = array('url' => 'http://shop.woodeliveryarea.com/product/woocommerce-delivery-area-notification/',
	'demo_url' => 'https://www.woodeliveryarea.com/woocommerce-delivery-area-notification-3/',
	 'thumb' => 'delivery-area-notification.jpg');
	  
	$delivery_area_dokan = array('url' => 'http://shop.woodeliveryarea.com/product/woocommerce-delivery-area-for-dokan/',
	 'thumb' => 'delivery-area-dokan.jpg');
	 
	$da_search_bar = array('url' => 'http://shop.woodeliveryarea.com/product/delivery-area-search-bar/',
	'demo_url' => 'https://www.woodeliveryarea.com/delivery-area-search-bar-demo/',
	 'thumb' => 'da-search-bar.jpg');
	 
	$confirm_delivery_on_saved = array('url' => 'http://shop.woodeliveryarea.com/product/confirm-delivery-on-saved-addresses/',
	'demo_url' => 'https://www.woodeliveryarea.com/multiple-addresses-for-woocommerce-customers/',
	 'thumb' => 'confirm-delivery-on-saved.jpg');
	 
	$capture_potential_leads = array('url' => 'http://shop.woodeliveryarea.com/product/woocommerce-capture-potential-leads/',
	'demo_url' => 'https://www.woodeliveryarea.com/woocommerce-product-can-be-delivered/',
	 'thumb' => 'capture-potential-leads.jpg');
	
	$request = array('url' => 'https://www.woodeliveryarea.com/contact/', 'thumb' => 'request-customisation.png');

    $extentions[] =  $confirm_delivery_on_saved;
    $extentions[] =  $da_search_bar;
	$extentions[] =  $products_by_delivery_area;
	$extentions[] =  $minimum_order_by_da;
	$extentions[] =  $display_matched_collections;
	$extentions[] =  $delivery_area_notification;
	$extentions[] =  $delivery_area_dokan;
	
	
	$extentions[] =  $capture_potential_leads;
	
	$extentions[] =  $request;

	$html = '<div class="fc-row">';

	$count = count($extentions);
	foreach($extentions as $key => $addon){

		if($key != 0 && $key % 4 == 0){ $html .= '</div><div class="fc-row">';	}

		if($key == $count -1) {

			$links = '<a target="_blank" href="'.$addon['url'].'">'.esc_html__( 'Contact Now', 'woo-delivery-area-pro' ).'</a>';
			
		}else{

			$links = '<a target="_blank" href="'.$addon['url'].'">'.esc_html__( 'Download', 'woo-delivery-area-pro' ).'</a>';
			
			if( isset($addon['demo_url']) && !empty($addon['demo_url'])){
				
				$links .= '<a target="_blank" href="'.$addon['demo_url'].'">'.esc_html__( 'View Demo', 'woo-delivery-area-pro' ).'</a>';
			}
		
				
		}

		$html .= '<div class="fc-3">
			<div class="addon_block">
			<div class="addon_block_overlay">
				'.$links.'
			</div>
			<img src="http://img.flippercode.com/wdap-addons/'.$addon['thumb'].'"/>
			</div>
   		</div>';
	}
	
	$html .= '</div>';

	$form->add_element(
		'html', 'wdap_extentions_listing', array(
			'id'     => 'wdap_extentions_listing',
			'class'  => 'wdap_extentions_listing',
			'html' => $html,
			'before' => '<div class="fc-12">',
			'after' => '</div>'
		)
	);

    $form->render();
