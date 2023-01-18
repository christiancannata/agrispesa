<?php

get_header(); ?>

<header class="page-header">
  <?php
  the_archive_title( '<h1 class="page-title">', '</h1>' );
  ?>
</header><!-- .page-header -->

<div class="articles-container append-posts">
<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/loop', 'posts' ); ?>

		<?php endwhile ?>

<?php endif;?>

</div>



<?php get_footer();
