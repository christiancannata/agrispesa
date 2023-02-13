<section class="hero">
  <div class="hero--item">
    <div class="hero--content">
      <div class="hero--text" data-aos="fade-in" data-aos-duration="600" data-aos-delay="0">
        <h1 class="hero--title"><?php echo the_field('hero_title'); ?></h1>
        <p class="hero--subtitle"><?php echo the_field('hero_subtitle'); ?></p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>box/facciamo-noi" class="btn btn-primary btn-big hero--btn" alt="Abbonati alla spesa">Abbonati alla spesa</a>
        <div class="hero-stars">
          <div class="review-page--stars">
            <span class="icon-star yellow"></span>
            <span class="icon-star yellow"></span>
            <span class="icon-star yellow"></span>
            <span class="icon-star yellow"></span>
            <span class="icon-star yellow"></span>
          </div>
          <div class="hero-stars--flex">
            <a href="https://it.trustpilot.com/review/www.agrispesa.it" target="_blank" title="Leggi le nostre recensioni su Trustpilot">
            <span class="value">4.8</span>
            <span>su Trustpilot</span>
            </a>
          </div>
        </div>
      </div>
      <div class="hero--box" data-aos="fade-up" data-aos-duration="700" data-aos-delay="50">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/box/box1_BZ.png" alt="Prova Agrispesa" />
      </div>
    </div>
  </div>
</section>
