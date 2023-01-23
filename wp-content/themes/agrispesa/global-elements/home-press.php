<?php
if( have_rows('agr_press', 'option') ): ?>
<section class="press" data-aos="fade-up" data-aos-duration="700" data-aos-delay="50">
  <div class="container">
    <h3 class="press--title">Dicono di noi</h3>
    <div class="press--slider">
      <?php
          while( have_rows('agr_press', 'option') ) : the_row();
          $logo = get_sub_field('agr_press_logo', 'option');
          $title = get_sub_field('agr_press_title', 'option');
          $url = get_sub_field('agr_press_url');
          ?>
          <div class="press--item">
            <?php if($url): ?>
            <a href="<?php echo $url; ?>" class="press--link" target="_blank" title="<?php echo $title; ?>">
            <?php endif; ?>
              <img src="<?php echo $logo; ?>" alt="<?php echo $title; ?>" class="press--logo" />
            <?php if($url): ?>
            </a>
            <?php endif; ?>
          </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php endif; ?>
