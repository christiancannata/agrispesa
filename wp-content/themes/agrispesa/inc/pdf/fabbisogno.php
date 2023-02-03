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


$producers = [];

foreach ($orders as $order) {
	$order = wc_get_order($order->ID);
	$items = $order->get_items();
	$gruppo = get_post_meta($order->get_id(), '_gruppo_consegna', true);

	foreach ($items as $item) {
		$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
		$product = wc_get_product($product_id);
		$productProducers = get_post_meta($product_id, 'product_producer', true);

		if (empty($productProducers)) {
			continue;
		}

		foreach ($productProducers as $productProducer) {

			if (!isset($producers[$productProducer])) {

				$producerObj = get_post($productProducer);

				$producers[$productProducer] = [];
				$producers[$productProducer]['producer'] = $producerObj;
				$producers[$productProducer]['groups'] = [];
			}

			if (!isset($producers[$productProducer]['groups'][$gruppo])) {
				$producers[$productProducer]['groups'][$gruppo] = [
					'products' => [],
					'total' => []
				];
			}

			$producers[$productProducer]['groups'][$gruppo]['products'][] = $item;

			if (!isset($producers[$productProducer]['groups'][$gruppo]['total_quantity'][$product_id])) {
				$producers[$productProducer]['groups'][$gruppo]['total_quantity'][$product_id] = 0;
			}

			if (!isset($producers[$productProducer]['groups'][$gruppo]['total_orders'][$product_id])) {
				$producers[$productProducer]['groups'][$gruppo]['total_orders'][$product_id] = 0;
			}

			$producers[$productProducer]['groups'][$gruppo]['total_quantity'][$product_id] += $item->get_quantity();
			$producers[$productProducer]['groups'][$gruppo]['total_orders'][$product_id] += 1;


		}

	}
}


$dompdf = new Dompdf();

ob_start();
?>
	<h1>
		Fabbisogno
	</h1>
<?php foreach ($producers as $producer): ?>
	<table>
		<thead>
		<th><b>Totale articolo</b></th>
		<th></th>
		<th><b><?php echo $producer['producer']->post_title; ?></b></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		</thead>
		<tbody>
		<?php

		foreach ($producer['groups'] as $groupName => $groupProducts): ?>
			<?php foreach ($groupProducts['products'] as $product) : ?>
				<?php
				$product = $product->get_product();
				?>
				<tr>
					<td><?php echo $product->get_sku(); ?></td>
					<td><?php echo $product->get_name(); ?></td>
					<td><?php echo $groupProducts['total_orders'][$product->get_id()] ?></td>
					<td>PZ</td>
					<td><?php echo $groupProducts['total_quantity'][$product->get_id()] ?></td>
					<td><?php echo $groupName; ?></td>
					<td></td>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endforeach; ?>

<?php
$content = ob_get_clean();
$dompdf->loadHtml($content);

$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();
