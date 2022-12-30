<?php
session_start();

//Load more
add_action( 'wp_enqueue_scripts', 'misha_my_load_more_scripts' );

function misha_my_load_more_scripts() {

	global $wp_query;

	// In most cases it is already included on the page and this line can be removed
	wp_enqueue_script('jquery');

	// register our main script but do not enqueue it yet
	wp_register_script( 'my_loadmore', get_stylesheet_directory_uri() . '/assets/js/loadmore.js', array('jquery') );

	// now the most interesting part
	// we have to pass parameters to myloadmore.js script but we can get the parameters values only in PHP
	// you can define variables directly in your HTML but I decided that the most proper way is wp_localize_script()

	wp_localize_script( 'my_loadmore', 'misha_loadmore_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
		'posts' => json_encode( $wp_query->query_vars ), // everything about your loop is here
		'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
		'max_page' => $wp_query->max_num_pages,

		'posts_home' => json_encode( $myquery->query_vars ), // everything about your loop is here
		'max_page_home' => $myquery->max_num_pages,

	) );

 	wp_enqueue_script( 'my_loadmore' );

}



//Carica altri articoli
add_action('wp_ajax_loadmore', 'misha_loadmore_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_loadmore', 'misha_loadmore_ajax_handler'); // wp_ajax_nopriv_{action}

function misha_loadmore_ajax_handler(){

	// prepare our arguments for the query
	$args = json_decode( stripslashes( $_POST['query'] ), true );
	if( $_SESSION['ishome'] == true ) {
		$args['paged'] = $_POST['page']; // we need next page to be loaded
	} else {
		$args['paged'] = $_POST['page'] + 1; // we need next page to be loaded
	}
	$args['post_status'] = 'publish';
	$args['post_type'] = 'post';
	$args['post_per_page'] = 3;
	$args['orderby'] = 'date';
	$args['order'] = 'DESC';

	if( $_SESSION['ishome'] == true ) {
		$args['post__not_in'] = $_SESSION['myhiddenposts'];
	}

	// it is always better to use WP_Query but not here
	query_posts( $args );

	if( have_posts() ) :

		// run the loop
		while( have_posts() ): the_post();
			$ids[] = get_the_ID();

			// look into your theme code how the posts are inserted, but you can use your own HTML of course
			// do you remember? - my example is adapted for Twenty Seventeen theme
			get_template_part( 'template-parts/loop', 'posts' );
			// for the test purposes comment the line above and uncomment the below one
			// the_title();


		endwhile;

	endif;
	die; // here we exit the script and even no wp_reset_query() required!
}
