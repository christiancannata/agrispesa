<?php
/* Template Name: Recensioni */

get_header(); ?>


<div class="wrapper" id="index-wrapper">


	<?php get_template_part( 'global-elements/hero', 'page' ); ?>

	<!-- TrustBox widget - Micro Review Count -->
	<div class="trustpilot-widget" data-locale="it-IT" data-template-id="5419b6a8b0d04a076446a9ad" data-businessunit-id="61ee37eca9b063488e95b01d" data-style-height="24px" data-style-width="100%" data-theme="light" data-min-review-count="10" data-style-alignment="center">
		<a href="https://it.trustpilot.com/review/www.agrispesa.it" target="_blank" rel="noopener">Trustpilot</a>
	</div>
	<!-- End TrustBox widget -->
	<section class="review-page">

		<div class="container-small">

			<?php
			if( have_rows('lista_recensioni') ):
					while( have_rows('lista_recensioni') ) : the_row();
					$review_name = get_sub_field('review_name');
					$review_quote = get_sub_field('review_quote');
					$review_title = get_sub_field('review_title');
					$review_star = get_sub_field('review_stars'); //Valore immagine
					?>

			<div class="review-page__item" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100" data-aos-offset="-100"> <!-- Inizio recensione -->

				<div class="review-page--flex">
					<div class="review-page__sx">
						<div class="review-page--valutation">
			        <div class="review-page--valutation--flex">
			          <div class="review-page--stars">
			            <span class="icon-star <?php if($review_star == 1 || $review_star > 1 ) { echo 'yellow'; } ?>"></span>
			            <span class="icon-star <?php if($review_star > 1) { echo 'yellow'; } ?>"></span>
			            <span class="icon-star <?php if($review_star > 2) { echo 'yellow'; } ?>"></span>
			            <span class="icon-star <?php if($review_star > 3) { echo 'yellow'; } ?>"></span>
			            <span class="icon-star <?php if($review_star > 4) { echo 'yellow'; } ?>"></span>
			          </div>
			          <div class="review-page--points">
			            <span><?php echo $review_star;?></span>
			            <span class="total">/5</span>
			          </div>
			        </div>
			        <div class="review-page--disclaimer">
			          <p><?php echo $review_name;?></p>
			        </div>
						</div>
			    </div>
					<div class="review-page__dx">
						<h2 class="review-page__title"><?php echo $review_title;?></h2>
						<div class="review-page__description"><p><?php echo $review_quote;?></p></div>
					</div>
				</div>

			</div> <!-- Fine recensione -->

			<?php endwhile; endif; ?>


	</div>
</section>




</div>

<?php
get_footer();
