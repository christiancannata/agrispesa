<?php
/**
 * Add New Tab on the My Account page
 */

function add_settings_box_endpoint()
{

	add_rewrite_endpoint('settings-box', EP_ROOT | EP_PAGES);

}

add_action('init', 'add_settings_box_endpoint');

// ------------------

// 2. Add new query var

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
				class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
				<?php foreach ($subscriptions as $subscription): ?>
					<h4>
						<?php $products = $subscription->get_items();
						foreach ($products as $product) {
							echo $product->get_name();
						}
						?>
					</h4>
					<br>
					<br>

				<?php endforeach; ?>
			</div>

			<div class="table-shadow"></div>
		</div>


	</div>
	<?php
}

add_action('woocommerce_account_settings-box_endpoint', 'settings_box_content');
