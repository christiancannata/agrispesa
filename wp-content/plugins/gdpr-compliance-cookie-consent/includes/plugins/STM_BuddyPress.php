<?php
namespace STM_GDPR\includes\plugins;

use STM_GDPR\includes\STM_Helpers;

class STM_BuddyPress
{
    const SLUG = 'buddypress';

	private static $instance = null;

	public function stm_buddypress_addCheckbox(){

		echo '<input id="stm_gdpr" class="stm_gdpr" type="checkbox" name="stm_gdpr" required />
		<label for="stm_gdpr">
			' . STM_Helpers::stm_helpers_checkboxText(self::SLUG) . '
		</label>';
	}

    public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}