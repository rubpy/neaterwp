<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Embed extends BasicContent {
  protected $width = -1;
  protected $height = -1;

  protected $_embedHtml = '';

  public function html() {
    return $this->_embedHtml;
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
      case 'width':
        $this->setWidth($v);
        break;
      case 'height':
        $this->setHeight($v);
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

  public function getWidth() {
    return $this->width;
  }

  public function setWidth($width) {
    if (!is_numeric($width)) {
      return;
    }

    $this->width = max(-1, (int) $width);
  }

  public function getHeight() {
    return $this->height;
  }

  public function setHeight($height) {
    if (!is_numeric($height)) {
      return;
    }

    $this->height = max(-1, (int) $height);
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_string($value)) {
      $this->_embedHtml = '';
      return;
    }

    $this->_embedHtml = $value;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'oembed',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'width' => ($this->width > -1 ? $this->width : ''),
      'height' => ($this->height > -1 ? $this->height : ''),
    ];
  }
}
