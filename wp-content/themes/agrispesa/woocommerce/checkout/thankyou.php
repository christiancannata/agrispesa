<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined('ABSPATH') || exit;

$next_week = date("LYmd", strtotime("+1 week"));
$next_week_weekday = date("l", strtotime("+1 week"));
$next_week_day = date("d", strtotime("+1 week"));
$next_week_month = date("m", strtotime("+1 week"));
$next_week_year = date("Y", strtotime("+1 week"));

$box_in_order = false;
$items = $order->get_items();
foreach ($items as $item) {
	$product_id = $item->get_product_id();
	if (has_term('box', 'product_cat', $product_id) || has_term('negozio', 'product_cat', $product_id)) {
		$box_in_order = true;
		break;
	}
}
if ($box_in_order) {

	$deliveryDate = get_order_delivery_date_from_date(new \DateTime(), null, $order->get_shipping_postcode());

	//print_r($deliveryDate);
//$shipping_date = get_order_delivery_date_from_date(new \DateTime(), null, $order->get_shipping_postcode())->format("l d M Y");
	$shipping_date_weekday = ($deliveryDate) ? $deliveryDate->format("l") : '';
	$shipping_date_year = ($deliveryDate) ? $deliveryDate->format("Y") : '';
	$shipping_date_month = ($deliveryDate) ? $deliveryDate->format("m") : '';
	$shipping_date_day = ($deliveryDate) ? $deliveryDate->format("d") : '';

	$shipping_month_it = '';
	if ($shipping_date_month === '01') {
		$shipping_month_it = 'Gennaio';
	} else if ($shipping_date_month === '02') {
		$shipping_month_it = 'Febbraio';
	} else if ($shipping_date_month === '03') {
		$shipping_month_it = 'Marzo';
	} else if ($shipping_date_month === '04') {
		$shipping_month_it = 'Aprile';
	} else if ($shipping_date_month === '05') {
		$shipping_month_it = 'Maggio';
	} else if ($shipping_date_month === '06') {
		$shipping_month_it = 'Giugno';
	} else if ($shipping_date_month === '07') {
		$shipping_month_it = 'Luglio';
	} else if ($shipping_date_month === '08') {
		$shipping_month_it = 'Agosto';
	} else if ($shipping_date_month === '09') {
		$shipping_month_it = 'Settembre';
	} else if ($shipping_date_month === '10') {
		$shipping_month_it = 'Ottobre';
	} else if ($shipping_date_month === '11') {
		$shipping_month_it = 'Novembre';
	} else if ($shipping_date_month === '12') {
		$shipping_month_it = 'Dicembre';
	}
	$shipping_weekday_it = '';
	if ($shipping_date_weekday === 'Monday') {
		$shipping_weekday_it = 'Lunedì';
	} else if ($shipping_date_weekday === 'Tuesday') {
		$shipping_weekday_it = 'Martedì';
	} else if ($shipping_date_weekday === 'Wednesday') {
		$shipping_weekday_it = 'Mercoledì';
	} else if ($shipping_date_weekday === 'Thursday') {
		$shipping_weekday_it = 'Giovedì';
	} else if ($shipping_date_weekday === 'Friday') {
		$shipping_weekday_it = 'Venerdì';
	} else if ($shipping_date_weekday === 'Saturday') {
		$shipping_weekday_it = 'Sabato';
	} else if ($shipping_date_weekday === 'Sunday') {
		$shipping_weekday_it = 'Domenica';
	}
}
//Giorni
$weekday_it = '';
if ($next_week_weekday === 'Monday') {
	$weekday_it = 'Lunedì';
} else if ($next_week_weekday === 'Tuesday') {
	$weekday_it = 'Martedì';
} else if ($next_week_weekday === 'Wednesday') {
	$weekday_it = 'Mercoledì';
} else if ($next_week_weekday === 'Thursday') {
	$weekday_it = 'Giovedì';
} else if ($next_week_weekday === 'Friday') {
	$weekday_it = 'Venerdì';
} else if ($next_week_weekday === 'Saturday') {
	$weekday_it = 'Sabato';
} else if ($next_week_weekday === 'Sunday') {
	$weekday_it = 'Domenica';
}

//Mesi
$month_it = '';
if ($next_week_month === '01') {
	$month_it = 'Gennaio';
} else if ($next_week_month === '02') {
	$month_it = 'Febbraio';
} else if ($next_week_month === '03') {
	$month_it = 'Marzo';
} else if ($next_week_month === '04') {
	$month_it = 'Aprile';
} else if ($next_week_month === '05') {
	$month_it = 'Maggio';
} else if ($next_week_month === '06') {
	$month_it = 'Giugno';
} else if ($next_week_month === '07') {
	$month_it = 'Luglio';
} else if ($next_week_month === '08') {
	$month_it = 'Agosto';
} else if ($next_week_month === '09') {
	$month_it = 'Settembre';
} else if ($next_week_month === '10') {
	$month_it = 'Ottobre';
} else if ($next_week_month === '11') {
	$month_it = 'Novembre';
} else if ($next_week_month === '12') {
	$month_it = 'Dicembre';
}


?>


<div class="woocommerce-order">

	<?php
	if ($order) :

		do_action('woocommerce_before_thankyou', $order->get_id());
		?>

		<?php if ($order->has_status('failed')) : ?>

		<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?></p>

		<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
			<a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"
			   class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
			<?php if (is_user_logged_in()) : ?>
				<a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
				   class="button pay"><?php esc_html_e('My account', 'woocommerce'); ?></a>
			<?php endif; ?>
		</p>

	<?php else : ?>

		<div class="thankyou">
			<div class="thankyou--intro">
				<h1 class="thankyou--title">
					Grazie, <span
						style="text-transform: capitalize;"><?php echo $order->get_billing_first_name(); ?></span>!
					<br/>Il tempo di raccogliere,<br class="only-desktop"/> e siamo da te.</h1>
				<p class="thankyou--subtitle">Riceverai presto una mail con i dettagli del tuo ordine.</p>
				<div class="thankyou--details">
					<?php if ($box_in_order && $shipping_weekday_it): ?>
						<div class="thankyou--details--item">
							<span class="icon-consegna"></span>
							<div class="thankyou--details--text">
								<h3 class="thankyou--details--title">Consegniamo la tua scatola</h3>
								<p class="thankyou--details--info">
									<?php echo $shipping_weekday_it . ', ' . $shipping_date_day . ' ' . $shipping_month_it . ' ' . $shipping_date_year; ?>
								</p>
							</div>
						</div>
					<?php endif; ?>
					<div class="thankyou--details--item">
						<span class="icon-ordine"></span>
						<div class="thankyou--details--text">
							<h3 class="thankyou--details--title">Sul tuo ordine</h3>
							<p class="thankyou--details--info"><?php echo 'È il numero #' . $order->get_order_number(); ?>
								— grazie!</p>
						</div>
					</div>
					<?php if ($box_in_order): ?>
						<div class="thankyou--details--item">
							<span class="icon-indirizzo"></span>
							<div class="thankyou--details--text">
								<h3 class="thankyou--details--title">Indirizzo di consegna</h3>
								<p class="thankyou--details--info"><?php echo $order->get_shipping_address_1() . '<br/>' . $order->get_shipping_postcode() . ' ' . $order->get_shipping_city(); ?></p>
							</div>
						</div>
					<?php endif; ?>
				<!--	<div class="thankyou--details--item">
						<span class="icon-totale"></span>
						<div class="thankyou--details--text">
							<h3 class="thankyou--details--title">Totale</h3>
							<p class="thankyou--details--info">Hai
								pagato <?php echo $order->get_formatted_order_total(); ?>
								<br/>
								Tramite <?php echo wp_kses_post($order->get_payment_method_title()); ?>
							</p>
						</div>
					</div>-->
					<div class="thankyou--details--item">

						<?php
						/*
						if ($box_in_order) {
							echo '<span class="icon-prossimo-pagamento"></span>';
							echo '<div class="thankyou--details--text">';
							echo '<h3 class="thankyou--details--title">Prossimo pagamento</h3>';
							echo '<p class="thankyou--details--info">' . $weekday_it . ', ' . $next_week_day . ' ' . $month_it . ' ' . $next_week_year . '</p>';
							echo '</div>';
						} else {
							echo '<span class="icon-email"></span>';
							echo '<div class="thankyou--details--text">';
							echo '<h3 class="thankyou--details--title">Controlla la tua mail</h3>';
							echo $order->get_billing_email();
							echo '</div>';
						} */ ?>

						<?php
						echo '<span class="icon-email"></span>';
						echo '<div class="thankyou--details--text">';
						echo '<h3 class="thankyou--details--title">Controlla la tua mail</h3>';
						echo $order->get_billing_email();
						echo '</div>';
						?>

					</div>
					<div class="thankyou--details--item buttons">
						<a href="<?php echo esc_url(home_url('/')); ?>bacheca" title="Vai alla tua bacheca"
						   class="btn btn-primary">
							Vai alla tua bacheca
						</a>
					</div>
				</div>
			</div>
			<div class="thankyou--image">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/elements/thank-you.svg"/>
			</div>
		</div>

	<?php endif; ?>
		<div class="thankyou--messages">
			<?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
			<?php do_action('woocommerce_thankyou', $order->get_id()); ?>
		</div>

	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters('woocommerce_thankyou_order_received_text', esc_html__('Thank you. Your order has been received.', 'woocommerce'), null); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

	<?php endif; ?>

</div>
