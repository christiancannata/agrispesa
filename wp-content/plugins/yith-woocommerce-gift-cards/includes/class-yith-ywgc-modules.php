<?php
/**
 * Class YITH_YWGC_Modules
 * Handle modules.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\GiftCards\Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_YWGC_Modules' ) ) {
	/**
	 * YITH_YWGC_Modules class.
	 *
	 * @since   4.0
	 */
	class YITH_YWGC_Modules {

		const AJAX_ACTION = 'yith-ywgc-modules-action';

		/**
		 * The modules data.
		 *
		 * @var array[]
		 */
		private $modules_data;

		/**
		 * The active module instances.
		 *
		 * @var YITH_YWGC_Modules[]
		 */
		private $active_modules;

		/**
		 * Single instance of the class
		 *
		 * @var YITH_YWGC_Modules
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return instance|YITH_YWGC_Modules
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * YITH_YWGC_Modules constructor.
		 */
		private function __construct() {
			$this->init_modules_data();

			add_action( 'yith_ywgc_print_modules_tab', array( $this, 'print_modules_tab' ) );

			// Load modules as soon as possible, to allow class extension (through Extensible Singleton trait) - mostly useful for the Premium version.
			$this->load_modules();
		}

		/**
		 * On load.
		 */
		private function init_modules_data() {
			$modules = require trailingslashit( YITH_YWGC_MODULES_PATH ) . 'modules.php';

			foreach ( $modules as $key => $data ) {
				$requires      = $data['requires'] ?? false;
				$always_active = $data['always_active'] ?? false;
				$init_file     = self::get_module_path( $key, 'init.php' );
				$is_available  = file_exists( $init_file );

				if ( 'premium' === $requires && ! defined( 'YITH_YWGC_PREMIUM' ) ) {
					$is_available = false;
				}

				if ( $always_active ) {
					$is_active = ! ! $is_available;
				} else {
					$active_option = self::get_module_active_option( $key );
					$is_active     = ! ! $is_available && 'yes' === get_option( $active_option, 'no' );
				}

				$data['key']          = $key;
				$data['name']         = $data['name'] ?? '';
				$data['description']  = $data['description'] ?? '';
				$data['needs_reload'] = $data['needs_reload'] ?? false;
				$data['hidden']       = $data['hidden'] ?? false;
				$data['init_file']    = $init_file;
				$data['is_available'] = ! ! $is_available;
				$data['is_active']    = ! ! $is_active;

				$this->modules_data[ $key ] = $data;
			}
		}

		/**
		 * On load.
		 */
		public function on_load() {
			$this->load_modules();
		}

		/**
		 * Reload modules.
		 */
		private function reload_modules() {
			$this->init_modules_data();
			$this->load_modules( true );
		}

		/**
		 * Load modules.
		 *
		 * @param bool $force Force reloading flag.
		 */
		private function load_modules( bool $force = false ) {
			static $fired = false;

			if ( ! $fired || $force ) {
				$fired = true;

				foreach ( $this->modules_data as $key => $data ) {
					$is_active         = $data['is_active'] ?? false;
					$init_file         = $data['init_file'] ?? '';
					$is_already_loaded = isset( $this->active_modules[ $key ] );

					if ( $is_already_loaded ) {
						if ( ! $is_active ) {
							unset( $this->active_modules[ $key ] );
						}
					} else {
						if ( $is_active && $init_file ) {
							$this->active_modules[ $key ] = require_once $init_file;

							if ( ! $this->active_modules[ $key ] instanceof YITH_YWGC_Module ) {
								error_log( sprintf( 'The module "%s" must be a child class of YITH_YWGC_Module.', $key ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
								continue;
							}

							if ( $key !== $this->active_modules[ $key ]->get_key() ) {
								error_log( sprintf( 'Module "%s": The module key must be the same of the one set in the configuration file.', $key ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

								continue;
							}
						}
					}
				}
			}
		}

		/**
		 * Get modules data.
		 *
		 * @return array
		 */
		public function get_modules_data(): array {
			return $this->modules_data;
		}

		/**
		 * Retrieve a module path.
		 *
		 * @param string $module_key The module key.
		 * @param string $path       The path.
		 *
		 * @return string
		 */
		public static function get_module_path( string $module_key, string $path = '' ): string {
			$module_key = sanitize_title( $module_key );
			$full_path  = trailingslashit( YITH_YWGC_MODULES_PATH ) . $module_key;
			$full_path  = trailingslashit( $full_path );

			if ( $path ) {
				$full_path = $full_path . $path;
			}

			return $full_path;
		}

		/**
		 * Retrieve a module URL.
		 *
		 * @param string $module_key The module key.
		 * @param string $url        The URL.
		 *
		 * @return string
		 */
		public static function get_module_url( string $module_key, string $url = '' ): string {
			$module_key = sanitize_title( $module_key );
			$full_url   = trailingslashit( YITH_YWGC_MODULES_URL ) . $module_key;
			$full_url   = trailingslashit( $full_url );

			if ( $url ) {
				$full_url = $full_url . $url;
			}

			return $full_url;
		}

		/**
		 * Retrieve the option to check for the module active status.
		 *
		 * @param string $module_key The module key.
		 *
		 * @return string
		 */
		public static function get_module_active_option( string $module_key ): string {
			$module_key = sanitize_title( $module_key );

			return "yith-ywgc-module-$module_key-active";
		}

		/**
		 * Activate the module
		 *
		 * @param string $module_key The module key.
		 */
		public function activate_module( string $module_key ) {
			$module_key = sanitize_title( $module_key );

			if ( $this->is_module_available( $module_key ) && ! $this->is_module_active( $module_key ) ) {
				update_option( self::get_module_active_option( $module_key ), 'yes' );

				// Force reloading modules to allow handle on_activation callback.
				$this->reload_modules();

				do_action( "yith_ywgc_modules_module_{$module_key}_activated" );
			}
		}

		/**
		 * Deactivate the module
		 *
		 * @param string $module_key The module key.
		 */
		public function deactivate_module( string $module_key ) {
			$module_key = sanitize_title( $module_key );

			if ( $this->is_module_available( $module_key ) && $this->is_module_active( $module_key ) ) {
				update_option( self::get_module_active_option( $module_key ), 'no' );

				do_action( "yith_ywgc_modules_module_{$module_key}_deactivated" );
			}
		}

		/**
		 * Is the module available?
		 *
		 * @param string $module_key The module key.
		 *
		 * @return bool
		 */
		public function is_module_available( string $module_key ): bool {
			return ! ! $this->modules_data[ $module_key ]['is_available'] ?? false;
		}

		/**
		 * Is the module active?
		 *
		 * @param string $module_key The module key.
		 *
		 * @return bool
		 */
		public function is_module_active( string $module_key ): bool {
			return ! ! $this->modules_data[ $module_key ]['is_active'] ?? false;
		}

		/**
		 * Print the modules tab.
		 */
		public function print_modules_tab() {
			$modules_data = $this->modules_data;

			$available_modules = array_filter(
				$modules_data,
				function ( $data ) {
					$is_available = $data['is_available'] ?? false;
					$hidden       = $data['hidden'] ?? false;

					return $is_available && ! $hidden;
				}
			);

			$non_available_modules = array_filter(
				$modules_data,
				function ( $data ) {
					$is_available = $data['is_available'] ?? false;
					$hidden       = $data['hidden'] ?? false;

					return ! $is_available && ! $hidden;
				}
			);

			yith_ywgc_get_view( 'settings-tabs/html-modules.php', compact( 'available_modules', 'non_available_modules' ) );
		}

	}
}
