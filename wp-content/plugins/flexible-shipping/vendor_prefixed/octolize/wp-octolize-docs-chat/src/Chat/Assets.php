<?php

namespace FSVendor\Octolize\Docs\Chat;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk\ShowDecision\ShouldShowStrategy;
class Assets implements Hookable
{
    private string $plugin_url;
    private string $version = '1.0.0';
    private ShouldShowStrategy $show_strategy;
    public function __construct(string $plugin_url, string $version, ShouldShowStrategy $show_strategy)
    {
        $this->plugin_url = $plugin_url;
        $this->version = $version;
        $this->show_strategy = $show_strategy;
    }
    public function hooks(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }
    public function enqueue_admin_scripts()
    {
        if (!$this->show_strategy->shouldDisplay()) {
            return;
        }
        wp_enqueue_script('octolize-docs-chat', trailingslashit($this->plugin_url) . '/vendor_prefixed/octolize/wp-octolize-docs-chat/assets/dist/OctolizeDocsChat.js', [], $this->version, \true);
        wp_enqueue_style('octolize-docs-chat', trailingslashit($this->plugin_url) . '/vendor_prefixed/octolize/wp-octolize-docs-chat/assets/dist/OctolizeDocsChat.css', [], $this->version);
    }
}
