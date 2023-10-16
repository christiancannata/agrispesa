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
 * @subpackage Plugin
 */
class TrustBox {
	/**
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 * 
	 * @var      string
	 */
	protected $plugin_name = 'Trustpilot-review';
	protected $products    = null;

	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	/**
	 * Setup instance attributes
	 */
	private function __construct() {
		$this->plugin_version = TRUSTPILOT_REVIEWS_VERSION;
	}

	/**
	 * Return an instance of this class.
	 * 
	 * @return    object    A single instance of this class.
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
	 * Return the plugin slug.
	 * 
	 * @return    Plugin name variable.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Return the plugin version.
	 * 
	 * @return    Plugin version variable.
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}


	private function do_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_trustbox' ) );
		add_action( 'posts_results', array( $this, 'get_current_category_products' ) );
	}

	public function getPage() {
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			if ( is_product() ) {
				return 'product';
			} elseif ( is_product_category() ) {
				return 'category';
			} elseif ( is_front_page() ) {
				return 'landing';
			}
		} else {
			if ( is_front_page() ) {
				return 'landing';
			}
		}
	}

	public function load_trustbox( $results ) {
		try {
			$trustbox  = trustpilot_get_settings( TRUSTPILOT_TRUSTBOX_CONFIGURATION );
			$settings  = array(
				'page' => $this->getPage(),
				'sku'  => $this->getSku(),
				'name' => $this->getName(),
			);
			$trusboxes = array(
				'trustboxes' => isset( $trustbox->trustboxes ) ? $trustbox->trustboxes : array(),
			);
			if ( $this->getPage() == 'category' && $this->repeatData( $trusboxes['trustboxes'] ) ) {
				$trusboxes['categoryProductsData'] = $this->get_category_product_info( $this->products );
			}
			$this->load_trustboxes( $settings, $trusboxes );
		} catch ( \Throwable $e ) {
			$message = 'Unable to load trustbox ';
			TrustpilotLogger::error($e, $message);
		} catch ( \Exception $e ) {
			$message = 'Unable to load trustbox ';
			TrustpilotLogger::error($e, $message);
		}
		
		return $results;
	}

	public function getName() {
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && is_product() ) {
			$product = wc_get_product( get_the_id() );
			return method_exists( $product, 'get_name' ) ? $product->get_name() : $product->get_title();
		}
		return null;
	}

	public function get_current_category_products( $results ) {
		try {
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && is_product_category() ) {
				$products = array();
				foreach ( $results as $result ) {
					if ( 'product' == $result->post_type ) {
						$product = wc_get_product( $result->ID );
						array_push( $products, $product );
					}
				}
				$this->products = $products;
			}
		} catch ( \Throwable $e ) {
			$message = 'Unable to get current category products ';
			TrustpilotLogger::error($e, $message);
		} catch ( \Exception $e ) {
			$message = 'Unable to get current category products ';
			TrustpilotLogger::error($e, $message);
		}
	
		return $results;
	}

	public function get_category_product_info( $products ) {
		$info = array();
		foreach ( $products as $product ) {
			$data             = new \stdClass();
			$data->productUrl = get_permalink( $product->get_id() );
			$data->id         = $product->get_id();
			$data->sku        = trustpilot_get_inventory_attribute( 'sku', $product );

			$data->name    = $product->get_name();
			$variation_ids = $product->get_children();
			if ( $variation_ids ) {
				$data->variationIds  = $variation_ids;
				$data->variationSkus = array();

				foreach ( $variation_ids as $variation_id ) {
					$variation = wc_get_product( $variation_id );
					$sku       = trustpilot_get_inventory_attribute( 'sku', $variation );
					array_push( $data->variationSkus, $sku );
				}
			}
			array_push( $info, $data );
		}
		return $info;
	}

	public function getSku() {
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && is_product() ) {
			$product = wc_get_product( get_the_id() );
			if ( $product->is_type( 'variable' ) ) {
				// make a list of product sku plus skus of all variations
				$skus       = array();
				$productSku = trustpilot_get_inventory_attribute( 'sku', $product );
				if ( $productSku ) {
					array_push( $skus, $productSku );
				}
				array_push( $skus, TRUSTPILOT_PRODUCT_ID_PREFIX . trustpilot_get_inventory_attribute( 'id', $product ) );
				$variation_ids = $product->get_children();
				if ( $variation_ids ) {
					foreach ( $variation_ids as $variation_id ) {
						$variation = wc_get_product( $variation_id );
						$sku       = trustpilot_get_inventory_attribute( 'sku', $variation );
						if ( $sku ) {
							array_push( $skus, $sku );
						}
						array_push( $skus, TRUSTPILOT_PRODUCT_ID_PREFIX . trustpilot_get_inventory_attribute( 'id', $variation ) );
					}
				}
				return implode( ',', $skus );
			} else {
				$skus = array();
				$sku  = trustpilot_get_inventory_attribute( 'sku', $product );
				if ( $sku ) {
					array_push( $skus, $sku );
				}
				array_push( $skus, TRUSTPILOT_PRODUCT_ID_PREFIX . trustpilot_get_inventory_attribute( 'id', $product ) );
				return $skus;
			}
		}
	}

	private function repeatData( $trustBoxes ) {
		foreach ( $trustBoxes as $trustbox ) {
			if ( property_exists( $trustbox, 'repeat' ) && $trustbox->repeat ) {
				return true;
			}
		}
		return false;
	}

	private function load_trustboxes( $settings, $trustboxes ) {
		if (count($trustboxes['trustboxes']) > 0) {
			wp_register_script( 'trustbox', plugins_url( 'assets/js/trustBoxScript.min.js#trustpilot_async', __FILE__ ), [], '1.0' );
			wp_localize_script( 'trustbox', 'trustbox_settings', $settings );
			wp_localize_script( 'trustbox', 'trustpilot_trustbox_settings', $trustboxes );
			wp_enqueue_script( 'trustbox' );
		}
	}
}
