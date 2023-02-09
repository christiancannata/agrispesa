<?php
/* Template Name: Manifesto */

get_header(); ?>


<div class="wrapper" id="index-wrapper">

	<?php get_template_part( 'global-elements/home', 'sections' ); ?>
	

	<section class="sec-home sec-full bg-green line-white">
		<div class="container-pg">
			<div class="sec-home--minititle">
				<h3>Le caratteristiche dei prodotti di Agrispesa:</h3>
			</div>

			<div class="sec-full--flex">
				<div class="sec-home--text pseudo-list">
				<span>sono di agricoltura contadina</span>
				<span>sono ottenuti in quantità limitate</span>
				<span>variano sempre, di lotto in lotto, di stagione in stagione</span>
				<span>sono semplici e di qualità</span>
				<span>sono freschissimi</span>
				<span>sono ottenuti con materie prime prodotte nell'azienda agricola</span>
				<span>sono riconducibili con facilità al luogo di produzione</span>
				<span>sono coltivati da agricoltori che hanno a cuore la salvaguardia dell'ambiente e la salute delle persone</span>
				<span>sono spesso di varietà locali</span>
				<span>se di origine animale, provengono da allevamenti nei quali gli animali non sono sottoposti a trattamenti farmacologici e nei periodi di ricovero in stalla sono alimentati con fieno, cereali e legumi prodotti dall’azienda stessa o da piccole aziende del territorio</span>
				<span>sono italiani</span>
	 		 </div>
			</div>
		</div>
	</section>

	<section class="section-hero">
		<div class="section-hero--container">
				<h4 class="section-hero--subtitle">
					Ne parliamo meglio.
				</h4>
		</div>
	</section>

	<section class="magazine">
		<div class="magazine--slider">
		<?php /* Blog */ ?>
	  <?php
	  $args = array(
	          'posts_per_page' => '3',
	          'category_name' => 'manifesto',
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





</div>

<?php
get_footer();
