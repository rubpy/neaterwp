<?php

namespace NeaterWP;

class MemoryCache {
  protected static $_data = [];

  public static function get($key) {
    if (!is_string($key) || !isset(static::$_data[$key])) {
      return null;
    }

    return static::$_data[$key];
  }

  public static function set($key, $value) {
    if (!is_string($key)) {
      return;
    }

    static::$_data[$key] = $value;
  }

  public static function exists($key) {
    if (!is_string($key)) {
      return false;
    }

    return isset(static::$_data[$key]);
  }
}
