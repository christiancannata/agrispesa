<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked wc_empty_cart_message - 10
 */
do_action( 'woocommerce_cart_is_empty' );

if ( wc_get_page_id( 'shop' ) > 0 ) : ?>


<div class="error-404">
	<img src="<?php echo get_template_directory_uri(); ?>/assets/images/box/box-sizes.png" class="error-404--image big" alt="Riempi la tua scatola" />
	<h2 class="error-404--title">Ooops, la tua scatola è vuota.</h2>
	<p class="error-404--subtitle">Che ne dici di tornare al negozio?</p>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>negozio" title="Fai la spesa" class="btn btn-primary">Fai la spesa!</a>
</div>
<?php endif; ?>
