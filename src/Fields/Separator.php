<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Separator extends BasicContent {
  public function parseValue($value, $pageId, $parentKey, $key) {}
  public function loadACF($pageId, $parentKey, $key) {}

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'separator',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,
    ];
  }
}
