<?php
/**
 * @var string $settings_field_id
 * @var string $settings_field_name
 * @var string $settings_field_title
 * @var array  $rules_table_settings
 * @package Flexible Shipping
 */

?>
<tr valign="top" class="flexible_shipping_method_rules">
	<th class="forminp" colspan="2">
		<label class="flexible-shipping-method-rules-settings-label" for="<?php echo esc_attr( $settings_field_name ); ?>"><?php echo wp_kses_post( $settings_field_title ); ?></label>
	</th>
</tr>
<tr valign="top" class="flexible-shipping-method-rules-settings">
	<td colspan="2" style="padding:0;"><div class="flexible-shipping-method-rules-settings">
		<?php do_action( 'flexible-shipping/method-rules-settings/table/before' ); ?>

		<p><a href="#0" class="button-hints js--button-hints"
			  data-second_label="<?php esc_attr_e( 'Close the FS hints', 'flexible-shipping' ); ?>"><?php esc_attr_e( 'Check the FS hints', 'flexible-shipping' ); ?></a>
		</p>

		<div class="flexible-shipping-rules-instruction js--hints">
			<?php if ( ! wpdesk_is_plugin_active( 'flexible-shipping-pro/flexible-shipping-pro.php' ) ) : ?>
				<p>
					<?php
					$fs_pro_link = get_locale() === 'pl_PL' ? 'https://octol.io/fs-hints-up-pl' : 'https://octol.io/fs-hints-up';

					echo wp_kses_post(
						sprintf(
						// Translators: open tag, close tag.
							__( 'Need more? Check %1$sFlexible Shipping PRO%2$s to unleash its full potential and add advanced rules based on shipping classes, products, quantity, include additional handling fees, insurance and much more.', 'flexible-shipping' ),
							'<a href="' . esc_url( $fs_pro_link ) . '" target="_blank">',
							'</a>'
						)
					);
					?>
				</p>
			<?php endif; ?>

			<p>
				<?php
				echo wp_kses_post(
					sprintf(
					// Translators: open tag, close tag.
						__( 'Want to find out how the table rate works? Hop on board and %1$slet us guide you through the whole setup &rarr;%2$s', 'flexible-shipping' ),
						'<a href="#0" class="js--open-onboarding">',
						'</a>'
					)
				);
				?>
			</p>

			<p>
				<?php echo wp_kses_post( __( 'Please mind that the ranges you define must not overlap each other and make sure there are no gaps between them.', 'flexible-shipping' ) ); ?>
				<br/>
				<?php
				echo wp_kses_post(
					sprintf(
					// Translators: open tag, close tag.
						__( '%1$sExample%2$s: If your rules are based on %1$sprice%2$s and the first range covers $0-$100, the next one should start from %1$s$100.01%2$s, not from %1$s$101%2$s, etc.', 'flexible-shipping' ),
						'<strong>',
						'</strong>'
					)
				);
				?>
			</p>
		</div>

		<script type="text/javascript">
			var <?php echo esc_attr( $settings_field_id ); ?> = <?php echo json_encode( $rules_table_settings ); ?>;

			document.addEventListener("DOMContentLoaded", function (event) {
				document.querySelector('#mainform button[name="save"]').addEventListener("click", function (event) {
					if (null === document.querySelector('#<?php echo esc_attr( $settings_field_id ); ?>_control_field')) {
						event.preventDefault();
						alert('<?php echo esc_attr( __( 'Missing rules table - settings cannot be saved!', 'flexible-shipping' ) ); ?>');
					}
				});
			});
		</script>

		<div class="flexible-shipping-rules-settings" id="<?php echo esc_attr( $settings_field_id ); ?>"
			 data-settings-field-name="<?php echo esc_attr( $settings_field_name ); ?>">
			<input name="<?php echo esc_attr( $settings_field_name ); ?>" type="hidden" value="<?php echo esc_attr( json_encode( $rules_table_settings['rules_settings'] ) ); ?>">
			<div class="notice notice-error inline">
				<?php echo wp_kses_post( wpautop( __( 'This is where the rules table should be displayed. If it\'s not, it is usually caused by the conflict with the other plugins you are currently using, JavaScript error or the caching issue. Clear your browser\'s cache or deactivate the plugins which may be interfering.', 'flexible-shipping' ) ) ); ?>
			</div>
		</div>

		<?php do_action( 'flexible-shipping/method-rules-settings/table/after' ); ?>
		</div><div class="flexible-shipping-method-rules-settings-modal" style="display:none;">
			<div class="notice notice-error inline">
				<?php echo wp_kses_post( wpautop(
					sprintf(
						// Translators: open tag, close tag.
						__( 'To use Flexible Shipping Rules Table you need to switch to non-modal mode. %1$sClick here%2$s to edit this shipping method with Rules Table.', 'flexible-shipping' ),
						'<a class="fs-non-modal-method-edit" href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping' ) ) . '">',
						'</a>'
					)
				) ); ?>
			</div>
		</div></td>
</tr>
