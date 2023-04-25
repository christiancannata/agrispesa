<?php
/**
 * Set up theme defaults and registers support for various WordPress feaures.
 */
add_action('after_setup_theme', function () {
	load_theme_textdomain('bathe', get_theme_file_uri('languages'));

	add_theme_support('automatic-feed-links');
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	));
	add_theme_support('post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	));
	add_theme_support('custom-background', apply_filters('bathe_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	)));

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support('custom-logo', array(
		'height' => 200,
		'width' => 50,
		'flex-width' => true,
		'flex-height' => true,
	));

	register_nav_menus(array(
		'primary' => __('Primary Menu', 'bathe'),
	));
});

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
add_action('after_setup_theme', function () {
	$GLOBALS['content_width'] = apply_filters('bathe_content_width', 960);
}, 0);

/**
 * Register widget area.
 */
add_action('widgets_init', function () {
	register_sidebar(array(
		'name' => __('Sidebar', 'bathe'),
		'id' => 'sidebar-1',
		'description' => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
	));
});

/**
 * Enqueue scripts and styles.
 */
add_action('wp_enqueue_scripts', function () {


	wp_enqueue_style('bathe-main', get_theme_file_uri('assets/css/main.css'));
	wp_enqueue_style('tailwind', get_theme_file_uri('assets/css/tailwind.css'));

	wp_enqueue_script('aos', get_template_directory_uri() . '/assets/js/aos.js', ['jquery'], null, true);
	wp_enqueue_script('slick', get_template_directory_uri() . '/assets/js/slick.min.js', ['jquery'], null, true);
	wp_enqueue_script('nice-select', get_template_directory_uri() . '/assets/js/jquery.nice-select.min.js', ['jquery'], null, true);
	// wp_enqueue_script('calendar-js', get_theme_file_uri('assets/js/jsCalendar.js'), array(), null, true);
	// wp_enqueue_script('calendar-lang-js', get_theme_file_uri('assets/js/jsCalendar.lang.it.js'), array(), null, true);
	wp_enqueue_script('agrispesa-js', get_theme_file_uri('assets/_src/js/main.js'), array(), null, true);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}

	wp_localize_script('agrispesa-js', 'WPURL', array('siteurl' => get_option('siteurl'), 'userId' => get_current_user_id()));

});


//Impostazioni immagini caricate
@ini_set('upload_max_size', '256M');
@ini_set('post_max_size', '256M');
@ini_set('max_execution_time', '300');


//AGRISPESA FILES
$ag_includes = array(
	'/agrispesa.php',
	'/post-produttori.php',
	'/post-faq.php',
	'/post-dog-ingredients.php',
	'/box.php',
	'/shop.php',
	'/profile.php',
	'/admin.php',
	'/checkout.php',
	'/admin-css.php',
	'/orders.php',
	//'/calendar.php', //lasciare disabilitato
	'/denso.php',
	'/dashboard.php'
);

foreach ($ag_includes as $file) {
	require_once get_template_directory() . '/inc' . $file;
}
