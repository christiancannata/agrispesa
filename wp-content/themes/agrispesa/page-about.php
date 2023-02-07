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

<div class="agr-section--container no-mg-top">
	<section class="agr-section agr-section--right">
		<div class="agr-section--flex">
			<div class="agr-section--image" data-aos="fade-right" data-aos-duration="700" data-aos-delay="50">
				<img src="https://admin.illo.tv/storage/uploads/2020/06/11/5ee2612a8ebb8GiovannaCrise_01.jpg" class="main-img" alt="Giovanna Traversa" />
			</div>
		<div class="agr-section--text" data-aos="fade-left" data-aos-duration="700" data-aos-delay="50" style="background-color:#765341; color:#e5d7c8;">
			<div class="agr-section--text--content">
				<p class="team--bio--text">
					Il mio nome è Giovanna.
				</p>
				<p class="team--bio--text">
					<br/>La campagna, i nonni, gli animali: questo è stato il mio “imprinting”, direbbe Konrad Lorenz. L’amore per gli animali e per il mondo contadino mi hanno segnata.
				</p>
				<p class="team--bio--text">
					<br/>Mi occupo principalmente di curare ogni dettaglio legato al servizio che le famiglie ricevono da parte di Agrispesa e sento che questo è apprezzato, percepito e riconosciuto.
				</p>
				<p class="team--bio--text">
					<br/>Da mia mamma ho imparato a trattare le parole con serietà, come strumenti di conoscenza, di trasparenza e di dialogo.<br/>
					Da mio papà a riconoscere la qualità dei prodotti e a tenerli tra le mie mani in modo delicato.
				</p>
			</div>
		</div>
		</div>
	</section>

	<section class="agr-section">
		<div class="agr-section--flex">
		<div class="agr-section--text" data-aos="fade-left" data-aos-duration="700" data-aos-delay="50" style="background-color:#e8532b; color:#e5d7c8;">
			<div class="agr-section--text--content">
				<p class="team--bio--text">
					La capacità di resistere, la volontà, la tenacia mi arrivano da mia madre Pierina, da Lina, da zia Rina, dalla balia Clarin, da Suor Angela Bologna: donne forti che hanno accompagnato la mia crescita, alle quali devo il senso della mia vita.
				</p>
				<p class="team--bio--text">
					<br/>Sono nata il 25 luglio 1953. Mia madre, per qualche calcolo sbagliato, mi attese per undici mesi. Ad Alba la conoscevano tutti, l’anciuèra. Quattro giorni la settimana, per 40 anni, al mercato c’era lei, con tenacia, con le mani rotte dal freddo e dal sale.
				</p>
				<p class="team--bio--text">
					<br/>Il mio nome fu per tutti una sorpresa: avere una bimba era una speranza che la mamma non osava coltivare.
				</p>
				<p class="team--bio--text">
					<br/>Mi chiamò Elena.
				</p>
			</div>
		</div>
		<div class="agr-section--image" data-aos="fade-right" data-aos-duration="700" data-aos-delay="50">
			<img src="https://admin.illo.tv/storage/uploads/2020/11/27/5fc0db722bf4fLiliana.jpg" class="main-img" alt="Elena Rovera" />
		</div>
		</div>
	</section>

	<section class="agr-section" data-aos="fade-up" data-aos-duration="700" data-aos-delay="50">
		<div class="agr-section--wide" style="background-color:#069460; color:#e5d7c8;">
		<div class="agr-section--wide--flex">
			<div class="agr-section--image">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/about/da-20-anni-2.jpg" class="mini-img" alt="Da 20 anni al servizio della terra" />
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/about/da-20-anni.jpg" class="main-img" alt="Da 20 anni al servizio della terra" />
			</div>
		<div class="agr-section--text">
			<div class="agr-section--text--content">
				<?php $today = date('Y');
							$born = 2003;
							$years = $today - $born;?>
				<h3 class="agr-section--title">Da <span class="number"><?php echo $years; ?></span> anni siamo<br class="only-desktop" /> al servizio della terra.</h3>
				<p class="agr-section--subtitle">
					Ne abbiamo viste di tutti colori e di tutte le forme.<br/>Sì, abbiamo fatto la nostra prima consegna a domicilio nel 2003: prima dei social, delle stories, dell'unboxing.<br/>Eppure abbiamo sempre idee freschissime.
				</p>
				<a href="<?php echo esc_url(home_url('/')); ?>manifesto" class="btn btn-primary" title="Leggi il nostro manifesto">Leggi il nostro manifesto</a>

			</div>
		</div>
		</div>
		</div>
	</section>

</div>

	<section class="manifesto--video">
		<div class="videoWrapper">
			<video width="320" height="240" autoplay loop muted>
				<source src="<?php echo get_template_directory_uri(); ?>/assets/video/farmer-3.mp4" type="video/mp4">
			</video>
		</div>
	</section>

	<?php get_template_part( 'global-elements/home', 'press' ); ?>





</div>

<?php
get_footer();
