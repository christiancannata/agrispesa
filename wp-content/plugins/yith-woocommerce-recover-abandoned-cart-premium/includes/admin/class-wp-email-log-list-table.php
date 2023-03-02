<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 */


if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Email Template List Table
 *
 * @class   YITH_YWRAC_Emails_List_Table
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */
class YITH_YWRAC_Email_Log_List_Table extends WP_List_Table {

	/**
	 * Post type
	 *
	 * @var string
	 */
	private $post_type;

	/**
	 * YITH_YWRAC_Email_Log_List_Table constructor.
	 *
	 * @param array $args Arguments.
	 */
	public function __construct( $args = array() ) {
		parent::__construct( array() );

	}

	/**
	 * Get columns
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />',
			'email'    => __( 'Email', 'yith-woocommerce-recover-abandoned-cart' ),
			'template' => __( 'Template', 'yith-woocommerce-recover-abandoned-cart' ),
			'cart_id'  => __( 'Abandoned Cart ID', 'yith-woocommerce-recover-abandoned-cart' ),
			'date'     => __( 'Date', 'yith-woocommerce-recover-abandoned-cart' ),
		);
		return $columns;
	}

	/**
	 * Prepare items
	 */
	public function prepare_items() {
		global $wpdb, $_wp_column_headers;

		$screen = get_current_screen();

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$search = ! empty( $_REQUEST['s'] ) ? $_REQUEST['s'] : ''; //phpcs:ignore

		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'date_send'; //phpcs:ignore
		$order   = ! empty( $_GET['order'] ) ? $_GET['order'] : 'DESC'; //phpcs:ignore

		$order_string = 'ORDER BY ' . $orderby . ' ' . $order;

		$table_name = $wpdb->prefix . 'yith_ywrac_email_log';

		$query = "SELECT ywrac_logs.* FROM $table_name as ywrac_logs $order_string";

		if ( ! empty( $search ) ) {
			$query = "SELECT ywrac_logs.* FROM $table_name as ywrac_logs where email_id like '%$search%' $order_string";
		}

		$totalitems = $wpdb->query( $query ); //phpcs:ignore

		$perpage = 15;
		// Which page is this?
		$paged = ! empty( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : ''; //phpcs:ignore
		// Page Number.
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}
		// How many pages do we have in total?.
		$totalpages = ceil( $totalitems / $perpage );
		// adjust the query to take pagination into account.
		if ( ! empty( $paged ) && ! empty( $perpage ) ) {
			$offset = ( $paged - 1 ) * $perpage;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}

		/* -- Register the pagination -- */
		$this->set_pagination_args(
			array(
				'total_items' => $totalitems,
				'total_pages' => $totalpages,
				'per_page'    => $perpage,
			)
		);
		// The pagination links are automatically built according to those parameters.

		$_wp_column_headers[ $screen->id ] = $columns;
		$this->items                       = $wpdb->get_results( $query ); //phpcs:ignore

	}

	/**
	 * Columns default
	 *
	 * @param array|object $item Item.
	 * @param string       $column_name Column name.
	 * @return string|void
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'email':
				return $item->email_id;

			case 'template':
				return get_post( $item->email_template_id )->post_title;

			case 'cart_id':
				return $item->ywrac_cart_id;

			case 'date':
				return $item->date_send;
			default:
				return ''; // Show the whole array for troubleshooting purposes.
		}
	}

	/**
	 * Return the bulk actions
	 *
	 * @return array
	 */
	public function get_bulk_actions() {

		$actions = $this->current_action();
		if ( ! empty( $actions ) && isset( $_POST['ywrac_email_ids'] ) ) { //phpcs:ignore
			global $wpdb;

			$table_name = $wpdb->prefix . 'yith_ywrac_email_log';
			$emails     = (array) sanitize_text_field( wp_unslash( $_POST['ywrac_email_ids'] ) ); //phpcs:ignore
			$emails_in  = implode( ',', $emails );
			$wpdb->query( "DELETE FROM $table_name WHERE id IN ($emails_in)" ); //phpcs:ignore

			$this->prepare_items();
		}

		$actions = array(
			'delete' => __( 'Delete', 'yith-woocommerce-recover-abandoned-cart' ),
		);

		return $actions;
	}

	/**
	 * Column cb
	 *
	 * @param array|object $item Item.
	 * @return string|void
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="ywrac_email_ids[]" value="%s" />',
			$item->id
		);
	}

	/**
	 * Get sortable columns
	 *
	 * @return array[]
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'email'    => array( 'email_id', false ),
			'template' => array( 'email_template_id', false ),
			'cart_id'  => array( 'ywrac_cart_id', false ),
			'date'     => array( 'date_send', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Display the search box.
	 *
	 * @param string $text The search button text.
	 * @param string $input_id The search input id.
	 * @since 3.1.0
	 * @access public
	 */
	public function search_box( $text, $input_id ) {

		$input_id = $input_id . '-search-input';
		$request = $_REQUEST; //phpcs:ignore
		if ( ! empty( $request['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( sanitize_text_field( wp_unslash( $request['orderby'] ) ) ) . '" />';
		}
		if ( ! empty( $request['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $request['order'] ) . '" />';
		}
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?>:</label>
			<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php esc_html( _admin_search_query() ); ?>"
				placeholder="<?php esc_html_e( 'Search Email', 'yith-woocommerce-recover-abandoned-cart' ); ?>"/>
			<?php submit_button( $text, 'button', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
		<?php
	}


}
