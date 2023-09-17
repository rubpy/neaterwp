<?php

if (!defined('WP_IS_NEATER')) {
  die();
}

NeaterWP\AssetManager::setPublicPrefix('public');
NeaterWP\AssetManager::useHashSuffixes(true);

NeaterWP\AssetManager::addTo(function () {
  ?>
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo NeaterWP\AssetManager::uri('/assets/icons/apple-touch-icon.png'); ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo NeaterWP\AssetManager::uri('/assets/icons/favicon-32x32.png'); ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo NeaterWP\AssetManager::uri('/assets/icons/favicon-16x16.png'); ?>">
  <link rel="manifest" href="<?php echo NeaterWP\AssetManager::uri('/assets/icons/site.webmanifest'); ?>">
  <link rel="mask-icon" href="<?php echo NeaterWP\AssetManager::uri('/assets/icons/safari-pinned-tab.svg'); ?>" color="#c76bf9">
  <meta name="apple-mobile-web-app-title" content="NeatyTheme">
  <meta name="application-name" content="NeatyTheme">
  <meta name="msapplication-TileColor" content="#c76bf9">
  <meta name="theme-color" content="#c76bf9">
  <?php
}, ['head', 'admin_head', 'login_head']);

NeaterWP\AssetManager::addTo(function () {
  ?>
  <link rel="stylesheet" type="text/css" href="<?php echo NeaterWP\AssetManager::uri('/assets/css/admin.css'); ?>">
  <script type="text/javascript" src="<?php echo NeaterWP\AssetManager::uri('/assets/js/admin.js'); ?>"></script>
  <?php
}, ['admin_head']);

NeaterWP\AssetManager::addTo(function () {
  ?>
  <style type="text/css">
.login h1 a {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' xml:space='preserve' width='512' height='512' style='overflow:visible;enable-background:new 0 0 512 512'%3E%3Cstyle%3E.st1%7Bfill:%235c0099%7D%3C/style%3E%3Cpath d='M0 0h512v512H0z' style='fill:%23c86bfa'/%3E%3Cpath d='M121.5 208.8c0 1.4 0 2.8-.2 4 8.6-8 18.2-14.6 25.4-14.6 5.8 0 11.2 7 11.2 14.6 0 4.4-3.6 10.8-10 22-6.4 10.2-13.2 23.4-13.2 30.8 0 4.2 1.4 6.8 4 6.8 6.8 0 17.2-13 24.4-26.4 2-3.8 4.8-3.6 6.6-.8 1.6 2.4 1.8 8.6-1 14-5.6 11-20.2 21.6-32.2 21.6-13.4 0-19.2-6.8-19.2-17.2 0-10.8 9.2-29.2 17-41.6 4.2-6.6 7.4-11.8 6-11.8-1.8 0-11.6 5.6-22.6 18-11 11.6-20 31.2-28.2 49.2-1.4 3.2-4 4.4-7.2 1.2-2.8-2.8-4.4-6.2-4.4-11 0-3 .6-6.4 1.8-10.4 3.8-11.2 19.2-39.2 31.6-57.8 2.6-3.8 5.6-3.6 7.6 0 1.8 3 2.6 6.2 2.6 9.4z' class='st1'/%3E%3Cpath d='M196.7 250c-4.8 0-9.6-1.4-14.2-4.4-1.2 4-1.8 8.2-1.8 11.8 0 8.8 3.6 15 9.6 15 15.2 0 28.6-14.8 36.8-27.2 2.2-3.2 4.8-3.6 6.4.4.6 1.8.8 7.6-2.8 13-8.4 12.2-24.4 21.6-40 21.8-18.6.2-28.8-12-28.8-28 0-7.2 2-15 6.2-23 10.4-21.2 26.6-31.8 38.2-31.8 2.8 0 5 .4 7 1.6 5.6 3.2 9.6 6.4 12.4 10.4 1.2 1.8 2 4.8 2 7.4 0 12-13.4 33-31 33zm-11.4-12c1.6 1 3 1.4 4.4 1.4 4.8 0 11.2-6 15.2-11.4 5.8-7.8 10-17.2 7.8-19-3-2.4-17.8 9.8-27.4 29z' class='st1'/%3E%3Cpath d='M278.7 197.4c7.2 0 11.6 3.6 13.4 8.8l3.8-6.2c2.4-4 5.8-4.2 8.2-1.4 3.4 4 5.2 12.8 0 23.2-5.2 11-22 32.8-22 46.2 0 3 .8 4.8 2.8 4.8 7.2 0 17.2-13.6 23.8-26.6 2.2-4 4-4.2 6 0 .8 1.8 2 7.2-.8 13-5.2 11-18 21.6-30.2 21.6-10 0-15.8-5.2-17.4-12.6-5.8 5.6-14.8 11.8-22.8 11.8-11.8 0-18.2-8.6-18.2-20.4 0-8.6 3.6-19.8 10.8-29.8 16-23 32.4-32.4 42.6-32.4zm-7 44.8c7.8-13.6 11.4-23.4 11.4-28.6 0-2.4-.4-3.8-2.4-3.8-4 0-17.8 12.6-28 29-6 9.4-9.4 20-9.4 26.4 0 3.6.8 6 2.8 6 6.6 0 15.8-12.2 25.6-29z' class='st1'/%3E%3Cpath d='M310.5 241.2c4-9.8 11.6-25.6 19.6-40-6.2-.8-9.8-3-11.4-5.4-2.8-4.2-.8-6.2 3.8-6 4.6.2 10 0 14.4-.4 6.2-10.8 10.4-17.4 15-24 3-4.4 5.4-4.2 7.6-.4 1.4 2.4 2.2 5.8 2.2 8.2 0 3.6-.8 8.2-3.2 13.8 6.2.8 9.6 4 11 6.4 2 3.6.6 6-4.2 5.4-3.6-.4-8.8 0-13.2.6-7.6 13-19 32.8-26 48-2.4 5.4-3.6 10.6-3.6 15 0 6 2 10.2 5.4 10.2 7.8 0 18.2-12.6 25.2-27.6 2.2-4.8 6.8-5.2 8.2.2 1 4.2.2 7.2-2 11.8-2.8 6-16.2 23.2-33 23.2-10.6 0-19.8-8.6-19.8-22.4 0-4.4 1.2-10 4-16.6z' class='st1'/%3E%3Cpath d='M428.7 257.6c-8 12.2-21.2 19.6-37.6 26.4-11 26.4-24.6 58.4-42 65-4.6 1.8-8.8 1.8-13.6-4.6-3-4.2-6-10.2-6-16.6 0-2.4.6-5.8 1.8-9.4 6.2-17.2 22.4-29.4 41.8-37.2l3-7c-5.2 3.8-10.4 5.6-16.4 5.6-10 0-14.4-7.6-14.4-16 0-4.2.8-9 2.6-14 4.6-12.6 14.4-32.8 26.8-49.8 2.6-3.4 5.6-3.6 7.8-.4 2 2.8 3.8 6.4 3.8 9.6 0 5-2.4 10.8-7 18.2-6.8 10.8-11 19-14.8 28.4-2.8 7.2-4.6 15.2.6 15.2 5.2 0 14.2-11.8 24-28.4 13.4-22.6 18.8-34.8 23.6-42 2.8-3.6 5.4-4 7.6-1.8 3 3 3.8 6 3.8 10.2 0 4-1 8.4-3.6 13.4-5.8 11.2-16.2 30.6-24.4 49.6 12.2-7.4 22.6-17.6 29-26.6 2.6-3.6 5.6-4 6.4-1.4 1 3.2.6 8.4-2.8 13.6zm-81.4 59.6c-5 9.4-6.4 17.6-4.4 19.4 1.4 1.2 5.6-4.6 11.2-14.4 4.2-7.4 9-17.2 13.8-28-9.6 6.6-16.4 15.2-20.6 23z' class='st1'/%3E%3C/svg%3E");
  background-size: 100% 100%;
  width: 100px;
  height: 100px;
  margin-bottom: 50px;
  border-radius: 12px;
  overflow: hidden;
}
  </style>
  <?php
}, ['login_head']);
