<?php

namespace NeaterWP;

use NeaterWP\System;
use NeaterWP\SystemException;
use WP_Post;

class PageManager {
  protected static $pageTemplates = [];

  protected static $_setupDone = false;

  public static function wpFront() {
    if (!function_exists('get_post') || !function_exists('get_option')) {
      return null;
    }

    $frontId = (int) get_option('page_on_front');
    if ($frontId <= 0) {
      return null;
    }

    return self::wpPost($frontId);
  }

  public static function wpPost($id) {
    if (!function_exists('get_post')) {
      return null;
    }

    if (!is_numeric($id)) {
      return null;
    }
    $id = (int) $id;

    $post = get_post($id);
    if (!($post instanceof WP_Post) || !isset($post->ID)) {
      return null;
    }

    return $post;
  }

  public static function render() {
    if (!function_exists('get_post')) {
      return false;
    }

    $post = get_post();
    if (!($post instanceof WP_Post) || !isset($post->ID)) {
      return false;
    }

    $postTemplate = get_post_meta($post->ID, '_wp_page_template', true);
    if (!is_string($postTemplate) || !isset(self::$pageTemplates[$postTemplate])) {
      return false;
    }

    $pageTemplate = self::$pageTemplates[$postTemplate];
    if (!is_array($pageTemplate) || !isset($pageTemplate['page_class'])) {
      return false;
    }
    $pageClass = $pageTemplate['page_class'];
    if (!class_exists($pageClass) || !is_subclass_of($pageClass, Page::class)) {
      return false;
    }

    $page = @call_user_func_array([$pageClass, 'fromWPPost'], [$post]);
    if (!($page instanceof $pageClass)) {
      return false;
    }

    $page->render();
    return true;
  }

  protected static function handlePageFields() {
    foreach (self::$pageTemplates as $pageTemplateFilename => $pageTemplate) {
      if (!is_array($pageTemplate) || !isset($pageTemplate['page_class'])) {
        continue;
      }
      $pageClass = $pageTemplate['page_class'];
      if (!class_exists($pageClass)) {
        continue;
      }

      @call_user_func_array([$pageClass, 'setupFields'], [$pageTemplateFilename]);
    }
  }

  public static function register($pageClass) {
    self::setup();

    if (is_string($pageClass)) {
      $pageClasses = [$pageClass];
    }

    foreach ($pageClasses as $pageClass) {
      if (!is_string($pageClass)
        || !class_exists($pageClass) || !is_subclass_of($pageClass, Page::class)) {
        continue;
      }

      $templateName = @call_user_func([$pageClass, 'templateName']);
      $templateKey = @call_user_func([$pageClass, 'templateKey']);
      if (empty($templateName) || empty($templateKey)) {
        continue;
      }

      $templateId = self::templateId($templateKey);
      if (empty($templateId)) {
        continue;
      }

      $templateFakeFilename = $templateId . '.fake.php';
      if (isset(self::$pageTemplates[$templateFakeFilename])) {
        continue;
      }

      @call_user_func_array([$pageClass, 'setup'], [$templateFakeFilename]);

      self::$pageTemplates[$templateFakeFilename] = [
        'name' => $templateName,
        'key' => $templateKey,
        'id' => $templateId,
        'fake_filename' => $templateFakeFilename,
        'page_class' => $pageClass,
      ];
    }
  }

  protected static function setup() {
    if (self::$_setupDone) {
      return;
    }
    if (!function_exists('add_action')) {
      return;
    }

    $registerTemplates = function ($atts) {
      return self::wpRegisterTemplates($atts);
    };
    $viewTemplate = function ($template) {
      return self::wpViewTemplate($template);
    };
    $addTemplate = function ($postsTemplates) {
      return self::wpAddTemplate($postsTemplates);
    };

    if (version_compare(floatval(get_bloginfo('version')), '4.7', '<')) {
      add_filter('page_attributes_dropdown_pages_args', $registerTemplates);
    } else {
      add_filter('theme_page_templates', $addTemplate);
    }
    add_filter('wp_insert_post_data', $registerTemplates);
    add_filter('template_include', $viewTemplate);

    add_action('acf/init', function () {
      try {
        self::handlePageFields();
      } catch (SystemException $e) {
        System::addLoggedException($e);
      }
    });

    self::$_setupDone = true;
  }

  protected static function templateId($key) {
    if (!is_string($key)) {
      return '';
    }
    $hash = md5($key);

    $id = 'ntrtpl_' . $hash;
    return $id;
  }

  public static function pageTemplatesAsWP() {
    $templates = [];

    foreach (self::$pageTemplates as $templateFilename => $template) {
      if (!is_array($template) || !isset($template['name'])) {
        continue;
      }

      $templates[$templateFilename] = $template['name'];
    }

    return $templates;
  }

  protected static function wpRegisterTemplates($atts) {
    $cacheKey = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());

    $templates = wp_get_theme()->get_page_templates();
    if (empty($templates) || !is_array($templates)) {
      $templates = [];
    }

    wp_cache_delete($cacheKey, 'themes');
    $templates = array_merge($templates, self::pageTemplatesAsWP());
    wp_cache_add($cacheKey, $templates, 'themes', 1800);

    return $atts;
  }

  protected static function wpViewTemplate($template) {
    if (is_search()) {
      return $template;
    }

    $post = get_post();
    if (!($post instanceof WP_Post) || !isset($post->ID)) {
      return $template;
    }

    $postTemplate = get_post_meta($post->ID, '_wp_page_template', true);
    if (!is_string($postTemplate) || !isset(self::$pageTemplates[$postTemplate])) {
      return $template;
    }

    return get_index_template();
  }

  protected static function wpAddTemplate($postsTemplates) {
    $postsTemplates = array_merge($postsTemplates, self::pageTemplatesAsWP());
    return $postsTemplates;
  }
}
