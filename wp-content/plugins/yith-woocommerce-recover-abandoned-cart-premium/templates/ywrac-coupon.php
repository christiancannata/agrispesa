<?php
/**
 * HTML Template Coupon
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */

?>

<?php if ( ! empty( $coupon_code ) ) : ?>
	<span class="ywrac-coupon-box"><?php echo $coupon_code; ?></span>
	<style type="text/css">
		.ywrac-coupon-box {
			padding: 10px 30px;
			background-color: #f9f9f9;
			text-transform: uppercase;
			border: 2px dashed #d0d0d0;
			font-size: 20px;
			font-weight: bold;
		}
	</style>
<?php endif; ?>
