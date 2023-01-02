<?php /* Template Name: Homepage */

// Start the session
session_start();
$_SESSION['havesearch'] = false;
?>

<?php get_header(); ?>

<?php get_template_part( 'global-elements/hero', 'agrispesa' ); ?>

<section class="sec-home sec-wide bg-orange no-line">

    <div class="container-pg">
      <div class="sec-wide--content">
        <h2 class="sec-wide--text">
          Agrispesa è una selezione di prodotti di agricoltura contadina — sotto forma di scatola. Così, semplice.<br/>
          Verdura, frutta, uova, latte, formaggi, pesce e carne<br class="only-desktop"/> che rispettano la terra e la vita degli animali.
        </h2>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>prodotto/box" class="btn btn-primary sec-wide--btn" title="">Crea la tua box</a>
      </div>
    </div>

</section>

<div class="clearfix"></div>

<?php get_template_part( 'global-elements/steps', 'home' ); ?>


<div class="clearfix"></div>

<section class="sec-home sec-image line-white">
  <div class="sec-image--content" style="background-image:url('<?php echo get_template_directory_uri(); ?>/assets/images/farmers/galline.jpg');"></div>
</section>

<div class="clearfix"></div>

<?php get_template_part( 'global-elements/reviews', 'home' ); ?>

<section class="products-home">

<?php /* Box */ ?>
<?php

$args = array(
        'posts_per_page' => '8',
        'product_cat' => 'shop',
        'post_type' => 'product',
				'orderby' => 'date',
        'order' => 'ASC'
    );

$query = new WP_Query( $args );
if( $query->have_posts()) : while( $query->have_posts() ) : $query->the_post(); ?>

<?php get_template_part( 'template-parts/loop', 'shop' ); ?>

<?php endwhile;
	wp_reset_postdata();
endif; ?>

</section>



<?php get_footer();
