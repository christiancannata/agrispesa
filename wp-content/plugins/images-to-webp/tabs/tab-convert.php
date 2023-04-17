<?php defined('ABSPATH') || exit ?>

<?php if( isset( $this->settings['delete_originals'] ) && $this->settings['delete_originals'] === 1 ): ?>
	<div class="below-h2 error"><p><?php _e( 'This operation will PERMANENTLY DELETE original images, because you set this in general settings. It is a good idea to create some backup.', 'images-to-webp' ) ?></p></div>
<?php else: ?>
	<div class="below-h2 updated"><p><?php _e( 'This operation will NOT alter your original images.', 'images-to-webp' ) ?></p></div>
<?php endif ?>

<div id="transparency_status_message" class="below-h2 error" style="display:none"><p><span></span></p></div>

<div id="hide-on-convert">
	<?php wp_nonce_field('itw_convert') ?>
	<h3><?php _e( 'Select folders you want to scan for images and convert them to WebP:', 'images-to-webp' ) ?></h3>
	<div id="jstree"></div>
	<br>
	<button type="button" class="button button-primary convert-missing-images"><?php _e( 'Find and convert MISSING images', 'images-to-webp' ) ?></button>
	&emsp;
	<button type="button" class="button button-primary convert-all-images"><?php _e( 'Find and convert ALL images', 'images-to-webp' ) ?></button>
</div>

<div id="show-on-convert"></div>