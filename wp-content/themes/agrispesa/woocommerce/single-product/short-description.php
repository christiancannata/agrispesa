<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;

$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
$long_description = wp_trim_words( $post->post_content, 48, '...' );


?>
<div class="woocommerce-product-details__short-description">

	<?php if ($short_description ) {
		echo $short_description;
	} elseif($long_description){
		echo '<p>' . $long_description . '</p>';
	}?>
	<?php if(($short_description || $long_description) && $post->post_title != 'Facciamo noi'): ?>
		<a href="#read-description" class="arrow-link product-description-button scroll-to">Scopri di più<span class="icon-arrow-down"></span></a>
	<?php endif;?>
</div>
