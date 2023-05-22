<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://christiancannata.acom
 * @since      1.0.0
 *
 * @package    Fiscal_Code_Validator_Woocommerce
 * @subpackage Fiscal_Code_Validator_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Fiscal_Code_Validator_Woocommerce
 * @subpackage Fiscal_Code_Validator_Woocommerce/public
 * @author     Christian Cannata <christian@christiancannata.com>
 */
class Fiscal_Code_Validator_Woocommerce_Public
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
         * defined in Fiscal_Code_Validator_Woocommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Fiscal_Code_Validator_Woocommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/fiscal-code-validator-woocommerce-public.css', array(), $this->version, 'all');

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
         * defined in Fiscal_Code_Validator_Woocommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Fiscal_Code_Validator_Woocommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/fiscal-code-validator-woocommerce-public.js', array('jquery'), $this->version, false);

    }


    public function addFiscalCodeField()
    {
        add_filter('woocommerce_checkout_fields', function ($fields) {

            $fields['billing']['fiscal_code'] = array(
                'label' => __('Fiscal Code', 'fiscal-code-validator'),
                'placeholder' => _x('Fiscal Code', 'placeholder', 'fiscal-code-validator'),
                'required' => true,
                'maxlength' => 16,
                'minlength' => 16,
                'class' => array('form-row'),
                'clear' => true,
                'priority' => 20
            );

            $fields['billing']['is_fiscal_code_valid'] = array(
                'priority' => 20,
                'type' => 'hidden',
                'class' => [''],
            );

            return $fields;
        });


        add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
            if (!empty($_POST['fiscal_code'])) {
                update_post_meta($order_id, '_billing_fiscal_code', strtoupper(sanitize_text_field($_POST['fiscal_code'])));
            }
        });


    }

    public function addRestRoutes()
    {

        add_action('rest_api_init', function () {
            register_rest_route("fiscal-code", "validate", [
                    "methods" => "POST",
                    "permission_callback" => function () {
                        return true;
                    },
                    "callback" => function ($request) {
                        $fiscalCode = $request->get_param('fiscal_code');

                        if (strlen($fiscalCode) == 16) {
                            $accessToken = get_option('_fiscal_code_access_token', false);
                            if ($accessToken) {
                                $apiResponse = wp_remote_get(sprintf('https://api.miocodicefiscale.com/reverse?cf=%s&access_token=%s', $fiscalCode, $accessToken));
                                $responseBody = wp_remote_retrieve_body($apiResponse);
                                $responseBody = json_decode($responseBody, true);

                                $responseMessage = __('Codice fiscale non valido.', 'fiscal-code-validator');
                                if ($responseBody['status']) {
                                    $responseMessage = __('Codice fiscale valido.', 'fiscal-code-validator');
                                }
                                $response = new WP_REST_Response([
                                    'is_valid' => $responseBody['status'],
                                    'message' => $responseMessage
                                ]);
                                $response->set_status(200);
                                return $response;
                            }

                        } else {
                            $response = new WP_REST_Response([
                                'is_valid' => false,
                                'message' => __('Il codice fiscale deve contenere 16 caratteri.', 'fiscal-code-validator'),
                            ]);
                            $response->set_status(200);
                            return $response;
                        }

                    }
                ]
            );
        });

    }

    public function addCheckoutFiscalCodeValidation()
    {
        add_action('woocommerce_after_checkout_validation', function ($fields, $errors) {
            if (isset($fields['is_fiscal_code_valid']) && $fields['is_fiscal_code_valid'] == 0) {
                $error = __('non valido.', 'fiscal-code-validator');;
                if (isset($fields['fiscal_code']) && strlen($fields['fiscal_code']) != 16) {
                    $error = __('deve contenere 16 caratteri.', 'fiscal-code-validator');
                }
                $errors->add('validation', '<b>Codice fiscale</b> ' . $error);
            }
        }, 10, 2);

    }


}
