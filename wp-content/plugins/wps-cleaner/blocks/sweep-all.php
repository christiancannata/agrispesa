<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>
<div class="wps_medias_download home">
	<p><?php _e( 'One-click cleanup: This will clean everything except plugins / themes / media / files.', 'wps-cleaner' ); ?> <br />
        <?php _e('For these, please go to each corresponding tab.', 'wps-cleaner' ); ?></p>
	<button id="sweep_all" data-nonce="<?php echo wp_create_nonce('sweep_all'); ?>"><?php _e( 'Sweep all', 'wps-cleaner' ); ?></button>
</div>