<?php
if (!defined('ABSPATH')) {
    exit; // Impedisce l'accesso diretto
}

/**
 * Plugin Name: Karmametrix Widget Integration
 * Plugin URI: https://karmametrix.com/
 * Description: A simple plugin to add a widget container and script to the footer.
 * Version: 1.2.5
 * Author: Karmametrix
 * Author URI: https://Karmametrix.com/
 * License: GPL2
 * Text Domain: karmametrix-widget-integration
 */

/**
 * Aggiunge lo script del widget al footer se lo shortcode è usato
 */
function karmametrix_enqueue_script()
{
    wp_enqueue_script(
        'karmametrix-widget-script',
        'https://widget-a.karmametrix.com/js/widget.js',
        [],
        '1.2.5',
        true
    );
}

add_action('wp_enqueue_scripts', 'karmametrix_enqueue_script');

/**
 * Aggiunge il menu di amministrazione per gestire il widget
 */
function karmametrix_add_admin_menu()
{
    add_options_page(
        esc_html__('Karmametrix Widget Settings', 'karmametrix-widget-integration'),
        'Karmametrix',
        'manage_options',
        'karmametrix-widget-plugin',
        'karmametrix_render_admin_page'
    );
}

add_action('admin_menu', 'karmametrix_add_admin_menu');

/**
 * Visualizza la pagina di impostazioni del plugin
 */
function karmametrix_render_admin_page()
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Karmametrix Widget Settings', 'karmametrix-widget'); ?></h1>
        <form method="post" action="" id="karmametrix-widget-form">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Widget Code', 'karmametrix-widget'); ?></th>
                    <td>
                        <input type="text" name="karmametrix_widget_code" value="<?php echo esc_attr(get_option('karmametrix_widget_code')); ?>" class="regular-text"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Theme', 'karmametrix-widget'); ?></th>
                    <td>
                        <select id="karmametrix-theme" class="regular-text">
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Use Custom Colors', 'karmametrix-widget'); ?></th>
                    <td>
                        <input type="checkbox" id="use-custom-colors"> <?php esc_html_e('Enable custom colors', 'karmametrix-widget'); ?>
                    </td>
                </tr>
                <tr id="custom-colors-section" style="display: none;">
                    <th scope="row"><?php esc_html_e('Text Color (optional)', 'karmametrix-widget'); ?></th>
                    <td>
                        <input type="color" id="karmametrix-text-color">
                        <button type="button" id="reset-text-color" class="button"><?php esc_html_e('Reset', 'karmametrix-widget'); ?></button>
                    </td>
                </tr>
                <tr id="custom-colors-section-bg" style="display: none;">
                    <th scope="row"><?php esc_html_e('Background Color (optional)', 'karmametrix-widget'); ?></th>
                    <td>
                        <input type="color" id="karmametrix-background-color">
                        <button type="button" id="reset-background-color" class="button"><?php esc_html_e('Reset', 'karmametrix-widget'); ?></button>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Shortcode', 'karmametrix-widget'); ?></th>
                    <td>
                        <textarea id="karmametrix-shortcode" readonly class="large-text">[karmawidget theme="light"]</textarea>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const useCustomColorsCheckbox = document.getElementById('use-custom-colors');
            const textColorInput = document.getElementById('karmametrix-text-color');
            const backgroundColorInput = document.getElementById('karmametrix-background-color');
            const themeSelect = document.getElementById('karmametrix-theme');
            const shortcodeField = document.getElementById('karmametrix-shortcode');
            const resetTextColorButton = document.getElementById('reset-text-color');
            const resetBackgroundColorButton = document.getElementById('reset-background-color');
            const customColorsSection = document.getElementById('custom-colors-section');
            const customColorsSectionBg = document.getElementById('custom-colors-section-bg');

            function toggleCustomColors() {
                const isChecked = useCustomColorsCheckbox.checked;

                // Mostra o nasconde i campi dei colori personalizzati
                customColorsSection.style.display = isChecked ? 'table-row' : 'none';
                customColorsSectionBg.style.display = isChecked ? 'table-row' : 'none';

                // Aggiorna lo shortcode
                updateShortcode();
            }

            function updateShortcode() {
                const theme = themeSelect.value;
                const useCustomColors = useCustomColorsCheckbox.checked;
                const textColor = useCustomColors ? textColorInput.value : null;
                const backgroundColor = useCustomColors ? backgroundColorInput.value : null;

                // Base shortcode
                let shortcode = `[karmawidget theme="${theme}"`;

                // Aggiungi il parametro custom_colors
                if (useCustomColors) {
                    shortcode += ` custom_colors="true"`;

                    // Aggiungi text_color e background_color solo se specificati
                    if (textColor) {
                        shortcode += ` text_color="${textColor}"`;
                    }
                    if (backgroundColor) {
                        shortcode += ` background_color="${backgroundColor}"`;
                    }
                }

                // Chiudi lo shortcode
                shortcode += `]`;

                // Aggiorna il campo di testo con lo shortcode
                shortcodeField.value = shortcode;
            }

            // Eventi
            useCustomColorsCheckbox.addEventListener('change', toggleCustomColors);
            textColorInput.addEventListener('input', updateShortcode);
            backgroundColorInput.addEventListener('input', updateShortcode);
            themeSelect.addEventListener('change', updateShortcode);

            resetTextColorButton.addEventListener('click', () => {
                textColorInput.value = '';
                updateShortcode();
            });

            resetBackgroundColorButton.addEventListener('click', () => {
                backgroundColorInput.value = '';
                updateShortcode();
            });

            // Inizializza il comportamento
            toggleCustomColors();
        });
    </script>
    <?php
}

/**
 * Registra la configurazione del plugin (se necessaria)
 */
function karmametrix_register_settings()
{
    register_setting('karmametrix-widget-settings-group', 'karmametrix_widget_code');
}

add_action('admin_init', 'karmametrix_register_settings');

/**
 * Shortcode per aggiungere il widget
 */
function karmametrix_shortcode($atts)
{
    $atts = shortcode_atts(
        [
            'theme' => 'light',
            'text_color' => null,
            'background_color' => null,
        ],
        $atts,
        'karmawidget'
    );

    $theme = esc_attr($atts['theme']);
    $text_color = $atts['text_color'] ? esc_attr($atts['text_color']) : '';
    $background_color = $atts['background_color'] ? esc_attr($atts['background_color']) : '';
    $widget_code = esc_attr(get_option('karmametrix_widget_code'));

    // Genera il contenitore con attributi data-*
    return sprintf(
        '<div id="karmawidget-container" data-widget-theme="%s" data-widget-code="%s" data-text-color="%s" data-background-color="%s"></div>',
        $theme,
        $widget_code,
        $text_color,
        $background_color
    );
}
add_shortcode('karmawidget', 'karmametrix_shortcode');

add_shortcode('karmawidget', 'karmametrix_shortcode');