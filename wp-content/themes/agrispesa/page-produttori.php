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

		<section class="sec-home sec-cards bg-beige">
			<div class="container-pg">

				<div class="glossario">
      <div class="glossario--index">
        <?php $alphas = range('A', 'Z');
        foreach ( $alphas as $letter ) : ?>
          <a href="#<?php echo $letter; ?>" data-alpha="<?php echo $letter; ?>" class="glossario--link sliding-link disabled" title="<?php echo $letter; ?>"><?php echo $letter; ?></a>
        <?php endforeach; ?>
      </div>
    	<div class="glossario--rows">

    		<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

          <?php
          $title=get_the_title();
          $initial=strtoupper(substr($title,0,1));
          if($initial!=$letter) {
            echo "</div>";

            echo "<div id='".$initial."' data-alpha='".$initial."' class='glossario--anchor'>$initial</div>";
            echo "<div class='sec-cards--flex'>";
            $letter=$initial;
          } ?>

					<div class="sec-cards--item producer-box">
		        <h4 class="producer-box--title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
						<p class="producer-box--city"><?php echo the_field('produttore_provenienza'); ?></p>

						<?php $prod_categories = get_field('produttore_categorie_associate');
						if($prod_categories) {
								echo '<div class="producer-box--categories">';
								foreach ($prod_categories as &$value) {
									$category = get_term_by('id', $value->term_id, 'category');
									$term_link = get_term_link( $value );
									echo '<a class="arrow-link" href="' . $term_link . '" title="' . $value->name . '">' . $value->name . '</a>';
								}
								echo '</div>';
							} ?>
						</div>




    		<?php endwhile ?>
				<?php wp_reset_postdata(); ?>

    	</div>
  	</div>





				</div>
			</div>
		</section>
	<?php endif; ?>







</div>

<?php
get_footer();
