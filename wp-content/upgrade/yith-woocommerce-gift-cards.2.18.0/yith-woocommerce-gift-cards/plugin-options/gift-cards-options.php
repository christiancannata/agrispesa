<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package yith-woocommerce-gift-cards\plugin-options\
 */

$tab_options = array(
	'gift-cards' => array(
		'custom-post-type_list_table' => array(
			'type'      => 'post_type',
			'post_type' => 'gift_card',
		),
	),
);

return $tab_options;
