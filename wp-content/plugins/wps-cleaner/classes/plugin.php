<?php

namespace WPS\WPS_Cleaner;

class Plugin {

    use Singleton;

	protected function init() {
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );

		add_action( 'wps_cleaner_indexation_medias_queue', array(
			__CLASS__,
			'wps_cleaner_indexation_medias_schedule_queue'
		) );
		add_action( 'wps_cleaner_indexation_medias', array( __CLASS__, 'wps_cleaner_indexation_medias_schedule' ) );

		add_action( 'save_post', array( __CLASS__, 'index_post_queue' ), 999 );
		add_action( 'delete_post', array( __CLASS__, 'delete_post' ) );

		/* Widget */
		add_filter( 'widget_update_callback', array( __CLASS__, 'widget_update_callback' ), 20, 4 );
		add_action( 'delete_widget', array( __CLASS__, 'delete_widget' ) );

		add_action( 'wp_ajax_wps_cleaner_clean_all', array( __CLASS__, 'wps_cleaner_clean_all' ) );

		/* Post */
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_revisions' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_auto_drafts' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_deleted_posts' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_orphaned_post_meta' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_duplicated_postmeta' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_oembed_caches' ) );

		/* Comment */
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_unapproved_comments' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_spammed_comments' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_deleted_comments' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_orphan_commentmeta' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_duplicated_commentmeta' ) );

		/* User */
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_orphan_usermeta' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_duplicated_usermeta' ) );

		/* Option */
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_transient_options' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_schedule_options' ) );

		/* Term */
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_orphan_termmeta' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_duplicated_termmeta' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_orphaned_term_relationship' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_unused_terms' ) );

		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_unuse_plugins' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_unuse_themes' ) );

		/* Redirection */
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_logs_redirection' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_404_logs_redirection' ) );

		/* Zerospam */
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_logs_zerospam' ) );

		/* Woocommerce */
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_woo_transients' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_orphaned_variations' ) );
		add_action( 'wp_ajax_wps_cleaner_clean', array( __CLASS__, 'wps_cleaner_delete_sessions_woo' ) );

		/* Delete dir and files */
		add_action( 'wp_ajax_delete_old_files', array( __CLASS__, 'delete_old_files' ) );
		add_action( 'wp_ajax_delete_dir_file', array( __CLASS__, 'delete_dir_file' ) );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );

		// Zip media
		add_action( 'wp_ajax_create_zip_archive_medias', array( __CLASS__, 'create_zip_archive_medias' ) );
		add_action( 'wp_ajax_delete_zip_archive_medias', array( __CLASS__, 'delete_zip_archive_medias' ) );

		// Zip files
		add_action( 'wp_ajax_create_zip_archive_files', array( __CLASS__, 'create_zip_archive_files' ) );
		add_action( 'wp_ajax_delete_zip_archive_files', array( __CLASS__, 'delete_zip_archive_files' ) );

		add_action( 'wp_ajax_delete_medias_whitelist', array( __CLASS__, 'delete_medias_whitelist' ) );

		add_filter( 'admin_footer', array( __CLASS__, 'admin_footer' ) );
		add_filter( 'admin_footer_text', array( __CLASS__, 'admin_footer_text' ), 1 );
		add_action( 'wp_ajax_wpscleaner_rated', array( __CLASS__, 'wpscleaner_rated' ) );

		add_filter( 'plugin_action_links_' . WPS_CLEANER_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'wps_bidouille_not_display_pub_array', array( __CLASS__, 'wps_bidouille_not_display_pub_array' ) );

		add_filter( 'wps_cleaner_list_files_delete', array( __CLASS__, 'wps_cleaner_list_files_delete' ) );
		add_filter( 'wps_cleaner_excluded_taxonomies', array( __CLASS__, 'wps_cleaner_excluded_taxonomies' ) );

		add_action( 'wp_ajax_delete_alert', array( __CLASS__, 'delete_alert' ) );

		add_action( 'wp_ajax_check_wps_cleaner_queue', array( __CLASS__, 'check_wps_cleaner_queue' ) );

		add_action( 'tool_box', array( __CLASS__, 'tool_box' ) );

		add_filter( 'wps_cleaner_db_get_data', array( __CLASS__, 'format_indexed_values' ), 100 );

		add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) );
		add_action( 'wp', array( __CLASS__, 'wp_schedule' ) );
	}

	public static function cron_schedules( $schedules ) {

		$schedules['wps_cleaner_every_one_minute'] = array(
			'interval' => 60,
			'display'  => __( 'Every 1 Minute', 'wps-cleaner' )
		);

		return $schedules;
	}

	/**
	 * Format the data :
	 *
	 * {object_type} : [
	 *      {media_id} : [
	 *          {object_id} : [ {type}, {type} ]
	 *      ]
	 * ]
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function format_indexed_values( $data ) {
		if ( empty( $data ) ) {
			return [];
		}

		$out = [];
		foreach ( $data as $_d ) {
			if ( isset( $out[ $_d->media_id ][ $_d->object_id ] ) && in_array( $_d->type, $out[ $_d->media_id ][ $_d->object_id ] ) ) {
				// Already exists
				continue;
			}

			if ( empty( $out[ $_d->media_id ][ $_d->object_id ] ) ) {
				// First value
				$out[ $_d->media_id ][ $_d->object_id ][] = $_d->type;
			} else {
				// Adding to the others
				array_push( $out[ $_d->media_id ][ $_d->object_id ], $_d->type );
			}
		}

		return $out;
	}

	/**
	 * Schedule cron
	 */
	public static function schedule() {
		// Index all content with a cron
		wp_schedule_single_event( time(), 'wps_cleaner_indexation_medias_queue' );
	}

	public static function wp_schedule() {
		if ( ! wp_next_scheduled( 'wps_cleaner_indexation_medias' ) ) {
			//wp_schedule_event( time(), 'wps_cleaner_every_one_minute', 'wps_cleaner_indexation_medias' );
			wp_schedule_event( time(), 'hourly', 'wps_cleaner_indexation_medias' );
		}
	}

	/**
	 * Admin menu WPServeur
	 */
	public static function admin_menu() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		add_management_page(
			__( 'WPS Cleaner', 'wps-cleaner' ),
			__( 'WPS Cleaner', 'wps-cleaner' ),
			'manage_options',
			'wps-cleaner',
			array( __CLASS__, 'admin_page' )
		);
	}

	/**
	 * Display a custom menu page
	 */
	public static function admin_page() {
		include( WPS_CLEANER_DIR . '/admin_page/plugin.php' );
	}

	public static function install() {
		add_option( 'wps_cleaner_alert', true );

		DB_Table::get_instance()->upgrade_database();
		// For safety, delete existing data
		DB::get_instance()->delete_blog( get_current_blog_id() );

		self::schedule();
	}

	public static function deactivate() {
		DB::get_instance()->delete_blog( get_current_blog_id() );

		global $wpdb;
		$wpdb->delete( $wpdb->wps_cleaner_queue, array( 'blog_id' => get_current_blog_id() ), array( '%d' ) );

		wp_clear_scheduled_hook( 'wps_cleaner_indexation_medias' );
	}

	public static function uninstall() {
		delete_network_option(- 1, 'wps_cleaner_version' );

		global $wpdb;
		$table_name = $wpdb->prefix . 'wps_cleaner_queue';
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);

		$table_name = $wpdb->prefix . 'wps_cleaner';
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);
	}

	public static function wps_cleaner_indexation_medias_schedule_queue() {
		$query = new \WP_Query( array(
			'no_found_rows'          => true,
			'nopaging'               => true,
			'post_type'              => 'any',
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );

		foreach ( $query->posts as $post_id ) {
			self::index_post_queue( $post_id );
		}

		wp_clear_scheduled_hook( 'wps_cleaner_indexation_medias_queue' );
	}

	public static function index_post_queue( $post_id ) {
		global $wpdb;

		/*$post = get_post( $post_id );
		$text = $post->post_content;

		$img_ids = array();

		preg_match_all( '/\[vc_[a-z_]+ (?:interval="[0-9]" )?image[s]?="([0-9]+)"\]/', $text, $images );
		$img_ids = Helpers::get_img_ids_regex( $img_ids, $images );

		var_dump($text, $img_ids);die;*/

		if ( 'attachment' === get_post_type( $post_id ) ) {
			return false;
		}

		$wpdb->insert(
			$wpdb->wps_cleaner_queue,
			array(
				'blog_id' => 1,
				'post_id' => $post_id,
			),
			array( '%s', '%s' ),
			'INSERT'
		);

		return true;
	}

	public static function wps_cleaner_indexation_medias_schedule() {
		$items_to_sync = self::get_results( WPS_CLEANER_CRON_QTY, get_current_blog_id() );

		foreach ( $items_to_sync as $item ) {
			self::index_post( $item, true );
			self::delete( $item->id );
		}
	}

	/**
	 * Get items
	 *
	 * @param integer $quantity
	 * @param integer $blog_id
	 *
	 * @return array
	 */
	public static function get_results( $quantity = 100, $blog_id = 0 ) {
		global $wpdb;

		/** @var WPDB $wpdb */

		if ( 0 < $blog_id ) {
			return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->wps_cleaner_queue WHERE blog_id = %d LIMIT %d", $blog_id, $quantity ) );
		}

		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->wps_cleaner_queue LIMIT %d", $quantity ) );
	}

	/**
	 * Delte an item from queue
	 *
	 * @param $id
	 *
	 * @return int|false
	 */
	public static function delete( $id ) {
		global $wpdb;

		/** @var WPDB $wpdb */

		return $wpdb->delete( $wpdb->wps_cleaner_queue, array( 'id' => $id ), array( '%d' ) );
	}

	/**
	 *
	 * On save post, index post's media
	 *
	 * @param $post_id
	 * @param $post
	 * @param $update
	 */
	public static function index_post( $item, $update ) {

		$post_id = $item->post_id;

		/**
		 * Before insert, delete all data against object_id
		 * To ensure to not store useless data
		 */
		DB::delete_all_object_id( $post_id );

		/**
		 * Fires once a post has been saved.
		 *
		 * Get images from multiple sources to index against post
		 *
		 * @param array $image_ids Array of images id.
		 * @param int $post_id Post ID.
		 */
		$image_ids = apply_filters( 'wps_cleaner_post_index', array(), $post_id );
		if ( empty( $image_ids ) ) {
			return;
		}

		// Validate image IDs
		$image_ids = Helpers::check_image_ids( $image_ids );

		if ( empty( $image_ids ) ) {
			return;
		}

		DB::insert( $image_ids, $post_id );
	}

	public static function widget_update_callback( $instance, $new_instance, $old_instance, $widget ) {
		if ( ! empty( $old_instance ) ) {
			if ( isset( $instance['ids'] ) ) {
				global $wpdb;
				$wpdb->delete( $wpdb->postmeta, array(
					'meta_key'   => 'wps_cleaner_media_use',
					'meta_value' => $widget->id
				) );
			}
		}

		if ( ! empty( $old_instance ) ) {
			if ( isset( $instance['attachment_id'] ) ) {
				global $wpdb;
				$wpdb->delete( $wpdb->postmeta, array(
					'meta_key'   => 'wps_cleaner_media_use',
					'meta_value' => $widget->id
				) );
			}
		}

		if ( ! isset( $instance['attachment_id'] ) && ! isset( $instance['ids'] ) ) {
			return $instance;
		}

		if ( isset( $instance['ids'] ) ) {
			foreach ( $instance['ids'] as $media_id ) {
				if ( ! wp_attachment_is_image( $media_id ) ) {
					continue;
				}
				add_post_meta( $media_id, 'wps_cleaner_media_use', $widget->id );
			}
		}

		if ( isset( $instance['attachment_id'] ) ) {
			add_post_meta( $instance['attachment_id'], 'wps_cleaner_media_use', $widget->id );
		}

		return $instance;
	}

	public static function delete_widget( $widget_id ) {
		global $wpdb;
		$wpdb->delete( $wpdb->postmeta, array(
			'meta_key'   => 'wps_cleaner_media_use',
			'meta_value' => $widget_id
		) );
	}

	/**
	 * @param $post_id
	 */
	public static function delete_post( $post_id ) {

		DB::delete_all_object_id( $post_id );

		/*global $wpdb;
		$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => 'wps_cleaner_media_use', 'meta_value' => $post_id ) );*/
	}

	public static function wps_cleaner_clean_all() {
		if ( ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'sweep_all' ) ) {
			return false;
		}

		Helpers::doing_clean( 'delete_revisions' );
		Helpers::doing_clean( 'delete_auto_draft' );
		Helpers::doing_clean( 'delete_deleted_posts' );
		Helpers::doing_clean( 'delete_orphaned_post_meta' );
		Helpers::doing_clean( 'delete_duplicated_postmeta' );
		Helpers::doing_clean( 'delete_oembed_caches' );
		Helpers::doing_clean( 'delete_unapproved_comments' );
		Helpers::doing_clean( 'delete_spammed_comments' );
		Helpers::doing_clean( 'delete_deleted_comments' );
		Helpers::doing_clean( 'delete_orphan_commentmeta' );
		Helpers::doing_clean( 'delete_duplicated_commentmeta' );
		Helpers::doing_clean( 'delete_orphan_usermeta' );
		Helpers::doing_clean( 'delete_duplicated_usermeta' );
		Helpers::doing_clean( 'delete_orphan_termmeta' );
		Helpers::doing_clean( 'delete_unused_terms' );
		Helpers::doing_clean( 'delete_orphaned_term_relationship' );
		Helpers::doing_clean( 'delete_transient_options' );
	}

	/**
	 * @return bool
	 */
	public static function wps_cleaner_delete_revisions() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_revisions' ) ) {
			return false;
		}

		$do_action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $do_action ) || $do_action !== 'clear_revisions' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_revisions' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'revisions' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_auto_drafts() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_auto_drafts' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_auto_drafts' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_auto_draft' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'auto_drafts' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_deleted_posts() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_deleted_posts' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_deleted_posts' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_deleted_posts' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'deleted_posts' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_orphaned_post_meta() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_orphaned_post_meta' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_orphaned_post_meta' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_orphaned_post_meta' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'orphan_postmeta' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_duplicated_postmeta() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_duplicated_postmeta' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_duplicated_postmeta' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_duplicated_postmeta' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'duplicated_postmeta' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_oembed_caches() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_oembed_caches' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_oembed_caches' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_oembed_caches' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'oembed_postmeta' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_unapproved_comments() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_unapproved_comments' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_unapproved_comments' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_unapproved_comments' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'unapproved_comments' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_spammed_comments() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_spammed_comments' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_spammed_comments' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_spammed_comments' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'spam_comments' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_deleted_comments() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_deleted_comments' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_deleted_comments' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_deleted_comments' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'deleted_comments' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_orphan_commentmeta() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_orphan_commentmeta' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_orphan_commentmeta' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_orphan_commentmeta' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'orphan_commentmeta' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_duplicated_commentmeta() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_duplicated_commentmeta' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_duplicated_commentmeta' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_duplicated_commentmeta' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'duplicated_commentmeta' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_orphan_usermeta() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_orphan_usermeta' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_orphan_usermeta' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_orphan_usermeta' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'orphan_usermeta' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_duplicated_usermeta() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_duplicated_usermeta' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_duplicated_usermeta' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_duplicated_usermeta' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'duplicated_usermeta' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_transient_options() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_transient_options' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_transient_options' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_transient_options' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'transient_options' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_schedule_options() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_schedule_options' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_schedule_options' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_schedule_options' );
		wp_die();
	}

	public static function wps_cleaner_delete_orphan_termmeta() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_orphan_termmeta' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_orphan_termmeta' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_orphan_termmeta' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'orphan_termmeta' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_unused_terms() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_unused_terms' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_unused_terms' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_unused_terms' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'unused_terms' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_duplicated_termmeta() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_duplicated_termmeta' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_duplicated_termmeta' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_duplicated_termmeta' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'duplicated_termmeta' ) );
		wp_die();
	}

	public static function wps_cleaner_delete_orphaned_term_relationship() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'clear_orphaned_term_relationship' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'clear_orphaned_term_relationship' ) {
			return false;
		}

		Helpers::doing_clean( 'delete_orphaned_term_relationship' );

		echo json_encode( (int) Helpers::count_cleanup_items( 'orphan_term_relationships' ) );
		wp_die();
	}

	/**
	 *
	 * Delete inactivate plugins
	 *
	 * @return bool
	 */
	public static function wps_cleaner_delete_unuse_plugins() {
		if ( ! current_user_can( 'delete_plugins' ) ) {
			return false;
		}

		if ( empty( $_POST ) ) {
			return false;
		}

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_plugins_unuse' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );

		if ( ! isset( $action ) || $action !== 'delete_plugins_unuse' ) {
			return false;
		}

		Helpers::delete_unuse_plugins();

		echo json_encode( 0 );
		wp_die();
	}

	/**
	 *
	 * Delete inactivate themes
	 *
	 * @return bool
	 */
	public static function wps_cleaner_delete_unuse_themes() {
		if ( ! current_user_can( 'delete_themes' ) ) {
			return false;
		}

		if ( empty( $_POST ) ) {
			return false;
		}

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_themes_unuse' ) ) {
			return false;
		}
		$action = esc_attr( $_POST['do_action'] );
		if ( ! isset( $action ) || $action !== 'delete_themes_unuse' ) {
			return false;
		}

		Helpers::delete_unuse_themes();

		echo json_encode( 0 );
		wp_die();
	}

	public static function wps_cleaner_delete_logs_redirection() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_logs_redirection' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );
		if ( ! isset( $action ) || $action !== 'delete_logs_redirection' ) {
			return false;
		}

		if ( ! class_exists( 'RE_Log' ) ) {
			return false;
		}

		\RE_Log::delete_all('', '');

		echo json_encode( 0 );
		wp_die();
	}

	public static function wps_cleaner_delete_404_logs_redirection() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_404_logs_redirection' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );
		if ( ! isset( $action ) || $action !== 'delete_404_logs_redirection' ) {
			return false;
		}

		if ( ! class_exists( 'RE_404' ) ) {
			return false;
		}

		\RE_404::delete_all('', '');

		echo json_encode( 0 );
		wp_die();
	}

	public static function wps_cleaner_delete_logs_zerospam() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_logs_zerospam' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );
		if ( ! isset( $action ) || $action !== 'delete_logs_zerospam' ) {
			return false;
		}

		if ( ! function_exists( 'zerospam_reset_log' ) ) {
			return false;
		}

		zerospam_reset_log();

		echo json_encode( 0 );
		wp_die();
	}

	public static function wps_cleaner_delete_woo_transients() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_woo_transients' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );
		if ( ! isset( $action ) || $action !== 'delete_woo_transients' ) {
			return false;
		}

		if ( ! class_exists( 'WC_Cache_Helper' ) ) {
			return false;
		}

		wc_delete_product_transients();
		wc_delete_shop_order_transients();
		\WC_Cache_Helper::get_transient_version( 'shipping', true );

		echo json_encode( 0 );
		wp_die();
	}

	public static function wps_cleaner_delete_orphaned_variations() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_orphaned_variations' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );
		if ( ! isset( $action ) || $action !== 'delete_orphaned_variations' ) {
			return false;
		}

		if ( ! function_exists( 'wc_delete_product_transients' ) ) {
			return false;
		}

		global $wpdb;

		$wpdb->query( "DELETE products
			FROM {$wpdb->posts} products
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = products.post_parent
			WHERE wp.ID IS NULL AND products.post_type = 'product_variation';" );

		echo json_encode( 0 );
		wp_die();
	}

	public static function wps_cleaner_delete_sessions_woo() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_sessions_woo' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );
		if ( ! isset( $action ) || $action !== 'delete_sessions_woo' ) {
			return false;
		}

		if ( ! function_exists( 'wc_delete_product_transients' ) ) {
			return false;
		}

		global $wpdb;
		$wpdb->query( "TRUNCATE {$wpdb->prefix}woocommerce_sessions" );
		wp_cache_flush();

		echo json_encode( 0 );
		wp_die();
	}

	public static function delete_old_files() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_old_files' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );
		if ( ! isset( $action ) || $action !== 'delete_old_files' ) {
			return false;
		}

		$bad_files = Helpers::get_old_files_wp();
		if ( empty( $bad_files ) ) {
			return false;
		}

		global $wp_filesystem;
		WP_Filesystem( true );
		foreach ( $bad_files as $file ) {
			$wp_filesystem->delete( ABSPATH . strip_tags( $file ), true );
		}

		wp_die();
	}

	public static function delete_dir_file() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_dir_file' ) ) {
			return false;
		}

		$action = esc_attr( $_POST['do_action'] );
		if ( ! isset( $action ) || $action !== 'delete_dir_file' ) {
			return false;
		}

		$dir_file = esc_attr( $_POST['dir_file'] );
		if ( ! isset( $dir_file ) ) {
			return false;
		}

		global $wp_filesystem;
		WP_Filesystem( true );

		$wp_filesystem->delete( ABSPATH . $dir_file, true );

		wp_die();
	}

	public static function admin_enqueue_scripts( $hook ) {
		if ( false === strpos( $hook, 'wps-cleaner' ) ) {
			return false;
		}

		wp_enqueue_style( 'wps-cleaner-fa', WPS_CLEANER_URL . 'assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css' );
		wp_enqueue_style( 'wps-cleaner-style', WPS_CLEANER_URL . 'assets/css/style.css', array(), WPS_CLEANER_VERSION );
		wp_enqueue_script( 'wps-cleaner-fa', WPS_CLEANER_URL . 'assets/fontawesome/fontawesome-all.min.js', array(), false, true );

		wp_enqueue_script( 'wps-cleaner-functions', WPS_CLEANER_URL . 'assets/js/functions.js', array(
			'jquery'
		), false, true );

		wp_localize_script( 'wps-cleaner-functions', 'wps_cleaner_i18n', array(
			'doing'           => __( 'Cleaning in progress ..', 'wps-cleaner' ),
			'delete'          => __( 'Removing ..', 'wps-cleaner' ),
			'downloading'     => __( 'Downloading ..', 'wps-cleaner' ),
			'finish_analysis' => __( 'Analysis completed', 'wps-cleaner' ),
            'view_list' => __( 'See the list', 'wps-cleaner' ),
            'hide_list' => __( 'Hide the list', 'wps-cleaner' )
		) );

		wp_enqueue_style( 'plugin-install' );

		wp_enqueue_script( 'plugin-install' );
		wp_enqueue_script( 'updates' );
		add_thickbox();
	}

	public static function create_zip_archive_medias() {

	    check_ajax_referer( 'download-zip' );

		$files = esc_attr( $_POST['files'] );

		if ( empty( $files ) ) {
			return false;
		}

		$upload_dir = wp_upload_dir();
		$path       = $upload_dir['basedir'] . '/';
		$filename   = esc_attr( $_POST['filename'] );

		$destination = $path . $filename;

		if ( empty( $files ) ) {
			return false;
		}

		$zip = new \ZipArchive();
		$res = $zip->open( $destination, \ZipArchive::CREATE );

		if ( $res !== true ) {
			return false;
		}

		//add the files
		$files      = explode( ',', $files );
		$limitItems = 0;
		foreach ( $files as $media_id ) {

		    $file = get_attached_file( $media_id );

			if ( $zip->addFile( $file, basename( $file ) ) ) {
				$limitItems ++;
			}

			if ( $limitItems > 200 ) {
				$limitItems = 0;
				@set_time_limit( 0 );
			}
		}

		$zip->close();

		echo json_encode( array( 'zip' => $upload_dir['baseurl'] . '/' . $filename, 'nonce' => wp_create_nonce( $upload_dir['baseurl'] . '/' . $filename ) ) );
		wp_die();
	}

	public static function delete_zip_archive_medias() {
		$zip = esc_attr( $_POST['zip'] );

		check_ajax_referer( $zip );

		if ( empty( $zip ) ) {
			return false;
		}

		$upload_dir = wp_upload_dir();
		$zip        = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $zip );

		unlink( $zip );
		wp_die();
	}

	public static function delete_medias_whitelist() {
	    check_ajax_referer( 'delete-whitelist' );

	    DB::delete_all_object_id(0);
		wp_die();
    }

	/**
	 *
	 * Create zip with files not native WordPress
	 *
	 * @return bool
	 */
	public static function create_zip_archive_files() {
	    check_ajax_referer( 'create-zip' );

		$ids = esc_attr( $_POST['ids'] );

		if ( empty( $ids ) ) {
			return false;
		}

		$upload_dir = wp_upload_dir();
		$path       = $upload_dir['basedir'] . '/';
		$filename   = esc_attr( $_POST['filename'] );

		$destination = $path . $filename;

		//create the archive
		$zip = new \ZipArchive();
		$res = $zip->open( $destination, \ZipArchive::CREATE );
		if ( $res !== true ) {
			return false;
		}

		$files = explode( ',', $ids );

		$dirs       = array();
		$limitItems = 0;
		foreach ( $files as $file ) {
			$file = ABSPATH . $file;
			if ( ! is_dir( $file ) ) {
				if ( $zip->addFile( $file, basename( $file ) ) ) {
					$limitItems ++;
				}

				if ( $limitItems > 200 ) {
					$limitItems = 0;
					@set_time_limit( 0 );
				}

			} else {
				$dirs[] = basename( $file ) . '/';
			}
		}

		while ( count( $dirs ) ) {
			$dir = current( $dirs );
			if ( $zip->addEmptyDir( $dir ) ) {
				$dh = opendir( ABSPATH . $dir );
				while ( $file = readdir( $dh ) ) {
					if ( $file != '.' && $file != '..' ) {
						if ( is_file( ABSPATH . $dir . $file ) ) {

							if ( $zip->addFile( ABSPATH . $dir . $file, $dir . $file ) ) {
								$limitItems ++;
							}

							if ( $limitItems > 200 ) {
								$limitItems = 0;
								@set_time_limit( 0 );
							}

						} else {
							$dirs[] = $dir . $file . "/";
						}
					}
				}
				closedir( $dh );
				array_shift( $dirs );
			}
		}

		$zip->close();

		echo json_encode( array( 'zip' => $upload_dir['baseurl'] . '/' . $filename, 'nonce' => wp_create_nonce( $upload_dir['baseurl'] . '/' . $filename ) ) );
		wp_die();
	}

	/**
	 *
	 * Delete zip archive files after created
	 *
	 * @return bool
	 */
	public static function delete_zip_archive_files() {
		$zip = esc_attr( $_POST['zip'] );

		check_ajax_referer( $zip );

		if ( empty( $zip ) ) {
			return false;
		}

		$upload_dir = wp_upload_dir();
		$zip        = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $zip );

		unlink( $zip );
		wp_die();
	}

	public static function admin_footer() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$current_screen = get_current_screen();

		if ( false === strpos( $current_screen->base, 'wps-cleaner' ) ) {
			return false;
		}

		echo "<script>
            jQuery( 'a.wc-rating-link' ).click( function() {
                jQuery.post( '" . admin_url( 'admin-ajax.php', 'relative' ) . "', { action: 'wpscleaner_rated', _ajax_nonce: jQuery( this ).data('nonce') } );
                jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
            });</script>";
	}

	/**
	 *
	 * Add review
	 *
	 * @param $footer_text
	 *
	 * @return string
	 */
	public static function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $footer_text;
		}

		$current_screen = get_current_screen();

		if ( false === strpos( $current_screen->base, 'wps-cleaner' ) ) {
			return $footer_text;
		}

		if ( ! get_option( 'wpscleaner_admin_footer_text_rated' ) ) {
			$footer_text = sprintf(
				__( 'If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', 'wps-cleaner' ),
				sprintf( '<strong>%s</strong>', esc_html__( 'WPS Cleaner', 'wps-cleaner' ) ),
				'<a href="https://wordpress.org/support/plugin/wps-cleaner/reviews?rate=5#new-post" target="_blank" class="wc-rating-link" data-nonce="' . wp_create_nonce( 'wpscleanerrated' ) . '" data-rated="' . esc_attr__( 'Thanks :)', 'wps-cleaner' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $footer_text;
	}

	/**
	 * Triggered when clicking the rating footer.
	 */
	public static function wpscleaner_rated() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		check_ajax_referer( 'wpscleanerrated' );

		update_option( 'wpscleaner_admin_footer_text_rated', 1 );
		wp_die();
	}

	/**
	 *
	 * Add link tools in plugin page
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public static function plugin_action_links( $links ) {

		array_unshift( $links, '<a href="' . admin_url( 'tools.php?page=wps-cleaner' ) . '">' . __( 'Tools' ) . '</a>' );

		return $links;
	}

	/**
	 *
	 * Check if is plugin installed
	 *
	 * @param $plugin
	 *
	 * @return bool
	 */
	public static function is_plugin_installed( $plugin ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $plugin ] );
	}

	/**
	 *
	 * Return PF current user
	 *
	 * @return null|string|string[]
	 */
	public static function wps_ip_check_return_pf() {
		$pf        = '';
		$host_name = gethostname();
		if ( strpos( $host_name, 'wps' ) !== false ) {

			if ( false !== strpos( $host_name, 'wpserveur' ) ) {
				$pf = 'pf1';

				return $pf;
			}

			$pf = preg_replace( "/[^0-9]/", '', $host_name );
			$pf = 'pf' . $pf;
		}

		return $pf;
	}

	/**
	 *
	 * Not display pub in plugin
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public static function wps_bidouille_not_display_pub_array( $array ) {
		$array[] = 'tools_page_wps-cleaner';

		return $array;
	}

	/**
	 *
	 * Add file .htaccess in list files delete
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public static function wps_cleaner_list_files_delete( $array ) {
	    global $is_nginx;

		if ( $is_nginx ) {
			$array[] = '.htaccess';
		}

		return $array;
	}

	/**
	 * Add 'term_language' and 'term_translations' to excluded taxonomies otherwise terms loose their language and translation group
	 *
	 * @param $excluded_taxonomies
	 *
	 * @return array
	 */
	public static function wps_cleaner_excluded_taxonomies( $excluded_taxonomies ) {
		return array_merge( $excluded_taxonomies, array( 'term_language', 'term_translations' ) );
	}

	/**
	 * Clean alert
	 */
	public static function delete_alert() {
	    check_ajax_referer( 'delete-alert' );

		delete_option( 'wps_cleaner_alert' );
		wp_die();
	}

	public static function check_wps_cleaner_queue() {
		global $wpdb;
		$did_index = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->wps_cleaner_queue WHERE blog_id = %d", get_current_blog_id() ) );

		$result = ( ! empty( $did_index ) ) ? true : false;

		echo json_encode( $result );
		wp_die();
	}

	/**
	 * Add tools in toolbox WordPress
	 */
	public static function tool_box() { ?>
        <div class="card">
            <h2 class="title"><?php _e( 'WPS Cleaner', 'wps-cleaner' ) ?></h2>
            <p><?php printf( __( 'If you want to completely clean your WordPress site use <a href="%s">WPS Cleaner</a>.' ), admin_url( 'tools.php?page=wps-cleaner' ) ); ?></p>
        </div>
		<?php
	}

}