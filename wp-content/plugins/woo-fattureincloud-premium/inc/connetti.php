<?php

$app_client_id = "X2oRM6dkUdcd353SNTfWEC8c5XYqaAbd";

$device_code_forwfic = get_option('wfic_device_code');

$token_attivato = get_option('wfic_api_key_fattureincloud');

$refresh_token_attivato = get_option('wfic_refresh_token');

$wfic_company_id = get_option('wfic_id_azienda');


########################################################################
#
#       Check connessione fattureincloud.it
#
#######################################################################

$url = "https://api-v2.fattureincloud.it/user/companies";

include plugin_dir_path(__FILE__) . '/retrive_data.php';

$json = json_decode($result, true);
if (is_array($json)) {

    if (!empty($json['error'])) { 

        error_log("errore nella connessione con fattureincloud =>");

        error_log(print_r($json['error'] , true));

        echo "<p><span style=\"font-size:1.0em;float:left\"> &#9888; </span> connessione con fattureincloud.it non attiva => </p>";
        print_r($json['error']);


        include plugin_dir_path(__FILE__) . '/refresh.php';

    
    } else { 

        error_log("connessione ok con fattureincloud =>");

        //error_log(print_r($json, true));
    
        echo "<p><span style=\"font-size:1.0em;float:left\"> &#9989; </span> connessione con fattureincloud.it ok</p>";

    }

}
    

##################################################################################



?><p> <input type='button' id='hideshow' value='mostra diagnostica' class="button"></p>
    
    <div id='wfic_diagnostic' style='display:none'> 
    
    <script type="text/javascript">
        jQuery(document).ready(function(){
        jQuery('#hideshow').on('click', function(event) {        
            jQuery('#wfic_diagnostic').toggle('show');
        });
        });
    </script>
    
    <?php



if (!empty($device_code_forwfic)) {

?>
    <p><span style="font-size:1.0em;float:left">  &#9989; </span> Device code presente </p>
<?php
} else {

    ?>
    <p><span style="font-size:1.0em;float:left"> &#9888; </span> no device code </p>
<?php

}

if (!empty($token_attivato)) {

    ?>
        <p><span style="font-size:1.0em;float:left">  &#9989; </span> token presente </p>
    <?php
    } else {
    
        ?>
        <p><span style="font-size:1.0em;float:left"> &#9888; </span> no token </p>
    <?php
    
    }

if (!empty($refresh_token_attivato)) {

        ?>
            <p><span style="font-size:1.0em;float:left">  &#9989; </span> refresh token presente </p>
        <?php
        } else {
        
            ?>
            <p><span style="font-size:1.0em;float:left"> &#9888; </span> no refresh token </p>
        <?php
        
        }    

if (!empty($wfic_company_id)) {

        ?>
                <p><span style="font-size:1.0em;float:left">  &#9989; </span> company ID presente </p>
            <?php
            } else {

                header("Location: admin.php?page=woo-fattureincloud-premium&tab=impostazioni");
            
                ?>
                <p><span style="font-size:1.0em;float:left"> &#9888; </span> no company ID </p>
            <?php
            
    }    

  ?>  </div> <?php

##########################################################


###########################################################################
#
#   Attiva Connessione
#
##############################################################################

if (empty($device_code_forwfic) && (empty($token_attivato) && (empty($refresh_token_attivato)) )) { 

    function attiva_conn() {
        echo "<h3>codice per la connessione</h3>";
        include plugin_dir_path(__FILE__) . '/attiva_connessione.php';
    }
   
    if(array_key_exists('attiva_conn', $_POST)) {
       
        attiva_conn();
        
    }
    
           
    ?> 
    <p>Attiva una nuova Connessione</p>

    <form method="post">
        <input type="submit" name="attiva_conn"
            class="button" value="Attiva la Connessione" />
        
    </form>
    <hr>

    <?php 


return;

}

#################################################################

#################################################################################
#
#       Reset dati connnessione
#
################################################################################

//if ((!empty(get_option('wfic_device_code'))) && (!empty(get_option('wfic_api_key_fattureincloud'))) &&
// (!empty(get_option('wfic_refresh_token'))) && (!empty(get_option('wfic_id_azienda')))  )  { 

function wfic_reset_conn_data() {
    //echo "Reset data";
    include plugin_dir_path(__FILE__) . '/reset_dati_connessione.php';

    header("Refresh:0");

    
}

if(array_key_exists('wfic_reset_conn_data', $_POST)) {
    wfic_reset_conn_data();
}

?> 

<p><b>Solo se necessario </b>rimuovi completamente i dati della connessione attuale</p>

<form method="post">
    <input type="submit" name="wfic_reset_conn_data"
        class="button" value="Reset Connessione" />
    
</form>

<?php



$url = "https://api-v2.fattureincloud.it/user/companies";

include plugin_dir_path(__FILE__) . '/retrive_data.php';

$json = json_decode($result, true);


if (is_array($json)) {

    if (empty($json['error']) && (!array_key_exists('wfic_reset_conn_data', $_POST)) ) { 



        //error_log("connessione attiva");


    } elseif (!empty($json['error']) && (!empty($refresh_token_attivato)) && (empty($token_attivato))) {

        error_log("connessione NON attiva =>");

        error_log(print_r($json['error'], true));

        include plugin_dir_path(__FILE__) . '/refresh.php';

    } elseif (!empty($json['error']) && (empty($refresh_token_attivato))) { 


#################################################################

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api-v2.fattureincloud.it/oauth/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "{  \"grant_type\": \"urn:ietf:params:oauth:grant-type:device_code\",  \"client_id\": \"$app_client_id\",  \"device_code\": \"$device_code_forwfic\"}");

if (!empty($device_code_forwfic)) { 

error_log("il device code wfic è presente e attivo");

} else {

    error_log("il device code wfic NON è presente");

}

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);


$dati_connessione_decod= json_decode($result, true);




if (!empty($dati_connessione_decod['error'] )) { 
$testo_errore_wfic = ($dati_connessione_decod)['error_description'];

###########################################################################################

$type = 'warning';
$message = __($testo_errore_wfic, 'woo-fattureincloud-premium');
add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
settings_errors('woo-fattureincloud-premium');

###########################################################################################

error_log($testo_errore_wfic);

} elseif (empty($dati_connessione_decod['error'] )) {

    $wfic_bearer_token = $dati_connessione_decod['access_token'];

    $wfic_refresh_token = $dati_connessione_decod['refresh_token'];

    error_log("token e refresh token creati");
           
    echo "<h4><span style='background: lightgreen; padding: 0.25em 0'> Connessione con Fattureincloud.it riuscita!</span></h4>";

    update_option('wfic_api_key_fattureincloud', sanitize_text_field($wfic_bearer_token ));

##############################################################################################
    
    $type = 'updated';
    $message = __('Connessione Attiva!', 'woo-fattureincloud-premium');
    add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
    settings_errors('woo-fattureincloud-premium');
    error_log("Connessione attivata wfic_api_key_fattureincloud aggiornata");

##############################################################################################

    update_option('wfic_refresh_token', sanitize_text_field($wfic_refresh_token ));

    header("Refresh:1");

} else {

    function attiva_conn() {
        echo "<h3>Ecco il codice per la connessione</h3>";
        include plugin_dir_path(__FILE__) . '/attiva_connessione.php';
    }
   
    if(array_key_exists('attiva_conn', $_POST)) {
        attiva_conn();
    }
    
           
    ?> 

    <form method="post">
        <input type="submit" name="attiva_conn"
            class="button" value="Attiva la Connessione" />
        
    </form>

    <?php 
    
    }
}

}

#######################################################################################################
#
#       Fine Reset Connessione
#
#######################################################################################################



