<?php
/* Template Name: Chi siamo */

get_header(); ?>


<div class="wrapper" id="index-wrapper">


	<section class="sec-home sec-framed no-line">
		<div class="container-pg">
			<div class="sec-framed--intro">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/about/agrispesa-conosciamoci.jpg" class="sec-framed--img" alt=".." />
				<h1 class="sec-home--title medium sec-framed--title">In questa foto sono insieme ai nonni,<br/> ai miei genitori, a mia sorella e al mio cane.<br/>
					Era il 1988, avevo 5 anni.</h1>
			</div>
		</div>
	</section>

	<section class="sec-home sec-wide bg-brown">

	    <div class="container-pg">
	      <div class="sec-wide--content">
					<p class="sec-wide--text">
						Mi chiamo Giovanna.
					</p>
	        <p class="sec-wide--text">
	          <br/>La campagna, i nonni, gli animali: questo è stato il mio “imprinting”, direbbe Konrad Lorenz. L’amore per gli animali e per il mondo contadino mi hanno segnata.
	        </p>
					<p class="sec-wide--text">
						<br/>Mi occupo principalmente di curare ogni dettaglio legato al servizio che le famiglie ricevono da parte di Agrispesa e sento che questo è apprezzato, percepito e riconosciuto.
					</p>
					<p class="sec-wide--text">
						<br/>Da mia mamma ho imparato a trattare le parole con serietà, come strumenti di conoscenza, di trasparenza e di dialogo.<br/>
						Da mio papà a riconoscere la qualità dei prodotti e a tenerli tra le mie mani in modo delicato.
					</p>



	      </div>
	    </div>

	</section>

	<section class="sec-home sec-three bg-panna">
	  <div class="sec-three--flex">
	    <div class="sec-three--sx" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/about/farmer-2.jpg);"></div>

	    <div class="sec-three--center">
	      <div class="sec-home--text">
						<?php $today = date('Y');
									$born = 2003;
									$years = $today - $born;?>
						<h3 class="sec-home--title big">Da <em class="number"><?php echo $years; ?></em> anni<br class="only-desktop" /> al servizio della terra.</h3>
						<p class="sec-home--subtitle">Portiamo nelle vostre case prodotti freschi e genuini. Sarà la stagione a decidere che cosa.</p>
	      </div>
	    </div>

	    <div class="sec-three--dx" style="background-image: url(<?php echo get_template_directory_uri(); ?>/assets/images/about/farmer-1.jpg);"></div>
	  </div>
	</section>

	<section class="sec-home sec-half bg-orange">
	  <div class="sec-half--flex">
	    <div class="sec-half--sx">
	      <div class="sec-home--rounded">
	        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/about/farmer-top.png" class="sec-home--rounded--floating" alt="Conosciamo da anni i contadini che ci forniscono i prodotti" />
	        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/about/farmer-bottom.png" class="sec-home--rounded--img" alt="Conosciamo da anni i contadini che ci forniscono i prodotti" />
				</div>
	    </div>

	    <div class="sec-half--dx">
				<div class="sec-home--text">
 				 <h3 class="sec-home--title big">Conosciamo <br class="only-desktop" />da anni i contadini che ci forniscono <br class="only-desktop" />i prodotti.</h3>
				 <p class="sec-home--subtitle">Concordiamo con loro un programma di acquisto, in modo da integrare l’economia di vendita diretta delle loro aziende.</p>
 			 </div>
	    </div>
	  </div>
	</section>

	<?php get_template_part( 'global-elements/home', 'press' ); ?>





</div>

<?php
get_footer();
