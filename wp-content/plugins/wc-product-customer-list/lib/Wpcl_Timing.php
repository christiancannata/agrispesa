<?php


class Wpcl_Timing {
	private static $instance = null;
	private $timings = [];

	private function __construct() {
	}

	public static function getInstance() {
		if ( self::$instance == null ) {
			self::$instance = new Wpcl_Timing();
		}

		return self::$instance;
	}

	public function add_timing( $text ) {

		if ( ! defined( 'WPCL_DEBUG' ) || WPCL_DEBUG == false ) {
			return;
		}

		$this->timings[ (string) microtime( true ) ] = $text;
	}

	public function display_timings( $display_in_js_console = true ) {

		if ( ! defined( 'WPCL_DEBUG' ) || WPCL_DEBUG == false ) {
			return false;
		}


		$sorted_timings = $this->timings;
		ksort( $sorted_timings );


		$calculated    = [];
		$previous_time = 0;
		$first_entry   = true;
		$start_time    = 0;

		foreach ( $sorted_timings as $microtime => $text ) {


			$microtime = floatval( $microtime );

			if ( $first_entry ) {
				$start_time = $microtime;
			}

			if ( $previous_time > 0 ) {
				$duration             = $microtime - $previous_time;
				$duration_since_start = $microtime - $start_time;
			} else {
				$duration = $duration_since_start = 'begin';
			}

			$pretty_time = DateTime::createFromFormat( 'U.u', sprintf( '%.f', $microtime ) );

			$calculated[] = [
				$pretty_time->format( 'Y-m-dH:i:s.u' ),
				$text,
				round( $duration, 3 ),
				round( $duration_since_start, 3 ),
			];

			$previous_time = $microtime;
			$first_entry   = false;
		}


		if ( $display_in_js_console ) {
			echo '<script>console.table(' . json_encode( $calculated ) . ')</script>';
		}

		return $calculated;


	}
}