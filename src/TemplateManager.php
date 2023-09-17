<?php

namespace NeaterWP;

use NeaterWP\TemplateMissingException;

class TemplateManager {
  protected static $directory = '';
  protected static $extension = '.php';

  public static function setDirectory($directory) {
    if (!is_string($directory)) {
      return;
    }

    self::$directory = $directory;
  }

  public static function getDirectory() {
    $dir = self::$directory;
    if (empty($dir)) {
      $dir = get_template_directory();
    }

    return $dir;
  }

  public static function render($filename, $args = []) {
    $filename = self::trimPath($filename) . self::$extension;

    $f = self::getDirectory() . '/' . self::trimPath($filename);
    if (!file_exists($f)) {
      throw new TemplateMissingException($f);
    }

    if (!is_array($args)) {
      $args = [];
    }

    $tpl = include $f;
    $ret = null;
    if ($tpl !== null && is_callable($tpl)) {
      $ret = $tpl($args);
    }

    return $ret;
  }

  protected static function trimPath($u) {
    return trim($u, "/\\ \n\r\t\x0b\x00");
  }
}
