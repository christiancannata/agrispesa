<?php

namespace WPS\WPS_Cleaner;

class DB {

	use Singleton;

	protected function init() {
		add_action( 'delete_blog', [ $this, 'delete_blog' ] );
	}

	/**
	 * On blog deletion, Manage to delete all data from the blog
	 *
	 * @param int $blog_id
	 */
	public function delete_blog( $blog_id = 0 ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return;
		}

		$db_table->db->delete( $db_table->get_table_name(), [ 'blog_id' => $blog_id ], [ '%d' ] );
	}

	/**
	 * Manage to insert into db the given media ids for indexation
	 *
	 * @param $media_ids
	 * @param $object_id
	 */
	public static function insert( $media_ids, $object_id ) {
		if ( empty( $media_ids ) ) {
			return;
		}

		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return;
		}

		$blog_id = get_current_blog_id();
		foreach ( $media_ids as $media_id => $types ) {

			// supprime le media de la whitelist si present dans un contenu
			$db_table->db->delete( $db_table->get_table_name(), [
				'blog_id'     => $blog_id,
				'type'        => 'whitelist',
				'media_id'    => $media_id,
				'object_id'   => '0',
			], [ '%d', '%s', '%d', '%s' ] );

			foreach ( $types as $type ) {
				$db_table->db->insert( $db_table->get_table_name(), [
					'blog_id'     => $blog_id,
					'type'        => $type,
					'media_id'    => $media_id,
					'object_id'   => $object_id,
				], [ '%d', '%s', '%d', '%s' ] );
			}
		}
	}

	/**
	 * Manage to delete all data against an object id
	 *
	 * @param $object_id
	 */
	public static function delete_all_object_id( $object_id ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return;
		}

		$db_table->db->delete( $db_table->get_table_name(), [
			'blog_id'     => get_current_blog_id(),
			'object_id'   => $object_id,
		], [ '%d', '%s' ] );
	}

	/**
	 * Manage to delete all data against a media id
	 *
	 * @param int $media_id
	 */
	public static function delete_all_media_id( $media_id ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return;
		}

		$db_table->db->delete( $db_table->get_table_name(), [
			'blog_id'  => get_current_blog_id(),
			'media_id' => $media_id,
		], [ '%d', '%d' ] );
	}

	/**
	 * Get the counter for a given media id
	 *
	 * @param int $media_id
	 *
	 * @return int
	 */
	public static function get_counter( $media_id ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return 0;
		}

		$counter = (int) $db_table->db->get_var( $db_table->db->prepare( "SELECT count(id) FROM " . $db_table->get_table_name() . " WHERE blog_id = %d AND media_id = %d", get_current_blog_id(), $media_id ) );

		return $counter;
	}


	/**
	 * Get the ids
	 */
	public static function get_ids() {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return false;
		}

		global $wpdb;
		$sql_attachments = "SELECT ID FROM {$wpdb->posts} posts WHERE posts.post_type = 'attachment' AND ((posts.post_status = 'inherit'))";
		$results_all       = $wpdb->get_results( $sql_attachments );
		$results_all = wp_list_pluck( $results_all, 'ID' );

		$results  = $wpdb->get_results( $wpdb->prepare( "SELECT distinct(media_id) FROM " . $db_table->get_table_name() . " WHERE blog_id = %d", get_current_blog_id() ) );
		$results = wp_list_pluck( $results, 'media_id');

		return array_diff( $results_all, $results );
	}

	/**
	 * Get all indexed data against a media
	 *
	 * @param int $media_id
	 *
	 * @return array
	 */
	public static function get_data( $media_id ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return [];
		}

		$data = $db_table->db->get_results( $db_table->db->prepare( "SELECT * FROM " . $db_table->get_table_name() . " WHERE blog_id = %d AND media_id = %d", get_current_blog_id(), $media_id ) );

		/**
		 * Filter saved indexed data against the given media
		 *
		 * @param array $data     The indexed data, reordoned.
		 * @param int   $media_id Media ID looking for.
		 */
		return apply_filters( 'wps_cleaner_db_get_data', $data, $media_id );
	}

	/**
	 * Check if a data exists into db
	 *
	 * @param string $type
	 * @param int    $media_id
	 *
	 * @return bool
	 */
	public static function exists( $type, $media_id, $object_id ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return false;
		}

		// Check if raw exists for insert
		$column_exists = $db_table->db->get_var( $db_table->db->prepare( "SELECT count(id) FROM " . $db_table->get_table_name() . " WHERE blog_id = %d AND type = %s AND media_id = %d AND object_id = %s", get_current_blog_id(), $type, $media_id, $object_id ) );

		return ! empty( $column_exists );
	}
}