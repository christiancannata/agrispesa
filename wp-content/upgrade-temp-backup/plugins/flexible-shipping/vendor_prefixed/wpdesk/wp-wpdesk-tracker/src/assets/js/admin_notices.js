jQuery(function() {
	jQuery(document).on('click', '#wpdesk_tracker_notice .notice-dismiss',function(e){
		e.preventDefault();
		jQuery.ajax( wpdesk_tracker_notice_localize.ajax_url,
			{
				type: 'POST',
				data: {
					security: wpdesk_tracker_notice_localize.nonce,
					action: wpdesk_tracker_notice_localize.action,
					type: 'dismiss',
					ctx: 'notice',
					plugin: wpdesk_tracker_notice_localize.plugin
				}
			}
		);
	})
	jQuery(document).on('click', '#wpdesk_tracker_allow_button_notice',function(e){
		e.preventDefault();
		jQuery.ajax( wpdesk_tracker_notice_localize.ajax_url,
			{
				type: 'POST',
				data: {
					security: wpdesk_tracker_notice_localize.nonce,
					action: wpdesk_tracker_notice_localize.action,
					type: 'allow',
					ctx: 'notice',
					plugin: wpdesk_tracker_notice_localize.plugin
				}
			}
		);
		jQuery('#wpdesk_tracker_notice').hide();
	});

});