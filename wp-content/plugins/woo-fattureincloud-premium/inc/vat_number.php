<?php
/*
 * Valori nei campi del checkout
 * 
 */

if ( ! is_admin() ) {

function Billing_Fields_woofc( $fields ) 
{


    global $woocommerce;
    $country = $woocommerce->customer->get_billing_country();
   
    if (($country !== 'IT') || get_option('woofic_soloricevute_chkout') == 1 
        || isset($_POST['woorichiestafattura']) && $_POST['woorichiestafattura'] == 'woorichiestafattura_ricevuta' || 
        
        isset($_POST['woorichiestafattura']) && $_POST['woorichiestafattura'] == 'woorichiestafattura_fattura' ||
        
        isset($_POST['woorichiestafattura']) && $_POST['woorichiestafattura'] == 'woorichiestafattura_fatturae'
        
        ) {
    
        $initaliasi = false;
    
    } else {

        $initaliasi = true;
        
    }

    

    $fields['billing_cod_fisc'] = array(
        'label'       => __('Fiscal Code', 'woo-fattureincloud-premium'),
        'placeholder' => __('Fiscal Code', 'woo-fattureincloud-premium'),
        'required'    => $initaliasi,
        'priority'    => 120,
        'class'       => array('form-row-wide'),
        
    );

    $fields['billing_partita_iva'] = array(
        'label'       => __('Vat Number', 'woo-fattureincloud-premium'),
        'placeholder' => __('Vat Number', 'woo-fattureincloud-premium'),
        'required'    => false,
        'priority' => 130,
        'class'       => array('form-row-wide'),
    );

    $fields['billing_pec_email'] = array(
        'label'       => __('PEC', 'woo-fattureincloud-premium'),
        'placeholder' => __('PEC for Electronic Billing', 'woo-fattureincloud-premium'),
        'required'    => false,
        'type'        => 'text',
        'priority' => 140,
        'class'       => array('form-row-wide'),

    );

    $fields['billing_codice_destinatario'] = array(
    'label'       => __('Recipient Code', 'woo-fattureincloud-premium'),
    'placeholder' => __('RC for Electronic Billing', 'woo-fattureincloud-premium'),
    'required'    => false,
    'type'        => 'text',
    'priority' => 150,
    'class'       => array('form-row-wide'),

    );

    
    return $fields;
}

}

####################################
/*
Reset no first and last name required
*/
#####################################

function woofic_override_default_address_fields( $fields )
{

            $fields['billing_first_name']['required'] = false;
            $fields['billing_last_name']['required'] = false;

            return $fields;
    

}

// Custom checkout fields validation

function woofic_validation_checkout_fields_process() 
{



    if ((empty($_POST['billing_first_name'])) 
        && ($_POST['woorichiestafattura'] == 'woorichiestafattura_ricevuta'
        || $_POST['woorichiestafattura'] == 'woorichiestafattura_fattura_privato'
        || $_POST['woorichiestafattura'] == 'woorichiestafattura_fatturae_privato')
    ) {
        
        wc_add_notice(__("<strong> First name </strong> is a required field", 'woo-fattureincloud-premium'), 'error');

    }

    if ((empty($_POST['billing_last_name'])) 
        && ($_POST['woorichiestafattura'] == 'woorichiestafattura_ricevuta'
        || $_POST['woorichiestafattura'] == 'woorichiestafattura_fattura_privato'
        || $_POST['woorichiestafattura'] == 'woorichiestafattura_fatturae_privato')
    ) {
            
        wc_add_notice(__("<strong> Last name </strong> is a required field", 'woo-fattureincloud-premium'), 'error');

    }

###########

    if (!isset($_POST['woorichiestafattura']) && empty($_POST['billing_last_name'])) {
            
        wc_add_notice(__("<strong> Last name </strong> is a required field", 'woo-fattureincloud-premium'), 'error');

    }

    if (!isset($_POST['woorichiestafattura']) && empty($_POST['billing_first_name'])) {
            
        wc_add_notice(__("<strong> First name </strong> is a required field", 'woo-fattureincloud-premium'), 'error');

    }

###########

    if (isset($_POST['woorichiestafattura'])) { 
        if ((empty($_POST['billing_company']))
            && ($_POST['woorichiestafattura'] == 'woorichiestafattura_fattura' 
            || $_POST['woorichiestafattura'] == 'woorichiestafattura_fatturae')
        ) {
         
            wc_add_notice(__("<strong> Company Name </strong> is a required field", 'woo-fattureincloud-premium'), 'error');

        }

    }

###########

    if (isset($_POST['woorichiestafattura'])) { 
        if ((empty($_POST['billing_partita_iva']))
            && ($_POST['woorichiestafattura'] == 'woorichiestafattura_fattura' 
            || $_POST['woorichiestafattura'] == 'woorichiestafattura_fatturae')
        ) {
        
            wc_add_notice(__("<strong> Vat Number </strong> is a required field", 'woo-fattureincloud-premium'), 'error');

        }

    }

    if (isset($_POST['woorichiestafattura']) && ($_POST['woorichiestafattura'] == 'niente')) { 
        
        
            wc_add_notice(__("Seleziona il <strong>tipo di cliente </strong> è un campo obbligatorio", 'woo-fattureincloud-premium'), 'error');

    }

    
}

#####################################



function billing_fattura_wc_custom_checkout_field() 
{

        $woofic_campi_fattura_normale = array(
            'niente' =>__('Choose customer type', 'woo-fattureincloud-premium'),
            'woorichiestafattura_ricevuta' => __('Private citizen - Receipt', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fattura_privato' => __('Private citizen - Invoice', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fattura' => __('Company - Invoice', 'woo-fattureincloud-premium'),
                    
        );

        $woofic_campi_fatturae = array(
            'niente' =>__('Choose customer type', 'woo-fattureincloud-premium'),
            'woorichiestafattura_ricevuta' => __('Private citizen - Receipt', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fatturae_privato' => __('Private citizen - Electronic invoice', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fatturae' => __('Company - Electronic invoice', 'woo-fattureincloud-premium'),
                    
        );

        $woofic_campi_fattura_fatturae= array(
            'niente' =>__('Choose customer type', 'woo-fattureincloud-premium'),
            'woorichiestafattura_ricevuta' => __('Private citizen - Receipt', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fattura_privato' => __('Private citizen - Invoice', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fatturae_privato' => __('Private citizen - Electronic invoice', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fattura' => __('Company - Invoice', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fatturae' => __('Company - Electronic invoice', 'woo-fattureincloud-premium'),
            
        
        );

        $woofic_campi_fattura_noricev= array(
            'niente' =>__('Choose customer type', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fattura' => __('Company - Invoice', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fattura_privato' => __('Private citizen - Invoice', 'woo-fattureincloud-premium'),
                        
        );

        $woofic_campi_fatturae_noricev= array(
            'niente' =>__('Choose customer type', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fatturae' => __('Company - Electronic invoice', 'woo-fattureincloud-premium'),
            'woorichiestafattura_fatturae_privato' => __('Private citizen - Electronic invoice', 'woo-fattureincloud-premium'),
                                           
        );

        
        $woofic_campi_fattura ="";

        if ('woofic_fattura_normale_checkout' == get_option('fattureincloud_select_field_checkout')) {

            $woofic_campi_fattura = $woofic_campi_fattura_normale;

        } elseif ('woofic_fattura_elettronica_checkout' == get_option('fattureincloud_select_field_checkout')) {

            $woofic_campi_fattura = $woofic_campi_fatturae;

        } elseif ('woofic_fattura_normelettr_checkout' == get_option('fattureincloud_select_field_checkout')) {
            
            $woofic_campi_fattura = $woofic_campi_fattura_fatturae;
            
        } elseif ('woofic_fattura_noricev_checkout' == get_option('fattureincloud_select_field_checkout')) {
            
            $woofic_campi_fattura = $woofic_campi_fattura_noricev;
            
        } elseif ('woofic_fatturae_noricev_checkout' == get_option('fattureincloud_select_field_checkout')) {
            
            $woofic_campi_fattura = $woofic_campi_fatturae_noricev;
            
        } elseif ('nulla' == get_option('fattureincloud_select_field_checkout')) {

            return;
            
        }


        

        echo '<div id="billing_fattura_wc_custom_checkout_field">';
    
        woocommerce_form_field(
            'woorichiestafattura', array(
            'type'      => 'select',
            'class'     => array('form-row-wide'),
            'required'    => true,
            'label'     => __('Customer type choice', 'woo-fattureincloud-premium'),
            'options'     => $woofic_campi_fattura,/* 'default' => 'woorichiestafattura_ricevuta' */
            )
        );
       

        echo '</div>';

        

    
}

function custom_checkout_field_update_order_meta( $order_id ) 
{ 
    if (isset($_POST['woorichiestafattura']) ) { 
        update_post_meta($order_id, 'woorichiestafattura', sanitize_text_field($_POST['woorichiestafattura']));
    }
}


####################################


function display_custom_field_on_order_edit_pages( $order )
{
    $woorichiestafattura = get_post_meta($order->get_id(), 'woorichiestafattura', true);
    if ($woorichiestafattura == 'woorichiestafattura_fattura') { 
        echo '<p><strong>Richiesta Fattura</strong></p>';
    
    } elseif ($woorichiestafattura == 'woorichiestafattura_fatturae') { 
        echo '<p><strong>Richiesta Fattura Elettronica</strong></p>';
    
    } elseif ($woorichiestafattura == 'woorichiestafattura_ricevuta') { 
        echo '<p><strong>Richiesta Ricevuta</strong></p>';
    
    } elseif ($woorichiestafattura == 'woorichiestafattura_fattura_privato') { 
        echo '<p><strong>Richiesta Fattura</strong></p>';
    
    } elseif ($woorichiestafattura == 'woorichiestafattura_fatturae_privato') { 
        echo '<p><strong>Richiesta Fattura Elettronica</strong></p>';
    }


}


#######################################

/*
 * Valori modificabili dell'ordine
 */

########################################


function Admin_Billing_field( $fields ) 
{

    $fields['cod_fisc'] = array(
        'label' => __('Fiscal Code', 'woo-fattureincloud-premium'),
        'wrapper_class' => 'form-field-wide',
        'show' => true,

    );
    $fields['partita_iva'] = array(
        'label' => __('Vat Number', 'woo-fattureincloud-premium'),
        'wrapper_class' => 'form-field-wide',
        'show' => true,

    );
    $fields['pec_email'] = array(
        'label' => __('Email PEC', 'woo-fattureincloud-premium'),
        'wrapper_class' => 'form-field-wide',
        'show' => true,

    );

    $fields['codice_destinatario'] = array(
    'label' => __('Codice Destinatario', 'woo-fattureincloud-premium'),
    'wrapper_class' => 'form-field-wide',
    'show' => true,

    );

    return $fields;
}


#############################################

/* 
add email custom field
*/

function woofic_woocommerce_email_order_meta_fields($order, $sent_to_admin, $plain_text, $email ) 
{

    $output1 = '';
    $output2 = '';
    $output3 = '';
    $output4 = '';


    $billing_cod_fisc = get_post_meta($order->get_id(), '_billing_cod_fisc', true);

    if (!empty($billing_cod_fisc) )
        $output1 = '<div><strong>' . __("Fiscal Code", "woo-fattureincloud-premium") . '</strong> <span class="text"> : ' . $billing_cod_fisc . '</span></div>';

    $billing_piva = get_post_meta($order->get_id(), '_billing_partita_iva', true);

    if (!empty($billing_piva) )
        $output2 = '<div><strong>' . __("Vat Number", "woo-fattureincloud-premium") . '</strong> <span class="text"> : ' . $billing_piva . '</span></div>';

    $billing_pec = get_post_meta($order->get_id(), '_billing_pec_email', true);

    if (!empty($billing_pec) )
        $output3 = '<div><strong>' . __( "PEC", "woo-fattureincloud-premium" ) . '</strong> <span class="text"> : ' . $billing_pec . '</span></div>';

    $billing_cod_dest = get_post_meta($order->get_id(), '_billing_codice_destinatario', true);

    if (!empty($billing_cod_dest) )
        $output4 = '<div><strong>' . __("Codice Destinatario", "woo-fattureincloud-premium") . '</strong> <span class="text"> : ' . $billing_cod_dest . '</span></div>';


    echo $output1;
    echo $output2;
    echo $output3;
    echo $output4;

}

##################################################
/*
add thank you page field after order
*/

function woo_fic_displayfield_typ($order)
{ 
   
    $billing_cod_fisc = get_post_meta($order->get_id(), '_billing_cod_fisc', true);

    if (!empty($billing_cod_fisc) ) { 
        echo '<div><strong>' . __("Fiscal Code", "woo-fattureincloud-premium") . '</strong> <span class="text"> :' . $billing_cod_fisc . '</span></div>';
    }

    $billing_piva = get_post_meta($order->get_id(), '_billing_partita_iva', true);

    if (!empty($billing_piva) )
        echo '<div><strong>' . __("Vat Number", "woo-fattureincloud-premium") . '</strong> <span class="text"> : ' . $billing_piva . '</span></div>';

    $billing_pec = get_post_meta($order->get_id(), '_billing_pec_email', true);

    if (!empty($billing_pec) )
        echo '<div><strong>' . __( "PEC", "woo-fattureincloud-premium" ) . '</strong> <span class="text"> : ' . $billing_pec . '</span></div>';

    $billing_cod_dest = get_post_meta($order->get_id(), '_billing_codice_destinatario', true);

    if (!empty($billing_cod_dest) )
        echo '<div><strong>' . __("Codice Destinatario", "woo-fattureincloud-premium") . '</strong> <span class="text"> : ' . $billing_cod_dest . '</span></div>';


}