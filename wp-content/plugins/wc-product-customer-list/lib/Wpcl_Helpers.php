<?php


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


class Wpcl_Helpers {

	public function __construct() {
	}


	/*
	 * https://www.php.net/manual/en/function.array-map.php#112857
	 */
	public static function array_map_recursive( $callback, $array ) {
		foreach ( $array as $key => $value ) {
			if ( is_array( $array[ $key ] ) ) {
				$array[ $key ] = self::array_map_recursive( $callback, $array[ $key ] );
			} else {
				$array[ $key ] = call_user_func( $callback, $array[ $key ] );
			}
		}

		return $array;
	}


	public static function is_it_a_woo_product( $post_id ) {
		$product_test = get_post( $post_id );

		if ( empty( $product_test )
		     || in_array(
			        $product_test->post_type,
			        [
				        'product',
				        'product_variation',
			        ]
		        ) === false ) {

			return false;
		}

		return true;
	}

}