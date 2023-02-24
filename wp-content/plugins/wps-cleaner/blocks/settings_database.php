<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $wpdb;
$result       = $wpdb->get_results( 'SHOW TABLE STATUS', ARRAY_A );
$total_tables = count( $result ); ?>

<h2><?php _e( 'Database', 'wps-cleaner' ) ?></h2>

<p class="wps_text_found_table"><?php echo __( 'Database name', 'wps-cleaner') . ': ' . $wpdb->__get( 'dbname' ); ?></p>

<div class="wps-list-tools">
    <table class="database">
        <thead>
            <th>N°</th>
            <th><?php _e( 'Table', 'wps-cleaner' ); ?></th>
            <th><?php _e( 'Records', 'wps-cleaner' ); ?></th>
            <th><?php _e( 'Weight', 'wps-cleaner' ); ?></th>
            <th><?php _e('Type', 'wps-cleaner' ); ?></th>
        </thead>
        <tbody>
		<?php
		$i           = 1;
		$total_sizes = $total_records = 0;
		foreach ( $result as $row ) {
			$size          = round( ( ( $row["Data_length"] + $row["Index_length"] ) / 1024 / 1024 ), 2 );
			$total_sizes   += $size;
			$record        = $wpdb->get_results( 'SELECT COUNT(*) as count FROM ' . $row['Name'] );
			$count_record  = reset( $record )->count;
			$total_records += $count_record;
			echo '<tr><td>' . $i . '</td><td>' . $row['Name'] . '</td><td>' . $count_record . '</td><td>' . $size . ' mb</td><td>' . $row['Engine'] . '</td></tr>';
			$i ++;
		} ?>
        </tbody>
        <tfoot>
        <tr>
            <td><?php _e( 'Total', 'wps-cleaner' ); ?>:</td>
            <td><?php echo $i - 1 . ' Tables'; ?></td>
            <td><?php echo $total_records; ?></td>
            <td></td>
            <td></td>
        </tr>
        </tfoot>
    </table>
</div>
<p class="wps_text_footer_table"><?php printf( __( 'Total size of database: %s mb', 'wps-cleaner' ), $total_sizes ); ?></p>