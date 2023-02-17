<form id="woo-fattureincloud-preview" action="" method="POST">

<?php

if (!defined('ABSPATH')) exit;

if (get_option('wfic_id_azienda') == null ) {

    header("Location: admin.php?page=woo-fattureincloud-premium&tab=impostazioni");


}

/*
 * Security form
 */

 wp_nonce_field();

 ####################################################################
 
 if ( is_admin() ) {


/*
if (get_option('wfic_id_azienda') == null ) {


    $type = 'warning';
    $message = __('Azienda ID mancante, verificare di aver selezionato l\'azienda e salvato le  <a href="admin.php?page=woo-fattureincloud&tab=impostazioni"> impostazioni</a>', 'woo-fattureincloud');
    add_settings_error('woo-fattureincloud', esc_attr('settings_updated'), $message, $type);
    settings_errors('woo-fattureincloud');
}
*/


    $url = "https://api-v2.fattureincloud.it/user/companies";

    include plugin_dir_path(__FILE__) . '/retrive_data.php';
    
    $json = json_decode($result, true);
    if (is_array($json)) {
    
        if (!empty($json['error'])) { 
    
            include plugin_dir_path(__FILE__) . '/connetti.php';
    
            ?>
       <!--         <script>                          
                   location.reload();
                       
                </script>
    -->
      <?php

return;
    
        } else { 




?>

<table style="max-width:800px">

    <tr>
<?php

/*
 *
 * Select last order ID
 *
 */

function Get_Last_Order_id()
{
    $query = new WC_Order_Query(
        array(
        'limit' => 1,
        'orderby' => 'date',
        'order' => 'DESC',
        'return' => 'ids',
        )
    );
    $orders = $query->get_orders();

    if (!$orders ) {

        echo "<p>nessun ordine presente</p>";

        return;

    }

    return ($orders[0]);

}




    $latest_order_id = Get_Last_Order_id(); // Last order ID


if ($latest_order_id =='') {

?>

    <div id="message" class="notice notice-error is-dismissible">

    <p><b>Non sono presenti Ordini WooCommerce!</b></p>

    </div>

 <?php

    return;
}


    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => 10,
        'post_status' => array_keys(wc_get_order_statuses())
        );

?>


    <td align="center" class="woo_fic_select_order">

   <!-- <div class="woo_fic_select_order"> -->
   
    Ordine

   

    <select name="woo_fattureincloud_order_id">

<?php

    $orders = get_posts($args);

if (get_option('woo_fattureincloud_order_id') == null) {

?>

<option value="<?php echo $latest_order_id; ?>" selected="selected">Selezionato:

<?php echo $latest_order_id; ?></option>

<?php } else { ?>

    <option value="" selected="selected">
    Selezionato: <?php echo get_option('woo_fattureincloud_order_id'); ?></option>

<?php }

foreach ($orders as $order) {

?>

<option value="<?php echo $order->ID; ?>">ID ordine : <?php echo $order->ID; ?></option>

<?php

}

?>
    </select>
    
   

</form>



<select name="woo_fattureincloud_search_order_id" id="woo_fattureincloud_orders">

<?php

if (!empty($_POST['search_order'])) {

?>

    <option value="<?php echo $_POST['search_order']; ?>" selected="selected">#ordine <?php echo $_POST['search_order']; ?> ></option>

<?php

}

?>
    <option value="">Cerca Ordine</option>
    </select>

<script type="text/javascript">
    jQuery( function() {

        if( typeof ajaxurl == 'undefined' ){
            ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        }
        jQuery.fn.select2.defaults.set('language', 'it');
        jQuery("#woo_fattureincloud_orders").select2({
            language: "it",
            placeholder: "Cerca Ordine",
            // data: [{ id:0, text:"something"}, { id:1, text:"something else"}],
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        action: 'woo_fattureincloud_search'
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data,
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });
    });
</script>


<button type="submit" name="submit" value="" class="button button-secondary">Seleziona</button>

<!-- </div> -->

</td>



</tr>
</table>

<!-- ################################################################################
FINE TABELLA RICERCA ORDINI INIZIO TABELLA DATI
#####################################################################################
 -->

<table style="max-width:800px" class="form-table">
<tr>

<td  bgcolor="FFFFFF" align="right">

<?php

}

###############################################################################


if (get_option('woo_fattureincloud_order_id') == null) {

    $id_ordine_scelto = $latest_order_id;

} else {

    $id_ordine_scelto = get_option('woo_fattureincloud_order_id');

}


//####################################################################################################################


    $order = wc_get_order($id_ordine_scelto);

    $order_data = $order->get_data(); // The Order data
    $order_note = $order->get_customer_note();
    $order_shipping_total = $order_data['shipping_total'];
    $order_shipping_tax = $order_data['shipping_tax'];
    $order_total = $order_data['total'];
    $order_total_tax = $order_data['total_tax'];
    $fattureincloud_iva = 22;
    $ivaDivisore = 1 + ($fattureincloud_iva / 100);
    $order_total_partial = $order_total / $ivaDivisore;
    $order_total_partial = round($order_total_partial, 2);
    $totale_iva_fattureincloud = $order_total - $order_total_partial;
    $totale_esclusaiva = $order_total  - $order_total_tax;

/* BILLING INFORMATION: */

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

//print_r($order_data);


//#################################################################################################################    

//#######################################################################################################################
/*   compatibilità col plugin woo-piva-codice-fiscale-e-fattura-pdf-per-italia  */
//#######################################################################################################################



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


//########################################################################################################################    
    
} elseif (get_post_meta($id_ordine_scelto, '_billing_partita_iva', true) || get_post_meta($id_ordine_scelto, '_billing_cod_fisc', true)
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


if ( is_admin() ) {


    echo "<b>Destinatario</b> 
                <br><b>".__('Name', 'woo-fattureincloud-premium')."</b> ".$order_billing_first_name." ".$order_billing_last_name.
            "<br><b>".__('Company', 'woo-fattureincloud-premium')."</b> ".$order_billing_company.
            "<br><b>".__('Address', 'woo-fattureincloud-premium')."</b> ".$order_billing_address_1.
            "<br><b>".__('City', 'woo-fattureincloud-premium')."</b> ".$order_billing_city.
            "<br><b>".__('State', 'woo-fattureincloud-premium')."</b> ".$order_billing_state.
            "<br><b>".__('Postal Code', 'woo-fattureincloud-premium')."</b> ".$order_billing_postcode.
            "<br><b>".__('Email', 'woo-fattureincloud-premium')."</b> ".$order_billing_email.
            "<br><b>".__('Email PEC', 'woo-fattureincloud-premium')."</b> ".$order_billing_emailpec.
            "<br><b>".__('Recipient Code Number', 'woo-fattureincloud-premium')."</b> ".$order_billing_coddest.
            "<br><b>".__('Phone number', 'woo-fattureincloud-premium')."</b> ".$order_billing_phone.
            "<br><b>".__('Country', 'woo-fattureincloud-premium')."</b> ".$order_billing_country.
            "<br><b>".__('Vat Number Code', 'woo-fattureincloud-premium')."</b> ".$order_billing_partiva.
            "<br><b>".__('Fiscal Code Number', 'woo-fattureincloud-premium')."</b> ".$order_billing_codfis.
            "<br><b>".__('Billing Method', 'woo-fattureincloud-premium')."</b> ".$order_billing_method.
            "<br><b>".__('Payment Method code', 'woo-fattureincloud-premium')."</b> ".$order_billing_payment_method.
            "<br><b>".__('Billing Note', 'woo-fattureincloud-premium')."</b> ".$order_note.

         "

            </td></tr>

            <tr>
            <td bgcolor=\"FFFFFF\">
                          
             <b>Elenco Prodotti </b><hr>";

   
foreach($order->get_items('tax') as $item_id => $item ) {

    $tax_rate_id    = $item->get_rate_id(); // Tax rate ID
    $tax_percent    = WC_Tax::get_rate_percent( $tax_rate_id ); // Tax percentage
    $tax_rate       = str_replace('%', '', $tax_percent); // Tax rate

    $array_tax_percent_value[]= $tax_rate;

    } 
    //print "<pre>";
    //$max_tax_percent_value = max($array_tax_percent_value);
    //print "</pre>";
}

/* Iterating through each WC_Order_Item_Product objects */

foreach ($order->get_items() as $item_key => $item_values) {

    /* Using WC_Order_Item methods */

    /* Item ID is directly accessible from the $item_key in the foreach loop or */

        $item_id = $item_values->get_id();

    /* Using WC_Order_Item_Product methods */

        $item_name = $item_values->get_name(); // Name of the product
        $item_type = $item_values->get_type(); // Type of the order item ("line_item")

        $product_id = $item_values->get_product_id(); // the Product id
        //$product_variation_id = $item_values->get_variation_id();
        $wc_product = $item_values->get_product(); // the WC_Product object
        $sku = $wc_product->get_sku();
        $short_description_prdct = $wc_product->get_short_description();
        $long_description_prdct = $wc_product->get_description();

    /* Access Order Items data properties (in an array of values) */

        $item_data = $item_values->get_data();

        
        if ($item_data['variation_id'] > 0) { 
    
            $product_id = $item_values->get_variation_id(); // the Product id
    
        } else {
    
            $product_id = $item_values->get_product_id(); // the Variable Product id
        }

        $_product = wc_get_product($product_id);
        //$_product_variable = wc_get_product($product_variation_id);

        $product_name = $item_data['name'];
        $product_id = $item_data['product_id'];
        $variation_id = $item_data['variation_id'];
        $quantity = $item_data['quantity'];
        $tax_class = $item_data['tax_class'];
        $line_subtotal = $item_data['subtotal'];
        $line_subtotal_tax = $item_data['subtotal_tax'];
        $line_total = $item_data['total'];
        $line_total_tax = $item_data['total_tax'];
/*      
        $prezzo_singolo_prodotto = $line_total/$quantity;
        $prezzo_singolo_prodotto = $prezzo_singolo_prodotto/ $ivaDivisore;
        $prezzo_singolo_prodotto = round($prezzo_singolo_prodotto, 2);
*/
        $item_tax_class = $item_data['tax_class'];
        $order_country_rate_code = $item_values->get_taxes();
        $order_vat_country =  $item_data['taxes']['total'];
        //$order_vat_country = array_key_first($order_vat_country_pre);



            $get_product_cats_fic = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'all'));
        
 /*      
        print "<pre>";
        print_r($item_data);
        print "</pre>";
        print "<hr>";
*/
        
       

        /*$tax_rate = array(); */   
        
        
            $tax_rates = WC_Tax::get_base_tax_rates($_product->get_tax_class(true));

            $tax_rates_class_list = WC_Tax::get_rates_for_tax_class($_product->get_tax_class(true));
        
        

        $mostra_percentuale_tasse = WC_Tax::get_rate_percent(key($order_vat_country));

        $mostra_percentuale_tasse_num     = str_replace('%', '', $mostra_percentuale_tasse); // Tax rate
        

       // print_r ($tax_rates);

###############################################################

if ( is_admin() ) {

//    $_product = reset($_product);

    echo "<b>".__('Nome Prodotto', 'woo-fattureincloud-premium')."</b> ". $product_name."<br>"
    
    .//#######################################################################. 
    
    "<b>".__('Categoria', 'woo-fattureincloud-premium')."</b> ";
    //print_r($get_product_cats_fic);

    foreach ($get_product_cats_fic as $term_single) {

        echo $term_single->name." ";

        $singoli_termini = explode(" ", $term_single->name);

        if ($singoli_termini[0] == 'sezionale') {

            if (is_numeric($singoli_termini[1])) {

                $sezionale_fic_dacategoria = "/".$singoli_termini[1];
            
            } else {

                $sezionale_fic_dacategoria = $singoli_termini[1];
            }

            echo "<b>Sezionale ".$sezionale_fic_dacategoria."</b>";
      
        }

        

    }
    echo "<br>";
    
    //#############################################################################

    if (!empty($tax_rates)) {
        $tax_rate = reset($tax_rates);

            print __('<b>Aliquota Iva</b> ', 'woo-fattureincloud-premium');
        
            
            //$aliquote_presenti_fic = array('0%', '4%', '5%', '8%','10%','20%','21%', '22%', '23%','24%');
            print $mostra_percentuale_tasse_num ."% " ; /*print_r($item_data['taxes']['total']);*/
            
            /*
            if (!in_array($mostra_percentuale_tasse, $aliquote_presenti_fic)) {
                echo '<span style="color:#FF0000";> (ALIQUOTA al momento NON RECEPIBILE IN Fattureincloud.it) </span>';
            }
            
            */
            print "<br>";
            //if (round($tax_rate['rate'], 0) == 0) {
            if ($mostra_percentuale_tasse_num == 0) {

                echo "<b>".__('Tipo aliquota 0% ', 'woo-fattureincloud-premium')."</b> " .$tax_class. "<br>";

            }
     //  }

        

    } elseif (empty($tax_rates)) {
        $tax_rate = 0;
        echo __('aliquota Iva ', 'woo-fattureincloud-premium').$tax_rate."%<br> 
        <b>".__('Tipo aliquota 0% per l\'Europa', 'woo-fattureincloud-premium')."</b> " .$tax_class. "<br>";
    
    }
    
 

    if (!$sku) { 
        $sku = "#" ; 
    }

        echo"<b>".__('SKU', 'woo-fattureincloud-premium')."</b> ". $sku. "<br>" ; 

    $product = wc_get_product($product_id);

    //print_r($item_data);

    //echo "<b>".__('Prezzo', 'woo-fattureincloud-premium')."</b> ".$product->get_price_html()."<br>

    echo "<b>".__('Prezzo', 'woo-fattureincloud-premium')."</b> ".wc_price($order->get_item_total( $item_values, false, true ))."<br>

                    <b>".__('Breve Descrizione ', 'woo-fattureincloud-premium')."</b>";
                    
                    if (1 == get_option('show_short_descr') ) {                    
                    
                        echo $short_description_prdct;

                    } else {

                        echo __(' Disabilitata', 'woo-fattureincloud-premium');

                    }
                     
    echo "<br>                   
                    <b>".__('Descrizione', 'woo-fattureincloud-premium')."</b> ";
                    
                    if (1 == get_option('show_long_descr') ) {
                    
                    echo $long_description_prdct;
                    
                    } else {

                        echo __(' Disabilitata', 'woo-fattureincloud-premium');

                    }
                    
    echo "<br>
					
					<b>".__('Quantità', 'woo-fattureincloud-premium')."</b> ".$quantity."<br>".

                    "<b>".__('Sub Totale', 'woo-fattureincloud-premium')."</b> €".round($line_total, 2).
        "<hr>";

}

}

##################################################################

/*
 * TAX Shipping
 *
 *
 */


    /* Initializing variables */
    $tax_items_labels   = array(); // The tax labels by $rate Ids
    $shipping_tax_label = '';      // The shipping tax label
    $shipping_tax_rate = '';

    /* 1. Loop through order tax items */

foreach ( $order->get_items('tax') as $tax_item ) {
    /* Set the tax labels by rate ID in an array */

    $tax_items_labels[$tax_item->get_rate_id()] = $tax_item->get_label();


    /* Get the tax label used for shipping (if needed) */
    if (!empty($tax_item->get_shipping_tax_total()) ) {
        
        $shipping_tax_label = $tax_item->get_label();

        $shipping_tax_rate = $tax_item->get_rate_percent();
        

    }
    //print_r($tax_item);

}

    /* 2. Loop through order line items and get the tax label */

foreach ( $order->get_items() as $item_id => $item ) {

    $taxes = $item->get_taxes();

    /* Loop through taxes array to get the right label */

    foreach ($taxes['subtotal'] as $rate_id => $tax) {

        $tax_label = $tax_items_labels[$rate_id]; /* <== Here the line item tax label */

        /* Test output line items tax label */
        /* echo '<pre>Item Id: '.$item_id.' | '; print_r($tax_label); echo '</pre>';*/
    }
}

/* Test output shipping tax label */
/*	echo '<pre>Shipping tax label: '; print_r($shipping_tax_label); echo '</pre>'; */

/* Marca da bollo*/

if ((1 == get_option('woofic_marca_bollo_elettronica')) 
    && ('fatturaelettronica' == get_option('fattureincloud_send_choice'))
) {
    include plugin_dir_path(__FILE__) . 'marca_bollo.php';
    
} 

#####################################################################

if ( is_admin() ) {

echo    "</td>
                </tr>
                <tr>
                    <td  align='right' bgcolor='FFFFFF'>

                    <br><br><b>".__('Numero Ordine', 'woo-fattureincloud-premium')."</b> ". $id_ordine_scelto.
                    "<br><b>".__('Data ordine ', 'woo-fattureincloud-premium')."</b>".$order_data['date_created']->date("Y-m-d").
                    "<br><b>".__('Costo Spedizione', 'woo-fattureincloud-premium')."</b> ". $order_shipping_total.
                    "<br><b>".__('Tasse Spedizione', 'woo-fattureincloud-premium')."</b> ". $shipping_tax_rate. "% = ".$order_shipping_tax ;
                   
                    foreach( $order->get_items('fee') as $item_id => $item_fee ){

                        // The fee name
                        $fee_name = $item_fee->get_name();
                    
                        // The fee total amount
                        $fee_total = $item_fee->get_total();
    
                        // The fee total tax amount
                        $fee_total_tax = $item_fee->get_total_tax();
    
                        $fee_total_ficl = round($fee_total_tax + $fee_total, 2, PHP_ROUND_HALF_UP);
    
                        if ($fee_total > 0) {
    
                            echo "<br><br><b>"
                            .__('Commissioni di pagamento ', 'woo-fattureincloud-premium')."</b> &nbsp; ".$fee_name. " =
                            ".$fee_total_ficl;
                            if ($fee_total_tax> 0 ) { echo " (Tax incl. = ".$fee_total_tax.")"; }
                        }
                            
                    }    

####################################################




####################################################


echo                "<br><br><b>".__('Totale iva esclusa', 'woo-fattureincloud-premium')."</b> ".  $totale_esclusaiva;

//$woofic_marca_bollo = 2;
/*
if (1 == get_option('woofic_marca_bollo_elettronica') && 'fatturaelettronica' == get_option('fattureincloud_send_choice')) {

    echo "<br><b>".__('Marca da bollo Virtuale', 'woo-fattureincloud-premium')."</b> ". $woofic_marca_bollo;

} 
*/


    echo "<br><b>".__('Totale', 'woo-fattureincloud-premium')."</b> ". $order_total; //}


?>
    </td>
</tr>
<tr>
    <td  align='right'>

<?php

if (get_option('woocommerce_prices_include_tax') == 'no') {

    if ('fatturaelettronica' == get_option('fattureincloud_send_choice') ) {


        echo "                 

        <form method=\"POST\">";

     //   if (0 == get_option('fattureincloud_paid') ) {  
                echo "<p>
						  <label for=\"woo-datepicker\">"
                          . __('Data Fattura Elettronica ', 'woo-fattureincloud-premium').
                          "</label>
				          <input type=\"text\" id=\"woo-datepicker\" class=\"woo-datepicker\" 
				          name=\"woo-datepicker\" value=\"woo-datepicker\" size=\"10\">
					  </p>";
     //   }

        echo "

			    <button type=\"submit\" name=\"submit_send_fe_fattureincloud\"  
				value=\"Seleziona\" class=\"button button-primary\">
				Crea la Fattura Elettronica su Fattureincloud
                </button>
                </form>";


    } elseif ('fattura' == get_option('fattureincloud_send_choice') ) {

            echo "                 

			<form method=\"POST\">";

    //    if (0 == get_option('fattureincloud_paid') ) {  

            echo "<p>
						  <label for=\"woo-datepicker\">"
                          . __('Data Fattura', 'woo-fattureincloud-premium').
                          "</label>
				          <input type=\"text\" id=\"woo-datepicker\" class=\"woo-datepicker\" 
				          name=\"woo-datepicker\" value=\"woo-datepicker\" size=\"10\">
					  </p>";
    //    }
            echo "

			    <button type=\"submit\" name=\"submit_send_fattureincloud\"  
				value=\"Seleziona\" class=\"button button-primary\">
				Crea la Fattura su Fattureincloud
                </button>
                </form>";

    } elseif ('ricevuta' == get_option('fattureincloud_send_choice') ) {

            echo "    
                
			<form method=\"POST\">";

            echo "<p>
			        	  <label for=\"woo-datepicker\">"
                          . __('Data', 'woo-fattureincloud-premium').
                          "</label>
						  <input type=\"text\" id=\"woo-datepicker\" class=\"woo-datepicker\" 
						  name=\"woo-datepicker\" value=\"woo-datepicker\" size=\"10\" 
						  >
						  </p>";

            echo "<button type=\"submit\" name=\"submit_ricevuta_fattureincloud\" 
			value=\"Seleziona\" class=\"button button-primary\">
				Crea la Ricevuta su Fattureincloud</button>
            </form>";

    }


} elseif (get_option('woocommerce_prices_include_tax') == 'yes') {

?>

<button type="submit" name="submit_send_fattureincloud" value="Seleziona" class="button button-primary" disabled>Crea la Fattura su Fattureincloud</button>

        <div id="message" class="notice notice-error">
            <p><b>Per utilizzare questo plugin è necessario impostare i 
                <a href="admin.php?page=wc-settings&tab=tax">prezzi al netto dell'imposta</a> |
                <a href="https://woofatture.com/documentazione/">Maggiori informazioni</a>
            </b> </p>
        </div>

<!--
        <div id="message2" class="notice notice-error">
            <p><b>Per utilizzare questo plugin è necessario impostare i
                <a href="admin.php?page=wc-settings&tab=tax">prezzi al netto dell'imposta</a> |
                <a href="https://woofatture.com/documentazione/">Maggiori informazioni</a>
            </b></p>
        </div>

        -->


<?php

}

}

####################################################################################


if (isset($_POST['submit_send_fattureincloud'])) {

    $order_id = $id_ordine_scelto;

    $doc_type_wfic = "invoice";

    $invoice_elet_type_wfic = false;

    $fatturaelettronica_fic = "false";

    $data_documento = date("Y-m-d");

    include plugin_dir_path(__FILE__) . '/prepare_to_send.php';

} elseif (isset($_POST['submit_send_fe_fattureincloud'])) {
    
    $doc_type_wfic = "invoice";
    
    $fatturaelettronica_fic = "true";

    $invoice_elet_type_wfic = true;

 

    $data_documento = date("Y-m-d");
    
    include plugin_dir_path(__FILE__) . '/prepare_to_send.php';


} elseif (isset($_POST['submit_ricevuta_fattureincloud'])) {

    $doc_type_wfic ="receipt";
    $fattureincloud_invoice_paid = $order_data['payment_method'];
    $mostra_info_pagamento = true;
    $data_saldo = $order_data['date_created']->date("Y-m-d");
    if (isset($_POST['woo-datepicker'])) { 

        $data_documento = $_POST['woo-datepicker'];
        
        
    } else {
    
        $data_documento = date("Y-m-d");
    
    }

    $invoice_elet_type_wfic = false;

 

    include plugin_dir_path(__FILE__) . '/prepare_to_send.php';

}

}

}

?>
</td>
</tr>
</table>