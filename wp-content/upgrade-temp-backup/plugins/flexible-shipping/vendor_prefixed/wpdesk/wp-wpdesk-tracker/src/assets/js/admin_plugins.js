jQuery(function() {
	jQuery("span.deactivate a").click(function (e) {

		var is_tracked = false;
		var data_plugin = jQuery(this).closest('tr').attr('data-plugin');

		jQuery.each(JSON.parse(wpdesk_tracker_plugins_localize.plugins), function (key, value) {
			if (value == data_plugin) {
				is_tracked = true;
			}
		});
		if (is_tracked) {
			e.preventDefault();
			window.location.href = wpdesk_tracker_plugins_localize.base_url + `&plugin=${data_plugin}`;
		}
	});
});