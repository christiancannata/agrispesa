<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


class Wpcl_Assets {


	public function __construct() {
	}

	public function init() {

		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_table_css' ] );
	}


	public function register_scripts() {
		// Register styles
		wp_register_style( 'wpcl-datatables-main', WPCL_PLUGIN_URL . '/assets/vendor/datatables/datatables-1.10.24/css/jquery.datatables.min.css', false, '' );
		wp_register_style( 'wpcl-datatables-buttons', WPCL_PLUGIN_URL . '/assets/vendor/datatables/buttons-1.7.0/css/buttons.datatables.min.css', false, '' );
		wp_register_style( 'wpcl-datatables-colreorder', WPCL_PLUGIN_URL . '/assets/vendor/datatables/colreorder-1.5.3/css/colreorder.datatables.min.css', false, '' );
		wp_register_style( 'wpcl-datatables-fixedheader', WPCL_PLUGIN_URL . '/assets/vendor/datatables/fixedheader-3.1.8/css/fixedheader.datatables.min.css', false, '' );
		wp_register_style( 'wpcl-datatables-select', WPCL_PLUGIN_URL . '/assets/vendor/datatables/select-1.3.2/css/select.datatables.min.css', false, '' );

		$css_timestamp = date( "YmdHis", filemtime( WPCL_PLUGIN_PATH . 'assets/wpcl-tables.css' ) );
		wp_register_style( 'wpcl-tables', WPCL_PLUGIN_URL . 'assets/wpcl-tables.css', [
			'wpcl-datatables-main',
			'wpcl-datatables-buttons',
			'wpcl-datatables-colreorder',
			'wpcl-datatables-fixedheader',
			'wpcl-datatables-select',
		], $css_timestamp );


		// Register scripts
		wp_register_script( 'wpcl-datatables-jszip', WPCL_PLUGIN_URL . 'assets/vendor/datatables/jszip-2.5.0/jszip.min.js', [ 'jquery' ], '2.5.0', true );
		wp_register_script( 'wpcl-datatables-pdfmake', WPCL_PLUGIN_URL . 'assets/vendor/datatables/pdfmake-0.1.36/pdfmake.min.js', [ 'jquery' ], '0.1.36', true );
		wp_register_script( 'wpcl-datatables-pdfmake-fonts', WPCL_PLUGIN_URL . 'assets/vendor/datatables/pdfmake-0.1.36/vfs_fonts.js', [ 'jquery' ], '0.1.36', true );
		wp_register_script( 'wpcl-datatables-main', WPCL_PLUGIN_URL . 'assets/vendor/datatables/datatables-1.10.24/js/jquery.datatables.min.js', [ 'jquery' ], '1.10.24', true );
		wp_register_script( 'wpcl-datatables-buttons', WPCL_PLUGIN_URL . 'assets/vendor/datatables/buttons-1.7.0/js/datatables.buttons.min.js', [ 'jquery' ], '1.7.0', true );
		wp_register_script( 'wpcl-datatables-buttons-html5', WPCL_PLUGIN_URL . 'assets/vendor/datatables/buttons-1.7.0/js/buttons.html5.min.js', [ 'jquery' ], '1.7.0', true );
		wp_register_script( 'wpcl-datatables-buttons-print', WPCL_PLUGIN_URL . 'assets/vendor/datatables/buttons-1.7.0/js/buttons.print.min.js', [ 'jquery' ], '1.7.0', true );
		wp_register_script( 'wpcl-datatables-colreorder', WPCL_PLUGIN_URL . 'assets/vendor/datatables/colreorder-1.5.3/js/datatables.colreorder.min.js', [ 'jquery' ], '1.5.3', true );
		wp_register_script( 'wpcl-datatables-fixedheader', WPCL_PLUGIN_URL . 'assets/vendor/datatables/fixedheader-3.1.8/js/datatables.fixedheader.min.js', [ 'jquery' ], '3.1.8', true );
		wp_register_script( 'wpcl-datatables-select', WPCL_PLUGIN_URL . 'assets/vendor/datatables/select-1.3.2/js/datatables.select.min.js', [ 'jquery' ], '1.3.2', true );


		// 2019-07-24 : Added IntersectionObserver polyfill for older browsers.
		wp_register_script( 'wpcl-intersection-observer-polyfill', 'https://polyfill.io/v2/polyfill.min.js?features=IntersectionObserver', [], '0.7.0' );

		$js_timestamp = date( "YmdHis", filemtime( WPCL_PLUGIN_PATH . 'assets/table-display.js' ) );
		wp_register_script( 'wpcl-script', WPCL_PLUGIN_URL . 'assets/table-display.js', [
			'jquery',

			'wpcl-datatables-jszip',
			'wpcl-datatables-pdfmake',
			'wpcl-datatables-pdfmake-fonts',
			'wpcl-datatables-main',
			'wpcl-datatables-buttons',
			'wpcl-datatables-buttons-html5',
			'wpcl-datatables-buttons-print',
			'wpcl-datatables-colreorder',
			'wpcl-datatables-fixedheader',
			'wpcl-datatables-select',

			'wpcl-intersection-observer-polyfill',
		], $js_timestamp, true );


		wp_localize_script( 'wpcl-script', 'wpcl_script_vars', [

			'productTitle'         => get_the_title(),
			'pdfPagesize'          => get_option( 'wpcl_export_pdf_pagesize', 'LETTER' ),
			'pdfOrientation'       => get_option( 'wpcl_export_pdf_orientation', 'portrait' ),
			'columnOrderIndex'     => get_option( 'wpcl_column_order_index', 0 ),
			'columnOrderDirection' => get_option( 'wpcl_column_order_direction', 'asc' ),
			'stateSave'            => get_option( 'wpcl_state_save', 'yes' ),
			'titleSku'             => get_option( 'wpcl_export_pdf_sku', 'no' ),
			'emailSeperator'       => get_option( 'wpcl_email_seperator', 'comma' ),
			//			'productSku'           => $product_sku,
			//			'productId'            => $post->ID,
			'trans'                => [
				'resetColumn'                => __( 'Reset column order', 'wc-product-customer-list' ),
				'lengthMenuAll'              => __( 'All', 'wc-product-customer-list' ),
				'info'                       => __( 'Showing _START_ to _END_ of _TOTAL_ entries', 'wc-product-customer-list' ),
				'copybtn'                    => __( 'Copy', 'wc-product-customer-list' ),
				'printbtn'                   => __( 'Print', 'wc-product-customer-list' ),
				'search'                     => __( 'Search', 'wc-product-customer-list' ),
				'emptyTable'                 => __( 'This product currently has no customers', 'wc-product-customer-list' ),
				'zeroRecords'                => __( 'No orders match your search', 'wc-product-customer-list' ),
				'tableinfo'                  => __( 'Showing _START_ to _END_ out of _TOTAL_ orders', 'wc-product-customer-list' ),
				'lengthMenu'                 => __( 'Show _MENU_ orders', 'wc-product-customer-list' ),
				'copyTitle'                  => __( 'Copy to clipboard', 'wc-product-customer-list' ),
				'copySuccessMultiple'        => __( 'Copied %d rows', 'wc-product-customer-list' ),
				'copySuccessSingle'          => __( 'Copied 1 row', 'wc-product-customer-list' ),
				'paginateFirst'              => __( 'First', 'wc-product-customer-list' ),
				'paginatePrevious'           => __( 'Previous', 'wc-product-customer-list' ),
				'paginateNext'               => __( 'Next', 'wc-product-customer-list' ),
				'paginateLast'               => __( 'Last', 'wc-product-customer-list' ),
				'processing_orders'          => __( 'Processing Orders: ', 'wc-product-customer-list' ),
				'ajax_error'                 => __( 'There was an AJAX error', 'wc-product-customer-list' ),
				'email_multiple_button_text' => __( 'Email all customers', 'wc-product-customer-list' ),
			],
			'ajax_nonce'           => wp_create_nonce( 'wc-product-customer-list-pro' ),
			'ajax_path'            => admin_url( 'admin-ajax.php' ),
		] );
	}


	public static function enqueue_table_css() {
		wp_enqueue_style( 'wpcl-tables' );

	}

	public static function enqueue_scripts() {
		wp_enqueue_script( 'wpcl-script' );
	}


}