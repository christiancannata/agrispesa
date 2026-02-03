<?php

namespace FSVendor\Octolize\Docs\Chat;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk\ShowDecision\ShouldShowStrategy;
class ChatContainer implements Hookable
{
    private ShouldShowStrategy $show_strategy;
    private string $plugin_slug;
    public function __construct(ShouldShowStrategy $show_strategy, string $plugin_slug)
    {
        $this->show_strategy = $show_strategy;
        $this->plugin_slug = $plugin_slug;
    }
    public function hooks(): void
    {
        add_action('admin_footer', [$this, 'render_chat']);
    }
    public function render_chat()
    {
        if ($this->show_strategy->shouldDisplay()) {
            $ajax_url = admin_url('admin-ajax.php');
            $nonce = wp_create_nonce(AjaxChatSettings::AJAX_ACTION);
            $ajax_action = AjaxChatSettings::AJAX_ACTION . '_' . $this->plugin_slug;
            $instance_id = sanitize_key($_GET['instance_id'] ?? '');
            $plugin_slug = $this->plugin_slug;
            include __DIR__ . '/views/html-chat-container.php';
        }
    }
}
