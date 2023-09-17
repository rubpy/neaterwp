<?php

namespace NeaterWP;

use NeaterWP\SystemException;

class SystemPseudoException extends SystemException {
  const LEVEL_ERROR = 'error';
  const LEVEL_WARNING = 'warning';
  const LEVEL_INFO = 'info';

  protected $level = self::LEVEL_ERROR;

  public function __construct($message = '', $level = '', $code = 0, $previous = null) {
    if (!is_string($level) || empty($level)) {
      $level = self::LEVEL_ERROR;
    }
    $this->level = $level;

    parent::__construct($message, $code, $previous);
  }

  public function __toString() {
    $s = '';

    if (!empty($this->level)) {
      $s .= '[' . $this->level . '] ';
    }

    $s .= System::class . ': ' . $this->getMessage();

    return $s;
  }

  public function getLevel() {
    return $this->level;
  }
}
