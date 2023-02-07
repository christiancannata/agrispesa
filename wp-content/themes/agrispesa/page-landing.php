<?php
/* Template Name: Landing */

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>


<div class="wrapper" id="index-wrapper">

	<?php get_template_part( 'global-elements/hero', 'landing' ); ?>

	<?php if(!get_field('landing_hide_how')):?>
	<?php get_template_part( 'global-elements/steps', 'home' ); ?>
	<?php endif;?>

	<?php if(!get_field('landing_hide_sections')):?>
	<?php get_template_part( 'global-elements/home', 'sections' ); ?>
	<?php endif;?>

	<?php if(!get_field('landing_hide_faq')):?>
	<?php get_template_part( 'global-elements/home', 'faq' ); ?>
	<?php endif;?>


</div>

<?php
get_footer();
