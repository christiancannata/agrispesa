<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$wps_cleaner_alert = get_option( 'wps_cleaner_alert' );
if ( $wps_cleaner_alert ) : ?>
    <div class="wps_cleaner_alert_bg">
        <div class="wps_cleaner_alert">
            <div class="wps_cleaner_alert_header">
                <h3><i class="fal fa-exclamation-triangle"></i> <?php _e( 'To read imperatively', 'wps-cleaner' ); ?> <i
                            class="fal fa-exclamation-triangle"></i></h3>
            </div>
            <div class="wps_cleaner_alert_content">
                <p><?php _e( 'This tool offers many cleaning options for your WordPress site, it is safe, however you must be aware that any cleaning operation is <strong>definitive</strong>!', 'wps-cleaner' ); ?></p>
                <p><?php _e( 'So, to avoid any worries, check that you have a backup of your site before using this tool.', 'wps-cleaner' ); ?>
					<?php if ( \WPS\WPS_Cleaner\Plugin::wps_ip_check_return_pf() ) : ?>
                        <br/><?php _e( 'If you are hosted at WPServeur your site is backed up daily.', 'wps-cleaner' ); ?>
                    <?php endif; ?>
                </p>
            </div>
            <div class="wps_cleaner_alert_footer">
                <a href="#" id="wps_cleaner_alert_footer_button" data-nonce="<?php echo wp_create_nonce( 'delete-alert' ); ?>"><?php _e( 'OK it\'s noted !', 'wps-cleaner' ); ?></a>
            </div>
        </div>
    </div>
<?php
endif;