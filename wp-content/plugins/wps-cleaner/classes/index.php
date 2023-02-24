<?php

namespace WPS\WPS_Cleaner;

class Index {

	use Singleton;

	/**
	 * All kind of field that involve object
	 *
	 * @var array
	 */
	private $_acf_object_fields = array();

	/**
	 * All kind of field that involve textual fields
	 *
	 * @var array
	 */
	private $_acf_textual_fields = array();

	/**
	 * Retrieved medias from ACF fields
	 *
	 * @var array
	 */
	private $_found_medias = array();

	public function __construct() {
		add_filter( 'wps_cleaner_post_index', array( __CLASS__, 'add_media_from_post_content' ), 10, 2 );
		add_filter( 'wps_cleaner_post_index', array( __CLASS__, 'add_media_from_post_thumbnail' ), 10, 2 );
		add_filter( 'wps_cleaner_post_index', array( __CLASS__, 'add_media_product_image_gallery' ), 10, 2 );
		add_filter( 'wps_cleaner_post_index', array( __CLASS__, 'add_media_product_variations' ), 10, 2 );
		add_filter( 'wps_cleaner_post_index', array( &$this, 'add_media_from_post_acf_fields' ), 10, 2 );

		// Indexation for post content
		add_filter( 'wps_cleaner_get_media_post_content', array( __CLASS__, 'get_media_from_text' ), 10, 2 );
		add_filter( 'wps_cleaner_get_media_post_content', array( __CLASS__, 'get_media_from_links' ), 10, 2 );
		add_filter( 'wps_cleaner_get_media_post_content', array(
			__CLASS__,
			'get_media_from_shortcode_gallery'
		), 10, 2 );

		// Indexation for post content Module DIVI
		add_filter( 'wps_cleaner_get_media_post_content', array(
			__CLASS__,
			'get_media_from_shortcode_galerie_divi'
		), 10, 2 );
		add_filter( 'wps_cleaner_get_media_post_content', array( __CLASS__, 'get_media_from_shortcode_divi' ), 10, 2 );

		// Indexation for post content Elementor
		add_filter( 'wps_cleaner_get_media_post_content', array( __CLASS__, 'get_media_from_elementor' ), 10, 3 );

		// Indexation for post content Beaver Builder
		add_filter( 'wps_cleaner_get_media_post_content', array( __CLASS__, 'get_media_from_beaver' ), 10, 3 );

		// Indexation for post content Visual Composer
		add_filter( 'wps_cleaner_get_media_post_content', array( __CLASS__, 'get_media_from_visual_composer' ), 10, 3 );
	}

	/**
	 * Parse the given post's content to get used images' ids
	 *
	 * @param array $media_ids
	 * @param int $post_id
	 *
	 * @return array
	 */
	public static function add_media_from_post_content( $media_ids, $post_id ) {
		$post = get_post( $post_id );

		if ( empty ( $post ) ) {
			return $media_ids;
		}

		$post_content = $post->post_content;
		if ( empty( $post_content ) ) {
			return $media_ids;
		}

		/**
		 * From post content, get image ids
		 *
		 * @param array $found_medias Array of found images id.
		 * @param string $post_content Post content.
		 */
		$found_medias = apply_filters( 'wps_cleaner_get_media_post_content', array(), $post_content, $post_id );
		if ( empty( $found_medias ) ) {
			return $media_ids;
		}

		return Helpers::merge( $media_ids, $found_medias, 'post_content' );
	}

	/**
	 * Get post's thumbnail id
	 *
	 * @param array $media_ids
	 * @param int $post_id
	 *
	 * @return array
	 */
	public static function add_media_from_post_thumbnail( $media_ids, $post_id ) {
		$thumb_id = get_post_thumbnail_id( $post_id ) ?: 0;
		if ( empty( $thumb_id ) ) {
			return $media_ids;
		}

		return Helpers::merge( $media_ids, [ $thumb_id ], 'post_thumbnail' );
	}

	/**
	 *
	 * Get product image gallery Woocommerce
	 *
	 * @param $media_ids
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public static function add_media_product_image_gallery( $media_ids, $post_id ) {
		$thumbs = get_post_meta( $post_id, '_product_image_gallery', true );

		if ( empty( $thumbs ) ) {
			return $media_ids;
		}

		$thumbs_array = explode( ',', $thumbs );

		return Helpers::merge( $media_ids, $thumbs_array, 'post_thumbnail' );
	}

	/**
	 *
	 * Get product image gallery Woocommerce
	 *
	 * @param $media_ids
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public static function add_media_product_variations( $media_ids, $post_id ) {

		if ( ! class_exists( 'WC_Product' ) ) {
			return $media_ids;
		}

		$variations = get_posts( array( 'post_parent' => $post_id, 'post_type' => 'product_variation' ) );

		if ( empty( $variations ) ) {
			return $media_ids;
		}

		$thumbs = array();
		foreach ( $variations as $variation ) {
			$thumbs[] = get_post_meta( $variation->ID, '_thumbnail_id', true );
		}

		if ( empty( $thumbs ) ) {
			return $media_ids;
		}

		return Helpers::merge( $media_ids, $thumbs, 'post_thumbnail' );
	}

	/**
	 * Get post's acf fields
	 *
	 * @param array $media_ids
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function add_media_from_post_acf_fields( $media_ids, $post_id ) {
		return Helpers::merge( $media_ids, $this->get_media_from_acf_fields( $post_id ), 'acf' );
	}

	/**
	 * Get media ids from text
	 *
	 * @param array $media_ids
	 * @param string $post_content
	 *
	 * @return array
	 */
	public static function get_media_from_text( $media_ids, $post_content ) {
		return array_merge( $media_ids, Helpers::get_media_from_text( $post_content ) );
	}

	/**
	 * Get media ids from beaver builder
	 *
	 * @param array $media_ids
	 * @param string $post_content
	 *
	 * @return array
	 */
	public static function get_media_from_beaver( $media_ids, $post_content, $post_id ) {
		return array_merge( $media_ids, Helpers::get_media_from_beaver( $post_content, $post_id ) );
	}

	/**
	 * Get media ids from visual composer
	 *
	 * @param array $media_ids
	 * @param string $post_content
	 *
	 * @return array
	 */
	public static function get_media_from_visual_composer( $media_ids, $post_content, $post_id ) {
		return array_merge( $media_ids, Helpers::get_media_from_visual_composer( $post_content, $post_id ) );
	}

	/**
	 * Get media ids from links
	 *
	 * @param array $media_ids
	 * @param string $post_content
	 *
	 * @return array
	 */
	public static function get_media_from_links( $media_ids, $post_content ) {
		return array_merge( $media_ids, Helpers::get_media_from_links( $post_content ) );
	}

	/**
	 * Get media ids from links
	 *
	 * @param array $media_ids
	 * @param string $post_content
	 *
	 * @return array
	 */
	public static function get_media_from_shortcode_gallery( $media_ids, $post_content ) {
		return array_merge( $media_ids, Helpers::get_media_from_shortcode_gallery( $post_content ) );
	}

	/**
	 * Parse post's ACF fields to get media ids
	 *
	 * @param int $post_id
	 *
	 * @return array Media ids
	 */
	public function get_media_from_acf_fields( $post_id ) {
		// ACF PRO is installed and enabled ?
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return array();
		}

		$new_post = get_post( $post_id );
		if ( false === $new_post || is_wp_error( $new_post ) ) {
			return array();
		}

		// Get only fields with medias
		$this->_acf_object_fields  = array();
		$this->_acf_textual_fields = array();
		$this->_found_medias       = array();

		// Get media possible fields
		$this->recursive_get_post_media_fields( get_field_objects( $post_id ) );

		// Use media fields to get media ids
		$this->recursive_get_post_medias( get_fields( $post_id, false ) );

		// Keep only valid ID && remove zero values
		return array_filter( array_map( 'intval', $this->_found_medias ) );
	}

	/**
	 * Recursive way to extract all possible fields for a post
	 *
	 * @param array $fields
	 */
	private function recursive_get_post_media_fields( $fields ) {
		if ( empty( $fields ) ) {
			return;
		}

		foreach ( (array) $fields as $key => $field ) {
			if ( in_array( $field['type'], array( 'flexible_content' ) ) ) {
				// Flexible is recursive structure with sub_fields into layouts
				foreach ( $field['layouts'] as $layout_field ) {
					$this->recursive_get_post_media_fields( $layout_field['sub_fields'] );
				}
			} elseif ( in_array( $field['type'], [ 'repeater', 'clone', 'group' ] ) ) {
				// Repeater, Clone and Group fields is a recursive structure with sub_fields
				$this->recursive_get_post_media_fields( $field['sub_fields'] );
			} elseif ( in_array( $field['type'], [
				'image',
				'gallery',
				'post_object',
				'relationship',
				'file',
				'page_link',
			] ) ) {
				// All type of ACF Fields which involve media as object
				$this->_acf_object_fields[ $field['key'] ] = $field['name'];
			} elseif ( in_array( $field['type'], [
				'wysiwyg',
				'textarea',
			] ) ) {
				// All type of ACF Fields which are textual
				$this->_acf_textual_fields[ $field['key'] ] = $field['name'];
			}
		}
	}

	/**
	 * From media fields, get media ids
	 *
	 * @since  2.0.4
	 *
	 * @author Maxime CULEA
	 *
	 * @param array $fields
	 */
	private function recursive_get_post_medias( $fields ) {
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $key => $field ) {
				if ( is_array( $field ) ) {
					// If not final key => field, recursively relaunch
					$this->recursive_get_post_medias( $field );
				}

				if ( empty( $field ) || is_array( $field ) ) {
					// Go to next one if empty, array (already recursively relaunched) and the key is not a media field
					continue;
				}

				// Save the media ID
				if ( in_array( $key, $this->_acf_object_fields ) ) {
					$this->_found_medias = array_merge( $this->_found_medias, (array) $field );
				} elseif ( in_array( $key, $this->_acf_textual_fields ) ) {
					$this->_found_medias = array_merge( $this->_found_medias, Helpers::get_media_from_text( $field ) );
				}
			}
		}
	}

	public static function get_media_from_shortcode_galerie_divi( $media_ids, $post_content ) {
		return array_merge( $media_ids, Helpers::get_media_from_shortcode_galerie_divi( $post_content ) );
	}

	public static function get_media_from_shortcode_divi( $media_ids, $post_content ) {
		return array_merge( $media_ids, Helpers::get_media_from_shortcode_divi( $post_content ) );
	}

	public static function get_media_from_elementor( $media_ids, $post_content, $post_id ) {
		return array_merge( $media_ids, Helpers::get_media_from_elementor( $post_id ) );
	}
}