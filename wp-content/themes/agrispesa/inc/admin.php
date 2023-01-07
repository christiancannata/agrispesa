<?php
function dd(...$vars)
{
	die(var_dump($vars));
}


// Adding Meta container admin shop_order pages
add_action('add_meta_boxes', 'mv_add_meta_boxes');
if (!function_exists('mv_add_meta_boxes')) {
	function mv_add_meta_boxes()
	{
		add_meta_box('mv_other_fields', 'Informazioni BOX', 'mv_add_other_fields_for_packaging', 'shop_order', 'side', 'core');
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

					</tr>
					</thead>

					<tbody id="the-comment-list" data-wp-lists="list:comment">
					<?php foreach ($subscriptions as $subscription): ?>
						<tr id="comment-1" class="comment even thread-even depth-1 approved">
							<th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-1">Seleziona
									un abbonamento</label>
								<input id="cb-select-1" type="checkbox" name="subscriptions[]"
									   value="<?php echo $subscription->get_id(); ?>">
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

						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<br><br>

				<button type="submit" class="button-primary">Genera Ordini</button>
			</form>
		</div>

		<div id="ajax-response"></div>

		<form method="get">
			<table style="display:none;">
				<tbody id="com-reply">
				<tr id="replyrow" class="inline-edit-row" style="display:none;">
					<td colspan="5" class="colspanchange">
						<fieldset class="comment-reply">
							<legend>
								<span class="hidden" id="editlegend">Modifica commento</span>
								<span class="hidden" id="replyhead">Rispondi al commento</span>
								<span class="hidden" id="addhead">Aggiungi un nuovo commento</span>
							</legend>

							<div id="replycontainer">
								<label for="replycontent" class="screen-reader-text">Commento</label>
								<div id="wp-replycontent-wrap" class="wp-core-ui wp-editor-wrap html-active">
									<link rel="stylesheet" id="editor-buttons-css"
										  href="https://agrispesa.loc/wp-includes/css/editor.min.css?ver=6.1.1"
										  media="all">
									<div id="wp-replycontent-editor-container" class="wp-editor-container">
										<div id="qt_replycontent_toolbar" class="quicktags-toolbar hide-if-no-js"><input
												type="button" id="qt_replycontent_strong"
												class="ed_button button button-small" aria-label="Grassetto"
												value="b"><input type="button" id="qt_replycontent_em"
																 class="ed_button button button-small"
																 aria-label="Corsivo" value="i"><input type="button"
																									   id="qt_replycontent_link"
																									   class="ed_button button button-small"
																									   aria-label="Inserisci link"
																									   value="link"><input
												type="button" id="qt_replycontent_block"
												class="ed_button button button-small" aria-label="Citazione"
												value="b-quote"><input type="button" id="qt_replycontent_del"
																	   class="ed_button button button-small"
																	   aria-label="Testo eliminato (barrato)"
																	   value="del"><input type="button"
																						  id="qt_replycontent_ins"
																						  class="ed_button button button-small"
																						  aria-label="Testo inserito"
																						  value="ins"><input
												type="button" id="qt_replycontent_img"
												class="ed_button button button-small" aria-label="Inserisci immagine"
												value="img"><input type="button" id="qt_replycontent_ul"
																   class="ed_button button button-small"
																   aria-label="Elenco puntato" value="ul"><input
												type="button" id="qt_replycontent_ol"
												class="ed_button button button-small" aria-label="Elenco numerato"
												value="ol"><input type="button" id="qt_replycontent_li"
																  class="ed_button button button-small"
																  aria-label="Voce in elenco" value="li"><input
												type="button" id="qt_replycontent_code"
												class="ed_button button button-small" aria-label="Codice"
												value="code"><input type="button" id="qt_replycontent_close"
																	class="ed_button button button-small"
																	title="Chiudi tutti i tag aperti"
																	value="chiudi tag"></div>
										<textarea class="wp-editor-area" rows="20" cols="40" name="replycontent"
												  id="replycontent"></textarea></div>
								</div>

							</div>

							<div id="edithead" style="display:none;">
								<div class="inside">
									<label for="author-name">Nome</label>
									<input type="text" name="newcomment_author" size="50" value="" id="author-name">
								</div>

								<div class="inside">
									<label for="author-email">Email</label>
									<input type="text" name="newcomment_author_email" size="50" value=""
										   id="author-email">
								</div>

								<div class="inside">
									<label for="author-url">URL</label>
									<input type="text" id="author-url" name="newcomment_author_url" class="code"
										   size="103" value="">
								</div>
							</div>

							<div id="replysubmit" class="submit">
								<p class="reply-submit-buttons">
									<button type="button" class="save button button-primary">
										<span id="addbtn" style="display: none;">Aggiungi commento</span>
										<span id="savebtn" style="display: none;">Aggiorna commento</span>
										<span id="replybtn" style="display: none;">Invia risposta</span>
									</button>
									<button type="button" class="cancel button">Annulla</button>
									<span class="waiting spinner"></span>
								</p>
								<div class="notice notice-error notice-alt inline hidden">
									<p class="error"></p>
								</div>
							</div>

							<input type="hidden" name="action" id="action" value="">
							<input type="hidden" name="comment_ID" id="comment_ID" value="">
							<input type="hidden" name="comment_post_ID" id="comment_post_ID" value="">
							<input type="hidden" name="status" id="status" value="">
							<input type="hidden" name="position" id="position" value="-1">
							<input type="hidden" name="checkbox" id="checkbox" value="1">
							<input type="hidden" name="mode" id="mode" value="detail">
							<input type="hidden" id="_ajax_nonce-replyto-comment" name="_ajax_nonce-replyto-comment"
								   value="ae5f6779d4"><input type="hidden" id="_wp_unfiltered_html_comment"
															 name="_wp_unfiltered_html_comment" value="0531d6012c">
						</fieldset>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
		<div class="hidden" id="trash-undo-holder">
			<div class="trash-undo-inside">
				Commento di <strong></strong> spostato nel cestino. <span class="undo untrash"><a
						href="#">Annulla</a></span>
			</div>
		</div>
		<div class="hidden" id="spam-undo-holder">
			<div class="spam-undo-inside">
				Il commento di <strong></strong> adesso è marcato come spam. <span class="undo unspam"><a href="#">Annulla</a></span>
			</div>
		</div>

		<div class="clear"></div>
	</div>
	<?php
}

add_action('admin_menu', 'register_my_custom_submenu_page', 99);
