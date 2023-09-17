<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class TrueFalse extends BasicContent {
  protected $message = '';
  protected $ui = false;
  protected $uiOnText = '';
  protected $uiOffText = '';

  protected $_value = false;

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
      case 'message':
        $this->setMessage($v);
        break;
      case 'ui':
        $this->setWithUi($v);
        break;
      case 'ui_on_text':
        $this->setUiOnText($v);
        break;
      case 'ui_off_text':
        $this->setUiOffText($v);
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

  public function getMessage() {
    return $this->message;
  }

  public function setMessage($message) {
    if (!is_string($message)) {
      return;
    }

    $this->message = $message;
  }

  public function isWithUi() {
    return $this->ui;
  }

  public function setWithUi($withUi) {
    $this->ui = (bool) $withUi;
  }

  public function getUiOnText() {
    return $this->uiOnText;
  }

  public function setUiOnText($uiOnText) {
    if (!is_string($uiOnText)) {
      return;
    }

    $this->uiOnText = $uiOnText;
  }

  public function getUiOffText() {
    return $this->uiOffText;
  }

  public function setUiOffText($uiOffText) {
    if (!is_string($uiOffText)) {
      return;
    }

    $this->uiOffText = $uiOffText;
  }

  public function isToggled() {
    return (bool) $this->_value;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_scalar($value)) {
      $this->_value = false;
      return;
    }

    $this->_value = (bool) $value;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'true_false',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'message' => $this->message,
      'ui' => (int) $this->ui,
      'ui_on_text' => $this->uiOnText,
      'ui_off_text' => $this->uiOffText,
    ];
  }
}
