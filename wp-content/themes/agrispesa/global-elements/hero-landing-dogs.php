<?php
/**
 * Hero setup
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

global $wp_query;
$post_id = $wp_query->post->ID;


function isMobileDevice() {
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo
|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i"
, $_SERVER["HTTP_USER_AGENT"]);
}
?>

<?php if( have_rows('agr_cat_hero_slider') ):


		echo '<div class="hero-landing" data-aos="fade-in" data-aos-duration="600" data-aos-delay="0">';
		echo '<div class="hero-landing--slider">';

    while( have_rows('agr_cat_hero_slider') ) : the_row();
    $title = get_sub_field('agr_cat_hero_title');
    $text = get_sub_field('agr_cat_hero_text');
    $image = get_sub_field('agr_cat_hero_image');
    $image_desktop = get_sub_field('agr_cat_hero_image_desktop');
		$landing_coupon = get_sub_field('landing_cat_coupon');
		$cta = get_sub_field('agr_cat_hero_cta');
		$hero_color = get_sub_field('hero_color');

		if(isMobileDevice()){
			if($image) {
				$image = get_sub_field('agr_cat_hero_image');
			} else {
				$image = get_sub_field('agr_cat_hero_image_desktop');
			}
		}
		else {
				if($image_desktop) {
					$image = get_sub_field('agr_cat_hero_image_desktop');

				} else {
					$image = get_sub_field('agr_cat_hero_image');
				}
		}

    ?>
		<?php if($hero_color == 'white'):?>
			<div class="hero-landing--item dark-hero" style="background-image: url(<?php echo $image; ?>);">
		<?php else:?>
			<div class="hero-landing--item light-hero" style="background-image: url(<?php echo $image; ?>);">
		<?php endif;?>


				<img src="<?php echo $image; ?>" id="getBright" />
				<div class="hero-landing--flex">
					<div class="hero-landing--text">
						<?php if(!$title):?>
						<h1 class="hero-landing--title"><?php echo $title; ?></h1>
						<?php else:?>
							<h1 class="hero-landing--title">La salute di <span class="name-pet"><span class="pet"><span id="petname">Argo</span></span></span><br/>comincia dalla pappa.</h1>
						<?php endif;?>
						<?php if($text): ?>
							<p class="hero-landing--subtitle"><?php echo $text; ?></p>
						<?php endif; ?>
						<?php if($landing_coupon): ?>
							<div class="landing-coupon">
								<p>Usa il codice <strong><?php echo $landing_coupon; ?></strong></p>
							</div>
						<?php endif; ?>

						<?php if( $cta ):?>
							<a href="#go-products" class="btn btn-primary scroll-to" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>


<?php endwhile;
		echo'</div>';
		echo'</div>';
	endif; ?>
