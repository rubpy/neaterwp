<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Link extends BasicContent {
  protected $_link = [];

  public function url() {
    if (!isset($this->_link['url'])) {
      return '';
    }

    return $this->_link['url'];
  }

  public function title() {
    if (!isset($this->_link['title'])) {
      return '';
    }

    return $this->_link['title'];
  }

  public function target() {
    if (!isset($this->_link['target'])) {
      return '';
    }

    return $this->_link['target'];
  }

  public function isEmpty() {
    return (!isset($this->_link['url']) || empty($this->_link['url']));
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_array($value)) {
      $this->_link = [];
      return;
    }

    $this->_link = $value;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'link',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'return_format' => 'array',
    ];
  }
}
