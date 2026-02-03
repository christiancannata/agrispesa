<?php
/**
 * Customer note email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-note.php.
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


echo wpautop( wptexturize( $message ) );

/**
 * Show user-defined additonal content - this is set in each email's settings.
 */
$additional_enable = Kadence_Woomail_Customizer::opt( 'additional_content_enable' );
if ( isset( $additional_content ) && ! empty( $additional_content ) && apply_filters( 'kadence_email_customizer_additional_enable', $additional_enable, $email ) ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
