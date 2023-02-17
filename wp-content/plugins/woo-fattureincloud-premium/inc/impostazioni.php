<?php // Don't access this directly, please
if (!defined('ABSPATH')) exit;


/*
 *
 * Controllo ID azienda e se manca aggiunta di default della prima azienda presente
 *
 *
 */

if (get_option('wfic_id_azienda') == null ) {



$url = "https://api-v2.fattureincloud.it/user/companies";

####################################

include plugin_dir_path(__FILE__) . '/retrive_data.php';

$json = json_decode($result, true);


if (!empty($json['error'])) { 

    error_log("connessione non attiva, attivo la procedura di riconnessione");

    include plugin_dir_path(__FILE__) . '/attiva_connessione.php';

    return;

}    



$id_azienda_default = ($json['data'])['companies']['0']['id'];


update_option('wfic_id_azienda', sanitize_text_field($id_azienda_default));


}



$url = "https://api-v2.fattureincloud.it/user/companies";

####################################

include plugin_dir_path(__FILE__) . '/retrive_data.php';

$json = json_decode($result, true);

//print_r($json);

//print_r ($json['data'])['companies'];



if (is_array($json)) {

    if (!empty($json['error'])) { 

        include plugin_dir_path(__FILE__) . '/connetti.php';

        echo "<h3>LOADING...</h3>";
        
        ?>
   <!--         <script>
                       
            location.reload();
        
            </script>
-->
  <?php

               
    } else { 


        echo "<form method=\"POST\">";
        
        echo wp_nonce_field(); 

#########################################################################################

        if (empty(get_option('wfic_id_azienda'))) {
        
            echo "<p><span style=\"font-size:1.4em;\"> &#9888; </span>Selezionare l'Azienda e cliccare sul tasto 'Salva Azienda'" ; 
     
            } else {
    
                echo "<p><span style=\"font-size:1.4em;\">  &#9989; </span> Azienda selezionata ID ". get_option('wfic_id_azienda'); 
    
            }


###########################################################################################

        echo '<select name="wfic_id_azienda">';
        

        ############################################################################

        foreach ($json as $value) {
    
            if (is_array($value)) {

                foreach ($value as $value2) {

                    if (is_array($value2)) {

                    $count = 0;

                        foreach ($value2 as $value3) {

                        //$count = $count + 1;

                       // print "Nome Azienda <b>".($value3)['name']."</b> ID Azienda <b>".$value3['id']."</b><hr>" ;
                        
                        echo '<option value="' . ($value3['id']) . '">' 
                        . ($value3)['name']. '</option>';
                       



                        if (get_option('wfic_id_azienda') == $value3['id']) {
                            echo '<option value="' . get_option('wfic_id_azienda') . '" selected>'. ($value3)['name'].' Azienda Selezionata</option>';

                        }


                        }

                    }

                }
    
            }

        }

        echo '</select>';
        ?>    <input type="submit" value="<?php echo __( 'Save Company', 'woo-fattureincloud-premium' );?>" class="button button-primary button-large"
        onclick="window.location='admin.php?page=woo-fattureincloud-premium&tab=impostazioni#setting-error-settings_updated';">
    </p>
        <?php


    } 



echo "</form>";
    
    

    

}



############################################################################
#
# Verifica presenza e aggiunta Conto di Saldo
#
############################################################################

$custom_conto_pay = get_option('woo_fic_custom_pay_method');

$company_ID =   get_option('wfic_id_azienda');

$wfic_token = get_option('wfic_api_key_fattureincloud');

###############################################################################

$ch_list_conti = curl_init();

curl_setopt($ch_list_conti, CURLOPT_URL, 'https://api-v2.fattureincloud.it/c/'.$company_ID.'/settings/payment_accounts');
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

$result_payment_list_fic = json_decode($result_pay_list, true);

//error_log(print_r($result_payment_list_fic , true));


#######################################################################

function in_array_conti_wfic($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_conti_wfic($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

#######################################################################

/*

echo "<pre>";
print_r($result_payment_list_fic);
echo "</pre>";

foreach ($result_payment_list_fic as $vals_list_pay) { 

    foreach ($vals_list_pay as $vals_list_pay_id) {

        echo "<pre>";
        print_r($vals_list_pay_id['name']);
        echo "</pre>";
        

    }

}
*/




//error_log(in_array_conti_wfic($custom_conto_pay,$result_payment_list_fic) ? 'found custom nei conti' : 'not found custom nei conti');



if (!in_array_conti_wfic("Paypal", $result_payment_list_fic) || !in_array_conti_wfic("Stripe", $result_payment_list_fic) ||

!in_array_conti_wfic("Bonifico Bancario", $result_payment_list_fic) || !in_array_conti_wfic("Pagamento alla Consegna", $result_payment_list_fic) ||

!in_array_conti_wfic("Assegno", $result_payment_list_fic) || !in_array_conti_wfic("Gratis", $result_payment_list_fic)


) { 

function crea_conti_wfic() {

    include_once plugin_dir_path(__FILE__) . '/crea_conti_saldo.php';
}

if(array_key_exists('crea_conti_wfic', $_POST)) {
    crea_conti_wfic();
}

       
?> 
<hr><span style="font-size:1.4em;float:left"> &#9888; </span>

<form method="post">
    <input type="submit" name="crea_conti_wfic"
        class="button" value="Crea Conti di Saldo di WooCommerce su fattureincloud.it" />
    </form>

    <p><span style="font-size:0.8em">se permane il segnale <span style="font-size:1.2em"> &#9888; </span> 
verificare se esistono già in <a href="https://secure.fattureincloud.it/settings-paymentaccounts" target="_blank">fattureincloud.it dei Conti di Saldo</a> con maiuscole e minuscole differenti (tipo paypal)</span><br>
<span style="font-size:0.8em">se così fosse, modificarli direttamente su fattureincloud.it (per esempio da paypal a Paypal)</span><br>
<span style="font-size:0.8em">l'elenco dei nomi dei Conti di Saldo corretti si trova <a href="https://woofatture.com/docs/conti-di-saldo/" target="_blank">in questa pagnina</a> della documentazione</span></p>

<?php

} else {

    echo "<hr><span style=\"font-size:1.4em;\">  &#9989; </span>  <span style=\"font-weight:bold;\"> I Conti di saldo di DEFAULT di WooCommerce sono presenti su fattureincloud.it</span></p>";

}

############################################################################
#
# Verifica presenza e aggiunta Metodi di Pagamento
#
############################################################################


$ch_list_metod = curl_init();

curl_setopt($ch_list_metod, CURLOPT_URL, 'https://api-v2.fattureincloud.it/c/'.$company_ID.'/settings/payment_methods');
curl_setopt($ch_list_metod, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_list_metod, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array(
    "Authorization: Bearer ".$wfic_token."",
    "Content-Type: application/json",
 );

curl_setopt($ch_list_metod, CURLOPT_HTTPHEADER, $headers);

$result_pay_met = curl_exec($ch_list_metod);
if (curl_errno($ch_list_metod)) {
    echo 'Error:' . curl_error($ch_list_metod);
}
curl_close($ch_list_metod);

$result_payment_methods_fic = json_decode($result_pay_met, true);


#######################################################################

function in_array_metodi_wfic($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_metodi_wfic($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

#######################################################################


/*
echo "<pre>";
print_r($result_payment_methods_fic);
echo "</pre>";


foreach ($result_payment_methods_fic as $vals_met_pay) {


    foreach ($vals_met_pay as $vals_met_pay_id) {

        
        echo "<pre>";
        print_r($vals_met_pay_id['name']);
        echo "</pre>";


        }

}
*/



if (!in_array_metodi_wfic("Paypal", $result_payment_methods_fic) || !in_array_metodi_wfic("Stripe", $result_payment_methods_fic) ||

!in_array_metodi_wfic("Bonifico Bancario", $result_payment_methods_fic) || !in_array_metodi_wfic("Pagamento alla Consegna", $result_payment_methods_fic) ||

!in_array_metodi_wfic("Assegno", $result_payment_methods_fic) || !in_array_metodi_wfic("Gratis", $result_payment_methods_fic) 


) { 


function crea_metodi_wfic() {
    
    include_once plugin_dir_path(__FILE__) . '/crea_metodo_pagamento.php';
}

if(array_key_exists('crea_metodi_wfic', $_POST)) {
    crea_metodi_wfic();
}

       
?> 
<hr>
<span style="font-size:1.4em;float:left"> &#9888; </span>

<form method="post">
    <input type="submit" name="crea_metodi_wfic"
        class="button" value="Crea Metodi di Pagamento di WooCommerce su fattureincloud.it" />
    
</form>


<?php 

/*
$type = 'warning';
$message = __("Creare i metodi di pagamento WooCommerce su fattureincloud.it", 'woo-fattureincloud');
add_settings_error('woo-fattureincloud', esc_attr('settings_updated'), $message, $type);
settings_errors('woo-fattureincloud');

*/


} else {

    echo "<p><span style=\"font-size:1.4em;\">  &#9989; </span> <span style=\"font-weight:bold;\"> I Metodi di Pagamento di WooCommerce sono presenti su fattureincloud.it</span></p>";

}

############################################################################
#
# FINE     Verifica Conti di Saldo e  Metodi di Pagamento
#
############################################################################

#############################################################################à
#
# Conto Custom
#
##############################################################################

$custom_conto_pay = get_option('woo_fic_custom_pay_method');


//error_log(in_array_metodi_wfic($custom_conto_pay,$result_payment_methods_fic) ? 'found custom nei metodi' : 'not found custom nei metodi');

//error_log("metodo custom conto trovato =>".$custom_conto_pay."<=");


if (!empty($custom_conto_pay)) { 

if (!in_array_metodi_wfic($custom_conto_pay, $result_payment_list_fic)) { 


function crea_contcust_wfic() {
    
    include_once plugin_dir_path(__FILE__) . '/crea_conto_custom.php';
}

if(array_key_exists('crea_contcust_wfic', $_POST)) {
    crea_contcust_wfic();
}

       
?> 
<hr>
<!--<span style="font-size:1.4em;float:left"> &#9888; </span> -->

<form method="post">
    <input type="submit" name="crea_contcust_wfic"
        class="button" value="Aggiungi <?php echo $custom_conto_pay; ?> come Conto di saldo Custom su fattureincloud.it" />
    
</form>


<?php 

/*
$type = 'warning';
$message = __("Creare i metodi di pagamento WooCommerce su fattureincloud.it", 'woo-fattureincloud');
add_settings_error('woo-fattureincloud', esc_attr('settings_updated'), $message, $type);
settings_errors('woo-fattureincloud');

*/


} else {

    echo "<p><span style=\"font-size:1.4em;\">  &#9989; </span> <span style=\"font-weight:bold;\"> Conto di saldo Custom ".get_option('woo_fic_custom_pay_method')." presente su Fattureincloud.it</span></p>";

}
}

###########################################################################
#
# Metodo Custom
#
###########################################################################

$custom_metodo_pay = get_option('woo_fic_custom_pay_method');


//error_log(in_array_metodi_wfic($custom_metodo_pay,$result_payment_methods_fic) ? 'found custom nei metodi' : 'not found custom nei metodi');

//error_log("metodo custom pagamenti trovato =>".$custom_metodo_pay."<=");

if (!empty($custom_metodo_pay)) { 


if (!in_array_metodi_wfic($custom_metodo_pay, $result_payment_methods_fic)) { 


function crea_metcust_wfic() {
    
    include_once plugin_dir_path(__FILE__) . '/crea_metodo_custom.php';
}

if(array_key_exists('crea_metcust_wfic', $_POST)) {
    crea_metcust_wfic();
}

       
?> 
<hr>
<!--<span style="font-size:1.4em;float:left"> &#9888; </span> -->

<form method="post">
    <input type="submit" name="crea_metcust_wfic"
        class="button" value="Aggiungi <?php echo $custom_conto_pay; ?> come Metodo di pagamento Custom su fattureincloud.it" />
    
</form>


<?php 

/*
$type = 'warning';
$message = __("Creare i metodi di pagamento WooCommerce su fattureincloud.it", 'woo-fattureincloud');
add_settings_error('woo-fattureincloud', esc_attr('settings_updated'), $message, $type);
settings_errors('woo-fattureincloud');

*/


} else {

    echo "<p><span style=\"font-size:1.4em;\">  &#9989; </span> 
    <span style=\"font-weight:bold;\"> Metodo di pagamento Custom ".get_option('woo_fic_custom_pay_method')." presente su Fattureincloud.it</span></p>";
//    <p>Codice del metodo di pagamento custom => <b>".get_option('woo_fic_payment_method_custom_code')."</b></p>";

}
}




############################################################################
#
# Verifica presenza Aliquote IVA
#
############################################################################
echo "<hr>";

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
/*
echo "<pre>";
echo($result_vat_type_list_fic['error']['message']);
echo "</pre>";
*/


if (!empty($result_vat_type_list_fic['error']['message'])) {

echo "<p><span style=\"font-size:1.4em;float:left\"> &#9888; </span><b>Company " .$result_vat_type_list_fic['error']['message']."</b></p>";

} elseif (empty($result_vat_type_list_fic['error']['message'])) { 

$wfic_vat_array = array();

    echo "<p><b>Elenco ID Aliquote Iva</b> presenti su fattureincloud.it per creare aliquote custom</p>";

    echo "<textarea rows=\"10\" cols=\"70\" wrap=\"hard\" readonly >";

    foreach ($result_vat_type_list_fic as $vals_list_vat) { 

        foreach ($vals_list_vat as $vals_list_vat_id => $value) {


        echo "[ ID = ".$value['id'] ."] || % = ". $value['value'] ." || Descrizione = ". $value['description'] ;

        echo "&#10;";

        $wfic_vat_array[] = $value['id'];

        }

    }



echo "</textarea>";

$wfic_vat_array_id = (implode(",", $wfic_vat_array));

}

//print_r($wfic_vat_array_id);

#######################################################################




##########################################################
#
#   Inizio impostazioni
#
###############################################################

?>
 <form method="POST">
    <?php wp_nonce_field('salva-impostazioni-wfcp', 'impostazioni-wfcp'); ?>


<table border="0" style="max-width:800px;" cellpadding="12" cellspacing="6">

<tr>
<td style="background-color:#288CCC;color:white;text-align:center;" colspan="2">

<?php
        echo __('La finalizzazione della <b>Fattura Elettronica</b> si esegue su Fattureincloud.it', 'woo-fattureincloud-premium'); 

?> <a style="color:white" href="http://bit.ly/2EEAOK4"> 📺 VIDEO</a>

        </td>
</tr>
</table>


<!-- ############################################################################################ -->



<table border="0" style="max-width:800px;" cellpadding="10" cellspacing="10" >

<tr>

<td id="cella_numero_imp">1</td>

    <td bgcolor="white" width="33.3333%"> 
    <span class="dashicons dashicons-format-aside"></span> 
    <label for="fattureincloud_send_choice">
                <?php echo __('Abilita la creazione <b>manuale</b> di<br>', 'woo-fattureincloud-premium');
                ?>
            </label>



    <input type="radio" id="fatturaelettronica_send_choice" name="fattureincloud_send_choice" value="fatturaelettronica"

        <?php
        if ('fatturaelettronica' == get_option('fattureincloud_send_choice')) {
            echo 'checked';
        } else {
            echo ''; 
        }
        ?> >

        <label for="contactChoice0">
            <?php echo __('FATTURA ELETTRONICA', 'woo-fattureincloud-premium'); ?>
        </label>                

<br>

    <input type="radio" id="fattura_send_choice" name="fattureincloud_send_choice" value="fattura"

        <?php

        if ('fattura' == get_option('fattureincloud_send_choice')) {
            echo 'checked';
        } else {
            echo ''; 
        }
        ?> >
        
        <label for="contactChoice1">
            <?php echo __('FATTURA', 'woo-fattureincloud-premium'); ?>
        </label>

<br>

    <input type="radio" id="ricevuta_send_choice" name="fattureincloud_send_choice" value="ricevuta"

        <?php

        if ('ricevuta' == get_option('fattureincloud_send_choice')) {
            echo 'checked';
        } else { 
            echo ''; 
        }
        ?>>

        <label for="contactChoice2">
            <?php echo __('RICEVUTA', 'woo-fattureincloud-premium'); ?>
        </label>

        <p></p>
<!-- <div id="tab1" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->
<div id="tab1" class="tab"><a href="https://woofatture.com/docs/funzionalita-1-creazione-manuale/" target="_blank">INFO</a></div>
    </td>

    <!-- ############################################################################################ -->
    <td id="cella_numero_imp">2</td>


    <td id="wfic_autocreate_stato" bgcolor="white" width="33.3333%">
    <span class="dashicons dashicons-update"></span>
    <label for="fattureincloud_status_order">
        <?php echo __('La creazione <b>in automatico</b> della fattura/ricevuta avviene <br> quando l\'ordine diventa nello stato <br>', 'woo-fattureincloud-premium');
        ?>
    </label>

    <!-- ############ -->

    <input type="radio" id="fattureincloud_status_order_completed" name="fattureincloud_status_order" value="completed" onclick="funzione_02()"

        <?php

        if ('completed' == get_option('fattureincloud_status_order')) {
            echo 'checked';
        } else {
               echo ''; 
        }
        ?> >

    <label for="contactChoice0">
        <?php echo __('COMPLETATO', 'woo-fattureincloud-premium'); ?>
    </label>
    
    <!-- ############ -->
    
 <br>   
    
    
    <input type="radio" id="fattureincloud_status_order_onhold" name="fattureincloud_status_order" value="onhold" onclick="funzione_02()"

        <?php

        if ('onhold' == get_option('fattureincloud_status_order')) {
            echo 'checked';
        } else {
            echo ''; 
        }
        ?> >
    <label for="fattureincloud_status_order_onhold">
        <?php echo __('IN SOSPESO', 'woo-fattureincloud-premium'); ?>
    </label>


 <!-- ############ -->
<br>

    <input type="radio" id="fattureincloud_status_order_processing" name="fattureincloud_status_order" value="processing" onclick="funzione_02()"

        <?php

        if ('processing' == get_option('fattureincloud_status_order')) {
            echo 'checked';
        
        } else {
            echo ''; 
        }
        
        ?>>

    <label for="fattureincloud_status_order_processing">
        <?php echo __('IN LAVORAZIONE', 'woo-fattureincloud-premium'); ?>
    </label>


 <!-- ############ -->
<br>

    <input type="radio" id="fattureincloud_status_order_nulla" name="fattureincloud_status_order" value= "nulla"

        <?php

        if ('nulla' == get_option('fattureincloud_status_order')) {
            echo 'checked';
        } else {
            echo ''; 
        }

        ?>>

    <label for="fattureincloud_status_order">
        <?php echo __('NULLA', 'woo-fattureincloud-premium'); ?>
    </label>

    <p></p>
<!-- <div id="tab12" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->

<div id="tab12" class="tab"><a href="https://woofatture.com/docs/funzionalita-2-3-generare-le-fatture-elettroniche-ricevute-in-modo-automatico/" target="_blank">INFO</a></div>


    </td>

    <!-- ############################################################################################ -->

    <td id="cella_numero_imp">3</td>

    <td id="wfic_autocreate_nocliente" bgcolor="white" width="33.3333%">
    <span class="dashicons dashicons-update"></span>

    <label for="fattureincloud_auto_save">

<?php
$ordine_fic_woo_status = get_option('fattureincloud_status_order');
$fic_woo_status = '';
switch ($ordine_fic_woo_status) {
case "onhold":
    $fic_woo_status = "IN SOSPESO";
    break;
case "completed":
    $fic_woo_status = "COMPLETATO";
    break;
case "processing":
    $fic_woo_status = "IN LAVORAZIONE";
    break;    
case "nulla":
    $fic_woo_status = "__________";
    break;
}


printf(
    __(
        'Quando l\'ordine è nello stato<br> %s <br>
        abilita la creazione in <b>Automatico</b>
        su Fattureincloud di <br>', 'woo-fattureincloud-premium'
    ),
    $fic_woo_status
);

?>
</label>



<input type="radio" id="fatturaelettronica_auto_save" name="fattureincloud_auto_save" value="fatturaelettronica"  onclick="funzione_03()"

<?php

if ('fatturaelettronica' == get_option('fattureincloud_auto_save')) {
    echo 'checked';
} else {
       echo ''; 
}
?> >

<label for="contactChoice0">
<?php echo __('FATTURA ELETTRONICA', 'woo-fattureincloud-premium'); ?>
</label>


<br>



<input type="radio" id="fattura_auto_save" name="fattureincloud_auto_save" value="fattura" onclick="funzione_03()"

<?php

if ('fattura' == get_option('fattureincloud_auto_save')) {
    echo 'checked';
} else {
    echo ''; 
}
?> >
<label for="contactChoice1">
<?php echo __('FATTURA', 'woo-fattureincloud-premium'); ?>
</label>

<br>



<input type="radio" id="ricevuta_auto_save" name="fattureincloud_auto_save" value="ricevuta" onclick="funzione_03()"

<?php

if ('ricevuta' == get_option('fattureincloud_auto_save')) {
    echo 'checked';

} else {
    echo ''; 
}

?>>

<label for="contactChoice2">
<?php echo __('RICEVUTA', 'woo-fattureincloud-premium'); ?>
</label>



<br>


<input type="radio" id="fattureincloud_auto_save_nulla" name="fattureincloud_auto_save" value= "nulla"

<?php

if ('nulla' == get_option('fattureincloud_auto_save')) {
    echo 'checked';
} else {
    echo ''; 
}

?>>

<label for="contactChoice3">
<?php echo __('NULLA/NON Predefinito', 'woo-fattureincloud-premium'); ?>
</label>

<p></p>
<!-- <div id="tab9" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->

<div id="tab9" class="tab"><a href="https://woofatture.com/docs/funzionalita-2-3-generare-le-fatture-elettroniche-ricevute-in-modo-automatico/" target="_blank">INFO</a></div>


    </td>
</tr>

<!-- ############################################################################################ -->

<!-- ############################################################################################ -->

<tr>

<td id="cella_numero_imp">4</td>

<td id="wfic_cliente_scelta" bgcolor="#e5e5e5" width="33.3333%">

<span class="dashicons dashicons-format-aside"></span>

<label for="fattureincloud_richiesta_fattura">
                    <?php echo __(
                        'Nella pagina <b>Checkout/Cassa</b> abilita la scelta del <b>tipo cliente</b> tra <b>PRIVATO</b> e <b>AZIENDA</b> e tra i seguenti documenti',
                        'woo-fattureincloud-premium'
                    );
                    ?>
</label>


<br>
<br>

<input type="radio" id="woofic_fattura_normale_checkout" name="fattureincloud_select_field_checkout" onclick="funzione_04()" value="woofic_fattura_normale_checkout" 

<?php

if ('woofic_fattura_normale_checkout' == get_option('fattureincloud_select_field_checkout')) {
        
        echo 'checked';
} else {
echo ''; 
}

?> >

<label for="woofic_fattura_normale_checkout"><?php echo __('Fattura/Ricevuta', 'woo-fattureincloud-premium'); ?></label>

<br>

<input type="radio" id="woofic_fattura_elettronica_checkout" name="fattureincloud_select_field_checkout" onclick="funzione_04()" value="woofic_fattura_elettronica_checkout" 

<?php

if ('woofic_fattura_elettronica_checkout' == get_option('fattureincloud_select_field_checkout')) {
        
        echo 'checked';
} else {
echo ''; 
}

?> >

<label for="woofic_fattura_elettronica_checkout"><?php echo __('Fattura Elettronica/Ricevuta', 'woo-fattureincloud-premium'); ?></label>

<br>


<input type="radio" id="woofic_fattura_normelettr_checkout" name="fattureincloud_select_field_checkout" onclick="funzione_04()" value="woofic_fattura_normelettr_checkout" 

<?php

if ('woofic_fattura_normelettr_checkout' == get_option('fattureincloud_select_field_checkout')) {
        
        echo 'checked';
} else {
echo ''; 
}

?> >
<label for="woofic_fattura_normelettr_checkout"><?php echo __('Fattura/FE/Ricevuta', 'woo-fattureincloud-premium'); ?></label>

<hr>

<span class="fattura_scelta_cliente">

<input type="radio" id="woofic_fattura_noricev_checkout" name="fattureincloud_select_field_checkout" onclick="funzione_04()" value="woofic_fattura_noricev_checkout" 

<?php

if ('woofic_fattura_noricev_checkout' == get_option('fattureincloud_select_field_checkout')) {
        
        echo 'checked';
} else {
echo ''; 
}

?> >

<label for="woofic_fattura_noricev_checkout"><?php echo __('Fattura', 'woo-fattureincloud-premium');

$wfp_woo_tipo_doc = get_option('fattureincloud_auto_save');

if ($wfp_woo_tipo_doc == "fattura") {

echo " <span style=\"font-size:10px;\">(impostato nella funzionalità 3) </span>";

}

?></label>

</span>

<br>

<span class="fatturae_scelta_cliente">

<input type="radio" id="woofic_fatturae_noricev_checkout" name="fattureincloud_select_field_checkout" onclick="funzione_04()" value="woofic_fatturae_noricev_checkout" 

<?php

if ('woofic_fatturae_noricev_checkout' == get_option('fattureincloud_select_field_checkout')) {
        
        echo 'checked';
} else {
echo ''; 
}

?> >

<label for="woofic_fatturae_noricev_checkout"><?php echo __('Fattura Elettronica', 'woo-fattureincloud-premium'); 

$wfp_woo_tipo_doc = get_option('fattureincloud_auto_save');

if ($wfp_woo_tipo_doc == "fatturaelettronica") {

echo " <span style=\"font-size:10px;\">(impostato nella funzionalità 3) </span>";

}

?></label>

</span>

<br>
<input type="radio" id="woofic_fattura_nulla_noricev_checkout" name="fattureincloud_select_field_checkout" value= "nulla"

<?php

if ('nulla' == get_option('fattureincloud_select_field_checkout')) {
echo 'checked';
} else {
echo ''; 
}

?>>

<label for="woofic_fattura_nulla_noricev_checkout">
<?php echo __('NULLA', 'woo-fattureincloud-premium'); ?>
</label>

<p></p>
<!-- <div id="tab11" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->

<div id="tab11" class="tab"><a href="https://woofatture.com/docs/funzionalita-4-scelta-cliente-privato-azienda-e-tipo-documento-dalla-versione-premium-1-9-0/" target="_blank">INFO</a></div>




    </td>

    <!-- ############################################################################################ -->
    <td id="cella_numero_imp">5</td>

    <td bgcolor="#e5e5e5" width="33.3333%">

    <label for="fattureincloud_invia_email_auto">
    <span class="dashicons dashicons-email"></span>
                <?php echo __('Se è attiva la creazione <b>Automatica</b> abilitare anche l\'invio <b>Automatico</b> via <b>Email</b><br> ', 'woo-fattureincloud-premium');
                ?></label>

                <input type="radio" id="yes" name="fattureincloud_invia_email_auto" value="yes"

<?php

if ('yes' == get_option('fattureincloud_invia_email_auto')) {
            
            echo 'checked';
} else {
    echo ''; 
}

?> >

                <label for="contactChoice1"><?php echo __('SI', 'woo-fattureincloud-premium'); ?></label>

                <input type="radio" id="no" name="fattureincloud_invia_email_auto" value="no"

<?php

if ('no' == get_option('fattureincloud_invia_email_auto')) {
        echo 'checked';
} else {
    echo '';
}
?> >

                <label for="contactChoice2"><?php echo __('NO', 'woo-fattureincloud-premium'); ?></label>

                <p></p>
<!-- <div id="tab8" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->

<div id="tab8" class="tab"><a href="https://woofatture.com/docs/funzionalita-5-come-inviare-le-email-in-automatico-quando-lordine-woocommerce-e-completato/" target="_blank">INFO</a></div>

                   


    </td>

    <!-- ############################################################################################ -->

    <td id="cella_numero_imp">6</td>
    
    <td bgcolor="#e5e5e5" width="33.3333%">
    <span class="dashicons dashicons-cart"></span>

    <label for="fattureincloud_paid">
                        <?php echo __('Abilita la creazione di una Fattura/Ricevuta <b>già pagata</b>', 'woo-fattureincloud-premium');
                        ?>
                    <p><span  STYLE="font-size:8.0pt" >Dall'introduzione delle API v2 questa funzionalità è automaticamente abilitata</span></p>    
                    
                    </label>
        
        <input type="hidden" name="fattureincloud_paid" value="0" />
        <input type="checkbox" name="fattureincloud_paid" id="fattureincloud_paid" value="1" onclick="return false" checked 
<?php

if (1 == get_option('fattureincloud_paid')) {
       
            echo 'checked';

} else {
            
            echo '';
}
        
?>>

<p></p>
<!-- <div id="tab7" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->

<div id="tab7" class="tab"><a href="https://woofatture.com/docs/e-possibile-impostare-la-generazione-di-un-documento-gia-pagata/" target="_blank">INFO</a></div>
        
       
        

    </td>
</tr>

<!-- ############################################################################################ -->

<!-- ############################################################################################ -->

<tr>

<td id="cella_numero_imp">7</td>
    <td bgcolor="white" width="33.3333%"> 

    <span class="dashicons dashicons-awards"></span>

    <label for="woofic_marca_bollo_elettronica">
                        <?php echo __(
                            'Applica la <b>Marca da Bollo Virtuale</b> di 2€ in automatico<br> nella <b>Fattura Elettronica</b> su Fattureincloud.it 
                            se l\'importo dell\'ordine supera 77.47€ ', 'woo-fattureincloud-premium'
                        );
                        ?></label>
        
        <input type="hidden" name="woofic_marca_bollo_elettronica" value="0" />
        <input type="checkbox" name="woofic_marca_bollo_elettronica" id="woofic_marca_bollo_elettronica" value="1" onclick="funzione_7_a()"
<?php

if (1 == get_option('woofic_marca_bollo_elettronica')) {
       
            echo 'checked';

} else {
            
            echo '';
}
        
?>>



<p></p>
<!-- <div id="tab13" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->

<div id="tab13" class="tab"><a href="https://woofatture.com/docs/funzionalita-7-marca-da-bollo-virtuale-2-e-versione-premium/" target="_blank">INFO</a></div>
        
       
        

    </td>

    <!-- ############################################################################################## -->

    <td id="cella_numero_imp">8</td>

    <td bgcolor="white" width="33.3333%">
    <span class="dashicons dashicons-id-alt"></span>

    <label for="fattureincloud_paid">
    <p>L'Aggiornamento dell'anagrafica cliente con i <b>dati della Fattura</b></p>
    <span  STYLE="font-size:8.0pt" >Dall'introduzione delle API v2 è automaticamente abilitata<br>
            Gli ordini e l'anagrafica creati con le API v1 non sono processabili</span></p>
            </label>

            <input type="hidden" name="update_customer_registry" value="0" />
            <input type="checkbox" name="update_customer_registry" id="update_customer_registry" value="1" onclick="return false" checked 
<?php
/*
if (1 == get_option('update_customer_registry')) {
    
    echo 'checked';

} else {
    echo '';
}
*/
?>>

<p></p>
<!-- <div id="tab6" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->

<!-- <div id="tab6" class="tab"><a href="admin.php?page=woo-fattureincloud-premium&tab=impostazioni#tab6show">Clicca per maggiori informazioni sulla funzionalità 8</a></div> -->

        

    </td>

        <!-- ############################################################################################## -->

        <td id="cella_numero_imp">9</td>

    <td bgcolor="white" width="33.3333%">

    <span class="dashicons dashicons-media-text"></span>

    <label for="show_short_descr">
            <?php echo __('Mostra nella fattura <b>la Breve Descrizione</b> del prodotto ', 'woo-fattureincloud-premium');
            ?>
        </label>

            <input type="hidden" name="show_short_descr" value="0" />
            <input type="checkbox" name="show_short_descr" id="show_short_descr" value="1" 
<?php

if (1 == get_option('show_short_descr') ) {
    echo 'checked';

} else {
    echo '';
}

?>>
<p></p>
<!-- <div id="tab5" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->

<div id="tab5" class="tab"><a href="https://woofatture.com/docs/funzionalita-9-descrizione-breve/" target="_blank">INFO</a></div>

            


    </td>
</tr>

<!-- ############################################################################################ -->

<!-- ############################################################################################ -->

<tr>
<td id="cella_numero_imp">10</td>
    
    <td bgcolor="#e5e5e5" width="33.3333%"> 

    <span class="dashicons dashicons-media-document"></span>

    <label for="show_long_descr">
            <?php echo __('Mostra nella fattura <b>la Descrizione</b> del prodotto ', 'woo-fattureincloud-premium');
            ?>
        </label>

            <input type="hidden" name="show_long_descr" value="0" />
            <input type="checkbox" name="show_long_descr" id="show_long_descr" value="1" 
<?php

if (1 == get_option('show_long_descr') ) {
    echo 'checked';

} else {
    echo '';
}

?>>

<p></p>
<!-- <div id="tab4" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->

<div id="tab4" class="tab"><a href="https://woofatture.com/docs/funzionalita-10-descrizione-estesa-versione-premium-dalla-1-8-0/" target="_blank">INFO</a></div>

           

    </td>
 <!-- ############################################################################################## -->

 <td id="cella_numero_imp">11</td>


    <td bgcolor="#e5e5e5" width="33.3333%">

    <span class="dashicons dashicons-media-spreadsheet"></span>

    <label for="woo_fattureincloud_sezionale">Opzionale: Imposta un <b>Sezionale</b> <br>( es: mettendo <b>/A</b> le fatture saranno numerate <b>1/A</b> )</label>

<input type="text" name="woo_fattureincloud_sezionale" placeholder=""
style="width: 60px;" value="<?php echo get_option('woo_fattureincloud_sezionale'); ?>">

<p></p>
<!-- <div id="tab3" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->

<div id="tab3" class="tab"><a href="https://woofatture.com/docs/sezionali/" target="_blank">INFO</a></div>




    </td>

     <!-- ############################################################################################## -->

     <td id="cella_numero_imp">12</td>

    <td bgcolor="#e5e5e5" width="33.3333%">

    <span class="dashicons dashicons-media-spreadsheet"></span>

    <label for="woo_sezionale_da_categoria">
    <?php echo __('Abilita un <b>Sezionale <u>Automatico</u></b> <br>prelevato dalla categoria <b>sezionale xx</b> del prodotto', 'woo-fattureincloud-premium');
    ?>
</label>

    <input type="hidden" name="woo_sezionale_da_categoria" value="0" />
    <input type="checkbox" name="woo_sezionale_da_categoria" id="woo_sezionale_da_categoria" value="1" 
<?php

if (1 == get_option('woo_sezionale_da_categoria') ) {
    echo 'checked';

} else {
    echo '';
}

?>>

<p></p>

<!-- <div id="tab2" class="tab"> <<< <a style=" text-decoration:underline;cursor:pointer;">info</a></div> -->

<div id="tab2" class="tab"><a href="https://woofatture.com/docs/funzionalita-12-sezionale-automatico-versione-premium-dalla-1-8-1/" target="_blank">INFO</a></div>



    </td>
</tr>

<!-- ############################################################################################ -->

<!-- ############################################################################################ -->

<tr>

 
 <td id="cella_numero_imp">13</td>
   
 <td id="wfic_voci_chkout" bgcolor="white" width="33.3333%"> 

<span class="dashicons dashicons-list-view"></span>

<div class="messaggio_disattiv"></div>

<label for="fattureincloud_partiva_codfisc">
<?php echo __(
    'Attiva le voci nel checkout di 
    <ul><li><b>Partita Iva</li></b>
    <li><b>Codice Fiscale</b> 
    <span style="font-size:12px">(solo se l\'indirizzo è italiano)</span></li>
    <li><b>PEC</b> <span style="font-size:12px">(per Fattura Elettronica)</span></li>
    <li><b>Codice Destinatario</b> <span style="font-size:12px">(per Fattura Elettronica)</span></li></ul>', 'woo-fattureincloud-premium'
);
?>
</label>

<input type="hidden" name="fattureincloud_partiva_codfisc" value="0" />
<input id="fattureincloud_partiva_codfisc" type="checkbox" name="fattureincloud_partiva_codfisc" value="1" 
<?php if (1 == get_option('fattureincloud_partiva_codfisc')) {
    echo 'checked';
} else {
echo '';
}

?> onclick="check_ita_field(this)">
<p></p>

<div id="tab10" class="tab"><a href="https://woofatture.com/docs/funzionalita-13-cf-piva-pec-cd/" target="_blank">INFO</a></div>







    </td>

     <!-- ############################################################################################## -->

     <td id="cella_numero_imp">14</td>

    <td bgcolor="white" width="33.3333%" align="center"> 
        

    <span class="dashicons dashicons-list-view"></span>
    <br>

<label for="woofic_soloricevute_chkout">
<?php echo __(
    'Disabilita obbligo<br> <b>Codice Fiscale</b><br>
    
    <i>NON ABILITARE SE è attiva la <b>funzionalità 4</b> 
    <a href="https://woofatture.com/docs/funzionalita-4-scelta-cliente-privato-azienda-e-tipo-documento-dalla-versione-premium-1-9-0/" target="_blank">scelta cliente</a>
    </i>', 'woo-fattureincloud-premium'
);
?>
</label>
<br>

<input type="hidden" name="woofic_soloricevute_chkout" value="0" />
<input type="checkbox" name="woofic_soloricevute_chkout" id="woofic_soloricevute_chkout" value="1"
<?php if (1 == get_option('woofic_soloricevute_chkout')) {
    echo 'checked';
} else {
echo '';
}

?>>
<p></p>

    </td>

     <!-- ############################################################################################## -->

     <td id="cella_numero_imp">15</td>

    <td bgcolor="white" width="33.3333%" align="center">

    <span class="dashicons dashicons-marker"></span><br>

    <label for="woofic_ordine_zero">
<?php echo __(
    'Disabilita la creazione<br> AUTOMATICA e MANUALE<br> di Fatture/Ricevute
     se il <b>totale dell\'ordine è uguale a zero</b><br>
     (<i>se l\'operazione lo consente</i>)', 'woo-fattureincloud-premium'
);
?>
</label>
<br>

<input type="hidden" name="woofic_ordine_zero" value="0" />
<input type="checkbox" name="woofic_ordine_zero" id="woofic_ordine_zero" value="1"
<?php if (1 == get_option('woofic_ordine_zero')) {
    echo 'checked';
} else {
echo '';
}

?>>


    </td>
</tr>

<!-- ############################################################################################## -->

<!-- ############################################################################################## -->

<tr>

 <!-- ############################################################################################## -->

 <td id="cella_numero_imp">16</td>


 <td bgcolor="#e5e5e5" width="33.3333%" align="center">

 <span class="dashicons dashicons-list-view"></span><br>

<label for="woo_fic_cf_chk">
<?php echo __(
'Abilita la verifica non vincolante<br> di correttezza formale <br> nei campi <b>Codice Fiscale</b><br> e <b>Partita Iva</b><br> nel checkout', 'woo-fattureincloud-premium'
);
?>
</label>
<br>

<input type="hidden" name="woo_fic_cf_chk" value="0" />
<input type="checkbox" name="woo_fic_cf_chk" id="woo_fic_cf_chk" value="1" class="funzione_16" onclick="funzione_16_a()"
<?php if (1 == get_option('woo_fic_cf_chk')) {
echo 'checked';
} else {
echo '';
}

?>>

<hr>

<label for="woo_fic_cf_chk_hard">
<?php echo __(
'Abilita la verifica <b>vincolante</b><br> di correttezza formale <br> nei campi <b>Codice Fiscale</b><br> e <b>Partita Iva</b><br> nel checkout', 'woo-fattureincloud-premium'
);
?>
</label>
<br>

<input type="hidden" name="woo_fic_cf_chk_hard" value="0" />
<input type="checkbox" name="woo_fic_cf_chk_hard" id="woo_fic_cf_chk_hard" value="1" class="funzione_16" onclick="funzione_16_b()"
<?php if (1 == get_option('woo_fic_cf_chk_hard')) {
echo 'checked';
} else {
echo '';
}

?>>



 </td>

<!--
         <td bgcolor="#e5e5e5" width="33.3333%" align="center">
    <span class="dashicons dashicons-cart"></span><br>
    <a href="#sotto">I codici del tipo di pagamento</a>
        </td>

-->

 <!-- ############################################################################################## -->

 <td id="cella_numero_imp">18</td>      

 <td bgcolor="#e5e5e5" width="33.3333%" align="center">


 <span class="dashicons dashicons-admin-generic"></span><br>

<label for="woo_fic_keep_data">
<?php echo __(
'Mantieni le impostazioni<br> del plugin in WordPress <br> anche dopo la rimozione<br>', 'woo-fattureincloud-premium'
);
?>
</label>
<br>

<input type="hidden" name="woo_fic_keep_data" value="0" />
<input type="checkbox" name="woo_fic_keep_data" id="woo_fic_keep_data" value="1"
<?php if (1 == get_option('woo_fic_keep_data')) {
echo 'checked';
} else {
echo '';
}

?>>


 </td>


<!-- ############################################################################################## -->

 <td id="cella_numero_imp">19</td>

    <td bgcolor="#e5e5e5" width="33.3333%" align="center">

    <span class="dashicons dashicons-email"></span><br>
    
    
    <label for="woo_fic_keep_data">
<?php echo __(
'Invia un email all\'amministratore dell\'installazione WordPress<br> per segnalare la mancata creazione automatica di una fattura', 'woo-fattureincloud-premium'
);
?>
</label>
<br>

<input type="hidden" name="woo_fic_send_error_email" value="0" />
<input type="checkbox" name="woo_fic_send_error_email" id="woo_fic_send_error_email" value="1"
<?php if (1 == get_option('woo_fic_send_error_email')) {
echo 'checked';
} else {
echo '';
}

?>>
    
    

</td>

   

</tr>



<!-- ################################################################################################# -->

<tr>

 
 <td id="cella_numero_imp">20</td>
   
 <td id="wfic_voci_chkout" bgcolor="white" width="33.3333%" align="center">
    
 <span class="dashicons  dashicons-admin-generic"></span><br>
    
 <p>Aggiungi un metodo di pagamento CUSTOM oltre a quelli standard WooCommerce</p>

 <label for="woo_fic_payment_method_custom_code">
<?php echo __(
'<b>Codice del metodo di pagamento</b> ', 'woo-fattureincloud-premium'
);
?>
</label>
<br>

<input type="text" name="woo_fic_payment_method_custom_code" placeholder=""
style="width: 190px;" value="<?php echo get_option('woo_fic_payment_method_custom_code'); ?>">



    <label for="woo_fic_keep_data">
<?php echo __(
'<b>Metodo di Pagamento</b>', 'woo-fattureincloud-premium'
);
?>
</label>
<br>

<input type="text" name="woo_fic_custom_pay_method" placeholder=""
style="width: 190px;" value="<?php echo get_option('woo_fic_custom_pay_method'); ?>">



 
</td>
 <td id="cella_numero_imp">#</td>
 <td id="wfic_voci_chkout" bgcolor="white" width="33.3333%"> </td>
 <td id="cella_numero_imp">#</td>
 <td id="wfic_voci_chkout" bgcolor="white" width="33.3333%"> </td>

</td>

</tr>



 <!-- ############################################################################################## -->

<tr>
        <td align="right" colspan="6">
        <input type="submit" value="Salva le Impostazioni" class="button button-primary button-large">
        
        <!--
        
        onclick="window.location='admin.php?page=woo-fattureincloud-premium&tab=impostazioni#setting-error-settings_updated';">


        -->
</form>

        </td>

    </tr>

</table>





<!-- ############################################################################################ -->
<table border="1" style="width:800px;" cellpadding="12" cellspacing="6">
    <tr>

    <td id="help" style="padding: 20px;background-color:white">

<div id="tab1show" class="tab-content">

<br>
<br>

<span class="dashicons dashicons-format-aside" style="font-size:80px;width:80px;height:80px"></span>

  <p>Imposta quale documento creare manualmente su Fattureincloud premendo il bottone in fondo all'ordine tra</p>
 <strong>
  <ul>
<li>Fattura Elettronica</li>
<li>Fattura</li>
<li>Ricevuta</li>
 </ul>
</strong>
 <a href="https://woofatture.com/documentazione/#Come_funziona">Pagina della Documentazione</a>
</div>  




<div id="tab2show" class="tab-content">
<a name="tab2show"></a>
<br>
<br>

<span class="dashicons dashicons-media-spreadsheet" style="font-size:80px;width:80px;height:80px"></span>

  <p>Assegnando una categoria nella forma <b>"sezionale + XXXX"</b> ad un prodotto, scritta esattamente così: <br>
  la parola <b>sezionale</b> tutta minuscola seguita da lettere o numeri <br>
  ( se numeri verrà aggiunto il carattere "/" in automatico per evitare di sovrascrivere la fattura numerica
  corrispondente )<br> verrà automaticamente creata una fattura col sezionale della categoria</p>

   Es: <br>
      <p><b>sezionale AA</b></p>

      <p>genererà un sezionale <b>AA</b></p>

    <p>quindi una fattura numerata così = nr. <b>1AA 2AA</b>, etc...</p>

   <p><b>IMPORTANTE</b> = è necessario impostare i prodotti del negozio in WoooCommerce in modo che <b>non siano vendute due categorie differenti di sezionali</b></p>

   
   <a href="https://woofatture.com/docs/funzionalita-12-sezionale-automatico-versione-premium-dalla-1-8-1/">Pagina della Documentazione</a>
   

</div>

<div id="tab3show" class="tab-content">
<a name="tab3show"></a>
<br>
<br>

<span class="dashicons dashicons-media-spreadsheet" style="font-size:80px;width:80px;height:80px"></span>

<p>
E' possibile impostare dei <b>Sezionali</b> per le vostre Fatture generate su Fattureincloud, in modo da poterle individuare a seconda delle vostre esigenze<br>
Inserisci il sezionale voluto nella casella, per esempio "<b>AA</b>" e le fatture saranno create manualmente o automaticamente con quel sezionale nella forma 
</p>
<b>
<ul>
<li>1AA</li>
<li>2AA</li>
<li>3AA</li>
</ul>
</b>
etc.

<p></p>
  <a href="https://woofatture.com/documentazione/#Sezionali_%5BVersione_Premium_dalla_1_8_0%5D">Pagina della Documentazione</a>


</div>

<div id="tab4show" class="tab-content">
<a name="tab4show"></a>
<br>
<br>

<span class="dashicons dashicons-media-document" style="font-size:80px;width:80px;height:80px"></span>
<p>
  Mostra nella fattura generata su Fattureincloud la <b>descrizione estesa</b> del prodotto impostata su WooCommerce
</p>
  <a href="https://woofatture.com/documentazione/#Descrizione_Estesa_%5BVersione_Premium_dalla_1_8_0%5D">Pagina della Documentazione</a>

</div>

<div id="tab5show" class="tab-content">
<a name="tab5show"></a>
<br>
<br>

<span class="dashicons dashicons-media-text" style="font-size:80px;width:80px;height:80px"></span>
<p>
  Mostra nella fattura generata su <b>Fattureincloud</b> dall'ordine <b>WooCommerce</b> la <b>breve descrizione</b> del prodotto impostata su WooCommerce
</p>
</div>

<div id="tab6show" class="tab-content">
<a name="tab6show"></a>
<br>
<br>

<span class="dashicons dashicons-id-alt" style="font-size:80px;width:80px;height:80px"></span>
<p>
L'aggiornamento dell’anagrafica clienti su <b>Fattureincloud.it</b> avviene con i dati anagrafici dell'ordine <b>WooCommerce</b> quando diventa una fattura/ricevuta<br>
<br>Se il cliente non esiste viene creato<br>
Se il cliente esiste già i dati vengono aggiornati<br>
<p>Il cliente viene aggiornato ricercando tra i campi <b>Partita Iva</b> e <b>Codice Fiscale</b></p>
</p>
<a href="https://woofatture.com/documentazione/#Aggiorna_Anagrafica_Cliente_%5BVersione_Gratuita_e_Versione_Premium%5D">Pagina della Documentazione</a>
</div>

<div id="tab7show" class="tab-content">
<a name="tab7show"></a>
<br>
<br>

<span class="dashicons dashicons-cart" style="font-size:80px;width:80px;height:80px"></span>
<p>
  Abilita la creazione di una <b>Fattura</b> / <b>Fattura Elettronica</b> / <b>Ricevuta</b> già Pagata <br>
  altrimenti imposta manualmente la data di scadenza<br>
  
</p>
<a href="https://woofatture.com/documentazione/#Fattura_/_Ricevuta_gia_pagata_o_data_personalizzata_%5BVersione_Gratuita_e_Versione_Premium%5D">Pagina della Documentazione</a>
  
</div>

<div id="tab8show" class="tab-content">
<a name="tab8show"></a>
<br>
<br>

<span class="dashicons dashicons-email" style="font-size:80px;width:80px;height:80px"></span>
<p>
  Abilita l'invio <b>automatico</b> dell'email, ATTENZIONE funziona solamente se è attiva la creazione automatica del documento ( Fattura Elettronica, Fattura o Ricevuta ) su Fattureincloud 
</p>
  <a href="https://woofatture.com/documentazione/#Come_inviare_le_Email_in_Automatico_quando_l%E2%80%99ordine_WooCommerce_e_Completato">Pagina della Documentazione</a>

</div>

<div id="tab9show" class="tab-content">
<a name="tab9show"></a>
<br>
<br>

<span class="dashicons dashicons-update" style="font-size:80px;width:80px;height:80px"></span>
<p>
Imposta quale documento sarà creato su Fattureincloud <b>automaticamente</b> quando l'ordine WooCommerce diventa</p>

<p>
"<i><b>Completato</b></i>" oppure "<i><b> in Sospeso</b></i>" oppure"<i><b> in Lavorazione</b></i>", tra

<strong>
<ul>
<li>Fattura Elettronica</li>
<li>Fattura</li>
<li>Ricevuta</li>
 </ul>
</strong>
 </p>
 <a href="https://woofatture.com/documentazione/#Generare_le_Fatture_Elettroniche_Ricevute_in_modo_automatico">Pagina della Documentazione</a>

</div>





<div id="tab10show" class="tab-content">
<a name="tab10show"></a>
<br>
<br>

<span class="dashicons dashicons-list-view" style="font-size:80px;width:80px;height:80px"></span>

<p>

    E' necessario abilitare questa voce per aggiungere i 4 campi necessari alla fatturazione italiana:

        <ul>
        <li><strong>Codice Fiscale</strong>&nbsp;(se l’indirizzo è italiano)</li>
        <li><strong>Partita Iva</strong> (opzionale)</li>
        <li><strong>PEC(per la Fattura Elettronica)</strong> (opzionale)</li>
        <li><strong>Codice Destinatario (per la Fattura Elettronica)</strong> (opzionale)</li>
        </ul>

        Le quattro voci sono visibili 
        <ul>
        <li>nella <b>pagina di checkout</b></li>
        <li>nell'<b>ordine</b> di WooCommerce</li>
        <li>nella pagina <b>"Il mio account"</b> del cliente</li>
        </ul> 
<hr>

        <p></p>
        <a href="https://woofatture.com/documentazione/#Codice_Destinatario_%7C_Codice_Fiscale_%7C_PEC_%7C_Partita_Iva_%5BVersione_Gratuita_e_Versione_Premium%5D">Pagina della Documentazione</a>

</p>
</div>

<div id="tab11show" class="tab-content">
<a name="tab11show"></a>
<br>
<br>

<span class="dashicons dashicons-format-aside" style="font-size:80px;width:80px;height:80px"></span>


<p>La funzionalità 4 abilita il <b>cliente</b> durante il checkout dell'ordine a scegliere se ricevere il documento come <b>Privato</b> oppure come <b>Azienda</b>, modificando così i dati necessari a compilare il documento.<br>
È possibile impostarlo in 5 combinazioni:<br>

<hr>
<ul>
    <li>Fattura + Ricevuta</li>        
    <li>Fattura Elettronica + Ricevuta</li>
    <li>Fattura + Fattura Elettronica + Ricevuta</li>
    <li>Fattura*</li>
    <li>Fattura Elettronica*</li>

</ul>
<hr>

<p style="background-color: #F5FD00;">*Le voci <b>Fattura</b> e <b>Fattura Elettronica</b> 
sono le uniche ad essere compatibili con l'automatismo della funzionalità 3 perché sono singoli documenti, le altre voci contengono scelte multiple</p>

</div>



<div id="tab12show" class="tab-content">
<a name="tab12show"></a>
<br>
<br>

<span class="dashicons dashicons-update" style="font-size:80px;width:80px;height:80px"></span>

<p>

<h4>È possibile scegliere lo stato dell'ordine in cui avviene la creazione automatica in Fattureincloud.it di 
    fattura/ricevuta, soddisfacendo così le esigenze specifiche del tuo negozio</h4>

<p><b>In Sospeso</b> =><br>
In attesa di pagamento – il magazzino è stato aggiornato</p>

<p><b>In Lavorazione</b> =><br> 
Il pagamento è stato ricevuto - il magazzino è stato aggiornato;<br>
l'ordine è in attesa di spedizione. <br>
Tutti gli ordini di prodotti passano dallo stato “In Lavorazione”, tranne quelli che contengono esclusivamente prodotti <a href="https://docs.woocommerce.com/document/digital-downloadable-product-handling/">virtuali e scaricabili</a></p>


<p><b>Completato</b> =><br>
Ordine completato - non richiede ulteriori azioni.</p>

Nella documentazione ufficiale WooCommerce qui sotto c'è un ulteriore approfondimento sull'argomento<br><br>
    
<a href="https://docs.woocommerce.com/document/managing-orders">https://docs.woocommerce.com/document/managing-orders</a>

</p>
</div>

<div id="tab13show" class="tab-content">
<a name="tab13show"></a>
<br>
<br>

<span class="dashicons dashicons-awards" style="font-size:80px;width:80px;height:80px"></span>

<p>

Attivando questa voce viene aggiunta in automatico la Marca Bollo Elettronica del valore di 2 euro per fatture con importi superiori a  77,47 euro<br>
<br>Prima <b>Verifica</b> se il tuo regime tributario necessita o meno di questo pagamento aggiuntivo
</p>
<a href="https://woofatture.com/documentazione/#Marca_da_Bollo_virtuale_2_E_%5BVersione_Premium%5D">Pagina della documentazione</a>
</div>



</td>

<!--######################################################################################################################################## -->



 
    </tr>

</table>


<!-- ############################################################################################ -->
<a name="sotto"></a>
<!-- ############################################################################################ -->

<table border="0" style="max-width:800px" cellpadding="12" cellspacing="6">



    <tr>
        <td style="width:40%" valign="top">

        Si consiglia di <b>verificare con molta attenzione</b> <br>che i dati per la Fattura Elettronica inviati a Fatureincloud siano corretti, <br>
        Gli autori del plugin declinano ogni responsabilità<br> per eventuali errori o mancanze nella generazione della Fattura Elettronica<br><br>

        <span class="dashicons dashicons-cart" style="font-size:60px;width:60px;height:60px"></span><br>
       
        I codici del tipo di pagamento (prelevati dallo 
        <a href="https://www.agenziaentrate.gov.it/"> SDI</a>) con cui viene pre-compilata<br>
         la Fattura Elettronica sono :<br>
        <i>
         <ol>
            <li>    <b>MP08</b> carta di pagamento (carta di credito e PayPal)</li>
            <li>    <b>MP02</b> assegno bancario</li>
            <li>    <b>MP05</b> bonifico bancario</li>
            <li>    <b>MP01</b> contanti (pagamento in contrassegno)</li>
        </ol>    
        </i>
         se è stato utilizzato un <b>altro tipo di pagamento</b> è necessario modificarlo <b>direttamente su Fattureincloud.it</b> 
        </td>
        <td valign="top"> 
        <span class="dashicons dashicons-chart-line" style="font-size:60px;width:60px;height:60px"></span><br>    
        
        <div id="zero_rate_settings"></div><hr> Per le <b>Aliquote Iva = 0%</b> 
        è possibile impostare quella specifica aggiungendola col rispettivo nome <b>Zero Rate (numero)</b> in WooCommerce > Impostazioni > Imposta > Aliquote addizionali:
<ul>

    <li><b>Zero Rate 7</b> = Regime dei minimi</li>
    <li><b>Zero Rate 9</b> = Fuori campo IVA</li>
    <li><b>Zero Rate 10</b> = Oper. non soggetta, art.7 ter</li>
    <li><b>Zero Rate 11</b> = Inversione contabile, art.7 ter </li>
    <li><b>Zero Rate 12</b> = Non Imponibile</li>
    <li><b>Zero Rate 13</b> = Non Imp. Art.8 </li>
    <li><b>Zero Rate 14</b> = Non Imp. Art.9 1C </li>
    <li><b>Zero Rate 16</b> = Non Imp. Art.41 D.P.R. 331/93 </li>
    <li><b>Zero Rate 17</b> = Non Imp. Art.72, D.P.R. 633/72 </li>
    <li><b>Zero Rate 18</b> = Non Imp. Art.74 quotidiani/libri </li>
    <li><b>Zero Rate 19</b> = Escluso Art.10 </li>
    <li><b>Zero Rate 20</b> = Escluso Art.13 5C DPR 633/72 </li>
    <li><b>Zero Rate 21</b> = Escluso Art.15 </li>
    <li><b>Zero Rate 23</b> = Escluso Art.74 ter D.P.R. 633/72</li>
    <li><b>Zero Rate 24</b> = Escluso Art.10 comma 1 </li>
    <li><b>Zero Rate 25</b> = Escluso Art.10 comma 20</li>
    <li><b>Zero Rate 26</b> = Non Imp. Art.9 </li>
    <li><b>Zero Rate 27</b> = Escluso Art.10 n.27 D.P.R 633/72 </li>
    <li><b>Zero Rate 30</b> = Regime del margine art.36 41/95</li>
    <li><b>Zero Rate 31</b> = Escluso Art.3 comma 4 D.P.R 633/72 </li>
    <li><b>Zero Rate 32</b> = Escluso Art.15/1c D.P.R 633/72 </li>
    <li><b>Zero Rate 33</b> = Non imp. Art.8/c D.P.R. 633/72 </li>
    <li><b>Zero Rate 34</b> = Non Imp. Art.7 ter" </li>
    <li><b>Zero Rate 35</b> = Escluso Art.7 D.P.R 633/72 </li>
    <li><b>Zero Rate 37</b> = Escluso Art.10 comma 9 </li>
    <li><b>Zero Rate 38</b> = Non imp. Art.7 quater DPR 633/72 </li>
    <li><b>Zero Rate 39</b> = Non Imp. Art.8 comma 1A</li>
    <li><b>Zero Rate 42</b> = Non Imp. Art.2 comma 4 D.P.R 633/72</li>
    <li><b>Zero Rate 43</b> = Non Imp. Art.18 633/72</li>
    <li><b>Zero Rate 44</b> = Fuori Campo IVA Art.7 ter D.P.R 633/72</li>
    <li><b>Zero Rate 45</b> = Non Imp. Art.10 n.18 DPR 633/72</li>
    <li><b>Zero Rate 46</b> = Esente Art.10 DPR 633/72</li>
    <li><b>Zero Rate 47</b> = Non imp. art.1 L. 244/2008</li>
    <li><b>Zero Rate 48</b> = Non imp. art.40 D.L. 427/93</li>
    <li><b>Zero Rate 49</b> = Non imp. art.41 D.L. 427/93</li>
    <li><b>Zero Rate 50</b> = Non imp. art.71 DPR 633/72</li>
    <li><b>Zero Rate 51</b> = Non imp. art.8 DPR 633/72</li>
    <li><b>Zero Rate 52</b> = Non imp. art.9 DPR 633/72 </li>
    <li><b>Zero Rate 53</b> = Regime minimi 2015 </li>
    <li><b>Zero Rate 55</b> = Non soggetta IVA </li>
    <li><b>Zero Rate 56</b> = R.C. art. 74/7-8 rottami e metalli ferrosi e non </li>
    <li><b>Zero Rate 57</b> = R.C. art. 17/5 materiale oro e argento </li>
    <li><b>Zero Rate 58</b> = R.C. art. 17/6/a settore edile subappalto </li>
    <li><b>Zero Rate 59</b> = R.C. art. 17/6/a-bis fabbricati </li>
    <li><b>Zero Rate 60</b> = R.C. art. 17/6/b telefoni cellulari </li>
    <li><b>Zero Rate 61</b> = R.C. art. 17/6/c prodotti elettronici </li>
    <li><b>Zero Rate 62</b> = R.C. art. 17/6/a-ter servizi comparto edile e settori connessi </li>
    <li><b>Zero Rate 63</b> = R.C. art. 17/6/d-bis,d-ter,d-quater gas/energia elettrica </li>
    <li><b>Zero Rate 64</b> = Non imp. art.71 DPR 633/72 (Vaticano) </li>
    <li><b>Zero Rate 65</b> = Non imp. art.71 DPR 633/72 (RSM) </li>
    <li><b>Zero Rate 66</b> = Contribuenti forfettari </li>


   
    <i>(i salti nella numerazione non sono un errore)</i>







</ul>   



        </td>

    </tr>
    




</table>

<script>
var $contents = jQuery('.tab-content');
$contents.slice(1).hide();
jQuery('.tab').click(function() {
  var $target = jQuery('#' + this.id + 'show').show();
  $contents.not($target).hide();
});
</script>
