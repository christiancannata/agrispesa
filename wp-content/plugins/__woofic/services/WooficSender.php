<?php
/**
 * WooFic
 *
 * @package   woofic
 * @author    Christian Cannata <christian@christiancannata.com>
 * @copyright 2022 WooFic
 * @license   MIT
 * @link      https://christiancannata.com
 */

declare( strict_types=1 );

namespace WooFic\Services;


use FattureInCloud\Api\InfoApi;
use FattureInCloud\Api\IssuedDocumentsApi;
use FattureInCloud\Api\ReceiptsApi;
use FattureInCloud\ApiException;
use FattureInCloud\Configuration;
use FattureInCloud\Model\CreateIssuedDocumentRequest;
use FattureInCloud\Model\CreateReceiptRequest;
use FattureInCloud\Model\Currency;
use FattureInCloud\Model\Entity;
use FattureInCloud\Model\IssuedDocument;
use FattureInCloud\Model\IssuedDocumentEiData;
use FattureInCloud\Model\IssuedDocumentItemsListItem;
use FattureInCloud\Model\IssuedDocumentOptions;
use FattureInCloud\Model\IssuedDocumentPaymentsListItem;
use FattureInCloud\Model\IssuedDocumentStatus;
use FattureInCloud\Model\IssuedDocumentType;
use FattureInCloud\Model\Language;
use FattureInCloud\Model\ModifyIssuedDocumentRequest;
use FattureInCloud\Model\ModifyReceiptRequest;
use FattureInCloud\Model\PaymentAccount;
use FattureInCloud\Model\PaymentMethod;
use FattureInCloud\Model\Receipt;
use FattureInCloud\Model\ReceiptItemsListItem;
use FattureInCloud\Model\ReceiptType;
use FattureInCloud\Model\VatType;
use FattureInCloud\OAuth2\OAuth2AuthorizationCodeManager;

/**
 * Utility to show prettified wp_die errors, write debug logs as
 * string or array and to deactivate plugin and print a notice
 *
 * @package Woofic\Config
 * @since 1.0.0
 */
class WooficSender {


	public function createInvoice( \WC_Order $order ) {

		if ( ! $this->__vl() ) {
			throw new \Exception( 'Licenza WooFic non valida o scaduta.' );
		}


		$type = $order->get_meta( '_billing_type' );

		$typeLabel = 'fattura';
		if ( $type == 'RECEIPT' ) {
			$typeLabel = 'ricevuta';
		}

		if ( ! $order ) {
			//@todo: throw error
		}

		//check payment method
		$ficPaymentMethod = get_option( 'fic_' . $order->get_payment_method() );
		if ( ! $ficPaymentMethod ) {

		}

		$company_id = get_option( 'woofic_company_id' );

		$config = $this->getConfig();

		$paymentMethodCode = $this->getOrderPaymentMethod( $order );


		$invoice = new IssuedDocument();

		if ( $type == 'INVOICE' ) {
			$invoice->setType( IssuedDocumentType::INVOICE );
		}

		if ( $type == 'RECEIPT' ) {
			$invoice->setType( IssuedDocumentType::RECEIPT );
		}


		$vatCode = $order->get_meta( '_billing_vat' );
		$pec     = $order->get_meta( '_billing_pec' );
		$sdi     = $order->get_meta( '_billing_sdi' );
		$taxCode = $order->get_meta( '_billing_tax_code' );

		$companyName = $order->get_billing_company();

		if ( ! $companyName ) {
			$companyName = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
		}

		$country = WC()->countries->countries[ $order->get_billing_country() ];

		$entity = new Entity();
		$entity
			->setName( $companyName )
			->setVatNumber( $vatCode )
			->setEmail( $order->get_billing_email() )
			->setCertifiedEmail( $pec )
			->setTaxCode( $taxCode )
			->setAddressStreet( $order->get_billing_address_1() )
			->setAddressPostalCode( $order->get_billing_postcode() )
			->setAddressCity( $order->get_billing_city() )
			->setAddressProvince( $order->get_billing_state() )
			->setCountry( $country );


		$invoice->setEntity( $entity );
		$invoice->setEiData(
			new IssuedDocumentEiData(
				array(
					"payment_method" => $paymentMethodCode
				)
			)
		);

		$cart = [];

		foreach ( $order->get_items() as $item ) {


			// Get an instance of the WC_Tax object
			$tax_obj = new \WC_Tax();

			// Get the tax data from customer location and product tax class
			$tax_rates_data = $tax_obj->find_rates( array(
				'country'   => $order->get_billing_country(),
				'state'     => $order->get_billing_state(),
				'city'      => $order->get_billing_city(),
				'postcode'  => $order->get_billing_postcode(),
				'tax_class' => $item->get_tax_class()
			) );

			//DEFAULT 22%
			$vatId = 0;

			$tax_rate = null;

			if ( empty( $tax_rates_data ) ) {
				//throw new \Exception( 'IVA non trovata' );
				$tax_rates_data = [
					[
						'label' => '0%'
					]
				];
			}

			$tax_rate = reset( $tax_rates_data );

			$taxes = \WC_Tax::get_rates_for_tax_class( $item->get_tax_class() );

			$selectedTax = null;

			foreach ( $taxes as $tax ) {
				if ( $tax_rate['label'] == $tax->tax_rate_name ) {
					$selectedTax = $tax;
				}
			}


			// Finally we get the tax rate (percentage number) and display it:

			$ficVatCode = null;

			if ( $selectedTax ) {
				$ficVatCode = get_option( 'fic_aliquota_' . $selectedTax->tax_rate_id, true );
			}


			if ( $ficVatCode == null ) {
				$taxRates = $this->getVatTypes();


				$ficTaxRate = array_filter( $taxRates, function ( VatType $rate ) use ( $tax_rate ) {
					return $rate->getValue() == $tax_rate['label'];
				} );

				$ficTaxRate = reset( $ficTaxRate );

				if ( ! $ficTaxRate ) {
					throw new \Exception( 'IVA ' . $tax_rate['label'] . ' non trovata su FattureInCloud' );
				}

				$vatId = $ficTaxRate->getId();

				$taxRateId = 0;
				if ( $selectedTax ) {
					$taxRateId = $selectedTax->tax_rate_id;
				}
				update_option( 'fic_aliquota_' . $taxRateId, $vatId );

				$ficVatCode = get_option( 'fic_aliquota_' . $taxRateId, true );
			}

			$price  = get_post_meta( $item->get_product()->get_id(), '_price', true );
			$cart[] = new IssuedDocumentItemsListItem(
				[
					"name"      => $item->get_name(),
					"net_price" => $price,
					"qty"       => $item->get_quantity(),
					"vat"       => new VatType(
						[
							"id" => $ficVatCode
						]
					)
				]
			);

		}

		$paymentMethod = new PaymentMethod( [ 'id' => get_option( 'fic_' . $order->get_payment_method(), true ) ] );

		$invoice->setItemsList( $cart );

		$suffix = get_option( 'woofic_suffix', null );

		if ( $suffix ) {
			$invoice->setNumeration( $suffix );
		}
		$invoice->setEInvoice( true );
		/*$invoice->setEiData( [
			'vat_kind' => 'D'
		] );*/
		$invoice->setPaymentMethod( $paymentMethod );
		$invoice->setSubject( 'Ordine n.' . $order->get_id() );
		$invoice->setVisibleSubject( 'Ordine n.' . $order->get_id() );

		$invoice->setCurrency(
			new Currency(
				array(
					"id" => "EUR"
				)
			)
		);

		$invoice->setLanguage(
			new Language(
				array(
					"code" => "it",
					"name" => "italiano"
				)
			)
		);

		$paymentDate = $order->get_date_paid();
		if ( $paymentDate && get_class( $paymentDate ) == \WC_DateTime::class ) {

			$invoice->setPaymentsList(
				[
					new IssuedDocumentPaymentsListItem(
						[
							"amount"          => $order->get_subtotal(),
							"paid_date"       => $paymentDate,
							'due_date'        => $paymentDate,
							"status"          => IssuedDocumentStatus::PAID,
							"payment_account" => new PaymentAccount( [ 'id' => intval( get_option( 'selected_account_payment_id' ) ) ] )
						]
					)
				]
			);
		}


//	$get_new_issued_document_totals_request = new GetNewIssuedDocumentTotalsRequest();
//	$get_new_issued_document_totals_request->setData( $invoice );

		$ficId = get_post_meta( $order->get_id(), 'woofic_invoice_id', true );

		$apiInstance = new IssuedDocumentsApi(
			null,
			$config
		);

		$result = null;

		if ( ! $ficId ) {

			$create_issued_document_request = new CreateIssuedDocumentRequest();
			$create_issued_document_request->setData( $invoice );
			$create_issued_document_request->setOptions(
				new IssuedDocumentOptions(
					[
						"fix_payments" => true
					]
				)
			);


			$result = $apiInstance->createIssuedDocument( $company_id, $create_issued_document_request );
			$order->add_order_note( 'Creata ' . $typeLabel . ' su FattureInCloud: #' . $result->getData()->getId() );

		} else {
			//check if resource exists


			try {
				$apiInstance->getIssuedDocument( $company_id, $ficId );

				$modify_issued_document_request = new ModifyIssuedDocumentRequest();
				$modify_issued_document_request->setData( $invoice );
				$modify_issued_document_request->setOptions(
					new IssuedDocumentOptions(
						[
							"fix_payments" => true
						]
					)
				);

				$result = $apiInstance->modifyIssuedDocument( $company_id, $ficId, $modify_issued_document_request );
				$order->add_order_note( 'Aggiornata ' . $typeLabel . ' su FattureInCloud: #' . $ficId );

			} catch ( ApiException $exception ) {

				$body = json_decode( $exception->getResponseBody(), true );

				if ( isset( $body['error'] ) && count( $body['error'] ) > 0 && $body['error']['message'] == 'Resource not found.' ) {
					$create_issued_document_request = new CreateIssuedDocumentRequest();
					$create_issued_document_request->setData( $invoice );
					$create_issued_document_request->setOptions(
						new IssuedDocumentOptions(
							[
								"fix_payments" => true
							]
						)
					);

					$result = $apiInstance->createIssuedDocument( $company_id, $create_issued_document_request );
					$order->add_order_note( 'Creata ' . $typeLabel . '  su FattureInCloud: #' . $result->getData()->getId() );
				}
			}


		}

		update_post_meta( $order->get_id(), 'woofic_invoice_id', $result->getData()->getId() );

	}


	public function createCorrispettivo( \WC_Order $order ) {

		if ( ! $this->__vl() ) {
			throw new \Exception( 'Licenza WooFic non valida o scaduta.' );
		}

		if ( ! $order ) {
			//@todo: throw error
		}

		//check payment method
		$ficPaymentMethod = get_option( 'fic_' . $order->get_payment_method() );
		if ( ! $ficPaymentMethod ) {

		}

		$company_id = get_option( 'woofic_company_id' );

		$config = $this->getConfig();


		$receipt = new Receipt();
		$receipt->setType( ReceiptType::TILL_RECEIPT );
		$receipt->setDescription( 'Ordine n.' . $order->get_id() );

		$cart = [];

		foreach ( $order->get_items() as $item ) {


			// Get an instance of the WC_Tax object
			$tax_obj = new \WC_Tax();

			// Get the tax data from customer location and product tax class
			$tax_rates_data = $tax_obj->find_rates( array(
				'country'   => $order->get_billing_country(),
				'state'     => $order->get_billing_state(),
				'city'      => $order->get_billing_city(),
				'postcode'  => $order->get_billing_postcode(),
				'tax_class' => $item->get_tax_class()
			) );

			//DEFAULT 22%

			$tax_rate = null;

			if ( empty( $tax_rates_data ) ) {
				//throw new \Exception( 'IVA non trovata' );
				$tax_rates_data = [
					[
						'label' => '0%'
					]
				];
			}

			$tax_rate = reset( $tax_rates_data );

			$taxes = \WC_Tax::get_rates_for_tax_class( $item->get_tax_class() );

			$selectedTax = null;

			foreach ( $taxes as $tax ) {
				if ( $tax_rate['label'] == $tax->tax_rate_name ) {
					$selectedTax = $tax;
				}
			}


			// Finally we get the tax rate (percentage number) and display it:

			$ficVatCode = null;

			if ( $selectedTax ) {
				$ficVatCode = get_option( 'fic_aliquota_' . $selectedTax->tax_rate_id, true );
			}


			if ( $ficVatCode == null ) {
				$taxRates = $this->getVatTypes();


				$ficTaxRate = array_filter( $taxRates, function ( VatType $rate ) use ( $tax_rate ) {
					return $rate->getValue() == $tax_rate['label'];
				} );

				$ficTaxRate = reset( $ficTaxRate );

				if ( ! $ficTaxRate ) {
					throw new \Exception( 'IVA ' . $tax_rate['label'] . ' non trovata su FattureInCloud' );
				}

				$vatId = $ficTaxRate->getId();

				$taxRateId = 0;
				if ( $selectedTax ) {
					$taxRateId = $selectedTax->tax_rate_id;
				}
				update_option( 'fic_aliquota_' . $taxRateId, $vatId );

				$ficVatCode = get_option( 'fic_aliquota_' . $taxRateId, true );
			}

			$price = get_post_meta( $item->get_product()->get_id(), '_price', true );

			$cart[] = new ReceiptItemsListItem(
				[
					"name"       => $item->get_name(),
					"amount_net" => $price,
					"qty"        => $item->get_quantity(),
					"vat"        => new VatType(
						[
							"id" => $ficVatCode
						]
					)
				]
			);

		}

		//$paymentMethod = new PaymentMethod( [ 'id' => get_option( 'fic_' . $order->get_payment_method(), true ) ] );

		$receipt->setItemsList( $cart );

		$suffix = get_option( 'woofic_suffix_receipt', null );

		if ( $suffix ) {
			$receipt->setNumeration( $suffix );
		}

		$receipt->setPaymentAccount( new PaymentAccount( [ 'id' => intval( get_option( 'selected_account_payment_id' ) ) ] ) );

		$paymentDate = $order->get_date_paid();
		if ( $paymentDate && get_class( $paymentDate ) == \WC_DateTime::class ) {
			$receipt->setDate( $paymentDate );
		}

		$ficId = get_post_meta( $order->get_id(), 'woofic_corrispettivo_id', true );

		$apiInstance = new ReceiptsApi(
			null,
			$config
		);

		$result = null;

		if ( ! $ficId ) {

			$create_issued_document_request = new CreateReceiptRequest();
			$create_issued_document_request->setData( $receipt );

			$result = $apiInstance->createReceipt( $company_id, $create_issued_document_request );
			$order->add_order_note( 'Creato corrispettivo su FattureInCloud: #' . $result->getData()->getId() );

		} else {

			try {
				$apiInstance->getReceipt( $company_id, $ficId );

				$modify_issued_document_request = new ModifyReceiptRequest();
				$modify_issued_document_request->setData( $receipt );

				$result = $apiInstance->modifyReceipt( $company_id, $ficId, $modify_issued_document_request );
				$order->add_order_note( 'Aggiornato corrispettivo su FattureInCloud: #' . $ficId );

			} catch ( ApiException $exception ) {

				$body = json_decode( $exception->getResponseBody(), true );

				if ( isset( $body['error'] ) && count( $body['error'] ) > 0 && $body['error']['message'] == 'Resource not found.' ) {
					$create_issued_document_request = new CreateReceiptRequest();
					$create_issued_document_request->setData( $receipt );


					$result = $apiInstance->createReceipt( $company_id, $create_issued_document_request );
					$order->add_order_note( 'Creato corrispettivo su FattureInCloud: #' . $result->getData()->getId() );

				}
			}


		}

		update_post_meta( $order->get_id(), 'woofic_corrispettivo_id', $result->getData()->getId() );

	}

	public function getOrderPaymentMethod( \WC_Order $order ) {
		switch ( $order->get_payment_method() ) {
			case 'paypal':
			case 'stripe':
				$paymentMethodCode = 'MP08';
				break;
			case 'bacs':
				$paymentMethodCode = 'MP05';
				break;
			case 'cheque':
				$paymentMethodCode = 'MP02';
				break;
			default:
				$paymentMethodCode = 'MP01';
				break;
		}

		return $paymentMethodCode;
	}

	public function getVatTypes() {
		$config      = $this->getConfig();
		$apiInstance = new InfoApi(
			null,
			$config
		);
		$company_id  = get_option( 'woofic_company_id' );

		$result = $apiInstance->listVatTypes( $company_id );

		return $result->getData();
	}

	public function getConfig() {

		$config = Configuration::getDefaultConfiguration()->setAccessToken( get_option( 'woofic_access_token' ) );

		//check if is expired
		$expireAt = get_option( 'woofic_token_expire', null );

		if ( $expireAt < new \DateTime() ) {
			if ( ! get_option( 'woofic_client_id' ) || ! get_option( 'woofic_client_secret' ) ) {
				return null;
			}
			$oauth = new OAuth2AuthorizationCodeManager( get_option( 'woofic_client_id' ), get_option( 'woofic_client_secret' ), get_option( 'woofic_redirect_uri' ) );

			if ( ! get_option( 'woofic_refresh_token' ) ) {
				return null;
			}

			$tokenObj = $oauth->refreshToken( get_option( 'woofic_refresh_token' ) );

			$accessToken  = $tokenObj->getAccessToken();
			$refreshToken = $tokenObj->getRefreshToken();
			$expireIn     = $tokenObj->getExpiresIn();

			$now = new \DateTime();
			$now->add( new \DateInterval( 'PT' . $expireIn . 'S' ) );

			update_option( 'woofic_access_token', $accessToken, true );
			update_option( 'woofic_refresh_token', $refreshToken, true );
			update_option( 'woofic_token_expire', $now, true );

			$config = Configuration::getDefaultConfiguration()->setAccessToken( $accessToken );

		}

		return $config;
	}

	public function __vl() {

		if ( ! get_option( 'woofic_license_key' ) ) {
			return false;
		}

		$responseActivation = wp_remote_get( WOOFIC_ENDPOINT . '/wp-json/lmfwc/v2/licenses/' . get_option( 'woofic_license_key' ), [
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( WOOFIC_ENDPOINT_USERNAME . ':' . WOOFIC_ENDPOINT_PASSWORD )
			)
		] );

		$response = json_decode( wp_remote_retrieve_body( $responseActivation ), true );

		if ( ! isset( $response['data']['expiresAt'] ) || ( new \DateTime( $response['data']['expiresAt'] ) < new \DateTime() ) ) {
			return false;
		}

		return $response['data'];

	}
}
