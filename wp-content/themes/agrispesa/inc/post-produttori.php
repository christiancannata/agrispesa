<?php

//***** Custom post: CORSI *****//

add_action( 'init', 'producers_posts_init' );
function producers_posts_init() {
    $theme = get_bloginfo("template_directory");
    $args = array(
        'labels' => array(
            'name' => 'Produttori',
            'singular_name' => 'Produttore',
            'add_new' => 'Aggiungi nuovo',
            'add_new_item' => 'Aggiungi produttore',
            'edit_item' => 'Modifica produttore',
            'new_item' => 'Nuovo produttore',
            'all_items' => 'Tutti i produttori',
            'view_item' => 'Visualizza produttore',
            'search_items' => 'Cerca produttori',
            'not_found' =>  'Nessun produttore trovato',
            'not_found_in_trash' => 'Nessun produttore trovato nel cestino',
            'parent_item_colon' => '',
            'menu_name' => 'Produttori'
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'produttori' ),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'menu_position'=>5,
        //'taxonomies'=>array('category')
    );

    register_post_type( 'producer', $args );
}


// Ordina per alfabeto
// function alpha_order_classes( $query ) {
//     if ( $query->is_post_type_archive('producer') && $query->is_main_query() ) {
//         $query->set('meta_key', 'producer_lastname');
//         $query->set('orderby', 'meta_value');
//         $query->set( 'order', 'ASC' );
//     }
// }
//
// add_action( 'pre_get_posts', 'alpha_order_classes' );
