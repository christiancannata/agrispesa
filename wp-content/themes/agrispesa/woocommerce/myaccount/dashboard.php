<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);

//Get User info
//$current_user = wp_get_current_user();


// Set limit
$limit = 3;

// Get customer $limit last orders
$customer_orders = wc_get_orders(array(
	'customer' => get_current_user_id(),
	'limit' => $limit
));

// Count customers orders
$count = count($customer_orders);

?>


<?php if ($count >= 1) {
	// Message
	echo '<h3 class="my-account--minititle address-title">' . sprintf(_n('Il tuo ultimo ordine', 'I tuoi ultimi %s ordini', $count, 'woocommerce'), $count) . '</h3>';
	?>
	<table
		class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
		<tr>
			<?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr($column_id); ?>">
					<span class="nobr"><?php echo esc_html($column_name); ?></span></th>
			<?php endforeach; ?>
		</tr>
		</thead>

		<tbody>
		<?php
		foreach ($customer_orders as $customer_order) {
			$order = wc_get_order($customer_order); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$item_count = $order->get_item_count() - $order->get_item_count_refunded();
			$isSubscription = get_post_meta($order->get_id(), '_subscription_id', true);

			?>
			<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr($order->get_status()); ?> order">
				<?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
					<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr($column_id); ?>"
						data-title="<?php echo esc_attr($column_name); ?>">
						<?php if (has_action('woocommerce_my_account_my_orders_column_' . $column_id)) : ?>
							<?php do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order); ?>

						<?php elseif ('order-number' === $column_id) : ?>
							<a href="<?php echo esc_url($order->get_view_order_url()); ?>">
								<?php echo esc_html(_x('Ordine #', 'hash before order number', 'woocommerce') . $order->get_order_number()); ?>
							</a>

						<?php elseif ('order-date' === $column_id) : ?>
							<time
								datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></time>

						<?php elseif ('order-status' === $column_id) : ?>
							<?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>

						<?php elseif ('order-total' === $column_id) : ?>
							<?php
							/* translators: 1: formatted order total 2: total order items */
							if (!$isSubscription) {
								echo wp_kses_post(sprintf(_n('%1$s', '%1$s', $item_count, 'woocommerce'), $order->get_formatted_order_total(), $item_count));
							} else {
								echo "In Abbonamento";
							}
							?>

						<?php elseif ('order-actions' === $column_id) : ?>
							<?php
							$actions = wc_get_account_orders_actions($order);

							if (!empty($actions)) {
								foreach ($actions as $key => $action) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
									echo '<a href="' . esc_url($action['url']) . '" class="woocommerce-button button ' . sanitize_html_class($key) . '">' . esc_html($action['name']) . '</a>';
								}
							}
							?>
						<?php endif; ?>
					</td>
				<?php endforeach; ?>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php
} else {
	?>
	<div class="agr-memo">

		<div class="agr-memo--flex">
			<div class="agr-memo--text">
				<h4 class="agr-memo--title">Facciamo noi o scegli tu?</h4>
				<p class="agr-memo--subtitle">
					Può capitare di avere fretta.<br/>
					Per questo prepariamo noi la tua spesa, scegliendo tra i prodotti di stagione più freschi che
					arriveranno a casa tua in una scatola su misura per te.
					<br/><br/>Proviamo?
				</p>
				<a href="<?php echo esc_url(home_url('/')); ?>box/facciamo-noi" class="btn btn-primary agr-memo--button"
				   title="Abbonati alla spesa" class="empty-states--subtitle">Sì, fate voi!</a>
			</div>
			<div class="agr-memo--image">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/box/banner-box.jpg"
					 alt="Facciamo noi o scegli tu?"/>
			</div>
		</div>
	</div>
	<?php
} ?>


<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action('woocommerce_account_dashboard');

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_before_my_account');

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_after_my_account');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
