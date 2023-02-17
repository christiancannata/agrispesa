<?php



##############################################################

if (1 == get_option('woofic_ordine_zero')) {

    if ($order_total == 0 ) { 

$testo_errore_wfic = "<p><b>Creazione non avvenuta come da <a href=\"admin.php?page=woo-fattureincloud-premium&tab=impostazioni\">impostazioni</a>
perché importo ordine uguale a zero</b></p>";


        $type = 'warning';
        $message = __($testo_errore_wfic, 'woo-fattureincloud-premium');
        add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
        settings_errors('woo-fattureincloud-premium');
        
    return;
        

    }

}

#############################################################

############################################################################
#
# Raccolta ID Aliquote IVA presenti
#
############################################################################


$company_ID =   get_option('wfic_id_azienda');

$wfic_token = get_option('wfic_api_key_fattureincloud');

###############################################################################

$ch_list_conti = curl_init();

curl_setopt($ch_list_conti, CURLOPT_URL, 'https://api-v2.fattureincloud.it/c/'.$company_ID.'/settings/vat_types');
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

$result_vat_type_list_fic = json_decode($result_pay_list, true);

$wfic_vat_array = array();


foreach ($result_vat_type_list_fic as $vals_list_vat) { 

    foreach ($vals_list_vat as $vals_list_vat_id) {

        $wfic_vat_total_value[] = $vals_list_vat_id;
        
        $wfic_vat_array[] = $vals_list_vat_id['id'];

        $wfic_vat_array_valore[] = $vals_list_vat_id['value'];
                
    
    }

}

//error_log(print_r($wfic_vat_total_value, true));

sort($wfic_vat_array, SORT_NUMERIC);

$wfic_vat_array_id = (implode(", ", $wfic_vat_array));

$wfic_vat_array_id_array = array_map('intval', explode(',', $wfic_vat_array_id));


#############################################################

require plugin_dir_path(__FILE__) . '/send_to_fattureincloud.php';

require plugin_dir_path(__FILE__) . '/error_cerca.php';

#############################################################


