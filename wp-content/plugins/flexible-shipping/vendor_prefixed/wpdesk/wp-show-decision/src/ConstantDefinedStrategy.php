<?php

namespace FSVendor\WPDesk\ShowDecision;

class ConstantDefinedStrategy implements \FSVendor\WPDesk\ShowDecision\ShouldShowStrategy
{
    /**
     * @var string
     */
    private string $constant;
    public function __construct(string $constant)
    {
        $this->constant = $constant;
    }
    public function shouldDisplay() : bool
    {
        return \defined($this->constant);
    }
}
