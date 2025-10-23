jQuery(function() {

	jQuery('input[type=radio]').click(function () {
		var reason = jQuery('input[name=selected-reason]:checked').val();
		console.log(reason);
		jQuery('.reason-input').hide();
		if (reason == 'found_better_plugin') {
			jQuery('#found_better_plugin').show();
		}
		if (reason == 'other') {
			jQuery('#other').show();
		}
		jQuery('.wpdesk_tracker_deactivate .button-deactivate').html(wpdesk_tracker_localize.submit_txt);
	});

	jQuery('.button-deactivate').click(function (e) {
		e.preventDefault();
		console.log('deactivate');
		var reason = jQuery('input[name=selected-reason]:checked').val();
		var additional_info = '';
		if (reason == 'found_better_plugin') {
			additional_info = jQuery('#found_better_plugin input').val();
		}
		if (reason == 'other') {
			additional_info = jQuery('#other input').val();
		}
		console.log(reason);
		if (typeof reason != 'undefined') {
			console.log('not undefined');
			jQuery('.button').attr('disabled', true);
			jQuery.ajax(wpdesk_tracker_localize.ajax_url,
				{
					type: 'POST',
					data: {
						action: wpdesk_tracker_localize.action,
						security: wpdesk_tracker_localize.nonce,
						reason: reason,
						plugin: wpdesk_tracker_localize.plugin,
						plugin_name: wpdesk_tracker_localize.plugin_name,
						additional_info: additional_info,
					}
				}
			).always(function () {
				window.location.href = wpdesk_tracker_localize.tracker_page;
			});
		}
		else {
			window.location.href = wpdesk_tracker_localize.tracker_page;
		}
	});

	jQuery('.button-close').click(function (e) {
		e.preventDefault();
		window.history.back();
	});

	jQuery('.trigger').click(function(e) {
		e.preventDefault();
		if (jQuery(this).parent().hasClass('open')) {
			jQuery(this).parent().removeClass('open')
		}
		else {
			jQuery(this).parent().addClass('open');
		}
	});

});