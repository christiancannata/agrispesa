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
	'label'    => '&nbsp;',
	'class'    => yith_set_wrapper_class(),
	'pages'    => 'ywrac_email',
	'context'  => 'normal',
	'priority' => 'default',
	'tabs'     => array(

		'settings' => array(
			'label'  => esc_html__( 'Settings', 'yith-woocommerce-recover-abandoned-cart' ),
			'fields' => apply_filters(
				'ywrac_email_metabox',
				array(
					'ywrac_email_title'     => array(
						'label' => '',
						'desc'  => '<h3>' . esc_html__( 'Email settings', 'yith-woocommerce-recover-abandoned-cart' ) . '</h3>',
						'type'  => 'title',
					),
					'ywrac_email_active'    => array(
						'label' => esc_html__( 'Active email', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'  => esc_html__( 'Choose to activate or deactivate this email', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'  => 'onoff',
						'std'   => 'yes',
					),
					// @since 1.1.0
					'ywrac_email_type'      => array(
						'label'   => esc_html__( 'Email type', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'    => esc_html__( 'Choose the email type', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'    => 'radio',
						'options' => array(
							'cart'  => esc_html__( 'Abandoned cart', 'yith-woocommerce-recover-abandoned-cart' ),
							'order' => esc_html__( 'Pending order', 'yith-woocommerce-recover-abandoned-cart' ),
						),
						'std'     => 'cart',
					),

					'ywrac_email_subject'   => array(
						'label' => esc_html__( 'Email subject', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'  => esc_html__( 'Enter a subject for this email', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'  => 'text',
						'std'   => '',
					),

					'ywrac_email_auto'      => array(
						'label' => esc_html__( 'Schedule automatic delivery', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'  => esc_html__( 'Choose if schedule automatic delivery for this email', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'  => 'onoff',
						'std'   => 'yes',
					),

					'ywrac_email_time'      => array(
						'label'  => esc_html__( 'Send after', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'   => esc_html__( 'Choose when to send this email to the user. For abandoned carts, this email will be sent X minutes/hours/days after the cart has been abandoned. For pending orders, the email will be sent to the customer X minutes/hours/days after the order has been placed but no payment has been received.', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'   => 'inline-fields',
						'fields' => array(
							'time' => array(
								'type'              => 'number',
								'min'               => 0,
								'std'               => 1,
								'custom_attributes' => 'style="width:40px"',
							),
							'type' => array(
								'type'              => 'select',
								'options'           => array(
									'minutes' => esc_html__( 'Minutes', 'yith-woocommerce-recover-abandoned-cart' ),
									'hours'   => esc_html__( 'Hours', 'yith-woocommerce-recover-abandoned-cart' ),
									'days'    => esc_html__( 'Days', 'yith-woocommerce-recover-abandoned-cart' ),
								),
								'custom_attributes' => 'style="width: 130px"',
								'std'               => 'days',
							),
						),
						'deps'   => array(
							'ids'    => '_ywrac_email_auto',
							'values' => 'yes',
						),
					),

					'ywrac_coupon_title'    => array(
						'label' => '',
						'desc'  => '<h3>' . esc_html__( 'Coupon', 'yith-woocommerce-recover-abandoned-cart' ) . '</h3>',
						'type'  => 'title',
					),

					'ywrac_coupon_enabled'  => array(
						'label' => esc_html__( 'Add coupon to this email', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'  => esc_html__( 'Enable if you want to send a discount coupon with this email', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'  => 'onoff',
						'std'   => 'yes',
					),

					'ywrac_coupon_value'    => array(
						'label'  => esc_html__( 'Coupon value', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'   => esc_html__( 'Enter the coupon value and choose if it is a % coupon or a fixed amount', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'   => 'inline-fields',
						'fields' => array(
							'amount' => array(
								'type'              => 'number',
								'min'               => 1,
								'max'               => 100,
								'std'               => 10,
								'custom_attributes' => 'style="width:40px"',
							),
							'type'   => array(
								'type'              => 'select',
								'options'           => array(
									'percent'    => esc_html__( '% - Percentage', 'yith-woocommerce-recover-abandoned-cart' ),
									'fixed_cart' => esc_html__( 'Fixed amount', 'yith-woocommerce-recover-abandoned-cart' ),
								),
								'custom_attributes' => 'style="width: 180px"',
								'std'               => 'percent',
							),
						),
						'deps'   => array(
							'ids'    => '_ywrac_coupon_enabled',
							'values' => 'yes',
						),
					),

					'ywrac_coupon_validity' => array(
						'label'  => esc_html__( 'Coupon expires after', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'   => esc_html__( 'Choose after how many days the coupon expires. Leave empty if the coupon never expires.', 'yith-woocommerce-recover-abandoned-cart' ),
						'type'   => 'custom',
						'mode'   => 'days',
						'action' => 'yith_ywrac_custom_number_field',
						'std'    => '7',
						'deps'   => array(
							'ids'    => '_ywrac_coupon_enabled',
							'values' => 'yes',
						),
					),

					'ywrac_email_to_send'   => array(
						'label'  => esc_html__( 'Send a test email to:', 'yith-woocommerce-recover-abandoned-cart' ),
						'desc'   => '',
						'type'   => 'custom',
						'action' => 'yith_ywrac_send_email_template',
						'std'    => get_bloginfo( 'admin_email' ),
					),
				)
			),
		),
	),
);
