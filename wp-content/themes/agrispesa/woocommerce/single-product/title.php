<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce\Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;
$produttori = get_field('product_producer');
$attributes = $product->get_attributes();

// unità di misura personalizzata
$product_data = $product->get_meta('_woo_uom_input');

the_title( '<h1 class="product_title entry-title">', '</h1>' );
echo '<div class="product-info">';
if ( $product->has_weight() ) {
	if($product_data) {
		echo '<span class="product-info--quantity">' . $product->get_weight() . ' '.$product_data.'</span>';
	} else {
		echo '<span class="product-info--quantity">' . $product->get_weight() . ' g</span>';
	}
}




if($produttori) {
	foreach( $produttori as $produttore ) {
		echo '<span class="product-info--producer">/ <a href="'.get_permalink( $produttore->ID ).'">'.get_the_title( $produttore->ID ).'</a></span>';
	}
}

echo '</div>';
