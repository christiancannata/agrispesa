<style>
/* Wider Admin Menu for WordPress <?php echo $wp_version; ?> */
#wpcontent,
#wpfooter {
	margin-left: <?php echo $wpx; ?>;
}
#adminmenuback,
#adminmenuwrap,
#adminmenu,
#adminmenu .wp-submenu {
	width: <?php echo $wpx; ?>;
}
#adminmenu .wp-submenu {
	left: <?php echo $wpx; ?>;
}
#adminmenu .wp-not-current-submenu .wp-submenu,
.folded #adminmenu .wp-has-current-submenu .wp-submenu {
	min-width: <?php echo $wpx; ?>;
}

/* Query Monitor plugin */
body.wp-admin #qm {
	margin-left: <?php echo $wpx; ?> !important;
}
</style>
