<?php
/**
 * Checkout coupon form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-coupon.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.4
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}

?>
<div class="woocommerce-coupons-section">
<h3 class="checkout--title"><?php esc_html_e( 'Codice sconto', 'woocommerce' ); ?></h3>

	<div class="woocommerce-info coupon-box">
		<span class="icon-star"></span>Hai un codice sconto? <a href="#" class="show-coupon">Usalo qui!</a>
	</div>

	<div class="coupon-form my-coupon" style="display: none;">
		<div class="coupon-form--sx">
			<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" />
		</div>
		<div class="coupon-form--dx">
			<button type="submit" class="btn btn-primary btn-small" name="apply_coupon" value="<?php esc_attr_e( 'Applica', 'woocommerce' ); ?>"><?php esc_html_e( 'Applica', 'woocommerce' ); ?></button>
		</div>
	</div>

	<div class="clear"></div>
</div>
