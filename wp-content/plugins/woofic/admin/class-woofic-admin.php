<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://christiancannata.acom
 * @since      1.0.0
 *
 * @package    Woofic
 * @subpackage Woofic/admin
 */

use FattureInCloud\Api\InfoApi;
use FattureInCloud\Api\UserApi;
use FattureInCloud\ApiException;
use FattureInCloud\Configuration;
use FattureInCloud\OAuth2\OAuth2AuthorizationCodeManager;
use FattureInCloud\OAuth2\Scope;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woofic
 * @subpackage Woofic/admin
 * @author     Christian Cannata <christian@christiancannata.com>
 */
class Woofic_Admin
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
         * defined in Woofic_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woofic_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woofic-admin.css', array(), $this->version, 'all');

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
         * defined in Woofic_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woofic_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woofic-admin.js', array('jquery'), $this->version, false);

    }

    public function addMetaBox()
    {

        add_action('add_meta_boxes', function () {
            add_meta_box(
                'woofic-box',
                'FattureInCloud',
                function ($post) {
                    $invoiceId = get_post_meta($post->ID, 'woofic_invoice_id', true);
                    if ($invoiceId): ?>
                        Importato con ID <?php echo $invoiceId; ?><br>
                        <a href="https://secure.fattureincloud.it/invoices-view-<?php echo $invoiceId; ?>"
                           target="_blank">Apri su FattureInCloud</a>
                        <br><br>
                        <a href="/wp-admin/admin.php?page=import-order&order_id=<?php echo $post->ID; ?>"
                           class="button import-fic button-primary" href="#">Aggiorna manualmente</a><br><br>
                    <?php
                    else: ?>
                        <span>Non ancora importato</span><br><br>
                        <a href="/wp-admin/admin.php?page=import-order&order_id=<?php echo $post->ID; ?>"
                           class="button import-fic button-primary">Importa ora</a>
                    <?php
                    endif;
                },
                'shop_order',
                'side',
                'core'
            );
        });

    }

    public function addAdminPages()
    {

        require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';

        add_action('admin_menu', function () {

            add_menu_page('WooFic', 'WooFic', 'manage_options', 'woofic', function () {

                $woofic = new \WooFic\Services\WooficSender();

                $app_client_id = "49V34bAZi48zalpm4VJVpXdoUrFyIm5I";

                if (isset($_GET['action']) && $_GET['action'] == 'connetti') {


                    if (!get_option('woofic_access_token', false)) {
                        // Generate Access token
                        $client = new \GuzzleHttp\Client();
                        $device_code_forwfic = get_option('woofic_device_code');

                        $response = $client->post('https://api-v2.fattureincloud.it/oauth/token', [
                            'json' => [
                                'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code',
                                'client_id' => $app_client_id,
                                'device_code' => $device_code_forwfic
                            ]
                        ]);

                        $body = json_decode($response->getBody()->getContents(), true);

                        update_option('woofic_access_token', $body['access_token']);
                        update_option('woofic_refresh_token', $body['refresh_token']);

                        $now = new \DateTime();
                        $now->add(new \DateInterval('PT' . $body['expires_in'] . 'S'));

                        update_option('woofic_token_expire', $now);


                    }


                    $config = Configuration::getDefaultConfiguration()->setAccessToken(get_option('woofic_access_token'));

                    $userApi = new UserApi(null, $config);
                    $companyData = $userApi->listUserCompanies();
                    $companies = $companyData->getData()->getCompanies();
                    update_option('woofic_companies', $companies);
                    update_option('woofic_company_id', $companies[0]->getId());
                }

                /*   if (isset($_POST['woofic_client_id'])) {
                       update_option('woofic_client_id', $_POST['woofic_client_id'], true);
                       update_option('woofic_client_secret', $_POST['woofic_client_secret'], true);
                       update_option('woofic_redirect_uri', $_POST['woofic_redirect_uri'], true);

                   }*/

                if (isset($_GET['logout_woofic'])) {
                    delete_option('woofic_access_token');
                    delete_option('woofic_refresh_token');
                    delete_option('woofic_client_id');
                    delete_option('woofic_client_secret');
                    delete_option('woofic_redirect_uri');

                    delete_option('woofic_active_licence');
                    delete_option('woofic_licence_key');
                    delete_option('woofic_licence_email');

                }

                if (isset($_POST['woofic_licence_key'])) {

                    $key = $_POST['woofic_licence_key'];

                    $responseActivation = wp_remote_get(WOOFIC_ENDPOINT . '/wp-json/lmfwc/v2/licenses/activate/' . $key, [
                        'headers' => array(
                            'Authorization' => 'Basic ' . base64_encode(WOOFIC_ENDPOINT_USERNAME . ':' . WOOFIC_ENDPOINT_PASSWORD)
                        )
                    ]);

                    $response = json_decode(wp_remote_retrieve_body($responseActivation), true);

                    if ($response['code'] == 'lmfwc_rest_data_error') {

                        $responseActivation = wp_remote_get(WOOFIC_ENDPOINT . '/wp-json/lmfwc/v2/licenses/' . $key, [
                            'headers' => array(
                                'Authorization' => 'Basic ' . base64_encode(WOOFIC_ENDPOINT_USERNAME . ':' . WOOFIC_ENDPOINT_PASSWORD)
                            )
                        ]);
                        $response = json_decode(wp_remote_retrieve_body($responseActivation), true);

                    }


                    if ($response['success']) {

                        delete_option('woofic_access_token');
                        delete_option('woofic_refresh_token');
                        delete_option('woofic_client_id');
                        delete_option('woofic_client_secret');
                        delete_option('woofic_redirect_uri');

                        update_option('woofic_active_license', $response['data']);
                        update_option('woofic_license_key', $key);
                        update_option('woofic_license_email', $_POST['woofic_licence_email']);
                    }

                }

                if (isset($_GET['logout'])) {
                    delete_option('woofic_access_token');
                    delete_option('woofic_refresh_token');
                }


                $oauth = new OAuth2AuthorizationCodeManager(get_option('woofic_client_id'), get_option('woofic_client_secret'), get_option('woofic_redirect_uri'));

                $state = uniqid();
                $_SESSION['state'] = $state;
                $scopes = [
                    Scope::SETTINGS_ALL,
                    Scope::ISSUED_DOCUMENTS_INVOICES_ALL,
                    Scope::ENTITY_SUPPLIERS_ALL,
                    Scope::ENTITY_CLIENTS_ALL,
                    Scope::ISSUED_DOCUMENTS_RECEIPTS_ALL,
                    Scope::RECEIPTS_ALL
                ];
                $url = $oauth->getAuthorizationUrl($scopes, $state);


                $errorMessage = null;

                if (isset($_GET['code'])) {


                    try {

                        $params = $oauth->getParamsFromUrl($_SERVER['REQUEST_URI']);

                        $code = $params->getAuthorizationCode();
                        $state = $params->getState();

                        //@todo: check state

                        $tokenObj = $oauth->fetchToken($code);
                        $accessToken = $tokenObj->getAccessToken();
                        $refreshToken = $tokenObj->getRefreshToken();
                        $expireIn = $tokenObj->getExpiresIn();

                        $now = new \DateTime();
                        $now->add(new \DateInterval('PT' . $expireIn . 'S'));

                        update_option('woofic_access_token', $accessToken);
                        update_option('woofic_refresh_token', $refreshToken);
                        update_option('woofic_token_expire', $now);

                        $config = Configuration::getDefaultConfiguration()->setAccessToken(get_option('woofic_access_token'));

                        $userApi = new UserApi(null, $config);
                        $companyData = $userApi->listUserCompanies();
                        $companies = $companyData->getData()->getCompanies();
                        update_option('woofic_companies', $companies);
                        update_option('woofic_company_id', $companies[0]->getId());

                    } catch (\Exception $e) {
                    }

                }

                $clientId = null;
                $clientSecret = null;


                $config = $woofic->getConfig();
                $apiInstance = new UserApi(
                    null,
                    $config
                );

                try {
                    //$result = $apiInstance->getUserInfo();
                } catch (Exception $e) {

                }

                $accessToken = get_option('woofic_access_token');

                $wooficLicenceKey = get_option('woofic_license_key');
                $wooficLicence = get_option('woofic_active_license');

                $deviceCode = null;


                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://api-v2.fattureincloud.it/oauth/device');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "\n{\n  \"client_id\": \"$app_client_id\",\n  \"scope\": \"situation:r entity.clients:a issued_documents.invoices:a issued_documents.receipts:a receipts:a archive:a emails:r settings:a\"\n}\n\n");

                $headers = array();
                $headers[] = 'Accept: application/json';
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);

                $result_decoded = json_decode($result, true);


                $device_code_forwfic = $result_decoded['data']['device_code'];
                $wfic_user_code = $result_decoded['data']['user_code'];
                update_option('woofic_device_code', $device_code_forwfic);

                $wooficLicenceKey = get_option('woofic_license_key');
                $wooficLicence = get_option('woofic_active_license');

                ?>

                <div class="wrap">

                    <h2>WooFic</h2>

                    <div id="tabs" class="settings-tab">

                        <?php include(plugin_dir_path(__FILE__) . 'partials/menu.php') ?>


                    </div>

                    <div class="metabox-holder">

                        <?php

                        //echo "Device Code = " . $device_code_forwfic  ."<br>";
                        echo "<p><b>1)</b> Prendi lo User Code = <b><span style='background: white; padding: 0.30em 0.80em'>" . $result_decoded['data']['user_code'] . "</span></b></p>";


                        echo "<p><b> 2)</b> Vai a questo indirizzo <a href='https://secure.fattureincloud.it/connetti' onClick=\"MyWindow=window.open('https://secure.fattureincloud.it/connetti','wfic_connection','width=600,height=700'); return false;\">https://secure.fattureincloud.it/connetti</a> </p>

<p> <b>3)</b> inserisci lo User Code <b><span style='background: white; padding: 0.30em 0.80em'>" . $result_decoded['data']['user_code'] . "</span></b></p>

<p> <b>4)</b>   clicca su <b><span style='background: lightblue; padding: 0.30em 0.80em'>Continua</span></b></p>

<p> <b> 5) </b> e poi Clicca su <b><span style='background: lightblue; padding: 0.30em 0.80em'>Autorizza</span></b></p>

<p><b>6)</b> torna qui e Clicca sul tab <b><span style='background: lightgrey; padding: 0.30em 0.80em'><a href=\"?page=woofic&action=connetti\">Connetti </a></span></b> </p>";

                        echo "<hr>";
                        ?>

                        <div class="postbox" style="max-width: inherit">
                            <h2 class="title">Chiave di Licenza WooFic</h2>

                            <?php if (!$wooficLicence): ?>
                                <form method="POST" action="">
                                    <p>
                                        Il tuo sito non è collegato con WooFic, inserisci la tua chiave di licenza
                                        ricevuta.
                                    </p>
                                    <label>Chiave di Licenza</label><br>
                                    <input autocomplete="off" style="width:100%" type="text" name="woofic_licence_key"
                                           required

                                        <?php if (get_option('woofic_licence_key')): ?>
                                            value="<?php echo get_option('woofic_licence_key'); ?>"
                                        <?php endif; ?>
                                    >
                                    <br><br>
                                    <label>Email di acquisto della licenza</label><br>
                                    <input autocomplete="off" style="width:100%" type="text" name="woofic_licence_email"
                                           required
                                        <?php if (get_option('woofic_licence_email')): ?>
                                            value="<?php echo get_option('woofic_licence_email'); ?>"
                                        <?php endif; ?>
                                    >
                                    <br><br>
                                    <button class="button-primary" type="submit">Attiva la tua licenza</button>

                                </form>
                            <?php else: ?>
                                <p>Correttamente collegato a Woofic</p>
                                <br><br>
                                <table>
                                    <tr>
                                        <td><b>Chiave di licenza</b></td>
                                        <td><?php echo $wooficLicence['licenseKey'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Email</b></td>
                                        <td><?php echo get_option('woofic_license_email') ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Attivata il</b></td>
                                        <?php if ($wooficLicence['createdAt']): ?>
                                            <td> Attiva
                                                dal <?php echo (new \DateTime($wooficLicence['createdAt']))->format("d-m-Y") ?> </td>
                                        <?php else: ?>
                                            <td>Non ancora attivata</td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td><b>Scade il</b></td>
                                        <td><?php echo (new \DateTime($wooficLicence['expiresAt']))->format("d-m-Y") ?></td>
                                    </tr>
                                </table><br><br>

                                <a href="/wp-admin/admin.php?logout_woofic=1&page=woofic" class="button-primary">Disconnetti
                                    da
                                    WooFic</a>
                            <?php endif; ?>
                        </div>


                        <?php if ($wooficLicenceKey): ?>
                            <div class="postbox" style="max-width: inherit">
                                <h2 class="title">Configurazione del Client</h2>

                                <p>
                                    Collega la tua Zoho APP al tuo sito Wordpress, per fare questo <a
                                            href="https://www.zoho.com/accounts/protocol/oauth-setup.html"
                                            target="_blank">leggi le
                                        istruzioni in
                                        questo link</a> e crea la tua app.<br>
                                <ol>

                                </ol>
                                </p>

                                <form method="POST" action="">

                                    <label>Client ID</label><br>
                                    <input autocomplete="off" style="width:100%" type="text" name="woofic_client_id"
                                           required

                                        <?php if (get_option('woofic_client_id')): ?>
                                            value="<?php echo get_option('woofic_client_id') ?>"
                                        <?php endif; ?>
                                    >
                                    <br><br>
                                    <label>Client Secret</label><br>
                                    <input autocomplete="off" style="width:100%" type="password"
                                           name="woofic_client_secret"
                                           required

                                        <?php if (get_option('woofic_client_secret')): ?>
                                            value="<?php echo get_option('woofic_client_secret'); ?>"
                                        <?php endif; ?>
                                    >
                                    <br><br>

                                    <label>Redirect URI</label><br>
                                    <input readonly autocomplete="off" style="width:100%" type="text"
                                           name="woofic_redirect_uri"
                                           required

                                           value="<?php echo get_site_url(); ?>/woofic-oauth-redirect"
                                    >
                                    <br><br>

                                    <button class="button-primary" type="submit">Salva i dati</button>

                                </form>


                            </div>
                        <?php endif; ?>
                        <?php if (get_option('woofic_client_id') && get_option('woofic_client_secret') && get_option('woofic_redirect_uri')): ?>
                            <div class="postbox" style="max-width: inherit">
                                <h2 class="title">Connessione a FattureInCloud</h2>

                                <?php if (!$args['access_token']): ?>
                                    <p>
                                        Il tuo sito non è collegato con Zoho, effettua l'accesso di seguito per
                                        completare
                                        l'integrazione
                                    </p>
                                    <a class="button-primary" target="_blank" href="<?php echo $args['auth_url']; ?>">Accedi
                                        qui</a>
                                <?php else: ?>
                                    <p>Correttamente collegato a FattureInCloud con token
                                        *******<?php echo substr($args['access_token'], -4); ?></p>

                                    <?php
                                    foreach (get_option('woofic_companies') as $company):
                                        ?>
                                        <label>
                                            <input
                                                <?php if (get_option('woofic_company_id') == $company->getId()): ?> checked <?php endif; ?>
                                                    type="radio" name="woofic_company_id"
                                                    value="<?php echo $company->getId(); ?>">
                                            <?php echo $company->getName(); ?>
                                        </label>
                                    <?php endforeach; ?>
                                    <br><br>
                                    <a href="/wp-admin/admin.php?logout=1&page=woofic" class="button-primary">Disconnetti
                                        da
                                        FattureInCloud</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>


                <?php
            });


            \add_menu_page(\__('WooFic Settings', W_TEXTDOMAIN), W_NAME, 'manage_options', W_TEXTDOMAIN, array(
                $this,
                'display_plugin_admin_page'
            ), 'dashicons-hammer', 90);


            \add_submenu_page(
                'woofic',
                'Metodi di pagamento',
                'Metodi di pagamento',
                'manage_options',
                'woofic-sync-payments-methods',
                [
                    $this,
                    'display_sync_payments_methods'
                ]);

            \add_submenu_page(
                'woofic',
                'Aliquote IVA',
                'Aliquote IVA',
                'manage_options',
                'woofic-sync-vat',
                [
                    $this,
                    'display_sync_vat'
                ]);

            \add_submenu_page(
                'woofic',
                'Avanzate',
                'Avanzate',
                'manage_options',
                'woofic-advanced',
                [
                    $this,
                    'display_advanced_page'
                ]);


            \add_submenu_page(
                null,
                'Importa Ordine',
                'manage_options',
                'manage_options',
                'import-order',
                function () {

                    if ($_GET['order_id']) {

                        $order = wc_get_order($_GET['order_id']);

                        $enabledTypes = get_option('woofic_document_types', [
                            'INVOICE',
                            'RECEIPT',
                            'CORRISPETTIVO'
                        ]);

                        //check enabled types
                        $type = $order->get_meta('_billing_type');

                        if (empty($enabledTypes) || !in_array($type, $enabledTypes)) {
                            $order->add_order_note('Errore creazione ordine su FattureInCloud:<br>Tipologia non abilitata, controllare le impostazioni nel plugin nella sezione "Avanzate"');
                            wp_redirect(wp_get_referer());
                        } else {

                            try {

                                $wooficSender = new \WooFic\Services\WooficSender();
                                $wooficSender->createInvoice($order);

                            } catch (ApiException $e) {

                                if ($e->getResponseBody()) {
                                    $error = json_decode($e->getResponseBody(), true);
                                    $message = $error['error']['message'];

                                    $errors = [];

                                    $validation = $error['error']['validation_result'];


                                    foreach ($validation as $field => $singleError) {
                                        $errors[] = $field . ": " . reset($singleError);
                                    }

                                    Errors::writeLog(
                                        [
                                            'title' => 'Errore creazione ordine #' . $_GET['order_id'] . ' su FattureInCloud',
                                            'message' => $message . "<br>" . implode("<br>", $errors)
                                        ]
                                    );


                                    $order->add_order_note('Errore creazione ordine su FattureInCloud: ' . $message . "<br>" . implode("<br>", $errors));
                                }

                            } catch (\Exception $e) {

                                error_log(
                                    implode(" - ", [
                                        'Errore creazione ordine #' . $_GET['order_id'] . ' su FattureInCloud',
                                        $e->getMessage()
                                    ])
                                );

                                $order->add_order_note('Errore creazione ordine su FattureInCloud: ' . $e->getMessage());
                            }


                            //if receipt create corrispettivo

                            if ($type == 'RECEIPT' && in_array('CORRISPETTIVO', $enabledTypes)) {

                                $wooficSender = new \WooFic\Services\WooficSender();
                                $wooficSender->createCorrispettivo($order);

                            }

                        }


                        wp_redirect(wp_get_referer());
                    }

                });


        });


    }

    public function display_sync_payments_methods()
    {

        // get payment methods from FIC

        $woofic = new \WooFic\Services\WooficSender();
        $config = $woofic->getConfig();

        $apiInstance = new InfoApi(
            null,
            $config
        );

        $company_id = get_option('woofic_company_id');

        $ficPaymentsMethods = $apiInstance->listPaymentMethods($company_id)->getData();
        $gateways = WC()->payment_gateways->get_available_payment_gateways();

        $enabled_gateways = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST['payment_methods'] as $id => $payment_method) {
                update_option('fic_' . $id, $payment_method, true);
            }
        }

        if ($gateways) {

            $gateways = array_filter($gateways, function ($gateway) {
                return $gateway->enabled == 'yes';
            });

            foreach ($gateways as $gateway) {


                if (!get_option('fic_' . $gateway->id)) {
                    $ficPaymentsMethod = array_filter($ficPaymentsMethods, function ($ficPayment) use ($gateway) {
                        return $ficPayment->getName() == $gateway->method_title;
                    });

                    if (!empty($ficPaymentsMethod)) {
                        $ficPaymentsMethod = reset($ficPaymentsMethod);
                        update_option('fic_' . $gateway->id, $ficPaymentsMethod->getId());
                    }
                }


                $enabled_gateways[] = [
                    'id' => $gateway->id,
                    'type' => $gateway->id,
                    'name' => $gateway->method_title,
                    'fic_id' => get_option('fic_' . $gateway->id)
                ];

            }
        }


        $args = [
            'payments_methods' => $enabled_gateways,
            'active_route' => 'sync-payments-methods',
            'fic_payments_methods' => $ficPaymentsMethods
        ];


        ?>

        <div class="wrap">

            <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

            <div id="tabs" class="settings-tab">
                <?php include(plugin_dir_path(__FILE__) . 'partials/menu.php') ?>


                <div id="<?php echo $args['active_route']; ?>" class="metabox-holder">


                    <div class="postbox" style="max-width: inherit">
                        <h2 class="title">Metodi di pagamento</h2>

                        <p>
                            <?php
                            if (empty($args['fic_payments_methods'])):
                            ?>
                        <div class="alert alert-danger">Non ci sono metodi di pagamento su FattureInCloud!</div>
                        <?php
                        endif;
                        ?>

                        </p>
                        <form method="POST" action="">
                            <table class="table">
                                <thead>
                                <th>Metodo di pagamento WooCommerce</th>
                                <th>Metodo di pagamento FattureInCloud</th>
                                <th></th>
                                </thead>
                                <tbody>
                                <?php foreach ($args['payments_methods'] as $payment): ?>
                                    <tr>
                                        <td><?php echo $payment['name']; ?></td>
                                        <td>
                                            <select autocomplete="off"
                                                    name="payment_methods[<?php echo $payment['id']; ?>]"
                                                    class="form-control">
                                                <?php foreach ($args['fic_payments_methods'] as $paymentMethod): ?>
                                                    <option value="<?php echo $paymentMethod->getId() ?>"
                                                        <?php if ($paymentMethod->getId() == $payment['fic_id']): ?> selected <?php endif; ?>
                                                    ><?php echo $paymentMethod->getName() ?></option>
                                                <?php endforeach; ?>
                                                <option value="0"
                                                    <?php if ($paymentMethod->getId() == 0): ?> selected <?php endif; ?>
                                                >Disabilita invio su Fatture in Cloud
                                                </option>
                                            </select>

                                        </td>
                                        <td>
                                            <?php
                                            if (empty($args['fic_payments_methods']) || !$payment['fic_id']): ?>
                                                <div class="alert alert-danger">Associa questo metodo di pagamento con
                                                    una scelta dall'elenco.
                                                </div>
                                            <?php
                                            endif;
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>

                            </table>

                            <button class="button button-primary button-large">Salva e sincronizza</button>
                        </form>


                    </div>


                </div>

            </div>

        </div>


        <?php

    }


    public function display_advanced_page()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            update_option('selected_account_payment_id', $_POST['account_payment_id']);
            update_option('woofic_suffix', $_POST['woofic_suffix']);
            update_option('woofic_document_types', $_POST['woofic_document_types']);
        }

        $wooficSender = new \WooFic\Services\WooficSender();
        $config = $wooficSender->getConfig();

        $apiInstance = new InfoApi(
            null,
            $config
        );

        $company_id = get_option('woofic_company_id');

        $result = $apiInstance->listPaymentAccounts($company_id);

        $orderStatuses = wc_get_order_statuses();
        unset($orderStatuses['wc-pending']);
        unset($orderStatuses['wc-refunded']);
        unset($orderStatuses['wc-failed']);
        unset($orderStatuses['wc-checkout-draft']);
        unset($orderStatuses['wc-cancelled']);

        $args = [
            'active_route' => 'advanced',
            'woofic_prefix' => get_option('woofic_prefix'),
            'payments_accounts' => $result->getData(),
            'selected_account_payment_id' => get_option('selected_account_payment_id'),
            'order_statuses' => $orderStatuses,
            'fic_automatic_status' => get_option('fic_automatic_status', 0),
            'woofic_document_types' => get_option('woofic_document_types', [
                'INVOICE',
                'RECEIPT',
                'CORRISPETTIVI'
            ])
        ];


        ?>
        <div class="wrap">

            <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

            <div id="tabs" class="settings-tab">
                <?php include(plugin_dir_path(__FILE__) . 'partials/menu.php') ?>


                <div id="<?php echo $args['active_route']; ?>" class="metabox-holder">


                    <div class="postbox">
                        <form method="POST" action="">

                            <h2 class="title">Conto di Saldo collegato</h2>
                            <p>
                                Seleziona il conto da utilizzare per registrare le tue fatture su FattureInCloud.
                            </p>
                            <?php foreach ($args['payments_accounts'] as $payment): ?>
                                <label>
                                    <input type="radio" name="account_payment_id"
                                           value="<?php echo $payment->getId() ?>"
                                        <?php if ($args['selected_account_payment_id'] == $payment->getId() || !$args['selected_account_payment_id']): ?> checked <?php endif; ?>
                                    >
                                    <?php echo $payment->getName(); ?>
                                </label><br><br>
                            <?php endforeach; ?>

                            <br><br>
                            <h2 class="title">Suffisso numerazione della fattura</h2>
                            <p>
                                Se vuoi inserire un prefisso alla numerazione delle tue fatture su FattureInCloud,
                                compila il
                                campo di testo
                                inserendo il valore che preferisci.
                            </p>
                            <input type="text" name="woofic_suffix" placeholder="/E"
                                   <?php if ($args['woofic_suffix']): ?>value="<?php echo $args['woofic_suffix']; ?>"<?php endif; ?>
                            >
                            <br><br>

                            <br><br>
                            <h2 class="title">Creazione automatica su FattureInCloud</h2>
                            <p>

                            </p>

                            <select autocomplete="off" name="fic_automatic_status"
                                    class="form-control">
                                <option value="0"
                                    <?php if ($args['fic_automatic_status'] == 0): ?> selected <?php endif; ?>
                                >Invio automatico disabilitato
                                </option>
                                <?php foreach ($args['order_statuses'] as $value => $order_status): ?>
                                    <option value="<?php echo $value; ?>"
                                        <?php if ($value === $args['fic_automatic_status']): ?> selected <?php endif; ?>
                                    >Quando l'ordine va in stato "<?php echo $order_status ?>"
                                    </option>
                                <?php endforeach; ?>

                            </select>

                            <br><br>
                            <h2 class="title">Documenti da creare su FattureInCloud</h2>
                            <p>

                            </p>


                            <label>
                                <input type="checkbox" name="woofic_document_types[]" value="INVOICE"
                                    <?php if (in_array('INVOICE', $args['woofic_document_types'])): ?> checked <?php endif; ?>
                                >
                                Fatture
                            </label><br><br>
                            <label>
                                <input type="checkbox" name="woofic_document_types[]" value="RECEIPT"
                                    <?php if (in_array('RECEIPT', $args['woofic_document_types'])): ?> checked <?php endif; ?>

                                >
                                Ricevute
                            </label><br><br>
                            <label>
                                <input type="checkbox" name="woofic_document_types[]" value="CORRISPETTIVO"
                                    <?php if (in_array('CORRISPETTIVO', $args['woofic_document_types'])): ?> checked <?php endif; ?>
                                >
                                Corrispettivi
                            </label><br><br>

                            <br>
                            <button class="button button-primary button-large">Salva</button>


                        </form>
                    </div>
                </div>

            </div>

        </div>

        <?php
    }

    public function display_sync_vat()
    {


        // get payment methods from FIC


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST['aliquote'] as $id => $aliquota) {
                update_option('fic_aliquota_' . $id, $aliquota);
            }
        }

        $woofic = new \WooFic\Services\WooficSender();

        $ficAliquote = $woofic->getVatTypes();


        $all_tax_rates = [];
        $tax_classes = \WC_Tax::get_tax_classes(); // Retrieve all tax classes.
        if (!in_array('', $tax_classes)) { // Make sure "Standard rate" (empty class name) is present.
            array_unshift($tax_classes, '');
        }
        foreach ($tax_classes as $tax_class) { // For each tax class, get all rates.
            $taxes = \WC_Tax::get_rates_for_tax_class($tax_class);
            $taxes = array_map(function ($tax) {
                $tax->fic_id = get_option('fic_aliquota_' . $tax->tax_rate_id, true);

                return $tax;
            }, $taxes);
            $all_tax_rates = array_merge($all_tax_rates, $taxes);
        }

        $args = [
            'aliquote' => $all_tax_rates,
            'active_route' => 'sync-vat',
            'fic_aliquote' => $ficAliquote
        ];


        ?>
        <div class="wrap">

            <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

            <div id="tabs" class="settings-tab">
                <?php include(plugin_dir_path(__FILE__) . 'partials/menu.php') ?>


                <div id="<?php echo $args['active_route']; ?>" class="metabox-holder">


                    <div class="postbox" style="max-width: inherit">
                        <h2 class="title">Aliquote IVA</h2>

                        <p>

                        </p>
                        <form method="POST" action="">
                            <table class="table">
                                <thead>
                                <th>Nazione</th>
                                <th>Aliquota</th>
                                <th>Aliquota WooCommerce</th>
                                <th>Aliquota FattureInCloud</th>
                                </thead>
                                <tbody>
                                <?php foreach ($args['aliquote'] as $tax): ?>
                                    <tr>
                                        <td><?php echo $tax->tax_rate_country; ?></td>
                                        <td><?php echo number_format($tax->tax_rate, 2); ?>%</td>
                                        <td><?php echo $tax->tax_rate_name; ?></td>
                                        <td>
                                            <select autocomplete="off" name="aliquote[<?php echo $tax->tax_rate_id; ?>]"
                                                    class="form-control">
                                                <?php foreach ($args['fic_aliquote'] as $fixAliquota): ?>
                                                    <option value="<?php echo $fixAliquota->getId() ?>"
                                                        <?php if ($fixAliquota->getId() == $tax->fic_id): ?> selected <?php endif; ?>
                                                    ><?php echo $fixAliquota->getValue() . '% ' . $fixAliquota->getDescription(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>

                            </table>

                            <button class="button button-primary button-large">Salva e sincronizza</button>
                        </form>


                    </div>


                </div>

            </div>

        </div>

        <?php
    }


    public function checkoutField()
    {

        add_filter('manage_edit-shop_order_columns', function ($columns) {
            $new_columns = array();
            foreach ($columns as $column_name => $column_info) {
                $new_columns[$column_name] = $column_info;
                if ('order_total' === $column_name) {
                    $new_columns['fic'] = 'Fattura / Ricevuta';
                    $new_columns['tipo_utente'] = 'Tipo Utente';
                }
            }

            return $new_columns;
        });


        add_action('manage_shop_order_posts_custom_column', function ($column) {
            global $post;
            if ('fic' === $column) {
                $billingType = get_post_meta($post->ID, '_billing_type', true);
                $billingType = $billingType == 'INVOICE' ? 'FATTURA' : 'RICEVUTA';

                echo esc_html_e($billingType, W_TEXTDOMAIN);

                return true;
            }

            if ('tipo_utente' === $column) {
                $customerType = get_post_meta($post->ID, '_billing_customer_type', true);
                $customerType = $customerType == 'COMPANY' ? 'AZIENDA' : 'PRIVATO';
                echo esc_html_e($customerType, W_TEXTDOMAIN);

                return true;
            }

        });

        add_action('woocommerce_order_status_changed', function ($order_id, $old_status, $new_status) {

            if ($new_status == get_option('woofic_order_status_triggered', 'completed')) {
                $order = wc_get_order($order_id);
                $wooficSender = new WooficSender();
                $wooficSender->createInvoice($order);
            }

        }, 10, 3);

    }


}
