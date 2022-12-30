<?php
/* Template Name: Chi siamo */

get_header(); ?>


<div class="wrapper" id="index-wrapper">


	<?php get_template_part( 'global-elements/hero', 'page' ); ?>

	<section class="sec-home sec-three bg-beige">
	  <div class="sec-three--flex">
	    <div class="sec-three--sx">
	      <div class="sec-home--image">
	        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/farmers/home-1.jpg" class="sec-home--img" alt=".." />
	      </div>
	    </div>

	    <div class="sec-three--center">
	      <div class="sec-home--text">
	        <h3 class="sec-home--title medium">Offriamo prodotti buoni, coltivati <br class="only-desktop" />con cura, pensando all’ambiente e alla salute delle persone;<br class="only-desktop" /> se di origine animale, sono ottenuti <br class="only-desktop" />in allevamenti <br class="only-desktop" />che ne rispettano <br class="only-desktop" />la vita e la socialità.</h3>
	      </div>
	    </div>

	    <div class="sec-three--dx">
	      <div class="sec-home--image">
	        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/farmers/home-2.jpg" class="sec-home--rounded--img" alt=".." />
	      </div>
	    </div>
	  </div>
	</section>

	<section class="sec-home sec-half bg-orange">
	  <div class="sec-half--flex">
	    <div class="sec-half--sx">
	      <div class="sec-home--rounded">
	        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/farmers/porro.png" class="sec-home--rounded--floating" alt=".." />
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





</div>

<?php
get_footer();
