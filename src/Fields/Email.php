<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Email extends BasicContent {
  protected $placeholder = '';
  protected $prepend = '';
  protected $append = '';

  protected $_email = '';

  public function email() {
    return $this->_email;
  }

  public function isEmpty() {
    return empty($this->_email);
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

  public function parseValue($value, $pageId, $parentKey, $key) {
    $email = '';
    if (is_scalar($value)) {
      $email = (string) $value;
    }

    $this->_email = $email;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'email',

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
    ];
  }
}
