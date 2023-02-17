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

//echo " Conti<br>";

/*

foreach ($result_payment_list_fic as $vals_list_pay) { 

    foreach ($vals_list_pay as $vals_list_pay_id) {

     
       echo "<pre>";
        print_r($vals_list_pay_id['name']);

        echo "</pre>";

    }

}

*/



$new_conto_saldo = array();

if (!in_array_conti_wfic("Paypal", $result_payment_list_fic) ) { 

    
#####################################################################################################################


$wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_accounts";
                    
$data_pre_conto_nuovo = array ("data" => array(

                            "name" => "Paypal",
                            "type" => "standard"

                            )
                        );


$data_tosend_wfic_postfields = json_encode($data_pre_conto_nuovo);

error_log(print_r($data_tosend_wfic_postfields, true));


include plugin_dir_path(__FILE__) . '/send_data.php';

$new_conto_saldo_py = "Paypal";


$new_conto_saldo[] = $new_conto_saldo_py;


} 

if (!in_array_conti_wfic("Stripe", $result_payment_list_fic) ) { 

    
    #####################################################################################################################
    
    
    $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_accounts";
                                
    $data_pre_cliente_nuovo = array ("data" => array(
    
                                    "name" => "Stripe",
                                    "type" => "standard"
            
                                    )
                                );
    
            //print_r($data_pre_cliente_nuovo['data'])." data pre [data]";
    
    $entity_result = $data_pre_cliente_nuovo['data'];
    
    $data_tosend_wfic_postfields = json_encode($data_pre_cliente_nuovo);
    
    include plugin_dir_path(__FILE__) . '/send_data.php';
    
    
    #####################################################################################################################
    
    $new_conto_saldo_s = "Stripe";

    $new_conto_saldo[] = $new_conto_saldo_s;
                
    
            
}

if (!in_array_conti_wfic("Bonifico Bancario", $result_payment_list_fic) ) { 


    
    #####################################################################################################################
    
    
    $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_accounts";
                                
    $data_pre_cliente_nuovo = array ("data" => array(
    
                                    "name" => "Bonifico Bancario",
                                    "type" => "bank"
            
                                    )
                                );
    
            //print_r($data_pre_cliente_nuovo['data'])." data pre [data]";
    
    $entity_result = $data_pre_cliente_nuovo['data'];
    
    $data_tosend_wfic_postfields = json_encode($data_pre_cliente_nuovo);
    
    include plugin_dir_path(__FILE__) . '/send_data.php';
    
    
    #####################################################################################################################
    
    $new_conto_saldo_b = "Bonifico Bancario";

    $new_conto_saldo[] = $new_conto_saldo_b;
    
            
}

if (!in_array_conti_wfic("Pagamento alla Consegna", $result_payment_list_fic) ) { 


    
    #####################################################################################################################
    
    
    $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_accounts";
                                
    $data_pre_cliente_nuovo = array ("data" => array(
    
                                    "name" => "Pagamento alla Consegna",
                                    "type" => "standard"
            
                                    )
                                );
    
            //print_r($data_pre_cliente_nuovo['data'])." data pre [data]";
    
    $entity_result = $data_pre_cliente_nuovo['data'];
    
    $data_tosend_wfic_postfields = json_encode($data_pre_cliente_nuovo);
    
    include plugin_dir_path(__FILE__) . '/send_data.php';
    
    
    #####################################################################################################################
    
    $new_conto_saldo_c = "Pagamento alla Consegna";

    $new_conto_saldo[] = $new_conto_saldo_c;
                
    
            
}

if (!in_array_conti_wfic("Assegno", $result_payment_list_fic) ) { 


    
    #####################################################################################################################
    
    
    $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_accounts";
                                
    $data_pre_cliente_nuovo = array ("data" => array(
    
                                    "name" => "Assegno",
                                    "type" => "bank"
            
                                    )
                                );
    
            //print_r($data_pre_cliente_nuovo['data'])." data pre [data]";
    
    $entity_result = $data_pre_cliente_nuovo['data'];
    
    $data_tosend_wfic_postfields = json_encode($data_pre_cliente_nuovo);
    
    include plugin_dir_path(__FILE__) . '/send_data.php';
    
    
    #####################################################################################################################
    
    $new_conto_saldo_a = "Assegno";
                
    $new_conto_saldo[] = $new_conto_saldo_a;
    
            
}

if (!in_array_conti_wfic("Gratis", $result_payment_list_fic) ) { 

    
    #####################################################################################################################
    
    
    $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_accounts";
                                
    $data_pre_cliente_nuovo = array ("data" => array(
    
                                    "name" => "Gratis",
                                    "type" => "standard"
            
                                    )
                                );
    
            //print_r($data_pre_cliente_nuovo['data'])." data pre [data]";
    
    $entity_result = $data_pre_cliente_nuovo['data'];
    
    $data_tosend_wfic_postfields = json_encode($data_pre_cliente_nuovo);
    
    include plugin_dir_path(__FILE__) . '/send_data.php';
    
    
    #####################################################################################################################
    
    $new_conto_saldo_g = "Gratis";

    $new_conto_saldo[] = $new_conto_saldo_g;
                
    
            
}






###################################################################





if (!empty($new_conto_saldo_s) || (!empty($new_conto_saldo_py)) || (!empty($new_conto_saldo_b))
|| (!empty($new_conto_saldo_c)) || (!empty($new_conto_saldo_a)) || (!empty($new_conto_saldo_g)) 
|| (!empty($new_conto_saldo_cust)) 


) {

$type = 'success';
$message = __('Conto di saldo WooCommerce aggiunto su Fattureincloud.it', 'woo-fattureincloud');
add_settings_error('woo-fattureincloud', esc_attr('settings_updated'), $message, $type);
settings_errors('woo-fattureincloud');

echo "<hr>Sono stati aggiunti i seguenti conti di saldo su fattureincloud.it: ";

    foreach ($new_conto_saldo as $key => $value){

        echo "<b>".$value."</b>\n";
    }

    header("Refresh:0");

} 
