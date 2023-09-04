<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Service Taxonomy class
 * Manage Service taxonomy
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\GiftCards\Modules\Services
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_YWGC_Category_Taxonomy' ) ) {
	/**
	 * YITH_YWGC_Category_Taxonomy
	 */
	class YITH_YWGC_Category_Taxonomy {
		/**
		 * The instance of the class.
		 *
		 * @var YITH_YWGC_Category_Taxonomy
		 */
		private static $instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_YWGC_Category_Taxonomy
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBK_Service_Tax_Admin constructor.
		 */
		private function __construct() {
			$taxonomy = YWGC_CATEGORY_TAXONOMY;

			add_action( "after-{$taxonomy}-table", array( $this, 'maybe_render_blank_state' ) );

			add_filter( "manage_edit-{$taxonomy}_columns", array( $this, 'get_columns' ) );
			add_filter( "manage_{$taxonomy}_custom_column", array( $this, 'custom_columns' ), 12, 3 );

			add_filter( 'tag_row_actions', array( $this, 'remove_row_actions' ), 10, 2 );
		}

		/**
		 * Filter columns
		 *
		 * @param array $columns The columns.
		 *
		 * @return array The columns list
		 * @use   manage_{$this->screen->id}_columns filter
		 */
		public function get_columns( $columns ) {
			if ( isset( $columns['posts'] ) ) {
				unset( $columns['posts'] );
			}
			if ( isset( $columns['description'] ) ) {
				unset( $columns['description'] );
			}
			if ( isset( $columns['slug'] ) ) {
				unset( $columns['slug'] );
			}

			if ( isset( $columns['count'] ) ) {
				unset( $columns['count'] );
			}

			$columns['actions'] = _x( 'Actions', 'Actions column in the gift card categories table', 'yith-woocommerce-gift-cards' );

			return $columns;
		}

		/**
		 * Display custom columns for Service Taxonomy
		 *
		 * @param string $custom_column Filter value.
		 * @param string $column_name   Column name.
		 * @param int    $term_id       The term id.
		 *
		 * @internal param \The $columns columns
		 * @use      manage_{$this->screen->taxonomy}_custom_column filter
		 */
		public function custom_columns( $custom_column, $column_name, $term_id ) {
			switch ( $column_name ) {
				case 'actions':
					$actions = yith_plugin_fw_get_default_term_actions( $term_id );

					unset( $actions['view'] );

					foreach ( $actions as $action ) {
						$custom_column .= yith_plugin_fw_get_component( $action, false );
					}

					break;
			}

			return $custom_column;
		}

		/**
		 * Remove Row Actions
		 *
		 * @param array   $actions An array of row action links. Defaults are
		 *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
		 *                         'Delete Permanently', 'Preview', and 'View'.
		 * @param WP_Term $tag     The post object.
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function remove_row_actions( $actions, $tag ) {
			if ( YWGC_CATEGORY_TAXONOMY === $tag->taxonomy ) {
				$actions = array();
			}

			return $actions;
		}

		/**
		 * Maybe render blank state
		 *
		 * @since 3.2.1
		 */
		public function maybe_render_blank_state() {
			$count = absint( wp_count_terms( YWGC_CATEGORY_TAXONOMY ) );

			if ( 0 < $count ) {
				return;
			}

			$this->render_blank_state();

			echo '<style type="text/css" id="yith-wcbk-blank-state-style">#posts-filter { display: none; } form.search-form {visibility: hidden;}</style>';
		}

		/**
		 * Render blank state.
		 *
		 * @since 3.2.1
		 */
		protected function render_blank_state() {
			$component = array(
				'type'     => 'list-table-blank-state',
				'icon_url' => YITH_YWGC_ASSETS_URL . '/images/empty-gift.svg',
				'message'  => _x( 'You have no gift card categories yet!', 'empty gift card categories message', 'yith-woocommerce-gift-cards' ),
			);

			yith_plugin_fw_get_component( $component, true );
		}

	}
}

/**
 * Unique access to instance of YITH_YWGC_Cart_Checkout class
 *
 * @return YITH_YWGC_Category_Taxonomy
 */
function YITH_YWGC_Category_Taxonomy() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	$instance = YITH_YWGC_Category_Taxonomy::get_instance();

	return $instance;
}
