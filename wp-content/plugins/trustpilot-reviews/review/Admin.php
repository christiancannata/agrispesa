<?php
/**
 * Trustpilot-reviews
 *
 * @package   Trustpilot-reviews
 * @link      https://trustpilot.com
 */

namespace Trustpilot\Review;

/**
 * Trustpilot-reviews
 * 
 * @subpackage Admin
 */
class Admin {

	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	/**
	 * Plugin basename.
	 */
	protected $plugin_basename = null;

	/**
	 * Return an instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->do_hooks();
		}
		return self::$instance;
	}

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 */
	private function __construct() {
		$plugin                = Plugin::get_instance();
		$this->plugin_slug     = $plugin->get_plugin_name();
		$this->version         = $plugin->get_plugin_version();
		$this->plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
	}

	/**
	 * Handle WP actions and filters.
	 */
	private function do_hooks() {
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'wp_ajax_handle_past_orders', array( $this, 'trustpilot_handle_past_orders_callback' ) );
		add_action( 'wp_ajax_handle_save_changes', array( $this, 'trustpilot_save_changes' ) );
		add_action( 'wp_ajax_reload_trustpilot_settings', array( $this, 'wc_reload_trustpilot_settings' ) );
		add_action( 'wp_ajax_check_product_skus', array( $this, 'trustpilot_check_product_skus' ) );
		add_action( 'wp_ajax_get_signup_data', array( $this, 'trustpilot_get_signup_data' ) );
		add_action( 'wp_ajax_get_category_product_info', array( $this, 'trustpilot_get_category_product_info' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'trustpilot_menu' ) );
	}

	public function trustpilot_save_changes() {
		if (current_user_can('manage_options')) {
			if ( isset( $_POST['settings'] ) ) {
				$this->verify_iframe_nonce();
				$settings = sanitize_text_field( $_POST['settings'] );
				update_option( 'trustpilot_settings', $settings );
				echo esc_html( $settings );
			}
			if ( isset( $_POST['pageUrls'] )) {
				$this->verify_iframe_nonce();
				$pageUrls = sanitize_text_field( $_POST['pageUrls'] );
				update_option( 'trustpilot_page_urls', $pageUrls );
				echo esc_html( $pageUrls );
			}
			if ( isset( $_POST['customTrustBoxes'] ) ) {
				$this->verify_iframe_nonce();
				$customTrustBoxes = sanitize_text_field( $_POST['customTrustBoxes'] );
				update_option( 'trustpilot_custom_TrustBoxes', $customTrustBoxes );
				echo esc_html( $customTrustBoxes );
			}
		}
		die();
	}

	public function wc_reload_trustpilot_settings() {
		if ( is_admin() ) {
			$info                = new \stdClass();
			$info->pluginVersion = TRUSTPILOT_PLUGIN_VERSION;
			$info->basis         = 'plugin';
			echo json_encode( $info );
		}
		die();
	}

	public function trustpilot_handle_past_orders_callback() {
		if (current_user_can('manage_options')) {
			if ( isset( $_POST['sync'] ) ) {
				$this->verify_iframe_nonce();
				$period = absint( $_POST['sync'] );
				$this->trustpilot_sync_past_orders( $period );
				$response_json = $this->trustpilot_get_past_orders_info();
				echo wp_kses_data($response_json);
				die();
			} elseif ( isset( $_POST['resync'] ) ) {
				$this->verify_iframe_nonce();
				$this->trustpilot_resync_failed_orders();
				$response_json = $this->trustpilot_get_past_orders_info();
				echo wp_kses_data($response_json);
				die();
			} elseif ( isset( $_POST['issynced'] ) ) {
				$this->verify_iframe_nonce();
				$response_json = $this->trustpilot_get_past_orders_info();
				echo wp_kses_data($response_json);
				die();
			} elseif ( isset( $_POST['showPastOrdersInitial'] ) ) {
				$this->verify_iframe_nonce();
				$value = filter_var( $_POST['showPastOrdersInitial'], FILTER_VALIDATE_BOOLEAN );
				update_option( 'show_past_orders_initial', var_export( $value, true ) );
				die();
			}
		}
		echo json_encode( array( 'error' => 'unsupported command received.' ) );
		die();
	}

	public function trustpilot_check_product_skus() {
		$products = Products::get_instance();
		if ( isset( $_POST['skuSelector'] ) ) {
			$this->verify_iframe_nonce();
			$results  = $products->trustpilot_check_skus( sanitize_text_field( $_POST['skuSelector'] ) );
			echo json_encode( $results );
			die();
		}
	}

	public function trustpilot_get_signup_data() {
		$results = base64_encode( json_encode( $this->get_business_information() ) );
		echo esc_html( $results );
		die();
	}

	public function trustpilot_get_category_product_info() {
		$trustbox         = TrustBox::get_instance();
		$products         = Products::get_instance();
		$category         = trustpilot_get_first_category();
		$categoryProducts = $category ? $products->trustpilot_get_products( 16, 1, $category ) : array();
		$results          = base64_encode( json_encode( $trustbox->get_category_product_info( $categoryProducts ) ) );
		echo esc_html( $results );
		die();
	}

	private function trustpilot_get_past_orders_info() {
		$orders        = PastOrders::get_instance();
		$info          = $orders->get_past_orders_info();
		$info['basis'] = 'plugin';
		return json_encode( $info );
	}

	private function trustpilot_sync_past_orders( $period ) {
		$orders = PastOrders::get_instance();
		$orders->sync( $period );
	}

	private function trustpilot_resync_failed_orders() {
		$orders = PastOrders::get_instance();
		$orders->resync();
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 */
	public function enqueue_admin_styles( $hook ) {
		if ( 'toplevel_page_woocommerce-trustpilot-settings-page' == $hook ) {
			wp_enqueue_style( 'trustpilotSettingsStylesheet', plugins_url( '/assets/css/trustpilot.min.css', __FILE__ ), [], '1.0' );
		}
		wp_enqueue_style( 'trustpilotSideLogoStylesheet', plugins_url( '/assets/css/trustpilot.min.css', __FILE__ ), [], '1.0' );
	}

	/**
	 * Register and enqueue admin-specific javascript
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'toplevel_page_woocommerce-trustpilot-settings-page' != $hook ) {
			return;
		}
		wp_enqueue_script( 'boot_js', plugins_url( '/assets/js/integrationScript.min.js', __FILE__ ), [], '1.0' );
		wp_localize_script(
			'boot_js',
			'trustpilot_integration_settings',
			array(
				'TRUSTPILOT_INTEGRATION_APP_URL' => $this->get_integration_app_url(),
			)
		);
	}

	public function trustpilot_menu() {
		add_menu_page( 'Trustpilot', 'Trustpilot', 'manage_options', 'woocommerce-trustpilot-settings-page', array( $this, 'wc_display_trustpilot_admin_page' ) );
	}

	public function wc_display_trustpilot_admin_page() {
		if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {
				echo '<h1>You don\'t have sufficient rights to modify plugin </h1><br>';
				die();
		}
		if ( trustpilot_compatible() ) {
			if ( isset( $_POST['clear_trustpilot_settings'] ) ) {
				check_admin_referer( 'trustpilot_settings_form' );
				$this->wc_clear_trustpilot_settings();
			}
			$this->wc_display_trustpilot_settings();
		} else {
			if ( version_compare( phpversion(), '5.2.0' ) < 0 ) {
				echo '<h1>Trustpilot plugin requires PHP 5.2.0 above.</h1><br>';
			}
			if ( ! function_exists( 'curl_init' ) ) {
				echo '<h1>Trustpilot plugin requires cURL library.</h1><br>';
			}
		}
	}

	public function get_product_identification_options() {
		$fields         = array( 'none', 'sku', 'id' );
		$optionalFields = array( 'upc', 'isbn', 'brand' );
		$dynamicFields  = array( 'mpn', 'gtin' );
		if ( class_exists( 'woocommerce' ) ) {
			$attrs = array_map(
				function ( $t ) {
					return $t->attribute_name;
				},
				wc_get_attribute_taxonomies()
			);
			foreach ( $attrs as $attr ) {
				foreach ( $optionalFields as $field ) {
					if ( $attr == $field && ! in_array( $field, $fields ) ) {
						array_push( $fields, $field );
					}
				}
				foreach ( $dynamicFields as $field ) {
					if ( stripos( $attr, $field ) !== false ) {
						array_push( $fields, $attr );
					}
				}
			}
		}

		return json_encode( $fields );
	}

	public function format_url( $siteUrl ) {
		$newUrl = ( parse_url( $siteUrl, PHP_URL_HOST ) != '' ) ? parse_url( $siteUrl, PHP_URL_HOST ) : $siteUrl;
		return preg_replace( '/^www./', '', $newUrl );
	}

	public function get_business_country() {
		if ( class_exists( 'woocommerce' ) ) {
			return \WC_Geolocation::geolocate_ip( '', true, true )['country'] ? \WC_Geolocation::geolocate_ip( '', true, true )['country'] : WC()->countries->get_base_country();
		}
	}

	public function get_business_information() {
		$owner = is_super_admin() ? wp_get_current_user() : get_user_by( 'login', get_super_admins()[0] );
		return array(
			'website' => $this->format_url( get_site_url() ),
			'company' => html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			'name'    => html_entity_decode( $owner ? $owner->first_name . ' ' . $owner->last_name : '', ENT_QUOTES ),
			'email'   => get_bloginfo( 'admin_email' ),
			'country' => $this->get_business_country(),
		);
	}

	private function load_iframe() {
		$iframe = '';

		try {
			$pageUrls          = new \stdClass();
			$pageUrls->landing = trustpilot_get_page_url( 'landing' );
			if ( class_exists( 'woocommerce' ) ) {
				$pageUrls->category = trustpilot_get_page_url( 'category' );
				$pageUrls->product  = trustpilot_get_page_url( 'product' );
			}
			$urls                         = trustpilot_get_field( 'trustpilot_page_urls' );
			$customTrustBoxes             = json_encode( trustpilot_get_field( 'trustpilot_custom_TrustBoxes' ) );
			$pageUrls                     = (object) array_merge( (array) $urls, (array) $pageUrls );
			$pageUrlsBase64               = base64_encode( json_encode( $pageUrls ) );
			$integration_app_url          = $this->get_integration_app_url();
			$settings                     = base64_encode( stripslashes( trustpilot_get_settings() ) );
			$past_orders_info             = $this->trustpilot_get_past_orders_info();
			$sku                          = trustpilot_get_product_sku();
			$name                         = trustpilot_get_product_name();
			$version                      = trustpilot_get_woo_version_number();
			$startingUrl                  = trustpilot_get_page_url( 'landing' );
			$productIdentificationOptions = $this->get_product_identification_options();
			$configuration_scope_tree     = base64_encode( json_encode( $this->get_configuration_scope_tree() ) );
			$pluginStatus                 = base64_encode( json_encode( trustpilot_get_field( TRUSTPILOT_PLUGIN_STATUS ) ) );
			$mode                         = class_exists( 'woocommerce' ) ? '' : 'data-mode=\'trustbox-only\'';
			wp_enqueue_script( 'TrustBoxPreviewComponent', TRUSTPILOT_TRUSTBOX_PREVIEW_URL, array(), '1.0');

			$iframe = "
				<script type='text/javascript'>
					function onTrustpilotIframeLoad() {
						if (typeof sendSettings === 'function') {
							if (typeof sendPastOrdersInfo === 'function') {
								sendSettings();
								sendPastOrdersInfo();
							}
						} else {
							window.addEventListener('load', function () {
								sendSettings();
								sendPastOrdersInfo();
							});
						}
					}
				</script>
				<div style='display:block;'>
					<iframe
						style='display: inline-block;'
						src='" . $integration_app_url . "'
						id='configuration_iframe'
						frameborder='0'
						scrolling='no'
						width='100%'
						height='1400px'
						data-plugin-version='" . TRUSTPILOT_PLUGIN_VERSION . "'
						data-source='WooCommerce'
						data-version='WooCommerce-" . $version . "'
						data-page-urls='" . $pageUrlsBase64 . "'
						data-transfer='" . $integration_app_url . "'
						data-past-orders='" . $past_orders_info . "'
						data-settings='" . $settings . "'
						data-product-identification-options='" . $productIdentificationOptions . "'
						data-is-from-marketplace='" . TRUSTPILOT_IS_FROM_MARKETPLACE . "'
						data-configuration-scope-tree='" . $configuration_scope_tree . "'
						data-plugin-status='" . $pluginStatus . "'
						" . $mode . "
						onload='onTrustpilotIframeLoad();'>
					</iframe>
					<div id='trustpilot-trustbox-preview'
						hidden='true'
						data-page-urls='" . $pageUrlsBase64 . "'
						data-custom-trustboxes='" . $customTrustBoxes . "'
						data-settings='" . $settings . "'
						data-src='" . $startingUrl . "'
						data-name='" . $name . "'
						data-sku='" . $sku . "'
						" . $mode . "
						data-source='WooCommerce'
					></div>
				</div>
			";
		} catch ( \Throwable $e ) {
			$message = 'Unable to construct iframe';
			TrustpilotLogger::error(
				$e,
				$message
			);
		} catch ( \Exception $e ) {
			$message = 'Unable to construct iframe';
			TrustpilotLogger::error(
				$e,
				$message
			);
		}

		return $iframe;
	}

	private function get_configuration_scope_tree() {
		if ( is_multisite() ) { // Multisite
			$networks = array();
			$sites    = array();
			$args     = array(
				'public'   => 1,
				'deleted'  => 0,
				'archived' => 0,
				'limit'    => 0,
			);
			if ( function_exists( 'get_sites' ) ) {
				$sites = get_sites( $args ); // WordPress >= 4.6
			} elseif ( function_exists( 'wp_get_sites' ) ) {
				// phpcs:ignore WordPress.WP.DeprecatedFunctions.wp_get_sitesFound -- Legacy support for WP < 4.6.
				$sites = wp_get_sites( $args ); // WordPress < 4.6
			}
			foreach ( $sites as $site ) {
				array_push( $networks, $this->get_network_info( $site->blog_id ) );
			}
			return $networks;
		} else { // Single site
			return array(
				array(
					'ids'    => array( 1 ),
					'names'  => array( 'store' => get_bloginfo( 'name' ) ),
					'domain' => preg_replace( '#^https?://#', '', get_bloginfo( 'url' ) ),
				),
			);
		}
	}

	private function get_network_info( $network_id ) {
		$network = get_blog_details( $network_id );
		return array(
			'ids'    => array( $network->blog_id ),
			'names'  => array( 'store' => $network->blogname ),
			'domain' => preg_replace( array( '#^https?://#', '#/?$#' ), '', $network->domain ),
		);
	}

	private function get_protocol() {
		if ( isset( $_SERVER['HTTPS']) ) {
			if ( 'on' == sanitize_text_field( $_SERVER['HTTPS'] ) || '1' == sanitize_text_field( $_SERVER['HTTPS'] ) ) {
				return 'https:';
			}
		}

		if ( isset( $_SERVER['SERVER_PORT']) && '443' == $_SERVER['SERVER_PORT'] ) {
		  return 'https:';
		}
		return 'http:';
	}

	private function get_integration_app_url() {
		$protocol = 'https:';
		try {
			$protocol = $this->get_protocol();
		} catch ( \Throwable $e ) { // For PHP 7
			$message = 'Unable get protocol of the website switching to default: ' . $protocol;
			TrustpilotLogger::error(
				$e,
				$message,
				array(
					'protocol' => $protocol,
				)
			);
		} catch ( \Exception $e ) { // For PHP 5
			$message = 'Unable get protocol of the website switching to default: ' . $protocol;
			TrustpilotLogger::error(
				$e,
				$message,
				array(
					'protocol' => $protocol,
				)
			);
		}
		return $protocol . TRUSTPILOT_INTEGRATION_APP_URL;
	}

	private function verify_iframe_nonce() {
		$nonce = isset( $_SERVER['HTTP_X_CSRF_TOKEN'] )
			? $_SERVER['HTTP_X_CSRF_TOKEN']
		   : '';

		if ( !wp_verify_nonce( $nonce, 'trustpilot_iframe_form' ) ) {
			die();
		}
	}

	private function wc_display_trustpilot_settings() {
		$settings      = trustpilot_get_settings();
		$allowed_tags = array(
			'a' => array(
				'class' => array(),
				'href'  => array(),
				'rel'   => array(),
				'title' => array(),
			),
			'div' => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
				'id' => array(),
				'hidden' => array(),
				'data-page-urls' => array(),
				'data-settings' => array(),
				'data-src' => array(),
				'data-name' => array(),
				'data-sku' => array(),
				'data-source' => array(),
				'data-plugin-mode' => array(),
				'data-custom-trustboxes' => array(),
			),
			'dl' => array(),
			'dt' => array(),
			'em' => array(),
			'h1' => array(),
			'h2' => array(),
			'h3' => array(),
			'h4' => array(),
			'h5' => array(),
			'h6' => array(),
			'i' => array(),
			'img' => array(
				'alt'    => array(),
				'class'  => array(),
				'height' => array(),
				'src'    => array(),
				'width'  => array(),
			),
			'li' => array(
				'class' => array(),
			),
			'ol' => array(
				'class' => array(),
			),
			'p' => array(
				'class' => array(),
			),
			'span' => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'strong' => array(),
			'ul' => array(
				'class' => array(),
			),
			'script' => array(
				'src'=> array(),
				'id'=> array(),
				'type'=> array(),
			),
			'iframe' => array(
				'style' => array(),
				'src' => array(),
				'id' => array(),
				'frameborder' => array(),
				'scrolling' => array(),
				'width' => array(),
				'height' => array(),
				'data-plugin-version' => array(),
				'data-source' => array(),
				'data-version' => array(),
				'data-page-urls' => array(),
				'data-transfer' => array(),
				'data-past-orders' => array(),
				'data-settings' => array(),
				'data-product-identification-options' => array(),
				'data-is-from-marketplace' => array(),
				'data-configuration-scope-tree' => array(),
				'data-plugin-status' => array(),
				'data-plugin-mode' => array(),
				'onload' => array(),
			),

			'form' => array(
				'method' => array(),
				'id' => array(),
				'style' => array(
					'display'=> array()
				),
			),
			'table' => array(
				'style'=> array(),
				'class'=> array()
			),
			'fieldset' => array(),
			'tr' => array(
				'valign' => array()
			),
			'th' => array(
				'scope' => array(),
			),
			'input' => array(
				'type'=> array(),
				'class'=> array(),
				'name'=> array(),
				'id'=> array(),
				'value'=> array(),
			),
			'script' => array(
				'type'=> array(),
			),
		);

		add_filter(
			'safe_style_css',
			function( $array) {
				$array[] = 'display';
				return $array;
			},
			30
		);

		$settings_html =
			"<div class='wrap'>
				<div id ='trustpilot_iframe_form'>" . wp_nonce_field( 'trustpilot_iframe_form' ) . ' </div>
			' . $this-> load_iframe() . "
                <form method='post' id='trustpilot_settings_form' style='display: none'>
                    <table class='form-table'>" . wp_nonce_field( 'trustpilot_settings_form' ) . "
                        <fieldset>
                            <tr valign='top'>
                                <th scope='row' >
                                    <div>
                                        master_settings_field
                                    </div>
                                </th>
                                <td>
                                    <div>
                                        <input
                                            type='text'
                                            id='master_settings_field'
                                            class='master_settings_field'
                                            name='master_settings_field'
                                            value='" . htmlspecialchars( stripslashes( $settings ) ) . "'
                                        />
                                    </div>
                                </td>
                            </tr>
                        </fieldset>
                    </table>
                    <div class='buttons-container '>
                        <input
                            type='submit'
                            name='clear_trustpilot_settings'
                            value='Clear settings'
                            class='button-primary'
                            id='clear_trustpilot_settings'
                        />
                    </div>
                </form>
            </div>";
			$validatedHTML = wp_kses($settings_html, $allowed_tags);
		if ($settings_html != $validatedHTML) {
			$obj = new \stdClass();
			$obj->valid = $settings_html;
			$obj->cleaned = $validatedHTML;
			$jsonObj = json_encode($obj);

			$trustpilot_api = new TrustpilotHttpClient( TRUSTPILOT_API_URL );
			$trustpilot_api->postLog( $obj );
		}
			echo $settings_html;
	}

	private function wc_clear_trustpilot_settings() {
		update_option( 'trustpilot_settings', trustpilot_get_default_settings() );
		update_option( TRUSTPILOT_PAST_ORDERS_FIELD, '0' );
		update_option( TRUSTPILOT_FAILED_ORDERS_FIELD, '{}' );
		update_option( 'show_past_orders_initial', 'true' );
	}
}
