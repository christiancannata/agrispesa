<?php /* Template Name: Homepage */ ?>

<?php get_header(); ?>

<?php get_template_part( 'global-elements/hero', 'agrispesa' ); ?>

<section class="sec-home sec-wide bg-orange no-line">

    <div class="container-pg">

      <?php
      if( have_rows('box_steps') ): ?>
      <section class="how-works">
        <div class="how-works--flex">
            <?php
                while( have_rows('box_steps') ) : the_row();
                $title = get_sub_field('step_title');
                $subtitle = get_sub_field('step_subtitle');
                ?>
                <div class="how-works--item" data-aos="fade-up" data-aos-duration="700" data-aos-delay="50">
                  <h3 class="how-works--item--title"><?php echo $title; ?></h3>
                  <p class="how-works--item--subtitle"><?php echo $subtitle; ?></p>
                </div>
            <?php endwhile; ?>
          </div>
        </section>
      <?php endif; ?>

      <div class="sec-wide--content" data-aos="fade-up" data-aos-duration="700" data-aos-delay="50">
        <h4 class="sec-wide--text">
          <?php echo the_field('home_intro'); ?>
        </h4>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>box/facciamo-noi" class="btn btn-primary btn-big sec-wide--btn" title="Abbonati alla spesa">Abbonati alla spesa</a>
      </div>
    </div>

</section>

<div class="clearfix"></div>

<?php get_template_part( 'global-elements/home', 'sections' ); ?>

<?php get_template_part( 'global-elements/reviews', 'home' ); ?>ì
<?php get_template_part( 'global-elements/home', 'press' ); ?>
<?php get_template_part( 'global-elements/home', 'newsletter' ); ?>
<?php //get_template_part( 'global-elements/home', 'popup' ); ?>

<?php get_footer();
