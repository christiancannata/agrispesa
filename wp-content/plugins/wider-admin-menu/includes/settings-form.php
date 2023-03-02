<?php
/**
 * Wider Admin Menu > Settings > Form
 */
?>
<style>


	/* UI Slider */

	.wpmwam-settings-container {
		min-width:200px;
		padding-left:15px !important;
	}

	.modula-settings-container .slider-container {
		position: relative;
		height: 2px;
		padding: 10px 0;
		width: 25em;
		box-sizing: border-box;
		margin-bottom: 10px;
		display: flex;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.modula-settings-container .slider-container input[type="text"] {
		border: none;
		text-align: center;
		padding: 2px;
		margin: 0 20px 0 0;
		font-size: 12px;
		color: #333;
		border-radius: 10px;
		background-color: #fff;
		-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.16);
		-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.16);
		box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.16);
		width: 40px;
		height: 22px;
		cursor: default;
	}

	.wpmwam-settings-container .slider-container .ui-slider {
		position: relative;
		text-align: left;
		height: 2px;
		border-radius: 3px;
		border: none;
		display: block;
		width: 100%;
		background: #d6d6d6;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}

	.wpmwam-settings-container .slider-container .ui-slider .ui-slider-handle {
		position: absolute;
		z-index: 2;
		top: -10px;
		cursor: default;
		-ms-touch-action: none;
		touch-action: none;
		width: 18px;
		height: 18px;
		-webkit-border-radius: 9px;
		-moz-border-radius: 9px;
		border-radius: 9px;
		background-color: #fff;
		-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
		-moz-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
		border: solid 1px #d7d7d7;
		transform: translateX(-50%);
		outline:none;
	}

	.wpmwam-settings-container .slider-container .ui-slider .ui-slider-range {
		position: absolute;
		z-index: 1;
		font-size: 0.7em;
		display: block;
		border: 0;
		background-position: 0 0;
		background: #57a7c9;
		top: 0;
		bottom: 0;
	}
</style>
<form method="post" action="options.php">

	<?php
	// version-based CSS classes
	if ( version_compare( $wp_version, '3.8', '>=' ) ) {
		$version_class = 'ver38';
		$reset_class   = 'dashicons dashicons-undo';
	} else {
		$version_class = 'pre-ver38';
		$reset_class   = 'undo';
	}

	$default_width = 160;
	settings_fields( 'wpmwam_settings_group' );
	$wpmwam_options = get_option( 'wpmwam_options' );
	$wpmwam_width   = $wpmwam_options['wpmwam_width'];
	if ( ! $wpmwam_width ) {
		$wpmwam_width = $default_width;
	}
	?>

	<input type="hidden" name="wp_version" value="<?php echo $wp_version; ?>">

	<table class="form-table wpmwam" style="width: auto;">
		<tr>
			<td><?php esc_html_e( 'New', 'wider-admin-menu' ); ?></td>
			<td class="input">
				<input id="wpmwam_width" type="text" name="wpmwam_options[wpmwam_width]"
					   value="<?php esc_attr_e( $wpmwam_width ); ?>"
					   size="8" maxlength="8" tabindex="1" data-min="160" data-max="300" data-step="10">px
			</td>
			<td class="slider wpmwam-settings-container">
				<div class="slider-container modula-ui-slider-container">
					<div id="wpmwam_slider" class="ss-slider wpmwam-ui-slider"></div>
				</div>
			</td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Current', 'wider-admin-menu' ); ?></td>
			<td><span id="wpmwam_current"
					  class="box <?php echo $version_class; ?>"><?php esc_attr_e( $wpmwam_width ); ?></span>px
			</td>
			<td class="lefted">
				<a id="reset-current" class="<?php echo $reset_class; ?>" href="#"
				   title="<?php esc_html_e( 'revert to the current width', 'wider-admin-menu' ); ?>"></a>
			</td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Default', 'wider-admin-menu' ); ?></td>
			<td><span id="wpmwam_default" class="box <?php echo $version_class; ?>"><?php echo $default_width; ?></span>px
			</td>
			<td class="lefted">
				<a id="reset-default" class="<?php echo $reset_class; ?>" href="#"
				   title="<?php esc_html_e( 'restore default width', 'wider-admin-menu' ); ?>"></a>
			</td>
		</tr>
	</table>

	<table class="form-table" style="width: auto;">
		<tr>
			<th scope="row">
				<label class="lnt" for="wpmwam_lnt"><?php esc_html_e( 'Leave No Trace', 'wider-admin-menu' ); ?></label>
			</th>
			<td>
				<div id="leave-no-trace">
					<select id="wpmwam_lnt" name="wpmwam_options[wpmwam_lnt]">
						<option value="1" <?php selected( $wpmwam_options['wpmwam_lnt'], 1 ); ?>>
							<?php esc_html_e( 'Yes - Deleting this plugin will also delete these settings.', 'wider-admin-menu' ); ?>
						</option>
						<option value="0" <?php selected( $wpmwam_options['wpmwam_lnt'], 0 ); ?>>
							<?php esc_html_e( 'No - These settings will remain after deleting this plugin.', 'wider-admin-menu' ); ?>
						</option>
					</select>

					<p class="help">
						<?php esc_html_e( 'Deactivating this plugin will not delete anything.', 'wider-admin-menu' ); ?>
					</p>
				</div>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>

</form>
