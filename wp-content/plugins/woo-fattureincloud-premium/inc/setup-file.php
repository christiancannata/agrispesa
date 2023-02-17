<?php

/* Don't access this directly, please */

if (!defined('ABSPATH') ) {

    exit;

}

###################################

if (!wc_tax_enabled()) {
    ?>

    <div id="message" class="notice notice-error is-dismissible">
        <p><b>Aliquote in <a href="admin.php?page=wc-settings">WooCommerce</a> non attivate</b></p>
    </div>

    <?php
}

###################################

/*
 *
 * Controllo API KEY e mostra messaggio se mancano
 *
 *
 

if (get_option('api_uid_fattureincloud') == null || get_option('api_key_fattureincloud') == null ) {

    if ( is_admin() ) {

?>


    <div id="message" class="notice notice-error is-dismissible">
        <p><a href="admin.php?page=woo-fattureincloud-premium&tab=impostazioni">Clicca qui</a>
        per Verificare che le API KEY siano state inserite</p>
    </div>
<?php
    }

}


*
* Se importo ordine uguale a zero
* non creare il documento su fattureincloud.it
*
*/ 

###############################################################

if (get_option('fattureincloud_autosent_id_importozero')!='') {

    if ( is_admin() ) {

    ?>
    <div id="message" class="notice notice-success">
        <p>Creazione automatica documento non avvenuta come da <a href="admin.php?page=woo-fattureincloud-premium&tab=impostazioni">impostazioni</a><br> perché 
        <b>importo ordine n <?php echo get_option('fattureincloud_autosent_id_importozero'); ?> uguale a zero</b></p>
        
        <form method="POST">
                <input type="hidden" name="delete_autosave_fattureincloud" />
                <input type="submit" value="Cancella" class="button button-small ">
        </form>

    </div>

    <?php
    }
}

###############################################################

/*
 *
 * Controllo mancato invio automatico Fattura al cambio 
 * di stato Completato e mostra messaggio di errore
 *
 */


if ( is_admin() ) {


if (get_option('fattureincloud_autosent_id_fallito')!='') {

error_log("passaggio da setup-file");        
        ?>
        <div id="message" class="notice notice-error">
        
                <p><b>Invio automatico ordine n <?php echo get_option('fattureincloud_autosent_id_fallito'); ?> non Riuscito</b><br>
        
                <form method="POST">
                    <input type="hidden" name="delete_autosave_fattureincloud" />
                    <input type="submit" value="Cancella" class="button button-small ">
                </form>
        </div>

        <?php
        
        /*
        if (0 == get_option('fattureincloud_paid') ) {
            
            ?>
                   
                <p>Controlla le <a href="admin.php?page=woo-fattureincloud-premium&tab=impostazioni">impostazioni</a>:
                    per utilizzare <b>l'invio automatico</b> è necessario abilitare 
                    la creazione di una Fattura/Ricevuta <b>già pagata</b></p>
                    
                <?php
                    
        }

        */
    // if (get_option('fattureincloud_autosent_id_fallito_codiva')!='') {

            ?>
          <!--
          
          <p><b>Aliquota Iva</b> al momento <b>NON RECEPIBILE</b> in Fattureincloud.it</b></p>
       
       -->         
                <?php
                
       // }

                
    } elseif (get_option('fattureincloud_autosent_id_riuscito')!='') {

        ?>
        <div id="message" class="notice notice-success">
        
                <p><b>Invio automatico ordine n <?php echo get_option('fattureincloud_autosent_id_riuscito'); ?> Riuscito!</b><br>

                <?php 

                if (get_option('fattureincloud_autosent_id_riuscito_email')!='') {
                
                ?>
                <p><b>Invio automatico email <?php echo get_option('fattureincloud_autosent_id_riuscito_email'); ?> Riuscito!</b><br>

                
                <?php } ?>
     
        
                <form method="POST">
                    <input type="hidden" name="delete_autosave_fattureincloud_success" />
                    <input type="submit" value="Cancella" class="button button-small ">
                </form>
        
                </p>



            </div>


<?php






}
        

}




/* Code displayed before the tabs (outside) Tabs */
?>

<div class="wrap woocommerce">

<!-- <div id="top_fattureincloud"></div> -->
<h1>
    <?php

if ( is_admin() ) {

    $plugin_data = get_plugin_data(plugin_dir_path(__FILE__) .'../woo-fattureincloud-premium.php', true, true);
    $plugin_version = $plugin_data['Version'];

    
        echo __(
            'WooCommerce Fattureincloud Premium '
            .$plugin_version, 'woo-fattureincloud-premium'
        );


        $wfic_activated_license = trim( get_option( 'wfic_license_activated' ));

        if ($wfic_activated_license == 'invalid') {

            echo "<span style=\"color:red\"> Verificare Attivazione Licenza</span>";

            echo '<span class="dashicons dashicons-warning" style="color:red;font-size:larger;"></span>';

                     
 
            $type = 'error';
            $message = __(' Verificare che sia stata Attivata la <a href="admin.php?page=woo-fattureincloud-premium&tab=licenza">Licenza</a>
            come illustrato nella <a href="https://woofatture.com/documentazione/#La_Licenza_Dalla_Versione_Premium_200">Documentazione</a>', 'woo-fattureincloud-premium');
            add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
            settings_errors('woo-fattureincloud-premium');

    
    }


    }
    ?>
</h1>

<?php



########################################################################


$tab = (! empty($_GET['tab'])) ? esc_attr($_GET['tab']) : 'ordine';

page_tabs($tab);


if ($tab == 'ordine' ) {

    include_once  'ordine.php';


    /* the code to be displayed in the first tab */

} elseif ($tab == 'fatture') {

    include_once plugin_dir_path(__FILE__) . 'fatture.php';

} elseif ($tab == 'ricevute') {

    include_once plugin_dir_path(__FILE__) . 'ricevute.php';

} elseif ($tab == 'corrispettivi') {

    include_once plugin_dir_path(__FILE__) . 'corrispettivi.php';

} elseif ($tab == 'email') {

    include_once plugin_dir_path(__FILE__) . 'get_email_fattureincloud.php';

} elseif ($tab == 'licenza') {

    include_once plugin_dir_path(__FILE__) . 'licenza.php';

} elseif ($tab == 'connetti') {

    include_once plugin_dir_path(__FILE__) . 'connetti.php';

} else {

    /* the code to be displayed in the second tab */

    include_once plugin_dir_path(__FILE__) . 'impostazioni.php';

}



/* Code after the tabs (outside) */
