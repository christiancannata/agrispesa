<?php
function array_insert_after(array $array, $key, array $new)
{
	$keys = array_keys($array);
	$index = array_search($key, $keys);
	$pos = false === $index ? count($array) : $index + 1;

	return array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
}


function enqueue_box_js()
{
	global $wp_query;

	if (isset($wp_query->query_vars['personalizza-scatola'])) {


		wp_register_style('select2css', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', false, '1.0', 'all');
		wp_register_script('select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), '1.0', true);

		wp_enqueue_style('select2css');
		wp_enqueue_script('select2');


		wp_register_script('axios', '//cdnjs.cloudflare.com/ajax/libs/axios/1.2.2/axios.min.js', array(), null, true);
		wp_enqueue_script('axios');

		wp_register_script('vuejs', '//unpkg.com/vue@3/dist/vue.global.js', array(), null, true);
		wp_enqueue_script('vuejs');
		wp_enqueue_script('agrispesa-box-js', get_theme_file_uri('assets/js/box.js'), array('jquery', 'select2'), null, true);

	}
}

add_action('wp_enqueue_scripts', 'enqueue_box_js');

function add_settings_box_endpoint()
{
	add_rewrite_endpoint('personalizza-scatola', EP_ROOT | EP_PAGES);
	add_rewrite_endpoint('fatture', EP_ROOT | EP_PAGES);

}

add_action('init', 'add_settings_box_endpoint');


function ts_redirect_login($redirect)
{
	$redirect_page_id = url_to_postid($redirect);
	$checkout_page_id = wc_get_page_id('checkout');

	if ($redirect_page_id == $checkout_page_id) {
		return $redirect;
	}

	return '/negozio';
}

add_filter('woocommerce_login_redirect', 'ts_redirect_login');


function settings_box_query_vars($vars)
{

	//$vars[] = 'personalizza-scatola';
	$vars[] = 'fatture';

	return $vars;

}

add_filter('query_vars', 'settings_box_query_vars', 0);

// ------------------

// 3. Insert the new endpoint into the My Account menu

function settings_box_link_my_account($items)
{
	$newMenu = [];
	$newMenu['orders'] = 'Ordini';
	$newMenu['fatture'] = 'Documenti contabili';
	$newMenu['woo-wallet'] = 'Acquista Credito';
	$newMenu['payment-methods'] = 'Metodi di pagamento';
	$newMenu['edit-account'] = 'Account';
	$newMenu['edit-address'] = 'Indirizzi';
	$newMenu['gift-cards'] = 'Carte Regalo';
	$newMenu['subscriptions'] = 'Facciamo Noi';
	$newMenu['customer-logout'] = 'Esci';

	return $newMenu;

}

add_filter('woocommerce_account_menu_items', 'settings_box_link_my_account');

// ------------------

// 4. Add content to the new tab

function settings_box_content()
{
	?>
	<div class="woocommerce-PreferenzeBox-content" id="box-app">

		<div class="woocommerce-notices-wrapper"></div>
		<h3 class="my-account--minititle">Personalizza la scatola</h3>

		<div class="account-banner">
			<div class="account-banner--text">
				<h3 class="account-banner--title"><span class="icon-heart"></span>Solo vero amore.</h3>
				<p class="account-banner--subtitle">Seleziona quali alimenti non vuoi trovare nella tua scatola.
					<br/>Riceverai solo i prodotti che preferisci. E li amerai alla follia.</p>
			</div>
		</div>

		<div class="table-shadow-relative">
			<div

				class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">

				<div v-for="subscription of subscriptions" class="subscription-box">

					<div class="box-name">
						<div>
							<h3 class="my-account--minititle" v-html="subscription.name"></h3>
						</div>
					</div>


					<h4 class="my-account--minititle small">Desidero non ricevere</h4>
					<p class="subscription-preferences-description">Sei allergico a qualcosa? Non ami i broccoli?<br/>Nessun
						problema! Seleziona i prodotti che non voi mai ricevere con questo abbonamento.</p>
					<div class="container-flex-box">

						<div class="right-categories-box">
							<ul v-if="categories.length > 0">
								<li :class="{'active':currentCategory == category}" v-for="category of categories">
									<a href="#" @click.prevent="currentCategory = category" v-html="category.name"></a>
								</li>
							</ul>
						</div>

						<div class="products-box" v-if="currentCategory && currentCategory.products.length > 0">

							<ul>
								<li :key="'list_all'">
									<label @click.prevent="toggleAllBlacklist(currentCategory,subscription)"
										   class="checkbox-container">
										<span class="label">Seleziona tutti</span>
										<input :checked="currentCategory.is_all_blacklist_selected" type="checkbox">
										<span class="checkmark"></span>
									</label>
								</li>
								<li :key="'list_'+subscription.id" v-for="product of currentCategory.products">
									<label @click.prevent="toggleBlacklist(product.code,subscription)"
										   class="checkbox-container">
										<span class="label" v-html="product.post_title"></span>
										<input :checked="isBlacklisted(product.code,subscription)" type="checkbox">
										<span class="checkmark"></span>
									</label>
								</li>

							</ul>
						</div>

						<div class="blacklist-box">
							<h4 class="my-account--minititle small">Prodotti che non ami</h4>
							<div class="blacklist-item" v-for="(preference) of subscription.box_blacklist">
								<a class="delete_item" @click.prevent="deleteBlacklist(subscription,preference.id)"
								   href="#"><span class="icon-close"></span></a>
								<span v-html="preference.name"></span>
							</div>
						</div>
					</div>
					<!--
										<h4 class="my-account--minititle small mg-top">Preferiti</h4>
										<p class="subscription-preferences-description">Ami un prodotto alla follia?<br/>Segnalalo qui,
											faremo in modo di mandartelo più spesso.</p>


										<div class="container-flex-box">

											<div class="right-categories-box">
												<ul v-if="categories.length > 0">
													<li :class="{'active':currentCategory == category}" v-for="category of categories">
														<a href="#" @click.prevent="currentCategory = category" v-html="category.name"></a>
													</li>
												</ul>
											</div>

											<div class="products-box" v-if="currentCategory && currentCategory.products.length > 0">
												<ul>
													<li :key="'list_all'">
														<label @click.prevent="toggleAllWishlist(currentCategory,subscription)"
															   class="checkbox-container">
															<span class="label">Seleziona tutti</span>
															<input :checked="currentCategory.is_all_wishlist_selected" type="checkbox">
															<span class="checkmark"></span>
														</label>
													</li>
													<li :key="'list_'+subscription.id" v-for="product of currentCategory.products">
														<label @click.prevent="togglePreference(product.code,subscription)"
															   class="checkbox-container">
															<span class="label" v-html="product.post_title"></span>
															<input :checked="isPreference(product.code,subscription)" type="checkbox">
															<span class="checkmark"></span>
														</label>
													</li>

												</ul>
											</div>

											<div class="preferences-box">
												<h4 class="my-account--minititle small">I tuoi prodotti preferiti</h4>
												<div class="blacklist-item" v-for="(preference) of subscription.box_preferences">
													<a class="delete_item" @click.prevent="deletePreference(subscription,preference.id)"
													   href="#"><span class="icon-close"></span></a>
													<span v-html="preference.name"></span>
												</div>
											</div>

											<hr>
										</div>

					-->
				</div>

			</div>

			<div class="table-shadow"></div>
		</div>


	</div>
	<?php
}

add_action('woocommerce_account_personalizza-scatola_endpoint', 'settings_box_content');


function invoice_box_content()
{
	$navisionId = get_user_meta(get_current_user_id(), 'navision_id', true);

	$userInvoices = get_posts([
		"post_type" => "invoice",
		"post_status" => "publish",
		"posts_per_page" => -1,
		"fields" => "ids",
		"meta_query" => [
			[
				"key" => "_customer_id",
				"value" => $navisionId,
				"compare" => "=",
			],
		],
	]);

	/*$args = [
		"status" => "wc-completed",
		"limit" => -1,

		"meta_key" => "_payment_method",
		"meta_value" => ["bacs", "wallet", ""],
		"meta_compare" => "NOT IN",
		'customer' => get_current_user_id(),
	];
*/
	//$payments = wc_get_orders($args);

	//$invoices = array_merge($userInvoices, $payments);

	/*usort($invoices, function ($a, $b) {
		return $a->ID < $b->ID;
	});*/

	?>
	<div class="woocommerce-Fatture-content">

		<div class="woocommerce-notices-wrapper"></div>
		<h3 class="my-account--minititle address-title">Documenti contabili</h3>

		<div class="table-shadow-relative">
			<table
				class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
				<thead>
				<tr>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number">
						<span class="nobr">Pagamento</span></th>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date">
						<span class="nobr">Data</span></th>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status">
						<span class="nobr">Totale</span></th>
					<th
						class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions">
						<span class="nobr">Azioni</span></th>
				</tr>
				</thead>

				<tbody>
				<?php foreach ($userInvoices as $invoice): ?>
					<?php
					$amount = 0;
					$navisionId = null;
					$date = null;
					$filename = null;
					$type = null;

					if (get_class($invoice) == Automattic\WooCommerce\Admin\Overrides\Order::class) {
						$amount = $invoice->get_total();
						$date = $invoice->get_date_paid()->format('Y-m-d');
						$type = 'PAGAMENTO';
						$navisionId = $invoice->get_id();
					} else {

						$amount = get_post_meta($invoice, '_amount', true);
						$amount = substr($amount, 0, -2);
						$navisionId = get_post_meta($invoice, '_navision_id', true);
						$date = get_post_meta($invoice, '_created_date', true);
						$filename = get_post_meta($invoice, '_filename', true);
						$invoiceName = explode('.', $filename);
						$invoiceName = $invoiceName[0];
						$invoiceName = str_replace("_", '/', $invoiceName);

						$type = 'PAGAMENTO';
						if (substr($filename, 0, 3) == 'VAB') {
							$type = 'FATTURA';
						}
					}

					?>

					<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order">
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
							data-title="Ordine">
							<span class="document-type"><?php echo $type; ?></span>
							<p><?php echo $invoiceName; ?></p>
						</td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date"
							data-title="Data">
							<?php echo (new DateTime($date))->format("d-m-Y"); ?>
						</td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
							data-title="Stato">
							<?php echo $amount; ?>€
						</td>
						<td
							class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions"
							data-title="Azioni">
							<?php if ($type == 'FATTURA'): ?>
								<a href="/wp-content/uploads/invoices/<?php echo $filename; ?>"
								   target="_blank"
								   class="woocommerce-button button view">Visualizza</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<div class="table-shadow"></div>
		</div>


	</div>
	<?php
}

add_action('woocommerce_account_fatture_endpoint', 'invoice_box_content');
