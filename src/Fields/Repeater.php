<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;
use NeaterWP\Fields\BasicLayout;
use NeaterWP\Fields\Group;
use NeaterWP\Fields\RepeaterRow;
use NeaterWP\Fields\Tab;

class Repeater extends BasicContent {
  protected $_fields = [];
  protected $_rows = [];

  protected $min = null;
  protected $max = null;
  protected $layout = '';
  protected $buttonLabel = '';

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
      case 'min':
        $this->setMin($v);
        break;
      case 'max':
        $this->setMax($v);
        break;
      case 'layout':
        $this->setLayout($v);
        break;
      case 'button_label':
        $this->setButtonLabel($v);
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

  public function getMin() {
    return $this->min;
  }

  public function setMin($min) {
    if (!is_scalar($min)) {
      $min = null;
    } else {
      $min = (int) $min;
    }

    $this->min = $min;
  }

  public function getMax() {
    return $this->max;
  }

  public function setMax($max) {
    if (!is_scalar($max)) {
      $max = null;
    } else {
      $max = (int) $max;
    }

    $this->max = $max;
  }

  public function getLayout() {
    return $this->layout;
  }

  public function setLayout($layout) {
    if (!is_string($layout)) {
      return;
    }

    $this->layout = $layout;
  }

  public function getButtonLabel() {
    return $this->buttonLabel;
  }

  public function setButtonLabel($buttonLabel) {
    if (!is_string($buttonLabel)) {
      return;
    }

    $this->buttonLabel = $buttonLabel;
  }

  public function setFields($fields) {
    if (!is_array($fields)) {
      return;
    }

    $containsTabs = false;
    foreach ($fields as $fieldKey => $field) {
      if ($field instanceof Tab) {
        $containsTabs = true;
        break;
      }
    }

    foreach ($fields as $fieldKey => $field) {
      if (!is_string($fieldKey)) {
        continue;
      }

      if ($field instanceof BasicContent) {
        if ($containsTabs) {
          throw new Exception($this, __METHOD__ . ': repeater that contains a tab cannot also have its own, unpaired/untabbed fields (key "' . $fieldKey . '")');
        }

        $this->_fields[$fieldKey] = $field;
      } elseif ($field instanceof BasicLayout) {
        if ($field instanceof Group) {
          throw new Exception($this, __METHOD__ . ': repeater field cannot be a group (key "' . $fieldKey . '")');
        }

        $this->_fields[$fieldKey] = $field;
      } else {
        continue;
      }
    }
  }

  public function forEach($callback) {
    if (!is_callable($callback)) {
      return;
    }

    foreach ($this->_rows as $row) {
      $ret = $callback(
        new RepeaterRow($row)
      );

      if ($ret === false || $ret === 0) {
        break;
      }
    }
  }

  public function rows() {
    $rows = [];
    foreach ($this->_rows as $row) {
      $rows[] = new RepeaterRow($row);
    }

    return $rows;
  }

  public function isEmpty() {
    return empty($this->_rows);
  }

  public function count() {
    return count($this->_rows);
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_array($value)) {
      $this->_rows = [];
      return;
    }

    $repeaterKey = self::makeFieldKey($parentKey, $key);

    $this->_rows = [];
    foreach ($value as $plainRow) {
      if (!is_array($plainRow)) {
        continue;
      }

      $row = [];
      foreach ($this->_fields as $subKey => $subField) {
        if (!is_string($subKey)) {
          continue;
        }

        $subFieldKey = self::makeFieldKey($repeaterKey, $subKey);
        if ($subField instanceof BasicContent) {
          $clonedField = clone $subField;
          $clonedField->lockLoadACF();

          if (isset($plainRow[$subFieldKey])) {
            $clonedField->parseValue($plainRow[$subFieldKey], $pageId, $repeaterKey, $subKey);
          }

          $row[$subKey] = $clonedField;
        } elseif ($subField instanceof BasicLayout) {
          $clonedField = clone $subField;
          $clonedField->lockLoadACF();

          $subLayoutFields = $clonedField->getFields();
          $subLayoutData = [];
          if (is_array($subLayoutFields)) {
            foreach ($subLayoutFields as $subLayoutKey => $subLayoutField) {
              $subLayoutFieldKey = self::makeFieldKey($subFieldKey, $subLayoutKey);

              if (isset($plainRow[$subLayoutFieldKey])) {
                $subLayoutData[$subLayoutFieldKey] = $plainRow[$subLayoutFieldKey];
              }
            }
          }

          $clonedField->parseValue($subLayoutData, $pageId, $repeaterKey, $subKey);
          $row[$subKey] = $clonedField;
        } else {
          continue;
        }
      }

      $this->_rows[] = $row;
    }
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    $repeaterKey = $fieldKey;

    $fields = [];
    foreach ($this->_fields as $subKey => $subField) {
      if (!is_string($subKey)) {
        continue;
      }

      if ($subField instanceof BasicContent) {
        $fields[$subKey] = $subField->asACF($repeaterKey, $subKey);
      } elseif ($subField instanceof BasicLayout) {
        if ($subField instanceof Group) {
          throw new Exception($this, __METHOD__ . ': repeater field cannot be a group (key "' . $subKey . '")');
        }

        $subLayoutFields = $subField->asACF($repeaterKey, $subKey);
        if (is_array($subLayoutFields)) {
          foreach ($subLayoutFields as $subLayoutKey => $subLayoutField) {
            if (!is_string($subLayoutKey) || !is_array($subLayoutField)) {
              throw new Exception($this, __METHOD__ . ': layout field generated a corrupt structure (group field key: "' . $subKey . '"' . (is_string($subLayoutKey) ? ', layout field key: "' . $subLayoutKey . '"' : '') . ')');
            }

            $fields[$subLayoutKey] = $subLayoutField;
          }
        }
      } else {
        continue;
      }
    }

    return [
      'type' => 'repeater',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'min' => ($this->min !== null ? $this->min : ''),
      'max' => ($this->max !== null ? $this->max : ''),
      'layout' => $this->layout,
      'button_label' => $this->buttonLabel,
      'sub_fields' => $fields,
    ];
  }
}
