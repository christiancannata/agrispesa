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
			//update_post_meta($orderId, '_is_order_updating', true);
			//as_enqueue_async_action('activate_order', ['orderId' => $orderId]);
			$subscription = new WC_Subscription($subscriptionId);
			$subscription->update_status('active');
			$lastOrder = $subscription->get_last_order();
			$order = wc_get_order($lastOrder);
			$order->update_status("completed", "Ordine completato da admin", true);
			//update_post_meta($orderId, "_is_order_updating", false);
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
				$lastOrder = $subscription->get_last_order();

				//$isWorkingActivation = get_post_meta($lastOrder, '_is_order_updating', true);
				//if (!$isWorkingActivation) {
				//	$isWorkingActivation = false;
				//}
				$isWorkingActivation = false;
				?>
				<tr>
					<td class="check-column">
						<?php if ($isWorkingActivation): ?>
							<i>Abilitazione in corso...</i>
						<?php endif; ?>
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

?>
