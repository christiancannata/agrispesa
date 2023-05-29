<?php
/**
 * WooFic
 *
 * @package   WooFic
 * @author    Christian Cannata <christian@christiancannata.com>
 * @copyright 2022 Christian Cannata
 * @license   GPL 2.0+
 * @link      https://christiancannata.com
 */

namespace WooFic\Rest;

use WooFic\Engine\Base;

/**
 * Example class for REST
 */
class Example extends Base {

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize() {
		parent::initialize();

		\add_action( 'rest_api_init', array( $this, 'add_custom_stuff' ) );
	}

	/**
	 * Examples
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_custom_stuff() {
		$this->add_custom_field();
		$this->add_custom_ruote();
	}

	/**
	 * Examples
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_custom_field() {
		\register_rest_field(
			'demo',
			W_TEXTDOMAIN . '_text',
			array(
				'get_callback'    => array( $this, 'get_text_field' ),
				'update_callback' => array( $this, 'update_text_field' ),
				'schema'          => array(
					'description' => \__( 'Text field demo of Post type', W_TEXTDOMAIN ),
					'type'        => 'string',
				),
			)
		);
	}

	/**
	 * Examples
	 *
	 * @since 1.0.0
     *
     *  Make an instance of this class somewhere, then
     *  call this method and test on the command line with
     * `curl http://example.com/wp-json/wp/v2/calc?first=1&second=2`
     * @return void
	 */
	public function add_custom_ruote() {
		// Only an example with 2 parameters
		\register_rest_route(
			'wp/v2',
			'calc',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'sum' ),
				'args'     => array(
					'first'  => array(
						'default'           => 0,
						'sanitize_callback' => 'absint',
					),
					'second' => array(
						'default'           => 0,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);
		\register_rest_route(
			'wp/v2',
			'demo/example',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'demo_example' ),
				'args'                => array(
					'nonce' => array(
						'required' => true,
					),
				),
			)
		);
	}

	/**
	 * Examples
	 *
	 * @since 1.0.0
	 * @param array $post_obj Post ID.
	 * @return string
	 */
	public function get_text_field( array $post_obj ) {
		$post_id = $post_obj['id'];

		return \strval( \get_post_meta( $post_id, W_TEXTDOMAIN . '_text', true ) );
	}

	/**
	 * Examples
	 *
	 * @since 1.0.0
	 * @param string   $value Value.
	 * @param \WP_Post $post  Post object.
	 * @param string   $key   Key.
	 * @return bool|\WP_Error
	 */
	public function update_text_field( string $value, \WP_Post $post, string $key ) {
		$post_id = \update_post_meta( $post->ID, $key, $value );

		if ( false === $post_id ) {
			return new \WP_Error(
				'rest_post_views_failed',
				\__( 'Failed to update post views.', W_TEXTDOMAIN ),
				array( 'status' => 500 )
			);
		}

		return true;
	}

	/**
	 * Examples
	 *
	 * @since 1.0.0
	 * @param \WP_REST_Request $request Values.
	 * @return array
	 */
	public function sum( \WP_REST_Request $request ) {
		if ( !isset( $request[ 'first' ], $request[ 'second' ] ) ) {
			return array( 'result' => 0 );
		}

		return array( 'result' => $request[ 'first' ] + $request[ 'second' ] );
	}

	/**
	 * Examples
	 *
	 * @since 1.0.0
	 * @param \WP_REST_Request $request Values.
	 * @return \WP_REST_Response|\WP_REST_Request
	 */
	public function demo_example( \WP_REST_Request $request ) {
		// $request is an array with various parameters
		if ( !\wp_verify_nonce( \strval( $request['nonce'] ), 'demo_example' ) ) {
			$response = \rest_ensure_response( 'Wrong nonce' );

			if ( \is_wp_error( $response ) ) {
				return $request;
			}

			$response->set_status( 500 );

			return $response;
		}

		$response = \rest_ensure_response( 'Something here' );

		if ( \is_wp_error( $response ) ) {
			return $request;
		}

		$response->set_status( 500 );

		return $response;
	}

}
