<?php
/* Template Name: Account */
get_header(); ?>

<?php if ( have_posts() ) : ?>
  <div class="container-pg">
    <header class="page-header">
      <?php
      the_title( '<h1 class="page-title">', '</h1>' );
      ?>
    </header><!-- .page-header -->
		<?php while ( have_posts() ) : the_post(); ?>


        <?php the_content(); ?>


		<?php endwhile ?>
  </div>
<?php endif;?>


<?php get_footer();
