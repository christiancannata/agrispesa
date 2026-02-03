<?php

namespace FSVendor;

/**
 * @var string $ajax_url
 * @var string $nonce
 * @var string $ajax_action
 * @var string $instance_id
 * @var string $plugin_slug
 */
?><div
	id="octolize-docs-chat"
	data-ajax_url="<?php 
echo \esc_url($ajax_url);
?>"
	data-nonce="<?php 
echo \esc_attr($nonce);
?>"
	data-ajax_action="<?php 
echo \esc_attr($ajax_action);
?>"
	data-instance_id="<?php 
echo \esc_attr($instance_id);
?>"
	data-plugin_slug="<?php 
echo \esc_attr($plugin_slug);
?>"
></div>
<?php 
