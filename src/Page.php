<?php

namespace NeaterWP;

use NeaterWP\PageException;
use WP_Post;

abstract class Page {
  protected static $_setupDone = false;
  protected static $_setupFieldsDone = false;
  protected static $_wpInstances = [];
  protected static $_pageClassKey = null;
  protected static $_pageTemplateFilename = '';
  protected static $_fieldGroups = [];

  protected $_fieldGroupCache = [];

  protected $_wpPost = null;
  protected $_wpId = '';

  protected static $_defaultSettings = [
    'ui_hide' => ['the_content', 'excerpt', 'discussion', 'comments', 'slug', 'author', 'revisions', 'send-trackbacks'],
    'ui_style' => 'default',
  ];

  // A page constructor cannot be called directly!
  // Use Page::fromWPPost() instead.
  private function __construct($wpPost) {
    if (!static::$_setupDone || !static::$_setupFieldsDone) {
      throw new PageException(__METHOD__ . ': page has not been set up/initialized correctly');
    }

    if (!($wpPost instanceof WP_Post)
      || !isset($wpPost->ID)) {
      throw new PageException(__METHOD__ . ' expects a ' . WP_Post::class);
    }

    $this->_wpPost = $wpPost;
    $this->_wpId = (string) $wpPost->ID;
  }

  public static function isWPPost($wpPost) {
    if (!($wpPost instanceof WP_Post)) {
      return false;
    }

    $id = (string) $wpPost->ID;
    $postTemplate = get_post_meta($id, '_wp_page_template', true);
    if (!is_string($postTemplate)
      || $postTemplate !== static::$_pageTemplateFilename) {
      return false;
    }

    return true;
  }

  public static function fromWPPost($wpPost) {
    if (!($wpPost instanceof WP_Post)) {
      throw new PageException(__METHOD__ . ' expects a ' . WP_Post::class);
    }

    $id = (string) $wpPost->ID;
    if (isset(static::$_wpInstances[$id])) {
      return static::$_wpInstances[$id];
    }

    $postTemplate = get_post_meta($id, '_wp_page_template', true);
    if (!is_string($postTemplate)
      || $postTemplate !== static::$_pageTemplateFilename) {
      throw new PageException(__METHOD__ . ' expects a ' . WP_Post::class . ' with matching template (expected: "' . static::$_pageTemplateFilename . '", given: "' . (is_string($postTemplate) ? $postTemplate : '') . '")');
    }

    $inst = new static($wpPost);
    static::$_wpInstances[$id] = $inst;
    return $inst;
  }

  protected function wpPost() {
    return $this->_wpPost;
  }

  public function wpId() {
    return $this->_wpId;
  }

  public static function calculatePageClassKey() {
    if (static::$_pageClassKey !== null && is_string(static::$_pageClassKey)) {
      return static::$_pageClassKey;
    }

    $templateKey = (string) static::templateKey();
    $hash = md5('ntrwp_' . $templateKey);

    $classKey = 'ntr-' . substr($hash, 0, 10);
    static::$_pageClassKey = $classKey;
    return $classKey;
  }

  // Called when page of a given class is registered for the first time.
  public static function setup($pageTemplateFilename) {
    if (static::$_setupDone) {
      return;
    }

    if (!is_string($pageTemplateFilename)) {
      return;
    }
    static::$_pageTemplateFilename = $pageTemplateFilename;

    static::$_setupDone = true;
  }

  // Called when page is supposed to prepare its internal field structure.
  public static function setupFields($pageTemplateFilename) {
    if (static::$_setupFieldsDone) {
      return;
    }

    if (!is_string($pageTemplateFilename)) {
      return;
    }

    if (!function_exists('acf_add_local_field_group')) {
      throw new PageException(__METHOD__ . ' has been called prior to ACF initialization');
    }

    $pageClassKey = static::calculatePageClassKey();

    $settings = static::settings();
    $fieldGroups = static::fieldGroups();
    if (!is_array($settings) || !is_array($fieldGroups)) {
      throw new PageException(__METHOD__ . ': invalid use of page API (settings, fieldGroups)');
    }

    $uiLocation = [
      [['param' => 'page_template', 'operator' => '==', 'value' => $pageTemplateFilename]],
    ];

    $uiHide = static::$_defaultSettings['ui_hide'];
    if (isset($settings['ui_hide']) && is_array($settings['ui_hide'])) {
      $uiHide = $settings['ui_hide'];
    }

    $uiStyle = static::$_defaultSettings['ui_style'];
    if (isset($settings['ui_style']) && is_string($settings['ui_style'])) {
      $uiStyle = $settings['ui_style'];
    }

    $uiOrder = 1;

    foreach ($fieldGroups as $fieldGroupKey => $fieldGroupClass) {
      if (!is_string($fieldGroupKey)) {
        continue;
      }
      if (!class_exists($fieldGroupClass)) {
        throw new PageException(__METHOD__ . ' got an unknown class: "' . (is_string($fieldGroupClass) ? $fieldGroupClass : '') . '"');
      }
      if (!is_subclass_of($fieldGroupClass, Fields\Group::class)) {
        throw new PageException(__METHOD__ . ' expected a class derived from ' . Fields\Group::class . ', got: "' . (is_string($fieldGroupClass) ? $fieldGroupClass : '') . '"');
      }

      $fieldGroupSettings = @call_user_func([$fieldGroupClass, 'settings']);
      if (!is_array($fieldGroupSettings)) {
        continue;
      }

      $fieldGroupFields = @call_user_func([$fieldGroupClass, 'fields']);
      if (!is_array($fieldGroupFields)) {
        continue;
      }

      $fieldGroupTitle = '';
      if (isset($fieldGroupSettings['title']) && is_string($fieldGroupSettings['title'])) {
        $fieldGroupTitle = $fieldGroupSettings['title'];
      }
      unset($fieldGroupSettings['title']);

      if (!isset($fieldGroupSettings['location'])) {
        $fieldGroupSettings['location'] = $uiLocation;
      }
      if (!isset($fieldGroupSettings['hide_on_screen'])) {
        $fieldGroupSettings['hide_on_screen'] = $uiHide;
      }
      if (!isset($fieldGroupSettings['style'])) {
        $fieldGroupSettings['style'] = $uiStyle;
      }
      if (!isset($fieldGroupSettings['order'])) {
        $fieldGroupSettings['order'] = $uiOrder;
      }

      $fieldGroupInst = new $fieldGroupClass(
        $fieldGroupTitle,
        $fieldGroupFields,
        $fieldGroupSettings
      );
      static::$_fieldGroups[$fieldGroupKey] = $fieldGroupInst;

      $acfFieldGroup = $fieldGroupInst->asACF($pageClassKey, $fieldGroupKey);
      if (!is_array($acfFieldGroup)
        || !acf_add_local_field_group($acfFieldGroup)) {
        throw new PageException(__METHOD__ . ' failed during field group setup (key: "' . $fieldGroupKey . '", class: ' . $fieldGroupClass . ')');
      }

      ++$uiOrder;
    }

    static::$_setupFieldsDone = true;
  }

  public function getFieldGroup($key, $expectedClass = null) {
    if ($expectedClass !== null && !is_string($expectedClass)) {
      throw new PageException(__METHOD__ . ': invalid expected class');
    }

    if (!is_string($key) || !isset(static::$_fieldGroups[$key])) {
      if ($expectedClass !== null) {
        throw new PageException(__METHOD__ . ': page does not contain group' . (is_string($key) ? ' "' . $key . '"' : ''));
      }

      return null;
    }

    $fieldGroup = static::$_fieldGroups[$key];
    if (!class_exists($expectedClass) || !($fieldGroup instanceof $expectedClass)) {
      throw new PageException(__METHOD__ . ': page group field "' . $key . '" is not of type ' . $expectedClass);
    }

    if (isset($this->_fieldGroupCache[$key])) {
      return $this->_fieldGroupCache[$key];
    }

    $clonedGroup = $fieldGroup->withParentPage(
      $this,
      static::calculatePageClassKey(),
      $key
    );
    $this->_fieldGroupCache[$key] = $clonedGroup;
    return $clonedGroup;
  }

  abstract public static function templateName();
  abstract public static function templateKey();

  public static function settings() {return [];}
  public static function fieldGroups() {return [];}

  abstract public function render();
}
