<?php
namespace STM_GDPR\includes\plugins;

use STM_GDPR\includes\STM_Helpers;

class STM_GravityForms
{
    const SLUG = 'gravity_forms';

    private static $instance = null;

	public function stm_gravityforms_updateForms() {

		if (!class_exists('\GFAPI')) {
			return;
		}
			
		if( STM_Helpers::stm_helpers_isEnabled(STM_GDPR_PREFIX . 'plugins', self::SLUG) ) {
			$this->stm_gravityforms_addCheckbox();
		}else {
			$this->stm_gravityforms_removeCheckbox();
		}
	}

	public function stm_gravityforms_addCheckbox() {

		foreach ($this->stm_gravityforms_getForms() as $form) {

			$updated = false;
			$choices = array(
				array(
					'text' => STM_Helpers::stm_helpers_checkboxText(self::SLUG),
					'value' => 'true',
					'isSelected' => false
				)
			);

			foreach ($form['fields'] as &$field) {
				if (isset($field->stm_gdpr) && $field->stm_gdpr === true) {
					$field['choices'] = $choices;
					$updated = true;
				}
			}

			if (!$updated) {

				$lastField = array_values(array_slice($form['fields'], -1));
				$lastField = (isset($lastField[0])) ? $lastField[0] : false;
				$id = (!empty($lastField)) ? (int)$lastField['id'] + 1 : 1;

				$args = array(
					'id' => $id,
					'type' => 'checkbox',
					'label' => __('GDPR Accepted On', 'gdpr-compliance-cookie-consent'),
					'labelPlacement' => 'hidden_label',
					'isRequired' => true,
					'enableChoiceValue' => true,
					'choices' => $choices,
					'inputs' => array(
						array(
							'id' => $id . '.1',
							'label' => STM_Helpers::stm_helpers_checkboxText(self::SLUG),
							'name' => 'stm_gdpr'
						)
					),
					'stm_gdpr' => true
				);

				$form['fields'][] = $args;

			}

			\GFAPI::update_form($form, $form['id']);

		}

	}

	public function stm_gravityforms_removeCheckbox() {

		foreach ($this->stm_gravityforms_getForms() as $form) {

			foreach ($form['fields'] as $index => $field) {

				if (isset($field['stm_gdpr']) && $field['stm_gdpr'] === true) {

					unset($form['fields'][$index]);
				}
			}
			
			\GFAPI::update_form($form, $form['id']);

		}

	}

	public function stm_gravityforms_displayOverviewDate($value = '', $formID = 0, $fieldID = 0, $entry = array()) {

		if (empty($value)) {

			$id = self::stm_gravityforms_getCheckboxId($formID);

			if (!empty($id) && $fieldID === $id) {

				$value = (!empty($entry[$fieldID])) ? $entry[$fieldID] : __('Not accepted.', 'gdpr-compliance-cookie-consent');

			}

		}

		return $value;
	}

	public function stm_gravityforms_displayDate($value, $form = array()) {

		$fieldID = self::stm_gravityforms_getCheckboxId($form['form_id']);

		if (!empty($fieldID) && isset($value[$fieldID])) {

			if (empty($value[$fieldID])) {
				$value = __('Not accepted.', 'gdpr-compliance-cookie-consent');
			}

		}

		return $value;
	}

	public function stm_gravityforms_displayOverviewDateColumn($columns = array()) {

		$key = array_search(STM_Helpers::stm_helpers_checkboxText(self::SLUG), $columns);

		if (!empty($key) && isset($columns[$key])) {

			$columns[$key] = __('GDPR Accepted On', 'gdpr-compliance-cookie-consent');

		}

		return $columns;
	}

	public function stm_gravityforms_addDate($value = '', $lead = array(), \GF_Field $field) {

		if (isset($field['stm_gdpr']) && $field['stm_gdpr'] === true) {

			if (!empty($value)) {

				$date = STM_Helpers::stm_helpers_localDate(get_option('date_format') . ' ' . get_option('time_format'), time());

				$value = sprintf(__('Accepted on %s.', 'gdpr-compliance-cookie-consent'), $date);

			} else {

				$value = __('Not accepted.', 'gdpr-compliance-cookie-consent');

			}
		}

		return $value;
	}

	public function stm_gravityforms_validate($validation = array()) {

		$form = $validation['form'];

		foreach ($form['fields'] as &$field) {

			if (isset($field['stm_gdpr']) && $field['stm_gdpr'] === true) {

				if (isset($field['failed_validation']) && $field['failed_validation'] === true) {

					$field['validation_message'] = STM_Helpers::stm_helpers_errorMessage(self::SLUG);
				}

			}

		}

		$validation['form'] = $form;

		return $validation;
	}

	private static function stm_gravityforms_getCheckboxId($formID = 0) {

		$form = \GFFormsModel::get_form_meta($formID);

		foreach ($form['fields'] as $field) {

			if (isset($field['stm_gdpr']) && $field['stm_gdpr'] === true) {
				if (isset($field['inputs'][0]['id'])) {
					return $field['inputs'][0]['id'];
				}
			}

		}

		return 0;
	}

	public function stm_gravityforms_getForms() {

		$forms = array();

		if (class_exists('\GFAPI')) {
			$forms = \GFAPI::get_forms();
		}

		return $forms;
	}

	public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;

	}

}