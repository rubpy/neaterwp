<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Message extends BasicContent {
  protected $message = '';
  protected $escapeHtml = false;
  protected $newLines = 'wpautop';

  public function __construct($label = '', $message = '', $options = []) {
    parent::__construct($label, $options);

    $this->setMessage($message);
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
      case 'esc_html':
        $this->setEscapeHtml($v);
        break;
      case 'new_lines':
        $this->setNewLines($v);
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

  public function escapesHtml() {
    return $this->escapeHtml;
  }

  public function setEscapeHtml($escape) {
    $this->escapeHtml = (bool) $escape;
  }

  public function getNewLines() {
    return $this->newLines;
  }

  public function setNewLines($newLines) {
    if (!is_string($newLines)) {
      return;
    }

    $this->newLines = $newLines;
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

  public function parseValue($value, $pageId, $parentKey, $key) {}
  public function loadACF($pageId, $parentKey, $key) {}

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'message',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => '',

      'message' => $this->message,
      'esc_html' => (int) $this->escapeHtml,
      'new_lines' => $this->newLines,
    ];
  }
}
