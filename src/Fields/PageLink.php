<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class PageLink extends BasicContent {
  protected $postTypes = [];
  protected $taxonomies = [];
  protected $allowNull = false;
  protected $allowMultiple = false;
  protected $allowArchives = true;

  protected $_links = [];

  public function links() {
    return $this->_links;
  }

  public function firstLink() {
    if (empty($this->_links)) {
      return '';
    }

    return reset($this->_links);
  }

  public function count() {
    return count($this->_links);
  }

  public function isEmpty() {
    return empty($this->_links);
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
      case 'allow_archives':
        $this->setAllowsArchives($v);
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

  public function allowsArchives() {
    return $this->allowArchives;
  }

  public function setAllowsArchives($allows) {
    $this->allowArchives = (bool) $allows;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    $this->_links = [];

    if (is_string($value)) {
      $this->_links[] = $value;
    } elseif (is_array($value)) {
      foreach ($value as $link) {
        if (!is_string($link) || empty($link)) {
          continue;
        }

        $this->_links[] = $link;
      }
    }
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'page_link',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'post_type' => $this->postTypes,
      'taxonomy' => $this->taxonomies,
      'allow_null' => (int) $this->allowNull,
      'multiple' => (int) $this->allowMultiple,
      'allow_archives' => (int) $this->allowArchives,
    ];
  }
}
