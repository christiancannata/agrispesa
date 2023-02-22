<?php get_header(); ?>


<div class="wrapper" id="index-wrapper">

	<?php if ( have_posts() ) : ?>


		<section class="manifesto--hero">
			<div class="manifesto--container">
					<div class="manifesto--hero--title"><h1>FAQ</h1></div>
					<h2 class="manifesto--hero--subtitle">
						<p>Abbiamo sempre tempo per te.<br/>
						Non siamo mai troppo impegnati per rispondere alle tue domande.</p>
					</h2>
			</div>
		</section>

		<section class="faq">
			<div class="container-small">
<?php
$categories = get_terms( 'faq_cats' );

$f = 1; foreach ( $categories as $category ):

    $faqs = new WP_Query(
        array(
					'orderby' => 'term_id',
            'post_type' => 'faq',
            'showposts' => -1,
            'tax_query' => array(
                array(
                    'taxonomy'  => 'faq_cats',
                    'terms'     => array( $category->slug ),
                    'field'     => 'slug'
                )
            )
        )
    );
?>

<h3 class="faq-category--title"><?php echo $category->name; ?></h3>
<ul>
<?php $i = 1; while ($faqs->have_posts()) : $faqs->the_post();
$delay = 50 * $i;?>
	<article id="post-<?php the_ID(); ?>" class="faq__item <?php echo 'faq-'.$f.$i; ?>"  data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?php echo $delay; ?>">

		<header class="faq__content">
			<h2 class="faq__title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="faq__link"><span class="faq__icon icon-arrow-down"></span><?php the_title(); ?></a></h2>
			<div class="faq__description"><?php the_content(); ?></div>
		</header>

	</article>
<?php $i++; endwhile; ?>
</ul>

<?php
    // Reset things, for good measure
    $faqs = null;
    wp_reset_postdata();

// end the loop
$f++; endforeach;

				?>



		</div>
	</section>

	<?php else : ?>

		<?php get_template_part( 'loop-templates/content', 'none' ); ?>

	<?php endif; ?>

</div>




<?php get_footer(); ?>
