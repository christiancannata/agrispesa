<?php
/** 
 * Don't access this directly, please 
*/

if (!defined('ABSPATH')) exit;

include plugin_dir_path(__FILE__) . '/check_conn.php';

$api_uid = get_option('wfic_id_azienda');
$api_key = get_option('wfic_api_key_fattureincloud');


//$lista_articoli = array();


$order_shipping_tax = $order_data['shipping_tax'];

$order_shipping_total = $order_data['shipping_total'];

error_log("spedizione totale piu tasse => ".$order_shipping_total."/".$order_shipping_tax);


$order_billing_payment_method = $order_data['payment_method'];

foreach ( $order->get_items('tax') as $tax_item ) {

    /* Set the tax labels by rate ID in an array */

    $tax_items_labels[$tax_item->get_rate_id()] = $tax_item->get_label();

   

    /* Get the tax label used for shipping (if needed) */

    if (!empty($tax_item->get_shipping_tax_total()) ) {
        
        $shipping_tax_label = $tax_item->get_label();

        $shipping_tax_rate = $tax_item->get_rate_percent();

        error_log("SHIPPING TAX RATE => ".$shipping_tax_rate);
       
        

    }

//    error_log("shipping label => ".$shipping_tax_label);

      // error_log (print_r($tax_item, true));

}

$order_note = $order->get_customer_note();
$order_billing_first_name = $order_data['billing']['first_name'];
$order_billing_last_name = $order_data['billing']['last_name'];
$order_billing_company = $order_data['billing']['company'];
$order_billing_address_1 = $order_data['billing']['address_1'];
$order_billing_address_2 = $order_data['billing']['address_2'];
$order_billing_city = $order_data['billing']['city'];
$order_billing_state = $order_data['billing']['state'];
$order_billing_postcode = $order_data['billing']['postcode'];
$order_billing_country = $order_data['billing']['country'];
$order_billing_email = $order_data['billing']['email'];
$order_billing_phone = $order_data['billing']['phone'];
$order_billing_method = $order_data['payment_method_title'];
$order_billing_payment_method = $order_data['payment_method'];

//#################################################################################################################    

//#######################################################################################################################
/*   compatibilità col plugin woo-piva-codice-fiscale-e-fattura-pdf-per-italia  DISATTIVA dalla versione 3.0.0 API v2*/
//#######################################################################################################################

/*
$id_ordine_scelto = $order_id;

if (get_post_meta($id_ordine_scelto, '_billing_piva', true) || get_post_meta($id_ordine_scelto, '_billing_cf', true) 
    || get_post_meta($id_ordine_scelto, '_billing_pa_code', true) || get_post_meta($id_ordine_scelto, '_billing_pec', true)
) {

    $order_billing_partiva = get_post_meta($id_ordine_scelto, '_billing_piva', true);
    $order_billing_codfis = get_post_meta($id_ordine_scelto, '_billing_cf', true);
    $order_billing_coddest = get_post_meta($id_ordine_scelto, '_billing_pa_code', true);
    $order_billing_emailpec = get_post_meta($id_ordine_scelto, '_billing_pec', true);

    if (empty($order_billing_coddest) && empty($order_billing_emailpec)) {
        $order_billing_coddest = "0000000";

        if ($order_billing_country !== 'IT') {
            $order_billing_emailpec = "";
            $order_billing_coddest = "XXXXXXX";
            $order_billing_postcode = "00000";
        }

    }
    
} elseif (get_post_meta($id_ordine_scelto, '_billing_partita_iva', true) || get_post_meta($id_ordine_scelto, '_billing_cod_fisc', true)*/

//########################################################################################################################    

//error_log("order_id =>".$order_id);
//error_log("id_ordine_Scelto => ".$id_ordine_scelto);

if (isset($wfic_woo_auto_activation) && $wfic_woo_auto_activation === true ) {

    $id_ordine_scelto = $order_id;

}

if (get_post_meta($id_ordine_scelto, '_billing_partita_iva', true) || get_post_meta($id_ordine_scelto, '_billing_cod_fisc', true)
    || get_post_meta($id_ordine_scelto, '_billing_pec_email', true) || get_post_meta($id_ordine_scelto, '_billing_codice_destinatario', true)
) {
    $order_billing_partiva = get_post_meta($id_ordine_scelto, '_billing_partita_iva', true);
    $order_billing_codfis = get_post_meta($id_ordine_scelto, '_billing_cod_fisc', true);
    $order_billing_emailpec = get_post_meta($id_ordine_scelto, '_billing_pec_email', true);
    $order_billing_coddest = get_post_meta($id_ordine_scelto, '_billing_codice_destinatario', true);

    if ($order_billing_country !== 'IT') {
        
        $order_billing_emailpec = "";
        $order_billing_coddest = "XXXXXXX";
        $order_billing_postcode = "00000";
        if (empty($order_billing_partiva)) {
            
            $order_billing_partiva = $order_billing_codfis;
            $order_billing_codfis = "";
        
        } else {
            
            $order_billing_codfis = "";
        }
        
    
    }


    if (empty($order_billing_coddest) && empty($order_billing_emailpec)) {
        $order_billing_coddest = "0000000";

        if ($order_billing_country !== 'IT') {
            $order_billing_emailpec = "";
            $order_billing_coddest = "XXXXXXX";
            $order_billing_postcode = "00000";
        }

    }


} else {

    $order_billing_partiva ="";
    $order_billing_codfis = "";
    $order_billing_emailpec = "";
    $order_billing_coddest = "0000000";

}



//####################################################################################################################


$spedizione_lorda = $order_data['shipping_total'] + $order_shipping_tax ;

error_log("Spedizione lorda => ".$order_data['shipping_total'] ." + ".$order_shipping_tax);

$spedizione_netta = $spedizione_lorda - $order_shipping_tax;



$codice_iva = '';


foreach ($order->get_items() as $item_key => $item_values):

    $item_data = $item_values->get_data();

    $line_total = $item_data['total'];

    if ($item_data['variation_id'] > 0) { 
    
        $product_id = $item_values->get_variation_id(); // the Product id

    } else {

        $product_id = $item_values->get_product_id(); // the Variable Product id
    }
    
    $wc_product = $item_values->get_product(); // the WC_Product object
    
    /* Access Order Items data properties (in an array of values) */
    
    $item_data = $item_values->get_data();

    $_product = wc_get_product($product_id);

    $order_vat_country =  $item_data['taxes']['total'];


    $tax_rates = WC_Tax::get_base_tax_rates($_product->get_tax_class(true));



//error_log(print_r($tax_rates, true));


    $mostra_percentuale_tasse = WC_Tax::get_rate_percent(key($order_vat_country));


//error_log(print_r("MOSTRA PERCENTUALE TASSE =>".$mostra_percentuale_tasse, true));

    $mostra_percentuale_tasse_num     = str_replace('%', '', $mostra_percentuale_tasse); // Tax rate


error_log(print_r("MOSTRA PERCENTUALE TASSE =>".$mostra_percentuale_tasse_num, true));

    //$tax_rate = reset($tax_rates);
    
    //##########################################################

    if (!empty($mostra_percentuale_tasse )) {


        foreach  (  $wfic_vat_array_id_array as $ratio ) {   
        
                    if ($item_data['tax_class'] == 'custom-rate-' . $ratio )  {
                                    
                    $codice_iva = $ratio;

                    error_log("ratio => ".$ratio);
                                        
                } 

        }

            


/*
            if ($mostra_percentuale_tasse_num == 22) {

                $codice_iva = 0;

            } elseif ($mostra_percentuale_tasse_num == 10) {

                $codice_iva = 3;

            } elseif ($mostra_percentuale_tasse_num == 20) {

                $codice_iva = 2;

            } elseif ($mostra_percentuale_tasse_num == 21) {

                $codice_iva = 1;

            } elseif ($mostra_percentuale_tasse_num == 23) {

                $codice_iva = 40;

            } elseif ($mostra_percentuale_tasse_num == 24) {

                $codice_iva = 41;
            
            } elseif ($mostra_percentuale_tasse_num == 4) {

                $codice_iva = 4;   

            } elseif ($mostra_percentuale_tasse_num == 5) {

                $codice_iva = 54;

            } elseif ($mostra_percentuale_tasse_num == 8) {

                $codice_iva = 29;

//##########################################################        

            }
            
            */
            
            if ($mostra_percentuale_tasse_num == 0) {

                            
            foreach  (  $wfic_vat_array_id_array as $ratio ) 
  
           
/* foreach (  array( 0, 1, 2, 3, 4, 6, 7, 9, 10 ) as $ratio ) */

                        {   

                        if ($item_data['tax_class'] == 'zero-rate-' . $ratio ) {
                            
                            $codice_iva = $ratio;

                            error_log("Codice Iva ratio => $codice_iva");

                        
                        } 

                        elseif (empty($codice_iva)) {
                            $codice_iva = 6; 

                            //print "CODICE IVA VUOTO" .$codice_iva;
                            error_log("Codice Iva vuoto => $codice_iva");
                        }


                    }

            } if ($mostra_percentuale_tasse_num == 22) {

                $codice_iva = 0;

            } elseif ($mostra_percentuale_tasse_num !== 22 && $mostra_percentuale_tasse_num > 0) { 

               
                foreach($wfic_vat_total_value as $key => $val){

                //error_log(print_r($val['id']." | ".$val['value'], true));
        
                //error_log(" shipping_tax_rate =>".$shipping_tax_rate);
               
    
                    if ($val['value'] == (int)$mostra_percentuale_tasse_num) {
        
                        
                        //error_log($val['value']);
                        //error_log($val['id']);
        
                        error_log("codice IVA TROVATO => ".$val['id']);
        
                        $codice_iva = $val['id'];
        
        
                    }

                    
    
            }
    
            
            } elseif (empty($tax_rates)) {
    
                $codice_iva = 6;

                        //  print "CODICE IVA EMPTY" .$codice_iva;
            }


  
    }




    //##########################################################à

    $prezzo_singolo_prodotto = ((round($item_data['total'], 2)+$item_data['total_tax']) / $item_data['quantity']);

    $prezzo_netto_singolo_prodotto = (round($item_data['total'], 2) / $item_data['quantity']);

    
    //$ivatosiono = true;

    //######################################################

    $mostra_brevedesc = '';

    if (1 == get_option('show_short_descr') ) {

        $mostra_brevedesc = $wc_product->get_short_description();

    }

    //######################################################à

    $mostra_lungadesc = '';

    if (1 == get_option('show_long_descr') ) {

        $mostra_lungadesc = $wc_product->get_description();

    }

    //########################################################

    $sezionale_woofatture = '';
    
    if (!empty(get_option('woo_fattureincloud_sezionale'))) {

        $sezionale_woofatture = get_option('woo_fattureincloud_sezionale');

        
    } elseif (1 == get_option('woo_sezionale_da_categoria')) { 

        if (!empty($sezionale_fic_dacategoria)) { 
        
            $sezionale_woofatture = $sezionale_fic_dacategoria;

        }

    }
    
    //#########################################################

    /*
    $lista_articoli[] = array(
    
        "nome" => $item_data['name'],
        "codice" => $wc_product->get_sku(),
        "descrizione" => $mostra_brevedesc .' '. $mostra_lungadesc,
        "quantita" => $item_data['quantity'],
        "cod_iva" => $codice_iva,
        "prezzo_netto" => $prezzo_netto_singolo_prodotto,
        "prezzo_lordo" => $prezzo_singolo_prodotto


    );

    */

    $lista_articoli_api2[] = array(
    
        "name" => $item_data['name'],
        "code" => $wc_product->get_sku(),
        "description" => $mostra_brevedesc.' '. $mostra_lungadesc,
        "qty" => $item_data['quantity'],
        "net_price" => $prezzo_netto_singolo_prodotto,
        "gross_price" => $prezzo_singolo_prodotto,
        "vat" => array (
            "id" => $codice_iva
        ),
        "apply_withholding_taxes" => true
    );


    //###########################################################

    if ('paypal' == $order_billing_payment_method) {

        $payment_method_fic = 'MP08';

    } elseif ('stripe' == $order_billing_payment_method) {

        $payment_method_fic = 'MP08';

    } elseif ('bacs' == $order_billing_payment_method) {

        $payment_method_fic = 'MP05';

    } elseif ('cheque' == $order_billing_payment_method) {

        $payment_method_fic = 'MP02';

    } elseif ('cod' == $order_billing_payment_method) {

        $payment_method_fic = 'MP01';

    } else {

        $payment_method_fic = 'MP01';
    }


endforeach;

//#########################################



foreach( $order->get_items('fee') as $item_id => $item_fee ){


    // The fee name
    $fee_name = $item_fee->get_name();

    error_log("Fee presente =>".$fee_name);

    // The fee total amount
    $fee_total = round($item_fee->get_total() , 2, PHP_ROUND_HALF_UP);    

    // The fee total tax amount
    $fee_total_tax = $item_fee->get_total_tax();

    error_log("Fee total tax =>".$fee_total_tax);

    if ($fee_total_tax > 0) {

        $cod_fee_iva = 0;

        error_log("codice fee iva =>".$cod_fee_iva);

    } else {

        $cod_fee_iva = 6;
    }


    $total_fee_fic = round($fee_total + $fee_total_tax , 2, PHP_ROUND_HALF_UP);    

    if (($fee_name !== "Rivalsa INPS") && ($fee_name !== "Cassa Previdenza") && ($fee_name !== "Marca da Bollo") ) {

        error_log("codice fee iva =>".$cod_fee_iva);


    $lista_articoli_api2[] 
        
        =   array(

            "name" => $fee_name,
            "qty" => 1,
            "vat" => array (
                "id" =>  $cod_fee_iva 
            ),
            "net_price" => $fee_total,
            "gross_price" => $total_fee_fic


        );


        
    } elseif ($fee_name == "Marca da Bollo") {

        $lista_articoli_api2[] 
        
        =   array(

            "name" => $fee_name,
            "qty" => 1,
            "vat" => array (
                "id" => 21
            ),
            "net_price" => $fee_total,
            "gross_price" => $total_fee_fic


        );



    }
}


###############################################################
#
#           Shipping Tax
#
###############################################################

//error_log("Order shipping Tax RATE =>".$shipping_tax_rate);

if (!$shipping_tax_rate) {

    $cod_shipping_iva = 6;

    $shipping_tax_class = get_option( 'woocommerce_shipping_tax_class');

    error_log("Shipping Tax Class => ". $shipping_tax_class);

    foreach  (  $wfic_vat_array_id_array as $ratio ) {  

        if ($shipping_tax_class == 'zero-rate-' . $ratio )  {

            $cod_shipping_iva = $ratio;

            error_log("shipping vat ratio => ".$ratio);

        }         
        
    } 
       
    
} else { 

   
###########################################


$shipping_tax_class = get_option( 'woocommerce_shipping_tax_class');

foreach  (  $wfic_vat_array_id_array as $ratio ) {   

       
    if ($shipping_tax_class == 'custom-rate-' . $ratio )  {
                    
    $cod_shipping_iva = $ratio;

    error_log("shipping vat ratio => ".$ratio);
                        
    } 

}


###########################################


    if ( $shipping_tax_rate == 0) {

    
        $cod_shipping_iva = 6;

###############################################

    } elseif ( $shipping_tax_rate == 22) {

    
        $cod_shipping_iva = 0;

###############################################

    } elseif ( $shipping_tax_rate > 0) { 

    
        foreach($wfic_vat_total_value as $key => $val){

                if ($val['value'] == (int)$shipping_tax_rate && $val['value'] !== 22) {
            
                    
                        $cod_shipping_iva = $val['id'];


                }

        }
        
    }

}
        

error_log("codice shipping Iva =>".$cod_shipping_iva);

error_log("costo spedizione = ". $spedizione_lorda);

            $lista_articoli_api2[] 
        
            =   array(
    
                "name" => "Spese di Spedizione",
                "qty" => 1,
                "net_price" => $spedizione_netta,
                "gross_price" => $spedizione_lorda,
                "vat" => array (
                    "id" => $cod_shipping_iva
                    )
                
    
    
            );




################################################
#
#       DATA
#
################################################

if (isset($_POST['woo-datepicker'])) { 

    /*$data_di_scadenza;*/
    $data_di_scadenza = $_POST['woo-datepicker'];
    
    
} else {

    $data_di_scadenza = date("Y-m-d");

}

################################################################
#
#       Salva Anagrafica ora con API v2 non più funzionante
#
################################################################


$salva_ononsalva = false;

if (1 == get_option('update_customer_registry') ) {

    $salva_ononsalva = true;

}


################################################
#
#       Fattura già pagata o non pagata
#
################################################

if (empty($order_data['payment_method']) && 
1 == get_option('fattureincloud_paid') &&
 $order_data['total'] > 0 ) {

    $fattureincloud_invoice_paid = 'not';
    $mostra_info_pagamento = false;
    $data_saldo = 'not';
    $data_scadenza = date("Y-m-d");

    /*
    $fattureincloud_invoice_paid = 'not';
    $mostra_info_pagamento = false;
    $data_saldo = 'not';
    $data_scadenza = date('d/m/Y');
    */
    //$data_scadenza = 'not';

} elseif (1 == get_option('fattureincloud_paid') ) {

    $fattureincloud_invoice_paid = $order_data['payment_method'];
    $mostra_info_pagamento = true;
    $data_saldo = $order_data['date_created']->date("Y-m-d");
    $data_scadenza = date("Y-m-d");

} elseif (0 == get_option('fattureincloud_paid') ) {

    $fattureincloud_invoice_paid = 'not';
    $mostra_info_pagamento = false;
    $data_saldo = 'not';
    $data_scadenza = $data_di_scadenza; //date('d/m/Y');

} 

#####################################################
#
#       Marca da Bollo 
#
#####################################################


if ((isset($_POST['submit_ricevuta_fattureincloud']) 
    || (isset($_POST['submit_send_fattureincloud'])))
) {

    if ( 1 < get_option('woofic_marca_bollo_elettronica')) 

    
    $woofic_marca_bollo = 0;

} elseif (1 == get_option('woofic_marca_bollo_elettronica')) {
    
    
    //include plugin_dir_path(__FILE__) . 'marca_bollo.php';
/*

    $lista_articoli_api2[] 
        
    =   array(

        'name' => 'Bollo in fattura',
        'net_price' => 2,
        'not_taxable' => true,
        'apply_withholding_taxes' => false,
        'vat' =>  array (
            
            'id' => 21
        
        )   

    );
*/

}



#########################################################


//if (!isset($_POST['submit_ricevuta_fattureincloud'])) {

if (isset($_POST['woo-datepicker'])) {

    $data_documento = $_POST['woo-datepicker'];

} else {
   
    $data_documento = date("Y-m-d");
}


#########################################################

$custom_note_var = get_option('wfic_custom_note');

if (isset($custom_note_var)) {
    $order_note = $order_note ." ". $custom_note_var;
}

#########################################################

$rivalsa_inps = get_option('woo_wfic_riv');
$rivalsa = NULL;

if (isset($rivalsa_inps)) {

    if (($rivalsa_inps == 1)) {
        $rivalsa = "4";
    } else {
        $rivalsa = '';
    }

}

#########################################################

$cassa_prev_wfic = get_option('woo_wfic_cassa');

error_log("cassa previdenza =>".$cassa_prev_wfic);

$cassa_wfic = NULL;

if (isset($cassa_prev_wfic)) {

    if (($cassa_prev_wfic == 1)) {
        $cassa_wfic = get_option('woo_cassa_tax_percent');

        error_log("cassa previdenza tax =>".$cassa_wfic);

    } else {
        $cassa_wfic = '';
    }

}

#########################################################


if ($order_data['total'] == 0) {

    $order_billing_method = "gratuito";
    $fattureincloud_invoice_paid = "gratuito";
    $mostra_info_pagamento = true;
    $data_saldo = $order_data['date_created']->date("Y-m-d");
    $data_scadenza = date("Y-m-d");

} /*else {

    $order_billing_method = $order_data['payment_method_title'];
    $fattureincloud_invoice_paid = $order_data['payment_method'];

}*/

#########################################################

if (empty($order_billing_company)) {

    $wfic_name_tosend = $order_billing_first_name . " " . $order_billing_last_name;

} elseif (empty($order_billing_first_name)) {

    $wfic_name_tosend = $order_billing_company;

} else {

    $wfic_name_tosend = $order_billing_company." / ".$order_billing_first_name . " " . $order_billing_last_name;

}
###########################################################

/*
$fattureincloud_request = array(

    "api_uid" => $api_uid,
    "api_key" => $api_key,
    "nome" => $wfic_name_tosend ,
    "indirizzo_via" => $order_billing_address_1,
    "indirizzo_cap" => $order_billing_postcode,
    "indirizzo_citta" => $order_billing_city,
    "indirizzo_provincia" => $order_billing_state,
    "paese_iso" => $order_billing_country,
    "prezzi_ivati" => $ivatosiono ,
    "piva" => $order_billing_partiva,
    "cf" => $order_billing_codfis,
    "salva_anagrafica" => $salva_ononsalva,
    "numero" => $sezionale_woofatture,
    "data" => $data_documento,
    "marca_bollo" => $woofic_marca_bollo,
    "rivalsa" => $rivalsa,
    "cassa" => $cassa_wfic,
    "oggetto_visibile" => "Ordine numero ".$id_ordine_scelto,
    "note" => $order_note,
    "mostra_info_pagamento" => $mostra_info_pagamento,
    "metodo_pagamento" => $order_billing_method,
    "lista_articoli" => $lista_articoli,
    "lista_pagamenti" => array(
        array(
        "data_scadenza" => $data_scadenza,
        "importo" => 'auto',
        "metodo" => $fattureincloud_invoice_paid,
        "data_saldo" => $data_saldo ,

        )
    ),
    "PA" => $fatturaelettronica_fic,
    "PA_tipo_cliente" => 'B2B',
    "PA_data" => $data_scadenza,
    "PA_pec" => $order_billing_emailpec,
    "PA_codice" => $order_billing_coddest,
    "PA_modalita_pagamento" => $payment_method_fic,

    "extra_anagrafica" => array(
        "mail" => $order_billing_email,
        "tel" => $order_billing_phone
    )
);

*/

#################################################################

$company_ID =   get_option('wfic_id_azienda');

$wfic_token = get_option('wfic_api_key_fattureincloud');

###################################################################

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

$wfic_metodo_custom_codice = get_option('woo_fic_payment_method_custom_code');

$wfic_metodo_custom_nome = get_option('woo_fic_custom_pay_method');

foreach ($result_payment_list_fic as $vals_list_pay) { 

    foreach ($vals_list_pay as $vals_list_pay_id) {


     
        if ($order_billing_payment_method == "paypal" && $vals_list_pay_id['name'] == "Paypal") {
            
            $payment_list_woo_fic_id = array("id" => $vals_list_pay_id['id']);

        } elseif ($order_billing_payment_method == "ppcp-gateway" && $vals_list_pay_id['name'] == "Paypal") {

            $payment_list_woo_fic_id = array("id" => $vals_list_pay_id['id']);

        } elseif ($order_billing_payment_method == "ppec-paypal" && $vals_list_pay_id['name'] == "Paypal") {

            $payment_list_woo_fic_id = array("id" => $vals_list_pay_id['id']);

        } elseif ($order_billing_payment_method == "bacs" && $vals_list_pay_id['name'] == "Bonifico Bancario") {
            
            $payment_list_woo_fic_id = array("id" => $vals_list_pay_id['id']);

        } elseif ($order_billing_payment_method == "stripe" && $vals_list_pay_id['name'] == "Stripe") {
            
            $payment_list_woo_fic_id = array("id" => $vals_list_pay_id['id']);

        } elseif ($order_billing_payment_method == "cheque" && $vals_list_pay_id['name'] == "Assegno") {
            
            $payment_list_woo_fic_id = array("id" => $vals_list_pay_id['id']);

        } elseif ($order_billing_payment_method == "cod" && $vals_list_pay_id['name'] == "Pagamento alla Consegna") {
            
            $payment_list_woo_fic_id = array("id" => $vals_list_pay_id['id']);

        } elseif ($order_billing_payment_method == "" && $vals_list_pay_id['name'] == "gratuito") { 

            $payment_list_woo_fic_id = array("id" => $vals_list_pay_id['id']);

        } elseif ($order_billing_payment_method == "" && $vals_list_pay_id['name'] == "Gratuito") { 

            $payment_list_woo_fic_id = array("id" => $vals_list_pay_id['id']);

        } elseif ($order_billing_payment_method == $wfic_metodo_custom_codice && $vals_list_pay_id['name'] == $wfic_metodo_custom_nome ) { 

            error_log(  "codice del metodo di pagamento custom=> ".$wfic_metodo_custom_codice);

            error_log( "nome del metodo di pagamento custom => ". $wfic_metodo_custom_nome);

            $payment_list_woo_fic_id = array("id" => $vals_list_pay_id['id']);

            error_log( "ID metodo pagamento =>".print_r($payment_list_woo_fic_id));

        } elseif (!empty($order_billing_method) && $vals_list_pay_id['name'] == "altro") {

            $payment_list_woo_fic_id = array("id" => $vals_list_pay_id['id']);
        
        }
    }

}


if (!empty($new_conto_saldo))  { 
$type = 'success';
$message = __('Conto di Saldo '.$new_conto_saldo.' aggiunto', 'woo-fattureincloud');
add_settings_error('woo-fattureincloud', esc_attr('settings_updated'), $message, $type);
settings_errors('woo-fattureincloud');
}


###############################################################################
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api-v2.fattureincloud.it/c/'.$company_ID.'/settings/payment_methods');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array(
    "Authorization: Bearer ".$wfic_token."",
    "Content-Type: application/json",
 );

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result_pay_met = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$result_payment_methods_fic = json_decode($result_pay_met, true);


//print_r($result_payment_methods_fic);
//echo "Metodi<br>";


foreach ($result_payment_methods_fic as $vals_met_pay) {


    foreach ($vals_met_pay as $vals_met_pay_id) {

  /*    
        echo "<pre>";
        print_r($vals_met_pay_id['name']);
        echo "</pre>";
  */      

        if ($order_billing_payment_method == "bacs" && $vals_met_pay_id['name']=== "Bonifico Bancario") {
            
            

            $payment_method_woo_fic_order = $vals_met_pay_id['id'];

            $payment_method_fic_ei_code = 'MP05';

        } elseif ($order_billing_payment_method == "paypal" && $vals_met_pay_id['name']=== "Paypal") {
            
            

            $payment_method_woo_fic_order = $vals_met_pay_id['id'];

            $payment_method_fic_ei_code = 'MP08';

        } elseif ($order_billing_payment_method == "ppcp-gateway" && $vals_met_pay_id['name']=== "Paypal") {
            
            

            $payment_method_woo_fic_order = $vals_met_pay_id['id'];

            $payment_method_fic_ei_code = 'MP08';

        } elseif ($order_billing_payment_method == "ppec-paypal" && $vals_met_pay_id['name']=== "Paypal") {
            
            

            $payment_method_woo_fic_order = $vals_met_pay_id['id'];

            $payment_method_fic_ei_code = 'MP08';

        } elseif ($order_billing_payment_method == "stripe" && $vals_met_pay_id['name']=== "Stripe") {
            
            

            $payment_method_woo_fic_order = $vals_met_pay_id['id'];

            $payment_method_fic_ei_code = 'MP08';

        } elseif ($order_billing_payment_method == "cheque" && $vals_met_pay_id['name']=== "Assegno") {
            
            

            $payment_method_woo_fic_order = $vals_met_pay_id['id'];

            $payment_method_fic_ei_code = 'MP05';

        } elseif ($order_billing_payment_method == "cod" && $vals_met_pay_id['name']=== "Pagamento alla Consegna") {
            
            

            $payment_method_woo_fic_order = $vals_met_pay_id['id'];

            $payment_method_fic_ei_code = 'MP01';

        } elseif ($order_billing_payment_method == "" && $vals_met_pay_id['name']=== "gratuito") {
            
            

            $payment_method_woo_fic_order = $vals_met_pay_id['id'];

            $payment_method_fic_ei_code = 'MP01';

            
        } elseif ($order_billing_payment_method == "" && $vals_met_pay_id['name']=== "Gratuito") {
            
            

            $payment_method_woo_fic_order = $vals_met_pay_id['id'];

            $payment_method_fic_ei_code = 'MP01';

            
        } elseif ($order_billing_payment_method == $wfic_metodo_custom_codice && $vals_met_pay_id['name']=== $wfic_metodo_custom_nome) {
            
            

            $payment_method_woo_fic_order = $vals_met_pay_id['id'];

            $payment_method_fic_ei_code = 'MP08';

        } elseif (($order_billing_payment_method !== "bacs") && ($order_billing_payment_method !== "paypal") &&
        
        ($order_billing_payment_method !== "ppcp-gateway") && ($order_billing_payment_method !== "ppec-paypal") &&

        ($order_billing_payment_method !== "stripe") && ($order_billing_payment_method !== "cheque") &&
        
        ($order_billing_payment_method !== "cod") && ($order_billing_payment_method !== "") 
        
        && $vals_met_pay_id['name']=== "altro") {

            $payment_method_woo_fic_order = $vals_met_pay_id['id'];

            $payment_method_fic_ei_code = 'MP08';
        
        
        }
        


    }

}


#################################################################################################
#                   ANAGRAFICA CLIENTE
#################################################################################################



#####################################################
# ELENCO CLienti da Fattureincloud.it
#####################################################

$url = "https://api-v2.fattureincloud.it/c/".$company_ID."/entities/clients?fieldset=detailed";


include plugin_dir_path(__FILE__) . '/retrive_data.php';

$json_lista_clienti = json_decode($result, true);

if (!is_array($json_lista_clienti)) {  error_log("Lista clienti non caricata");  }


//error_log(print_r($json_lista_clienti, true));

//echo $order_billing_email;
  

/*
$found = (array_search($order_billing_email , array_column($json_lista_clienti, 'email') )) ;
*/
if ( count($json_lista_clienti['data']) > 0 ) {


error_log("clienti già presenti su fattureincloud.it, vedo se necessario aggiornare");

error_log( $order_billing_email." || " . $order_billing_codfis ." ||".$order_billing_partiva  );



    foreach ($json_lista_clienti as $value) {

        if (is_array($value)) {

            //foreach ($value as $value2) {

            //error_log(print_r($value, true));

####################################################################################################################


            $found_vat = (array_search($order_billing_partiva , array_column($value, 'vat_number') )) ;


#####################################################à

            if (($order_billing_partiva > 0 ) && (is_int($found_vat))) { 


            error_log("(".$found_vat. ") P.Iva cliente già presente => ".$value[$found_vat]['vat_number']);

            $id_cliente_daup = $value[$found_vat]['id'] ;

            error_log("ID cliente da aggiornare => ".$id_cliente_daup);

            }

##########################################

            $found_cf = (array_search($order_billing_codfis , array_column($value, 'tax_code') )) ;

#########################################
            

            if (is_int($found_cf)) { 

            error_log("(".$found_cf. ") CF già presente =>".$value[$found_cf]['tax_code']);
                
                    $id_cliente_daup = $value[$found_cf]['id'] ;
                
            error_log("ID cliente da aggiornare => ".$id_cliente_daup);
            
            }
                      
##############################################

            $found_email = (array_search($order_billing_email , array_column($value, 'email') )) ;
                
##############################################

           if (is_int($found_email)) { 
                
            error_log("(".$found_email. ") email cliente già presente  => ".$value[$found_email]['email']);
                    $id_cliente_daup = $value[$found_email]['id'] ;
            error_log("ID cliente da aggiornare => ".$id_cliente_daup);

            }


              

##################################################################
#           Aggiorna cliente se già presente
##################################################################

                if (isset($id_cliente_daup)) {  
                
 /*
                $id_cliente = ($value[$found_email]['id']);
                $nome_cliente = ($value2['name']);
                $codice_fiscale_fic = ($value2['tax_code']);
                $partita_iva_fic = ($value2['vat_number']);
                $email_cliente_fic = ($value2['email']);

                $found = array_search($order_billing_email , $value);
                
             */


                    $data_pre_cliente = array ("data" => array(

                        "id" => $id_cliente_daup,
                        "name" => $wfic_name_tosend,
                        "country_iso" => $order_billing_country,
                        "first_name" => $order_billing_first_name,
                        "last_name" => $order_billing_last_name,
                        "vat_number" => $order_billing_partiva,
                        "tax_code" => $order_billing_codfis,
                        "address_street" => $order_billing_address_1,
                        "address_postal_code" => $order_billing_postcode,  
                        "address_city" => $order_billing_city,
                        "address_province" => $order_billing_state,
                        "email" => $order_billing_email,
                        "certified_email" => "$order_billing_emailpec",
                        "phone" => $order_billing_phone,
                        "ei_code" => $order_billing_coddest
                        ));

                        //print_r($data_pre_cliente['data'])." data pre [data]";

                        $entity_result = $data_pre_cliente['data'];

                        $put_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/entities/clients/".$id_cliente_daup;

                        //echo "PUT URL ".$put_url;

                        $data_to_put_wfic_postfields = json_encode($data_pre_cliente);

                        include plugin_dir_path(__FILE__) . '/put_data.php';

                        //$id_cliente = $value2['id'];

                        include plugin_dir_path(__FILE__) . '/invio_fattura.php';

                break;

                    

                    

                } else { 

                    

                 //   echo "STO CREANDO<br>";

                    ###############################################################
                    #       Crea il cliente se non è già presente
                    ###############################################################
                    
                                    $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/entities/clients";
                    
                                    $data_pre_cliente_nuovo = array ("data" => array(
                                
                                        "name" => $wfic_name_tosend,
                                        "country_iso" => $order_billing_country,
                                        "first_name" => $order_billing_first_name,
                                        "last_name" => $order_billing_last_name,
                                        "vat_number" => $order_billing_partiva,
                                        "tax_code" => $order_billing_codfis,
                                        "address_street" => $order_billing_address_1,
                                        "address_postal_code" => $order_billing_postcode,  
                                        "address_city" => $order_billing_city,
                                        "address_province" => $order_billing_state,
                                        "email" => $order_billing_email,
                                        "certified_email" => "$order_billing_emailpec",
                                        "phone" => $order_billing_phone,
                                        "ei_code" => $order_billing_coddest
                                      
                                    )
                                    );
                                
                                    //print_r($data_pre_cliente_nuovo['data'])." data pre [data]";

                                    $entity_result = $data_pre_cliente_nuovo['data'];
                                
                                    $data_tosend_wfic_postfields = json_encode($data_pre_cliente_nuovo);
                                
                                    include plugin_dir_path(__FILE__) . '/send_data.php';
                                /*
                                    echo "<hr>";
                                    print_r($response_value);
                                    echo "<hr>";
                                */
                                    $id_cliente = $response_value['data']['id'];
                                    
                                    error_log("Nuovo cliente creato in anagrafica con ID => ".$id_cliente);                                    

                                    $entity_result['id'] = $id_cliente;
                    
                                    include plugin_dir_path(__FILE__) . '/invio_fattura.php';
                                    
                                    break;


##################################################################################





###################################################################à                    




                }


            }
            
        }

    

} else { 





###################################################################################
  
//    echo "CREO PRIMO CLIENTE";

################################################################
# Crea il PRIMO cliente se in fattureincloud.it non ce ne sono
################################################################

    $wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/entities/clients";

    $data_pre = array ("data" => array(

        "name" => $wfic_name_tosend,
        "country_iso" => $order_billing_country,
        "first_name" => $order_billing_first_name,
        "last_name" => $order_billing_last_name,
        "vat_number" => $order_billing_partiva,
        "tax_code" => $order_billing_codfis,
        "address_street" => $order_billing_address_1,
        "address_postal_code" => $order_billing_postcode,  
        "address_city" => $order_billing_city,
        "address_province" => $order_billing_state,
        "email" => $order_billing_email,
        "certified_email" => "$order_billing_emailpec",
        "phone" => $order_billing_phone,
        "ei_code" => $order_billing_coddest,
       
        'default_vat' => 
        array (
          'id' => '',
          'value' => '',
          'description' => '',
          'is_disabled' => false,
        ),
    )
    );


    $data_tosend_wfic_postfields = json_encode($data_pre);

    include plugin_dir_path(__FILE__) . '/send_data.php';






    $id_cliente = $response_value['data']['id'];
    
    error_log("creato PRIMO nuovo cliente con ID => ".$id_cliente );

    $entity_result['id'] = $id_cliente;
    $entity_result['name'] = $wfic_name_tosend;






include plugin_dir_path(__FILE__) . '/invio_fattura.php';

}