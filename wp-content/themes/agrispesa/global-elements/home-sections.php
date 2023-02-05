<?php
if( have_rows('agr_sections') ):
  echo '<div class="agr-section--container">';
    while( have_rows('agr_sections') ) : the_row();
    $wide = get_sub_field('agr_sections_wide');
    $align = get_sub_field('agr_sections_align'); //Allineamento griglia
    $title = get_sub_field('agr_sections_title');
    $text = get_sub_field('agr_sections_text');
    $show_buttons = get_sub_field('agr_sections_show_buttons');
    $cta = get_sub_field('agr_sections_cta');
    $iscategory = get_sub_field('agr_sections_iscategory');
    $url = get_sub_field('agr_sections_url');
    $url_category = get_sub_field('agr_sections_url_category');
    $image = get_sub_field('agr_sections_image');
    $mini_image = get_sub_field('agr_sections_mini_image');
    $background = get_sub_field('agr_sections_background');
    $color = get_sub_field('agr_sections_text_color');
    $btn_secondary = get_sub_field('agr_sections_btn_secondary');
    $link = get_term_link( $url_category, 'product_cat' );
    if($color == 'nero') {
      $text_color = '#343535';
    } else {
      $text_color = '#e5d7c8';
    }
    ?>
<?php if($wide): ?>

    <section class="agr-section" data-aos="fade-up" data-aos-duration="700" data-aos-delay="50">
      <div class="agr-section--wide" style="background-color:<?php echo $background;?>; color:<?php echo $text_color;?>">
      <div class="agr-section--wide--flex">
        <div class="agr-section--image">
          <?php if($mini_image): ?>
            <img src="<?php echo $mini_image; ?>" class="mini-img" alt="<?php echo strip_tags($title); ?>" />
          <?php endif;?>
          <img src="<?php echo $image; ?>" class="main-img" alt="<?php echo strip_tags($title); ?>" />
        </div>
      <div class="agr-section--text">
        <div class="agr-section--text--content">
          <h3 class="agr-section--title"><?php echo $title; ?></h3>
          <p class="agr-section--subtitle"><?php echo $text; ?></p>
          <?php if($show_buttons): ?>
            <?php if($iscategory): ?>
              <a href="<?php echo $link; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
            <?php else: ?>
              <a href="<?php echo $url; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
            <?php endif;?>
          <?php endif;?>
        </div>
      </div>
      </div>
      </div>
    </section>
<?php else: ?>

    <?php if($align): ?>
        <section class="agr-section agr-section--right">
          <div class="agr-section--flex">
            <div class="agr-section--image" data-aos="fade-right" data-aos-duration="700" data-aos-delay="50">
              <img src="<?php echo $image; ?>" class="main-img" alt="<?php echo strip_tags($title); ?>" />
            </div>
          <div class="agr-section--text" data-aos="fade-left" data-aos-duration="700" data-aos-delay="50" style="background-color:<?php echo $background;?>; color:<?php echo $text_color;?>">
            <div class="agr-section--text--content">
              <h3 class="agr-section--title"><?php echo $title; ?></h3>
              <p class="agr-section--subtitle"><?php echo $text; ?></p>
              <?php if($show_buttons): ?>
                <?php if($iscategory): ?>
                  <a href="<?php echo $link; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
                <?php else: ?>
                  <a href="<?php echo $url; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
                <?php endif;?>
              <?php endif;?>
            </div>
          </div>
          </div>
        </section>
      <?php else: ?>
        <section class="agr-section">
          <div class="agr-section--flex">
          <div class="agr-section--text" data-aos="fade-left" data-aos-duration="700" data-aos-delay="50" style="background-color:<?php echo $background;?>; color:<?php echo $text_color;?>">
            <div class="agr-section--text--content">
              <h3 class="agr-section--title"><?php echo $title; ?></h3>
              <p class="agr-section--subtitle"><?php echo $text; ?></p>
              <?php if($show_buttons): ?>
                <?php if($iscategory): ?>
                  <a href="<?php echo $link; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
                <?php else: ?>
                  <a href="<?php echo $url; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
                <?php endif;?>
              <?php endif;?>
            </div>
          </div>
          <div class="agr-section--image" data-aos="fade-right" data-aos-duration="700" data-aos-delay="50">
            <img src="<?php echo $image; ?>" class="main-img" alt="<?php echo strip_tags($title); ?>" />
          </div>
          </div>
        </section>
      <?php endif; ?>


      <?php endif; ?>

    <?php endwhile; ?>

<?php echo '</div>';
    endif; ?>
