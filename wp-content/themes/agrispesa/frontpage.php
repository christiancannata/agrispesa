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


<section class="products-carousel--container">
  <div class="products-carousel--intro">
    <h2 class="products-carousel--title">Oppure creala tu!</h2>
  </div>
  <div class="products-carousel">

  <?php /* Box */ ?>
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

<section class="all-categories">
  <?php
  $orderby = 'ID';
    $order = 'asc';
    $hide_empty = false;

    $getIDbyNAME = get_term_by('name', 'negozio', 'product_cat');
    $get_product_cat_ID = $getIDbyNAME->term_id;
    $cat_args = array(
        'orderby'    => $orderby,
        'order'      => $order,
        'hide_empty' => $hide_empty,
        'parent' => $get_product_cat_ID,
    );

$product_categories = get_terms( 'product_cat', $cat_args );

if( !empty($product_categories) ){
    echo '

<ul class="all-categories--list">';
    foreach ($product_categories as $key => $category) {
        echo '

<li>';
        echo '<a href="'.get_term_link($category).'" title="'.$category->name.'">';
        echo get_template_part( 'global-elements/icon', $category->slug );
        echo $category->name;
        echo '</a>';
        echo '</li>';
    }
    echo '</ul>


';
} ?>
</section>


<?php get_template_part( 'global-elements/reviews', 'home' ); ?>

<?php get_footer();
