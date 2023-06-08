<?php
class ICAPFWPRO_WOOsettings {
    /**
     * Bootstraps - La classe e gli hook richiedono di aggiungere azioni e filtri.
     *
     */
    public static function icapfwpro_init() {
        add_action( 'woocommerce_settings_tabs_icapfw', __CLASS__ . '::icapfwpro_settings_tab' );
        add_action( 'woocommerce_update_options_icapfw', __CLASS__ . '::icapfwpro_update_settings' );
    }
    
    /**
     * Utilizza l'API dei campi di amministrazione di WooCommerce per le impostazioni di output tramite la funzione @see woocommerce_admin_fields ().
     *
     * @uses woocommerce_admin_fields()
     * @uses self::icapfwpro_get_settings()
     */
    public static function icapfwpro_settings_tab() {
        woocommerce_admin_fields( self::icapfwpro_get_settings() );
    }
    /**
     * Utilizza l'API delle opzioni WooCommerce per salvare le impostazioni tramite la funzione @see woocommerce_update_options ().
     *
     * @uses woocommerce_update_options()
     * @uses self::icapfwpro_get_settings()
     */
    public static function icapfwpro_update_settings() {
        woocommerce_update_options( self::icapfwpro_get_settings() );
    }
    /**
     * Ottieni tutte le impostazioni per questo plugin per la funzione @see woocommerce_admin_fields ().
     *
     * @return array Serie di impostazioni per le funzioni @see woocommerce_admin_fields ().
     */
    public static function icapfwpro_get_settings() {
        $settings = array(

            'section_title' => array(
                'name'     => __( 'Versione Pro' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'WC_icapfwpro_section_title'
            ),
            'section_readonlycap_mode' => array(
                'name'     => __( 'Non permettere di modificare il cap' ),
                'desc_tip' => __( 'La funzionalità si avvia solo se è attiva l\'autocompilazione del cap (non ci deve essere il flag alla voce sotto).' ),
                'id'       => 'wcicapfwpro_readonlycap_field',
                'type'     => 'checkbox',
                'css'      => 'min-width:300px;',
                'desc'     => __( 'Inserendo il flag NON permetterai la modifica del cap nel checkout' ),
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'WC_icapfwpro_section_end'
            )
        );
        return apply_filters( 'WC_icapfwpro_settings', $settings );
    }
}
ICAPFWPRO_WOOsettings::icapfwpro_init();
?>