<?php
/* Template Name: Landing - Azienda */

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
$agr_landing_product = get_field('agr_landing_product');
$what_faq_category_ID = get_field('agr_landing_faq_category');
$faq_mega_title = get_field('landing_cat_faq_title');
$landing_cat_quote_image = get_field('landing_cat_quote_image');
$agr_landing_coupon = get_field('agr_landing_coupon');

if($agr_landing_coupon){
	$coupon = '&coupon-code='.$agr_landing_coupon;
} else {
	$coupon= "";
}


?>


<div class="wrapper" id="index-wrapper">

	<?php get_template_part( 'global-elements/hero', 'landing-category' ); ?>



	<?php if(!get_field('landing_cat_hide_sections')):?>

	<?php get_template_part( 'global-elements/home', 'sections' ); ?>
	<?php endif;?>

	<?php if(!get_field('landing_cat_hide_values')):?>

	<section class="landing-category">
		<div class="container-pg">
			<div class="landing-category--values">
				<div class="landing-category--values--image" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0">
					<img src="<?php echo the_field('landing_cat_values_image');?>" alt="Tutti i vantaggi di Agrispesa" />
				</div>
				<?php $i = 1; if( have_rows('landing_cat_values') ):
						echo '<div class="landing-category--values--list">';
				    while( have_rows('landing_cat_values') ) : the_row();
				    $title = get_sub_field('landing_cat_values_title');
				    $text = get_sub_field('landing_cat_values_subtitle');

						$delay = 50 * $i;
							?>

					<div class="landing-category--values--list--item" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?php echo $delay; ?>">
						<h3 class="landing-category--values--list--title"><?php echo $title; ?></h3>
						<p class="landing-category--values--list--subtitle"><?php echo $text; ?></p>
					</div>

			<?php $i++; endwhile;
					echo'</div>';
				endif; ?>

			</div>
		</div>
	</section>
	<?php endif;?>

<?php if(!get_field('landing_cat_hide_quote')): ?>
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
					<img src="<?php echo $landing_cat_quote_image;?>" alt="Tutti i vantaggi di Agrispesa" />
				</div>
				<?php endif;?>
			</div>
		</div>
	</section>
	<?php endif;?>




<section id="go-products" class="landing-category--loop" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0">
	<h3 class="landing-category--loop--title">Prova Agrispesa.</h3>
	<div class="container-big">
			<div class="landing-products">
				<?php if( $agr_landing_product ): ?>

    <?php foreach( $agr_landing_product as $post ):

        setup_postdata($post);
				$product = wc_get_product( $post );
				$thumb_id = get_post_thumbnail_id();
				$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
				$thumb_url = $thumb_url_array[0];
				// print_r($product->get_id());
				// print_r($product->get_sku());
				 ?>
				 <article class="product-box">
 				  <a href="<?php the_permalink(); ?>" class="product-box--link" title="<?php echo the_title(); ?>">
 				    <?php if($thumb_id):?>
 				      <img src="<?php the_post_thumbnail_url(); ?>" class="product-box--thumb" alt="<?php echo esc_html( $title ); ?>" />
 				    <?php else: ?>
 				      <img src="https://staging.agrispesa.it/wp-content/uploads/2023/02/default.png" class="product-box--thumb" alt="<?php echo esc_html( $title ); ?>" />
 				    <?php endif;?>
 				  </a>
 				  <div class="product-box--text">
 				    <div class="product-box--text--top">
 				      <h2 class="product-box--title"><a href="<?php the_permalink(); ?>" title="<?php echo $product->get_title(); ?>"><?php echo $product->get_title(); ?></a></h2>
							<div class="product-box--attributes">
								<span><?php echo $product->get_attribute('pa_dimensione');?></span>
								<span><?php echo $product->get_attribute('pa_tipologia');?></span>
							</div>
							<div class="product-box--price--flex">

 				        <div class="product-box--price">
 				          <?php echo $product->get_price_html(); ?>
 				        </div>
 				      </div>
							<a href="<?php echo esc_url(home_url('carrello?add-to-cart='.$product->get_id().'&quantity=1'. $coupon )); ?>" class="btn btn-primary btn-small">Abbonati</a>

 				      <?php// echo do_shortcode('[add_to_cart id="'.get_the_ID().'" show_price="false" class="btn-fake" quantity="1" style="border:none;"]');?>
 				    </div>
 				  </div>
 				</article>
    <?php endforeach; ?>

    <?php
    // Reset the global post object so that the rest of the page works correctly.
    wp_reset_postdata(); ?>
<?php endif; ?>

			</div>
		</div>
	</section>






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
