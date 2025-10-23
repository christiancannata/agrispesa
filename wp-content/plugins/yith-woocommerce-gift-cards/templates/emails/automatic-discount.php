<?php
/**
 * Show a section for the automatic discount link and description
 *
 * @author YITH <plugins@yithemes.com>
 * @package yith-woocommerce-gift-cards\templates\emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$email_button_label_get_option = get_option( 'ywgc_email_button_label', esc_html__( 'Apply your gift card code', 'yith-woocommerce-gift-cards' ) );

?>
<div class="ywgc-add-cart-discount">
	<div class="ywgc-discount-link-section">
		<a
			<?php if ( 'email' === $context ) : ?>
				href="<?php echo esc_url( $apply_discount_url ); ?>"
			<?php endif; ?>
			class="ywgc-discount-link" style="background-color:<?php echo esc_attr( get_option( 'ywgc_plugin_main_color', '#000000' ) ); ?>"><?php echo wp_kses_post( empty( $email_button_label_get_option ) ? __( 'Apply your gift card code', 'yith-woocommerce-gift-cards' ) : $email_button_label_get_option ); ?></a>
	</div>
</div>
