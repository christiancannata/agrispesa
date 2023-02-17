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
					$thumb_url = $thumb_url_array[0];
					$old_image = get_field('produttore_immagine');
		?>

		<div class="producer-hero">
			<div class="container-pg">
				<h1 class="producer-hero--title"><?php echo the_title(); ?></h1>
			</div>
		</div>

		<div class="producer-content">
			<div class="container-xsmall">
				<?php if(has_post_thumbnail()): ?>
						<img src="<?php echo $thumb_url; ?>" alt="<?php echo the_title(); ?>" class="producer-hero--image" />
				<?php elseif($old_image): ?>
						<img src="<?php echo get_template_directory_uri() . '/assets/images/produttori/' . $old_image; ?>" alt="<?php echo the_title(); ?>" class="producer-hero--image" />
				<?php endif; ?>
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

						<section class="section-hero small">
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
								  <a href="<?php the_permalink($producer->ID); ?>" class="product-box--link" title="<?php echo esc_html( get_the_title($producer->ID) ); ?>">
										<?php if($thumb_id):?>
								      <img src="<?php echo $thumb_url; ?>" class="product-box--thumb" alt="<?php echo esc_html( get_the_title($producer->ID) ); ?>" />
								    <?php else: ?>
								      <img src="https://staging.agrispesa.it/wp-content/uploads/2023/02/default.png" class="product-box--thumb" alt="<?php echo esc_html( get_the_title($producer->ID) ); ?>" />
								    <?php endif;?>

								  </a>
								  <div class="product-box--text">
								    <div class="product-box--text--top">
								      <h2 class="product-box--title"><?php echo get_the_title($producer->ID); ?></h2>
											<div class="product-box--price--flex">
												<?php $product_data = $product->get_meta('_woo_uom_input', $producer->ID);
												if ( $product->has_weight($producer->ID) ) {
								        	if($product_data && $product_data != 'gr') {
								        		echo '<span class="product-info--quantity">' . $product->get_weight($producer->ID) . ' '.$product_data.'</span>';
								        	} else {
								            if($product->get_weight($producer->ID) == 1000) {
								        			echo '<span class="product-info--quantity">1 kg</span>';
								        		} else {
								        			echo '<span class="product-info--quantity">' . $product->get_weight($producer->ID) . ' gr</span>';
								        		}
								        	}
								        } ?>


									      <div class="product-box--price">
									        <?php echo $product->get_price_html($producer->ID); ?>
									      </div>
								      </div>
								      <?php echo do_shortcode('[add_to_cart id="'.$product->get_id($producer->ID).'" show_price="false" class="btn-fake" quantity="1" style="border:none;"]');?>
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
