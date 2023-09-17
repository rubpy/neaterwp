<?php

if (!defined('ABSPATH')) {
  die();
}

$guardFlag = 'WP_IS_NEATER';
if (defined($guardFlag)) {
  return;
} else {
  define($guardFlag, 1);
}

require_once dirname(__FILE__) . '/vendor/autoload.php';

if (!class_exists('NeaterWP\\System')) {
  throw new RuntimeException('NeaterWP failed to load properly');
}

try {
  require_once dirname(__FILE__) . '/setup.php';
  require_once dirname(__FILE__) . '/assets.php';
  require_once dirname(__FILE__) . '/templates.php';
  require_once dirname(__FILE__) . '/pages.php';

  NeaterWP\System::setInitialized(true);
} catch (NeaterWP\SystemException $e) {
  NeaterWP\System::addLoggedException($e);
}
