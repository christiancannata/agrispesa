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
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>box/facciamo-noi" class="btn btn-primary" alt="Scopri le box">Scopri le box</a>
			</div>
		</div>
		<div class="how-works-page--item">
			<div class="how-works-page--item--text">
				<h2 class="how-works-page--title">Facciamo noi.</h2>
				<p class="how-works-page--description">Sarà la stagione a decidere che cosa si può mangiare, sarà la terra a decidere, Agrispesa inserisce nella tua scatola ciò che ogni contadino in quel momento produce e può vendere. Questa è la condizione affinchè sia possibile nutrirsi bene nel futuro, collaborando con la terra e instaurando un rapporto privilegiato di fiducia con alcuni contadini. </p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>box/facciamo-noi" class="btn btn-primary" alt="Scegli la tua box">Comincia subito</a>
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
				<p class="how-works-page--description">Arriviamo a casa tua, puntuali ed educati. Con il nostro furgone e sempre con lo stesso autista, oppure in bicicletta, con ragazzi regolarmente assunti e pagati il giusto.
<br/>Ogni spesa, per noi, corrisponde a una famiglia che quasi sempre conosciamo da tempo, con le proprie preferenze, abitudini, esigenze, caratteristiche.
 </p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>box/facciamo-noi" class="btn btn-primary" alt="Scegli la tua box">Non vedo l'ora!</a>
			</div>
		</div>
	</section>







</div>

<?php
get_footer();
