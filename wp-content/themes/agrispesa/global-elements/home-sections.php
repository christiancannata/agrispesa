<?php
if( have_rows('agr_sections') ):
  echo '<div class="agr-section--container">';
    while( have_rows('agr_sections') ) : the_row();
    $type = get_sub_field('agr_sections_wide');
    $align = get_sub_field('agr_sections_align'); //Allineamento griglia
    $small_columns = get_sub_field('agr_sections_small_columns');

    $title = get_sub_field('agr_sections_title');
    $text = get_sub_field('agr_sections_text');
    $show_buttons = get_sub_field('agr_sections_show_buttons');
    $cta = get_sub_field('agr_sections_cta');
    $iscategory = get_sub_field('agr_sections_iscategory');
    $url = get_sub_field('agr_sections_url');
    $url_category = get_sub_field('agr_sections_url_category');
    $show_colors = get_sub_field('agr_sections_showcolors');
    $background_color = get_sub_field('agr_sections_background');
    $color = get_sub_field('agr_sections_text_color');
    $btn_secondary = get_sub_field('agr_sections_btn_secondary');

    $title_second = get_sub_field('agr_sections_title_second');
    $text_second = get_sub_field('agr_sections_text_second');
    $show_buttons_second = get_sub_field('agr_sections_show_buttons_second');
    $cta_second = get_sub_field('agr_sections_cta_second');
    $iscategory_second = get_sub_field('agr_sections_iscategory_second');
    $url_second = get_sub_field('agr_sections_url_second');
    $url_category_second = get_sub_field('agr_sections_url_category_second');
    $show_colors_second = get_sub_field('agr_sections_showcolors_second');
    $background_color_second = get_sub_field('agr_sections_background_second');
    $color_second = get_sub_field('agr_sections_text_color_second');
    $btn_secondary_second = get_sub_field('agr_sections_btn_secondary_second');

    $image = get_sub_field('agr_sections_image');
    $mini_image = get_sub_field('agr_sections_mini_image');
    $no_images = get_sub_field('agr_sections_noimages');
    $video_file = get_sub_field('agr_sections_video_file');
    $link = get_term_link( $url_category, 'product_cat' );

    if($color == 'nero' ) {
      $text_color = '#343535';
    } else {
      $text_color = '#e5d7c8';
    }

    if($color_second == 'nero' ) {
      $text_color_second = '#343535';
    } else {
      $text_color_second = '#e5d7c8';
    }

    if ($show_colors) {
      $background = get_sub_field('agr_sections_background_custom');
    } else {
      if($background_color == 'orange') {
        $background = "#e8532b";
        $text_color = "#e5d7c8";
      } else if($background_color == 'green') {
        $background = "#069460";
        $text_color = "#e5d7c8";
      } else if($background_color == 'blue') {
        $background = "#3c21ff";
        $text_color = "#e5d7c8";
      } else if($background_color == 'brown') {
        $background = "#765341";
        $text_color = "#e5d7c8";
      } else if($background_color == 'beige') {
        $background = "#e5d7c8";
        $text_color = "#343535";
      }
    }

    if ($show_colors_second) {
      $background_second = get_sub_field('agr_sections_background_custom_second');
    } else {
      if($background_color_second == 'orange') {
        $background_second = "#e8532b";
        $text_color_second = "#e5d7c8";
      } else if($background_color_second == 'green') {
        $background_second = "#069460";
        $text_color_second = "#e5d7c8";
      } else if($background_color_second == 'blue') {
        $background_second = "#3c21ff";
        $text_color_second = "#e5d7c8";
      } else if($background_color_second == 'brown') {
        $background_second = "#765341";
        $text_color_second = "#e5d7c8";
      } else if($background_color_second == 'beige') {
        $background_second = "#e5d7c8";
        $text_color_second = "#343535";
      }
    }
    ?>
<?php if($type == 'wide'): ?>
    <?php if($align): ?>

    <section class="wb-section" data-aos="fade-in" data-aos-duration="700" data-aos-delay="50">
      <div class="wb-section--wide wb-section--container" style="color:<?php echo $text_color;?>; background-color:<?php echo $background;?>;  background-image:url(<?php echo $image; ?>)">
    		<div class="wb-section--content--sx">
          <?php if($title): ?>
    			     <h3 class="wb-section--content--title"><?php echo $title; ?></h3>
          <?php endif;?>
          <?php if($show_buttons): ?>
            <div class="wb-section--content--buttons">
              <?php if($iscategory): ?>
                <a href="<?php echo $link; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
              <?php else: ?>
                <a href="<?php echo $url; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
              <?php endif;?>
            </div>
          <?php endif;?>
    		</div>
    		<div class="wb-section--content--dx" data-aos="fade-up" data-aos-duration="700" data-aos-delay="50">
          <?php if($text): ?>
      			<div class="wb-section--content--descr wide">
      				<?php echo $text; ?>
      			</div>
          <?php endif;?>
    		</div>
  		</div>
  	</section>

    <?php else: ?>
      <section class="wb-section" data-aos="fade-in" data-aos-duration="700" data-aos-delay="50">
        <div class="wb-section--wide wb-section--container" style="color:<?php echo $text_color;?>; background-color:<?php echo $background;?>;  background-image:url(<?php echo $image; ?>)">
          <div class="wb-section--content--dx" data-aos="fade-up" data-aos-duration="700" data-aos-delay="50">
            <?php if($text): ?>
        			<div class="wb-section--content--descr wide">
        				<?php echo $text; ?>
        			</div>
            <?php endif;?>
      		</div>
          <div class="wb-section--content--sx">
            <?php if($title): ?>
      			     <h3 class="wb-section--content--title"><?php echo $title; ?></h3>
            <?php endif;?>
            <?php if($show_buttons): ?>
              <div class="wb-section--content--buttons">
                <?php if($iscategory): ?>
                  <a href="<?php echo $link; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
                <?php else: ?>
                  <a href="<?php echo $url; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
                <?php endif;?>
              </div>
            <?php endif;?>
      		</div>

    		</div>
    	</section>
    <?php endif; ?>
<?php elseif($type == 'columns'): ?>

  <?php if($small_columns):?>
    <?php if($align): ?>

      <section class="wb-section" data-aos="fade-in" data-aos-duration="700" data-aos-delay="50">
        <div class="wb-section--container">
          <div class="wb-section--content">
            <div class="wb-section--content--flex">
              <div class="wb-section--content--sx" data-aos="fade-in" data-aos-duration="600" data-aos-delay="100">
                <div class="wb-section--content--top">
                  <?php if($title): ?>
                  <div class="wb-section--content--title"><?php echo $title; ?></div>
                  <?php endif;?>
                  <?php if($text): ?>
                  <div class="wb-section--content--descr wide">
                    <?php echo $text; ?>
                  </div>
                  <?php endif;?>
                  <?php if($show_buttons): ?>
                    <div class="wb-section--content--buttons">
                      <?php if($iscategory): ?>
                        <a href="<?php echo $link; ?>" class="btn btn-primary" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
                      <?php else: ?>
                        <a href="<?php echo $url; ?>" class="btn btn-primary" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
                      <?php endif;?>
                    </div>
                  <?php endif;?>
                </div>
                <?php if($mini_image):?>
                  <div class="wb-section--content--bottom">
                    <img class="wb-section--content--image" src="<?php echo $mini_image; ?>" alt="<?php echo esc_attr($title); ?>" />
                  </div>
                <?php endif;?>
              </div>
              <div class="wb-section--content--dx" data-aos="fade-in" data-aos-duration="600" data-aos-delay="50">
                <?php if($video_file): ?>
                  <video autoplay muted loop>
                    <source src="<?php echo $video_file; ?>" type="video/mp4">
                      Your browser does not support the video tag.
                  </video>
                <?php else: ?>
                  <img src="<?php echo $image; ?>" class="the-image" alt="<?php echo esc_attr($title); ?>" />
                <?php endif;?>
              </div>
            </div>
          </div>
        </div>
      </section>

    <?php else: ?>

      <section class="wb-section" data-aos="fade-in" data-aos-duration="700" data-aos-delay="50">
        <div class="wb-section--container">
          <div class="wb-section--content">
            <div class="wb-section--content--flex">
              <div class="wb-section--content--dx" data-aos="fade-in" data-aos-duration="600" data-aos-delay="50">
                <?php if($video_file): ?>
                  <video autoplay muted loop>
                    <source src="<?php echo $video_file; ?>" type="video/mp4">
                      Your browser does not support the video tag.
                  </video>
                <?php else: ?>
                  <img src="<?php echo $image; ?>" class="the-image" alt="<?php echo esc_attr($title); ?>" />
                <?php endif;?>
              </div>
              <div class="wb-section--content--sx" data-aos="fade-in" data-aos-duration="600" data-aos-delay="100">
                <div class="wb-section--content--top">
                  <?php if($title): ?>
                  <div class="wb-section--content--title"><?php echo $title; ?></div>
                  <?php endif;?>
                  <?php if($text): ?>
                  <div class="wb-section--content--descr wide">
                    <?php echo $text; ?>
                  </div>
                  <?php endif;?>
                  <?php if($show_buttons): ?>
                    <div class="wb-section--content--buttons">
                      <?php if($iscategory): ?>
                        <a href="<?php echo $link; ?>" class="btn btn-primary" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
                      <?php else: ?>
                        <a href="<?php echo $url; ?>" class="btn btn-primary" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
                      <?php endif;?>
                    </div>
                  <?php endif;?>
                </div>
                <?php if($mini_image):?>
                  <div class="wb-section--content--bottom">
                    <img class="wb-section--content--image" src="<?php echo $mini_image; ?>" alt="<?php echo esc_attr($title); ?>" />
                  </div>
                <?php endif;?>
              </div>

            </div>
          </div>
        </div>
      </section>
    <?php endif; ?>

  <?php elseif($no_images):?>
    <section class="agr-section agr-section--right">
  		<div class="agr-section--flex">
  			<div class="agr-section--text" data-aos="fade-right" data-aos-duration="700" data-aos-delay="50" style="background-color:<?php echo $background;?>; color:<?php echo $text_color;?>">
  				<div class="agr-section--text--content">
            <div class="agr-section--title"><?php echo $title; ?></div>
            <div class="agr-section--subtitle"><?php echo $text; ?></div>
            <?php if($show_buttons): ?>
              <?php if($iscategory): ?>
                <a href="<?php echo $link; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
              <?php else: ?>
                <a href="<?php echo $url; ?>" class="btn <?php if($btn_secondary) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
              <?php endif;?>
            <?php endif;?>
  				</div>
  			</div>
  			<div class="agr-section--text" data-aos="fade-left" data-aos-duration="700" data-aos-delay="50" style="background-color:<?php echo $background_second;?>; color:<?php echo $text_color_second;?>">
  				<div class="agr-section--text--content">
            <div class="agr-section--title"><?php echo $title_second; ?></div>
            <div class="agr-section--subtitle"><?php echo $text_second; ?></div>
            <?php if($show_buttons_second): ?>
              <?php if($iscategory_second): ?>
                <a href="<?php echo $link_second; ?>" class="btn <?php if($btn_secondary_second) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta_second; ?>"><?php echo $cta_second; ?></a>
              <?php else: ?>
                <a href="<?php echo $url_second; ?>" class="btn <?php if($btn_secondary_second) { echo 'btn-white'; } else { echo 'btn-primary'; }?>" title="<?php echo $cta_second; ?>"><?php echo $cta_second; ?></a>
              <?php endif;?>
            <?php endif;?>
          </div>
  			</div>
  		</div>
  	</section>
  <?php else: ?>


    <?php if($align): ?>
        <section class="agr-section agr-section--right" data-aos="fade-in" data-aos-duration="700" data-aos-delay="50" style="background-color:<?php echo $background;?>; color:<?php echo $text_color;?>">
          <div class="agr-section--flex">
            <div class="agr-section--image" data-aos="fade-right" data-aos-duration="700" data-aos-delay="50">
              <div class="img-bg-column" style="background-image:url(<?php echo $image; ?>);"></div>
            </div>
          <div class="agr-section--text" data-aos="fade-left" data-aos-duration="700" data-aos-delay="50">
            <div class="agr-section--text--content">
              <div class="agr-section--title"><?php echo $title; ?></div>
              <div class="agr-section--subtitle"><?php echo $text; ?></div>
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
        <section class="agr-section" data-aos="fade-in" data-aos-duration="700" data-aos-delay="50" style="background-color:<?php echo $background;?>; color:<?php echo $text_color;?>">
          <div class="agr-section--flex">
          <div class="agr-section--text" data-aos="fade-left" data-aos-duration="700" data-aos-delay="50">
            <div class="agr-section--text--content">
              <div class="agr-section--title"><?php echo $title; ?></div>
              <div class="agr-section--subtitle"><?php echo $text; ?></div>
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
            <div class="img-bg-column" style="background-image:url(<?php echo $image; ?>);"></div>
          </div>
          </div>
        </section>
      <?php endif; ?>
<?php endif; ?>
      <?php elseif($type == 'manifesto'): ?>

        <section class="manifesto--hero">
      		<div class="manifesto--container">
            <?php if($title): ?>
              <div class="manifesto--hero--title">
                <?php echo $title; ?>
              </div>
            <?php endif; ?>
            <?php if($text): ?>
      				<div class="manifesto--hero--subtitle">
                <?php echo $text; ?>
      				</div>
              <?php endif; ?>
      		</div>
      	</section>

        <?php elseif($type == 'full_video'): ?>

          <section class="manifesto--video">
        		<div class="videoWrapper">
        			<video width="320" height="240" autoplay loop muted>
        				<source src="<?php echo $video_file; ?>" type="video/mp4">
        			</video>
        		</div>
        	</section>

        <?php elseif($type == 'full_photo'): ?>

          <section class="manifesto--picture" style="background-image:url(<?php echo $image; ?>);"></section>

      <?php endif; ?>

    <?php endwhile; ?>

<?php echo '</div>';
    endif; ?>
