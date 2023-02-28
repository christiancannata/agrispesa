<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function blz_bcl_delete() {
	delete_option( 'WC_Settings_Tab_BLZ_BCL_cus_status' );
}

blz_bcl_delete();
