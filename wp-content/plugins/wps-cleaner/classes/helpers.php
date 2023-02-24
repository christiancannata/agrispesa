<?php

namespace WPS\WPS_Cleaner;

class Helpers {

	use Singleton;

	/**
	 * Get excluded taxonomies
	 *
	 * @access private
	 * @return array Excluded taxonomies
	 */
	public static function get_excluded_taxonomies() {
		$excluded_taxonomies   = array();
		$excluded_taxonomies[] = 'link_category';
		$excluded_taxonomies[] = 'term_language';
		$excluded_taxonomies[] = 'term_translations';

		return apply_filters( 'wps_cleaner_excluded_taxonomies', $excluded_taxonomies );
	}

	/**
	 * Get excluded term IDs
	 *
	 * @access private
	 * @return array Excluded term IDs
	 */
	public static function get_excluded_termids() {
		$default_term_ids = self::get_default_taxonomy_termids();
		if ( ! is_array( $default_term_ids ) ) {
			$default_term_ids = array();
		}
		$parent_term_ids = self::get_parent_termids();
		if ( ! is_array( $parent_term_ids ) ) {
			$parent_term_ids = array();
		}

		$excluded_term_ids = array_merge( $default_term_ids, $parent_term_ids );

		$_term_ids = array();
		if ( function_exists( 'pll_get_term_translations' ) ) {
			foreach ( $excluded_term_ids as $excluded_term_id ) {
				$_term_ids = array_merge( $_term_ids, array_values( pll_get_term_translations( $excluded_term_id ) ) );
			}

			$excluded_term_ids = array_merge( $excluded_term_ids, $_term_ids );

			// Add the terms of our languages.
			$excluded_term_ids = array_merge(
				$excluded_term_ids,
				pll_languages_list( array( 'fields' => 'term_id' ) ),
				pll_languages_list( array( 'fields' => 'tl_term_id' ) )
			);
		}

		return array_unique( $excluded_term_ids );
	}

	/**
	 * Get all default taxonomy term IDs
	 *
	 * @access private
	 * @return array Default taxonomy term IDs
	 */
	private static function get_default_taxonomy_termids() {
		$taxonomies       = get_taxonomies();
		$default_term_ids = array();
		if ( $taxonomies ) {
			$tax = array_keys( $taxonomies );
			if ( $tax ) {
				foreach ( $tax as $t ) {
					$term_id = intval( get_option( 'default_' . $t ) );
					if ( $term_id > 0 ) {
						$default_term_ids[] = $term_id;
					}
				}
			}
		}

		return $default_term_ids;
	}

	/**
	 * Get terms that has a parent term
	 *
	 * @access private
	 * @return array Parent term IDs
	 */
	private static function get_parent_termids() {
		global $wpdb;

		return $wpdb->get_col( $wpdb->prepare( "SELECT tt.parent FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.parent > %d", 0 ) );
	}

	/**
	 * Count the number of items concerned by the database cleanup
	 *
	 * @param string $name Item name to count.
	 *
	 * @return int Number of items for this type
	 */
	public static function count_cleanup_items( $name ) {
		global $wpdb;

		$count = 0;

		switch ( $name ) {
			case 'revisions':
				$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = %s", 'revision' ) );
				break;
			case 'auto_drafts':
				$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = %s", 'auto-draft' ) );
				break;
			case 'deleted_posts':
				$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = %s", 'trash' ) );
				break;
			case 'unapproved_comments':
				$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_approved = %s", '0' ) );
				break;
			case 'spam_comments':
				$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_approved = %s", 'spam' ) );
				break;
			case 'deleted_comments':
				$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE (comment_approved = %s OR comment_approved = %s)", 'trash', 'post-trashed' ) );
				break;
			case 'transient_options':
				$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(option_id) FROM $wpdb->options WHERE option_name LIKE(%s)", '%_transient_%' ) );
				break;
			case 'orphan_postmeta':
				$count = $wpdb->get_var( "SELECT COUNT(meta_id) FROM $wpdb->postmeta WHERE post_id NOT IN (SELECT ID FROM $wpdb->posts)" );
				break;
			case 'orphan_commentmeta':
				$count = $wpdb->get_var( "SELECT COUNT(meta_id) FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_ID FROM $wpdb->comments)" );
				break;
			case 'orphan_usermeta':
				$count = $wpdb->get_var( "SELECT COUNT(umeta_id) FROM $wpdb->usermeta WHERE user_id NOT IN (SELECT ID FROM $wpdb->users)" );
				break;
			case 'orphan_termmeta':
				$count = $wpdb->get_var( "SELECT COUNT(meta_id) FROM $wpdb->termmeta WHERE term_id NOT IN (SELECT term_id FROM $wpdb->terms)" );
				break;
			case 'orphan_term_relationships':
				$count = $wpdb->get_var( "SELECT COUNT(object_id) FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy NOT IN ('" . implode( '\',\'', self::get_excluded_taxonomies() ) . "') AND tr.object_id NOT IN (SELECT ID FROM $wpdb->posts)" );
				break;
			case 'unused_terms':
				$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(t.term_id) FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.count = %d AND t.term_id NOT IN (" . implode( ',', self::get_excluded_termids() ) . ')', 0 ) );
				break;
			case 'duplicated_postmeta':
				$query = $wpdb->get_col( $wpdb->prepare( "SELECT COUNT(meta_id) AS count FROM $wpdb->postmeta GROUP BY post_id, meta_key, meta_value HAVING count > %d", 1 ) );
				if ( is_array( $query ) ) {
					$count = array_sum( array_map( 'intval', $query ) );
				}
				break;
			case 'duplicated_commentmeta':
				$query = $wpdb->get_col( $wpdb->prepare( "SELECT COUNT(meta_id) AS count FROM $wpdb->commentmeta GROUP BY comment_id, meta_key, meta_value HAVING count > %d", 1 ) );
				if ( is_array( $query ) ) {
					$count = array_sum( array_map( 'intval', $query ) );
				}
				break;
			case 'duplicated_usermeta':
				$query = $wpdb->get_col( $wpdb->prepare( "SELECT COUNT(umeta_id) AS count FROM $wpdb->usermeta GROUP BY user_id, meta_key, meta_value HAVING count > %d", 1 ) );
				if ( is_array( $query ) ) {
					$count = array_sum( array_map( 'intval', $query ) );
				}
				break;
			case 'duplicated_termmeta':
				$query = $wpdb->get_col( $wpdb->prepare( "SELECT COUNT(meta_id) AS count FROM $wpdb->termmeta GROUP BY term_id, meta_key, meta_value HAVING count > %d", 1 ) );
				if ( is_array( $query ) ) {
					$count = array_sum( array_map( 'intval', $query ) );
				}
				break;
			case 'optimize_database':
				$count = sizeof( $wpdb->get_col( 'SHOW TABLES' ) );
				break;
			case 'oembed_postmeta':
				$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(meta_id) FROM $wpdb->postmeta WHERE meta_key LIKE(%s)", '%_oembed_%' ) );
				break;
		}

		return $count;
	}

	public static function total_count( $name ) {
		global $wpdb;

		$count = 0;

		switch ( $name ) {
			case 'posts':
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts" );
				break;
			case 'postmeta':
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->postmeta" );
				break;
			case 'comments':
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments" );
				break;
			case 'commentmeta':
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->commentmeta" );
				break;
			case 'users':
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
				break;
			case 'usermeta':
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->usermeta" );
				break;
			case 'term_relationships':
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->term_relationships" );
				break;
			case 'term_taxonomy':
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->term_taxonomy" );
				break;
			case 'terms':
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->terms" );
				break;
			case 'termmeta':
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->termmeta" );
				break;
			case 'options':
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->options" );
				break;
		}

		return (int) $count;
	}

	/**
	 * Delete plugins unuse
	 */
	public static function delete_unuse_plugins() {
		if ( is_multisite() && ! current_user_can( 'manage_network_plugins' ) ) {
			return false;
		}

		$plugins = get_plugins();

		if ( empty( $plugins ) ) {
			return false;
		}

		$plugins_delete = array();
		foreach ( $plugins as $path_plugin => $plugin ) {
			if ( ! is_plugin_inactive( $path_plugin ) || is_plugin_active_for_network( $path_plugin ) ) {
				continue;
			}

			$plugins_delete[] = $path_plugin;

		}

		if ( ! empty( $plugins_delete ) ) {
			delete_plugins( $plugins_delete );
		}
	}

	/**
	 * Delete themes unuse
	 */
	public static function delete_unuse_themes() {
		$themes = wp_prepare_themes_for_js();

		if ( is_multisite() && ! current_user_can( 'manage_network_themes' ) ) {
			return false;
		}

		foreach ( $themes as $theme ) {

			if ( $theme['active'] === true ) {
				continue;
			}

			if ( ! isset( $theme['actions']['delete'] ) ) {
				continue;
			}

			delete_theme( $theme['id'] );

		}
	}

	public static function folderSize( $dir ) {
		$size = 0;
		foreach ( glob( rtrim( $dir, '/' ) . '/*', GLOB_NOSORT ) as $each ) {
			$size += is_file( $each ) ? filesize( $each ) : self::folderSize( $each );
		}

		return $size;
	}

	public static function humanFileSize( $size, $unit = "" ) {
		if ( ( ! $unit && $size >= 1 << 30 ) || $unit == "GB" ) {
			return number_format( $size / ( 1 << 30 ), 2 ) . " GB";
		}
		if ( ( ! $unit && $size >= 1 << 20 ) || $unit == "MB" ) {
			return number_format( $size / ( 1 << 20 ), 2 ) . " MB";
		}

		return number_format( $size / ( 1 << 10 ), 2 ) . " KB";
	}

	public static function img_count() {
		if ( false === ( $count = get_transient( 'wps_cleaner_img_count' ) ) ) {
			global $wpdb;
			$sql   = "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' AND post_status = 'inherit'";
			$count = $wpdb->get_var( $sql );
			set_transient( 'wps_cleaner_img_count', $count, 48 * HOUR_IN_SECONDS );
		}

		return $count;
	}

	/**
	 * Check image validity in database
	 *
	 * @param int $image_id
	 *
	 * @return bool
	 */
	public static function check_image_id( $image_id ) {
		if ( 0 === (int) $image_id ) {
			return false;
		}

		$object = get_post( $image_id );
		if ( false == $object || is_wp_error( $object ) ) {
			return false;
		}

		if ( $object->post_type !== 'attachment' ) {
			return false;
		}

		return true;
	}

	/**
	 * Check given array of image for validation vs DB
	 *
	 * @param array $image_ids
	 *
	 * @return array
	 */
	public static function check_image_ids( $image_ids ) {
		return array_filter( $image_ids, array( __CLASS__, 'check_image_id' ), ARRAY_FILTER_USE_KEY );
	}

	/**
	 * From a text, get the inserted html image ids
	 *
	 * @param $text
	 *
	 * @return array
	 */
	static function get_media_from_text( $text ) {
		if ( empty( $text ) ) {
			return [];
		}

		// match all wp-image-{media_id} from img html classes
		preg_match_all( '/wp-image-(\d*)/', $text, $images );
		if ( empty( $images ) ) {
			return [];
		}

		return $images[1];
	}

	/**
	 * From a text, get the inserted html image ids
	 *
	 * @param $text
	 *
	 * @return array
	 */
	static function get_media_from_beaver( $text, $post_id ) {
		$img_ids = array();
		if ( empty( $text ) ) {
			return $img_ids;
		}

		$datas       = get_post_meta( $post_id, '_fl_builder_data', true );
		$datas_draft = get_post_meta( $post_id, '_fl_builder_draft', true );

		$img_ids = self::save_img_ids( $datas, $img_ids );
		$img_ids = self::save_img_ids( $datas_draft, $img_ids );

		preg_match_all( '@src="([^"]+)"@', $text, $match );

		if ( empty( $match ) ) {
			return [];
		}

		$src = array_pop( $match );

		global $wpdb;
		foreach ( $src as $url ) {
			// Check if retrieved media from href really exists for the current site
			$attachment_id = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid='%s';", $url ) );
			if ( empty( $attachment_id ) ) {
				continue;
			}
			$img_ids[] = (int) $attachment_id[0];

		}

		return $img_ids;
	}

	public static function get_img_ids_regex( $img_ids, $match ) {
		if ( empty( $match ) ) {
			return $img_ids;
		}

		foreach ( $match[1] as $gallery ) {
			$imgs = array_map( 'intval', explode( ',', $gallery ) );
			if ( is_array( $imgs ) ) {
				// Multiple images into shortcode
				foreach ( $imgs as $img ) {
					$img_ids[] = $img;
				}
			} else {
				// Only one image into shortcode
				$img_ids[] = $imgs;
			}
		}

		return $img_ids;
	}

	public static function get_media_from_visual_composer( $text, $post_id ) {
		$img_ids = array();
		if ( empty( $text ) ) {
			return $img_ids;
		}

		preg_match_all( '/\[vc_[a-z_]+ (?:interval="[0-9]" )?image[s]?="([0-9]+)"\]/', $text, $images );
		$img_ids = self::get_img_ids_regex( $img_ids, $images );

		preg_match_all( '~\bbackground(-image)?\s*:(.*?)\(\s*(\'|")?(?<image>.*?)\3?\s*\)~i', $text, $matches );
		if ( isset( $matches['image'] ) ) {
			$images = $matches['image'];
			if ( $images ) {
				foreach ( $images as $image_url ) {
					$image     = explode( '?', $image_url );
					$img_ids[] = self::get_image_id( $image[0] );
				}
			}
		}

		return $img_ids;
	}

	public static function save_img_ids( $datas, $img_ids ) {
		if ( empty( $datas ) ) {
			return $img_ids;
		}

		foreach ( $datas as $data ) {

			if ( ! is_object( $data ) ) {
				continue;
			}

			if ( isset( $data->settings->video_webm ) ) {
				$img_ids[] = (int) $data->settings->video_webm;
			}

			if ( isset( $data->settings->bg_image_src ) ) {
				$img_ids[] = self::get_image_id( $data->settings->bg_image_src );
			}

			if ( isset( $data->settings->bg_video_fallback_src ) ) {
				$img_ids[] = self::get_image_id( $data->settings->bg_video_fallback_src );
			}

			if ( isset( $data->settings->bg_parallax_image_src ) ) {
				$img_ids[] = self::get_image_id( $data->settings->bg_parallax_image_src );
			}

			if ( isset( $data->settings->poster_src ) ) {
				$img_ids[] = self::get_image_id( $data->settings->poster_src );
			}

			if ( isset( $data->settings->data->id ) ) {
				$img_ids[] = $data->settings->data->id;
			}

			if ( isset( $data->settings->photo_data ) ) {
				if ( is_array( $data->settings->photo_data ) ) {
					foreach ( $data->settings->photo_data as $id => $photo_data ) {
						$img_ids[] = $id;
					}
				} else {
					$img_ids[] = $data->settings->photo_data->id;
				}
			}

			if ( isset( $data->settings->slides ) ) {
				foreach ( $data->settings->slides as $slide ) {
					if ( isset( $slide->bg_photo_src ) ) {
						$img_ids[] = self::get_image_id( $slide->bg_photo_src );
					}
					if ( isset( $slide->fg_photo_src ) ) {
						$img_ids[] = self::get_image_id( $slide->fg_photo_src );
					}
					if ( isset( $slide->bg_photo ) ) {
						$img_ids[] = (int) $slide->bg_photo;
					}
					if ( isset( $slide->bg_video ) ) {
						$img_ids[] = (int) $slide->bg_video;
					}
					if ( isset( $slide->fg_photo ) ) {
						$img_ids[] = (int) $slide->fg_photo;
					}
					if ( isset( $slide->fg_video ) ) {
						$img_ids[] = (int) $slide->fg_video;
					}
				}
			}
		}

		return $img_ids;
	}

	/**
	 * From links into a text, get the inserted html image ids
	 *
	 * @param $text
	 *
	 * @return array
	 */
	static function get_media_from_links( $text ) {
		$img_ids = [];
		if ( empty( $text ) ) {
			return $img_ids;
		}

		/**
		 * Match all href="" from content
		 * @see : https://regex101.com/r/63ILkx/1
		 */
		preg_match_all( '/href="([^"\\\']+)"/', $text, $urls );
		if ( empty( $urls ) ) {
			return $img_ids;
		}

		global $wpdb;
		foreach ( $urls[1] as $url ) {
			// Check if retrieved media from href really exists for the current site
			$attachment_id = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid='%s';", $url ) );
			if ( empty( $attachment_id ) ) {
				continue;
			}
			$img_ids[] = (int) $attachment_id[0];
		}

		return $img_ids;
	}

	/**
	 * From post content and especially gallery shortcode, get image ids
	 *
	 * @param $text
	 *
	 * @return array
	 */
	static function get_media_from_shortcode_gallery( $text ) {
		$img_ids = array();
		if ( empty( $text ) ) {
			return $img_ids;
		}

		/**
		 * Match all [gallery ids=""] from content
		 * @see : https://regex101.com/r/KkmqkL/1
		 */
		preg_match_all( '/\[gallery ids="(.*)"\]/', $text, $galleries );
		$img_ids = self::get_img_ids_regex( $img_ids, $galleries );

		return $img_ids;
	}

	/**
	 * From post content and especially DIVI galerie shortcode, get image ids
	 *
	 * @param $text
	 *
	 * @return array
	 */
	public static function get_media_from_shortcode_galerie_divi( $text ) {
		$img_ids = array();
		if ( empty( $text ) ) {
			return $img_ids;
		}

		preg_match_all( '/\[et_pb_gallery(.+?)?\](?:(.+?)?\[\/et_pb_gallery\])?/', $text, $images );

		if ( empty( $images ) ) {
			return $img_ids;
		}

		foreach ( $images[1] as $image ) {

			preg_match( '@gallery_ids="([^"]+)"@', $image, $match );

			if ( empty( $match ) ) {
				continue;
			}

			$images_id = array_pop( $match );

			$imgs = array_map( 'intval', explode( ',', $images_id ) );

			if ( is_array( $imgs ) ) {
				// Multiple images into shortcode
				foreach ( $imgs as $img ) {
					$img_ids[] = $img;
				}
			} else {
				// Only one image into shortcode
				$img_ids[] = $imgs;
			}
		}

		return $img_ids;
	}

	/**
	 * From post content and especially DIVI shortcode, get image ids
	 *
	 * @param $text
	 *
	 * @return array
	 */
	public static function get_media_from_shortcode_divi( $text ) {
		$img_ids = array();
		if ( empty( $text ) ) {
			return $img_ids;
		}

		preg_match_all( '/\[et_pb_(.+?)\]/', $text, $images );

		if ( empty( $images ) ) {
			return $img_ids;
		}

		foreach ( $images[1] as $image ) {

			preg_match( '@background_image="([^"]+)"@', $image, $match );

			if ( empty( $match ) ) {
				continue;
			}

			$image_url = array_pop( $match );

			$image_url = str_replace( 'https://', '', $image_url );
			$image_url = str_replace( 'http://', '', $image_url );

			global $wpdb;
			$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid LIKE '%s';", '%' . $image_url . '%' ) );

			if ( empty( $attachment ) ) {
				continue;
			}

			$img_ids[] = $attachment[0];
		}

		foreach ( $images[1] as $image ) {

			preg_match( '@image="([^"]+)"@', $image, $match );

			if ( empty( $match ) ) {
				continue;
			}

			$image_url = array_pop( $match );

			$image_url = str_replace( 'https://', '', $image_url );
			$image_url = str_replace( 'http://', '', $image_url );

			global $wpdb;
			$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid LIKE '%s';", '%' . $image_url . '%' ) );

			if ( empty( $attachment ) ) {
				continue;
			}

			$img_ids[] = $attachment[0];
		}

		foreach ( $images[1] as $image ) {

			preg_match( '@src="([^"]+)"@', $image, $match );

			if ( empty( $match ) ) {
				continue;
			}

			$image_url = array_pop( $match );

			$image_url = str_replace( 'https://', '', $image_url );
			$image_url = str_replace( 'http://', '', $image_url );

			global $wpdb;
			$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid LIKE '%s';", '%' . $image_url . '%' ) );

			if ( empty( $attachment ) ) {
				continue;
			}

			$img_ids[] = $attachment[0];
		}

		return $img_ids;
	}

	public static function search( $array, $key ) {
		$results = array();

		if ( is_array( $array ) ) {
			if ( isset( $array[ $key ] ) ) {
				$results[] = $array;
			}

			foreach ( $array as $subarray ) {
				$results = array_merge( $results, self::search( $subarray, $key ) );
			}
		}

		return $results;
	}

	public static function get_media_from_elementor( $post_id ) {
		$img_ids = array();
		$meta    = get_post_meta( $post_id, '_elementor_data', true );

		if ( is_string( $meta ) && ! empty( $meta ) ) {
			$meta = json_decode( $meta, true );
		}

		if ( empty( $meta ) ) {
			return $img_ids;
		}

		$elements = ( self::search( $meta, 'elements' ) );

		foreach ( $elements as $element ) {
			if ( isset( $element['settings']['image'] ) ) {
				$img_ids[] = $element['settings']['image']['id'];
			}

			if ( isset( $element['settings']['carousel'] ) ) {
				foreach ( $element['settings']['carousel'] as $carousel ) {
					$img_ids[] = $carousel['id'];
				}
			}

			if ( isset( $element['settings']['wp_gallery'] ) ) {
				foreach ( $element['settings']['wp_gallery'] as $wp_gallery ) {
					$img_ids[] = $wp_gallery['id'];
				}
			}

			if ( isset( $element['settings']['background_image'] ) ) {
				$img_ids[] = $element['settings']['background_image']['id'];
			}

			if ( isset( $element['settings']['background_overlay_image'] ) ) {
				$img_ids[] = $element['settings']['background_overlay_image']['id'];
			}

			if ( isset( $element['settings']['background_video_link'] ) ) {
				$img_ids[] = self::get_image_id( $element['settings']['background_video_link'] );
			}

			if ( isset( $element['settings']['background_video_fallback'] ) ) {
				$img_ids[] = $element['settings']['background_video_fallback']['id'];
			}

			if ( isset( $element['settings']['image_overlay'] ) ) {
				$img_ids[] = $element['settings']['image_overlay']['id'];
			}
		}

		return $img_ids;
	}

	/**
	 *
	 * Retrieves the attachment ID from the file URL
	 *
	 * @param $image_url
	 *
	 * @return mixed
	 */
	public static function get_image_id( $image_url ) {
		global $wpdb;
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );

		return (int) $attachment[0];
	}


	/**
	 * Merge new data with old ones in order to have the same array format :
	 * [ {id} => [ 'type_1', 'type_2', ... ], ... ]
	 *
	 * @param array $old
	 * @param array $new
	 * @param string $type
	 *
	 * @return mixed
	 */
	public static function merge( $old, $new, $type ) {
		if ( empty( $new ) ) {
			return $old;
		}

		foreach ( $new as $media_id ) {
			// TODO : check if media really exists in DB

			// Not already existing into the old array, then create the row with type
			if ( ! isset( $old[ $media_id ] ) ) {
				$old[ $media_id ] = [ $type ];
				continue;
			}

			// Current type already exists into old array for the given media id
			if ( in_array( $type, $old[ $media_id ] ) ) {
				continue;
			}

			// Finally add the current type for the media id
			$old[ $media_id ][] = $type;
		}

		return $old;
	}

	public static function get_old_files_wp() {
		global $_old_files;

		require_once( ABSPATH . 'wp-admin/includes/update-core.php' );

		$bad_files = array();

		if ( empty( $_old_files ) || ! is_array( $_old_files ) ) {
			return false;
		}

		foreach ( $_old_files as $file ) {
			if ( @file_exists( ABSPATH . $file ) ) {
				$bad_files[] = sprintf( '<code>%s</code>', $file );
			}
		}

		return $bad_files;
	}

	/**
	 * @param $items
	 * @param $max_count
	 */
	public static function slice_text( &$items, $max_count ) {
		$count = count( $items ) - $max_count;
		if ( $count > 0 ) {
			$items = array_slice( $items, 0, $max_count );
			array_push( $items, sprintf( _n( '%s other', '%s others', $count, 'wps-cleaner' ), number_format_i18n( $count ) ) );
		}
	}

	/**
	 * @param $name
	 */
	public static function doing_clean( $name ) {
		global $wpdb;

		switch ( $name ) {
			case 'delete_revisions':
				$query = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type = 'revision'" );
				if ( $query ) {
					foreach ( $query as $id ) {
						wp_delete_post_revision( intval( $id ) );
					}
				}
				break;
			case 'delete_auto_draft':
				$query = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_status = 'auto-draft'" );
				if ( $query ) {
					foreach ( $query as $id ) {
						wp_delete_post( intval( $id ), true );
					}
				}
				break;
			case 'delete_deleted_posts':
				$query = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_status = %s", 'trash' ) );
				if ( $query ) {
					foreach ( $query as $id ) {
						wp_delete_post( intval( $id ), true );
					}
				}
				break;
			case 'delete_orphaned_post_meta':
				$query = $wpdb->get_results( "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE post_id NOT IN (SELECT ID FROM $wpdb->posts)" );
				if ( $query ) {
					foreach ( $query as $meta ) {
						$post_id = intval( $meta->post_id );
						if ( 0 === $post_id ) {
							$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post_id, $meta->meta_key ) );
						} else {
							delete_post_meta( $post_id, $meta->meta_key );
						}
					}
				}
				break;
			case 'delete_duplicated_postmeta':
				$query = $wpdb->get_results( $wpdb->prepare( "SELECT GROUP_CONCAT(meta_id ORDER BY meta_id DESC) AS ids, post_id, COUNT(*) AS count FROM $wpdb->postmeta GROUP BY post_id, meta_key, meta_value HAVING count > %d", 1 ) );
				if ( $query ) {
					foreach ( $query as $meta ) {
						$ids = array_map( 'intval', explode( ',', $meta->ids ) );
						array_pop( $ids );
						$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_id IN (" . implode( ',', $ids ) . ') AND post_id = %d', intval( $meta->post_id ) ) );
					}
				}
				break;
			case 'delete_oembed_caches':
				$query = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_key LIKE(%s)", '%_oembed_%' ) );
				if ( $query ) {
					foreach ( $query as $meta ) {
						$post_id = intval( $meta->post_id );
						if ( 0 === $post_id ) {
							$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post_id, $meta->meta_key ) );
						} else {
							delete_post_meta( $post_id, $meta->meta_key );
						}
					}
				}
				break;
			case 'delete_unapproved_comments':
				$query = $wpdb->get_col( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = %s", '0' ) );
				if ( $query ) {
					foreach ( $query as $id ) {
						wp_delete_comment( intval( $id ), true );
					}
				}
				break;
			case 'delete_spammed_comments':
				$query = $wpdb->get_col( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = %s", 'spam' ) );
				if ( $query ) {
					foreach ( $query as $id ) {
						wp_delete_comment( intval( $id ), true );
					}
				}
				break;
			case 'delete_deleted_comments':
				$query = $wpdb->get_col( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE (comment_approved = %s OR comment_approved = %s)", 'trash', 'post-trashed' ) );
				if ( $query ) {
					foreach ( $query as $id ) {
						wp_delete_comment( intval( $id ), true );
					}
				}
				break;
			case 'delete_orphan_commentmeta':
				$query = $wpdb->get_results( "SELECT comment_id, meta_key FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_ID FROM $wpdb->comments)" );
				if ( $query ) {
					foreach ( $query as $meta ) {
						$comment_id = intval( $meta->comment_id );
						if ( 0 === $comment_id ) {
							$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->commentmeta WHERE comment_id = %d AND meta_key = %s", $comment_id, $meta->meta_key ) );
						} else {
							delete_comment_meta( $comment_id, $meta->meta_key );
						}
					}
				}
				break;
			case 'delete_duplicated_commentmeta':
				$query = $wpdb->get_results( $wpdb->prepare( "SELECT GROUP_CONCAT(meta_id ORDER BY meta_id DESC) AS ids, comment_id, COUNT(*) AS count FROM $wpdb->commentmeta GROUP BY comment_id, meta_key, meta_value HAVING count > %d", 1 ) );
				if ( $query ) {
					foreach ( $query as $meta ) {
						$ids = array_map( 'intval', explode( ',', $meta->ids ) );
						array_pop( $ids );
						$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->commentmeta WHERE meta_id IN (" . implode( ',', $ids ) . ') AND comment_id = %d', intval( $meta->comment_id ) ) );
					}
				}
				break;
			case 'delete_orphan_usermeta':
				$query = $wpdb->get_results( "SELECT user_id, meta_key FROM $wpdb->usermeta WHERE user_id NOT IN (SELECT ID FROM $wpdb->users)" );
				if ( $query ) {
					foreach ( $query as $meta ) {
						$user_id = intval( $meta->user_id );
						if ( 0 === $user_id ) {
							$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $meta->meta_key ) );
						} else {
							delete_user_meta( $user_id, $meta->meta_key );
						}
					}
				}
				break;
			case 'delete_duplicated_usermeta':
				$query = $wpdb->get_results( $wpdb->prepare( "SELECT GROUP_CONCAT(umeta_id ORDER BY umeta_id DESC) AS ids, user_id, COUNT(*) AS count FROM $wpdb->usermeta GROUP BY user_id, meta_key, meta_value HAVING count > %d", 1 ) );
				if ( $query ) {
					foreach ( $query as $meta ) {
						$ids = array_map( 'intval', explode( ',', $meta->ids ) );
						array_pop( $ids );
						$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->usermeta WHERE umeta_id IN (" . implode( ',', $ids ) . ') AND user_id = %d', intval( $meta->user_id ) ) );
					}
				}
				break;
			case 'delete_transient_options':
				$query = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE(%s)", '%_transient_%' ) );
				if ( $query ) {
					foreach ( $query as $option_name ) {
						if ( strpos( $option_name, '_site_transient_' ) !== false ) {
							delete_site_transient( str_replace( '_site_transient_', '', $option_name ) );
						} else {
							delete_transient( str_replace( '_transient_', '', $option_name ) );
						}
					}
				}
				break;
			case 'delete_schedule_options':
				wp_clear_scheduled_hook( 'plugin_security_scanner_daily_event_hook' );
				$wpdb->query( "TRUNCATE {$wpdb->actionscheduler_logs}" );
				$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->actionscheduler_actions WHERE status IN ('canceled', 'failed', 'complete' )" ) );
				break;
			case 'delete_orphan_termmeta':
				$query = $wpdb->get_results( "SELECT term_id, meta_key FROM $wpdb->termmeta WHERE term_id NOT IN (SELECT term_id FROM $wpdb->terms)" );
				if ( $query ) {
					foreach ( $query as $meta ) {
						$term_id = intval( $meta->term_id );
						if ( 0 === $term_id ) {
							$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->termmeta WHERE term_id = %d AND meta_key = %s", $term_id, $meta->meta_key ) );
						} else {
							delete_term_meta( $term_id, $meta->meta_key );
						}
					}
				}
				break;
			case 'delete_unused_terms':
				$query = $wpdb->get_results( $wpdb->prepare( "SELECT tt.term_taxonomy_id, t.term_id, tt.taxonomy FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.count = %d AND t.term_id NOT IN (" . implode( ',', self::get_excluded_termids() ) . ')', 0 ) );
				if ( $query ) {
					$check_wp_terms = false;
					foreach ( $query as $tax ) {
						if ( taxonomy_exists( $tax->taxonomy ) ) {
							wp_delete_term( intval( $tax->term_id ), $tax->taxonomy );
						} else {
							$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = %d", intval( $tax->term_taxonomy_id ) ) );
							$check_wp_terms = true;
						}
					}
					// We need this for invalid taxonomies
					if ( $check_wp_terms ) {
						$wpdb->get_results( "DELETE FROM $wpdb->terms WHERE term_id NOT IN (SELECT term_id FROM $wpdb->term_taxonomy)" );
					}
				}
				break;
			case 'delete_duplicated_termmeta':
				$query = $wpdb->get_results( $wpdb->prepare( "SELECT GROUP_CONCAT(meta_id ORDER BY meta_id DESC) AS ids, term_id, COUNT(*) AS count FROM $wpdb->termmeta GROUP BY term_id, meta_key, meta_value HAVING count > %d", 1 ) );
				if ( $query ) {
					foreach ( $query as $meta ) {
						$ids = array_map( 'intval', explode( ',', $meta->ids ) );
						array_pop( $ids );
						$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->termmeta WHERE meta_id IN (" . implode( ',', $ids ) . ') AND term_id = %d', intval( $meta->term_id ) ) );
					}
				}
				break;
			case 'delete_orphaned_term_relationship':
				$query = $wpdb->get_results( "SELECT tr.object_id, tr.term_taxonomy_id, tt.term_id, tt.taxonomy FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy NOT IN ('" . implode( '\',\'', self::get_excluded_taxonomies() ) . "') AND tr.object_id NOT IN (SELECT ID FROM $wpdb->posts)" );
				if ( $query ) {
					foreach ( $query as $tax ) {
						$wp_remove_object_terms = wp_remove_object_terms( intval( $tax->object_id ), intval( $tax->term_id ), $tax->taxonomy );
						if ( true !== $wp_remove_object_terms ) {
							$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->term_relationships WHERE object_id = %d AND term_taxonomy_id = %d", $tax->object_id, $tax->term_taxonomy_id ) );
						}
					}
				}
				break;
		}
	}

	public static function get_whitelist_medias() {
		$db_table = DB_Table::get_instance();
		$blog_id  = get_current_blog_id();

		global $wpdb;
		$results = $wpdb->get_results( "SELECT media_id FROM {$db_table->get_table_name()} WHERE type = 'whitelist' AND blog_id = $blog_id AND object_id = '0'", ARRAY_A );

		return $results;
	}
}