<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;
use NeaterWP\Fields\BasicLayout;
use NeaterWP\Fields\Exception;

class RepeaterAccordion extends BasicLayout {
  protected $open = false;
  protected $multiExpand = false;
  protected $endpoint = false;

  protected $_fields = [];
  protected $_data = [];

  public function __construct($label = '', $fields = [], $options = []) {
    parent::__construct($label, $options);

    $this->setFields($fields);
  }

  public function __clone() {
    $clonedFields = [];

    foreach ($this->_fields as $fieldKey => $field) {
      $clonedFields[$fieldKey] = clone $field;
    }

    $this->_fields = $clonedFields;
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
      case 'open':
        $this->setOpen($v);
        break;
      case 'multi_expand':
        $this->setMultiExpand($v);
        break;
      case 'endpoint':
        $this->setEndpoint($v);
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

  public function getOpen() {
    return $this->open;
  }

  public function setOpen($open) {
    $this->open = (bool) $open;
  }

  public function getMultiExpand() {
    return $this->multiExpand;
  }

  public function setMultiExpand($multiExpand) {
    $this->multiExpand = (bool) $multiExpand;
  }

  public function isEndpoint() {
    return $this->endpoint;
  }

  public function setEndpoint($endpoint) {
    $this->endpoint = (bool) $endpoint;
  }

  public function getFields() {
    return $this->_fields;
  }

  public function setFields($fields) {
    if (!is_array($fields)) {
      return;
    }

    foreach ($fields as $fieldKey => $field) {
      if (!is_string($fieldKey)) {
        continue;
      }

      if ($field instanceof BasicContent) {
        $this->_fields[$fieldKey] = $field;
      } elseif ($field instanceof BasicLayout) {
        throw new Exception($this, __METHOD__ . ': accordion field cannot be a layout field (key "' . $fieldKey . '")');
      }
    }
  }

  public function getField($key, $expectedClass = null) {
    if ($expectedClass !== null && !is_string($expectedClass)) {
      throw new Exception($this, __METHOD__ . ': invalid expected class');
    }

    if (!is_string($key) || !isset($this->_data[$key])) {
      if ($expectedClass !== null) {
        throw new Exception($this, __METHOD__ . ': accordion does not contain key' . (is_string($key) ? ' (' . $key . ')' : ''));
      }

      return null;
    }

    $field = $this->_data[$key];
    if (!class_exists($expectedClass) || !($field instanceof $expectedClass)) {
      throw new Exception($this, __METHOD__ . ': accordion field "' . $key . '" is not of type ' . $expectedClass);
    }

    return $field;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_array($value)) {
      $this->_data = [];
      return;
    }

    $accordionKey = self::makeFieldKey($parentKey, $key);

    $this->_data = [];
    foreach ($this->_fields as $subKey => $subField) {
      if (!is_string($subKey)) {
        continue;
      }

      if (!($subField instanceof Basic)) {
        continue;
      }

      $clonedField = clone $subField;
      $clonedField->lockLoadACF();

      $subFieldKey = self::makeFieldKey($accordionKey, $subKey);
      if (isset($value[$subFieldKey])) {
        $clonedField->parseValue($value[$subFieldKey], $pageId, $accordionKey, $subKey);
      }

      $this->_data[$subKey] = $clonedField;
    }
  }

  public function loadACF($pageId, $parentKey, $key) {}

  public function asACF($parentKey, $key) {
    $accordionKey = self::makeFieldKey($parentKey, $key);
    $acfFields = [];

    $acfFields[$key] = [
      'type' => 'accordion',

      'key' => 'field_' . $accordionKey,
      'name' => $accordionKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,

      'open' => (int) $this->open,
      'multi_expand' => (int) $this->multiExpand,
      'endpoint' => (int) $this->endpoint,
    ];

    foreach ($this->_fields as $subKey => $subField) {
      if (!is_string($subKey)) {
        continue;
      }

      if ($subField instanceof BasicContent) {
        $subFieldKey = self::makeFieldKey($accordionKey, $subKey);

        $acfFields[$subFieldKey] = $subField->asACF($accordionKey, $subKey);
      } elseif ($subField instanceof BasicLayout) {
        throw new Exception($this, __METHOD__ . ': accordion field cannot be a layout field (key "' . $subKey . '")');
      } else {
        continue;
      }
    }

    return $acfFields;
  }
}
