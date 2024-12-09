<?php
if (!defined('ABSPATH')) {
    exit; // Impedisce l'accesso diretto
}

/**
 * Plugin Name: Karmametrix Widget
 * Plugin URI: https://karmametrix.com/
 * Description: A simple plugin to add a widget container and script to the footer.
 * Version: 1.2.2
 * Author: Christian Cannata
 * Author URI: https://christiancannata.com/
 * License: GPL2
 * Text Domain: karmametrix-widget
 */

/**
 * Aggiunge lo script del widget al footer se lo shortcode è usato
 */
function karmametrix_enqueue_script()
{
    global $post;

    if (!is_singular() || !isset($post->post_content)) {
        return; // Evita problemi in contesti non-singolari
    }

    if (has_shortcode($post->post_content, 'karmawidget')) {
        wp_enqueue_script(
            'karmametrix-widget-script',
            'https://widget-a.karmametrix.com/js/widget.js',
            [],
            '1.2.2',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'karmametrix_enqueue_script');

/**
 * Aggiunge il menu di amministrazione per gestire il widget
 */
function karmametrix_add_admin_menu()
{
    add_options_page(
        esc_html__('Karmametrix Widget Settings', 'karmametrix-widget'),
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
    if (
        isset($_POST['karmametrix_widget_code']) &&
        check_admin_referer('karmametrix_update_code', 'karmametrix_nonce')
    ) {
        $widget_code = sanitize_text_field(wp_unslash($_POST['karmametrix_widget_code']));
        update_option('karmametrix_widget_code', $widget_code);
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Widget code saved successfully.', 'karmametrix-widget') . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Karmametrix Widget Settings', 'karmametrix-widget'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('karmametrix_update_code', 'karmametrix_nonce'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Widget Code', 'karmametrix-widget'); ?></th>
                    <td>
                        <input type="text" name="karmametrix_widget_code" value="<?php echo esc_attr(get_option('karmametrix_widget_code')); ?>" class="regular-text"/>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Shortcode', 'karmametrix-widget'); ?></th>
                    <td>
                        <textarea readonly class="large-text">[karmawidget theme="light"]</textarea>
                        <p>
                            <em><?php esc_html_e('Change the "theme" attribute to "light" or "dark" as desired.', 'karmametrix-widget'); ?></em>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
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
        ],
        $atts,
        'karmawidget'
    );

    $theme = in_array($atts['theme'], ['light', 'dark']) ? $atts['theme'] : 'light';
    $widget_code = esc_attr(get_option('karmametrix_widget_code'));

    return sprintf(
        '<div id="karmawidget-container" data-widget-theme="%s" data-widget-code="%s"></div>',
        esc_attr($theme),
        $widget_code
    );
}
add_shortcode('karmawidget', 'karmametrix_shortcode');