<?php
/**
 * Description: Display a notice to install WooCommerce if it's not installed or activated.
 * Author:      Mahdi Yazdani
 * Author URI:  https://mahdiyazdani.com
 * Version:     1.0.0
 * Text Domain: wc-install-notice
 * License:     GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package WC_Install_Notice
 */

namespace WC_Install_Notice;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Class Nag.
 */
class Nag {

	/**
	 * The plugin slug.
	 *
	 * @since 1.0.0
	 */
	const PLUGIN_SLUG = 'woocommerce';

	/**
	 * The plugin file.
	 *
	 * @since 1.0.0
	 */
	const PLUGIN_FILE = 'woocommerce.php';

	/**
	 * The plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $plugin_name = '';

	/**
	 * The plugin file path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $plugin_file = '';

	/**
	 * The dismiss notice transient.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $dismiss_transient = '';

	/**
	 * The plugin learn more link.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $learn_more_link = 'https://wordpress.org/plugins/woocommerce/';

	/**
	 * Set the plugin name for the admin notice.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of the plugin to be used in admin notices.
	 *
	 * @return __CLASS__
	 */
	public function set_plugin_name( $name ) {

		$this->plugin_name = $name;
	
		return $this;
	}

	/**
	 * Set the plugin file path.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path The plugin file path.
	 *
	 * @return __CLASS__
	 */
	public function set_file_path( $path ) {

		$this->plugin_file       = $path;
		$this->dismiss_transient = 'wc_install_notice_dismiss_transient_' . sanitize_title( plugin_basename( $path ) );
	
		return $this;
	}

	/**
	 * Set the WooCommerce plugin learn more link.
	 *
	 * @since 1.0.0
	 *
	 * @param string $link The learn more link.
	 *
	 * @return __CLASS__
	 */
	public function set_learn_more_link( $link ) {

		$this->learn_more_link = $link;
		
		return $this;
	}

	/**
	 * Conditionally hook in an admin notice.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function does_it_requires_nag() {

		// Bail early if the admin screen is not loaded.
		if ( ! is_admin() ) {
			return false;
		}

		// Bail early if the WooCommerce plugin is activated.
		if ( $this->is_activated() ) {
			return false;
		}

		// Bail early if the notice has been dismissed.
		if ( $this->dismiss_transient && get_transient( $this->dismiss_transient ) ) {
			return false;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_notices', array( $this, 'print_nag' ) );
		add_action( 'network_admin_notices', array( $this, 'print_nag' ) );
		add_action( 'wp_ajax_wc_install_notice_dismiss_notice', array( $this, 'dismiss_notice' ) );

		return true;
	}

	/**
	 * Enqueue the admin notice assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue() {

		// Bail early if the plugin file path is not set.
		if ( empty( $this->plugin_file ) ) {
			return;
		}

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : trailingslashit( 'minified' );

		wp_enqueue_style( 'wc-install-notice', plugin_dir_url( $this->plugin_file ) . 'vendor/mahdiyazdani/wc-install-notice/assets/css/' . $min . 'admin.css', array(), '1.0.0' );
		wp_enqueue_script( 'wc-install-notice', plugin_dir_url( $this->plugin_file ) . 'vendor/mahdiyazdani/wc-install-notice/assets/js/' . $min . 'admin.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script(
			'wc-install-notice',
			'wc_install_notice',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wc_install_notice_dismiss_notice_nonce' ),
			)
		);
	}

	/**
	 * Print the admin notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function print_nag() {

		?>
		<div class="notice notice-error wc-install-notice-nux<?php echo ! empty( $this->dismiss_transient ) ? ' is-dismissible' : ''; ?>">
			<span class="notice-icon"></span>
			<div class="notice-content">
				<?php $this->get_admin_notice(); ?>
				<?php $this->install_button(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX dismiss notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function dismiss_notice() {

		// Bail early if the nonce is invalid.
		if ( ! isset( $_POST['nonce'] )
			|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'wc_install_notice_dismiss_notice_nonce' )
			|| ! current_user_can( 'manage_options' )
		) {
			die();
		}

		// Set the transient to dismiss the notice for 1 hour.
		set_transient( $this->dismiss_transient, true, HOUR_IN_SECONDS );
	}

	/**
	 * Return the string to be shown in the admin notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_admin_notice() {

		// Ignore the plugin name if it's not set.
		if ( ! empty( $this->plugin_name ) ) {
			/* translators: %s: Plugin name */
			echo '<h2>' . sprintf( esc_html__( 'Thanks for installing %s, you rock! 🤘', 'wc-install-notice' ), $this->plugin_name ) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<p>' . esc_html__( 'To enable eCommerce features you need to install the WooCommerce plugin.', 'wc-install-notice' ) . '</p>';
	}

	/**
	 * Output a button that will install or activate a plugin if it doesn't exist, or display a disabled button if the
	 * plugin is already activated.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function install_button() {

		// Bail early if the current user doesn't have the required capabilities.
		if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Check if the WooCommerce plugin is installed.
		if ( $this->is_installed() ) {
			$url = $this->is_installed();

			// The plugin exists but isn't activated yet.
			$button = array(
				'message' => __( 'Activate WooCommerce', 'wc-install-notice' ),
				'url'     => $url,
				'classes' => 'activate-now',
			);
		} else {
				// The plugin doesn't exist.
				$url    = wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'install-plugin',
							'plugin' => self::PLUGIN_SLUG,
						),
						self_admin_url( 'update.php' )
					),
					'install-plugin_' . self::PLUGIN_SLUG
				);
				$button = array(
					'message' => __( 'Install WooCommerce', 'wc-install-notice' ),
					'url'     => $url,
					'classes' => ' install-now install-' . self::PLUGIN_SLUG,
				);
		}
		?>
		<p>
			<span class="plugin-card-woocommerce">
				<a 
					href="<?php echo esc_url( $button['url'] ); ?>" 
					class="wc-install-notice <?php echo esc_attr( $button['classes'] ); ?>" 
					data-originaltext="<?php echo esc_attr( $button['message'] ); ?>" 
					data-slug="<?php echo esc_attr( self::PLUGIN_SLUG ); ?>" 
					aria-label="<?php echo esc_attr( $button['message'] ); ?>"
				><?php echo esc_html( $button['message'] ); ?></a>
			</span>

			<?php esc_html_e( 'or', 'wc-install-notice' ); ?>

			<a 
				href="<?php echo esc_url( $this->learn_more_link ); ?>" 
				target="_blank"
				rel="noopener noreferrer"
			><?php esc_html_e( 'Learn more', 'wc-install-notice' ); ?></a>
		</p>
		<?php
	}

	/**
	 * Check if the WooCommerce plugin is activated.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_activated() {

		// This statement prevents from producing fatal errors,
		// in case the WooCommerce plugin is not activated on the site.
		 // phpcs:ignore WooCommerce.Commenting.CommentHooks.HookCommentWrongStyle
		$woocommerce_plugin = apply_filters( 'wc_install_notice_woocommerce_path', self::PLUGIN_SLUG . '/' . self::PLUGIN_FILE );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$subsite_active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$network_active_plugins = apply_filters( 'active_plugins', get_site_option( 'active_sitewide_plugins' ) );

		// Bail early in case the plugin is not activated on the website.
		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		if ( ( empty( $subsite_active_plugins ) || ! in_array( $woocommerce_plugin, $subsite_active_plugins ) ) && ( empty( $network_active_plugins ) || ! array_key_exists( $woocommerce_plugin, $network_active_plugins ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the WooCommerce plugin is installed and return the url to activate it if so.
	 *
	 * @since 1.0.0
	 *
	 * @return string|bool
	 */
	private function is_installed() {

		// Check if the plugin directory exists.
		if ( ! file_exists( WP_PLUGIN_DIR . '/' . self::PLUGIN_SLUG ) ) {
			return false;
		}

		$plugins = get_plugins( '/' . self::PLUGIN_SLUG );

		// Check if the plugin is installed.
		if ( empty( $plugins ) ) {
			return false;
		}

		$keys        = array_keys( $plugins );
		$plugin_file = self::PLUGIN_SLUG . '/' . $keys[0];
		$url         = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'activate',
					'plugin' => $plugin_file,
				),
				admin_url( 'plugins.php' )
			),
			'activate-plugin_' . $plugin_file
		);

		return $url;
	}
}
