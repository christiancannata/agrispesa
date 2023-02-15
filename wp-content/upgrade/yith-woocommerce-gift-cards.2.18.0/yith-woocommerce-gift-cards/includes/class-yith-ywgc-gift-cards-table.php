<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists( 'YITH_YWGC_Gift_Cards_Table' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Gift_Cards_Table
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Gift_Cards_Table {

		/**
		 * Gift card table columns
		 */
		const COLUMN_ID_ORDER           = 'purchase_order';
		const COLUMN_ID_INFORMATION     = 'information';
		const COLUMN_ID_BALANCE         = 'balance';
		const COLUMN_ID_DEST_ORDERS     = 'dest_orders';
		const COLUMN_ID_ACTIONS         = 'gift_card_actions';
		const COLUMN_ID_EXPIRATION_DATE = 'expiration_date';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {

			add_filter( 'manage_posts_columns', array( $this, 'remove_default_columns' ), 10, 2 );

			add_filter( 'manage_edit-gift_card_columns', array( $this, 'add_custom_columns_title' ) );

			add_action( 'manage_gift_card_posts_custom_column', array( $this, 'show_custom_column_content' ), 10, 2 );

			add_action( 'manage_posts_extra_tablenav', array( $this, 'maybe_render_blank_state' ) );

		}

		/**
		 * Remove default columns to custom post type table
		 *
		 * @param array $columns columns.
		 * @param array $post_type post_type.
		 * @return array new columns
		 */
		public function remove_default_columns( $columns, $post_type ) {

			if ( YWGC_CUSTOM_POST_TYPE_NAME === $post_type ) {
				unset( $columns['date'] );
				unset( $columns['shortcode'] );
				$columns['title'] = esc_html__( 'Code', 'yith-woocommerce-gift-cards' );
			}

			return $columns;
		}


		/**
		 * Add custom columns to custom post type table
		 *
		 * @param array $defaults current columns
		 *
		 * @return array new columns
		 */
		function add_custom_columns_title( $defaults ) {
			$columns = array_slice( $defaults, 0, 2 );

			$columns[ self::COLUMN_ID_ORDER ]           = esc_html__( 'Purchase order', 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_BALANCE ]         = esc_html__( 'Balance', 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_DEST_ORDERS ]     = esc_html__( 'Used in orders', 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_EXPIRATION_DATE ] = esc_html__( 'Expiration date', 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_INFORMATION ]     = esc_html__( 'Recipient', 'yith-woocommerce-gift-cards' );
			$columns[ self::COLUMN_ID_ACTIONS ]         = '';

			/**
			 * APPLY_FILTERS: yith_wcgc_custom_columns_title
			 *
			 * Filter the column names in the gift cards dashboard.
			 *
			 * @param array $columns array of columns titles
			 *
			 * @return array
			 */
			$columns = apply_filters( 'yith_wcgc_custom_columns_title', $columns );
			return array_merge( $columns, array_slice( $defaults, 1 ) );
		}

		/**
		 * Get the order details
		 *
		 * @param WC_Order|int $order the order.
		 *
		 * @return int
		 */
		public function get_order_number_and_details( $order ) {

			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}

			if ( ! $order instanceof WC_Order ) {
				return '';
			}

			$customer = $order->get_user();
			if ( $customer ) {
				$username = '<a href="user-edit.php?user_id=' . absint( $customer->ID ) . '">';

				if ( $customer->first_name || $customer->last_name ) {
					$username .= esc_html( ucfirst( $customer->first_name ) . ' ' . ucfirst( $customer->last_name ) );
				} else {
					$username .= esc_html( ucfirst( $customer->display_name ) );
				}

				$username .= '</a>';

			} else {
				$billing_first_name = $order->get_billing_first_name();
				$billing_last_name  = $order->get_billing_last_name();

				if ( $billing_first_name || $billing_last_name ) {
					$username = trim( $billing_first_name . ' ' . $billing_last_name );
				} else {
					$username = esc_html__( 'Guest', 'yith-woocommerce-gift-cards' );
				}
			}

			return sprintf(
				_x( '%s by %s', 'Order number by X', 'yith-woocommerce-gift-cards' ),
				'<a href="' . admin_url( 'post.php?post=' . absint( $order->get_id() ) . '&action=edit' ) . '" class="row-title"><strong>#' .
				esc_attr( $order->get_order_number() ) . '</strong></a>',
				$username
			);
		}


		/**
		 * Show content for custom columns
		 *
		 * @param mixed $column_name  column shown.
		 * @param mixed $post_ID          post to use.
		 */
		public function show_custom_column_content( $column_name, $post_ID ) {

			$gift_card = new YITH_YWGC_Gift_Card( array( 'ID' => $post_ID ) );

			if ( ! $gift_card->exists() ) {
				return;
			}

			switch ( $column_name ) {
				case self::COLUMN_ID_ORDER:
					if ( $gift_card->order_id ) {
						echo wp_kses( $this->get_order_number_and_details( $gift_card->order_id ), 'post' );
					} else {
						echo wp_kses( apply_filters( 'yith_wcgc_table_created_manually_message', esc_html__( 'Created manually', 'yith-woocommerce-gift-cards' ) ), 'post' );
					}

					break;

				case self::COLUMN_ID_BALANCE:
					echo wp_kses( wc_price( $gift_card->get_balance() ), 'post' );

					break;

				case self::COLUMN_ID_DEST_ORDERS:
					$orders = $gift_card->get_registered_orders();
					if ( $orders ) {
						foreach ( $orders as $order_id ) {
							echo wp_kses( $this->get_order_number_and_details( $order_id ), 'post' );
							echo '<br>';
						}
					} elseif ( $gift_card->get_balance() < $gift_card->total_amount && $gift_card->get_balance() > 0 ) {
						echo wp_kses( apply_filters( 'yith_wcgc_table_partially_redeemed_message', esc_html__( 'Partially redeemed', 'yith-woocommerce-gift-cards' ) ), 'post' );
					} elseif ( $gift_card->get_balance() == 0 ) {//phpcs:ignore
						echo wp_kses( apply_filters( 'yith_wcgc_table_completely_redeemed_message', esc_html__( 'Completely redeemed', 'yith-woocommerce-gift-cards' ) ), 'post' );
					} else {
						echo wp_kses( apply_filters( 'yith_wcgc_table_code_no_used_message', esc_html__( 'The code has not been used yet', 'yith-woocommerce-gift-cards' ) ), 'post' );
					}

					break;

				case self::COLUMN_ID_INFORMATION:
					$this->show_details_on_gift_cards_table( $post_ID, $gift_card );

					break;

				case self::COLUMN_ID_EXPIRATION_DATE:
					$date_format = apply_filters( 'yith_wcgc_date_format', 'Y-m-d' );

					$expiration_date = ! is_numeric( $gift_card->expiration ) ? strtotime( $gift_card->expiration ) : $gift_card->expiration;

					if ( $expiration_date ) {
						echo wp_kses( date_i18n( $date_format, $expiration_date ), 'post' );
					} else {
						esc_html_e( 'Unlimited', 'yith-woocommerce-gift-cards' );
					}

					break;

				case self::COLUMN_ID_ACTIONS:
					$this->show_send_email_button( $post_ID, $gift_card );

					break;

				default:
					echo wp_kses( apply_filters( 'yith_wcgc_column_default', '', $post_ID, $column_name ), 'post' );
			}
		}

		/**
		 * Show send email button
		 *
		 * @param int                    $post_ID the post ID.
		 * @param YITH_YWGC_Gift_Card $gift_card the gift card.
		 */
		public function show_send_email_button( $post_ID, $gift_card ) {

			if ( $gift_card->is_enabled() ) {

				$recipient = $gift_card->recipient;

				if ( ! empty( $recipient ) ) {

					$send_now_link = sprintf(
						'<a class="ywgc-actions %s" href="%s" title="%s" style="display: none; box-shadow: rgba(0, 113, 161, 0.3) 0px 0px 5px;"><img src="' . YITH_YWGC_ASSETS_URL . '/images/mail.svg" alt=""></a>',
						'gift-cards send-now',
						esc_url_raw(
							add_query_arg(
								array(
									YWGC_ACTION_RETRY_SENDING => 1,
									'id' => $post_ID,
								)
							)
						),
						esc_html__( 'Send now', 'yith-woocommerce-gift-cards' )
					);

					echo $send_now_link;
				}
			}
		}

		/**
		 * Show details
		 *
		 * @param int                    $post_ID
		 * @param YITH_YWGC_Gift_Card $gift_card
		 */
		public function show_details_on_gift_cards_table( $post_ID, $gift_card ) {

			if ( $gift_card->is_dismissed() ) {
				?>
				<span
					class="ywgc-dismissed-text"><?php echo esc_html__( 'This card is dismissed.', 'yith-woocommerce-gift-cards' ); ?></span>
				<?php
			}

			if ( ! $gift_card->is_digital ) {
				?>
				<div>
					<span><?php echo esc_html__( 'Physical product', 'yith-woocommerce-gift-cards' ); ?></span>
				</div>
				<?php
			} else {

				if ( $gift_card->delivery_send_date ) {
					$status_class = 'sent';
					$message      = sprintf( esc_html__( 'Sent on %s', 'yith-woocommerce-gift-cards' ), $gift_card->get_formatted_date( $gift_card->delivery_send_date ) );
				} elseif ( $gift_card->delivery_date >= current_time ( 'timestamp' ) ) {//phpcs:ignore --timestamp is discouraged
					$status_class = 'scheduled';
					$message      = esc_html__( 'Scheduled', 'yith-woocommerce-gift-cards' );
				} elseif ( '' === $gift_card->has_been_sent() ) {
					$status_class = 'not-sent';
					$message      = esc_html__( 'Not yet sent', 'yith-woocommerce-gift-cards' );
				} else {
					$status_class = 'failed';
					$message      = esc_html__( 'Failed', 'yith-woocommerce-gift-cards' );
				}
				?>

				<div>
					<span><?php echo sprintf( '%s', $gift_card->recipient ); ?></span>
				</div>

				<div>
					<?php

					if ( $gift_card->delivery_date ) :
						?>
						<span><?php echo sprintf( esc_html__( 'Delivery date: %s', 'yith-woocommerce-gift-cards' ), get_post_meta( $post_ID, '_ywgc_delivery_date_formatted', true ) ); ?></span>
						<br>
					<?php endif; ?>
					<span
						class="ywgc-delivery-status <?php echo esc_attr( $status_class ); ?>"><?php echo esc_attr( $message ); ?></span>

				</div>

				<?php
			}
		}

		/**
		 * Show blank slate.
		 *
		 * @param string $which String which table-nav is being shown.
		 */
		public function maybe_render_blank_state( $which ) {
			global $post_type;

			if ( 'gift_card' === $post_type && 'bottom' === $which ) {
				$counts = (array) wp_count_posts( $post_type );
				unset( $counts['auto-draft'] );
				$count = array_sum( $counts );

				if ( 0 < $count ) {
					return;
				}

				$this->render_blank_state();

				echo '<style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom > *, .wrap .subsubsub  { display: none; } #posts-filter .tablenav.bottom, .tablenav.bottom #ywgc-list-table-blank-state{ height: auto; display: block } .yith-plugin-ui--gift_card-post_type .wrap a.page-title-action { display: none; } </style>';
			}

		}

		/**
		 * Render an empty state.
		 */
		public function render_blank_state() {

			if ( ! wp_style_is( 'yith-plugin-ui', 'registered' ) ) {
				$plugin_fw_assets = class_exists( 'YIT_Assets' ) && is_callable( 'YIT_Assets::instance' ) ? YIT_Assets::instance() : false;

				if ( $plugin_fw_assets && is_callable( array( $plugin_fw_assets, 'register_styles_and_scripts' ) ) ) {
					$plugin_fw_assets->register_styles_and_scripts();
				}
			}

			$message = __( 'You don’t have any gift card code to display. Once a user has purchased a gift card you will be able to see the code here. You can also manually create a gift card code to share.', 'yith-woocommerce-gift-cards' );

			yith_plugin_fw_get_component(
				array(
					'id'       => 'ywgc-list-table-blank-state',
					'type'     => 'list-table-blank-state',
					'icon_url' => YITH_YWGC_ASSETS_URL . '/images/empty-gift.svg',
					'message'  => $message,
					'cta'      => array(
						'title' => __( 'Create code', 'yith-woocommerce-gift-cards' ),
						'url'   => esc_url( admin_url( 'post-new.php?post_type=gift_card' ) ),
					),
				),
				true
			);

		}

	}
}

/**
 * Unique access to instance of YITH_YWGC_Gift_Cards_Table class
 *
 * @return YITH_YWGC_Gift_Cards_Table|YITH_YWGC_Gift_Cards_Table_Premium|YITH_YWGC_Gift_Cards_Table_Extended
 * @since 2.0.0
 */
function YITH_YWGC_Gift_Cards_Table() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	if ( defined( 'YITH_YWGC_PREMIUM' ) ) {
		$instance = YITH_YWGC_Gift_Cards_Table_Premium::get_instance();
	} elseif ( defined( 'YITH_YWGC_EXTENDED' ) ) {
		$instance = YITH_YWGC_Gift_Cards_Table_Extended::get_instance();
	} else {
		$instance = YITH_YWGC_Gift_Cards_Table::get_instance();
	}

	return $instance;
}
