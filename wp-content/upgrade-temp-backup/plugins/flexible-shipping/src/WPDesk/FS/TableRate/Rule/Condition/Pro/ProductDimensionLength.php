<?php

namespace WPDesk\FS\TableRate\Rule\Condition\Pro;

use WPDesk\FS\TableRate\Rule\Condition\AbstractCondition;

/**
 * Product length condition.
 */
class ProductDimensionLength extends AbstractCondition {

	const CONDITION_ID = 'product_length';

	/**
	 * Product constructor.
	 *
	 * @param int $priority .
	 */
	public function __construct( int $priority = 10 ) {
		$this->condition_id = self::CONDITION_ID;
		$this->name         = __( 'Length', 'flexible-shipping' );
		$this->description  = __( 'Shipping cost based on the product\'s length', 'flexible-shipping' );
		$this->group        = __( 'Product', 'flexible-shipping' );
		$this->priority     = $priority;
		$this->is_disabled  = true;

		$this->name .= ' ' . __( '(PRO feature)', 'flexible-shipping' );
	}
}
