<?php
/**
 * Wider Admin Menu > Settings > Alternate method
 */
?>
<div class="wpmwam">

<p><?php printf( esc_html__( 'Find the %s stylesheet.', 'wider-admin-menu' ), '<code>/wp-content/plugins/wider-admin-menu/css/wider-admin-menu.css</code>' ); ?></p>
<p><?php esc_html_e( 'Then either copy its contents to your theme\'s stylesheet', 'wider-admin-menu' ); ?></p>
<p><?php esc_html_e( 'OR', 'wider-admin-menu' ); ?></p>
<p><?php esc_html_e( "copy the file to your theme folder and add this to your theme's <code>functions.php</code> to load it:", 'wider-admin-menu' ); ?></p>

<pre
<?php
if ( version_compare( $wp_version, '3.8', '<' ) ) {
	echo ' class="lt38"';}
?>
>
/*
 * Wider Admin Menu stylesheet
 */
function wpmwam_style() {
  wp_enqueue_style( 'wpmwam-style', get_stylesheet_directory_uri() . '/wider-admin-menu.css' );
}
add_action( 'admin_enqueue_scripts', 'wpmwam_style' );
</pre>

<p><strong><?php esc_html_e( 'That stylesheet covers WordPress version 5.', 'wider-admin-menu' ); ?></strong></p>
<p><?php printf( esc_html__( 'For WordPress %1$s, substitute %2$s.', 'wider-admin-menu' ), '4', '<code>wider-admin-menu-40.css</code>' ); ?></p>
<p><?php printf( esc_html__( 'For WordPress %1$s to %2$s, substitute %3$s.', 'wider-admin-menu' ), '3.8', '3.9.2', '<code>wider-admin-menu-38.css</code>' ); ?></p>
<p><?php printf( esc_html__( 'For WordPress %1$s to %2$s, substitute %3$s.', 'wider-admin-menu' ), '3.5', '3.7.1', '<code>wider-admin-menu-35.css</code>' ); ?></p>
<p><?php printf( esc_html__( 'For WordPress %1$s to %2$s, substitute %3$s.', 'wider-admin-menu' ), '3.3', '3.4.2', '<code>wider-admin-menu-33.css</code>' ); ?></p>
<p><?php esc_html_e( 'Then you can deactivate this plugin.', 'wider-admin-menu' ); ?></p>

</div>
