<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$plugins        = get_plugins();
$active_plugins = 0;
if ( ! empty( $plugins ) ) {
	foreach ( $plugins as $key => $plugin ) {
		if ( is_plugin_active_for_network( $key ) || is_plugin_active( $key ) ) {
			$active_plugins ++;
		}
	}
}
$result           = count( $plugins );
$inactive_plugins = ( $result - $active_plugins ); ?>

<h2><?php _e( 'Plugins', 'wps-cleaner' ) ?></h2>

<div class="wps-list-tools">
    <table>
        <thead class="wps_text_found">
        <th><?php printf( __( 'Total found: %s plugins', 'wps-cleaner' ), number_format_i18n( $result ) ); ?></th>
        <th>Action</th>
        </thead>
        <tbody>
        <tr>
            <th class="wps-desc-tool">
                <strong class="name"><?php _e( 'Plugins unuse', 'wps-cleaner' ); ?></strong>
                <p class="description">
                    <strong>Note
                        : </strong> <?php _e( 'This tool will clean up ALL plugins unuse from WordPress.', 'wps-cleaner' ); ?>
                </p>
            </th>
            <td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-btn-fixed-width wps-clean" data-action="delete_plugins_unuse" data-nonce="<?php echo wp_create_nonce( 'delete_plugins_unuse' ); ?>"><span class="text"><?php _e( 'Delete plugins unuse', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $inactive_plugins; ?>)</span></a>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<?php /*if ( is_plugin_active( 'redirection/redirection.php' ) ) :

	$logs = RE_Filter_Log::get( 'redirection_logs', 'RE_Log', array() );
	$logs_404 = RE_Filter_Log::get( 'redirection_404', 'RE_404', array() ); ?>

    <div class="wps-list-tools wps-settings-other-plugins">
        <table>
            <thead class="wps_text_found">
            <th><?php echo sprintf( __( 'The %s plugin is present', 'wps-cleaner' ), 'Redirection' ); ?></th>
            <th><?php _e( 'Action', 'wps-cleaner' ); ?></th>
            </thead>
            <tbody>
            <tr>
                <th class="wps-desc-tool">
                    <strong class="name"><?php _e( 'Logs', 'wps-cleaner' ); ?></strong>
                    <p class="description">
                        <strong>Note
                            : </strong> <?php printf( __( 'This tool will clean up ALL logs from %s plugin.', 'wps-cleaner' ), 'Redirection' ); ?>
                    </p>
                </th>
                <td class="run-tool">
                    <a href="#" class="button btn-wps clear_sessions wps-btn-fixed-width wps-clean" data-action="delete_logs_redirection" data-nonce="<?php echo wp_create_nonce( 'delete_logs_redirection' ); ?>"><span class="text"><?php _e( 'Delete logs', 'wps-cleaner' ); ?></span>
                        <span class="count">(<?php echo $logs['total']; ?>)</span></a>
                </td>
            </tr>
            <tr>
                <th class="wps-desc-tool">
                    <strong class="name"><?php _e( '404 log', 'wps-cleaner' ); ?></strong>
                    <p class="description">
                        <strong>Note
                            : </strong> <?php _e( 'This tool will clean up ALL 404 logs from redirection plugin.', 'wps-cleaner' ); ?>
                    </p>
                </th>
                <td class="run-tool">
                    <a href="#" class="button btn-wps clear_sessions wps-btn-fixed-width wps-clean" data-action="delete_404_logs_redirection" data-nonce="<?php echo wp_create_nonce( 'delete_404_logs_redirection' ); ?>"><span class="text"><?php _e( 'Delete 404 logs', 'wps-cleaner' ); ?></span>
                        <span class="count">(<?php echo $logs_404['total']; ?>)</span></a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php endif;*/ ?>

<?php if ( function_exists( 'wc_delete_product_transients' ) && function_exists( 'wc_delete_shop_order_transients' ) ) :

	global $wpdb;
	$total_sessions_woo        = $wpdb->query( "SELECT COUNT(*) {$wpdb->prefix}woocommerce_sessions" );
	$total_orhpaned_variations = $wpdb->query( "SELECT COUNT(*)
			FROM {$wpdb->posts} products
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = products.post_parent
			WHERE wp.ID IS NULL AND products.post_type = 'product_variation';" );
	?>

    <div class="wps-list-tools wps-settings-other-plugins">
        <table>
            <thead class="wps_text_found">
            <th><?php echo sprintf( __( 'The %s plugin is present', 'wps-cleaner' ), 'Woocommerce' ); ?></th>
            <th><?php _e( 'Action', 'wps-cleaner' ); ?></th>
            </thead>
            <tbody>
            <tr>
                <th class="wps-desc-tool">
                    <strong class="name"><?php _e( 'Woocommerce Transient', 'wps-cleaner' ); ?></strong>
                    <p class="description">
                        <strong>Note
                            : </strong> <?php _e( 'This tool will clean up ALL transients from woocommerce plugin.', 'wps-cleaner' ); ?>
                    </p>
                </th>
                <td class="run-tool">
                    <a href="#" class="button btn-wps clear_sessions wps-btn-fixed-width wps-clean" data-action="delete_woo_transients" data-nonce="<?php echo wp_create_nonce( 'delete_woo_transients' ); ?>"><span class="text"><?php _e( 'Delete transients', 'wps-cleaner' ); ?></span>
                    </a>
                </td>
            </tr>
            <tr>
                <th class="wps-desc-tool">
                    <strong class="name"><?php _e( 'Orphan variations', 'wps-cleaner' ); ?></strong>
                    <p class="description">
                        <strong>Note
                            : </strong> <?php _e( 'This tool will remove all variations without parent from the woocommerce plugin.', 'wps-cleaner' ); ?>
                    </p>
                </th>
                <td class="run-tool">
                    <a href="#" class="button btn-wps clear_sessions wps-btn-fixed-width wps-clean" data-action="delete_orphaned_variations" data-nonce="<?php echo wp_create_nonce( 'delete_orphaned_variations' ); ?>"><span class="text"><?php _e( 'Delete orphaned variations', 'wps-cleaner' ); ?></span>
                        <span class="count">(<?php echo $total_orhpaned_variations; ?>)</span></a>
                </td>
            </tr>
            <tr>
                <th class="wps-desc-tool">
                    <strong class="name"><?php _e( 'Delete client sessions', 'wps-cleaner' ); ?></strong>
                    <p class="description">
                        <strong>Note
                            : </strong> <?php _e( 'This tool will destroy all client session data in the database, including the current baskets of the woocommerce plugin.', 'wps-cleaner' ); ?>
                    </p>
                </th>
                <td class="run-tool">
                    <a href="#" class="button btn-wps clear_sessions wps-btn-fixed-width wps-clean" data-action="delete_sessions_woo" data-nonce="<?php echo wp_create_nonce( 'delete_sessions_woo' ); ?>"><span class="text"><?php _e( 'Delete all sessions', 'wps-cleaner' ); ?></span>
                        <span class="count">(<?php echo $total_sessions_woo; ?>)</span></a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if ( is_plugin_active( 'zero-spam/zero-spam.php' ) ) :

	if ( function_exists( 'zerospam_all_spam_ary' ) ) {
		$all_spam = zerospam_all_spam_ary();
	} ?>

    <div class="wps-list-tools wps-settings-other-plugins">
        <table>
            <thead class="wps_text_found">
            <th><?php echo sprintf( __( 'The %s plugin is present', 'wps-cleaner' ), 'Zerospam' ); ?></th>
            <th><?php _e( 'Action', 'wps-cleaner' ); ?></th>
            </thead>
            <tbody>
            <tr>
                <th class="wps-desc-tool">
                    <strong class="name"><?php _e( 'Logs', 'wps-cleaner' ); ?></strong>
                    <p class="description">
                        <strong>Note
                            : </strong> <?php printf( __( 'This tool will clean up ALL logs from %s plugin.', 'wps-cleaner' ), 'Zerospam' ); ?>
                    </p>
                </th>
                <td class="run-tool">
                    <a href="#" class="button btn-wps clear_sessions wps-btn-fixed-width wps-clean" data-action="delete_logs_zerospam" data-nonce="<?php echo wp_create_nonce( 'delete_logs_zerospam' ); ?>"><span class="text"><?php _e( 'Delete logs', 'wps-cleaner' ); ?></span>
                        <span class="count">(<?php echo count( $all_spam['by_spam_count'] ); ?>)</span></a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
$themes              = wp_get_themes();
$activeTheme         = wp_get_theme();
$result              = count( $themes );
$themesInactive      = $result - 1;
$themesInactiveChild = $result - 2;

$nb_themes_unuse = 0;
if ( $activeTheme->get( 'Template' ) != "" ) {
	if ( $themesInactiveChild > 0 ) {
		$nb_themes_unuse = $themesInactiveChild;
	}
} else {
	if ( $themesInactive > 0 ) {
		$nb_themes_unuse = $themesInactive;
	}
}
?>

<h2><?php _e( 'Themes', 'wps-cleaner' ) ?></h2>

<div class="wps-list-tools">
    <table>
        <thead class="wps_text_found">
        <th><?php printf( __( 'Total found: %s themes', 'wps-cleaner' ), number_format_i18n( $result ) ); ?></th>
        <th>Action</th>
        </thead>
        <tbody>
        <tr>
            <th class="wps-desc-tool">
                <strong class="name"><?php _e( 'Themes unuse', 'wps-cleaner' ); ?></strong>
                <p class="description">
                    <strong>Note
                        : </strong> <?php _e( 'This tool will clean up ALL themes unuse from WordPress.', 'wps-cleaner' ); ?>
                </p>
            </th>
            <td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean" data-action="delete_themes_unuse" data-nonce="<?php echo wp_create_nonce( 'delete_themes_unuse' ); ?>"><span class="text"><?php _e( 'Delete themes unuse', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $nb_themes_unuse; ?>)</span></a>
            </td>
        </tr>
        </tbody>
    </table>
</div>
