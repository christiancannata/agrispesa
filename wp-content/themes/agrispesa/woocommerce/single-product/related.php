<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
$terms = wp_get_post_terms( $post->ID, 'product_cat' );
foreach ( $terms as $term ) $categories[] = $term->slug;

if ( in_array( 'box', $categories ) ): ?>

<section class="faq">
<div class="container-small">
	<h3 class="faq--title">Ci chiedono spesso.</h3>
	<div class="faq--list">
		<?php //Loop FAQs
		$args = array(
		'post_type' => 'faq',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'order' => 'ASC',
		'tax_query' => array(
				array(
						'taxonomy'  => 'faq_cats',
						'terms'     =>  'petfood',
						'field'     => 'slug',
						'operator' => 'NOT IN'
				)
		)
		);

		$loop = new WP_Query( $args );
		$i = 1;
		while ( $loop->have_posts() ) : $loop->the_post(); ?>

				<article id="post-<?php the_ID(); ?>" class="faq__item <?php echo 'faq-'.$i; ?>">
					<header class="faq__content">
						<h2 class="faq__title"><a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>" class="faq__link"><span class="faq__icon icon-arrow-down"></span><?php the_title(); ?></a></h2>
						<div class="faq__description"><?php the_content(); ?></div>
					</header>
				</article>
		<?php $i++; endwhile; wp_reset_postdata(); ?>
	</div>
</div>
</section>

<?php elseif ( !in_array( 'gift-card', $categories ) ): ?>

	<?php if ( in_array( 'petfood', $categories ) ) : ?>
		<section class="landing-ingredients">
			<div class="landing-ingredients--top" data-aos="fade-up" data-aos-duration="600" data-aos-delay="50">
				<p class="landing-ingredients--minititle">Ingredienti buoni. Parola di Agrispesa.</p>
				<h3 class="landing-ingredients--megatitle">Non è magia.<br/>È natura.</h3>
			</div>
			<div class="landing-ingredients--flex">

			<div class="landing-ingredients--sx" data-aos="fade-uo" data-aos-duration="800" data-aos-delay="150">
				<img class="landing-meet--image sticky" src="<?php echo get_template_directory_uri(); ?>/assets/images/petfood/camomilla.svg" alt="Per ogni razza, per ogni tipetto." />
			</div>
			<div class="landing-ingredients--dx">
				<?php
				$args = array(
				  'post_type' => 'ingredienti',
				  'posts_per_page' => -1,
					'orderby' => 'post_title',
				  'order' => 'ASC',
				);
				$the_query = new WP_Query( $args );
				if ( $the_query->have_posts() ) : $i = 1;
				 while ( $the_query->have_posts() ) : $the_query->the_post();
				 $delay = 50 * $i;?>
					<div class="landing-ingredients--item" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?php echo $delay; ?>">
		        <h4 class="landing-ingredients--title">
							<?php the_title(); ?>
						</h4>
					</div>
	  		<?php $i++;  endwhile; endif; ?>
				<?php wp_reset_postdata(); ?>

				<div class="landing-ingredients--buttons" data-aos="fade-up" data-aos-duration="600" data-aos-delay="50">
					<a href="<?php echo esc_url(home_url('/ingredienti')); ?>" class="btn btn-primary" title="Scopri gli ingredienti">Scopri gli ingredienti</a>
				</div>
			</div>

			</div>
		</section>
	<?php endif;?>

	<?php if ( $related_products ) : ?>


		<section class="section-hero small">
			<div class="section-hero--container">
					<h4 class="section-hero--subtitle ">
						Potrebbero farti gola.
					</h4>
			</div>
		</section>
		<section class="products-carousel--container">
			<div class="products-carousel">
				<?php $i = 1;
						foreach ( $related_products as $related_product ) : ?>

						<?php
						$post_object = get_post( $related_product->get_id() );

						setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
						 ?>


						<?php get_template_part( 'template-parts/loop', 'shop' ); ?>



				<?php $i++; endforeach; ?>

			<?php //woocommerce_product_loop_end(); ?>
		</div>
	</section>
		<?php
	endif; ?>


	<section class="big-search">
	  <div class="big-search--content">
	    <div class="big-search--text">
	      <h3 class="big-search--title">Cerca i tuoi prodotti preferiti.</h3>
	    </div>
	    <?php get_search_form() ?>
	  </div>
	</section>

	<section class="all-categories">
	  <?php
	  $orderby = 'ID';
	    $order = 'ASC';
	    $hide_empty = false;

	    $getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
	    $get_product_cat_ID = $getIDbyNAME->term_id;
			$getSpeciali = get_term_by('name', 'speciali', 'product_cat');
	    $specialiID = $getSpeciali->term_id;
	    $cat_args = array(
					'orderby'  => 'meta_value',
					'meta_key' => 'categories_order_agr',
	        'order'      => $order,
	        'hide_empty' => $hide_empty,
	        'parent' => $get_product_cat_ID,
					'exclude' => $specialiID
	    );

	$product_categories = get_terms( 'product_cat', $cat_args );

	$special_category = get_field('agr_special_category', 'option');
	$special_icon = get_field('agr_special_icon', 'option');
	$link = get_term_link( $special_category, 'product_cat' );
	$special = get_term_by('term_id', $special_category, 'product_cat');
	$special_name = $special->name;
	$special_slug = $special->slug;

	if( !empty($product_categories) ){
		$categoriesNumber = count($product_categories);

	  if($special_category) {
	    $allCategoriesNr = $categoriesNumber + 1;
	  } else {
	    $allCategoriesNr = $categoriesNumber;
	  }
	  $calcWidth = 100 / $allCategoriesNr;

	    echo '<ul class="all-categories--list">';
			if($special_category) {
	      echo '<li style="min-width:'.$calcWidth.'%;">';
	      echo '<a href="'.$link.'" title="'.$special_name.'">';
	      if($special_icon == 'heart') {
	        echo get_template_part( 'global-elements/icon', 'heart' );
	      } else {
	        echo get_template_part( 'global-elements/icon', 'star' );
	      }
	      echo $special_name;
	      echo '</a>';
	      echo '</li>';
	    }
	    foreach ($product_categories as $key => $category) {
	       echo '<li style="min-width:'.$calcWidth.'%;">';
	        echo '<a href="'.get_term_link($category).'" title="'.$category->name.'">';
	        echo get_template_part( 'global-elements/icon', $category->slug );
	        echo $category->name;
	        echo '</a>';
	        echo '</li>';
	    }
	    echo '</ul>';
	} ?>
	</section>


<?php endif;


wp_reset_postdata(); ?>
