<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version 3.7.0
 */

/**
 * NOTES ABOUT TEMPLATE EDIT FOR KADENCE WOOMAIL DESIGNER,
 * 1. add hook 'kadence_woomail_designer_email_details' to pull in main text
 * 2. Remove static main text area.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

/**
 * @hooked Kadence_Woomail_Designer::email_main_text_area
 */
do_action( 'kadence_woomail_designer_email_details', $order, $sent_to_admin, $plain_text, $email );

$deliveryDate = get_order_delivery_date_from_date(new \DateTime(), null, $order->get_shipping_postcode());
//$shipping_date = get_order_delivery_date_from_date(new \DateTime(), null, $order->get_shipping_postcode())->format("l d M Y");
$shipping_date_weekday = ($deliveryDate) ? $deliveryDate->format("l") : '';
$shipping_date_year = ($deliveryDate) ? $deliveryDate->format("Y") : '';
$shipping_date_month = ($deliveryDate) ? $deliveryDate->format("m") : '';
$shipping_date_day = ($deliveryDate) ? $deliveryDate->format("d") : '';

$shipping_month_it = '';
if ($shipping_date_month === '01') {
	$shipping_month_it = 'Gennaio';
} else if ($shipping_date_month === '02') {
	$shipping_month_it = 'Febbraio';
} else if ($shipping_date_month === '03') {
	$shipping_month_it = 'Marzo';
} else if ($shipping_date_month === '04') {
	$shipping_month_it = 'Aprile';
} else if ($shipping_date_month === '05') {
	$shipping_month_it = 'Maggio';
} else if ($shipping_date_month === '06') {
	$shipping_month_it = 'Giugno';
} else if ($shipping_date_month === '07') {
	$shipping_month_it = 'Luglio';
} else if ($shipping_date_month === '08') {
	$shipping_month_it = 'Agosto';
} else if ($shipping_date_month === '09') {
	$shipping_month_it = 'Settembre';
} else if ($shipping_date_month === '10') {
	$shipping_month_it = 'Ottobre';
} else if ($shipping_date_month === '11') {
	$shipping_month_it = 'Novembre';
} else if ($shipping_date_month === '12') {
	$shipping_month_it = 'Dicembre';
}
$shipping_weekday_it = '';
if ($shipping_date_weekday === 'Monday') {
	$shipping_weekday_it = 'Lunedì';
} else if ($shipping_date_weekday === 'Tuesday') {
	$shipping_weekday_it = 'Martedì';
} else if ($shipping_date_weekday === 'Wednesday') {
	$shipping_weekday_it = 'Mercoledì';
} else if ($shipping_date_weekday === 'Thursday') {
	$shipping_weekday_it = 'Giovedì';
} else if ($shipping_date_weekday === 'Friday') {
	$shipping_weekday_it = 'Venerdì';
} else if ($shipping_date_weekday === 'Saturday') {
	$shipping_weekday_it = 'Sabato';
} else if ($shipping_date_weekday === 'Sunday') {
	$shipping_weekday_it = 'Domenica';
}

if($deliveryDate) {
	echo 'La consegna è prevista per ' . $shipping_weekday_it . ' ' . $shipping_date_day . ' ' . $shipping_month_it . ' ' . $shipping_date_year . '.';
}
echo 'Controlla il tuo account per tenere sotto controllo la consegna e il tuo ordine.';


/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additonal content - this is set in each email's settings.
 */
// $additional_enable = Kadence_Woomail_Customizer::opt( 'additional_content_enable' );
// if ( isset( $additional_content ) && ! empty( $additional_content ) && apply_filters( 'kadence_email_customizer_additional_enable', $additional_enable, $email ) ) {
// 	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
// }

echo '<p style="text-align: center;">Per informazioni chiamaci allo 0173.386204 oppure scrivi ad agrispesa@agrispesa.it</p><br/>';
echo '<p style="text-align: center;font-size:12px; line-height: 1.4;color:#999;">Ricorda che questa mail e il documento che troverai all\'interno della tua scatola non
sono la Fattura, ma la Conferma d\'ordine. Troverai la Fattura dentro il tuo profilo sul nostro sito.</p><br/>';


/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
