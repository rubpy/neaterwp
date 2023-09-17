<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;
use NeaterWP\Fields\BasicLayout;
use NeaterWP\Fields\Exception;

class Tab extends BasicLayout {
  protected $placement = 'top';
  protected $endpoint = false;

  protected $_fields = [];

  protected $_pageId = '';
  protected $_parentKey = '';
  protected $_selfKey = '';

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
      case 'placement':$this->setPlacement($v);
        break;
      case 'endpoint':$this->setEndpoint($v);
        break;
      default:{
          $unknownOptions[$k] = $v;
          break;
        }
      }
    }

    return $unknownOptions;
  }

  public function getPlacement() {
    return $this->placement;
  }

  public function setPlacement($placement) {
    if (!is_string($placement)) {
      return;
    }

    $this->placement = $placement;
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
        throw new Exception($this, __METHOD__ . ': tab field cannot be a layout field (key "' . $fieldKey . '")');
      }
    }
  }

  public function getField($key, $expectedClass = null) {
    if ($expectedClass !== null && !is_string($expectedClass)) {
      throw new Exception($this, __METHOD__ . ': invalid expected class');
    }

    if (!is_string($key) || !isset($this->_fields[$key])) {
      if ($expectedClass !== null) {
        throw new Exception($this, __METHOD__ . ': tab does not contain key' . (is_string($key) ? ' (' . $key . ')' : ''));
      }

      return null;
    }

    $field = $this->_fields[$key];
    if (!class_exists($expectedClass) || !($field instanceof $expectedClass)) {
      throw new Exception($this, __METHOD__ . ': tab field "' . $key . '" is not of type ' . $expectedClass);
    }

    if (!empty($this->_pageId) && !empty($this->_parentKey)) {
      $tabKey = self::makeFieldKey($this->_parentKey, $this->_selfKey);

      $field->loadACF($this->_pageId, $tabKey, $key);
    }

    return $field;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {}

  public function loadACF($pageId, $parentKey, $key) {
    if (!is_scalar($pageId) || !is_string($parentKey) || !is_string($key)) {
      throw new Exception($this, __METHOD__ . ': invalid use of tab API');
    }

    $this->_pageId = (string) $pageId;
    $this->_parentKey = $parentKey;
    $this->_selfKey = $key;
  }

  public function asACF($parentKey, $key) {
    $tabKey = self::makeFieldKey($parentKey, $key);
    $acfFields = [];

    // NOTE(rubpy): the order of [tab, tabField1, tabField2, ...] is crucial,
    // but since associative arrays in PHP are implemented as ordered hash
    // tables, no additional ordering scheme is needed here (i.e., to maintain
    // the order despite using a non-sequential array).

    $acfFields[$key] = [
      'type' => 'tab',

      'key' => 'field_' . $tabKey,
      'name' => $tabKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,

      'placement' => $this->placement,
      'endpoint' => (int) $this->endpoint,
    ];

    foreach ($this->_fields as $subKey => $subField) {
      if (!is_string($subKey)) {
        continue;
      }

      if ($subField instanceof BasicContent) {
        $subFieldKey = self::makeFieldKey($tabKey, $subKey);

        $acfFields[$subFieldKey] = $subField->asACF($tabKey, $subKey);
      } elseif ($subField instanceof BasicLayout) {
        throw new Exception($this, __METHOD__ . ': tab field cannot be a layout field (key "' . $subKey . '")');
      }
    }

    return $acfFields;
  }
}
