<?php

namespace NeaterWP\Fields;

use DateTime;
use NeaterWP\Fields\BasicContent;

class DateTimePicker extends BasicContent {
  protected $_date = '';
  protected $_dateInstance = null;
  protected $_format = 'Y-m-d H:i:s';

  public function raw() {
    if (!($this->_dateInstance instanceof DateTime)) {
      return null;
    }

    return $this->_dateInstance;
  }

  public function format($fmt) {
    if (!is_string($fmt)
      || !($this->_dateInstance instanceof DateTime)) {
      return '';
    }

    try {
      $s = $this->_dateInstance->format($fmt);
      if (!is_string($s)) {
        return '';
      }

      return $s;
    } catch (\Exception $e) {}

    return '';
  }

  public function datetime() {
    if (!($this->_dateInstance instanceof DateTime)) {
      return '';
    }

    try {
      $s = $this->_dateInstance->format($this->_format);
      if (!is_string($s)) {
        return '';
      }

      return $s;
    } catch (\Exception $e) {}

    return '';
  }

  public function timestamp() {
    if (!($this->_dateInstance instanceof DateTime)) {
      return 0;
    }

    try {
      $t = $this->_dateInstance->getTimestamp();
      if (!is_int($t)) {
        return 0;
      }

      return $t;
    } catch (\Exception $e) {}

    return 0;
  }

  public function isEmpty() {
    return empty($this->_date) || !($this->_dateInstance instanceof DateTime);
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_string($value)) {
      $this->_date = '';
      $this->_dateInstance = null;
      return;
    }

    try {
      $dt = DateTime::createFromFormat('!' . $this->_format, $value);
      if ($dt instanceof DateTime) {
        $this->_date = $value;
        $this->_dateInstance = $dt;
        return;
      }
    } catch (\Exception $e) {}

    $this->_date = '';
    $this->_dateInstance = null;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'date_time_picker',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'return_format' => $this->_format,
      'display_format' => $this->_format,
      'first_day' => 1,
    ];
  }
}
