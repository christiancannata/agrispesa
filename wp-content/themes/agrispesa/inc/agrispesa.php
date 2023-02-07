<?php

//Aggiungi funzionalità Woocommerce
add_theme_support( 'woocommerce' );

// Change add to cart text on product archives page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives' );
function woocommerce_add_to_cart_button_text_archives() {
    return __( 'Acquista', 'woocommerce' );
}

//Options page ACF
add_action('acf/init', 'my_acf_op_init');
function my_acf_op_init() {

    // Check function exists.
    if( function_exists('acf_add_options_page') ) {

        // Add parent.
        $parent = acf_add_options_page(array(
            'page_title'  => __('Impostazioni generali Agrispesa'),
            'menu_title'  => __('Agrispesa'),
            'redirect'    => false,
        ));

        // Add sub page.
        $child = acf_add_options_page(array(
            'page_title'  => __('Impostazioni Newsletter'),
            'menu_title'  => __('Newsletter'),
            'parent_slug' => $parent['menu_slug'],
        ));

        // Add sub page.
        $child = acf_add_options_page(array(
            'page_title'  => __('Impostazioni Striscia informativa'),
            'menu_title'  => __('Striscia informativa'),
            'parent_slug' => $parent['menu_slug'],
        ));

        // Add sub page.
        $child = acf_add_options_page(array(
            'page_title'  => __('Impostazioni Press'),
            'menu_title'  => __('Press'),
            'parent_slug' => $parent['menu_slug'],
        ));
    }
}

// Lunghezza Riassunto
function mytheme_custom_excerpt_length( $length ) {
    return 15;
}
add_filter( 'excerpt_length', 'mytheme_custom_excerpt_length', 999 );

function new_excerpt_more( $more ) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

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
			'mini_menu' => __( 'Mini menu' ),
			'footer_menu_one' => __( 'Footer Menu 1' ),
			'footer_menu_two' => __( 'Footer Menu 2' ),
			'footer_menu_three' => __( 'Footer Menu 3' ),
			'user_menu' => __( 'User menu' )
		)
	);
}

add_action( 'init', 'footer_menu' );


//Rename account endpoints
add_filter ( 'woocommerce_account_menu_items', 'misha_remove_my_account_links' );
function misha_remove_my_account_links( $menu_links ){

	unset( $menu_links['downloads'] ); // Disable Downloads

  $menu_links['gift-cards'] = __('Carte Regalo', 'textdomain');
  $menu_links['customer-logout'] = __('Esci', 'textdomain');

	return $menu_links;
}
