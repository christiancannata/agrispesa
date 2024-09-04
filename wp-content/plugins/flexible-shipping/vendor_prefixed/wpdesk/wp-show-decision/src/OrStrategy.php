<?php

namespace FSVendor\WPDesk\ShowDecision;

class OrStrategy implements \FSVendor\WPDesk\ShowDecision\ShouldShowStrategy
{
    /**
     * @var ShouldShowStrategy[]
     */
    private array $conditions = [];
    public function __construct(\FSVendor\WPDesk\ShowDecision\ShouldShowStrategy $strategy)
    {
        $this->conditions[] = $strategy;
    }
    public function addCondition(\FSVendor\WPDesk\ShowDecision\ShouldShowStrategy $condition) : self
    {
        $this->conditions[] = $condition;
        return $this;
    }
    public function shouldDisplay() : bool
    {
        foreach ($this->conditions as $condition) {
            if ($condition->shouldDisplay()) {
                return \true;
            }
        }
        return \false;
    }
}
