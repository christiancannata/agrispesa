<?php get_header(); ?>


<div class="wrapper" id="index-wrapper">

	<?php if ( have_posts() ) : ?>



		<section class="manifesto--hero">
			<div class="manifesto--container">
				<div class="manifesto--hero--title">
					<h1>Produttori</h1>
				</div>
					<div class="manifesto--hero--subtitle">
						<h2>Conosciamo da anni i contadini che ci forniscono <br />i prodotti: lavorano sentendosi parte della natura, con esperienza e coscienza, per questo te li presentiamo.</h2>
					</div>

			</div>
		</section>

		<?php
		$args = array(
		  'post_type' => 'ingredienti',
		  'posts_per_page' => -1,
			'orderby' => 'post_title',
		  'order' => 'ASC',
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
	            echo "<div class='sec-cards--container'>";
	            $letter=$initial;
	          } ?>

						<div class="sec-cards--item wide ingredient-box">
							<div class="ingredient-box--flex">

				        <h4 class="ingredient-box--title">
									<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
								</h4>
								<?php echo the_post_thumbnail();?>
							</div>

								<p class="ingredient-box--descr">
									<?php echo the_content();?>
								</p>

							</div>




	    		<?php endwhile ?>
					<?php wp_reset_postdata(); ?>

	    	</div>
	  	</div>





					</div>
				</div>
			</section>
		<?php endif; ?>


		<section id="go-products" class="landing-category--loop" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0">
			<h3 class="landing-category--loop--title">Abbiamo il prodotto giusto.</h3>
			<div class="container-big">
					<div class="products-carousel">
					<?php $args = array(
				        'product_cat' => 'Petfood',
				        'posts_per_page' => 6,
				        'orderby' => 'rand'
				    );
				    $loop = new WP_Query($args);
				    while ($loop->have_posts()) : $loop->the_post();
				        global $product; ?>
				        <?php get_template_part( 'template-parts/loop', 'shop' ); ?>
				    <?php endwhile; ?>
				    <?php wp_reset_query(); ?>
					</div>
				</div>
			</section>



	<?php else : ?>

		<?php get_template_part( 'loop-templates/content', 'none' ); ?>

	<?php endif; ?>

</div>




<?php get_footer(); ?>
