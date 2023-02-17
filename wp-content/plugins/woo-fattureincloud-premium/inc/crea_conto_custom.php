<?php

if (!defined('ABSPATH')) exit;


$company_ID =   get_option('wfic_id_azienda');

$wfic_token = get_option('wfic_api_key_fattureincloud');

###############################################################################

$ch_list_conti = curl_init();

curl_setopt($ch_list_conti, CURLOPT_URL, 'https://api-v2.fattureincloud.it/c/'.$company_ID.'/settings/payment_accounts');
curl_setopt($ch_list_conti, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_list_conti, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array(
    "Authorization: Bearer ".$wfic_token."",
    "Content-Type: application/json",
 );

curl_setopt($ch_list_conti, CURLOPT_HTTPHEADER, $headers);

$result_pay_list = curl_exec($ch_list_conti);
if (curl_errno($ch_list_conti)) {
    echo 'Error:' . curl_error($ch_list_conti);
}
curl_close($ch_list_conti);

$result_payment_list_fic = json_decode($result_pay_list, true);


################################################################################################################

# aggiunta conto custom

######################################################################

$custom_conto_pay  = get_option('woo_fic_custom_pay_method');

error_log($custom_conto_pay );

if (!empty($custom_conto_pay ) && !in_array_conti_wfic($custom_conto_pay , $result_payment_list_fic)) {  
        
                
            
    #####################################################################################################################
    
    
    $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_accounts";
                                
    $data_pre_cliente_nuovo = array ("data" => array(
    
                                    "name" => $custom_conto_pay ,
                                    "type" => "standard"
            
                                    )
                                );
    
            //print_r($data_pre_cliente_nuovo['data'])." data pre [data]";
    
    $entity_result = $data_pre_cliente_nuovo['data'];
    
    $data_tosend_wfic_postfields = json_encode($data_pre_cliente_nuovo);
    
    include plugin_dir_path(__FILE__) . '/send_data.php';
    
    
    #####################################################################################################################
    
    $new_conto_cust = $custom_conto_pay ;

    $new_conto_saldo_cust[] = $new_conto_cust;
                
    
            
}

###################################################################





if (!empty($new_conto_saldo_cust)) 


 {

$type = 'success';
$message = __('Conto di saldo Custom aggiunto su Fattureincloud.it', 'woo-fattureincloud-premium');
add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
settings_errors('woo-fattureincloud-premium');


    header("Refresh:0");

} 
