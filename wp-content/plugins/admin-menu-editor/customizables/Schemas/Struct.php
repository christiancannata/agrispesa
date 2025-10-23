<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Schemas;

class Struct extends Schema {
	protected $convertEmptyStringsToNull = false;

	/**
	 * @var array<string,Schema>
	 */
	protected $fieldSchemas = [];
	protected $requiredFields = [];

	public function __construct($fieldSchemas = [], $label = null) {
		parent::__construct($label);

		$this->defaultValue([]);
		$this->fieldSchemas = $fieldSchemas;
	}

	/**
	 * Make some or all fields required.
	 *
	 * @param string[]|null $fieldNames Defaults to all fields.
	 * @return $this
	 */
	public function required(?array $fieldNames = null) {
		if ( $fieldNames === null ) {
			$fieldNames = array_keys($this->fieldSchemas);
		}
		$this->requiredFields = array_fill_keys($fieldNames, true);
		return $this;
	}

	/**
	 * Make some or all fields optional.
	 *
	 * @param string[]|null $fieldNames Defaults to all fields.
	 * @return $this
	 */
	public function optional(?array $fieldNames = null) {
		if ( $fieldNames === null ) {
			$this->requiredFields = [];
			return $this;
		}
		foreach ($fieldNames as $fieldName) {
			unset($this->requiredFields[$fieldName]);
		}
		return $this;
	}

	protected function isRequiredField($fieldName) {
		return !empty($this->requiredFields[$fieldName]);
	}

	public function parse($value, $errors = null, $stopOnFirstError = false) {
		$value = $this->checkForNull($value, $errors);
		if ( ($value === null) || is_wp_error($value) ) {
			return $value;
		}

		if ( !is_array($value) ) {
			return self::addError($errors, 'struct_value_invalid', 'Struct value must be an associative array');
		}

		$parsedValues = [];
		$foundErrors = false;
		foreach ($this->fieldSchemas as $field => $schema) {
			if ( array_key_exists($field, $value) ) {
				$parsedValue = $schema->parse($value[$field], $errors, $stopOnFirstError);
				if ( is_wp_error($parsedValue) ) {
					$errors = $parsedValue;
					$foundErrors = true;
					if ( $stopOnFirstError ) {
						break;
					}
				} else {
					$parsedValues[$field] = $parsedValue;
				}
			} else if ( $this->isRequiredField($field) ) {
				//If a required field is missing, but it has a default value, use that.
				if ( $schema->hasDefaultValue() ) {
					$parsedValues[$field] = $schema->getDefaultValue();
				} else {
					//Otherwise, it is an error.
					$errors = self::addError($errors, 'missing_field', 'Field "' . $field . '" is required');
					$foundErrors = true;
					if ( $stopOnFirstError ) {
						break;
					}
				}
			}
		}

		if ( $foundErrors ) {
			return $errors;
		} else {
			return $parsedValues;
		}
	}

	public function getFields() {
		return $this->fieldSchemas;
	}

	public function getSimplifiedDataType() {
		return 'map';
	}
}