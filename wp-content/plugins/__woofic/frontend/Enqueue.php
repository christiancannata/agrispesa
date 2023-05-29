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

namespace WooFic\Frontend;

use Inpsyde\Assets\Asset;
use Inpsyde\Assets\AssetManager;
use Inpsyde\Assets\Script;
use Inpsyde\Assets\Style;
use WooFic\Engine\Base;
use WooFic\Services\WP_Route;

/**
 * Enqueue stuff on the frontend
 */
class Enqueue extends Base {

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize() {
		parent::initialize();

		add_action( 'template_redirect', function () {
			global $wp_query;
			if ( isset( $wp_query->query_vars['pagename'] ) && ( $wp_query->query_vars['pagename'] == 'woofic-oauth-redirect' ) ) {
				wp_redirect( '/wp-admin/admin.php?page=woofic&' . http_build_query( $_GET ) );
				exit();
			}
		} );


		\add_action( AssetManager::ACTION_SETUP, array( $this, 'enqueue_assets' ) );

		$this->__addVatCode();

	}


	/**
	 * Enqueue assets with Inpyside library https://inpsyde.github.io/assets
	 *
	 * @param \Inpsyde\Assets\AssetManager $asset_manager The class.
	 *
	 * @return void
	 */
	public function enqueue_assets( AssetManager $asset_manager ) {
		// Load public-facing style sheet and JavaScript.
		$assets = $this->enqueue_styles();

		if ( ! empty( $assets ) ) {
			foreach ( $assets as $asset ) {
				$asset_manager->register( $asset );
			}
		}

		$assets = $this->enqueue_scripts();

		if ( ! empty( $assets ) ) {
			foreach ( $assets as $asset ) {
				$asset_manager->register( $asset );
			}
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		$styles    = array();
		$styles[0] = new Style( W_TEXTDOMAIN . '-plugin-styles', \plugins_url( 'assets/build/plugin-public.css', W_PLUGIN_ABSOLUTE ) );
		$styles[0]
			->forLocation( Asset::FRONTEND )
			->useAsyncFilter()
			->withVersion( W_VERSION );
		$styles[0]->dependencies();

		return $styles;
	}


	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function enqueue_scripts() {
		$scripts    = array();
		$scripts[0] = new Script( W_TEXTDOMAIN . '-plugin-script', \plugins_url( 'assets/build/plugin-public.js', W_PLUGIN_ABSOLUTE ) );
		$scripts[0]
			->forLocation( Asset::FRONTEND )
			->useAsyncFilter()
			->withVersion( W_VERSION );
		$scripts[0]->dependencies();

		/*	$scripts[0]->withLocalize(
				'example_demo',
				array(
					'alert'   => \__( 'Error!', W_TEXTDOMAIN ),
					'nonce'   => \wp_create_nonce( 'demo_example' ),
					'wp_rest' => \wp_create_nonce( 'wp_rest' ),
				)
			); */


		return $scripts;
	}

	private function __addVatCode() {
		/*****************************  FRONTEND  ****************************************/

		/**************************
		 *
		 * Filter to add a VAT field to:
		 * - My Account - Edit Form -- Billing fields
		 * - Checkout - Edit Form - Billing Fields
		 * This function is also reordering the form fields.
		 ***************************/

		add_filter( 'woocommerce_billing_fields', function ( $billing_fields ) {

			$billing_fields2['billing_customer_type'] = [
				'type'     => 'select',
				'label'    => get_option( 'woofic_customer_type_field_label', __( 'Customer Type', 'woofic' ) ),
				'class'    => [ 'form-row-wide' ],
				'required' => true,
				'clear'    => true,
				'value'    => null,
				'options'  => [
					null      => __( 'Select a choice', 'woofic' ),
					'USER'    => __( 'Private User', 'woofic' ),
					'COMPANY' => __( 'Company / Professional', 'woofic' )
				]
			];


			$billing_fields2['billing_first_name'] = $billing_fields['billing_first_name'];
			$billing_fields2['billing_last_name']  = $billing_fields['billing_last_name'];
			$billing_fields2['billing_company']    = $billing_fields['billing_company'];


			$billing_fields2['billing_type'] = [
				'type'     => 'select',
				'label'    => get_option( 'woofic_type_field_label', __( 'Billing Type', 'woofic' ) ),
				'class'    => [ 'form-row-wide hidden' ],
				'required' => true,
				'clear'    => true,
				'options'  => [
					'RECEIPT' => __( 'Receipt', 'woofic' ),
					'INVOICE' => __( 'Invoice', 'woofic' )
				]
			];

			$billing_fields2['billing_vat'] = array(
				'type'     => 'text',
				'label'    => __( 'VAT number', 'woofic' ),
				'class'    => [ 'form-row-wide' ],
				'required' => false,
				'clear'    => true
			);

			$billing_fields2['billing_pec'] = array(
				'type'     => 'text',
				'label'    => __( 'PEC', 'woofic' ),
				'class'    => [ 'form-row-wide' ],
				'required' => false,
				'clear'    => true
			);

			$billing_fields2['billing_sdi'] = array(
				'type'     => 'text',
				'label'    => __( 'SDI', 'woofic' ),
				'class'    => [ 'form-row-wide' ],
				'required' => false,
				'clear'    => true
			);

			$merged_billing_fields = $billing_fields2 + $billing_fields;


			return $merged_billing_fields;
		} );


	}
}
