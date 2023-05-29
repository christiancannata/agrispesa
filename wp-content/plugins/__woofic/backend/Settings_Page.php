<?php

/**
 * WooFic
 *
 * @package   WooFic
 * @author    Christian Cannata <christian@christiancannata.com>
 * @copyright 2022 Christian Cannata
 * @license   GPL 2.0+
 * @link      https://christiancannata.com
 */

namespace WooFic\Backend;

use FattureInCloud\Api\InfoApi;
use FattureInCloud\Api\UserApi;
use FattureInCloud\ApiException;
use FattureInCloud\Configuration;
use FattureInCloud\OAuth2\OAuth2AuthorizationCodeManager;
use FattureInCloud\OAuth2\Scope;
use Woofic\Common\Utils\Errors;
use WooFic\Engine\Base;
use WooFic\Services\WooficSender;

/**
 * Create the settings page in the backend
 */
class Settings_Page extends Base {

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize() {
		if ( ! parent::initialize() ) {
			return;
		}

		// Add the options page and menu item.
		\add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		$realpath        = (string) \realpath( \dirname( __FILE__ ) );
		$plugin_basename = \plugin_basename( \plugin_dir_path( $realpath ) . W_TEXTDOMAIN . '.php' );
		\add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );


		add_action( 'add_meta_boxes', function () {
			add_meta_box(
					'woofic-box',
					'FattureInCloud',
					function ( $post ) {
						$invoiceId = get_post_meta( $post->ID, 'woofic_invoice_id', true );
						if ( $invoiceId ): ?>
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
		} );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function add_plugin_admin_menu() {
		/*
		 * Add a settings page for this plugin to the Settings menu
		 *
		 * @TODO:
		 *
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities

		add_options_page( __( 'Page Title', W_TEXTDOMAIN ), W_NAME, 'manage_options', W_TEXTDOMAIN, array( $this, 'display_plugin_admin_page' ) );
		 *
		 */
		/*
		 * Add a settings page for this plugin to the main menu
		 *
		 */
		\add_menu_page( \__( 'WooFic Settings', W_TEXTDOMAIN ), W_NAME, 'manage_options', W_TEXTDOMAIN, array(
				$this,
				'display_plugin_admin_page'
		), 'dashicons-hammer', 90 );


		\add_submenu_page(
				W_TEXTDOMAIN,
				'Metodi di pagamento',
				'Metodi di pagamento',
				'manage_options',
				'woofic-sync-payments-methods',
				[
						$this,
						'display_sync_payments_methods'
				] );

		\add_submenu_page(
				W_TEXTDOMAIN,
				'Aliquote IVA',
				'Aliquote IVA',
				'manage_options',
				'woofic-sync-vat',
				[
						$this,
						'display_sync_vat'
				] );

		\add_submenu_page(
				W_TEXTDOMAIN,
				'Avanzate',
				'Avanzate',
				'manage_options',
				'woofic-advanced',
				[
						$this,
						'display_advanced_page'
				] );


		\add_submenu_page(
				null,
				'Importa Ordine',
				'manage_options',
				'manage_options',
				'import-order',
				function () {

					if ( $_GET['order_id'] ) {

						$order = wc_get_order( $_GET['order_id'] );

						$enabledTypes = get_option( 'woofic_document_types', [
								'INVOICE',
								'RECEIPT',
								'CORRISPETTIVI'
						] );

						//check enabled types
						$type = $order->get_meta( '_billing_type' );


						if ( empty( $enabledTypes ) || ! in_array( $type, $enabledTypes ) ) {
							$order->add_order_note( 'Errore creazione ordine su FattureInCloud:<br>Tipologia non abilitata, controllare le impostazioni nel plugin nella sezione "Avanzate"' );
							wp_redirect( wp_get_referer() );
						} else {

							try {

								$wooficSender = new WooficSender();
								$wooficSender->createInvoice( $order );

							} catch ( ApiException $e ) {

								if ( $e->getResponseBody() ) {
									$error   = json_decode( $e->getResponseBody(), true );
									$message = $error['error']['message'];

									$errors = [];

									$validation = $error['error']['validation_result'];


									foreach ( $validation as $field => $singleError ) {
										$errors[] = $field . ": " . reset( $singleError );
									}

									Errors::writeLog(
											[
													'title'   => 'Errore creazione ordine #' . $_GET['order_id'] . ' su FattureInCloud',
													'message' => $message . "<br>" . implode( "<br>", $errors )
											]
									);


									$order->add_order_note( 'Errore creazione ordine su FattureInCloud: ' . $message . "<br>" . implode( "<br>", $errors ) );
								}

							} catch ( \Exception $e ) {

								error_log(
										implode( " - ", [
												'Errore creazione ordine #' . $_GET['order_id'] . ' su FattureInCloud',
												$e->getMessage()
										] )
								);

								$order->add_order_note( 'Errore creazione ordine su FattureInCloud: ' . $e->getMessage() );
							}


							//if receipt create corrispettivo

							if ( $type == 'RECEIPT' && in_array( 'CORRISPETTIVO', $enabledTypes ) ) {

								$wooficSender = new WooficSender();
								$wooficSender->createCorrispettivo( $order );

							}

						}


						wp_redirect( wp_get_referer() );
					}

				} );


	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function display_plugin_admin_page() {


		if ( isset( $_POST['woofic_client_id'] ) ) {
			update_option( 'woofic_client_id', $_POST['woofic_client_id'], true );
			update_option( 'woofic_client_secret', $_POST['woofic_client_secret'], true );
			update_option( 'woofic_redirect_uri', $_POST['woofic_redirect_uri'], true );

		}

		if ( isset( $_GET['logout_woofic'] ) ) {
			delete_option( 'woofic_access_token' );
			delete_option( 'woofic_refresh_token' );
			delete_option( 'woofic_client_id' );
			delete_option( 'woofic_client_secret' );
			delete_option( 'woofic_redirect_uri' );

			delete_option( 'woofic_active_licence' );
			delete_option( 'woofic_licence_key' );
			delete_option( 'woofic_licence_email' );

		}

		if ( isset( $_POST['woofic_licence_key'] ) ) {

			$key = $_POST['woofic_licence_key'];

			$responseActivation = wp_remote_get( WOOFIC_ENDPOINT . '/wp-json/lmfwc/v2/licenses/activate/' . $key, [
					'headers' => array(
							'Authorization' => 'Basic ' . base64_encode( WOOFIC_ENDPOINT_USERNAME . ':' . WOOFIC_ENDPOINT_PASSWORD )
					)
			] );

			$response = json_decode( wp_remote_retrieve_body( $responseActivation ), true );

			if ( $response['code'] == 'lmfwc_rest_data_error' ) {

				$responseActivation = wp_remote_get( WOOFIC_ENDPOINT . '/wp-json/lmfwc/v2/licenses/' . $key, [
						'headers' => array(
								'Authorization' => 'Basic ' . base64_encode( WOOFIC_ENDPOINT_USERNAME . ':' . WOOFIC_ENDPOINT_PASSWORD )
						)
				] );
				$response           = json_decode( wp_remote_retrieve_body( $responseActivation ), true );

			}


			if ( $response['success'] ) {

				delete_option( 'woofic_access_token' );
				delete_option( 'woofic_refresh_token' );
				delete_option( 'woofic_client_id' );
				delete_option( 'woofic_client_secret' );
				delete_option( 'woofic_redirect_uri' );

				update_option( 'woofic_active_license', $response['data'] );
				update_option( 'woofic_license_key', $key );
				update_option( 'woofic_license_email', $_POST['woofic_licence_email'] );
			}

		}

		if ( isset( $_GET['logout'] ) ) {
			delete_option( 'woofic_access_token' );
			delete_option( 'woofic_refresh_token' );
		}


		$oauth = new OAuth2AuthorizationCodeManager( get_option( 'woofic_client_id' ), get_option( 'woofic_client_secret' ), get_option( 'woofic_redirect_uri' ) );

		$state             = uniqid();
		$_SESSION['state'] = $state;
		$scopes            = [
				Scope::SETTINGS_ALL,
				Scope::ISSUED_DOCUMENTS_INVOICES_ALL,
				Scope::ENTITY_SUPPLIERS_ALL,
				Scope::ENTITY_CLIENTS_ALL,
				Scope::ISSUED_DOCUMENTS_RECEIPTS_ALL,
				Scope::RECEIPTS_ALL
		];
		$url               = $oauth->getAuthorizationUrl( $scopes, $state );


		$errorMessage = null;

		if ( isset( $_GET['code'] ) ) {


			try {

				$params = $oauth->getParamsFromUrl( $_SERVER['REQUEST_URI'] );

				$code  = $params->getAuthorizationCode();
				$state = $params->getState();

				//@todo: check state

				$tokenObj     = $oauth->fetchToken( $code );
				$accessToken  = $tokenObj->getAccessToken();
				$refreshToken = $tokenObj->getRefreshToken();
				$expireIn     = $tokenObj->getExpiresIn();

				$now = new \DateTime();
				$now->add( new \DateInterval( 'PT' . $expireIn . 'S' ) );

				update_option( 'woofic_access_token', $accessToken );
				update_option( 'woofic_refresh_token', $refreshToken );
				update_option( 'woofic_token_expire', $now );

				$config = Configuration::getDefaultConfiguration()->setAccessToken( get_option( 'woofic_access_token' ) );

				$userApi     = new UserApi( null, $config );
				$companyData = $userApi->listUserCompanies();
				$companies   = $companyData->getData()->getCompanies();
				update_option( 'woofic_companies', $companies );
				update_option( 'woofic_company_id', $companies[0]->getId() );

			} catch ( \Exception $e ) {
			}

		}

		$clientId     = null;
		$clientSecret = null;


		$woofic      = new WooficSender();
		$config      = $woofic->getConfig();
		$apiInstance = new UserApi(
				null,
				$config
		);

		try {
			//$result = $apiInstance->getUserInfo();
		} catch ( Exception $e ) {

		}

		$accessToken = get_option( 'woofic_access_token' );

		$wooficLicenceKey = get_option( 'woofic_license_key' );
		$wooficLicence    = get_option( 'woofic_active_license' );


		$args = [
				'auth_url'              => $url,
				'access_token'          => $accessToken,
				'errorMessage'          => $errorMessage,
				'active_route'          => 'woofic',
				'client_id'             => $clientId,
				'client_secret'         => $clientSecret,
				'woofic_licence_key'    => $wooficLicenceKey,
				'woofic_active_licence' => $wooficLicence
		];

		include_once W_PLUGIN_ROOT . 'backend/views/summary_page.php';
	}

	public function display_advanced_page() {

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			update_option( 'selected_account_payment_id', $_POST['account_payment_id'] );
			update_option( 'woofic_suffix', $_POST['woofic_suffix'] );
			update_option( 'woofic_document_types', $_POST['woofic_document_types'] );
		}

		$wooficSender = new WooficSender();
		$config       = $wooficSender->getConfig();

		$apiInstance = new InfoApi(
				null,
				$config
		);

		$company_id = get_option( 'woofic_company_id' );

		$result = $apiInstance->listPaymentAccounts( $company_id );

		$orderStatuses = wc_get_order_statuses();
		unset( $orderStatuses['wc-pending'] );
		unset( $orderStatuses['wc-refunded'] );
		unset( $orderStatuses['wc-failed'] );
		unset( $orderStatuses['wc-checkout-draft'] );
		unset( $orderStatuses['wc-cancelled'] );

		$args = [
				'active_route'                => 'advanced',
				'woofic_prefix'               => get_option( 'woofic_prefix' ),
				'payments_accounts'           => $result->getData(),
				'selected_account_payment_id' => get_option( 'selected_account_payment_id' ),
				'order_statuses'              => $orderStatuses,
				'fic_automatic_status'        => get_option( 'fic_automatic_status', 0 ),
				'woofic_document_types'       => get_option( 'woofic_document_types', [
						'INVOICE',
						'RECEIPT',
						'CORRISPETTIVI'
				] )
		];

		include_once W_PLUGIN_ROOT . 'backend/views/advanced_page.php';
	}

	public function display_sync_vat() {


		// get payment methods from FIC


		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			foreach ( $_POST['aliquote'] as $id => $aliquota ) {
				update_option( 'fic_aliquota_' . $id, $aliquota );
			}
		}

		$woofic = new WooficSender();

		$ficAliquote = $woofic->getVatTypes();


		$all_tax_rates = [];
		$tax_classes   = \WC_Tax::get_tax_classes(); // Retrieve all tax classes.
		if ( ! in_array( '', $tax_classes ) ) { // Make sure "Standard rate" (empty class name) is present.
			array_unshift( $tax_classes, '' );
		}
		foreach ( $tax_classes as $tax_class ) { // For each tax class, get all rates.
			$taxes         = \WC_Tax::get_rates_for_tax_class( $tax_class );
			$taxes         = array_map( function ( $tax ) {
				$tax->fic_id = get_option( 'fic_aliquota_' . $tax->tax_rate_id, true );

				return $tax;
			}, $taxes );
			$all_tax_rates = array_merge( $all_tax_rates, $taxes );
		}

		$args = [
				'aliquote'     => $all_tax_rates,
				'active_route' => 'sync-vat',
				'fic_aliquote' => $ficAliquote
		];


		include_once W_PLUGIN_ROOT . 'backend/views/sync_vat_page.php';
	}


	public function display_sync_payments_methods() {


		// get payment methods from FIC

		$woofic = new WooficSender();
		$config = $woofic->getConfig();

		$apiInstance = new InfoApi(
				null,
				$config
		);

		$company_id = get_option( 'woofic_company_id' );

		$ficPaymentsMethods = $apiInstance->listPaymentMethods( $company_id )->getData();
		$gateways           = WC()->payment_gateways->get_available_payment_gateways();

		$enabled_gateways = [];

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			foreach ( $_POST['payment_methods'] as $id => $payment_method ) {
				update_option( 'fic_' . $id, $payment_method, true );
			}
		}

		if ( $gateways ) {

			$gateways = array_filter( $gateways, function ( $gateway ) {
				return $gateway->enabled == 'yes';
			} );

			foreach ( $gateways as $gateway ) {


				if ( ! get_option( 'fic_' . $gateway->id ) ) {
					$ficPaymentsMethod = array_filter( $ficPaymentsMethods, function ( $ficPayment ) use ( $gateway ) {
						return $ficPayment->getName() == $gateway->method_title;
					} );

					if ( ! empty( $ficPaymentsMethod ) ) {
						$ficPaymentsMethod = reset( $ficPaymentsMethod );
						update_option( 'fic_' . $gateway->id, $ficPaymentsMethod->getId() );
					}
				}


				$enabled_gateways[] = [
						'id'     => $gateway->id,
						'type'   => $gateway->id,
						'name'   => $gateway->method_title,
						'fic_id' => get_option( 'fic_' . $gateway->id )
				];

			}
		}


		$args = [
				'payments_methods'     => $enabled_gateways,
				'active_route'         => 'sync-payments-methods',
				'fic_payments_methods' => $ficPaymentsMethods
		];


		include_once W_PLUGIN_ROOT . 'backend/views/payments_methods_page.php';
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @param array $links Array of links.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function add_action_links( array $links ) {
		return \array_merge(
				array(
						'settings' => '<a href="' . \admin_url( 'options-general.php?page=' . W_TEXTDOMAIN ) . '">' . \__( 'Settings', W_TEXTDOMAIN ) . '</a>',
						'donate'   => '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=danielemte90@alice.it&item_name=Donation">' . \__( 'Donate', W_TEXTDOMAIN ) . '</a>',
				),
				$links
		);
	}

}
