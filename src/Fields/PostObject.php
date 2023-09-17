<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;
use WP_Post;

class PostObject extends BasicContent {
  protected $postTypes = [];
  protected $taxonomies = [];
  protected $allowNull = false;
  protected $allowMultiple = false;

  protected $_objects = [];

  public function objects() {
    return $this->_objects;
  }

  public function firstObject() {
    if (empty($this->_objects)) {
      return null;
    }

    return reset($this->_objects);
  }

  public function count() {
    return count($this->_objects);
  }

  public function isEmpty() {
    return empty($this->_objects);
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
      case 'post_types':
        $this->setPostTypes($v);
        break;
      case 'taxonomies':
        $this->setTaxonomies($v);
        break;
      case 'allow_null':
        $this->setAllowsNull($v);
        break;
      case 'multiple':
        $this->setAllowsMultiple($v);
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

  public function getPostTypes() {
    return $this->postTypes;
  }

  public function setPostTypes($postTypes) {
    if (!is_array($postTypes)) {
      return;
    }

    $this->postTypes = $postTypes;
  }

  public function getTaxonomies() {
    return $this->taxonomies;
  }

  public function setTaxonomies($taxonomies) {
    if (!is_array($taxonomies)) {
      return;
    }

    $this->taxonomies = $taxonomies;
  }

  public function allowsNull() {
    return $this->allowNull;
  }

  public function setAllowsNull($allows) {
    $this->allowNull = (bool) $allows;
  }

  public function allowsMultiple() {
    return $this->allowMultiple;
  }

  public function setAllowsMultiple($allows) {
    $this->allowMultiple = (bool) $allows;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    $this->_objects = [];

    $objs = [];
    if (!is_array($value)) {
      $objs[] = $value;
    } else {
      $objs = $value;
    }

    foreach ($objs as $obj) {
      if (!($obj instanceof WP_Post)) {
        continue;
      }

      $this->_objects[] = $obj;
    }
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'post_object',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'return_format' => 'object',
      'post_type' => $this->postTypes,
      'taxonomy' => $this->taxonomies,
      'allow_null' => (int) $this->allowNull,
      'multiple' => (int) $this->allowMultiple,
    ];
  }
}
