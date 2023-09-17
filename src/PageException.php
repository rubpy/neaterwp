<?php

namespace NeaterWP;

use NeaterWP\SystemException;

class PageException extends SystemException {
  public function __toString() {
    return Page::class . ': ' . $this->getMessage();
  }
}
