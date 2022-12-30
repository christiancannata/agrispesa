<?php get_header(); ?>


<div class="wrapper" id="index-wrapper">

	<?php if ( have_posts() ) : ?>

		<?php get_template_part( 'global-elements/hero', 'faq' ); ?>

		<section class="faq">
			<div class="container-small">

			<?php /* Start the Loop */ ?>
			<?php $i = 1; while ( have_posts() ) : the_post(); ?>


				<article id="post-<?php the_ID(); ?>" class="faq__item <?php echo 'faq-'.$i; ?>" data-aos-duration="600" data-aos-delay="100" data-aos-offset="-100">

					<header class="faq__content">
						<h2 class="faq__title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="faq__link"><span class="faq__icon icon-arrow-down"></span><?php the_title(); ?></a></h2>
						<div class="faq__description"><?php the_content(); ?></div>
					</header>

				</article>

			<?php $i++; endwhile; ?>

		</div>
	</section>

	<?php else : ?>

		<?php get_template_part( 'loop-templates/content', 'none' ); ?>

	<?php endif; ?>

</div>




<?php get_footer(); ?>
