<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Checkbox extends BasicContent {
  protected $choices = [];
  protected $layout = 'vertical';
  protected $toggle = false;

  protected $_option = '';

  public function __construct($label = '', $choices = [], $options = []) {
    parent::__construct($label, $options);

    $this->setChoices($choices);
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
      case 'layout':
        $this->setLayout($v);
        break;
      case 'toggle':
        $this->setToggle($v);
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

  public function getToggle() {
    return $this->toggle;
  }

  public function setToggle($toggle) {
    $this->toggle = (bool) $toggle;
  }

  public function getChoices() {
    return $this->choices;
  }

  public function setChoices($choices) {
    if (!is_array($choices)) {
      return;
    }

    $ch = [];
    foreach ($choices as $choiceKey => $choiceLabel) {
      if (!is_scalar($choiceKey)) {
        continue;
      }
      $choiceKey = (string) $choiceKey;

      if ($choiceLabel === null) {
        $choiceLabel = '';
      } elseif (!is_scalar($choiceLabel)) {
        continue;
      }
      $choiceLabel = (string) $choiceLabel;

      $ch[$choiceKey] = $choiceLabel;
    }

    $this->choices = $ch;
  }

  public function getLayout() {
    return $this->layout;
  }

  public function setLayout($layout) {
    if (!is_string($layout)) {
      return;
    }

    $this->layout = $layout;
  }

  public function isSelected($option) {
    if (!is_string($option)) {
      return false;
    }

    return in_array($option, $this->_selected);
  }

  public function selected($withLabels = false) {
    if (!$withLabels) {
      return $this->_selected;
    }

    $sel = [];
    foreach ($this->_selected as $opt) {
      if (!is_string($opt)
        || !isset($this->choices[$opt])) {
        continue;
      }

      $sel[$opt] = $this->choices[$opt];
    }

    return $sel;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_array($value)) {
      $this->_selected = [];
      return;
    }

    foreach ($value as $opt) {
      if (!is_string($opt)
        || !isset($this->choices[$opt])) {
        continue;
      }

      $this->_selected[] = $opt;
    }
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'checkbox',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'return_format' => 'value',
      'choices' => $this->choices,
      'toggle' => $this->toggle,
      'layout' => $this->layout,
    ];
  }
}
