<?php


// Don't access this directly, please

if (! defined('ABSPATH')) { 

    exit;

}

// check user permission to admin setup values

function woo_fattureincloud_setup_page_display() {
    if (!current_user_can('manage_woocommerce')) {
        wp_die('Unauthorized user');
    }

/**
 *
 * Get the value from menu or from search text field
 *
*/

    if (isset($_POST['woo_fattureincloud_order_id']) or isset($_POST['woo_fattureincloud_search_order_id']) && wp_verify_nonce($_POST['_wpnonce'])) {

        if ($_POST['woo_fattureincloud_search_order_id']) {

            update_option('woo_fattureincloud_order_id', $_POST['woo_fattureincloud_search_order_id']);

        } else {
                update_option('woo_fattureincloud_order_id', $_POST['woo_fattureincloud_order_id']);

        }

    }

    /**
     *
     * update value API UID and API KEY
     *
     */


    if (isset($_POST['wfic_id_azienda']) && wp_verify_nonce($_POST['_wpnonce'])) {
        update_option('wfic_id_azienda', sanitize_text_field($_POST['wfic_id_azienda']));
        
    }


    if (isset($_POST['woo_fic_custom_pay_method']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('woo_fic_custom_pay_method', sanitize_text_field($_POST['woo_fic_custom_pay_method']));

    }
   
    if (isset($_POST['woo_fic_payment_method_custom_code']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('woo_fic_payment_method_custom_code', sanitize_text_field($_POST['woo_fic_payment_method_custom_code']));

    }

    

    if (isset($_POST['wfic_nome_azienda']) && wp_verify_nonce($_POST['_wpnonce'])) {
        update_option('wfic_nome_azienda', sanitize_text_field($_POST['wfic_nome_azienda']));
        
    }


    if (isset($_POST['api_uid_fattureincloud']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('api_uid_fattureincloud', sanitize_text_field($_POST['api_uid_fattureincloud']));
        
    }

    if (isset($_POST['wfic_api_key_fattureincloud']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('wfic_api_key_fattureincloud', sanitize_text_field($_POST['wfic_api_key_fattureincloud']));

    }

    ####################

    if (isset($_POST['fattureincloud_license_key']) && wp_verify_nonce($_POST['_wpnonce'])) {
        update_option('fattureincloud_license_key', sanitize_text_field($_POST['fattureincloud_license_key']));
        $type = 'updated';
        $message4 = __('Valore Aggiornato', 'woo-fattureincloud-premium');
        add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message4, $type);
        settings_errors('woo-fattureincloud-premium');
        error_log("fattureincloud_license_key");
        
    }

    if (isset($_POST['fattureincloud_license_email']) && wp_verify_nonce($_POST['_wpnonce'])) {
        update_option('fattureincloud_license_email', sanitize_text_field($_POST['fattureincloud_license_email']));
        
    }

#####################################



    if (isset($_POST['woo-fattureincloud-anno-fatture']) && wp_verify_nonce($_POST['anno-fatture'], 'salva-anno-fatture' )) {
        update_option('woo-fattureincloud-anno-fatture', sanitize_text_field($_POST['woo-fattureincloud-anno-fatture']));

    }

    if (isset($_POST['woo-fattureincloud-anno-ricevute']) && wp_verify_nonce($_POST['_wpnonce'])) {
        update_option('woo-fattureincloud-anno-ricevute', sanitize_text_field($_POST['woo-fattureincloud-anno-ricevute']));

    }

    if (isset($_POST['woo_fattureincloud_sezionale']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('woo_fattureincloud_sezionale', sanitize_text_field($_POST['woo_fattureincloud_sezionale']));

    }

    if (isset($_POST['woo_sezionale_da_categoria']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('woo_sezionale_da_categoria', sanitize_text_field($_POST['woo_sezionale_da_categoria']));

    }

    if (isset($_POST['fattureincloud_auto_save'])) {
        update_option('fattureincloud_auto_save', sanitize_text_field($_POST['fattureincloud_auto_save']));

    }

    if (isset($_POST['fattureincloud_status_order'])) {
        update_option('fattureincloud_status_order', sanitize_text_field($_POST['fattureincloud_status_order']));

    }

    if (isset($_POST['fattureincloud_invia_email_auto'])) {
        update_option('fattureincloud_invia_email_auto', sanitize_text_field($_POST['fattureincloud_invia_email_auto']));
    }

    if (isset($_POST['fattureincloud_select_field_checkout'])) {
        update_option('fattureincloud_select_field_checkout', sanitize_text_field($_POST['fattureincloud_select_field_checkout']));
    }
    
    if (isset($_POST['woofic_marca_bollo_elettronica'])) {
        update_option('woofic_marca_bollo_elettronica', sanitize_text_field($_POST['woofic_marca_bollo_elettronica']));
    }

    if (isset($_POST['fattureincloud_send_choice'])) {
        update_option('fattureincloud_send_choice', sanitize_text_field($_POST['fattureincloud_send_choice']));

    }

    if (isset($_POST['fattureincloud_paid'])) {
        update_option('fattureincloud_paid', sanitize_text_field($_POST['fattureincloud_paid']));

    }

    if (isset($_POST['fattureincloud_richiesta_fattura'])) {
        update_option('fattureincloud_richiesta_fattura', sanitize_text_field($_POST['fattureincloud_richiesta_fattura']));

    }

    if (isset($_POST['update_customer_registry'])) {
        update_option('update_customer_registry', sanitize_text_field($_POST['update_customer_registry']));

    }

    if (isset($_POST['show_short_descr'])) {
        update_option('show_short_descr', sanitize_text_field($_POST['show_short_descr']));

    }

    if (isset($_POST['show_long_descr'])) {
        update_option('show_long_descr', sanitize_text_field($_POST['show_long_descr']));

    }

    if (isset($_POST['woofic_soloricevute_chkout']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('woofic_soloricevute_chkout', sanitize_text_field($_POST['woofic_soloricevute_chkout']));
    
    }

    if (isset($_POST['woofic_ordine_zero']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('woofic_ordine_zero', sanitize_text_field($_POST['woofic_ordine_zero']));
    
    }

    if (isset($_POST['woo_fic_cf_chk']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('woo_fic_cf_chk', sanitize_text_field($_POST['woo_fic_cf_chk']));
    
    }

    if (isset($_POST['woo_fic_cf_chk_hard']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('woo_fic_cf_chk_hard', sanitize_text_field($_POST['woo_fic_cf_chk_hard']));
    
    } 

    if (isset($_POST['woo_fic_keep_data']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('woo_fic_keep_data', sanitize_text_field($_POST['woo_fic_keep_data']));
    
    }

    if (isset($_POST['woo_fic_send_error_email']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('woo_fic_send_error_email', sanitize_text_field($_POST['woo_fic_send_error_email']));
    
    }

       



    if (isset($_POST['delete_autosave_fattureincloud'])) {
        //delete_option('fattureincloud_autosent_id_importozero');
        delete_option('fattureincloud_autosent_id_fallito');
        delete_option('fattureincloud_autosent_id_fallito_codiva');
        $type = 'updated';
        $message = __( 'Segnalazione errore rimossa', 'woo-fattureincloud-premium' );
        add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
        settings_errors('woo-fattureincloud-premium');

    }

    if (isset($_POST['delete_autosave_fattureincloud_success'])) {
        delete_option('fattureincloud_autosent_id_importozero');
        delete_option('fattureincloud_autosent_id_riuscito');
        delete_option('fattureincloud_autosent_id_riuscito_email');
        $type = 'updated';
        $message = __( 'Segnalazione rimossa', 'woo-fattureincloud-premium' );
        add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message, $type);
        settings_errors('woo-fattureincloud-premium');

    }

    if (isset($_POST['fattureincloud_partiva_codfisc']) && wp_verify_nonce($_POST['impostazioni-wfcp'], 'salva-impostazioni-wfcp')) {
        update_option('fattureincloud_partiva_codfisc', sanitize_text_field($_POST['fattureincloud_partiva_codfisc']));
        $type = 'updated';
        $message3 = __('Valore Aggiornato', 'woo-fattureincloud-premium');
        add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message3, $type);
        settings_errors('woo-fattureincloud-premium');
      //  error_log("fattureincloud_partiva_codfisc");
    

    }

    if (isset($_POST['fattureincloud_partiva_codfisc_extplugin'])) {
        update_option('fattureincloud_partiva_codfisc_extplugin', sanitize_text_field($_POST['fattureincloud_partiva_codfisc_extplugin']));
        $type = 'updated';
        $message2 = __('Valore Aggiornato', 'woo-fattureincloud-premium');
        add_settings_error('woo-fattureincloud-premium', esc_attr('settings_updated'), $message2, $type);
        settings_errors('woo-fattureincloud-premium');
       // error_log("fattureincloud_partiva_codfisc_extplugin");

    }

    // include setup form external
    // get values from setup-file.php

    include_once plugin_dir_path(__FILE__) . '../inc/setup-file.php';

}