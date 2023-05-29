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

/**
 * Create Shortcode and Gutenberg Block with Widget support
 */
class ShortcodeBlock extends \WP_Super_Duper {

	/**
	 * Parameters shared between methods
	 *
	 * @var array
	 */
	public $arguments;

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() { // phpcs:ignore
		$options = array(
			'textdomain'     => W_TEXTDOMAIN,
			// textdomain of the plugin/theme (used to prefix the Gutenberg block)
			'block-icon'     => 'fas fa-globe-americas',
			// Dash icon name for the block: https://developer.wordpress.org/resource/dashicons/#arrow-right
			// OR font-awesome 5 class name: fas fa-globe-americas
			'block-category' => 'widgets',
			// the category for the block, 'common', 'formatting', 'layout', 'widgets', 'embed'.
			'block-keywords' => "['hello','world']",
			// used in the block search, MAX 3
			'block-output'   => array( // the block visual output elements as an array
				array(
					'element' => 'p',
					'title'   => \__( 'Placeholder', W_TEXTDOMAIN ),
					'class'   => '[%className%]',
					'content' => 'Hello: [%after_text%]', // block properties can be added by wrapping them in [%name%]
				),
			),
			'block-wrap'     => '', // You can specify the type of element to wrap the block `div` or `span` etc.. Or blank for no wrap at all.
			'class_name'     => self::class,
			// The calling class name
			'base_id'        => 'hello_world',
			// this is used as the widget id and the shortcode id.
			'name'           => \__( 'Hello World', W_TEXTDOMAIN ),
			// the name of the widget/block
			'widget_ops'     => array(
				'classname'   => 'hello-world-class',
				// widget class
				'description' => \esc_html__( 'This is an example that will take a text parameter and output it after `Hello:`.', W_TEXTDOMAIN ),
				// widget description
			),
			'no_wrap'        => true, // This will prevent the widget being wrapped in the containing widget class div.
			'arguments'      => array( // these are the arguments that will be used in the widget, shortcode and block settings.
				'after_text' => array( // this is the input name=''
					'title'       => \__( 'Text after hello:', W_TEXTDOMAIN ),
					// input title
					'desc'        => \__( 'This is the text that will appear after `Hello:`.', W_TEXTDOMAIN ),
					// input description
					'type'        => 'text',
					// the type of input, test, select, checkbox etc.
					'placeholder' => 'World',
					// the input placeholder text.
					'desc_tip'    => true,
					// if the input should show the widget description text as a tooltip.
					'default'     => 'World',
					// the input default value.
					'advanced'    => false,
					// not yet implemented
				),
			),
		);

		parent::__construct( $options );
	}

	/**
	 * This is the output function for the widget, shortcode and block (front end).
	 *
	 * @param array  $args The arguments values.
	 * @param array  $widget_args The widget arguments when used.
	 * @param string $content The shortcode content argument.
     * @return string
	 */
	public function output( $args = array(), $widget_args = array(), $content = '' ) { // phpcs:ignore
		$after_text    = '';
		$another_input = '';

		\extract( $args, EXTR_SKIP ); // phpcs:ignore

		return 'Hello: ' . $after_text . '' . $another_input; // phpcs:ignore
	}

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public function initialize() {
		// To enable as widget
		/*
		\add_action(
		'widgets_init',
		static function() {
			\register_widget( 'WooFic\Internals\ShortCodeBlock' );
		}
		);
		*/
	}

}
