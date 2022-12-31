<?php
namespace STM_GDPR\includes\plugins;

use STM_GDPR\includes\STM_Helpers;

class STM_WooCommerce
{
    const SLUG = 'woocommerce';

    private static $instance = null;

	public function stm_woocommerce_displayCheckbox() {

		$args = array(
			'type' => 'checkbox',
			'class' => array('stmgdpr-checkbox'),
			'label' => STM_Helpers::stm_helpers_checkboxText(self::SLUG),
			'required' => true,
		);
		woocommerce_form_field('stmgdpr', $args);

	}

	public function stm_woocommerce_displayError() {

		if (!isset($_POST['stmgdpr'])) {
			wc_add_notice(STM_Helpers::stm_helpers_errorMessage(self::SLUG), 'error');
		}

	}

	public function stm_woocommerce_updateOrderMeta($orderID = 0) {

		if (isset($_POST['stmgdpr']) && !empty($orderID)) {
			update_post_meta($orderID, '_stmgdpr', time());
		}

	}

	public function stm_woocommerce_displayOrderData(\WC_Order $order) {

		$label = __('GDPR accepted on:', 'gdpr-compliance-cookie-consent');
		$date = get_post_meta($order->get_id(), '_stmgdpr', true);
		$value = (!empty($date)) ? STM_Helpers::stm_helpers_localDate(get_option('date_format') . ' ' . get_option('time_format'), $date) : __('Not accepted.', 'gdpr-compliance-cookie-consent');

		echo sprintf('<p class="form-field form-field-wide stm-gdpr-date"><strong>%s</strong><br />%s</p>', $label, $value);
	}

	public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
