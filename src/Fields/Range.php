<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Range extends BasicContent {
  protected $min = null;
  protected $max = null;
  protected $step = null;
  protected $prepend = '';
  protected $append = '';
  protected $readonly = false;
  protected $disabled = false;

  protected $_value = 0;

  public function __construct($label = '', $min = null, $max = null, $options = []) {
    parent::__construct($label, $options);

    $this->setMin($min);
    $this->setMax($max);
  }

  public function value() {
    return $this->_value;
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
      case 'prepend':
        $this->setPrepend($v);
        break;
      case 'append':
        $this->setAppend($v);
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

  public function getMin() {
    return $this->min;
  }

  public function setMin($min) {
    if (is_numeric($min)) {
      $min = (int) $min;
    } else {
      $min = null;
    }

    $this->min = $min;
  }

  public function getMax() {
    return $this->max;
  }

  public function setMax($max) {
    if (is_numeric($max)) {
      $max = (int) $max;
    } else {
      $max = null;
    }

    $this->max = $max;
  }

  public function getStep() {
    return $this->step;
  }

  public function setStep($step) {
    if (is_numeric($step)) {
      $step = (int) $step;
    } else {
      $step = null;
    }

    $this->step = $step;
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
    $num = 0;
    if (is_numeric($value)) {
      $num = (int) $value;
    }

    $this->_value = $num;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'range',

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
      'prepend' => $this->prepend,
      'append' => $this->append,
      'readonly' => $this->readonly,
      'disabled' => $this->disabled,
    ];
  }
}
