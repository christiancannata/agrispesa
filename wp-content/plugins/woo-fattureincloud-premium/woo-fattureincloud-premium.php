<?php
/*
Plugin Name: WooCommerce Fattureincloud Premium
Plugin URI:  https://woofatture.com/
Description: WooCommerce Fattureincloud integration
Author:      Woofatture
Author URI:  https://woofatture.com
Version:     3.0.1
Text Domain: woo-fattureincloud-premium
Domain Path: /languages
Contributors: cristianozanca
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 4.0
WC tested up to: 7.2
Bitbucket Plugin URI: https://bitbucket.org/cristianozanca/woofatture-premium/
 */


################################################################

$wfic_activated_license = trim( get_option( 'wfic_license_activated' ));

if ($wfic_activated_license == 'valid') {

require 'plugin-update-checker/plugin-update-checker.php';
    $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
        'https://bitbucket.org/cristianozanca/woofatture-premium',
        __FILE__,
        'woo-fattureincloud-premium'
    );

    $myUpdateChecker->setAuthentication(
        array(
            'consumer_key' => 'q5zRTkevKm8fSadBFH',
            'consumer_secret' => 'KPE2ZTMCBjqPs3NB3LV5mErkrmMzP56g',
        )
    );

    $myUpdateChecker->setBranch('master');

}

####################################################################


    function woo_fattureincloud_premium_textdomain()
    {
        load_plugin_textdomain('woo-fattureincloud-premium', false, basename(dirname(__FILE__)) . '/languages');
    }

    add_action('plugins_loaded', 'woo_fattureincloud_premium_textdomain');

    ############################################################
    
    function wfic_ms_activate($networkwide) {
        if (is_multisite() || $networkwide) { 

            deactivate_plugins( plugin_basename( __FILE__ ) );
  
            wp_die( __( 'Spiacenti il plugin WooCommerce Fattureincloud Premium non è attivabile nel Network di
            WordPress Multisite.', 'woo_fattureincloud_premium_textdomain' ) );

        }
    }

    
    register_activation_hook(__FILE__,'wfic_ms_activate');

#######################################################################################



    if (class_exists('woo_fattureincloud')) {

    wp_die('disabilitare <b>WooCommerce Fattureincloud</b> versione gratuita <button onclick="history.back()">Indietro</button>');

    }

#############################################################################################


    if (!class_exists('woo_fattureincloud_premium')) : {
        class woo_fattureincloud_premium
        {
            

            public function check_wcfic() 
            {


                if (in_array('woo-fattureincloud/woo-fattureincloud.php', apply_filters('active_plugins', get_option('active_plugins')))) {

                    deactivate_plugins(
                        plugin_basename('woo-fattureincloud-premium/woo-fattureincloud-premium.php')
                    );

                    $class = "error";
                    $message = sprintf(
                        __(
                            '<b>WooCommerce Fattureincloud Premium</b> 
                            activation caused %sWooCommerce Fattureincloud%s to be <b>deactivated</b>!', 'woo-fattureincloud-premium'
                        ),
                        '<a href="https://wordpress.org/plugins/woo-fattureincloud/">', '</a>'
                    );
                    echo"<div class=\"$class\"> <p>$message</p></div>";
                }   
            }
        


            public function __construct()
            {

                if (!defined('ABSPATH')) {

                    exit; // Exit if accessed directly

                }

                add_action( 'before_woocommerce_init', function() {
                    if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
                        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
                    }
                } );

                add_action('admin_notices', array ( $this, 'check_wc_cf_piva'));

                add_action('admin_notices', array ( $this, 'check_wcfic'));

                include_once plugin_dir_path(__FILE__) . 'inc/menu_setup.php';

                include_once plugin_dir_path(__FILE__) . 'inc/setup_page_display.php';

                


                add_option('fattureincloud_send_choice', 'fatturaelettronica');

                add_option('fattureincloud_paid', '1');

                add_option('fattureincloud_auto_save', 'nulla');

                add_option('fattureincloud_invia_email_auto', 'no');

                add_option('fattureincloud_partiva_codfisc', '1');

                /*add_filter('woocommerce_navigation_current_screen_id', 'woocommerce_admin_log_current_screen_id', 1 );

                
                function woocommerce_admin_log_current_screen_id( $screen_id ){
                    error_log( $screen_id );
                    return $screen_id;
                }

                */


                add_action('admin_enqueue_scripts', array($this, 'register_woo_fattureincloud_styles_and_scripts'));

                add_action('admin_menu', 'woo_fattureincloud_setup_menu');

                add_action('admin_menu', 'add_to_woocommerce_navigation_bar');

                add_action('wp_ajax_woo_fattureincloud_search', array($this, 'woo_fattureincloud_search'));

                add_action('wp_footer', array($this, 'woo_fattureincloud_enqueue_script'));

                add_action('admin_notices', array($this, 'woo_fic_admin_notices'));

                add_action( 'woocommerce_cart_calculate_fees', array($this,'wfic_marca_bollo_surcharge' ));

                


                if ('completed' == get_option('fattureincloud_status_order')) {

                    add_action('woocommerce_order_status_completed', array(&$this, 'fattureincloud_order_autoconvert'), 10, 3);
                
                }
                                
                if ('onhold' == get_option('fattureincloud_status_order')) {

                    add_action('woocommerce_order_status_on-hold', array(&$this, 'fattureincloud_order_autoconvert'), 10, 3);

                }
                
                if ('processing' == get_option('fattureincloud_status_order')) {
                
                    add_action('woocommerce_order_status_processing', array(&$this, 'fattureincloud_order_autoconvert'), 10, 3);
                
                }



                if (1 == get_option('fattureincloud_partiva_codfisc') ) {

                    include_once plugin_dir_path(__FILE__) . 'inc/vat_number.php';

                    
                    //add_filter('woocommerce_checkout_get_value', '__return_empty_string', 1, 1); // clear checkout field
                    

                    //add_action('woocommerce_review_order_before_submit', 'billing_fattura_wc_custom_checkout_field', 10);


                    add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta', 10, 1);

                    add_action('woocommerce_before_checkout_billing_form', 'billing_fattura_wc_custom_checkout_field', 10, 3);

                    
                                        
                    add_action('woocommerce_admin_order_data_after_billing_address', 'display_custom_field_on_order_edit_pages', 10, 1);

                    add_action('woocommerce_order_details_after_customer_details', 'woo_fic_displayfield_typ', 9, 1);

                    add_filter('woocommerce_admin_billing_fields', 'Admin_Billing_field', 9, 3);

                    if ( ! is_admin() ) {

                    add_filter('woocommerce_billing_fields', 'Billing_Fields_woofc', 9, 3);

                    }

                    add_filter('woocommerce_email_customer_details', 'woofic_woocommerce_email_order_meta_fields', 9, 4);

                    add_filter('woocommerce_billing_fields', 'woofic_override_default_address_fields', 10, 3);

                    add_action('woocommerce_checkout_process', 'woofic_validation_checkout_fields_process', 10, 3);

                    
                    ########################################################################

                    register_activation_hook( __FILE__, array ( $this,'run_on_activate')) ; 
      
                    add_action( 'woofic_lic_cron_hook',  array ( $this,'woofic_cron_hourly'));
               
                    add_filter( 'cron_schedules', array ( $this,'woofic_add_cron_intervals')) ;    
               
                    register_deactivation_hook(__FILE__, array ( $this,'my_deactivation'));

                    #######################################################################

                    
 
                    
                    
                }
             

            }

            ########################################################

                      

            function my_deactivation() {
                wp_clear_scheduled_hook('woofic_lic_cron_hook');
             }
       
       
       
              function woofic_add_cron_intervals( $schedules ) {
                       
                $schedules['hourly'] = array( // the name to be used in code
                   'interval' => 3600, // Intervals, listed in seconds
                   'display' => __('Every hour') // display name
                );
                return $schedules; // give back the list of schedules
              }
       
              function run_on_activate(){
       
                if( !wp_next_scheduled( 'woofic_lic_cron_hook' ) ) {
                   wp_schedule_event( time(), 'hourly', 'woofic_lic_cron_hook' );
                }
              
              }
       
              public function woofic_cron_hourly() {

                require_once(plugin_dir_path( __FILE__ ) . "inc/licenza.php");
       
             }


            ########################################################

            ###########################################################################
            #
            #       MARCA Bollo aggiungi nel carrello
            #
            ###########################################################################

            

            function wfic_marca_bollo_surcharge() {
                

            
                include_once plugin_dir_path(__FILE__) . 'inc/marca_bollo.php';

            }

            ##############################################################################

            


            function woo_fic_admin_notices()
            {
                if (!is_plugin_active('woocommerce/woocommerce.php')) {
                    echo "<div class='notice error is-dismissible'><p>".__(
                        'To use the plug-in <b>WooCommerce Fattureincloud</b> the
                         <b>WooCommerce</b> plug-in must be installed and activated!', 'woo-fattureincloud'
                    )."</div>";
                }
            }

            public function check_wc_cf_piva() 
            {

                if (1 == get_option('fattureincloud_partiva_codfisc')) {

                    if (in_array('woo-piva-codice-fiscale-e-fattura-pdf-per-italia/dot4all-wc-cf-piva.php', apply_filters('active_plugins', get_option('active_plugins')))) {


                        deactivate_plugins(plugin_basename('woo-piva-codice-fiscale-e-fattura-pdf-per-italia/dot4all-wc-cf-piva.php'));

                        $class = "error";
                        $message = sprintf(
                            __('L\'attivazione dell\'opzione <b>WooCommerce Fatteincloud campi CF PI PEC CD</b> ha causato la <b>disattivazione</b> del plugin %sWooCommerce P.IVA e Codice Fiscale per Italia%s!', 'woo-fattureincloud-premium'),
                            '<a href="https://it.wordpress.org/plugins/woo-piva-codice-fiscale-e-fattura-pdf-per-italia/">', '</a>'
                        );

                        echo"<div class=\"$class\"> <p>$message</p></div>";

                    }
                }
            }

            
            
            function fattureincloud_order_autoconvert($order_id) 
            {                

                $wfic_woo_auto_activation = true;

                ###################################################################
                
                $order = wc_get_order($order_id);
    
                $order_data = $order->get_data(); // The Order data
    
                $order_total = $order_data['total'];

                /* if order uguale a zero non generare il documento se attivato nelle impostazioni  */

                if (1 == get_option('woofic_ordine_zero')) {

                    if ($order_total == 0 ) { 

                        update_option('fattureincloud_autosent_id_importozero', $order_id);
                    
                    return;

                    }
                                    
                }
                ####################################################################


                global $fattureincloud_result;

                $woorichiestafattura = get_post_meta($order_id, 'woorichiestafattura', true);
                if ($woorichiestafattura == 'woorichiestafattura_ricevuta') { 

                    error_log("$order_id richiesta ricevuta, questa è una segnalazione non è un errore", 0); 

                    $doc_type_wfic = "receipt";

                    $invoice_elet_type_wfic = false;
                    

                    include plugin_dir_path(__FILE__) . 'inc/woo-fic-sendinc-ricevuta.php';

                ###########################################################################################

                } elseif ($woorichiestafattura == 'woorichiestafattura_fattura') { 

                    error_log("$order_id richiesta fattura, questa è una segnalazione non è un errore", 0); 

                    $doc_type_wfic = "invoice";

                    $invoice_elet_type_wfic = false;

                    include plugin_dir_path(__FILE__) . 'inc/woo-fic-sendinc.php';

                ############################################################################################


                } elseif ($woorichiestafattura == 'woorichiestafattura_fatturae') { 

                    error_log("$order_id richiesta fattura elettronica, questa è una segnalazione non è un errore", 0); 

                    $doc_type_wfic = "invoice";

                    $invoice_elet_type_wfic = true;

                    include plugin_dir_path(__FILE__) . 'inc/woo-fic-sendinc.php';
                    
                    
                #############################################################################################
                
                } elseif ($woorichiestafattura == 'woorichiestafattura_fattura_privato') { 

                    error_log("$order_id set to fattura da privato, questa è una segnalazione non è un errore", 0); 

                    $doc_type_wfic = "invoice";

                    $invoice_elet_type_wfic = true;

                    include plugin_dir_path(__FILE__) . 'inc/woo-fic-sendinc.php';
                    
                    
                #############################################################################################

                } elseif ($woorichiestafattura == 'woorichiestafattura_fatturae_privato') { 

                    error_log("$order_id set to fattura elettronica da privato, questa è una segnalazione non è un errore", 0); 

                    $doc_type_wfic = "invoice";

                    $invoice_elet_type_wfic = true;

                    include plugin_dir_path(__FILE__) . 'inc/woo-fic-sendinc.php';
                    

                ##############################################################################################
                    
                
                } elseif ('fatturaelettronica' == get_option('fattureincloud_auto_save')) {
                    
                    error_log("$order_id set to fattura elettronica, questa è una segnalazione non è un errore", 0); 

                    $doc_type_wfic = "invoice";
                   
                    $invoice_elet_type_wfic = true;

                    include plugin_dir_path(__FILE__) . 'inc/woo-fic-sendinc.php';


                ##############################################################################################


                } elseif ('fattura' == get_option('fattureincloud_auto_save')) {
                                
                    error_log("$order_id set to fattura, questa è una segnalazione non è un errore", 0); 

                    $doc_type_wfic = "invoice";

                    $invoice_elet_type_wfic = false;
         
                    //$fatturaelettronica_fic = "false";
         
                    include plugin_dir_path(__FILE__) . 'inc/woo-fic-sendinc.php';

                ########################################################################################

                } elseif ('ricevuta' == get_option('fattureincloud_auto_save')) {

                    $doc_type_wfic = "receipt";

                    $invoice_elet_type_wfic = false;

                    include plugin_dir_path(__FILE__) . 'inc/woo-fic-sendinc-ricevuta.php';

                }
            }

            /**
             *
             *
             * Ajax Callback to Search Orders
             *
             *
             */
            
            public function woo_fattureincloud_search()
            {

                $q = filter_input(INPUT_GET, 'q');

                $args = array(
                'post_type' => 'shop_order',
                'posts_per_page' => 10,
                'post_status' => array_keys(wc_get_order_statuses()),
                'post__in' => array($q)
                );
            
                $response = array();
                $orders = new WP_Query($args);

                while ($orders->have_posts()):
                    $orders->the_post();
                    $id = get_the_id();
                    $response[] = array('id' => $id, 'text' => '#order :' . $id);
                endwhile;

                wp_reset_postdata();

                wp_send_json($response);
            }



            function woo_fattureincloud_enqueue_script() 
            {
                if (is_checkout()) {
                
                    wp_enqueue_script('woo_fic_cf', plugins_url('assets/js/woo_fic_cf.js', __FILE__, array('jquery'), 1.0, true));

                    if (1 == get_option('woo_fic_cf_chk')) {

                        wp_enqueue_script('woo_fic_cf_chk', plugins_url('assets/js/woo_fic_cf_chk.js', __FILE__, array('jquery'), 1.0, true));

                    } else if (1 == get_option('woo_fic_cf_chk_hard')) {

                        wp_enqueue_script('woo_fic_cf_chk', plugins_url('assets/js/woo_fic_cf_chk_hard.js', __FILE__, array('jquery'), 1.0, true));
    
                        }

                }

                          
            }


            /* Custom stylesheet to load image and js scripts only on backend page */


            function register_woo_fattureincloud_styles_and_scripts($hook)
            {

                $current_screen = get_current_screen();

                if (strpos($current_screen->base, 'woo-fattureincloud-premium') === false) {
                    return;
                

                    /*elseif( $hook != 'woocommerce_page_digthis-woocommerce-fattureincloud' ){
                            return;
                    }*/

                } else {

                    wp_enqueue_style('boot_css', plugins_url('assets/css/woo_fattureincloud.css', __FILE__));
                    wp_enqueue_style('woo-fattureincloud-select2-css', plugins_url('assets/css/select2.min.css', __FILE__));
                    wp_enqueue_script('woo-fattureincloud-select2-js', plugins_url('assets/js/select2/select2.min.js', __FILE__));
                    wp_enqueue_script('woo-fattureincloud-it-select2-js', plugins_url('assets/js/select2/i18n/it.js', __FILE__));

                                    /* Load the datepicker jQuery-ui plugin script */
                
                    wp_enqueue_script('jquery-ui-datepicker');
                    wp_enqueue_script(
                        'wp-jquery-date-picker', plugins_url(
                            'assets/js/woo_admin.js', __FILE__, array(
                            'jquery', 'jquery-ui-core'), time(), true
                        )
                    );
  
                    /*    wp_enqueue_style( 'jquery-ui-datepicker' ); */

                    wp_register_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
                    wp_enqueue_style('jquery-ui');    

                }
            }

        }
        
        }

        /* Creates a new instance */
        
        new woo_fattureincloud_premium;
    endif;

