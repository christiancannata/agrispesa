<?php

//Remove payments from resume table
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
//Add payments methods after shipping address
add_action( 'woocommerce_checkout_payment_hook', 'woocommerce_checkout_payment', 10 ); 
