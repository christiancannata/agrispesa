<?php

get_header(); ?>

<div class="container-pg">
<header class="page-header">

</header><!-- .page-header -->

<section class="fogliospesa--hero">
  <div class="fogliospesa--hero--container">
    <?php
    the_archive_title( '<h1 class="fogliospesa--hero--title">', '</h1>' );
    ?>
  </div>
</section>


<div class="archive-blog--flex">
<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/loop', 'blog' ); ?>

		<?php endwhile ?>

<?php endif;?>

</div>
<div class="container-pg">



<?php get_footer();
