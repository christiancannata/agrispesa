<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>
<div class="wrap wps-cleaner-page-settings">

    <?php include( WPS_CLEANER_DIR . 'blocks/alert.php' ); ?>

	<?php include( WPS_CLEANER_DIR . 'blocks/title.php' ); ?>

	<div class="wps-content-cleaner">
		<div class="wps-content-tab">
            <?php
            if ( ! isset( $_GET[ 'tab' ] ) ) {
	            include( WPS_CLEANER_DIR . 'blocks/sweep-all.php' );
            } elseif (  isset( $_GET[ 'tab' ] ) && 'comment' === $_GET[ 'tab' ] ) {
	            include( WPS_CLEANER_DIR . 'blocks/sweep-all.php' );
            } elseif ( isset( $_GET[ 'tab' ] ) && 'user' === $_GET[ 'tab' ] ) {
	            include( WPS_CLEANER_DIR . 'blocks/sweep-all.php' );
            } elseif (  isset( $_GET[ 'tab' ] ) && 'option' === $_GET[ 'tab' ] ) {
	            include( WPS_CLEANER_DIR . 'blocks/sweep-all.php' );
            } elseif ( isset( $_GET[ 'tab' ] ) && 'term' === $_GET[ 'tab' ] ) {
	            include( WPS_CLEANER_DIR . 'blocks/sweep-all.php' );
            } ?>
			<?php include( WPS_CLEANER_DIR . 'blocks/menu.php' ); ?>
			<div class="wps_content-box">
				<div class="wps-tab">
					<?php
					if ( ! isset( $_GET[ 'tab' ] ) ) {
						include( WPS_CLEANER_DIR . 'blocks/settings.php' );
					} elseif (  isset( $_GET[ 'tab' ] ) && 'comment' === $_GET[ 'tab' ] ) {
						include( WPS_CLEANER_DIR . 'blocks/settings_comment.php' );
					} elseif ( isset( $_GET[ 'tab' ] ) && 'user' === $_GET[ 'tab' ] ) {
						include( WPS_CLEANER_DIR . 'blocks/settings_user.php' );
					} elseif (  isset( $_GET[ 'tab' ] ) && 'option' === $_GET[ 'tab' ] ) {
						include( WPS_CLEANER_DIR . 'blocks/settings_option.php' );
					} elseif ( isset( $_GET[ 'tab' ] ) && 'term' === $_GET[ 'tab' ] ) {
						include( WPS_CLEANER_DIR . 'blocks/settings_term.php' );
					} elseif ( isset( $_GET[ 'tab' ] ) && 'plugin_theme' === $_GET[ 'tab' ] ) {
						include( WPS_CLEANER_DIR . 'blocks/settings_plugin.php' );
					} elseif ( isset( $_GET[ 'tab' ] ) && 'database' === $_GET[ 'tab' ] ) {
						include( WPS_CLEANER_DIR . 'blocks/settings_database.php' );
					} elseif ( isset( $_GET[ 'tab' ] ) && 'media' === $_GET[ 'tab' ] ) {
						include( WPS_CLEANER_DIR . 'blocks/settings_media.php' );
					} elseif ( isset( $_GET[ 'tab' ] ) && 'files' === $_GET[ 'tab' ] ) {
						include( WPS_CLEANER_DIR . 'blocks/settings_files.php' );
					} ?>
				</div>
				<div class="wps-autopromo" id="plugin-filter">
					<?php include( WPS_CLEANER_DIR . '/blocks/pub-wpserveur.php' ); ?>
					<?php include( WPS_CLEANER_DIR . '/blocks/pub.php' ); ?>
				</div>
			</div>
		</div>
	</div>
</div>