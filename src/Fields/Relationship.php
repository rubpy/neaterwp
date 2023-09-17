<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;
use WP_Post;

class Relationship extends BasicContent {
  protected $postTypes = [];
  protected $taxonomies = [];
  protected $min = null;
  protected $max = null;
  protected $filters = ['search', 'post_type', 'taxonomy'];
  protected $elements = [];

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
      case 'min':
        $this->setMin($v);
        break;
      case 'max':
        $this->setMax($v);
        break;
      case 'filters':
        $this->setFilters($v);
        break;
      case 'elements':
        $this->setElements($v);
        break;
      default:{
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

  public function getMin() {
    return $this->min;
  }

  public function setMin($min) {
    if (is_numeric($min)) {
      $min = (int) $min;
    } else {
      $min = null;
    }

    $this->min = $min;
  }

  public function getMax() {
    return $this->max;
  }

  public function setMax($max) {
    if (is_numeric($max)) {
      $max = (int) $max;
    } else {
      $max = null;
    }

    $this->max = $max;
  }

  public function getFilters() {
    return $this->filters;
  }

  public function setFilters($filters) {
    if (!is_array($filters)) {
      return;
    }

    $this->filters = $filters;
  }

  public function getElements() {
    return $this->elements;
  }

  public function setElements($elements) {
    if (!is_array($elements)) {
      return;
    }

    $this->elements = $elements;
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
      'type' => 'relationship',

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
      'min' => ($this->min !== null ? $this->min : ''),
      'max' => ($this->max !== null ? $this->max : ''),
      'filters' => $this->filters,
      'elements' => $this->elements,
    ];
  }
}
