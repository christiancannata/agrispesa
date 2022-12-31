<?php
/**
 * Plugin Name: GDPR Compliance & Cookie Consent
 * Plugin URI:  https://stylemixthemes.com/gdpr/
 * Description: The GDPR (General Data Protection Regulation) is a set of instructions for companies that collect and process EU user data on the Internet. The new regulation is aimed at improving the level of protection and giving EU residents wide control over their data.
 * Author:      StylemixThemes
 * Author URI:  https://stylemixthemes.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Version:     1.2
 * Text Domain: gdpr-compliance-cookie-consent
 * Domain Path: /languages
 */

namespace STM_GDPR;

use STM_GDPR\includes\STM_Cookie;
use STM_GDPR\includes\STM_DataAccess;
use STM_GDPR\includes\STM_PluginOptions;
use STM_GDPR\includes\STM_Helpers;
use STM_GDPR\includes\STM_Plugins;

if (!defined('ABSPATH')) {
    exit;
}

define('STM_GDPR_SLUG', 'gdpr-compliance-cookie-consent');
define('STM_GDPR_PREFIX', 'stmgdpr_');
define('STM_GDPR_ROOT_FILE', __FILE__);
define('STM_GDPR_PATH', dirname(__FILE__));
define('STM_GDPR_URL', plugins_url('', __FILE__));

spl_autoload_register(__NAMESPACE__ . '\\stm_autoload');
add_action('plugins_loaded', array(STM_GDPR::getInstance(), 'init'));

class STM_GDPR
{

    private static $instance = null;

    public function init()
    {

        load_plugin_textdomain('gdpr-compliance-cookie-consent', false, basename(dirname(__FILE__)) . '/languages/');

        if (is_admin()) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            add_action('cmb2_admin_init', array(STM_PluginOptions::getInstance(), 'stm_pluginOptions_generateOptionsPage'));
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'stm_pluginSettingsLink'));
            add_action('admin_enqueue_scripts', array(STM_Helpers::getInstance(), 'stm_enqueue_admin_scripts'));
        }

        if (STM_Helpers::stm_helpers_isEnabled(STM_GDPR_PREFIX . 'general', 'popup') && !STM_Cookie::getInstance()->stm_cookie_isAccepted()) {
            if (!is_admin() && STM_Helpers::stm_helpers_cmb_get_option(STM_GDPR_PREFIX . 'general', 'block_cookies')) {
                add_action('template_redirect', array(STM_Cookie::getInstance(), 'stm_cookie_block_cookies'), 0);
                add_action('shutdown', array(STM_Cookie::getInstance(), 'stm_cookie_block_cookies'), 0);
            }
            add_action('wp_footer', array(STM_Cookie::getInstance(), 'stm_cookie_displayPopup'));
            add_action('wp_ajax_stm_gdpr_cookie_accept', array(STM_Cookie::getInstance(), 'stm_cookie_cookieAccept'));
            add_action('wp_ajax_nopriv_stm_gdpr_cookie_accept', array(STM_Cookie::getInstance(), 'stm_cookie_cookieAccept'));
        }

        /* Enqueue front scripts and Ajax requests */
        add_action('wp_enqueue_scripts', array(STM_Helpers::getInstance(), 'stm_enqueue_scripts'));
        add_action('wp_ajax_stm_gpdr_data_request', array(STM_DataAccess::getInstance(), 'stm_gpdr_data_request'));
        add_action('wp_ajax_nopriv_stm_gpdr_data_request', array(STM_DataAccess::getInstance(), 'stm_gpdr_data_request'));

        /* GDPR Shortcode and Widget */
        add_shortcode('stm-gpdr-data-access', array(STM_DataAccess::getInstance(), 'stm_gdpr_shortcode'));
        require_once(STM_GDPR_PATH . '/includes/STM_DataAccessWidget.php');

        /* Integrated plugins */
        STM_Plugins::getInstance();

    }

    public function stm_pluginSettingsLink($links = array())
    {

        $actionLinks = array(
            'settings' => '<a href="' . add_query_arg(array('page' => STM_GDPR_SLUG), admin_url('admin.php')) . '" aria-label="' . esc_html__('STM GDPR settings', 'gdpr-compliance-cookie-consent') . '">' . esc_html__('Settings', 'gdpr-compliance-cookie-consent') . '</a>',
        );

        return array_merge($actionLinks, $links);
    }

    public static function getInstance()
    {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


}

function stm_autoload($class = '')
{

    if (strpos($class, 'STM_GDPR') !== 0) {
        return;
    }

    $return = str_replace('STM_GDPR\\', '', $class);
    $return = str_replace('\\', '/', $return);

    require $return . '.php';
}