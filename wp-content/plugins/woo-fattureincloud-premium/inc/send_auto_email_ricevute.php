<?php


$company_id = get_option('wfic_id_azienda');

$url ="https://api-v2.fattureincloud.it/c/".$company_id."/issued_documents/".$idricevuta."/email";

//echo $url;

include plugin_dir_path(__FILE__) . '/retrive_data.php';

$json_email = json_decode($result, true);


//error_log(print_r($json_email , true));


if (!empty($json_email['data']['recipient_email']) || !empty($json_email['data']['cc_email'])) {


    $email_destinatario = $json_email['data']['recipient_email'];

    $email_mittente = $json_email['data']['default_sender_email']['email'];


    if (empty($json_email['data']['recipient_email'])) {

        $email_destinatario = $json_email['data']['cc_email'];

    }

    $oggetto_email = $json_email['data']['subject'];

    $email_body = $json_email['data']['body'];

    //$oggetto_ordine_fic = $oggetto_email;

    //error_log("destinatario ".$email_destinatario ."mittente  ".$email_mittente." oggetto email  ".$oggetto_email." messaggio  ".$email_body );

    include plugin_dir_path(__FILE__) . '/send_email_ricevuteincloud.php';


}



/*
$url = "https://api.fattureincloud.it:443/v1/ricevute/infomail";

$ch = curl_init();

$request_dett = array(
    "api_uid" => $api_uid,
    "api_key" => $api_key,
    "id" => $idricevuta

);

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_dett));

$headers = array();
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$pre_result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$result = json_decode($pre_result, true);


if (!empty($result['mail_destinatario']) || !empty($result['mail_cc'])) {


    $email_destinatario = $result['mail_destinatario'];

    if (empty($result['mail_destinatario'])) {

        $email_destinatario = $result['mail_cc'];

    }

    $oggetto_email = $result['oggetto_default'];

    $messaggio_default = $result['messaggio_default'];

    $oggetto_ordine_fic = $oggetto_email;

    include plugin_dir_path(__FILE__) . '/send_email_ricevuteincloud.php';

}

*/