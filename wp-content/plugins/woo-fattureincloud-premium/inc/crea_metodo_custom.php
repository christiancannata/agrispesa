<?php

if (!defined('ABSPATH')) exit;


$company_ID =   get_option('wfic_id_azienda');

$wfic_token = get_option('wfic_api_key_fattureincloud');

###############################################################################


###############################################################################
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api-v2.fattureincloud.it/c/'.$company_ID.'/settings/payment_methods');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array(
    "Authorization: Bearer ".$wfic_token."",
    "Content-Type: application/json",
 );

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result_pay_met = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$result_payment_methods_fic = json_decode($result_pay_met, true);


#########################################################################

################################################################################################################

# aggiunta metodo custom

######################################################################

$custom_metodo_pay = get_option('woo_fic_custom_pay_method');

error_log($custom_metodo_pay);

if (!empty($custom_metodo_pay) && !in_array_metodi_wfic($custom_metodo_pay, $result_payment_methods_fic)) {  
        
                
            
    #####################################################################################################################
    
    
    $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_methods";
                                
    $data_pre_cliente_nuovo = array ("data" => array(
    
                                    "name" => $custom_metodo_pay,
                                    "type" => "standard"
            
                                    )
                                );
    
            //print_r($data_pre_cliente_nuovo['data'])." data pre [data]";
    
    $entity_result = $data_pre_cliente_nuovo['data'];
    
    $data_tosend_wfic_postfields = json_encode($data_pre_cliente_nuovo);
    
    include plugin_dir_path(__FILE__) . '/send_data.php';
    
    
    #####################################################################################################################
    
    $new_metodo_pagamento_cust = $custom_metodo_pay;

    $new_metodo_pagamento[] = $new_metodo_pagamento_cust;
                
    
            
}

if (!empty($new_metodo_pagamento_cust))

{

$type = 'success';
$message = __('Metodo di Pagamento di WooCommerce aggiunto su Fattureincloud.it', 'woo-fattureincloud');
add_settings_error('woo-fattureincloud', esc_attr('settings_updated'), $message, $type);
settings_errors('woo-fattureincloud');


    header("Refresh:0");


}