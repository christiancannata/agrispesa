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

add_action('wp_dashboard_setup', 'prefix_add_dashboard_widget');
function prefix_add_dashboard_widget()
{
	wp_add_dashboard_widget(
		'misha_dashboard_widget', // widget ID
		'Abbonamenti da attivare', // widget title
		'misha_dashboard_widget', // callback #1 to display it
		'misha_process_my_dashboard_widget' // callback #2 for settings
	);
}

add_action("activate_subscription", function ($subscriptionId) {
	$subscription = new WC_Subscription($subscriptionId);
	$subscription->update_status('active');
});
/*
 * Callback #1 function
 * Displays widget content
 */
function misha_dashboard_widget()
{
	if (isset($_POST['activate_subscriptions'])) {
		$subscriptionsIds = $_POST['subscriptions'];
		foreach ($subscriptionsIds as $subscriptionId) {
			$subscription = new WC_Subscription($subscriptionId);
			$subscription->update_status('active');
			//as_enqueue_async_action('activate_subscription', ['subscriptionId' => $subscriptionId]);
		}

	}

	$enabledSubscription = [];

	$subscriptions = wcs_get_subscriptions(['subscriptions_per_page' => -1, 'subscription_status' => ['on-hold']]);
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
	<form action="/wp-admin/index.php" method="POST">
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
				<tr>
					<td class="check-column"><input type="checkbox" name="subscriptions[]"
													value="<?php echo $subscription->get_id(); ?>"></td>
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
						<?php
						$products = $subscription->get_items();
						$product = reset($products);
						echo $product['name'];
						?>
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

/*
 * Callback #2 function
 * This function displays your widget settings
 */
function misha_process_my_dashboard_widget()
{


}
