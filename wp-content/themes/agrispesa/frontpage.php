<?php /* Template Name: Homepage */

// Start the session
session_start();
$_SESSION['havesearch'] = false;
?>

<?php get_header(); ?>

<?php get_template_part( 'global-elements/hero', 'agrispesa' ); ?>

<section class="sec-home sec-wide bg-orange no-line">

    <div class="container-pg">

      <?php get_template_part( 'global-elements/steps', 'home' ); ?>

      <div class="sec-wide--content">
        <h2 class="sec-wide--text">
          Agrispesa è una selezione di prodotti di agricoltura contadina — ogni settimana a casa tua. Semplice.<br/>
          Verdura, frutta, uova, latte, formaggi, pesce e carne<br class="only-desktop"/> che rispettano la terra e la vita degli animali.
        </h2>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>box/facciamo-noi" class="btn btn-primary btn-big sec-wide--btn" title="Scegli la tua box">Scegli la tua box</a>
      </div>
    </div>

</section>

<div class="clearfix"></div>

<section class="manifesto--video">
  <div class="videoWrapper">
    <video width="320" height="240" autoplay loop muted>
      <source src="<?php echo get_template_directory_uri(); ?>/assets/video/farmer-4.mp4" type="video/mp4">
    </video>
  </div>
</section>


<section class="big-search">
  <div class="big-search--content">
    <div class="big-search--text">
      <h3 class="big-search--title">Cerca i tuoi prodotti preferiti.</h3>
    </div>
    <?php get_search_form() ?>
  </div>
</section>

<?php get_template_part( 'global-elements/all', 'categories' ); ?>


<section class="products-carousel-home--container">
  <div class="products-carousel-home--hero">
    <h4 class="products-carousel-home--title">Verdura di stagione.</h4>
    <p class="products-carousel-home--subtitle">È la terra che decide cosa regalarci. Noi ringraziamo.</p>
    <a href="<?php echo esc_url(home_url('/')); ?>negozio/verdura" class="arrow-link" title="Verdura di stagione">Vedi tutto <span class="icon-arrow-right"></span></a>
  </div>
  <div class="products-carousel-home--slider">

  <?php /* Prodotti */ ?>
  <?php

  $args = array(
          'posts_per_page' => '10',
          'product_cat' => 'verdura',
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

  </div>
</section>

<section class="products-carousel-home--container">


  <div class="products-carousel-home--slider">

  <?php /* Prodotti */ ?>
  <?php

  $args = array(
          'posts_per_page' => '10',
          'product_cat' => 'frutta',
          'post_type' => 'product',
  				'orderby' => 'date',
          'order' => 'ASC',
      );

  $query = new WP_Query( $args );
  if( $query->have_posts()) : while( $query->have_posts() ) : $query->the_post(); ?>

  <?php get_template_part( 'template-parts/loop', 'shop' ); ?>

  <?php endwhile;
  	wp_reset_postdata();
  endif; ?>

  </div>
  <div class="products-carousel-home--hero right">
    <h4 class="products-carousel-home--title">Dagli alberi,<br class="only-desktop"/> con amore.</h4>
    <p class="products-carousel-home--subtitle">La frutta più fresca, cresciuta nel rispetto delle api e di tutta la biodiversità del territorio.</p>
    <a href="<?php echo esc_url(home_url('/')); ?>negozio/frutta" class="arrow-link" title="Frutta fresca">Vedi tutto <span class="icon-arrow-right"></span></a>
  </div>
</section>


<section class="products-carousel-home--container">
  <div class="products-carousel-home--hero">
    <h4 class="products-carousel-home--title">I prodotti<br class="only-desktop"/> più amati</h4>
    <p class="products-carousel-home--subtitle">Quello che i nostri clienti comprano più spesso.<br/> Un motivo ci sarà.</p>
    <a href="<?php echo esc_url(home_url('/')); ?>negozio" class="arrow-link" title="Verdura di stagione">Vedi tutto <span class="icon-arrow-right"></span></a>
  </div>
  <div class="products-carousel-home--slider">

  <?php /* Prodotti */ ?>
  <?php

  $args = array(
          'posts_per_page' => '10',
          'product_cat' => 'negozio',
          'post_type' => 'product',
  				'orderby' => 'date',
          'order' => 'ASC',
          'meta_key' => 'total_sales',
          'orderby' => 'meta_value_num',
      );

  $query = new WP_Query( $args );
  if( $query->have_posts()) : while( $query->have_posts() ) : $query->the_post(); ?>

  <?php get_template_part( 'template-parts/loop', 'shop' ); ?>

  <?php endwhile;
  	wp_reset_postdata();
  endif; ?>

  </div>
</section>


<section class="manifesto--video">
  <div class="videoWrapper">
    <video width="320" height="240" autoplay loop muted>
      <source src="<?php echo get_template_directory_uri(); ?>/assets/video/cibo-per-cani.mp4" type="video/mp4">
    </video>
  </div>
</section>

<section class="manifesto--hero">
  <div class="manifesto--container">
      <h3 class="manifesto--hero--subtitle">
        Crediamo che nutrirsi bene sia il primo passo per essere felici. Per questo dedichiamo un'attenzione speciale anche ai vostri amici a quattro zampe.
      </h3>
  </div>
</section>


<section class="products-carousel-home--container">
  <div class="products-carousel-home--hero">
    <h4 class="products-carousel-home--title">Compagni speciali.</h4>
    <p class="products-carousel-home--subtitle">Alimenti naturali per cani e gatti. Una pappa sana anche per Fido.<br/>Bau.</p>
    <a href="<?php echo esc_url(home_url('/')); ?>negozio/animali" class="arrow-link" title="Per gli animali">Vedi tutto <span class="icon-arrow-right"></span></a>
  </div>
  <div class="products-carousel-home--slider">

  <?php /* Prodotti */ ?>
  <?php

  $args = array(
          'posts_per_page' => '10',
          'product_cat' => 'animali',
          'post_type' => 'product',
  				'orderby' => 'date',
          'order' => 'ASC',
      );

  $query = new WP_Query( $args );
  if( $query->have_posts()) : while( $query->have_posts() ) : $query->the_post(); ?>

  <?php get_template_part( 'template-parts/loop', 'shop' ); ?>

  <?php endwhile;
  	wp_reset_postdata();
  endif; ?>

  </div>
</section>




<?php get_template_part( 'global-elements/reviews', 'home' ); ?>
<?php get_template_part( 'global-elements/sospesa', 'home' ); ?>

<?php get_template_part( 'global-elements/home', 'press' ); ?>
<?php get_template_part( 'global-elements/home', 'newsletter' ); ?>
<?php get_template_part( 'global-elements/home', 'popup' ); ?>

<?php get_footer();
