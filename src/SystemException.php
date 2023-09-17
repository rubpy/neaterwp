<?php

namespace NeaterWP;

use Exception;

class SystemException extends Exception {
  public function __toString() {
    return System::class . ': ' . $this->getMessage();
  }
}
