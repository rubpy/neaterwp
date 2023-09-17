<?php

namespace NeaterWP;

class Utils {
  public static function escape($text) {
    if (!is_scalar($text)) {
      return '';
    }

    return htmlspecialchars((string)$text);
  }

  public static function isColorBright($hexCode) {
    $hex = preg_replace('/[^a-f0-9]/', '', strtolower($hexCode));
    if (empty($hex)) {
      return false;
    }

    $hexlen = strlen($hex);
    if ($hexlen < 3) {
      return $hexCode;
    } elseif ($hexlen == 3) {
      $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    if (strlen($hex) < 6) {
      return false;
    }

    $c = array_map('hexdec', str_split($hex, 2));

    $yiq = (float)((($c[0] * 299) + ($c[1] * 587) + ($c[2] * 114)) / 1000);
    return ($yiq >= 128);
  }

  public static function adjustColorBrightness($hexCode, $percent) {
    $hex = ltrim($hexCode, '#');
    if (empty($hex)) {
      return $hexCode;
    }

    $hexlen = strlen($hex);
    if ($hexlen < 3) {
      return $hexCode;
    } elseif ($hexlen == 3) {
      $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    $hex = array_map('hexdec', str_split($hex, 2));
    foreach ($hex as &$color) {
      $limit = $percent < 0 ? $color : 255 - $color;
      $amount = ceil($limit * $percent);

      $color = str_pad(dechex($color + $amount), 2, '0', STR_PAD_LEFT);
    }

    return '#' . implode($hex);
  }

  public static function quote($text) {
    if (!is_scalar($text)) return '""';
    $text = (string)$text;
    $len = strlen($text);

    $s = '"';
    for ($i = 0; $i < $len; ++$i) {
      $c = $text[$i];

      if ($c === '\\' || $c === '"') {
        $s .= '\\';
      }
      $s .= $c;
    }
    $s .= '"';

    return $s;
  }
}
