<?php

namespace NeaterWP;

use NeaterWP\SystemException;

class SystemRequirementException extends SystemException {
  protected $failedRequirements = [];

  public function __construct($failedRequirements, $code = 0, $previous = null) {
    if (is_array($failedRequirements)) {
      foreach ($failedRequirements as $req) {
        if (!is_array($req) || count($req) < 2 || !isset($req[0]) || !is_string($req[0]) || !isset($req[1])) {
          continue;
        }

        $this->failedRequirements[] = $req;
      }
    }

    parent::__construct('', $code, $previous);
  }

  public function serializeRequirements() {
    $s = '';

    $i = 0;
    foreach ($this->failedRequirements as $req) {
      $reqSize = count($req);
      if ($reqSize < 2) {
        continue;
      }

      if ($i > 0) {
        $s .= ', ';
      }

      $s .= '[' . $req[0] . ': ' . $req[1];
      if ($reqSize > 2) {
        $s .= ' (';
        $s .= Utils::quote($req[2]);
        $s .= ')';
      }
      $s .= ']';

      ++$i;
    }

    return $s;
  }

  public function __toString() {
    return System::class . ': failed requirements (' . $this->serializeRequirements() . ')';
  }
}
