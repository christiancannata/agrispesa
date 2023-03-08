<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SA_Smart_Manager_Deactivation' ) ) {

	class SA_Smart_Manager_Deactivation {

		public static $sm_deactivation_string;

		/**
		* @var string Plugin name
		* @access public 
		*/
		public static $plugin_name = '';

		/**
		 * @var string Plugin file name
		 * @access public
		 */
		public static $sa_plugin_file_name = '';

		/**
		 * @var string Plugin URL
		 * @access public
		 */
		public static $sa_plugin_url = '';

		function __construct( $sa_plugin_file_name = '', $sa_plugin_name = '' ) {

			self::$sa_plugin_file_name = $sa_plugin_file_name;
			self::$plugin_name         = $sa_plugin_name;
			self::$sa_plugin_url       = untrailingslashit( plugin_dir_path ( __FILE__ ) );

			self::sa_load_all_str();
			add_action( 'admin_footer', array( $this, 'maybe_load_deactivate_options' ) );
			add_action( 'wp_ajax_sm_submit_survey', array( $this, 'sa_submit_deactivation_reason_action' ) );

			add_filter( 'plugin_action_links_' . self::$sa_plugin_file_name, array( $this, 'sa_plugin_settings_link' ) );

		}

		/**
		 * Settings link on Plugins page
		 * 
		 * @access public
		 * @param array $links 
		 * @return array
		 */
		public static function sa_plugin_settings_link( $links ) {
			
			if ( isset ( $links['deactivate'] ) ) {
				$links['deactivate'] .= '<i class="sa-sm-slug" data-slug="' . self::$sa_plugin_file_name  . '"></i>';
			}
			return $links;
		}

		/**
		 * Localizes all the string used
		 */
		public static function sa_load_all_str() {
			self::$sm_deactivation_string = array(
				'deactivation-headline'		               => __( 'Quick Feedback for Smart Manager plugin', 'smart-manager-for-wp-e-commerce' ),
				'deactivation-share-reason'                => __( 'Take a moment to let us know why you are deactivating', 'smart-manager-for-wp-e-commerce' ),
				'deactivation-modal-button-submit'         => __( 'Submit & Deactivate', 'smart-manager-for-wp-e-commerce' ),
				'deactivation-modal-button-cancel'         => __( 'Skip & Deactivate', 'smart-manager-for-wp-e-commerce' ),
				'deactivation-modal-button-confirm'        => __( 'Yes - Deactivate', 'smart-manager-for-wp-e-commerce' ),
				'deactivation-modal-skip-deactivate'       => __( 'Submit a reason to deactivate', 'smart-manager-for-wp-e-commerce' ),
				'deactivation-modal-error'       		   => __( 'Please select an option', 'smart-manager-for-wp-e-commerce' ),
			);
		}

		/**
		 * Checking current page and pushing html, js and css for this task
		 * @global string $pagenow current admin page
		 * @global array $vars global vars to pass to view file
		 */
		public static function maybe_load_deactivate_options() {
			global $pagenow;
			if ( $pagenow == 'plugins.php' ) {
				global $vars;
				$vars = array( 'slug' => "asvbsd", 'reasons' => self::deactivate_options() );
				include_once self::$sa_plugin_url . '/class-sa-smart-manager-deactivation-modal.php';
			}
		}

		/**
		 * deactivation reasons in array format
		 * @return array reasons array
		 * @since 1.0.0
		 */
		public static function deactivate_options() {

			$reasons = array();
			$reasons = array(
							array(
									'id'                => 1,
									'text'              => __( 'The plugin is not working / not compatible with another plugin.' , 'smart-manager-for-wp-e-commerce' ),
									'input_type'        => 'textarea',
									'input_placeholder' => __( 'Kindly share what did not work for you / conflicting with which plugin so we can fix it...', 'smart-manager-for-wp-e-commerce' )
								),
							array(
									'id'                => 2,
									'text'              => __( 'I only needed the plugin for a short period' , 'smart-manager-for-wp-e-commerce' ),
									'input_type'        => 'textarea',
									'input_placeholder' => __( 'What did you wanted to do in short period?', 'smart-manager-for-wp-e-commerce' )
								),
							array(
									'id'                => 3,
									'text'              => __( 'The plugin is great, but I need specific feature that you don\'t support' , 'smart-manager-for-wp-e-commerce' ),
									'input_type'        => 'textarea',
									'input_placeholder' => __( 'What specific feature you need?', 'smart-manager-for-wp-e-commerce' )
								),
							array(
									'id'                => 4,
									'text'              => __( 'I found another plugin for my needs' , 'smart-manager-for-wp-e-commerce' ),
									'input_type'        => 'textfield',
									'input_placeholder' => __( 'What is that plugin name?', 'smart-manager-for-wp-e-commerce' )
								),
							array(
									'id'                => 5,
									'text'              => __( 'It is a temporary deactivation. I am just debugging an issue.' , 'smart-manager-for-wp-e-commerce' ),
									'input_type'        => '',
									'input_placeholder' => ''
								),
							array(
									'id'                => 6,
									'text'              => __( 'Other' , 'smart-manager-for-wp-e-commerce' ),
									'input_type'        => 'textarea',
									'input_placeholder' => __( 'Please mention...', 'smart-manager-for-wp-e-commerce' )
								)
						);

			$uninstall_reasons['default'] = $reasons;

			return $uninstall_reasons;
		}

		/**
		 * get exact str against the slug
		 *
		 * @param type $slug
		 *
		 * @return type
		 */
		public static function load_str( $slug ) {
			return self::$sm_deactivation_string[ $slug ];
		}

		/**
		 * Called after the user has submitted his reason for deactivating the plugin.
		 *
		 * @since  1.1.2
		 */
		public static function sa_submit_deactivation_reason_action() {
			if ( ! isset( $_POST[ 'reason_id' ] ) ) {
				exit;
			}

			$api_url = 'https://www.storeapps.org/wp-admin/admin-ajax.php';

			// Plugin specific options should be added from here
			$sm_lite_activation_date = get_option( 'sm_lite_activation_date', false );
			$sm_update_416_date = get_option( '_sm_update_416_date', false );
			$sm_update_417_date = get_option( '_sm_update_417_date', false );
			$sm_update_418_date = get_option( '_sm_update_418_date', false );
			$sm_inline_update_count = get_option( 'sm_inline_update_count', 0 );

			if( !empty( $_POST ) ) {
				$plugin_data = $_POST;
				$plugin_data['sm_activation_date'] = array( $sm_lite_activation_date, $sm_update_416_date, $sm_update_417_date, $sm_update_418_date );
				$plugin_data['sm_inline_update_count'] = $sm_inline_update_count;
				$plugin_data['domain'] = home_url();
				$plugin_data['action'] = 'submit_survey';
			} else {
				exit();
			}

			$method = 'POST';
			$qs = http_build_query( $plugin_data );
			$options = array(
				'timeout' => 45,
				'method' => $method
			);
			if ( $method == 'POST' ) {
				$options['body'] = $qs;
			} else {
				if ( strpos( $api_url, '?' ) !== false ) {
					$api_url .= '&'.$qs;
				} else {
					$api_url .= '?'.$qs;
				}
			}

			$response = wp_remote_request( $api_url, $options );

			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$data = json_decode( $response['body'], true );

				if ( empty( $data['error'] ) ) {
					if( !empty( $data ) && !empty( $data['success'] ) ) {
						echo 1;
					}
					echo ( json_encode( $data ) );
					exit();     
				}
			}
			// Print '1' for successful operation.
			echo 1;
			exit();
		}

	} // End of Class

}
