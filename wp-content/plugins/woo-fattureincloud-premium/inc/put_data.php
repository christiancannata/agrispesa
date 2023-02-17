<?php

$token_key = get_option('wfic_api_key_fattureincloud');

$curl_wfic = curl_init();

curl_setopt($curl_wfic, CURLOPT_URL, $put_url);
curl_setopt($curl_wfic, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_wfic, CURLOPT_CUSTOMREQUEST, 'PUT');

curl_setopt($curl_wfic, CURLOPT_POSTFIELDS, $data_to_put_wfic_postfields);

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer '.$token_key.'';
$headers[] = 'Content-Type: application/json';
curl_setopt($curl_wfic, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($curl_wfic);
if (curl_errno($curl_wfic)) {
    echo 'Error:' . curl_error($curl_wfic);
}
curl_close($curl_wfic);

$response_value = (json_decode($response, true));

if (!empty($response_value ['error'] ) ) {

error_log("errore aggiornamento cliente =>". $response_value ['error']['message']);

if (is_admin()) { 

  /*
  $type = 'warning';
  $message = __('Aggiornamento anagrafica non effettuato', 'woo-fattureincloud');
  add_settings_error('woo-fattureincloud', esc_attr('settings_updated'), $message, $type);
  settings_errors('woo-fattureincloud');
  */
//echo "=> <b>" . $response_value['error']['message'] . "</b>";

//echo "=> <b>" . $response_value['error']['message'] . "</b>";
}


} else {

  if (is_admin()) { 

  /*
  $type = 'success';
  $message = __('Aggiornamento anagrafica cliente effettuato', 'woo-fattureincloud');
  add_settings_error('woo-fattureincloud', esc_attr('settings_updated'), $message, $type);
  settings_errors('woo-fattureincloud');
  */

  }

  error_log("aggiornamento cliente riuscito");


}
