<?php

namespace NeaterWP;

class AssetManager {
  protected static $hashSuffixKey = '_v';
  protected static $_useHashSuffixes = false;
  protected static $publicPrefix = '';

  protected static $attached = false;
  protected static $renderers = [];

  public static function useHashSuffixes($use = null) {
    if ($use === null) {
      return self::$_useHashSuffixes;
    } elseif (is_bool($use)) {
      self::$_useHashSuffixes = $use;
    }
  }

  protected static function trimPath($u) {
    return trim($u, "/\\ \n\r\t\x0b\x00");
  }

  public static function setPublicPrefix($prefix) {
    if (!is_string($prefix)) {
      return;
    }

    self::$publicPrefix = self::trimPath($prefix);
  }

  public static function getPublicPrefix() {
    return self::$publicPrefix;
  }

  protected static function localFileHash($filename) {
    if (!is_string($filename)) {
      return '';
    }

    if (file_exists($filename)) {
      return hash_file('crc32b', $filename);
    }

    $n = floor((time() / 86400));
    return hash('crc32b', $n);
  }

  public static function uri($relativePath, $escape = true) {
    $relativePath = self::trimPath($relativePath);

    $prefix = (!empty(self::$publicPrefix) ? '/' . self::trimPath(self::$publicPrefix) : '');
    $localBase = get_template_directory() . $prefix;
    $publicBase = get_template_directory_uri() . $prefix;

    $local = $localBase . '/' . $relativePath;
    $public = $publicBase . '/' . $relativePath;
    if (self::$_useHashSuffixes) {
      $q = '&';
      if (strrpos($u, '?') === false) {
        $q = '?';
      }

      $hash = self::localFileHash($local);
      $q .= (self::$hashSuffixKey . '=' . $hash);
    }

    $u = $public . $q;
    if ($escape) {
      return htmlspecialchars($u);
    }

    return $u;
  }

  public static function handleRenderers($tag) {
    if (!is_string($tag) || !isset(self::$renderers[$tag]) || !is_array(self::$renderers[$tag])) {
      return;
    }

    foreach (self::$renderers[$tag] as $renderer) {
      if (!is_callable($renderer)) {
        continue;
      }

      $renderer();
    }
  }

  protected static function attachHandlers() {
    if (self::$attached) {
      return;
    }
    if (!function_exists('add_action')) {
      return;
    }

    add_action('wp_head', function () {
      self::handleRenderers('head');
    });
    add_action('login_head', function () {
      self::handleRenderers('login_head');
    });
    add_action('admin_head', function () {
      self::handleRenderers('admin_head');
    });

    self::$attached = true;
  }

  public static function addTo($renderer, $toTag) {
    self::attachHandlers();
    if (!is_callable($renderer)) {
      return;
    }
    if (!is_string($toTag) && !is_array($toTag)) {
      return;
    }

    if (is_string($toTag)) {
      $toTag = [$toTag];
    }

    foreach ($toTag as $tag) {
      if (!is_string($tag)) {
        continue;
      }

      if (!isset(self::$renderers[$tag]) || !is_array(self::$renderers[$tag])) {
        self::$renderers[$tag] = [];
      }
      self::$renderers[$tag][] = $renderer;
    }
  }
}
