<?php
/**
 * Checkout terms and conditions area.
 *
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( apply_filters( 'woocommerce_checkout_show_terms', true ) && function_exists( 'wc_terms_and_conditions_checkbox_enabled' ) ) {
	do_action( 'woocommerce_checkout_before_terms_and_conditions' );

	?>

	<div class="woocommerce-terms-and-conditions-wrapper">

		<div class="checkbox-form form-agree">
			<input id="terms" type="checkbox" style="display:none;" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); // WPCS: input var ok, csrf ok. ?> />
			<label for="terms">Ho letto e accetto i <a href="<?php echo esc_url( home_url( '/termini-condizioni' ) ); ?>" target="_blank">Termini & Condizioni</a></label>
			<input type="hidden" name="terms-field" value="1" />
		</div>

	</div>
	<?php

	do_action( 'woocommerce_checkout_after_terms_and_conditions' );
}
