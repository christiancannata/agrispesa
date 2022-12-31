<?php

namespace STM_GDPR\includes;

use STM_GDPR\includes\plugins\STM_ContactForm7;
use STM_GDPR\includes\plugins\STM_GravityForms;

require_once STM_GDPR_PATH . '/includes/cmb2/init.php';

class STM_PluginOptions
{
    private static $instance = null;

    public function stm_pluginOptions_generateOptionsPage()
    {

        if (isset($_GET['settings-updated'])) {
            STM_ContactForm7::getInstance()->stm_contactform7_updateForms();
            STM_GravityForms::getInstance()->stm_gravityforms_updateForms();
        }

        $pluginData = STM_Helpers::stm_plugin_data();

        /**
         * Registers options page menu item and form.
         */
        $cmb_options = new_cmb2_box(array(
            'id' => STM_GDPR_SLUG,
            'title' => $pluginData['Name'],
            'menu_title' => 'GDPR',
            'object_types' => array('options-page'),
            'option_key' => STM_GDPR_SLUG,
            'icon_url' => 'dashicons-unlock',
            'capability' => 'manage_options',
        ));

        /* General Tab */
        $general_group_id = $cmb_options->add_field(array(
            'id' => STM_GDPR_PREFIX . 'general',
            'type' => 'group',
            'repeatable' => false,
            'before_group' => '<div class="tab-content" id="general">',
            'after_group' => '</div>',
            'options' => array(
                'group_title' => __('General', 'gdpr-compliance-cookie-consent'),
                'sortable' => false,
                'show_as_tab' => true
            )
        ));

        $cmb_options->add_group_field($general_group_id, array(
            'name' => __('Privacy and Cookie consent popup', 'gdpr-compliance-cookie-consent'),
            'desc' => __('Ask visitors to accept using Cookie and data.', 'gdpr-compliance-cookie-consent'),
            'id' => 'privacy_title',
            'type' => 'title',
        ));

        $cmb_options->add_group_field($general_group_id, array(
            'name' => __('Enable', 'gdpr-compliance-cookie-consent'),
            'desc' => __('Display Privacy and Cookie popup box', 'gdpr-compliance-cookie-consent'),
            'id' => 'popup',
            'type' => 'checkbox'
        ));

        $cmb_options->add_group_field($general_group_id, array(
            'name' => __('Block Cookies', 'gdpr-compliance-cookie-consent'),
            'desc' => __('Block Cookies until accepting the Coockie Consent', 'gdpr-compliance-cookie-consent'),
            'id' => 'block_cookies',
            'type' => 'checkbox'
        ));

        do_action('stm_gdpr_visitor_field', $cmb_options, $general_group_id);

        $cmb_options->add_group_field($general_group_id, array(
            'name' => __('Expire time (seconds)', 'gdpr-compliance-cookie-consent'),
            'desc' => __('Cookie consent expire time. Default 6 months', 'gdpr-compliance-cookie-consent'),
            'id' => 'expire_time',
            'type' => 'text',
            'default' => '15768000',
            'attributes' => array(
                'type' => 'number',
                'pattern' => '\d*',
            ),
        ));

        $cmb_options->add_group_field($general_group_id, array(
            'name' => __('Button text', 'gdpr-compliance-cookie-consent'),
            'id' => 'button_text',
            'type' => 'text',
            'default' => 'Ok, I agree'
        ));

        $cmb_options->add_group_field($general_group_id, array(
            'name' => __('Popup content', 'gdpr-compliance-cookie-consent'),
            'id' => 'popup_content',
            'type' => 'wysiwyg',
            'default' => __('This website uses cookies and asks your personal data to enhance your browsing experience.', 'gdpr-compliance-cookie-consent'),
            'options' => array(
                'textarea_rows' => 15,
            ),
        ));

        $cmb_options->add_group_field($general_group_id, array(
            'name' => __('Popup background color', 'gdpr-compliance-cookie-consent'),
            'id' => 'popup_bg_color',
            'type' => 'colorpicker',
            'default' => '#131323',
        ));

        $cmb_options->add_group_field($general_group_id, array(
            'name' => __('Popup text color', 'gdpr-compliance-cookie-consent'),
            'id' => 'popup_text_color',
            'type' => 'colorpicker',
            'default' => '#fff',
        ));

        $cmb_options->add_group_field($general_group_id, array(
            'name' => __('Popup position', 'gdpr-compliance-cookie-consent'),
            'id' => 'popup_position',
            'type' => 'select',
            'options' => array(
                'right_top_' => 'Right Top',
                'left_top_' => 'Left Top',
                'right_bottom_' => 'Right Bottom',
                'left_bottom_' => 'Left Bottom'
            ),
            'default' => 'left_bottom_'
        ));

        $cmb_options->add_group_field($general_group_id, array(
            'name' => __('Custom CSS', 'gdpr-compliance-cookie-consent'),
            'id' => 'popup_custom_css',
            'type' => 'textarea',
            'class' => 'cmb2-textarea-code'
        ));

        /* Privacy Policy */
        $privacy_group_id = $cmb_options->add_field(array(
            'id' => STM_GDPR_PREFIX . 'privacy',
            'type' => 'group',
            'repeatable' => false,
            'before_group' => '<div class="tab-content" id="general">',
            'after_group' => '</div>',
            'options' => array(
                'group_title' => __('Privacy Policy', 'gdpr-compliance-cookie-consent'),
                'sortable' => false,
                'show_as_tab' => true
            )
        ));

        $cmb_options->add_group_field($privacy_group_id, array(
            'name' => __('Privacy Policy content', 'gdpr-compliance-cookie-consent'),
            'desc' => __('Select Privacy Policy page and link text for the Popup', 'gdpr-compliance-cookie-consent'),
            'id' => 'privacy_page_title',
            'type' => 'title',
        ));

        $cmb_options->add_group_field($privacy_group_id, array(
            'name' => __('Privacy Policy page', 'gdpr-compliance-cookie-consent'),
            'id' => 'privacy_page',
            'type' => 'select',
            'options' => STM_Helpers::stm_helpers_cmb_pages_array(),
            'default' => STM_Helpers::stm_helpers_get_privacy_page()
        ));

        $cmb_options->add_group_field($privacy_group_id, array(
            'name' => __('Custom link text', 'gdpr-compliance-cookie-consent'),
            'id' => 'link_text',
            'type' => 'text',
            'default' => 'Privacy Policy'
        ));

        /* Plugins Tab */
        $plugins_group_id = $cmb_options->add_field(array(
            'id' => STM_GDPR_PREFIX . 'plugins',
            'type' => 'group',
            'repeatable' => false,
            'before_group' => '<div class="tab-content" id="plugins">',
            'after_group' => '</div>',
            'options' => array(
                'group_title' => __('Integrated plugins', 'gdpr-compliance-cookie-consent'),
                'sortable' => false,
                'show_as_tab' => true
            )
        ));

        $pluginsList = STM_Helpers::stm_helpers_pluginsList();
        if (!empty($pluginsList)) {
            foreach ($pluginsList as $plugin) {
                $cmb_options->add_group_field($plugins_group_id, array(
                    'name' => $plugin['name'],
                    'desc' => $plugin['desc'],
                    'id' => $plugin['slug'] . '_title',
                    'type' => 'title',
                ));

                $cmb_options->add_group_field($plugins_group_id, array(
                    'name' => __('Enable', 'gdpr-compliance-cookie-consent'),
                    'id' => $plugin['slug'],
                    'type' => 'checkbox',
                ));

                $cmb_options->add_group_field($plugins_group_id, array(
                    'name' => __('Checkbox label', 'gdpr-compliance-cookie-consent'),
                    'id' => $plugin['slug'] . '_label',
                    'type' => 'text',
                    'default' => __('I agree with storage and handling of my data by this website.', 'gdpr-compliance-cookie-consent')
                ));

                $cmb_options->add_group_field($plugins_group_id, array(
                    'name' => __('Error notification', 'gdpr-compliance-cookie-consent'),
                    'id' => $plugin['slug'] . '_error',
                    'type' => 'text',
                    'default' => __('You have to accept the privacy checkbox', 'gdpr-compliance-cookie-consent')
                ));
            }
        }

        $cmb_options->add_group_field($plugins_group_id, array(
            'name' => __('The list of integrated plugins', 'gdpr-compliance-cookie-consent'),
            'desc' => __('Our plugin currently supports <b>Contact Form 7</b>, <b>Gravity Forms</b>, <b>WooCommerce</b>, <b>WordPress Comments</b>, <b>MailChimp Wp</b>, <b>BuddyPress</b>.
				Integrated plugins options will be displayed automatically after installing and activating supported plugins.', 'gdpr-compliance-cookie-consent'),
            'id' => 'plugins_title',
            'type' => 'title',
        ));

        do_action('wordpress_gdpr_compliance_add_fileds', $cmb_options, STM_GDPR_PREFIX);

        /* Data Access Tab */
        $data_access_group_id = $cmb_options->add_field(array(
            'id' => STM_GDPR_PREFIX . 'data_access',
            'type' => 'group',
            'repeatable' => false,
            'before_group' => '<div class="tab-content" id="data_access">',
            'after_group' => '</div>',
            'options' => array(
                'group_title' => __('Data Access & To Be Forgotten', 'gdpr-compliance-cookie-consent'),
                'sortable' => false,
                'show_as_tab' => true
            )
        ));

        $cmb_options->add_group_field($data_access_group_id, array(
            'name' => __('NOTE: ', 'gdpr-compliance-cookie-consent'),
            'desc' => __('You have to create a page with a shortcode: <span>[stm-gpdr-data-access]</span> or use <b>GDPR Compliance</b> widget.<br>
								Also, here you can see <a href="' . add_query_arg(array('page' => 'export_personal_data'), admin_url('tools.php')) . '">Data Export Requests</a> and
								<a href="' . add_query_arg(array('page' => 'remove_personal_data'), admin_url('tools.php')) . '">Data Erasure Requests</a>. 
								This feature works only for <b>WordPress 4.9.6</b> or higher versions.', 'gdpr-compliance-cookie-consent'),
            'id' => 'plugins_title',
            'type' => 'title',
        ));

        $cmb_options->add_group_field($data_access_group_id, array(
            'name' => __('Error message', 'gdpr-compliance-cookie-consent'),
            'id' => 'error_prefix',
            'type' => 'text',
            'default' => __('Some errors occurred:', 'gdpr-compliance-cookie-consent')
        ));

        $cmb_options->add_group_field($data_access_group_id, array(
            'name' => __('Success message', 'gdpr-compliance-cookie-consent'),
            'id' => 'success',
            'type' => 'wysiwyg',
            'default' => __('Your request have been submitted. Check your email to validate your data request.', 'gdpr-compliance-cookie-consent'),
            'options' => array(
                'textarea_rows' => 15,
            ),
        ));

        $cmb_options->add_group_field($data_access_group_id, array(
            'name' => __('Input field classes', 'gdpr-compliance-cookie-consent'),
            'id' => 'input-class',
            'type' => 'text',
            'default' => ''
        ));

        $cmb_options->add_group_field($data_access_group_id, array(
            'name' => __('Submit button classes', 'gdpr-compliance-cookie-consent'),
            'id' => 'button-class',
            'type' => 'text',
            'default' => ''
        ));

        $pro_version_deactivate = true;
        $pro_version_deactivate = apply_filters('stm_gdpr_pro_version_activate', $pro_version_deactivate);

        if ($pro_version_deactivate) {
            /* Pro version */

            $stm_lock = '  <div class="stm-icon-lock">
                             <div class="stm-lock-top-1"></div>
                             <div class="stm-lock-top-2"></div>
                             <div class="stm-lock-body"></div>
                             <div class="stm-lock-hole"></div>
                           </div><a href="https://stylemixthemes.com/plugins/" class="stm_title">' . esc_html__('GDPR PRO', 'gdpr-compliance-cookie-consent') . '</a>';
            $stm_lock .= '	<div id="stm-features"><h1 class="stm-title-features">' . esc_html__('GDPR PRO FEATURES', 'gdpr-compliance-cookie-consent') . '</h1>
                            <ol class="stm-new-features-list">
                                <li><a>' . __('Geo Location', 'gdpr-compliance-cookie-consent') . '</a></a></li>
                                <li><a>' . __('Cookie Scanner', 'gdpr-compliance-cookie-consent') . '</a></li>      
                                <li><a>' . __('Additional Cookies', 'gdpr-compliance-cookie-consent') . '</a></li>                                                      
                                <li><a>' . __('Iframe/Video Blocker', 'gdpr-compliance-cookie-consent') . '</a></li>                                
                                <li><a>' . __('Settings Import/Export', 'gdpr-compliance-cookie-consent') . '</a></li>
                            </ol></div>';

            $pro_version = $cmb_options->add_field(array(
                'id' => STM_GDPR_PREFIX . 'pro_version',
                'type' => 'group',
                'repeatable' => false,
                'before_group' => '<div class="tab-content" id="pro_version">',
                'after_group' => '</div>',
                'options' => array(
                    'group_title' => __('GDPR PRO', 'gdpr-compliance-cookie-consent'),
                    'sortable' => false,
                    'show_as_tab' => true
                )
            ));

            $cmb_options->add_group_field($pro_version, array(
                'name' => __('', 'gdpr-compliance-cookie-consent'),
                'desc' => $stm_lock,
                'id' => 'button-class',
                'type' => 'title',
            ));
        }
    }

    public static function getInstance()
    {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}