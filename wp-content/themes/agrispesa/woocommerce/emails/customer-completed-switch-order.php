<?php
/**
 * Customer completed subscription change email
 *
 * @author  Brent Shepherd
 * @package WooCommerce_Subscriptions/Templates/Emails
 * @version 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * NOTES ABOUT TEMPLATE EDIT FOR KADENCE WOOMAIL DESIGNER,
 * 1. add hook 'kadence_woomail_designer_email_details' to pull in main text
 * 2. Remove static main text area.
 */
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php
/**
 * @hooked Kadence_Woomail_Designer::email_main_text_area
 */
do_action( 'kadence_woomail_designer_email_details', $order, $sent_to_admin, $plain_text, $email );
?>

<?php do_action( 'woocommerce_subscriptions_email_order_details', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email ); ?>

<h2><?php echo esc_html__( 'New Subscription Details', 'kadence-woocommerce-email-designer' ); ?></h2>
<?php foreach ( $subscriptions as $subscription ) : ?>
	<?php do_action( 'woocommerce_subscriptions_email_order_details', $subscription, $sent_to_admin, $plain_text, $email ); ?>
<?php endforeach; ?>

<?php
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additonal content - this is set in each email's settings.
 */
// $additional_enable = Kadence_Woomail_Customizer::opt( 'additional_content_enable' );
// if ( isset( $additional_content ) && ! empty( $additional_content ) && apply_filters( 'kadence_email_customizer_additional_enable', $additional_enable, $email ) ) {
// 	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
// }

do_action( 'woocommerce_email_footer', $email );
?>
