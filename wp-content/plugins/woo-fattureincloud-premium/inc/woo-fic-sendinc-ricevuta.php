<?php


update_option('woo_fattureincloud_order_id', $order_id);

require plugin_dir_path(__FILE__) . 'setup-file.php';
require plugin_dir_path(__FILE__) . 'prepare_to_send.php';
//require plugin_dir_path(__FILE__) . 'send_to_fattureincloud.php';


if (!empty($response_value ['error'] ) || $err) {


    update_option('fattureincloud_autosent_id_fallito', $order_id);

    if (1 == get_option('woo_fic_send_error_email')) {

    $to = get_option( 'admin_email' );
    $domain_name_email = substr(strrchr($to, "@"), 1);
    $subject = "[WooCommerce Fattureincloud Premium] Ordine #". $order_id ." creazione automatica della ricevuta fallita";
    $body = "<p>La creazione automatica della ricevuta su fattureincloud.it dall'ordine WooCommerce #" . $order_id . " è fallita. </p>
    <p>Il consiglio è di verificare le proprie impostazioni e successivamente la documentazione all'indirizzo
    <a href='https://woofatture.com/documentazione/'>https://woofatture.com/documentazione</a></p>
    <p>Questa email è stata spedita perché la funzionalità 19 del plugin WooCommerce Fattureincloud Premium è attiva, per non riceverne il consiglio è di disattivare la funzionalità 19</p>
    <p></p> ";
    $headers = array( 'Content-Type: text/html; charset=UTF-8' );
    $headers[] = 'From: alert <creazione_fattura@'. $domain_name_email .'>';
    wp_mail( $to, $subject, $body, $headers);
    
    }

} else {

    update_option('fattureincloud_autosent_id_riuscito', $order_id);

}



if ('yes' == get_option('fattureincloud_invia_email_auto')) {

    

    if ($response_value ['data']['id']) {

        $idricevuta = $response_value ['data']['id'];

        error_log("numero ID ricevuta appena creata => ".$idricevuta);

        include plugin_dir_path(__FILE__) . 'send_auto_email_ricevute.php';
    
    } else {

        error_log("Ordine #".$order_id." invio automatico email fallito");
    }

}


/*

update_option('woo_fattureincloud_order_id', $order_id);

$doc_type_wfic = "receipt";

$invoice_elet_type_wfic = false;

//$fattureincloud_url = "https://api.fattureincloud.it:443/v1/ricevute/nuovo";

require plugin_dir_path(__FILE__) . 'setup-file.php';
require plugin_dir_path(__FILE__) . 'prepare_to_send.php';

//if (!in_array("success", $fattureincloud_result)) {

if (!empty($response_value ['error'] ) || $err) {  
  
    update_option('fattureincloud_autosent_id_fallito', $order_id);

    error_log("creazione automatica ricevuta dell'ordine " .$order_id . " non riuscita");

}


if ('yes' == get_option('fattureincloud_invia_email_auto')) {

    if ($fattureincloud_result['new_id']) {

        $idricevuta = $fattureincloud_result['new_id'];
        include plugin_dir_path(__FILE__) . 'send_auto_email_ricevute.php';
    } else {

        error_log("$order_id set to ricevuta fallito", 0);
    }
}
*/

/*
*
* cerca il termine cod_iva nella risposta di errore
*
*/
/*
$term = 'cod_iva';
$ser = function ( $val ) use ( $term ) {
    return ( stripos($val, $term) !== false ? true : false );
};
$valore_iva = array_keys(array_filter($fattureincloud_result, $ser));

######################

if (!empty($valore_iva)) {

    update_option('fattureincloud_autosent_id_fallito_codiva', $order_id);

}
*/