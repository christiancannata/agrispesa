/* phpcs:ignoreFile */
jQuery(function() {
	if ( 'undefined' === typeof manage_with_sm ) {
		return;
	}

	let titleAction = jQuery( 'body' ).find( '.page-title-action' ).last();
	if ( manage_with_sm.url ) {
		jQuery(titleAction).after(
			'<a href="' +
			manage_with_sm.url +
			'" class="page-title-action edit-sm">' +
			manage_with_sm.string +
			'</a>'
		);
	}

});
