<?php get_header(); ?>


<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post();?>

<div class="the_project">




</div>

<?php endwhile ?>
<?php endif; //end post?>

<?php get_footer();
