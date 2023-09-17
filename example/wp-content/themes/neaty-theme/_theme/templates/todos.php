<?php

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
        <?php if (empty($items)): ?>
        <aside class="neaty-todos__message">
          <p>No todos... ¯\_(ツ)_/¯</p>
        </aside>
        <?php else: ?>
        <div class="neaty-todos__items">
          <?php foreach ($items as $item): ?>
          <section class="neaty-todos__item<?php if ($item['done']): ?> is-checked<?php endif;?>">
            <div class="neaty-todos__item__actions">
              <label class="neaty-todos__item__actions__checkbox neaty-todos__item__done">
                <input type="checkbox" disabled>
                <div></div>
              </label>
            </div>
            <div class="neaty-todos__item__details">
              <div class="neaty-todos__item__title"><?php echo NeaterWP\Utils::escape($item['title']); ?></div>
            </div>
          </section>
          <?php endforeach;?>
        </div>
        <?php endif;?>
      </section>
    </div>
<?php
};
