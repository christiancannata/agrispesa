<?php
//custom fields
$posh_intro = get_field('posh_intro');

?>

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
