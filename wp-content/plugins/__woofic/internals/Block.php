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

namespace WooFic\Internals;

use WooFic\Engine\Base;

/**
 * Block of this plugin
 */
class Block extends Base {

	/**
	 * Initialize the class.
	 *
	 * @return void|bool
	 */
	public function initialize() {
		parent::initialize();

		\add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Registers and enqueue the block assets
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_block() {
		// Register the block by passing the location of block.json to register_block_type.
		$json = \wp_json_file_decode( W_PLUGIN_ROOT . 'assets/src/block/block.json' );

		if ( !\is_array( $json ) ) {
			return;
		}

		\register_block_type( 'woofic/block-name', $json );
	}

}
