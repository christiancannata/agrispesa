<?php
//Get User info
$current_user = wp_get_current_user();
global $current_user;
global $woocommerce;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<link rel="apple-touch-icon" sizes="180x180"
		  href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32"
		  href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16"
		  href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon/favicon-16x16.png">
	<link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon/site.webmanifest">
	<link rel="mask-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon/safari-pinned-tab.svg"
		  color="#479460">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="theme-color" content="#ffffff">


	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/aos.css">
	<link rel="stylesheet" type="text/css"
		  href="<?php echo get_template_directory_uri(); ?>/assets/fonts/emoji/emoji.min.css">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>


<header class="header">
	<div class="container-pg">
		<div class="header--flex">
			<div class="header--brand">
				<div class="header--hamburger">
					<span class="get-menu icon-hamburger"></span>
				</div>
				<div class="main-logo">
					<a href="<?php echo esc_url(home_url('/')); ?>" class="main-logo--link"
					   title="<?php bloginfo('name'); ?>">
					<span>
						<?php bloginfo('name'); ?>
					</span>
					</a>
				</div>

			</div>

			<div class="top-user">
				<?php if (is_user_logged_in()): ?>
					<div class="user-header">
						<a href="<?php echo wc_get_page_permalink('myaccount'); ?>"
						   class="top-user__link user-link-mobile get-user-menu">
							<span class="top-user__name">Ciao, <?php echo $current_user->first_name; ?> <span
									class="icon-arrow-right top-user__arrow"></span></span>
						</a>
						<div class="top-user__menu">
							<?php wp_nav_menu(
								array(
									'theme_location' => 'user_menu',
									'container_class' => 'menu-container',
									'container_id' => 'menu-user',
									'menu_class' => 'user-menu',
								)
							); ?>
							<ul>
								<li><a href="<?php echo wp_logout_url(home_url()) ?>" title="Esci">Esci</a></li>
							</ul>
						</div>
					</div>
				<?php else: ?>
					<a href="<?php echo wc_get_page_permalink('myaccount'); ?>" class="link-icon top-user__login"><span
							class="icon-account"></span>Accedi</a>
				<?php endif; ?>

				<?php if ($woocommerce->cart->cart_contents_count > 0): ?>
					<div class="header--cart">
						<a href="<?php echo wc_get_cart_url(); ?>" title="Visualizza il carrello" class="cart--link">
							<?php if ($woocommerce->cart->cart_contents_count > 0): ?>
								<span class="cart--items">
								<span class="icon-heart"></span>
								<?php get_template_part('global-elements/logo', 'open'); ?>
							</span>
							<?php endif; ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

		</div>
	</div>

</header>

<div class="agr-menu">
	<div class="agr-menu--flex">
		<div class="agr-menu--sx">
			<div class="agr-menu--header">
				<div class="header--flex">
					<div class="header--sx">
						<div class="header--logo">
							<div class="main-logo">
								<a href="<?php echo esc_url(home_url('/')); ?>" class="main-logo--link"
								   title="<?php bloginfo('name'); ?>">
										<span>
											<?php bloginfo('name'); ?>
										</span>
								</a>
							</div>
						</div>
						<div class="header--hamburger">
							<span class="close-menu icon-close"></span>
						</div>
					</div>
				</div>
			</div>
			<nav class="agr-menu--nav">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_class' => 'main-menu',
						'fallback_cb' => '',
						'menu_id' => 'main-menu',
						'depth' => 2
					)
				);
				?>
				<?php if (is_user_logged_in()): ?>
					<span class="menu-user-name">Ciao, <?php echo $current_user->first_name; ?>.</span>
					<?php wp_nav_menu(
						array(
							'theme_location' => 'user_menu',
							'container_class' => 'menu-container',
							'container_id' => 'menu-user',
							'menu_class' => 'user-menu',
						)
					); ?>
					<ul class="menu-container-mobile">
						<li><a href="<?php echo wp_logout_url(home_url()) ?>" title="Esci">Esci</a></li>
					</ul>
				<?php endif; ?>
			</nav>
		</div>
	</div>
</div>

<main id="primary" class="site-main" role="main">
