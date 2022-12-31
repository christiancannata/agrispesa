<?php
namespace STM_GDPR\includes;

use STM_GDPR\includes\plugins\STM_BuddyPress;
use STM_GDPR\includes\plugins\STM_ContactForm7;
use STM_GDPR\includes\plugins\STM_GravityForms;
use STM_GDPR\includes\plugins\STM_MailChimp;
use STM_GDPR\includes\plugins\STM_WooCommerce;
use STM_GDPR\includes\plugins\STM_WordPress;

class STM_Plugins
{
    private static $instance = null;

    public function __construct() {

        foreach (STM_Helpers::stm_helpers_enabledPlugins() as $plugin) {

            switch ($plugin['slug']) {

                case STM_WordPress::SLUG :

                    add_filter('comment_form_submit_field', array(STM_WordPress::getInstance(), 'stm_wordpress_addCheckbox'), 999);
                    add_action('pre_comment_on_post', array(STM_WordPress::getInstance(), 'stm_wordpress_displayError'));
                    add_action('comment_post', array(STM_WordPress::getInstance(), 'stm_wordpress_addCommentMeta'));
                    add_filter('manage_edit-comments_columns', array(STM_WordPress::getInstance(), 'stm_wordpress_displayMetaColumn'));
                    add_action('manage_comments_custom_column', array(STM_WordPress::getInstance(), 'stm_wordpress_displayCommentOverview'), 10, 2);

                    break;

                case STM_ContactForm7::SLUG :

                    add_action('wpcf7_init', array(STM_ContactForm7::getInstance(), 'stm_contactform7_addFormTag'));
                    add_filter('wpcf7_before_send_mail', array(STM_ContactForm7::getInstance(), 'stm_contactform7_addMailMsg'), 100);
                    add_filter('wpcf7_validate_stmgdpr', array(STM_ContactForm7::getInstance(), 'stm_contactform7_validate'), 10, 2);

                    break;

				case STM_WooCommerce::SLUG :

					add_action('woocommerce_review_order_before_submit', array(STM_WooCommerce::getInstance(), 'stm_woocommerce_displayCheckbox'), 100);
					add_action('woocommerce_checkout_process', array(STM_WooCommerce::getInstance(), 'stm_woocommerce_displayError'));
					add_action('woocommerce_checkout_update_order_meta', array(STM_WooCommerce::getInstance(), 'stm_woocommerce_updateOrderMeta'));
					add_action('woocommerce_admin_order_data_after_order_details', array(STM_WooCommerce::getInstance(), 'stm_woocommerce_displayOrderData'));

					break;

				case STM_MailChimp::SLUG :

					add_filter('mc4wp_form_errors', array( STM_MailChimp::getInstance(), 'stm_mailchimp_displayError'), 10, 2 );
					add_filter('mc4wp_form_content', array( STM_MailChimp::getInstance(), 'stm_mailchimp_addCheckbox'), 10, 3 );

					break;

                case STM_BuddyPress::SLUG :
                
                    add_action( 'bp_after_message_reply_box', array( STM_BuddyPress::getInstance(), 'stm_buddypress_addCheckbox' ), 100 );
                    add_action( 'bp_after_messages_compose_content', array( STM_BuddyPress::getInstance(), 'stm_buddypress_addCheckbox' ), 100 );
                    add_action( 'bp_activity_post_form_options', array( STM_BuddyPress::getInstance(), 'stm_buddypress_addCheckbox' ), 100 );
                    add_action( 'bp_after_group_forum_post_new', array( STM_BuddyPress::getInstance(), 'stm_buddypress_addCheckbox' ), 100 );
                    add_action( 'groups_forum_new_topic_after', array( STM_BuddyPress::getInstance(), 'stm_buddypress_addCheckbox' ), 100 );
                    add_action( 'groups_forum_new_reply_after', array( STM_BuddyPress::getInstance(), 'stm_buddypress_addCheckbox' ), 100 );

					break;

				case STM_GravityForms::SLUG :

					add_filter('gform_entries_field_value', array(STM_GravityForms::getInstance(), 'stm_gravityforms_displayOverviewDate'), 10, 4);
                    add_filter('gform_get_field_value', array(STM_GravityForms::getInstance(), 'stm_gravityforms_displayDate'), 10, 2);
                    
                    foreach (STM_GravityForms::getInstance()->stm_gravityforms_getForms() as $form) {
                        add_filter('gform_entry_list_columns_' . $form['id'], array(STM_GravityForms::getInstance(), 'stm_gravityforms_displayOverviewDateColumn'), 10, 2);
                        add_filter('gform_save_field_value_' . $form['id'], array(STM_GravityForms::getInstance(), 'stm_gravityforms_addDate'), 10, 3);
                        add_action('gform_validation_' . $form['id'], array(STM_GravityForms::getInstance(), 'stm_gravityforms_validate'));
                    }

					break;
            }

        }

    }

    public static function stm_plugins_supportedPlugins() {

        return array(
            array(
                'slug' => STM_BuddyPress::SLUG,
                'file' => 'buddypress/bp-loader.php',
                'name' => __('BuddyPress', 'gdpr-compliance-cookie-consent'),
                'desc' => __('GDPR checkbox will be added automatically above the submit button. You can use HTML tags and <span>%privacy_policy%</span> shortcode link for below inputs.', 'gdpr-compliance-cookie-consent'),
            ),
            array(
                'slug' => STM_ContactForm7::SLUG,
                'file' => 'contact-form-7/wp-contact-form-7.php',
                'name' => __('Contact Form 7', 'gdpr-compliance-cookie-consent'),
				'desc' => __('GDPR checkbox will be added automatically to all your Contact Forms. You can use HTML tags and <span>%privacy_policy%</span> shortcode link for below inputs.', 'gdpr-compliance-cookie-consent'),
            ),
            array(
                'slug' => STM_GravityForms::SLUG,
                'file' => 'gravityforms/gravityforms.php',
                'name' => __('Gravity Forms', 'gdpr-compliance-cookie-consent'),
				'desc' => __('GDPR checkbox will be added automatically to all your Gravity Forms. HTML tags are NOT allowed due to plugin limitations.', 'gdpr-compliance-cookie-consent'),
            ),
            array(
                'slug' => STM_MailChimp::SLUG,
                'file' => 'mailchimp-for-wp/mailchimp-for-wp.php',
                'name' => __('MailChimp', 'gdpr-compliance-cookie-consent'),
				'desc' => __('GDPR checkbox will be added automatically at the end of the MailChimp form. You can use HTML tags and <span>%privacy_policy%</span> shortcode link for below inputs.', 'gdpr-compliance-cookie-consent'),
            ),
            array(
                'slug' => STM_WooCommerce::SLUG,
                'file' => 'woocommerce/woocommerce.php',
                'name' => __('WooCommerce', 'gdpr-compliance-cookie-consent'),
				'desc' => __('GDPR checkbox will be added automatically at the end of the Checkout page. You can use HTML tags and <span>%privacy_policy%</span> shortcode link for below inputs.', 'gdpr-compliance-cookie-consent'),
            ),
            array(
                'slug' => STM_WordPress::SLUG,
				'file' => 'wordpress',
                'name' => __('WordPress Comments', 'gdpr-compliance-cookie-consent'),
				'desc' => __('GDPR checkbox will be added automatically above the submit button. You can use HTML tags and <span>%privacy_policy%</span> shortcode link for below inputs.', 'gdpr-compliance-cookie-consent'),
            )
        );
    }

	public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}