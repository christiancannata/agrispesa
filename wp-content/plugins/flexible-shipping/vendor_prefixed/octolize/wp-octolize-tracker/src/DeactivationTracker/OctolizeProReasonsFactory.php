<?php

namespace FSVendor\Octolize\Tracker\DeactivationTracker;

use FSVendor\WPDesk\Tracker\Deactivation\Reason;
use FSVendor\WPDesk\Tracker\Deactivation\ReasonsFactory;
class OctolizeProReasonsFactory implements \FSVendor\WPDesk\Tracker\Deactivation\ReasonsFactory
{
    private \FSVendor\Octolize\Tracker\DeactivationTracker\OctolizeReasonsFactory $reasons_factory;
    public function __construct(string $plugin_docs_url = '', string $contact_us_url = '')
    {
        $this->reasons_factory = new \FSVendor\Octolize\Tracker\DeactivationTracker\OctolizeReasonsFactory($plugin_docs_url, '', '', $contact_us_url);
    }
    /**
     * Create reasons.
     *
     * @return Reason[]
     */
    public function createReasons() : array
    {
        $reasons = $this->reasons_factory->createReasons();
        $reasons[\FSVendor\Octolize\Tracker\DeactivationTracker\OctolizeReasonsFactory::MISSING_FEATURE]->setDescription(\__('Can you let us know, what functionality you\'re looking for?', 'flexible-shipping'));
        return $reasons;
    }
}
