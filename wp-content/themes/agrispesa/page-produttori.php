<?php
/* Template Name: Produttori */

get_header(); ?>


<div class="wrapper" id="index-wrapper">


	<?php get_template_part( 'global-elements/hero', 'page' ); ?>

	<section class="sec-home sec-full bg-orange line-white">
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


	<?php
	$args = array(
	    'post_type' => 'producer',
			'orderby' => 'post_title',
      'order' => 'ASC',
	    'posts_per_page' => -1
	);
	$the_query = new WP_Query( $args ); ?>

	<?php if ( $the_query->have_posts() ) : ?>

		<section class="sec-home sec-full bg-beige">
			<div class="sec-home--minititle">
				<h3>I nostri fornitori</h3>
			</div>
			<div class="sec-full--flex">
				<div class="sec-home--text list">

	    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
	        <h4><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
	    <?php endwhile; ?>

	    <?php wp_reset_postdata(); ?>

				</div>
			</div>
		</section>
	<?php endif; ?>







</div>

<?php
get_footer();
