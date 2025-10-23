
/*
 * Custom Taxonomy Order NE
 */


jQuery(document).ready(function(jQuery) {

	/* Submit button click event */
	jQuery("#custom-loading").hide();
	jQuery("#order-submit").on( 'click', function() {
		customtaxorder_ordersubmit();
	});

	/* Button to sort the list alphabetically */
	jQuery("#order-alpha").on( 'click', function(e) {
		e.preventDefault();
		jQuery("#custom-loading").show();
		customtaxorder_orderalpha();
		setTimeout(function(){
			jQuery("#custom-loading").hide();
		},500);
		jQuery("#order-alpha").trigger('blur');
	});

	/* Button to sort the list on slug */
	jQuery("#order-slug").on( 'click', function(e) {
		e.preventDefault();
		jQuery("#custom-loading").show();
		customtaxorder_orderslug();
		setTimeout(function(){
			jQuery("#custom-loading").hide();
		},500);
		jQuery("#order-slug").trigger('blur');
	});

});


function customtaxorder_addloadevent(){

	/* Make the Terms sortable */
	jQuery("#custom-order-list").sortable({
		placeholder: "sortable-placeholder",
		revert: false,
		tolerance: "pointer"
	});

	/* The same for the Taxonomies */
	jQuery("#custom-taxonomy-list").sortable({
		placeholder: "sortable-placeholder",
		revert: false,
		tolerance: "pointer"
	});

};
addLoadEvent(customtaxorder_addloadevent);


/* Get all the term_orders and send it in a submit. */
function customtaxorder_ordersubmit() {

	/* Terms */
	var newOrder = jQuery("#custom-order-list").sortable("toArray");
	jQuery("#custom-loading").show();
	jQuery("#hidden-custom-order").val(newOrder);

	/* Taxonomies */
	var newOrder_tax = jQuery("#custom-taxonomy-list").sortable("toArray");
	jQuery("#custom-loading").show();
	jQuery("#hidden-taxonomy-order").val(newOrder_tax);

	return true;
}

/* Alphabetical ascending sort based on text. */
function customtaxorder_orderalpha() {
	jQuery("#custom-order-list li").sort(customtaxorder_asc_sort).appendTo('#custom-order-list');
	var newOrder = jQuery("#custom-order-list").sortable("toArray");
	jQuery("#custom-loading").show();
	jQuery("#hidden-custom-order").val(newOrder);
	return true;
}
function customtaxorder_asc_sort(a, b) {
	return jQuery(a).text().localeCompare(jQuery(b).text(), undefined, {numeric: true, sensitivity: 'base'});
}


/* Alphabetical ascending sort based on slug. */
function customtaxorder_orderslug() {
	jQuery("#custom-order-list li").sort(customtaxorder_slug_sort).appendTo('#custom-order-list');
	var newOrder = jQuery("#custom-order-list").sortable("toArray");
	jQuery("#custom-loading").show();
	jQuery("#hidden-custom-order").val(newOrder);
	return true;
}
function customtaxorder_slug_sort(a, b) {
	return jQuery(a).attr('data-slug').localeCompare(jQuery(b).attr('data-slug'), undefined, {numeric: true, sensitivity: 'base'});
}


/* Keep menu for Tools.php open on our pages. */
jQuery(document).ready(function($) {

	if ( jQuery( "div.customtaxorder" ).hasClass( 'wrap-customtaxorder' ) ) {
		jQuery( 'li#menu-tools' ).removeClass( 'wp-not-current-submenu' );
		jQuery( 'li#menu-tools' ).addClass( 'wp-has-current-submenu' );
		jQuery( 'li#menu-tools' ).addClass( 'wp-menu-open' );
	}

});


/*
 * Show slug of term as well.
 *
 * @since 4.0.1
 */
jQuery(document).ready(function($) {
	jQuery( '#customtaxorder-toggle-slug' ).on( 'click', function() {

		var height = jQuery( '.customtaxorder li.lineitem' ).first().css( 'height' );
		if ( height == '24px' ) {
			jQuery( '.customtaxorder li.lineitem' ).css( 'height', '48px' );
		} else if ( height == '48px' ) {
			jQuery( '.customtaxorder li.lineitem' ).css( 'height', '24px' );
		}

	});
});
