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

$products = [];

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
foreach ($orders as $order) {
	$order = wc_get_order($order->ID);
	$items = $order->get_items();
	foreach ($items as $item) {
		$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();

		if (!isset($products[$product_id])) {
			$product = wc_get_product($product_id);

			$products[$product_id] = [];
			$products[$product_id]['orders'] = [];
			$products[$product_id]['gruppi_consegna'] = [];
			$products[$product_id]['quantity'] = 0;
			$products[$product_id]['product'] = $product;
		}

		$gruppoConsegna = get_post_meta($order->get_id(), '_gruppo_consegna', true);

		$products[$product_id]['orders'][] = $order;
		$products[$product_id]['gruppi_consegna'][] = $gruppoConsegna;
		$products[$product_id]['quantity'] += $item->get_quantity();
	}
}

$dompdf = new Dompdf();

ob_start();
?>
	<h1>
		Fabbisogno
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

				</td>
				<td>PZ</td>
				<td>

				</td>
				<td>

				</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
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
