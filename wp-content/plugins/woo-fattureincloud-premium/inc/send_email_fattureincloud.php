<?php

// Don't access this directly, please

if (!defined('ABSPATH')) {
    exit;
} 

$wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/". $company_id ."/issued_documents/". $idfattura ."/email";


$data_tosend_wfic_postfields = "{\"data\":{\"sender_email\":\"$email_mittente\",
  \"recipient_email\":\"$email_destinatario\",
  \"subject\":\"$oggetto_email\",
  \"body\":\"$email_body\",
  \"include\":{\"document\":true,
               \"delivery_note\":false,
               \"attachment\":false,
               \"accompanying_invoice\":false
              },
  \"attach_pdf\":true,
  \"send_copy\":false}
}";

error_log("invio email con oggetto => " .$oggetto_email);

##########################################################################################

include plugin_dir_path(__FILE__) . '/send_data.php';

#######################################################################################

if (!empty($response_value ['error'] ) || $err) {


  if (is_admin()) { 
    $type = 'error';
    $message = __("Invio Email non Riuscito: ".$response_value['error']['message'], 'woo-fattureincloud-premium');
    add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
    settings_errors('woo-fattureincloud-premium');
  }


    error_log($response_value['error']['message']);

    
  /*
    echo "Error #:" . $err ."<br>"; 
    echo "<pre>";
    print_r(json_decode($response, true)); 
    echo "</pre>";
  */
  

  
  } else {

    
    error_log("invio email ".$oggetto_email ." Riuscito!");

    update_option('fattureincloud_autosent_id_riuscito_email', $oggetto_email );
  
    if (is_admin()) { 
 
      $type = 'success';
      $message = __("Invio Email Riuscito!", 'woo-fattureincloud-premium');
      add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
      settings_errors('woo-fattureincloud-premium');
    }

    /*
      echo "<pre>";
      print_r(json_decode($response, true));
      echo "</pre>"; 
    */  
   
  
  }






#####################################################################################################################################################################

// Don't access this directly, please

/*if (!defined('ABSPATH')) exit;


$fattureincloud_url = "https://api.fattureincloud.it:443/v1/fatture/inviamail";

$api_uid = get_option('api_uid_fattureincloud');
$api_key = get_option('api_key_fattureincloud');

//print_r($lista_articoli);


$fattureincloud_request = array(

    "id" => $idfattura,
    "api_uid" => $api_uid,
    "api_key" => $api_key,
    "mail_mittente"=> "no-reply@fattureincloud.it",
    "mail_destinatario" => $email_destinatario,
    "oggetto" => $oggetto_email,
  */
    /*    "messaggio" => "Gentile " .$nome_cliente_fic. " ,<br>\n in allegato la fattura dell'".$oggetto_ordine_fic." in versione PDF.
    <br><br>\n\nE' inoltre possibile  scaricarne una copia  premendo sul bottone sottostante<br><br>\n\n{{allegati}}<br><br>\n\nCordiali saluti",
	*/


    /*
    "messaggio" => $result['messaggio_default'],
    "includi_documento" => true,
    "invia_fa" => true,
    "includi_allegato" => true,
    "invia_copia" => true,
    "allega_pdf" => true

);



$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $fattureincloud_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fattureincloud_request));

$headers = array();
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$pre_result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$fattureincloud_result = json_decode($pre_result, true);
*/

/*
$fattureincloud_options = array(
    "http" => array(
        "header" => "Content-type: text/json\r\n",
        "method" => "POST",
        "content" => json_encode($fattureincloud_request)
    ),
);
$fattureincloud_context = stream_context_create($fattureincloud_options);
$fattureincloud_result = json_decode(file_get_contents($fattureincloud_url, false, $fattureincloud_context), true);
*/

//print_r($fattureincloud_result);

/*
if (in_array("success", $fattureincloud_result)) {

?>
<div id="message" class="notice notice-success is-dismissible">
    <p><b>Invio Riuscito!</b></p>
</div>
<?php

} else {

?>
<div id="message" class="notice notice-error is-dismissible">
    <p><b>Invio non Riuscito: 
<?php

    echo $fattureincloud_result['error'];

                ?></b>
</div>
<?php
}

*/