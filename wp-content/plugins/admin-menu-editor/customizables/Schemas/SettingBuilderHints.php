<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Schemas;

use YahnisElsts\AdminMenuEditor\Customizable\Settings\AbstractSetting;

class SettingBuilderHints {
	protected $params = [];

	/**
	 * @var null|class-string<AbstractSetting>
	 */
	protected $classNameHint = null;

	/**
	 * @var array<string,string>
	 */
	protected $paramReferences = [];

	public function addParams(array $settingParams) {
		foreach ($settingParams as $key => $value) {
			$this->params[$key] = $value;
		}
		return $this;
	}

	public function getParams() {
		return $this->params;
	}

	/**
	 * @param class-string<AbstractSetting> $className
	 * @return $this
	 */
	public function setClassHint($className) {
		$this->classNameHint = $className;
		return $this;
	}

	public function getClassHint() {
		return $this->classNameHint;
	}

	public function addSettingReference($paramName, $siblingSettingKey) {
		$this->paramReferences[$paramName] = $siblingSettingKey;
		return $this;
	}

	public function getSettingReferences() {
		return $this->paramReferences;
	}
}