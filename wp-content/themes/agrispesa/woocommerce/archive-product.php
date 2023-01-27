<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>


<header class="woocommerce-products-header">

	<section class="big-search">
	  <div class="big-search--content">
	    <div class="big-search--text">
				<h1 class="big-search--h1"><?php woocommerce_page_title(); ?></h1>
				<?php if(the_archive_description()): ?>
		      <h3 class="big-search--title"><?php echo the_archive_description(); ?></h3>
				<?php else: ?>
					<h3 class="big-search--title">Freschissimi, come sempre.</h3>
				<?php endif; ?>
	    </div>
	    <?php get_search_form() ?>
	  </div>
	</section>



  <div class="all-categories">
    <?php
  		$current_cat = get_queried_object();
      $getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
      $get_product_cat_ID = $getIDbyNAME->term_id;
      $cat_args = array(
          'orderby'    => 'ID',
          'order'      => 'asc',
          'hide_empty' => false,
          'parent' => $get_product_cat_ID,
      );

  $product_categories = get_terms( 'product_cat', $cat_args );

  if( !empty($product_categories) ){
      echo '<ul class="all-categories--list">';
      foreach ($product_categories as $key => $category) {
          echo '<li>';
  				if( !is_shop() && $current_cat->slug == $category->slug) {
  					echo '<a href="'.get_term_link($category).'" title="'.$category->name.'" class="current">';
  				} else {
  	        echo '<a href="'.get_term_link($category).'" title="'.$category->name.'">';
  				}
          echo get_template_part( 'global-elements/icon', $category->slug );
          echo $category->name;
          echo '</a>';
          echo '</li>';
      }
      echo '</ul>';
  } ?>
</div>

</header>

<?php
if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );

	//woocommerce_product_loop_start();

	//Loop archivio
	$page_id = get_queried_object_id();
  $idNegozio = get_the_category_by_ID( $page_id );

	$hasNoChildren = get_term_children( $page_id, 'product_cat' );

	if ( is_shop() ) {
		$getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
		$get_product_cat_ID = $getIDbyNAME->term_id;
		$args = array(
			 'hide_empty' => true,
			 'fields' => 'slugs',
			 'taxonomy' => 'product_cat',
			 'parent' => $get_product_cat_ID,
			 'orderby'    => 'ID',
			 'order'      => 'asc',
		);
		$categories = get_terms( $args );
    foreach ( $categories as $category_slug ) {
       $term_object = get_term_by( 'slug', $category_slug , 'product_cat' );
       echo '<div class="shop--list">';
       echo '<div class="shop--list--header">';
       echo '<h2 class="shop--minititle">' . $term_object->name . '</h2>';
 			echo '<a href="' . $term_object->slug . '" title="Vedi tutto ' . $term_object->name . '" class="arrow-link">Vedi tutto<span class="icon-arrow-right"></span></a>';
 			echo '</div>';
       echo do_shortcode( '[products limit="-1" columns="1" category="' . $category_slug . '"]' );
       echo '</div>';
       wp_reset_postdata();
    }
	} elseif ( !empty( $hasNoChildren ) && !is_wp_error( $hasNoChildren ) ){
		//categorie che hanno sottocategorie
		if ( is_shop() || $idNegozio === 'Negozio' ) {
			$getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
		  $get_product_cat_ID = $getIDbyNAME->term_id;
			$args = array(
	       'hide_empty' => true,
	       'fields' => 'slugs',
	       'taxonomy' => 'product_cat',
	       'parent' => $get_product_cat_ID,
				 'orderby'    => 'ID',
				 'order'      => 'asc',
	    );
		}  elseif( is_product_category() || is_product_tag() ) {

			$page_id = get_queried_object_id();
		  $get_product_cat_ID = $page_id;

			$args = array(
	       'hide_empty' => true,
	       'fields' => 'slugs',
	       'taxonomy' => 'product_cat',
	       'child_of' => $get_product_cat_ID,
				 'orderby'    => 'ID',
				 'order'      => 'asc',
	    );
		}

   $categories = get_terms( $args );
   foreach ( $categories as $category_slug ) {
      $term_object = get_term_by( 'slug', $category_slug , 'product_cat' );
      echo '<div class="shop--list">';
      echo '<div class="shop--list--header">';
      echo '<h2 class="shop--minititle">' . $term_object->name . '</h2>';
			echo '<a href="' . $term_object->slug . '" title="Vedi tutto ' . $term_object->name . '" class="arrow-link">Vedi tutto<span class="icon-arrow-right"></span></a>';
			echo '</div>';
      echo do_shortcode( '[products limit="-1" columns="1" category="' . $category_slug . '"]' );
      echo '</div>';
      wp_reset_postdata();
   }

} else {
	//Categorie senza sottocategorie
	$nomeCategoria = get_term_by( 'id', $page_id, 'product_cat' );

	echo '<div class="shop--list">';
	echo '<div class="shop--list--header">';
	echo '<h2 class="shop--minititle">' . $nomeCategoria->name . '</h2>';
	echo '</div>';
	echo do_shortcode( '[products limit="-1" columns="1" category="' . $idNegozio . '"]' );
	echo '</div>';
	wp_reset_postdata();
}




	//woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
//do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
