<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://christiancannata.acom
 * @since      1.0.0
 *
 * @package    Fiscal_Code_Validator_Woocommerce
 * @subpackage Fiscal_Code_Validator_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fiscal_Code_Validator_Woocommerce
 * @subpackage Fiscal_Code_Validator_Woocommerce/admin
 * @author     Christian Cannata <christian@christiancannata.com>
 */
class Fiscal_Code_Validator_Woocommerce_Admin
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
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/fiscal-code-validator-woocommerce-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/fiscal-code-validator-woocommerce-admin.js', array('jquery'), $this->version, false);

    }

    public function addAdminPages()
    {
        add_action('admin_menu', function () {
            add_menu_page('Validatore Codice Fiscale', 'Validatore Codice Fiscale', 'manage_options', 'validatore-codice-fiscale', function () {

                if (isset($_POST['api_key'])) {
                    update_option('_fiscal_code_access_token', trim($_POST['api_key']));
                }

                if (isset($_GET['action']) && $_GET['action'] == 'delete_key') {
                    delete_option('_fiscal_code_access_token');
                }

                $apiKey = get_option('_fiscal_code_access_token', false);
                $isApiKeyValid = false;
                $apiKeyDetail = null;

                if ($apiKey) {
                    //{tua-chiave-API}
                    $apiResponse = wp_remote_get(sprintf('http://api.miocodicefiscale.com/profile?access_token=%s', $apiKey));
                    $responseBody = wp_remote_retrieve_body($apiResponse);
                    $responseBody = json_decode($responseBody, true);
                    if ($responseBody['status']) {
                        $isApiKeyValid = true;
                        $apiKeyDetail = $responseBody['data'];
                    }
                }

                ?>
                <div class="plugin-container">
                    <h1>Validatore Codice Fiscale</h1>
                    <span>Configura il plugin in base alle tue preferenze.</span><br><br>
                    <h3>Chiave API</h3>

                    <?php
                    if (!$apiKey || !$isApiKeyValid):
                        ?>
                        <?php if (!$isApiKeyValid): ?>
                        <div class="alert alert-danger">
                            Chiave API non valida
                        </div>
                    <?php endif; ?>
                        <div class="container-text">
                            Inserisci una chiave API valida per poter utilizzare il plugin.<br>
                            <h5>ISTRUZIONI:</h5>
                            <span>Clicca qui per richiedere la tua chiave API per poter utilizzare il plugin: </span><br>
                            <a target="_blank" class="button button-primary request-api"
                               href="https://www.miocodicefiscale.com/it/api-rest-verifica-e-calcolo-codice-fiscale">Richiedi
                                qui la tua chiave API</a>
                        </div>
                        <form
                                class="api-key-form"
                            <?php if (!$apiKey): ?> style="display:none" <?php endif; ?>
                                method="POST" action="/wp-admin/admin.php?page=validatore-codice-fiscale">
                            <input type="password" name="api_key" placeholder="*********">
                            <button class="button button-primary">Aggiorna la chiave API</button>
                        </form>
                    <?php elseif ($isApiKeyValid): ?>
                        <div class="alert alert-success">
                            Chiave API valida
                        </div>
                        <table class="table">
                            <tbody>
                            <tr>
                                <td>
                                    <b>Dominio</b>
                                </td>
                                <td>
                                    <?php echo $apiKeyDetail['site_url']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Email</b>
                                </td>
                                <td>
                                    <?php echo $apiKeyDetail['email']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Limite chiamate</b>
                                </td>
                                <td>
                                    <?php echo $apiKeyDetail['max_call']; ?>/giorno
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Chiamate oggi</b>
                                </td>
                                <td>
                                    <?php echo $apiKeyDetail['total_calls_today']; ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <a href="/wp-admin/admin.php?page=validatore-codice-fiscale&action=delete_key">Elimina la chiave
                            API</a>
                    <?php else: ?>

                    <?php endif; ?>
                </div>


                <?php
            });
        });
    }

    public function addAdminOrderField()
    {
        // Admin orders Billing codice_fiscale editable field and display
        add_filter('woocommerce_admin_billing_fields', function ($fields) {
            $fields['_billing_fiscal_code'] = array('label' => __('Codice Fiscale', 'woocommerce'));
            return $fields;
        });

        //inserisco il codice fiscale nel back end
        add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {
            echo '<p><strong>' . __('Codice Fiscale') . ':</strong> ' . get_post_meta($order->get_id(), '_billing_fiscal_code', true) . '</p>';
        }, 10, 1);

//inserisco il codice fiscale nella mail dell'ordine
        add_filter('woocommerce_email_order_meta_keys', function ($keys) {
            $keys[] = 'Codice Fiscale';
            return $keys;
        });


    }

}
