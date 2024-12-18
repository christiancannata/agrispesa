<?php
/* Template Name: Servizio Clienti */

get_header(); ?>


<div class="wrapper" id="index-wrapper">

	<section class="manifesto--hero">
		<div class="manifesto--container">
				<div class="manifesto--hero--title"><h1>Servizio clienti</h1></div>
				<h2 class="manifesto--hero--subtitle">
					<p>Crediamo nel valore della gentilezza e la pratichiamo.
					<br/>
					Accudiamo la relazione con tutte le persone, trattando le parole con serietà, come strumenti di conoscenza, di trasparenza e di dialogo.
					<br/>
					Quando commettiamo un errore ci scusiamo e, nel limite del possibile, rimediamo.</p>
				</h2>
		</div>
	</section>

	<section class="prices-page page-panna">
	  <div class="container-xsmall">
			<div class="prices--top">
        <h2 class="prices--title">Contattaci</h2>
      </div>
			<?php echo do_shortcode('[contact-form-7 id="379" title="Servizio Clienti"]'); ?>
</div>
</section>













</div>

<?php
get_footer();
