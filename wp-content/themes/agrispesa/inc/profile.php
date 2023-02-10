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

	if (isset($wp_query->query_vars['personalizza-box'])) {


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
	add_rewrite_endpoint('personalizza-box', EP_ROOT | EP_PAGES);
}

add_action('init', 'add_settings_box_endpoint');


function settings_box_query_vars($vars)
{

	$vars[] = 'personalizza-box';

	return $vars;

}

add_filter('query_vars', 'settings_box_query_vars', 0);

// ------------------

// 3. Insert the new endpoint into the My Account menu

function settings_box_link_my_account($items)
{
	$items = array_insert_after($items, 'subscriptions', [
		'personalizza-box' => 'Personalizza la box'
	]);
	return $items;

}

add_filter('woocommerce_account_menu_items', 'settings_box_link_my_account');

// ------------------

// 4. Add content to the new tab

function settings_box_content()
{
	$subscriptions = wcs_get_subscriptions(['subscriptions_per_page' => -1, 'customer_id' => get_current_user_id(), 'subscription_status' => 'active']);
	?>
	<div class="woocommerce-PreferenzeBox-content" id="box-app">

		<div class="woocommerce-notices-wrapper"></div>
		<h3 class="my-account--minititle">Personalizza la box</h3>

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


					<h4 class="my-account--minititle small">Black list</h4>
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
								<li :key="'list_'+subscription.id" v-for="product of currentCategory.products">
									<label @click.prevent="toggleBlacklist(product.ID,subscription)"
										   class="checkbox-container">
										<span class="label" v-html="product.post_title"></span>
										<input :checked="isBlacklisted(product.ID,subscription)" type="checkbox">
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
								<li :key="'list_'+subscription.id" v-for="product of currentCategory.products">
									<label @click.prevent="togglePreference(product.ID,subscription)"
										   class="checkbox-container">
										<span class="label" v-html="product.post_title"></span>
										<input :checked="isPreference(product.ID,subscription)" type="checkbox">
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


				</div>

			</div>

			<div class="table-shadow"></div>
		</div>


	</div>
	<?php
}

add_action('woocommerce_account_personalizza-box_endpoint', 'settings_box_content');
