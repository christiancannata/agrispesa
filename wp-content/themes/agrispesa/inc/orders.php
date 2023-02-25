<?php


// // Add min value to the quantity field (default = 1)
// add_filter('woocommerce_quantity_input_min', 'min_decimal');
// function min_decimal($val) {
//     return 0.1;
// }
//
// // Add step value to the quantity field (default = 1)
// add_filter('woocommerce_quantity_input_step', 'nsk_allow_decimal');
// function nsk_allow_decimal($val) {
//     return 0.1;
// }

// Removes the WooCommerce filter, that is validating the quantity to be an int
remove_filter('woocommerce_stock_amount', 'intval');

// Add a filter, that validates the quantity to be a float
add_filter('woocommerce_stock_amount', 'floatval');


// Add custom column headers here
add_action('woocommerce_admin_order_item_headers', 'my_woocommerce_admin_order_item_headers');
function my_woocommerce_admin_order_item_headers() {
    echo '<th style="width:80px;" class="item_weight sortable">Peso</th>';
    echo '<th style="width:120px;" class="item_producer sortable" data-sort="string-ins">Produttore</th>';
    echo '<th class="item_measure sortable">PZ</th>';
    echo '<th class="item_conf sortable">Cod. Conf.</th>';
}

// Add custom column values here
add_action('woocommerce_admin_order_item_values', 'my_woocommerce_admin_order_item_values', 10, 3);
function my_woocommerce_admin_order_item_values($_product, $item, $item_id = null) {
  // Only for "line_item" items type, to avoid errors
    if( ! $item->is_type('line_item') ) return;
    // get the post meta value from the associated product
    $weight = get_post_meta($_product->get_id(), '_weight', 1);
    $uom = get_post_meta($_product->get_id(), '_woo_uom_input', 1);
    $conf = get_post_meta($_product->get_id(), '_codice_confezionamento', 1);
    $uom_acq = get_post_meta($_product->get_id(), '_uom_acquisto', 1);
    $qty_acq = get_post_meta($_product->get_id(), '_qty_acquisto', 1);

    $producer = get_post_meta($_product->get_id(), 'product_producer', true);
    $producerString = '';
    if (!empty($producer)) {
      $producer = reset($producer);
      $producer = get_post($producer);
      $producer = $producer->post_title;
    }
    if ( $weight ) {
      if($uom && $uom != 'gr') {
        $weight = $weight . ' ' . $uom;

      } else {
        if($weight == 1000) {
          $weight = ' 1 kg';
        } else {
          $weight = $weight . ' gr';
        }
      }
    } else {
      $weight = '-';
    }
    if($producer) {
      $producer = $producer;
    } else {
      $producer ='-';
    }
    if($conf) {
      $conf = $conf;
    } else {
      $conf ='-';
    }
    if($uom_acq) {
      $uom_acq = $uom_acq;
    } else {
      $uom_acq ='-';
    }

    // display the value
    echo '<td>' . $weight . '</td>';
    echo '<td>' . $producer . '</td>';
    echo '<td>' . $uom_acq . '</td>';
    echo '<td>' . $conf . '</td>';

}
