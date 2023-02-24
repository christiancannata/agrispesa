<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
if ( ! class_exists( 'WPS_CLEANER_MEDIA_List_Table' ) ) {
	class WPS_CLEANER_MEDIA_List_Table extends WP_List_Table {
		/**
		 * Prepare the items for the table to process
		 *
		 * @return Void
		 */
		public function prepare_items() {
			$columns  = $this->get_columns();
			$hidden   = $this->get_hidden_columns();
			$sortable = $this->get_sortable_columns();

			/** Process bulk action */
			$this->process_bulk_action();

			$perPage     = 30;
			$currentPage = $this->get_pagenum();

			$totalItems = self::record_count();

			$this->set_pagination_args( array(
				'total_items' => $totalItems,
				'per_page'    => $perPage
			) );

			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->items           = $this->table_data( $perPage, $currentPage );
		}

		public function record_count() {
			global $wpdb;
			$sql_attachments = "SELECT COUNT(*) FROM {$wpdb->posts} posts WHERE posts.post_type = 'attachment' AND ((posts.post_status = 'inherit'))";
			$count_all       = (int) $wpdb->get_var( $sql_attachments );

			$items_sync  = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(distinct(media_id)) as count FROM " . \WPS\WPS_Cleaner\DB_Table::get_instance()->get_table_name() . " WHERE blog_id = %d", get_current_blog_id() ) );
			$count_items = (int) reset( $items_sync )->count;

			return ( $count_all - $count_items );
		}

		/**
		 * Returns an associative array containing the bulk action
		 *
		 * @return array
		 */
		public function get_bulk_actions() {
			$actions = [
				'bulk-whitelist' => __( 'Exclusion' ),
				'bulk-delete'    => __( 'Delete' ),
				'bulk-download'  => __( 'Download', 'wps-cleaner' )
			];

			return $actions;
		}

		public function process_bulk_action() {

			$db_table = \WPS\WPS_Cleaner\DB_Table::get_instance();
			$blog_id  = get_current_blog_id();

			//Detect when a bulk action is being triggered...
			if ( 'delete' === $this->current_action() ) {

				// In our file that handles the request, verify the nonce.
				$nonce = esc_attr( $_REQUEST['_wpnonce'] );

				if ( ! wp_verify_nonce( $nonce, 'wps_cleaner_delete_attachment' ) ) {
					die( 'Go get a life script kiddies' );
				} else {
					wp_delete_attachment( absint( $_GET['attachment_id'] ) );

					wp_redirect( wp_get_referer() );
					exit;
				}

			}

			if ( 'whitelist' === $this->current_action() ) {

				// In our file that handles the request, verify the nonce.
				$nonce = esc_attr( $_REQUEST['_wpnonce'] );

				if ( ! wp_verify_nonce( $nonce, 'wps_cleaner_add_whitelist' ) ) {
					die( 'Go get a life script kiddies' );
				} else {
					$db_table->db->insert( $db_table->get_table_name(), [
						'blog_id'   => $blog_id,
						'type'      => 'whitelist',
						'media_id'  => absint( $_GET['attachment_id'] ),
						'object_id' => '0',
					], [ '%d', '%s', '%d', '%s' ] );

					wp_redirect( wp_get_referer() );
					exit;
				}

			}

			$action  = esc_attr( $_POST['action'] );
			$action2 = esc_attr( $_POST['action2'] );

			// If the delete bulk action is triggered
			if ( ( isset( $action ) && $action == 'bulk-whitelist' )
			     || ( isset( $action2 ) && $action2 == 'bulk-whitelist' )
			) {

				$whitelist_ids = esc_sql( $_POST['bulk-delete'] );

				if ( $whitelist_ids ) {
					// loop over the array of record IDs and delete them
					foreach ( $whitelist_ids as $media_id ) {
						$db_table->db->insert( $db_table->get_table_name(), [
							'blog_id'   => $blog_id,
							'type'      => 'whitelist',
							'media_id'  => $media_id,
							'object_id' => '0',
						], [ '%d', '%s', '%d', '%s' ] );
					}
				}
			}

			// If the delete bulk action is triggered
			if ( ( isset( $action ) && $action == 'bulk-delete' )
			     || ( isset( $action2 ) && $action2 == 'bulk-delete' )
			) {

				$delete_ids = esc_sql( $_POST['bulk-delete'] );

				// loop over the array of record IDs and delete them
				foreach ( $delete_ids as $id ) {
					wp_delete_attachment( $id );
				}
			}

			// If the download bulk action is triggered
			if ( ( isset( $action ) && $action == 'bulk-download' )
			     || ( isset( $action2 ) && $action2 == 'bulk-download' )
			) {

				$download_ids = esc_sql( $_POST['bulk-delete'] );

				if ( empty( $download_ids ) ) {
					return false;
				}

				$upload_dir = wp_upload_dir();
				$path       = $upload_dir['basedir'] . '/';
				$filename   = 'archive_wps_cleaner_medias_' . date( 'Y_m_d' ) . '_' . time() . '.zip';

				$destination = $path . $filename;

				$files = array();
				foreach ( $download_ids as $attachment_id ) {
					$files[] = get_attached_file( $attachment_id );
				}

				if ( empty( $files ) ) {
					return false;
				}

				//create the archive
				$zip = new ZipArchive();
				$res = $zip->open( $destination, ZipArchive::CREATE );
				if ( $res !== true ) {
					return false;
				}

				//add the files
				foreach ( $files as $file ) {
					$zip->addFile( $file, basename( $file ) );
				}

				$zip->close();

				if ( file_exists( $destination ) ) {

					$file_name = basename( $destination );

					header( "Content-Type: application/zip" );
					header( "Content-Disposition: attachment; filename=$file_name" );
					header( "Content-Length: " . filesize( $destination ) );

					ob_clean();
					flush();
					readfile( $destination );
					@unlink( $destination );
					exit;
				}

				wp_redirect( wp_get_referer() );
				exit;
			}
		}

		/**
		 * Override the parent columns method. Defines the columns to use in your listing table
		 *
		 * @return array
		 */
		public function get_columns() {
			$columns = array(
				'cb'        => '<input type="checkbox" />',
				'title'     => _x( 'File', 'column name' ),
				'author'    => __( 'Author' ),
				'date'      => __( 'Date' ),
				'whitelist' => __( 'Exclusion' ),
			);

			return $columns;
		}

		public function get_hidden_columns() {
			return array();
		}

		private function is_site_icon( WP_Post $attachment ) {
			return ( 'site-icon' === get_post_meta( $attachment->ID, '_wp_attachment_context', true ) );
		}

		private function is_logo_div( WP_Post $attachment ) {
			if ( ! function_exists( 'et_get_option' ) ) {
				return false;
			}

			$logo = et_get_option( 'divi_logo' );

			return ( $logo === $attachment->guid );
		}

		/**
		 * Get the table data
		 *
		 * @return array
		 */
		public function table_data( $per_page = 30, $page_number = 1 ) {

			$db_table = \WPS\WPS_Cleaner\DB_Table::get_instance();
			$results  = $db_table->db->get_results( $db_table->db->prepare( "SELECT media_id FROM " . $db_table->get_table_name() . " WHERE blog_id = %d", get_current_blog_id() ), ARRAY_A );

			$args  = array(
				'post_type'      => 'attachment',
				'posts_per_page' => $per_page,
				'offset'         => ( $page_number - 1 ) * $per_page,
				'post__not_in'   => wp_list_pluck( $results, 'media_id' ),
				'post_status'    => 'inherit',
			);
			$query = new WP_Query( $args );

			if ( empty( $query->posts ) ) {
				return array();
			}

			foreach ( $query->posts as $raw_data ) {
				if ( $this->is_site_icon( $raw_data ) ) {
					continue;
				}

				if ( $this->is_logo_div( $raw_data ) ) {
					continue;
				}

				$data[] = array(
					'id'     => $raw_data->ID,
					'author' => get_the_author_meta( 'display_name', $raw_data->post_author ),
					'date'   => $raw_data->post_date,
				);
			}

			wp_reset_query();

			return $data;
		}

		/**
		 * Define the sortable columns
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			return array(
				'id'     => array( 'id', false ),
				'author' => array( 'author', false ),
				'date'   => array( 'date', false )
			);
		}

		/**
		 * Handles the date column output.
		 */
		public function column_date( $item ) {
			if ( '0000-00-00 00:00:00' === $item['date'] ) {
				$h_time = __( 'Unpublished' );
			} else {
				$m_time = $item['date'];
				$time   = get_post_time( 'G', true, $item['id'], false );
				if ( ( abs( $t_diff = time() - $time ) ) < DAY_IN_SECONDS ) {
					if ( $t_diff < 0 ) {
						$h_time = sprintf( __( '%s from now' ), human_time_diff( $time ) );
					} else {
						$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
					}
				} else {
					$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
				}
			}

			echo $h_time;
		}

		/**
		 * Handles the title column output.
		 *
		 */
		public function column_title( $item ) {

			// create a nonce
			$delete_nonce = wp_create_nonce( 'wps_cleaner_delete_attachment' );

			$actions = [
				'delete' => sprintf( '<a href="?page=%s&tab=media&action=%s&attachment_id=%s&_wpnonce=%s">' . __( 'Delete' ) . '</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
			];

			$post_id = $item['id'];

			$post = get_post( $post_id );

			list( $mime ) = explode( '/', $post->post_mime_type );

			$title      = _draft_or_post_title( $post_id );
			$thumb      = wp_get_attachment_image( $post->ID, array( 60, 60 ), true, array( 'alt' => '' ) );
			$link_start = $link_end = '';

			if ( current_user_can( 'edit_post', $post->ID ) && ! $this->is_trash ) {
				$link_start = sprintf(
					'<a href="%s" aria-label="%s">',
					get_edit_post_link( $post->ID ),
					/* translators: %s: attachment title */
					esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $title ) )
				);
				$link_end   = '</a>';
			}

			$class = $thumb ? ' class="has-media-icon"' : '';
			?>
            <strong<?php echo $class; ?>>
				<?php
				echo $link_start;
				if ( $thumb ) : ?>
                    <span class="media-icon <?php echo sanitize_html_class( $mime . '-icon' ); ?>"><?php echo $thumb; ?></span>
				<?php endif;
				echo $title . $link_end;
				_media_states( $post );
				?>
            </strong>
            <p class="filename">
                <span class="screen-reader-text"><?php _e( 'File name:' ); ?> </span>
				<?php
				$file = get_attached_file( $post->ID );
				echo esc_html( wp_basename( $file ) );
				echo $this->row_actions( $actions );
				?>
            </p>
			<?php
		}


		/**
		 * Handles the title column output.
		 *
		 */
		public function column_whitelist( $item ) {

			// create a nonce
			$whitelist_nonce = wp_create_nonce( 'wps_cleaner_add_whitelist' );

			$url = sprintf( '<a href="?page=%s&tab=media&action=%s&attachment_id=%s&_wpnonce=%s" title="' . __( 'I think my media is used', 'wps-cleaner' ) . '">%s</a>', esc_attr( $_REQUEST['page'] ), 'whitelist', absint( $item['id'] ), $whitelist_nonce, __( 'Exclude from this list', 'wps-cleaner' ) ); ?>

            <p class="wps_whitelist_medias"><?php echo $url; ?></p>
			<?php
		}

		/**
		 * Define what data to show on each column of the table
		 *
		 * @param  array $item Data
		 * @param  String $column_name - Current column name
		 *
		 * @return Mixed
		 */
		public function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'id':
				case 'author':
				case 'delete':
					return $item[ $column_name ];

				default:
					return print_r( $item, true );
			}
		}

		/**
		 * Render the bulk edit checkbox
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
			);
		}

		/**
		 * Get a list of CSS classes for the WP_List_Table table tag.
		 *
		 * @since 3.1.0
		 *
		 * @return array List of CSS classes for the table tag.
		 */
		protected function get_table_classes() {
			return array( 'widefat', 'fixed', 'striped', $this->_args['plural'], 'wps-list-media-unuse', 'media' );
		}
	}
}

if ( ! class_exists( 'WPS_CLEANER_MEDIA_Whitelist_List_Table' ) ) {
	class WPS_CLEANER_MEDIA_Whitelist_List_Table extends WP_List_Table {
		/**
		 * Prepare the items for the table to process
		 *
		 * @return Void
		 */
		public function prepare_items() {
			$columns  = $this->get_columns();
			$hidden   = $this->get_hidden_columns();
			$sortable = $this->get_sortable_columns();

			/** Process bulk action */
			$this->process_bulk_action();

			$perPage     = 30;
			$currentPage = $this->get_pagenum();

			$totalItems = self::record_count();

			$this->set_pagination_args( array(
				'total_items' => $totalItems,
				'per_page'    => $perPage
			) );

			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->items           = $this->table_data( $perPage, $currentPage );
		}

		public function record_count() {
			global $wpdb;

			$result = \WPS\WPS_Cleaner\Helpers::get_whitelist_medias();

			return count( $result );
		}

		/**
		 * Returns an associative array containing the bulk action
		 *
		 * @return array
		 */
		public function get_bulk_actions() {
			$actions = [
				'bulk-delete' => __( 'Delete' ),
			];

			return $actions;
		}

		public function process_bulk_action() {

			$db_table = \WPS\WPS_Cleaner\DB_Table::get_instance();
			$blog_id  = get_current_blog_id();

			//Detect when a bulk action is being triggered...
			if ( 'delete' === $this->current_action() ) {

				// In our file that handles the request, verify the nonce.
				$nonce = esc_attr( $_REQUEST['_wpnonce'] );

				if ( ! wp_verify_nonce( $nonce, 'wps_cleaner_delete_whitelist_attachment' ) ) {
					die( 'Go get a life script kiddies' );
				} else {
				    $db_table->db->delete( $db_table->get_table_name(), [
						'blog_id'     => $blog_id,
						'media_id'    => absint( $_GET['attachment_id'] ),
						'type'        => 'whitelist',
						'object_id'   => '0',
					], [ '%d', '%s' ] );

					wp_redirect( wp_get_referer() );
					exit;
				}

			}

			$action  = esc_attr( $_POST['action'] );
			$action2 = esc_attr( $_POST['action2'] );

			// If the delete bulk action is triggered
			if ( ( isset( $action ) && $action == 'bulk-delete' )
			     || ( isset( $action2 ) && $action2 == 'bulk-delete' )
			) {

				$delete_ids = esc_sql( $_POST['bulk-delete'] );

				// loop over the array of record IDs and delete them
				foreach ( $delete_ids as $id ) {
					$db_table->db->delete( $db_table->get_table_name(), [
						'blog_id'     => $blog_id,
						'media_id'    => $id,
						'type'        => 'whitelist',
						'object_id'   => '0',
					], [ '%d', '%s' ] );
				}
			}
		}

		/**
		 * Override the parent columns method. Defines the columns to use in your listing table
		 *
		 * @return array
		 */
		public function get_columns() {
			$columns = array(
				'cb'     => '<input type="checkbox" />',
				'title'  => _x( 'File', 'column name' ),
				'author' => __( 'Author' ),
				'date'   => __( 'Date' )
			);

			return $columns;
		}

		public function get_hidden_columns() {
			return array();
		}

		/**
		 * Get the table data
		 *
		 * @return array
		 */
		public function table_data( $per_page = 30, $page_number = 1 ) {
			$results  = \WPS\WPS_Cleaner\Helpers::get_whitelist_medias();

			if ( empty( $results ) ) {
			    return false;
            }

			foreach ( $results as $raw_data ) {
			    $media = get_post( $raw_data['media_id'] );

				$data[] = array(
					'id'     => $media->ID,
					'author' => get_the_author_meta( 'display_name', $media->post_author ),
					'date'   => $media->post_date,
				);
			}

			wp_reset_query();

			return $data;
		}

		/**
		 * Define the sortable columns
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			return array(
				'id'     => array( 'id', false ),
				'author' => array( 'author', false ),
				'date'   => array( 'date', false )
			);
		}

		/**
		 * Handles the date column output.
		 */
		public function column_date( $item ) {
			if ( '0000-00-00 00:00:00' === $item['date'] ) {
				$h_time = __( 'Unpublished' );
			} else {
				$m_time = $item['date'];
				$time   = get_post_time( 'G', true, $item['id'], false );
				if ( ( abs( $t_diff = time() - $time ) ) < DAY_IN_SECONDS ) {
					if ( $t_diff < 0 ) {
						$h_time = sprintf( __( '%s from now' ), human_time_diff( $time ) );
					} else {
						$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
					}
				} else {
					$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
				}
			}

			echo $h_time;
		}

		/**
		 * Handles the title column output.
		 *
		 */
		public function column_title( $item ) {

			// create a nonce
			$delete_nonce = wp_create_nonce( 'wps_cleaner_delete_whitelist_attachment' );

			$actions = [
				'delete' => sprintf( '<a href="?page=%s&tab=media&action=%s&attachment_id=%s&_wpnonce=%s">' . __( 'Delete' ) . '</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
			];

			$post_id = $item['id'];

			$post = get_post( $post_id );

			list( $mime ) = explode( '/', $post->post_mime_type );

			$title      = _draft_or_post_title( $post_id );
			$thumb      = wp_get_attachment_image( $post->ID, array( 60, 60 ), true, array( 'alt' => '' ) );
			$link_start = $link_end = '';

			if ( current_user_can( 'edit_post', $post->ID ) && ! $this->is_trash ) {
				$link_start = sprintf(
					'<a href="%s" aria-label="%s">',
					get_edit_post_link( $post->ID ),
					/* translators: %s: attachment title */
					esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $title ) )
				);
				$link_end   = '</a>';
			}

			$class = $thumb ? ' class="has-media-icon"' : '';
			?>
            <strong<?php echo $class; ?>>
				<?php
				echo $link_start;
				if ( $thumb ) : ?>
                    <span class="media-icon <?php echo sanitize_html_class( $mime . '-icon' ); ?>"><?php echo $thumb; ?></span>
				<?php endif;
				echo $title . $link_end;
				_media_states( $post );
				?>
            </strong>
            <p class="filename">
                <span class="screen-reader-text"><?php _e( 'File name:' ); ?> </span>
				<?php
				$file = get_attached_file( $post->ID );
				echo esc_html( wp_basename( $file ) );
				echo $this->row_actions( $actions );
				?>
            </p>
			<?php
		}

		/**
		 * Define what data to show on each column of the table
		 *
		 * @param  array $item Data
		 * @param  String $column_name - Current column name
		 *
		 * @return Mixed
		 */
		public function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'id':
				case 'author':
				case 'delete':
					return $item[ $column_name ];

				default:
					return print_r( $item, true );
			}
		}

		/**
		 * Render the bulk edit checkbox
		 *
		 * @param array $item
		 *
		 * @return string
		 */
		function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
			);
		}

		/**
		 * Get a list of CSS classes for the WP_List_Table table tag.
		 *
		 * @since 3.1.0
		 *
		 * @return array List of CSS classes for the table tag.
		 */
		protected function get_table_classes() {
			return array( 'widefat', 'fixed', 'striped', $this->_args['plural'], 'wps-list-media-unuse', 'media' );
		}
	}
}

// WP_Media_List_Table is not loaded automatically so we need to load it in our application
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

$medias_table           = new WPS_CLEANER_MEDIA_List_Table;
$medias_whitelist_table = new WPS_CLEANER_MEDIA_Whitelist_List_Table;
$total_medias           = \WPS\WPS_Cleaner\Helpers::img_count();
$files                  = \WPS\WPS_Cleaner\DB::get_ids();
$whitelist_medias       = \WPS\WPS_Cleaner\Helpers::get_whitelist_medias(); ?>

<h2><?php _e( 'Medias', 'wps-cleaner' ) ?></h2>

<?php
$upload_dir = wp_upload_dir();
if ( false === ( $dirsize = get_transient( 'wps_cleaner_dir_size' ) ) ) {
	$dirsize = \WPS\WPS_Cleaner\Helpers::folderSize( $upload_dir['basedir'] );
	set_transient( 'wps_cleaner_dir_size', $dirsize, 48 * HOUR_IN_SECONDS );
} ?>

<p class="wps_text_found_table"><?php printf( __( 'Total found: %s Medias.', 'wps-cleaner' ), number_format_i18n( $total_medias ) ); ?>
    <br> <?php printf( __( 'Total Size: %s', 'wps-cleaner' ), \WPS\WPS_Cleaner\Helpers::humanFileSize( $dirsize ) ); ?>
</p>

<?php
$arr = array(
	__( 'Content of a post', 'wps-cleaner' ),
	__( 'Featured thumbnail', 'wps-cleaner' ),
	__( 'Website icon', 'wps-cleaner' ),
	__( 'Image Widget and Gallery', 'wps-cleaner' ),
	__( 'Woocommerce', 'wps-cleaner' ),
	__( 'DIVI Builder', 'wps-cleaner' ),
	__( 'Logo DIVI', 'wps-cleaner' ),
	__( 'Beaver Builder', 'wps-cleaner' ),
	__( 'Elementor', 'wps-cleaner' ),
	__( 'Visual Composer', 'wps-cleaner' ),
);

if ( function_exists( 'acf_get_field_groups' ) ) {
	$arr[] = __( 'ACF Field', 'wps-cleaner' );
} ?>

<div class="clearfix"></div>
<div class="wps_medias_info">
    <p><?php _e( 'Here is the list of media that appear to be <strong>unused</strong>, they are <strong>not</strong> used in the following cases:', 'wps-cleaner' ); ?> <?php echo implode( ', ', $arr ); ?>.</p>

    <p><?php _e( 'You can, however, exclude from the list the media of your choice (if they seem useful to you for your WordPress site) by placing them on an exclusion list (click in front of each image on "Exclude from this The list will not be available for deletion.', 'wps-cleaner' ); ?></p>

    <p class="wps_medias_info_alert"><i
                class="fal fa-exclamation-circle"></i> <?php _e( 'Warning, the plugin doesn\'t detect images used elsewhere than in the list above.', 'wps-cleaner' ); ?>
    </p>
</div>

<?php
global $wpdb;
$did_index = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->wps_cleaner_queue WHERE blog_id = %d", get_current_blog_id() ) );

if ( $did_index ) :
	// Verification du contenu de votre site ( x contenus restants )
	echo '<div class="media-indexing"><div class="doing"><i class="fal fa-spinner fa-spin"></i> ' . __( 'Content analysis in progress', 'wps-cleaner' ) . '</div><div class="response" style="display: none"><br /><br /><button type="button" class="button" onClick="window.location.reload()">' . __( 'Reload Page', 'wps-cleaner' ) . '</button></div></div>'; ?>
    <script>
        jQuery(document).ready(function () {
            function check_wps_cleaner_queue() {
                data = {
                    'action': 'check_wps_cleaner_queue'
                };

                t = jQuery.post(ajaxurl, data);

                t.success(function (response) {
                    if (response == 'false') {
                        jQuery('.doing').html(wps_cleaner_i18n.finish_analysis);
                        jQuery('.response').show();
                    }
                });
            }

            setInterval(check_wps_cleaner_queue, 10000);
        });
    </script>
<?php
else :
	if ( $files ) : ?>
        <div class="wps_medias_download">
            <p><?php echo sprintf( __( 'You can download a zip of %s "unused" images on the site, before permanently deleting them.', 'wps-cleaner' ), number_format_i18n( count( $files ) ) ); ?></p>
            <p><?php _e( 'Be careful, if you have too many "unused" media, the zip may not download.', 'wps-cleaner' ); ?></p>

            <button id="download_zip" data-nonce="<?php echo wp_create_nonce( 'download-zip' ); ?>"
                    data-files="<?php echo implode( ',', $files ); ?>"><?php _e( 'Download the zip of "unused" images', 'wps-cleaner' ); ?>
            </button>
        </div>
	<?php endif; ?>

	<?php if ( $whitelist_medias ) : ?>
        <div class="wps_medias_whitelist">
            <p>
                <?php echo sprintf( __( 'You have %s media in the exclusion list', 'wps-cleaner' ), number_format_i18n( count( $whitelist_medias ) ) ); ?>
                <button id="delete_whitelist" data-nonce="<?php echo wp_create_nonce( 'delete-whitelist' ); ?>"><?php _e( 'Empty the list', 'wps-cleaner' ); ?></button>
                <button id="view_whitelist"><?php _e( 'See the list', 'wps-cleaner' ); ?></button>
            </p>

            <div class="view_whitelist" style="display: none;">
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                            <div class="meta-box-sortables ui-sortable">
                                <form method="post">
                                    <?php $medias_whitelist_table->prepare_items(); ?>

                                    <?php $medias_whitelist_table->display(); ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    <br class="clear">
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <?php $medias_table->prepare_items(); ?>

                        <?php $medias_table->display(); ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
<?php endif; ?>
