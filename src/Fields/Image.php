<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class Image extends BasicContent {
  protected $previewSize = 'medium';
  protected $library = 'all';
  protected $minWidth = 0;
  protected $minHeight = 0;
  protected $minSize = 0;
  protected $maxWidth = 0;
  protected $maxHeight = 0;
  protected $maxSize = 0;
  protected $mimeTypes = '';

  protected $_image = [];

  public function raw() {
    return $this->_image;
  }

  public function toArray() {
    $sizes = $this->sizes();

    $arr = [
      'id' => $this->id(),
      'link' => $this->link(),
      'author_id' => $this->authorId(),
      'description' => $this->description(),
      'name' => $this->name(),
      'created' => $this->created(),
      'modified' => $this->modified(),
      'url' => $this->url(),
      'title' => $this->title(),
      'alt' => $this->alt(),
      'caption' => $this->caption(),
      'width' => $this->width(),
      'height' => $this->height(),
      'mime_type' => $this->mimeType(),
      'icon' => $this->icon(),
      'filesize' => $this->filesize(),
      'sizes' => $sizes,
      '_sizes' => [],
    ];

    foreach ($sizes as $size) {
      $arr['_sizes'][$size] = [
        'url' => $this->url($size),
        'width' => $this->width($size),
        'height' => $this->height($size),
      ];
    }

    return $arr;
  }

  public function id() {
    if (!isset($this->_image['id']) || !is_scalar($this->_image['id'])) {
      return 0;
    }

    return (string) $this->_image['id'];
  }

  public function link() {
    if (!isset($this->_image['link']) || !is_string($this->_image['link'])) {
      return '';
    }

    return $this->_image['link'];
  }

  public function authorId() {
    if (!isset($this->_image['author']) || !is_scalar($this->_image['author'])
      || empty($this->_image['author'])) {
      return '';
    }

    return (string) $this->_image['author'];
  }

  public function description() {
    if (!isset($this->_image['description']) || !is_string($this->_image['description'])) {
      return '';
    }

    return $this->_image['description'];
  }

  public function name() {
    if (!isset($this->_image['name']) || !is_string($this->_image['name'])) {
      return '';
    }

    return $this->_image['name'];
  }

  public function created() {
    if (!isset($this->_image['date']) || !is_string($this->_image['date'])) {
      return '';
    }

    return (string) $this->_image['date'];
  }

  public function modified() {
    if (!isset($this->_image['modified']) || !is_string($this->_image['modified'])) {
      return '';
    }

    return (string) $this->_image['modified'];
  }

  public function url($size = 'full') {
    if (!is_string($size)) {
      return '';
    }

    if (!isset($this->_image['url']) || !is_string($this->_image['url'])) {
      return '';
    }

    if ($size === 'full') {
      return $this->_image['url'];
    }

    if (!isset($this->_image['sizes'])
      || !is_array($this->_image['sizes'])
      || !isset($this->_image['sizes'][$size])
      || !is_string($this->_image['sizes'][$size])) {
      return '';
    }

    return $this->_image['sizes'][$size];
  }

  public function title() {
    if (!isset($this->_image['title']) || !is_string($this->_image['title'])) {
      return '';
    }

    return $this->_image['title'];
  }

  public function alt() {
    if (!isset($this->_image['alt']) || !is_string($this->_image['alt'])) {
      return '';
    }

    return $this->_image['alt'];
  }

  public function caption() {
    if (!isset($this->_image['caption']) || !is_string($this->_image['caption'])) {
      return '';
    }

    return $this->_image['caption'];
  }

  public function width($size = 'full') {
    if (!is_string($size)) {
      return 0;
    }

    if ($size === 'full') {
      if (!isset($this->_image['width'])) {
        return 0;
      }

      return (int) $this->_image['width'];
    }

    if (!isset($this->_image['sizes'])
      || !isset($this->_image['sizes'][$size])
      || !isset($this->_image['sizes'][$size . '-width'])) {
      return 0;
    }
    return (int) $this->_image['sizes'][$size . '-width'];
  }

  public function height($size = 'full') {
    if (!is_string($size)) {
      return 0;
    }

    if ($size === 'full') {
      if (!isset($this->_image['height'])) {
        return 0;
      }

      return (int) $this->_image['height'];
    }

    if (!isset($this->_image['sizes'])
      || !isset($this->_image['sizes'][$size])
      || !isset($this->_image['sizes'][$size . '-height'])) {
      return 0;
    }
    return (int) $this->_image['sizes'][$size . '-height'];
  }

  public function mimeType() {
    if (!isset($this->_image['mime_type']) || !is_string($this->_image['mime_type'])) {
      return '';
    }

    return $this->_image['mime_type'];
  }

  public function icon() {
    if (!isset($this->_image['icon']) || !is_string($this->_image['icon'])) {
      return '';
    }

    return $this->_image['icon'];
  }

  public function filesize() {
    if (!isset($this->_image['filesize']) || !is_int($this->_image['filesize'])) {
      return -1;
    }

    return $this->_image['filesize'];
  }

  public function sizes() {
    if (empty($this->_image) || !isset($this->_image['url'])) {
      return [];
    }

    $sz = ['full'];
    if (!isset($this->_image['sizes']) || !is_array($this->_image['sizes'])) {
      return $sz;
    }

    foreach ($this->_image['sizes'] as $k => $v) {
      if (!is_string($k)) {
        continue;
      }

      $sep = strpos($k, '-');
      if ($sep !== false) {
        continue;
      }

      if (strtolower($k) === 'full'
        || in_array($k, $sz)) {
        continue;
      }

      $sz[] = $k;
    }

    return $sz;
  }

  public function isEmpty() {
    return empty($this->_image);
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
      case 'preview_size':
        $this->setPreviewSize($v);
        break;
      case 'library':
        $this->setLibrary($v);
        break;
      case 'min_width':
        $this->setMinWidth($v);
        break;
      case 'min_height':
        $this->setMinHeight($v);
        break;
      case 'min_size':
        $this->setMinSize($v);
        break;
      case 'max_width':
        $this->setMaxWidth($v);
        break;
      case 'max_height':
        $this->setMaxHeight($v);
        break;
      case 'max_size':
        $this->setMaxSize($v);
        break;
      case 'mime_types':
        $this->setMimeTypes($v);
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

  public function getPreviewSize() {
    return $this->previewSize;
  }

  public function setPreviewSize($previewSize) {
    if (!is_string($previewSize)) {
      return;
    }

    $this->previewSize = $previewSize;
  }

  public function getLibrary() {
    return $this->library;
  }

  public function setLibrary($library) {
    if (!is_string($library)) {
      return;
    }

    $this->library = $library;
  }

  public function getMinWidth() {
    return $this->minWidth;
  }

  public function setMinWidth($minWidth) {
    if (!is_scalar($minWidth)) {
      return;
    }

    $this->minWidth = max(0, (int) $minWidth);
  }

  public function getMinHeight() {
    return $this->minHeight;
  }

  public function setMinHeight($minHeight) {
    if (!is_scalar($minHeight)) {
      return;
    }

    $this->minHeight = max(0, (int) $minHeight);
  }

  public function getMinSize() {
    return $this->minSize;
  }

  public function setMinSize($minSize) {
    if (!is_scalar($minSize)) {
      return;
    }

    $this->minSize = max(0, (int) $minSize);
  }

  public function getMaxWidth() {
    return $this->maxWidth;
  }

  public function setMaxWidth($maxWidth) {
    if (!is_scalar($maxWidth)) {
      return;
    }

    $this->maxWidth = max(0, (int) $maxWidth);
  }

  public function getMaxHeight() {
    return $this->maxHeight;
  }

  public function setMaxHeight($maxHeight) {
    if (!is_scalar($maxHeight)) {
      return;
    }

    $this->maxHeight = max(0, (int) $maxHeight);
  }

  public function getMaxSize() {
    return $this->maxSize;
  }

  public function setMaxSize($maxSize) {
    if (!is_scalar($maxSize)) {
      return;
    }

    $this->maxSize = max(0, (int) $maxSize);
  }

  public function getMimeTypes() {
    return $this->mimeTypes;
  }

  public function setMimeTypes($mimeTypes) {
    $mimes = '';
    if (is_array($mimeTypes)) {
      $i = 0;
      foreach ($mimeTypes as $mimeType) {
        if (strpos($mimeType, ',') !== false) {
          continue;
        }

        if ($i > 0) {
          $mimes .= ',';
        }
        $mimes .= $mimeType;

        ++$i;
      }
    } elseif (is_string($mimeTypes)) {
      $mimes = $mimeTypes;
    } else {
      return;
    }

    $this->mimeTypes = $mimes;
  }

  public function parseValue($value, $pageId, $parentKey, $key) {
    if (!is_array($value)) {
      $this->_image = [];
      return;
    }

    $this->_image = $value;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'image',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'return_format' => 'array',
      'preview_size' => $this->previewSize,
      'library' => $this->library,
      'min_width' => $this->minWidth,
      'min_height' => $this->minHeight,
      'min_size' => $this->minSize,
      'max_width' => $this->maxWidth,
      'max_height' => $this->maxHeight,
      'max_size' => $this->maxSize,
      'mime_types' => $this->mimeTypes,
    ];
  }
}
