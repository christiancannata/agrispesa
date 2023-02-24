<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$total_options     = \WPS\WPS_Cleaner\Helpers::total_count( 'options' );
$transient_options = \WPS\WPS_Cleaner\Helpers::count_cleanup_items( 'transient_options' ); ?>

<h2><?php _e( 'Options', 'wps-cleaner' ) ?></h2>

<p class="wps-italic"><?php _e( 'This will clean up the options in the database.', 'wps-cleaner' ); ?></p>

<div class="wps-list-tools">
    <table>
        <thead class="wps_text_found">
        <th><?php printf( __( 'Total found: %s Options', 'wps-cleaner' ), number_format_i18n( $total_options ) ); ?></th>
        <th>Action</th>
        </thead>
        <tbody>
        <tr>
            <th class="wps-desc-tool">
                <strong class="name"><?php _e( 'Transient Options', 'wps-cleaner' ); ?></strong>
                <p class="description">
                    <strong>Note
                        : </strong><?php echo sprintf( __( 'This tool will clean up ALL <a href="%s" target="_blank">transients</a> Options from WordPress.', 'wps-cleaner' ), 'https://wpformation.com/transients-wordpress/' ); ?>
                </p>
            </th>
            <td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_transient_options"
                   data-nonce="<?php echo wp_create_nonce( 'clear_transient_options' ); ?>"><span
                            class="text"><?php _e( 'Clear Transient Options', 'wps-cleaner' ); ?></span>
                    <span class="count">(<?php echo $transient_options; ?>)</span></a>
            </td>
        </tr>
        <tr>
            <th class="wps-desc-tool">
                <strong class="name"><?php _e( 'Schedule Options', 'wps-cleaner' ); ?></strong>
                <p class="description">
                    <strong>Note
                        : </strong><?php echo sprintf( __( 'This tool will clean up ALL <a href="%s" target="_blank">scheduled</a> Options from WordPress.', 'wps-cleaner' ), 'https://wpformation.com/cron-wordpress/' ); ?>
                </p>
            </th>
            <td class="run-tool">
                <a href="#" class="button btn-wps clear_sessions wps-clean" data-action="clear_schedule_options"
                   data-nonce="<?php echo wp_create_nonce( 'clear_schedule_options' ); ?>"><span
                            class="text"><?php _e( 'Clear Schedule Options', 'wps-cleaner' ); ?></span>
                </a>
            </td>
        </tr>
        </tbody>
    </table>
</div>