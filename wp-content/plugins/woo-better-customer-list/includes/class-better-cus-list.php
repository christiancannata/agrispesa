<?php
/**
 * Better Customer List for WooCommerce setup
 *
 * @author   Blaze Concepts
 * @category Users
 * @package  Better Customer List for WooCommerce
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WooBLZBCL {

    public $cusid;

    public function __construct($userid)
    {
        $this->cusid = $userid;
    }

    public function blz_bcl_get_customer_status($lastorderdate)
    {
        $settingscusstatus = get_option( 'WC_Settings_Tab_BLZ_BCL_cus_status', '31' );

        $dt = $lastorderdate;
        $date = new DateTime($dt);
        $now = new DateTime();
        $diff = $now->diff($date);

        if($diff->days < $settingscusstatus) {
            return __('Active', 'woo-better-customer-list' );
        } else {
            return __('Inactive', 'woo-better-customer-list' );
        }
    }

    public function blz_bcl_get_order_average($firstorderdate, $lastorderdate, $totalcusorders)
    {
        if (!empty($firstorderdate) && !empty($lastorderdate)) {

            $date1 = new DateTime($firstorderdate);
            $date2 = new DateTime($lastorderdate);

            $diff = $date2->diff($date1)->format("%a");
        } else {
            $diff = 0;
        }

        if ($diff > 0) {
            $countco = $totalcusorders - 1;
						update_user_meta($this->cusid, 'customer_average', round($diff / $countco));
            return __('Every '.round($diff / $countco).' days', 'woo-better-customer-list' );
        } else {
						update_user_meta($this->cusid, 'customer_average', 0);
            return __('No Average', 'woo-better-customer-list' );
        }
    }

}
