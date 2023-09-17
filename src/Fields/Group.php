<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;
use NeaterWP\Fields\BasicLayout;
use NeaterWP\Fields\Exception;
use NeaterWP\Fields\Tab;
use NeaterWP\Page;

class Group extends BasicLayout {
  protected $_fields = [];

  protected $location = [];
  protected $hideOnScreen = [];
  protected $order = -1;

  protected $_parentPage = null;
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

  public function withParentPage($page, $parentKey, $selfKey) {
    if (!($page instanceof Page)) {
      throw new Exception($this, __METHOD__ . ' expects page to be an instance of ' . Page::class . (is_object($page) ? ' (' . get_class($page) . ' given)' : ''));
    }
    if (!is_string($parentKey) || !is_string($selfKey)) {
      throw new Exception($this, __METHOD__ . ': invalid use of group API');
    }

    $cloned = clone $this;
    $cloned->_parentPage = $page;
    $cloned->_parentKey = $parentKey;
    $cloned->_selfKey = $selfKey;

    return $cloned;
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
      case 'location':
        $this->setLocation($v);
        break;
      case 'hide_on_screen':
        $this->setHideOnScreen($v);
        break;
      case 'order':
        $this->setOrder($v);
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

  public function getLocation() {
    return $this->location;
  }

  public function setLocation($location) {
    if (!is_array($location)) {
      return;
    }

    $this->location = $location;
  }

  public function getHideOnScreen() {
    return $this->location;
  }

  public function setHideOnScreen($hideOnScreen) {
    if (!is_array($hideOnScreen)) {
      return;
    }

    $this->hideOnScreen = $hideOnScreen;
  }

  public function getOrder() {
    return $this->order;
  }

  public function setOrder($order) {
    if (!is_scalar($order)) {
      return;
    }

    $this->order = (int) $order;
  }

  public function getFields() {
    return $this->_fields;
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
          throw new Exception($this, __METHOD__ . ': group that contains a tab cannot also have its own, unpaired/untabbed fields (key "' . $fieldKey . '")');
        }

        $this->_fields[$fieldKey] = $field;
      } elseif ($field instanceof BasicLayout) {
        if ($field instanceof Group) {
          throw new Exception($this, __METHOD__ . ': group field cannot be another group (key "' . $fieldKey . '")');
        }

        $this->_fields[$fieldKey] = $field;
      }
    }
  }

  public function getField($key, $expectedClass = null) {
    if ($expectedClass !== null && !is_string($expectedClass)) {
      throw new Exception($this, __METHOD__ . ': invalid expected class');
    }

    if (!is_string($key) || !isset($this->_fields[$key])) {
      if ($expectedClass !== null) {
        throw new Exception($this, __METHOD__ . ': group does not contain key' . (is_string($key) ? ' (' . $key . ')' : ''));
      }

      return null;
    }

    $field = $this->_fields[$key];
    if (!class_exists($expectedClass) || !($field instanceof $expectedClass)) {
      throw new Exception($this, __METHOD__ . ': group field "' . $key . '" is not of type ' . $expectedClass);
    }

    if ($this->_parentPage !== null) {
      $groupKey = self::makeFieldKey($this->_parentKey, $this->_selfKey);

      $field->loadACF($this->_parentPage->wpId(), $groupKey, $key);
    }

    return $field;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {}
  public function loadACF($pageId, $parentKey, $key) {}

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    $fields = [];
    foreach ($this->_fields as $subKey => $subField) {
      if (!is_string($subKey)) {
        continue;
      }

      if ($subField instanceof BasicContent) {
        $fields[$subKey] = $subField->asACF($fieldKey, $subKey);
      } elseif ($subField instanceof BasicLayout) {
        if ($subField instanceof Group) {
          throw new Exception($this, __METHOD__ . ': group field cannot be another group (key "' . $subKey . '")');
        }

        $subLayoutFields = $subField->asACF($fieldKey, $subKey);
        if (is_array($subLayoutFields)) {
          foreach ($subLayoutFields as $subLayoutKey => $subLayoutField) {
            if (!is_string($subLayoutKey) || !is_array($subLayoutField)) {
              throw new Exception($this, __METHOD__ . ': layout field generated a corrupt structure (group field key: "' . $subKey . '"' . (is_string($subLayoutKey) ? ', layout field key: "' . $subLayoutKey . '"' : '') . ')');
            }

            $fields[$subLayoutKey] = $subLayoutField;
          }
        }
      }
    }

    return [
      'type' => 'group',

      'key' => 'group_' . $fieldKey,
      'title' => $this->label,
      'location' => $this->location,
      'hide_on_screen' => $this->hideOnScreen,
      'menu_order' => $this->order,

      'fields' => $fields,
    ];
  }

  public static function settings() {return [];}
  public static function fields() {return [];}
}
