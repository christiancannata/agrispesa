<?php
/* Template Name: Manifesto */

get_header(); ?>


<div class="wrapper" id="index-wrapper">


	<section class="manifesto--hero">
		<div class="manifesto--container">
				<h1 class="manifesto--hero--title">Manifesto</h1>
				<h2 class="manifesto--hero--subtitle">
					C’è ancora un’agricoltura contadina diffusa<br /> in tutte le regioni d’Italia.<br/>
					Si fonda sul lavoro di donne e uomini che in prima persona coltivano la terra e allevano gli animali, rispettandone caratteristiche e tempi di crescita.
				</h2>
		</div>
	</section>

	<section class="manifesto--video">
		<div class="videoWrapper">
			<video width="320" height="240" autoplay loop muted>
				<source src="<?php echo get_template_directory_uri(); ?>/assets/video/farmer-2.mp4" type="video/mp4">
			</video>
		</div>
	</section>

	<section class="manifesto--hero">
		<div class="manifesto--container">
				<h3 class="manifesto--hero--subtitle">
					In un mondo che si muove veloce, in cui siamo abituati a ricevere qualsiasi prodotto in 24 ore, noi scegliamo di cambiare ritmo: un ritmo più naturale. Più umano.
				</h3>
		</div>
	</section>


	<section class="manifesto--ritmi">
		<div class="manifesto--ritmi--flex">
			<div class="manifesto--ritmi--item bg-orange">
				<h4 class="manifesto--ritmi--item--title">
					Prima l'uomo.<br/>Poi il prodotto.
				</h4>
				<p class="manifesto--ritmi--item--text">
					Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
				</p>
			</div>
			<div class="manifesto--ritmi--item bg-brown">
				<h4 class="manifesto--ritmi--item--title">
					Il ciclo della natura <br/>e degli animali.
				</h4>
				<p class="manifesto--ritmi--item--text">
					Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
				</p>
			</div>
		</div>

	</section>

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