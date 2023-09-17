<?php

namespace NeaterWP;

use Exception;
use NeaterWP\SystemPseudoException;

class System {
  protected static $directory = '';
  protected static $initialized = false;
  protected static $loggedExceptions = [];

  public static function setDirectory($directory) {
    if (!is_string($directory)) {
      return;
    }

    self::$directory = $directory;
  }

  public static function getDirectory() {
    return self::$directory;
  }

  public static function isInitialized() {
    return self::$initialized;
  }

  public static function setInitialized($initialized) {
    self::$initialized = $initialized;
  }

  protected static function normalizeLoggedException($e) {
    if (is_scalar($e)) {
      $e = new SystemPseudoException((string) $e);
    } else if (!($e instanceof Exception)) {
      return null;
    }

    return $e;
  }

  public static function addLoggedException($e) {
    $e = self::normalizeLoggedException($e);
    if ($e === null || in_array($e, self::$loggedExceptions)) {
      return;
    }

    self::$loggedExceptions[] = $e;
  }

  public static function removeLoggedException($e) {
    $e = self::normalizeLoggedException($e);
    if ($e === null) {
      return;
    }

    $key = null;
    foreach (self::$loggedExceptions as $k => $v) {
      if ($v !== $e) {
        continue;
      }

      $key = $k;
      break;
    }

    if ($key !== null) {
      unset(self::$loggedExceptions[$key]);
    }
  }

  public static function getLoggedExceptions() {
    return self::$loggedExceptions;
  }

  public static function checkRequirements($reqs) {
    if (!is_array($reqs)) {
      return;
    }

    $failed = [];

    foreach ($reqs as $req) {
      if (!is_array($req) || count($req) < 2 || !isset($req[0]) || !is_string($req[0]) || !isset($req[1])) {
        continue;
      }
      $req[0] = strtolower($req[0]);

      $loaded = false;
      if ($req[0] === 'class') {
        if (is_string($req[1]) && class_exists($req[1])) {
          $loaded = true;
        }
      } elseif ($req[1] === 'function') {
        if (is_string($req[1]) && function_exists($req[1])) {
          $loaded = true;
        }
      } elseif ($req[1] === 'constant') {
        if (is_string($req[1]) && defined($req[1])) {
          $loaded = true;
        }
      }

      if (!$loaded) {
        $failed[] = $req;
      }
    }

    if (!empty($failed)) {
      throw new SystemRequirementException($failed);
    }
  }
}
