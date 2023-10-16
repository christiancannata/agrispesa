<?php

namespace FSVendor\Octolize\BetterDocs\Beacon;

use FSVendor\WPDesk\Beacon\BeaconShouldShowStrategy;
/**
 * Can display BetterDocs Beacon without confirmation.
 */
class BeaconPro extends \FSVendor\Octolize\BetterDocs\Beacon\Beacon
{
    public function __construct(\FSVendor\Octolize\BetterDocs\Beacon\BeaconOptions $beacon_options, \FSVendor\WPDesk\Beacon\BeaconShouldShowStrategy $strategy, $assets_url, $beacon_search_elements_class = 'search-input')
    {
        parent::__construct($beacon_options, $strategy, $assets_url, $beacon_search_elements_class);
        $this->confirmation_message = '';
    }
}
