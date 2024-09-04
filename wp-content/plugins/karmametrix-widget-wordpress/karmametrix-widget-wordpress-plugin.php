<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Plugin Name: Karmametrix Widget
 * Plugin URI: https://karmametrix.com/
 * Description: A simple plugin to add a widget container and script to the footer.
 * Version: 1.0
 * Author: Christian Cannata
 * Author URI: https://christiancannata.com/
 * License: GPL2
 */

// Funzione per aggiungere il div e lo script al footer
function add_widget_to_footer()
{
    if(get_option('karmametrix_widget_code')){
        echo '<script defer src="https://widget-a.karmametrix.com/js/widget.js"></script>';
    }
}

// Agganciare la funzione all'azione 'wp_footer'
add_action('wp_footer', 'add_widget_to_footer');


// Aggiungere il menu di amministrazione
function footer_widget_plugin_menu()
{
    add_options_page(
        'Karmametrix',  // Titolo della pagina
        'Karmametrix',           // Titolo del menu
        'manage_options',          // Capability
        'karmametrix-widget-plugin',    // Slug del menu
        'footer_widget_plugin_settings_page'  // Funzione della pagina
    );
}

add_action('admin_menu', 'footer_widget_plugin_menu');

// Funzione per rendere la pagina di impostazioni
function footer_widget_plugin_settings_page()
{
    if (isset($_POST['karmametrix_widget_code'])) {
        update_option('karmametrix_widget_code', sanitize_text_field($_POST['karmametrix_widget_code']));
    }
    ?>
    <div class="wrap">
        <h1>Footer Widget Settings</h1>
        <form method="post" action="">
            <?php
            settings_fields('footer-widget-plugin-settings-group');
            do_settings_sections('footer-widget-plugin-settings-group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Codice</th>
                    <td><input type="text" name="karmametrix_widget_code"
                               value="<?php echo esc_attr(get_option('karmametrix_widget_code')); ?>"/></td>
                </tr>
                <tr>
                    <th>
                      Copia e incolla il seguente shortcode nella zona dove vuoi che appaia il widget
                    </th>
                    <td>
                        <textarea readonly>[karmawidget]</textarea>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Registrare le impostazioni
function footer_widget_plugin_register_settings()
{
    register_setting('footer-widget-plugin-settings-group', 'footer_widget_code');
}

add_action('admin_init', 'footer_widget_plugin_register_settings');


function my_custom_shortcode()
{
    // Leggi un valore dalla tabella wp_options
    $option_value = get_option('karmametrix_widget_code');

    // Verifica se l'opzione esiste
    if ($option_value === false) {
        return '';
        return '<p>Nessun codice Karmametrix inserito</p>';
    }

    // Genera il codice HTML da restituire
    return'<div id="karmawidget-container" data-widget-code="' . $option_value . '"></div>';
}

add_shortcode('karmawidget', 'my_custom_shortcode');