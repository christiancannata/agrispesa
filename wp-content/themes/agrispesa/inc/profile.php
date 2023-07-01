<?php
function array_insert_after(array $array, $key, array $new)
{
	$keys = array_keys($array);
	$index = array_search($key, $keys);
	$pos = false === $index ? count($array) : $index + 1;

	return array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
}


// Initiate the WooCommerce Sessions manually for guest users.
add_action('woocommerce_init', function () {

	if (is_user_logged_in() && isset($_GET['skipCheckSubscription'])) {
		WC()->session->set('skip_check_subscription', true);
	}

});

function enqueue_box_js()
{
	global $wp_query;


	if (isset($wp_query->query_vars['view-subscription'])) {
		wp_enqueue_script('agrispesa-change-subscription-js', get_theme_file_uri('assets/js/change-subscription.js'), array('jquery'), time(), true);
		wp_register_script('swal', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array('jquery'), '1.0', true);
		wp_enqueue_script('swal');
	}

	if (isset($wp_query->query_vars['calendar'])) {
		wp_register_script('fullcalendar', '//cdn.jsdelivr.net/npm/fullcalendar@6.1.7/index.global.min.js', array('jquery'), '1.0', true);
		wp_enqueue_script('agrispesa-calendar-js', get_theme_file_uri('assets/js/calendar.js'), array('jquery', 'fullcalendar', 'moment'), time(), true);


		wp_register_script('swal', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array('jquery'), '1.0', true);
		wp_enqueue_script('swal');
	}

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
	add_rewrite_endpoint('calendar', EP_ROOT | EP_PAGES);

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
	$vars[] = 'calendar';

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
	$newMenu['calendar'] = 'Calendario';
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
				<?php
				//CLEAN DUPLICATED
				$invoices = [];

				foreach ($userInvoices as $invoice):
					$amount = 0;
					$navisionId = null;
					$date = null;
					$filename = null;
					$type = null;


					$amount = get_post_meta($invoice, '_amount', true);
					$amount = substr($amount, 0, -2);
					$date = get_post_meta($invoice, '_created_date', true);
					$filename = get_post_meta($invoice, '_filename', true);
					$invoiceName = explode('.', $filename);
					$invoiceName = $invoiceName[0];
					$invoiceName = str_replace("_", '/', $invoiceName);

					$type = 'PAGAMENTO';
					if (substr($filename, 0, 3) == 'VAB' || substr($filename, 0, 2) == 'VV') {
						$type = 'FATTURA';
					}

					$invoiceType = get_post_meta($invoice, '_invoice_type', true);
					if ($invoiceType == 'NOTA_CREDITO') {
						$type = 'NOTA DI CREDITO';
					}

					$invoiceObj = new stdClass();

					$invoiceObj->type = $type;
					$invoiceObj->date = $date;
					$invoiceObj->filename = $filename;
					$invoiceObj->invoiceName = $invoiceName;
					$invoiceObj->amount = $amount;

					//find same invoices
					if (new DateTime($invoiceObj->date) < new DateTime('2023-05-05')) {
						$isDuplicated = array_filter($invoices, function ($invoiceTmp) use ($invoiceObj) {
							return $invoiceTmp->date == $invoiceObj->date && $invoiceTmp->amount == $invoiceObj->amount;
						});
						if (!empty($isDuplicated)) {
							continue;
						}
					}

					$invoices[] = $invoiceObj;
				endforeach;


				usort($invoices, function ($a, $b) {
					$dateA = DateTime::createFromFormat('Y-m-d', $a->date);
					$dateB = DateTime::createFromFormat('Y-m-d', $b->date);
					if ($dateA == $dateB) return 0;
					return $dateA < $dateB ? 1 : -1;
				});

				?>
				<?php foreach ($invoices as $invoice):
					?>

					<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order">
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
							data-title="Ordine">
							<span class="document-type"><?php echo $invoice->type; ?></span>
							<p><?php echo $invoice->invoiceName; ?></p>
						</td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date"
							data-title="Data">
							<?php echo (new DateTime($invoice->date))->format("d-m-Y"); ?>
						</td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
							data-title="Stato">
							<?php echo $invoice->amount; ?>€
						</td>
						<td
							class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions"
							data-title="Azioni">
							<?php if ($invoice->type == 'FATTURA' || $invoice->type == 'NOTA DI CREDITO'): ?>
								<a href="/wp-content/uploads/invoices/<?php echo $invoice->filename; ?>"
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

function enable_subscription($subscriptionId, $enableDate)
{
	$subscription = wcs_get_subscription($subscriptionId);
	$subscription->update_status('active', 'Abbonamento riattivato da calendario.', true);
	$nextPaymentDate = $enableDate->add(new DateInterval('PT2H'));

	$subscription->update_dates([
		'next_payment' => $nextPaymentDate->getTimestamp()
	]);
	$subscription->save();

}

function disable_subscription($subscriptionId, $enableDate, $week)
{
	$subscription = wcs_get_subscription($subscriptionId);
	$subscription->update_status('on-hold', 'Abbonamento sospeso da calendario.', true);

	enable_subscription($subscriptionId, $enableDate);

	as_schedule_single_action($enableDate->getTimestamp(), "enable_subscription", [
		"subscriptionId" => $subscription->get_id(),
		"enableDate" => $enableDate
	], 'enable_subscription_' . $subscription->get_id() . '_' . $week);

}

add_action('woocommerce_account_fatture_endpoint', 'invoice_box_content');


function get_first_and_last_day_of_week($year_number, $week_number)
{
	// we need to specify 'today' otherwise datetime constructor uses 'now' which includes current time
	$today = new DateTime();

	return [
		clone $today->setISODate($year_number, $week_number, 0)->add(new \DateInterval('P1D')),
		clone $today->setISODate($year_number, $week_number, 1)->add(new \DateInterval('P1D')),
		clone $today->setISODate($year_number, $week_number, 2)->add(new \DateInterval('P1D')),
		clone $today->setISODate($year_number, $week_number, 3)->add(new \DateInterval('P1D')),
		clone $today->setISODate($year_number, $week_number, 4)->add(new \DateInterval('P1D')),
		clone $today->setISODate($year_number, $week_number, 5)->add(new \DateInterval('P1D')),
		clone $today->setISODate($year_number, $week_number, 6)->add(new \DateInterval('P1D')),
	];
}


function calendar_content()
{

	$subscription = wcs_get_subscriptions([
		"subscriptions_per_page" => 1,
		'orderby' => 'ID',
		'order' => 'DESC',
		'subscription_status' => ['active', 'on-hold'],
		"customer_id" => get_current_user_id()
	]);
	$subscription = reset($subscription);

	if (isset($_GET['schedule'])) {

		$week = $_GET['week'];

		//schedule on the day before generating order in the specified week

		$disableWeeks = get_post_meta($subscription->get_id(), 'disable_weeks_' . date('Y'), true);
		if (!$disableWeeks) {
			$disableWeeks = [];
		}

		$disableWeeks[] = $week;

		sort($disableWeeks);

		update_post_meta($subscription->get_id(), 'disable_weeks_' . date('Y'), $disableWeeks);

	}


	$testoCalendario = get_option('options_calendar_text');

	$order_statuses = [
		"wc-pending" => 'In attesa',
		"wc-on-hold" => 'Sospeso',
		"wc-expired" => 'Scaduto',
		"wc-pending-cancel" => 'Eliminato',
		"wc-active" => 'Attivo',
		"wc-cancelled" => 'Eliminato'
	];

	?>

	<div class="woocommerce-Fatture-content">

		<div class="woocommerce-notices-wrapper"></div>
		<h3 class="my-account--minititle address-title">Calendario</h3>

		<?php if ($subscription): ?>
			<?php
			$subscriptionStatus = $order_statuses["wc-" . $subscription->get_status()];


			?>
			<span
				style="display: block;">Stato abbonamento: <b><?php echo $subscriptionStatus; ?></b> <?php if ($subscription->get_status() != 'active'): ?>
					(<a style="text-decoration: underline; color: #3c21ff"
						href="/bacheca/visualizza-abbonamento/<?php echo $subscription->get_id(); ?>">Riattivalo qui</a>)
				<?php endif; ?></span>


			<p><?php echo $testoCalendario; ?></p>
			<div class="table-shadow-relative" style="margin-top:20px">
				<div class="loading">
					<span>Caricamento in corso, attendere...</span></div>

				<div id="calendar"></div>

				<button class=" btn btn-primary alt wp-element-button confirm-calendar" style="display: none">Conferma
				</button>

				<div class="table-shadow"></div>
			</div>
		<?php else: ?>
			<h2 class="error-404--title">Nessun abbonamento attivo.</h2>
			<p class="error-404--subtitle">Puoi gestire il tuo calendario solamente con un abbonamento attivo.</p>
			<a href="/box/facciamo-noi/" class="btn btn-primary">Scopri gli abbonamenti</a>
		<?php endif; ?>
	</div>
	<?php
}

add_action('woocommerce_account_calendar_endpoint', 'calendar_content');


//Check if subscription is blocked
add_action("woocommerce_scheduled_subscription_payment", function ($subscription_id) {

	$date = new DateTime();
	$week = $date->format("W");

	$disabledWeeks = get_post_meta(
		$subscription_id,
		"disable_weeks_" . date('Y'),
		true
	);

	if ($disabledWeeks && is_array($disabledWeeks)) {
		if (in_array($week, $disabledWeeks)) {
			$subscription = wcs_get_subscription($subscription_id);
			if ($subscription->get_payment_method() != "bacs") {
				$subscription->update_status('on-hold', 'Abbonamento sospeso prima del rinnovo per blocco calendario');
			}
		}
	}

}, 0, 1);


add_action('profile_update', 'my_profile_update', 10, 2);
function my_profile_update($user_id, $old_user_data)
{

	$user = get_user_by('ID', $user_id);

	$subscriptions = wcs_get_subscriptions([
		"subscriptions_per_page" => -1,
		"customer_id" => $user_id,
	]);

	foreach ($subscriptions as $subscription) {
		update_post_meta(
			$subscription->get_id(),
			"_billing_email",
			$user->user_email
		);
	}
}
