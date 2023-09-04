<?php
/**
 * Gift Card Categories options
 *
 */

defined( 'ABSPATH' ) || exit(); // Exit if accessed directly.

$tab = array(
	'settings-gift-card-categories' => array(
		'configuration-gift-card-categories-list' => array(
			'type'     => 'taxonomy',
			'taxonomy' => YWGC_CATEGORY_TAXONOMY,
			'wp-list-style' => 'classic',
		),
	),
);

return $tab;