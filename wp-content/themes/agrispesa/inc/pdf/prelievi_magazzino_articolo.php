<?php

use Dompdf\Dompdf;

$dataConsegna = $_POST['data_consegna'];
$confezionamento = $_POST['confezionamento'];

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


/*
 *
$args = [
	'posts_per_page' => -1,
	'post_type' => 'product',
	'meta_query' => [
		'relation' => 'AND',
		[
			'key' => '_codice_confezionamento',
			'value' => $confezionamento,
		]
	]
];

$products = new WP_Query($args);
$products = wp_list_pluck($products->posts, 'ID');
$products = reset($products);
$product = wc_get_product($products);

 */

$products = [];

foreach ($orders as $order) {
	$order = wc_get_order($order->ID);
	$items = $order->get_items();
	foreach ($items as $item) {

		$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();

		if (!isset($products[$product_id])) {
			$product = wc_get_product($product_id);

			$products[$product_id] = [];
			$products[$product_id]['orders'] = [];
			$products[$product_id]['quantity'] = 0;
			$products[$product_id]['product'] = $product;
		}

		$itemOrder = clone $order;

		$itemOrder->product_quantity = $item->get_quantity();

		$products[$product_id]['orders'][] = $itemOrder;
		$products[$product_id]['quantity'] += $item->get_quantity();

	}
}

$dompdf = new Dompdf();

ob_start();
?>
	<h1>
		Lista prelievi magazzino per articolo
	</h1>
	<table>
		<thead>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		</thead>
		<tbody>
		<?php foreach ($products as $product): ?>
			<tr>
				<td>
					Articolo: <strong><?php echo $product['product']->get_sku(); ?></strong><br>
					<strong><?php echo $product['product']->get_name(); ?></strong><br><br><br>

					Tot ordinato: <?php echo $product['quantity']; ?>
				</td>
				<td>
					<table>

						<thead>
						<th><b>Nr Documento</b></th>
						<th><b>Tipo Spesa</b></th>
						<th><b>Cliente</b></th>
						<th><b>Cod ubicazione</b></th>
						<th><b>Data Spedizione</b></th>
						<th><b>U.M.</b></th>
						<th><b>Quantità</b></th>
						</thead>
						<tbody>
						<?php foreach ($product['orders'] as $order): ?>
							<tr>
								<td><?php echo $order->get_id(); ?></td>
								<td><?php
									$subscription = get_post_meta($order->get_id(), '_subscription_id', true);
									if ($subscription) {
										$subscription = wcs_get_subscription($subscription);
										$subscription = $subscription->get_items();
										$subscription = reset($subscription)->get_product();
										if ($subscription) {
											$productData = $subscription->get_data();
											echo $productData['name'];
										}
									}
									?></td>
								<td><?php echo $order->get_shipping_first_name() . " " . $order->get_shipping_last_name(); ?></td>
								<td>
									<?php
									$gruppo = get_post_meta($order->get_id(), '_gruppo_consegna', true);
									echo $gruppo;
									?>
								</td>
								<td>
									<?php
									$data = get_post_meta($order->get_id(), '_data_consegna', true);
									echo (new \DateTime($data))->format("d/m/Y");
									?>
								</td>
								<td>PZ</td>
								<td>
									<?php
									echo $order->product_quantity;
									?>
								</td>
							</tr>
						<?php endforeach; ?>
						<tr>
							<td colspan="6"></td>
							<td><?php echo $product['quantity']; ?></td>
						</tr>
						</tbody>
					</table>
				</td>

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php
$content = ob_get_clean();
$dompdf->loadHtml($content);

$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();
