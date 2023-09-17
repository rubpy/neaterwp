<?php

if (!defined('WP_IS_NEATER')) die();

get_header();

if (NeaterWP\System::isInitialized()) {
  try {
    NeaterWP\PageManager::render();
  } catch (NeaterWP\SystemException $e) {
    NeaterWP\System::addLoggedException($e);
  }
}

get_footer();
