<?php
class ICAPFW_WOOsettings {
    /**
     * Bootstraps - La classe e gli hook richiedono di aggiungere azioni e filtri.
     *
     */
    public static function icapfw_init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::icapfw_add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_icapfw', __CLASS__ . '::icapfw_settings_tab' );
        add_action( 'woocommerce_update_options_icapfw', __CLASS__ . '::icapfw_update_settings' );
    }
    
    /**
     * Aggiunge una nuova scheda delle impostazioni nelle schede native delle impostazioni di WooCommerce.
     *
     * @param array $settings_tabs Serie delle impostazioni tab di WooCommerce e relative etichette.
     * @return array $settings_tabs Serie delle impostazioni tab di WooCommerce e relative etichette.
     */
    public static function icapfw_add_settings_tab( $settings_tabs ) {
        $settings_tabs['icapfw'] = __( 'Città Italiane e C.A.P' );
        return $settings_tabs;
    }
    /**
     * Utilizza l'API dei campi di amministrazione di WooCommerce per le impostazioni di output tramite la funzione @see woocommerce_admin_fields ().
     *
     * @uses woocommerce_admin_fields()
     * @uses self::icapfw_get_settings()
     */
    public static function icapfw_settings_tab() {
        woocommerce_admin_fields( self::icapfw_get_settings() );
    }
    /**
     * Utilizza l'API delle opzioni WooCommerce per salvare le impostazioni tramite la funzione @see woocommerce_update_options ().
     *
     * @uses woocommerce_update_options()
     * @uses self::icapfw_get_settings()
     */
    public static function icapfw_update_settings() {
        woocommerce_update_options( self::icapfw_get_settings() );
    }
    /**
     * Ottieni tutte le impostazioni per questo plugin per la funzione @see woocommerce_admin_fields ().
     *
     * @return array Serie di impostazioni per le funzioni @see woocommerce_admin_fields ().
     */
    public static function icapfw_get_settings() {
        $settings = array(

            'section_title' => array(
                'name'     => __( 'Impostazioni Generali' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'WC_icapfw_section_title'
            ),
            'section_disable_zipcode_mode' => array(
                'name'     => __( 'Non Autocompilare il cap' ),
                'desc_tip' => __( 'Il cap non verrà autocompilato.' ),
                'id'       => 'wcicapfw_disable_zipcode_field',
                'type'     => 'checkbox',
                'css'      => 'min-width:300px;',
                'desc'     => __( 'Inserendo il flag NON permetterai che il cap venga autocompilato dal plugin.' ),
            ),
			'section_enable_fraction_mode' => array(
                'name'     => __( 'Abilita il campo Frazione/Località' ),
                'desc_tip' => __( 'Il campo libero Frazione/Località verrà abilitato.' ),
                'id'       => 'wcicapfw_enable_fraction_field',
                'type'     => 'checkbox',
                'css'      => 'min-width:300px;',
                'desc'     => __( 'Inserendo il flag abiliterai il campo libero Frazione/Località.' ),
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'WC_icapfw_section_end'
            )
        );
        return apply_filters( 'WC_icapfw_settings', $settings );
    }
}
ICAPFW_WOOsettings::icapfw_init();
?>