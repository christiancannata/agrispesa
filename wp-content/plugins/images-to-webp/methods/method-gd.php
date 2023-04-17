<?php

defined('ABSPATH') || exit;

class webp_converter{

	public function convertImage( $path, $quality ){
		ini_set( 'memory_limit', '1G' );
		set_time_limit( 120 );

		$output = $path . '.webp';
		$image_extension = pathinfo( $path, PATHINFO_EXTENSION );
		$methods = array(
			'jpg' => 'imagecreatefromjpeg',
			'jpeg' => 'imagecreatefromjpeg',
			'png' => 'imagecreatefrompng',
			'gif' => 'imagecreatefromgif'
		);

		try{
			$image = @$methods[ $image_extension ]( $path );
			imageistruecolor( $image );
			imagepalettetotruecolor( $image );
			imagewebp( $image, $output, $quality );
		}catch( \Throwable $e ){
			error_log( print_r( $e, 1 ) );
			return false;
		}

		return array(
			'path' => $output,
			'size' => array(
				'before' => filesize( $path ),
				'after' => filesize( $output )
			)
		);
	}

}