<?php

namespace FSVendor\WPDesk\RepositoryRating\PopupPetition;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class PopupPetitionAjax implements Hookable
{
    public const AJAX_ACTION = 'wpdesk_repository_rating_petition';
    private string $plugin_slug;
    private PopupPetitionOption $option;
    private int $minimal_stars_for_rating_url;
    private string $rating_url;
    /**
     * Address to send feedback e-mails to.
     */
    private string $send_to;
    public function __construct(string $plugin_slug, PopupPetitionOption $option, string $send_to, string $rating_url = '', int $minimal_stars_for_rating_url = 4)
    {
        $this->plugin_slug = $plugin_slug;
        $this->option = $option;
        $this->send_to = $send_to;
        $this->minimal_stars_for_rating_url = $minimal_stars_for_rating_url;
        $this->rating_url = $rating_url;
    }
    public function hooks()
    {
        add_action('wp_ajax_' . $this->get_ajax_action(), [$this, 'handle_ajax_request']);
    }
    public function handle_ajax_request()
    {
        if (empty($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), $this->get_ajax_action())) {
            wp_send_json_error(['message' => __('Invalid nonce.', 'flexible-shipping')], 400);
        }
        $subaction = isset($_POST['subaction']) ? sanitize_text_field(wp_unslash($_POST['subaction'])) : '';
        switch ($subaction) {
            case 'submit_rating':
                $this->handle_submit_rating();
                break;
            case 'submit_feedback':
                $this->handle_submit_feedback();
                break;
            case 'postpone':
                $this->handle_postpone();
                break;
            default:
                wp_send_json_error(['message' => __('Unknown action.', 'flexible-shipping')], 400);
        }
    }
    private function handle_submit_rating(): void
    {
        $rating = isset($_POST['rating']) ? absint($_POST['rating']) : 0;
        if ($rating <= 0 || $rating > 5) {
            wp_send_json_error(['message' => __('Invalid rating value.', 'flexible-shipping')], 400);
        }
        if ($rating >= $this->minimal_stars_for_rating_url) {
            $this->option->set_option();
            wp_send_json_success(['action' => 'open_url', 'url' => $this->build_rating_url($rating)]);
        } else {
            wp_send_json_success(['action' => 'open_form']);
        }
    }
    private function handle_submit_feedback(): void
    {
        $message = isset($_POST['message']) ? wp_kses_post(wp_unslash($_POST['message'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $rating = isset($_POST['rating']) ? absint($_POST['rating']) : 0;
        if (empty($message)) {
            wp_send_json_error(['message' => __('Message is required.', 'flexible-shipping')], 400);
        }
        $subject = sprintf(__('Feedback for %s', 'flexible-shipping'), $this->plugin_slug);
        $headers = [];
        if (!empty($email) && is_email($email)) {
            $headers[] = 'Reply-To: ' . $email;
        }
        $body = "Rating: {$rating}\n\n";
        $body .= "Message:\n{$message}\n";
        if (empty($message)) {
            wp_send_json_error(['message' => __('Message is required.', 'flexible-shipping')], 400);
        }
        $sent = wp_mail($this->send_to, $subject, $body, $headers);
        if ($sent) {
            $this->option->set_option();
            wp_send_json_success(['message' => __('Thank you for your feedback!', 'flexible-shipping')]);
        } else {
            wp_send_json_error(['message' => __('Could not send email. Please try again later.', 'flexible-shipping')], 500);
        }
    }
    /**
     * Postpone displaying the popup.
     */
    private function handle_postpone(): void
    {
        /**
         * Action fired after postponing rating petition popup.
         *
         * @param string $plugin_slug Plugin slug passed to the popup system.
         */
        do_action('wpdesk_rating_petition_postpone', $this->plugin_slug);
        wp_send_json_success(['message' => __('Postponed.', 'flexible-shipping')]);
    }
    private function build_rating_url(int $rating): string
    {
        if (!empty($this->rating_url)) {
            return $this->rating_url;
        }
        $base = 'https://wordpress.org/support/plugin/' . rawurlencode($this->plugin_slug) . '/reviews/';
        return add_query_arg(['rate' => max(1, min(5, $rating))], $base) . '#new-post';
    }
    public function get_ajax_action(): string
    {
        return $this->plugin_slug . '_' . self::AJAX_ACTION;
    }
    public function get_nonce(): string
    {
        return wp_create_nonce($this->get_ajax_action());
    }
    public function get_plugin_slug(): string
    {
        return $this->plugin_slug;
    }
    public function get_ajax_url(): string
    {
        return admin_url('admin-ajax.php');
    }
}
