<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @author YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

return array(
	'email' => array(
		'yith_ywgc_email_settings' => array(
			'type'        => 'custom_tab',
			'action'      => 'yith_ywgc_email_settings',
			'title'       => __( 'Emails', 'yith-woocommerce-gift-cards' ),
			'description' => __( 'Manage and customize the emails sent to users about gift cards.', 'yith-woocommerce-gift-cards' ),
		),
	),
);
