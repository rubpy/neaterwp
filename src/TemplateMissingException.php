<?php

namespace NeaterWP;

use NeaterWP\SystemException;

class TemplateMissingException extends SystemException {
  protected $filename = '';

  public function __construct($filename, $code = 0, $previous = null) {
    if (is_string($filename)) {
      $this->filename = $filename;
    }

    parent::__construct('', $code, $previous);
  }

  public function __toString() {
    return TemplateManager::class . ': missing template (' . $this->filename . ')';
  }
}
