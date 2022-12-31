<?php
namespace STM_GDPR\includes\plugins;

use STM_GDPR\includes\STM_Helpers;

class STM_WordPress
{
    const SLUG = 'wordpress';

    private static $instance = null;

	public function stm_wordpress_addCheckbox( $submit = '' ) {

		$checkbox = apply_filters(
			STM_GDPR_PREFIX . 'wordpress_checkbox',
			'<p class="' . STM_GDPR_SLUG . '-checkbox"><label><input type="checkbox" name="' . STM_GDPR_SLUG . '" id="' . STM_GDPR_SLUG . '" value="1" />' . STM_Helpers::stm_helpers_checkboxText(self::SLUG) . ' <abbr class="required" title="' . esc_attr__('required', 'gdpr-compliance-cookie-consent') . '">*</abbr></label></p>',
			$submit
		);

		return $checkbox . $submit;
	}

	public function stm_wordpress_displayError() {

		if (!isset($_POST[STM_GDPR_SLUG])) {

			wp_die(
				'<p>' . sprintf(
					__('<strong>ERROR</strong>: %s', 'gdpr-compliance-cookie-consent'),
					Helpers::errorMessage(self::SLUG)
				) . '</p>',
				__('Comment Submission Failure'),
				array('back_link' => true)
			);

		}

	}

	public function stm_wordpress_addCommentMeta($commentId = 0) {

		if (isset($_POST[STM_GDPR_SLUG]) && !empty($commentId)) {

			add_comment_meta($commentId, STM_GDPR_SLUG, time());

		}

	}

	public function stm_wordpress_displayMetaColumn($columns = array()) {

		$columns[STM_GDPR_SLUG] = esc_html__('GDPR Accepted On', 'gdpr-compliance-cookie-consent');

		return $columns;
	}

	public function stm_wordpress_displayCommentOverview($column = '', $commentId = 0) {

		if ($column === STM_GDPR_SLUG) {

			$date = get_comment_meta($commentId, STM_GDPR_SLUG, true);
			$value = (!empty($date)) ? STM_Helpers::stm_helpers_localDate(get_option('date_format') . ' ' . get_option('time_format'), $date) : esc_html__('Not accepted.', 'gdpr-compliance-cookie-consent');
			echo $value;

		}

		return $column;
	}

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}