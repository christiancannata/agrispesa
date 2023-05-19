<?php
/**
 * Gift Card Product Options
 *
 * @package YITH\GiftCards\Includes\Admin\Views
 */

global $thepostid;

?>
	<div class="ywgc-product-edit-page-options yith-plugin-ui options_group show_if_gift-card">

		<div class="yith-plugin-ui ywgc-product-edit-page-amount-options">
			<h2 class="ywgc-product-edit-page-options-title">
				<?php echo esc_html_x( 'Gift Card Options', '[Admin] Gift Card Options in product edit page', 'yith-woocommerce-gift-cards' ); ?>
			</h2>

			<div class="form-field ywgc-amounts-list">

				<label for="gift_card-amount"><?php _e( 'Gift card amounts', 'yith-woocommerce-gift-cards' ); ?></label>
				<?php YITH_YWGC_Backend::get_instance()->show_gift_card_amount_list( $thepostid ); ?>

				<span class="add-new-amount-section">
					<input type="text" id="gift_card-amount" name="gift_card-amount" class="short wc_input_price">
					<span class="ywgc-currency-symbol-enter-amount"><?php echo get_woocommerce_currency_symbol(); ?></span>
					<a href="#" class="add-new-amount">+</a>
					<span class="ywgc-tooltip-container ywgc-amount-already-added ywgc-hidden"><?php echo __( 'Amount value already used', 'yith-woocommerce-gift-cards' ); ?></span>
					<span class="ywgc-tooltip-container ywgc-invalid-amount ywgc-hidden"><?php echo __( 'Enter a valid amount', 'yith-woocommerce-gift-cards' ); ?></span>
				</span>
			</div>
		</div>

		<div class="yith-plugin-ui ywgc-product-edit-page-multi-currency-options">
			<?php do_action( 'yith_ywgc_multi_currency_settings', $thepostid ); ?>
		</div>

		<div class="yith-plugin-ui ywgc-product-edit-page-after-amount-options">
			<?php do_action( 'yith_ywgc_product_settings_after_amount_list', $thepostid ); ?>
		</div>

	</div>
