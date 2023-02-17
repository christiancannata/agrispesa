<?php

$app_client_id = "X2oRM6dkUdcd353SNTfWEC8c5XYqaAbd";



$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api-v2.fattureincloud.it/oauth/device');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "\n{\n  \"client_id\": \"$app_client_id\",\n  \"scope\": \"situation:r entity.clients:a issued_documents.invoices:a issued_documents.receipts:a receipts:a archive:a emails:r settings:a\"\n}\n\n");

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$result_decoded = json_decode($result, true);


$device_code_forwfic = $result_decoded['data']['device_code'];
$wfic_user_code = $result_decoded['data']['user_code'];


//echo "Device Code = " . $device_code_forwfic  ."<br>";   
echo "<p><b>1)</b> Prendi lo User Code = <b><span style='background: white; padding: 0.30em 0.80em'>" .$result_decoded['data']['user_code'] ."</span></b></p>";  


update_option('wfic_device_code', $device_code_forwfic );

/*
$type = 'updated';
$message = __('User Code attivato: '.$wfic_user_code, 'woo-fattureincloud-premium');
add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
settings_errors('woo-fattureincloud-premium');
*/


echo "<p><b> 2)</b> Vai a questo indirizzo <a href='https://secure.fattureincloud.it/connetti' onClick=\"MyWindow=window.open('https://secure.fattureincloud.it/connetti','wfic_connection','width=600,height=700'); return false;\">https://secure.fattureincloud.it/connetti</a> </p>

<p> <b>3)</b> inserisci lo User Code <b><span style='background: white; padding: 0.30em 0.80em'>" .$result_decoded['data']['user_code'] ."</span></b></p>

<p> <b>4)</b>   clicca su <b><span style='background: lightblue; padding: 0.30em 0.80em'>Continua</span></b></p>

<p> <b> 5) </b> e poi Clicca su <b><span style='background: lightblue; padding: 0.30em 0.80em'>Autorizza</span></b></p>

<p><b>6)</b> torna qui e Clicca sul tab <b><span style='background: lightgrey; padding: 0.30em 0.80em'><a href=\"?page=woo-fattureincloud-premium&tab=connetti\">Connetti </a></span></b> </p>";

echo "<hr>";
