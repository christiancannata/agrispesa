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


$settings = array(

	'general' => array(

		'section_general_settings'         => array(
			'name' => esc_html__( 'General settings', 'yith-woocommerce-recover-abandoned-cart' ),
			'type' => 'title',
			'id'   => 'ywrac_section_general',
		),

		'enabled'                          => array(
			'name'      => esc_html__( 'Enable cart recovery', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Select to enable cart recovery', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_enabled',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'enable_shop_manager'              => array(
			'name'      => esc_html__( 'Allow shop manager to manage plugin settings', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'If enabled, shop manager can manage the plugin settings', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_enable_shop_manager',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),

		'cut_off_time'                     => array(
			'name'      => esc_html__( 'Identify a cart as an "abandoned cart" after', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Choose after how many minutes a cart can be considered as "abandoned"', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_cut_off_time_config',
			'default'   => array(
				'cut_off_time' => 60,
				'cut_off_type' => 'minutes',
			),
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'fields'    => array(
				'cut_off_time' => array(
					'type'              => 'number',
					'min'               => 1,
					'std'               => 60,
					'custom_attributes' => 'required style="width:40px"',
				),
				'cut_off_type' => array(
					'type'              => 'select',
					'options'           => array(
						'minutes' => esc_html__( 'Minutes', 'yith-woocommerce-recover-abandoned-cart' ),
						'hours'   => esc_html__( 'Hours', 'yith-woocommerce-recover-abandoned-cart' ),
						'days'    => esc_html__( 'Days', 'yith-woocommerce-recover-abandoned-cart' ),
					),
					'std'               => 'minutes',
					'custom_attributes' => 'style="width:140px"',
				),
			),
		),

		'delete_cart'                      => array(
			'name'      => esc_html__( 'Delete Abandoned Cart after:', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Choose when to delete an abandoned cart. Leave 0 to never delete a cart', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_delete_cart_config',
			'default'   => array(
				'delete_cart_time' => 160,
				'delete_cart_type' => 'hours',
			),
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'fields'    => array(
				'delete_cart_time' => array(
					'type'              => 'number',
					'min'               => 0,
					'std'               => 160,
					'custom_attributes' => 'style="width:40px"',
				),
				'delete_cart_type' => array(
					'type'              => 'select',
					'options'           => array(
						'minutes' => esc_html__( 'Minutes', 'yith-woocommerce-recover-abandoned-cart' ),
						'hours'   => esc_html__( 'Hours', 'yith-woocommerce-recover-abandoned-cart' ),
						'days'    => esc_html__( 'Days', 'yith-woocommerce-recover-abandoned-cart' ),
					),
					'std'               => 'hours',
					'custom_attributes' => 'style="width:140px"',
				),
			),
		),

		// from 1.1.0.
		'pending_orders_enabled'           => array(
			'name'      => esc_html__( 'Enable pending order recovery', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Enable to allow pending orders to be recovered. If an order is pending, you can send out email reminders to encourage the customer to complete their order.', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_pending_orders_enabled',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
		),

		// from 1.1.0.
		'pending_orders_delete'            => array(
			'name'      => esc_html__( 'Delete pending orders after:', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Choose when to delete a pending order. Leave 0 to never delete an order', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_pending_order_delete_config',
			'default'   => array(
				'delete_pending_order_time' => 360,
				'delete_pending_order_type' => 'hours',
			),
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'fields'    => array(
				'delete_pending_order_time' => array(
					'type'              => 'number',
					'min'               => 0,
					'std'               => 360,
					'custom_attributes' => 'style="width:40px"',
				),
				'delete_pending_order_type' => array(
					'type'              => 'select',
					'options'           => array(
						'minutes' => esc_html__( 'Minutes', 'yith-woocommerce-recover-abandoned-cart' ),
						'hours'   => esc_html__( 'Hours', 'yith-woocommerce-recover-abandoned-cart' ),
						'days'    => esc_html__( 'Days', 'yith-woocommerce-recover-abandoned-cart' ),
					),
					'std'               => 'hours',
					'custom_attributes' => 'style="width:140px"',
				),
			),
		),

		'user_selection'                   => array(
			'name'      => esc_html__( 'Recover pending orders and carts of:', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Choose whether to recover carts of all users or only of specific user roles.', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_user_selection',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'all'           => esc_html__( 'All users', 'yith-woocommerce-recover-abandoned-cart' ),
				'role_selected' => esc_html__( 'Only users of specific roles', 'yith-woocommerce-recover-abandoned-cart' ),
			),
			'default'   => 'all',
		),

		'user_roles'                       => array(
			'name'      => esc_html__( 'Choose user role(s)', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Choose for which user roles you want to recover orders and cart', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_user_roles',
			'class'     => 'ywrac-chosen wc-enhanced-select',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'multiple'  => true,
			'options'   => yith_ywrac_get_roles(),
			'deps'      => array(
				'id'    => 'ywrac_user_selection',
				'value' => 'role_selected',
				'type'  => 'hidden',
			),
		),

		'enable_guest'                     => array(
			'name'      => esc_html__( 'Recover carts of guest users:', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Choose whether to recover a cart of a guest user', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_user_guest_enabled',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'never'   => esc_html__( 'Never - Recover carts only of registered users', 'yith-woocommerce-recover-abandoned-cart' ),
				'ever'    => esc_html__( 'Always - Recover all guest carts', 'yith-woocommerce-recover-abandoned-cart' ),
				'privacy' => esc_html__( 'Only if "Recover Abandoned Term and Condition" is checked', 'yith-woocommerce-recover-abandoned-cart' ),
			),
			'default'   => 'never',
		),

		'guest_privacy'                    => array(
			'name'      => esc_html__( '"Recover Abandoned Terms and conditions" text', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Enter the text shown to guest users about the terms and conditions checkbox.', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_guest_privacy',
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'deps'      => array(
				'id'    => 'ywrac_user_guest_enabled',
				'value' => 'privacy',
				'type'  => 'hidden',
			),
			'default'   => esc_html__(
				'If you check this box, you are giving us permission to save some of your details into a contact list. You may receive email messages containing information of commercial or promotional nature concerning this store.
Personal Data collected: email address, first name, last name and phone number.',
				'yith-woocommerce-recover-abandoned-cart'
			),
		),

		'section_end_form'                 => array(
			'type' => 'sectionend',
			'id'   => 'ywrac_section_general_end_form',
		),

		'section_email_settings'           => array(
			'name' => esc_html__( 'User Email Settings', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc' => esc_html__( "Users will receive an email as a reminder of the pending carts and orders they have in your store. Here you can set the sender's info that will be shown to the users.", 'yith-woocommerce-recover-abandoned-cart' ),
			'type' => 'title',
			'id'   => 'ywrac_section_email',
		),

		'sender_name'                      => array(
			'name'      => esc_html__( "Sender's name", 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( "Enter the sender's name for all the emails sent to users", 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_sender_name',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => get_bloginfo( 'name' ),
		),

		'sender_email'                     => array(
			'name'      => esc_html__( "Sender's email address", 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( "Enter the sender's email address", 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_email_sender',
			'type'      => 'yith-field',
			'yith-type' => 'custom',
			'action'    => 'ywrac_custom_email_type',
			'default'   => get_bloginfo( 'admin_email' ),
		),

		'reply_to'                         => array(
			'name'      => esc_html__( 'Reply to:', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Enter the email address that users can use to reply', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_email_reply',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => '',
		),

		'section_email_end_form'           => array(
			'type' => 'sectionend',
			'id'   => 'ywrac_section_email_end_form',
		),

		'section_email_admin_settings'     => array(
			'name' => esc_html__( 'Admin email settings', 'yith-woocommerce-recover-abandoned-cart' ),
			'type' => 'title',
			'id'   => 'ywrac_section_email_admin',
		),

		'enable_email_admin'               => array(
			'name'      => esc_html__( 'Notify admin when a cart is recovered', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'If enabled, admin will get an email when a cart is recovered.', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_enable_email_admin',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'email_admin_sender_name'          => array(
			'name'      => esc_html__( 'Email heading', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Enter the heading for the admin email', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_admin_sender_name',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => esc_html__( 'Newly Recovered Order', 'yith-woocommerce-recover-abandoned-cart' ),
		),

		'email_admin_recipient'            => array(
			'name'      => esc_html__( 'Email recipient', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Enter the email where to send the notification to (comma-separated)', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_admin_email_recipient',
			'type'      => 'yith-field',
			'yith-type' => 'custom',
			'action'    => 'ywrac_custom_email_type',
			'default'   => get_bloginfo( 'admin_email' ),
		),

		'email_admin_subject'              => array(
			'name'      => esc_html__( 'Email subject', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Enter a subject to identify this notification', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_admin_email_subject',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => esc_html__( 'Great news! A new order has been recovered', 'yith-woocommerce-recover-abandoned-cart' ),
		),

		'section_email_admin_end_form'     => array(
			'type' => 'sectionend',
			'id'   => 'ywrac_section_email_admin_end_form',
		),

		'section_coupon_settings'          => array(
			'name' => esc_html__( 'Coupon settings', 'yith-woocommerce-recover-abandoned-cart' ),
			'type' => 'title',
			'id'   => 'ywrac_section_coupon',
		),

		'coupon_prefix'                    => array(
			'name'      => esc_html__( 'Coupon prefix', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Enter a prefix to identify all coupons used to recover a cart and sent to users', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_coupon_prefix',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => 'RAC',
		),

		'coupon_delete_after_use'          => array(
			'name'      => esc_html__( 'Delete used coupons', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Enable to automatically delete coupons once used', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_coupon_delete_after_use',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'coupon_delete_expired'            => array(
			'name'      => esc_html__( 'Delete expired coupons', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Enable to automatically delete coupons once expired', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_coupon_delete_expired',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		'section_coupon_settings_end_form' => array(
			'type' => 'sectionend',
			'id'   => 'ywrac_section_coupon_end_form',
		),


		'section_cron_settings'            => array(
			'name' => esc_html__( 'CRON Settings', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc' => esc_html__( 'CRON are used to automatically send emails to users. The email will be sent when the time set in the email of the template will expire and the CRON will be performed.', 'yith-woocommerce-recover-abandoned-cart' ),
			'type' => 'title',
			'id'   => 'ywrac_section_cron',
		),

		'cron_config'                      => array(
			'name'      => esc_html__( 'Set CRON each', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'      => esc_html__( 'Set the time interval for the recovery emails delivery', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'        => 'ywrac_cron_config',
			'default'   => array(
				'cron_time' => 10,
				'cron_type' => 'minutes',
			),
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'fields'    => array(
				'cron_time' => array(
					'type'              => 'number',
					'min'               => 1,
					'std'               => 10,
					'custom_attributes' => 'required style="width:40px"',
				),
				'cron_type' => array(
					'type'              => 'select',
					'options'           => array(
						'minutes' => esc_html__( 'Minutes', 'yith-woocommerce-recover-abandoned-cart' ),
						'hours'   => esc_html__( 'Hours', 'yith-woocommerce-recover-abandoned-cart' ),
						'days'    => esc_html__( 'Days', 'yith-woocommerce-recover-abandoned-cart' ),
					),
					'std'               => 'minutes',
					'custom_attributes' => 'style="width:140px"',
				),
			),
		),

		'section_cron_settings_end_form'   => array(
			'type' => 'sectionend',
			'id'   => 'ywrac_section_cron_end_form',
		),

	),

);

return apply_filters( 'yith_ywrac_panel_settings_options', $settings );
