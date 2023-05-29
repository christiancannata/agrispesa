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

use Inpsyde\Assets\Asset;
use Inpsyde\Assets\AssetManager;
use Inpsyde\Assets\Script;
use Inpsyde\Assets\Style;
use WooFic\Engine\Base;
use WooFic\Services\WooficSender;

/**
 * This class contain the Enqueue stuff for the backend
 */
class Enqueue extends Base {

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize() {
		if ( ! parent::initialize() ) {
			return;
		}

		\add_action( AssetManager::ACTION_SETUP, array( $this, 'enqueue_assets' ) );

		add_filter( 'manage_edit-shop_order_columns', function ( $columns ) {
			$new_columns = array();
			foreach ( $columns as $column_name => $column_info ) {
				$new_columns[ $column_name ] = $column_info;
				if ( 'order_total' === $column_name ) {
					$new_columns['fic']         = 'Fattura / Ricevuta';
					$new_columns['tipo_utente'] = 'Tipo Utente';
				}
			}

			return $new_columns;
		} );


		add_action( 'manage_shop_order_posts_custom_column', function ( $column ) {
			global $post;
			if ( 'fic' === $column ) {
				$billingType = get_post_meta( $post->ID, '_billing_type', true );
				$billingType = $billingType == 'INVOICE' ? 'FATTURA' : 'RICEVUTA';

				echo esc_html_e( $billingType, W_TEXTDOMAIN );

				return true;
			}

			if ( 'tipo_utente' === $column ) {
				$customerType = get_post_meta( $post->ID, '_billing_customer_type', true );
				$customerType = $customerType == 'COMPANY' ? 'AZIENDA' : 'PRIVATO';
				echo esc_html_e( $customerType, W_TEXTDOMAIN );

				return true;
			}

		} );

		add_action( 'woocommerce_order_status_changed', function ( $order_id, $old_status, $new_status ) {

			if ( $new_status == get_option( 'woofic_order_status_triggered', 'completed' ) ) {
				$order        = wc_get_order( $order_id );
				$wooficSender = new WooficSender();
				$wooficSender->createInvoice( $order );
			}

		}, 10, 3 );

		add_action( 'restrict_manage_posts', function () {
			if ( ! isset( $_GET['post_type'] ) || 'shop_order' !== $_GET['post_type'] ) {
				return;
			}

			?>
			<!--<select name="requested_invoice">
				<option value="">Fatture e ricevute</option>
				<option value="INVOICE">Solo fatture</option>
				<option value="RECEIPT">Solo ricevute</option>
			</select>-->

			<?php
		} );

	}

	/**
	 * Enqueue assets with Inpyside library https://inpsyde.github.io/assets
	 *
	 * @param \Inpsyde\Assets\AssetManager $asset_manager The class.
	 *
	 * @return void
	 */
	public function enqueue_assets( AssetManager $asset_manager ) {
		// Load admin style sheet and JavaScript.
		$assets = $this->enqueue_admin_styles();

		if ( ! empty( $assets ) ) {
			foreach ( $assets as $asset ) {
				$asset_manager->register( $asset );
			}
		}

		$assets = $this->enqueue_admin_scripts();

		if ( ! empty( $assets ) ) {
			foreach ( $assets as $asset ) {
				$asset_manager->register( $asset );
			}
		}

	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function enqueue_admin_styles() {
		$admin_page = \get_current_screen();
		$styles     = array();

		if ( ! \is_null( $admin_page ) && strstr( $admin_page->id, 'woofic' ) !== false ) {
			$styles[0] = new Style( W_TEXTDOMAIN . '-settings-style', \plugins_url( 'assets/build/plugin-settings.css', W_PLUGIN_ABSOLUTE ) );
			$styles[0]
				->forLocation( Asset::BACKEND )
				->withVersion( W_VERSION );
			$styles[0]->withDependencies( 'dashicons' );
		}

		$styles[1] = new Style( W_TEXTDOMAIN . '-admin-style', \plugins_url( 'assets/build/plugin-admin.css', W_PLUGIN_ABSOLUTE ) );
		$styles[1]
			->forLocation( Asset::BACKEND )
			->withVersion( W_VERSION );
		$styles[1]->withDependencies( 'dashicons' );

		return $styles;
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function enqueue_admin_scripts() {
		$admin_page = \get_current_screen();
		$scripts    = array();

		if ( ! \is_null( $admin_page ) && 'toplevel_page_woofic' === $admin_page->id ) {
			$scripts[0] = new Script( W_TEXTDOMAIN . '-settings-script', \plugins_url( 'assets/build/plugin-settings.js', W_PLUGIN_ABSOLUTE ) );
			$scripts[0]
				->forLocation( Asset::BACKEND )
				->withVersion( W_VERSION );
			$scripts[0]->withDependencies( 'jquery-ui-tabs' );
			$scripts[0]->canEnqueue(
				function () {
					return \current_user_can( 'manage_options' );
				}
			);
		}

		$scripts[1] = new Script( W_TEXTDOMAIN . '-settings-admin', \plugins_url( 'assets/build/plugin-admin.js', W_PLUGIN_ABSOLUTE ) );
		$scripts[1]
			->forLocation( Asset::BACKEND )
			->withVersion( W_VERSION );
		$scripts[1]->dependencies();

		return $scripts;
	}

}
