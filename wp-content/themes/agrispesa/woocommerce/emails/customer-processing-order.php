<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<?php /* translators: %s: Order number */ ?>
<p><?php printf( esc_html__( 'Just to let you know &mdash; we\'ve received your order #%s, and it is now being processed:', 'woocommerce' ), esc_html( $order->get_order_number() ) ); ?></p>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */


do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */

do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
// if ( $additional_content ) {
// 	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
// }

echo '<img src="'.get_template_directory_uri(). '/assets/images/elements/firma-email.png" style="width:300px;display:block;margin:24px auto;" />';

echo '<p style="margin:24px 0;font-size: 11px;color:#999;line-height: 1.2;">';
echo "Pssst — Ricorda che questa mail e il documento che troverai all'interno della tua scatola non sono la Fattura, ma la Conferma d'ordine. Potrai quindi riscontrare differenze tra gli importi previsti dalla Conferma d'ordine e quelli della Fattura.<br/>
Questo perché, quando riempiremo la scatola, peseremo ad uno ad uno i diversi prodotti e verificheremo il peso esatto di ciascuno di essi. Poi perfezioneremo l’ordine ed emetteremo la Fattura in modo che tu possa pagare esattamente quanto ricevuto.
È dunque probabile che l’importo effettivo della Fattura si possa discostare un po' da quanto ti propone la Conferma d'ordine.";
echo '</p>';

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
