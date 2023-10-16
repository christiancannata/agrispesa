<?php
/**
 * @package Flexible Shipping
 *
 * @var string $shipping_method_id .
 */

$fsie_link = get_locale() === 'pl_PL' ? 'https://octol.io/fs-info-addons-pl' : 'https://octol.io/addons-box-fs';
?>

<div class="fs-flexible-shipping-sidebar fs-flexible-shipping-sidebar-fsie <?php echo esc_attr( isset( $shipping_method_id ) ? $shipping_method_id : '' ); ?>"
	 style="height: auto;">
	<div class="wpdesk-metabox">
		<div class="wpdesk-stuffbox">
			<h3 class="title"><?php esc_html_e( 'Extend the Flexible Shipping capabilities with functional add-ons', 'flexible-shipping' ); ?></h3>

			<div class="inside">
				<div class="main">
					<ul>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Calculate the shipping cost based on your custom locations or the WooCommerce defaults', 'flexible-shipping' ); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Define shipping cost for each Vendor / Product Author in your marketplace', 'flexible-shipping' ); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Move, replace, update or backup multiple shipping methods with Import / Export feature', 'flexible-shipping' ); ?>
						</li>
					</ul>

					<a class="button button-primary" href="<?php echo esc_url( $fsie_link ); ?>"
					   target="_blank"><?php esc_html_e( 'Buy Flexible Shipping Add-ons &rarr;', 'flexible-shipping' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
