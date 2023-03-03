<?php

use Dompdf\Dompdf;

$dataConsegna = $_POST['data_consegna'];
$confezionamento = $_POST['confezionamento'];
$week = null;
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
				padding: 20px;
			}
		</style>
	</head>
	<body>
	<?php
	$args = [
		'posts_per_page' => -1,
		'post_type' => 'delivery-group',
		'post_status' => ['publish'],
	];
	$groups = new WP_Query($args);
	$groups = $groups->get_posts();

	foreach ($groups as $group): ?>
		<?php
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
				],
				[
					'key' => '_gruppo_consegna',
					'value' => $group->post_title,
					'compare' => '='
				]
			]
		];
		$orders = new WP_Query($args);
		$orders = wp_list_pluck($orders->posts, 'ID');
		$orders = array_map(function ($order) {
			$order = wc_get_order($order);
			$order->_numero_consegna = get_post_meta($order->get_id(), '_numero_consegna', true);
			$order->secondary_phone = get_post_meta($order->get_id(), '_secondary_phone', true);
			$order->week = get_post_meta($order->get_id(), '_week', true);
			return $order;
		}, $orders);

		$ordinamento = get_post_meta($group->ID, 'ordinamento_numero_consegna', true);

		if (!$ordinamento) {
			$ordinamento = 'CRESCENTE';
		}

		usort($orders, function ($a, $b) {
			return strcmp($a->_numero_consegna, $b->_numero_consegna);
		});

		if ($ordinamento == 'DECRESCENTE') {
			$orders = array_reverse($orders);
		}

		if (count($orders) > 0):
			?>
			<table class="table">
				<td><h5>Nr. Spedizione: </h5></td>
				<td><h5>Data Spedizione: <?php echo (new \DateTime($dataConsegna))->format("d/m/Y"); ?></h5></td>
				<td><h5>Settimana: <?php echo $orders[0]->week; ?>/<?php echo date('y'); ?></h5></td>
				<td><h5>Ubicazione: <?php echo $group->post_title; ?></h5></td>
			</table>
			<table class="table border">
				<thead>
				<td><b>Nr Colli</b></td>
				<td><b>num cons</b></td>
				<td><b>Spedire a</b></td>
				<td><b>Indirizzo</b></td>
				<td><b>Città</b></td>
				<td><b>Telefono</b></td>
				<td><b>Note consegna</b></td>
				</thead>
				<tbody>
				<?php foreach ($orders as $order): ?>

					<tr>
						<td></td>
						<td>
							<?php
							echo str_pad($order->_numero_consegna, 4, 0, STR_PAD_LEFT);
							?></td>
						<td>
							<?php echo $order->get_shipping_first_name() . " " . $order->get_shipping_last_name(); ?>
						</td>
						<td>
							<?php echo $order->get_shipping_address_1(); ?>
						</td>
						<td>
							<?php echo $order->get_shipping_city(); ?> (<?php echo $order->get_shipping_state(); ?>)
						</td>
						<td>
							<?php echo $order->get_billing_phone(); ?><br>
							<?php if ($order->get_shipping_phone()): ?>
								<?php echo $order->get_shipping_phone(); ?><br>
							<?php endif; ?>
							<?php echo $order->secondary_phone; ?>
						</td>
						<td>
							<span style="font-size: 10px;"><?php echo $order->get_customer_note(); ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<div class="page-break"></div>
	<?php endforeach; ?>
	</body>
	</html>
<?php
$content = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($content);

$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('riepilogo_spedizioni.pdf');
