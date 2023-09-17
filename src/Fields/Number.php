<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Number extends BasicContent {
  protected $min = null;
  protected $max = null;
  protected $step = null;
  protected $placeholder = '';
  protected $prepend = '';
  protected $append = '';

  protected $_number = 0;

  public function number() {
    return $this->_number;
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
      case 'min':
        $this->setMin($v);
        break;
      case 'max':
        $this->setMax($v);
        break;
      case 'step':
        $this->setStep($v);
        break;
      case 'placeholder':
        $this->setPlaceholder($v);
        break;
      case 'prepend':
        $this->setPrepend($v);
        break;
      case 'append':
        $this->setAppend($v);
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

  public function getMin() {
    return $this->min;
  }

  public function setMin($min) {
    if (!is_numeric($min)) {
      return;
    }

    $this->min = $min;
  }

  public function getMax() {
    return $this->max;
  }

  public function setMax($max) {
    if (!is_numeric($max)) {
      return;
    }

    $this->max = $max;
  }

  public function getStep() {
    return $this->step;
  }

  public function setStep($step) {
    if (!is_numeric($step)) {
      return;
    }

    $this->step = $step;
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

  public function parseValue($value, $pageId, $parentKey, $key) {
    $number = 0;
    if (is_numeric($value)) {
      $number = $value + 0;
    }

    $this->_number = $number;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'number',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'min' => ($this->min !== null ? $this->min : ''),
      'max' => ($this->max !== null ? $this->max : ''),
      'step' => ($this->step !== null ? $this->step : ''),
      'placeholder' => $this->placeholder,
      'prepend' => $this->prepend,
      'append' => $this->append,
    ];
  }
}
