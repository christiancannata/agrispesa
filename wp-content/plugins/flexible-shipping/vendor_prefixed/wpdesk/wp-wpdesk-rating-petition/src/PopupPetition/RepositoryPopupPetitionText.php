<?php

namespace FSVendor\WPDesk\RepositoryRating\PopupPetition;

use FSVendor\WPDesk\RepositoryRating\PetitionText;
class RepositoryPopupPetitionText implements PetitionText
{
    private string $plugin_title;
    public function __construct(string $plugin_title)
    {
        $this->plugin_title = $plugin_title;
    }
    /**
     * @inheritDoc
     */
    public function get_petition_text(): string
    {
        return sprintf(__('How\'s %1$s so far?', 'flexible-shipping'), $this->plugin_title);
    }
}
