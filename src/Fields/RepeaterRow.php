<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\Exception;

class RepeaterRow {
  protected $_rowFields = [];

  public function __construct($rowFields = []) {
    if (is_array($rowFields)) {
      $this->_rowFields = $rowFields;
    }
  }

  public function getField($key, $expectedClass = null) {
    if ($expectedClass !== null && !is_string($expectedClass)) {
      throw new Exception(null, __METHOD__ . ': invalid expected class');
    }

    if (!is_string($key) || !isset($this->_rowFields[$key])) {
      if ($expectedClass !== null) {
        throw new Exception(null, __METHOD__ . ': row does not contain field of key' . (is_string($key) ? ' (' . $key . ')' : ''));
      }

      return null;
    }

    $field = $this->_rowFields[$key];
    if (!class_exists($expectedClass) || !($field instanceof $expectedClass)) {
      throw new Exception(null, __METHOD__ . ': row field "' . $key . '" is not of type ' . $expectedClass);
    }

    return $field;
  }
}
