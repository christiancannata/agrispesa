<?php

namespace FSVendor\Octolize\Docs\Chat;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class AjaxChatSettings implements Hookable
{
    const AJAX_ACTION = 'octolize_docs_chat_settings';
    private string $plugin_slug;
    public function __construct(string $plugin_slug)
    {
        $this->plugin_slug = $plugin_slug;
    }
    function hooks()
    {
        add_action('wp_ajax_' . self::AJAX_ACTION . '_' . $this->plugin_slug, [$this, 'get_settings_ajax']);
    }
    public function get_settings_ajax(): void
    {
        check_ajax_referer(self::AJAX_ACTION, 'nonce');
        $data = [];
        $data['instance_id'] = sanitize_key($_POST['instance_id'] ?? '');
        wp_send_json_success(['settings' => apply_filters('octolize_docs_chat_settings_' . $this->plugin_slug, [], $data)]);
    }
}
