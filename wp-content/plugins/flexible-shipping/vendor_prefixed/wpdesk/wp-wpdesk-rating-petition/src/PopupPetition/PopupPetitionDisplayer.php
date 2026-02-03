<?php

namespace FSVendor\WPDesk\RepositoryRating\PopupPetition;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk\RepositoryRating\DisplayStrategy\DisplayDecision;
use FSVendor\WPDesk\RepositoryRating\PetitionText;
/**
 * Can display popup petition.
 */
class PopupPetitionDisplayer implements Hookable
{
    private const SCRIPTS_VERSION = '1';
    private string $display_on_action;
    private DisplayDecision $display_decision;
    private PetitionText $petition_text;
    private PetitionText $rating_text;
    private PopupPetitionAjax $popup_petition_ajax;
    private PopupPetitionOption $option;
    private string $user_email;
    public function __construct(string $display_on_action, DisplayDecision $display_decision, PetitionText $petition_text, PetitionText $rating_text, PopupPetitionAjax $popup_petition_ajax, PopupPetitionOption $option, string $user_email)
    {
        $this->display_on_action = $display_on_action;
        $this->display_decision = $display_decision;
        $this->petition_text = $petition_text;
        $this->rating_text = $rating_text;
        $this->popup_petition_ajax = $popup_petition_ajax;
        $this->option = $option;
        $this->user_email = $user_email;
    }
    public function hooks()
    {
        add_action($this->display_on_action, [$this, 'display_petition_if_should']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_js_and_css_if_should']);
    }
    /**
     * @internal
     */
    public function enqueue_js_and_css_if_should()
    {
        if ($this->display_decision->should_display() && !$this->option->is_option_set()) {
            wp_enqueue_script('wp-wpdesk-rating-petition-popup', plugin_dir_url(__DIR__ . '/../../assets/dist/RatingPetitionPopup.js') . 'RatingPetitionPopup.js', [], self::SCRIPTS_VERSION, \true);
            // Provide translations to the React component via wp_localize_script.
            wp_localize_script('wp-wpdesk-rating-petition-popup', 'wpdeskRatingPetitionL10n', ['unexpected_response' => __('Unexpected server response.', 'flexible-shipping'), 'request_failed' => __('Request failed', 'flexible-shipping'), 'msg_empty' => __('Message cannot be empty.', 'flexible-shipping'), 'your_feedback' => __('Your feedback', 'flexible-shipping'), 'email' => __('Email', 'flexible-shipping'), 'email_optional' => __('Email (optional)', 'flexible-shipping'), 'send' => __('Send', 'flexible-shipping'), 'later' => __('Later', 'flexible-shipping'), 'close' => __('Close', 'flexible-shipping'), 'thanks' => __('Thank you!', 'flexible-shipping'), 'aria_star' => __('%d star', 'flexible-shipping'), 'placeholder_email' => __('you@example.com', 'flexible-shipping')]);
            wp_enqueue_style('wp-wpdesk-rating-petition-popup', plugin_dir_url(__DIR__ . '/../../assets/dist/RatingPetitionPopup.css') . 'RatingPetitionPopup.css', [], self::SCRIPTS_VERSION);
        }
    }
    /**
     * @internal
     */
    public function display_petition_if_should()
    {
        if ($this->display_decision->should_display()) {
            $petition_text = $this->petition_text->get_petition_text();
            $rating_text = $this->rating_text->get_petition_text();
            $nonce = $this->popup_petition_ajax->get_nonce();
            $ajax_url = $this->popup_petition_ajax->get_ajax_url();
            $plugin_slug = $this->popup_petition_ajax->get_plugin_slug();
            $ajax_action = $this->popup_petition_ajax->get_ajax_action();
            $user_email = $this->user_email;
            include __DIR__ . '/views/html-popup-petition-code.php';
        }
    }
}
