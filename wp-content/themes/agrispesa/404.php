<?php get_header(); ?>


<div class="error-404">
  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/elements/ops.svg" class="error-404--image" alt="Errore 404" />
  <h2 class="error-404--title">Non c'è niente, qui.</h2>
  <p class="error-404--subtitle">Meglio un uovo oggi che una pagina non trovata domani.<br/>C’è stato un errore, che ne dici di tornare al negozio?</p>
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>negozio" title="Fai la spesa" class="btn btn-primary">Fai la spesa!</a>
</div>
<?php get_footer(); 
