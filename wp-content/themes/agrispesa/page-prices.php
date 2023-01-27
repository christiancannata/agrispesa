<?php
/* Template Name: Prezzi */

get_header(); ?>


<div class="wrapper" id="index-wrapper">


	<section class="manifesto--hero">
		<div class="manifesto--container">
				<h1 class="manifesto--hero--title">Prezzi</h1>
				<h2 class="manifesto--hero--subtitle">
					Parlare di soldi non piace nemmeno a noi. Crediamo, però, sia importante riconoscere il valore delle cose e delle persone che stanno dietro le cose.
				</h2>
		</div>
	</section>

	<section class="manifesto--video">
		<div class="videoWrapper">
			<video width="320" height="240" autoplay loop muted>
				<source src="<?php echo get_template_directory_uri(); ?>/assets/video/farmer-3.mp4" type="video/mp4">
			</video>
		</div>
	</section>

	<section class="manifesto--ritmi">
		<div class="manifesto--ritmi--flex">
			<div class="manifesto--ritmi--item bg-orange">
				<h4 class="manifesto--ritmi--item--title">
					Nelle tasche della natura, degli animali<br class="only-desktop"/> e delle persone.
				</h4>
				<p class="manifesto--ritmi--item--text">
					Non smetteremo mai di dirlo: un prezzo onesto, non può essere basso.
					Diamo a tutti il rispetto che meritano. A partire da te, offrendoti prodotti genuini al miglior prezzo possibile, ma anche ai coltivatori e agli animali.
					Per questo, il 60% del prezzo copre il costo del prodotto, pagando il lavoro degli agricoltori.
					Il 40% del prezzo è quanto serve per rendere possibile un servizio più rispettoso della natura.
				</p>
			</div>
			<div class="manifesto--ritmi--item bg-brown">
				<h4 class="manifesto--ritmi--item--title">
					Meno plastica, per un mondo più pulito.
				</h4>
				<p class="manifesto--ritmi--item--text">
					Per natura cerchiamo, nel nostro piccolo, di invadere meno possibile il pianeta che ci ospita.
					<br/>
					Prediligiamo la carta, a discapito della plastica. Nelle grandi città, giovani ragazzi consegnano le vostre scatole a bordo di biciclette colorate.
					<br/>
					Per fare questo, richiediamo un contributo di €5 per le spese di consegna, ad eccezione delle province di Cuneo e di Asti: esplicitiamo questa voce in fattura e non vogliamo mascherarla nel prezzo, perché è cosa a sé, esiste e non si può ignorare
				</p>
			</div>
		</div>

	</section>

	<section class="manifesto--hero">
		<div class="manifesto--container">
				<h3 class="manifesto--hero--subtitle">
					Puoi fare la spesa acquistando i prodotti che preferisci. Altrimenti, puoi scegliere la nostra box settimanale.
					Riceverai prodotti di stagione ed altre cose buone. Tranquill*, la facciamo noi.
				</h3>
		</div>
	</section>


	<section class="prices-page">
	  <div class="container-pg">
			<div class="prices--top">
        <h2 class="prices--title"></h2>
      </div>
			<div class="prices">
				<div class="prices--flex">
					<div class="prices--item">
						<div class="prices--header">x-small</div>
						<div class="prices--element">
							Per 1 persona
						</div>
						<div class="prices--element">
							Vegana, vegetariana,<br/> con pesce o carne
						</div>
						<div class="prices--element">
							Arriva ogni settimana
						</div>
						<div class="prices--element">
							Spedizione €5
							<span class="info-shipping">La prima consegna è gratuita!</span>
						</div>
						<div class="prices--element price">
							<span class="symbol">€</span>26
						</div>
						<div class="prices--element final">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>box/facciamo-noi" class="btn btn-primary" alt="Scegli la tua box">Scegli la tua box</a>
						</div>
					</div>
					<div class="prices--item">
						<div class="prices--header">small</div>
						<div class="prices--element">
							Per 2 persone
						</div>
						<div class="prices--element">
							Vegana, vegetariana,<br/> con pesce o carne
						</div>
						<div class="prices--element">
							Arriva ogni settimana
						</div>
						<div class="prices--element">
							Spedizione €5
							<span class="info-shipping">La prima consegna è gratuita!</span>
						</div>
						<div class="prices--element price">
							<span class="symbol">€</span>38
						</div>
						<div class="prices--element final">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>box/facciamo-noi" class="btn btn-primary" alt="Scegli la tua box">Scegli la tua box</a>
						</div>
					</div>
					<div class="prices--item">
						<div class="prices--header">medium</div>
						<div class="prices--element">
							Per 3 o 4 persone
						</div>
						<div class="prices--element">
							Vegana, vegetariana,<br/> con pesce o carne
						</div>
						<div class="prices--element">
							Arriva ogni settimana
						</div>
						<div class="prices--element">
							Spedizione €5
							<span class="info-shipping">La prima consegna è gratuita!</span>
						</div>
						<div class="prices--element price">
							<span class="symbol">€</span>55
						</div>
						<div class="prices--element final">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>box/facciamo-noi" class="btn btn-primary" alt="Scegli la tua box">Scegli la tua box</a>
						</div>
					</div>
					<div class="prices--item">
						<div class="prices--header">large</div>
						<div class="prices--element">
							Per 4 persone o più
						</div>
						<div class="prices--element">
							Vegana, vegetariana,<br/> con pesce o carne
						</div>
						<div class="prices--element">
							Arriva ogni settimana
						</div>
						<div class="prices--element">
							Spedizione €5
							<span class="info-shipping">La prima consegna è gratuita!</span>
						</div>
						<div class="prices--element price">
							<span class="symbol">€</span>74
						</div>
						<div class="prices--element final">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>box/facciamo-noi" class="btn btn-primary" alt="Scegli la tua box">Scegli la tua box</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>










</div>

<?php
get_footer();
