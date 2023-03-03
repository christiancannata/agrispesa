<style>
/* Wider Admin Menu for WordPress <?php echo $wp_version; ?> */
#wpcontent,
#wpfooter {
	margin-left: <?php echo $w2px; ?>;
}
#adminmenuback,
#adminmenuwrap,
#adminmenu,
#adminmenu .wp-submenu,
#adminmenu .wp-submenu-wrap,
.folded #adminmenu .wp-has-current-submenu .wp-submenu {
	width: <?php echo $wpx; ?>;
}
#adminmenu li .wp-submenu,
.folded #adminmenu .wp-has-current-submenu .wp-submenu {
	left: <?php echo $w1px; ?>;
}
.wp-menu-arrow {
	-moz-transform:    translate( <?php echo $w1px; ?> );
	-webkit-transform: translate( <?php echo $w1px; ?> );
	-o-transform:      translate( <?php echo $w1px; ?> );
	-ms-transform:     translate( <?php echo $w1px; ?> );
	transform:         translate( <?php echo $w1px; ?> );
}
#adminmenu li.wp-not-current-submenu .wp-menu-arrow {
	-moz-transform:    translate( <?php echo $wpx; ?> );
	-webkit-transform: translate( <?php echo $wpx; ?> );
	-o-transform:      translate( <?php echo $wpx; ?> );
	-ms-transform:     translate( <?php echo $wpx; ?> );
	transform:         translate( <?php echo $wpx; ?> );
}

/* Query Monitor plugin */
body.wp-admin #qm {
	margin-left: <?php echo $wpx; ?> !important;
}
</style>
