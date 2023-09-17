<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Wysiwyg extends BasicContent {
  protected $tabs = '';
  protected $toolbar = '';
  protected $allowMediaUpload = true;
  protected $delayed = false;

  protected $_html = '';

  public function html() {
    return $this->_html;
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
      case 'tabs':
        $this->setTabs($v);
        break;
      case 'toolbar':
        $this->setToolbar($v);
        break;
      case 'media_upload':
        $this->setAllowsMediaUpload($v);
        break;
      case 'delay':
        $this->setDelayed($v);
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

  public function getTabs() {
    return $this->tabs;
  }

  public function setTabs($tabs) {
    if (!is_string($tabs)) {
      return;
    }

    $this->tabs = $tabs;
  }

  public function getToolbar() {
    return $this->toolbar;
  }

  public function setToolbar($toolbar) {
    if (!is_string($toolbar)) {
      return;
    }

    $this->toolbar = $toolbar;
  }

  public function allowsMediaUpload() {
    return $this->allowMediaUpload;
  }

  public function setAllowsMediaUpload($allows) {
    $this->allowMediaUpload = (bool) $allows;
  }

  public function isDelayed() {
    return $this->delayed;
  }

  public function setDelayed($delayed) {
    $this->delayed = (bool) $delayed;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_string($value)) {
      $this->_html = '';
      return;
    }

    $this->_html = $value;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'wysiwyg',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'tabs' => $this->tabs,
      'toolbar' => $this->toolbar,
      'media_upload' => (int) $this->allowMediaUpload,
      'delay' => (int) $this->delayed,
    ];
  }
}
