<?php
/* Template Name: Layout Colonna */
get_header(); ?>


<?php if ( have_posts() ) : ?>
  <div class="container-pg">
    <header class="page-header">
      <?php
      the_title( '<h1 class="page-title">', '</h1>' );
      ?>
    </header><!-- .page-header -->
		<?php while ( have_posts() ) : the_post(); ?>

      <div class="single--content">
        <?php the_content(); ?>
      </div>

		<?php endwhile ?>
  </div>
<?php endif;?>


<?php get_footer();
