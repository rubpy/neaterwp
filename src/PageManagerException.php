<?php

namespace NeaterWP;

use NeaterWP\SystemException;

class PageManagerException extends SystemException {
  public function __toString() {
    return PageManager::class . ': ' . $this->getMessage();
  }
}
