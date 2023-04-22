<?php
/* Template Name: Landing - Categoria */

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
$what_category_ID = get_field('agr_landing_category');
$what_faq_category_ID = get_field('agr_landing_faq_category');
$faq_mega_title = get_field('landing_cat_faq_title');
$landing_cat_quote_image = get_field('landing_cat_quote_image');

if($what_category_ID) {
	$what_category = get_term( $what_category_ID )->name;
} else {
	$what_category = 'Negozio';
}
?>


<div class="wrapper" id="index-wrapper">
	<?php if($what_category == 'Petfood'):?>
		<?php get_template_part( 'global-elements/hero', 'landing-dogs' ); ?>
	<?php else: ?>
			<?php get_template_part( 'global-elements/hero', 'landing-category' ); ?>
	<?php endif; ?>




	<?php get_template_part( 'global-elements/home', 'sections' ); ?>




	<section id="go-products" class="products-petfood--content" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0">

		<div class="products-petfood--loop">
			<h3 class="products-petfood--title">Buoni, come ciò che mangi tu.</h3>
				<div class="products-petfood">
				<?php $args = array(
			        'product_cat' => $what_category,
			        'posts_per_page' => 5,
			        'orderby' => 'rand'
			    );
			    $loop = new WP_Query($args);
			    $i = 1; while ($loop->have_posts()) : $loop->the_post();
			        global $product;?>
			        <?php get_template_part( 'template-parts/loop', 'petfood' ); ?>
			    <?php $i++; endwhile; ?>
			    <?php wp_reset_query(); ?>


				</div>
			</div>
		</section>



	<section class="landing-ingredients" data-aos="fade-in" data-aos-duration="600" data-aos-delay="50">
		<div class="landing-ingredients--top">
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

	<section class="landing-category--quote">
		<div class="container-pg">
			<div class="landing-category--quote--flex">
				<div class="landing-category--quote--text <?php if(!$landing_cat_quote_image) { echo 'big';}?>" data-aos="fade-up" data-aos-duration="600" data-aos-delay="0">
					<div class="landing-category--quote--text--quote">
						<?php $quote = get_field('landing_cat_quote'); ?>
						<?php if($quote):?>
							<h3><?php echo $quote; ?></h3>
						<?php endif;?>
					</div>
					<p>
						<?php echo the_field('landing_cat_quote_text');?>
					</p>
				</div>
				<?php if($landing_cat_quote_image): ?>
				<div class="landing-category--quote--image" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0">
					<div class="need-help">
						<span class="need-help--label">Bisogno di aiuto?<br/>Scrivici!</span>
					</div>
					<img src="<?php echo $landing_cat_quote_image;?>" alt="Tutti i vantaggi dei prodotti <?php echo $what_category?> " />
				</div>
				<?php endif;?>
			</div>
		</div>
	</section>

	<?php endif;?>


<?php if($what_category != 'Petfood'):?>
	<section id="go-products" class="landing-category--loop" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0">
		<h3 class="landing-category--loop--title">Abbiamo il prodotto giusto.</h3>
		<div class="container-big">
				<div class="products-carousel">
				<?php $args = array(
			        'product_cat' => $what_category,
			        'posts_per_page' => 6,
			        'orderby' => 'rand'
			    );
			    $loop = new WP_Query($args);
			    while ($loop->have_posts()) : $loop->the_post();
			        global $product; ?>
			        <?php get_template_part( 'template-parts/loop', 'shop' ); ?>
			    <?php endwhile; ?>
			    <?php wp_reset_query(); ?>
				</div>
			</div>
		</section>
<?php endif;?>





		<?php if(!get_field('landing_cat_hide_faq' && $what_faq_category_ID)):
			$faq_category = array_column($what_faq_category_ID, 'slug');?>
			<section class="faq">
			  <div class="container-small">
					<?php if($faq_mega_title):?>
			    	<h3 class="faq--title" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0"><?php echo $faq_mega_title;?></h3>
					<?php else: ?>
						<h3 class="faq--title" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0">Ci chiedono spesso.</h3>
					<?php endif; ?>
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
                    'terms'     =>  $faq_category,
                    'field'     => 'slug'
                )
            )
			      );

			      $loop = new WP_Query( $args );
			      $i = 1;
			      while ( $loop->have_posts() ) : $loop->the_post();
						$delay = 50 * $i;
						?>

			          <article id="post-<?php the_ID(); ?>" class="faq__item <?php echo 'faq-'.$i; ?>"  data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?php echo $delay; ?>">
			            <header class="faq__content">
			              <h2 class="faq__title"><a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>" class="faq__link"><span class="faq__icon icon-arrow-down"></span><?php the_title(); ?></a></h2>
			              <div class="faq__description"><?php the_content(); ?></div>
			            </header>
			          </article>
			      <?php $i++; endwhile; wp_reset_postdata(); ?>
			    </div>
			  </div>
			</section>

		<?php endif;?>


</div>

<?php
get_footer();
