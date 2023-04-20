<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined('ABSPATH') || exit;

/**
 * My Account navigation.
 *
 * @since 2.6.0
 */

$current_user = wp_get_current_user();


global $current_user;

$user_id = $current_user->ID;
//$postcode = get_user_meta( $current_user->ID, 'shipping_postcode', true );
$registration = $current_user->user_registered;

$reg_day = date('d', strtotime($current_user->user_registered));
$reg_month = date('m', strtotime($current_user->user_registered));
$reg_year = date('Y', strtotime($current_user->user_registered));

// $shipping_date = get_order_delivery_date_from_date(new \DateTime(), null, $postcode);
// echo $shipping_date;

if ($reg_month === '01') {
	$reg_month = 'Gennaio';
} else if ($reg_month === '02') {
	$reg_month = 'Febbraio';
} else if ($reg_month === '03') {
	$reg_month = 'Marzo';
} else if ($reg_month === '04') {
	$reg_month = 'Aprile';
} else if ($reg_month === '05') {
	$reg_month = 'Maggio';
} else if ($reg_month === '06') {
	$reg_month = 'Giugno';
} else if ($reg_month === '07') {
	$reg_month = 'Luglio';
} else if ($reg_month === '08') {
	$reg_month = 'Agosto';
} else if ($reg_month === '09') {
	$reg_month = 'Settembre';
} else if ($reg_month === '10') {
	$reg_month = 'Ottobre';
} else if ($reg_month === '11') {
	$reg_month = 'Novembre';
} else if ($reg_month === '12') {
	$reg_month = 'Dicembre';
}

?>


<header class="client-header">

	<div class="user-profile--header">
		<div class="user-profile--user">
			<h2 class="user-profile--name">
				Ciao<?php if ($current_user->first_name): ?>, <span
					style="text-transform: capitalize;"><?php echo esc_html($current_user->first_name); ?></span><?php endif; ?>
				.</h2>
			<p class="user-profile--registered">Fai parte di Agrispesa
				dal <?php echo $reg_day . ' ' . $reg_month . ' ' . $reg_year; ?> <span class="ec ec-green-heart"></span>
				<span class="ec ec-sparkles"></span></p>

			<?php
			$fido = get_user_meta($current_user->ID, '_saldo_navision', true);
			if ($fido):
				?>
				<span><strong>Saldo:</strong> <?php echo $fido; ?>€ </span>
			<?php endif; ?>
		</div>
		<div class="user-profile--details">

			<?php
			// $date = new DateTime();
			// $currentWeek = $date->format("W");
			//
			// $subscriptions = wcs_get_users_subscriptions($user_id);
			// if (count($subscriptions) > 0) {
			//
			// 	foreach ($subscriptions as $subscription) {
			//
			// 		$orders = wc_get_orders([
			// 			'limit' => 1,
			// 			'status' => ['processing', 'completed'],
			// 			'customer_id' => $user_id,
			// 			'meta_query' => [
			// 				'relation' => 'AND',
			// 				[
			// 					'key' => 'week',
			// 					'compare' => '=',
			// 					'value' => $currentWeek
			// 				],
			// 				[
			// 					'key' => '_subscription_id',
			// 					'compare' => '=',
			// 					'value' => $subscription->get_id()
			// 				]
			// 			],
			// 		]);
			//
			//
			//
			// 		if (count($orders) > 0) {
			// 			$i=0;
			// 			foreach ($orders as $order) {
			//
			// 				$dataConsegna = get_post_meta($order->get_id(), '_data_consegna', true);
			// 				if ($dataConsegna) {
			// 					$origin = new DateTimeImmutable();
			// 					$target = new DateTimeImmutable($dataConsegna);
			// 					$interval = $origin->diff($target);
			// 					echo '<div class="user-profile--details--item next-box">';
			// 					echo '<p class="user-profile--details--number">' . $interval->format('%a') . '</p>';
			// 					echo '<h3 class="user-profile--details--title">Consegna a<br/>'. $order->get_shipping_first_name() .'</h3>';
			// 					echo '</div>';
			// 					$i++;
			// 					if($i==3) break;
			// 				}
			//
			//
			// 			}
			//
			// 		}
			//
			//
			// 	}
			//
			// }
			?>


			<div class="user-profile--details--item subscriptions">
				<?php
				$has_sub = wcs_user_has_subscription($user_id, '', 'active');
				$subscriptions = wcs_get_users_subscriptions($user_id, $has_sub);
				if ($has_sub) {
					$i = 1;
					foreach ($subscriptions as $subscription) {
						if ($subscription->has_status(array('active'))) {
							echo '<p class="user-profile--details--number">' . $i . '</p>';
						}
						$i++;
					}
					if ($i === 1) {
						echo '<h3 class="user-profile--details--title">Facciamo noi<br/>attiva</h3>';
					} else {
						echo '<h3 class="user-profile--details--title">Facciamo noi<br/>attive</h3>';
					}


				} else {
					echo '<p class="user-profile--details--number">0</p>';
					echo '<h3 class="user-profile--details--title">Facciamo noi</h3>';
				}
				?>
			</div>
			<div class="user-profile--details--item">
				<?php
				$args = array(
					'customer_id' => $user_id,
					'limit' => -1,
					'status' => array('wc-completed')
				);
				$orders = wc_get_orders($args);
				echo '<p class="user-profile--details--number">' . count($orders) . '</p>';
				echo '<h3 class="user-profile--details--title">Scatole ricevute<br/>(finora <span class="ec ec-v"></span>)</h3>';
				?>
			</div>
		</div>
	</div>

</header><!-- .page-header -->

<div class="woocommerce-flex">

	<?php do_action('woocommerce_account_navigation'); ?>

	<div class="woocommerce-MyAccount-content">

		<?php
		/**
		 * My Account content.
		 *
		 * @since 2.6.0
		 */
		do_action('woocommerce_account_content');
		?>
	</div>
</div>
