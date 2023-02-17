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


//print_r($result_payment_methods_fic);
//echo "Metodi<br>";


/*

foreach ($result_payment_methods_fic as $vals_met_pay) {


    foreach ($vals_met_pay as $vals_met_pay_id) {

        
        echo "<pre>";
        print_r($vals_met_pay_id['name']);
        echo "</pre>";

     
  
  }
}   

*/

$new_metodo_pagamento = array();

if (!in_array_metodi_wfic("Paypal", $result_payment_methods_fic)) { 


        
            
        #####################################################################################################################
        
        
        $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_methods";
                            
        $data_pre_cliente_nuovo = array ("data" => array(
        
                                    "name" => "Paypal",
                                    "type" => "standard"
        
                                    )
                                );
        
        //$entity_result = $data_pre_cliente_nuovo['data'];
        
        $data_tosend_wfic_postfields = json_encode($data_pre_cliente_nuovo);

        error_log(print_r($data_tosend_wfic_postfields, true));
        
        include plugin_dir_path(__FILE__) . '/send_data.php';
        
        $new_metodo_pagamento_py = "Paypal";
        
        
        $new_metodo_pagamento[] = $new_metodo_pagamento_py;
        
        
} 
        
if (!in_array_metodi_wfic("Stripe", $result_payment_methods_fic)) {         
        
        
        
            
            #####################################################################################################################
            
            
            $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_methods";
                                        
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
            
            $new_metodo_pagamento_s = "Stripe";
        
            $new_metodo_pagamento[] = $new_metodo_pagamento_s;
                        
            
                    
}

        
if (!in_array_metodi_wfic("Bonifico Bancario", $result_payment_methods_fic)) {          
        
        
            
            #####################################################################################################################
            
            
            $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_methods";
                                        
            $data_pre_cliente_nuovo = array ("data" => array(
            
                                            "name" => "Bonifico Bancario",
                                            "type" => "riba"
                    
                                            )
                                        );
            
                    //print_r($data_pre_cliente_nuovo['data'])." data pre [data]";
            
            $entity_result = $data_pre_cliente_nuovo['data'];
            
            $data_tosend_wfic_postfields = json_encode($data_pre_cliente_nuovo);
            
            include plugin_dir_path(__FILE__) . '/send_data.php';
            
            
            #####################################################################################################################
            
            $new_metodo_pagamento_b = "Bonifico Bancario";
        
            $new_metodo_pagamento[] = $new_metodo_pagamento_b;
            
                    
}

if (!in_array_metodi_wfic("Pagamento alla Consegna", $result_payment_methods_fic)) {  
        
                
            
            #####################################################################################################################
            
            
            $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_methods";
                                        
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
            
            $new_metodo_pagamento_c = "Pagamento alla Consegna";
        
            $new_metodo_pagamento[] = $new_metodo_pagamento_c;
                        
            
                    
}
        
if (!in_array_metodi_wfic("Assegno", $result_payment_methods_fic)) {  
                
            
            #####################################################################################################################
            
            
            $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_methods";
                                        
            $data_pre_cliente_nuovo = array ("data" => array(
            
                                            "name" => "Assegno",
                                            "type" => "riba"
                    
                                            )
                                        );
            
                    //print_r($data_pre_cliente_nuovo['data'])." data pre [data]";
            
            $entity_result = $data_pre_cliente_nuovo['data'];
            
            $data_tosend_wfic_postfields = json_encode($data_pre_cliente_nuovo);
            
            include plugin_dir_path(__FILE__) . '/send_data.php';
            
            
            #####################################################################################################################
            
            $new_metodo_pagamento_a = "Assegno";
                        
            $new_metodo_pagamento[] = $new_metodo_pagamento_a;
            
                    
}

if (!in_array_metodi_wfic("Gratis", $result_payment_methods_fic)) {  
        
                
            
            #####################################################################################################################
            
            
            $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/settings/payment_methods";
                                        
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
            
            $new_metodo_pagamento_g = "Gratis";
        
            $new_metodo_pagamento[] = $new_metodo_pagamento_g;
                        
            
                    
}





        



        
        
#################################################################################################################
        
        
        
        
        
        if (!empty($new_metodo_pagamento_s) || (!empty($new_metodo_pagamento_py)) || (!empty($new_metodo_pagamento_b))
        || (!empty($new_metodo_pagamento_c)) || (!empty($new_metodo_pagamento_a)) || (!empty($new_metodo_pagamento_g)) ||
        (!empty($new_metodo_pagamento_cust))
        
        ) {
        
        $type = 'success';
        $message = __('Metodo di Pagamento di WooCommerce aggiunto su Fattureincloud.it', 'woo-fattureincloud');
        add_settings_error('woo-fattureincloud', esc_attr('settings_updated'), $message, $type);
        settings_errors('woo-fattureincloud');

        echo "Sono stati aggiunti i seguenti metodi di Pagamento su fattureincloud.it: ";
        
            foreach ($new_metodo_pagamento as $key => $value){
        
                echo "<b>".$value."</b>\n";
        
            }
        
            header("Refresh:0");


        }
        
     
