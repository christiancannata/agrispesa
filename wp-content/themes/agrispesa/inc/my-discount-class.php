<?php

class AgrispesaDiscountChecker extends WC_Discounts
{
	public function __construct($object = null)
	{
		parent::__construct($object);
	}

	public function is_coupon_valid($coupon, $userId = null)
	{
		try {
			$this->validate_coupon_exists($coupon);
			$this->validate_coupon_usage_limit($coupon);
			$this->validate_coupon_user_usage_limit($coupon, $userId);
			$this->validate_coupon_expiry_date($coupon);
			$this->validate_coupon_minimum_amount($coupon);
			$this->validate_coupon_maximum_amount($coupon);
			$this->validate_coupon_product_ids($coupon);
			$this->validate_coupon_product_categories($coupon);
			$this->validate_coupon_excluded_items($coupon);
			$this->validate_coupon_eligible_items($coupon);

			if (!apply_filters('woocommerce_coupon_is_valid', true, $coupon, $this)) {
				throw new Exception(__('Coupon is not valid.', 'woocommerce'), 100);
			}
		} catch (Exception $e) {
			/**
			 * Filter the coupon error message.
			 *
			 * @param string $error_message Error message.
			 * @param int $error_code Error code.
			 * @param WC_Coupon $coupon Coupon data.
			 */
			$message = apply_filters('woocommerce_coupon_error', is_numeric($e->getMessage()) ? $coupon->get_coupon_error($e->getMessage()) : $e->getMessage(), $e->getCode(), $coupon);

			return new WP_Error(
				'invalid_coupon',
				$message,
				array(
					'status' => 400,
				)
			);
		}
		return true;
	}

}
