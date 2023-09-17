## 🤕 NeaterWP
A set of simple utility/wrapper classes, created as a coping mechanism for
dealing with WordPress template development (without going bonkers in the process)...

> ❕ Caution: although this package has been used in a couple of places, please
> note it is still a work-in-progress.

### Requirements
- `WordPress` *(tested on 6.3.1)*
- `Advanced Custom Fields (ACF)` plugin
- `ACF: Repeater Field` plugin

### Example
This repository includes a simple theme which demonstrates how various parts of
`NeaterWP` can be used in practice.

You can give the example WordPress theme (called `neaty-theme`) a try using Docker:
```console
$ docker compose up
```
> NOTE: database credentials can be found in the `compose.yaml` file.

------------------------------

### Screenshots

![Example theme homepage](/screenshots/neatytheme-homepage.png)

![Example theme homepage edit page](/screenshots/neatytheme-homepage-edit.png)

```php
<?php // wp-content/themes/neaty-theme/_theme/setup.php

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

```

```html+php
<?php // wp-content/themes/neaty-theme/_theme/templates/todos.php

if (!defined('WP_IS_NEATER')) {
  die();
}

return function ($args = []) {
  if (!isset($args['_']) || !($args['_'] instanceof NeatyTheme\Fields\Todos)) {
    throw new NeatyTheme\Exception('invalid template arguments');
  }
  $todos = $args['_'];

  // --------------------------------------------------

  $hidden = false;
  if (($field = $todos->getField('hidden', NeaterWP\Fields\TrueFalse::class))) {
    $hidden = $field->isToggled();
  }
  if ($hidden) {
    return;
  }

  $title = 'Todos';
  if (($field = $todos->getField('title', NeaterWP\Fields\Text::class)) && !$field->isEmpty()) {
    $title = $field->text();
  }

  $items = [];
  if (($group = $todos->getField('items', NeaterWP\Fields\Repeater::class))) {
    foreach ($group->rows() as $row) {
      $item = [
        'title' => '',
        'done' => false,
      ];

      if (($field = $row->getField('title', NeaterWP\Fields\Text::class)) && !$field->isEmpty()) {
        $item['title'] = $field->text();
      }
      if (($field = $row->getField('done', NeaterWP\Fields\TrueFalse::class))) {
        $item['done'] = $field->isToggled();
      }

      if (empty($item['title'])) {
        continue;
      }

      $items[] = $item;
    }
  }

  ?>
    <div class="container">
      <section class="neaty-todos">
        <header class="neaty-todos__header">
          <h3 class="neaty-todos__title"><?php echo NeaterWP\Utils::escape($title); ?></h3>
        </header>
        <!-- ... -->
```

![Error reporting example #1](/screenshots/error-reporting1.png)

![Error reporting example #2](/screenshots/error-reporting2.png)
