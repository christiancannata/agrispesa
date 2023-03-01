<?php
/**
 * HTML Template Recover Cart button
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */

?>

<?php if ( ! empty( $label ) && ! empty( $link ) ) : ?>
	<?php $color = apply_filters( 'ywrac_recover_button_color', get_option( 'woocommerce_email_base_color', '#96588a' ) ); ?>
	<a class="ywrac-cart-button" href="<?php echo esc_url( $link ); ?>"><?php echo wp_kses_post( $label ); ?></a>
	<style type="text/css">
		.ywrac-cart-button {
			padding: 15px 30px;
			line-height: 50px;
			background-color: <?php echo esc_html( $color ); ?>;
			text-transform: uppercase;
			text-decoration: none;
			border-radius: 3px;
			font-weight: bold;
			color: white;
		}
	</style>
<?php endif; ?>
