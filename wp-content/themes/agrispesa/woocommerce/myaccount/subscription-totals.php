<?php
/**
 * Subscription totals table
 *
 * @author  Prospress
 * @package WooCommerce_Subscription/Templates
 * @since 1.0.0 - Migrated from WooCommerce Subscriptions v2.2.19
 * @version 1.0.0 - Migrated from WooCommerce Subscriptions v2.6.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<?php
$include_switch_links = true;
$include_item_removal_links = wcs_can_items_be_removed($subscription);
$totals = $subscription->get_order_item_totals();

// Don't display the payment method as it is included in the main subscription details table.
unset($totals['payment_method']);

$hasGratis = false;
$hasDelivery = false;

foreach ($totals as $index => $total) {
	if ($total['label'] == 'Spedizione:' && $total['value'] == 'Gratuita') {
		$hasGratis = true;
	}
	if ($total['label'] == 'Consegna:' && $total['value'] != 'Gratuita') {
		$hasDelivery = true;
	}
}
if ($hasDelivery && $hasGratis) {
	foreach ($totals as $index => $total) {
		if ($total['label'] == 'Spedizione:' && $total['value'] == 'Gratuita') {
			$totals[$index]['label'] = 'Prima consegna:';
		}
		if ($total['label'] == 'Consegna:' && $total['value'] != 'Gratuita') {
			$totals[$index]['label'] = 'Dalla seconda consegna:';
		}
	}
}

$totals['order_total']['value'] = str_replace('every anni', ' / 2 Settimane', $totals['order_total']['value']);
if ($hasDelivery) {
	$totals['order_total']['value'] = ($subscription->get_total() - 5) . '€ / 2 Settimane';

}

?>


<h3 class="my-account--minititle"><?php esc_html_e('Subscription totals', 'woocommerce-subscriptions'); ?></h3>
<br>
<a href="#" class="button change_subscription">Cambia tipo di Facciamo noi</a>
<br><br>
<?php do_action('woocommerce_subscription_totals', $subscription, $include_item_removal_links, $totals, $include_switch_links); ?>
