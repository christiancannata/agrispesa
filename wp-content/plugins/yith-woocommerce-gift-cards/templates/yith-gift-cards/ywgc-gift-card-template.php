<?php
/**
 * Gift Card product add to cart
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\GiftCards\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Context -> email or pdf
 */

$shop_page_url = apply_filters( 'yith_ywgc_shop_page_url_qr', get_permalink( wc_get_page_id( 'shop' ) ) ? get_permalink( wc_get_page_id( 'shop' ) ) : site_url() );

$date_format     = apply_filters( 'yith_wcgc_date_format', 'Y-m-d' );
$expiration_date = ! is_numeric( $object->expiration ) ? strtotime( $object->expiration ) : $object->expiration;


?>
<table cellspacing="0" class="ywgc-table-template">

	<?php do_action( 'yith_wcgc_template_before_main_image', $object, $context ); ?>

	<?php
	$header_image_url = esc_url( apply_filters( 'ywgc_custom_header_image_url', $header_image_url ) );
	if ( $header_image_url ) :

		// This add the default gift card image when the image is lost.
		if ( substr( $header_image_url, -strlen( '/' ) ) === '/' ) {
			$header_image_url = $default_header_image_url;
		}

		?>

		<tr>

			<td class="ywgc-main-image-td" colspan="2">
				<?php
				if ( 'custom-modal' === $object->design_type && 'email' === $context ) {
					$header_image_64 = $object->design;
					?>
					<img src="<?php echo esc_url( $header_image_64 ); ?>"
						class="ywgc-main-image"
						alt="<?php esc_html_e( 'Gift card image', 'yith-woocommerce-gift-cards' ); ?>"
						title="<?php esc_html_e( 'Gift card image', 'yith-woocommerce-gift-cards' ); ?>">
					<?php
				} else {
					?>
					<img src="<?php echo esc_url( $header_image_url ); ?>"
						class="ywgc-main-image"
						alt="<?php esc_html_e( 'Gift card image', 'yith-woocommerce-gift-cards' ); ?>"
						title="<?php esc_html_e( 'Gift card image', 'yith-woocommerce-gift-cards' ); ?>">
				<?php } ?>

			</td>

		</tr>
	<?php endif; ?>

	<?php do_action( 'yith_wcgc_template_after_main_image', $object, $context ); ?>

	<tr>
		<td class="ywgc-card-product-name" style="float: left">
			<?php

			$product = wc_get_product( $product_id );

			$product_name_text = is_object( $product ) && $product instanceof WC_Product_Gift_Card ? $product->get_name() : esc_html__( 'Gift card', 'yith-woocommerce-gift-cards' );

			echo wp_kses( apply_filters( 'yith_wcgc_template_product_name_text', $product_name_text . ' ' . esc_html__( 'on', 'yith-woocommerce-gift-cards' ) . ' ' . get_bloginfo( 'name' ), $object, $context, $product_id ), 'post' );
			?>
		</td>

		<?php if ( apply_filters( 'ywgc_display_price_template', true, $formatted_price, $object, $context ) ) : ?>

			<td class="ywgc-card-amount" valign="bottom">

				<?php echo wp_kses( apply_filters( 'yith_wcgc_template_formatted_price', $formatted_price, $object, $context ), 'post' ); ?>

			</td>

		<?php endif; ?>

		<?php do_action( 'yith_wcgc_template_after_price', $object, $context ); ?>

	</tr>

	<?php do_action( 'yith_wcgc_template_after_logo_price', $object, $context ); ?>

	<tr>
		<td colspan="2"> <hr style="color: lightgrey"> </td>
	</tr>

	<?php if ( $message ) : ?>
		<tr>
			<td class="ywgc-message-text" colspan="2"> <?php echo nl2br( ( str_replace( '\\', '', $message ) ) ); ?> </td>
		</tr>
		<tr>
			<td><br></td>
		</tr>
	<?php endif; ?>

	<?php do_action( 'yith_wcgc_template_after_message', $object, $context ); ?>

		<tr>
			<td colspan="2" class="ywgc-card-code-column">
				<span class="ywgc-card-code-title"><?php echo wp_kses( apply_filters( 'ywgc_preview_code_title', esc_html__( 'Gift card code:', 'yith-woocommerce-gift-cards' ) ), 'post' ); ?></span>
				<br>
				<br>
				<span class="ywgc-card-code">  <?php echo wp_kses( $gift_card_code, 'post' ); ?></span>
			</td>
		</tr>


	<?php do_action( 'yith_wcgc_template_after_code', $object, $context ); ?>

		<tr>
			<td colspan="2"> <hr style="color: lightgrey"> </td>
		</tr>

		<tr>
			<td colspan="2" class="ywgc-description-template-email-message" style="text-align: center"><?php echo wp_kses( apply_filters( 'yith_ywgc_email_description_text', get_option( 'ywgc_description_template_email_text', esc_html__( 'To use this gift card, you can either enter the code in the gift card field on the cart page or click on the following link to automatically get the discount.', 'yith-woocommerce-gift-cards' ) ) ), 'post' ); ?></td>
		</tr>

</table>
