<?php
/**
 * My gift cards
 *
 * @package YITH\GiftCards\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * APPLY_FILTERS: yith_ywgc_my_gift_cards_columns
 *
 * Filter the columns to display in the gift card table in "My account".
 *
 * @param array the columns
 *
 * @return array
 */
$gift_card_columns = apply_filters(
	'yith_ywgc_my_gift_cards_columns',
	array(
		'code'    => esc_html__( 'Code', 'yith-woocommerce-gift-cards' ),
		'balance' => esc_html__( 'Balance', 'yith-woocommerce-gift-cards' ),
		'usage'   => esc_html__( 'Usage', 'yith-woocommerce-gift-cards' ),
		'status'  => esc_html__( 'Status', 'yith-woocommerce-gift-cards' ),
	)
);

$user = wp_get_current_user();

/**
 * APPLY_FILTERS: yith_ywgc_woocommerce_my_account_my_orders_query
 *
 * Filter the query to display in the gift cards table in "My account".
 *
 * @param array the gift cards query
 *
 * @return array
 */
$gift_cards_args = apply_filters(
	'yith_ywgc_woocommerce_my_account_my_orders_query',
	array(
		'numberposts' => - 1,
		'fields'      => 'ids',
		'meta_query'  => array(  // phpcs:ignore WordPress.DB.SlowDBQuery
			'relation' => 'OR',
			array(
				'key'   => YWGC_META_GIFT_CARD_CUSTOMER_USER,
				'value' => get_current_user_id(),
			),
			array(
				'key'   => '_ywgc_recipient',
				'value' => $user->user_email,
			),
		),
		'post_type'   => YWGC_CUSTOM_POST_TYPE_NAME,
		'post_status' => 'any',
	)
);

// Retrieve the gift cards matching the criteria.
$ids = get_posts( $gift_cards_args );




if ( $ids ) : ?>


	<table class="shop_table shop_table_responsive my_account_giftcards">
		<thead>
		<tr>
			<?php foreach ( $gift_card_columns as $column_id => $column_name ) : ?>
				<th class="<?php echo esc_attr( $column_id ); ?>"><span
						class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
			<?php endforeach; ?>
		</tr>
		</thead>

		<tbody>
		<?php
		foreach ( $ids as $gift_card_id ) :

			$gift_card = new YITH_YWGC_Gift_Card( array( 'ID' => $gift_card_id ) );
			( array( 'ID' => $gift_card_id ) );

			if ( ! $gift_card->exists() ) {
				continue;
			}
			?>
			<tr class="ywgc-gift-card status-<?php echo esc_attr( $gift_card->status ); ?>">
				<?php foreach ( $gift_card_columns as $column_id => $column_name ) : ?>
					<td class="<?php echo esc_attr( $column_id ); ?> "
						data-title="<?php echo esc_attr( $column_name ); ?>">

						<?php
						$value = '';
						switch ( $column_id ) {
							case 'code':
								$value = $gift_card->get_code();
								break;
							case 'balance':
								$value = wc_price( apply_filters( 'yith_ywgc_get_gift_card_price', $gift_card->get_balance() ) );
								break;

							case 'status':
								$value       = ywgc_get_status_label( $gift_card );
								$date_format = apply_filters( 'yith_wcgc_date_format', 'Y-m-d' );
								if ( $gift_card->expiration ) {
									/* translators: %1: Date format %1: Date  */
									$value .= '<br>' . sprintf( _x( 'Expires on: %1$s (%2$s)', 'gift card expiration date', 'yith-woocommerce-gift-cards' ), date_i18n( $date_format, $gift_card->expiration ), $date_format );
								}
								break;

							case 'usage':
								$orders = $gift_card->get_registered_orders();

								if ( $orders ) {
									foreach ( $orders as $order_id ) {
										$order_object = wc_get_order( $order_id );
										$user         = wp_get_current_user();

										if ( $user->ID === $order_object->get_customer_id() ) {
											?>
											<a href="<?php echo wc_get_endpoint_url( 'view-order', $order_id ); //phpcs:ignore --wc_functions?>"
											class="ywgc-view-order button">
												<?php
												/* translators: %s: Order_id */
												printf( esc_html__( 'Order %s', 'yith-woocommerce-gift-cards' ), wp_kses( $order_id, 'post' ) );
												?>
											</a><br>
											<?php
										}
									}
								} else {
									esc_html_e( 'The code has not been used yet', 'yith-woocommerce-gift-cards' );
								}
								break;

							default:
								$value = apply_filters( 'yith_ywgc_my_account_column', '', $column_id, $gift_card );
						}

						if ( $value ) {
							echo '<span>' . wp_kses( $value, 'post' ) . '</span>';
						}
						?>

					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<div style="margin-top: 5em">
		<?php
		/**
		 * DO_ACTION: ywgc_empty_table_state_action_customer
		 *
		 * Trigger the empty table state.
		 */
		do_action( 'ywgc_empty_table_state_action_customer' );
		?>
	</div>
<?php endif; ?>
