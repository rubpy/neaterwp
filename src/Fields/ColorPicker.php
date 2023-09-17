<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class ColorPicker extends BasicContent {
  protected $enableOpacity = false;

  protected $_color = [];

  public function isEmpty() {
    return empty($this->_color);
  }

  public function r() {
    if (!isset($this->_color['red']) || !is_int($this->_color['red'])) {
      return 0;
    }

    return min(255, max(0, $this->_color['red']));
  }

  public function g() {
    if (!isset($this->_color['green']) || !is_int($this->_color['green'])) {
      return 0;
    }

    return min(255, max(0, $this->_color['green']));
  }

  public function b() {
    if (!isset($this->_color['blue']) || !is_int($this->_color['blue'])) {
      return 0;
    }

    return min(255, max(0, $this->_color['blue']));
  }

  public function a() {
    if (!isset($this->_color['alpha']) || !is_numeric($this->_color['alpha'])) {
      return (float) 0.0;
    }

    $a = (float) $this->_color['alpha'];
    if ($a < 0.0) {
      $a = (float) 0.0;
    } elseif ($a > 1.0) {
      $a = (float) 1.0;
    }

    return $a;
  }

  public function rgb() {
    $r = $this->r();
    $g = $this->g();
    $b = $this->b();

    return sprintf('rgb(%d,%d,%d)', $r, $g, $b);
  }

  public function rgba() {
    $r = $this->r();
    $g = $this->g();
    $b = $this->b();
    $a = $this->a();

    return sprintf('rgba(%d,%d,%d,%f)', $r, $g, $b, $a);
  }

  public function hex($withAlpha = false, $withHash = true) {
    $r = $this->r();
    $g = $this->g();
    $b = $this->b();

    $h = '';
    if ($withHash) {
      $h = '#';
    }

    if ($withAlpha) {
      $a = $this->a();
      $h .= sprintf('%02x%02x%02x%02x', $r, $g, $b, min(255, (int) round($a * 255)));
    } else {
      $h .= sprintf('%02x%02x%02x', $r, $g, $b);
    }

    return $h;
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
      case 'enable_opacity':
        $this->setOpacityEnabled($v);
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

  public function isOpacityEnabled() {
    return $this->enableOpacity;
  }

  public function setOpacityEnabled($enable) {
    $this->enableOpacity = (bool) $enable;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_array($value)) {
      $this->_color = [];
      return;
    }

    $this->_color = $value;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'color_picker',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'return_format' => 'array',
      'enable_opacity' => (int) $this->enableOpacity,
    ];
  }
}
