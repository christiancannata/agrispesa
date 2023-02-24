<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>
<div class="wps-wrap-menu">
	<nav class="wps-menu">
		<div class="wps-nav-menu <?php echo ( $_GET[ 'page' ] === 'wps-cleaner' && ! isset( $_GET[ 'tab' ] ) ) ? 'current' : ''; ?>">
			<a href="<?php echo esc_url( admin_url( 'tools.php?page=wps-cleaner' ) ); ?>">
				<?php _e( 'Posts', 'wps-cleaner' ); ?>
			</a>
		</div>
        <div class="wps-nav-menu <?php echo ( $_GET[ 'page' ] === 'wps-cleaner' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'comment' ) ? 'current' : ''; ?>">
            <a href="<?php echo esc_url( admin_url( 'tools.php?page=wps-cleaner&tab=comment' ) ); ?>">
				<?php _e( 'Comments', 'wps-cleaner' ); ?>
            </a>
        </div>
        <div class="wps-nav-menu <?php echo ( $_GET[ 'page' ] === 'wps-cleaner' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'term' ) ? 'current' : ''; ?>">
            <a href="<?php echo esc_url( admin_url( 'tools.php?page=wps-cleaner&tab=term' ) ); ?>">
				<?php _e( 'Terms', 'wps-cleaner' ); ?>
            </a>
        </div>
        <div class="wps-nav-menu <?php echo ( $_GET[ 'page' ] === 'wps-cleaner' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'user' ) ? 'current' : ''; ?>">
            <a href="<?php echo esc_url( admin_url( 'tools.php?page=wps-cleaner&tab=user' ) ); ?>">
				<?php _e( 'Users', 'wps-cleaner' ); ?>
            </a>
        </div>
        <div class="wps-nav-menu <?php echo ( $_GET[ 'page' ] === 'wps-cleaner' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'option' ) ? 'current' : ''; ?>">
            <a href="<?php echo esc_url( admin_url( 'tools.php?page=wps-cleaner&tab=option' ) ); ?>">
				<?php _e( 'Options', 'wps-cleaner' ); ?>
            </a>
        </div>
		<div class="wps-nav-menu <?php echo ( $_GET[ 'page' ] === 'wps-cleaner' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'plugin_theme' ) ? 'current' : ''; ?>">
			<a href="<?php echo esc_url( admin_url( 'tools.php?page=wps-cleaner&tab=plugin_theme' ) ); ?>">
				<?php echo __( 'Plugins', 'wps-cleaner' ) . ' / ' .  __( 'Themes', 'wps-cleaner' ); ?>
			</a>
		</div>
        <!--<div class="wps-nav-menu <?php echo ( $_GET[ 'page' ] === 'wps-cleaner' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'database' ) ? 'current' : ''; ?>">
            <a href="<?php echo esc_url( admin_url( 'tools.php?page=wps-cleaner&tab=database' ) ); ?>">
				<?php _e( 'Database', 'wps-cleaner' ); ?>
            </a>
        </div>-->
        <div class="wps-nav-menu <?php echo ( $_GET[ 'page' ] === 'wps-cleaner' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'media' ) ? 'current' : ''; ?>">
            <a href="<?php echo esc_url( admin_url( 'tools.php?page=wps-cleaner&tab=media' ) ); ?>">
				<?php _e( 'Medias', 'wps-cleaner' ); ?>
            </a>
        </div>
        <div class="wps-nav-menu <?php echo ( $_GET[ 'page' ] === 'wps-cleaner' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'files' ) ? 'current' : ''; ?>">
            <a href="<?php echo esc_url( admin_url( 'tools.php?page=wps-cleaner&tab=files' ) ); ?>">
				<?php _e( 'Files', 'wps-cleaner' ); ?>
            </a>
        </div>
		<div class="clearfix"></div>
	</nav>
</div>