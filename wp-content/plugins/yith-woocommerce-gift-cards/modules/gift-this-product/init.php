<?php
/**
 * People module init.
 *
 * @package YITH\Booking\Modules\People
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/class-yith-ywgc-gift-this-product-module.php';

return YITH_YWGC_Gift_This_Product_Module::get_instance();
