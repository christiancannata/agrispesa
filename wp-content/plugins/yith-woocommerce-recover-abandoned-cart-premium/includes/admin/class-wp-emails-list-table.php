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
class YITH_YWRAC_Emails_List_Table extends WP_List_Table {

	/**
	 * Post type
	 *
	 * @var string
	 */
	private $post_type;


	/**
	 * YITH_YWRAC_Emails_List_Table constructor.
	 *
	 * @param array $args Arguments.
	 */
	public function __construct( $args = array() ) {
		parent::__construct( array() );
		$this->post_type = YITH_WC_Recover_Abandoned_Cart_Email()->post_type_name;
	}

	/**
	 * Get columns
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'post_title' => esc_html__( 'Name', 'yith-woocommerce-recover-abandoned-cart' ),
			'type'       => esc_html__( 'Type', 'yith-woocommerce-recover-abandoned-cart' ),
			'send_after' => esc_html__( 'Send after', 'yith-woocommerce-recover-abandoned-cart' ),
			'subject'    => esc_html__( 'Subject', 'yith-woocommerce-recover-abandoned-cart' ),
			'conversion' => esc_html__( 'Conversion Rate', 'yith-woocommerce-recover-abandoned-cart' ),
			'status'     => esc_html__( 'Status', 'yith-woocommerce-recover-abandoned-cart' ),
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

		$args  = array(
			'post_type' => $this->post_type,
		);
		$query = new WP_Query( $args );

		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : ''; //phpcs:ignore
		$order   = ! empty( $_GET['order'] ) ? $_GET['order'] : 'DESC'; //phpcs:ignore  

		$link         = '';
		$order_string = '';
		if ( ! empty( $orderby ) & ! empty( $order ) ) {
			$order_string = 'ORDER BY ywrac_pm.meta_value ' . $order;
			switch ( $orderby ) {
				case 'subject':
					$link = " AND ( ywrac_pm.meta_key = '_ywrac_email_subject' ) ";
					break;
				case 'status':
					$link = " AND ( ywrac_pm.meta_key = '_ywrac_email_active' ) ";
					break;
				default:
					$order_string = ' ORDER BY ' . $orderby . ' ' . $order;
			}
		}

		$query = $wpdb->prepare(
			"SELECT ywrac_p.* FROM $wpdb->posts as ywrac_p INNER JOIN " . $wpdb->prefix . "postmeta as ywrac_pm ON ( ywrac_p.ID = ywrac_pm.post_id )
        WHERE 1=1 $link
        AND ywrac_p.post_type = %s
        AND (ywrac_p.post_status = 'publish' OR ywrac_p.post_status = 'future' OR ywrac_p.post_status = 'draft' OR ywrac_p.post_status = 'pending' OR ywrac_p.post_status = 'private')
        GROUP BY ywrac_p.ID $order_string",
			$this->post_type
		);

		$totalitems = $wpdb->query( $query ); //phpcs:ignore

		$perpage = 5;
		// Which page is this?.
		$paged = ! empty( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : ''; //phpcs:ignore
		// Page Number.
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1; }
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
		$this->items                       = $wpdb->get_results( $query );

	}

	/**
	 * Column default
	 *
	 * @param array|object $item Item.
	 * @param string       $column_name Column name.
	 * @return mixed|string|void
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'post_title':
				return $item->$column_name;
			case 'status':
				$status = get_post_meta( $item->ID, '_ywrac_email_active', true );
				echo "<div class='yith-plugin-ui'>";
				echo yith_plugin_fw_get_field(
					array(
						'type'  => 'onoff',
						'class' => 'ywrac-toggle-enabled',
						'value' => yith_plugin_fw_is_true( $status ) ? 'yes' : 'no',
						'data'  => array(
							'email-template-id' => $item->ID,
							'security'          => wp_create_nonce( 'email-template-status-toggle-enabled' ),
						),
					)
				);
				echo '</div>';

				break;
			case 'type':
				$email_type       = get_post_meta( $item->ID, '_ywrac_email_type', true );
				$email_type_label = '';
				if ( $email_type && 'cart' === $email_type ) {
					$email_type_label = esc_html__( 'Abandoned cart', 'yith-woocommerce-recover-abandoned-cart' );
				} elseif ( $email_type && 'order' === $email_type ) {
					$email_type_label = esc_html__( 'Pending order', 'yith-woocommerce-recover-abandoned-cart' );
				}
				return $email_type_label;
			case 'send_after':
				$email_time = get_post_meta( $item->ID, '_ywrac_email_time', true );
				$type_time  = is_array( $email_time ) && ! empty( $email_time['type'] ) ? $email_time['type'] : get_post_meta( $item->ID, '_ywrac_type_time', true );
				$time       = is_array( $email_time ) && ! empty( $email_time['time'] ) ? $email_time['time'] : get_post_meta( $item->ID, '_ywrac_time', true );
				return $time . ' ' . $type_time;
			case 'subject':
				$user_email = get_post_meta( $item->ID, '_ywrac_email_subject', true );
				return $user_email;
			case 'conversion':
				$email_sent      = intval( apply_filters( 'ywrac_email_template_sent_counter', get_post_meta( $item->ID, '_email_sent_counter', true ), $item->ID ) );
				$recovered_carts = intval( apply_filters( 'ywrac_email_template_cart_recovered', get_post_meta( $item->ID, '_cart_recovered', true ), $item->ID ) );
				if ( $email_sent != 0 ) {    //phpcs:ignore
					$conversion = number_format( 100 * $recovered_carts / $email_sent, 2, '.', '' ) . ' %';
				} else {
					$conversion = '0.00 %';
				}
				return $conversion;
			default:
				return ''; // Show the whole array for troubleshooting purposes.
		}
	}

	/**
	 * Get bulk actions
	 *
	 * @return array
	 */
	public function get_bulk_actions() {

		$actions = $this->current_action();
		if ( ! empty( $actions ) && isset( $_POST['ywrac_email_ids'] ) ) {     //phpcs:ignore

			$emails = (array) $_POST['ywrac_email_ids']; //phpcs:ignore
			if ( 'activate' === $actions ) {
				foreach ( $emails as $email_id ) {
					YITH_WC_Recover_Abandoned_Cart_Email()->activate( $email_id, true );
				}
			} elseif ( 'deactivate' === $actions ) {
				foreach ( $emails as $email_id ) {
					YITH_WC_Recover_Abandoned_Cart_Email()->activate( $email_id, false );
				}
			} elseif ( 'delete' === $actions ) {
				foreach ( $emails as $email_id ) {
					wp_delete_post( $email_id, true );
				}
			}

			$this->prepare_items();
		}

		$actions = array(
			'activate'   => esc_html__( 'Activate', 'yith-woocommerce-recover-abandoned-cart' ),
			'deactivate' => esc_html__( 'Deactivate', 'yith-woocommerce-recover-abandoned-cart' ),
			'delete'     => esc_html__( 'Delete', 'yith-woocommerce-recover-abandoned-cart' ),
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
			$item->ID
		);
	}

	/**
	 * Return sortable columns
	 *
	 * @return array[]
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'post_title' => array( 'post_title', false ),
			'status'     => array( 'status', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Column post title
	 *
	 * @param mixed $item Item.
	 * @return string
	 */
	public function column_post_title( $item ) {
		admin_url( 'post.php?post=' . $item->ID . 'action=edit' );
		$actions = array(
			'edit'   => '<a href="' . admin_url( 'post.php?post=' . $item->ID . '&action=edit' ) . '">' . __( 'Edit', 'yith-woocommerce-recover-abandoned-cart' ) . '</a>',
			'delete' => '<a href="' . YITH_WC_Recover_Abandoned_Cart_Email()->get_delete_post_link( '', $item->ID ) . '">' . __( 'Delete', 'yith-woocommerce-recover-abandoned-cart' ) . '</a>',
		);

		return sprintf( '%1$s %2$s', wp_kses_post( $item->post_title ), $this->row_actions( $actions ) );
	}

}
