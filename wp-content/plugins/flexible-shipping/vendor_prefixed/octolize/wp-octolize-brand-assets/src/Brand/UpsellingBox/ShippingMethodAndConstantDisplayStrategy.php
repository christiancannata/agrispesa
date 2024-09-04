<?php

namespace FSVendor\Octolize\Brand\UpsellingBox;

use FSVendor\WPDesk\ShowDecision\AndStrategy;
class ShippingMethodAndConstantDisplayStrategy extends \FSVendor\WPDesk\ShowDecision\AndStrategy
{
    public function __construct(string $method_id, string $constant)
    {
        parent::__construct(new \FSVendor\Octolize\Brand\UpsellingBox\ConstantShouldShowStrategy($constant));
        $this->addCondition(new \FSVendor\Octolize\Brand\UpsellingBox\ShippingMethodShouldShowStrategy($method_id));
    }
}
