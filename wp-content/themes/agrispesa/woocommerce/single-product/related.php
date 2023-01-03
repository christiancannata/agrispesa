<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
$terms = wp_get_post_terms( $post->ID, 'product_cat' );
foreach ( $terms as $term ) $categories[] = $term->slug;

if ( in_array( 'box', $categories ) ): ?>

<section class="faq">
<div class="container-small">
	<h3 class="faq--title">Ci chiedono spesso.</h3>
	<div class="faq--list">
		<?php //Loop FAQs
		$args = array(
		'post_type' => 'faq',
		'post_status' => 'publish',
		'posts_per_page' => 5,
		'order' => 'ASC',
		);

		$loop = new WP_Query( $args );
		$i = 1;
		while ( $loop->have_posts() ) : $loop->the_post(); ?>

				<article id="post-<?php the_ID(); ?>" class="faq__item <?php echo 'faq-'.$i; ?>">
					<header class="faq__content">
						<h2 class="faq__title"><a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>" class="faq__link"><span class="faq__icon icon-arrow-down"></span><?php the_title(); ?></a></h2>
						<div class="faq__description"><?php the_content(); ?></div>
					</header>
				</article>
		<?php $i++; endwhile; wp_reset_postdata(); ?>
	</div>
</div>
</section>

<?php else: ?>
<?php if ( $related_products ) : ?>


	<section class="related products">

		<?php //woocommerce_product_loop_start(); ?>
		<div class="related--list">
			<?php $i = 1;
					foreach ( $related_products as $related_product ) : ?>

					<?php
					$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					if($i == 2): ?>

					<article class="product-box text-box">
						<h3 class="text-box--title">Potrebbero<br/>farti gola</h3>
					</article>

					<?php get_template_part( 'template-parts/loop', 'shop' ); ?>

				<?php else: ?>
						<?php get_template_part( 'template-parts/loop', 'shop' ); ?>
					<?php endif;	?>

			<?php $i++; endforeach; ?>

		<?php //woocommerce_product_loop_end(); ?>
		</div>

	</section>
	<?php
endif;
endif;


wp_reset_postdata();
