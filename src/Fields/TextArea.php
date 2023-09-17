<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class TextArea extends BasicContent {
  protected $newLines = false;
  protected $maxLength = -1;
  protected $placeholder = '';
  protected $rows = -1;
  protected $readonly = false;
  protected $disabled = false;

  protected $_text = '';

  public function text() {
    return $this->_text;
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
      case 'new_lines':
        $this->setNewLines($v);
        break;
      case 'max_length':
        $this->setMaxLength($v);
        break;
      case 'placeholder':
        $this->setPlaceholder($v);
        break;
      case 'rows':
        $this->setRows($v);
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

  public function getNewLines() {
    return $this->newLines;
  }

  public function setNewLines($newLines) {
    if (!is_string($newLines)) {
      return;
    }

    $this->newLines = $newLines;
  }

  public function getRows() {
    return $this->rows;
  }

  public function setRows($rows) {
    if (!is_numeric($rows)) {
      return;
    }

    $this->rows = (int) $rows;
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
      'type' => 'textarea',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'new_lines' => $this->newLines,
      'maxlength' => ($this->maxLength > -1 ? $this->maxLength : ''),
      'placeholder' => $this->placeholder,
      'rows' => ($this->rows > -1 ? $this->rows : ''),
      'readonly' => $this->readonly,
      'disabled' => $this->disabled,
    ];
  }
}
