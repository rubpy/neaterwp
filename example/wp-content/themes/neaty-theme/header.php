<?php

if (!defined('WP_IS_NEATER')) {
  die();
}

?>
<!doctype html>
<html <?php language_attributes();?>>
<head>
  <meta charset="<?php bloginfo('charset');?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">

  <link rel="profile" href="http://gmpg.org/xfn/11">
  <?php if (is_singular() && pings_open(get_queried_object())): ?>
  <link rel="pingback" href="<?php echo esc_url(get_bloginfo('pingback_url')); ?>">
  <?php endif;?>

  <link href="<?php echo NeaterWP\AssetManager::uri('/assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo NeaterWP\AssetManager::uri('/assets/css/theme.css'); ?>" rel="stylesheet">

  <?php wp_head();?>
</head>
<body <?php body_class();?>>
  <?php do_action('wp_body_open');?>

  <main>
    <header class="neaty-header">
      <div class="container">
        <div class="neaty-header-logo"></div>
      </div>
    </header>
