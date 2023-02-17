<?php

if ( is_admin() && ! defined( 'DOING_AJAX' ) || ! is_checkout()  )
return;


if (1 == get_option('woofic_marca_bollo_elettronica')) {


    if (('fatturaelettronica' == get_option('fattureincloud_send_choice')) 
        || ('fatturaelettronica' == get_option('fattureincloud_auto_save'))
 
       

    ) {

        global $woocommerce;

        $wfic_cart_total = $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total + $woocommerce->cart->tax_total;

        
        if ($wfic_cart_total > 77.47) { 

        WC()->cart->add_fee('Marca da Bollo', 2);


	
        }
    
    }

}
