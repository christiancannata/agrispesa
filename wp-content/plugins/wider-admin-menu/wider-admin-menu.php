<?php
/**
 * Plugin Name:             Wider Admin Menu
 * Description: 			Let your admin menu breathe.
 * Author: 					WPChill
 * Version: 				1.4
 * Author URI: 				https:/wpchill.com/
 * Text Domain: 			wider-admin-menu
 * Requires: 				4.6 or higher
 * License:                 GPLv3 or later
 * License URI:             http://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP:            5.6
 * Tested up to:            5.9
 *
 * Copyright 2014-2019      Chris Dillon        chris@strongwp.com
 * Copyright 2019           MachoThemes         office@machothemes.com
 *
 * Original Plugin URI:     https://strongplugins.com/plugins/wider-admin-menu
 * Original Author URI:     https://strongplugins.com
 * Original Author:         https://profiles.wordpress.org/cdillon27/
 *
* NOTE:
 * Chris Dillon transferred ownership rights on: 01/20/2019 06:56:07 PM when ownership was handed over to MachoThemes
 * The MachoThemes ownership period started on: 01/20/2019 06:56:08 PM
 * SVN commit proof of ownership transferral: https://plugins.trac.wordpress.org/changeset/2015927/wider-admin-menu
 * 
 * MachoThemes transferred ownership to WPChill on: 5th of November, 2020. WPChill is a restructure and rebrand of MachoThemes.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WiderAdminMenu
 */
class WiderAdminMenu {

	public function __construct() {
		add_action( 'load-settings_page_wider-admin-menu', array( $this, 'load_admin_scripts' ) );
		add_action( 'load-settings_page_wider-admin-menu', array( $this, 'load_lnt_style' ) );

		add_action( 'admin_head', array( $this, 'custom_admin_style' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );

		// LNT icon
		add_action( 'load-plugins.php', array( $this, 'load_lnt_style' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'wider-admin-menu', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	public function load_admin_scripts() {
		wp_enqueue_style( 'wpmwam-options', plugins_url( '/css/options.css', __FILE__ ) );

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'settings';
		if ( 'settings' == $active_tab ) {

			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script( 'wpmwam-script', plugins_url( '/js/wider-admin-menu.js', __FILE__ ), array( 'jquery-ui-slider' ) );
		}
	}

	public function load_lnt_style() {
		wp_enqueue_style( 'wpmwam-lnt', plugins_url( '/css/lnt.css', __FILE__ ) );
	}

	/**
	 * Install with default setting.
	 */
	public static function plugin_activation() {
		$options = array(
			'wpmwam_width' => 200,
			'wpmwam_lnt'   => 1,
		);
		update_option( 'wpmwam_options', $options );
	}

	/**
	 * Plugin list action links
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return mixed
	 */
	public function plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( __FILE__ ) ) {
			$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=wider-admin-menu.php' ) ) . '">' . esc_html__( 'Settings', 'wider-admin-menu' ) . '</a>';
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Plugin meta row
	 *
	 * @param $plugin_meta
	 * @param $plugin_file
	 * @param $plugin_data
	 * @param $status
	 *
	 * @return array
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( $plugin_file == plugin_basename( __FILE__ ) ) {
			$plugin_meta[] = '<span class="lnt">' . esc_html__( 'Leave No Trace', 'wider-admin-menu' ) . '</span>';
		}

		return $plugin_meta;
	}

	/**
	 * Insert custom admin style.
	 */
	public function custom_admin_style() {
		$wp_version = get_bloginfo( 'version' );
		// Get width option. Prevent zero in case of installation error.
		$wpmwam = get_option( 'wpmwam_options' );
		$w      = (int) $wpmwam['wpmwam_width'];
		if ( ! $w ) {
			$w = 160;
		}
		$wpx  = $w . 'px';
		$w1px = ( $w + 1 ) . 'px';
		$w2px = ( $w + 20 ) . 'px';

		$file = '';

		if ( version_compare( $wp_version, '5', '>' ) ) {
			$file = 'style50';
		} elseif ( version_compare( $wp_version, '4', '>=' ) ) {
			$file = 'style40';
		} elseif ( version_compare( $wp_version, '3.8', '>=' ) ) {
			$file = 'style38';
		} elseif ( version_compare( $wp_version, '3.5', '>=' ) ) {
			$file = 'style35';
		} elseif ( version_compare( $wp_version, '3.3', '>=' ) ) {
			$file = 'style33';
		}

		if ( $file ) {
			include plugin_dir_path( __FILE__ ) . "includes/$file.php";
		}
	}

	/**
	 * Add options page to Settings menu.
	 */
	public function add_options_page() {
		add_options_page(
			'Wider Admin Menu',
			'Wider Admin Menu',
			'manage_options',
			basename( __FILE__ ),
			array(
				$this,
				'settings_page',
			)
		);
	}

	/**
	 * Register the setting.
	 */
	public function register_settings() {
		register_setting( 'wpmwam_settings_group', 'wpmwam_options', array( $this, 'sanitize_options' ) );
	}

	/**
	 * Sanitize user input.
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function sanitize_options( $input ) {
		$input['wpmwam_width'] = sanitize_text_field( $input['wpmwam_width'] );
		$input['wpmwam_lnt']   = isset( $input['wpmwam_lnt'] ) ? $input['wpmwam_lnt'] : 0;

		return $input;
	}

	/**
	 * Our settings page.
	 */
	function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		$wp_version = get_bloginfo( 'version' );
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Wider Admin Menu', 'wider-admin-menu' ); ?></h2>
			<p><?php esc_html_e( 'Adjust the width of the admin menu to accomodate longer menu items.', 'wider-admin-menu' ); ?></p>

			<?php $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'settings'; ?>
			<h2 class="nav-tab-wrapper">
				<a href="?page=wider-admin-menu.php"
				   class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Settings', 'wider-admin-menu' ); ?></a>
				<a href="?page=wider-admin-menu.php&tab=alternate"
				   class="nav-tab <?php echo $active_tab == 'alternate' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Alternate Method', 'wider-admin-menu' ); ?></a>
			</h2>

			<?php
			if ( $active_tab == 'alternate' ) {
				include plugin_dir_path( __FILE__ ) . 'includes/settings-alternate.php';
			} else {
				include plugin_dir_path( __FILE__ ) . 'includes/settings-form.php';
			}
			?>
		</div>
		<?php
	}

}

register_activation_hook( __FILE__, array( 'WiderAdminMenu', 'plugin_activation' ) );

new WiderAdminMenu();
