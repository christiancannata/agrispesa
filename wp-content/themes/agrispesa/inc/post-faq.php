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
       //'taxonomies'=>array('category')
   );

   register_post_type( 'faq', $args );
}
