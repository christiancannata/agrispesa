<?php

$app_client_id = "X2oRM6dkUdcd353SNTfWEC8c5XYqaAbd";

$refresh_token_attivato = get_option('wfic_refresh_token');

if (!empty($refresh_token_attivato)) { 

error_log("il refresh token wfic è attivo e presente");

$ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api-v2.fattureincloud.it/oauth/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{  \"grant_type\": \"refresh_token\", \"client_id\": \"$app_client_id \",  \"refresh_token\": \"$refresh_token_attivato\"}");

    //error_log("il device code wfic è => ". $device_code_forwfic);

    $headers = array();
    $headers[] = 'Accept: application/json';
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    $dati_connessione_refresh= json_decode($result, true);

    //error_log(print_r($dati_connessione_refresh, true));

    if (empty($dati_connessione_refresh['error'] )) {

        $wfic_bearer_token = $dati_connessione_refresh['access_token'];

        $wfic_refresh_token = $dati_connessione_refresh['refresh_token'];

        

        update_option('wfic_api_key_fattureincloud', sanitize_text_field($wfic_bearer_token ));

        
        $type = 'updated';
     
        $message = __('Access token aggiornato', 'woo-fattureincloud-premium');
        add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
        settings_errors('woo-fattureincloud-premium');
      

        update_option('wfic_refresh_token', sanitize_text_field($wfic_refresh_token ));


        header("Refresh:0");

        return;

    }


} else {

        error_log("il refresh token wfic NON è presente");
    
}
