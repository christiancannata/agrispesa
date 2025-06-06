<?php
get_header('shop');
$getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
$negozioID = $getIDbyNAME->term_id;
?>

	<div class="woocommerce-products-header">
		<section class="big-search">
			<div class="big-search--content">
				<div class="big-search--text">
					<?php
					$allsearch = new WP_Query("s=$s&showposts=0");
					echo '<h1 class="big-search--h1">' . $allsearch->found_posts . ' risultati</h1>';
					?>
					<h3 class="big-search--title">Hai cercato:</h3>
				</div>
				<?php get_search_form() ?>
			</div>
		</section>


		<div class="all-categories">
			<?php
			$current_cat = get_queried_object();
			$getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
			$get_product_cat_ID = $getIDbyNAME->term_id;
			$getSpeciali = get_term_by('name', 'speciali', 'product_cat');
			$specialiID = $getSpeciali->term_id;
			$get_product_cat_ID = $getIDbyNAME->term_id;
			$cat_args = array(
				'orderby' => 'meta_value',
				'meta_key' => 'categories_order_agr',
				'order' => 'asc',
				'hide_empty' => false,
				'parent' => $get_product_cat_ID,
				'exclude' => $specialiID
			);

			$product_categories = get_terms('product_cat', $cat_args);

			$special_category = get_field('agr_special_category', 'option');
			$special_icon = get_field('agr_special_icon', 'option');
			$link = get_term_link($special_category, 'product_cat');
			$special = get_term_by('term_id', $special_category, 'product_cat');
			$special_name = $special->name;
			$special_slug = $special->slug;

			if (!empty($product_categories)) {
				$categoriesNumber = count($product_categories);

				if ($special_category) {
					$allCategoriesNr = $categoriesNumber + 1;
				} else {
					$allCategoriesNr = $categoriesNumber;
				}
				$calcWidth = 100 / $allCategoriesNr;

				echo '<ul class="all-categories--list">';
				if ($special_category) {
					echo '<li style="min-width:' . $calcWidth . '%;">';
					echo '<a href="' . $link . '" title="' . $special_name . '">';
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
					echo '<a href="' . get_term_link($category) . '" title="' . $category->name . '">';
					echo get_template_part('global-elements/icon', $category->slug);
					echo $category->name;
					echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			} ?>
		</div>
	</div>


<?php
$search_string = get_search_query();

$args = array(
	's' => $search_string,
	'post_status' => 'publish',
);

$search_posts = new WP_Query($args);
if ($search_posts->have_posts()) :

	$count_posts = new WP_Query($args);
	$posts_per_cat = $count_posts->found_posts; ?>

	<div class="negozio--flex">
		<div class="products-list-agr">

			<div class="products-list--header">
				<h3 class="products-list--title">Abbiamo trovato questi!</h3>
			</div>


			<div class="woocommerce">
				<div class="shop--list">

					<ul class="products">


						<?php while ($search_posts->have_posts()) : $search_posts->the_post();
							$product = wc_get_product(get_the_ID());
							$thumb_id = get_post_thumbnail_id();
							$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
							$thumb_url = $thumb_url_array[0];
							$title = get_the_title();
							$title_without_weight = preg_replace(
								array('/(kg\s\d+|ml\s\d+|cl\s\d+|g\s\d+|pz\s\d+|l\s\d+)/'),
								array(''),
								$title
							);
							$the_weight_array = getNumbersFromString($title);
							$i = 1;
							$weigth_nav = "";
							if (isset($the_weight_array) && !empty($the_weight_array)) {
								foreach ($the_weight_array as $the_weight) {
									if (isset($the_weight[0])) {
										$weigth_nav = $the_weight[0];
									} else {
										$weigth_nav = "";
									}

									if ($i === 1) {
										break;
									}
								}
							}


							if (empty($weigth_nav)) {
								$product_data = $product->get_meta('_woo_uom_input');
								$weigth_nav = $product_data . ' ' . $product->get_weight();
							}
							?>


							<li class="product remove-last-p">
								<a href="<?php the_permalink(); ?>" title="<?php echo the_title(); ?>"
								   class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
									<?php if ($thumb_id): ?>
										<img width="300" height="300" src="<?php the_post_thumbnail_url(); ?>"
											 class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
											 alt="<?php echo the_title(); ?>" decoding="async" loading="lazy"/>
									<?php else: ?>
										<img src="https://agrispesa.it/wp-content/uploads/2023/02/default.png"
											 class="product-box--thumb" alt="<?php echo esc_html($title); ?>"/>
									<?php endif; ?>

									<div class="product-loop-title-meta">
										<h6 class="woocommerce-loop-product__title"><?php echo $title_without_weight; ?></h6>
										<?php echo '<span class="product-info--quantity">' . $weigth_nav . '</span>'; ?>
									</div>

									<span class="price"><?php echo $product->get_price_html(); ?></span>

								</a>
								<?php echo do_shortcode('[add_to_cart id="' . get_the_ID() . '" show_price="false" quantity="1" style="border:none; padding:0;margin: 0;"]'); ?>
							</li>

						<?php endwhile;
						wp_reset_postdata(); ?>

					</ul>


					<div class="products-list--footer">
						<?php if ($posts_per_cat == 1) {
							$labelprodotti = ' prodotto';
						} else {
							$labelprodotti = ' prodotti';
						}
						echo '<span>' . $posts_per_cat . $labelprodotti . '</span>';
						?>
					</div>
				</div>
			</div>
		</div>

		<div class="negozio-sidebar">
			<ul class="negozio-sidebar--list">
				<?php $my_walker = new Walker_Category_Custom();

				$excludeSpecial = '';
				if ($special_category) {
					$excludeSpecial = '';
				} else {
					$excludeSpecial = $specialiID;
				}

				$sidebar = array(
					'taxonomy' => 'product_cat',
					'orderby' => 'name',
					//'orderby'  => 'meta_value',
					//'meta_key' => 'categories_order_agr',
					'order' => 'ASC',
					'show_count' => 0,
					'hierarchical' => 1,
					'hide_empty' => 1,
					'title_li' => '',
					'walker' => $my_walker,
					'exclude' => $excludeSpecial,
					'child_of' => $negozioID,
				);
				wp_list_categories($sidebar); ?>
			</ul>
		</div>


	</div>

<?php else : ?>


	<div class="not-found">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/images/empty/no-products.svg"
			 class="not-found--image" alt="Nessun risultato"/>
		<h2 class="not-found--title">Continua a scavare.</h2>
		<p class="not-found--subtitle">Ci dispiace, non abbiamo trovato niente.<br class="only-desktop"/> Prova a
			cambiare la tua ricerca.</p>
	</div>


<?php endif; ?>

<?php get_footer();
