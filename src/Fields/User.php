<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;
use WP_User;

class User extends BasicContent {
  protected $role = '';
  protected $allowMultiple = false;
  protected $allowNull = false;

  protected $_users = [];

  public function users() {
    return $this->_users;
  }

  public function firstUser() {
    if (empty($this->_users)) {
      return null;
    }

    return reset($this->_users);
  }

  public function count() {
    return count($this->_users);
  }

  public function isEmpty() {
    return empty($this->_users);
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
      case 'role':
        $this->setRole($v);
        break;
      case 'multiple':
        $this->setAllowsMultiple($v);
        break;
      case 'allow_null':
        $this->setAllowsNull($v);
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

  public function getRole() {
    return $this->role;
  }

  public function setRole($role) {
    if (!is_string($role)) {
      return;
    }

    $this->role = $role;
  }

  public function allowsMultiple() {
    return $this->allowMultiple;
  }

  public function setAllowsMultiple($allows) {
    $this->allowMultiple = (bool) $allows;
  }

  public function allowsNull() {
    return $this->allowNull;
  }

  public function setAllowsNull($allows) {
    $this->allowNull = (bool) $allows;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    $this->_users = [];

    $users = [];
    if (!is_array($value)) {
      $users[] = $value;
    } else {
      $users = $value;
    }

    foreach ($users as $user) {
      if (!($user instanceof WP_User)) {
        continue;
      }

      $this->_users[] = $user;
    }
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'user',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'return_format' => 'object',
      'role' => $this->role,
      'multiple' => $this->allowMultiple,
      'allow_null' => $this->allowNull,
    ];
  }
}
