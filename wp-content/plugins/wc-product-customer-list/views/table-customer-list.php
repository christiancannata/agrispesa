<?php
/**
 * @package WC_Product_Customer_List
 * @version 2.8.5
 */




if ( ! function_exists( 'wpcl_count_rightpress_entries' ) ) {
	function wpcl_count_rightpress_entries( $display_values ) {
		$found_keys    = array();
		$highest_count = 0;

		foreach ( $display_values as $display_value ) {
			if ( ! isset( $found_keys[ $display_value['key'] ] ) ) {
				$found_keys[ $display_value['key'] ] = 1;
			} else {
				$found_keys[ $display_value['key'] ] ++;
			}

			$highest_count = $found_keys[ $display_value['key'] ] > $highest_count ? $found_keys[ $display_value['key'] ] : $highest_count;
		}


		return $highest_count;

	}
}



