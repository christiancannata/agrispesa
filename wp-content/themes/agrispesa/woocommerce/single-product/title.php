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

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


global $product;

global $post;
$terms = wp_get_post_terms($post->ID, 'product_cat');
foreach ($terms as $term) $categories[] = $term->slug;

$produttori = get_field('product_producer');
$attributes = $product->get_attributes();

// unità di misura personalizzata
$product_data = $product->get_meta('_woo_uom_input');
wc_print_notices();

if (in_array('petfood', $categories)) {
	echo '<div class="pawer-logo-badge">';
	echo get_template_part('global-elements/logo', 'pawer');
	echo '</div>';
}

$title = get_the_title();
$title_without_weight = preg_replace(
	array('/(kg\s\d+|ml\s\d+|cl\s\d+|g\s\d+|pz\s\d+|l\s\d+)/'),
	array(''),
	$title
);
$the_weight_array = getNumbersFromString($title);
$i = 1;
$weigth_nav = "";
if (isset($the_weight_array) && !empty($the_weight_array)) {
	foreach ($the_weight_array as $the_weight) {
		if (isset($the_weight[0])) {
			$weigth_nav = $the_weight[0];
		} else {
			$weigth_nav = "";
		}

		if ($i === 1) {
			break;
		}
	}
}

if (empty($weigth_nav)) {
	$weigth_nav = $product->get_weight() . ' g';
}


echo '<h1 class="product_title entry-title">' . $title_without_weight . '</h1>';
echo '<div class="product-info">';
echo '<span class="product-info--quantity">' . $weigth_nav . '</span>';


if ($produttori) {
	foreach ($produttori as $produttore) {
		echo '<span class="product-info--producer">/ <a href="' . get_permalink($produttore->ID) . '">' . get_the_title($produttore->ID) . '</a></span>';
	}
}

echo '</div>';
