<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;
use WP_Term;

class Taxonomy extends BasicContent {
  protected $taxonomy = 'category';
  protected $fieldType = 'checkbox';
  protected $allowMultiple = false;
  protected $allowNull = false;

  protected $_terms = [];

  public function terms() {
    return $this->_terms;
  }

  public function firstTerm() {
    if (empty($this->_terms)) {
      return null;
    }

    return reset($this->_terms);
  }

  public function count() {
    return count($this->_terms);
  }

  public function isEmpty() {
    return empty($this->_terms);
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
      case 'taxonomy':
        $this->setTaxonomy($v);
        break;
      case 'field_type':
        $this->setFieldType($v);
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

  public function getTaxonomy() {
    return $this->taxonomy;
  }

  public function setTaxonomy($taxonomy) {
    if (!is_string($taxonomy)) {
      return;
    }

    $this->taxonomy = $taxonomy;
  }

  public function getFieldType() {
    return $this->fieldType;
  }

  public function setFieldType($fieldType) {
    if (!is_string($fieldType)) {
      return;
    }

    $this->fieldType = $fieldType;
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
    $this->_terms = [];

    $terms = [];
    if (!is_array($value)) {
      $terms[] = $value;
    } else {
      $terms = $value;
    }

    foreach ($terms as $term) {
      if (!($term instanceof WP_Term)) {
        continue;
      }

      $this->_terms[] = $term;
    }
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'taxonomy',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'return_format' => 'object',
      'taxonomy' => $this->taxonomy,
      'field_type' => $this->fieldType,
      'multiple' => $this->allowMultiple,
      'allow_null' => $this->allowNull,
    ];
  }
}
