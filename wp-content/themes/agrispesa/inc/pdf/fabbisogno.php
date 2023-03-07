<?php

use Dompdf\Dompdf;

$dataConsegna = $_POST['data_consegna'];

$groups = get_posts([
	'post_type' => 'delivery-group',
	'post_status' => 'publish',
	'posts_per_page' => -1,
]);
$groups = array_map(function ($group) {
	return [
		'id' => $group->ID,
		'name' => $group->post_title
	];
}, $groups);

$totalProducts = [];

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
$totalOrders = new WP_Query($args);
$totalOrders = $totalOrders->get_posts();

$products = [];


$producers = [];

foreach ($totalOrders as $order) {
	$order = wc_get_order($order->ID);
	$items = $order->get_items();
	$gruppo = get_post_meta($order->get_id(), '_gruppo_consegna', true);

	foreach ($items as $item) {
		$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
		$product = wc_get_product($product_id);

		if (!isset($totalProducts[$product_id])) {
			$productProducers = get_post_meta($product_id, 'product_producer', true);
			if (!is_array($productProducers)) {
				continue;
			}
			$producerObj = get_post(reset($productProducers));

			$totalProducts[$product_id] = [
				'product' => $product,
				'producer' => $producerObj->post_title,
				'product_orders' => [],
				'product_groups' => []
			];
		}

		$item->group = $gruppo;

		$totalProducts[$product_id]['product_orders'][] = $item;


	}

}

// group by gruppo consegna

foreach ($totalProducts as $key => $product) {

	foreach ($groups as $group) {
		$groupOrders = [
			'group_name' => $group['name'],
			'items' => []
		];
		// get all orders of specified group
		foreach ($product['product_orders'] as $orderProduct) {
			if ($orderProduct->group == $group['name']) {
				$groupOrders['items'][] = $orderProduct;
			}
		}

		$totalProducts[$key]['product_groups'][] = $groupOrders;
	}
}

//$fabbisogno = get_fabbisogno();

$dompdf = new Dompdf();

ob_start();
?>
	<html>
	<head>
		<style>
			.page-break {
				page-break-before: always;
			}

			table {
				width: 100%;
				margin-bottom: 30px;
			}

			table tfoot th {
				border-top: 2px solid;
				text-align: left;
				padding-top: 10px;
				margin-top: 20px;
			}

			table thead th {
				text-align: left;
				padding-bottom: 10px;
			}

			table tbody {
				padding-top: 15px;
				margin-top: 15px;
			}
		</style>
	</head>
	<body>
	<h1>
		Fabbisogno
	</h1>
	<?php foreach ($totalProducts as $product): ?>

		<table>
			<thead>
			<th style="width:180px;"><b>SKU</b></th>
			<th style="width:350px;"><b>Descrizione</b></th>
			<th style="width:80px;"><b>Quantità</b></th>
			<th style="width:80px;"><b>UM</b></th>
			<th style="width:80px;"><b> Qta Kg/Pz</b></th>
			<th style="width:80px;"><b>Ubicazione</b></th>
			<th style="width:80px;"><b>Magazzino</b></th>
			</thead>
			<tbody>
			<?php
			$totalOrder = 0;
			$totalQuantity = 0;
			$productObj = $product['product'];

			foreach ($product['product_groups'] as $group): ?>

				<?php

				if (empty($group['items'])) {
					continue;
				}
				$totalGroupQuantity = 0;
				foreach ($group['items'] as $item) {
					$totalGroupQuantity += $item->get_quantity();
					$totalOrder += $item->get_quantity();
				}


				$unitaMisura = 'gr';
				$measureUnit = get_post_meta($productObj->get_id(), '_woo_uom_input', true);

				if (!empty($measureUnit)) {
					$unitaMisura = $measureUnit;
				}

				?>

				<tr>
					<td><?php echo $productObj->get_sku(); ?></td>
					<td><?php echo $productObj->get_name(); ?></td>
					<td><?php echo $totalGroupQuantity; ?></td>
					<td><?php echo $unitaMisura; ?></td>
					<td><?php echo $totalGroupQuantity; ?></td>
					<td><?php echo $group['group_name']; ?></td>
					<td></td>
				</tr>
				<!--
				<?php foreach ($group['items'] as $item) : ?>
					<?php
					/*$totalQuantity += $groupProducts['total_quantity'][$product->get_id()];
					$totalOrder += $groupProducts['total_orders'][$product->get_id()];
*/
					?>

				<?php endforeach; ?>
-->
			<?php endforeach; ?>

			</tbody>
			<tfoot>
			<th style="width:180px;"><b>Totale articolo</b></th>
			<th><b><?php echo $productObj->get_sku(); ?> </b>
			</th>
			<th><?php echo $totalOrder; ?></th>
			<th></th>

			<th><?php echo $totalQuantity; ?></th>
			<th></th>
			<th><?php echo $productObj->get_stock_quantity() + $totalOrder; ?></th>
			</tfoot>
		</table>
	<?php endforeach; ?>
	</body>
	</html>
<?php
$content = ob_get_clean();
$dompdf->loadHtml($content);

$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('fabbisogno.pdf');
