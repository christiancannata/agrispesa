<?php

namespace FSVendor;

/**
 * @var string $petition_text
 * @var string $rating_text
 * @var string $nonce
 * @var string $ajax_url
 * @var string $plugin_slug
 * @var string $ajax_action
 * @var string $user_email
 */
?>
<div
    id="wpdesk-rating-petition-popup"
    data-petition-text="<?php 
echo \esc_attr($petition_text);
?>"
    data-rating-text="<?php 
echo \esc_attr($rating_text);
?>"
    data-nonce="<?php 
echo \esc_attr($nonce);
?>"
    data-ajax-url="<?php 
echo \esc_url($ajax_url);
?>"
    data-plugin-slug="<?php 
echo \esc_attr($plugin_slug);
?>"
    data-ajax-action="<?php 
echo \esc_attr($ajax_action);
?>"
    data-user-email="<?php 
echo \esc_attr($user_email);
?>"
></div>
<?php 
