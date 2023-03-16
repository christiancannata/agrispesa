<?php
/**
 * Parse Shortcode and display maps.
 *
 * @package Woocommerce Delivery Area Pro
 * @author Flipper Code <hello@flippercode.com>
 */
$product_id = '';
if ( isset( $options['product_id'] ) ) {
	 $product_id = $options['product_id'];
	 $map_div = 'product_avalibility' . $product_id . '';
} else {
	$map_div = 'product_avalibility';

}

$dboptions = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );

$delivery_area = new WDAP_Delivery_Area();
$map_data = $delivery_area->wdap_get_all_zipcodes( $product_id );
$all_zip_codes = array();
if(isset($map_data['map_data']['allzipcode']) && count($map_data['map_data']['allzipcode']) > 0 ){
	$all_zip_codes = $map_data['map_data']['allzipcode'];
}

global $wpdb;
$table_name = $wpdb->prefix . 'wdap_geocode_details'; 
$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

if ( $wpdb->get_var( $query ) == $table_name ) {
	$map_data['map_data']['geocode_zipcode'] = $delivery_area->wdap_get_all_zipcodes_with_lat_long($all_zip_codes);
}

$random_id = uniqid();
$map_data['map_data']['product_id'] = $product_id;
$map_data['map_data']['map_id'] = $random_id;
$autosuggest = 'id="pac-input' . esc_attr( $random_id ) . '"';

$height = $width = $map_output = '';
if ( ! empty( $dboptions['wdap_googleapikey'] ) ) {

	if ( ! empty( $options['from_tab'] ) ) {

		$map_data['map_data']['from_tab'] = $options['from_tab'];
		$height = ! empty( $dboptions['wdap_map_height'] ) ? stripslashes( wp_strip_all_tags( $dboptions['wdap_map_height'] ) ) : 700;
		$width  = ! empty( $dboptions['wdap_map_width'] ) ? stripslashes( wp_strip_all_tags( $dboptions['wdap_map_width'] ) ) . 'px' : '100%';
	} else {

		$height = ! empty( $dboptions['shortcode_map_height'] ) ? stripslashes( wp_strip_all_tags( $dboptions['shortcode_map_height'] ) ) : 500;
		$width  = ! empty( $dboptions['shortcode_map_width'] ) ? stripslashes( wp_strip_all_tags( $dboptions['shortcode_map_width'] ) ) . 'px' : '100%';
	}

	$placeholder_text = esc_html__( 'Find Your Location', 'woo-delivery-area-pro' );
	$placeholder = apply_filters( 'wdap_placeholder_search', $placeholder_text );

	$map_data = isset( $map_data['map_data'] ) ? json_encode( $map_data['map_data'] ) : array();
	$map_output .= '<div  class="wdap-shortcode-container" >';
	$map_output .= '<div  class="wdap-shortcode-parent" >';

	$map_output .= '<input ' . $autosuggest . ' class="controls pac-input" type="text" placeholder="' . esc_attr( $placeholder ) . '">';
	$map_title = '';
	if ( ! empty( $dboptions['shortcode_map_title'] ) && empty( $options['from_tab'] ) ) {
		$map_title = '<h1 class="wdap-hero-title">' . stripslashes( wp_strip_all_tags( $dboptions['shortcode_map_title'] ) ) . '</h1>';
	}
	$map_output .=apply_filters('wdap_map_title',$map_title);


	$map_output .= '<div class="wdap_map ' . esc_attr( $map_div ) . '" style="width:' . esc_attr( $width ) . ';margin-bottom:20px; height:' . esc_attr( $height ) . 'px;" id="' . esc_attr( $random_id ) . '" ></div>';

	$map_description = '';
	if ( ! empty( $dboptions['shortcode_map_description'] ) && empty( $options['from_tab'] ) ) {
		$map_description .= '<div class="wdap-shortcode-desc" ><span>' . stripslashes( wp_strip_all_tags( $dboptions['shortcode_map_description'] ) ) . '</span></div>';
	}
	$map_output .=apply_filters('wdap_map_description',$map_description);
	
	$map_output .= '<script>jQuery(document).ready(function($) {';
	if ( $product_id ) {
		$map_output .= 'var map' . $product_id . ' = $("#' . $random_id . '").deliveryMap(' . $map_data . ').data("wdap_delivery_map");';
	} else {
		$map_output .= 'var map = $("#' . $random_id . '").deliveryMap(' . $map_data . ').data("wdap_delivery_map");';
	}
	$map_output .= '});

	</script>';
	$map_output .= '</div>';
	$map_output .= '</div>';
}

return $map_output;
