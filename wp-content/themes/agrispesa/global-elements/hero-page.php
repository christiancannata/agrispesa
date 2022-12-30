<?php

//custom fields
$page_title= get_field('page_title');
$page_subtitle = get_field('page_subtitle');
$page_image = get_field('page_image');

?>

<div class="hero-page" data-aos="fade-in" data-aos-duration="600" data-aos-delay="0">

	<div class="hero-page--item" style="background-image: url(<?php echo $page_image; ?>);">

			<div class="hero-page--text">
				<h1 class="hero-page--title"><?php echo $page_title; ?></h1>
				<?php if($page_subtitle): ?>
					<p class="hero-page--subtitle"><?php echo $page_subtitle; ?></p>
				<?php endif; ?>
			</div>

	</div>

</div>
