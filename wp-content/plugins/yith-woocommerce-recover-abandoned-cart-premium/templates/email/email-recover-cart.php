<?php
/**
 * Admin new order email
 *
 * @author YITH
 *
 * @var string   $email_heading
 * @var WC_Email $email
 * @var WC_Order $order
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'woocommerce_email_header', $email_heading, $email );
$order_id           = $order->get_id();
$billing_first_name = $order->get_billing_first_name();
$billing_last_name  = $order->get_billing_last_name();
$order_date         = strtotime( $order->get_date_created() );
?>
	<h2><?php esc_html_e( 'Great news!', 'yith-woocommerce-recover-abandoned-cart' ); ?></h2>
    <h2><?php esc_html_e( 'A new order has been placed thanks to the recovery email reminder', 'yith-woocommerce-recover-abandoned-cart' ); ?></h2>
	<p style="margin-bottom: 30px;"><?php printf( esc_html( __( 'You have received an order from %s. The order is as follows:', 'yith-woocommerce-recover-abandoned-cart' ) ), esc_html( $billing_first_name . ' ' . $billing_last_name ) ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, true, false, $email ); ?>

	<h3>
		<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ); ?>"><?php printf( esc_html( __( 'Order #%s', 'yith-woocommerce-recover-abandoned-cart' ) ), esc_html( $order->get_order_number() ) ); ?></a>
    </h3>

<?php
$lang     = get_post_meta( $order_id, 'wpml_language', true );
$currency = get_post_meta( $order_id, '_order_currency', true );
wc_get_template(
	'pending-order-content.php',
	array(
		'order'    => $order,
		'lang'     => $lang,
		'currency' => $currency,
	)
);

