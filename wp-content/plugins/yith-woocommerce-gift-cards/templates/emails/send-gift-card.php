<?php
/**
 * Send the gift card code email
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\GiftCards\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * @hooked YITH_WooCommerce_Gift_Cards_Premium::include_css_for_emails() Add CSS style to gift card emails header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

/**
 * DO_ACTION: ywgc_gift_cards_email_before_preview
 *
 * Display the introductory text on the gift card email before the gift card preview.
 *
 * @param string $introductory_text the introductory text
 * @param object $gift_card the gift card object
 */
do_action( 'ywgc_gift_cards_email_before_preview', $introductory_text, $gift_card );

/**
 * DO_ACTION: ywgc_gift_cards_email_before_preview_gift_card_param
 *
 * Allow to add content before the gift card preview in the email.
 *
 * @param object $gift_card the gift card object
 */
do_action( 'ywgc_gift_cards_email_before_preview_gift_card_param', $gift_card );

YITH_YWGC()->preview_digital_gift_cards( $gift_card, 'email', $case );

/**
 * DO_ACTION: ywgc_gift_card_email_after_preview
 *
 * Allow to add content after the gift card preview in the email.
 *
 * @param object $gift_card the gift card object
 */
do_action( 'ywgc_gift_card_email_after_preview', $gift_card );

/*
 * @hooked YITH_WooCommerce_Gift_Cards_Premium::add_footer_information() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
