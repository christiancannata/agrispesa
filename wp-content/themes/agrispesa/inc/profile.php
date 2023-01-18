<?php
function enqueue_box_js()
{
	global $wp_query;

	if (isset($wp_query->query_vars['settings-box'])) {


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
	add_rewrite_endpoint('settings-box', EP_ROOT | EP_PAGES);
}

add_action('init', 'add_settings_box_endpoint');


function settings_box_query_vars($vars)
{

	$vars[] = 'settings-box';

	return $vars;

}

add_filter('query_vars', 'settings_box_query_vars', 0);

// ------------------

// 3. Insert the new endpoint into the My Account menu

function settings_box_link_my_account($items)
{

	$items['settings-box'] = 'Preferenze Box';

	return $items;

}

add_filter('woocommerce_account_menu_items', 'settings_box_link_my_account');

// ------------------

// 4. Add content to the new tab

function settings_box_content()
{
	$subscriptions = wcs_get_subscriptions(['subscriptions_per_page' => -1, 'customer_id' => get_current_user_id(), 'subscription_status' => 'active']);
	?>
	<div class="woocommerce-PreferenzeBox-content">

		<div class="woocommerce-notices-wrapper"></div>
		<h3 class="my-account--minititle address-title">Preferenze BOX</h3>

		<div class="table-shadow-relative">
			<div
				id="box-app"
				class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">

				<div v-for="subscription of subscriptions" class="subscription-box">

					<div class="box-name">
						<img src="https://picsum.photos/300/300" class="box-image">
						<div>
							<h4 class="title" v-html="subscription.name"></h4>
							<span class="description">Lorem ipsum</span>
						</div>
					</div>


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
									<label @click.prevent="togglePreference(product,subscription)" class="container">
										<span v-html="product.post_title"></span>
										<input :checked="isBlacklisted(product,subscription)" type="checkbox">
										<span class="checkmark"></span>
									</label>
								</li>

							</ul>
						</div>

						<div class="blacklist-box">
							<h4>Blacklist</h4>
							<div class="blacklist-item" v-for="(preference) of subscription.box_preferences">
								<a @click.prevent="deletePreference(subscription,preference)" href="#">Elimina</a>
								<span v-html="preference.name"></span>
							</div>
						</div>
					</div>


				</div>

			</div>

			<div class="table-shadow"></div>
		</div>


	</div>
	<?php
}

add_action('woocommerce_account_settings-box_endpoint', 'settings_box_content');
