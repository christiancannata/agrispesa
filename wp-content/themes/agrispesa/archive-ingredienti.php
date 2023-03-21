<?php get_header(); ?>


<div class="wrapper" id="index-wrapper">

	<?php if ( have_posts() ) : ?>

		<section class="landing-ingredients archive-ingredients">
			<div class="landing-ingredients--top">
				<p class="landing-ingredients--minititle">Ingredienti buoni. Parola di Agrispesa.</p>
				<h3 class="landing-ingredients--megatitle">Non è magia.<br/>È natura.</h3>
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

			<section class="sec-home sec-cards bg-green">
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

	            //echo "<div id='".$initial."' data-alpha='".$initial."' class='glossario--anchor'>$initial</div>";
	            echo "<div id='".$initial."' data-alpha='".$initial."' class='glossario--anchor sec-cards--container'>";
	            $letter=$initial;
	          } ?>

						<div class="sec-cards--item wide ingredient-box">
							<div class="ingredient-box--flex">
				        <h4 class="ingredient-box--title">
									<?php the_title(); ?>
								</h4>
							</div>
							<div class="ingredient-box--descr">
								<?php echo the_content();?>
							</div>
						</div>

	    		<?php endwhile ?>
					<?php wp_reset_postdata(); ?>

	    	</div>
	  	</div>





					</div>
				</div>
			</section>
		<?php endif; ?>


		<section id="go-products" class="products-petfood--content" data-aos="fade-in" data-aos-duration="800" data-aos-delay="0">

			<div class="products-petfood--loop">
					<div class="products-petfood">
					<?php $args = array(
				        'product_cat' => 'Petfood',
				        'posts_per_page' => 5,
				        'orderby' => 'rand'
				    );
				    $loop = new WP_Query($args);
				    $i = 1; while ($loop->have_posts()) : $loop->the_post();
				        global $product;?>
				        <?php get_template_part( 'template-parts/loop', 'petfood' ); ?>
				    <?php $i++; endwhile; ?>
				    <?php wp_reset_query(); ?>


					</div>
				</div>
			</section>



	<?php else : ?>

		<?php get_template_part( 'loop-templates/content', 'none' ); ?>

	<?php endif; ?>

</div>




<?php get_footer(); ?>
