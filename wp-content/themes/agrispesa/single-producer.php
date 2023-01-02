<?php get_header(); ?>



<?php while ( have_posts() ) : the_post(); ?>


	<div class="wrapper" id="author-wrapper">

		<?php $thumb_id = get_post_thumbnail_id();
		$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'full', true);
		$thumb_url = $thumb_url_array[0]; ?>

		<div class="producer-hero">
			<div class="container-pg">
				<h1 class="producer-hero--title"><?php echo the_title(); ?></h1>
				<img src="<?php echo $thumb_url; ?>" alt="<?php echo the_title(); ?>" class="producer-hero--image" />
			</div>
		</div>

		<div class="producer-content">
			<div class="container-xsmall">
				<?php echo the_content(); ?>
			</div>
		</div>




		<div class="small-container" id="content" tabindex="-1">


				<main class="site-main" id="main">



						<?php

						$product_procuders = get_posts(array(
							'post_type' => 'product',
							'meta_query' => array(
								array(
									'key' => 'product_producer', // cerco i prodotti correlati
									'value' => '"' . get_the_ID() . '"',
									'compare' => 'LIKE'
								)
							)
						));
						?>
						<?php if( $product_procuders ): ?>


							<section class="related products">
								<h2 class="related--title">Prodotti</h2>
								<div class="related--list">
							<?php foreach( $product_procuders as $producer ):
								setup_postdata(  $producer );
								$thumb_id = get_post_thumbnail_id($producer->ID);
								$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
								$thumb_url = $thumb_url_array[0];
								$product_quantity = get_field('product_quantity', $producer->ID);
								?>

								<article class="product-box">
								  <a href="<?php the_permalink($producer->ID); ?>" class="product-box--link" title="<?php echo get_the_title($producer->ID); ?>">
								    <img src="<?php echo $thumb_url; ?>" class="product-box--thumb" alt="<?php echo get_the_title($producer->ID); ?>" />
								  </a>
								  <div class="product-box--text">
								    <div class="product-box--text--top">
								      <h2 class="product-box--title"><?php echo get_the_title($producer->ID); ?></h2>
											<div class="product-box--price--flex">
												<?php if($product_quantity) {
													echo '<p class="product-box--description product-info--quantity">' . $product_quantity . ' — </p>';
												} ?>
									      <div class="product-box--price">
									        <?php echo $product->get_price_html($producer->ID); ?>
									      </div>
								      </div>
								      <a href="<?php the_permalink($producer->ID); ?>" class="btn btn-primary btn-small product-box--button" title="<?php echo get_the_title($producer->ID); ?>">Scopri di più</a>
								        <div class="categories-list">

								        </div>
								    </div>
								  </div>
								</article>

							<?php endforeach; ?>
						</div>
					</section>
						<?php endif; ?>









				</main><!-- #main -->



		</div><!-- #content -->

	</div><!-- #author-wrapper -->

<?php endwhile; // end of the loop. ?>


<?php get_footer();
