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

$email_sent      = '';
$clicks          = '';
$recovered_carts = '';
$conversion      = '';
if ( isset( $_GET['post'] ) ) { //phpcs:ignore
	$post_id         = sanitize_text_field( wp_unslash( $_GET['post'] ) ); //phpcs:ignore
	$email_sent      = intval( apply_filters( 'ywrac_email_template_sent_counter', get_post_meta( $post_id, '_email_sent_counter', true ), $post_id ) );
	$clicks          = intval( apply_filters( 'ywrac_email_template_clicks_counter', get_post_meta( $post_id, '_email_clicks_counter', true ), $post_id ) );
	$recovered_carts = intval( apply_filters( 'ywrac_email_template_cart_recovered', get_post_meta( $post_id, '_cart_recovered', true ), $post_id ) );
	if ( ! empty( $email_sent ) ) {
		$conversion = number_format( 100 * $recovered_carts / $email_sent, 2, '.', '' ) . ' %';
	}
}
return array(
	'label'    => esc_html__( 'Email Report', 'yith-woocommerce-recover-abandoned-cart' ),
	'pages'    => 'ywrac_email',
	'context'  => 'normal',
	'priority' => 'default',
	'tabs'     => array(

		'stats' => array(
			'label'  => esc_html__( 'Report', 'yith-woocommerce-recover-abandoned-cart' ),
			'fields' => apply_filters(
				'ywrac_email_metabox_stat',
				array(
					'ywrac_email_stat'      => array(
						'label' => '',
						'desc'  => sprintf( '<span class="label">%s</span><span class="value">%d</span>', esc_html__( 'Sent Emails', 'yith-woocommerce-recover-abandoned-cart' ), $email_sent ),
						'type'  => 'simple-text',
					),

					'ywrac_click'           => array(
						'label' => '',
						'desc'  => sprintf( '<span class="label">%s</span><span class="value">%d</span>', esc_html__( 'Clicks', 'yith-woocommerce-recover-abandoned-cart' ), $clicks ),
						'type'  => 'simple-text',
					),

					'ywrac_recovered_carts' => array(
						'label' => '',
						'desc'  => sprintf( '<span class="label">%s</span><span class="value">%d</span>', esc_html__( 'Recovered Carts', 'yith-woocommerce-recover-abandoned-cart' ), $recovered_carts ),
						'type'  => 'simple-text',
					),

					'ywrac_conversion'      => array(
						'label' => '',
						'desc'  => sprintf( '<span class="label">%s</span><span class="value">%s</span>', esc_html__( 'Conversion Rate', 'yith-woocommerce-recover-abandoned-cart' ), $conversion ),
						'type'  => 'simple-text',
					),

				)
			),
		),
	),
);
