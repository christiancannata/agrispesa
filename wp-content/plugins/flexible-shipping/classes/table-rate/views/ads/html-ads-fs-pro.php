<?php
/**
 * @package Flexible Shipping
 *
 * @var string $shipping_method_id .
 */

?>

<div class="fs-flexible-shipping-sidebar fs-flexible-shipping-sidebar-pro <?php echo esc_attr( isset( $shipping_method_id ) ? $shipping_method_id : '' ); ?>" style="height: auto;">
	<div class="wpdesk-metabox">
		<div class="wpdesk-stuffbox">
			<h3 class="title"><?php esc_html_e( 'Get Flexible Shipping PRO!', 'flexible-shipping' ); ?></h3>
			<?php
			$fs_link = get_locale() === 'pl_PL' ? 'https://octol.io/fs-box-upgrade-pl' : 'https://octol.io/fs-box-upgrade';
			?>

			<div class="inside">
				<div class="main">
					<ul>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Shipping Classes support', 'flexible-shipping' ); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Product count based costs', 'flexible-shipping' ); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Stopping, Cancelling a rule', 'flexible-shipping' ); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Additional calculation methods', 'flexible-shipping' ); ?>
						</li>
					</ul>

					<a class="button button-primary" href="<?php echo esc_url( $fs_link ); ?>"
					   target="_blank"><?php esc_html_e( 'Upgrade now to PRO version &rarr;', 'flexible-shipping' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
