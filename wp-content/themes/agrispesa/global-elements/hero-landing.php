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

//custom fields
$landing_expire = get_field('landing_expire', $post_id);
$landing_countdown_text = get_field('landing_countdown_text', $post_id);

function isMobileDevice() {
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo
|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i"
, $_SERVER["HTTP_USER_AGENT"]);
}
?>

<?php if( have_rows('agr_hero_slider') ):
		echo '<div class="hero-landing" data-aos="fade-in" data-aos-duration="600" data-aos-delay="0">';
		echo '<div class="hero-landing--slider">';

    while( have_rows('agr_hero_slider') ) : the_row();
    $title = get_sub_field('agr_hero_title');
    $text = get_sub_field('agr_hero_text');
    $image = get_sub_field('agr_hero_image');
    $image_desktop = get_sub_field('agr_hero_image_desktop');
		$landing_price_info = get_sub_field('landing_price_info');
		$landing_special_price = get_sub_field('landing_special_price');
		$landing_old_price = get_sub_field('landing_old_price');
		$landing_coupon = get_sub_field('landing_coupon');

		if(isMobileDevice()){
				$image = get_sub_field('agr_hero_image');
		}
		else {
				if($image_desktop) {
					$image = get_sub_field('agr_hero_image_desktop');

				} else {
					$image = get_sub_field('agr_hero_image');
				}
		}

    ?>

			<div class="hero-landing--item" style="background-image: url(<?php echo $image; ?>);">
				<div class="hero-landing--flex">
					<div class="hero-landing--text">
						<h1 class="hero-landing--title"><?php echo $title; ?></h1>
						<?php if($text): ?>
							<p class="hero-landing--subtitle"><?php echo $text; ?></p>
						<?php endif; ?>
						<?php if($landing_special_price || $landing_old_price):?>
							<div class="landing-price">
								<?php if($landing_price_info):?>
									<span class="info-price"><?php echo $landing_price_info; ?></span>
								<?php endif; ?>
								<?php if($landing_special_price):?>
								<span class="special-price<?php if($landing_old_price):?> red<?php endif;?>"><?php echo $landing_special_price; ?></span>
								<?php endif; ?>
								<?php if($landing_old_price):?>
								<span class="old-price"><?php echo $landing_old_price; ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?php if($landing_coupon): ?>
							<div class="landing-coupon">
								<p>Usa il codice <strong><?php echo $landing_coupon; ?></strong></p>
							</div>
						<?php endif; ?>

						<?php if( have_rows('agr_hero_buttons') ):
								echo '<div class="hero-landing--buttons">';
						    while( have_rows('agr_hero_buttons') ) : the_row();
								$cta = get_sub_field('agr_hero_cta');
						    $url = get_sub_field('agr_hero_url');
								?>
								<a href="<?php echo $url; ?>" class="btn btn-primary" title="<?php echo $cta; ?>"><?php echo $cta; ?></a>
							<?php endwhile;
									echo'</div>';
								endif; ?>
					</div>
				</div>
			</div>


<?php endwhile;
		echo'</div>';

		if(!get_field('landing_hide_countdown')){
		if($landing_expire){
			echo '<div class="landing--expire">';
			echo '<div class="landing--expire--flex">';
				date_default_timezone_set('Europe/Rome');
				$now  = new DateTime();
				$ends = new DateTime($landing_expire);
				$left = $now->diff($ends);
				if($landing_countdown_text){
					echo '<div class="landing--expire__title"><span>' . $landing_countdown_text . '</span></div>';
				}
					echo '<div class="landing--expire__item"><span class="time">' . $left->format('%a') . '</span><span class="label">Giorni</span></div>';
		    	echo '<div class="landing--expire__item"><span class="time">' . $left->format('%h') . '</span><span class="label">Ore</span></div>';
		    	echo '<div class="landing--expire__item"><span class="time">' . $left->format('%i') . '</span><span class="label">Minuti</span></div>';
			echo '</div>';
			echo '</div>';
		}
	}
		echo'</div>';
	endif; ?>
