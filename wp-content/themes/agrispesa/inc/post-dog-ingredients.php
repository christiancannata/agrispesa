<?php //***** Custom post: Ingredient *****//
add_action( 'init', 'ingredienti_posts_init' );
function ingredienti_posts_init() {
  $theme = get_bloginfo("template_directory");
   $args = array(
       'labels' => array(
           'name' => 'Ingredienti',
           'singular_name' => 'Ingrediente',
           'add_new' => 'Aggiungi nuovo',
           'add_new_item' => 'Aggiungi nuovo Ingrediente',
           'edit_item' => 'Modifica Ingrediente',
           'new_item' => 'Nuovo Ingrediente',
           'all_items' => 'Tutti gli Ingredienti',
           'view_item' => 'Visualizza Ingrediente',
           'search_items' => 'Cerca Ingrediente',
           'not_found' =>  'Nessun Ingrediente trovato',
           'not_found_in_trash' => 'Nessun Ingrediente trovato nel cestino',
           'parent_item_colon' => '',
           'menu_name' => 'Ingredienti Cani'
       ),
       'public' => true,
       'publicly_queryable' => true,
       'show_ui' => true,
       'show_in_menu' => true,
       'query_var' => true,
       'rewrite' => array( 'slug' => 'ingredienti' ),
       'capability_type' => 'post',
       'has_archive' => true,
       'hierarchical' => false,
       'menu_position' => null,
       'supports' => array( 'title', 'editor', 'thumbnail' ),
       'menu_position'=>4,
       'taxonomies' => ['ingredienti_cats']
   );

   register_post_type( 'ingredienti', $args );
}

add_action('init', function() {
	register_taxonomy('ingredienti_cats', ['ingredienti'], [
		'label' => __('Categorie', 'txtdomain'),
		'hierarchical' => false,
		'rewrite' => ['slug' => 'supporto'],
		'show_admin_column' => true,
		'show_in_rest' => true,
		'labels' => [
			'singular_name' => __('Categoria', 'txtdomain'),
			'all_items' => __('Tutte le Categorie', 'txtdomain'),
			'edit_item' => __('Modifica Categoria', 'txtdomain'),
			'view_item' => __('Visualizza Categoria', 'txtdomain'),
			'update_item' => __('Aggiorna Categoria', 'txtdomain'),
			'add_new_item' => __('Aggiungi nuova Categoria', 'txtdomain'),
			'new_item_name' => __('Nuova Categoria', 'txtdomain'),
			'search_items' => __('Cerca Categorie', 'txtdomain'),
			'popular_items' => __('Categorie popolari', 'txtdomain'),
			'separate_items_with_commas' => __('Separa le Categorie con una virgola', 'txtdomain'),
			'choose_from_most_used' => __('Scegli tra le Categorie più usate', 'txtdomain'),
			'not_found' => __('Nessuna categoria trovata', 'txtdomain'),
		]
	]);
});
