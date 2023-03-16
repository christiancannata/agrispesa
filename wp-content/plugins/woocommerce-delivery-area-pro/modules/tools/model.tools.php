<?php
/**
 * Class: WDAP_Model_Tools
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.0.1
 * @package woo-delivery-area-pro
 */

if ( ! class_exists( 'WDAP_Model_Tools' ) ) {

	/**
	 * Setting model for Plugin Options.
	 *
	 * @package age-gate-pro
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WDAP_Model_Tools extends FlipperCode_Model_Base {

		/**
		 * Intialize Backup object.
		 */
		function __construct() {}

		/**
		 * Admin menu for Settings Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			
			return array(
				'wdap_tools_tools' => esc_html__( 'Reset', 'woo-delivery-area-pro' ),
			);
		}

		public function install_fresh_settings() {

			if ( isset( $_POST['plugin_fresh_install_submit'] ) && sanitize_text_field($_POST['plugin_install_fresh_settings']) == 'YES' ) {
				$drs = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );
				delete_option( 'wp-delivery-area-pro');
				$showzipcodesearch = WDAP_Fresh_Settings::get_fresh_settings();
				
				if(isset($drs) && !empty($drs) && isset($drs['wdap_enabled']) && $drs['wdap_enabled'] == 'yes'){
					$showzipcodesearch['wdap_enabled'] = 'yes';
					if( isset( $drs['wdap_debug_info'] ) && !empty($drs['wdap_debug_info']) ){
						$showzipcodesearch['wdap_debug_info'] = $drs['wdap_debug_info'];
					}	
				}

				if( isset( $drs ) && ! empty( $drs ) && isset( $drs[ 'wdap_googleapikey' ] ) && ! empty ( $drs[ 'wdap_googleapikey' ] ) ) {
					$showzipcodesearch[ 'wdap_googleapikey' ] = $drs[ 'wdap_googleapikey' ];
				}

				update_option('wp-delivery-area-pro',serialize(wp_unslash($showzipcodesearch)) );

				$response['success'] = esc_html__( 'Plugin\'s default settings stored successfully.', 'woo-delivery-area-pro' );
			}
			else{
				$response['error'] = esc_html__( 'Please type "YES" in capitals to reset plugin and to install plugin\'s fresh settings again.', 'woo-delivery-area-pro' );
			}
			return $response;

		}

		/**
		 * Add or Edit Operation.
		 */
		function save() {
			
			//Nonce Verification	
			if ( isset( $_REQUEST['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ); }
			if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {
				die( 'Cheating...' );
			}
			
			$this->verify( $_POST );
			if ( is_array( $this->errors ) and ! empty( $this->errors ) ) {
				$this->throw_errors();
			}
			
			//Install Fresh Settings
			if(isset($_POST['plugin_fresh_install_submit'])){

		 		if ( empty ($_POST['plugin_install_fresh_settings'])){
					$this->errors[] = esc_html__( 'Please type "YES" to reset plugin settings.', 'woo-delivery-area-pro' );
					$this->throw_errors();
				}
				$response = $this->install_fresh_settings();
				return $response;
				
		 	}
		 	
		}

	}
}
