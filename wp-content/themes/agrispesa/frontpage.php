<?php /* Template Name: Homepage */

// Start the session
session_start();
$_SESSION['havesearch'] = false;
?>

<?php get_header(); ?>

<?php get_template_part( 'global-elements/hero', 'agrispesa' ); ?>

<section class="sec-home sec-wide bg-orange no-line">

    <div class="container-pg">

      <?php get_template_part( 'global-elements/steps', 'home' ); ?>

      <div class="sec-wide--content" data-aos="fade-up" data-aos-duration="700" data-aos-delay="50">
        <h2 class="sec-wide--text">
          Agrispesa è una selezione di prodotti di agricoltura contadina — ogni settimana a casa tua. Semplice.<br/>
          Verdura, frutta, uova, latte, formaggi, pesce e carne<br class="only-desktop"/> che rispettano la terra e la vita degli animali.
        </h2>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>box/facciamo-noi" class="btn btn-primary btn-big sec-wide--btn" title="Scegli la tua box">Scegli la tua box</a>
      </div>
    </div>

</section>

<div class="clearfix"></div>

<?php get_template_part( 'global-elements/home', 'sections' ); ?>

<?php get_template_part( 'global-elements/reviews', 'home' ); ?>ì
<?php get_template_part( 'global-elements/home', 'press' ); ?>
<?php get_template_part( 'global-elements/home', 'newsletter' ); ?>
<?php get_template_part( 'global-elements/home', 'popup' ); ?>

<?php get_footer();
