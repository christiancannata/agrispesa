<?php
function dd($vars)
{
    die(var_dump($vars));
}

add_action("user_register", "myplugin_registration_save", 10, 1);

function myplugin_registration_save($user_id)
{
    $user = get_user_by("id", $user_id);
    if ($user) {
        wp_mail(
            "agrispesa@agrispesa.it",
            "Nuovo utente registrato",
            "Un nuovo utente si è registrato con la mail: " . $user->user_email
        );
    }
}


// Add a Header
function filter_manage_edit_shop_coupon_columns( $columns ) {
    // Add new column
    $columns['coupon_parent'] = 'Coupon Correlato';

    return $columns;
}
add_filter( 'manage_edit-shop_coupon_columns', 'filter_manage_edit_shop_coupon_columns', 10, 1 );

// Populate the Column
function action_manage_shop_coupon_posts_custom_column( $column, $post_id ) {
    // Compare
    if ( $column == 'coupon_parent' ) {
        // Author ID
        $relatedCoupon = get_post_meta( $post_id, 'coupon_parent_id',true );
		if(!empty($relatedCoupon)){
			echo $relatedCoupon;
		}
    }
}
add_action( 'manage_shop_coupon_posts_custom_column' , 'action_manage_shop_coupon_posts_custom_column', 10, 2 );

add_action("woocommerce_coupon_options", "add_coupon_text_field", 10);
function add_coupon_text_field()
{
    global $wpdb;

    $coupons = $wpdb->get_results(
        "SELECT p.post_title FROM $wpdb->posts p, wp_postmeta m WHERE p.ID = m.post_id AND m.meta_key = 'discount_type' AND m.meta_value = 'percent' AND p.post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_name ASC"
    );

    $couponsArray = [
        "" => "-- Seleziona un coupon --",
    ];
    foreach ($coupons as $coupon) {
        $couponsArray[$coupon->post_title] = $coupon->post_title;
    }

    woocommerce_wp_select([
        "id" => "coupon_parent_id",
        "label" => 'Coupon "Scegli tu" collegato',
        "description" => 'Seleziona il coupon "Scegli Tu" collegato.',
        "options" => $couponsArray,
    ]);
}

// Save the custom field value from Admin coupon settings pages
add_action("woocommerce_coupon_options_save", "save_coupon_text_field", 10, 2);
function save_coupon_text_field($post_id, $coupon)
{
    if (isset($_POST["coupon_parent_id"])) {
        $coupon->update_meta_data(
            "coupon_parent_id",
            sanitize_text_field($_POST["coupon_parent_id"])
        );
        $coupon->save();
    }
}

add_action("reload_terms_count", function ($productId) {});

// define woocommerce_order_status_completed callback function
function call_order_status_changed($orderId)
{
    $order = wc_get_order($orderId);

    if ($order->get_created_via() == "checkout") {
        $ordersType = [];

        foreach ($order->get_items() as $item) {
            if ($item->get_name() == "Acquisto credito") {
                $ordersType[] = "CREDITO";
            } else {
                $categories = get_the_terms(
                    $item->get_product_id(),
                    "product_cat"
                );
                foreach ($categories as $term) {
                    if (in_array($term->slug, ["box"])) {
                        $ordersType[] = "ABBONAMENTO FN";
                    } else {
                        $ordersType[] = "ST";
                    }
                }
            }
        }

        $ordersType = array_unique($ordersType);

        update_post_meta($orderId, "_order_type", implode(" + ", $ordersType));

        $groups = get_posts([
            "post_type" => "delivery-group",
            "post_status" => "publish",
            "posts_per_page" => -1,
        ]);
        foreach ($groups as $group) {
            $caps = get_post_meta($group->ID, "cap", true);
            if (in_array($order->get_shipping_postcode(), $caps)) {
                update_post_meta(
                    $order->get_id(),
                    "_gruppo_consegna",
                    $group->post_title
                );
            }
        }

        calculate_delivery_date_order($order->get_id(), false);
    }
}

function call_order_status_pending($orderId)
{
    $order = wc_get_order($orderId);

    $orderType = "ST";

    foreach ($order->get_items() as $item_id => $item) {
        $categories = get_the_terms($item->get_product_id(), "product_cat");
        foreach ($categories as $term) {
            if (in_array($term->slug, ["box"])) {
                $orderType = "FN";
            }
        }

        if ($item->get_name() == "Acquisto credito") {
            $orderType = "CREDITO";
        }
    }

    update_post_meta($orderId, "_order_type", $orderType);

    //generate settimana
    /* $today = new \DateTime();
    $today->add(new \DateInterval("P7D"));
    if ($today->format("w") >= 3 && $today->format("H") >= 12) {
        $today->add(new \DateInterval("P7D"));
    }
    $week = $today->format("W");*/
    //$defaultWeek = str_pad($week, 2, 0, STR_PAD_LEFT);

    //$currentWeek = get_option('current_order_week',true);
    //update_post_meta($orderId, "_week", $currentWeek);
}

function wp_kama_woocommerce_new_order_action($order_id, $order)
{
    $currentWeek = get_option("current_order_week", true);
    update_post_meta($order_id, "_week", $currentWeek);
}
add_action(
    "woocommerce_new_order",
    "wp_kama_woocommerce_new_order_action",
    10,
    2
);

// Call our custom function with the action hook
add_action(
    "woocommerce_order_status_changed",
    "call_order_status_changed",
    10,
    1
);

add_action(
    "woocommerce_order_status_pending",
    "call_order_status_pending",
    10,
    1
);

function merge_orders($subscriptionOrder, $orders)
{
    foreach ($orders as $order) {
        $first_order_id = $orders->get_id();
        foreach ($order->get_items() as $item_id => $item) {
            $product_id = $item->get_product_id();
            $variation_id = $item->get_variation_id();
            $product_quantity = $item->get_quantity();

            //if product type is simple
            if ($variation_id == 0) {
                $product = wc_get_product($product_id);
                $subscriptionOrder->add_product($product, $product_quantity);
            }
            //if product type is variable
            else {
                $variation = wc_get_product($variation_id);
                $subscriptionOrder->add_product($variation, $product_quantity);
            }
        }
        $subscriptionOrder->calculate_totals();
        update_post_meta($order->get_id(), "is_merged_order", 1);
        $order->update_status(
            "cancelled",
            "This order is cancelled since it is combined with order ID: " .
                $subscriptionOrder->get_id()
        );
        $subscriptionOrder->add_order_note(
            "Order ID: $first_order_id is cancelled and combined with this order."
        );
    }
}
function give_user_subscription($product, $user, $row)
{
    $user_id = $user->ID;
    // First make sure all required functions and classes exist
    if (
        !function_exists("wc_create_order") ||
        !function_exists("wcs_create_subscription") ||
        !class_exists("WC_Subscriptions_Product")
    ) {
        return false;
    }
    $payment_gateways = WC()->payment_gateways->payment_gateways();
    $order = wc_create_order(["customer_id" => $user_id]);
    update_post_meta($order->get_id(), "_disable_order_emails", true);

    if (is_wp_error($order)) {
        dd($order);
        return false;
    }
    $user = get_user_by("ID", $user_id);
    $fname = $user->first_name;
    $lname = $user->last_name;
    $email = $user->user_email;
    /*$address_1 = get_user_meta( $user_id, 'billing_address_1', true );
		$address_2 = get_user_meta( $user_id, 'billing_address_2', true );
		$city      = get_user_meta( $user_id, 'billing_city', true );
		$postcode  = get_user_meta( $user_id, 'billing_postcode', true );
		$country   = get_user_meta( $user_id, 'billing_country', true );
		$state     = get_user_meta( $user_id, 'billing_state', true );
		*/
    $shippingAddress = [
        "first_name" => $fname,
        "last_name" => $lname,
        "email" => $email,
        "phone" => isset($row["automatismiSettimanali_utenze::telmobile"])
            ? $row["automatismiSettimanali_utenze::telmobile"]
            : "",
        "address_1" =>
            $row["automatismiSettimanali_indirizzoSpedizione::indirizzo"] .
            " " .
            $row["automatismiSettimanali_indirizzoSpedizione::numeroCivico"],
        "city" => $row["automatismiSettimanali_indirizzoSpedizione::città"],
        "state" => $row["automatismiSettimanali_speseSpedizione::provincia"],
        "postcode" => $row["automatismiSettimanali_indirizzoSpedizione::cap"],
        "country" => "IT",
    ];
    $invoiceAddress = [
        "first_name" =>
            $row["automatismiSettimanali_intestazioneFattura::nome"],
        "last_name" =>
            $row["automatismiSettimanali_intestazioneFattura::cognome"],
        "email" => $email,
        "phone" => isset($row["automatismiSettimanali_utenze::telmobile"])
            ? $row["automatismiSettimanali_utenze::telmobile"]
            : "",
        "address_1" =>
            $row["automatismiSettimanali_intestazioneFattura::indirizzo"] .
            " " .
            $row["automatismiSettimanali_intestazioneFattura::numeroCivico"],
        "city" => $row["automatismiSettimanali_intestazioneFattura::città"],
        "state" => $row["automatismiSettimanali_speseSpedizione::provincia"],
        "postcode" => $row["automatismiSettimanali_intestazioneFattura::cap"],
        "country" => "IT",
    ];
    $order->set_customer_id($user_id);
    $order->set_address($invoiceAddress, "billing");
    $order->set_address($shippingAddress, "shipping");
    $order->add_product($product, 1);
    $order->set_status("completed");
    $order->set_payment_method($payment_gateways["wallet"]);
    $order->calculate_totals();
    update_post_meta(
        $order->get_id(),
        "_billing_partita_iva",
        $row["automatismiSettimanali_intestazioneFattura::codiceFiscale"]
    );
    update_post_meta(
        $order->get_id(),
        "_billing_codice_fiscale",
        $row["automatismiSettimanali_intestazioneFattura::partitaIva"]
    );
    $order->save();
    $subscriptionParams = [
        "order_id" => $order->get_id(),
        "customer_id" => $user_id,
        "status" => $row["riceveSpesa"] == 1 ? "active" : "on-hold",
        "billing_period" => WC_Subscriptions_Product::get_period($product),
        "billing_interval" => WC_Subscriptions_Product::get_interval($product),
    ];
    $sub = wcs_create_subscription($subscriptionParams);
    update_post_meta($sub->get_id(), "_disable_order_emails", true);
    if (is_wp_error($sub)) {
        dd($sub);
        return false;
    }
    $sub->set_payment_method($payment_gateways["wallet"]);
    $sub->set_address($invoiceAddress, "billing");
    $sub->set_address($shippingAddress, "shipping");
    // Modeled after WC_Subscriptions_Cart::calculate_subscription_totals()
    $start_date = gmdate("Y-m-d H:i:s");
    // Add product to subscription
    $sub->add_product($product, 1);
    $dates = [
        "trial_end" => WC_Subscriptions_Product::get_trial_expiration_date(
            $product,
            $start_date
        ),
        "next_payment" => WC_Subscriptions_Product::get_first_renewal_payment_date(
            $product,
            $start_date
        ),
        "end" => WC_Subscriptions_Product::get_expiration_date(
            $product,
            $start_date
        ),
    ];
    $sub->update_dates($dates);
    $sub->calculate_totals();
    $sub->save();
    // Update order status with custom note
    $note = !empty($note)
        ? $note
        : __("Programmatically added order and subscription.");
    //$order->update_status( 'completed', $note, true );
    // Also update subscription status to active from pending (and add note)
    //	$sub->update_status( 'active', $note, true );
    return $sub;
}
function get_order_delivery_date($id)
{
    $order = wc_get_order($id);
    if (!$order) {
        return null;
    }
    $deliveryDate = get_post_meta($id, "_delivery_date", true);
    if ($deliveryDate) {
        return DateTime::createFromFormat("Y-m-d", $deliveryDate)->format(
            "d/m/Y"
        );
    }
}
function get_order_delivery_date_from_date(
    $date = null,
    $group = null,
    $cap = null
) {
    if (!$group && $cap) {
        $groups = get_posts([
            "post_type" => "delivery-group",
            "post_status" => "publish",
            "posts_per_page" => -1,
        ]);
        foreach ($groups as $singleGroup) {
            $caps = get_post_meta($singleGroup->ID, "cap", true);
            if (is_array($caps)) {
                if (in_array($cap, $caps)) {
                    $group = $singleGroup->post_title;
                }
            }
        }
    }
    if (!$group) {
        return null;
    }
    global $wpdb;
    $ids = $wpdb->get_col(
        "select ID from $wpdb->posts where post_title = '" .
            $group .
            "' AND post_status = 'publish'"
    );
    $ids = reset($ids);
    $dowMap = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
    if (is_object($date) && get_class($date) != DateTime::class) {
        $date = DateTime::createFromFormat("d-m-Y", $date);
    }
    //if (($date->format('w') > 5 && $date->format('H') >= 8) || $date->format('w') == 0) {
    if (is_string($date)) {
        $date = new DateTime($date);
    }
    //$date->add(new DateInterval("P7D"));
    $date->add(new DateInterval("P1D"));
    //}
    $deliveryDay = get_post_meta($ids, "delivery_day", true);
    $deliveryDate = strtotime($dowMap[$deliveryDay], $date->getTimestamp());
    $deliveryDate = DateTime::createFromFormat("U", $deliveryDate);
    return $deliveryDate;
}
function calculate_delivery_date_order($id, $updateWeek = true)
{
    $order = wc_get_order($id);
    if (!$order) {
        return null;
    }
    $gruppoConsegna = get_post_meta($id, "_gruppo_consegna", true);
    if (!$gruppoConsegna) {
        return null;
    }
    $order_date = $order->get_date_paid();
    if (!$order_date) {
        $order_date = new DateTime();
    }

    if ($order_date) {
        if ($updateWeek) {
            $week = $order_date->format("W");
            if ($order->get_created_via() == "checkout") {
                $week = str_pad($week + 1, 2, 0, STR_PAD_LEFT);
            } else {
                if (
                    ($order_date->format("w") > 5 &&
                        $order_date->format("H") >= 8) ||
                    $order_date->format("w") == 0
                ) {
                    $week = str_pad($week + 1, 2, 0, STR_PAD_LEFT);
                }
            }
            update_post_meta($order->get_id(), "_week", $week);
        }
        $deliveryDate = get_order_delivery_date_from_date(
            $order_date->format("d-m-Y"),
            $gruppoConsegna
        );
        update_post_meta(
            $order->get_id(),
            "_delivery_date",
            $deliveryDate->format("Y-m-d")
        );
    }
}
/*
add_action(
    "woocommerce_new_order",
    function ($order_id, $order) {
        if ($order->get_created_via() == "checkout") {
            $groups = get_posts([
                "post_type" => "delivery-group",
                "post_status" => "publish",
                "posts_per_page" => -1,
            ]);
            foreach ($groups as $group) {
                $caps = get_post_meta($group->ID, "cap", true);
                if (in_array($order->get_shipping_postcode(), $caps)) {
                    update_post_meta(
                        $order->get_id(),
                        "_gruppo_consegna",
                        $group->post_title
                    );
                }
            }
            $gruppoConsegna = get_post_meta(
                $order_id,
                "_gruppo_consegna",
                true
            );
            $order_date = $order->get_date_created();
            //nathi
            $order_date->modify("+1 week");
            $week = $order_date->format("W");
            $week = str_pad($week + 1, 2, 0, STR_PAD_LEFT);
            update_post_meta($order->get_id(), "_week", $week);
            if ($gruppoConsegna) {
                $deliveryDate = get_order_delivery_date_from_date(
                    $order_date->format("d-m-Y"),
                    $gruppoConsegna
                );
                if ($deliveryDate) {
                    update_post_meta(
                        $order->get_id(),
                        "_delivery_date",
                        $deliveryDate->format("Y-m-d")
                    );
                }
            }
        }
    },
    10,
    2
);

*/
add_action("woocommerce_product_options_advanced", function () {
    woocommerce_wp_text_input([
        "id" => "_codice_confezionamento",
        "label" => "Codice Confezionamento",
    ]);
    woocommerce_wp_checkbox([
        "id" => "_is_magazzino",
        "label" => "È da Magazzino?",
    ]);
    woocommerce_wp_text_input([
        "id" => "_qty_acquisto",
        "label" => "Quantità (Acquisto)",
    ]);
    woocommerce_wp_text_input([
        "id" => "_uom_acquisto",
        "label" => "Cod. Unità di misura",
    ]);
});
add_action("woocommerce_product_options_general_product_data", function () {
    global $post;
    /*
    woocommerce_wp_text_input([
        "id" => "_prezzo_acquisto",
        "label" => "Prezzo di acquisto (€)",
        "placeholder" => "0.00",
        "description" => __(
            "I valori decimali sono separati con un punto. Es. €2.30",
            "woocommerce"
        ),
    ]);
    woocommerce_wp_checkbox([
        "id" => "_tipo_percentuale_ricarico",
        "label" => "Eredita percentuale ricarico dalla categoria",
    ]);
    woocommerce_wp_text_input([
        "id" => "_percentuale_ricarico",
        "label" => "Ricarico %",
        "placeholder" => "0",
        "description" => __("Valore della percentuale.", "woocommerce"),
    ]);
   */
});
function woocommerce_product_custom_fields_save1($post_id)
{
    if (isset($_POST["_codice_confezionamento"])) {
        update_post_meta(
            $post_id,
            "_codice_confezionamento",
            esc_attr($_POST["_codice_confezionamento"])
        );
    }
    if (isset($_POST["_is_magazzino"])) {
        update_post_meta(
            $post_id,
            "_is_magazzino",
            esc_attr($_POST["_is_magazzino"])
        );
    }
    if (isset($_POST["_prezzo_acquisto"])) {
        update_post_meta(
            $post_id,
            "_prezzo_acquisto",
            esc_attr($_POST["_prezzo_acquisto"])
        );
    }
    if (isset($_POST["_percentuale_ricarico"])) {
        update_post_meta(
            $post_id,
            "_percentuale_ricarico",
            esc_attr($_POST["_percentuale_ricarico"])
        );
    }
    if (isset($_POST["_uom_acquisto"])) {
        update_post_meta(
            $post_id,
            "_uom_acquisto",
            esc_attr($_POST["_uom_acquisto"])
        );
    }
    if (isset($_POST["_qty_acquisto"])) {
        update_post_meta(
            $post_id,
            "_qty_acquisto",
            esc_attr($_POST["_qty_acquisto"])
        );
    }
}
function wpse27856_set_content_type()
{
    return "text/html";
}
add_filter("wp_mail_content_type", "wpse27856_set_content_type");
add_action(
    "woocommerce_process_product_meta",
    "woocommerce_product_custom_fields_save1"
);
add_action("rest_api_init", function () {
    register_rest_route("agrispesa/v1", "import-preferences", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $lines = explode(PHP_EOL, $request->get_body());
            $preferences = [];
            foreach ($lines as $line) {
                $preferences[] = str_getcsv($line, ";");
            }
            $employee_csv = [];
            foreach ($preferences as $row) {
                if (!empty($row)) {
                    $employee_csv[] = $row;
                }
            }
            array_shift($preferences);
            $users = [];
            foreach ($preferences as $preference) {
                if (!isset($preference[1])) {
                    continue;
                }
                if (!isset($users[$preference[0]])) {
                    $users[$preference[0]] = [];
                }
                $users[$preference[0]][] = $preference[1];
            }
            global $wpdb;
            foreach ($users as $id => $userPreference) {
                $args = [
                    "fields" => "ids",
                    "meta_query" => [
                        [
                            "key" => "navision_id",
                            "value" => $id,
                            "compare" => "=",
                        ],
                    ],
                ];
                $member_arr = get_users($args); //finds all users with this meta_key == 'member_id' and meta_value == $member_id passed in url
                if (!empty($member_arr)) {
                    $member_arr = reset($member_arr);
                    $userPreference = array_map(function ($product) {
                        $ids = explode("-", $product);
                        return end($ids);
                    }, $userPreference);

                    $productsBlacklistIds = $wpdb->get_results(
                        "SELECT p.ID,p.post_title,m.meta_value FROM wp_posts p," .
                            $wpdb->postmeta .
                            ' m WHERE p.ID = m.post_id and m.meta_key = "codice_gruppo_prodotto" and m.meta_value IN ("' .
                            implode('","', $userPreference) .
                            '")'
                    );
                    $productsBlacklistIds = array_map(function ($product) {
                        return [
                            "id" => $product->ID,
                            "name" => $product->post_title,
                            "code" => $product->meta_value,
                        ];
                    }, $productsBlacklistIds);

                    $subscriptions = wcs_get_subscriptions([
                        "subscriptions_per_page" => -1,
                        "customer_id" => $member_arr,
                    ]);
                    foreach ($subscriptions as $subscription) {
                        update_post_meta(
                            $subscription->get_id(),
                            "_box_blacklist",
                            $productsBlacklistIds
                        );
                    }
                }
            }
            $response = new WP_REST_Response([]);
            $response->set_status(204);
            return $response;
        },
    ]);
    register_rest_route("agrispesa/v1", "import-extra-preferences", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $lines = explode(PHP_EOL, $request->get_body());
            $users = [];
            foreach ($lines as $line) {
                $users[] = str_getcsv($line, ";");
            }
            $header_row = array_shift($users);
            $employee_csv = [];
            foreach ($users as $row) {
                if (!empty($row)) {
                    $employee_csv[] = array_combine($header_row, $row);
                }
            }
            $categories = [
                "01" => "Verdura",
                "02" => "Frutta",
                "05" => "Formaggio",
                "06" => "Uova",
                "08" => "Carne",
                "10" => "Pesce",
            ];
            foreach ($employee_csv as $user) {
                $args = [
                    "fields" => "ids",
                    "meta_query" => [
                        [
                            "key" => "navision_id",
                            "value" => $user["idUtente"],
                            "compare" => "=",
                        ],
                    ],
                ];
                $member_arr = get_users($args); //finds all users with this meta_key == 'member_id' and meta_value == $member_id passed in url
                if (empty($member_arr)) {
                    continue;
                }
                $member_arr = $member_arr[0];
                $hasCarne = true;
                $hasUova = true;
                $hasFrutta = true;
                $hasFormaggi = true;
                $hasPesce = true;
                $hasVerdura = true;
                $jsonPreferencesBlacklist = [];
                if (!empty($user["carni"])) {
                    $hasCarne = false;
                    $jsonPreferencesBlacklist[] = [
                        "name" => "Carne",
                        "substitute" => isset($categories[$user["carniSost"]])
                            ? $categories[$user["carniSost"]]
                            : "-",
                    ];
                }
                if (!empty($user["formaggi"])) {
                    $hasFormaggi = false;
                    $jsonPreferencesBlacklist[] = [
                        "name" => "Formaggio",
                        "substitute" => isset(
                            $categories[$user["formaggiSost"]]
                        )
                            ? $categories[$user["formaggiSost"]]
                            : "-",
                    ];
                }
                if (!empty($user["frutta"])) {
                    $hasFrutta = false;
                    $jsonPreferencesBlacklist[] = [
                        "name" => "Frutta",
                        "substitute" => isset($categories[$user["fruttaSost"]])
                            ? $categories[$user["fruttaSost"]]
                            : "-",
                    ];
                }
                if (!empty($user["pesce"])) {
                    $hasPesce = false;
                    $jsonPreferencesBlacklist[] = [
                        "name" => "Pesce",
                        "substitute" => isset($categories[$user["pesceSost"]])
                            ? $categories[$user["pesceSost"]]
                            : "-",
                    ];
                }
                if (!empty($user["uova"])) {
                    $hasUova = false;
                    $jsonPreferencesBlacklist[] = [
                        "name" => "Uova",
                        "substitute" => isset($categories[$user["uovaSost"]])
                            ? $categories[$user["uovaSost"]]
                            : "-",
                    ];
                }
                if (!empty($user["verdura"])) {
                    $hasVerdura = false;
                    $jsonPreferencesBlacklist[] = [
                        "name" => "Verdura",
                        "substitute" => isset($categories[$user["verduraSost"]])
                            ? $categories[$user["verduraSost"]]
                            : "-",
                    ];
                }
                if (!empty($jsonPreferencesBlacklist)) {
                    update_user_meta(
                        $member_arr,
                        "old_box_preferences",
                        $jsonPreferencesBlacklist
                    );
                }
            }
            $response = new WP_REST_Response([]);
            $response->set_status(204);
            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "fix-subscriptions", [
        "methods" => "GET",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $subscriptions = wcs_get_subscriptions([
                "subscriptions_per_page" => -1,
                "subscription_status" => ["active", "on-hold"],
            ]);

            $subscriptionsArray = [];

            foreach ($subscriptions as $subscription) {
                $paymentMethod = $subscription->get_payment_method();
                $manualRenew = get_post_meta(
                    $subscription->get_id(),
                    "_requires_manual_renewal",
                    true
                );
                if ($paymentMethod == "bacs" || $manualRenew) {
                    $subscription->set_status("active");
                    $subscription->set_billing_period("year");
                    $subscription->set_billing_interval(100);

                    $subscription->update_dates([
                        "next_payment" => "2100-01-01 00:00:00",
                    ]);
                    $subscription->set_requires_manual_renewal(true);
                    $subscription->save();
                } else {
                    /*	$subscriptionsArray[] = $subscription->get_id();
					$today = new DateTime();
					$today->add(new DateInterval('P7D'));
					$today->setTime(12,00);
					  $subscription->update_dates([
                        "next_payment" => $today->format("Y-m-d H:i:s"),
                    ]);
					  $subscription->save();*/
                }
            }

            $response = new WP_REST_Response($subscriptionsArray);
            $response->set_status(200);
            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "fix-sku", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            global $wpdb;
            $duplicatedProducts = $wpdb->get_results(
                "select meta_value,COUNT(meta_value),GROUP_CONCAT(DISTINCT post_id ORDER BY post_id SEPARATOR ',') post_id
FROM wp_postmeta
JOIN wp_posts ON wp_posts.ID=wp_postmeta.post_id
WHERE meta_key = '_sku'
AND meta_value != ''
AND post_type = 'product'
GROUP BY meta_value HAVING COUNT(meta_value) > 1"
            );

            foreach ($duplicatedProducts as $duplicatedPost) {
                $postIds = explode(",", $duplicatedPost->post_id);

                foreach ($postIds as $postId) {
                    //$content_post = get_post($postId);
                    //$content = $content_post->post_content;
                    $terms = get_the_terms($postId, "product_cat");

                    if (count($terms) == 1) {
                        update_post_meta(
                            $postId,
                            "_sku",
                            "__" . $duplicatedPost->meta_value . "__" . time()
                        );
                        update_post_meta(
                            $postId,
                            "_navision_id",
                            "__" . $duplicatedPost->meta_value . "__" . time()
                        );
                    }
                }
            }
        },
    ]);

    register_rest_route("agrispesa/v1", "import-products", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $now = new DateTime();
            $file = "prodotti_" . $now->format("dmY_Hi") . ".xml";
            $uploadDire = wp_upload_dir($now->format("Y/m"));
            file_put_contents(
                $uploadDire["path"] . "/" . $file,
                $request->get_body()
            );

            ($xml = simplexml_load_string($request->get_body())) or
                die("Error: Cannot create object");
            $products = (array) $xml;

            //create categories blacklist
            $categories = [];
            global $wpdb;

            $productsToExclude = get_posts([
                "post_type" => ["product", "product_variation"],
                "numberposts" => -1,
                "fields" => "ids",
                "post_status" => "any",
                "tax_query" => [
                    [
                        "taxonomy" => "product_cat",
                        "field" => "slug",
                        "terms" => ["box", "sos", "box-singola", "gift-card"],
                        "operator" => "IN",
                    ],
                ],
            ]);

            $wpdb->query("UPDATE wp_posts
			SET post_status = 'draft'
			WHERE post_type = 'gruppo-prodotto';");

            $productIds = [];
            $newProducts = [];

            $productsToInclude = get_posts([
                "post_type" => "product",
                "numberposts" => -1,
                "fields" => "ids",
                "post_status" => "any",
            ]);

            /*  $wpdb->query(
                "UPDATE wp_postmeta SET meta_value = '0' WHERE meta_key = '_is_active_shop' AND post_id IN (" .
                    implode(",", $productsToInclude) .
                    ");"
            );*/
            $wpdb->query(
                "UPDATE wp_posts SET post_status = 'trash' WHERE post_type = 'product' AND ID IN (" .
                    implode(",", $productsToInclude) .
                    ");"
            );

            if (!empty($productsToExclude)) {
                /*  $wpdb->query(
                    "UPDATE wp_postmeta SET meta_value = '1' WHERE meta_key = '_is_active_shop' AND post_id IN (" .
                        implode(",", $productsToExclude) .
                        ");"
                );*/
                $wpdb->query(
                    "UPDATE wp_posts SET post_status = 'publish' WHERE ID IN (" .
                        implode(",", $productsToExclude) .
                        ");"
                );
            }

            $arrotondamenti = array_filter($products["ROW"], function (
                $product
            ) {
                $product = (array) $product;
                return strtolower(
                    (string) $product["productgroupdescription"]
                ) == "arrotondamento spese";
            });

            $productIds = [];

            foreach ($arrotondamenti as $arrotondamento) {
                $arrotondamento = (array) $arrotondamento;

                $productId = get_posts([
                    "post_type" => "product",
                    "post_status" => ["publish", "draft", "trash"],
                    "fields" => "ids",
                    "posts_per_page" => 1,
                    "meta_key" => "_sku",
                    "meta_value" => $arrotondamento["id_product"],
                ]);

                if (empty($productId)) {
                    $productObj = new WC_Product_Simple();
                    $productObj->set_name(
                        (string) $arrotondamento["description"] .
                            " " .
                            (string) $arrotondamento["description2"]
                    );
                    $productObj->set_sku(
                        (string) $arrotondamento["id_product"]
                    );
                    $productObj->save();
                    $productId = $productObj->get_id();

                    $term = get_term_by(
                        "slug",
                        "arrotondamenti",
                        "product_cat"
                    );
                    wp_set_object_terms(
                        $productId,
                        $term->term_id,
                        "product_cat"
                    );

                    $price = floatval(
                        str_replace(
                            ",",
                            ".",
                            (string) $arrotondamento["unitprice"]
                        )
                    );
                    update_post_meta($productId, "_regular_price", $price);
                    update_post_meta($productId, "_price", $price);
                    update_post_meta(
                        $productId,
                        "_navision_id",
                        (string) $arrotondamento["id_product"]
                    );

                    update_post_meta(
                        $productId,
                        "_sku",
                        (string) $arrotondamento["id_product"]
                    );

                    $newProducts[] = $productObj;
                }
            }

            $activeProducts = array_filter($products["ROW"], function (
                $product
            ) {
                $product = (array) $product;
                $price = str_replace(",", ".", (string) $product["unitprice"]);
                return $price > 0;
            });

            foreach ($activeProducts as $key => $product) {
                $product = (array) $product;

                if ((string) $product["productgroupcode"] == "arrotondamento") {
                    continue;
                }
                if ((string) $product["productgroupcode"] == "TRASPORTO") {
                    update_option(
                        "delivery_product_sku",
                        (string) $product["id_product"]
                    );
                    continue;
                }

                // $sku = (string) $product["id_product"];
                // $sku = explode("_", $sku);

                /* CREATE GROUP */

                $product["itemcategorydescription"] =
                    (string) $product["itemcategorydescription"];
                $product["productgroupcode"] =
                    (string) $product["productgroupcode"];
                $product["productgroupdescription"] =
                    (string) $product["productgroupdescription"];

                if (!isset($categories[$product["itemcategorydescription"]])) {
                    $categories[$product["itemcategorydescription"]] = [
                        "name" => $product["itemcategorydescription"],
                        "subcategories" => [],
                    ];
                }

                $product["productgroupcode"] = explode(
                    "-",
                    $product["productgroupcode"]
                );
                if (isset($product["productgroupcode"][1])) {
                    $product["productgroupcode"] =
                        $product["productgroupcode"][1];
                } else {
                    $product["productgroupcode"] =
                        $product["productgroupcode"][0];
                }

                if (
                    !isset(
                        $categories[$product["itemcategorydescription"]][
                            "subcategories"
                        ][$product["productgroupcode"]]
                    )
                ) {
                    $categories[$product["itemcategorydescription"]][
                        "subcategories"
                    ][$product["productgroupcode"]] = [
                        "description" => $product["productgroupdescription"],
                        "code" => $product["productgroupcode"],
                        "products" => [],
                    ];
                }

                $categories[$product["itemcategorydescription"]][
                    "subcategories"
                ][$product["productgroupcode"]]["products"][] =
                    (string) $product["id_product"];
            }

            foreach ($categories as $category) {
                if (
                    $category["name"] == "TRASPORTO" ||
                    $category["name"] == "ABBONAMENTI"
                ) {
                    continue;
                }

                foreach ($category["subcategories"] as $code => $subcategory) {
                    if (empty($subcategory)) {
                        continue;
                    }

                    $categoryAlreadyExists = get_posts([
                        "post_type" => "gruppo-prodotto",
                        "post_status" => ["publish", "draft"],
                        "fields" => "ids",
                        "posts_per_page" => 1,
                        "meta_key" => "codice_gruppo_prodotto",
                        "meta_value" => $code,
                    ]);
                    $gruppoProdotto = null;

                    if (empty($categoryAlreadyExists)) {
                        $gruppoProdotto = wp_insert_post([
                            "post_title" => $subcategory["description"],
                            "post_content" => "",
                            "post_status" => "draft",
                            "post_author" => 1,
                            "post_type" => "gruppo-prodotto",
                        ]);
                    } else {
                        $gruppoProdotto = $categoryAlreadyExists[0];
                    }

                    if (!$gruppoProdotto) {
                        continue;
                    }

                    update_post_meta(
                        $gruppoProdotto,
                        "codice_gruppo_prodotto",
                        $code
                    );
                    update_post_meta(
                        $gruppoProdotto,
                        "categoria_principale_gruppo_prodotto",
                        strtolower($category["name"])
                    );
                    update_post_meta(
                        $gruppoProdotto,
                        "products_sku",
                        $subcategory["products"]
                    );
                }
            }

            foreach ($activeProducts as $product) {
                $product = (array) $product;

                if (
                    is_object($product["description2"]) &&
                    get_class($product["description2"]) ==
                        SimpleXMLElement::class
                ) {
                    $product["description2"] = $product[
                        "description2"
                    ]->__toString();
                }

                $sku = (string) $product["id_product"];
                //  $sku = explode("_", $sku);

                $productName =
                    (string) $product["description"] .
                    " " .
                    (string) $product["description2"];

                $productId = get_posts([
                    "post_type" => "product",
                    "post_status" => ["publish", "draft", "trash"],
                    "fields" => "ids",
                    "posts_per_page" => 1,
                    "meta_key" => "_sku",
                    "meta_value" => $sku,
                ]);

                if (!empty($productId)) {
                    $productId = reset($productId);
                }

                $productObj = null;

                if (empty($productId)) {
                    $productObj = new WC_Product_Simple();
                    $productObj->set_name($productName);
                    $productObj->save();
                    $productId = $productObj->get_id();

                    $term = get_term_by(
                        "slug",
                        "senza-categoria",
                        "product_cat"
                    );
                    wp_set_object_terms(
                        $productId,
                        $term->term_id,
                        "product_cat"
                    );

                    $newProducts[] = $productObj;
                } else {
                    $productObj = wc_get_product($productId);
                }

                $price = (string) $product["unitprice"];
                $price = str_replace(",", ".", $price);
                $price = floatval($price);
                $productObj->set_price($price);
                $productObj->save();
                $product["wordpress_id"] = $productObj->get_id();
                //$productIds[] = $productObj->get_id();
                //update_post_meta($productId,'_is_active_shop',1);
                /*
				$product = new WC_Product($productId);
				$product->set_status('publish');
				$product->save();
				*/

                update_post_meta(
                    $productObj->get_id(),
                    "_regular_price",
                    $price
                );
                update_post_meta($productObj->get_id(), "_price", $price);
                update_post_meta($productObj->get_id(), "_sku", $sku);
                update_post_meta(
                    $productObj->get_id(),
                    "_id_produttore_navision",
                    (string) $product["id_producercard"]
                );

                update_post_meta(
                    $productObj->get_id(),
                    "_navision_id",
                    (string) $product["id_product"]
                );

                $code = (string) $product["productgroupcode"];
                $code = explode("-", $code);
                if (isset($code[1])) {
                    $code = $code[1];
                } else {
                    $code = $code[0];
                }
                update_post_meta(
                    $productObj->get_id(),
                    "_gruppo_prodotto",
                    $code
                );
            }

            //Attivo i prodotti
            /*$productIds = array_unique($productIds);
            $wpdb->query(
                "UPDATE wp_posts SET post_status = 'publish' WHERE ID IN (" .
                    implode(",", $productIds) .
                    ")"
            );*/

            $wpdb->query(
                "UPDATE wp_posts
	SET post_status = 'draft'
	WHERE post_type = 'product' AND ID NOT IN (" .
                    implode(",", $productsToExclude) .
                    ")"
            );

            $newIdsProducts = [];
            if (count($newProducts) > 0) {
                $list = "<ul>";
                foreach ($newProducts as $product) {
                    $newIdsProducts[] = $product->get_id();
                    $list .=
                        '<li><a href="https://agrispesa.it/wp-admin/post.php?post=' .
                        $product->get_id() .
                        '&action=edit">' .
                        $product->get_name() .
                        "</a></li>";
                }
                $list .= "</ul>";
                wp_mail(
                    "agrispesa@agrispesa.it",
                    count($newProducts) . " nuovi prodotti",
                    "Ecco la lista dei nuovi prodotti inseriti: <br><br>" .
                        $list
                );
            }
            update_option(
                "last_import_products",
                (new DateTime())->format("Y-m-d H:i:s")
            );

            if (!empty($productsToExclude)) {
                /* $wpdb->query(
                    "UPDATE wp_postmeta SET meta_value = '1' WHERE meta_key = '_is_active_shop' AND post_id IN (" .
                        implode(",", $productsToExclude) .
                        ");"
                );*/
                $wpdb->query(
                    "UPDATE wp_posts SET post_status = 'publish' WHERE ID IN (" .
                        implode(",", $productsToExclude) .
                        ");"
                );
            }

            wc_recount_all_terms();

            $response = new WP_REST_Response($newIdsProducts);
            $response->set_status(201);
            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "import-box", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $now = new DateTime();
            $file = "box_" . $now->format("dmY_Hi") . ".xml";
            $uploadDire = wp_upload_dir($now->format("Y/m"));
            file_put_contents(
                $uploadDire["path"] . "/" . $file,
                $request->get_body()
            );

            ($xml = simplexml_load_string($request->get_body())) or
                die("Error: Cannot create object");
            $products = (array) $xml;
            $boxes = [];
            global $wpdb;

            $productsToExclude = get_posts([
                "post_type" => ["product", "product_variation"],
                "numberposts" => -1,
                "fields" => "ids",
                "post_status" => "any",
                "tax_query" => [
                    [
                        "taxonomy" => "product_cat",
                        "field" => "slug",
                        "terms" => ["box", "sos", "box-singola", "gift-card"],
                        "operator" => "IN",
                    ],
                ],
            ]);
            /* ACTIVATE GRUPPI CATEGORIE */

            $allGroups = get_posts([
                "post_type" => "gruppo-prodotto",
                "post_status" => ["draft", "publish"],
                "posts_per_page" => -1,
            ]);

            $allGroups = array_map(function ($group) {
                $group->products_sku = get_post_meta(
                    $group->ID,
                    "products_sku",
                    true
                );
                return $group;
            }, $allGroups);

            foreach ($products["ROW"] as $product) {
                $product = (array) $product;

                if (strstr((string) $product["offer_no"], "STCOMP") == false) {
                    continue;
                }

                $sku = $product["id_product"];

                $isFoundGroup = array_filter($allGroups, function ($group) use (
                    $sku
                ) {
                    return is_array($group->products_sku) &&
                        in_array($sku, $group->products_sku);
                });

                if (!empty($isFoundGroup)) {
                    foreach ($isFoundGroup as $group) {
                        wp_update_post([
                            "ID" => $group->ID,
                            "post_status" => "publish",
                        ]);
                    }
                }
            }
            /* FINE ACTIVATE GRUPPI CATEGORIE */

            $productsSku = [];
            $week = null;
            foreach ($products["ROW"] as $product) {
                $product = (array) $product;
                if (!isset($boxes[(string) $product["offer_no"]])) {
                    $boxes[(string) $product["offer_no"]] = [];
                }
                $productsSku[] = (string) $product["id_product"];
                $boxes[(string) $product["offer_no"]][] = $product;
                $week = explode("-", (string) $product["offer_no"]);
                $week = substr($week[0], -2);
            }
            $productsSku = array_unique($productsSku);

            $productsToInclude = get_posts([
                "post_type" => ["product", "product_variation"],
                "numberposts" => -1,
                "fields" => "ids",
                "post_status" => "any",
            ]);

            /* $wpdb->query(
                "UPDATE wp_postmeta SET meta_value = '0' WHERE meta_key = '_is_active_shop' AND post_id IN (" .
                    implode(",", $productsToInclude) .
                    ");"
            );*/
            $wpdb->query(
                "UPDATE wp_posts SET post_status = 'trash' WHERE post_type = 'product' AND ID IN (" .
                    implode(",", $productsToInclude) .
                    ");"
            );

            $productsToExclude[] = 17647;

            if (!empty($productsToExclude)) {
                /* $wpdb->query(
                    "UPDATE wp_postmeta SET meta_value = '1' WHERE meta_key = '_is_active_shop' AND post_id IN (" .
                        implode(",", $productsToExclude) .
                        ");"
                );*/
                $wpdb->query(
                    "UPDATE wp_posts SET post_status = 'publish' WHERE ID IN (" .
                        implode(",", $productsToExclude) .
                        ");"
                );
            }

            $facciamoNoiProducts = [];
            $scegliTuProducts = [];

            /*
 			$importedPosts = $wpdb->get_results(
                "SELECT post_id,meta_value FROM " .
                    $wpdb->postmeta .
                    ' WHERE meta_key = "_navision_id" and meta_value IN ("' .
                    implode('","', $productsSku) .
                    '")'
            );
            $postIds = array_map(function ($post) {
                return $post->post_id;
            }, $importedPosts);*/

            $postIds = [];
            $skuBoxSingole = array_keys($boxes);
            $skuBoxSingole = array_map(function ($box) {
                $id = explode("-", $box);
                return $id[1];
            }, $skuBoxSingole);

            foreach ($skuBoxSingole as $sku) {
                $singleProductBox = new WP_Query([
                    "post_type" => "product_variation",
                    "meta_key" => "_sku",
                    "meta_value" => $sku,
                    "order" => "DESC",
                    "posts_per_page" => 1,
                ]);
                if ($singleProductBox->have_posts()) {
                    $singleProductBox = $singleProductBox->get_posts();
                    $postIds[] = $singleProductBox[0]->ID;
                    $postIds[] = $singleProductBox[0]->post_parent;
                }
            }

            if (!empty($postIds)) {
                $wpdb->query(
                    "UPDATE wp_posts SET post_status = 'publish' WHERE ID IN (" .
                        implode(",", $postIds) .
                        ")"
                );
                /*$wpdb->query(
                    "UPDATE wp_postmeta SET meta_value = '1' WHERE meta_key = '_is_active_shop' AND post_id IN (" .
                        implode(",", $postIds) .
                        ")"
                );*/
            }

            $boxIds = [];
            //delete all box for the same week
            /*$boxIdsToDelete = $wpdb->get_results(
                "select ID from wp_posts p,wp_postmeta m where p.post_type = 'weekly-box' and p.ID = m.post_id and m.meta_key = '_week' and m.meta_value = '" .
                    date("Y") .
                    "_" .
                    $week .
                    "'"
            );
            $boxIdsToDelete = array_map(function ($post) {
                return $post->ID;
            }, $boxIdsToDelete);

            if (!empty($boxIdsToDelete)) {
                $wpdb->query(
                    "DELETE from wp_posts  WHERE ID IN (" .
                        implode(",", $boxIdsToDelete) .
                        ")"
                );
            }*/
            $wpdb->query("DELETE pm
	FROM wp_postmeta pm
	LEFT JOIN wp_posts wp ON wp.ID = pm.post_id
	WHERE wp.ID IS NULL");

            foreach ($boxes as $idBox => $boxProducts) {
                $boxName =
                    "Box settimana " . date("Y") . "_" . $week . " - " . $idBox;

                // DELETE BOX FOR THE CURRENT WEEK
                $wpdb->query(
                    'DELETE from wp_posts WHERE post_title = "' . $boxName . '"'
                );

                $navisionId = explode("-", $idBox);
                $navisionId = end($navisionId);

                $singleProductBox = new WP_Query([
                    "post_type" => "product_variation",
                    "fields" => "ids",
                    "meta_key" => "_sku",
                    "post_status" => ["publish"],
                    "meta_value" => $navisionId,
                    "order" => "DESC",
                    "posts_per_page" => 1,
                ]);

                if (!$singleProductBox->have_posts()) {
                    continue;
                }

                $singleProductBox = $singleProductBox->get_posts();
                $singleProductBox = reset($singleProductBox);

                $post_id = wp_insert_post([
                    "post_type" => "weekly-box",
                    "post_title" => $boxName,
                    "post_content" => "",
                    "post_status" => "publish",
                    "comment_status" => "closed", // if you prefer
                    "ping_status" => "closed", // if you prefer
                ]);

                if (!$post_id) {
                    $response = new WP_REST_Response([
                        "error" =>
                            "Errore creazione box " .
                            "Box settimana " .
                            date("Y") .
                            "_" .
                            $week .
                            " - " .
                            $idBox,
                    ]);
                    $response->set_status(500);
                    return $response;
                }

                // insert post meta
                $deliveryDate =
                    (string) $boxProducts[0]["requesteddeliverydate"];
                $deliveryDate = DateTime::createFromFormat(
                    "dmY",
                    $deliveryDate
                );
                update_post_meta($post_id, "_week", date("Y") . "_" . $week);
                update_post_meta(
                    $post_id,
                    "_data_consegna",
                    $deliveryDate->format("Y-m-d")
                );
                update_post_meta(
                    $post_id,
                    "_product_box_id",
                    $singleProductBox
                );
                update_post_meta(
                    $post_id,
                    "_navision_id",
                    (string) $boxProducts[0]["offer_no"]
                );

                $arrayProducts = [];
                foreach ($boxProducts as $boxProduct) {
                    if (is_array($boxProduct["id_product"])) {
                        $boxProduct["id_product"] =
                            $boxProduct["id_product"][0];
                    }

                    $boxProduct["id_product"] =
                        (string) $boxProduct["id_product"];

                    if ($boxProduct["id_product"] == "TRASPORTO1") {
                        update_option(
                            "delivery_product_offer_no",
                            (string) $boxProduct["offer_line_no"]
                        );
                        continue;
                    }

                    $singleProduct = new WP_Query([
                        "post_type" => "product",
                        "meta_key" => "_navision_id",
                        "post_status" => [
                            "publish",
                            "private",
                            "trash",
                            "draft",
                        ],
                        "meta_value" => $boxProduct["id_product"],
                        "order" => "ASC",
                        "posts_per_page" => 1,
                    ]);
                    $productId = null;
                    $productName = null;

                    if ($singleProduct->have_posts()) {
                        // continue;

                        $singleProduct = $singleProduct->get_posts();
                        $singleProduct = reset($singleProduct);
                        $productId = $singleProduct->ID;
                        $productName = $singleProduct->post_title;
                    }

                    //update_post_meta($singleProduct->ID,'_is_active_shop',1);

                    $arrayProducts[] = [
                        "id" => $productId,
                        "navision_id" => $boxProduct["id_product"],
                        "quantity" => 1,
                        "name" => $productName,
                        "offer_line_no" =>
                            (string) $boxProduct["offer_line_no"],
                    ];

                    $isActive = 1;

                    if (
                        strstr((string) $product["offer_no"], "STCOMP") == false
                    ) {
                        $isActive = 0;
                        $facciamoNoiProducts[] = $singleProduct->ID;
                    } else {
                        $scegliTuProducts[] = $singleProduct->ID;
                    }

                   /* update_post_meta(
                        $singleProduct->ID,
                        "_is_active_shop",
                        $isActive
                    );*/
                    update_post_meta(
                        $singleProduct->ID,
                        "_sku",
                        $boxProduct["id_product"]
                    );
                }
                add_post_meta($post_id, "_products", $arrayProducts);
                $boxIds[] = $post_id;
            }

            // ENABLE PRODUCTS
            if (!empty($scegliTuProducts)) {
                $wpdb->query(
                    "UPDATE wp_posts SET post_status = 'publish' WHERE ID IN (" .
                        implode(",", $scegliTuProducts) .
                        ");"
                );
            }

            if (!empty($facciamoNoiProducts)) {
                $wpdb->query(
                    "UPDATE wp_posts SET post_status = 'private' WHERE post_type = 'product' AND ID IN (" .
                        implode(",", $facciamoNoiProducts) .
                        ");"
                );
            }

            wc_recount_all_terms();
            update_option(
                "last_import_box",
                (new DateTime())->format("Y-m-d H:i:s")
            );

            if (!empty($productsToExclude)) {
                /*  $wpdb->query(
                    "UPDATE wp_postmeta SET meta_value = '1' WHERE meta_key = '_is_active_shop' AND post_id IN (" .
                        implode(",", $productsToExclude) .
                        ");"
                );*/
                $wpdb->query(
                    "UPDATE wp_posts SET post_status = 'publish' WHERE ID IN (" .
                        implode(",", $productsToExclude) .
                        ");"
                );
            }

            $lastOrderWeek = get_option("current_order_week", true);

            update_option("current_order_week", date("Y") . "_" . $week);
            update_option("last_order_week", $lastOrderWeek);

            $response = new WP_REST_Response($boxIds);
            $response->set_status(201);

            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "import-fido", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $now = new DateTime();
            $file = "fido_" . $now->format("dmY") . ".xml";
            $uploadDire = wp_upload_dir($now->format("Y/m"));
            file_put_contents(
                $uploadDire["path"] . "/" . $file,
                $request->get_body()
            );

            ($xml = simplexml_load_string($request->get_body())) or
                die("Error: Cannot create object");
            $users = (array) $xml;

            $users = $users["ROW"];

            foreach ($users as $user) {
                $user = (array) $user;

                $args = [
                    "fields" => "ids",
                    "meta_query" => [
                        [
                            "key" => "navision_id",
                            "value" => $user["id_codeclient"],
                            "compare" => "=",
                        ],
                    ],
                ];

                $userObj = get_users($args); //finds all users with this meta_key == 'member_id' and meta_value == $member_id passed in url
                if (empty($userObj)) {
                    continue;
                }

                $user["balance"] = substr($user["balance"], 0, -2);

                update_user_meta(
                    $userObj[0],
                    "_saldo_navision",
                    $user["balance"]
                );

                $saldo = $user["balance"];
                if ($saldo == "-0") {
                    $saldo = 0;
                }
                $saldo = str_replace(",", ".", $saldo);
                $saldo = floatval($saldo);

                if ($saldo > 0) {
                    update_user_meta(
                        $userObj[0],
                        "_current_woo_wallet_balance",
                        $saldo
                    );
                } else {
                    update_user_meta(
                        $userObj[0],
                        "_current_woo_wallet_balance",
                        0
                    );
                }
            }

            update_option(
                "last_import_fido",
                (new DateTime())->format("Y-m-d H:i:s")
            );

            $response = new WP_REST_Response([]);
            $response->set_status(204);

            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "export-customers", [
        "methods" => "GET",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $orders = wc_get_orders([
                "limit" => -1,
                "status" => "completed",
                "orderby" => "date",
                "order" => "DESC",
            ]);

            $doc = new DOMDocument();
            $doc->formatOutput = true;
            $root = $doc->createElement("ROOT");
            $root = $doc->appendChild($root);
            $customers = [];
            foreach ($orders as $order) {
                if (
                    get_class($order) ==
                    \Automattic\WooCommerce\Admin\Overrides\OrderRefund::class
                ) {
                    continue;
                }

                if (in_array($order->get_customer_id(), $customers)) {
                    continue;
                }

                $user = get_userdata($order->get_customer_id());

                if (!$user) {
                    continue;
                }
                $customers[] = $order->get_customer_id();

                $isSubscription = get_post_meta(
                    $order->get_id(),
                    "_subscription_id",
                    true
                );

                $customerType = "STP";

                $subscription = null;
                if ($isSubscription) {
                    $subscription = new WC_Subscription($isSubscription);
                    $products = $subscription->get_items();

                    if (empty($products)) {
                        continue;
                    }

                    $box = reset($products)->get_product();
                    if (!$box) {
                        continue;
                    }

                    $tipologia = get_post_meta(
                        $box->get_id(),
                        "attribute_pa_tipologia",
                        true
                    );
                    $dimensione = get_post_meta(
                        $box->get_id(),
                        "attribute_pa_dimensione",
                        true
                    );
                    $productBox = get_single_box_from_attributes(
                        $tipologia,
                        $dimensione
                    );

                    if (!$productBox) {
                        continue;
                    }

                    $navisionIdBox = get_post_meta(
                        $productBox->get_id(),
                        "_navision_id",
                        true
                    );

                    if (empty($navisionIdBox)) {
                        continue;
                    }

                    $customerType = $navisionIdBox[0];
                }

                $row = $doc->createElement("ROW");
                $ele1 = $doc->createElement("id_codeclient");

                $idNavision = get_user_meta(
                    $order->get_customer_id(),
                    "navision_id",
                    true
                );
                if (!$idNavision) {
                    $idNavision = 500000 + $order->get_customer_id();
                    update_user_meta(
                        $order->get_customer_id(),
                        "navision_id",
                        $idNavision
                    );
                }

                $ele1->nodeValue = $idNavision;
                $row->appendChild($ele1);
                $ele2 = $doc->createElement("business_name");
                $ele2->nodeValue = ucwords(
                    strtolower(
                        $order->get_billing_first_name() .
                            " " .
                            $order->get_billing_last_name()
                    )
                );
                $row->appendChild($ele2);

                $taxCode = get_post_meta(
                    $order->get_id(),
                    "_codice_fiscale",
                    true
                );

                if (!$taxCode) {
                    $taxCode = get_user_meta(
                        $order->get_customer_id(),
                        "codice_fiscale",
                        true
                    );
                }

                if (!$taxCode) {
                    $taxCode = "";
                }

                $ele2 = $doc->createElement("tax_code");
                $ele2->nodeValue = $taxCode;
                $row->appendChild($ele2);
                $vatCode = get_post_meta(
                    $order->get_id(),
                    "_partita_iva",
                    true
                );
                if (!$vatCode) {
                    $vatCode = "";
                }

                $phone = $order->get_billing_phone();
                if (empty($phone)) {
                    $phone = $order->get_shipping_phone();
                }

                if (empty($phone)) {
                    $phone = get_user_meta(
                        $order->get_customer_id(),
                        "billing_phone",
                        true
                    );
                    if (!$phone) {
                        $phone = get_user_meta(
                            $order->get_customer_id(),
                            "shipping_phone",
                            true
                        );
                    }
                    if (!$phone) {
                        $phone = get_user_meta(
                            $order->get_customer_id(),
                            "billing_cellulare",
                            true
                        );
                        update_user_meta(
                            $order->get_customer_id(),
                            "billing_phone",
                            $phone
                        );
                        update_user_meta(
                            $order->get_customer_id(),
                            "shipping_phone",
                            $phone
                        );
                    }
                }

                $ele2 = $doc->createElement("vat_number");
                $ele2->nodeValue = $vatCode;
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("address");
                $ele2->nodeValue = $order->get_billing_address_1();
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("city");
                $ele2->nodeValue = $order->get_billing_city();
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("postcode");
                $ele2->nodeValue = $order->get_billing_postcode();
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("province");
                $ele2->nodeValue = $order->get_billing_state();
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("nation");
                $ele2->nodeValue = "IT";
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("email");
                $ele2->nodeValue = $user->user_email;
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("phone");
                $ele2->nodeValue = $phone;
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("phoneoffice");
                $ele2->nodeValue = "";
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("mobile");
                $ele2->nodeValue = $order->get_billing_phone();
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("mobile2");
                $ele2->nodeValue = $order->get_billing_phone();
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("fax");
                $ele2->nodeValue = "";
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("codicemodellocliente");
                $ele2->nodeValue = "ITPRIV";
                $row->appendChild($ele2);

                //FIX MAPPING
                if (in_array($customerType, ["FNPESG", "FNVEGAG", "FNVEGEG"])) {
                    $customerType = "FNG";
                }

                if (in_array($customerType, ["FNPESM", "FNVEGAM", "FNVEGEM"])) {
                    $customerType = "FNM";
                }

                if (
                    in_array($customerType, [
                        "FNPESP",
                        "FNVEGAP",
                        "FNVEGEP",
                        "FNPESPP",
                        "FNPP",
                        "FNVEGEPP",
                        "FNVEGAPP",
                    ])
                ) {
                    $customerType = "FNP";
                }

                $ele2 = $doc->createElement("codiceabbonamento");
                $ele2->nodeValue = "ABSP-" . $customerType;
                $row->appendChild($ele2);

                $startDate = $subscription
                    ? $subscription->get_date("start")
                    : null;

                $dataAbbonamento = "";
                if ($startDate) {
                    $startDate = new DateTime($startDate);
                    $dataAbbonamento = $startDate->format("dmY");
                }

                $ele2 = $doc->createElement("dataabbonamento");
                $ele2->nodeValue = $dataAbbonamento;
                $row->appendChild($ele2);
                $ele2 = $doc->createElement("fido");

                $fido = get_user_meta(
                    $order->get_customer_id(),
                    "_saldo_navision",
                    true
                );
                if (!$fido) {
                    $fido = 0;
                    $fido = number_format(floatval($fido), 4);
                    $fido = str_replace(".", ",", $fido);
                }

                $ele2->nodeValue = $fido;
                $row->appendChild($ele2);
                $root->appendChild($row);
            }
            header("Content-type: text/xml");
            die($doc->saveXml());
        },
    ]);
    register_rest_route("agrispesa/v1", "import-invoices", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            ($xml = simplexml_load_string($request->get_body())) or
                die("Error: Cannot create object");
            $invoices = (array) $xml;

            $invoices = $invoices["ROW"];

            if (!is_array($invoices)) {
                $invoices = [$invoices];
            }

            $newInvoices = [];
            foreach ($invoices as $key => $invoice) {
                $invoice = (array) $invoice;

                $alreadyExists = get_posts([
                    "post_type" => "invoice",
                    "fields" => "ids",
                    "post_status" => "publish",
                    "posts_per_page" => 1,
                    "meta_query" => [
                        [
                            "key" => "_navision_id",
                            "value" => $invoice["documentno"],
                            "compare" => "=",
                        ],
                    ],
                ]);
                if (!empty($alreadyExists)) {
                    continue;
                }
                $my_post = [
                    "post_title" => $invoice["documentno"],
                    "post_content" => "",
                    "post_status" => "publish",
                    "post_type" => "invoice",
                ];
                // Insert the post into the database
                $postId = wp_insert_post($my_post);
                $createdDate = DateTime::createFromFormat(
                    "dmY",
                    $invoice["postingdate"]
                );
                update_post_meta(
                    $postId,
                    "_navision_id",
                    $invoice["documentno"]
                );
                update_post_meta(
                    $postId,
                    "_customer_id",
                    $invoice["id_codeclient"]
                );
                update_post_meta(
                    $postId,
                    "_created_date",
                    $createdDate->format("Y-m-d")
                );
                update_post_meta(
                    $postId,
                    "_order_id",
                    trim(str_replace("Ordine ", "", $invoice["reasoncode"]))
                );

                $type = "ORDINE";
                if (
                    strstr(strtolower($invoice["reasoncode"]), "nota") !== false
                ) {
                    $type = "NOTA_CREDITO";
                }

                update_post_meta($postId, "_invoice_type", $type);

                update_post_meta(
                    $postId,
                    "_filename",
                    trim(str_replace("/", "_", $invoice["documentno"])) . ".pdf"
                );
                update_post_meta(
                    $postId,
                    "_amount",
                    trim(str_replace("-", "", $invoice["amount"]))
                );
                $newInvoices[] = $postId;
            }
            update_option(
                "last_import_invoices",
                (new DateTime())->format("Y-m-d H:i:s")
            );
            $response = new WP_REST_Response($newInvoices);
            $response->set_status(201);
            return $response;
        },
    ]);

    function addItemToOrder(
        $doc,
        $root,
        $navisionId,
        $order,
        $piano,
        $scala,
        $orderNote,
        $productNavisionId,
        $item,
        $currentWeek,
        $offerLineNo,
        $productPrice,
        $datePaid = null,
        $boxCode = null,
        $shippingName = null
    ) {
        $userNavisionId = $order->user_navision_id;

        if (!$boxCode) {
            $boxCode = $order->box_code;
        }

        $couponPercent = 0;
        foreach ($order->get_coupon_codes() as $coupon_code) {
            $coupon = new WC_Coupon($coupon_code);
            if ($coupon->get_discount_type() == "percent") {
                $couponPercent += $coupon->get_amount();
            }
        }
        $productNavisionId = str_replace("__", "", $productNavisionId);

        $row = $doc->createElement("ROW");
        $ele1 = $doc->createElement("id_order");
        $ele1->nodeValue = $navisionId;
        $row->appendChild($ele1);

        $ele1 = $doc->createElement("id_codeclient");
        $ele1->nodeValue = $userNavisionId;

        $row->appendChild($ele1);
        $ele1 = $doc->createElement("date");
        $ele1->nodeValue = (new DateTime($datePaid))->format("dmY");
        $row->appendChild($ele1);
        $ele1 = $doc->createElement("date_consegna");
        $ele1->nodeValue = "01011970";
        $row->appendChild($ele1);
        $ele1 = $doc->createElement("sh_name");

        if (!$shippingName) {
            $shippingName =
                $order->get_shipping_last_name() .
                " " .
                $order->get_shipping_first_name();
        }
        $ele1->nodeValue = ucwords(strtolower($shippingName));
        $row->appendChild($ele1);
        $ele1 = $doc->createElement("sh_address");
        $ele1->nodeValue = $order->get_shipping_address_1();
        $row->appendChild($ele1);
        $ele1 = $doc->createElement("sh_description1");

        $details = [];
        if (!empty($scala)) {
            $details[] = $scala;
        }
        if (!empty($piano)) {
            $details[] = $piano;
        }
        $ele1->nodeValue = trim(implode(" | ", $details));
        $row->appendChild($ele1);

        $ele1 = $doc->createElement("comment_lines");
        $ele1->nodeValue = $orderNote;
        $row->appendChild($ele1);

        $city = str_replace("-", " ", $order->get_shipping_city());

        $isToUpdate = false;

        $cities = explode(" ", $city);
        foreach ($cities as $key => $city) {
            for ($i = 0; $i < strlen($city); $i++) {
                if (isset($city[$i + 1])) {
                    if (ctype_lower($city[$i]) && ctype_upper($city[$i + 1])) {
                        $city = substr_replace($city, "'", $i + 1, 0);
                        $isToUpdate = true;
                    }
                }
            }
            $cities[$key] = $city;
        }

        if ($isToUpdate) {
        }

        $city = implode(" ", $cities);
        $city = strtoupper($city);

        $ele2 = $doc->createElement("sh_city");
        $ele2->nodeValue = $city;
        $row->appendChild($ele2);
        $ele2 = $doc->createElement("sh_postcode");
        $ele2->nodeValue = $order->get_shipping_postcode();
        $row->appendChild($ele2);
        $ele2 = $doc->createElement("sh_province");
        $ele2->nodeValue = $order->get_shipping_state();
        $row->appendChild($ele2);
        $ele2 = $doc->createElement("id_product");
        $ele2->nodeValue = $productNavisionId;

        $quantity = 1;
        if ($item) {
            $quantity = $item->get_quantity();
        }

        $row->appendChild($ele2);
        $ele2 = $doc->createElement("quantity");
        $ele2->nodeValue = $quantity;

        $row->appendChild($ele2);
        $ele2 = $doc->createElement("discount");
        $ele2->nodeValue = "" . $couponPercent;
        $row->appendChild($ele2);
        $ele2 = $doc->createElement("unitprice");

        $productPrice = str_replace(
            ".",
            ",",
            number_format(floatval($productPrice) / $quantity, 4)
        );

        $ele2->nodeValue = $productPrice;
        $row->appendChild($ele2);
        $ele2 = $doc->createElement("ref_offer_no");

        $ele2->nodeValue = $currentWeek . "-" . $boxCode;
        $row->appendChild($ele2);
        $ele2 = $doc->createElement("ref_offer_line_no");
        $ele2->nodeValue = $offerLineNo;
        $row->appendChild($ele2);
        $root->appendChild($row);
    }

    register_rest_route("agrispesa/v1", "check-cart-coupon", [
        "methods" => "GET",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $couponCode = $request->get_param("coupon_code");

            global $wpdb;
            $postid = $wpdb->get_var(
                "SELECT ID FROM $wpdb->posts WHERE post_title = '" .
                    $couponCode .
                    "'"
            );

            $relatedCouponId = get_post_meta($postid, "coupon_parent_id", true);

            if (!$relatedCouponId && strtolower($couponCode) != "welovedenso") {
                $response = new WP_REST_Response([
                    "coupon_code" => $couponCode,
                ]);
                $response->set_status(200);
                return $response;
            }


            $items = WC()->session->get("cart", null);

            WC()->session->set("applied_coupons", []);

            $coupons = [];

            $hasProducts = false;
            $hasBox = false;

            foreach ($items as $cart_item) {
                // Product id
                $product_id = $cart_item["product_id"];

                $categories = get_the_terms($product_id, "product_cat");
                foreach ($categories as $term) {
                    if (in_array($term->slug, ["box"])) {
                        $hasBox = true;
                    } else {
                        $hasProducts = true;
                    }
                }
            }

            if (strtolower($couponCode) == "welovedenso") {
                if ($hasProducts) {
                    $coupons[] = "WELOVEDENSO10";
                }
                if ($hasBox) {
                    $coupons[] = "WELOVEDENSO";
                }
            }else{

            $couponCodeForScegliTu = $relatedCouponId;

			 if ($hasProducts) {
                    $coupons[] = $couponCodeForScegliTu;
                }
                if ($hasBox) {
                    $coupons[] = $couponCode;
                }

            }

			$userId = $request->get_param("user_id");
			if($userId==0){
				$email = $request->get_param("email");
			if($email){
				$user = get_user_by('email', $email);
				$userId = $user->ID;
				}
			}

			foreach($coupons as $coupon){
				    $coupon_object = new WC_Coupon($coupon);
					$discountChecker = new \AgrispesaDiscountChecker( WC()->cart );
					$valid = $discountChecker->is_coupon_valid($coupon_object,intval($userId));

					if(is_object($valid)){
						 $response = new WP_REST_Response([
             			   "error" => 'Non puoi utilizzare questo coupon.',
           				 ]);
          				  $response->set_status(500);
          			  return $response;

					}

			}

            WC()->session->set("applied_coupons", $coupons);

            $response = new WP_REST_Response([
                "coupon_code" => $coupons,
            ]);
            $response->set_status(200);
            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "export-orders", [
        "methods" => "GET",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $limit = $request->get_param("limit");
            $week = $request->get_param("week");

            $today = new \DateTime();
            $today->add(new \DateInterval("P7D"));
            $week = $today->format("W");
            $currentWeek = str_pad($week, 2, 0, STR_PAD_LEFT);
            $currentWeek = date("y") . $currentWeek;

            $doc = new DOMDocument();
            $doc->formatOutput = true;
            $root = $doc->createElement("ROOT");
            $root = $doc->appendChild($root);

            //$lastWeek = (new \DateTime())->setTime(18,0)->sub(new DateInterval("P7D"));
            //$lastWeek = getLastDeliveryDay();

            /*$dates = getAllDeliveryDates();
             $lastWeek = $dates[1];*/

            $lastOrderWeek = get_option("last_order_week", true);

            /*if(!empty($week)){
				$lastOrderWeek = $week;
			}*/

            /*
 *            "meta_key" => "_date_completed",
                "meta_compare" => ">",
                "meta_value" => $lastWeek->getTimestamp(),
 */

            $orders = wc_get_orders([
                "limit" => -1,
                "orderby" => "date",
                "order" => "ASC",
                //"meta_key" => "_date_completed",
                //"meta_compare" => ">",
                //"meta_value" => 1683195289,
                "meta_key" => "_week",
                "meta_compare" => "=",
                "meta_value" => $lastOrderWeek,
            ]);

            $items = 0;

            $productsScegliTu = [];

            $scegliTuBox = get_posts([
                "post_type" => "weekly-box",
                "post_status" => "publish",
                "fields" => "ids",
                "posts_per_page" => 1,
                "meta_key" => "_navision_id",
                "meta_value" => $currentWeek . "-STCOMP",
            ]);

            if (!empty($scegliTuBox)) {
                $productsScegliTu = get_post_meta(
                    $scegliTuBox[0],
                    "_products",
                    true
                );
            }

            $customersOrders = [];

            foreach ($orders as $order) {
                if ($order->get_status() != "completed") {
                    continue;
                }

                /*  $checkPaidDate = (new \DateTime())->setTime(18,00)->sub(
                    new DateInterval("P7D")
                );
                if ($order->get_date_paid() < $checkPaidDate) {
                    continue;
                } */

                if ($limit && $items > $limit) {
                    continue;
                }

                /* USER NAVISION ID */
                $userNavisionId = get_user_meta(
                    $order->get_customer_id(),
                    "navision_id",
                    true
                );
                if (!$userNavisionId) {
                    continue;
                }

                $boxCode = "STCOMP";

                $orderType = get_post_meta(
                    $order->get_id(),
                    "_order_type",
                    true
                );

                if (
                    $orderType != "ST" &&
                    $orderType != "fn" &&
                    $orderType != "ABBONAMENTO fn + ST"
                ) {
                    continue;
                }

                $isSubscription = get_post_meta(
                    $order->get_id(),
                    "_subscription_id",
                    true
                );

                $isAcquistoCredito = false;
                foreach ($order->get_items() as $item_id => $item) {
                    $product = $item->get_product();

                    if (!$product) {
                        continue;
                    }

                    if ($product->get_name() == "Acquisto credito") {
                        $isAcquistoCredito = true;
                    }
                }

                if ($isAcquistoCredito) {
                    continue;
                }

                if ($isSubscription) {
                    $subscription = new WC_Subscription($isSubscription);
                    $products = $subscription->get_items();

                    if (empty($products)) {
                        continue;
                    }

                    $box = reset($products)->get_product();
                    if (!$box) {
                        continue;
                    }

                    $tipologia = get_post_meta(
                        $box->get_id(),
                        "attribute_pa_tipologia",
                        true
                    );
                    $dimensione = get_post_meta(
                        $box->get_id(),
                        "attribute_pa_dimensione",
                        true
                    );
                    $productBox = get_single_box_from_attributes(
                        $tipologia,
                        $dimensione
                    );

                    if (!$productBox) {
                        continue;
                    }

                    $navisionIdBox = get_post_meta(
                        $productBox->get_id(),
                        "_navision_id",
                        true
                    );

                    if (empty($navisionIdBox)) {
                        continue;
                    }

                    $boxCode = $navisionIdBox[0];
                }

                $order->box_code = $boxCode;
                $order->is_subscription = $isSubscription;
                $order->order_type = $orderType;
                $order->user_navision_id = $userNavisionId;

                $recipient = $userNavisionId;

                $address = base64_encode(
                    strtolower($order->get_shipping_address_1())
                );

                if (!isset($customersOrders[$recipient . $address])) {
                    $customersOrders[$recipient . $address] = [];
                }

                $customersOrders[$recipient . $address][] = $order;
            }

            foreach ($customersOrders as $orders) {
                $navisionId = 6000000 + $orders[0]->get_id();

                $boxCode = "ST";
                $notes = [];

                foreach ($orders as $order) {
                    if ($order->box_code != "ST") {
                        $boxCode = $order->box_code;
                    }

                    $orderNote = $order->get_customer_note();
                    if (empty($orderNote)) {
                        $orderNote = get_user_meta(
                            $order->get_customer_id(),
                            "shipping_citofono",
                            true
                        );
                        if (!$orderNote) {
                            $orderNote = "";
                        }
                    }

                    $notes[] = $orderNote;
                }

                $notes = array_unique($notes);
                $notes = implode(" - ", $notes);

                $piani = [];
                $scale = [];
                foreach ($orders as $order) {
                    $piano = get_post_meta(
                        $order->get_id(),
                        "shipping_piano",
                        true
                    );
                    if (!$piano) {
                        $piano = "";
                    } else {
                        $piano = "Piano " . $piano;
                    }

                    $scala = get_post_meta(
                        $order->get_id(),
                        "shipping_scala",
                        true
                    );
                    if (!$scala) {
                        $scala = "";
                    } else {
                        $scala = "Scala " . $scala;
                    }

                    $piani[] = $piano;
                    $scale[] = $scala;
                }

                $piani = array_unique($piani);
                $scale = array_unique($scale);

                $piano = implode(" ", $piani);
                $scala = implode(" ", $scale);

                $piano = str_replace("Piano Piano", "Piano", $piano);

                foreach ($orders as $order) {
                    if (
                        strstr($order->order_type, "ST") !== false ||
                        $order->is_subscription
                    ) {
                        foreach ($order->get_items() as $item) {
                            $productId = null;
                            if ($item->get_variation_id()) {
                                $productId = $item->get_variation_id();
                            } else {
                                $productId = $item->get_product_id();
                            }

                            $productNavisionId = get_post_meta(
                                $productId,
                                "_navision_id",
                                true
                            );

                            /*   $productPrice = get_post_meta(
                                $productId,
                                "_regular_price",
                                true
                            );

                            if (!$productPrice) {
                               // continue;
                            }*/

                            if (
                                is_array($productNavisionId) &&
                                !empty($productNavisionId)
                            ) {
                                $productNavisionId = $productNavisionId[0];
                            }

                            if (!$productNavisionId) {
                                continue;
                            }

                            $offerLineNo = $item->get_meta("offer_line_no");

                            if (
                                !$offerLineNo &&
                                strstr($order->order_type, "ST") !== false
                            ) {
                                $product = wc_get_product($productId);

                                if (!$product) {
                                    continue;
                                }

                                //lo vado a prendere nella lista di prodotti dalla scegli tu

                                $foundProductInSt = array_filter(
                                    $productsScegliTu,
                                    function ($stProduct) use ($product) {
                                        return $stProduct["id"] ==
                                            $product->get_id();
                                    }
                                );

                                if (empty($foundProductInSt)) {
                                    $foundProductInSt = array_filter(
                                        $productsScegliTu,
                                        function ($stProduct) use ($product) {
                                            return $stProduct["name"] ==
                                                $product->get_name();
                                        }
                                    );
                                    if (empty($foundProductInSt)) {
                                        // provo a cercare il nome
                                        $explodedProductName = explode(
                                            " ",
                                            $product->get_name()
                                        );

                                        if (count($explodedProductName) > 0) {
                                            while (
                                                count($explodedProductName) > 0
                                            ) {
                                                array_pop($explodedProductName);
                                                if (empty($foundProductInSt)) {
                                                    $newProductName = implode(
                                                        " ",
                                                        $explodedProductName
                                                    );
                                                    $foundProductInSt = array_filter(
                                                        $productsScegliTu,
                                                        function (
                                                            $stProduct
                                                        ) use (
                                                            $newProductName
                                                        ) {
                                                            return $stProduct[
                                                                "name"
                                                            ] ==
                                                                $newProductName;
                                                        }
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }

                                if (empty($foundProductInSt)) {
                                    /* $response = new WP_REST_Response([
                                            "order_id" => $order->get_id(),
                                            "error" =>
                                                "Prodotto non trovato nella scegli tu: " .
                                                $product->get_name().' ID: '.$product->get_id(),
                                            "scegli_tu" => $productsScegliTu,
                                        ]);
                                        $response->set_status(500);
                                        return $response;*/
                                    continue;
                                }

                                $foundProductInSt = reset($foundProductInSt);
                                $offerLineNo =
                                    $foundProductInSt["offer_line_no"];
                            }

                            if (!$offerLineNo) {
                                $response = new WP_REST_Response([
                                    "order_id" => $order->get_id(),
                                    "error" =>
                                        "Offer Line No non trovato per: " .
                                        $product->get_name() .
                                        " SKU " .
                                        $product->get_sku() .
                                        " Ordine: " .
                                        $order->get_id(),
                                ]);
                                $response->set_status(500);
                                return $response;
                            }

                            addItemToOrder(
                                $doc,
                                $root,
                                $navisionId,
                                $order,
                                $piano,
                                $scala,
                                $notes,
                                $productNavisionId,
                                $item,
                                $currentWeek,
                                $offerLineNo,
                                $item->get_total(),
                                $orders[0]->get_date_paid(),
                                $boxCode,
                                $orders[0]->get_shipping_last_name() .
                                    " " .
                                    $orders[0]->get_shipping_first_name()
                            );
                        }

                        $shipping_method_total = 0;

                        foreach ($order->get_items("shipping") as $item) {
                            $shipping_method_total = $item->get_total();
                        }

                        if (
                            $shipping_method_total > 0 &&
                            !in_array($order->get_shipping_state(), [
                                "CN",
                                "AT",
                            ])
                        ) {
                            $productNavisionId = get_option(
                                "delivery_product_sku"
                            );

                            $offerLineNo = get_option(
                                "delivery_product_offer_no"
                            );

                            addItemToOrder(
                                $doc,
                                $root,
                                $navisionId,
                                $order,
                                $piano,
                                $scala,
                                $notes,
                                $productNavisionId,
                                null,
                                $currentWeek,
                                $offerLineNo,
                                5,
                                $orders[0]->get_date_paid(),
                                $boxCode,
                                $orders[0]->get_shipping_last_name() .
                                    " " .
                                    $orders[0]->get_shipping_first_name()
                            );
                        }
                    }

                    update_post_meta(
                        $order->get_id(),
                        "navision_last_export",
                        (new \DateTime())->format("Y-m-d H:i")
                    );

                    update_post_meta(
                        $order->get_id(),
                        "navision_id",
                        $navisionId
                    );
                }
            }
            header("Content-type: text/xml");
            die($doc->saveXml());
        },
    ]);
    register_rest_route("agrispesa/v1", "export-payments", [
        "methods" => "GET",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            //ORDER ID MINIMO
            //39319

            $args = [
                "status" => "wc-completed",
                "limit" => -1,
                "meta_key" => "_payment_method",
                "meta_value" => ["bacs", "wallet", ""],
                "meta_compare" => "NOT IN",
            ];

            $orders = wc_get_orders($args);
            $doc = new DOMDocument();
            $doc->formatOutput = true;
            $root = $doc->createElement("ROOT");
            $root = $doc->appendChild($root);

            foreach ($orders as $order) {
                if ($order->get_id() <= 39319) {
                    continue;
                }

                if (empty($order->get_payment_method())) {
                    continue;
                }

                $isExported = get_post_meta(
                    $order->get_id(),
                    "_payment_exported",
                    true
                );

                if ($isExported) {
                    continue;
                }

                //check if has navision id
                $navisionId = get_user_meta(
                    $order->get_customer_id(),
                    "navision_id",
                    true
                );

                if (!$navisionId) {
                    continue;
                }

                $row = $doc->createElement("ROW");
                $ele1 = $doc->createElement("id_payment");
                $ele1->nodeValue = 9000000 + $order->get_id();
                $row->appendChild($ele1);

                $ele1 = $doc->createElement("id_codeclient");
                $ele1->nodeValue = $navisionId;
                $row->appendChild($ele1);
                $ele1 = $doc->createElement("datein");
                $ele1->nodeValue = (new DateTime(
                    $order->get_date_paid()
                ))->format("dmY");
                $row->appendChild($ele1);
                $ele1 = $doc->createElement("paymentbatchname");
                $ele1->nodeValue = "CREDITCARD";
                $row->appendChild($ele1);
                $ele1 = $doc->createElement("paymentinprogress");
                $ele1->nodeValue = "0";
                $row->appendChild($ele1);
                $ele1 = $doc->createElement("amount");
                $ele1->nodeValue = str_replace(
                    ".",
                    ",",
                    number_format($order->get_total(), 4)
                );
                $row->appendChild($ele1);
                $root->appendChild($row);
                update_post_meta(
                    $order->get_id(),
                    "_payment_exported",
                    (new \DateTime())->format("Y-m-d H:i:s")
                );
            }

            header("Content-type: text/xml");
            die($doc->saveXml());
        },
    ]);
    register_rest_route(
        "agrispesa/v1",
        "products/(?P<product_id>\d+)/category",
        [
            "methods" => "GET",
            "permission_callback" => function () {
                return true;
            },
            "callback" => function ($request) {
                $terms = get_the_terms($request["product_id"], "product_cat");
                $terms = array_reverse($terms);
                $selectedTerm = null;
                foreach ($terms as $term) {
                    $ricarico = get_term_meta(
                        $term->term_id,
                        "ricarico_percentuale",
                        true
                    );
                    if (!empty($ricarico)) {
                        $selectedTerm = $term;
                        $selectedTerm->ricarico_percentuale = !empty($ricarico)
                            ? floatval($ricarico)
                            : 0;
                    }
                }
                $response = new WP_REST_Response($selectedTerm);
                $response->set_status(200);
                return $response;
            },
        ]
    );
    register_rest_route("agrispesa/v1", "weekly-box", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $body = $request->get_json_params();
            $post_id = wp_insert_post([
                "post_type" => "weekly-box",
                "post_title" =>
                    "Box settimana " .
                    $body["week"] .
                    " - " .
                    $body["product_box_id"],
                "post_content" => "",
                "post_status" => "publish",
                "comment_status" => "closed", // if you prefer
                "ping_status" => "closed", // if you prefer
            ]);
            if ($post_id) {
                // insert post meta
                add_post_meta($post_id, "_week", $body["week"]);
                add_post_meta(
                    $post_id,
                    "_data_consegna",
                    $body["data_consegna"]
                );
                add_post_meta(
                    $post_id,
                    "_product_box_id",
                    $body["product_box_id"]
                );
                add_post_meta($post_id, "_products", $body["products"]);
            }
            $response = new WP_REST_Response(["id" => $post_id]);
            $response->set_status(201);
            return $response;
        },
    ]);
    register_rest_route("agrispesa/v1", "weekly-box/duplicate", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $body = $request->get_json_params();
            $lastWeek = $body["week"] - 1;
            $lastWeekBox = get_posts([
                "post_type" => "weekly-box",
                "post_status" => "publish",
                "posts_per_page" => 1,
                "meta_query" => [
                    "relation" => "and",
                    [
                        "key" => "_week",
                        "value" => str_pad($lastWeek, 2, 0, STR_PAD_LEFT),
                        "compare" => "=",
                    ],
                    [
                        "key" => "_product_box_id",
                        "value" => $body["product_box_id"],
                        "compare" => "=",
                    ],
                ],
            ]);
            if (empty($lastWeekBox)) {
                $response = new WP_REST_Response([
                    "message" =>
                        "Nessuna Box Settimana trovata per la settimana " .
                        $lastWeek,
                ]);
                $response->set_status(404);
                return $response;
            }
            $lastWeekBox = reset($lastWeekBox);
            $post_id = wp_insert_post([
                "post_type" => "weekly-box",
                "post_title" =>
                    "Box settimana " .
                    $body["week"] .
                    " - " .
                    $body["product_box_id"],
                "post_content" => "",
                "post_status" => "publish",
                "comment_status" => "closed", // if you prefer
                "ping_status" => "closed", // if you prefer
            ]);
            if ($post_id) {
                // insert post meta
                add_post_meta($post_id, "_week", $body["week"]);
                add_post_meta(
                    $post_id,
                    "_data_consegna",
                    $body["data_consegna"]
                );
                add_post_meta(
                    $post_id,
                    "_product_box_id",
                    $body["product_box_id"]
                );
                $products = get_post_meta($lastWeekBox->ID, "_products", true);
                add_post_meta($post_id, "_products", $products);
            }
            $response = new WP_REST_Response(["id" => $post_id]);
            $response->set_status(201);
            return $response;
        },
    ]);
    register_rest_route(
        "agrispesa/v1",
        "weekly-box/(?P<box_id>\d+)/products/(?P<index>\d+)",
        [
            "methods" => "DELETE",
            "permission_callback" => function () {
                return true;
            },
            "callback" => function ($request) {
                $products = get_post_meta(
                    $request["box_id"],
                    "_products",
                    true
                );
                unset($products[$request["index"]]);
                update_post_meta($request["box_id"], "_products", $products);
                $response = new WP_REST_Response([]);
                $response->set_status(204);
                return $response;
            },
        ]
    );
    register_rest_route("agrispesa/v1", "weekly-box/(?P<box_id>\d+)/products", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $products = get_post_meta($request["box_id"], "_products", true);
            foreach ($request["product_ids"] as $key => $id) {
                $product = wc_get_product($id);
                $unitaMisura = "gr";
                $measureUnit = get_post_meta($id, "_woo_uom_input", true);
                $price = get_post_meta($id, "_price", true);
                if (!empty($measureUnit)) {
                    $unitaMisura = $measureUnit;
                }
                $products[] = [
                    "id" => $id,
                    "name" => $product->get_name(),
                    "quantity" => $request["quantity"][$key],
                    "price" => $product->get_price(),
                    "unit_measure" => $unitaMisura,
                    "unit_measure_print" => get_post_meta(
                        $id,
                        "_uom_acquisto",
                        true
                    ),
                ];
            }
            $products = array_map(
                "unserialize",
                array_unique(array_map("serialize", $products))
            );
            $newProducts = [];
            foreach ($products as $product) {
                $newProducts[] = $product;
            }
            update_post_meta($request["box_id"], "_products", $newProducts);
            $response = new WP_REST_Response([]);
            $response->set_status(204);
            return $response;
        },
    ]);
    register_rest_route("agrispesa/v1", "shop-categories", [
        "methods" => "GET",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $gruppiProdotti = get_posts([
                "post_type" => "gruppo-prodotto",
                "post_status" => "publish",
                "posts_per_page" => -1,
            ]);

            $categorie = [];
            foreach ($gruppiProdotti as $gruppoProdotto) {
                $categoria = get_post_meta(
                    $gruppoProdotto->ID,
                    "categoria_principale_gruppo_prodotto",
                    true
                );
                $code = get_post_meta(
                    $gruppoProdotto->ID,
                    "codice_gruppo_prodotto",
                    true
                );

                if (!isset($categorie[$categoria])) {
                    $categorie[$categoria] = [
                        "name" => $categoria,
                        "products" => [],
                    ];
                }
                $categorie[$categoria]["products"][] = [
                    "post_title" => $gruppoProdotto->post_title,
                    "code" => $code,
                ];
            }

            $categorie = array_values($categorie);

            $response = new WP_REST_Response($categorie);
            $response->set_status(200);
            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "add-user-blocked-weeks", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $loggedUser = $_POST["userId"];

            $events = $_POST["events"];

            $subscription = wcs_get_subscriptions([
                "subscriptions_per_page" => 1,
                "orderby" => "ID",
                "order" => "DESC",
                "subscription_status" => ["active", "on-hold"],
                "customer_id" => $loggedUser,
            ]);
            $subscription = reset($subscription);

            foreach ($events as $event) {
                $day = new \DateTime($event["start"]);

                $disabledWeeks = get_post_meta(
                    $subscription->get_id(),
                    "disable_weeks_" . $day->format("Y"),
                    true
                );

                if (!$disabledWeeks || !is_array($disabledWeeks)) {
                    $disabledWeeks = [];
                }

                $disabledWeeks[] = $event["week"];

                $disabledWeeks = array_unique($disabledWeeks);

                update_post_meta(
                    $subscription->get_id(),
                    "disable_weeks_" . $day->format("Y"),
                    $disabledWeeks
                );
            }

            $response = new WP_REST_Response($disabledWeeks);
            $response->set_status(201);
            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "delete-user-blocked-weeks", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $loggedUser = $_POST["userId"];

            $day = $_POST["day"];
            $day = new DateTime($day);
            $week = $_POST["week"];

            $subscription = wcs_get_subscriptions([
                "subscriptions_per_page" => 1,
                "orderby" => "ID",
                "order" => "DESC",
                "subscription_status" => ["active", "on-hold"],
                "customer_id" => $loggedUser,
            ]);
            $subscription = reset($subscription);

            $disabledWeeks = get_post_meta(
                $subscription->get_id(),
                "disable_weeks_" . $day->format("Y"),
                true
            );
            if (!$disabledWeeks || !is_array($disabledWeeks)) {
                $disabledWeeks = [];
            }

            if (in_array($week, $disabledWeeks)) {
                $key = array_search($week, $disabledWeeks);
                if ($key !== false) {
                    unset($disabledWeeks[$key]);
                }
            }

            sort($disabledWeeks);
            array_unique($disabledWeeks);
            update_post_meta(
                $subscription->get_id(),
                "disable_weeks_" . $day->format("Y"),
                $disabledWeeks
            );

            $response = new WP_REST_Response($disabledWeeks);
            $response->set_status(201);
            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "user-blocked-weeks", [
        "methods" => "GET",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $loggedUser = $_GET["userId"];

            $startDate = $_GET["start"];
            $startDate = new DateTime($startDate);

            $endDate = $_GET["end"];
            $endDate = new DateTime($endDate);

            $subscription = wcs_get_subscriptions([
                "subscriptions_per_page" => 1,
                "orderby" => "ID",
                "order" => "DESC",
                "subscription_status" => ["active", "on-hold"],
                "customer_id" => $loggedUser,
            ]);
            $subscription = reset($subscription);

            require_once get_template_directory() .
                "/libraries/carbon/autoload.php";

            $fromDate = new Carbon\Carbon($startDate);

            $fromDate =
                $fromDate->dayOfWeek == Carbon\Carbon::THURSDAY
                    ? $fromDate
                    : $fromDate->copy()->modify("next Thursday");

            $toDate = new Carbon\Carbon($endDate);
            $dates = [];

            $events = [];

            $disabledWeeks = get_post_meta(
                $subscription->get_id(),
                "disable_weeks_" . $startDate->format("Y"),
                true
            );
            if (is_array($disabledWeeks)) {
                for ($date = $fromDate; $date->lte($toDate); $date->addWeek()) {
                    if (in_array($date->format("W"), $disabledWeeks)) {
                        $timestamp =
                            mktime(0, 0, 0, 1, 1, date("Y")) +
                            $date->format("W") * 7 * 24 * 60 * 60;
                        $timestamp_for_monday =
                            $timestamp - 86400 * (date("N", $timestamp) - 1);

                        $monday = new DateTime();
                        $monday->setTimestamp($timestamp_for_monday);

                        $sunday = clone $monday;
                        $sunday->modify("next sunday");

                        $events[] = [
                            "start" => $monday->format("Y-m-d 00:00:00"),
                            "end" => $sunday->format("Y-m-d 23:59:59"),
                            "title" =>
                                "Questa settimana non ricevi la Facciamo Noi",
                            "week" => $sunday->format("W"),
                        ];
                    }
                }
            }

            $response = new WP_REST_Response($events);
            $response->set_status(200);
            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "user-subscriptions", [
        "methods" => "GET",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $loggedUser = $_GET["userId"];
            $subscriptions = wcs_get_subscriptions([
                "subscriptions_per_page" => -1,
                "customer_id" => $loggedUser,
                "subscription_status" => "active",
            ]);
            $json = [];
            foreach ($subscriptions as $subscription) {
                $products = $subscription->get_items();
                $productsToAdd = get_products_to_add_from_subscription(
                    $subscription
                );
                $boxPreferences = get_post_meta(
                    $subscription->get_id(),
                    "_box_preferences",
                    true
                );
                if (empty($boxPreferences)) {
                    $boxPreferences = [];
                }
                $boxBlacklist = get_post_meta(
                    $subscription->get_id(),
                    "_box_blacklist",
                    true
                );
                if (empty($boxBlacklist)) {
                    $boxBlacklist = [];
                }
                $json[] = [
                    "name" => reset($products)->get_name(),
                    "id" => $subscription->get_id(),
                    "box_preferences" => $boxPreferences,
                    "box_blacklist" => $boxBlacklist,
                    "products" => $productsToAdd,
                ];
            }
            $response = new WP_REST_Response($json);
            $response->set_status(200);
            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "export-subscription", [
        "methods" => "GET",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $subscriptions = wcs_get_subscriptions([
                "subscriptions_per_page" => -1,
            ]);

            $f = fopen("php://memory", "w");

            $csv = [
                "ID",
                "Stato",
                "ID NAVISION UTENTE",
                "PRODOTTO",
                "NOME SPEDIZIONE",
                "COGNOME SPEDIZIONE",
                "NOME FATTURAZIONE",
                "COGNOME FATTURAZIONE",
            ];
            fputcsv($f, $csv, ";");
            foreach ($subscriptions as $subscription) {
                $navisionId = get_user_meta(
                    $subscription->get_customer_id(),
                    "navision_id",
                    true
                );

                $product = [];
                foreach ($subscription->get_items() as $item) {
                    $product[] = $item->get_name();
                }

                $csv[] = [
                    $subscription->get_id(),
                    $subscription->get_status(),
                    $navisionId,
                    implode(", ", $product),
                    $subscription->get_shipping_first_name(),
                    $subscription->get_shipping_last_name(),
                    $subscription->get_billing_first_name(),
                    $subscription->get_billing_last_name(),
                ];
            }
            foreach ($csv as $line) {
                fputcsv($f, $line, ";");
            }
            fseek($f, 0);
            header("Content-Type: text/csv");
            header(
                'Content-Disposition: attachment; filename="Abbonamenti.csv";'
            );
            fpassthru($f);
            die();
        },
    ]);

    register_rest_route("agrispesa/v1", "delivery-group-csv", [
        "methods" => "GET",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $dataConsegna = $_GET["data_consegna"];
            $caps = get_post_meta($_GET["delivery_group"], "cap", true);
            $args = [
                "posts_per_page" => -1,
                "post_type" => "shop_order",
                "post_status" => ["wc-processing", "wc-completed"],
                "meta_query" => [
                    "relation" => "AND",
                    [
                        "key" => "_data_consegna",
                        "value" => $dataConsegna,
                        "compare" => "=",
                    ],
                    [
                        "key" => "_shipping_postcode",
                        "value" => $caps,
                        "compare" => "IN",
                    ],
                ],
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
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    $order->get_shipping_first_name() .
                    " " .
                    $order->get_shipping_last_name(),
                ];
            }
            $f = fopen("php://memory", "w");
            foreach ($csv as $line) {
                fputcsv($f, $line);
            }
            fseek($f, 0);
            header("Content-Type: text/csv");
            header(
                'Content-Disposition: attachment; filename="PIEM ' .
                    (new \DateTime($dataConsegna))->format("d-m-Y") .
                    ' da nav a map & guide . csv";'
            );
            fpassthru($f);
            die();
        },
    ]);
    register_rest_route("agrispesa/v1", "subscription-preference", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $body = $request->get_json_params();
            $boxPreferences = get_post_meta(
                $body["subscription_id"],
                "_box_preferences",
                true
            );
            if (empty($boxPreferences)) {
                $boxPreferences = [];
            }
            foreach ($body["product_ids"] as $productId) {
                $productToAdd = get_posts([
                    "post_type" => "gruppo-prodotto",
                    "post_status" => "publish",
                    "posts_per_page" => 1,
                    "meta_key" => "codice_gruppo_prodotto",
                    "meta_value" => $productId,
                ]);
                $productToAdd = $productToAdd[0];

                $boxPreferences[] = [
                    "id" => $productToAdd->ID,
                    "name" => $productToAdd->post_title,
                    "code" => $productId,
                ];
            }
            $boxPreferences = array_map(
                "unserialize",
                array_unique(array_map("serialize", $boxPreferences))
            );
            $newBoxPreferences = [];
            foreach ($boxPreferences as $boxPreference) {
                $newBoxPreferences[] = $boxPreference;
            }
            update_post_meta(
                $body["subscription_id"],
                "_box_preferences",
                $newBoxPreferences
            );
            $response = new WP_REST_Response([]);
            $response->set_status(201);
            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "subscription-blacklist", [
        "methods" => "POST",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $body = $request->get_json_params();
            $boxPreferences = get_post_meta(
                $body["subscription_id"],
                "_box_blacklist",
                true
            );
            if (empty($boxPreferences)) {
                $boxPreferences = [];
            }
            foreach ($body["product_ids"] as $productId) {
                $productToAdd = get_posts([
                    "post_type" => "gruppo-prodotto",
                    "post_status" => "publish",
                    "posts_per_page" => 1,
                    "meta_key" => "codice_gruppo_prodotto",
                    "meta_value" => $productId,
                ]);
                $productToAdd = $productToAdd[0];

                $boxPreferences[] = [
                    "id" => $productToAdd->ID,
                    "name" => $productToAdd->post_title,
                    "code" => $productId,
                ];
            }
            $boxPreferences = array_map(
                "unserialize",
                array_unique(array_map("serialize", $boxPreferences))
            );
            $newBoxPreferences = [];
            foreach ($boxPreferences as $boxPreference) {
                $newBoxPreferences[] = $boxPreference;
            }
            update_post_meta(
                $body["subscription_id"],
                "_box_blacklist",
                $newBoxPreferences
            );
            $response = new WP_REST_Response([]);
            $response->set_status(201);
            return $response;
        },
    ]);

    register_rest_route("agrispesa/v1", "subscription-preference", [
        "methods" => "DELETE",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $body = $request->get_json_params();
            $boxPreferences = get_post_meta(
                $body["subscription_id"],
                "_box_preferences",
                true
            );
            $productIds = $body["product_ids"];
            $newBoxPreferences = [];
            foreach ($productIds as $productId) {
                //find product
                $index = array_filter($boxPreferences, function ($product) use (
                    $productId
                ) {
                    return $product["id"] == $productId;
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
            update_post_meta(
                $body["subscription_id"],
                "_box_preferences",
                $newBoxPreferences
            );
            $response = new WP_REST_Response([]);
            $response->set_status(204);
            return $response;
        },
    ]);
    register_rest_route("agrispesa/v1", "subscription-blacklist", [
        "methods" => "DELETE",
        "permission_callback" => function () {
            return true;
        },
        "callback" => function ($request) {
            $body = $request->get_json_params();
            $boxPreferences = get_post_meta(
                $body["subscription_id"],
                "_box_blacklist",
                true
            );
            $productIds = $body["product_ids"];
            $newBoxPreferences = [];
            foreach ($productIds as $productId) {
                //find product
                $index = array_filter($boxPreferences, function ($product) use (
                    $productId
                ) {
                    return $product["id"] == $productId;
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
            update_post_meta(
                $body["subscription_id"],
                "_box_blacklist",
                $newBoxPreferences
            );
            $response = new WP_REST_Response([]);
            $response->set_status(204);
            return $response;
        },
    ]);
});
function my_enqueue($hook)
{
    if ($hook == "edit.php" || $hook == "post.php") {
        wp_enqueue_script(
            "agrispesa-admin-delivery-box-js",
            get_theme_file_uri("assets/js/admin-delivery-box.js"),
            ["jquery", "select2"],
            null,
            true
        );
        wp_localize_script("agrispesa-admin-delivery-box-js", "WPURL", [
            "siteurl" => get_option("siteurl"),
            "userId" => get_current_user_id(),
        ]);
    } else {
        if ("toplevel_page_esporta-documenti" == $hook) {
            wp_enqueue_script(
                "agrispesa-export-js",
                get_theme_file_uri("assets/js/export.js"),
                ["jquery"],
                null,
                true
            );
            return;
        }
        if (
            "toplevel_page_box-settimanali" !== $hook &&
            "woocommerce_page_genera-ordini-box" !== $hook
        ) {
            return;
        }
        wp_register_style(
            "select2css",
            "//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css",
            false,
            "1.0",
            "all"
        );
        wp_register_script(
            "select2",
            "//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js",
            ["jquery"],
            "1.0",
            true
        );
        wp_enqueue_style("select2css");
        wp_enqueue_script("select2");
        wp_enqueue_script(
            "moment",
            get_template_directory_uri() . "/assets/js/moment.min.js",
            ["jquery"],
            null,
            true
        );
        wp_register_script(
            "axios",
            "//cdnjs.cloudflare.com/ajax/libs/axios/1.2.2/axios.min.js",
            [],
            null,
            true
        );
        wp_enqueue_script("axios");
        wp_register_script(
            "vuejs",
            "//unpkg.com/vue@3/dist/vue.global.js",
            [],
            null,
            true
        );
        wp_enqueue_script("vuejs");
        wp_register_style(
            "datatable",
            "//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css",
            false,
            "1.0",
            "all"
        );
        wp_enqueue_style("datatable");
        wp_register_script(
            "datatable-js",
            "//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js",
            [],
            null,
            true
        );
        wp_enqueue_script("datatable-js");
        wp_enqueue_style(
            "agrispesa-admin-css",
            get_theme_file_uri("assets/css/admin.css"),
            false,
            "1.0",
            "all"
        );
        /* wp_enqueue_script(
            "agrispesa-admin-js",
            get_theme_file_uri("assets/js/admin.js"),
            ["jquery", "select2"],
            null,
            true
        );*/

        wp_localize_script("agrispesa-admin-js", "WPURL", [
            "siteurl" => get_option("siteurl"),
            "userId" => get_current_user_id(),
        ]);
    }
}
add_action("admin_enqueue_scripts", "my_enqueue");
// Adding Meta container admin shop_order pages
add_action("add_meta_boxes", "mv_add_meta_boxes");
if (!function_exists("mv_add_meta_boxes")) {
    function mv_add_meta_boxes()
    {
        add_meta_box(
            "old_preferences",
            "Preferenze Utente Vecchio sito",
            "old_preferences_meta_box_callback",
            "shop_subscription",
            "normal",
            "default"
        );
        add_meta_box(
            "old_preferences_order",
            "Preferenze Utente Vecchio sito",
            "old_preferences_order_meta_box_callback",
            "shop_order",
            "advanced",
            "core"
        );
        add_meta_box(
            "box_preferences",
            "Preferenze Facciamo noi",
            "box_preferences_meta_box_callback",
            "shop_order",
            "advanced",
            "core",
            []
        );
        add_meta_box(
            "mv_other_fields",
            "INFORMAZIONI CONSEGNA",
            "mv_add_other_fields_for_packaging",
            "shop_order",
            "side",
            "core"
        );
    }
    function old_preferences_order_meta_box_callback($order)
    {
        $order = new WC_Order($order->ID);
        $oldPreferences = get_user_meta(
            $order->get_customer_id(),
            "old_box_preferences",
            true
        );
        if (!empty($oldPreferences)): ?>

			<h4>Preferenze inserite nel vecchio sito Agrispesa</h4>
			<table class="table">
			<?php foreach ($oldPreferences as $preference): ?>
			<tr>
			<td>NO <?php echo $preference["name"]; ?></td>
			<td>Sostituire con <?php echo $preference["substitute"]; ?></td>
	</tr>
			<?php endforeach; ?>
	</table>
			<?php endif;
    }
    function old_preferences_meta_box_callback($post)
    {
        $subscription = new WC_Subscription($post->ID);
        $oldPreferences = get_user_meta(
            $subscription->get_customer_id(),
            "old_box_preferences",
            true
        );
        if (!empty($oldPreferences)): ?>

			<h4>Preferenze inserite nel vecchio sito Agrispesa</h4>
			<table class="table">
			<?php foreach ($oldPreferences as $preference): ?>
			<tr>
			<td>NO <?php echo $preference["name"]; ?></td>
			<td>Sostituire con <?php echo $preference["substitute"]; ?></td>
	</tr>
			<?php endforeach; ?>
	</table>
			<?php endif;
    }
    function box_preferences_meta_box_callback($order)
    {
        global $post;
        $subscriptionId = get_post_meta($post->ID, "_subscription_id", true);
        $boxPreferences = get_post_meta(
            $subscriptionId,
            "_box_preferences",
            true
        );
        $boxBlacklist = get_post_meta($subscriptionId, "_box_blacklist", true);
        if (!empty($boxPreferences)): ?>

			<h4>Cosa mi piacerebbe ricevere</h4>
			<table class="table">
			<?php foreach ($boxPreferences as $preference): ?>
			<tr>
			<td><?php echo $preference["name"]; ?></td>
			<td><a href="/wp-admin/post.php?post=<?php echo $preference[
       "id"
   ]; ?>&action=edit" target="_blank">Vai al prodotto</a></td>
	</tr>
			<?php endforeach; ?>
	</table>
			<?php endif;
        if (!empty($boxBlacklist)): ?>

			<h4>Cosa non voglio ricevere</h4>
			<table class="table">
			<?php foreach ($boxBlacklist as $preference): ?>
			<?php $category = get_post_meta(
       $preference["id"],
       "categoria_principale_gruppo_prodotto",
       true
   ); ?>
			<tr>
			<td><?php echo $category . " > " . $preference["name"]; ?></td>
			</tr>
	</tr>
			<?php endforeach; ?>
	</table>
	<?php endif;
    }
}
// if you don't add 3 as as 4th argument, this will not work as expected
add_action("save_post", "my_save_post_function", 10, 3);
function my_save_post_function($post_ID, $post, $update)
{
    if ($post->post_type == "shop_order") {
        if (isset($_POST["_numero_consegna"])) {
            update_post_meta(
                $post->ID,
                "_numero_consegna",
                $_POST["_numero_consegna"]
            );
        }
        if (isset($_POST["_data_consegna"])) {
            update_post_meta(
                $post->ID,
                "_data_consegna",
                $_POST["_data_consegna"]
            );
        }
    }
}
// Adding Meta field in the meta container admin shop_order pages
if (!function_exists("mv_add_other_fields_for_packaging")) {
    function mv_add_other_fields_for_packaging()
    {
        global $post;
        $weight = get_post_meta($post->ID, "_total_box_weight", true);
        $week = get_post_meta($post->ID, "_week", true);
        $numConsegna = get_post_meta($post->ID, "_numero_consegna", true);
        $consegna = get_post_meta($post->ID, "_data_consegna", true);
        $gruppoConsegna = get_post_meta($post->ID, "_gruppo_consegna", true);
        $deliveryDay = get_order_delivery_date($post->ID);
        if (empty($weight)) {
            $weight = 0;
        }
        //echo "<span>Peso della scatola: <strong>" . $weight . " kg</strong></span><br>";
        echo "<span>Settimana: <strong>" . $week . "</strong></span><br><br>";
        echo "<span>Gruppo di consegna: <strong>" .
            $gruppoConsegna .
            "</strong></span><br><br>";
        echo "<span>Data di ricezione: <strong>" .
            $deliveryDay .
            "</strong></span><br><br>";
        /*  echo '<strong>Numero di consegna:</strong><br>
			<input autocomplete="off" type="text" value="' . $numConsegna . '" name="_numero_consegna"><br><br>';

			global $wpdb;
			$allDataConsegna = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_data_consegna' group by meta_value", ARRAY_A);
	*/
    }
}
function get_products_to_add_from_subscription(
    $subscription,
    $week = null,
    $overrideProducts = false
) {
    $box = get_box_from_subscription($subscription);
    if (!$box) {
        return [];
    }

    $productsToAdd = get_post_meta($box->ID, "_products", true);
    /*if ($overrideProducts) {
			//check preferences
			$boxPreferences = get_post_meta($subscription->get_id(), "_box_preferences", true);
			if (empty($boxPreferences)) {
				$boxPreferences = [];
			}
			$boxPreferences = [];
			foreach ($boxPreferences as $preference) {
				$productSearched = array_filter($productsToAdd, function ($product) use ($preference) {
					return $product["id"] == $preference["id"];
				});
				if (!empty($productSearched)) {
					$keys = array_keys($productSearched);
					$productSearched = reset($productSearched);
					$productSearchedKey = reset($keys);
					$quantity = $productsToAdd[$productSearchedKey]["quantity"];
					unset($productsToAdd[$productSearchedKey]);
					$categories = get_the_terms($productSearched["id"], "product_cat");
					$category = reset($categories);
					$prod_categories = [$category->term_id];
					$product_args = ["numberposts" => - 1, "post_status" => ["publish"], "post_type" => ["product"], "suppress_filters" => false, "order" => "ASC", "offset" => 0, ];
					$product_args["tax_query"] = [["taxonomy" => "product_cat", "field" => "id", "terms" => $prod_categories, "operator" => "IN", ], ];
					$productsByCategory = get_posts($product_args);
					$productToAdd = reset($productsByCategory);
					$productsToAdd[] = ["id" => $productToAdd->ID, "name" => $productToAdd->post_title, "quantity" => $quantity, ];
				}
			}
		}*/
    return $productsToAdd;
}
function get_box_from_subscription($subscription, $week = null)
{
    if (!$week) {
        $date = new DateTime();
        $date->modify("+1 week");
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
    $tipologia = get_post_meta($box->get_id(), "attribute_pa_tipologia", true);
    $dimensione = get_post_meta(
        $box->get_id(),
        "attribute_pa_dimensione",
        true
    );
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
        "post_type" => "weekly-box",
        "post_status" => "publish",
        "posts_per_page" => 1,
        "meta_query" => [
            "relation" => "and",
            [
                "key" => "_week",
                "value" => date("Y") . "_" . $week,
                "compare" => "=",
            ],
            ["key" => "_product_box_id", "value" => $id, "compare" => "="],
        ],
    ]);
    if (empty($box)) {
        return null;
    }
    return reset($box);
}
function send_email_produttori($week)
{
    $groupedFabbisogno = get_fabbisogno($week);
    foreach ($groupedFabbisogno as $fornitore => $fabbisogno) {
    }
}
function generate_fabbisogno()
{
    $date = new DateTime();
    //$date->modify('+1 week');
    $week = $date->format("W");
    //dd($week);
    //get all pending orders
    $args = [
        "posts_per_page" => -1,
        "post_type" => "shop_order",
        "post_status" => ["wc-processing", "wc-on-hold"],
        "meta_query" => [
            "relation" => "and",
            ["key" => "_week", "value" => $week, "compare" => "="],
        ],
    ];
    $orders = new WP_Query($args);
    $orders = $orders->get_posts();
    $fabbisogni = [];
    foreach ($orders as $order) {
        $order = wc_get_order($order->ID);
        $gruppoConsegna = get_post_meta($order->ID, "_gruppo_consegna", true);
        foreach ($order->get_items() as $item) {
            $quantity = $item->get_quantity();
            $product = $item->get_product();
            if (!$product->is_type("simple")) {
                continue;
            }
            if (!isset($fabbisogni[$product->get_id()])) {
                $weight = get_post_meta($product->get_id(), "_weight", true);
                $measureUnit = get_post_meta(
                    $product->get_id(),
                    "_woo_uom_input",
                    true
                );
                if (!$measureUnit) {
                    $measureUnit = "gr";
                }
                $measureAcquisto = get_post_meta(
                    $product->get_id(),
                    "_uom_acquisto",
                    true
                );
                if (empty($measureAcquisto)) {
                    $measureAcquisto = "pz";
                }
                $fornitore = get_post_meta(
                    $product->get_id(),
                    "product_producer",
                    true
                );
                $fornitoreString = "";
                if (!empty($fornitore)) {
                    $fornitore = reset($fornitore);
                    $fornitore = get_post($fornitore);
                    $fornitoreString = $fornitore->post_title;
                }
                $tmpFabbisogno = [
                    "fabbisogno" => $quantity,
                    "weight" => $weight,
                    "product_name" => $product->get_name(),
                    "quantity_type" => $measureAcquisto,
                    "weight_type" => $measureUnit,
                    "sku" => $product->get_sku(),
                    "produttore" => $fornitoreString,
                    "gruppo_consegna" => $gruppoConsegna,
                ];
                $fabbisogni[
                    $product->get_id() . "_" . $gruppoConsegna
                ] = $tmpFabbisogno;
            } else {
                $fabbisogni[$product->get_id() . "_" . $gruppoConsegna][
                    "fabbisogno"
                ] += $quantity;
            }
        }
    }
    global $wpdb;
    $wpdb->query(
        "DELETE p, pm
	  FROM {$wpdb->prefix}posts p
	 INNER
	  JOIN {$wpdb->prefix}postmeta pm
		ON pm.post_id = p.ID
	 WHERE p.post_type = 'fabbisogno' AND
		   pm.meta_key = 'settimana'
	   AND pm.meta_value = '" .
            $week .
            "';"
    );
    $wpdb->query("
		DELETE pm
	FROM {$wpdb->prefix}postmeta pm
	LEFT JOIN {$wpdb->prefix}posts wp ON wp.ID = pm.post_id
	WHERE wp.ID IS NULL
		");
    foreach ($fabbisogni as $key => $product) {
        $key = explode("_", $key);
        $productId = $key[0];
        $gruppoConsegna = $key[1];
        $post_id = wp_insert_post([
            "post_type" => "fabbisogno",
            "post_title" => $product["product_name"] . " - " . $gruppoConsegna,
            "post_content" => "",
            "post_status" => "publish",
            "comment_status" => "closed", // if you prefer
            "ping_status" => "closed", // if you prefer
        ]);
        add_post_meta($post_id, "settimana", $week);
        add_post_meta($post_id, "prodotto", [$productId]);
        foreach ($product as $key => $value) {
            add_post_meta($post_id, $key, $value);
        }
    }
}

add_filter(
    "woo_wallet_current_balance",
    function ($wallet_balance, $user_id) {
        $customWallet = get_user_meta(
            $user_id,
            "_current_woo_wallet_balance",
            true
        );
        if ($customWallet) {
            $wallet_balance = $customWallet;
        }

        return $wallet_balance;
    },
    10,
    2
);

add_filter(
    "woocommerce_email_enabled_new_order",
    "dcwd_conditionally_send_wc_email",
    10,
    2
);
add_filter(
    "woocommerce_email_enabled_cancelled_order",
    "dcwd_conditionally_send_wc_email",
    10,
    2
);
add_filter(
    "woocommerce_email_enabled_customer_completed_order",
    "dcwd_conditionally_send_wc_email",
    10,
    2
);
add_filter(
    "woocommerce_email_enabled_customer_invoice",
    "dcwd_conditionally_send_wc_email",
    10,
    2
);
add_filter(
    "woocommerce_email_enabled_customer_note",
    "dcwd_conditionally_send_wc_email",
    10,
    2
);
add_filter(
    "woocommerce_email_enabled_customer_on_hold_order",
    "dcwd_conditionally_send_wc_email",
    10,
    2
);
add_filter(
    "woocommerce_email_enabled_customer_processing_order",
    "dcwd_conditionally_send_wc_email",
    10,
    2
);
add_filter(
    "woocommerce_email_enabled_customer_refunded_order",
    "dcwd_conditionally_send_wc_email",
    10,
    2
);
add_filter(
    "woocommerce_email_enabled_failed_order",
    "dcwd_conditionally_send_wc_email",
    10,
    2
);

function dcwd_conditionally_send_wc_email($whether_enabled, $object)
{
    if (null == $object) {
        return $whether_enabled;
    }

    //disable if is subscription order
    $isSubscriptionOrder = get_post_meta(
        $object->get_id(),
        "_disable_order_emails",
        true
    );
    if ($isSubscriptionOrder) {
        return false;
    }

    return $whether_enabled;
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

    //check products status
    $week = get_post_meta($box->ID, "_week", true);

    $productsToAdd = get_products_to_add_from_subscription(
        $subscription,
        $week,
        true
    );

    $productsToAddWoocommerce = [];

    global $wpdb;
    foreach ($productsToAdd as $productToAdd) {
        if (isset($productToAdd["navision_id"])) {
            $productId = $wpdb->get_results(
                "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_navision_id' AND meta_value='" .
                    $productToAdd["navision_id"] .
                    "' order by post_id DESC LIMIT 1",
                ARRAY_A
            );
            if (!empty($productId)) {
                $productId = $productId[0]["post_id"];
            }
        } else {
            $productId = $productToAdd["id"];
        }

        $productObj = wc_get_product($productId);

        if (!$productObj) {
            error_log(
                "Prodotto " .
                    $productsToAdd["name"] .
                    " con ID #" .
                    $productsToAdd["id"] .
                    " non trovato!"
            );
            continue;
        }

        $productsToAddWoocommerce[] = [
            "product" => $productObj,
            "quantity" => $productToAdd["quantity"],
            "offer_line_no" => $productToAdd["offer_line_no"],
        ];
    }

    $consegna = get_post_meta($box->ID, "_data_consegna", true);
    $boxNavisionId = get_post_meta($box->ID, "_navision_id", true);
    $customerId = $subscription->get_user_id();
    $order = wc_create_order();
    $order->set_customer_id($customerId);
    update_post_meta($order->get_id(), "_disable_order_emails", "1");

    $productsToAdd = get_products_to_add_from_subscription(
        $subscription,
        $week,
        true
    );

    foreach ($productsToAddWoocommerce as $productToAdd) {
        $itemId = $order->add_product(
            $productToAdd["product"],
            $productToAdd["quantity"]
        );
        wc_add_order_item_meta(
            $itemId,
            "offer_line_no",
            $productToAdd["offer_line_no"]
        );
    }

    // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
    $order->set_address(
        [
            "first_name" => $subscription->get_billing_first_name(),
            "last_name" => $subscription->get_billing_last_name(),
            "company" => $subscription->get_billing_company(),
            "email" => $subscription->get_billing_email(),
            "phone" => $subscription->get_billing_phone(),
            "address_1" => $subscription->get_billing_address_1(),
            "address_2" => $subscription->get_billing_address_2(),
            "city" => $subscription->get_billing_city(),
            "state" => $subscription->get_billing_state(),
            "postcode" => $subscription->get_billing_postcode(),
            "country" => $subscription->get_billing_country(),
        ],
        "billing"
    );
    $order->set_address(
        [
            "first_name" => $subscription->get_shipping_first_name(),
            "last_name" => $subscription->get_shipping_last_name(),
            "company" => $subscription->get_shipping_company(),
            "email" => $subscription->get_billing_email(),
            "phone" => $subscription->get_shipping_phone(),
            "address_1" => $subscription->get_shipping_address_1(),
            "address_2" => $subscription->get_shipping_address_2(),
            "city" => $subscription->get_shipping_city(),
            "state" => $subscription->get_shipping_state(),
            "postcode" => $subscription->get_shipping_postcode(),
            "country" => $subscription->get_shipping_country(),
        ],
        "shipping"
    );
    /*$items = $subscription->get_items();
		foreach ($items as $item) {

		$order->add_product(, 1);
		}

		foreach ($order->get_items() as $item) {
		$item->set_name($item->get_name() . ' - Settimana ' . $week);
		$item->save();
		}*/
    $args = [
        "customer_id" => $subscription->get_customer_id(),
        "status" => ["wc-completed"],
    ];
    $orders = wc_get_orders($args);

    // Set the array for tax calculations
    $calculate_tax_for = [
        "country" => "",
        "state" => $subscription->get_shipping_state(), // Can be set (optional)
        "postcode" => "", // Can be set (optional)
        "city" => "", // Can be set (optional)
    ];

    $item = new WC_Order_Item_Shipping();

    if (
        !in_array($subscription->get_shipping_state(), ["CN", "AT"]) &&
        count($orders) > 0
    ) {
        $item->set_method_title("Consegna");
        $item->set_method_id("flat_rate:1"); // set an existing Shipping method rate ID
        $item->set_total(5); // (optional)
    } else {
        $item->set_method_title("Consegna Gratuita");
        $item->set_method_id("free_shipping:3"); // set an existing Shipping method rate ID
        $item->set_total(0); // (optional)
    }

    $order->add_item($item);
    $item->calculate_taxes($calculate_tax_for);

    $order->calculate_totals();
    $order->update_status("completed", "Ordine generato in automatico", true);
    update_post_meta($order->get_id(), "_total_box_weight", $weight);
    update_post_meta($order->get_id(), "_week", $week);
    update_post_meta($order->get_id(), "_box_navision_id", $boxNavisionId);
    $piano = get_user_meta($customerId, "shipping_piano", true);
    $pianoValue = "";
    if ($piano) {
        $pianoValue = "Piano " . $piano;
    }
    update_post_meta($order->get_id(), "shipping_piano", $pianoValue);
    /*
		if (($order->get_date_paid()->format('w') > 5 && $order->get_date_paid()->format('H') >= 8) || $order->get_date_paid()->format('w') == 0) {
		$order->get_date_paid()->add(new DateInterval('P7D'));
		}
		*/
    update_post_meta($order->get_id(), "_data_consegna", $consegna);
    update_post_meta($order->get_id(), "_order_type", "FN");
    update_post_meta($order->get_id(), "_subscription_id", $id);
    $boxPreferences = get_post_meta(
        $subscription->get_id(),
        "_box_preferences",
        true
    );
    if (empty($boxPreferences)) {
        $boxPreferences = [];
    }
    update_post_meta($order->get_id(), "_box_preferences", $boxPreferences);
    $groups = get_posts([
        "post_type" => "delivery-group",
        "post_status" => "publish",
        "posts_per_page" => -1,
    ]);
    foreach ($groups as $group) {
        $caps = get_post_meta($group->ID, "cap", true);
        if (in_array($order->get_shipping_postcode(), $caps)) {
            update_post_meta(
                $order->get_id(),
                "_gruppo_consegna",
                $group->post_title
            );
        }
    }

    //spedizione gratuita per primi ordini oppure asti cuneo

    //get all orders of same user
    /*$args = array(
		'customer_id' => $customerId,
		'status' => 'completed',
		'limit' => -1, // to retrieve _all_ orders by this user
		);
		$orders = wc_get_orders($args);
	*/

    calculate_delivery_date_order($order->get_id(), false);
}
function get_all_single_box()
{
    $products = get_posts([
        "post_type" => "product",
        "numberposts" => -1,
        "post_status" => "publish",
        "tax_query" => [
            [
                "taxonomy" => "product_cat",
                "field" => "slug",
                "terms" => "box singola",
                "operator" => "IN",
            ],
        ],
    ]);
    $singleBoxes = [];
    foreach ($products as $product) {
        $product = wc_get_product($product->ID);
        $children = $product->get_children();
        foreach ($children as $variation) {
            $variation = wc_get_product($variation);
            $singleBoxes[] = $variation;
        }
    }
    return $singleBoxes;
}
function get_single_box_from_attributes($tipologia, $dimensione)
{
    $products = get_posts([
        "post_type" => "product",
        "numberposts" => -1,
        "post_status" => "publish",
        "tax_query" => [
            [
                "taxonomy" => "product_cat",
                "field" => "slug",
                "terms" => "box singola",
                "operator" => "IN",
            ],
        ],
    ]);
    $productFound = false;
    foreach ($products as $product) {
        $product = wc_get_product($product->ID);
        $children = $product->get_children();
        foreach ($children as $variation) {
            $tipologiaVariation = get_post_meta(
                $variation,
                "attribute_pa_tipologia",
                true
            );
            $dimensioneVariation = get_post_meta(
                $variation,
                "attribute_pa_dimensione",
                true
            );
            if (
                $tipologia == $tipologiaVariation &&
                $dimensioneVariation == $dimensione
            ) {
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
function get_subscription_box_from_attributes($tipologia, $dimensione)
{
    $products = get_posts([
        "post_type" => "product",
        "numberposts" => -1,
        "post_status" => "publish",
        "tax_query" => [
            [
                "taxonomy" => "product_cat",
                "field" => "slug",
                "terms" => "box",
                "operator" => "IN",
            ],
        ],
    ]);
    $productFound = false;
    foreach ($products as $product) {
        $product = wc_get_product($product->ID);
        $children = $product->get_children();
        foreach ($children as $variation) {
            $tipologiaVariation = get_post_meta(
                $variation,
                "attribute_pa_tipologia",
                true
            );
            $dimensioneVariation = get_post_meta(
                $variation,
                "attribute_pa_dimensione",
                true
            );
            if (
                $tipologia == $tipologiaVariation &&
                $dimensioneVariation == $dimensione
            ) {
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
function get_fabbisogno($week)
{
    $fabbisognoList = new WP_Query([
        "posts_per_page" => -1,
        "post_type" => "fabbisogno",
        "meta_query" => [
            "relation" => "and",
            ["key" => "settimana", "value" => $week, "compare" => "="],
        ],
    ]);
    $fabbisognoList = $fabbisognoList->get_posts();
    $groupedFabbisogno = [];
    foreach ($fabbisognoList as $fabbisogno) {
        $prodottoId = get_post_meta($fabbisogno->ID, "prodotto", true);
        $prodottoId = reset($prodottoId);
        $fornitore = get_post_meta($prodottoId, "product_producer", true);
        $fornitoreString = "";
        if (!empty($fornitore)) {
            $fornitore = reset($fornitore);
            $fornitore = get_post($fornitore);
            $fornitoreString = $fornitore->post_title;
        }
        if (!isset($groupedFabbisogno[$fornitoreString])) {
            $groupedFabbisogno[$fornitoreString] = [];
        }
        $groupedFabbisogno[$fornitoreString][] = $fabbisogno;
    }
    return $groupedFabbisogno;
}
function register_my_custom_submenu_page()
{
    add_menu_page(
        "Genera Ordini Box",
        "Genera Ordini Box",
        "manage_options",
        "genera-ordini-box",
        "my_custom_submenu_page_callback"
    );

    add_menu_page(
        "Scegli Tu in Sospeso",
        "Scegli Tu in Sospeso",
        "manage_options",
        "pending-scegli-tu",
        "scegli_tu_page"
    );

    add_menu_page(
        "Rinnovi FN in Sospeso",
        "Rinnovi FN in Sospeso",
        "manage_options",
        "pending-rinnovi",
        "rinnovi_fn_page"
    );

    add_menu_page(
        "Abbonamenti disattivi con debito",
        "Abbonamenti disattivi con debito",
        "manage_options",
        "abbonamenti-debito",
        "abbonamenti_debito_page"
    );

    add_menu_page(
        "Sospensioni Abbonamento",
        "Sospensioni Abbonamento",
        "manage_options",
        "sospensioni-abbonamento",
        "sospensioni_abbonamento_page"
    );
}

add_action("create_order_subscription", function ($subscriptionId) {
    create_order_from_subscription($subscriptionId);
    update_post_meta($subscriptionId, "_is_order_creating", false);
});

add_action("activate_order", function ($orderId) {
    $order = wc_get_order($orderId);
    $order->update_status("completed", "Ordine completato da admin", true);
    update_post_meta($orderId, "_is_order_updating", false);
});

add_action("change_order_status", function ($orderId, $status) {
    $order = wc_get_order($orderId);
    $order->update_status($status, "Ordine aggiornato da admin", true);
    update_post_meta($orderId, "_is_order_updating", false);
});

function rinnovi_fn_page()
{
    if (isset($_POST["complete_orders"])) {

        $orderIds = $_POST["orders"];
        foreach ($orderIds as $orderId) {
            // update_post_meta($orderId, "_is_order_updating", true);
            $order = wc_get_order($orderId);
            $order->update_status(
                $_POST["status"],
                "Ordine aggiornato da admin",
                true
            );
            // update_post_meta($orderId, "_is_order_updating", false);
            //change_order_status($orderId,$_POST["status"]);
            /*as_enqueue_async_action("change_order_status", [
                "orderId" => $orderId,
                "status" => $_POST["status"]
            ]);*/
        }
        ?>

        <!--<h4 style="color:white;background:darkgreen;padding:15px;display:block">Sto completando tutti gli ordini, l'operazione viene fatta in background in modo che non devi aspettare tempo.</h4>
		<br>-->
		<?php
    }

    $pendingOrders = wc_get_orders([
        "limit" => -1,
        "status" => ["pending", "on-hold", "processing"],
    ]);

    foreach ($pendingOrders as $key => $order) {
        $orderType = null;
        $subscriptionId = null;

        $orderRenewal = get_post_meta(
            $order->get_id(),
            "_subscription_renewal",
            true
        );
        if ($orderRenewal) {
            $orderType = "RINNOVO SETTIMANALE";
            $subscriptionId = $orderRenewal;
        } else {
            global $wpdb;
            $isParent = $wpdb->get_results(
                "SELECT ID FROM {$wpdb->prefix}posts WHERE post_parent = " .
                    $order->get_id(),

                ARRAY_A
            );

            if (!empty($isParent)) {
                $orderType = "PRIMO PAGAMENTO";
                $subscriptionId = $isParent[0]["ID"];
            }
        }

        if (!$orderType) {
            unset($pendingOrders[$key]);
            continue;
        }

        $pendingOrders[$key]->type = $orderType;
        $pendingOrders[$key]->subscription_id = $subscriptionId;
        $items = $order->get_items();
        $pendingOrders[$key]->total_products = count($items);
        $pendingOrders[$key]->products = [];
        // Going through each current customer order items
        foreach ($items as $item_id => $item_values) {
            $product = $item_values->get_product();
            $pendingOrders[$key]->products[] = $product->get_name();
        }
    }
    $order_statuses = [
        "wc-pending" => _x("Pending payment", "Order status", "woocommerce"),
        "wc-processing" => _x("Processing", "Order status", "woocommerce"),
        "wc-on-hold" => _x("On hold", "Order status", "woocommerce"),
        "wc-completed" => _x("Completed", "Order status", "woocommerce"),
        "wc-cancelled" => _x("Cancelled", "Order status", "woocommerce"),
        "wc-refunded" => _x("Refunded", "Order status", "woocommerce"),
        "wc-failed" => _x("Failed", "Order status", "woocommerce"),
    ];
    ?>

		<div id="wpbody-content">

			<div class="wrap">
				<div class="agr-create-new-orders">

					<h1 class="wp-heading-inline">
						Rinnovi FN in sospeso</h1>

					<p style="font-size: 16px; margin-bottom: 24px;">In questa pagina puoi settare gli ordini "FACCIAMO NOI" ancora in sospeso come COMPLETATO.<br>
					In questo modo gli abbonamenti si riattiveranno.</p>


					<hr class="wp-header-end">

					<br>

					<form id="comments-form" method="POST"
						  action="">

						<input type="hidden" name="complete_orders" value="1">


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
							<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;">
								<span>Prodotti</span>
							</th>
							<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;">
								<span>Totale</span>
							</th>
							<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;"
								scope="col" id="comment" class="manage-column column-comment column-primary">
								<span>Stato</span>
							</th>
<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;"
								scope="col" id="comment" class="manage-column column-comment column-primary">
								<span>Tipo</span>
							</th>
<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;">
								<span>Creato Il</span>
							</th>

							</thead>

							<tbody>
<?php foreach ($pendingOrders as $order): ?>
<?php $isUpdating = get_post_meta(
    $order->get_id(),
    "_is_order_updating",
    true
); ?>
<tr>
<td>
<input type="checkbox" name="orders[]" value="<?php echo $order->get_id(); ?>"><br>
<?php if ($isUpdating == true): ?>
<i>Sto completando...</i>
<?php endif; ?>
</td>
<td>
 <a href="/wp-admin/post.php?post=<?php echo $order->get_id(); ?>&action=edit" target="_blank">

 <?php if (!empty($order->get_shipping_last_name())) {
     echo $order->get_shipping_last_name() .
         " " .
         $order->get_shipping_first_name();
 } else {
     echo $order->get_billing_last_name() .
         " " .
         $order->get_billing_first_name();
 } ?>

    </a>
</td>
<td>
<div style="height: 80px; width:200px;overflow: scroll">
<?php echo implode("<br>", $order->products); ?>

</div>
</td>
<td>
<?php echo $order->get_total() . "€"; ?>
</td>
<td>
<mark class="order-status status-<?php echo $order->get_status(); ?> tips"><span>
<?php echo $order_statuses["wc-" . $order->get_status()]; ?>
</span></mark>

</td>
<td>
<a href="/wp-admin/post.php?post=<?php echo $order->subscription_id; ?>&action=edit" target="_blank"><?php echo $order->type; ?></a>

</td>
<td>
<?php echo $order->get_date_created()->format("d/m/Y H:i"); ?>
</td>
</tr>
<?php endforeach; ?>
							</tbody>
						</table>
						<br><br>
						<select name="status">
						<option value="completed">Aggiorna a COMPLETATO</option>
						<option value="cancelled">Aggiorna a CANCELLATO</option>
						</select>

						<button type="submit" class="button-primary">Aggiorna Ordini</button>
					</form>

					<br/>

				</div>


			</div>

			<div id="ajax-response"></div>

			<div class="clear"></div>
		</div>
		<?php
}
function scegli_tu_page()
{
    if (isset($_POST["complete_orders"])) {

        $orderIds = $_POST["orders"];
        foreach ($orderIds as $orderId) {
            update_post_meta($orderId, "_is_order_updating", 1);
            as_enqueue_async_action("activate_order", [
                "orderId" => $orderId,
            ]);
        }
        ?>

        <h4 style="color:white;background:darkgreen;padding:15px;display:block">Sto completando tutti gli ordini, l'operazione viene fatta in background in modo che non devi aspettare tempo.</h4>
		<br>
		<?php
    }
    $pendingOrders = wc_get_orders([
        "limit" => -1,
        "status" => ["pending", "on-hold", "processing"],
        "meta_key" => "_order_type",
        "meta_value" => "ST",
        "meta_compare" => "=",
    ]);
    foreach ($pendingOrders as $key => $order) {
        $items = $order->get_items();
        $pendingOrders[$key]->total_products = count($items);
        $pendingOrders[$key]->products = []; // Going through each current customer order items
        foreach ($items as $item_id => $item_values) {
            $product = $item_values->get_product();
            $pendingOrders[$key]->products[] = $product->get_name();
        }
    }
    $order_statuses = [
        "wc-pending" => _x("Pending payment", "Order status", "woocommerce"),
        "wc-processing" => _x("Processing", "Order status", "woocommerce"),
        "wc-on-hold" => _x("On hold", "Order status", "woocommerce"),
        "wc-completed" => _x("Completed", "Order status", "woocommerce"),
        "wc-cancelled" => _x("Cancelled", "Order status", "woocommerce"),
        "wc-refunded" => _x("Refunded", "Order status", "woocommerce"),
        "wc-failed" => _x("Failed", "Order status", "woocommerce"),
    ];
    ?>

		<div id="wpbody-content">

			<div class="wrap">
				<div class="agr-create-new-orders">

					<h1 class="wp-heading-inline">
						Scegli Tu in sospeso</h1>

					<p style="font-size: 16px; margin-bottom: 24px;">In questa pagina puoi settare gli ordini "SCEGLI TU" ancora in sospeso come COMPLETATO.<br>
					In questo modo saranno importati su Navision.</p>


					<hr class="wp-header-end">

					<br>

					<form id="comments-form" method="POST"
						  action="">

						<input type="hidden" name="complete_orders" value="1">


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
							<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;">
								<span>Prodotti</span>
							</th>
							<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;">
								<span>Totale</span>
							</th>
							<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;"
								scope="col" id="comment" class="manage-column column-comment column-primary">
								<span>Stato</span>
							</th>
<th style="padding: 16px;border-width: 1px; border-style: solid; border-color: rgb(241, 241, 241) rgb(241, 241, 241) rgb(0, 0, 0); border-image: initial; background: rgb(255, 255, 255); font-size: 16px; border-radius: 6px 6px 0px 0px;">
								<span>Creato Il</span>
							</th>

							</thead>

							<tbody>
<?php foreach ($pendingOrders as $order): ?>
<?php $isUpdating = get_post_meta(
    $order->get_id(),
    "_is_order_updating",
    true
); ?>
<tr>
<td>
<input type="checkbox" name="orders[]" value="<?php echo $order->get_id(); ?>"><br>
<?php if ($isUpdating == true): ?>
<i>Sto completando...</i>
<?php endif; ?>
</td>
<td>
 <a href="/wp-admin/post.php?post=<?php echo $order->get_id(); ?>&action=edit" target="_blank"><?php echo $order->get_shipping_last_name() .
    " " .
    $order->get_shipping_first_name(); ?></a>
</td>
<td>
<div style="height: 80px; width:200px;overflow: scroll">
<?php echo implode("<br>", $order->products); ?>

</div>
</td>
<td>
<?php echo $order->get_total() . "€"; ?>
</td>
<td>
<mark class="order-status status-<?php echo $order->get_status(); ?> tips"><span>
<?php echo $order_statuses["wc-" . $order->get_status()]; ?>
</span></mark>

</td>
<td>
<?php echo $order->get_date_created()->format("d/m/Y H:i"); ?>
</td>
</tr>
<?php endforeach; ?>
							</tbody>
						</table>
						<br><br>
						<select name="status">
						<option value="completed">Aggiorna a COMPLETATO</option>
						<!--<option value="trash">Sposta nel cestino</option>-->
						</select>

						<button type="submit" class="button-primary">Aggiorna Ordini</button>
					</form>

					<br/>

				</div>


			</div>

			<div id="ajax-response"></div>

			<div class="clear"></div>
		</div>
		<?php
}
function my_custom_submenu_page_callback()
{
    $date = new DateTime();
    $date->modify("+1 week");
    $week = $date->format("W");
    if (isset($_POST["generate_orders"])) {

        $subscriptionIds = $_POST["subscriptions"];
        foreach ($subscriptionIds as $subscriptionId) {
            //create_order_from_subscription($subscriptionId);
            update_post_meta($subscriptionId, "_is_order_creating", true);
            as_enqueue_async_action(
                "create_order_subscription",
                ["subscriptionId" => $subscriptionId],
                "create_subscription"
            );
        }
        ?>
		<br>
		<h4 style="color:white;background:darkgreen;padding:15px;display:block">Creazione ordini in corso, l'operazione ci metterà qualche minuto...</h4><br>
		<?php
    }
    if (isset($_GET["generate_fabbisogno"])) {
        //generate_fabbisogno();
    }
    if (isset($_POST["send_email_produttori"])) {
        //send_email_produttori($week);
    }
    $subscriptions = wcs_get_subscriptions([
        "subscriptions_per_page" => -1,
        "subscription_status" => "active",
    ]);
    $groupedFabbisogno = [];
    $lastOrderWeek = get_option("last_order_week", true);
    $orders = wc_get_orders([
        "limit" => -1,
        "orderby" => "date",
        "order" => "ASC",
        "meta_key" => "_week",
        "meta_compare" => "=",
        "meta_value" => $lastOrderWeek,
    ]);
    foreach ($orders as $key => $order) {
        if ($order->get_status() != "completed") {
            unset($orders[$key]);
        }
        $orderType = get_post_meta($order->get_id(), "_order_type", true);
        if (strstr($orderType, "ST") === false) {
            unset($orders[$key]);
        }
    }
    foreach ($subscriptions as $key => $subscription) {
        $disabledWeeks = get_post_meta(
            $subscription->get_id(),
            "disable_weeks_" . $date->format("Y"),
            true
        );
        if (is_array($disabledWeeks) && in_array($week, $disabledWeeks)) {
            unset($subscriptions[$key]);
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
						prodotti che preferisci. Seleziona gli ordini che vuoi generare.</p>

					<span
						style="background: rgba(60,33,255,.1);padding:8px 12px;border-radius: 8px;font-weight: 700;font-size: 16px;margin: 16px 0;display: inline-block;">Settimana <?php echo $week; ?> di 52</span>
					<?php $wednesday = date("d/m/Y", strtotime("wednesday next week")); ?>
					<span
						style="background: rgba(60,33,255,.1);padding:8px 12px;border-radius: 8px;font-weight: 700;font-size: 16px;margin: 16px 0;display: inline-block;">Data di consegna: <?php echo $wednesday; ?></span>
					<hr class="wp-header-end">

					<br>

					<h3>Facciamo Noi: <?php echo count($subscriptions); ?></h3>
					<h3>Scegli Tu: <?php echo count($orders); ?></h3>

					<form id="comments-form" method="POST"
						  action="">

						<input type="hidden" name="generate_orders" value="1">
						<div class="tablenav top">


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
               "posts_per_page" => -1,
               "post_type" => "shop_order",
               "post_status" => ["wc-processing", "wc-completed"],
               "meta_query" => [
                   "relation" => "AND",
                   [
                       "key" => "_week",
                       "value" => date("Y") . "_" . $week,
                       "compare" => "=",
                   ],
                   [
                       "key" => "_subscription_id",
                       "value" => $subscription->get_id(),
                       "compare" => "=",
                   ],
               ],
           ];
           $orders = new WP_Query($args);
           $orders = $orders->get_posts();
           $products = $subscription->get_items();
           $boxProduct = reset($products);
           $variationProduct = $boxProduct->get_product();
           if (!$variationProduct) {
               continue;
           }
           $tipologia = get_post_meta(
               $variationProduct->get_id(),
               "attribute_pa_tipologia",
               true
           );
           $dimensione = get_post_meta(
               $variationProduct->get_id(),
               "attribute_pa_dimensione",
               true
           );
           ?>
								<tr id="comment-1" class="comment even thread-even depth-1 approved">
									<th scope="row" class="check-column" style="padding: 16px;">
										<label class="screen-reader-text" for="cb-select-1">Seleziona un abbonamento</label>

										<?php
          $box = get_single_box_from_attributes($tipologia, $dimensione);
          if (!$box) {
              echo "Box Singola Non disponibile";
          } else {

              //check if exist weekly box
              $weekBox = get_weekly_box_from_box($box->get_id(), $week);
              if ($weekBox): ?>
												<input id="cb-select-1" type="checkbox" name="subscriptions[]"
													   value="<?php echo $subscription->get_id(); ?>"
													<?php if (count($orders) > 0): ?>
														disabled
													<?php endif; ?>
												><br>
											<?php else: ?>
												Devi prima creare la box
											<?php endif;
              ?>
										<?php
          }
          ?>

									</th>
									<td class="author column-author" data-colname="Autore" style="padding: 16px;">
										<span><?php echo $subscription->get_billing_first_name() .
              " " .
              $subscription->get_billing_last_name(); ?></span>
									</td>
									<td class="comment column-comment has-row-actions column-primary"
										data-colname="Commento" style="padding: 16px;">
										<span><?php echo $boxProduct->get_name(); ?>
											</span>
									</td>

									<td class="response column-response" data-colname="In risposta a"
										style="padding: 16px;">
										<span>
										<?php // fix nathi per errore data di consegna


          $fixdate = $subscription->get_date_created();
          $fixdate = new DateTime($fixdate);
          echo $fixdate->format("d/m/Y");
          ?>
										</span>
									</td>
									<td style="padding: 16px;">
										<?php if (count($orders) > 0): ?>
											<a target="_blank"
											   href="/wp-admin/post.php?post=<?php echo $orders[0]->ID; ?>&action=edit">Vai
												all'ordine</a>
										<?php endif; ?>
									</td>
								</tr>
							<?php
       endforeach; ?>
							</tbody>
						</table>
						<br><br>

						<button type="submit" class="button-primary">Genera Ordini</button>
					</form>
	<!--
					<br>
					<br>
					<br>
					<h2>Fabbisogno (non modificabile)</h2>

					<p style="font-size: 16px; margin-bottom: 24px;">
						Qui puoi generare il fabbisogno automatico degli ordini appena generati (Facciamo noi) e di tutti
						gli ordini ricevuti per questa settimana attraverso il negozio.<br/>
						Per modificare il fabbisogno, bisogna andare nel menu "Modifica fabbisogno".
					</p>

					<table class="datatable styled-table" style="width:100%;border-collapse: collapse;">
						<thead>
						<tr>
							<th style="padding: 8px 10px;">Fornitore</th>
							<th style="padding: 8px 10px;">Prodotti</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($groupedFabbisogno as $fornitore => $fabbisognoList): ?>
							<tr>
								<td><?php echo $fornitore; ?></td>
								<td>
									<table style="width:100%;border-collapse: collapse;">
										<thead>
										<th style="padding: 8px 10px;">Descrizione</th>
										<th style="padding: 8px 10px;">Codice</th>
										<th style="padding: 8px 10px;">Gruppo Consegna</th>
										<th style="padding: 8px 10px;">Peso</th>

										<th style="padding: 8px 10px;">Prezzo</th>
										<th style="padding: 8px 10px;">Un. Misura</th>
										<th style="padding: 8px 10px;">Cod. Conf</th>
										<th style="padding: 8px 10px;">Disponibilità<br/>in magazzino</th>
										<th style="padding: 8px 10px;">Quantità<br/>richiesta</th>
										</thead>
										<tbody>

										<?php
          $countFB = 0;
          $sum = 0;
          $sum_weight = 0;
          foreach ($fabbisognoList as $fabbisogno):

              echo "<tr>";
              $prodottoId = get_post_meta($fabbisogno->ID, "prodotto", true);
              $prodottoId = reset($prodottoId);
              $product = wc_get_product($prodottoId);
              $weight = get_post_meta($fabbisogno->ID, "weight", true);
              $price = get_post_meta($prodottoId, "_regular_price", true);
              $sku = get_post_meta($prodottoId, "_sku", true);
              $codiceConfezionamento = get_post_meta(
                  $prodottoId,
                  "_codice_confezionamento",
                  true
              );
              if (
                  is_array($codiceConfezionamento) &&
                  empty($codiceConfezionamento)
              ) {
                  $codiceConfezionamento = "";
              }
              if (
                  is_array($codiceConfezionamento) &&
                  !empty($codiceConfezionamento)
              ) {
                  $codiceConfezionamento = reset($codiceConfezionamento);
              }
              $unitaMisura =
                  " " . get_post_meta($fabbisogno->ID, "weight_type", true); //tabella riepilogo box
              $measureAcquisto = get_post_meta(
                  $fabbisogno->ID,
                  "quantity_type",
                  true
              );
              $gruppoConsegna = get_post_meta(
                  $fabbisogno->ID,
                  "gruppo_consegna",
                  true
              );
              $fabbisogno = get_post_meta($fabbisogno->ID, "fabbisogno", true);
              $sum += $fabbisogno;
              $sum_weight += $weight;
              ?>
											<td style="padding: 8px 10px;">
												<a href="<?php echo esc_url(home_url()) .
                "/wp-admin/post.php?post=" .
                $prodottoId .
                "&action=edit"; ?>"><?php echo $product->get_name(); ?></a>
											</td>

											<td style="padding: 8px 10px;"><?php echo $sku; ?></td>
											<td style="padding: 8px 10px;"><?php echo $gruppoConsegna; ?></td>

											<td style="padding: 8px 10px;"><?php echo $weight . $unitaMisura; ?></td>
											<td style="padding: 8px 10px;"><?php echo "€" . $price; ?></td>
											<td style="padding: 8px 10px;"><?php echo $measureAcquisto; ?></td>
											<td style="padding: 8px 10px;"><?php echo $codiceConfezionamento; ?></td>

											<td style="padding: 8px 10px;"><?php echo $product->get_stock_quantity(); ?></td>
											<td style="padding: 8px 10px;"><?php echo $fabbisogno; ?></td>

											</tr>
											<?php
           if ($countFB == count($fabbisognoList) - 1): ?>
												<tr>
													<td style="padding: 8px 10px 26px; border-top:2px solid #000;border-bottom: none;"></td>
													<td style="padding: 8px 10px 26px; border-top:2px solid #000;border-bottom: none;"></td>
													<td style="padding: 8px 10px 26px; border-top:2px solid #000;border-bottom: none;"></td>
													<td style="padding: 8px 10px 26px; border-top:2px solid #000;border-bottom: none;">
														<strong><?php echo $sum_weight; ?></strong>
													</td>
													<td style="padding: 8px 10px 26px; border-top:2px solid #000;border-bottom: none;"></td>
													<td style="padding: 8px 10px 26px; border-top:2px solid #000;border-bottom: none;"></td>
													<td style="padding: 8px 10px 26px; border-top:2px solid #000;border-bottom: none;"></td>
													<td style="padding: 8px 10px 26px; border-top:2px solid #000;border-bottom: none;"></td>
													<td style="padding: 8px 10px 26px; border-top:2px solid #000;border-bottom: none;">
														<strong><?php echo $sum; ?></strong>
													</td>
												</tr>
											<?php endif;
           $countFB = $countFB + 1;

          endforeach;
          ?>
										</tbody>
									</table>

								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<br/>
					<a href="/wp-admin/admin.php?page=genera-ordini-box&generate_fabbisogno=1" class="button-primary">
						Genera Fabbisogno
					</a>

					<?php if (!empty($fabbisognoList)): ?>
						<br><br>

						<form method="POST" action="/wp-admin/admin.php?page=genera-ordini-box">
							<input type="hidden" name="send_email_produttori" value="1">

							<button type="submit"
									class="button-primary">
								Invia email ai produttori
							</button>
						</form>


					<?php endif; ?>
	-->
					<br/>

				</div>


			</div>

			<div id="ajax-response"></div>

			<div class="clear"></div>
		</div>
		<?php
}
add_action("admin_menu", "register_my_custom_submenu_page", 99);
add_filter("manage_edit-shop_order_columns", "custom_shop_order_column", 20);
function custom_shop_order_column($columns)
{
    $reordered_columns = []; // Inserting columns to a specific location
    foreach ($columns as $key => $column) {
        $reordered_columns[$key] = $column;
        if ($key == "order_status") {
            $reordered_columns["payment_method"] = "Metodo di pagamento";
            $reordered_columns["type_shopping"] = "Spesa";
        }
    }
    unset($reordered_columns["export_status"]);
    unset($reordered_columns["subscription_relationship"]);
    $columns = $reordered_columns;
    return $columns; //return $reordered_columns;
} // Custom column content
add_action(
    "manage_shop_order_posts_custom_column",
    "shop_order_column_meta_field_value"
);
function get_order_type($id)
{
    global $wpdb;
    $orderType = get_post_meta("_order_type", $id, true);
    if ($orderType) {
        return $orderType;
    }
    $orderTypes = [];
    $isParent = $wpdb->get_results(
        "SELECT ID FROM {$wpdb->prefix}posts WHERE post_parent = " . $id,
        ARRAY_A
    );
    if (!empty($isParent)) {
        $orderTypes[] = "ABBONAMENTO";
    }
    $orderRenewal = get_post_meta($id, "_subscription_renewal", true);
    if (!empty($orderRenewal)) {
        $orderTypes[] = "ABBONAMENTO";
    } //check in cart
    $order = wc_get_order($id);
    foreach ($order->get_items() as $item_id => $item) {
        if ($item->get_name() == "Acquisto credito") {
            $orderTypes[] = "CREDITO";
        } else {
            $categories = get_the_terms($item->get_product_id(), "product_cat");
            foreach ($categories as $term) {
                if (in_array($term->slug, ["box"])) {
                    $orderTypes[] = "FN";
                } else {
                    $orderTypes[] = "ST";
                }
            }
        }
    }
    return implode(" + ", $orderTypes);
}
function shop_order_column_meta_field_value($column)
{
    global $post;
    global $wpdb;
    if ($column == "export_status") {
        echo "";
    }
    if ($column == "subscription_relationship") {
        echo "";
    }
    if ($column == "payment_method") {
    }
    if ($column == "type_shopping") {
        $orderRenewal = get_post_meta($post->ID, "_subscription_renewal", true);
        if ($orderRenewal) {
            echo '<a href="/wp-admin/post.php?post=' .
                $orderRenewal .
                '&action=edit" target="_blank">RINNOVO FN</a>';
        } else {
            $isParent = $wpdb->get_results(
                "SELECT ID FROM {$wpdb->prefix}posts WHERE post_parent = " .
                    $post->ID,
                ARRAY_A
            );
            $orderType = get_post_meta($post->ID, "_order_type", true);
            if ($orderType) {
                echo $orderType;
            }
            if (!empty($isParent)) {
                echo '<br><a href="/wp-admin/post.php?post=' .
                    $isParent[0]["ID"] .
                    '&action=edit" target="_blank">Vedi abbonamento</a>';
            }
        }
        $paymentMethod = get_post_meta($post->ID, "_payment_method", true);
        if ($paymentMethod == "bacs") {
            echo "<br> Bonifico Bancario";
        }
        if ($paymentMethod == "woocommerce_payments") {
            echo "<br> Pagamento con carta";
        }
        if ($paymentMethod == "satispay") {
            echo "<br> Satispay";
        }
        if ($paymentMethod == "wallet") {
            echo "<br> Credito utente";
        }
    }
} // add_filter( "manage_edit-shop_order_sortable_columns", 'shop_order_column_meta_field_sortable' ); // function shop_order_column_meta_field_sortable( $columns ) // { //     $meta_key = 'name'; //     return wp_parse_args( array('type_notes' => $meta_key), $columns ); //     return wp_parse_args( array('type_shopping' => $meta_key), $columns ); // }
// Make custom column sortable
function cptui_register_my_cpts_delivery_group()
{
    /**
     * Post Type: Gruppi di Consegna.
     */ $labels = [
        "name" => esc_html__("Gruppi di Consegna", "custom-post-type-ui"),
        "singular_name" => esc_html__(
            "Gruppo di consegna",
            "custom-post-type-ui"
        ),
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
    $labels = [
        "name" => esc_html__("Gruppi di Prodotto", "custom-post-type-ui"),
        "singular_name" => esc_html__(
            "Gruppi di Prodotto",
            "custom-post-type-ui"
        ),
    ];
    $args = [
        "label" => esc_html__("Gruppi Prodotto", "custom-post-type-ui"),
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
        "rewrite" => ["slug" => "gruppo-prodotto", "with_front" => true],
        "query_var" => true,
        "supports" => ["title", "editor"],
        "show_in_graphql" => false,
    ];
    register_post_type("gruppo-prodotto", $args); /**
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
    $labels = [
        "name" => esc_html__("Fabbisogno", "custom-post-type-ui"),
        "singular_name" => esc_html__("Fabbisogno", "custom-post-type-ui"),
    ];
    $args = [
        "label" => esc_html__("Fabbisogno", "custom-post-type-ui"),
        "labels" => $labels,
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
        "rewrite" => ["slug" => "fabbisogno", "with_front" => true],
        "query_var" => true,
        "supports" => ["title", "editor"],
        "show_in_graphql" => false,
    ];
    register_post_type("fabbisogno", $args);
}
add_action("init", "cptui_register_my_cpts_delivery_group");
add_action("admin_menu", "consegne_ordini_pages");
function consegne_ordini_pages()
{
    add_menu_page(
        "Consegne Ordini",
        "Consegne Ordini",
        "manage_options",
        "consegne-ordini",
        function () {
            $groups = get_posts([
                "post_type" => "delivery-group",
                "post_status" => "publish",
                "posts_per_page" => -1,
            ]);
            if (isset($_POST["import_consegne"])) {
                if (isset($_FILES["file"])) {
                    //if there was an error uploading the file
                    if ($_FILES["file"]["error"] > 0) {
                        echo "Return Code: " .
                            $_FILES["file"]["error"] .
                            "<br />";
                    } else {

                        $storagename = "csv.txt";
                        move_uploaded_file(
                            $_FILES["file"]["tmp_name"],
                            get_temp_dir() . "/" . $storagename
                        );
                        $file = fopen(get_temp_dir() . "/" . $storagename, "r");
                        $csv = [];
                        while (!feof($file)) {
                            $csv[] = fgetcsv($file, null, ";");
                        }
                        fclose($file);
                        $args = [
                            "posts_per_page" => -1,
                            "post_type" => "shop_order",
                            "post_status" => ["wc-processing", "wc-completed"],
                            "meta_query" => [
                                "relation" => "AND",
                                [
                                    "key" => "_data_consegna",
                                    "value" => $_POST["_data_consegna"],
                                    "compare" => "=",
                                ],
                            ],
                        ];
                        $orders = new WP_Query($args);
                        $orders = $orders->get_posts();
                        $i = 0;
                        foreach ($csv as $single) {
                            $order = array_filter($orders, function (
                                $tmpOrder
                            ) use ($single) {
                                $address = get_post_meta(
                                    $tmpOrder->ID,
                                    "_shipping_address_1",
                                    true
                                );
                                $town = get_post_meta(
                                    $tmpOrder->ID,
                                    "_shipping_city",
                                    true
                                );
                                return trim($address) == trim($single[4]) &&
                                    trim($town) == trim($single[3]);
                            });
                            if (!empty($order)) {
                                $order = reset($order);
                                update_post_meta(
                                    $order->ID,
                                    "_numero_consegna",
                                    trim($single[0])
                                );
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
       global $wpdb;
       $allDataConsegna = $wpdb->get_results(
           "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_data_consegna' group by meta_value",
           ARRAY_A
       );
       ?>
							<strong>Data di consegna:</strong><br>
							<select autocomplete="off" name="_data_consegna">

								<?php foreach ($allDataConsegna as $dataConsegna):
            $dataConsegna = new DateTime($dataConsegna["meta_value"]); ?>
									<option

										value="<?php echo $dataConsegna->format(
              "Y-m-d"
          ); ?>"><?php echo $dataConsegna->format("d/m/Y"); ?></option>
								<?php
        endforeach; ?>
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

           $caps = get_post_meta($group->ID, "cap", true);
           $orders = wc_get_orders([
               "limit" => -1,
               "meta_key" => "_gruppo_consegna",
               "meta_value" => $group->post_title,
               "meta_compare" => "=",
           ]);
           $orders = array_filter($orders, function ($order) {
               return $order->get_status() == "processing";
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
												<?php $consegna = get_post_meta($order->get_id(), "_numero_consegna", true); ?>
												<tr>
													<td>#<?php echo $order->get_id(); ?>
														- <?php echo $order->get_shipping_first_name(); ?> <?php echo $order->get_shipping_last_name(); ?></td>
													<td><?php echo $consegna; ?></td>
												</tr>
											<?php endforeach; ?>
										</table>
									</td>


								</tr>
							<?php
       endforeach; ?>
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
        }
    );
    add_menu_page(
        "Box Settimanali",
        "Box Settimanali",
        "manage_options",
        "box-settimanali",
        function () {
            $lastOrderBox = get_option("last_order_week", true);
            $boxs = get_posts([
                "post_type" => "weekly-box",
                "post_status" => "publish",
                "posts_per_page" => -1,
                "meta_key" => "_week",
                "meta_value" => $lastOrderBox,
                "meta_compare" => "=",
                "order" => "DESC",
            ]);
            $date = new DateTime();
            $currentWeek = $date->format("W");
            ?>

			<div id="wpbody-content">

				<div class="wrap" id="box-app">

					<div class="agr-create-new-boxes">
						<h1 class="wp-heading-inline">
							Box Settimanali</h1>

						<hr class="wp-header-end">


					<form id="comments-form" method="POST"
						  action="" style="margin-top:100px;width:100%;">
						<input type="hidden" name="generate_orders" value="1">

						<table style="max-width: 100%;" class="wp-list-table box-table">
							<thead>
							<tr>
								<th scope="col" id="author" class="manage-column column-author sortable sorting_desc"
									style="width:100px;border:1px solid #f1f1f1;background-image: none !important;border-bottom: 1px solid #000;font-size: 16px;background: #fff;border-radius: 6px 6px 0 0;">
									<span style="padding-right:16px;">Creata il</span></th>
								<th scope="col" id="author" class="manage-column column-author sortable sorting_desc"
									style="width:100px;border:1px solid #f1f1f1;background-image: none !important;border-bottom: 1px solid #000;font-size: 16px;background: #fff;border-radius: 6px 6px 0 0;">
									<span style="padding-right:16px;">Settimana</span></th>
								<th scope="col" id="comment" class="manage-column column-comment column-primary"
									style="border:1px solid #f1f1f1;background-image: none !important;border-bottom: 1px solid #000;font-size: 16px;background: #fff;border-radius: 6px 6px 0 0;">
									<span style="padding-right:16px;">Box</span>
								</th>

								<th scope="col" id="comment" class="manage-column column-comment column-primary"
									style="border:1px solid #f1f1f1;background-image: none !important;border-bottom: 1px solid #000;font-size: 16px;background: #fff;border-radius: 6px 6px 0 0;">
									<span style="padding-right:16px;">Prodotti</span>
								</th>

							</tr>
							</thead>

							<tbody id="the-comment-list" class="create-box-table--mega-table" data-wp-lists="list:comment">

							<?php
       $i = 1;
       foreach ($boxs as $box):

           if ($i < 10) {
               $i = "0" . $i;
           }
           $boxId = get_post_meta($box->ID, "_product_box_id", true);
           $productBox = get_post($boxId);
           if (!$productBox) {
               continue;
           }
           $week = get_post_meta($box->ID, "_week", true);
           $products = get_post_meta($box->ID, "_products", true);
           if (!is_array($products)) {
               $products = [];
           }
           $dataConsegna = get_post_meta($box->ID, "_data_consegna", true);
           $productsAlreadyInBox = array_map(function ($p) {
               return $p["id"];
           }, $products); // fix nathi per errore data di consegna
           $fixdate = new DateTime($dataConsegna);
           ?>

								<tr id="comment-1" class="comment even thread-even depth-1 approved ">

									<td class="author column-author" data-colname="Autore" style="padding:25px 10px 10px;">
										<span
											class="create-box-table--span-item week"><?php
           $boxdate = date_create($box->post_date);
           echo '<span style="display:block;">' .
               date_format($boxdate, "Y/m/d") .
               '</span><span style="display:block;margin-top:4px;">' .
               date_format($boxdate, "H:i:s") .
               "</span>";
           ?></span>
									</td>
									<td class="author column-author" data-colname="Autore" style="padding:25px 10px 10px;">
										<span class="create-box-table--span-item week">Settimana <?php echo $week; ?></span>
									</td>
									<td class="comment column-comment has-row-actions column-primary"
										data-colname="Commento" style="padding:25px 10px 10px;">
										<span
											class="create-box-table--span-item the-product"><?php echo $productBox->post_excerpt; ?></span>
									</td>
									<td class="response column-response">
										<table style="max-width: 100%;border-collapse: collapse">
											<thead>
											<th>Descrizione</th>
											<th>Codice</th>
											<th>Fornitore</th>
											<th>Prezzo</th>
											<th>Quantità</th>
											</thead>
											<tbody>
											<?php
           $totalWeight = 0;
           $totalPrice = 0;
           ?>
											<?php foreach ($products as $key => $product): ?>
												<?php
            $sku = get_post_meta($product["id"], "_sku", true);
            $product["price"] = get_post_meta(
                $product["id"],
                "_regular_price",
                true
            );
            $fornitore = get_post_meta(
                $product["id"],
                "product_producer",
                true
            );
            $fornitoreString = "";
            if (!empty($fornitore)) {
                $fornitore = reset($fornitore);
                $fornitore = get_post($fornitore);
                $fornitoreString = $fornitore->post_title;
            }
            $unitaMisura = "";

               //tabella riepilogo box
               ?>

												<tr class="create-box-table--row">
													<td class="create-box-table--name">
														<a target="_blank"
														   href="<?php echo esc_url(home_url()) .
                     "/wp-admin/post.php?post=" .
                     $product["id"] .
                     "&action=edit"; ?>"><?php echo $product["name"]; ?></a>
													</td>
													<td>
														<?php echo $sku; ?>
													</td>
													<td class="create-box-table--producer">
														<?php if ($fornitoreString): ?>
															<?php echo $fornitoreString; ?>
														<?php else: ?>
															<?php echo "-"; ?>
														<?php endif; ?>
													</td>
													<td class="create-box-table--price">
														€<?php echo number_format($product["price"] * $product["quantity"], 2); ?>
													</td>

													<td class="create-box-table--quantity">
<?php echo $product["quantity"]; ?>
													</td>

												</tr>
											<?php endforeach; ?>


											</tbody>
										</table>
										<span>
									</span>
									</td>

								</tr>
								<?php $i++;
       endforeach;?>
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
        }
    );
    add_menu_page(
        "Esporta Documenti",
        "Esporta Documenti",
        "manage_options",
        "esporta-documenti",
        function () {
            global $wpdb;
            if (isset($_POST["document_type"])) {
                require_once get_template_directory() .
                    "/libraries/dompdf/autoload.inc.php";
                require_once get_template_directory() .
                    "/inc/pdf/" .
                    $_POST["document_type"] .
                    ".php";
                die();
            }
            $sql =
                "SELECT meta_value from wp_postmeta where meta_key='_codice_confezionamento' group by meta_value";
            $confezionamento = $wpdb->get_results($sql, ARRAY_A);
            $confezionamento = array_map(function ($cod) {
                return $cod["meta_value"];
            }, $confezionamento);
            $confezionamento = array_unique($confezionamento);
            sort($confezionamento);
            $allDataConsegna = $wpdb->get_results(
                "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_data_consegna' group by meta_value",
                ARRAY_A
            );
            ?>
			<div id="wpbody-content">

				<div class="wrap" id="box-app">


					<div class="agr-create-new-boxes">
						<h1 class="wp-heading-inline">
							Esporta Documenti</h1>

						<br/>
						<br/>
						<br/>
						<hr class="wp-header-end">

						<form method="POST" action="/wp-admin/admin.php?noheader=1&page=esporta-documenti" target="_blank">
							<div style="display:flex;">
								<div style="margin-right: 16px;">
									<label style="font-size: 14px; font-weight: bold; margin-bottom: 6px; display: block;">Settimana
										n°</label>
									<?php
         $date = new DateTime(); //	$date->modify('+1 week');
         $currentWeek = $date->format("W");
         ?>
									<input class="change_week_print" name="week_print" id="week_print"
										   value="<?php echo $currentWeek; ?>"
										   type="number" style="width:150px;">
								</div>
								<div id="data_consegna_div">
									<label style="font-size: 14px; font-weight: bold; margin-bottom: 6px; display: block;">Data
										di consegna</label>

									<?php if (count($allDataConsegna) == 0): ?>
										<i>Nessun ordine con data consegna.</i>
									<?php
             //fix nathi per errore data di consegna quiiii
             //print_r($wednesday);
             //fix nathi per errore data di consegna quiiii
             //print_r($wednesday);
             ?>else: ?>
										<select name="data_consegna" autocomplete="on" class="get_date_shipping">
											<option disabled selected value="null">Seleziona</option>
											<?php foreach ($allDataConsegna as $dataConsegna):

               $fixdate = $dataConsegna["meta_value"];
               $fixdate = new DateTime($fixdate);
               $fixdate = $fixdate->format("d-m-Y");
               $wednesday = date("d-m-Y", strtotime("wednesday next week"));

               //print_r($wednesday);
               ?>

												<?php if (
                is_array($dataConsegna["meta_value"]) ||
                empty($dataConsegna["meta_value"])
            ) {
                continue;
            } ?>
												<option <?php if ($wednesday === $fixdate) {
                echo "selected";
            } ?> value="<?php echo $fixdate; ?>"><?php echo $fixdate; ?></option>

											<?php
           endforeach; ?>
										</select>
									<?php wp_enqueue_script(
             "moment",
             get_template_directory_uri() . "/assets/js/moment.min.js",
             ["jquery"],
             null,
             true
         ); ?>
										<script type="text/javascript">
											jQuery(document).ready(function ($) {
												$(".change_week_print").on("change paste keyup", function () {
													const y = new Date().getFullYear();
													const jan1 = new Date(y, 0, 1);
													const jan1Day = jan1.getDay();
													const daysToMonday = jan1Day === 1 ? 0 : jan1Day === 0 ? 1 : 8 - jan1Day

													const firstWednesday = daysToMonday === 0 ? jan1 : new Date(+jan1 + daysToMonday * 86400e3);
													// console.log(moment(new Date(+firstWednesday + (($(this).val() - 1) * 7 * 86400e3) + (86400e3 * 2))).format('DD-MM-YYYY'));
													// console.log($(this).val());

													if ($('.get_date_shipping :contains(' + moment(new Date(+firstWednesday + (($(this).val() - 1) * 7 * 86400e3) + (86400e3 * 2))).format('DD-MM-YYYY') + ')').length) {
														$('.get_date_shipping option[value=' + moment(new Date(+firstWednesday + (($(this).val() - 1) * 7 * 86400e3) + (86400e3 * 2))).format('DD-MM-YYYY') + ']').attr('selected', 'selected');
													} else {
														$('.get_date_shipping option[value=null]').attr('selected', 'selected');
													}


												});
											})
										</script>


									<?php endif; ?>
								</div>
							</div>

							<br/>


							<div id="codice_confezionamento_container">
								<h4 style="font-size: 14px; font-weight: bold; margin-bottom: 6px; display: block;">Codice
									di confezionamento</h4>
								<div style="display: flex">
									<div style="margin-right:8px;">
										<label
											style="font-size: 14px; font-weight: bold; margin-bottom: 6px; display: block;">Dal</label>
										<select autocomplete="off" name="confezionamento_dal">
											<option value="">-- Seleziona --</option>
											<?php foreach ($confezionamento as $codice): ?>
												<option value="<?php echo $codice; ?>"><?php echo $codice; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div>
										<label
											style="font-size: 14px; font-weight: bold; margin-bottom: 6px; display: block;">Al</label>
										<select class="select2" name="confezionamento_al">
											<option value="">-- Seleziona --</option>
											<?php foreach ($confezionamento as $codice): ?>
												<option value="<?php echo $codice; ?>"><?php echo $codice; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
							<div id="settimana_div" style="display: none">

								<div style="display: flex">
									<div>
										<label
											style="font-size: 14px; font-weight: bold; margin-bottom: 6px; display: block;">Settimana</label>
										<?php
          $date = new DateTime(); //		$date->modify('+1 week');
          $currentWeek = $date->format("W");
          $allSettimaneFabbisogno = $wpdb->get_results(
              "select meta_value from wp_postmeta pm, wp_posts p where p.ID = pm.post_id and pm.meta_key = 'settimana' and p.post_type = 'fabbisogno' group by pm.meta_value"
          );
          $allSettimaneFabbisogno = array_map(function ($tmp) {
              return $tmp->meta_value;
          }, $allSettimaneFabbisogno);
          ?>
										<select autocomplete="off" name="settimana">
											<option value="">-- Seleziona --</option>
											<?php foreach ($allSettimaneFabbisogno as $week): ?>
												<option
													<?php if ($week == $currentWeek): ?> selected <?php endif; ?>
													value="<?php echo $week; ?>"><?php echo $week; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
							<br>
							<br>
							<label style="font-size: 14px; font-weight: bold; margin-bottom: 6px; display: block;">Cosa vuoi
								esportare?</label>

							<select id="document_type" name="document_type" autocomplete="off">
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
				</div>

				<div class="clear"></div>
			</div>

			<?php
        }
    );
}
add_action(
    "woocommerce_product_after_variable_attributes",
    "variation_settings_fields",
    10,
    3
);
add_action(
    "woocommerce_save_product_variation",
    "save_variation_settings_fields",
    10,
    2
);
add_filter("woocommerce_get_formatted_subscription_total", function (
    $formatted_order_total
) {
    return str_replace("every anni", "/settimana", $formatted_order_total);
});
add_filter("woocommerce_available_variation", "load_variation_settings_fields");
function variation_settings_fields($loop, $variation_data, $variation)
{
    $idNavision = get_post_meta($variation->ID, "_navision_id", true);
    if (is_array($idNavision)) {
        $idNavision = $idNavision[0];
    }
    woocommerce_wp_text_input(
        [
            "id" => "_navision_id{$loop}",
            "name" => "_navision_id[{$loop}][]",
            "wrapper_class" => "form-row form-row-full",
            "label" => "ID Navision",
            "value" => $idNavision,
        ],
        $variation_data->ID
    );
}
function save_variation_settings_fields($variation_id, $loop)
{
    if (isset($_POST["_navision_id"][$loop])) {
        $post_data = $_POST["_navision_id"][$loop];
        update_post_meta($variation_id, "_navision_id", $post_data);
    }
}
function load_variation_settings_fields($variation)
{
    $variation["_id_navision"] = get_post_meta(
        $variation["variation_id"],
        "_id_navision",
        true
    );
    return $variation;
}
function woocommerce_wp_multi_select($field, $variation_id = 0)
{
    global $thepostid, $post;
    if ($variation_id == 0) {
        $the_id = empty($thepostid) ? $post->ID : $thepostid;
    } else {
        $the_id = $variation_id;
    }
    $field["class"] = isset($field["class"]) ? $field["class"] : "select short";
    $field["wrapper_class"] = isset($field["wrapper_class"])
        ? $field["wrapper_class"]
        : "";
    $field["name"] = isset($field["name"]) ? $field["name"] : $field["id"];
    $meta_data = maybe_unserialize(get_post_meta($the_id, $field["id"], true));
    $meta_data = $meta_data ? $meta_data : [];
    $field["value"] = isset($field["value"]) ? $field["value"] : $meta_data;
    echo '<p class="form-field ' .
        esc_attr($field["id"]) .
        "_field " .
        esc_attr($field["wrapper_class"]) .
        '"><label for="' .
        esc_attr($field["id"]) .
        '">' .
        wp_kses_post($field["label"]) .
        '</label><select id="' .
        esc_attr($field["id"]) .
        '" name="' .
        esc_attr($field["name"]) .
        '" class="' .
        esc_attr($field["class"]) .
        '" multiple="multiple">';
    foreach ($field["options"] as $key => $value) {
        echo '<option value="' .
            esc_attr($key) .
            '" ' .
            (is_array($field["value"]) && in_array($key, $field["value"])
                ? 'selected="selected"'
                : "") .
            ">" .
            esc_html($value) .
            "</option>";
    }
    echo "</select> ";
    if (!empty($field["description"])) {
        if (isset($field["desc_tip"]) && false !== $field["desc_tip"]) {
            echo '<img class="help_tip" data-tip="' .
                esc_attr($field["description"]) .
                '" src="' .
                esc_url(WC()->plugin_url()) .
                '/assets/images/help.png" height="16" width="16" />';
        } else {
            echo '<span class="description">' .
                wp_kses_post($field["description"]) .
                "</span>";
        }
    }
}
add_filter("manage_delivery-group_posts_columns", function ($columns) {
    $columns["week"] = "CSV";
    return $columns;
});
// Add the data to the custom columns for the book post type:
add_action(
    "manage_delivery-group_posts_custom_column",
    function ($column, $post_id) {
        switch ($column) {
            case "week":

                global $wpdb;
                $allDataConsegna = $wpdb->get_results(
                    "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_data_consegna'",
                    ARRAY_A
                );
                $allDataConsegna = array_map(function ($val) {
                    return $val["meta_value"];
                }, $allDataConsegna);
                $date = new DateTime(); //	$date->modify('+1 week');
                $currentWeek = $date->format("W"); // // create DateTime object with current time // // $dt->setISODate($dt->format('o'), $dt->format('W') + 1); // // set object to Monday on next week // // $periods = new DatePeriod($dt, new DateInterval('P1D'), 6); // // get all 1day periods from Monday to +6 days // // $days = iterator_to_array($periods); // // convert DatePeriod object to array // // $currentWeek = $days[0]->format("W"); // echo '<br/>Mon:' . $days[0]->format('Y-m-d'); // echo '<br/>Sun:' . $days[6]->format('Y-m-d'); // //print_r($days); // $dt = new DateTime();
                $allDataConsegna = array_unique($allDataConsegna);
                sort($allDataConsegna);
                ?>
				<?php if (count($allDataConsegna) == 0): ?>
				<i>Nessun ordine con data consegna.</i>
			<?php
        // fix nathi per errore data di consegna
        // fix nathi per errore data di consegna
        ?>else: ?>
				<select name="data_consegna" autocomplete="off">
					<?php foreach ($allDataConsegna as $dataConsegna):

         $fixdate = $dataConsegna;
         try {
             $fixdate = new DateTime($fixdate);
         } catch (\Exception $e) {
             continue;
         }
         ?>
						<option
							value="<?php echo $dataConsegna; ?>"><?php echo $fixdate->format(
    "d/m/Y"
); ?></option>
					<?php
     endforeach; ?>
				</select>

			<?php endif; ?>
				<a class="btn button-primary generate-csv" href="#" data-delivery-group="<?php echo $post_id; ?>">
					Genera CSV
				</a>

				<br>
				<em>Settimana corrente: <?php echo $currentWeek; ?></em>
				<?php break;
        }
    },
    10,
    2
);
function my_saved_post($post_id, $json, $is_update)
{
    $product = wc_get_product($post_id);
    if ($product) {
        // Retrieve the import ID.
        // Convert SimpleXml object to array for easier use.
        if (isset($json->_percentuale_ricarico)) {
            update_post_meta(
                $post_id,
                "_percentuale_ricarico",
                (string) $json->_percentuale_ricarico
            );
        }
        if (isset($json->costounitario)) {
            update_post_meta(
                $post_id,
                "_prezzo_acquisto",
                number_format((string) $json->costounitario, 2)
            );
        }
        if (isset($json->codicecategoriaconfezionamento)) {
            update_post_meta(
                $post_id,
                "_codice_confezionamento",
                (string) $json->codicecategoriaconfezionamento
            );
        }
        if (isset($json->_is_magazzino)) {
            update_post_meta(
                $post_id,
                "_is_magazzino",
                (string) $json->_is_magazzino
            );
        }
        if (isset($json->_uom_acquisto)) {
            update_post_meta(
                $post_id,
                "_uom_acquisto",
                (string) $json->_uom_acquisto
            );
        }
        if (isset($json->_qty_acquisto)) {
            update_post_meta(
                $post_id,
                "_qty_acquisto",
                (string) $json->_qty_acquisto
            );
        }
        $product->set_manage_stock(true);
        if (isset($json->scorte)) {
            $product->set_stock_quantity((string) $json->scorte);
        }
        $product->set_stock_status();
        $json->costounitario = str_replace(
            ",",
            ".",
            (string) $json->costounitario
        );
        $price = number_format((string) $json->costounitario, 2);
        if (
            !isset($json->_percentuale_ricarico) ||
            empty($json->_percentuale_ricarico)
        ) {
            $json->_percentuale_ricarico = 0;
        }
        if (is_array($json->_percentuale_ricarico)) {
            $json->_percentuale_ricarico =
                (string) $json->_percentuale_ricarico[0];
        }
        $json->_percentuale_ricarico = str_replace(
            ",",
            ".",
            (string) $json->_percentuale_ricarico
        );
        $price *= 1 + (string) $json->_percentuale_ricarico / 100;
        $price = number_format($price, 2);
        $iva = (string) $json->iva;
        if (empty(trim($iva))) {
            $iva = 0;
        }
        if ($iva > 0) {
            $price = $price + $iva * ($price / 100);
            $price = round($price, 2);
        }
        $product->set_regular_price($price);
        $product->set_price($price);
        $product->save();
        wc_delete_product_transients($product->get_id()); // Do something.
    }
}
add_action("pmxi_saved_post", "my_saved_post", 10, 3);
function getLabelDay(DateTime $date)
{
    $mesi = [
        1 => "gennaio",
        "febbraio",
        "marzo",
        "aprile",
        "maggio",
        "giugno",
        "luglio",
        "agosto",
        "settembre",
        "ottobre",
        "novembre",
        "dicembre",
    ];
    $giorni = [
        "domenica",
        "lunedì",
        "marted",
        "mercoledì",
        "giovedì",
        "venerdì",
        "sabato",
    ];
    list($sett, $giorno, $mese, $anno) = explode("-", $date->format("w-d-n-Y"));
    return ucwords($giorni[$sett] . " " . $giorno . " " . $mesi[$mese]);
}
function getNextLimitDate()
{
    $nextThursday = (new DateTime())->modify("next Wednesday");
    $nextThursday->setTime(12, 0);
    return $nextThursday;
}
function getAllDeliveryDates()
{
    //get last time I generated FN orders
    $orders = wc_get_orders([
        "limit" => -1,
        "status" => "completed",
        "orderby" => "date",
        "order" => "DESC",
        "meta_key" => "_subscription_id",
        "meta_compare" => "EXISTS",
    ]);
    $days = [];
    foreach ($orders as $order) {
        $days[] = $order->get_date_completed()->setTime(13, 0);
    }
    $days = array_unique($days);
    $days = array_values($days);
    return $days;
}
function getLastDeliveryDay()
{
    //get last time I generated FN orders
    $orders = wc_get_orders([
        "limit" => 1,
        "status" => "completed",
        "orderby" => "date",
        "order" => "DESC",
        "meta_key" => "_subscription_id",
        "meta_compare" => "EXISTS",
    ]);
    $lastDay = null;
    foreach ($orders as $order) {
        $lastDay = $order->get_date_completed();
    }
    return $lastDay;
}
