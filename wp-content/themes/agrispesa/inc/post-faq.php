<?php //***** Custom post: FAQ *****//
add_action( 'init', 'faq_posts_init' );
function faq_posts_init() {
  $theme = get_bloginfo("template_directory");
   $args = array(
       'labels' => array(
           'name' => 'FAQs',
           'singular_name' => 'FAQ',
           'add_new' => 'Aggiungi nuova',
           'add_new_item' => 'Aggiungi nuova FAQ',
           'edit_item' => 'Modifica FAQ',
           'new_item' => 'Nuova FAQ',
           'all_items' => 'Tutte le FAQs',
           'view_item' => 'VisualizzaFAQ',
           'search_items' => 'Cerca FAQ',
           'not_found' =>  'Nessuna FAQ trovata',
           'not_found_in_trash' => 'Nessuna FAQ trovata nel cestino',
           'parent_item_colon' => '',
           'menu_name' => 'FAQs'
       ),
       'public' => true,
       'publicly_queryable' => true,
       'show_ui' => true,
       'show_in_menu' => true,
       'query_var' => true,
       'rewrite' => array( 'slug' => 'faq' ),
       'capability_type' => 'post',
       'has_archive' => true,
       'hierarchical' => false,
       'menu_position' => null,
       'supports' => array( 'title', 'editor' ),
       'menu_position'=>4,
       'taxonomies' => ['faq_cats']
   );

   register_post_type( 'faq', $args );
}

add_action('init', function() {
	register_taxonomy('faq_cats', ['faq'], [
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
