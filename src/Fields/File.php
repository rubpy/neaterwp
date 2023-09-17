<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\BasicContent;

class File extends BasicContent {
  protected $library = 'all';
  protected $minSize = 0;
  protected $maxSize = 0;
  protected $mimeTypes = '';

  protected $_file = [];

  public function raw() {
    return $this->_file;
  }

  public function isEmpty() {
    return empty($this->_file);
  }

  public function toArray() {
    return [
      'id' => $this->id(),
      'title' => $this->title(),
      'filename' => $this->filename(),
      'filesize' => $this->filesize(),
      'url' => $this->url(),
      'link' => $this->link(),
      'alt' => $this->alt(),
      'author_id' => $this->authorId(),
      'description' => $this->description(),
      'caption' => $this->caption(),
      'name' => $this->name(),
      'created' => $this->created(),
      'modified' => $this->modified(),
      'mime_type' => $this->mimeType(),
      'icon' => $this->icon(),
    ];
  }

  public function id() {
    if (!isset($this->_file['id']) || !is_scalar($this->_file['id'])) {
      return 0;
    }

    return (string) $this->_file['id'];
  }

  public function title() {
    if (!isset($this->_file['title']) || !is_string($this->_file['title'])) {
      return '';
    }

    return $this->_file['title'];
  }

  public function filename() {
    if (!isset($this->_file['filename']) || !is_string($this->_file['filename'])) {
      return '';
    }

    return $this->_file['filename'];
  }

  public function filesize() {
    if (!isset($this->_file['filesize']) || !is_numeric($this->_file['filesize'])) {
      return -1;
    }

    return (int) $this->_file['filesize'];
  }

  public function url() {
    if (!isset($this->_file['url']) || !is_string($this->_file['url'])) {
      return '';
    }

    return $this->_file['url'];
  }

  public function link() {
    if (!isset($this->_file['link']) || !is_string($this->_file['link'])) {
      return '';
    }

    return $this->_file['link'];
  }

  public function alt() {
    if (!isset($this->_file['alt']) || !is_string($this->_file['alt'])) {
      return '';
    }

    return $this->_file['alt'];
  }

  public function authorId() {
    if (!isset($this->_file['author']) || !is_scalar($this->_file['author'])
      || empty($this->_file['author'])) {
      return '';
    }

    return (string) $this->_file['author'];
  }

  public function description() {
    if (!isset($this->_file['description']) || !is_string($this->_file['description'])) {
      return '';
    }

    return $this->_file['description'];
  }

  public function caption() {
    if (!isset($this->_file['caption']) || !is_string($this->_file['caption'])) {
      return '';
    }

    return $this->_file['caption'];
  }

  public function name() {
    if (!isset($this->_file['name']) || !is_string($this->_file['name'])) {
      return '';
    }

    return $this->_file['name'];
  }

  public function created() {
    if (!isset($this->_file['date']) || !is_string($this->_file['date'])) {
      return '';
    }

    return $this->_file['date'];
  }

  public function modified() {
    if (!isset($this->_file['modified']) || !is_string($this->_file['modified'])) {
      return '';
    }

    return $this->_file['modified'];
  }

  public function mimeType() {
    if (!isset($this->_file['mime_type']) || !is_string($this->_file['mime_type'])) {
      return '';
    }

    return $this->_file['mime_type'];
  }

  public function icon() {
    if (!isset($this->_file['icon']) || !is_string($this->_file['icon'])) {
      return '';
    }

    return $this->_file['icon'];
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
      case 'library':
        $this->setLibrary($v);
        break;
      case 'min_size':
        $this->setMinSize($v);
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

  public function getLibrary() {
    return $this->library;
  }

  public function setLibrary($library) {
    if (!is_string($library)) {
      return;
    }

    $this->library = $library;
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
      $this->_file = [];
      return;
    }

    $this->_file = $value;
  }

  public function asACF($parentKey, $key) {
    $fieldKey = self::makeFieldKey($parentKey, $key);

    return [
      'type' => 'file',

      'key' => 'field_' . $fieldKey,
      'name' => $fieldKey,
      'label' => $this->label,
      'instructions' => $this->instructions,
      'required' => $this->required,
      'conditional_logic' => $this->conditionals,
      'wrapper' => $this->wrapper,
      'default_value' => $this->defaultValue,

      'return_format' => 'array',
      'library' => $this->library,
      'min_size' => $this->minSize,
      'max_size' => $this->maxSize,
      'mime_types' => $this->mimeTypes,
    ];
  }
}
