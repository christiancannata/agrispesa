<?php
function dd($vars)
{
	die(var_dump($vars));
}

function get_order_delivery_date($id)
{
	$order = wc_get_order($id);
	if (!$order) {
		return null;
	}

	$deliveryDate = get_post_meta($id, '_delivery_date', true);

	if ($deliveryDate) {
		return DateTime::createFromFormat('Y-m-d', $deliveryDate)->format('d/m/Y');
	}
}

function get_order_delivery_date_from_date($date = null, $group = null, $cap = null)
{

	if (!$group && $cap) {
		$groups = get_posts([
			'post_type' => 'delivery-group',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		]);

		foreach ($groups as $singleGroup) {

			$caps = get_post_meta($singleGroup->ID, 'cap', true);

			if (in_array($cap, $caps)) {
				$group = $singleGroup->post_title;
			}
		}
	}

	if (!$group) {
		return null;
	}

	global $wpdb;

	$ids = $wpdb->get_col("select ID from $wpdb->posts where post_title = '" . $group . "' AND post_status = 'publish'");
	$ids = reset($ids);

	$dowMap = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');

	if (get_class($date) != DateTime::class) {
		$date = DateTime::createFromFormat('d-m-Y', $date);
	}

	//if (($date->format('w') > 5 && $date->format('H') >= 8) || $date->format('w') == 0) {
	$date->add(new DateInterval('P7D'));
	//}

	$deliveryDay = get_post_meta($ids, 'delivery_day', true);

	$deliveryDate = strtotime($dowMap[$deliveryDay], $date->getTimestamp());
	$deliveryDate = DateTime::createFromFormat('U', $deliveryDate);

	return $deliveryDate;
}

function calculate_delivery_date_order($id)
{

	$order = wc_get_order($id);
	if (!$order) {
		return null;
	}

	$gruppoConsegna = get_post_meta($id, '_gruppo_consegna', true);

	if (!$gruppoConsegna) {
		return null;
	}


	$order_date = $order->get_date_paid();


	$week = $order_date->format("W");

	if ($order->get_created_via() == 'checkout') {
		$week = str_pad($week + 1, 2, 0, STR_PAD_LEFT);
	} else {
		if (($order_date->format('w') > 5 && $order_date->format('H') >= 8) || $order_date->format('w') == 0) {
			$week = str_pad($week + 1, 2, 0, STR_PAD_LEFT);
		}
	}


	update_post_meta($order->get_id(), '_week', $week);

	$deliveryDate = get_order_delivery_date_from_date($order_date->format('d-m-Y'), $gruppoConsegna);
	update_post_meta($order->get_id(), '_delivery_date', $deliveryDate->format("Y-m-d"));

}


add_action('woocommerce_new_order', function ($order_id, $order) {
	if ($order->get_created_via() == 'checkout') {

		$groups = get_posts([
			'post_type' => 'delivery-group',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		]);

		foreach ($groups as $group) {

			$caps = get_post_meta($group->ID, 'cap', true);

			if (in_array($order->get_shipping_postcode(), $caps)) {
				update_post_meta($order->get_id(), '_gruppo_consegna', $group->post_title);
			}
		}

		$gruppoConsegna = get_post_meta($order_id, '_gruppo_consegna', true);

		$order_date = $order->get_date_created();

		$week = $order_date->format("W");
		$week = str_pad($week + 1, 2, 0, STR_PAD_LEFT);
		update_post_meta($order->get_id(), '_week', $week);

		if ($gruppoConsegna) {
			$deliveryDate = get_order_delivery_date_from_date($order_date->format('d-m-Y'), $gruppoConsegna);
			if ($deliveryDate) {
				update_post_meta($order->get_id(), '_delivery_date', $deliveryDate->format("Y-m-d"));
			}
		}

	}
}, 10, 2);

add_action('woocommerce_product_options_advanced', function () {
	woocommerce_wp_text_input([
		'id' => '_codice_confezionamento',
		'label' => 'Codice Confezionamento',
	]);

	woocommerce_wp_checkbox([
		'id' => '_is_magazzino',
		'label' => "È da Magazzino?",
	]);
	woocommerce_wp_text_input([
		'id' => '_qty_acquisto',
		'label' => 'Quantità (Acquisto)',
	]);
	woocommerce_wp_text_input([
		'id' => '_uom_acquisto',
		'label' => 'Cod. Unità di misura',
	]);
});

add_action('woocommerce_product_options_general_product_data', function () {
	global $post;

	woocommerce_wp_text_input([
		'id' => '_prezzo_acquisto',
		'label' => 'Prezzo di acquisto (€)',
		'placeholder' => '0.00',
		'description' => __('I valori decimali sono separati con un punto. Es. €2.30', 'woocommerce'),
	]);

	woocommerce_wp_checkbox([
		'id' => '_tipo_percentuale_ricarico',
		'label' => 'Eredita percentuale ricarico dalla categoria',

	]);

	woocommerce_wp_text_input([
		'id' => '_percentuale_ricarico',
		'label' => 'Ricarico %',
		'placeholder' => '0',
		'description' => __('Valore della percentuale.', 'woocommerce'),
	]);


});


function woocommerce_product_custom_fields_save1($post_id)
{
	if (isset($_POST['_codice_confezionamento']))
		update_post_meta($post_id, '_codice_confezionamento', esc_attr($_POST['_codice_confezionamento']));
	if (isset($_POST['_is_magazzino']))
		update_post_meta($post_id, '_is_magazzino', esc_attr($_POST['_is_magazzino']));

	if (isset($_POST['_prezzo_acquisto']))
		update_post_meta($post_id, '_prezzo_acquisto', esc_attr($_POST['_prezzo_acquisto']));

	if (isset($_POST['_percentuale_ricarico'])) {
		update_post_meta($post_id, '_percentuale_ricarico', esc_attr($_POST['_percentuale_ricarico']));
	}

	if (isset($_POST['_uom_acquisto'])) {
		update_post_meta($post_id, '_uom_acquisto', esc_attr($_POST['_uom_acquisto']));
	}
	if (isset($_POST['_qty_acquisto'])) {
		update_post_meta($post_id, '_qty_acquisto', esc_attr($_POST['_qty_acquisto']));
	}

}

add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save1');


add_action('rest_api_init', function () {


	register_rest_route('agrispesa/v1', 'products/(?P<product_id>\d+)/category', array(
		'methods' => 'GET',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {

			$terms = get_the_terms($request['product_id'], 'product_cat');

			$terms = array_reverse($terms);

			$selectedTerm = null;
			foreach ($terms as $term) {
				$ricarico = get_term_meta($term->term_id, 'ricarico_percentuale', true);
				if (!empty($ricarico)) {
					$selectedTerm = $term;
					$selectedTerm->ricarico_percentuale = !empty($ricarico) ? floatval($ricarico) : 0;
				}
			}


			$response = new WP_REST_Response($selectedTerm);
			$response->set_status(200);

			return $response;
		}
	));


	register_rest_route('agrispesa/v1', 'weekly-box', array(
		'methods' => 'POST',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {

			$body = $request->get_json_params();

			$post_id = wp_insert_post(array(
				'post_type' => 'weekly-box',
				'post_title' => 'Box settimana ' . $body['week'] . ' - ' . $body['product_box_id'],
				'post_content' => '',
				'post_status' => 'publish',
				'comment_status' => 'closed',   // if you prefer
				'ping_status' => 'closed',      // if you prefer
			));

			if ($post_id) {
				// insert post meta
				add_post_meta($post_id, '_week', $body['week']);
				add_post_meta($post_id, '_data_consegna', $body['data_consegna']);
				add_post_meta($post_id, '_product_box_id', $body['product_box_id']);
				add_post_meta($post_id, '_products', $body['products']);
			}

			$response = new WP_REST_Response([
				'id' => $post_id
			]);
			$response->set_status(201);

			return $response;
		}
	));


	register_rest_route('agrispesa/v1', 'weekly-box/duplicate', array(
		'methods' => 'POST',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {

			$body = $request->get_json_params();

			$lastWeek = $body['week'] - 1;

			$lastWeekBox = get_posts([
				'post_type' => 'weekly-box',
				'post_status' => 'publish',
				'posts_per_page' => 1,
				'meta_query' => [
					'relation' => 'and',
					[
						'key' => '_week',
						'value' => str_pad($lastWeek, 2, 0, STR_PAD_LEFT),
						'compare' => '='
					],
					[
						'key' => '_product_box_id',
						'value' => $body['product_box_id'],
						'compare' => '='
					]
				]
			]);

			if (empty($lastWeekBox)) {
				$response = new WP_REST_Response([
					'message' => 'Nessuna Box Settimana trovata per la settimana ' . $lastWeek
				]);
				$response->set_status(404);

				return $response;
			}

			$lastWeekBox = reset($lastWeekBox);

			$post_id = wp_insert_post(array(
				'post_type' => 'weekly-box',
				'post_title' => 'Box settimana ' . $body['week'] . ' - ' . $body['product_box_id'],
				'post_content' => '',
				'post_status' => 'publish',
				'comment_status' => 'closed',   // if you prefer
				'ping_status' => 'closed',      // if you prefer
			));

			if ($post_id) {
				// insert post meta
				add_post_meta($post_id, '_week', $body['week']);
				add_post_meta($post_id, '_data_consegna', $body['data_consegna']);
				add_post_meta($post_id, '_product_box_id', $body['product_box_id']);

				$products = get_post_meta($lastWeekBox->ID, '_products', true);

				add_post_meta($post_id, '_products', $products);
			}

			$response = new WP_REST_Response([
				'id' => $post_id
			]);
			$response->set_status(201);

			return $response;
		}
	));


	register_rest_route('agrispesa/v1', 'weekly-box/(?P<box_id>\d+)/products/(?P<index>\d+)', array(
		'methods' => 'DELETE',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {


			$products = get_post_meta($request['box_id'], '_products', true);

			unset($products[$request['index']]);

			update_post_meta($request['box_id'], '_products', $products);

			$response = new WP_REST_Response([]);
			$response->set_status(204);

			return $response;
		}
	));


	register_rest_route('agrispesa/v1', 'weekly-box/(?P<box_id>\d+)/products', array(
		'methods' => 'POST',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {


			$products = get_post_meta($request['box_id'], '_products', true);

			foreach ($request['product_ids'] as $key => $id) {
				$unitaMisura = 'gr';

				$measureUnit = get_post_meta($id, '_woo_uom_input', true);

				if (!empty($measureUnit)) {
					$unitaMisura = $measureUnit;
				}


				$products[] = [
					'id' => $id,
					'name' => $request['product_name'][$key],
					'quantity' => $request['quantity'][$key],
					'unit_measure' => $unitaMisura,
					'unit_measure_print' => get_post_meta($id, '_uom_acquisto', true)
				];
			}


			$products = array_map("unserialize", array_unique(array_map("serialize", $products)));

			$newProducts = [];
			foreach ($products as $product) {
				$newProducts[] = $product;
			}

			update_post_meta($request['box_id'], '_products', $newProducts);

			$response = new WP_REST_Response([]);
			$response->set_status(204);

			return $response;
		}
	));


	register_rest_route('agrispesa/v1', 'shop-categories', array(
		'methods' => 'GET',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {

			$taxonomy = 'product_cat';
			$orderby = 'name';
			$show_count = 0;      // 1 for yes, 0 for no
			$pad_counts = 0;      // 1 for yes, 0 for no
			$hierarchical = 1;      // 1 for yes, 0 for no
			$title = '';
			$empty = 0;

			$args = array(
				'taxonomy' => $taxonomy,
				'orderby' => $orderby,
				'show_count' => $show_count,
				'pad_counts' => $pad_counts,
				'hierarchical' => $hierarchical,
				'title_li' => $title,
				'hide_empty' => $empty
			);
			$all_categories = get_categories($args);

			$categories = [];
			foreach ($all_categories as $cat) {
				if ($cat->category_parent == 0) {

					$category_id = $cat->term_id;

					$isVisible = get_term_meta($category_id, 'in_preferenze_utente', true);
					if (empty($isVisible)) {
						continue;
					}

					$args2 = array(
						'taxonomy' => $taxonomy,
						'child_of' => 0,
						'parent' => $category_id,
						'orderby' => $orderby,
						'show_count' => $show_count,
						'pad_counts' => $pad_counts,
						'hierarchical' => $hierarchical,
						'title_li' => $title,
						'hide_empty' => $empty
					);
					$sub_cats = get_categories($args2);
					if ($sub_cats) {
						foreach ($sub_cats as $sub_category) {
							$isVisible = get_term_meta($sub_category->term_id, 'in_preferenze_utente', true);
							if (empty($isVisible)) {
								continue;
							}


							$categoryProducts = get_posts(array(
								'post_type' => 'product',
								'numberposts' => -1,
								'post_status' => 'publish',
								'tax_query' => array(
									array(
										'taxonomy' => 'product_cat',
										'field' => 'slug',
										'terms' => $sub_category->slug,
										'operator' => 'IN',
									)
								),
							));
							$categories[] =
								[
									'id' => $sub_category->term_id,
									'name' => $sub_category->name,
									'products' => $categoryProducts
								];

						}
					}
				}
			}

			$response = new WP_REST_Response($categories);
			$response->set_status(200);

			return $response;
		}
	));


	register_rest_route('agrispesa/v1', 'user-subscriptions', array(
		'methods' => 'GET',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {

			$subscriptions = wcs_get_subscriptions(['subscriptions_per_page' => -1, 'customer_id' => get_current_user_id(), 'subscription_status' => 'active']);

			$json = [];

			foreach ($subscriptions as $subscription) {
				$products = $subscription->get_items();

				$productsToAdd = get_products_to_add_from_subscription($subscription);

				$boxPreferences = get_post_meta($subscription->get_id(), '_box_preferences', true);
				if (empty($boxPreferences)) {
					$boxPreferences = [];
				}

				$boxBlacklist = get_post_meta($subscription->get_id(), '_box_blacklist', true);
				if (empty($boxBlacklist)) {
					$boxBlacklist = [];
				}

				$json[] = [
					'name' => reset($products)->get_name(),
					'id' => $subscription->get_id(),
					'box_preferences' => $boxPreferences,
					'box_blacklist' => $boxBlacklist,
					'products' => $productsToAdd
				];
			}

			$response = new WP_REST_Response($json);
			$response->set_status(200);

			return $response;
		}
	));


	register_rest_route('agrispesa/v1', 'delivery-group-csv', array(
		'methods' => 'GET',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {

			$dataConsegna = $_GET['data_consegna'];

			$caps = get_post_meta($_GET['delivery_group'], 'cap', true);

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
						'key' => '_shipping_postcode',
						'value' => $caps,
						'compare' => 'IN'
					]
				]
			];
			$orders = new WP_Query($args);
			$orders = $orders->get_posts();

			$csv = [];

			foreach ($orders as $order) {
				$order = wc_get_order($order->ID);

				$csv[] = [
					$order->get_shipping_postcode(),
					$order->get_shipping_city(),
					$order->get_shipping_address_1(),
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					$order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name()
				];

			}

			$f = fopen('php://memory', 'w');
			foreach ($csv as $line) {
				fputcsv($f, $line);
			}
			fseek($f, 0);
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="PIEM ' . $dataConsegna . ' da nav a map&guide.csv";');
			fpassthru($f);
			die();

		}
	));


	register_rest_route('agrispesa/v1', 'subscription-preference', array(
		'methods' => 'POST',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {
			$body = $request->get_json_params();

			$boxPreferences = get_post_meta($body['subscription_id'], '_box_preferences', true);

			if (empty($boxPreferences)) {
				$boxPreferences = [];
			}

			foreach ($body['product_ids'] as $productId) {
				$productToAdd = get_post($productId);
				$boxPreferences[] = [
					'id' => $productToAdd->ID,
					'name' => $productToAdd->post_title
				];
			}

			$boxPreferences = array_map("unserialize", array_unique(array_map("serialize", $boxPreferences)));

			$newBoxPreferences = [];
			foreach ($boxPreferences as $boxPreference) {
				$newBoxPreferences[] = $boxPreference;
			}

			update_post_meta($body['subscription_id'], '_box_preferences', $newBoxPreferences);

			$response = new WP_REST_Response([]);
			$response->set_status(201);

			return $response;
		}
	));

	register_rest_route('agrispesa/v1', 'subscription-blacklist', array(
		'methods' => 'POST',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {
			$body = $request->get_json_params();

			$boxPreferences = get_post_meta($body['subscription_id'], '_box_blacklist', true);

			if (empty($boxPreferences)) {
				$boxPreferences = [];
			}


			foreach ($body['product_ids'] as $productId) {
				$productToAdd = get_post($productId);
				$boxPreferences[] = [
					'id' => $productToAdd->ID,
					'name' => $productToAdd->post_title
				];
			}

			$boxPreferences = array_map("unserialize", array_unique(array_map("serialize", $boxPreferences)));

			$newBoxPreferences = [];

			foreach ($boxPreferences as $boxPreference) {
				$newBoxPreferences[] = $boxPreference;
			}

			update_post_meta($body['subscription_id'], '_box_blacklist', $newBoxPreferences);

			$response = new WP_REST_Response([]);
			$response->set_status(201);

			return $response;
		}
	));


	register_rest_route('agrispesa/v1', 'subscription-preference', array(
		'methods' => 'DELETE',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {
			$body = $request->get_json_params();
			$boxPreferences = get_post_meta($body['subscription_id'], '_box_preferences', true);


			$productIds = $body['product_ids'];
			$newBoxPreferences = [];

			foreach ($productIds as $productId) {
				//find product
				$index = array_filter($boxPreferences, function ($product) use ($productId) {
					return $product['id'] == $productId;
				});

				if (!empty($index)) {
					$index = array_keys($index);
					$index = reset($index);
					unset($boxPreferences[$index]);
				}

			}

			foreach ($boxPreferences as $preference) {
				$newBoxPreferences[] = $preference;
			}


			update_post_meta($body['subscription_id'], '_box_preferences', $newBoxPreferences);


			$response = new WP_REST_Response([]);
			$response->set_status(204);
			return $response;
		}
	));

	register_rest_route('agrispesa/v1', 'subscription-blacklist', array(
		'methods' => 'DELETE',
		'permission_callback' => function () {
			return true;
		},
		'callback' => function ($request) {
			$body = $request->get_json_params();
			$boxPreferences = get_post_meta($body['subscription_id'], '_box_blacklist', true);

			$productIds = $body['product_ids'];
			$newBoxPreferences = [];

			foreach ($productIds as $productId) {
				//find product
				$index = array_filter($boxPreferences, function ($product) use ($productId) {
					return $product['id'] == $productId;
				});

				if (!empty($index)) {
					$index = array_keys($index);
					$index = reset($index);
					unset($boxPreferences[$index]);
				}

			}

			foreach ($boxPreferences as $preference) {
				$newBoxPreferences[] = $preference;
			}

			update_post_meta($body['subscription_id'], '_box_blacklist', $newBoxPreferences);

			$response = new WP_REST_Response([]);
			$response->set_status(204);
			return $response;
		}
	));


});


function my_enqueue($hook)
{

	if ($hook == 'edit.php' || $hook == 'post.php') {
		wp_enqueue_script('agrispesa-admin-delivery-box-js', get_theme_file_uri('assets/js/admin-delivery-box.js'), array('jquery', 'select2'), null, true);
		wp_localize_script('agrispesa-admin-delivery-box-js', 'WPURL', array('siteurl' => get_option('siteurl')));

	} else {

		if ('toplevel_page_box-settimanali' !== $hook && 'woocommerce_page_my-custom-submenu-page' !== $hook) {
			return;
		}


		wp_register_style('select2css', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', false, '1.0', 'all');
		wp_register_script('select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), '1.0', true);

		wp_enqueue_style('select2css');
		wp_enqueue_script('select2');

		wp_enqueue_script('moment', get_template_directory_uri() . '/assets/js/moment.min.js', ['jquery'], null, true);


		wp_register_script('axios', '//cdnjs.cloudflare.com/ajax/libs/axios/1.2.2/axios.min.js', array(), null, true);
		wp_enqueue_script('axios');

		wp_register_script('vuejs', '//unpkg.com/vue@3/dist/vue.global.js', array(), null, true);
		wp_enqueue_script('vuejs');

		wp_register_style('datatable', '//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css', false, '1.0', 'all');
		wp_enqueue_style('datatable');

		wp_register_script('datatable-js', '//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js', array(), null, true);
		wp_enqueue_script('datatable-js');

		wp_enqueue_style('agrispesa-admin-css', get_theme_file_uri('assets/css/admin.css'), false, '1.0', 'all');
		wp_enqueue_script('agrispesa-admin-js', get_theme_file_uri('assets/js/admin.js'), array('jquery', 'select2'), null, true);
		wp_localize_script('agrispesa-admin-js', 'WPURL', array('siteurl' => get_option('siteurl')));

	}
}

add_action('admin_enqueue_scripts', 'my_enqueue');

// Adding Meta container admin shop_order pages
add_action('add_meta_boxes', 'mv_add_meta_boxes');
if (!function_exists('mv_add_meta_boxes')) {
	function mv_add_meta_boxes()
	{
		add_meta_box(
			'box_preferences',
			'Preferenze Facciamo noi',
			'box_preferences_meta_box_callback',
			'shop_order',
			'advanced',
			'core',
			[]
		);

		add_meta_box('mv_other_fields', 'INFORMAZIONI CONSEGNA', 'mv_add_other_fields_for_packaging', 'shop_order', 'side', 'core');


	}

	function box_preferences_meta_box_callback($order)
	{
		global $post;

		$subscriptionId = get_post_meta($post->ID, '_subscription_id', true);
		echo $subscriptionId;

		?>
		<h4>Da Eliminare</h4><br>

		<?php

	}

}

// if you don't add 3 as as 4th argument, this will not work as expected
add_action('save_post', 'my_save_post_function', 10, 3);

function my_save_post_function($post_ID, $post, $update)
{
	if ($post->post_type == 'shop_order') {
		if (isset($_POST['_numero_consegna'])) {
			update_post_meta($post->ID, '_numero_consegna', $_POST['_numero_consegna']);
		}

		if (isset($_POST['_data_consegna'])) {
			update_post_meta($post->ID, '_data_consegna', $_POST['_data_consegna']);
		}

	}
}

// Adding Meta field in the meta container admin shop_order pages
if (!function_exists('mv_add_other_fields_for_packaging')) {
	function mv_add_other_fields_for_packaging()
	{
		global $post;

		$weight = get_post_meta($post->ID, '_total_box_weight', true);
		$week = get_post_meta($post->ID, '_week', true);
		$numConsegna = get_post_meta($post->ID, '_numero_consegna', true);
		$consegna = get_post_meta($post->ID, '_data_consegna', true);
		$gruppoConsegna = get_post_meta($post->ID, '_gruppo_consegna', true);
		$deliveryDay = get_order_delivery_date($post->ID);


		if (empty($weight)) {
			$weight = 0;
		}
		echo '<span>Peso della scatola: <strong>' . $weight . ' kg</strong></span><br>';
		echo '<span>Settimana: <strong>' . $week . '</strong></span><br><br>';
		echo '<span>Gruppo di consegna: <strong>' . $gruppoConsegna . '</strong></span><br><br>';
		echo '<span>Data di ricezione: <strong>' . $deliveryDay . '</strong></span><br><br>';
		echo '<strong>Numero di consegna:</strong><br>
		<input autocomplete="off" type="text" value="' . $numConsegna . '" name="_numero_consegna"><br><br>';

		global $wpdb;
		$allDataConsegna = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_data_consegna' group by meta_value", ARRAY_A);
		?>
		<strong>Data di consegna:</strong><br>
		<select autocomplete="off" name="_data_consegna">

			<?php foreach ($allDataConsegna as $dataConsegna):
				//fix nathi per errore data di consegna
				if ($dataConsegna['meta_value'] === "Nessuna data di consegna"):?>
					<option <?php if (!$consegna): ?> selected <?php endif; ?>>Nessuna data di consegna</option>
				<?php else:
					$fixshippingdate = $dataConsegna['meta_value'];
					$fixshippingdate = strtotime($fixshippingdate);
					$fixshippingdate = date("d/m/Y", $fixshippingdate);
					?>
					<option
						<?php if ($consegna && $dataConsegna['meta_value'] == $consegna): ?> selected <?php endif; ?>
						value="<?php echo $fixshippingdate; ?>"><?php echo $fixshippingdate; ?></option>
				<?php endif; ?>

			<?php endforeach; ?>
		</select>
		<?php
	}
}
function get_products_to_add_from_subscription($subscription, $week = null, $overrideProducts = false)
{
	$box = get_box_from_subscription($subscription, $week);

	if (!$box) {
		return [];
	}

	$productsToAdd = get_post_meta($box->ID, '_products', true);

	if ($overrideProducts) {
		//check preferences
		$boxPreferences = get_post_meta($subscription->get_id(), '_box_preferences', true);
		if (empty($boxPreferences)) {
			$boxPreferences = [];
		}

		foreach ($boxPreferences as $preference) {
			$productSearched = array_filter(
				$productsToAdd,
				function ($product) use ($preference) {
					return $product['id'] == $preference['id'];
				}
			);

			if (!empty($productSearched)) {

				$keys = array_keys($productSearched);

				$productSearched = reset($productSearched);


				$productSearchedKey = reset($keys);

				$quantity = $productsToAdd[$productSearchedKey]['quantity'];
				unset($productsToAdd[$productSearchedKey]);

				$categories = get_the_terms($productSearched['id'], 'product_cat');
				$category = reset($categories);

				$prod_categories = [$category->term_id];
				$product_args = array(
					'numberposts' => -1,
					'post_status' => array('publish'),
					'post_type' => array('product'),
					'suppress_filters' => false,
					'order' => 'ASC',
					'offset' => 0
				);

				$product_args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field' => 'id',
						'terms' => $prod_categories,
						'operator' => 'IN',
					));

				$productsByCategory = get_posts($product_args);

				$productToAdd = reset($productsByCategory);

				$productsToAdd[] = [
					'id' => $productToAdd->ID,
					'name' => $productToAdd->post_title,
					'quantity' => $quantity
				];

			}

		}

	}


	return $productsToAdd;
}

function get_box_from_subscription($subscription, $week = null)
{

	if (!$week) {
		$date = new DateTime();
		$week = $date->format("W");
	}


	$products = $subscription->get_items();

	if (empty($products)) {
		return null;
	}

	$box = reset($products)->get_product();

	if (!$box) {
		return null;
	}

	$tipologia = get_post_meta($box->get_id(), 'attribute_pa_tipologia', true);
	$dimensione = get_post_meta($box->get_id(), 'attribute_pa_dimensione', true);


	$productBox = get_single_box_from_attributes($tipologia, $dimensione);

	if (empty($productBox)) {
		return null;
	}

	//get product data box
	$box = get_weekly_box_from_box($productBox->get_id(), $week);
	return $box;
}


function get_weekly_box_from_box($id, $week)
{
	$box = get_posts([
		'post_type' => 'weekly-box',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'meta_query' => [
			'relation' => 'and',
			[
				'key' => '_week',
				'value' => $week,
				'compare' => '='
			],
			[
				'key' => '_product_box_id',
				'value' => $id,
				'compare' => '='
			]
		]
	]);

	if (empty($box)) {
		return null;
	}


	return reset($box);
}

function create_order_from_subscription($id)
{
	$subscription = wcs_get_subscription($id);

	if (!$subscription) {
		return false;
	}

	$weight = 0;
	/*    if (!empty($productData['weight'])) {
	$weight = $productData['weight'];
	}*/

	$box = get_box_from_subscription($subscription);

	if (!$box) {
		return false;
	}

	$week = get_post_meta($box->ID, '_week', true);
	$consegna = get_post_meta($box->ID, '_data_consegna', true);

	$productsToAdd = get_products_to_add_from_subscription($subscription, $week, true);

	$customerId = $subscription->get_user_id();

	$order = wc_create_order();
	$order->set_customer_id($customerId);


	foreach ($productsToAdd as $productToAdd) {
		$productObjToAdd = wc_get_product($productToAdd['id']);

		if ($productToAdd['quantity'] > $productObjToAdd->get_stock_quantity()) {
			//Non ho più disponibilità
		}

		$order->add_product($productObjToAdd, $productToAdd['quantity']);
	}

	// The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
	$order->set_address([
		'first_name' => $subscription->get_billing_first_name(),
		'last_name' => $subscription->get_billing_last_name(),
		'company' => $subscription->get_billing_company(),
		'email' => $subscription->get_billing_email(),
		'phone' => $subscription->get_billing_phone(),
		'address_1' => $subscription->get_billing_address_1(),
		'address_2' => $subscription->get_billing_address_2(),
		'city' => $subscription->get_billing_city(),
		'state' => $subscription->get_billing_state(),
		'postcode' => $subscription->get_billing_postcode(),
		'country' => $subscription->get_billing_country()
	], 'billing');

	$order->set_address([
		'first_name' => $subscription->get_shipping_first_name(),
		'last_name' => $subscription->get_shipping_last_name(),
		'company' => $subscription->get_shipping_company(),
		'email' => $subscription->get_billing_email(),
		'phone' => $subscription->get_shipping_phone(),
		'address_1' => $subscription->get_shipping_address_1(),
		'address_2' => $subscription->get_shipping_address_2(),
		'city' => $subscription->get_shipping_city(),
		'state' => $subscription->get_shipping_state(),
		'postcode' => $subscription->get_shipping_postcode(),
		'country' => $subscription->get_shipping_country()
	], 'shipping');


	/*$items = $subscription->get_items();
	foreach ($items as $item) {

	$order->add_product(, 1);
	}

	foreach ($order->get_items() as $item) {
	$item->set_name($item->get_name() . ' - Settimana ' . $week);
	$item->save();
	}*/

	$order->calculate_totals();
	$order->update_status("processing", '', TRUE);


	update_post_meta($order->get_id(), '_total_box_weight', $weight);
	update_post_meta($order->get_id(), '_week', $week);

	if (($order->get_date_paid()->format('w') > 5 && $order->get_date_paid()->format('H') >= 8) || $order->get_date_paid()->format('w') == 0) {
		$order->get_date_paid()->add(new DateInterval('P7D'));
	}


	update_post_meta($order->get_id(), '_data_consegna', $consegna);
	update_post_meta($order->get_id(), '_order_type', 'BOX');
	update_post_meta($order->get_id(), '_subscription_id', $id);


	$boxPreferences = get_post_meta($subscription->get_id(), '_box_preferences', true);
	if (empty($boxPreferences)) {
		$boxPreferences = [];
	}

	update_post_meta($order->get_id(), '_box_preferences', $boxPreferences);


	$groups = get_posts([
		'post_type' => 'delivery-group',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	]);

	foreach ($groups as $group) {

		$caps = get_post_meta($group->ID, 'cap', true);

		if (in_array($order->get_shipping_postcode(), $caps)) {
			update_post_meta($order->get_id(), '_gruppo_consegna', $group->post_title);
		}
	}

	calculate_delivery_date_order($order->get_id());

}

function get_single_box_from_attributes($tipologia, $dimensione)
{
	$products = get_posts(array(
		'post_type' => 'product',
		'numberposts' => -1,
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field' => 'slug',
				'terms' => 'box singola',
				'operator' => 'IN',
			)
		),
	));

	$productFound = false;

	foreach ($products as $product) {
		$product = wc_get_product($product->ID);
		$children = $product->get_children();
		foreach ($children as $variation) {
			$tipologiaVariation = get_post_meta($variation, 'attribute_pa_tipologia', true);
			$dimensioneVariation = get_post_meta($variation, 'attribute_pa_dimensione', true);

			if ($tipologia == $tipologiaVariation && $dimensioneVariation == $dimensione) {
				$productFound = $variation;
			}
		}
	}

	if ($productFound) {
		$productFound = wc_get_product($productFound);
		return $productFound;
	}

	return $productFound;

}

function register_my_custom_submenu_page()
{
	add_menu_page('Genera Ordini Box', 'Genera Ordini Box', 'manage_options', 'my-custom-submenu-page', 'my_custom_submenu_page_callback');
}

function my_custom_submenu_page_callback()
{

	if (isset($_POST['generate_orders'])) {
		$subscriptionIds = $_POST['subscriptions'];
		foreach ($subscriptionIds as $subscriptionId) {
			create_order_from_subscription($subscriptionId);
		}
	}

	$subscriptions = wcs_get_subscriptions(['subscriptions_per_page' => -1, 'subscription_status' => 'active']);
	/*$subscriptions = array_filter($subscriptions, function ($subscription) {
	return $subscription->has_status('active');
	});*/

	$date = new DateTime();
	$week = $date->format("W");

	$allProductsNeed = [];

	foreach ($subscriptions as $subscription) {
		$args = [
			'posts_per_page' => -1,
			'post_type' => 'shop_order',
			'post_status' => ['wc-processing', 'wc-completed'],
			'meta_query' => [
				'relation' => 'and',
				[
					'key' => '_week',
					'value' => $week,
					'compare' => '='
				],
				[
					'key' => '_subscription_id',
					'value' => $subscription->get_id(),
					'compare' => '='
				]
			]
		];
		$orders = new WP_Query($args);
		$orders = $orders->get_posts();
		if (count($orders) > 0) {
			continue;
		}

		$productsToAdd = get_products_to_add_from_subscription($subscription, $week, true);
		foreach ($productsToAdd as $productToAdd) {

			if (!isset($allProductsNeed[$productToAdd['id']])) {
				$allProductsNeed[$productToAdd['id']] = [
					'name' => $productToAdd['name'],
					'product_id' => $productToAdd['id'],
					'quantity' => $productToAdd['quantity']
				];
			} else {
				$allProductsNeed[$productToAdd['id']]['quantity'] += $productToAdd['quantity'];
			}

		}


		foreach ($allProductsNeed as $key => $productNeed) {
			$productObjToAdd = wc_get_product($productNeed['product_id']);
			$allProductsNeed[$key]['current_availability'] = $productObjToAdd->get_stock_quantity();
		}

	}

	?>

	<div id="wpbody-content">

		<div class="wrap">
			<div class="agr-create-new-orders">

				<h1 class="wp-heading-inline">
					Genera Ordini BOX</h1>

				<p style="font-size: 16px; margin-bottom: 24px;">In questa pagina puoi generare in automatico gli ordini
					per gli abbonamenti delle "Facciamo noi" attivi, in base
					alle loro preferenze espresse.<br/>Potrai modificare successivamente il singolo ordine modificando i
					prodotti che preferisci.</p>

				<span
					style="background: rgba(60,33,255,.1);padding:8px 12px;border-radius: 8px;font-weight: 700;font-size: 16px;margin: 16px 0;display: inline-block;">Settimana <?php echo $week; ?> di 52</span>
				<hr class="wp-header-end">


				<br>
				<h3>Disponibilità prodotti</h3>

				<table class="datatable styled-table" style="width:100%;border-collapse: collapse;">
					<thead>
					<th style="padding: 8px 10px;">Prodotto</th>
					<th style="padding: 8px 10px;">Disponibilità</th>
					<th style="padding: 8px 10px;">Quantità richiesta</th>
					</thead>
					<tbody>
					<?php foreach ($allProductsNeed as $product): ?>
						<tr>
							<td style="padding: 8px 10px;"><?php echo $product['name'] ?></td>
							<td style="padding: 8px 10px;"><?php echo $product['current_availability'] ?></td>
							<td style="padding: 8px 10px;"><?php echo $product['quantity'] ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<br/>

			</div>

			<form id="comments-form" method="POST"
				  action="">

				<input type="hidden" name="generate_orders" value="1">
				<div class="tablenav top">

					<div class="alignleft actions bulkactions">
						<label for="bulk-action-selector-top" class="screen-reader-text">Seleziona l'azione di
							gruppo</label><select name="action" id="bulk-action-selector-top">
							<option value="-1">Azioni di gruppo</option>
							<option value="unapprove">Genera ordini</option>
						</select>
						<input type="submit" id="doaction" class="button action" value="Applica">
					</div>

					<div class="tablenav-pages one-page">
						<span class="displaying-num">Abbonamenti attivi</span>
					</div>
					<br class="clear">
				</div>
				<h2 class="screen-reader-text">Elenco abbonamenti</h2>


				<table class="datatable styled-table" style="width:100%;border-collapse: collapse;">
					<thead>

					<th id="cb" class="manage-column column-cb check-column"
						style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;">
						<span style="display:flex;align-items:center;">
							<input id="cb-select-all-1" type="checkbox" style="margin: 0 8px 0 0;">
							<label for="cb-select-all-1" style="font-size:16px;">
								Seleziona tutti
							</label>
						</span>
					</th>
					<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;"
						scope="col" id="author" class="manage-column column-author sortable desc">
						<span>Cliente</span>
					</th>
					<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;"
						scope="col" id="comment" class="manage-column column-comment column-primary">
						<span>Abbonamento</span>
					</th>
					<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;"
						scope="col" id="comment" class="manage-column column-comment column-primary">
						<span>Attivo dal</span>
					</th>
					<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;">
						<span>Ordine</span>
					</th>
					</thead>

					<tbody>
					<?php foreach ($subscriptions as $subscription):

						$args = [
							'posts_per_page' => -1,
							'post_type' => 'shop_order',
							'post_status' => ['wc-processing', 'wc-completed'],
							'meta_query' => [
								'relation' => 'AND',
								[
									'key' => '_week',
									'value' => $week,
									'compare' => '>='
								],
								[
									'key' => '_subscription_id',
									'value' => $subscription->get_id(),
									'compare' => '='
								]
							]
						];
						$orders = new WP_Query($args);
						$orders = $orders->get_posts();
						$products = $subscription->get_items();
						$boxProduct = reset($products);

						$variationProduct = $boxProduct->get_product();

						if (!$variationProduct) {
							continue;
						}


						$tipologia = get_post_meta($variationProduct->get_id(), 'attribute_pa_tipologia', true);
						$dimensione = get_post_meta($variationProduct->get_id(), 'attribute_pa_dimensione', true);

						?>
						<tr id="comment-1" class="comment even thread-even depth-1 approved">
							<th scope="row" class="check-column" style="padding: 16px;">
								<label class="screen-reader-text" for="cb-select-1">Seleziona un abbonamento</label>

								<?php

								if (!$box = get_single_box_from_attributes($tipologia, $dimensione)) {
									echo "Box Singola Non disponibile";
								} else {
									//check if exist weekly box
									$weekBox = get_weekly_box_from_box($box->get_id(), $week);

									if ($weekBox):
										?>
										<input id="cb-select-1" type="checkbox" name="subscriptions[]"
											   value="<?php echo $subscription->get_id(); ?>"
											<?php if (count($orders) > 0): ?>
												disabled
											<?php endif; ?>
										><br>
									<?php else: ?>
										Nessuna Box Settimanale
									<?php endif; ?>
								<?php } ?>

							</th>
							<td class="author column-author" data-colname="Autore" style="padding: 16px;">
								<span><?php echo $subscription->get_billing_first_name() . " " . $subscription->get_billing_last_name(); ?></span>
							</td>
							<td class="comment column-comment has-row-actions column-primary"
								data-colname="Commento" style="padding: 16px;">
								<span><?php

									echo $boxProduct->get_name();

									?>
									</span>
							</td>

							<td class="response column-response" data-colname="In risposta a" style="padding: 16px;">
								<span>
								<?php
								// fix nathi per errore data di consegna
								$fixdate = $subscription->get_date_created();
								$fixdate = new DateTime($fixdate);
								echo $fixdate->format("d/m/Y"); ?>
								</span>
							</td>
							<td style="padding: 16px;">
								<?php if (count($orders) > 0): ?>
									<a target="_blank"
									   href="/wp-admin/post.php?post=<?php echo $orders[0]->ID ?>&action=edit">Vai
										all'ordine</a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<br><br>

				<button type="submit" class="button-primary">Genera Ordini</button>
			</form>
		</div>

		<div id="ajax-response"></div>

		<div class="clear"></div>
	</div>
	<?php
}

add_action('admin_menu', 'register_my_custom_submenu_page', 99);


add_filter('manage_edit-shop_order_columns', 'custom_shop_order_column', 20);
function custom_shop_order_column($columns)
{
	$reordered_columns = array();

	// Inserting columns to a specific location
	foreach ($columns as $key => $column) {
		$reordered_columns[$key] = $column;
		if ($key == 'order_status') {
			// Inserting after "Status" column
			// $reordered_columns['my-column1'] = 'Tipo';
			// $reordered_columns['my-column2'] = 'Data Consegna';
			// $reordered_columns['my-column3'] = 'Preferenze';
		}
	}
	return $reordered_columns;
}

// Adding custom fields meta data for each new column (example)
add_action('manage_shop_order_posts_custom_column', 'custom_orders_list_column_content', 20, 2);
function custom_orders_list_column_content($column, $post_id)
{
	switch ($column) {
		case 'my-column1' :
			// Get custom post meta data
			$orderType = get_post_meta($post_id, '_order_type', true);
			if (!empty($orderType))
				echo 'FN';
			// Testing (to be removed) - Empty value case
			else
				echo 'ST';

			break;

		case 'my-column2' :

			// Get custom post meta data NATHI QUIIII
			$dataConsegna = get_post_meta($post_id, '_data_consegna', true);

			if ($dataConsegna === "Nessuna data di consegna") {
				echo '-';
			} else {
				$fixshippingdate = new DateTime($dataConsegna);
				echo $fixshippingdate->format('d/m/Y');
			}

			break;

		case 'my-column3' :
			// Get custom post meta data
			$orderType = get_post_meta($post_id, '_order_type', true);


			if ($orderType == 'BOX') {
				$boxPreferences = get_post_meta($post_id, '_box_preferences', true);
				if (!empty($boxPreferences))
					echo '✅';
			}


			break;

	}
}


function cptui_register_my_cpts_delivery_group()
{

	/**
	 * Post Type: Gruppi di Consegna.
	 */

	$labels = [
		"name" => esc_html__("Gruppi di Consegna", "custom-post-type-ui"),
		"singular_name" => esc_html__("Gruppo di consegna", "custom-post-type-ui"),
	];

	$args = [
		"label" => esc_html__("Gruppi di Consegna", "custom-post-type-ui"),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => ["slug" => "delivery-group", "with_front" => true],
		"query_var" => true,
		"supports" => ["title", "editor"],
		"show_in_graphql" => false,
	];

	register_post_type("delivery-group", $args);


	/**
	 * Post Type: Gruppi di Consegna.
	 */

	$labels = [
		"name" => esc_html__("Consegne", "custom-post-type-ui"),
		"singular_name" => esc_html__("Consegna", "custom-post-type-ui"),
	];

	$args = [
		"label" => esc_html__("Consegna", "custom-post-type-ui"),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => ["slug" => "delivery-item", "with_front" => true],
		"query_var" => true,
		"supports" => ["title", "editor"],
		"show_in_graphql" => false,
	];


	$labels = [
		"name" => esc_html__("Box settimanali", "custom-post-type-ui"),
		"singular_name" => esc_html__("Box settimanale", "custom-post-type-ui"),
	];

	$args = [
		"label" => esc_html__("Box settimanale", "custom-post-type-ui"),
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"publicly_queryable" => false,
		"show_ui" => false,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => false,
		"show_in_nav_menus" => false,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => ["slug" => "weekly-box", "with_front" => true],
		"query_var" => true,
		"supports" => ["title", "editor"],
		"show_in_graphql" => false,
	];

	register_post_type("weekly-box", $args);
}

add_action('init', 'cptui_register_my_cpts_delivery_group');


add_action('admin_menu', 'consegne_ordini_pages');
function consegne_ordini_pages()
{
	add_menu_page('Consegne Ordini', 'Consegne Ordini', 'manage_options', 'consegne-ordini-pages', function () {
		$groups = get_posts([
			'post_type' => 'delivery-group',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		]);

		if (isset($_POST['import_consegne'])) {

			if (isset($_FILES["file"])) {

				//if there was an error uploading the file
				if ($_FILES["file"]["error"] > 0) {
					echo "Return Code: " . $_FILES["file"]["error"] . "<br />";

				} else {
					$storagename = "csv.txt";
					move_uploaded_file($_FILES["file"]["tmp_name"], get_temp_dir() . "/" . $storagename);

					$file = fopen(get_temp_dir() . "/" . $storagename, "r");

					$csv = [];
					while (!feof($file)) {
						$csv[] = fgetcsv($file, null, ';');
					}

					fclose($file);

					$args = [
						'posts_per_page' => -1,
						'post_type' => 'shop_order',
						'post_status' => ['wc-processing', 'wc-completed'],
						'meta_query' => [
							'relation' => 'AND',
							[
								'key' => '_week',
								'value' => str_pad($_POST['week'], 2, 0, STR_PAD_LEFT),
								'compare' => '='
							]
						]
					];
					$orders = new WP_Query($args);
					$orders = $orders->get_posts();

					$i = 0;
					foreach ($csv as $single) {

						$order = array_filter($orders, function ($tmpOrder) use ($single) {
							$address = get_post_meta($tmpOrder->ID, '_shipping_address_1', true);
							return trim($address) == trim($single[4]);
						});

						if (!empty($order)) {
							$order = reset($order);
							update_post_meta($order->ID, '_numero_consegna', trim($single[0]));
							$i++;
						}
					}

					?>
					<span class="custom-alert alert-success"
						  style="font-size: 14px;padding: 16px;background: greenyellow;margin: 24px 19px 4px 2px;display: block;border-radius: 8px;">Ordini aggiornati: <?php echo $i; ?></span>
					<?php

				}
			} else {
				echo "<span style='font-size: 14px;padding: 16px;background: orangered; color:#fff;margin: 24px 19px 4px 2px;display: block;border-radius: 8px;'>Nessun file inserito.</span>";
			}


		}

		?>
		<div id="wpbody-content">

			<div class="wrap">
				<div class="agr-create-new-boxes">
					<h1 class="wp-heading-inline">
						Consegne Ordini</h1>

					<hr class="wp-header-end">

					<p style="font-size: 16px; margin-bottom: 24px;">
						In questa pagina puoi caricare il file di Map&Guide.</p>

					<form enctype="multipart/form-data" method="POST" action="">
						<input type="hidden" name="import_consegne" value="1">
						<?php
						$date = new DateTime();
						$currentWeek = $date->format("W");
						?>

						<label style="font-size: 14px; font-weight: bold; margin-bottom: 6px; display: block;">
							Settimana di consegna
						</label>
						<select name="week" autocomplete="off" style="width:250px;">
							<?php for ($i = 1; $i <= 52; $i++): ?>
								<option
									value="<?php echo $i; ?>"
									<?php if ($i == $currentWeek): ?> selected <?php endif; ?>
								>Settimana <?php echo $i; ?></option>
							<?php endfor; ?>
						</select>
						<p style="font-style:italic;font-size:14px;">
							Settimana corrente: <?php echo $currentWeek; ?>
						</p>
						<br>
						<label style="font-size: 14px; font-weight: bold; margin-bottom: 6px; display: block;">CSV di
							Map&Guide</label>
						<input type="file" name="file" required><br><br>
						<button class="btn button-primary">
							Importa CSV
						</button>

						<br>
					</form>

				</div>


				<form id="comments-form" method="POST"
					  action="" style="margin-top:40px;width:100%;">

					<input type="hidden" name="generate_orders" value="1">
					<table class="wp-list-table widefat fixed striped table-view-list comments"
						   style="background:transparent;border:none;">
						<thead>
						<tr>
							<!--<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text"
																							for="cb-select-all-1">Seleziona
									tutto</label><input id="cb-select-all-1" type="checkbox"></td>-->
							<th scope="col" id="author" class="manage-column column-author sortable desc"
								style="padding: 16px;font-weight: bold;border-width: 1px;border-style: solid;border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0);border-image: initial;background: rgb(255, 255, 255);font-size: 16px;border-radius: 6px 6px 0px 0px;">
								<span>Gruppo</span>
							</th>
							<th scope="col" id="comment" class="manage-column column-comment column-primary"
								style="padding: 16px;font-weight: bold;border-width: 1px;border-style: solid;border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0);border-image: initial;background: rgb(255, 255, 255);font-size: 16px;border-radius: 6px 6px 0px 0px;">
								<span>Ordini</span>
							</th>

						</tr>
						</thead>

						<tbody id="the-comment-list" class="create-box-table--mega-table" data-wp-lists="list:comment">
						<?php foreach ($groups as $group):

							$caps = get_post_meta($group->ID, 'cap', true);

							$orders = wc_get_orders([
								'limit' => -1,
								'meta_key' => '_shipping_postcode',
								'meta_value' => $caps,
								'meta_compare' => 'IN',
							]);

							$orders = array_filter($orders, function ($order) {
								return $order->get_status() == 'processing';
							});

							?>

							<tr id="comment-1" class="comment even thread-even depth-1 approved">
								<!--	<th scope="row" class="check-column"><label class="screen-reader-text"
																			for="cb-select-1">Seleziona
										un abbonamento</label>
									<?php if (count($orders) == 0): ?>
										<input id="cb-select-1" type="checkbox" name="subscriptions[]"
											   value="<?php echo $group->ID; ?>">
									<?php else: ?>
										<input id="cb-select-1" type="checkbox" name="subscriptions[]"
											   value="<?php echo $group->ID; ?>" disabled><br>
									<?php endif; ?>
								</th>-->
								<td class="author column-author" data-colname="Autore" style="padding: 16px;">
									<span><?php echo $group->post_name; ?></span>
								</td>
								<td class="comment column-comment has-row-actions column-primary" style="padding: 16px;"
									data-colname="Commento">
									<table style="width:100%;border-collapse: collapse;">

										<tr>
											<td><b>ID</b></td>
											<td><b>Consegna</b></td>
										</tr>

										<?php foreach ($orders as $order): ?>
											<?php
											$consegna = get_post_meta($order->get_id(), '_numero_consegna', true);
											?>
											<tr>
												<td>#<?php echo $order->get_id(); ?>
													- <?php echo $order->get_shipping_first_name(); ?> <?php echo $order->get_shipping_last_name(); ?></td>
												<td><?php echo $consegna; ?></td>
											</tr>
										<?php endforeach; ?>
									</table>
								</td>


							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<br><br>

					<!--<button type="submit" class="button-primary">Genera Ordini</button>-->
				</form>
			</div>

			<div id="ajax-response"></div>

			<div class="clear"></div>
		</div>

		<?php
	});


	add_menu_page('Box Settimanali', 'Box Settimanali', 'manage_options', 'box-settimanali', function () {

		if (isset($_GET['delete_box'])) {
			wp_delete_post($_GET['delete_box']);

		}

		$boxs = get_posts([
			'post_type' => 'weekly-box',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		]);

		$date = new DateTime();
		$currentWeek = $date->format("W");


		$products = get_posts(array(
			'post_type' => 'product',
			'numberposts' => -1,
			'post_status' => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'field' => 'slug',
					'terms' => 'box singola',
					'operator' => 'IN',
				)
			),
		));

		$taxonomy = 'product_cat';
		$orderby = 'name';
		$show_count = 0;      // 1 for yes, 0 for no
		$pad_counts = 0;      // 1 for yes, 0 for no
		$hierarchical = 1;      // 1 for yes, 0 for no
		$title = '';
		$empty = 0;

		$args = array(
			'taxonomy' => $taxonomy,
			'orderby' => $orderby,
			'show_count' => $show_count,
			'pad_counts' => $pad_counts,
			'hierarchical' => $hierarchical,
			'title_li' => $title,
			'hide_empty' => $empty
		);
		$all_categories = get_categories($args);

		$jsonProducts = [];
		foreach ($all_categories as $cat) {
			if ($cat->category_parent == 0) {

				$category_id = $cat->term_id;

				$args2 = array(
					'taxonomy' => $taxonomy,
					'child_of' => 0,
					'parent' => $category_id,
					'orderby' => $orderby,
					'show_count' => $show_count,
					'pad_counts' => $pad_counts,
					'hierarchical' => $hierarchical,
					'title_li' => $title,
					'hide_empty' => $empty
				);
				$sub_cats = get_categories($args2);
				if ($sub_cats) {
					foreach ($sub_cats as $sub_category) {
						$categoryProducts = get_posts(array(
							'post_type' => 'product',
							'numberposts' => -1,
							'post_status' => 'publish',
							'tax_query' => array(
								array(
									'taxonomy' => 'product_cat',
									'field' => 'slug',
									'terms' => $sub_category->slug,
									'operator' => 'IN',
								)
							),
						));
						$categories[$sub_category->term_id] =
							[
								'name' => $sub_category->name,
								'products' => $categoryProducts
							];

						foreach ($categoryProducts as $categoryProduct) {


							$price = get_post_meta($categoryProduct->ID, '_price', true);
							$weight = get_post_meta($categoryProduct->ID, '_weight', true);

							$unitaMisura = ''; //tabella prodotti selezionati
							$measureUnit = get_post_meta($categoryProduct->ID, '_woo_uom_input', true);
							if (!empty($measureUnit)) {
								$unitaMisura = ' ' . $measureUnit;
							} else {
								$unitaMisura = ' gr';
							}

							$fornitore = get_post_meta($categoryProduct->ID, 'product_producer', true);
							$fornitoreString = '';
							if (!empty($fornitore)) {
								$fornitore = reset($fornitore);
								$fornitore = get_post($fornitore);
								$fornitoreString = $fornitore->post_title;
							}


							$jsonProducts[] = [
								'id' => $categoryProduct->ID,
								'name' => $categoryProduct->post_title,
								'weight' => $weight,
								'fornitore' => $fornitoreString,
								'unit_measure' => $unitaMisura,
								'codice_confezionamento' => get_post_meta($categoryProduct->ID, '_codice_confezionamento', true),
								'unit_measure_print' => get_post_meta($categoryProduct->ID, '_uom_acquisto', true),
								'price' => floatval($price)
							];
						}
					}
				}
			}
		}

		?>

		<script>
			let productIds = <?php echo json_encode($jsonProducts); ?>
		</script>
		<div id="wpbody-content">

			<div class="wrap" id="box-app">

				<div class="agr-create-new-boxes">
					<h1 class="wp-heading-inline">
						Box Settimanali</h1>

					<hr class="wp-header-end">

					<p v-text="message"></p>

					<p style="font-size:16px;margin-bottom:24px;">Qui puoi preparare le offerte della settimana.</p>

					<div style="display: flex; align-items: flex-start; justify-content:flex-start;">
						<div style="margin-right:24px;">
							<label style="font-size: 14px; font-weight: bold; margin-bottom:6px;display:block;">Settimana
								n°</label>
							<input class="change_week" name="week" id="week" value="<?php echo $currentWeek; ?>"
								   type="number" style="width:150px;">
						</div>
						<div>

							<label style="font-size: 14px; font-weight: bold; margin-bottom:6px;display:block;">Data
								Consegna</label>
							<?php $wednesday = date('Y-m-d', strtotime('wednesday this week')); ?>
							<input class="change_shipping_date" name="data_consegna" id="data_consegna"
								   value="<?php echo $wednesday; ?>" required type="date" style="width:150px;">
						</div>
					</div>
					<br><br>

					<div style="display: flex; align-items: flex-start; justify-content:flex-start;">
						<div style="margin-right:24px;">

							<label style="font-size: 14px; font-weight: bold; margin-bottom:6px;display:block;">Seleziona
								la Facciamo Noi</label>
							<select name="box_id" id="box_id" class="select2">
								<option disabled selected value="">-- Scegli la box --</option>
								<?php foreach ($products as $product): ?>
									<?php
									$product = wc_get_product($product->ID);
									if ($product->get_type() == 'variable-subscription') {
										continue;
									}

									$children = $product->get_children();
									?>
									<optgroup label="<?php echo $product->get_name(); ?>">
										<?php foreach ($children as $child): ?>
											<?php
											$child = wc_get_product($child);
											?>
											<option
												value="<?php echo $child->get_id() ?>"><?php echo $child->get_attribute('pa_tipologia') . ' - ' . $child->get_attribute('pa_dimensione'); ?></option>
										<?php endforeach; ?>
									</optgroup>
								<?php endforeach; ?>
							</select>

						</div>
						<div style="width:40%; padding-top: 24px;">
							<button class="button-secondary add-product" @click="copyFromLastWeek">Copia dalla settimana
								passata
							</button>
						</div>

					</div>

					<br><br>


					<div style="display: flex; align-items: flex-start; justify-content:flex-start;">
						<div style="width:40%;">

							<label style="font-size: 14px; font-weight: bold; margin-bottom:6px;display:block;">
								Prodotti in negozio</label>

							<select name="products_id" id="products_id" class="select2 agr-select" style="width: 100%">
								<option disabled selected value="">-- Scegli il prodotto --</option>
								<?php foreach ($categories as $category) {
									$args = array(
										'posts_per_page' => -1,
										'tax_query' => array(
											'relation' => 'AND',
											'hide_empty' => 1,
											'paged' => false,
											array(
												'taxonomy' => 'product_cat',
												'field' => 'slug',
												'terms' => $category['name']
											),
										),
										'post_type' => 'product',
										'orderby' => 'menu_order',
										'order' => 'asc',
										'meta_query' => array(
											array(
												'key' => '_is_active_shop',
												'value' => '1',
												'compare' => '=='
											)
										),
									);
									$cat_query = new WP_Query($args);
									$count_posts = new WP_Query($args);
									$posts_per_cat = $count_posts->found_posts;

									if ($posts_per_cat != 0) {
										echo '<optgroup label="' . $category['name'] . '">';
									}

									while ($cat_query->have_posts()) : $cat_query->the_post();
										//Valori prodotto
										$productID = get_the_ID();
										$price = get_post_meta($productID, '_regular_price', true);
										$weight = get_post_meta($productID, '_weight', true);
										$fornitore = get_post_meta($productID, 'product_producer', true);

										$measureUnit = get_post_meta($productID, '_woo_uom_input', true);
										if (!empty($measureUnit)) {
											$unitaMisura = ' ' . $measureUnit;
										} else {
											$unitaMisura = ' gr'; //select prodotti
										}
										$fornitoreString = '';
										if (!empty($fornitore)) {
											$fornitore = reset($fornitore);
											$fornitore = get_post($fornitore);
											$fornitoreString = $fornitore->post_title;
										}

										$codiceConfezionamento = get_post_meta($productID, '_codice_confezionamento', true);

										if (is_array($codiceConfezionamento) && empty($codiceConfezionamento)) {
											$codiceConfezionamento = '';
										}

										if (is_array($codiceConfezionamento) && !empty($codiceConfezionamento)) {
											$codiceConfezionamento = reset($codiceConfezionamento);
										}
										if ($codiceConfezionamento) {
											$codiceConfezionamento = $codiceConfezionamento;
										}

										//echo the_title() . ' '. $weight. ' <br>';
										echo '<option value="' . $productID . '" data-producer="' . $fornitoreString . '" data-conf="' . $codiceConfezionamento . '" data-weight="' . $weight . $unitaMisura . '" data-price="' . $price . '">' . get_the_title() . '</option>';
									endwhile; // end of the loop.
									wp_reset_postdata();

									echo '</optgroup>';
								} //endforeach category
								?>
							</select>
							<div style="display:block;width:100%;margin-top:16px;">
								<button class="button-primary add-product" @click="addProduct('products_id')">
									Aggiungi alla box
								</button>
							</div>
						</div>
						<div style="width:40%;margin-left:40px;">

							<label style="font-size: 14px; font-weight: bold; margin-bottom:6px;display:block;">
								Prodotti non in negozio</label>
							<select name="products_id" id="products_id_unavailable" class="select2 agr-select"
									style="width:100%;">
								<option disabled selected value="">-- Scegli il prodotto --</option>
								<?php foreach ($categories as $category) {
									$args = array(
										'posts_per_page' => -1,
										'tax_query' => array(
											'relation' => 'AND',
											'hide_empty' => 1,
											'paged' => false,
											array(
												'taxonomy' => 'product_cat',
												'field' => 'slug',
												'terms' => $category['name']
											),
										),
										'post_type' => 'product',
										'orderby' => 'menu_order',
										'order' => 'asc',
										'meta_query' => array(
											array(
												'key' => '_is_active_shop',
												'value' => '1',
												'compare' => '!='
											)
										),
									);
									$cat_query = new WP_Query($args);
									$count_posts = new WP_Query($args);
									$posts_per_cat = $count_posts->found_posts;

									if ($posts_per_cat != 0) {
										echo '<optgroup label="' . $category['name'] . '">';
									}

									while ($cat_query->have_posts()) : $cat_query->the_post();
										//Valori prodotto
										$productID = get_the_ID();
										$price = get_post_meta($productID, '_regular_price', true);
										$weight = get_post_meta($productID, '_weight', true);
										$fornitore = get_post_meta($productID, 'product_producer', true);
										$unitaMisura = ' gr';
										$measureUnit = get_post_meta($productID, '_woo_uom_input', true);
										if (!empty($measureUnit)) {
											$unitaMisura = ' ' . $measureUnit;
										}
										$fornitoreString = '';
										if (!empty($fornitore)) {
											$fornitore = reset($fornitore);
											$fornitore = get_post($fornitore);
											$fornitoreString = $fornitore->post_title;
										}

										$codiceConfezionamento = get_post_meta($productID, '_codice_confezionamento', true);

										if (is_array($codiceConfezionamento) && empty($codiceConfezionamento)) {
											$codiceConfezionamento = '';
										}

										if (is_array($codiceConfezionamento) && !empty($codiceConfezionamento)) {
											$codiceConfezionamento = reset($codiceConfezionamento);
										}
										if ($codiceConfezionamento) {
											$codiceConfezionamento = $codiceConfezionamento;
										}

										echo '<option value="' . $productID . '" data-producer="' . $fornitoreString . '" data-conf="' . $codiceConfezionamento . '" data-weight="' . $weight . $unitaMisura . '" data-price="' . $price . '">' . get_the_title() . '</option>';
									endwhile; // end of the loop.
									wp_reset_postdata();

									echo '</optgroup>';
								} //endforeach category
								?>
							</select>
							<div style="display:block;width:100%;margin-top:16px;">
								<button class="button-primary add-product"
										@click="addProduct('products_id_unavailable')">
									Aggiungi alla box
								</button>
							</div>
						</div>
					</div>
					<br><br>

					<table id="new-products" class="dataTable" style="border-collapse: collapse; width: 100%;">
						<thead>
						<th>Descrizione</th>
						<th style="width: 70px;">Peso</th>
						<th>Fornitore</th>
						<th>Prezzo</th>
						<th>Un. Misura</th>
						<th>Quantità</th>
						<th>Cod. Conf.</th>
						<th>Azioni</th>
						</thead>
						<tbody>
						<tr v-for="(product,index) of products">
							<td>
								<span v-html="product.name"></span>
							</td>
							<td style="width: 80px;">
								<span v-html="product.weight"></span>
								<span v-html="product.unit_measure"></span>
							</td>
							<td>
								<span v-html="product.fornitore"></span>
							</td>
							<td>
								€<span v-html="product.price"></span>
							</td>
							<td>
								<span v-html="product.unit_measure_print"></span>
							</td>
							<td>
								<input style="width:70px;float:left" type="number" v-model="product.quantity">
							</td>
							<td>
								<span v-html="product.codice_confezionamento"></span>
							</td>
							<td>
								<a href="#" @click="deleteProduct(index)">Elimina</a>
							</td>
						</tr>
						</tbody>
						<tfoot>
						<tr>
							<td style="border-top: 2px solid #000; border-bottom:none;"></td>
							<td style="width: 80px;border-top: 2px solid #000; border-bottom:none;">
								<b>Peso Totale</b><br/><b v-html="totalWeight"></b>
							</td>
							<td style="border-top: 2px solid #000; border-bottom:none;"></td>
							<td style="border-top: 2px solid #000; border-bottom:none;"
								style="border-top: 2px solid #000; border-bottom:none;">
								<b>Totale</b><br/><b v-html="totalPrice"></b>
							</td>
							<td style="border-top: 2px solid #000; border-bottom:none;"></td>
							<td style="border-top: 2px solid #000; border-bottom:none;"></td>
							<td style="border-top: 2px solid #000; border-bottom:none;"></td>
							<td style="border-top: 2px solid #000; border-bottom:none;"></td>
							<td style="border-top: 2px solid #000; border-bottom:none;"></td>

						</tr>
						</tfoot>
					</table>

					<br/><br/>
					<button class="button-primary add-product" @click="createBox" v-if="products.length>0">Crea Box
						Settimanale
					</button>

				</div>


				<form id="comments-form" method="POST"
					  action="" style="margin-top:100px;width:100%;">
					<input type="hidden" name="generate_orders" value="1">

					<table style="max-width: 100%;" class="wp-list-table datatable">
						<thead>
						<tr>
							<th scope="col" id="author" class="manage-column column-author sortable desc"
								style="width:100px;border:1px solid #f1f1f1;background-image: none !important;border-bottom: 1px solid #000;font-size: 16px;background: #fff;border-radius: 6px 6px 0 0;">
								<span style="padding-right:16px;">Settimana</span></th>
							<th scope="col" id="comment" class="manage-column column-comment column-primary"
								style="border:1px solid #f1f1f1;background-image: none !important;border-bottom: 1px solid #000;font-size: 16px;background: #fff;border-radius: 6px 6px 0 0;">
								<span style="padding-right:16px;">Box</span>
							</th>
							<th scope="col" id="comment" class="manage-column column-comment column-primary"
								style="border:1px solid #f1f1f1;background-image: none !important;border-bottom: 1px solid #000;font-size: 16px;background: #fff;border-radius: 6px 6px 0 0;">
								<span style="padding-right:16px;">Data consegna</span>
							</th>
							<th scope="col" id="comment" class="manage-column column-comment column-primary"
								style="border:1px solid #f1f1f1;background-image: none !important;border-bottom: 1px solid #000;font-size: 16px;background: #fff;border-radius: 6px 6px 0 0;">
								<span style="padding-right:16px;">Prodotti</span>
							</th>
							<th style="border:1px solid #f1f1f1;background-image: none !important;border-bottom: 1px solid #000;font-size: 16px;background: #fff;border-radius: 6px 6px 0 0;">
								<span style="padding-right:16px;">Azioni</span></th>
						</tr>
						</thead>

						<tbody id="the-comment-list" class="create-box-table--mega-table" data-wp-lists="list:comment">

						<?php foreach ($boxs as $box):
							$boxId = get_post_meta($box->ID, '_product_box_id', true);

							$productBox = get_post($boxId);

							if (!$productBox) {
								continue;
							}

							$week = get_post_meta($box->ID, '_week', true);
							$products = get_post_meta($box->ID, '_products', true);
							$dataConsegna = get_post_meta($box->ID, '_data_consegna', true);
							$productsAlreadyInBox = array_map(function ($p) {
								return $p['id'];
							}, $products);

							// fix nathi per errore data di consegna
							$fixdate = new DateTime($dataConsegna);
							?>

							<tr id="comment-1" class="comment even thread-even depth-1 approved">

								<td class="author column-author" data-colname="Autore" style="padding:25px 10px 10px;">
									<span class="create-box-table--span-item week">Settimana <?php echo $week; ?></span>
								</td>
								<td class="comment column-comment has-row-actions column-primary"
									data-colname="Commento" style="padding:25px 10px 10px;">
									<span
										class="create-box-table--span-item the-product"><?php echo $productBox->post_excerpt; ?></span>
								</td>
								<td class="comment column-comment has-row-actions column-primary"
									data-colname="Commento" style="padding:25px 10px 10px;">
									<span
										class="create-box-table--span-item delivery"><?php echo ($dataConsegna) ? $fixdate->format("d/m/Y") : '-'; ?></span>

								</td>
								<td class="response column-response">
									<table style="max-width: 100%;border-collapse: collapse">
										<thead>
										<th>Descrizione</th>
										<th style="width: 70px;">Peso</th>
										<th>Fornitore</th>
										<th>Prezzo</th>
										<th>Un. Misura</th>
										<th>Quantità</th>
										<th>Cod. Conf.</th>
										<th>Azioni</th>
										</thead>
										<tbody>
										<?php
										$totalWeight = 0;
										$totalPrice = 0;
										?>
										<?php foreach ($products as $key => $product): ?>
											<?php
											$weight = get_post_meta($product['id'], '_weight', true);

											if (!isset($product['price'])) {
												$product['price'] = 0;
											}

											$totalWeight += ($product['quantity'] * $weight);
											$totalPrice += ($product['price'] * $product['quantity']);

											$fornitore = get_post_meta($product['id'], 'product_producer', true);
											$fornitoreString = '';
											if (!empty($fornitore)) {
												$fornitore = reset($fornitore);
												$fornitore = get_post($fornitore);
												$fornitoreString = $fornitore->post_title;
											}

											$codiceConfezionamento = get_post_meta($product['id'], '_codice_confezionamento', true);

											if (is_array($codiceConfezionamento) && empty($codiceConfezionamento)) {
												$codiceConfezionamento = '';
											}

											if (is_array($codiceConfezionamento) && !empty($codiceConfezionamento)) {
												$codiceConfezionamento = reset($codiceConfezionamento);
											}

											$unitaMisura = ' gr'; //tabella riepilogo box
											$measureUnit = get_post_meta($product['id'], '_woo_uom_input', true);

											if (!empty($measureUnit)) {
												$unitaMisura = ' ' . $measureUnit;
											}
											if (!empty($measureUnit)) {
												$unitaMisura = ' ' . $measureUnit;
											}

											$measureAcquisto = get_post_meta($product['id'], '_uom_acquisto', true);
											$misura_acquisto = '-';
											if (!empty($measureAcquisto)) {
												$misura_acquisto = get_post_meta($product['id'], '_uom_acquisto', true);
											}

											?>

											<tr class="create-box-table--row">
												<td class="create-box-table--name">
													<a target="_blank"
													   href="<?php echo esc_url(home_url()) . '/wp-admin/post.php?post=' . $product['id'] . '&action=edit'; ?>"><?php echo $product['name']; ?></a>
												</td>
												<td class="create-box-table--weight" style="width: 70px;">
													<?php echo $weight . $unitaMisura; ?>
												</td>
												<td class="create-box-table--producer">
													<?php if ($fornitoreString): ?>
														<?php echo $fornitoreString; ?>
													<?php else: ?>
														<?php echo '-'; ?>
													<?php endif; ?>
												</td>
												<td class="create-box-table--price">
													€<?php echo number_format($product['price'] * $product['quantity'], 2); ?>
												</td>
												<td class="create-box-table--misura">
													<?php echo $misura_acquisto; ?>
												</td>
												<td class="create-box-table--quantity" style="display:flex;">
													<input style="width:70px;" readonly
														   value="<?php echo $product['quantity']; ?>"
														<?php if ($week < $currentWeek): ?> disabled <?php endif; ?>
														   type="number"
														   name="quantity[<?php echo $key; ?>][]">
												</td>
												<td class="create-box-table--conf">
													<?php if ($codiceConfezionamento): ?>
														<?php echo $codiceConfezionamento; ?>
													<?php else: ?>
														<?php echo '-'; ?>
													<?php endif; ?>
												</td>
												<td class="create-box-table--actions">
													<a class="delete-product-box" data-box-id="<?php echo $box->ID; ?>"
													   data-index="<?php echo $key; ?>"
													   href="#">Elimina</a>
												</td>
											</tr>
										<?php endforeach; ?>
										<?php if ($week >= $currentWeek): ?>
											<tr class="create-box-table--add-product-row">
												<td class="create-box-table--add-product-item"
													style="border-bottom:none;">
													<select data-box-id="<?php echo $box->ID; ?>"
															class="select2 new-product-box">
														<option disabled selected value="">-- Scegli il prodotto --
														</option>
														<?php foreach ($categories as $category): ?>
															<optgroup label="<?php echo $category['name']; ?>">

																<?php
																$category['products'] = array_filter($category['products'], function ($product) use ($productsAlreadyInBox) {
																	return !in_array($product->ID, $productsAlreadyInBox);
																});
																?>
																<?php foreach ($category['products'] as $product): ?>
																	<?php

																	$price = get_post_meta($product->ID, '_price', true);
																	$unitaMisura = 'gr';

																	$measureUnit = get_post_meta($product->ID, '_woo_uom_input', true);

																	if (!empty($measureUnit)) {
																		$unitaMisura = $measureUnit;
																	}

																	$fornitore = get_post_meta($product->ID, 'product_producer', true);
																	$fornitoreString = '';
																	if (!empty($fornitore)) {
																		$fornitore = reset($fornitore);
																		$fornitore = get_post($fornitore);
																		$fornitoreString = ' - ' . $fornitore->post_title;
																	}

																	$codiceConfezionamento = get_post_meta($product->ID, '_codice_confezionamento', true);


																	if (is_array($codiceConfezionamento) && empty($codiceConfezionamento)) {
																		$codiceConfezionamento = '';
																	}

																	if (is_array($codiceConfezionamento) && !empty($codiceConfezionamento)) {
																		$codiceConfezionamento = reset($codiceConfezionamento);
																	}
																	if ($codiceConfezionamento) {
																		$codiceConfezionamento = ' - ' . $codiceConfezionamento;
																	}
																	$weight = get_post_meta($product->ID, '_weight', true);

																	?>
																	<option
																		data-price="<?php echo $price; ?>"
																		data-name="<?php echo str_replace('"', '', $product->post_title); ?>"
																		data-unit-measure="<?php echo $unitaMisura; ?>"
																		value="<?php echo $product->ID ?>"><?php echo $product->post_title . '(' . $weight . $measureUnit . ') ' . $fornitoreString . $codiceConfezionamento; ?></option>
																<?php endforeach; ?>
															</optgroup>
														<?php endforeach; ?>
													</select>
												</td>
												<td style="border-bottom:none;"></td>
												<td style="border-bottom:none;"></td>
												<td style="border-bottom:none;"></td>
												<td class="create-box-table--add-product-qty" colspan="2"
													style="display: flex;align-items: center;border-bottom:none;">
													<input
														style="width:70px"
														type="number"
														name="quantity" class="new-quantity">
												</td>
												<td style="border-bottom:none;"></td>
												<td class="create-box-table--add-product-actions"
													style="border-bottom:none;">
													<a class="add-product-box" data-box-id="<?php echo $box->ID; ?>"
													   href="#">Aggiungi</a>
												</td>
											</tr>
										<?php endif; ?>

										<tr class="create-box-table--totals">
											<td style="border-top:2px solid #000;border-bottom:none;"></td>
											<td style="border-top:2px solid #000;border-bottom:none;"><strong>Peso
													Box</strong></td>
											<td style="border-top:2px solid #000;border-bottom:none;"></td>
											<td style="border-top:2px solid #000;border-bottom:none;">
												<strong>Totale</strong></td>
											<td style="border-top:2px solid #000;border-bottom:none;"></td>
											<td style="border-top:2px solid #000;border-bottom:none;"></td>
											<td style="border-top:2px solid #000;border-bottom:none;"></td>
											<td style="border-top:2px solid #000;border-bottom:none;"></td>
										</tr>
										<tr>
											<td style="border-bottom:none;"></td>
											<td style="border-bottom:none;"><?php echo $totalWeight; ?> gr</td>
											<td style="border-bottom:none;"></td>
											<td style="border-bottom:none;">€<?php echo $totalPrice; ?></td>
											<td style="border-bottom:none;"></td>
											<td style="border-bottom:none;"></td>
											<td style="border-bottom:none;"></td>
											<td style="border-bottom:none;"></td>
										</tr>
										</tbody>
									</table>
									<span>
								</span>
								</td>
								<td style="padding:25px 10px 10px;">
									<a href="/wp-admin/admin.php?page=box-settimanali&delete_box=<?php echo $box->ID; ?>">Elimina
										box settimanale</a>
								</td>

							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<br><br>

					<!--<button type="submit" class="button-primary">Genera Ordini</button>-->
				</form>
			</div>

			<div id="ajax-response"></div>

			<div class="clear"></div>
		</div>

		<?php
	});


	add_menu_page('Esporta Documenti', 'Esporta Documenti', 'manage_options', 'esporta-documenti', function () {
		global $wpdb;

		if (isset($_POST['document_type'])) {
			require_once get_template_directory() . '/libraries/dompdf/autoload.inc.php';
			require_once get_template_directory() . '/inc/pdf/' . $_POST['document_type'] . '.php';
			die();
		}


		$sql = "SELECT meta_value from wp_postmeta where meta_key='_codice_confezionamento' group by meta_value";
		$confezionamento = $wpdb->get_results($sql, ARRAY_A);

		$confezionamento = array_map(function ($cod) {
			return $cod['meta_value'];
		}, $confezionamento);


		$allDataConsegna = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_data_consegna' group by meta_value", ARRAY_A);
		?>
		<div id="wpbody-content">

			<div class="wrap" id="box-app">


				<h1 class="wp-heading-inline">
					Esporta Documenti</h1>

				<hr class="wp-header-end">
				<form method="POST" action="/wp-admin/admin.php?noheader=1&page=esporta-documenti" target="_blank">
					<label>Data di consegna</label><br>

					<?php if (count($allDataConsegna) == 0): ?>
						<i>Nessun ordine con data consegna.</i>
					<?php else: ?>
						<select name="data_consegna" autocomplete="off">
							<?php
							foreach ($allDataConsegna as $dataConsegna):
								// fix nathi per errore data di consegna
								$fixdate = $dataConsegna['meta_value'];
								$fixdate = new DateTime($fixdate);
								?>
								<?php if (is_array($dataConsegna['meta_value']) || empty($dataConsegna['meta_value'])) continue; ?>
								<option
									value="<?php echo $dataConsegna['meta_value']; ?>"><?php echo $fixdate->format('d/m/Y'); ?></option>
							<?php endforeach; ?>
						</select>

					<?php endif; ?>
					<br><br>

					<h4>Codice di confezionamento</h4><br>
					<div style="display: flex">
						<div>
							<label>Dal</label><br>
							<select class="select2" name="confezionamento_dal">
								<option value="">-- Seleziona --</option>
								<?php foreach ($confezionamento as $codice): ?>
									<option value="<?php echo $codice; ?>"><?php echo $codice; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div>
							<label>Al</label><br>
							<select class="select2" name="confezionamento_al">
								<option value="">-- Seleziona --</option>
								<?php foreach ($confezionamento as $codice): ?>
									<option value="<?php echo $codice; ?>"><?php echo $codice; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>


					<br><br>
					<label>Cosa vuoi esportare?</label><br>

					<select class="select2" name="document_type">
						<option value="prelievi_magazzino_cliente">Lista prelievi magazzino per cliente</option>
						<option value="prelievi_magazzino_articolo">Lista prelievi magazzino per articolo</option>
						<option value="fabbisogno">Fabbisogno</option>
						<option value="confezionamento">Stampa per confezionamento</option>
						<option value="riepilogo_spedizione">Riepilogo di consegna</option>
						<option value="etichette">Etichette</option>
					</select>


					<button type="submit" class="button-primary">Scarica PDF</button>

				</form>


			</div>

			<div class="clear"></div>
		</div>

		<?php
	});

}


add_action('woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3);
add_action('woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2);
add_filter('woocommerce_available_variation', 'load_variation_settings_fields');

function variation_settings_fields($loop, $variation_data, $variation)
{

	$categoriesSelect = [];

	$taxonomy = 'product_cat';
	$orderby = 'name';
	$show_count = 0;      // 1 for yes, 0 for no
	$pad_counts = 0;      // 1 for yes, 0 for no
	$hierarchical = 1;      // 1 for yes, 0 for no
	$title = '';
	$empty = 0;

	$args = array(
		'taxonomy' => $taxonomy,
		'orderby' => $orderby,
		'show_count' => $show_count,
		'pad_counts' => $pad_counts,
		'hierarchical' => $hierarchical,
		'title_li' => $title,
		'hide_empty' => $empty
	);
	$all_categories = get_categories($args);
	foreach ($all_categories as $cat) {

		if ($cat->category_parent == 0) {

			$category_id = $cat->term_id;
			$categoriesSelect[$category_id] = strtoupper('TUTTI ' . $cat->name);

			$args2 = array(
				'taxonomy' => $taxonomy,
				'child_of' => 0,
				'parent' => $category_id,
				'orderby' => $orderby,
				'show_count' => $show_count,
				'pad_counts' => $pad_counts,
				'hierarchical' => $hierarchical,
				'title_li' => $title,
				'hide_empty' => $empty
			);
			$sub_cats = get_categories($args2);
			if ($sub_cats) {
				foreach ($sub_cats as $sub_category) {
					$categoriesSelect[$sub_category->term_id] = $cat->name . ' > ' . $sub_category->name;
				}
			}
		}
	}
	/*
		$categoriesVariation = get_post_meta($variation->ID, '_box_categories', true);

		woocommerce_wp_multi_select(array(
			'id' => "_box_categories{$loop}",
			'name' => "_box_categories[{$loop}][]",
			'wrapper_class' => 'form-row form-row-full',
			'label' => 'Categorie compatibili',
			'options' => $categoriesSelect,
			'value' => $categoriesVariation
		), $variation_data->ID);
	*/
}

function save_variation_settings_fields($variation_id, $loop)
{

	if (isset($_POST['_box_categories'][$loop])) {
		$post_data = $_POST['_box_categories'][$loop];
		$sanitize_data = [];
		if (is_array($post_data) && !empty($post_data)) {
			foreach ($post_data as $value) {
				$sanitize_data[] = intval(esc_attr($value));
			}
		}
		update_post_meta($variation_id, '_box_categories', $sanitize_data);
	}

}

function load_variation_settings_fields($variation)
{
	$variation['box_categories'] = get_post_meta($variation['variation_id'], 'box_categories', true);

	return $variation;
}


function woocommerce_wp_multi_select($field, $variation_id = 0)
{
	global $thepostid, $post;

	if ($variation_id == 0)
		$the_id = empty($thepostid) ? $post->ID : $thepostid;
	else
		$the_id = $variation_id;

	$field['class'] = isset($field['class']) ? $field['class'] : 'select short';
	$field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
	$field['name'] = isset($field['name']) ? $field['name'] : $field['id'];

	$meta_data = maybe_unserialize(get_post_meta($the_id, $field['id'], true));
	$meta_data = $meta_data ? $meta_data : array();

	$field['value'] = isset($field['value']) ? $field['value'] : $meta_data;

	echo '<p class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label><select id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['name']) . '" class="' . esc_attr($field['class']) . '" multiple="multiple">';

	foreach ($field['options'] as $key => $value) {
		echo '<option value="' . esc_attr($key) . '" ' . (is_array($field['value']) && in_array($key, $field['value']) ? 'selected="selected"' : '') . '>' . esc_html($value) . '</option>';
	}
	echo '</select> ';
	if (!empty($field['description'])) {
		if (isset($field['desc_tip']) && false !== $field['desc_tip']) {
			echo '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />';
		} else {
			echo '<span class="description">' . wp_kses_post($field['description']) . '</span>';
		}
	}
}


add_filter('manage_delivery-group_posts_columns', function ($columns) {
	$columns['week'] = 'CSV';
	return $columns;
});
// Add the data to the custom columns for the book post type:
add_action('manage_delivery-group_posts_custom_column', function ($column, $post_id) {
	switch ($column) {

		case 'week' :
			global $wpdb;
			$allDataConsegna = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_data_consegna' group by meta_value", ARRAY_A);

			$date = new DateTime();
			$currentWeek = $date->format("W");


			?>
			<?php if (count($allDataConsegna) == 0): ?>
			<i>Nessun ordine con data consegna.</i>
		<?php else: ?>
			<select name="data_consegna" autocomplete="off">
				<?php
				foreach ($allDataConsegna as $dataConsegna):
					// fix nathi per errore data di consegna
					$fixdate = $dataConsegna['meta_value'];
					$fixdate = new DateTime($fixdate); ?>
					<option
						value="<?php echo $dataConsegna['meta_value']; ?>"><?php echo $fixdate->format('d/m/Y'); ?></option>
				<?php endforeach; ?>
			</select>

		<?php endif; ?>
			<a class="btn button-primary generate-csv" data-delivery-group="<?php echo $post_id; ?>">
				Genera CSV
			</a>

			<br>
			<em>Settimana corrente: <?php echo $currentWeek; ?></em>
			<?php
			break;

	}
}, 10, 2);


function my_saved_post($post_id, $json, $is_update)
{

	$product = wc_get_product($post_id);

	if ($product) {
		// Retrieve the import ID.
		// Convert SimpleXml object to array for easier use.

		if (isset($json->_percentuale_ricarico)) {
			update_post_meta($post_id, '_percentuale_ricarico', (string)$json->_percentuale_ricarico);
		}
		if (isset($json->costounitario)) {
			update_post_meta($post_id, '_prezzo_acquisto', number_format((string)$json->costounitario, 2));
		}
		if (isset($json->codicecategoriaconfezionamento)) {
			update_post_meta($post_id, '_codice_confezionamento', (string)$json->codicecategoriaconfezionamento);
		}
		if (isset($json->_is_magazzino)) {
			update_post_meta($post_id, '_is_magazzino', (string)$json->_is_magazzino);
		}
		if (isset($json->_uom_acquisto)) {
			update_post_meta($post_id, '_uom_acquisto', (string)$json->_uom_acquisto);
		}
		if (isset($json->_qty_acquisto)) {
			update_post_meta($post_id, '_qty_acquisto', (string)$json->_qty_acquisto);
		}

		$product->set_manage_stock(true);
		if (isset($json->scorte)) {
			$product->set_stock_quantity((string)$json->scorte);
		}
		$product->set_stock_status();

		$json->costounitario = str_replace(",", '.', (string)$json->costounitario);


		$price = number_format((string)$json->costounitario, 2);

		if (!isset($json->_percentuale_ricarico) || empty($json->_percentuale_ricarico)) {
			$json->_percentuale_ricarico = 0;
		}

		if (is_array($json->_percentuale_ricarico)) {
			$json->_percentuale_ricarico = (string)$json->_percentuale_ricarico[0];
		}

		$json->_percentuale_ricarico = str_replace(",", '.', (string)$json->_percentuale_ricarico);


		$price *= (1 + (string)$json->_percentuale_ricarico / 100);
		$price = number_format($price, 2);

		$iva = (string)$json->iva;

		if (empty(trim($iva))) {
			$iva = 0;
		}

		if ($iva > 0) {
			$price = $price + ($iva * ($price / 100));
			$price = round($price, 2);
		}

		$product->set_regular_price($price);
		$product->set_price($price);
		$product->save();
		wc_delete_product_transients($product->get_id());
		// Do something.
	}

}

add_action('pmxi_saved_post', 'my_saved_post', 10, 3);
