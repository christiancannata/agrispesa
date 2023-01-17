<?php
/* Template Name: Prezzi */

get_header(); ?>


<div class="wrapper" id="index-wrapper">


	<?php get_template_part( 'global-elements/hero', 'page' ); ?>

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
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>prodotto/box" class="btn btn-primary" alt="Crea la tua box">Crea la tua box</a>
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
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>prodotto/box" class="btn btn-primary" alt="Crea la tua box">Crea la tua box</a>
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
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>prodotto/box" class="btn btn-primary" alt="Crea la tua box">Crea la tua box</a>
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
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>prodotto/box" class="btn btn-primary" alt="Crea la tua box">Crea la tua box</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>










</div>

<?php
get_footer();
