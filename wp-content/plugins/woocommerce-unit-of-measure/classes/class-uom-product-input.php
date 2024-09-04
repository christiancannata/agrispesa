<?php
if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly
endif;

class Woo_Uom_Input {

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
    add_action( 'woocommerce_product_options_inventory_product_data', array( &$this, 'woo_uom_product_fields' ) );
    add_action( 'woocommerce_process_product_meta', array( &$this, 'woo_uom_save_field_input' ) );
  }

  /**
   * Add the custom fields or the UOM to the prodcut general tab
   * @since 1.0
   */
  function woo_uom_product_fields() {
    global $woocommerce, $post;

  	echo '<div class="wc_uom_input">';
  		// Woo_UOM fields will be created here.
  		woocommerce_wp_text_input(
  			array(
  				'id'          => '_woo_uom_input',
  				'label'       => __( 'Unit of Measure', 'woo_uom' ),
  				'placeholder' => '',
  				'desc_tip'    => 'true',
  				'description' => __( 'Enter your unit of measure for this product here.', 'woo_uom' )
  			)
  		);
  	echo '</div>';
  }

  /**
   * Update the database with the new input
   * @since 1.0
   */
   function woo_uom_save_field_input( $post_id ){
    // Woo_UOM text field
    $woo_uom_input = $_POST['_woo_uom_input'];
    update_post_meta( $post_id, '_woo_uom_input', esc_attr( $woo_uom_input ) );
  }

}
// Instantiate the class
$woo_uom_input = new Woo_Uom_Input();
