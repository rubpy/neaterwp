<?php

namespace NeaterWP\Fields;

use NeaterWP\Fields\Basic;
use NeaterWP\SystemException;

class Exception extends SystemException {
  protected $field = null;

  public function __construct($field, $message = '', $code = 0, $previous = null) {
    if ($field instanceof Basic) {
      $this->field = $field;
    }

    parent::__construct($message, $code, $previous);
  }

  public function __toString() {
    return $this->message;
  }
}
