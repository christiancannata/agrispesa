<?php
/**
 * Class Item
 */

namespace WPDesk\FS\TableRate\Rule\Condition\Pro;

use WPDesk\FS\TableRate\Rule\Condition\AbstractCondition;

/**
 * Item condition.
 */
class Item extends AbstractCondition {

	const CONDITION_ID = 'item';

	/**
	 * Product constructor.
	 *
	 * @param int $priority .
	 */
	public function __construct( int $priority = 10 ) {
		$this->condition_id = self::CONDITION_ID;
		$this->name         = __( 'Item', 'flexible-shipping' );
		$this->group        = __( 'Cart', 'flexible-shipping' );
		$this->priority     = $priority;
		$this->is_disabled  = true;

		$this->name .= ' ' . __( '(PRO feature)', 'flexible-shipping' );
	}
}
