<?php

//CSS Admin area
add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
  body.wp-admin .wp-list-table td.price .uom {
    display: none;
  }
  body.wp-admin .menu-top.ame-unclickable-menu-item {
    font-weight: bold;
    border-top: 1px solid #fff;
    padding: 12px 0 6px !important;
    cursor: default !important;
  }
  body.wp-admin .menu-top.ame-unclickable-menu-item:hover {
    color: #fff !important;
    box-shadow: none !important;
  }
  body.wp-admin .menu-top.ame-unclickable-menu-item .wp-menu-image {
    display: none;
  }
  body.wp-admin .menu-top.ame-unclickable-menu-item .wp-menu-name {
    padding-left: 10px !important;
    font-weight: bold;
    font-size: 16px;
  }

  body.wp-admin .select2-results__option .prodotto {
    font-weight: 600;
    display: block;
  }

  body.wp-admin .select2-results__option .option-flex {
    display: flex;
    justify-content: space-between;
  }
  body.wp-admin .select2-results__option .option-flex--sx {
    display: flex;
  }
  body.wp-admin .select2-results__option .prodotto {
    margin-right:16px;
  }
  body.wp-admin .select2-results__option .peso {
    color: #999;
  }
  body.wp-admin .select2-results__option .conf {
    color: #999;
  }
  body.wp-admin .select2-results__option .fornitore {
    display: block;
  }
  body.wp-admin #woocommerce-order-items .woocommerce_order_items_wrapper table.woocommerce_order_items th.line_tax {
    display: none !important;
  }
  </style>';
}
