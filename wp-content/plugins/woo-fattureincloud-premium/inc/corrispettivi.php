<?php

// Don't access this directly, please
if (!defined('ABSPATH')) exit;

if (get_option('wfic_id_azienda') == null ) {

    header("Location: admin.php?page=woo-fattureincloud-premium&tab=impostazioni");


}

if ( in_array(
    'woo-fattureincloud-corrisp/woo-fattureincloud-corrisp.php',
    apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
) 
    {
        require_once plugin_dir_path( __FILE__ ) .'../../woo-fattureincloud-corrisp/inc/setup_file.php';

    } else {

        echo '<h3>Installa e Attiva il modulo <a href="https://woofatture.com/shop/woocommerce-fattureincloud-corrispettivi/">
        WooCommerce Fattureincloud Corrispettivi</a> </h3><p><img src="https://woofatture.com/wp-content/uploads/2019/11/icon-woofic-corr-256x256.png"></p>';

}    


