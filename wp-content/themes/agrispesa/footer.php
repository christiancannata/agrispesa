
</main>

<footer class="footer">

	<div class="container-pg">

	<div class="footer--flex">
		<div class="footer--sx">


			<div class="footer--menu--flex">
				<div class="footer--column one">

					<div class="footer--menu">
						<h6 class="footer--menu--title">La spesa <span class="icon-arrow-down footer--menu--title__icon only-mobile"></span></h6>
						<?php
								wp_nav_menu( array(
										'theme_location' => 'footer_menu_one',
										'container_class' => 'footer--menu--list' ) );
								?>
					</div>
					<div class="footer--logo only-desktop">
						<div class="footer--logo--flex">
							<?php get_template_part( 'global-elements/logo', 'open' ); ?>
							<span><?php bloginfo( 'name' ); ?></span>
						</div>
					</div>
					<div class="footer--info only-desktop">
						<span>
							Corso Guglielmo Marconi, 64<br/>12050 Magliano Alfieri (Cn) — Italia<br/>P.IVA 03494390044
							<br/><br/>
							<strong><?php echo date('Y'); ?> © Tutti i diritti riservati.</strong></span>
						<span class="footer--info--flex">
							<a href="#" target="_blank" title="Privacy Policy">Privacy Policy</a>
						</span>
					</div>

				</div>
				<div class="footer--menu">
					<h6 class="footer--menu--title">Su Agrispesa <span class="icon-arrow-down footer--menu--title__icon only-mobile"></span></h6>
					<?php
							wp_nav_menu( array(
									'theme_location' => 'footer_menu_two',
									'container_class' => 'footer--menu--list' ) );
							?>
				</div>
				<div class="footer--menu">
					<h6 class="footer--menu--title">Supporto <span class="icon-arrow-down footer--menu--title__icon only-mobile"></span></h6>
						<?php
								wp_nav_menu( array(
										'theme_location' => 'footer_menu_three',
										'container_class' => 'footer--menu--list' ) );
								?>
				</div>
			</div>

		</div>
		<div class="footer--dx">
			<div class="footer--logo only-mobile">
				<div class="footer--logo--flex">
					<?php get_template_part( 'global-elements/logo', 'open' ); ?>
					<span><?php bloginfo( 'name' ); ?></span>
				</div>
			</div>
			<div class="footer--social">
				<a href="https://www.instagram.com/agrispesa/" target="_blank" class="social"><span><span class="icon-instagram"></span></span></a>
				<a href="https://www.facebook.com/Agrispesa" target="_blank" class="social"><span><span class="icon-facebook"></span></span></a>
				<a href="https://twitter.com/agrispesa" target="_blank" class="social"><span><span class="icon-twitter"></span></span></a>
				<a href="https://www.youtube.com/@agrispesaonline" target="_blank" class="social"><span><span class="icon-youtube"></span></span></a>
			</div>
			<div class="footer--info only-mobile">
				<span>
					Corso Guglielmo Marconi, 64<br/>12050 Magliano Alfieri (Cn) — Italia<br/>P.IVA 03494390044
					<br/><br/>
					<strong><?php echo date('Y'); ?> © Tutti i diritti riservati.</strong></span>
				<span class="footer--info--flex">
					<a href="<?php echo esc_url(home_url('/')); ?>privacy-policy" target="_blank" title="Privacy Policy">Privacy Policy</a>
				</span>
			</div>

		</div>
	</div>
	<div class="footer--flex">
		<div class="secure-payments">
			<span>PAGAMENTI SICURI</span>
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/elements/payments-methods@2x.png" alt="Pagamenti Sicuri" />
		</div>
		<div class="secure-payments bando">
			<span>Progetto Agricoltura Contadina 2.0</span>
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/footer/banner-footer.png" alt="Regione Piemonte" />
		</div>

	</div>


</div>

</footer>

<?php wp_footer(); ?>
<script>
//jQuery('select').niceSelect();
	AOS.init({
    easing: 'ease-in-out-sine',
		disable: function() {
	    var maxWidth = 800;
	    return window.innerWidth < maxWidth;
	  }
  });
</script>
</body>
</html>
