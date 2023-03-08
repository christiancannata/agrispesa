<?php
/**
 * Welcome Page Class
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * SM_Admin_Welcome class
 */
class Smart_Manager_Admin_Welcome {

	/**
	 * Hook in tabs.
	 */
	public $sm_redirect_url, $plugin_url;

	static $text_domain, $prefix, $sku, $plugin_file;

	public function __construct() {

		$this->sm_redirect_url = admin_url( 'admin.php?page=smart-manager' );

		self::$text_domain = (defined('SM_TEXT_DOMAIN')) ? SM_TEXT_DOMAIN : 'smart-manager-for-wp-e-commerce';
		self::$prefix = (defined('SM_PREFIX')) ? SM_PREFIX : 'sa_smart_manager';
		self::$sku = (defined('SM_SKU')) ? SM_SKU : 'sm';
		self::$plugin_file = (defined('SM_PLUGIN_FILE')) ? SM_PLUGIN_FILE : '';

		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'smart_manager_welcome' ), 11 );
		add_action( 'admin_footer', array( $this, 'smart_manager_support_ticket_content' ) );

		$this->plugin_url = plugins_url( '', __FILE__ );
	}

	/**
	 * Handle welcome page
	 */
	public function show_welcome_page() {
		
		if( empty($_GET['landing-page']) ) {
			return;
		}
		
		switch ( $_GET['landing-page'] ) {
			case 'sm-about' :
				$this->about_screen();
			break;
			case 'sm-faqs' :
				$this->faqs_screen();
			break;
		}

		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#toplevel_page_smart-manager').find('.wp-first-item').closest('li').removeClass('current');
				jQuery('#toplevel_page_smart-manager').find('a[href$=sm-about]').closest('li').addClass('current');
				jQuery('#toplevel_page_smart-manager').find('a[href$=sm-faqs]').closest('li').addClass('current');
				jQuery('#sa_smart_manager_beta_post_query_table').find('input[name="include_data"]').attr('checked', true);
			});
		</script>
		<?php

	}

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 */
	public function admin_head() {

		if ( !( isset($_GET['page']) && ($_GET['page'] == "smart-manager" || $_GET['page'] == "smart-manager-settings") && ( isset($_GET['landing-page']) ) ) ) {
			return;
		}

		?>
		<style type="text/css">
			/*<![CDATA[*/
			.sm-welcome.about-wrap {
				max-width: unset !important;
			}
			.sm-welcome.about-wrap h3 {
				margin-top: 1em;
				margin-right: 0em;
				margin-bottom: 0.1em;
				font-size: 1.25em;
				line-height: 1.3em;
			}
			.sm-welcome.about-wrap .button-primary {
				margin-top: 18px;
			}
			.sm-welcome.about-wrap .button-hero {
				color: #FFF!important;
				border-color: #03a025!important;
				background: #03a025 !important;
				box-shadow: 0 1px 0 #03a025;
				font-size: 1em;
				font-weight: bold;
			}
			.sm-welcome.about-wrap .button-hero:hover {
				color: #FFF!important;
				background: #0AAB2E!important;
				border-color: #0AAB2E!important;
			}
			.sm-welcome.about-wrap p {
				margin-top: 0.6em;
				margin-bottom: 0.8em;
				line-height: 1.6em;
				font-size: 14px;
			}
			.sm-welcome.about-wrap .feature-section {
				padding-bottom: 5px;
			}
			#sm_promo_msg_content a {
				color: #A3B745 !important;
			}
			#sm_promo_msg_content .button-primary {
				background: #a3b745 !important;
				border-color: #829237 #727f30 #727f30 !important;
				color: #fff !important;
				box-shadow: 0 1px 0 #727f30 !important;
				text-shadow: 0 -1px 1px #727f30, 1px 0 1px #727f30, 0 1px 1px #727f30, -1px 0 1px #727f30 !important;

				animation-duration: 5s;
				animation-iteration-count: infinite;
				animation-name: shake-hv;
				animation-timing-function: ease-in-out;
			}
			div#TB_window {
				background: lightgrey;
			}
			@keyframes shake-hv {
				0%, 80% {
					transform: translate(0, 0) rotate(0); }
				60%, 70% {
					transform: translate(0, -0.5px) rotate(2.5deg); }
				62%, 72% {
					transform: translate(0, 1.5px) rotate(-0.5deg); }
				65%, 75% {
					transform: translate(0, -1.5px) rotate(2.5deg); }
				67%, 77% {
					transform: translate(0, 2.5px) rotate(-1.5deg); } }

			#sm_promo_msg_content input[type=checkbox]:checked:before {
				color: #A3B745 !important;
			}
			#sm_promo_valid_msg {
				text-align: center;
				padding-left: 0.5em;
				font-size: 0.8em;
				float: left;
				padding-top: 0.25em;
				font-style: italic;
				color: #A3B745;
			}
			.update-nag, .updated, .error {
				display: none;
			}

			.sm-video-container {
				position: relative;
				padding-top: 56.25%;
			}

			.sm-video-iframe {
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
			}

			/*]]>*/
		</style>
		<script type="text/javascript">
			jQuery(function($) {
				$(document).ready(function() {
					$('#sm_promo_msg').insertBefore('.sm-welcome');
				});
			});
		</script>
		<?php
	}

	/**
	 * Smart Manager's Support Form
	 */
	function smart_manager_support_ticket_content() {

		if ( !( isset($_GET['page']) && ($_GET['page'] == "smart-manager" || $_GET['page'] == "smart-manager-settings") && (isset($_GET['landing-page']) && $_GET['landing-page'] == "sm-faqs") ) ) {
			return;
		}

		global $smart_manager_beta;

		if (!wp_script_is('thickbox')) {
			if (!function_exists('add_thickbox')) {
				require_once ABSPATH . 'wp-includes/general-template.php';
			}
			add_thickbox();
		}

		if( !is_callable( array( $smart_manager_beta, 'get_latest_upgrade_class' ) ) ){
			return;
		}

		$latest_upgrade_class = $smart_manager_beta->get_latest_upgrade_class();

		if ( ! method_exists( $latest_upgrade_class, 'support_ticket_content' ) ) return;

		$plugin_data = get_plugin_data( self::$plugin_file );
		$license_key = get_site_option( self::$prefix.'_license_key' );

		$latest_upgrade_class::support_ticket_content( self::$prefix, self::$sku, $plugin_data, $license_key, 'smart-manager-for-wp-e-commerce' );
	}

	/**
	 * Intro text/links shown on all about pages.
	 */
	private function intro() {

		$version = '';
		if( is_callable( array( 'Smart_Manager', 'get_version' ) ) ) {
			$version = Smart_Manager::get_version();
		}

		?>
		<h1><?php printf( __( 'Thank you for installing Smart Manager %s!', 'smart-manager-for-wp-e-commerce' ), $version ); ?></h1>

		<div style="margin-top:0.3em;"><?php _e( "Glad to have you onboard. We hope Smart Manager adds to your desired success 🏆", 'smart-manager-for-wp-e-commerce' ); ?></div>

		<div id="sm_welcome_feature_section" class="has-2-columns is-fullwidth feature-section col two-col">
			<div class="column col">
				<a href="<?php echo $this->sm_redirect_url; ?>" class="button button-hero"><?php _e( 'Get started with Smart Manager', 'smart-manager-for-wp-e-commerce' ); ?></a>
			</div>
			<div class="column col last-feature">
				<p align="right">
					<?php 
						if ( !wp_script_is( 'thickbox' ) ) {
							if ( !function_exists( 'add_thickbox' ) ) {
								require_once ABSPATH . 'wp-includes/general-template.php';
							}
							add_thickbox();
						}
						?> <a href="https://www.storeapps.org/support/contact-us/?utm_source=sm&utm_medium=welcome_page&utm_campaign=view_docs" target="_blank"> 
								<?php echo __( 'Questions? Need Help?', 'smart-manager-for-wp-e-commerce' );?>
							</a>
						<br>
					<?php if ( SMPRO === true ) { ?>
						<a class="button-primary" href="options-general.php?page=smart-manager&sm-settings" target="_blank"><?php _e( 'Settings', 'smart-manager-for-wp-e-commerce' ); ?></a>
					<?php } ?>
					<a class="button-primary" href="https://www.storeapps.org/knowledgebase_category/smart-manager/?utm_source=sm&utm_medium=welcome_page&utm_campaign=view_docs" target="_blank"><?php _e( 'Docs', 'smart-manager-for-wp-e-commerce' ); ?></a>
				</p>
			</div>
		</div>
		<br>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( $_GET['landing-page'] == 'sm-about' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( add_query_arg( array( 'landing-page' => 'sm-about' ), $this->sm_redirect_url ) ); ?>">
				<?php _e( "Know Smart Manager", 'smart-manager-for-wp-e-commerce' ); ?>
			</a>
			<a class="nav-tab <?php if ( $_GET['landing-page'] == 'sm-faqs' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( add_query_arg( array( 'landing-page' => 'sm-faqs' ), $this->sm_redirect_url ) ); ?>">
				<?php _e( "FAQ's", 'smart-manager-for-wp-e-commerce' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		?>
		<div class="wrap sm-welcome about-wrap">

			<?php $this->intro();?>
			<div class = "col" style="margin:0 auto;">
				<br/>
				<p style="font-size:1em;"><?php echo __( 'Smart Manager is a unique, revolutionary tool that gives you the power to <b>boost your productivity by 10x</b> in managing your <b>WooCommerce</b> store by using a <b>familiar, single page, spreadsheet like interface</b>. ', 'smart-manager-for-wp-e-commerce' ); ?></p>
				<p><?php echo sprintf(__( 'Apart from WooCommerce post types like Products, Orders, Coupons, now you can manage %s. Be it Posts, Pages, Media, WordPress Users, etc. you can now manage everything using Smart Manager.', 'smart-manager-for-wp-e-commerce' ), '<strong>' . __( 'any custom post type in WordPress', 'smart-manager-for-wp-e-commerce' ) . '</strong>' ); ?></p>
				<!-- <div class="headline-feature feature-video">
					<?php echo $embed_code = wp_oembed_get('http://www.youtube.com/watch?v=kOiBXuUVF1U', array('width'=>5000, 'height'=>560)); ?>
				</div> -->
			</div>

			<h3 class="aligncenter"><?php echo __( 'Manage your entire store from a single screen', 'smart-manager-for-wp-e-commerce' ); ?></h3>
			<div class="has-3-columns is-fullwidth feature-section col three-col" >
				<div class="column col">
						<h3><?php echo __( 'Filter / Search Records', 'smart-manager-for-wp-e-commerce' ); ?></h3>
					<div class="sm-video-container">
						<iframe class="sm-video-iframe" src="https://www.youtube.com/embed/20iodFpP5ow" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
						<p>
							<?php echo sprintf(__( 'Simply enter the keyword you wish to filter records in the “Simple Search” field at the top of the grid (%s). If you need to have a more specific search result, then you can switch to “%s“ and then search.', 'smart-manager-for-wp-e-commerce' ), '<a href="https://www.storeapps.org/docs/sm-how-to-filter-records-using-simple-search/?utm_source=sm&utm_medium=welcome_page&utm_campaign=sm_know" target="_blank">' . __( 'see how', 'smart-manager-for-wp-e-commerce' ) . '</a>', '<a href="https://www.youtube.com/watch?v=hX7CcZYo060" target="_blank">' . __( 'Advanced Search', 'smart-manager-for-wp-e-commerce' ) . '</a>' ); ?>
						</p>
					</div>
				<div class="column col">
						<h3><?php echo __( 'Inline Editing', 'smart-manager-for-wp-e-commerce' ); ?></h3>
					<div class="sm-video-container">
						<iframe class="sm-video-iframe" src="https://www.youtube.com/embed/BrvU6GD9pWU" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
						<p>
							<?php echo sprintf(__( 'You can quickly update your Products, Orders, Coupons and Posts from Smart Manager itself. This facilitates editing of multiple rows at a time instead of editing and saving each row separately, %s.', 'smart-manager-for-wp-e-commerce' ), '<a href="https://www.storeapps.org/docs/sm-how-to-use-inline-editing/?utm_source=sm&utm_medium=welcome_page&utm_campaign=sm_know" target="_blank">' . __( 'see how', 'smart-manager-for-wp-e-commerce' ) . '</a>' ); ?>
						</p>
					</div>
				<div class="column last-feature col">
						<h3><?php echo __( 'Show/Hide & Sort Data Columns', 'smart-manager-for-wp-e-commerce' ); ?></h3>
					<div class="sm-video-container">
						<iframe class="sm-video-iframe" src="https://www.youtube.com/embed/WHQtEsmPDbw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
						<p>
							<?php echo __( 'Show/hide multiple data columns of your WooCommerce store data as per your requirements. Sort them in ascending or descending order. Smart Manager also gives you persistent state management.', 'smart-manager-for-wp-e-commerce' ); ?>
						</p>
					</div>
				</div>
			<div class="has-3-columns is-fullwidth feature-section col three-col">
				<div class="column col">
					<h3><?php echo __( 'Delete Records', 'smart-manager-for-wp-e-commerce' ); ?></h3>
					<div class="sm-video-container">
						<iframe class="sm-video-iframe" src="https://www.youtube.com/embed/e9bpXTPdSqc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
						<p>
							<?php echo sprintf(__( 'You can simply select records you want to delete (check the header box if you want to delete all records) and click on the “Delete” icon. All the selected records will be deleted. You can even delete records by applying search filters. %s.', 'smart-manager-for-wp-e-commerce' ), '<a href="https://www.storeapps.org/docs/sm-how-to-delete-rows/?utm_source=sm&utm_medium=welcome_page&utm_campaign=sm_know" target="_blank">' . __( 'See how', 'smart-manager-for-wp-e-commerce' ) . '</a>' ); ?>
						</p>
					</div>
				<div class="column col">
						<h3>
							<?php 
								if ( SMPRO === true ) {
									echo __( 'Bulk Edit', 'smart-manager-for-wp-e-commerce' );											
								} else {
									echo sprintf(__( 'Bulk Edit - %1s (only in %2s)', 'smart-manager-for-wp-e-commerce' ), '<span style="color: red;">' . __( 'Biggest Time Saver', 'smart-manager-for-wp-e-commerce' ) . '</span>' , '<a href="https://www.storeapps.org/product/smart-manager/?utm_source=sm&utm_medium=welcome_page&utm_campaign=sm_know" target="_blank">' . __( 'Pro', 'smart-manager-for-wp-e-commerce' ) . '</a>' );
								}
							?>
						</h3>
					<div class="sm-video-container">
						<iframe class="sm-video-iframe" src="https://www.youtube.com/embed/COXCuX2rFrk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
						<p>
							<?php echo sprintf(__( 'You can change / update multiple fields of the entire store OR for selected items by selecting multiple records and then click on Bulk Edit. %s.', 'smart-manager-for-wp-e-commerce' ), '<a href="https://www.storeapps.org/docs/sm-how-to-use-batch-update/?utm_source=sm&utm_medium=welcome_page&utm_campaign=sm_know" target="_blank">' . __( 'See how', 'smart-manager-for-wp-e-commerce' ) . '</a>' ); ?>
						</p>
					</div>
				<div class="column last-feature col">
						<h3><?php 
								if ( SMPRO === true ) {
									echo __( 'Export CSV', 'smart-manager-for-wp-e-commerce' );											
								} else {
									echo sprintf(__( 'Export CSV (only in %s)', 'smart-manager-for-wp-e-commerce' ), '<a href="https://www.storeapps.org/product/smart-manager/?utm_source=sm&utm_medium=welcome_page&utm_campaign=sm_know" target="_blank">' . __( 'Pro', 'smart-manager-for-wp-e-commerce' ) . '</a>' );
								}
							?>
						</h3>
					<div class="sm-video-container">
						<iframe class="sm-video-iframe" src="https://www.youtube.com/embed/GMgysSQw7_g" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
						<p>
							<?php echo sprintf(__( 'You can export all the records OR filtered records (%s) by simply clicking on the Export CSV button at the bottom right of the grid.', 'smart-manager-for-wp-e-commerce' ), '<i>' . __( 'using Simple Search” or Advanced Search', 'smart-manager-for-wp-e-commerce' ) . '</i>' ); ?>
						</p>
				</div>
			</div>
			<div class="changelog" style="font-size: 1.5em; text-align: center;">
				<h4><a href="<?php echo $this->sm_redirect_url; ?>"><?php _e( 'Get started with Smart Manager', 'smart-manager-for-wp-e-commerce' ); ?></a></h4>
			</div>
			<p style="text-align: right;">
				<a target="_blank" href="<?php echo esc_url( 'https://www.storeapps.org/shop/?utm_source=sm&utm_medium=welcome_page&utm_campaign=sm_know' ); ?>"><?php echo __( 'View our other WooCommerce plugins', 'smart-manager-for-wp-e-commerce' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Output the FAQ's screen.
	 */
	public function faqs_screen() {
		?>
		<div class="wrap sm-welcome about-wrap">

			<?php $this->intro(); ?>
		
			<h3 class="aligncenter"><?php echo __( "FAQ / Common Problems", 'smart-manager-for-wp-e-commerce' ); ?></h3>

			<?php
				$faqs = array(
							array(
									'que' => __( 'Smart Manager is empty?', 'smart-manager-for-wp-e-commerce' ),
									'ans' => sprintf( __( 'Make sure you are using %s of Smart Manager. If still the issue persist, temporarily de-activate all plugins except WooCommerce/WPeCommerce & Smart Manager. Re-check the issue, if the issue still persists, contact us. If the issue goes away, re-activate other plugins one-by-one & re-checking the fields, to find out which plugin is conflicting.', 'smart-manager-for-wp-e-commerce' ), '<a href="https://www.storeapps.org/docs/sm-changelog/" target="_blank">' . __( 'latest version', 'smart-manager-for-wp-e-commerce' ) . '</a>' )
								),
							array(
									'que' => __( 'Smart Manager search functionality not working', 'smart-manager-for-wp-e-commerce' ),
									'ans' => __( 'Request you to kindly de-activate and activate the Smart Manager plugin once and then have a recheck with the search functionality.', 'smart-manager-for-wp-e-commerce' )
								),
							array(
									'que' => __( 'Updating variation parent price/sales price not working?', 'smart-manager-for-wp-e-commerce' ),
									'ans' => __( 'Smart Manager is based on WooCommerce and WPeCommerce and the same e-commerce plugins sets the price/sales price of the variation parents automatically based on the price/sales price of its variations.', 'smart-manager-for-wp-e-commerce' )
								),
							array(
									'que' => __( 'How to manage any custom field of any custom plugin using Smart Manager?', 'smart-manager-for-wp-e-commerce' ),
									'ans' => __( 'Smart Manager will allow you to manage custom field of any other plugin.', 'smart-manager-for-wp-e-commerce' )
								),
							array(
									'que' => __( 'How to add columns to Smart Manager dashboard?', 'smart-manager-for-wp-e-commerce' ),
									'ans' => sprintf( __( 'To show / hide columns in the Smart Manager, %s.', 'smart-manager-for-wp-e-commerce' ), '<a href="https://www.storeapps.org/docs/sm-how-to-show-hide-columns-in-dashboard/?utm_source=sm&utm_medium=welcome_page&utm_campaign=sm_faqs" target="_blank">' . __( 'click here', 'smart-manager-for-wp-e-commerce' ) . '</a>')
								),
							array(
									'que' => __( 'Can I import using Smart Manager?', 'smart-manager-for-wp-e-commerce' ),
									'ans' => __( 'You cannot import using Smart Manager. Use import functionality of WooCommerce.', 'smart-manager-for-wp-e-commerce' )
								),
						);

				
				if ( SMPRO === true ) {
					$faqs[] = array(
									'que' => __( 'I can\'t find a way to do X...', 'smart-manager-for-wp-e-commerce' ),
									'ans' => sprintf(__( 'Smart Manager is actively developed. If you can\'t find your favorite feature (or have a suggestion) %s. We\'d love to hear from you.', 'smart-manager-for-wp-e-commerce' ), '<a class="thickbox" href="' . admin_url('#TB_inline?inlineId=sa_smart_manager_beta_post_query_form&height=550') .'" title="' . __( 'Submit your query', 'smart-manager-for-wp-e-commerce' ) .'">' . __( 'contact us', 'smart-manager-for-wp-e-commerce' ) . '</a>' )
								);
				} else {
					$faqs[] = array(
									'que' => __( 'How do I upgrade a Lite version to a Pro version?', 'smart-manager-for-wp-e-commerce' ),
									'ans' => sprintf( __( 'Follow steps listed here: %s', 'smart-manager-for-wp-e-commerce' ), '<a href="https://www.storeapps.org/docs/how-to-update-from-lite-to-pro-version/" target="_blank">' . __( 'latest version', 'smart-manager-for-wp-e-commerce' ) . '</a>' )
								);
				}

				$faqs = array_chunk( $faqs, 2 );

				foreach ( $faqs as $fqs ) {
					echo '<div class="has-2-columns is-fullwidth two-col">';
					foreach ( $fqs as $index => $faq ) {
						echo '<div' . ( ( $index == 1 ) ? ' class="column col last-feature"' : ' class="column col"' ) . '>';
						echo '<h4>' . sprintf(__( '%s', 'smart-manager-for-wp-e-commerce' ), $faq['que'] ) . '</h4>';
						echo '<p>' . $faq['ans'] . '</p>';
						echo '</div>';
					}
					echo '</div>';
				}
			?>
		</div>
		
		<?php
	}


	/**
	 * Sends user to the welcome page on first activation.
	 */
	public function smart_manager_welcome() {

		if ( ! get_transient( '_sm_activation_redirect' ) ) {
			return;
		}
		
		// Delete the redirect transient
		delete_transient( '_sm_activation_redirect' );

		wp_redirect( admin_url( 'admin.php?page=smart-manager&landing-page=sm-about' ) );
		
		exit;

	}
}

$GLOBALS['smart_manager_admin_welcome'] = new Smart_Manager_Admin_Welcome();
