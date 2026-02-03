<?php

namespace FSVendor\WPDesk\RepositoryRating\PopupPetition;

use FSVendor\WPDesk\RepositoryRating\PetitionText;
class RepositoryPopupRatingText implements PetitionText
{
    private ?string $rating_text;
    public function __construct(?string $rating_text = null)
    {
        $this->rating_text = $rating_text;
    }
    /**
     * @inheritDoc
     */
    public function get_petition_text(): string
    {
        return $this->rating_text ?? __('Rate us clicking on the stars:', 'flexible-shipping');
    }
}
