<?php
/**
 * Customer Reset Password email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-reset-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer username */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $user_login ) ); ?></p>
<?php /* translators: %s: Store name */ ?>
<p><?php printf( esc_html__( 'Siamo qui perché hai chiesto una nuova password per il tuo account %s.', 'woocommerce' ), esc_html( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) ); ?></p>
<?php /* translators: %s: Customer username */ ?>
<p><?php printf( esc_html__( 'Username: %s', 'woocommerce' ), esc_html( $user_login ) ); ?></p>
<p>Se non sei tu o non vuoi creare una nuova password, ignora questa email.</p>
<p><?php esc_html_e( 'Per creare una nuova password clicca sul link che troverai di seguito:', 'woocommerce' ); ?></p>
<p>
	<a class="link" href="<?php echo esc_url( add_query_arg( array( 'key' => $reset_key, 'id' => $user_id ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ); ?>"><?php // phpcs:ignore ?>
		<?php esc_html_e( 'Clicca qui per reimpostare la tua password', 'woocommerce' ); ?>
	</a>
</p>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

echo '<img src="https://staging.agrispesa.it/wp-content/themes/agrispesa/assets/images/elements/firma-email.png" style="width:300px;display:block;margin:24px auto;" />';

do_action( 'woocommerce_email_footer', $email );
