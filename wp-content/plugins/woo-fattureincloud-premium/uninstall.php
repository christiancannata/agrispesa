<?php

/* If uninstall not called from WordPress exit */

if (!defined('WP_UNINSTALL_PLUGIN')) {

    exit();

}

if (0 == get_option('woo_fic_keep_data')) {

    


   
/* Delete option from options table */

delete_option('wfic_api_key_fattureincloud');

delete_option('api_key_fattureincloud');

delete_option('api_uid_fattureincloud');

delete_option('fattureincloud_consumer_key');

delete_option('fattureincloud_consumer_secret');


delete_option('woo-fattureincloud-anno-ricevute');

delete_option('woo-fattureincloud-anno-fatture');


delete_option('woo_fattureincloud_sezionale');

delete_option('woo_sezionale_da_categoria');

delete_option('fattureincloud_status_order');

delete_option('fattureincloud_invia_email_auto');

delete_option('fattureincloud_select_field_checkout');


delete_option('woofic_marca_bollo_elettronica');

delete_option('fattureincloud_paid');

delete_option('fattureincloud_richiesta_fattura');

delete_option('update_customer_registry');

delete_option('show_short_descr');

delete_option('show_long_descr');


delete_option('delete_autosave_fattureincloud');

delete_option('fattureincloud_partiva_codfisc');

delete_option('fattureincloud_partiva_codfisc_extplugin');



delete_option('woo_fattureincloud_order_id');

delete_option('fattureincloud_auto_save');

delete_option('fattureincloud_send_choice');

delete_option('fattureincloud_license_key');

delete_option('fattureincloud_license_email');

delete_option('woofic_ordine_zero');

delete_option('woo_fic_cf_chk');

delete_option(('woo_fic_send_error_email'));



}







