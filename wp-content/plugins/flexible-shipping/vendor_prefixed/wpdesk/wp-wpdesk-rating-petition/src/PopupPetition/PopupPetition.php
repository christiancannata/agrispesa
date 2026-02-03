<?php

namespace FSVendor\WPDesk\RepositoryRating\PopupPetition;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use FSVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use FSVendor\WPDesk\RepositoryRating\DisplayStrategy\DisplayDecision;
class PopupPetition implements Hookable, HookableCollection
{
    use HookableParent;
    private string $plugin_slug;
    private string $plugin_name;
    private string $send_to;
    private string $reply_to;
    private string $display_on_action;
    private DisplayDecision $display_decision;
    public function __construct(string $plugin_slug, string $plugin_name, string $send_to, string $reply_to, string $display_on_action, DisplayDecision $display_decision)
    {
        $this->plugin_slug = $plugin_slug;
        $this->plugin_name = $plugin_name;
        $this->send_to = $send_to;
        $this->reply_to = $reply_to;
        $this->display_on_action = $display_on_action;
        $this->display_decision = $display_decision;
    }
    public function init(): self
    {
        $option = new PopupPetitionOption($this->plugin_slug);
        $popup_petition_ajax = new PopupPetitionAjax($this->plugin_slug, $option, $this->send_to);
        $this->add_hookable(new PopupPetitionDisplayer($this->display_on_action, $this->display_decision, new RepositoryPopupPetitionText($this->plugin_name), new RepositoryPopupRatingText(), $popup_petition_ajax, $option, $this->reply_to));
        $this->add_hookable($popup_petition_ajax);
        return $this;
    }
    public function hooks()
    {
        $this->hooks_on_hookable_objects();
    }
}
