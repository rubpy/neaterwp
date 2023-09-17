<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Radio extends BasicContent {
  protected $choices = [];
  protected $layout = 'vertical';
  protected $allowNull = false;

  protected $_selected = '';

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
      case 'allow_null':
        $this->setAllowsNull($v);
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

  public function allowsNull() {
    return $this->allowNull;
  }

  public function setAllowsNull($allows) {
    $this->allowNull = (bool) $allows;
  }

  public function selected() {
    return $this->_selected;
  }

  public function selectedLabel() {
    if (!isset($this->choices[$this->_selected])) {
      return '';
    }

    return $this->choices[$this->_selected];
  }

  public function isSelected($option) {
    if (!is_string($option)) {
      return false;
    }

    return ($this->_selected === $option);
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

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_string($value) || !isset($this->choices[$value])) {
      $this->_selected = '';
      return;
    }

    $this->_selected = $value;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'radio',

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
      'allow_null' => $this->allowNull,
      'layout' => $this->layout,
    ];
  }
}
