<?php
namespace STM_GDPR\includes\plugins;

use STM_GDPR\includes\STM_Helpers;

class STM_MailChimp
{
    const SLUG = 'mailchimp';

    private static $instance = null;

	public function stm_mailchimp_addCheckbox( $content, $form, $element ) {

    	$content .= '<div class="stm_gdpr_checker"><input id="stm_gdpr" class="stm_gdpr" type="checkbox" name="stm_gdpr" required />
			<label for="stm_gdpr">
				' . STM_Helpers::stm_helpers_checkboxText(self::SLUG) . '
			</label></div>';

		return $content;
	}

	public function stm_mailchimp_displayError( $errors, $form ) {

		if ( empty( $_POST['stm_gdpr'] ) ) {
			$errors[] = STM_Helpers::stm_helpers_errorMessage(self::SLUG);
		}

		return $errors;
	}

	public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}