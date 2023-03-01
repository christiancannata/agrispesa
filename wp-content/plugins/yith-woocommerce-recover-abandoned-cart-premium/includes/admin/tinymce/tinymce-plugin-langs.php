<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 */


if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( '_WP_Editors' ) ) {
	require ABSPATH . WPINC . '/class-wp-editor.php';
}
/**
 * Tinymce plugin translation
 *
 * @return string
 */
function ywrac_tinymce_plugin_translation() {
	$strings = array(
		'firstname'             => __( 'User First Name', 'yith-woocommerce-recover-abandoned-cart' ),
		'lastname'              => __( 'User Last Name', 'yith-woocommerce-recover-abandoned-cart' ),
		'fullname'              => __( 'Full Name', 'yith-woocommerce-recover-abandoned-cart' ),
		'useremail'             => __( 'User Email', 'yith-woocommerce-recover-abandoned-cart' ),
		'cartcontent'           => __( 'Cart Content', 'yith-woocommerce-recover-abandoned-cart' ),
		'cartlink'              => __( 'Cart Link', 'yith-woocommerce-recover-abandoned-cart' ),
		'recoverbutton'         => __( 'Recover Button', 'yith-woocommerce-recover-abandoned-cart' ),
		'cartlink-label'        => __( 'Recover Cart', 'yith-woocommerce-recover-abandoned-cart' ),
		'unsubscribelink'       => __( 'Unsubscribe Link', 'yith-woocommerce-recover-abandoned-cart' ),
		'unsubscribelink-label' => __( 'To unsubscribe from this mail click here', 'yith-woocommerce-recover-abandoned-cart' ),
		'coupon'                => __( 'Coupon', 'yith-woocommerce-recover-abandoned-cart' ),
	);

	$locale     = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.tc_button", ' . wp_json_encode( $strings ) . ");\n";

	return $translated;
}

$strings = ywrac_tinymce_plugin_translation();
