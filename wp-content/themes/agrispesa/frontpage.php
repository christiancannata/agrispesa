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
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>prodotto/box" class="btn btn-primary btn-big sec-wide--btn" title="Scegli la tua box">Scegli la tua box</a>
      </div>
    </div>

</section>


<div class="clearfix"></div>



<section class="negozio-home">

  <div class="negozio-home--container">
    <div class="negozio-home--product">
      <?php /* In evidenza */ ?>
      <?php

      $args = array(
              'posts_per_page' => '1',
              'product_cat' => 'negozio',
              'post_type' => 'product',
      				'orderby' => 'date',
              'order' => 'ASC',
              'meta_key'          => 'sticky_product',
          );

      $query = new WP_Query( $args );
      if( $query->have_posts()) : while( $query->have_posts() ) : $query->the_post(); ?>

      <?php get_template_part( 'template-parts/loop', 'sticky' ); ?>

      <?php endwhile;
      	wp_reset_postdata();
      endif; ?>
    </div>
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

<section class="products-carousel--container">
  <div class="products-carousel--intro">
    <h2 class="products-carousel--title">Oppure creala tu!</h2>
  </div>
  <div class="products-carousel">

  <?php /* Prodotti */ ?>
  <?php

  $args = array(
          'posts_per_page' => '8',
          'product_cat' => 'negozio',
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

<?php get_template_part( 'global-elements/reviews', 'home' ); ?>
<?php get_template_part( 'global-elements/sospesa', 'home' ); ?>

<?php get_footer();
