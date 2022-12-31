<?php
namespace STM_GDPR\includes\plugins;

use STM_GDPR\includes\STM_Helpers;

class STM_ContactForm7
{
    const SLUG = 'contact_form_7';

    private static $instance = null;

    public function stm_contactform7_updateForms() {

		if( STM_Helpers::stm_helpers_isEnabled(STM_GDPR_PREFIX . 'plugins', self::SLUG) ) {
			$this->stm_contactform7_addFormTags();
			$this->stm_contactform7_addFormMailTags();
		}else {
			$this->stm_contactform7_removeFormTags();
			$this->stm_contactform7_removeFormMailTags();
		}

	}

	public function stm_contactform7_addFormTag() {

		wpcf7_add_form_tag('stmgdpr', array($this, 'stm_contactform7_customTagHandler'));

	}

	public function customTagHandler($tag) {

		$tag = (is_array($tag)) ? new \WPCF7_FormTag($tag) : $tag;
		$return = '';

		if ($tag->type == 'stmgdpr') {

			$tag->name = 'stmgdpr';
			$label = (!empty($tag->labels[0])) ? esc_html($tag->labels[0]) : STM_Helpers::stm_helpers_checkboxText(self::SLUG);
			$class = wpcf7_form_controls_class($tag->type, 'wpcf7-validates-as-required');
			$validation_error = wpcf7_get_validation_error($tag->name);

			if ($validation_error) {
				$class .= ' wpcf7-not-valid';
			}

			$label_first = $tag->has_option('label_first');
			$use_label_element = $tag->has_option('use_label_element');
			$atts = wpcf7_format_atts(array(
				'class' => $tag->get_class_option($class),
				'id' => $tag->get_id_option(),
			));
			$item_atts = wpcf7_format_atts(array(
				'type' => 'checkbox',
				'name' => $tag->name,
				'value' => 1,
				'tabindex' => $tag->get_option('tabindex', 'signed_int', true),
				'aria-required' => 'true',
				'aria-invalid' => ($validation_error) ? 'true' : 'false',
			));

			if ($label_first) {
				$return = sprintf(
					'<span class="wpcf7-list-item-label">%1$s</span><input %2$s />',
					esc_html($label),
					$item_atts
				);
			} else {
				$return = sprintf(
					'<input %2$s /><span class="wpcf7-list-item-label">%1$s</span>',
					esc_html($label),
					$item_atts
				);
			}

			if ($use_label_element) {
				$return = '<label>' . $return . '</label>';
			}

			$return = '<span class="wpcf7-list-item">' . $return . '</span>';
			$return = sprintf(
				'<span class="wpcf7-form-control-wrap %1$s"><span %2$s>%3$s</span>%4$s</span>',
				sanitize_html_class($tag->name),
				$atts,
				$return,
				$validation_error
			);

		}

		return $return;
	}

	public function stm_contactform7_validate(\WPCF7_Validation $return, $tag) {

		$tag = (gettype($tag) == 'array') ? new \WPCF7_FormTag($tag) : $tag;

		if($tag->type == 'stmgdpr') {

			$tag->name = 'stmgdpr';
			$name = $tag->name;
			$value = (isset($_POST[$name])) ? filter_var($_POST[$name], FILTER_VALIDATE_BOOLEAN) : false;

			if ($value === false) {
				$return->invalidate($tag, STM_Helpers::stm_helpers_errorMessage(self::SLUG));
			}

		}

		return $return;
	}

	public function stm_contactform7_addMailMsg(\WPCF7_ContactForm $cf7) {

		$mail = $cf7->prop('mail');

		if (!empty($mail['body'])) {

			$submission = \WPCF7_Submission::get_instance();

			if (!empty($submission)) {

				$data = $submission->get_posted_data();

				if (isset($data['stmgdpr']) && $data['stmgdpr'] == 1) {
					$msg = STM_Helpers::stm_helpers_localDate(get_option('date_format') . ' ' . get_option('time_format'), time());
				} else {
					$msg = __('Not accepted.', 'gdpr-compliance-cookie-consent');
				}

				$return = __('GDPR accepted on:', 'gdpr-compliance-cookie-consent') . "\n$msg";
				$mail['body'] = str_replace('[stmgdpr]', $return, $mail['body']);
				$cf7->set_properties(array('mail' => $mail));

			}

		}

		return $cf7;
	}

	public function stm_contactform7_addFormTags() {

		foreach ($this->stm_contactform7_getForms() as $id) {

			$tag = '[stmgdpr "' . STM_Helpers::stm_helpers_checkboxText(self::SLUG) . '"]';
			$content = get_post_meta($id, '_form', true);
			preg_match('/(\[stmgdpr?.*\])/', $content, $matches);

			if (!empty($matches)) {
				$content = str_replace($matches[0], $tag, $content);
			} else {
				$pattern = '/(\[submit?.*\])/';
				preg_match($pattern, $content, $matches);

				if (!empty($matches)) {
					$content = preg_replace($pattern, "$tag\n\n" . $matches[0], $content);
				} else {
					$content = $content . "\n\n$tag";
				}
			}

			update_post_meta($id, '_form', $content);
		}
	}

	public function stm_contactform7_addFormMailTags() {

		foreach ($this->stm_contactform7_getForms() as $id) {

			$content = get_post_meta($id, '_mail', true);

			if (!empty($content)) {

				$tag = '[stmgdpr]';
				$body = $content['body'];
				preg_match('/(\[stmgdpr\])/', $body, $matches);

				if (empty($matches)) {
					$pattern = '/(--)/';
					preg_match($pattern, $body, $matches);

					if (!empty($matches)) {
						$body = preg_replace($pattern, "$tag\n\n" . $matches[0], $body);
					} else {
						$body = $body . "\n\n$tag";
					}
				}

				$content['body'] = $body;
				update_post_meta($id, '_mail', $content);
			}
		}

	}

	public function stm_contactform7_removeFormTags() {

		foreach ($this->stm_contactform7_getForms() as $id) {

			$content = get_post_meta($id, '_form', true);
			$pattern = '/(\n\n\[stmgdpr?.*\])/';
			preg_match($pattern, $content, $matches);

			if (!empty($matches)) {
				$content = preg_replace($pattern, '', $content);
				update_post_meta($id, '_form', $content);
			}

		}

	}

	public function stm_contactform7_removeFormMailTags() {

		foreach ($this->stm_contactform7_getForms() as $id) {

			$content = get_post_meta($id, '_mail', true);
			$pattern = '/(\n\n\[stmgdpr\])/';
			if(!empty($content['body'])) preg_match($pattern, $content['body'], $matches);

			if (!empty($matches) && !empty($content['body'])) {
				$content['body'] = preg_replace($pattern, '', $content['body']);
				update_post_meta($id, '_mail', $content);
			}

		}

	}

	public function stm_contactform7_getForms() {

		$forms = get_posts(array(
			'post_type' => 'wpcf7_contact_form',
			'posts_per_page' => -1,
			'fields' => 'ids'
		));

		return $forms;
	}

	public function collectData( $email ) {

		$search_criteria = array(
			'field_filters' => array(
				'value' => $email
			)
		);
		
		$entries = class_exists( 'GFAPI' ) ? GFAPI::get_entries( 0, $search_criteria ) : array();

		return (is_array($entries)) ? $entries : array();
	}

	public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}