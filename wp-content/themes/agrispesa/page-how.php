<?php
/* Template Name: Come funziona */

get_header(); ?>


<div class="wrapper" id="index-wrapper">


	<?php get_template_part( 'global-elements/hero', 'page' ); ?>

	<section class="how-works-page">
		<div class="how-works-page--item">
			<div class="how-works-page--item--img">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/how/step-1.avif" alt="Scegli la tua box" />
			</div>
			<div class="how-works-page--item--text">
				<h2 class="how-works-page--title">Scegli la tua box.</h2>
				<p class="how-works-page--description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>prodotto/box" class="btn btn-primary" alt="Scopri le box">Scopri le box</a>
			</div>
		</div>
		<div class="how-works-page--item">
			<div class="how-works-page--item--text">
				<h2 class="how-works-page--title">Facciamo noi.</h2>
				<p class="how-works-page--description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>prodotto/box" class="btn btn-primary" alt="Scegli la tua box">Comincia subito</a>
			</div>
			<div class="how-works-page--item--img">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/how/step-1.avif" alt="Scegli la tua box" />
			</div>
		</div>
		<div class="how-works-page--item">
			<div class="how-works-page--item--img">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/how/step-1.avif" alt="Scegli la tua box" />
			</div>
			<div class="how-works-page--item--text">
				<h2 class="how-works-page--title">Aspetta la spesa.</h2>
				<p class="how-works-page--description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>prodotto/box" class="btn btn-primary" alt="Scegli la tua box">Non vedo l'ora!</a>
			</div>
		</div>
	</section>







</div>

<?php
get_footer();
