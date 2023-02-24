<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$total_term_relationships   = \WPS\WPS_Cleaner\Helpers::total_count( 'term_relationships' );
$total_term_taxonomy        = \WPS\WPS_Cleaner\Helpers::total_count( 'term_taxonomy' );
$total_terms                = \WPS\WPS_Cleaner\Helpers::total_count( 'terms' );
$total_termmeta             = \WPS\WPS_Cleaner\Helpers::total_count( 'termmeta' );

$orphan_term_relationships  = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'orphan_term_relationships' );
$unused_terms               = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'unused_terms' );
$orphan_termmeta            = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'orphan_termmeta' );
$duplicated_termmeta        = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'duplicated_termmeta' ); ?>

<h2><?php _e( 'Terms', 'wps-cleaner' ) ?></h2>

<p><?php _e( 'Taxonomy: how to group posts (ex: Category).', 'wps-cleaner' ); ?><br />
<?php _e( 'Terms: group of a taxonomy (ex: Not classified).', 'wps-cleaner' ); ?></p>

<p class="wps-italic"><?php _e( 'This will clean up the terms of the database.', 'wps-cleaner' ); ?></p>

<div class="wps-list-tools">
	<table>
        <thead class="wps_text_found">
        <th><?php echo sprintf( __('Total found: %s Terms, %s Term Meta, %s Term Taxonomy and %s Term Relationships.', 'wps-cleaner' ), number_format_i18n( $total_terms ), number_format_i18n( $total_termmeta ), number_format_i18n( $total_term_taxonomy ), number_format_i18n( $total_term_relationships ) ); ?></th>
        <th>Action</th>
        </thead>
		<tbody>
		<tr>
			<th class="wps-desc-tool">
				<strong class="name"><?php _e( 'Orphaned Term Meta', 'wps-cleaner' ); ?></strong>
				<p class="description">
					<strong>Note : </strong> <?php _e( 'This tool will clean up ALL Orphaned Term Meta from WordPress.', 'wps-cleaner' ); ?>
				</p>
			</th>
			<td class="run-tool">
				<a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_orphan_termmeta" data-nonce="<?php echo wp_create_nonce( 'clear_orphan_termmeta' ); ?>"><span class="text"><?php _e( 'Clear Orphaned Term Meta', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $orphan_termmeta; ?>)</span></a>
			</td>
		</tr>
        <tr>
            <th class="wps-desc-tool">
                <strong class="name"><?php _e( 'Unused term', 'wps-cleaner' ); ?></strong>
                <p class="description">
                    <strong>Note : </strong> <?php _e( 'This tool will clean up ALL Unused terms from WordPress.', 'wps-cleaner' ); ?>
                </p>
            </th>
            <td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_unused_terms" data-nonce="<?php echo wp_create_nonce( 'clear_unused_terms' ); ?>"><span class="text"><?php _e( 'Clear Unused terms', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $unused_terms; ?>)</span></a>
            </td>
        </tr>
		<tr>
			<th class="wps-desc-tool">
				<strong class="name"><?php _e( 'Duplicated Term Meta', 'wps-cleaner' ); ?></strong>
				<p class="description">
					<strong>Note : </strong> <?php _e( 'This tool will clean up ALL Duplicated Term Meta from WordPress.', 'wps-cleaner' ); ?>
				</p>
			</th>
			<td class="run-tool">
				<a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_duplicated_termmeta" data-nonce="<?php echo wp_create_nonce( 'clear_duplicated_termmeta' ); ?>"><span class="text"><?php _e( 'Clear Duplicated Term Meta', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $duplicated_termmeta; ?>)</span></a>
			</td>
		</tr>
		<tr>
			<th class="wps-desc-tool">
				<strong class="name"><?php _e( 'Orphaned Term Relationship', 'wps-cleaner' ); ?></strong>
				<p class="description">
					<strong>Note : </strong> <?php _e( 'This tool will clean up ALL Orphaned Term Relationship from WordPress.', 'wps-cleaner' ); ?>
				</p>
			</th>
			<td class="run-tool">
				<a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_orphaned_term_relationship" data-nonce="<?php echo wp_create_nonce( 'clear_orphaned_term_relationship' ); ?>"><span class="text"><?php _e( 'Clear Orphaned Term Relationship', 'wps-cleaner' ); ?></span>
                        <span class="count">(<?php echo $orphan_term_relationships; ?>)</span></a>
			</td>
		</tr>
		</tbody>
	</table>
</div>