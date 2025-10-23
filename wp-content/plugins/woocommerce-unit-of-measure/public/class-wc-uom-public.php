<?php
/**
 * The public specific functionality for WooCommerce RRP.
 *
 * @author     Bradley Davis
 * @package    WooCommerce_RRP
 * @subpackage WooCommerce_RRP/public
 * @since      3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly.
endif;

/**
 * Public parent class that outputs everything.
 *
 * @since 3.0.0
 */
class WC_UOM_Public {
	/**
	 * The Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->wc_uom_public_activate();
	}

	/**
	 * Add all filter type actions.
	 *
	 * @since 3.0.0
	 */
	public function wc_uom_public_activate() {
		add_filter( 'woocommerce_get_price_html', array( $this, 'wc_uom_render_output' ), 999, 2 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'wc_uom_add_uom_to_cart_item_price' ), 10, 3 );
	}

	/**
	 * Render the output.
	 *
	 * @since 1.0.1
	 * @param string $price Gives access to product price html.
	 * @return $price
	 */
	public function wc_uom_render_output( $price ) {
		if ( ! $price || ! $this->woo_uom_output_getter() ) :
			return $price;
		endif;

		$price = $price . ' <span class="uom">' . esc_attr( $this->woo_uom_output_getter(), 'woocommerce-uom' ) . '</span>';

		return $price;
	}

	/**
     * Add UOM to cart.php item price.
     *
     * @since 3.2.0
     * @param string $price The product price.
     * @param array $cart_item The cart item array.
     * @param string $cart_item_key The cart item key.
     * @return string Modified price with UOM.
     */
    public function wc_uom_add_uom_to_cart_item_price( $price, $cart_item, $cart_item_key ) {
        $uom_item_id = $cart_item['product_id'];
		$uom = $this->woo_uom_output_getter($uom_item_id);

        if ( $uom ) {
            $price .= ' <span class="uom">' . esc_attr( $uom, 'woocommerce-uom' ) . '</span>';
        }

        return $price;
    }

	/**
	 * Get the uom from post meta for a product.
	 *
	 * @since 3.0.3
	 * @return string $woo_uom_output
	 */
    private function woo_uom_output_getter($uom_item_id = null) {
		if (is_null($uom_item_id)) {
			global $post;
			$uom_item_id = $post->ID;
		}

        $woo_uom_output = get_post_meta( $uom_item_id, '_woo_uom_input', true );

        return $woo_uom_output;
    }
}

/**
 * Instantiate the class
 *
 * @since 3.0.0
 */
$wc_uom_public = new WC_UOM_Public();
