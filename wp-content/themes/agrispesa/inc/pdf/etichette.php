<?php

use Dompdf\Dompdf;

$dataConsegna = $_POST['data_consegna'];
?>
	<html>
	<head>
		<style>
			.page-break {
				page-break-before: always;
			}

			.card-container {
				width: 100%;
			}

			.card {
				width: 33%;
				float: left;
				margin-bottom: 30px;
			}

			@page {
				margin: 0px;
			}

			body {
				margin: 0px;
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

		if (empty($orders)) {
			continue;
		}

		$orders = array_map(function ($order) {
			$order = wc_get_order($order);
			$order->num_consegna = get_post_meta($order->get_id(), '_num_consegna', true);
			$order->secondary_phone = get_post_meta($order->get_id(), '_secondary_phone', true);
			$order->week = get_post_meta($order->get_id(), '_week', true);
			return $order;
		}, $orders);

		$ordinamento = get_post_meta($group->ID, 'ordinamento_numero_consegna', true);

		if (!$ordinamento) {
			$ordinamento = 'CRESCENTE';
		}

		usort($orders, function ($a, $b) {
			return strcmp($a->num_consegna, $b->num_consegna);
		});

		if ($ordinamento == 'DECRESCENTE') {
			$orders = array_reverse($orders);
		}

		?>
		<h3><?php echo $group->post_title; ?></h3><br>
		<?php foreach ($orders as $order): ?>
			<div class="card">
				<h2><?php echo $order->get_shipping_first_name() . " " . $order->get_shipping_last_name(); ?></h2>
				<h3>
					<?php echo $group->post_title; ?>
				</h3>
				<h4><?php
					echo str_pad($order->num_consegna, 4, 0, STR_PAD_LEFT);
					?></h4>
			</div>
		<?php endforeach; ?>
		<div class="page-break"></div>
	<?php endforeach; ?>
	</body>
	</html>
<?php
$content = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($content);

$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('etichette.pdf');
