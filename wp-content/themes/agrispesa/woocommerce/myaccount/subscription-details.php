<?php
/**
 * Subscription details table
 *
 * @author  Prospress
 * @package WooCommerce_Subscription/Templates
 * @since 1.0.0 - Migrated from WooCommerce Subscriptions v2.2.19
 * @version 1.0.0 - Migrated from WooCommerce Subscriptions v2.6.5
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
global $current_user;
$user_id = $current_user->ID;
?>
<table class="shop_table subscription_details">

	<?php
	$subscription_coupons = $subscription->get_coupon_codes();
	if (in_array('welovedenso', $subscription_coupons)) {
		$coupon_class = 'class="remove-me"';
	} else {
		$coupon_class = '';
	}

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
	/*if($current_date < $deliveryDate) {
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

	}*/

	$nextThursday = getNextLimitDate();
	?>

	<?php if (in_array('welovedenso', $subscription_coupons) || in_array('WELOVEDENSO', $subscription_coupons)): ?>
		<div class="top_banner">
			<h3 class="top_banner--title">We love Denso.</h3>
			<p class="top_banner--subtitle">Consegniamo gratuitamente nella tua azienda.</p>
			<div class="top_banner--logos">
			<span class="agrispesa">
				<?php get_template_part('global-elements/logo', 'open'); ?>
				<?php bloginfo('name'); ?>
			</span>
				<span class="per">
				per
			</span>
				<span class="denso">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/partner/denso.png" alt="Denso"/>
			</span>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($subscription->get_status() == 'active'): ?>
		<?php //Se la data di consegna è più avanti di oggi
		if ($current_date > $deliveryDate):?>
			<!--
	<div class="top_banner">
		<div class="top_banner--flex">
			<div class="top_banner--text">
				<?php if ($days_until > 3): ?>
					<h3 class="top_banner--title">Agrispesa si sta preparando!</h3>
				<?php else: ?>
					<h3 class="top_banner--title">Agrispesa sta arrivando!</h3>
				<?php endif; ?>
				<?php if ($days_until > 1): ?>
					<p class="top_banner--subtitle">Il corriere busserà alla tua porta tra <?php echo $days_until; ?> giorni.</p>
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
	</div> -->

			<div class="top_banner orange m-t">
				<p class="top_banner--advice">Sospendi l'abbonamento <strong>entro </strong>
					se non vuoi ricevere questa spesa.</p>
			</div>
		<?php
		//Se la data di consegna è più indietro di oggi
		else: ?>
			<!--
		<div class="top_banner">
			<div class="top_banner--flex">
				<div class="top_banner--text">
					<?php if ($days_until > 3): ?>
						<h3 class="top_banner--title">Agrispesa si sta preparando!</h3>
					<?php else: ?>
						<h3 class="top_banner--title">Agrispesa sta arrivando!</h3>
					<?php endif; ?>
					<?php if ($days_until > 1): ?>
						<p class="top_banner--subtitle">Il corriere busserà alla tua porta tra <?php echo $days_until; ?> giorni.</p>
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
		</div> -->

			<div class="top_banner orange m-t">
				<p class="top_banner--advice">Sospendi l'abbonamento
					<strong>entro <?php echo getLabelDay($nextThursday); ?>
						ore <?php echo $nextThursday->format("H"); ?></strong>
					se non vuoi ricevere questa scatola.</p>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<div class="top_banner">
			<p class="top_banner--advice">Riattiva <strong>entro <?php echo getLabelDay($nextThursday); ?>
					ore <?php echo $nextThursday->format("H"); ?></strong> se vuoi ricevere
				la scatola della prossima settimana.</p>
		</div>
	<?php endif; ?>

	<tbody>
	<tr>
		<td><?php esc_html_e('Status', 'woocommerce-subscriptions'); ?></td>
		<td><?php echo esc_html(wcs_get_subscription_status_name($subscription->get_status())); ?></td>
	</tr>
	<?php do_action('wcs_subscription_details_table_before_dates', $subscription); ?>
	<?php
	$dates_to_display = apply_filters('wcs_subscription_details_table_dates_to_display', array(
		'start_date' => _x('Start date', 'customer subscription table header', 'woocommerce-subscriptions'),
		'last_order_date_created' => _x('Last order date', 'customer subscription table header', 'woocommerce-subscriptions'),
		'next_payment' => _x('Next payment date', 'customer subscription table header', 'woocommerce-subscriptions'),
		'end' => _x('End date', 'customer subscription table header', 'woocommerce-subscriptions'),
		'trial_end' => _x('Trial end date', 'customer subscription table header', 'woocommerce-subscriptions'),
	), $subscription);
	foreach ($dates_to_display as $date_type => $date_title) : ?>
		<?php $date = $subscription->get_date($date_type); ?>
		<?php if (!empty($date)) : ?>
			<tr>
				<td><?php echo esc_html($date_title); ?></td>
				<td><?php echo esc_html($subscription->get_date_to_display($date_type)); ?></td>
			</tr>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php do_action('wcs_subscription_details_table_after_dates', $subscription); ?>
	<?php if (WCS_My_Account_Auto_Renew_Toggle::can_user_toggle_auto_renewal($subscription)) : ?>
		<tr>
			<td><?php esc_html_e('Auto renew', 'woocommerce-subscriptions'); ?></td>
			<td>
				<div class="wcs-auto-renew-toggle">
					<?php

					$toggle_classes = array('subscription-auto-renew-toggle', 'subscription-auto-renew-toggle--hidden');

					if ($subscription->is_manual()) {
						$toggle_label = __('Enable auto renew', 'woocommerce-subscriptions');
						$toggle_classes[] = 'subscription-auto-renew-toggle--off';

						if (WCS_Staging::is_duplicate_site()) {
							$toggle_classes[] = 'subscription-auto-renew-toggle--disabled';
						}
					} else {
						$toggle_label = __('Disable auto renew', 'woocommerce-subscriptions');
						$toggle_classes[] = 'subscription-auto-renew-toggle--on';
					} ?>
					<a href="#" class="<?php echo esc_attr(implode(' ', $toggle_classes)); ?>"
					   aria-label="<?php echo esc_attr($toggle_label) ?>"><i class="subscription-auto-renew-toggle__i"
																			 aria-hidden="true"></i></a>
					<?php if (WCS_Staging::is_duplicate_site()) : ?>
						<small
							class="subscription-auto-renew-toggle-disabled-note"><?php echo esc_html__('Using the auto-renewal toggle is disabled while in staging mode.', 'woocommerce-subscriptions'); ?></small>
					<?php endif; ?>
				</div>
			</td>
		</tr>
	<?php endif; ?>
	<?php do_action('wcs_subscription_details_table_before_payment_method', $subscription); ?>
	<?php if ($subscription->get_time('next_payment') > 0) : ?>
		<tr>
			<td><?php esc_html_e('Payment', 'woocommerce-subscriptions'); ?></td>
			<td>
				<span data-is_manual="<?php echo esc_attr(wc_bool_to_string($subscription->is_manual())); ?>"
					  class="subscription-payment-method"><?php echo esc_html($subscription->get_payment_method_to_display('customer')); ?></span>
			</td>
		</tr>
	<?php endif; ?>
	<?php do_action('woocommerce_subscription_before_actions', $subscription); ?>
	<?php $actions = wcs_get_all_user_actions_for_subscription($subscription, get_current_user_id()); ?>
	<?php if (!empty($actions)) :
		?>
		<tr>
			<td><?php esc_html_e('Actions', 'woocommerce-subscriptions'); ?></td>
			<td <?php echo $coupon_class; ?>>

				<?php

				//remove renew when subscription is active
				if ($subscription->get_status() == 'active') {
					unset($actions['subscription_renewal_early']);
				}

				foreach ($actions as $key => $action) :
					if ($action['name'] == 'Elimina') {
						$action['name'] = 'Sospendi';
					}
					?>
					<a href="<?php echo esc_url($action['url']); ?>"
					   class="button <?php echo sanitize_html_class($key) ?>"><?php echo esc_html($action['name']); ?></a>
				<?php endforeach; ?>
			</td>
		</tr>
	<?php endif; ?>
	<?php do_action('woocommerce_subscription_after_actions', $subscription); ?>
	</tbody>
</table>

<?php if ($notes = $subscription->get_customer_order_notes()) : ?>
	<h2><?php esc_html_e('Subscription updates', 'woocommerce-subscriptions'); ?></h2>
	<ol class="woocommerce-OrderUpdates commentlist notes">
		<?php foreach ($notes as $note) : ?>
			<li class="woocommerce-OrderUpdate comment note">
				<div class="woocommerce-OrderUpdate-inner comment_container">
					<div class="woocommerce-OrderUpdate-text comment-text">
						<p class="woocommerce-OrderUpdate-meta meta"><?php echo esc_html(date_i18n(_x('l jS \o\f F Y, h:ia', 'date on subscription updates list. Will be localized', 'woocommerce-subscriptions'), wcs_date_to_time($note->comment_date))); ?></p>
						<div class="woocommerce-OrderUpdate-description description">
							<?php echo wp_kses_post(wpautop(wptexturize($note->comment_content))); ?>
						</div>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
			</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>
