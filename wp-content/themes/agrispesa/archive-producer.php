<?php get_header(); ?>


<div class="wrapper" id="index-wrapper">

	<?php if ( have_posts() ) : ?>



		<section class="manifesto--hero">
			<div class="manifesto--container">
					<h1 class="manifesto--hero--title">Produttori</h1>
					<h2 class="manifesto--hero--subtitle">
						Conosciamo da anni i contadini che ci forniscono <br />i prodotti: lavorano sentendosi parte della natura, con esperienza e coscienza, per questo te li presentiamo.
					</h2>
			</div>
		</section>

		<?php
		$args = array(
		    'post_type' => 'producer',
				'orderby' => 'post_title',
	      'order' => 'ASC',
		    'posts_per_page' => -1
		);
		$the_query = new WP_Query( $args ); ?>

		<?php if ( $the_query->have_posts() ) : ?>

			<section class="sec-home sec-cards bg-beige">
				<div class="container-pg">

				<div class="glossario">
	      <div class="glossario--index">
	        <?php $alphas = range('A', 'Z');
	        foreach ( $alphas as $letter ) : ?>
	          <a href="#<?php echo $letter; ?>" data-alpha="<?php echo $letter; ?>" class="glossario--link sliding-link disabled" title="<?php echo $letter; ?>"><?php echo $letter; ?></a>
	        <?php endforeach; ?>
	      </div>
	    	<div class="glossario--rows">

	    		<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

	          <?php
	          $title=get_the_title();
	          $initial=strtoupper(substr($title,0,1));
	          if($initial!=$letter) {
	            echo "</div>";

	            echo "<div id='".$initial."' data-alpha='".$initial."' class='glossario--anchor'>$initial</div>";
	            echo "<div class='sec-cards--flex'>";
	            $letter=$initial;
	          } ?>

						<div class="sec-cards--item producer-box">
			        <h4 class="producer-box--title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
							<?php $producer_citta = get_field('producer_citta');
										$producer_provincia = get_field('producer_provincia');

							if($producer_citta || $producer_provincia): ?>
								<p class="producer-box--city">
									<?php if($producer_citta): ?>
										<span><?php echo $producer_citta; ?></span>
									<?php endif; ?>
									<?php if($producer_provincia): ?>
										<span><?php echo '(' . $producer_provincia . ')'; ?></span>
									<?php endif; ?>
									</p>
							<?php endif; ?>

							<?php $prod_categories = get_field('produttore_categorie_associate');
							if($prod_categories) {
									echo '<div class="producer-box--categories">';
									foreach ($prod_categories as &$value) {
										$category = get_term_by('id', $value->term_id, 'category');
										$term_link = get_term_link( $value );
										echo '<a class="arrow-link" href="' . $term_link . '" title="' . $value->name . '">' . $value->name . '</a>';
									}
									echo '</div>';
								} ?>
							</div>




	    		<?php endwhile ?>
					<?php wp_reset_postdata(); ?>

	    	</div>
	  	</div>





					</div>
				</div>
			</section>
		<?php endif; ?>


		<section class="section-hero">
			<div class="section-hero--container">
					<h4 class="section-hero--subtitle">
						Storie di agricoltura contadina e di artigianato locale.
					</h4>
			</div>
		</section>

		<section class="magazine">
			<div class="magazine--slider">
			<?php /* Blog */ ?>
		  <?php
		  $args = array(
		          'posts_per_page' => '3',
		          'category_name' => 'storie',
		          'post_type' => 'post',
		  				'orderby' => 'date',
		          'order' => 'DESC'
		      );
		  $query = new WP_Query( $args );
		  if( $query->have_posts()) : while( $query->have_posts() ) : $query->the_post(); ?>

		  <?php get_template_part( 'template-parts/loop', 'blog' ); ?>

		  <?php endwhile;
		  	wp_reset_postdata();
		  endif; ?>
			</div>
		</section>



	<?php else : ?>

		<?php get_template_part( 'loop-templates/content', 'none' ); ?>

	<?php endif; ?>

</div>




<?php get_footer(); ?>
