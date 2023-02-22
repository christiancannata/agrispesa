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

defined('ABSPATH') || exit;

get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action('woocommerce_before_main_content');

$description = get_the_archive_description();

$current_cat = get_queried_object();
$getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
$negozioID = $getIDbyNAME->term_id;
$getSpeciali = get_term_by('name', 'speciali', 'product_cat');
$specialiID = $getSpeciali->term_id;

?>
	<header class="woocommerce-products-header">
		<section class="big-search">
			<div class="big-search--content">
				<div class="big-search--text">
					<h1 class="big-search--h1"><?php woocommerce_page_title(); ?></h1>
					<?php if ($description) {
						echo '<h3 class="big-search--title">' . $description . '</h3>';
					} else {
						echo '<h3 class="big-search--title">La biodiversità è il nostro futuro.</h3>';
					} ?>

				</div>
				<?php get_search_form() ?>
			</div>
		</section>

		<div class="all-categories">
			<?php

			$cat_args = array(
				'orderby' => 'meta_value',
				'meta_key' => 'categories_order_agr',
				'order' => 'ASC',
				'hide_empty' => false,
				'parent' => $negozioID,
				'exclude' => $specialiID
			);

			$product_categories = get_terms('product_cat', $cat_args);

			$special_category = get_field('agr_special_category', 'option');
			$special_icon = get_field('agr_special_icon', 'option');
			$link = get_term_link($special_category, 'product_cat');
			$special = get_term_by('term_id', $special_category, 'product_cat');
			$special_name = ($special) ? $special->name : '';
			$special_slug = ($special) ? $special->slug : '';

			if (!empty($product_categories)) {
				$categoriesNumber = count($product_categories);
				if ($special_category) {
					$allCategoriesNr = $categoriesNumber + 1;
				} else {
					$allCategoriesNr = $categoriesNumber;
				}
				$fontSize = 'small';
				if ($allCategoriesNr > 8) {
					$fontSize = 'big';
				}
				$calcWidth = 100 / $allCategoriesNr;

				$taxonomy_name = 'product_cat';
				$queried_object = get_queried_object();
				$term_id = $queried_object->term_id;
				$parentcats = get_ancestors($term_id, 'product_cat');


				echo '<ul class="all-categories--list">';
				if ($special_category) {
					echo '<li style="min-width:' . $calcWidth . '%;">';
					if (!is_shop() && $current_cat->slug == $special->slug) {
						echo '<a href="' . $link . '" title="' . $special_name . '" class="current ' . $fontSize . '">';
					} elseif (in_array($special->term_id, $parentcats)) {
						echo '<a href="' . $link . '" title="' . $special_name . '" class="current ' . $fontSize . '">';
					} else {
						echo '<a href="' . $link . '" title="' . $special_name . '" class="' . $fontSize . '">';
					}
					if ($special_icon == 'heart') {
						echo get_template_part('global-elements/icon', 'heart');
					} else {
						echo get_template_part('global-elements/icon', 'star');
					}
					echo $special_name;
					echo '</a>';
					echo '</li>';
				}

				foreach ($product_categories as $key => $category) {
					echo '<li style="min-width:' . $calcWidth . '%;">';
					if (!is_shop() && $current_cat->slug == $category->slug) {
						echo '<a href="' . get_term_link($category) . '" title="' . $category->name . '" class="current ' . $fontSize . '">';
					} elseif (in_array($category->term_id, $parentcats)) {
						echo '<a href="' . get_term_link($category) . '" title="' . $category->name . '" class="current ' . $fontSize . '">';
					} else {
						echo '<a href="' . get_term_link($category) . '" title="' . $category->name . '" class="' . $fontSize . '">';
					}
					echo get_template_part('global-elements/icon', $category->slug);
					echo $category->name;
					echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			} ?>
		</div>
	</header>
<?php
if (woocommerce_product_loop()) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action('woocommerce_before_shop_loop');

	echo '<div class="negozio--flex">';
	echo '<div class="products-list-agr">';

	/* Category - SubCategory START */
	$term = get_queried_object();
	$term_id = $term->term_id;
	$parent_id = empty($term->term_id) ? 0 : $term->term_id;

	if($negozioID == $term_id) {
		$orderby = 'meta_value';
		$meta_key = 'categories_order_agr';
	} else {
		$orderby = 'name';
		$meta_key = '';
	}


	$loop_categories = get_categories(
		array(
			'taxonomy' => 'product_cat',
			'orderby' => $orderby,
			'meta_key' => $meta_key,
			'hide_empty' => 1,
			'parent' => $parent_id,
		)
	);

	if (empty($loop_categories)) {
		echo '<div class="products-list--header">';
		echo '<h3 class="products-list--title">' . $term->name . '</h3>';
		echo '</div>';
		woocommerce_product_loop_start();
		if (wc_get_loop_prop('total')) {
			while (have_posts()) {
				the_post();

				/**
				 * Hook: woocommerce_shop_loop.
				 *
				 * @hooked WC_Structured_Data::generate_product_data() - 10
				 */
				do_action('woocommerce_shop_loop');



				wc_get_template_part('content', 'product');
			}
		}
		woocommerce_product_loop_end();

	} else {

		$i = 1;
		foreach ($loop_categories as $loop_category) {

			if ($loop_category->category_count != 0) {
				echo '<div class="products-list--header">';
				echo '<h3 class="products-list--title">' . $loop_category->name . '</h3>';
				echo '</div>';
				woocommerce_product_loop_start(); //open ul
			}
			$args = array(
				'posts_per_page' => 5,
				'tax_query' => array(
					'relation' => 'AND',
					'hide_empty' => 1,
					'paged' => false,
					array(
						'taxonomy' => 'product_cat',
						'field' => 'slug',
						'terms' => $loop_category->slug
					),
				),
				'post_type' => 'product',
				'orderby' => 'menu_order',
				'order' => 'asc',
				'meta_query' => array(
					array(
						'key' => '_stock_status',
						'value' => 'instock'
					),
				)
			);
			$cat_query = new WP_Query($args);

			while ($cat_query->have_posts()) : $cat_query->the_post();

				wc_get_template_part('content', 'product');
			endwhile; // end of the loop.
			wp_reset_postdata();
			if ($loop_category->category_count != 0) {
				woocommerce_product_loop_end(); //close ul
				echo '<div class="products-list--footer">';

				if($loop_category->category_count == 1) {
					$labelprodotti = ' prodotto';
				} else {
					$labelprodotti = ' prodotti';
				}

				if($loop_category->category_count > 5) {
					echo '<span>5 di ' . $loop_category->category_count . $labelprodotti .'</span>';
					echo '<a href="'.get_term_link($loop_category->term_id).'" title="Visualizza tutto '.$loop_category->name.'" class="arrow-link">Vedi tutto <span class="icon-arrow-right"></span></a>';
				} else {
					echo '<span>' . $loop_category->category_count . $labelprodotti .'</span>';
				}

				echo '</div>';
			}
			if ($i < count($loop_categories) && $loop_category->category_count != 0)
				echo '<div class="products-list--separator"></div>';
			$i++;
		}//foreach
	}
	/* Category - SubCategory END */

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action('woocommerce_after_shop_loop');

	echo '</div>';
	echo '<div class="negozio-sidebar">';
	echo '<ul class="negozio-sidebar--list">';

	$my_walker = new Walker_Category_Custom();

	$excludeSpecial = '';
	if ($special_category) {
		$excludeSpecial = '';
	} else {
		$excludeSpecial = $specialiID;
	}

	$sidebar = array(
		'taxonomy'     => 'product_cat',
		// 'orderby'  => 'name',
		'orderby'  => 'meta_value',
		'meta_key' => 'categories_order_agr',
		'order'      => 'ASC',
		'show_count'   => 0,
		'hierarchical' => 1,
		'hide_empty'   => 1,
		'title_li'     => '',
		'walker' => $my_walker,
		'exclude' => $excludeSpecial,
		'child_of' => $negozioID,
		);
		wp_list_categories($sidebar);
		echo '</ul>';
	echo '</div>';
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action('woocommerce_no_products_found');
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
//do_action( 'woocommerce_sidebar' );


get_footer('shop');
