<?php

namespace NeatyTheme\Pages;

use NeaterWP\Page;
use NeaterWP\TemplateManager;
use NeatyTheme\Fields;

class Homepage extends Page {
  public static function templateName() {
    return 'Neaty — homepage';
  }

  public static function templateKey() {
    return 'neaty_homepage';
  }

  public static function settings() {
    return [
      'ui_hide' => ['the_content', 'excerpt', 'discussion', 'comments', 'slug', 'author', 'revisions', 'send-trackbacks', 'page_attributes', 'featured_image', 'categories', 'permalink'],
      'ui_style' => 'seamless',
    ];
  }

  public static function fieldGroups() {
    return [
      'todos' => Fields\Todos::class,
    ];
  }

  // ==================================================

  public function render() {
    TemplateManager::render('todos', [
      '_' => $this->getFieldGroup('todos', Fields\Todos::class),
    ]);
  }
}
