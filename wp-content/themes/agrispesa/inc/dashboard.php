<?php
add_action(
	'admin_head',
	function () {
		echo "<style type='text/css'>
            #dashboard-widgets .postbox-container {width: 33.333333%;}
            #dashboard-widgets #postbox-container-1 {width: 100% !important;}
            .table-subscription{width: 100%;}
        </style>
        <script>
        jQuery(document).ready(function($){
            $('#select_all').click(function(){
                if($(this).is(':checked')){
                   $(this).closest('table').find('input').prop('checked',true)
                }else{
                   $(this).closest('table').find('input').prop('checked',false)
                }
            })
        })
</script>
        ";
	}
);


add_action("activate_subscription", function ($subscriptionId) {
	$subscription = new WC_Subscription($subscriptionId);
	$subscription->update_status('active');
	$lastOrder = $subscription->get_last_order();
	$order = wc_get_order($lastOrder);
	$order->update_status("completed", "Ordine completato da admin", true);

	update_post_meta($subscriptionId, '_is_working_activation', false);
});
/*
 * Callback #1 function
 * Displays widget content
 */
function abbonamenti_debito_page()
{
	if (isset($_POST['activate_subscriptions'])) {
		$subscriptions = $_POST['subscriptions'];
		foreach ($subscriptions as $subscriptionId) {
			$subscription = new WC_Subscription($subscriptionId);
			$subscription->update_status('active');
			$lastOrder = $subscription->get_last_order();
			$order = wc_get_order($lastOrder);
			$order->update_status("completed", "Ordine completato da admin", true);
		}
	}

	$enabledSubscription = [];

	$subscriptions = wcs_get_subscriptions(['subscriptions_per_page' => -1, 'subscription_status' => ['on-hold', 'cancelled']]);
	foreach ($subscriptions as $subscription) {
		$hasWallet = get_user_meta($subscription->get_customer_id(), '_saldo_navision', true);
		if (!$hasWallet) {
			continue;
		}

		$hasWallet = str_replace(",", ".", $hasWallet);
		$hasWallet = floatval($hasWallet);
		if ($hasWallet < 0) {
			$subscription->wallet = $hasWallet;
			$enabledSubscription[] = $subscription;
		}
	}

	?>
	<form action="/wp-admin/admin.php?page=abbonamenti-debito" method="POST">
		<input type="hidden" name="activate_subscriptions" value="true">
		<table class="table-subscription">
			<thead>
			<th style="width:80px" align="left"><input id="select_all" type="checkbox" style="margin: 0 8px 0 0;">
				<label for="cb-select-all-1" style="font-size:16px;">
					Seleziona tutti
				</label></th>
			<th style="width:300px" align="left">Utente</th>
			<th style="width:100px" align="left">Credito</th>
			<th style="width:300px" align="left">Abbonamento</th>
			</thead>
			<tbody>
			<?php foreach ($enabledSubscription as $subscription): ?>
				<?php

				$isWorkingActivation = get_post_meta($subscription->get_id(), '_is_working_activation', true);
				if (!$isWorkingActivation) {
					$isWorkingActivation = false;
				}
				?>
				<tr>
					<td class="check-column">
						<!--	<?php if ($isWorkingActivation): ?>
							<i>Abilitazione in corso...</i>
						<?php endif; ?>-->
						<input type="checkbox" name="subscriptions[]"
							   value="<?php echo $subscription->get_id() ?>">
					</td>
					<td>
						<?php
						$user = $subscription->get_user();
						echo $user->last_name . ' ' . $user->first_name;
						?>
					</td>
					<td>
						<?php
						echo $subscription->wallet . '€';
						?>
					</td>
					<td>
						<a href="/wp-admin/post.php?post=<?php echo $subscription->get_id(); ?>&action=edit"
						   target="_blank">    <?php
							$products = $subscription->get_items();
							$product = reset($products);
							echo $product['name'];
							?></a>
						<br>
						<span><?php echo $subscription->get_payment_method(); ?></span>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<br><br>
		<button type="submit" class="button-primary">Attiva Abbonamenti</button>
	</form>

	<?php

}


function sospensioni_abbonamento_page()
{

	global $wpdb;

	$today = new DateTime();
	$currentYear = $today->format('Y');
	$nextYear = $today->format('Y') + 1;

	// Recupera sia le sospensioni dell'anno corrente che dell'anno successivo
	$subscriptionsDatabase = $wpdb->get_results(
		"SELECT * FROM wp_postmeta
         WHERE meta_key IN ('disable_weeks_{$currentYear}', 'disable_weeks_{$nextYear}')",
		ARRAY_A
	);

	$subscriptions = [];
	foreach ($subscriptionsDatabase as $record) {
		$subscription = wcs_get_subscription($record['post_id']);
		if (!$subscription) {
			continue;
		}

		$subscriptions[$subscription->get_id()] = [
			'subscription' => $subscription,
			'weeks' => unserialize($record['meta_value']),
			'year' => str_replace('disable_weeks_', '', $record['meta_key']) // Estrae l'anno dalla meta_key
		];
	}

	$currentWeek = $today->format("W");

	$weeksArray = [];

	// Gestisce le settimane dell'anno corrente
	for ($i = $currentWeek; $i <= 52; $i++) {
		$weeksArray[] = [
			'week' => $i,
			'year' => $currentYear,
			'subscriptions' => array_filter($subscriptions, function ($subscription) use ($i, $currentYear) {
				return $subscription['year'] == $currentYear && in_array($i, $subscription['weeks']);
			})
		];
	}

	// Gestisce le settimane dell'anno successivo
	for ($i = 1; $i <= 52; $i++) {
		$weeksArray[] = [
			'week' => $i,
			'year' => $nextYear,
			'subscriptions' => array_filter($subscriptions, function ($subscription) use ($i, $nextYear) {
				return $subscription['year'] == $nextYear && in_array($i, $subscription['weeks']);
			})
		];
	}

	?>
	<h1>Sospensioni Abbonamento</h1>

	<?php
	foreach ($weeksArray as $week):
		if (empty($week['subscriptions'])) continue; // Salta le settimane senza sospensioni
		?>
		<h3>Settimana <?php echo $week['week'] . ' - ' . $week['year']; ?></h3>
		<table class="table-admin-subscriptions">
			<thead>
			<tr>
				<th>Nome Cliente</th>
				<th>Prodotti</th>
				<th>Azioni</th>
			</tr>
			</thead>
			<tbody>
			<?php
			usort($week['subscriptions'], function ($a, $b) {
				return strcmp($a['subscription']->get_shipping_last_name(), $b['subscription']->get_shipping_last_name());
			});

			foreach ($week['subscriptions'] as $subscription):
				$items = $subscription['subscription']->get_items();
				?>
				<tr>
					<td>
						<?php echo $subscription['subscription']->get_shipping_last_name() . " " . $subscription['subscription']->get_shipping_first_name(); ?>
					</td>
					<td>
						<?php
						foreach ($items as $item) {
							echo $item->get_name() . "<br>";
						}
						?>
					</td>
					<td>
						<a target="_blank"
						   href="/wp-admin/post.php?post=<?php echo $subscription['subscription']->get_id(); ?>&action=edit">
							Vai all'abbonamento
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endforeach; ?>

	<style>
		.table-admin-subscriptions {
			border-collapse: collapse;
			width: 100%;
		}

		.table-admin-subscriptions td, .table-admin-subscriptions th {
			border: 1px solid #ddd;
			padding: 8px;
		}

		.table-admin-subscriptions tr:nth-child(even) {
			background-color: #f2f2f2;
		}

		.table-admin-subscriptions tr:hover {
			background-color: #ddd;
		}

		.table-admin-subscriptions th {
			background-color: #04AA6D;
			color: white;
			padding: 12px;
			text-align: left;
		}
	</style>
	<?php
}

?>
