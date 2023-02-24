<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $wp_filesystem;

WP_Filesystem( true );

$list_files_natif_wp = apply_filters( 'wps_cleaner_list_files_natif_wp', array(
	'wp-admin',
	'wp-content',
	'wp-includes',
	'index.php',
	'wp-activate.php',
	'wp-blog-header.php',
	'wp-comments-post.php',
	'wp-config.php',
	'wp-cron.php',
	'wp-links-opml.php',
	'wp-load.php',
	'wp-login.php',
	'wp-mail.php',
	'wp-settings.php',
	'wp-signup.php',
	'wp-trackback.php',
	'xmlrpc.php',
    '.htaccess',
) );

$list_files_delete = apply_filters( 'wps_cleaner_list_files_delete', array(
	'license.txt',
	'readme.html',
	'wp-config-sample.php',
) );

$list_files_website = apply_filters( 'wps_cleaner_list_files_website', array(
	'robots.txt',
	'sitemap.xml',
	'sitemap.xml.gz',
	'favicon.ico',
	'favicon.png',
	'humans.txt',
	'BingSiteAuth.xml',
    '.user.ini',
    'CMCIC_Tpe.inc.php',
    'wordfence-waf.php'
) );

$list_dir_website = apply_filters( 'wps_cleaner_list_dir_website', array(
	'.well-known',
    'cgi-bin',
    'wc-logs',
    '0-corbeille-0'
) );

$filelist = $wp_filesystem->dirlist( ABSPATH );

if ( empty( $filelist ) ) {
	return false;
}

$dirs = $files = array();

foreach ( $filelist as $key => $data ) {
	if ( is_dir( ABSPATH . $key ) ) {
		$dirs[] = $key;
	} else {
		$files[] = $key;
	}
} ?>

<h2><?php _e( 'Files', 'wps-cleaner' ) ?></h2>

<div class="wps_files_info">
    <p><?php _e( 'The files we recommend deleting are not used for site management.', 'wps-cleaner' ); ?></p>
    <p class="wps_files_info_alert"><i
                class="fal fa-exclamation-circle"></i> <?php _e( 'This tab represents <strong>recommendations</strong> and not an obligation.', 'wps-cleaner' ); ?>
        <br/><?php _e( 'We strongly recommend that you scan these files, as these may be files that are of great value to you. Deleting files commits the <strong>permanent deletion</strong> of your files on your server.', 'wps-cleaner' ); ?>
    </p>
</div>

<?php

$bad_files = \WPS\WPS_Cleaner\Helpers::get_old_files_wp();
if ( ! empty( $bad_files ) ) :
	$count = count( $bad_files );
	\WPS\WPS_Cleaner\Helpers::slice_text( $bad_files, 3 ); ?>

    <br/>
    <div class="wps-list-tools wps-table-old-files">
        <table>
            <thead class="wps_text_found">
            <th><?php printf( __( 'Old WordPress files %s', 'wps-cleaner' ), $count ); ?></th>
            <th>Action</th>
            </thead>
            <tbody>
            <tr>
                <th class="wps-desc-tool">
                    <strong class="name"><?php _e( 'Your WordPress installation contains old WordPress files', 'wps-cleaner' ); ?></strong>
                    <p class="description">
						<?php echo implode( ', ', $bad_files ); ?>
                    </p>
                </th>
                <td class="run-tool">
                    <a href="#" class="button btn-wps clear_sessions delete_old_files wps-clean"
                       data-action="delete_old_files" data-nonce="<?php echo wp_create_nonce( 'delete_old_files' ); ?>"><span
                                class="text"><?php _e( 'Delete old files', 'wps-cleaner' ); ?></span>
                        <span class="count">(<?php echo $count; ?>)</span></a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php
endif; ?>

<?php
$files_download = array();
foreach ( $dirs as $dir ) {
	if ( ! in_array( $dir, $list_files_natif_wp ) && ! in_array( $dir, $list_files_delete ) ) {
		$files_download[] = $dir;
	}
}
foreach ( $files as $file ) {
	if ( ! in_array( $file, $list_files_natif_wp ) && ! in_array( $file, $list_files_delete ) ) {
		$files_download[] = $file;
	}
}

if ( $files_download ) : ?>
    <div class="wps_medias_download">
        <p><?php echo sprintf( __( 'You can download a zip of %s no natif files WordPress on the site, before permanently deleting them.', 'wps-cleaner' ), number_format_i18n( count( $files_download ) ) ); ?></p>
        <button id="download_zip_file" data-nonce="<?php echo wp_create_nonce( 'create-zip' ); ?>"
                data-ids="<?php echo implode( ',', $files_download ); ?>"><?php _e( 'Download the zip of non native files WordPress', 'wps-cleaner' ); ?>
        </button>
    </div>
<?php endif; ?>

<table class="wps_files_table">
    <thead>
    <th><?php _e( 'Type', 'wps-cleaner' ); ?></th>
    <th><?php _e( 'Folder / File', 'wps-cleaner' ); ?></th>
    <th><?php _e( 'Weight', 'wps-cleaner' ); ?></th>
    <th class="wps_table_fixed_width"><?php _e( 'Information', 'wps-cleaner' ); ?></th>
    <th><?php _e( 'Action', 'wps-cleaner' ); ?></th>
    </thead>
    <tbody>
	<?php
	asort( $dirs );
	foreach ( $dirs as $dir ) {
		$to_delete = '';
		if ( ! in_array( $dir, $list_files_natif_wp ) && ! in_array( $dir, $list_files_delete ) ) {
			if ( in_array( $dir, $list_dir_website ) ) {
				$check_natif = '<span class="wps_files_website">' . __( 'Useful dir for smooth operation', 'wps-cleaner' ) . '</span>';
			} else {
				$check_natif = '<span class="wps_files_nonatif">' . __( 'Not native in WordPress', 'wps-cleaner' ) . '</span>';
			}
			if ( ! in_array( $dir, $list_dir_website ) ) {
				$to_delete = '<a href="#" class="button btn-wps clear_sessions delete_dir_file" data-action="delete_dir_file" data-nonce="' . wp_create_nonce( 'delete_dir_file' ) . '" data-dir_file="' . $dir . '">' . __( 'Delete' ) . '</a>';
			}
		} else {
			$check_natif = '<span class="wps_files_ok">' . __( 'Native in WordPress', 'wps-cleaner' ) . '</span>';
		}
		if ( false === ( $dirsize = get_transient( 'wps_cleaner_dir_size_' . $dir ) ) ) {
			$dirsize = \WPS\WPS_Cleaner\Helpers::folderSize( ABSPATH . $dir );
			set_transient( 'wps_cleaner_dir_size_' . $dir, $dirsize, 48 * HOUR_IN_SECONDS );
		}
		echo '<tr><td><i class="fal fa-folder"></i></td><td>' . $dir . '</td><td>' . \WPS\WPS_Cleaner\Helpers::humanFileSize( $dirsize ) . '</td><td>' . $check_natif . '</td><td>' . $to_delete . '</td></tr>';
	}
	asort( $files );
	foreach ( $files as $file ) {
		$to_delete = '';
		if ( ! in_array( $file, $list_files_natif_wp ) && ! in_array( $file, $list_files_delete ) ) {
			if ( in_array( $file, $list_files_website ) ) {
				$check_natif = '<span class="wps_files_website">' . __( 'Useful file for smooth operation', 'wps-cleaner' ) . '</span>';
			} else {
				$check_natif = '<span class="wps_files_nonatif">' . __( 'Not native in WordPress', 'wps-cleaner' ) . '</span>';
			}
			if ( ! in_array( $file, $list_files_website ) ) {
				$to_delete = '<a href="#" class="button btn-wps clear_sessions delete_dir_file" data-action="delete_dir_file" data-nonce="' . wp_create_nonce( 'delete_dir_file' ) . '" data-dir_file="' . $file . '">' . __( 'Delete' ) . '</a>';
			}
		} else {
			$conseil = '';
			if ( in_array( $file, $list_files_delete ) ) {
				$conseil = ' ' . __( '(useless file)', 'wps-cleaner' );
			}
			$check_natif = '<span class="wps_files_ok">' . __( 'Native in WordPress', 'wps-cleaner' ) . $conseil . '</span>';
		}
		if ( in_array( $file, $list_files_delete ) ) {
			$to_delete = '<a href="#" class="button btn-wps clear_sessions delete_dir_file" data-action="delete_dir_file" data-nonce="' . wp_create_nonce( 'delete_dir_file' ) . '" data-dir_file="' . $file . '">' . __( 'Delete' ) . '</a>';
		}
		if ( false === ( $filesize = get_transient( 'wps_cleaner_dir_size_' . $file ) ) ) {
			$filesize = filesize( ABSPATH . $file );
			set_transient( 'wps_cleaner_dir_size_' . $file, $filesize, 48 * HOUR_IN_SECONDS );
		}

		echo '<tr><td><i class="fal fa-file"></i></td><td>' . $file . '</td><td>' . \WPS\WPS_Cleaner\Helpers::humanFileSize( $filesize ) . '</td><td>' . $check_natif . '</td><td>' . $to_delete . '</td></tr>';
	}
	?>
    </tbody>
</table>
