<?php

if (!defined('ABSPATH')) die();

$themeException = function () {
  return new RuntimeException('"' . $themeName . '" theme appears corrupted');
};

$wpTheme = wp_get_theme();
if (!($wpTheme instanceof WP_Theme)) {
  throw $themeException();
}
$themeName = (string)$wpTheme->name;

$themePath = dirname(__FILE__) . '/_theme';
if (!is_dir($themePath)) {
  throw $themeException();
}

$themeInitFilename = $themePath . '/init.php';
if (!file_exists($themeInitFilename)) {
  throw $themeException();
}

require_once($themeInitFilename);

if (!defined('WP_IS_NEATER')) {
  throw $themeException();
}
