<?php
function dd($vars)
{
	die(var_dump($vars));
}

add_action('rest_api_init', function () {
	register_rest_route('agrispesa/v1', 'weekly-box', array(
		'methods' => 'POST',
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
});


function my_enqueue($hook)
{
	if ('toplevel_page_box-settimanali' !== $hook) {
		return;
	}


	wp_register_style('select2css', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', false, '1.0', 'all');
	wp_register_script('select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), '1.0', true);

	wp_enqueue_style('select2css');
	wp_enqueue_script('select2');


	wp_register_script('axios', '//cdnjs.cloudflare.com/ajax/libs/axios/1.2.2/axios.min.js', array(), null, true);
	wp_enqueue_script('axios');

	wp_register_script('vuejs', '//unpkg.com/vue@3/dist/vue.global.js', array(), null, true);
	wp_enqueue_script('vuejs');

	wp_enqueue_script('axios');

	wp_register_style('datatable', '//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css', false, '1.0', 'all');
	wp_enqueue_style('datatable');

	wp_register_script('datatable-js', '//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js', array(), null, true);
	wp_enqueue_script('datatable-js');

	wp_enqueue_style('agrispesa-admin-css', get_theme_file_uri('assets/css/admin.css'), false, '1.0', 'all');
	wp_enqueue_script('agrispesa-admin-js', get_theme_file_uri('assets/js/admin.js'), array('jquery', 'select2'), null, true);
}

add_action('admin_enqueue_scripts', 'my_enqueue');

// Adding Meta container admin shop_order pages
add_action('add_meta_boxes', 'mv_add_meta_boxes');
if (!function_exists('mv_add_meta_boxes')) {
	function mv_add_meta_boxes()
	{
		add_meta_box(
			'box_preferences',
			'Preferenze BOX ',
			'box_preferences_meta_box_callback',
			'shop_order',
			'advanced',
			'core',
			[]
		);

		add_meta_box('mv_other_fields', 'Informazioni BOX', 'mv_add_other_fields_for_packaging', 'shop_order', 'side', 'core');
	}

	function box_preferences_meta_box_callback($order)
	{
		global $post;

		$subscriptionId = get_post_meta($post->ID, '_subscription_id', true);
		echo $subscriptionId;

		?>
		<h4>Da Eliminare</h4><br>

		<h4>Da Aggiungere</h4>
		<?php

	}

}

// Adding Meta field in the meta container admin shop_order pages
if (!function_exists('mv_add_other_fields_for_packaging')) {
	function mv_add_other_fields_for_packaging()
	{
		global $post;

		$weight = get_post_meta($post->ID, '_total_box_weight', true);
		$week = get_post_meta($post->ID, '_week', true);

		if (empty($weight)) {
			$weight = 0;
		}
		echo '<span>Peso della BOX: <strong>' . $weight . 'Kg</strong></span><br>';
		echo '<span>Settimana: <strong>' . $week . '</strong></span>';

	}
}


function create_order_from_subscription($id)
{
	$subscription = wcs_get_subscription($id);

	if (!$subscription) {
		return false;
	}

	$products = $subscription->get_items();
	$product = reset($products)->get_product();
	$productData = $product->get_data();

	$weight = 0;
	if (!empty($productData['weight'])) {
		$weight = $productData['weight'];
	}

	$customerId = $subscription->get_user_id();

	$order = wc_create_order();
	$order->set_customer_id($customerId);


	// The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
	//$order->add_product(get_product('275962'), 1);
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


	$date = new DateTime();
	$week = $date->format("W");

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
	update_post_meta($order->get_id(), '_order_type', 'BOX');
	update_post_meta($order->get_id(), '_subscription_id', $id);

}

function register_my_custom_submenu_page()
{
	add_submenu_page('woocommerce', 'Genera Ordini Box', 'Genera Ordini Box', 'manage_options', 'my-custom-submenu-page', 'my_custom_submenu_page_callback');
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

	?>

	<div id="wpbody-content">

		<div class="wrap">
			<h1 class="wp-heading-inline">
				Genera Ordini BOX</h1>

			<h2>Settimana <?php echo $week; ?> di 52</h2>
			<hr class="wp-header-end">

			<p>In questa pagina puoi generare in automatico gli ordini per gli abbonamenti delle BOX attivi, in base
				alle loro preferenze espresse. Potrai modificare successivamente il singolo ordine modificando i
				prodotti che preferisci.</p>

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
						<span class="displaying-num">2 abbonamenti attivi</span>
					</div>
					<br class="clear">
				</div>
				<h2 class="screen-reader-text">Elenco abbonamenti</h2>
				<table class="wp-list-table widefat fixed striped table-view-list comments">
					<thead>
					<tr>
						<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text"
																						for="cb-select-all-1">Seleziona
								tutto</label><input id="cb-select-all-1" type="checkbox"></td>
						<th scope="col" id="author" class="manage-column column-author sortable desc">
							<span>Utente</span></th>
						<th scope="col" id="comment" class="manage-column column-comment column-primary">Abbonamento
						</th>
						<th scope="col" id="comment" class="manage-column column-comment column-primary">Attivo da</th>
						<th>
							Ordine
						</th>
					</tr>
					</thead>

					<tbody id="the-comment-list" data-wp-lists="list:comment">
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

						?>
						<tr id="comment-1" class="comment even thread-even depth-1 approved">
							<th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-1">Seleziona
									un abbonamento</label>

								<input id="cb-select-1" type="checkbox" name="subscriptions[]"
									   value="<?php echo $subscription->get_id(); ?>"
									<?php if (count($orders) > 0): ?>
										disabled
									<?php endif; ?>
								><br>
							</th>
							<td class="author column-author" data-colname="Autore">
								<span><?php echo $subscription->get_billing_first_name() . " " . $subscription->get_billing_first_name(); ?></span>
							</td>
							<td class="comment column-comment has-row-actions column-primary" data-colname="Commento">
								<span><?php $products = $subscription->get_items();
									foreach ($products as $product) {
										echo $product->get_name();
									}
									?>
									</span>
							</td>

							<td class="response column-response" data-colname="In risposta a">
								<span>
								<?php
								echo (new DateTime($subscription->get_date_created()))->format("d-m-Y H:i"); ?>
								</span>
							</td>
							<td>
								<?php if (count($orders) > 0): ?>
									<a href="/wp-admin/post.php?post=<?php echo $orders[0]->ID ?>&action=edit">Vai
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
			$reordered_columns['my-column1'] = 'Tipo';
			$reordered_columns['my-column2'] = 'Settimana';
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
				echo $orderType;
			// Testing (to be removed) - Empty value case
			else
				echo '';

			break;

		case 'my-column2' :
			// Get custom post meta data
			$week = get_post_meta($post_id, '_week', true);
			if (!empty($week))
				echo $week;

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

		?>
		<div id="wpbody-content">

			<div class="wrap">
				<h1 class="wp-heading-inline">
					Consegne Ordini</h1>

				<hr class="wp-header-end">

				<p>In questa pagina puoi generare in automatico gli ordini per gli abbonamenti delle BOX attivi, in base
					alle loro preferenze espresse. Potrai modificare successivamente il singolo ordine modificando i
					prodotti che preferisci.</p>

				<form id="comments-form" method="POST"
					  action="">

					<input type="hidden" name="generate_orders" value="1">
					<table class="wp-list-table widefat fixed striped table-view-list comments">
						<thead>
						<tr>
							<!--<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text"
																							for="cb-select-all-1">Seleziona
									tutto</label><input id="cb-select-all-1" type="checkbox"></td>-->
							<th scope="col" id="author" class="manage-column column-author sortable desc">
								<span>Gruppo</span></th>
							<th scope="col" id="comment" class="manage-column column-comment column-primary">Ordini
							</th>
							<th scope="col" id="comment" class="manage-column column-comment column-primary">Attivo da
							</th>
							<th>
								Ordine
							</th>
						</tr>
						</thead>

						<tbody id="the-comment-list" data-wp-lists="list:comment">
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
								<td class="author column-author" data-colname="Autore">
									<span><?php echo $group->post_name; ?></span>
								</td>
								<td class="comment column-comment has-row-actions column-primary"
									data-colname="Commento">
									<?php foreach ($orders as $order): ?>
										<span>
											#<?php echo $order->get_id(); ?> - <?php echo $order->get_shipping_first_name(); ?> <?php echo $order->get_shipping_last_name(); ?>
									</span><br>
									<?php endforeach; ?>
								</td>

								<td class="response column-response" data-colname="In risposta a">
								<span>

								</span>
								</td>
								<td>

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
		$week = $date->format("W");


		$products = get_posts(array(
			'post_type' => 'product',
			'numberposts' => -1,
			'post_status' => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'field' => 'slug',
					'terms' => 'box',
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
								'name' => $cat->name . ' > ' . $sub_category->name,
								'products' => $categoryProducts
							];

						foreach ($categoryProducts as $categoryProduct) {
							$jsonProducts[] = [
								'id' => $categoryProduct->ID,
								'name' => $categoryProduct->post_title
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


				<h1 class="wp-heading-inline">
					Box Settimanali</h1>

				<hr class="wp-header-end">

				<p v-text="message"></p>

				<h5>Crea una box settimanale</h5>

				<label>Settimana</label>
				<input name="week" id="week" value="<?php echo $week; ?>" type="number" readonly>
				<br>

				<label>Box</label>
				<select name="box_id" id="box_id" class="select2">
					<option disabled selected>-- Scegli la box --</option>
					<?php foreach ($products as $product): ?>
						<option value="<?php echo $product->ID ?>"><?php echo $product->post_title; ?></option>
					<?php endforeach; ?>
				</select>

				<br>

				<label>Prodotto da inserire</label>
				<select name="products_id" id="products_id" class="select2">
					<option disabled selected>-- Scegli il prodotto --</option>
					<?php foreach ($categories as $category): ?>
						<optgroup label="<?php echo $category['name']; ?>">
							<?php foreach ($category['products'] as $product): ?>
								<option value="<?php echo $product->ID ?>"><?php echo $product->post_title; ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select><br>

				<button class="button-primary add-product" @click="addProduct">Aggiungi alla box</button>
				<br><br>

				<div class="row">
					<div class="product-box" v-for="(product,index) of products">
						<a href="#" @click="deleteProduct(index)">Elimina</a>
						<h4 v-html="product.name"></h4>
						<label>Quantità</label>
						<input type="number" v-model="product.quantity">
					</div>
				</div>
				<br><br>

				<button class="button-primary add-product" @click="createBox">Crea Box Settimanale</button>


				<form id="comments-form" method="POST"
					  action="" style="margin-top:100px">

					<input type="hidden" name="generate_orders" value="1">
					<table class="wp-list-table datatable">
						<thead>
						<tr>
							<th scope="col" id="author" class="manage-column column-author sortable desc">
								<span>Settimana</span></th>
							<th scope="col" id="comment" class="manage-column column-comment column-primary">Box
							</th>
							<th scope="col" id="comment" class="manage-column column-comment column-primary">Prodotti
							</th>
							<th></th>
						</tr>
						</thead>

						<tbody id="the-comment-list" data-wp-lists="list:comment">

						<?php foreach ($boxs as $box):
							$boxId = get_post_meta($box->ID, '_product_box_id', true);

							$productBox = get_post($boxId);

							if (!$productBox) {
								continue;
							}

							$week = get_post_meta($box->ID, '_week', true);
							$products = get_post_meta($box->ID, '_products', true);

							?>

							<tr id="comment-1" class="comment even thread-even depth-1 approved">

								<td class="author column-author" data-colname="Autore">
									<span><?php echo $week; ?></span>
								</td>
								<td class="comment column-comment has-row-actions column-primary"
									data-colname="Commento">
									<span><?php echo $productBox->post_title; ?></span>
								</td>

								<td class="response column-response" data-colname="In risposta a">
									<table>
										<tbody>
										<?php foreach ($products as $key => $product): ?>
											<tr>
												<td><?php echo $product['name']; ?></td>
												<td><input value="<?php echo $product['quantity']; ?>" type="number"
														   name="quantity[<?php echo $key; ?>][]">Kg
												</td>
												<td>
													<a class="delete-product-box" data-index="<?php echo $key; ?>"
													   href="#">Elimina</a>
												</td>
											</tr>
										<?php endforeach; ?>
										</tbody>
									</table>
									<span>
								</span>
								</td>
								<td>
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

	$categoriesVariation = get_post_meta($variation->ID, '_box_categories', true);

	woocommerce_wp_multi_select(array(
		'id' => "_box_categories{$loop}",
		'name' => "_box_categories[{$loop}][]",
		'wrapper_class' => 'form-row form-row-full',
		'label' => 'Categorie compatibili',
		'options' => $categoriesSelect,
		'value' => $categoriesVariation
	), $variation_data->ID);

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
		echo '<option value="' . esc_attr($key) . '" ' . (in_array($key, $field['value']) ? 'selected="selected"' : '') . '>' . esc_html($value) . '</option>';
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


