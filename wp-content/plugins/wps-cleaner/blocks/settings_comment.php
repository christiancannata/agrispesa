<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$total_comments             = \WPS\WPS_Cleaner\Helpers::total_count( 'comments' );
$total_commentmeta          = \WPS\WPS_Cleaner\Helpers::total_count( 'commentmeta' );

$unapproved_comments        = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'unapproved_comments' );
$spam_comments              = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'spam_comments' );
$deleted_comments           = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'deleted_comments' );
$orphan_commentmeta         = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'orphan_commentmeta' );
$duplicated_commentmeta     = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'duplicated_commentmeta' ); ?>

<h2><?php _e( 'Comments', 'wps-cleaner' ) ?></h2>

<p><?php _e( 'Metadata: Additional data related to a comment.', 'wps-cleaner' ); ?></p>

<p class="wps-italic"><?php _e( 'This will clean up the comments from the database.', 'wps-cleaner' ); ?></p>

<div class="wps-list-tools">
	<table>
        <thead class="wps_text_found">
        <th><?php printf( __('Total found: %s Comments and %s Comment Meta.', 'wps-cleaner' ), number_format_i18n( $total_comments ), number_format_i18n( $total_commentmeta ) ); ?></th>
        <th>Action</th>
        </thead>
		<tbody>
		<tr>
			<th class="wps-desc-tool">
				<strong class="name"><?php _e( 'Unapproved Comments', 'wps-cleaner' ); ?></strong>
				<p class="description">
					<strong>Note : </strong> <?php _e( 'This tool will clean up ALL Unapproved Comments from WordPress.', 'wps-cleaner' ); ?>
				</p>
			</th>
			<td class="run-tool">
				<a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_unapproved_comments" data-nonce="<?php echo wp_create_nonce( 'clear_unapproved_comments' ); ?>"><span class="text"><?php _e( 'Clear Unapproved Comments', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $unapproved_comments; ?>)</span></a>
			</td>
		</tr>
		<tr>
			<th class="wps-desc-tool">
				<strong class="name"><?php _e( 'Spammed Comments', 'wps-cleaner' ); ?></strong>
				<p class="description">
					<strong>Note : </strong> <?php _e( 'This tool will clean up ALL Spammed Comments from WordPress.', 'wps-cleaner' ); ?>
				</p>
			</th>
			<td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean"  data-action="clear_spammed_comments" data-nonce="<?php echo wp_create_nonce( 'clear_spammed_comments' ); ?>"><span class="text"><?php _e( 'Clear Spammed Comments', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $spam_comments; ?>)</span></a>
			</td>
		</tr>
		<tr>
			<th class="wps-desc-tool">
				<strong class="name"><?php _e( 'Deleted Comments', 'wps-cleaner' ); ?></strong>
				<p class="description">
					<strong>Note : </strong> <?php _e( 'This tool will clean up ALL Deleted Comments from WordPress.', 'wps-cleaner' ); ?>
				</p>
			</th>
			<td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_deleted_comments" data-nonce="<?php echo wp_create_nonce( 'clear_deleted_comments' ); ?>"><span class="text"><?php _e( 'Clear Deleted Comments', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $deleted_comments; ?>)</span></a>
			</td>
		</tr>
		<tr>
			<th class="wps-desc-tool">
				<strong class="name"><?php _e( 'Orphaned Comment Meta', 'wps-cleaner' ); ?></strong>
				<p class="description">
					<strong>Note : </strong> <?php _e( 'This tool will clean up ALL Orphaned Comment Meta from WordPress.', 'wps-cleaner' ); ?>
				</p>
			</th>
			<td class="run-tool">
				<a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_orphan_commentmeta" data-nonce="<?php echo wp_create_nonce( 'clear_orphan_commentmeta' ); ?>"><span class="text"><?php _e( 'Clear Orphaned Comment Meta', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $orphan_commentmeta; ?>)</span></a>
			</td>
		</tr>
		<tr>
			<th class="wps-desc-tool">
				<strong class="name"><?php _e( 'Duplicated Comment Meta', 'wps-cleaner' ); ?></strong>
				<p class="description">
					<strong>Note : </strong> <?php _e( 'This tool will clean up ALL Duplicated Comment Meta from WordPress.', 'wps-cleaner' ); ?>
				</p>
			</th>
			<td class="run-tool">
				<a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_duplicated_commentmeta" data-nonce="<?php echo wp_create_nonce( 'clear_duplicated_commentmeta' ); ?>"><span class="text"><?php _e( 'Clear Duplicated Comment Meta', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $duplicated_commentmeta; ?>)</span></a>
			</td>
		</tr>
		</tbody>
	</table>
</div>