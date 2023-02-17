<?php

$token_key = get_option('wfic_api_key_fattureincloud');

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $wfic_datatosend_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $data_tosend_wfic_postfields,
  CURLOPT_HTTPHEADER => array(
    "authorization: Bearer ".$token_key,
    "content-type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

$response_value = (json_decode($response, true));

if (!empty($response_value ['error'] ) || $err) {

  //error_log("beccato l'errore!");

if (isset($wfic_woo_auto_activation) && $wfic_woo_auto_activation === true ) {


  update_option('fattureincloud_autosent_id_fallito', $order_id);

  //error_log(" ERROR ORDER ID =>".$order_id);

  include_once plugin_dir_path(__FILE__) . '/setup-file.php';
  
  //error_log(print_r($response_value , true));
}
  
    
  ?>

  <div id="message" class="notice notice-error is-dismissible">
  <p>
    
  <?php

  $fic_risposta = (json_decode($response, true));
  
  echo "Documento non creato => ";
  echo $fic_risposta['error']['message'];

  error_log("Documento non creato => ".$fic_risposta['error']['message']);

  $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/issued_documents/totals";

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $wfic_datatosend_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $data_tosend_wfic_postfields,
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer ".$token_key,
      "content-type: application/json"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $response_value = (json_decode($response, true));



  //  echo "Error #:" . $err ."<br>"; 
/*  echo "<pre>";
  print_r(json_decode($response, true)); 
  echo "</pre>";
*/
  error_log(print_r($response_value , true));

  ?>
  </p>
  </div>

  <?php  

  } else {

  error_log("Invio riuscito ");

  ?>
  


  <div id="message" class="notice notice-success is-dismissible">
    <p><b>Invio Riuscito!</b></p>
    <?php
  /*
    echo "<pre>";
    print_r(json_decode($response, true));
    echo "</pre>"; 
  */
    ?>
  </div>




  <?php 
}
