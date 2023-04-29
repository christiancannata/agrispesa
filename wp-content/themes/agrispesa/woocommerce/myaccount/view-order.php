<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

defined('ABSPATH') || exit;

$notes = $order->get_customer_order_notes();
$orderType = get_post_meta($order->get_id(), '_order_type', true);

?>

<h3 class="my-account--minititle address-title">Ordine #<?php echo $order->get_order_number(); ?></h3>

<p class="my-account--description">
	<?php
	printf(
	/* translators: 1: order number 2: order date 3: order status */
		esc_html__('Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce'),
		'<strong class="order-number">' . $order->get_order_number() . '</strong>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'<strong class="order-date">' . wc_format_datetime($order->get_date_created()) . '</strong>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'<strong class="order-status">' . wc_get_order_status_name($order->get_status()) . '</strong>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
	?>
</p>
<!--
<?php if ($orderType == 'BOX'):
	$shippingDate = get_post_meta($order->get_id(), '_delivery_date', true);
	?>
	<section class="woocommerce-order-details">

		<h2 class="my-account--minititle">Data di consegna</h2>
		<?php if ($shippingDate): ?>
			<span>Il tuo ordine arriverà il <?php echo DateTime::createFromFormat('Y-m-d', $shippingDate)->format('d/m/Y') ?> <b></b></span>
		<?php endif; ?>

	</section>
<?php endif; ?>
 -->
<?php if ($notes) : ?>
	<h2 class="my-account--minititle"><?php esc_html_e('Order updates', 'woocommerce'); ?></h2>
	<ol class="woocommerce-OrderUpdates commentlist notes">
		<?php foreach ($notes as $note) : ?>
			<li class="woocommerce-OrderUpdate comment note">
				<div class="woocommerce-OrderUpdate-inner comment_container">
					<div class="woocommerce-OrderUpdate-text comment-text">
						<p class="woocommerce-OrderUpdate-meta meta"><?php echo date_i18n(esc_html__('l jS \o\f F Y, h:ia', 'woocommerce'), strtotime($note->comment_date)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
						<div class="woocommerce-OrderUpdate-description description">
							<?php echo wpautop(wptexturize($note->comment_content)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
			</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>

<?php do_action('woocommerce_view_order', $order_id); ?>
