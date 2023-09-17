<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class URL extends BasicContent {
  protected $placeholder = '';
  protected $readonly = false;
  protected $disabled = false;

  protected $_url = '';

  public function url() {
    return $this->_url;
  }

  public function isEmpty() {
    return empty($this->_url);
  }

  protected function parseExtraOptions($options) {
    if (!is_array($options)) {
      return [];
    }

    $unknownOptions = [];
    foreach ($options as $k => $v) {
      if (!is_string($k)) {
        continue;
      }

      switch ($k) {
      case 'placeholder':
        $this->setPlaceholder($v);
        break;
      case 'readonly':
        $this->setReadonly($v);
        break;
      case 'disabled':
        $this->setDisabled($v);
        break;
      default:
        {
          $unknownOptions[$k] = $v;
          break;
        }
      }
    }

    return $unknownOptions;
  }

  public function getPlaceholder() {
    return $this->placeholder;
  }

  public function setPlaceholder($placeholder) {
    if (!is_string($placeholder)) {
      return;
    }

    $this->placeholder = $placeholder;
  }

  public function isReadonly() {
    return $this->readonly;
  }

  public function setReadonly($readonly) {
    $this->readonly = (bool) $readonly;
  }

  public function isDisabled() {
    return $this->disabled;
  }

  public function setDisabled($disabled) {
    $this->disabled = (bool) $disabled;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_string($value)) {
      $this->_url = '';
      return;
    }

    $this->_url = $value;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'url',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'placeholder' => $this->placeholder,
      'readonly' => (int) $this->readonly,
      'disabled' => (int) $this->disabled,
    ];
  }
}
