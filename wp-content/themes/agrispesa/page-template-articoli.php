<?php
/* Template Name: Template con articoli */

get_header(); ?>

	<div class="wrapper" id="index-wrapper">
		<div class="container-pg">

			<?php
			// =====================================================
			// 1) Categoria scelta nella pagina via Custom Field
			// =====================================================
			$page_id = get_queried_object_id();
			$cat_id_raw = get_post_meta($page_id, 'template_category_id', true);
			$cat_id = (int) $cat_id_raw;

			$term = $cat_id ? get_term($cat_id, 'category') : null;

			if (!$cat_id || !$term || is_wp_error($term)) : ?>
				<div class="alert alert-warning">
					<strong>Categoria non impostata.</strong><br>
					Aggiungi un custom field alla pagina:
					<code>template_category_id</code> = ID categoria.
				</div>
				<?php
				get_footer();
				exit;
			endif;

			$ids = [];

			// =====================================================
			// 2) Sticky: 1 post più recente della categoria scelta
			// =====================================================
			$stickyQuery = new WP_Query([
				'post_type'           => 'post',
				'posts_per_page'      => 1,
				'post_status'         => 'publish',
				'orderby'             => 'date',
				'order'               => 'DESC',
				'cat'                 => $cat_id,
				'ignore_sticky_posts' => true,
			]);

			if ($stickyQuery->have_posts()) : ?>

				<div class="fogliospesa--sticky">
					<section class="fogliospesa--hero">
						<div class="fogliospesa--hero--container">
							<h1 class="fogliospesa--hero--title">
								<?php echo esc_html(get_the_title($page_id)); ?>
								<span class="what_week"><?php echo esc_html(date('W')); ?></span>
							</h1>

							<div class="categories-list">
								<a href="<?php echo esc_url(get_category_link($cat_id)); ?>">
									<?php echo esc_html($term->name); ?>
								</a>
							</div>
						</div>
					</section>

					<?php while ($stickyQuery->have_posts()) : $stickyQuery->the_post();
						$ids[] = get_the_ID(); ?>

						<div class="fogliospesa--sticky--flex">

							<div class="fogliospesa--sticky--dx">
								<div class="fogliospesa--sticky--text">
									<div class="fogliospesa--sticky--text--top">
										<h2 class="fogliospesa--sticky--title">
											<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
												<?php the_title(); ?>
											</a>
										</h2>
										<p><?php echo wp_kses_post(get_the_excerpt()); ?></p>
									</div>

									<div class="fogliospesa--sticky--text--bottom">
										<div class="fogliospesa--sticky--data">
											<p><?php echo esc_html(get_the_date('j F Y')); ?></p>
										</div>
									</div>
								</div>
							</div>

							<div class="fogliospesa--sticky--sx">
								<?php if (has_post_thumbnail()) : ?>
									<a href="<?php the_permalink(); ?>" class="fogliospesa--sticky--thumb--link" title="<?php the_title_attribute(); ?>">
                  <span class="fogliospesa--sticky--thumb"
						style="background-image: url(<?php echo esc_url(get_the_post_thumbnail_url(null, 'large')); ?>);">
                  </span>
									</a>
								<?php endif; ?>
							</div>

						</div>

					<?php endwhile; ?>
				</div>

				<?php wp_reset_postdata(); ?>
			<?php endif; ?>


			<!-- =====================================================
				 3) Magazine Top: 3 post della categoria scelta
				 ===================================================== -->
			<section class="fogliospesa--magazine">
				<div class="fogliospesa--magazine--top">
					<div class="magazine--slider">

						<?php
						$featuredQuery = new WP_Query([
							'post_type'           => 'post',
							'posts_per_page'      => 3,
							'post_status'         => 'publish',
							'orderby'             => 'date',
							'order'               => 'DESC',
							'cat'                 => $cat_id,
							'post__not_in'        => $ids,
							'ignore_sticky_posts' => true,
						]);

						if ($featuredQuery->have_posts()) :
							while ($featuredQuery->have_posts()) : $featuredQuery->the_post();
								$ids[] = get_the_ID();
								get_template_part('template-parts/loop', 'blog');
							endwhile;
							wp_reset_postdata();
						endif;
						?>

					</div>
				</div>

				<div class="fogliospesa--magazine--flex">

					<!-- SX: altri 3 -->
					<div class="fogliospesa--magazine--sx">
						<?php
						$moreQuery = new WP_Query([
							'post_type'           => 'post',
							'posts_per_page'      => 3,
							'post_status'         => 'publish',
							'orderby'             => 'date',
							'order'               => 'DESC',
							'cat'                 => $cat_id,
							'post__not_in'        => $ids,
							'ignore_sticky_posts' => true,
						]);

						if ($moreQuery->have_posts()) :
							while ($moreQuery->have_posts()) : $moreQuery->the_post();
								$ids[] = get_the_ID();
								get_template_part('template-parts/loop', 'blog');
							endwhile;
							wp_reset_postdata();
						endif;
						?>
					</div>

					<!-- DX: lista titoli (stile "ricette") ma sempre stessa categoria -->
					<div class="fogliospesa--magazine--dx">
						<div class="fogliospesa--ricette">
							<h4 class="fogliospesa--ricette--title">Altri articoli</h4>

							<?php
							$sidebarQuery = new WP_Query([
								'post_type'           => 'post',
								'posts_per_page'      => 10,
								'post_status'         => 'publish',
								'orderby'             => 'date',
								'order'               => 'DESC',
								'cat'                 => $cat_id,
								'post__not_in'        => $ids,
								'ignore_sticky_posts' => true,
							]);

							if ($sidebarQuery->have_posts()) :
								while ($sidebarQuery->have_posts()) : $sidebarQuery->the_post();
									$ids[] = get_the_ID(); ?>

									<article class="fogliospesa--ricette--post">
										<h2 class="fogliospesa--ricette--post--title">
											<a href="<?php the_permalink(); ?>" class="fogliospesa--ricette--post--link" title="<?php the_title_attribute(); ?>">
												<?php the_title(); ?>
											</a>
										</h2>
									</article>

								<?php endwhile;
								wp_reset_postdata();
							endif;
							?>
						</div>
					</div>

				</div>
			</section>

		</div>
	</div>

<?php get_footer();
