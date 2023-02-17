<?php

// Don't access this directly, please
if (!defined('ABSPATH')) exit;

if (get_option('wfic_id_azienda') == null ) {

    header("Location: admin.php?page=woo-fattureincloud-premium&tab=impostazioni");


}

?>




<table border="0" style="max-width:800px;" cellpadding="6">

        <tr>
            <td>
            <?php echo __('<i> è possibile che le Fatture con i sezionali attivati non appaiano in questa pagina, controllare direttamente su fattureincloud.it</i>', 'woo-fattureincloud-premium'); ?>
            </td>
        </tr>
</table>
    <div id="fatture-elenco">

<?php

##############################################################à

$url = "https://api-v2.fattureincloud.it/user/companies";

include plugin_dir_path(__FILE__) . '/retrive_data.php';

$json = json_decode($result, true);
if (is_array($json)) {

    if (!empty($json['error'])) { 

        include plugin_dir_path(__FILE__) . '/connetti.php';

        echo "aspettare il caricamento completo della pagina per visualizzare le fatture";
        
        ?>
            <script>
                       
            location.reload();
        
            </script>

  <?php

    } else { 
        
        $company_id = get_option('wfic_id_azienda');
        $token_key = get_option('wfic_api_key_fattureincloud');
        //$annofatture = get_option('woo-fattureincloud-anno-fatture');

        if ( $company_id == null || $token_key  == null ) {

            $type = 'warning';
            $message = __('Verificare che la Connessione e Id Azienda siano stati impostati per poter visualizzare l\'elenco fatture', 'woo-fattureincloud-premium');
            add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
            settings_errors('woo-fattureincloud-premium');

        return;

        }






$url = "https://api-v2.fattureincloud.it/c/".$company_id."/issued_documents?filter_type=and&per_page=5&sort=-created_at&type=invoice";



include plugin_dir_path(__FILE__) . '/retrive_data.php';

$json = json_decode($result, true);


//echo "<pre>";
//print_r($json);
//echo "</pre>";


if (is_array($json)) {

    if (!empty($json['error'])) {


        $azienda_error_message = $json['error']['message'];

        $type = 'warning';
        $message = __($azienda_error_message, 'woo-fattureincloud-premium');
        add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
        settings_errors('woo-fattureincloud-premium');

        return;

    } else {

foreach ($json as $value) {

    if (is_array($value)) {

        $count = 0;

            foreach ($value as $value2) {

                $count = $count + 1;

                //echo $value2[$i];

                //echo "<pre>";
                //print_r($value2);
                //echo "</pre>"; 


                $idfattura = $value2['id'];
                $pdf_fattura = $value2 ['url'];
                $nome_cliente = ($value2['entity'])['name'];
                $importo_lordo = $value2 ['amount_gross'];
                $oggetto_documento = $value2 ['visible_subject'];
                
                if (!empty($value2['entity']['id'])) {

                    $id_cliente = $value2['entity']['id']; 
                    
                
                $url ="https://api-v2.fattureincloud.it/c/".$company_id."/entities/clients/". $id_cliente;

                include plugin_dir_path(__FILE__) . '/retrive_data.php';

                $json_email_client_id = json_decode($result, true);

               // echo "<pre>";
               // print_r($json_email_client_id);
               // echo "</pre>";


                
                }
                
                ##############################################################################


                $url ="https://api-v2.fattureincloud.it/c/".$company_id."/issued_documents/".$idfattura."/email";

                //echo $url;

                include plugin_dir_path(__FILE__) . '/retrive_data.php';
                
                $json_email = json_decode($result, true);

               // echo "<pre>";
                //print_r($json_email);
                //echo "</pre>";


                
                
                //print_r($result_dett['mail_destinatario']);

                //print $pdf_fattura;


                ###############################################################################


                if (empty($value2['entity']['id'])) {

                    
                    $oggetto_email = $value2['visible_subject'];
                    $order_numb = (int) filter_var($oggetto_email, FILTER_SANITIZE_NUMBER_INT);
                    $id_cliente = $order_numb;

                    
                    echo "<p><b>Fatture dell'ordine WooCommerce #".$id_cliente." non recuperabile a causa delle modifiche introdotte dalle API2.0,</b></p>"; 
    

                    $email_destinatario = "";

                    return;
                
                } else {

                    $email_destinatario = ($json_email_client_id['data'])['email'];

                }


                

                $email_body = ($json_email['data'])['body'];

                $email_mittente = ($json_email['data']['sender_emails_list'][0]['email']);

                //echo $email_body;

                if (!empty($email_destinatario)) {

                        echo "<form id=\"send_email_fattureincloud$idfattura\" method=\"POST\">";

                        //$email_destinatario = $json_email['cc_email'];
                    
                    /*
                    if (empty($result_dett['mail_destinatario'])) {

                             $email_destinatario = $result_dett['mail_cc'];

                    }
                    */


                    print "<a href=\"https://secure.fattureincloud.it/invoices-view-".$idfattura."\">Visualizza fattura su Fattureincloud</a><br>";
                    print "<a href=\"$pdf_fattura\">Visualizza PDF fattura</a><br>";
                    print $oggetto_documento."<br>";
                    print "<b>".($json_email['data'])['subject']."</b><br>";
                    print " Destinatario: ".$nome_cliente."<br>";
                    print " <b>importo iva inclusa</b> €".$importo_lordo." <br> ";
                    print "<b>email</b> ".$email_destinatario."<br>";


                    $oggetto_email = ($json_email['data'])['subject'];

                    $nome_cliente_fic = $nome_cliente;

                    $oggetto_ordine_fic = $oggetto_email;

                        echo "<input type=\"hidden\" value=\"$email_destinatario\" name=\"email_destinatario\" />";

                        echo "<button type=\"submit\" name=\"$idfattura\" value=\"$idfattura\" class=\"button button-primary\" >Invia Fattura via Email</button><hr>";

                    if (isset($_POST[$idfattura])) {

                        include plugin_dir_path(__FILE__) . '/send_email_fattureincloud.php';

                    }

                    echo "</form><br>";
                }


                if ($count == 5) {

                    print "numero massimo ( 5 ) di fatture visualizzabili raggiunto";
                    break;

                } else {

                }

            }

        }

    }

}

echo "</div>";

}

}

}