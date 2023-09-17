<?php

namespace NeatyTheme\Fields;

use NeaterWP\Fields;

class Todos extends Fields\Group {
  public static function settings() {
    return [
      'title' => "📋\xe2\x80\x82Todos",
    ];
  }

  public static function fields() {
    return [
      'hidden' => new Fields\TrueFalse('Hide this section', ['ui' => true]),
      'title' => new Fields\Text('Section title'),

      'sep1' => new Fields\Separator(),

      'items' => new Fields\Repeater('Items', [
        'title' => new Fields\Text('Title'),
        'done' => new Fields\TrueFalse('Done', ['ui' => true]),
      ], ['button_label' => 'Add an item', 'layout' => 'table']),
    ];
  }
}
