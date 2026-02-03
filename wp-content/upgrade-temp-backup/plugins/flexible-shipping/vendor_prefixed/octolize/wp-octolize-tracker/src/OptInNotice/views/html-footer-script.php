<?php

namespace FSVendor;

/**
 * @var string $plugin_slug
 */
?>
<script type="text/javascript">
	jQuery(document).on('click', '#wpdesk_tracker_allow_button_notice-<?php 
echo \esc_attr($plugin_slug);
?>',function(e){
		e.preventDefault();
		jQuery.ajax( '<?php 
echo \admin_url('admin-ajax.php');
?>',
			{
				type: 'POST',
				data: {
					security: '<?php 
echo \wp_create_nonce(WPDesk_Tracker::WPDESK_TRACKER_NOTICE);
?>',
					action: 'wpdesk_tracker_notice_handler',
					type: 'allow',
				}
			}
		);
		jQuery('#wpdesk-notice-octolize_opt_in_<?php 
echo \esc_attr($plugin_slug);
?>').toggle( false );
	});
</script>
<?php 
