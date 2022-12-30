<?php
/**
 * The template for displaying search forms
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
	<label class="sr-only" for="s">Cerca</label>
	<div class="input-group search-form">
		<div class="input-search">
			<span class="icon-close delete-search"></span>
			<input class="field form-control search-input-field" id="s" name="s" type="text"
				placeholder="Cerca..." value="<?php the_search_query(); ?>">
				<button class="go-search" id="searchsubmit" name="submit" type="submit"><span class="icon-search"></span></button>
		</div>
	</div>
</form>
