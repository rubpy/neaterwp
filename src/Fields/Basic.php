<?php

namespace NeaterWP\Fields;

abstract class Basic {
  protected $label = '';
  protected $instructions = '';
  protected $required = false;
  protected $conditionals = [];
  protected $wrapper = [];
  protected $defaultValue = '';

  protected $options = [];

  protected $_value = null;
  protected $_acfLoadLocked = false;

  public function __construct($label = '', $options = []) {
    $this->setLabel($label);
    $this->setOptions($options);
  }

  public function getValue() {
    return $this->_value;
  }

  // ==================================================

  public function getLabel() {
    return $this->label;
  }

  public function setLabel($label) {
    if (!is_string($label)) {
      return;
    }

    $this->label = $label;
  }

  protected function parseExtraOptions($options) {
    if (!is_array($options)) {
      return [];
    }

    return $options;
  }

  public function getOptions() {
    return $this->options;
  }

  public function setOptions($options) {
    if (!is_array($options)) {
      return;
    }

    $extraOptions = [];
    foreach ($options as $k => $v) {
      if (!is_string($k)) {
        continue;
      }

      switch ($k) {
      case 'instructions':
        $this->setInstructions($v);
        break;
      case 'required':
        $this->setRequired($v);
        break;
      case 'conditionals':
        $this->setConditionals($v);
        break;
      case 'wrapper':
        $this->setWrapper($v);
        break;
      case 'default_value':
        $this->setDefaultValue($v);
        break;
      default:
        {
          $extraOptions[$k] = $v;
          break;
        }
      }
    }

    $extraOptions = $this->parseExtraOptions($extraOptions);
    if (is_array($extraOptions)) {
      $this->options = $extraOptions;
    }
  }

  // ==================================================

  public function getInstructions() {
    return $this->instructions;
  }

  public function setInstructions($instructions) {
    if (!is_string($instructions)) {
      return;
    }

    $this->instructions = $instructions;
  }

  public function isRequired() {
    return $this->required;
  }

  public function setRequired($required) {
    $this->required = (bool) $required;
  }

  public function getConditionals() {
    return $this->conditionals;
  }

  public function setConditionals($conditionals) {
    if (!is_array($conditionals)) {
      return;
    }

    $this->conditionals = $conditionals;
  }

  public function getWrapper() {
    return $this->wrapper;
  }

  public function setWrapper($wrapper) {
    if (!is_array($wrapper)) {
      return;
    }

    $this->wrapper = $wrapper;
  }

  public function getDefaultValue() {
    return $this->defaultValue;
  }

  public function setDefaultValue($defaultValue) {
    if (!is_scalar($defaultValue)) {
      return;
    }

    $this->defaultValue = $defaultValue;
  }

  // ==================================================

  protected static function keyToId($key) {
    if (!is_string($key)) {
      $key = '';
    }

    $hash = substr(md5($key), 0, 10);
    return $hash;
  }

  public static function makeFieldKey($parentFieldKey, $key) {
    if (!is_string($key)) {
      $key = '';
    }
    if (!is_string($parentFieldKey)) {
      $parentFieldKey = '';
    }

    $keyId = self::keyToId($key);
    $fieldKey = (!empty($parentFieldKey) ? $parentFieldKey . '_' : '') . $keyId;

    return $fieldKey;
  }

  // ==================================================

  public function parseValue($value, $pageId, $parentKey, $key) {}

  public function lockLoadACF() {$this->_acfLoadLocked = true;}
  public function unlockLoadACF() {$this->_acfLoadLocked = false;}

  public function loadACF($pageId, $parentKey, $key) {
    if ($this->_acfLoadLocked) {
      return;
    }

    $fieldKey = self::makeFieldKey($parentKey, $key);
    $value = get_field($fieldKey, $pageId);

    $this->parseValue($value, $pageId, $parentKey, $key);
  }

  abstract public function asACF($parentKey, $key);
}
