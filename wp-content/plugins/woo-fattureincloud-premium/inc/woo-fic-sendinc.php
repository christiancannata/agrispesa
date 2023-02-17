<?php

update_option('woo_fattureincloud_order_id', $order_id);

error_log("ID Ordine =>". $order_id);

require plugin_dir_path(__FILE__) . 'setup-file.php';
require plugin_dir_path(__FILE__) . 'prepare_to_send.php';
//require plugin_dir_path(__FILE__) . 'send_to_fattureincloud.php';



if (!empty(get_option('fattureincloud_autosent_id_fallito'))) { 
    
    //if (!empty($response_value ['error'] ) || $err) {

    error_log("errore in woo-fic-sendinc => ". $order_id);

    update_option('fattureincloud_autosent_id_fallito', $order_id);

    if (1 == get_option('woo_fic_send_error_email')) {

    $to = get_option( 'admin_email' );
    $domain_name_email = substr(strrchr($to, "@"), 1);
    $subject = "[WooCommerce Fattureincloud Premium] Ordine #". $order_id ." creazione automatica della fattura fallita";
    $body = "<p>La creazione automatica della fattura su fattureincloud.it dall'ordine WooCommerce #" . $order_id . " è fallita. </p>
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

    

    if (empty($response_value ['data']['id'])) {

        error_log("Ordine #".$order_id." invio automatico email fallito");
           
    } else {

        $idfattura = $response_value ['data']['id'];

        error_log("numero ID fattura appena creata => ".$idfattura);

        include plugin_dir_path(__FILE__) . 'send_auto_email_fatture.php';

        
    }

}

