<?php

namespace FSVendor\Octolize\Csat;

class CsatOption
{
    private string $option_name;
    private int $display_on_value;
    public function __construct(string $option_name, int $display_on_value = 1)
    {
        $this->option_name = $option_name;
        $this->display_on_value = $display_on_value;
    }
    public function is_value_to_display(): bool
    {
        return $this->display_on_value === $this->get_value();
    }
    public function increase(): void
    {
        update_option($this->option_name, $this->get_value() + 1);
    }
    public function get_value(): int
    {
        return (int) get_option($this->option_name, 0);
    }
}
