<?php
/**
 * Admin View: Settings
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\GiftCards\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="yith-plugin-fw yit-admin-panel-container" id="yith-ywgc-emails-wrapper">
	<div id="yith-ywgc-table-emails">
		<div class="heading-table yith-ywgc-row">
			<span class="yith-ywgc-column email"><?php esc_html_e( 'Email', 'yith-woocommerce-gift-cards' ); ?></span>
			<span class="yith-ywgc-column recipient"><?php esc_html_e( 'Recipient', 'yith-woocommerce-gift-cards' ); ?></span>
			<span class="yith-ywgc-column action"></span>
			<span class="yith-ywgc-column status"><?php esc_html_x( 'Active', '[ADMIN] Column name table emails', 'yith-woocommerce-gift-cards' ); ?></span>
		</div>
		<div class="content-table">
			<?php foreach ( $emails_table as $email_key => $email ) : ?>
				<?php $url = YITH_YWGC_Admin()->build_single_email_settings_url( $email_key ); ?>
				<div class="yith-ywgc-row">
					<span class="yith-ywgc-column email">
						<?php echo esc_html( $email['title'] ); ?>
						<?php echo wc_help_tip( $email['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php if ( 'ywgc-email-send-gift-card' !== $email_key ) : ?>
                            <span class="premium-badge">
							<?php
							esc_html_e(
								'Premium',
								'yith-woocommerce-waiting-list'
							);
							?>
									</span>
						<?php endif; ?>
					</span>
					<span class="yith-ywgc-column recipient">
						<?php echo esc_html( $email['recipient'] ); ?>
					</span>
					<span class="yith-ywgc-column action">
						<?php
						yith_plugin_fw_get_component(
							array(
								'title'  => __( 'Edit', 'yith-woocommerce-gift-cards' ),
								'type'   => 'action-button',
								'action' => 'edit',
								'icon'   => 'edit',
								'url'    => esc_url( $url ),
								'data'   => array(
									'target' => $email_key,
								),
								'class'  => 'toggle-settings',
							)
						);
						?>
					</span>
					<span class="yith-ywgc-column status">
						<?php
							$email_status = array(
								'id'      => 'yith-ywgc-email-status',
								'type'    => 'onoff',
								'default' => 'yes',
								'value'   => $email['enable'],
								'data'    => array(
									'email_key' => $email_key,
								),
							);

							yith_plugin_fw_get_field( $email_status, true );
							?>
					</span>
					<div class="email-settings" id="<?php echo esc_attr( $email_key ); ?>">
						<?php do_action( 'yith_ywgc_print_email_settings', $email_key ); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
