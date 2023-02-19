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
  $menu_links['edit-address'] = __('Indirizzo', 'woocommerce');
  $menu_links['edit-account'] = __('Account', 'woocommerce');
  $menu_links['customer-logout'] = __('Esci', 'textdomain');

	return $menu_links;
}

//CSS Admin area
add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
  body.wp-admin .wp-list-table td.price .uom {
    display: none;
  }
  </style>';
}


// ADDING A CUSTOM COLUMN TITLE TO ADMIN PRODUCTS LIST
add_filter( 'manage_edit-product_columns', 'custom_product_column',8);
function custom_product_column($columns){
   //add columns
   $columns['weight'] = __( 'Peso','woocommerce'); // title
   return $columns;
}

// ADDING THE DATA FOR EACH PRODUCTS BY COLUMN (EXAMPLE)
add_action( 'manage_product_posts_custom_column' , 'custom_product_list_column_content', 10, 2 );
function custom_product_list_column_content( $column, $product_id ){
    global $post;

    // HERE get the data from your custom field (set the correct meta key below)
    $product_unit = get_post_meta( $product_id, '_woo_uom_input', true );
    $product_weight = get_post_meta( $product_id, '_weight', true );
    if( $product_weight) {
      $product_weight = $product_weight;
    } else {
      $product_weight = '-';
    }
    if( $product_unit) {
      $product_unit = $product_unit;
    } else {
      $product_unit = 'gr';
    }

    switch ( $column )
    {
        case 'weight' :
            echo $product_weight . ' ' .$product_unit; // display the data
            break;
    }
}
