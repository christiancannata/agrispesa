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


function sospensioni_abbonamento_page() {
	global $wpdb;

	$today       = new DateTime();
	$currentYear = (int) $today->format('Y');
	$nextYear    = $currentYear + 1;
	$currentWeek = (int) $today->format('W'); // ISO week

	// Prendi entrambe le chiavi (anno corrente + prossimo)
	$meta_keys = [
		"disable_weeks_{$currentYear}",
		"disable_weeks_{$nextYear}",
	];
	$placeholders = implode(',', array_fill(0, count($meta_keys), '%s'));

	$subscriptionsDatabase = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT post_id, meta_key, meta_value
             FROM {$wpdb->postmeta}
             WHERE meta_key IN ($placeholders)",
			$meta_keys
		),
		ARRAY_A
	);

	// Costruiamo una lista di record (NON indicizzata per id, così evitiamo sovrascritture).
	// Ogni record = un abbinamento (subscription, anno, settimane)
	$records = [];

	foreach ($subscriptionsDatabase as $record) {
		$subscription = wcs_get_subscription($record['post_id']);
		if (!$subscription) {
			continue;
		}

		// Decodifica weeks in modo robusto
		$weeks = [];
		$raw   = $record['meta_value'];

		if (is_string($raw) && $raw !== '') {
			// prova JSON
			$decoded = json_decode($raw, true);
			if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
				$weeks = $decoded;
			} else {
				// prova PHP serialize
				$tmp = @unserialize($raw);
				if ($tmp !== false && is_array($tmp)) {
					$weeks = $tmp;
				}
			}
		} elseif (is_array($raw)) {
			$weeks = $raw;
		}

		// normalizza array di interi
		$weeks = array_values(array_unique(array_filter(array_map('intval', $weeks))));
		if (empty($weeks)) {
			continue;
		}

		$year = (int) str_replace('disable_weeks_', '', $record['meta_key']);

		$records[] = [
			'subscription' => $subscription,
			'weeks'        => $weeks,
			'year'         => $year,
		];
	}

	// Costruisci le settimane da mostrare (anno corrente dalla settimana attuale; anno prossimo 1..53)
	$weeksArray = [];

	// limite massimo 53 settimane
	$maxWeeks = 53;

	// anno corrente
	for ($w = $currentWeek; $w <= $maxWeeks; $w++) {
		// filtra i record che hanno quell'anno e contengono quella settimana
		$subs = array_filter($records, function ($rec) use ($w, $currentYear) {
			return ($rec['year'] === $currentYear) && in_array($w, $rec['weeks'], true);
		});

		if (!empty($subs)) {
			// reindicizza e ordina per cognome spedizione
			$subs = array_values($subs);
			usort($subs, function ($a, $b) {
				return strcmp(
					(string) $a['subscription']->get_shipping_last_name(),
					(string) $b['subscription']->get_shipping_last_name()
				);
			});

			$weeksArray[] = [
				'week'          => $w,
				'year'          => $currentYear,
				'subscriptions' => $subs,
			];
		}
	}

	// anno prossimo
	for ($w = 1; $w <= $maxWeeks; $w++) {
		$subs = array_filter($records, function ($rec) use ($w, $nextYear) {
			return ($rec['year'] === $nextYear) && in_array($w, $rec['weeks'], true);
		});

		if (!empty($subs)) {
			$subs = array_values($subs);
			usort($subs, function ($a, $b) {
				return strcmp(
					(string) $a['subscription']->get_shipping_last_name(),
					(string) $b['subscription']->get_shipping_last_name()
				);
			});

			$weeksArray[] = [
				'week'          => $w,
				'year'          => $nextYear,
				'subscriptions' => $subs,
			];
		}
	}

	?>
	<h1>Sospensioni Abbonamento</h1>

	<?php foreach ($weeksArray as $week): ?>
		<?php
		$year = (int) $week['year'];
		$w    = (int) $week['week'];

// Calcolo range settimana ISO
		$monday = new \DateTime();
		$monday->setISODate($year, $w, 1);

		$sunday = new \DateTime();
		$sunday->setISODate($year, $w, 7);
		?>

		<h3>
			Settimana <?php echo $w; ?> - <?php echo $year; ?>
			(dal <?php echo $monday->format('d/m/Y'); ?> al <?php echo $sunday->format('d/m/Y'); ?>)
		</h3>
		<table class="table-admin-subscriptions">
			<thead>
			<tr>
				<th>Nome Cliente</th>
				<th>Prodotti</th>
				<th>Azioni</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($week['subscriptions'] as $rec): ?>
				<?php
				/** @var WC_Subscription $sub */
				$sub   = $rec['subscription'];
				$items = $sub->get_items();
				$name  = trim($sub->get_shipping_last_name() . ' ' . $sub->get_shipping_first_name());
				?>
				<tr>
					<td><?php echo esc_html($name); ?></td>
					<td>
						<?php foreach ($items as $item): ?>
							<?php echo esc_html($item->get_name()); ?><br>
						<?php endforeach; ?>
					</td>
					<td>
						<a target="_blank"
						   href="<?php echo esc_url(admin_url('post.php?post=' . $sub->get_id() . '&action=edit')); ?>">
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
