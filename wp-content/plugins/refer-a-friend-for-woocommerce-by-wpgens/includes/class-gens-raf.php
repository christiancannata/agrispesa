<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Gens_RAF
 * @subpackage Gens_RAF/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Gens_RAF
 * @subpackage Gens_RAF/includes
 * @author     Your Name <email@example.com>
 */
class Gens_RAF {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Gens_RAF_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $gens_raf    The string used to uniquely identify this plugin.
	 */
	protected $gens_raf;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->gens_raf = 'gens-raf';
		$this->version = '1.2.3';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Gens_RAF_Loader. Orchestrates the hooks of the plugin.
	 * - Gens_RAF_Admin. Defines all hooks for the dashboard.
	 * - Gens_RAF_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gens-raf-loader.php';

		/**
		 * The class responsible for site wide notifications. Run only on PHP 5.3+
		 */
		if(version_compare(PHP_VERSION, '5.3.0') >= 0) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/gens-notifications.php';
		}
		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gens-raf-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-gens-raf-public.php';

		$this->loader = new Gens_RAF_Loader();

	}

	/**
	 * Load Localisation files.
	 *
	 * @since  1.1.6
	 */
	public function set_locale()
	{
		load_plugin_textdomain( 'gens-raf', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {


		$plugin_admin = new Gens_RAF_Admin( $this->get_gens_raf(), $this->get_version() );
		
		$this->loader->add_filter( 'woocommerce_get_settings_pages', $plugin_admin, 'add_settings_page' );

		$this->loader->add_filter( 'plugin_action_links_refer-a-friend-for-woocommerce-by-wpgens/gens-raf.php', $plugin_admin, 'add_settings_link' );
		$this->loader->add_filter( 'plugin_action_links_refer-a-friend-for-woocommerce-by-wpgens/gens-raf.php', $plugin_admin, 'docs_link' );
		$this->loader->add_filter( 'plugin_action_links_refer-a-friend-for-woocommerce-by-wpgens/gens-raf.php', $plugin_admin, 'premium_version' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Gens_RAF_Public( $this->get_gens_raf(), $this->get_version() );

		// Simple script calling
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		// Create coupon and send it after order has been changed to complete - main function
		$this->loader->add_action( 'woocommerce_order_status_completed', $plugin_public, 'gens_create_send_coupon' );
		// Save RAF ID in Order Meta after Order is Complete
		$this->loader->add_action('woocommerce_checkout_update_order_meta', $plugin_public, 'save_raf_id');
		//Show referal link on My Account Page
		$this->loader->add_action('woocommerce_before_my_account', $plugin_public, 'account_page_show_link');
		//Show unused referral coupons
		$this->loader->add_action('woocommerce_before_my_account', $plugin_public, 'account_page_show_coupons');
		//Remove Cookie after checkout if Setting is set
		$this->loader->add_action('woocommerce_thankyou', $plugin_public, 'remove_cookie_after' );

		$this->loader->add_action( 'woocommerce_admin_order_data_after_order_details', $plugin_public, 'show_admin_raf_notes' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_gens_raf() {
		return $this->gens_raf;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Gens_RAF_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
