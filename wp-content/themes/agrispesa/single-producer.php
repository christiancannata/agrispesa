<?php get_header(); ?>



<?php while ( have_posts() ) : the_post(); ?>


	<div class="wrapper" id="author-wrapper">
		<div class="woocommerce">
			<nav class="woocommerce-breadcrumb">
				<a href="<?php echo esc_url(home_url('/')); ?>">Home</a>&nbsp;/&nbsp;<a href="<?php echo esc_url(home_url('/')); ?>">Produttori</a>&nbsp;/&nbsp;<?php echo the_title(); ?>
			</nav>
		</div>
		<?php $thumb_id = get_post_thumbnail_id();
		$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'full', true);
		$thumb_url = $thumb_url_array[0]; ?>

		<div class="producer-hero">
			<div class="container-pg">
				<h1 class="producer-hero--title"><?php echo the_title(); ?></h1>
			</div>
		</div>

		<div class="producer-content">
			<div class="container-xsmall">
				<img src="<?php echo $thumb_url; ?>" alt="<?php echo the_title(); ?>" class="producer-hero--image" />
				<?php echo the_content(); ?>
			</div>
		</div>



						<?php

						$product_producers = get_posts(array(
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
						<?php if( $product_producers ): ?>

						<section class="section-hero">
							<div class="section-hero--container">
									<h4 class="section-hero--subtitle">
										Produce cose buone.
									</h4>
							</div>
						</section>
						<section class="products-carousel--container">
						  <div class="products-carousel">
							<?php $i = 1; foreach( $product_producers as $producer ):
								setup_postdata(  $producer );
								$thumb_id = get_post_thumbnail_id($producer->ID);
								$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
								$thumb_url = $thumb_url_array[0];
								?>

								<article class="product-box">
								  <a href="<?php the_permalink($producer->ID); ?>" class="product-box--link" title="<?php echo get_the_title($producer->ID); ?>">
								    <img src="<?php echo $thumb_url; ?>" class="product-box--thumb" alt="<?php echo get_the_title($producer->ID); ?>" />
								  </a>
								  <div class="product-box--text">
								    <div class="product-box--text--top">
								      <h2 class="product-box--title"><?php echo $i; ?><?php echo get_the_title($producer->ID); ?></h2>
											<div class="product-box--price--flex">
												<?php if ( $product->has_weight($producer->ID) ) {
								        	echo '<p class="product-box--description product-info--quantity">' .  $product->get_weight($producer->ID) . ' kg — </p>';
								        } ?>
									      <div class="product-box--price">
									        <?php echo $product->get_price_html($producer->ID); ?>
									      </div>
								      </div>
								      <a href="<?php the_permalink($producer->ID); ?>" class="btn btn-primary btn-small product-box--button" title="<?php echo get_the_title($producer->ID); ?>">Scopri di più</a>
								    </div>
								  </div>
								</article>

							<?php
										$i++; endforeach; ?>
									</div>
								</section>
						<?php endif; ?>









	</div><!-- #author-wrapper -->

<?php endwhile; // end of the loop. ?>


<?php get_footer();
