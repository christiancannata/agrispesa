<?php
session_start();
$_SESSION['ishome'] = false;

get_header(); ?>

<div class="search-results--header">
  <header class="page-header">
    <h1 class="page-title small">Hai cercato:</h1>
    <?php get_search_form() ?>
    <?php
    $allsearch = new WP_Query("s=$s&showposts=0");
    echo '<p class="search-results--found">' . $allsearch ->found_posts . ' risultati</p>';
    ?>
  </header><!-- .page-header -->

</div>


<?php
      $search_string = get_search_query();
      $args = array(
          's'              => $search_string,
          'orderby'        => 'date',
          'order'          => 'DESC',
      );

      $search_posts = new WP_Query( $args );
      if ( $search_posts->have_posts() ) : ?>

      <?php $_SESSION['havesearch'] = true; ?>

      <div class="articles-container append-posts">

        <?php while ( $search_posts->have_posts() ) : $search_posts->the_post(); ?>
            <?php get_template_part( 'template-parts/loop', 'posts' ); ?>
        <?php endwhile;
        wp_reset_postdata();?>

      </div>

      <?php

      if (  $wp_query->max_num_pages > 1 )
        echo '<div class="loadmore"><span class="loadmore--btn misha_loadmore">Carica altri</span></div>'; // you can use <a> as well
      ?>

      <?php else : ?>

        <?php $_SESSION['havesearch'] = false; ?>

        <div class="not-found">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/ill/not-found.png" class="not-found--image" alt="Nessun risultato" />
          <h2 class="not-found--title">Nessun risultato</h2>
          <p class="not-found--subtitle">Prova a cambiare la tua ricerca e ad utilizzare solo parole chiave</p>
        </div>

        <?php /* Potrebbero interessarti */ ?>
        <?php
        	$related = new WP_Query([
        		'post_type'      => 'post',
        	  'posts_per_page' => 3,
        	  'post_status'    => 'publish',
            'orderby' => 'date',
            'order'   => 'DESC'

        	]);

         if($related->have_posts()): ?>

         <div class="related">
           <h2 class="related--title small">Potrebbero interessarti</h2>
           <div class="related--flex">

          		<?php
          		while($related->have_posts()) : $related->the_post(); ?>

              <?php get_template_part( 'template-parts/loop', 'posts' ); ?>

            	<?php endwhile;	?>


            <?php wp_reset_postdata(); ?>

          </div>
        </div>
        <?php endif; ?>


      <?php endif; ?>

<?php get_footer();
