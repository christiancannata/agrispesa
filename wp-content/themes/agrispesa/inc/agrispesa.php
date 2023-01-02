<?php

//Aggiungi funzionalità Woocommerce
add_theme_support( 'woocommerce' );

// Change add to cart text on product archives page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives' );
function woocommerce_add_to_cart_button_text_archives() {
    return __( 'Aggiungi alla box', 'woocommerce' );
}


// Lunghezza Riassunto
function my_excerpt_length($length){
return 15;
}
add_filter('excerpt_length', 'my_excerpt_length');


// Titoli pagine di categoria
add_filter( 'get_the_archive_title', function ($title) {
    if ( is_category() ) {
            $title = single_cat_title( '', false );
        } elseif ( is_tag() ) {
            $title = single_tag_title( '', false );
        } elseif ( is_author() ) {
            $title = '<span class="vcard">' . get_the_author() . '</span>' ;
        } elseif ( is_tax() ) { //for custom post types
            $title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
        }
    return $title;
});



//Custom menu
function footer_menu() {
	register_nav_menus(
		array(
			'footer_menu_one' => __( 'Footer Menu 1' ),
			'footer_menu_two' => __( 'Footer Menu 2' ),
			'footer_menu_three' => __( 'Footer Menu 3' ),
			'user_menu' => __( 'User menu' )
		)
	);
}

add_action( 'init', 'footer_menu' );


//Remove downloads from account
function custom_my_account_menu_items( $items ) {
    unset($items['downloads']);
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'custom_my_account_menu_items' );
