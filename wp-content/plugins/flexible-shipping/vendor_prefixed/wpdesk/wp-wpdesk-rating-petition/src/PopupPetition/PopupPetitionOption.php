<?php

namespace FSVendor\WPDesk\RepositoryRating\PopupPetition;

class PopupPetitionOption
{
    private string $option_name;
    public function __construct(string $plugin_slug)
    {
        $this->option_name = $plugin_slug . '_popup_petition_displayed';
    }
    public function set_option()
    {
        update_option($this->option_name, 1);
    }
    public function is_option_set(): bool
    {
        return (int) get_option($this->option_name, 0) === 1;
    }
}
