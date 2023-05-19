<?php
/**
 * Modules tab content.
 *
 * @var array $available_modules     The available modules data.
 * @var array $non_available_modules The non-available modules data.
 *
 * @package YITH\Booking\Views
 */

defined( 'ABSPATH' ) || exit();

$premium_url = yith_plugin_fw_add_utm_data( YITH_YWGC_PREMIUM_LANDING_URL, YITH_YWGC_SLUG, 'button-upgrade', 'wp-extended-dashboard' );
?>
<div class="yith-ywgc-modules">
	<div class="modules">
		<?php foreach ( $available_modules as $module_data ) : ?>
			<?php yith_ywgc_get_view( 'settings-tabs/html-module.php', compact( 'module_data' ) ); ?>
		<?php endforeach; ?>
	</div>
</div>
