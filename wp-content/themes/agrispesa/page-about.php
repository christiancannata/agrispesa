<?php
/* Template Name: Chi siamo */

get_header();

$intro_image = get_field('about_intro_image');
$intro_text = get_field('about_intro_text');

$giovanna_image = get_field('giovanna_image');
$elena_image = get_field('giovanna_image');

$giovanna_text = get_field('giovanna_text');
$elena_text = get_field('elena_text');


?>


<div class="wrapper" id="index-wrapper">


	<section class="sec-home sec-framed no-line">
		<div class="container-pg">
			<div class="sec-framed--intro">
				<img src="<?php echo $intro_image;?>" class="sec-framed--img" alt="<?php echo strip_tags($intro_text);?>" />
				<h1 class="sec-home--title medium sec-framed--title"><?php echo strip_tags($intro_text);?></h1>
			</div>
		</div>
	</section>

	<section class="agr-section agr-section--right" data-aos="fade-in" data-aos-duration="700" data-aos-delay="50" style="background-color:#765341; color:#e5d7c8;">
		<div class="agr-section--flex">
			<div class="agr-section--image" data-aos="fade-right" data-aos-duration="700" data-aos-delay="50">
				<div class="img-bg-column" style="background-image:url(<?php echo $giovanna_image; ?>);"></div>
			</div>
		<div class="agr-section--text" data-aos="fade-left" data-aos-duration="700" data-aos-delay="50">
			<div class="agr-section--text--content team">
				<?php echo $giovanna_text; ?>
			</div>
		</div>
		</div>
	</section>

	<section class="agr-section" data-aos="fade-in" data-aos-duration="700" data-aos-delay="50" style="background-color:#e8532b; color:#e5d7c8;">
		<div class="agr-section--flex">
		<div class="agr-section--text" data-aos="fade-left" data-aos-duration="700" data-aos-delay="50">
			<div class="agr-section--text--content team">
				<?php echo $elena_text; ?>
			</div>
		</div>
		<div class="agr-section--image" data-aos="fade-right" data-aos-duration="700" data-aos-delay="50">
			<div class="img-bg-column" style="background-image:url(<?php echo $elena_image; ?>);"></div>
		</div>
		</div>
	</section>


</div>

<?php get_template_part( 'global-elements/home', 'sections' ); ?>
<?php get_template_part( 'global-elements/home', 'press' ); ?>





</div>

<?php
get_footer();
