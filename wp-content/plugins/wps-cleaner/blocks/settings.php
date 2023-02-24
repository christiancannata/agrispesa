<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( false === ( $total_posts = get_transient( 'wps_cleaner_total_posts' ) ) ) {
	$total_posts = \WPS\WPS_Cleaner\Helpers::total_count( 'posts' );
	set_transient( 'wps_cleaner_total_posts', $total_posts, DAY_IN_SECONDS );
}

if ( false === ( $total_postmeta = get_transient( 'wps_cleaner_total_postmeta' ) ) ) {
	$total_postmeta = \WPS\WPS_Cleaner\Helpers::total_count( 'postmeta' );
	set_transient( 'wps_cleaner_total_postmeta', $total_postmeta, DAY_IN_SECONDS );
}

$revisions           = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'revisions' );
$auto_drafts         = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'auto_drafts' );
$deleted_posts       = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'deleted_posts' );
$orphan_postmeta     = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'orphan_postmeta' );
$duplicated_postmeta = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'duplicated_postmeta' );
$oembed_postmeta     = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'oembed_postmeta' ); ?>

<h2><?php _e( 'Posts', 'wps-cleaner' ) ?></h2>

<p><?php _e( 'Posts: are articles, pages, and all types of content.', 'wps-cleaner' ); ?><br/>
	<?php _e( 'Metadata: Additional data related to a post.', 'wps-cleaner' ); ?><br/>
	<?php _e( 'oEmbed: Function to integrate HTML from another site.', 'wps-cleaner' ); ?></p>

<p class="wps-italic"><?php _e( 'This will clean up the posts in the database.', 'wps-cleaner' ); ?></p>

<div class="wps-list-tools">
    <table>
        <thead class="wps_text_found">
        <th><?php printf( __( 'Total found: %s Posts and %s Post Meta.', 'wps-cleaner' ), number_format_i18n( $total_posts ), number_format_i18n( $total_postmeta ) ); ?></th>
        <th>Action</th>
        </thead>
        <tbody>
        <tr>
            <th class="wps-desc-tool">
                <strong class="name"><?php _e( 'Revisions', 'wps-cleaner' ); ?></strong>
                <p class="description">
                    <strong>Note
                        : </strong> <?php _e( 'This tool will clean up ALL revisions from WordPress.', 'wps-cleaner' ); ?>
                </p>
            </th>
            <td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_revisions"
                   data-nonce="<?php echo wp_create_nonce( 'clear_revisions' ); ?>"><span
                            class="text"><?php _e( 'Clear revisions', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $revisions; ?>)</span></a>
            </td>
        </tr>
        <tr>
            <th class="wps-desc-tool">
                <strong class="name"><?php _e( 'Auto draft', 'wps-cleaner' ); ?></strong>
                <p class="description">
                    <strong>Note
                        : </strong> <?php _e( 'This tool will clean up ALL auto drafts from WordPress.', 'wps-cleaner' ); ?>
                </p>
            </th>
            <td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_auto_drafts"
                   data-nonce="<?php echo wp_create_nonce( 'clear_auto_drafts' ); ?>"><span
                            class="text"><?php _e( 'Clear auto drafts', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $auto_drafts; ?>)</span></a>
            </td>
        </tr>
        <tr>
            <th class="wps-desc-tool">
                <strong class="name"><?php _e( 'Deleted posts', 'wps-cleaner' ); ?></strong>
                <p class="description">
                    <strong>Note
                        : </strong> <?php _e( 'This tool will clean up ALL deleted posts from WordPress.', 'wps-cleaner' ); ?>
                </p>
            </th>
            <td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_deleted_posts"
                   data-nonce="<?php echo wp_create_nonce( 'clear_deleted_posts' ); ?>"><span
                            class="text"><?php _e( 'Clear deleted posts', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $deleted_posts; ?>)</span></a>
            </td>
        </tr>
        <tr>
            <th class="wps-desc-tool">
                <strong class="name"><?php _e( 'Orphaned Post Meta', 'wps-cleaner' ); ?></strong>
                <p class="description">
                    <strong>Note
                        : </strong> <?php _e( 'This tool will clean up ALL Orphaned Post Meta from WordPress.', 'wps-cleaner' ); ?>
                </p>
            </th>
            <td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_orphaned_post_meta"
                   data-nonce="<?php echo wp_create_nonce( 'clear_orphaned_post_meta' ); ?>"><span
                            class="text"><?php _e( 'Clear Orphaned Post Meta', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $orphan_postmeta; ?>)</span></a>
            </td>
        </tr>
        <tr>
            <th class="wps-desc-tool">
                <strong class="name"><?php _e( 'Duplicated Post Meta', 'wps-cleaner' ); ?></strong>
                <p class="description">
                    <strong>Note
                        : </strong> <?php _e( 'This tool will clean up ALL Duplicated Post Meta from WordPress.', 'wps-cleaner' ); ?>
                </p>
            </th>
            <td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_duplicated_postmeta"
                   data-nonce="<?php echo wp_create_nonce( 'clear_duplicated_postmeta' ); ?>"><span
                            class="text"><?php _e( 'Clear Duplicated Post Meta', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $duplicated_postmeta; ?>)</span></a>
            </td>
        </tr>
        <tr>
            <th class="wps-desc-tool">
                <strong class="name"><?php _e( 'oEmbed Caches In Post Meta', 'wps-cleaner' ); ?></strong>
                <p class="description">
                    <strong>Note
                        : </strong> <?php _e( 'This tool will clean up ALL oEmbed Caches In Post Meta from WordPress.', 'wps-cleaner' ); ?>
                </p>
            </th>
            <td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_oembed_caches"
                   data-nonce="<?php echo wp_create_nonce( 'clear_oembed_caches' ); ?>"><span
                            class="text"><?php _e( 'Clear oEmbed Caches In Post Meta', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $oembed_postmeta; ?>)</span></a>
            </td>
        </tr>
        </tbody>
    </table>
</div>