<?php
if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly
endif;

class Woo_Uom_Output {

  /**
   * The Constructor!
   * @since 1.0.1
   */
  public function __construct() {
    $this->uom_add_actions_filters();
  }

  /**
   * Add actions and filters.
   * @since 1.0.1
   */
  function uom_add_actions_filters() {
    add_filter( 'woocommerce_get_price_html', array( $this, 'woo_uom_render_output' ), 10, 2 );
  }

	/**
	 * Render the output
	 * @since 1.0.1
	 * @return $price + UOM string
	 */
	 function woo_uom_render_output( $price ) {
	  global $post;
	  // Check if uom text exists
	  $woo_uom_output = get_post_meta( $post->ID, '_woo_uom_input', true );
	  // Check if variable OR UOM text exists
	  if ( $woo_uom_output ) :
			$woo_uom_price_string = $price . ' ' . '<span class="uom">' . esc_attr_x( $woo_uom_output, 'woocommerce-uom' ) . '</span>';
			return $woo_uom_price_string;
	  else :
			return $price;
	  endif;
	}

}
// Instantiate the class
$woo_uom_output = new Woo_Uom_Output();
