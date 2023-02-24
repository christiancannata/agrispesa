<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$total_users                = \WPS\WPS_Cleaner\Helpers::total_count( 'users' );
$total_usermeta             = \WPS\WPS_Cleaner\Helpers::total_count( 'usermeta' );

$orphan_usermeta            = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'orphan_usermeta' );
$duplicated_usermeta        = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'duplicated_usermeta' ); ?>

<h2><?php _e( 'Users', 'wps-cleaner' ) ?></h2>

<p><?php _e( 'Metadata: Additional data related to a use.', 'wps-cleaner' ); ?></p>

<p class="wps-italic"><?php _e( 'This will clean up the users of the database.', 'wps-cleaner' ); ?></p>

<div class="wps-list-tools">
	<table>
        <thead class="wps_text_found">
        <th><?php printf( __('Total found: %s Users and %s User Meta.', 'wps-cleaner' ), number_format_i18n( $total_users ), number_format_i18n( $total_usermeta ) ); ?></th>
        <th>Action</th>
        </thead>
		<tbody>
		<tr>
			<th class="wps-desc-tool">
				<strong class="name"><?php _e( 'Orphaned User Meta', 'wps-cleaner' ); ?></strong>
				<p class="description">
					<strong>Note : </strong> <?php _e( 'This tool will clean up ALL Orphaned User Meta from WordPress.', 'wps-cleaner' ); ?>
				</p>
			</th>
			<td class="run-tool">
				<a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_orphan_usermeta" data-nonce="<?php echo wp_create_nonce( 'clear_orphan_usermeta' ); ?>"><span class="text"><?php _e( 'Clear Orphaned User Meta', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $orphan_usermeta; ?>)</span></a>
			</td>
		</tr>
		<tr>
			<th class="wps-desc-tool">
				<strong class="name"><?php _e( 'Duplicated User Meta', 'wps-cleaner' ); ?></strong>
				<p class="description">
					<strong>Note : </strong> <?php _e( 'This tool will clean up ALL Duplicated User Meta from WordPress.', 'wps-cleaner' ); ?>
				</p>
			</th>
			<td class="run-tool">
				<a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_duplicated_usermeta" data-nonce="<?php echo wp_create_nonce( 'clear_duplicated_usermeta' ); ?>"><span class="text"><?php _e( 'Clear Duplicated User Meta', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $duplicated_usermeta; ?>)</span></a>
			</td>
		</tr>
		</tbody>
	</table>
</div>

