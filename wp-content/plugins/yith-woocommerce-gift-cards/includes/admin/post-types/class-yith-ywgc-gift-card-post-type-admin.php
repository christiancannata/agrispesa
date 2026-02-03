<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Class YITH_YWGC_Gift_Cards_Post_Type_Admin
 *
 * Handles the "Classic" post type on admin side.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PluginFrameworkTestPlugin\Classes
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_YWGC_Gift_Cards_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_YWGC_Gift_Cards_Post_Type_Admin
	 */
	class YITH_YWGC_Gift_Cards_Post_Type_Admin extends YITH_Post_Type_Admin {

		/**
		 * The post type.
		 *
		 * @var string
		 */
		protected $post_type = YWGC_CUSTOM_POST_TYPE_NAME;

		/**
		 * The classic object.
		 *
		 * @var YITH_WooCommerce_Gift_Cards
		 */
		protected $object;

		/**
		 * YITH_YWGC_Gift_Cards_Post_Type_Admin constructor.
		 */
		protected function __construct() {
			parent::__construct();

			add_filter( 'views_edit-' . $this->post_type, array( $this, 'filter_views' ) );

			add_filter( 'yith_plugin_fw_panel_url', array( $this, 'set_all_view_as_default' ), 10, 5 );

			add_action( 'admin_head-edit.php', array( $this, 'add_custom_dashboard_header' ) );

			add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'render_columns_custom' ), 10, 2 );
		}

		/**
		 * Define primary column.
		 *
		 * @return string
		 */
		protected function get_primary_column() {
			return 'code';
		}

		/**
		 * Return true if you want to use the object. False otherwise.
		 *
		 * @return bool
		 */
		protected function use_object() {
			return false;
		}

		/**
		 * Filter Views
		 *
		 * @param array $views The views.
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function filter_views( $views ) {
			global $wpdb;

			// Views args.
			$args_all_gift_cards = array(
				'post_type'   => $this->post_type,
				'post_status' => 'publish',
				'all_posts'   => 1,
			);

			$args_not_redeemed = array(
				'post_type'   => $this->post_type,
				'post_status' => 'publish',
				'balance'     => 'active',
			);

			$args_redeemed = array(
				'post_type'   => $this->post_type,
				'post_status' => 'publish',
				'balance'     => 'used',
			);

			// Views count.
			$num_posts   = wp_count_posts( $this->post_type );
			$total_posts = $num_posts->publish;

			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$count_redeemed     = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( DISTINCT( post_id ) ) FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id WHERE meta_key = %s AND ROUND(meta_value, %d) = 0 AND p.post_type= %s AND p.post_status= %s", '_ywgc_balance_total', wc_get_price_decimals(), 'gift_card', 'publish' ) );
			$count_not_redeemed = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( DISTINCT( post_id ) ) FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id WHERE meta_key = %s AND ROUND(meta_value, %d) > 0 AND p.post_type= %s AND p.post_status= %s", '_ywgc_balance_total', wc_get_price_decimals(), 'gift_card', 'publish' ) );
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

			// Views URL.
			$all_url   = add_query_arg( $args_all_gift_cards, admin_url( 'edit.php' ) );
			$all_attrs = isset( $_GET['all_posts'] ) || ! isset( $_GET['balance'] ) ? 'class="current" aria-current="page"' : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$redeemed_url   = add_query_arg( $args_redeemed, admin_url( 'edit.php' ) );
			$redeemed_attrs = $this->is_redeemed_view() ? 'class="current" aria-current="page"' : '';

			$not_redeemed_url   = add_query_arg( $args_not_redeemed, admin_url( 'edit.php' ) );
			$not_redeemed_attrs = $this->is_not_redeemed_view() ? 'class="current" aria-current="page"' : '';

			$views = array(
				'all'          => "<a href='{$all_url}' {$all_attrs}>" . esc_html__( 'All', 'yith-woocommerce-gift-cards' ) . ' <span class="count">(' . esc_html( number_format_i18n( $total_posts ) ) . ')</span></a>',
				'redeemed'     => "<a href='{$redeemed_url}' {$redeemed_attrs}>" . esc_html__( 'Redeemed', 'yith-woocommerce-gift-cards' ) . ' <span class="count">(' . esc_html( number_format_i18n( $count_redeemed ) ) . ')</span></a>',
				'not_redeemed' => "<a href='{$not_redeemed_url}' {$not_redeemed_attrs}>" . esc_html__( 'Not Redeemed', 'yith-woocommerce-gift-cards' ) . ' <span class="count">(' . esc_html( number_format_i18n( $count_not_redeemed ) ) . ')</span></a>',
			);

			return $views;
		}

		/**
		 * Set the "all" view as default when loading the dashboard
		 *
		 * @param string $url         URL.
		 * @param string $page        Page.
		 * @param string $tab         Tab.
		 * @param string $sub_tab     Sub tab.
		 * @param string $parent_page Parent page.
		 *
		 * @return string
		 */
		public function set_all_view_as_default( $url, $page, $tab, $sub_tab, $parent_page ) {
			if ( 'yith_woocommerce_gift_cards_panel' === $page && str_ends_with( $url, 'edit.php?post_type=gift_card' ) ) {
				$url .= '&post_status=publish';
			}

			return $url;
		}

		/**
		 * Return true if this is the "redeemed" view.
		 *
		 * @return bool
		 */
		private function is_redeemed_view() {
			static $is_redeemed = null;

			if ( is_null( $is_redeemed ) ) {
				$is_redeemed = isset( $_GET['balance'] ) && 'used' === $_GET['balance']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			return $is_redeemed;
		}

		/**
		 * Return true if this is the "redeemed" view.
		 *
		 * @return bool
		 */
		private function is_not_redeemed_view() {
			static $is_not_redeemed = null;

			if ( is_null( $is_not_redeemed ) ) {
				$is_not_redeemed = isset( $_GET['balance'] ) && 'active' === $_GET['balance']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			return $is_not_redeemed;
		}

		/**
		 * Define bulk actions.
		 *
		 * @param array $actions Existing actions.
		 *
		 * @return array
		 */
		public function define_bulk_actions( $actions ) {
			if ( isset( $actions['edit'] ) ) {
				unset( $actions['edit'] );
			}

			if ( isset( $actions['trash'] ) ) {
				unset( $actions['trash'] );
			}

			$post_type_object = get_post_type_object( $this->post_type );

			if ( current_user_can( $post_type_object->cap->delete_posts ) ) {
				$actions['delete'] = __( 'Delete', 'yith-woocommerce-gift-cards' );
			}

			return $actions;
		}

		/**
		 * Handle bulk actions.
		 *
		 * @param string $redirect_to URL to redirect to.
		 * @param string $action      Action name.
		 * @param array  $ids         List of ids.
		 *
		 * @return string
		 */
		public function handle_bulk_actions( $redirect_to, $action, $ids ) {
			switch ( $action ) {
				case 'delete_gift_cards':
					foreach ( $ids as $gift_card_id ) {
						wp_delete_post( $gift_card_id );
					}
					break;

				default:
			}

			return esc_url_raw( $redirect_to );
		}

		/**
		 * Define which columns to show on this screen.
		 *
		 * @param array $columns Existing columns.
		 *
		 * @return array
		 */
		public function define_columns( $columns ) {
			unset( $columns['date'] );
			unset( $columns['shortcode'] );
			unset( $columns['title'] );

			$columns['code']       = esc_html__( 'Code', 'yith-woocommerce-gift-cards' );
			$columns['order']      = esc_html__( 'Order', 'yith-woocommerce-gift-cards' );
			$columns['balance']    = esc_html__( 'Balance', 'yith-woocommerce-gift-cards' );
			$columns['redeemed']   = esc_html__( 'Redeemed', 'yith-woocommerce-gift-cards' );
			$columns['expiration'] = esc_html__( 'Expire on', 'yith-woocommerce-gift-cards' );
			$columns['recipient']  = esc_html__( 'Recipient', 'yith-woocommerce-gift-cards' );
			$columns['enabled']    = esc_html__( 'Enabled', 'yith-woocommerce-gift-cards' );
			$columns['actions']    = '';

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

			return $columns;
		}

		/**
		 * Show content for custom columns
		 *
		 * @param mixed $column  column shown.
		 * @param mixed $post_id post to use.
		 */
		public function render_columns_custom( $column, $post_id ) {
			$gift_card = new YITH_YWGC_Gift_Card( array( 'ID' => $post_id ) );

			if ( ! $gift_card->exists() ) {
				return;
			}

			switch ( $column ) {
				case 'code':
					$gift_card_code = $gift_card->get_code();
					$code_field     = array(
						'id'    => 'gift_card_code',
						'type'  => 'copy-to-clipboard',
						'value' => $gift_card_code,
					);

					yith_plugin_fw_get_field( $code_field, true );

					break;

				case 'order':
					$generated_in_bulk = get_post_meta( $gift_card->ID, 'generated_in_bulk' );

					if ( $gift_card->order_id ) {
						echo wp_kses_post( $this->get_order_number_and_details( $gift_card->order_id ) );
					} elseif ( $generated_in_bulk ) {
						/**
						 * APPLY_FILTERS: yith_wcgc_table_generated_in_bulk_message
						 *
						 * Filter the "Generated in bulk" string in the gift cards dashboard.
						 *
						 * @param string the "Generated in bulk" string
						 *
						 * @return string
						 */
						echo esc_html( apply_filters( 'yith_wcgc_table_generated_in_bulk_message', __( 'Generated in bulk', 'yith-woocommerce-gift-cards' ) ) );
					} else {
						/**
						 * APPLY_FILTERS: yith_wcgc_table_created_manually_message
						 *
						 * Filter the "Created manually" string in the gift cards dashboard.
						 *
						 * @param string the "Created manually" string
						 *
						 * @return string
						 */
						echo esc_html( apply_filters( 'yith_wcgc_table_created_manually_message', __( 'Created manually', 'yith-woocommerce-gift-cards' ) ) );
					}

					break;

				case 'balance':
					/**
					 * APPLY_FILTERS: yith_wcgc_table_get_balance
					 *
					 * Filter the gift card balance in the gift cards dashboard.
					 *
					 * @param string the gift card balance using the wc_price method
					 * @param object $gift_card the gift card object
					 *
					 * @return string
					 */
					echo wp_kses_post( apply_filters( 'yith_wcgc_table_get_balance', wc_price( $gift_card->get_balance(), array( 'currency' => $gift_card->currency ) ), $gift_card ) );
					break;

				case 'redeemed':
					$redemptions = $gift_card->get_redemption_history();

					if ( ! empty( $redemptions ) ) {
						foreach ( $redemptions as $redemption ) {
							echo wp_kses_post( $redemption );
							echo '<br>';
						}
					}

					$orders = $gift_card->get_registered_orders();

					if ( ! empty( $orders ) ) {
						echo esc_html_x( 'In order:', 'Display the gift card used in specific WC orders, for example, In order: #1234', 'yith-woocommerce-gift-cards' );
						echo '<br>';

						$order_count = count( $orders );
						$counter     = 1;

						foreach ( $orders as $order_id ) {
							echo wp_kses_post( $this->get_order_number_and_details( $order_id ) );

							if ( $counter < $order_count ) {
								echo ',';
							}

							++$counter;
						}
					}

					if ( $gift_card->get_balance() === floatval( $gift_card->total_amount ) && empty( $redemptions ) && empty( $orders ) ) {
						/**
						 * APPLY_FILTERS: yith_wcgc_table_code_no_used_message
						 *
						 * Filter the "The code has not been used yet" string in the gift cards dashboard.
						 *
						 * @param string the "The code has not been used yet" string
						 *
						 * @return string
						 */
						echo esc_html( apply_filters( 'yith_wcgc_table_code_no_used_message', __( 'The code has not been used yet', 'yith-woocommerce-gift-cards' ) ) );
					}

					break;

				case 'expiration':
					$date_format     = apply_filters( 'yith_wcgc_date_format', 'Y-m-d' );
					$expiration_date = ! is_numeric( $gift_card->expiration ) ? strtotime( $gift_card->expiration ) : $gift_card->expiration;

					if ( $expiration_date ) {
						echo esc_html( date_i18n( $date_format, $expiration_date ) );
					} else {
						echo esc_html__( 'Unlimited', 'yith-woocommerce-gift-cards' );
					}

					break;

				case 'recipient':
					$this->show_details_on_gift_cards_table( $post_id, $gift_card );

					break;

				case 'enabled':
					yith_plugin_fw_get_field(
						array(
							'type'  => 'onoff',
							'id'    => 'ywgc-toggle-enabled-' . $gift_card->ID,
							'class' => 'ywgc-toggle-enabled',
							'value' => $gift_card->is_enabled(),
							'data'  => array(
								'gift-card-id' => $gift_card->ID,
							),
						),
						true
					);

					break;

				case 'actions':
					$more_menu = array(
						'send-email' => array(
							'name' => __( 'Send email', 'yith-woocommerce-gift-cards' ),
							'url'  => $this->send_email_link( $post_id, $gift_card ),
						),
					);

					if ( ! $gift_card->is_virtual() || empty( $gift_card->recipient ) || $gift_card->is_disabled() || $gift_card->is_dismissed() ) {
						unset( $more_menu['send-email'] );
					}

					$options = array(
						'delete-directly'        => true,
						'more-menu'              => $more_menu,
						'confirm-delete-message' => __( 'Are you sure you want to delete this gift card?', 'yith-woocommerce-gift-cards' ) . '<br /><br />' . __( 'This action cannot be undone and you will be not able to recover this data.', 'yith-woocommerce-gift-cards' ),
					);

					$actions = yith_plugin_fw_get_default_post_actions( $post_id, $options );

					yith_plugin_fw_get_action_buttons( $actions, true );

					break;

				default:
					/**
					 * APPLY_FILTERS: yith_wcgc_column_default
					 *
					 * Filter the default column in the gift cards dashboard.
					 *
					 * @param string the column data
					 * @param int $post_ID the gift card ID
					 * @param string $column_name the column name
					 *
					 * @return string
					 */
					echo wp_kses( apply_filters( 'yith_wcgc_column_default', '', $post_id, $column ), 'post' );
			}
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

			return '<a href="' . admin_url( 'post.php?post=' . absint( $order->get_id() ) . '&action=edit' ) . '" class="row-title">#' . esc_attr( $order->get_order_number() );
		}

		/**
		 * Show details
		 *
		 * @param int                 $post_ID   Post ID.
		 * @param YITH_YWGC_Gift_Card $gift_card Gift card object.
		 */
		public function show_details_on_gift_cards_table( $post_ID, $gift_card ) {
			if ( $gift_card->is_dismissed() ) {
				?>
				<span class="ywgc-dismissed-text"><?php echo esc_html__( 'This card is dismissed.', 'yith-woocommerce-gift-cards' ); ?></span>
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
					$status_class   = 'sent';
					$formatted_date = $gift_card->get_formatted_date( $gift_card->delivery_send_date );

					// translators: %s is the date when the gift card was sent to the receiver.
					$message = sprintf( esc_html__( 'Sent on %s', 'yith-woocommerce-gift-cards' ), (string) $formatted_date );
				} elseif ( $gift_card->delivery_date >= current_time( 'timestamp' ) ) { // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
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
					<span><?php echo wp_kses_post( apply_filters( 'yith_ywgc_table_gift_card_recipient', $gift_card->recipient, $gift_card ) ); ?></span>
				</div>
				<div>
					<?php
					if ( $gift_card->delivery_date ) :
						?>
						<span>
							<?php
							// translators: %s is the gift card delivery date.
							echo esc_html( sprintf( __( 'Delivery date: %s', 'yith-woocommerce-gift-cards' ), get_post_meta( $post_ID, '_ywgc_delivery_date_formatted', true ) ) );
							?>
						</span>
						<br>
					<?php endif; ?>
					<span class="ywgc-delivery-status <?php echo esc_attr( $status_class ); ?>"><?php echo esc_attr( $message ); ?></span>
				</div>
				<?php
			}
		}

		/**
		 * Retrieve an array of parameters for blank state.
		 *
		 * @return array{
		 * @type string $icon_url The icon URL.
		 * @type string $message  The message to be shown.
		 * @type string $cta      The call-to-action button title.
		 * @type string $cta_icon The call-to-action button icon.
		 * @type string $cta_url  The call-to-action button URL.
		 *              }
		 */
		protected function get_blank_state_params() {
			return array(
				'icon'    => 'more',
				'message' => __( 'You have no Gift Cards yet!', 'yith-woocommerce-gift-cards' ),
				'cta'     => array(
					'title' => _x( 'Create code', 'Button text', 'yith-woocommerce-gift-cards' ),
				),
			);
		}

		/**
		 * Show blank slate.
		 *
		 * @param string $which String which table-nav is being shown.
		 */
		public function maybe_render_blank_state( $which ) {
			global $post_type;

			if ( 'gift_card' === $post_type && 'top' === $which ) {
				$counts = (array) wp_count_posts( $post_type );
				unset( $counts['auto-draft'], $counts['draft'] );
				$count = array_sum( $counts );

				if ( 0 < $count ) {
					return;
				}

				$this->render_blank_state();

				echo '<style type="text/css">#posts-filter .wp-list-table, #posts-filter .yith-plugin-ui__wp-list-auto-h-scroll__wrapper, #posts-filter .tablenav.top, .tablenav.top > *, .edit-php.post-type-gift_card .subsubsub  { display: none; } #posts-filter .tablenav.top, .tablenav.top #ywgc-list-table-blank-state{ height: auto; display: block } .yith-plugin-ui--gift_card-post_type .wrap a.page-title-action { display: none; } </style>';
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

			$message = __( 'You don\'t have any gift card code to display. Once a user has purchased a gift card you will be able to see the code here. You can also manually create a gift card code to share.', 'yith-woocommerce-gift-cards' );

			yith_plugin_fw_get_component(
				array(
					'id'       => 'ywgc-list-table-blank-state',
					'type'     => 'list-table-blank-state',
					'icon_url' => YITH_YWGC_ASSETS_URL . '/images/empty-gift.svg',
					'message'  => $message,
					'cta'      => array(
						'title' => __( 'Create code', 'yith-woocommerce-gift-cards' ),
						'url'   => esc_url( admin_url( 'post-new.php?post_type=gift_card' ) ),
						'class' => 'create-code-custom-button',
					),
				),
				true
			);
		}

		/**
		 * Show send email button
		 *
		 * @param int                 $post_id the post ID.
		 * @param YITH_YWGC_Gift_Card $gift_card the gift card.
		 */
		public function send_email_link( $post_id, $gift_card ) {
			return wp_nonce_url(
				add_query_arg(
					array(
						YWGC_ACTION_RETRY_SENDING => 1,
						'id'                      => $post_id,
					),
				),
				'gift-card-nonce',
				'gift-card-nonce',
			);
		}

		/**
		 * Print the "Back to WP List" button in Edit Post pages
		 */
		public function print_back_to_wp_list_button() {
			$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$screen_id = $screen ? $screen->id : false;

			if ( $screen_id === $this->post_type ) {
				$url  = add_query_arg(
					array(
						'post_type'   => $this->post_type,
						'post_status' => 'publish',
					),
					admin_url( 'edit.php' )
				);
				$text = $this->get_back_to_wp_list_text();

				if ( $text ) {
					?>
					<div id='yith-plugin-fw__back-to-wp-list__wrapper' class='yith-plugin-fw__back-to-wp-list__wrapper'>
						<a id='yith-plugin-fw__back-to-wp-list' class='yith-plugin-fw__back-to-wp-list' href='<?php echo esc_url( $url ); ?>'><?php echo esc_html( $text ); ?></a>
					</div>
					<script type="text/javascript">
						( function () {
							var wrap   = document.querySelector( '.wrap' ),
								backTo = document.querySelector( '#yith-plugin-fw__back-to-wp-list__wrapper' );

							wrap.insertBefore( backTo, wrap.childNodes[ 0 ] );
						} )();
					</script>
					<?php
				}
			}
		}

		/**
		 * Adds custom "Create code" button in the table
		 */
		public function add_custom_dashboard_header() {
			global $current_screen;

			if ( 'gift_card' !== $current_screen->post_type ) {
				return;
			}

			?>
			<script type="text/javascript">
				jQuery(function () {
					jQuery('hr.wp-header-end').before("<a class='page-title-action export-import-custom-button' title='<?php echo esc_html__( 'Available on Premium', 'yith-woocommerce-gift-cards' ); ?>'><?php echo esc_html__( 'Export/Import', 'yith-woocommerce-gift-cards' ); ?></a>");
					jQuery('hr.wp-header-end').before("<div class='yith-plugin-fw-wp-page__description'><?php echo esc_html_x( 'A table with all the gift card codes generated in your shop.', 'gift cards dashboard description', 'yith-woocommerce-gift-cards' ); ?></div>");
				});
			</script>

			<?php
		}
	}
}

return YITH_YWGC_Gift_Cards_Post_Type_Admin::instance();
