<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Text extends BasicContent {
  protected $placeholder = '';
  protected $prepend = '';
  protected $append = '';
  protected $maxLength = -1;
  protected $readonly = false;
  protected $disabled = false;

  protected $_text = '';

  public function text() {
    return $this->_text;
  }

  public function isEmpty() {
    return empty($this->_text);
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
      case 'prepend':
        $this->setPrepend($v);
        break;
      case 'append':
        $this->setAppend($v);
        break;
      case 'max_length':
        $this->setMaxLength($v);
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

  public function getPrepend() {
    return $this->prepend;
  }

  public function setPrepend($prepend) {
    if (!is_string($prepend)) {
      return;
    }

    $this->prepend = $prepend;
  }

  public function getAppend() {
    return $this->append;
  }

  public function setAppend($append) {
    if (!is_string($append)) {
      return;
    }

    $this->append = $append;
  }

  public function getMaxLength() {
    return $this->maxLength;
  }

  public function setMaxLength($maxLength) {
    if (!is_int($maxLength)) {
      return;
    }
    if ($maxLength < -1) {
      $maxLength = -1;
    }

    $this->maxLength = $maxLength;
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
    $text = '';
    if (is_scalar($value)) {
      $text = (string) $value;
    }

    $this->_text = $text;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'text',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'placeholder' => $this->placeholder,
      'prepend' => $this->prepend,
      'append' => $this->append,
      'maxlength' => ($this->maxLength > -1 ? $this->maxLength : ''),
      'readonly' => $this->readonly,
      'disabled' => $this->disabled,
    ];
  }
}
