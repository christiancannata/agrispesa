<?php

namespace FSVendor\WPDesk\Tracker\Deactivation;

class DefaultReasonsFactory implements \FSVendor\WPDesk\Tracker\Deactivation\ReasonsFactory
{
    public function createReasons() : array
    {
        $reasons = [];
        $reason = new \FSVendor\WPDesk\Tracker\Deactivation\Reason('not_selected', '', '', \false, '', \true, \true);
        $reasons[$reason->getValue()] = $reason;
        $reason = new \FSVendor\WPDesk\Tracker\Deactivation\Reason('plugin_stopped_working', \__('The plugin suddenly stopped working', 'flexible-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new \FSVendor\WPDesk\Tracker\Deactivation\Reason('broke_my_site', \__('The plugin broke my site', 'flexible-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new \FSVendor\WPDesk\Tracker\Deactivation\Reason('found_better_plugin', \__('I have found a better plugin', 'flexible-shipping'), '', \true, \__('What\'s the plugin\'s name?', 'flexible-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new \FSVendor\WPDesk\Tracker\Deactivation\Reason('plugin_for_short_period', \__('I only needed the plugin for a short period', 'flexible-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new \FSVendor\WPDesk\Tracker\Deactivation\Reason('no_longer_need', \__('I no longer need the plugin', 'flexible-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new \FSVendor\WPDesk\Tracker\Deactivation\Reason('temporary_deactivation', \__('It\'s a temporary deactivation. I\'m just debugging an issue.', 'flexible-shipping'));
        $reasons[$reason->getValue()] = $reason;
        $reason = new \FSVendor\WPDesk\Tracker\Deactivation\Reason('other', \__('Other', 'flexible-shipping'), '', \true, \__('Please let us know how we can improve our plugin', 'flexible-shipping'));
        $reasons[$reason->getValue()] = $reason;
        return $reasons;
    }
}
