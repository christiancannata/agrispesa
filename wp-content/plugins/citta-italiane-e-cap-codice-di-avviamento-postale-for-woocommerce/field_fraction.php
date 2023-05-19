<?php

	
	
	if( get_option( 'wcicapfw_enable_fraction_field' ) == "yes" ) {
		
		function extra_register_fields() {
        $text_domain  = 'italy-city-and-postcode-for-woocommerce';
        return array(
        'fraction' => array('type' => 'text',    'class' => ['form-row-first'], 'required' => 1, 'label' => __('Frazione/Località', $text_domain) ),
         );
         }
		
		
		add_filter( 'woocommerce_default_address_fields' , 'add_extra_address_fields' );
		
		function add_extra_address_fields( $address_fields ){
			
			
			$address_fields['fraction'] = array(
			'label'     => __('Frazione/Località:', 'italy-city-and-postcode-for-woocommerce'),
			'required'  => false,
			'placeholder' => 'Frazione/Località',
			'class'     => array('form-row-wide'),
			'priority' => 65,
			'type'  => 'text'
			);
		
			
			return $address_fields;
		}
		
		
		add_filter('woocommerce_formatted_address_replacements', 'extra_formatted_address_replacements', 99, 2);
		function extra_formatted_address_replacements( $address, $args ){
			if (isset($args['fraction']))
			$address['{city}'] = $args['city']."\n".$args['fraction']."\n"; 
			return $address;
		} 
		
		
		add_filter( 'woocommerce_order_formatted_billing_address', 'extrab_update_formatted_billing_address', 99, 2);
		function extrab_update_formatted_billing_address( $address, $obj ){
        $fraction = 'fraction';
				
				
					$address[$fraction] = get_post_meta($obj->get_id(), '_billing_'.$fraction, true);
			
			return $address;    
		}
		

		add_filter( 'woocommerce_order_formatted_shipping_address', 'extras_update_formatted_shipping_address', 99, 2);
		function extras_update_formatted_shipping_address( $address, $obj ){
			
			$fraction = 'fraction';

				
			 
			        $address[$fraction] = get_post_meta($obj->get_id(), '_shipping_'.$fraction, true);
			    
			
			
			return $address;    
		}
		
		add_filter('woocommerce_admin_billing_fields', 'add_fraction_admin_billing_fields');
        function add_fraction_admin_billing_fields($billing_fields) {
			
           $billing_fields['fraction'] = array( 'label' => __('Frazione/Località', 'italy-city-and-postcode-for-woocommerce') );

          return $billing_fields;
       
		}	
		
		add_filter('woocommerce_admin_shipping_fields', 'add_fraction_admin_shipping_fields');
        function add_fraction_admin_shipping_fields($shipping_fields) {
			
           $shipping_fields['fraction'] = array( 'label' => __('Frazione/Località', 'italy-city-and-postcode-for-woocommerce') );

          return $shipping_fields;
       
		}	
		
		
		
		add_filter('woocommerce_my_account_my_address_formatted_address', 'extra_my_account_address_formatted_address', 99, 3);
		function extra_my_account_address_formatted_address( $address, $customer_id, $name ){
			
			$fraction = 'fraction';

		
					$address[$fraction] = get_user_meta( $customer_id, $name.'_'.$fraction, true );
				
			
			return $address;
		}	
		
		add_filter('woocommerce_customer_meta_fields', 'admin_user_account_fraction_field');
        function admin_user_account_fraction_field( $fields ) {
	
            foreach ( extra_register_fields() as $fkey => $values ) {
              if ( in_array($fkey, ['fraction']) ) {
                  $fields['billing']['fields']['billing_'.$fkey] = array(
                  'label'       => $values['label'],
                  'description' => ''
                 );
			     $fields['shipping']['fields']['shipping_'.$fkey] = array(
                 'label'       => $values['label'],
                 'description' => ''
                );
             }
          }
			
      return $fields;
			
    }	
	
}//Chiudi




      