<?php

use Dompdf\Dompdf;

$dataConsegna = $_GET['data_consegna'];
$confezionamento = $_GET['confezionamento'];

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

$orders = array_filter($orders, function ($order) use ($product) {
	$order = wc_get_order($order->ID);
	$items = $order->get_items();

	$hasProduct = false;
	foreach ($items as $item_id => $item) {
		$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
		if ($product_id == $product->get_id()) {
			$hasProduct = true;
		}
	}

	return ($hasProduct) ? $order : null;

});


$dompdf = new Dompdf();

ob_start();
?>

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
		<?php foreach ($orders as $order): ?>
			<?php
			$order = wc_get_order($order->ID);

			$numConsegna = get_post_meta($order->get_id(), '_numero_consegna', true);
			$giroConsegna = get_post_meta($order->get_id(), '_giro_consegna', true);
			$items = $order->get_items();
			?>
			<tr>
				<td>
					Nr.Cons.: <strong><?php echo $numConsegna; ?></strong><br>
					Ubicaz.: <strong><?php echo $giroConsegna; ?></strong><br><br>

					Cliente: <strong><?php echo $order->get_customer_id(); ?></strong><br>
					<strong><?php echo $order->get_shipping_first_name() . " " . $order->get_shipping_last_name(); ?></strong><br>
					<span><?php echo $order->get_shipping_address_1() ?></span><br>
					<span><?php echo $order->get_shipping_postcode() . " " . $order->get_shipping_city(); ?> (<?php echo $order->get_shipping_state(); ?>)</span>
				</td>
				<td>
					<?php
					foreach ($items as $item_id => $item) {
						$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
						if ($product_id == $product->get_id()) {
							?>
							<strong><?php echo $product->get_name(); ?></strong>
							<?php
						}
					}
					?>
				</td>
				<td>PZ</td>
				<td>
					<?php
					foreach ($items as $item_id => $item) {
						$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
						if ($product_id == $product->get_id()) {
							?>
							<strong><?php echo $item->get_quantity(); ?></strong>
							<?php
						}
					}
					?>
				</td>
				<td>
					<strong><?php echo $order->get_id(); ?></strong>
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
