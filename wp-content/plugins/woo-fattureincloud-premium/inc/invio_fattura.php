<?php

if (1 == get_option('fattureincloud_paid')) {

    $status_paid = "paid";

} else {

    $status_paid ="not_paid";

}

error_log("rivalsa nella pagina invio =>".$rivalsa);

#########################################################################
# Crea la fattura
###########################################################################

$data_pre = array ("data" => array(
   

####################################################################    


"entity" => $entity_result,
    
    "type" => $doc_type_wfic,    
//    "numeration" => $sezionale_woofatture,    
    "language" => array (

        "code" => "it",
        //"name" => "italiano"

    ),
    
    "visible_subject" => "Ordine numero ".$id_ordine_scelto,
    "numeration" => $sezionale_woofatture,
    "notes" => $order_note,
    "rivalsa" => $rivalsa,
    "cassa" => $cassa_wfic,
    "stamp_duty" => 0,
    "use_gross_prices" => false,
    "e_invoice" => $invoice_elet_type_wfic,
    //"amount_net" => $order_data['total'] - $order_data['total_tax'] ,
    "amount_vat" => $order_data['total_tax'],
    "amount_gross"=> $order_data['total'],
   
    "date" => $data_scadenza,
    "items_list" => $lista_articoli_api2,

    "payments_list" => array ( 
        array (
        "amount" => $order_data['total'] ,
        "due_date" => $data_scadenza,
        "status" => $status_paid,
        "payment_account" => $payment_list_woo_fic_id,

       "paid_date" => $data_scadenza,

        ),
        
    ),

    "show_payments" => true, 
    "show_payment_method" => true, 
    "show_totals" => "all",

    "ei_data" => array (


        "payment_method" => $payment_method_fic_ei_code

    ),

      
   
#######################################################################
    
    "payment_method" =>  array (
        
        "id" => $payment_method_woo_fic_order

    ),
  
    //"amount_rivalsa_taxable" => 0,



   

)
);

/*
echo "<pre>";
print_r($data_pre);
echo "</pre>";
*/

error_log("data inviati =>");

error_log(print_r($data_pre, true));


$data_pre_test = '{
    "data": {
      "entity": {
        "name": "Rossi S.r.l.",
        "type": null,
        "vat_number": "01234567890",
        "tax_code": "01234567890",
        "address_street": "Via dei tigli, 12",
        "address_postal_code": "24010",
        "address_city": "Bergamo",
        "address_province": "BG",
        "country_iso": "IT",
        "email": "mario@rossi.example.it",
        "certified_email": "mario@pec.example.it",
        "phone": "3451234567",
        "ei_code": "M5UXCR1"
      },
      "type": "invoice",
      "numeration": "",
      "date": "2022-07-29",
      "visible_subject": "oggetto visibile",
      "notes": "note documento",
      "rivalsa": 0,
      "cassa": 0,
      "stamp_duty": 0,
      "payment_method": {
        "id": 97603,
        "name": "Paypal",
        "details": [
          {
            "title": "IBAN",
            "description": "IT1242342342340230003204023422"
          },
          {
            "title": "Beneficiario",
            "description": "Mario Rossi S.r.l."
          }
        ]
      },
      "use_gross_prices": false,
      "e_invoice": true, 
      "ei_data": {
        "payment_method": "MP01"
      },
      "items_list": [
        {
          "name": "Water bottle",
          "qty": 1,
          "net_price": 1,
          "gross_price": 1.22,
          "vat": {
            "id": 0
          }
        }
      ],
      "payments_list": [
        {
          "due_date": "2021-07-29",
          "amount": 1.22,
          "status": "paid",
          "payment_account": {
            "id": 97603
          },
          "paid_date": "2021-07-29"
        }
      ],
      "show_payments": true,
      "show_payment_method": true
    }
  }';


############################## FINE PRE ########################################;


$wfic_datatosend_url = "https://api-v2.fattureincloud.it/c/".$company_ID."/issued_documents";

$data_tosend_wfic_postfields = json_encode($data_pre);

//$data_tosend_wfic_postfields  = $data_pre_test ;

#######################################################################################

include plugin_dir_path(__FILE__) . '/send_data.php';

#######################################################################################

