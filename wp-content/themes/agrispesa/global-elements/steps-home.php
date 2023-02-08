<?php
if( have_rows('box_steps', 'option') ): ?>
<section class="how-works">
  <div class="how-works--flex">
      <?php
          while( have_rows('box_steps', 'option') ) : the_row();
          $title = get_sub_field('step_title', 'option');
          $subtitle = get_sub_field('step_subtitle', 'option');
          ?>
          <div class="how-works--item" data-aos="fade-up" data-aos-duration="700" data-aos-delay="50">
            <h3 class="how-works--item--title"><?php echo $title; ?></h3>
            <p class="how-works--item--subtitle"><?php echo $subtitle; ?></p>
          </div>
      <?php endwhile; ?>
    </div>
  </section>
<?php endif; ?>
