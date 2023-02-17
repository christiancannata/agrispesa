<?php

$url = "https://api-v2.fattureincloud.it/user/companies";

include plugin_dir_path(__FILE__) . '/retrive_data.php';

$json = json_decode($result, true);


if (is_array($json)) {

    if (!empty($json['error'])) { 

        error_log("connessione non attiva, attivo la procedura di riconnessione");

        include plugin_dir_path(__FILE__) . '/connetti.php';
    
    }
}