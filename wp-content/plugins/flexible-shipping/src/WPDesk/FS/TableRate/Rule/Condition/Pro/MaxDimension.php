<?php
/**
 * Class MaxDimension
 */

namespace WPDesk\FS\TableRate\Rule\Condition\Pro;

use WPDesk\FS\TableRate\Rule\Condition\AbstractCondition;

/**
 * Max Dimension condition.
 */
class MaxDimension extends AbstractCondition {

	const CONDITION_ID = 'max_dimension';

	/**
	 * Product constructor.
	 *
	 * @param int $priority .
	 */
	public function __construct( int $priority = 10 ) {
		$this->condition_id = self::CONDITION_ID;
		$this->name         = __( 'Max dimension', 'flexible-shipping' );
		$this->description  = __( 'Shipping cost based on the product\'s maximum dimension', 'flexible-shipping' );
		$this->group        = __( 'Product', 'flexible-shipping' );
		$this->priority     = $priority;
		$this->is_disabled  = true;

		$this->name .= ' ' . __( '(PRO feature)', 'flexible-shipping' );
	}
}
