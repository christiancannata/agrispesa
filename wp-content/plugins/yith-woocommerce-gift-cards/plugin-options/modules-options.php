<?php
/**
 * Modules options
 *
 * @package YITH\Booking\Options
 */

defined( 'ABSPATH' ) || exit(); // Exit if accessed directly.

return array(
	'modules' => array(
		'modules-tab' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_ywgc_print_modules_tab',
		),
	),
);