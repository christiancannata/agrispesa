<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://christiancannata.acom
 * @since      1.0.0
 *
 * @package    Woofic
 * @subpackage Woofic/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woofic
 * @subpackage Woofic/public
 * @author     Christian Cannata <christian@christiancannata.com>
 */
class Woofic_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woofic_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woofic_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woofic-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woofic_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woofic_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woofic-public.js', array('jquery'), $this->version, false);

    }

    public function addUserTypeField()
    {
        add_filter('woocommerce_checkout_fields', function ($fields) {

            $fields['billing']['billing_type'] = array(
                'label' => __('Vuoi ricevere la fattura?', 'woofic'),
                'placeholder' => _x('Codice Fiscale', 'placeholder', 'woofic'),
                'required' => true,
                'type' => 'select',
                'options' => [
                    'RECEIPT' => __('No, voglio la Ricevuta', 'woofic'),
                    // 'INVOICE' => 'Fattura',
                    'INVOICE' => __('Si, voglio la fattura elettronica', 'woofic'),
                ],
                'class' => array('form-row'),
                'priority' => 1
            );

            return $fields;
        });


        add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
            if (!empty($_POST['user_type'])) {
                update_post_meta($order_id, '_billing_type', strtoupper(sanitize_text_field($_POST['billing_type'])));
            }
        });


    }

}
