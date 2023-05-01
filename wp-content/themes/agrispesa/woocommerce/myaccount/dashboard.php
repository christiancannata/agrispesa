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
$current_user = wp_get_current_user();

// Count customers orders
$has_sub = wcs_user_has_subscription('', '', 'active');

$date = new DateTime();
$date->modify("+1 week");
$week = $date->format("W");
$currentWeek = str_pad($week, 2, 0, STR_PAD_LEFT);

$customer_orders = wc_get_orders([
	"limit" => 1,
	"status" => ["completed"],
	"customer" => get_current_user_id(),
	"meta_key" => '_week',
	"meta_value" => $currentWeek,
	"meta_compare" => "=",
]);

dd($customer_orders);

$count = count($customer_orders);


$subscriptions = wcs_get_users_subscriptions($current_user->ID);

foreach ($subscriptions as $subscription) {


	//Prendi il cap della spedizione, se non esiste quello della fatturazione
	$subscription_postcode = $subscription->get_billing_postcode();
	if ($subscription->get_shipping_postcode()) {
		$subscription_postcode = $subscription->get_shipping_postcode();
	} else {
		$subscription_postcode = $subscription->get_billing_postcode();
	}


//Robe di date di consegna
	$current_date = date('Y-m-d');
//$current_date_hour = date('Y-m-d H:i:s' );
	$current_date_hour = new DateTime("now", new DateTimeZone("Europe/Rome"));
	$current_date_hour->format('d-m-Y H:i:s');

	$deliveryDate = get_order_delivery_date_from_date(new \DateTime(), null, $subscription_postcode);

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

	$next_date = "";
	$thurs_next_date = "";
	$shipping_weekday_it = '';
	if ($shipping_date_weekday === 'Monday') {
		$shipping_weekday_it = 'Lunedì';
		$next_shipping_weekday_it = 'Lunedì';
		$next_date = new DateTime('next Monday');
		$thurs_next_date = new DateTime('next Monday');

	} else if ($shipping_date_weekday === 'Tuesday') {
		$shipping_weekday_it = 'Martedì';
		$next_shipping_weekday_it = 'Martedì';
		$next_date = new DateTime('next Tuesday');
		$thurs_next_date = new DateTime('next Tuesday');
	} else if ($shipping_date_weekday === 'Wednesday') {
		$weekday_it = 'Mercoledì';
		$shipping_weekday_it = 'Mercoledì';
		$next_shipping_weekday_it = 'Mercoledì';
		$next_date = new DateTime('next Wednesday');
		$thurs_next_date = new DateTime('next Wednesday');
	} else if ($shipping_date_weekday === 'Thursday') {
		$shipping_weekday_it = 'Giovedì';
		$next_shipping_weekday_it = 'Giovedì';
		$next_date = new DateTime('next Thursday');
		$thurs_next_date = new DateTime('next Thursday');
	} else if ($shipping_date_weekday === 'Friday') {
		$next_shipping_weekday_it = 'Venerdì';
		$next_shipping_weekday_it = 'Venerdì';
		$next_date = new DateTime('next Friday');
		$thurs_next_date = new DateTime('next Friday');
	} else if ($shipping_date_weekday === 'Saturday') {
		$shipping_weekday_it = 'Sabato';
		$next_shipping_weekday_it = 'Sabato';
		$next_date = new DateTime('next Saturday');
		$thurs_next_date = new DateTime('next Saturday');
	} else if ($shipping_date_weekday === 'Sunday') {
		$shipping_weekday_it = 'Domenica';
		$next_shipping_weekday_it = 'Domenica';
		$next_date = new DateTime('next Sunday');
		$thurs_next_date = new DateTime('next Sunday');
	}

	$next_shipping_month_it = '';
	if ($next_date->format("m") === '01') {
		$next_shipping_month_it = 'Gennaio';
	} else if ($next_date->format("m") === '02') {
		$next_shipping_month_it = 'Febbraio';
	} else if ($next_date->format("m") === '03') {
		$next_shipping_month_it = 'Marzo';
	} else if ($next_date->format("m") === '04') {
		$next_shipping_month_it = 'Aprile';
	} else if ($next_date->format("m") === '05') {
		$next_shipping_month_it = 'Maggio';
	} else if ($next_date->format("m") === '06') {
		$next_shipping_month_it = 'Giugno';
	} else if ($next_date->format("m") === '07') {
		$next_shipping_month_it = 'Luglio';
	} else if ($next_date->format("m") === '08') {
		$next_shipping_month_it = 'Agosto';
	} else if ($next_date->format("m") === '09') {
		$next_shipping_month_it = 'Settembre';
	} else if ($next_date->format("m") === '10') {
		$next_shipping_month_it = 'Ottobre';
	} else if ($next_date->format("m") === '11') {
		$next_shipping_month_it = 'Novembre';
	} else if ($next_date->format("m") === '12') {
		$next_shipping_month_it = 'Dicembre';
	}

//Quanti giorni mancano alla consegna?
	if ($current_date < $deliveryDate) {
		$date1 = new DateTime($current_date);
		$date2 = $deliveryDate;
		$diff = $date2->diff($date1)->format("%a");
		$days_until = intval($diff);

		$previousThursday = $deliveryDate->modify("Thursday ago")->format('M d, Y');

	} else {
		$date1 = new DateTime($current_date);
		$date2 = $next_date;
		$diff = $date2->diff($date1)->format("%a");
		$days_until = intval($diff);

		$previousThursday = $thurs_next_date->modify("Thursday ago")->format('M d, Y');

	}

}


?>

<?php if ($has_sub):
	foreach ($subscriptions as $subscription):

		if ($subscription->get_status() == 'active'):?>

			<?php //Se la data di consegna è più avanti di oggi
			if ($current_date > $deliveryDate):?>

				<div class="top_banner">
					<div class="top_banner--flex">
						<div class="top_banner--text">
							<?php if ($days_until > 3): ?>
								<h3 class="top_banner--title">Agrispesa si sta preparando!</h3>
							<?php else: ?>
								<h3 class="top_banner--title">Agrispesa sta arrivando!</h3>
							<?php endif; ?>
							<?php if ($days_until > 1): ?>
								<p class="top_banner--subtitle">Il corriere busserà alla tua porta
									tra <?php echo $days_until; ?> giorni.</p>
							<?php else: ?>
								<p class="top_banner--subtitle">Il corriere arriverà oggi. Iu-uh!</p>
							<?php endif; ?>

						</div>
						<div class="top_banner--calendar">
							<div class="mini-cal">
								<span class="mini-cal--weekday"><?php echo $next_shipping_weekday_it; ?></span>
								<span class="mini-cal--day"><?php echo $next_date->format("d"); ?></span>
								<span class="mini-cal--month"><?php echo $next_shipping_month_it; ?></span>
							</div>
						</div>
					</div>
				</div>

			<?php
			//Se la data di consegna è più indietro di oggi
			else: ?>

				<div class="top_banner">
					<div class="top_banner--flex">
						<div class="top_banner--text">
							<?php if ($days_until > 3): ?>
								<h3 class="top_banner--title">Agrispesa si sta preparando!</h3>
							<?php else: ?>
								<h3 class="top_banner--title">Agrispesa sta arrivando!</h3>
							<?php endif; ?>
							<?php if ($days_until > 1): ?>
								<p class="top_banner--subtitle">Il corriere busserà alla tua porta
									tra <?php echo $days_until; ?> giorni.</p>
							<?php else: ?>
								<p class="top_banner--subtitle">Il corriere arriverà oggi. Iu-uh!</p>
							<?php endif; ?>

						</div>
						<div class="top_banner--calendar">
							<div class="mini-cal">
								<span class="mini-cal--weekday"><?php echo $shipping_weekday_it; ?></span>
								<span class="mini-cal--day"><?php echo $shipping_date_day; ?></span>
								<span class="mini-cal--month"><?php echo $shipping_month_it; ?></span>
							</div>
						</div>
					</div>
				</div>

			<?php endif;endif; endforeach; ?>

	<div class="account-get-products">
		<h3 class="account-get-products--title">Aggiungi prodotti alla tua scatola</h3>
		<div class="account-get-products--loop">
			<?php
			$args = array(
				'limit' => '8',
				'orderby' => array('meta_value_num' => 'DESC', 'title' => 'ASC'),
				'product_cat' => 'Negozio',
				'meta_key' => 'total_sales',
			);

			$query = new WC_Product_Query($args);
			$products = $query->get_products();
			if ($products): ?>
				<?php foreach ($products as $product):
					$product = wc_get_product($product->get_id());
					//print_r($product);
					$thumb_id = get_post_thumbnail_id($product->get_id());
					$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
					$thumb_url = $thumb_url_array[0];
					// unità di misura personalizzata
					$product_data = $product->get_meta('_woo_uom_input');
					?>
					<article class="product-box">
						<a href="<?php the_permalink($product->get_id()); ?>" class="product-box--link"
						   title="<?php echo $product->get_name(); ?>">
							<?php if ($thumb_id): ?>
								<img src="<?php echo $thumb_url; ?>" class="product-box--thumb"
									 alt="<?php echo strip_tags($product->get_name()); ?>"/>
							<?php else: ?>
								<img src="https://agrispesa.it/wp-content/uploads/2023/02/default.png"
									 class="product-box--thumb" alt="<?php echo strip_tags($product->get_name()); ?>"/>
							<?php endif; ?>
						</a>
						<div class="product-box--text">
							<div class="product-box--text--top">
								<h2 class="product-box--title"><a href="<?php the_permalink($product->get_id()); ?>"
																  title="<?php echo $product->get_name(); ?>"><?php echo $product->get_name(); ?></a>
								</h2>
								<div class="product-box--price--flex">

									<?php if ($product->has_weight()) {
										if ($product_data && $product_data != 'gr') {
											echo '<span class="product-info--quantity">' . $product->get_weight() . ' ' . $product_data . '</span>';
										} else {
											if ($product->get_weight() == 1000) {
												echo '<span class="product-info--quantity">1 kg</span>';
											} else {
												echo '<span class="product-info--quantity">' . $product->get_weight() . ' gr</span>';
											}
										}
									} ?>
									<div class="product-box--price">
										<?php echo $product->get_price_html(); ?>
									</div>
								</div>

								<?php echo do_shortcode('[add_to_cart id="' . $product->get_id() . '" show_price="false" class="btn-fake" quantity="1" style="border:none;"]'); ?>
							</div>
						</div>
					</article>

				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>


<?php else: ?>
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
<?php endif; ?>


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
