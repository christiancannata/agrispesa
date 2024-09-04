<?php

namespace FSVendor\Octolize\Tracker\DeactivationTracker;

use FSVendor\WPDesk\Tracker\Deactivation\Reason;
use FSVendor\WPDesk\Tracker\Deactivation\ReasonsFactory;
class OctolizeReasonsFactory implements \FSVendor\WPDesk\Tracker\Deactivation\ReasonsFactory
{
    const MISSING_FEATURE = 'missing_feature';
    const NOT_SELECTED = 'not_selected';
    const I_HAD_DIFFICULTIES = 'i_had_difficulties';
    const STOPPED_WORKING = 'stopped_working';
    const FOUND_ANOTHER_PLUGIN = 'found_another_plugin';
    const DONT_NEED_ANYMORE = 'dont_need_anymore';
    const TEMPORARY_DEACTIVATION = 'temporary_deactivation';
    const OTHER = 'other';
    private string $plugin_docs_url;
    private string $plugin_support_forum_url;
    private string $pro_plugin_title;
    private string $contact_us_url;
    public function __construct(string $plugin_docs_url = '', string $plugin_support_forum_url = '', string $pro_plugin_title = '', string $contact_us_url = '')
    {
        $this->plugin_docs_url = $plugin_docs_url === '' ? 'https://octol.io/docs-exit-pop-up' : $plugin_docs_url;
        $this->plugin_support_forum_url = $plugin_support_forum_url;
        $this->pro_plugin_title = $pro_plugin_title;
        $this->contact_us_url = $contact_us_url;
    }
    /**
     * Create reasons.
     *
     * @return Reason[]
     */
    public function createReasons() : array
    {
        return [self::NOT_SELECTED => new \FSVendor\WPDesk\Tracker\Deactivation\Reason(self::NOT_SELECTED, '', '', \false, '', \true, \true), self::I_HAD_DIFFICULTIES => new \FSVendor\WPDesk\Tracker\Deactivation\Reason(self::I_HAD_DIFFICULTIES, \__('I had difficulties configuring the plugin', 'wp-tracker-octolize'), \sprintf(\__('Sorry to hear that! We\'re certain that with a little help, configuring the plugin will be a breeze. Before you deactivate, try to find a solution in our %1$sdocumentation%2$s%3$s.', 'wp-tracker-octolize'), '<a href="' . \esc_url($this->plugin_docs_url) . '" target="_blank">', '</a>', $this->plugin_support_forum_url ? \sprintf(\__(' or post a question on the %1$sforum%2$s', 'wp-tracker-octolize'), '<a href="' . \esc_url($this->plugin_support_forum_url) . '" target="_blank">', '</a>') : '')), self::STOPPED_WORKING => new \FSVendor\WPDesk\Tracker\Deactivation\Reason(self::STOPPED_WORKING, \__('The plugin stopped working', 'wp-tracker-octolize'), \sprintf(\__('We take any issues with our plugins very seriously. Try to find a reason in our %1$sdocumentation%2$s%3$s.', 'wp-tracker-octolize'), '<a href="' . \esc_url($this->plugin_docs_url) . '" target="_blank">', '</a>', $this->plugin_support_forum_url ? \sprintf(\__(' or post the problem on the %1$sforum%2$s', 'wp-tracker-octolize'), '<a href="' . \esc_url($this->plugin_support_forum_url) . '" target="_blank">', '</a>') : '')), self::FOUND_ANOTHER_PLUGIN => new \FSVendor\WPDesk\Tracker\Deactivation\Reason(self::FOUND_ANOTHER_PLUGIN, \__('I have found another plugin', 'wp-tracker-octolize'), \__('That hurts a little bit, but we\'re tough! Can you let us know which plugin you are switching to?', 'wp-tracker-octolize'), \true, \__('Which plugin are you switching to?', 'wp-tracker-octolize')), self::MISSING_FEATURE => new \FSVendor\WPDesk\Tracker\Deactivation\Reason(self::MISSING_FEATURE, \__('The plugin doesn\'t have the functionality I need', 'wp-tracker-octolize'), $this->pro_plugin_title ? \sprintf(\__('Good news! There\'s a great chance that the functionality you need is already implemented in the PRO version of the plugin. %1$sContact us%2$s to receive a discount for %3$s. Also, can you describe what functionality you\'re looking for?', 'wp-tracker-octolize'), '<a href="' . \esc_url($this->contact_us_url) . '" target="_blank">', '</a>', $this->pro_plugin_title) : \__('We\'re sorry to hear that. Can you describe what functionality you\'re looking for?', 'wp-tracker-octolize'), \true, \__('What functionality are you looking for?', 'wp-tracker-octolize')), self::DONT_NEED_ANYMORE => new \FSVendor\WPDesk\Tracker\Deactivation\Reason(self::DONT_NEED_ANYMORE, \__('I don\'t need the plugin anymore', 'wp-tracker-octolize'), \__('Sorry to hear that! Can you let us know why the plugin is not needed anymore?', 'wp-tracker-octolize'), \true, \__('Why is the plugin not needed anymore?', 'wp-tracker-octolize')), self::TEMPORARY_DEACTIVATION => new \FSVendor\WPDesk\Tracker\Deactivation\Reason(self::TEMPORARY_DEACTIVATION, \__('I\'m deactivating temporarily for debugging purposes', 'wp-tracker-octolize')), self::OTHER => new \FSVendor\WPDesk\Tracker\Deactivation\Reason(self::OTHER, \__('Other reason', 'wp-tracker-octolize'), \__('Can you provide some details on the reason behind deactivation?', 'wp-tracker-octolize'), \true, \__('Please provide details', 'wp-tracker-octolize'))];
    }
}
