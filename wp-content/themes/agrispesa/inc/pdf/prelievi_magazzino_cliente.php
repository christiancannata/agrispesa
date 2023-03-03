<?php

use Dompdf\Dompdf;

$dataConsegna = $_POST['data_consegna'];
$confezionamentoDal = $_POST['confezionamento_dal'];
$confezionamentoAl = $_POST['confezionamento_al'];

$codiciConfezionamento = [];

for ($i = $confezionamentoDal; $i <= $confezionamentoAl; $i++) {
	$codiciConfezionamento[] = $i;
}

$args = [
	'posts_per_page' => -1,
	'post_type' => 'product',
	'meta_query' => [
		'relation' => 'AND',
		[
			'key' => '_codice_confezionamento',
			'value' => $codiciConfezionamento,
			'compare' => 'IN'
		]
	]
];

$products = new WP_Query($args);
$products = wp_list_pluck($products->posts, 'ID');

foreach ($products as $key => $product) {
	$products[$key] = wc_get_product($product);
}

$args = [
	'posts_per_page' => -1,
	'post_type' => 'shop_order',
	'post_status' => ['wc-processing', 'wc-completed'],
	'meta_query' => [
		'relation' => 'AND',
		[
			'key' => '_data_consegna',
			'value' => $dataConsegna,
			'compare' => '='
		]
	]
];
$orders = new WP_Query($args);
$orders = $orders->get_posts();

$orders = array_filter($orders, function ($order) use ($products) {
	$order = wc_get_order($order->ID);
	$items = $order->get_items();

	$hasProduct = false;
	foreach ($items as $item_id => $item) {
		$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();

		$hasProducts = array_filter($products, function ($product) use ($product_id) {
			return $product_id == $product->get_id();
		});

		if (!empty($hasProducts)) {
			$hasProduct = true;
		}
	}

	return ($hasProduct) ? $order : null;

});

$dompdf = new Dompdf();
ob_start();
?>
	<html>
	<head>
		<style>
			.page-break {
				page-break-before: always;
			}

			.table {
				width: 100%;

			}

			.border {
				border: 1px solid;
				border-collapse: collapse;
			}

			.border td {
				border: 1px solid;
				border-collapse: collapse;
				padding: 5px;
			}
		</style>
	</head>
	<body>

	<h1>
		Lista prelievi magazzino per cliente
	</h1>
	<table class="table border">

		<?php foreach ($orders as $order): ?>
			<?php
			$order = wc_get_order($order->ID);
			$dataConsegna = get_post_meta($order->get_id(), '_data_consegna', true);
			$numConsegna = get_post_meta($order->get_id(), '_numero_consegna', true);
			$gruppoConsegna = get_post_meta($order->get_id(), '_gruppo_consegna', true);
			$subscriptionId = get_post_meta($order->get_id(), '_subscription_id', true);
			$subscription = wcs_get_subscription($subscriptionId);
			$productSubscription = $subscription->get_items();
			$productSubscription = reset($productSubscription);

			$items = $order->get_items();

			?>
			<tr>
				<td>
					Nr.Cons.: <strong><?php echo $numConsegna; ?></strong><br>
					Ubicaz.: <strong><?php echo $gruppoConsegna; ?></strong><br><br>

					Cliente: <strong><?php echo $order->get_customer_id(); ?></strong><br>
					<strong><?php echo $order->get_shipping_first_name() . " " . $order->get_shipping_last_name(); ?></strong><br>
					<span><?php echo $order->get_shipping_address_1() ?></span><br>
					<span><?php echo $order->get_shipping_postcode() . " " . $order->get_shipping_city(); ?> (<?php echo $order->get_shipping_state(); ?>)</span><br>
					Abbonamento cliente: <span><?php echo $productSubscription->get_name(); ?></span>
				</td>
				<td>
					<table class="table">
						<thead>
						<th>Articolo/Produttore</th>
						<th>U.M.</th>
						<th>Quantità</th>
						<th>Nr. Ordine</th>
						<th>Tipo Spesa</th>
						<th>Cod Ubicazione</th>
						<th>Data Spedizione</th>
						</thead>
						<tbody>
						<?php
						foreach ($items as $item_id => $item) {
							$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
							$product = array_filter($products, function ($product) use ($product_id) {
								return $product_id == $product->get_id() ? $product : null;
							});

							if (!$product) {
								continue;
							}

							$product = reset($product);

							$unitaMisura = 'gr';
							$measureUnit = get_post_meta($product->get_id(), '_woo_uom_input', true);
							if (!empty($measureUnit)) {
								$unitaMisura = $measureUnit;
							}

							?>
							<tr>

								<td>
									<strong><?php echo $product->get_name(); ?></strong>
								</td>
								<td><strong><?php echo $unitaMisura; ?></strong></td>

								<td>
									<strong><?php echo $item->get_quantity(); ?></strong>
								</td>
								<td>
									<strong><?php echo $order->get_id(); ?></strong>

								</td>
								<td>
									<strong><?php echo $productSubscription->get_name(); ?></strong>
								</td>
								<td>
									<strong><?php echo $gruppoConsegna; ?></strong>
								</td>
								<td>
									<strong><?php echo (new \DateTime($dataConsegna))->format("d/m/Y"); ?></strong>
								</td>


							</tr>
							<?php

						}
						?>
						</tbody>
					</table>

				</td>

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	</body>
	</html>
<?php
$content = ob_get_clean();
$dompdf->loadHtml($content);

$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('prelievi_magazzino_cliente.pdf');
