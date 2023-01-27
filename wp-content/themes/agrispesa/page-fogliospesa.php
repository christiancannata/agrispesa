<?php
/* Template Name: Fogliospesa */

get_header(); ?>


<div class="wrapper" id="index-wrapper">
<div class="container-pg">





	<?php /* Sticky post */ ?>
<?php
	$wp_query = new WP_Query([
		'post_type'      => 'post',
	  'posts_per_page' => 1,
	  'post_status'    => 'publish',
    'orderby' => 'date',
    'order'   => 'DESC',
		'category_name' => 'storie',
	]);
 if($wp_query->have_posts()): ?>


	<div class="fogliospesa--sticky">
		<section class="fogliospesa--hero">
			<div class="fogliospesa--hero--container">
				<?php $today = date('Y');
							$what_week = date('W',strtotime($today));?>
				<h1 class="fogliospesa--hero--title">
					Fogliospesa <span class="what_week"><?php echo $what_week; ?></span>
				</h1>
			</div>
		</section>

		<?php
		$ids = array();
		while($wp_query->have_posts()) : $wp_query->the_post();
		$ids[] = get_the_ID();


    $thumb_id = get_post_thumbnail_id();
    $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'medium', true);
    $thumb_url = $thumb_url_array[0]; ?>

    <div class="fogliospesa--sticky--flex">

    <div class="fogliospesa--sticky--dx">
      <div class="fogliospesa--sticky--text">
        <div class="fogliospesa--sticky--text--top">
					<div class="categories-list">
						<?php
						$category = get_the_category();
						$first_category = $category[0];
						echo sprintf( '<a href="%s">%s</a>', get_category_link( $first_category ), $first_category->name );
						?>
					</div>
          <h2 class="fogliospesa--sticky--title"><a href="<?php the_permalink(); ?>" title="<?php echo the_title(); ?>"><?php echo the_title(); ?></a></h2>
          <p><?php echo the_excerpt(); ?></p>
      </div>
      <div class="fogliospesa--sticky--text--bottom">
        <div class="fogliospesa--sticky--data">
          <?php
          $post_date = get_the_date( 'j F Y' );
          echo '<p>'.$post_date.'</p>';?>
        </div>
      </div>
    </div>
    </div>
		<div class="fogliospesa--sticky--sx">
      <?php if ( has_post_thumbnail() ): ?>
				<a href="<?php the_permalink(); ?>" class="fogliospesa--sticky--thumb--link" title="<?php echo the_title(); ?>">
        <span class="fogliospesa--sticky--thumb" style="background-image: url(<?php the_post_thumbnail_url();?>);"></span>
				</a>
      <?php endif; ?>
    </div>

    </div>

  	<?php endwhile;	?>
	</div>

<?php wp_reset_postdata(); ?>
<?php endif; ?>



	<section class="fogliospesa--magazine">
		<div class="fogliospesa--magazine--top">
			<div class="magazine--slider">

				<?php $getIDbyNAME = get_term_by('name', 'ricette', 'category');
	      $ricette_ID = $getIDbyNAME->term_id;
				$remove_ricette = '-' . $ricette_ID;  ?>
			<?php /* Post in evidenza */ ?>
			<?php
				$wp_query = new WP_Query([
					'post_type'      => 'post',
					'posts_per_page' => 3,
					'post_status'    => 'publish',
					'orderby' => 'date',
					'order'   => 'DESC',
					'cat' => $remove_ricette,
					'post__not_in' => $ids

				]);

			 if($wp_query->have_posts()): ?>

					<?php
					while($wp_query->have_posts()) : $wp_query->the_post();
					$ids[] = get_the_ID(); ?>

					<?php get_template_part( 'template-parts/loop', 'blog' ); ?>

					<?php endwhile;	?>
				</div>

			<?php wp_reset_postdata(); ?>
			<?php endif; ?>

		</div>
		<div class="fogliospesa--magazine--flex">
			<div class="fogliospesa--magazine--sx">
				<?php /* Tutti i post */ ?>
				<?php
					$myquery = new WP_Query([
						'post_type'      => 'post',
					  'posts_per_page' => 3,
					  'post_status'    => 'publish',
				    'orderby' => 'date',
				    'order'   => 'DESC',
						'post__not_in' => $ids,
						'cat' => $remove_ricette
					]);
				 if($myquery->have_posts()): ?>


						<?php
						while($myquery->have_posts()) : $myquery->the_post();
				    $ids[] = get_the_ID(); ?>

				    <?php get_template_part( 'template-parts/loop', 'blog' ); ?>

				  	<?php endwhile;	?>


					<?php wp_reset_postdata(); ?>
					<?php endif; ?>
			</div>
			<div class="fogliospesa--magazine--dx">
				<div class="fogliospesa--ricette">
					<h4 class="fogliospesa--ricette--title">Ricettine gustose</h4>
					<?php /* Ricette */ ?>
				  <?php
				  $args = array(
				          'posts_per_page' => '10',
				          'category_name' => 'ricette',
				          'post_type' => 'post',
				  				'orderby' => 'date',
				          'order' => 'DESC',
									'post__not_in' => $ids,
				      );
				  $query = new WP_Query( $args );
				  if( $query->have_posts()) : while( $query->have_posts() ) : $query->the_post();
					$ids[] = get_the_ID(); ?>

					<article class="fogliospesa--ricette--post">
						<h2 class="fogliospesa--ricette--post--title">
							<a href="<?php the_permalink(); ?>" class="fogliospesa--ricette--post--link" title="<?php echo the_title(); ?>">
								<?php echo the_title(); ?>
							</a>
						</h2>
					</article>

				  <?php endwhile;
				  	wp_reset_postdata();
				  endif; ?>
				</div>
			</div>
		</div>
	</section>





</div>
</div>

<?php
get_footer();
