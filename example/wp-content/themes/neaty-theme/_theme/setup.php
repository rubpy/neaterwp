<?php

if (!defined('WP_IS_NEATER')) {
  die();
}

NeaterWP\System::setDirectory(dirname(__FILE__));

// ==================================================

add_filter('use_block_editor_for_post', '__return_false');

add_filter('kses_allowed_protocols', function ($protocols) {
  $target = 'data';
  if (is_array($protocols) && !in_array($target, $protocols)) {
    $protocols[] = $target;
  }

  return $protocols;
});

add_filter('login_title', function ($loginTitle, $title) {
  return $title . ' · ' . get_bloginfo('name');
}, 10, 2);
add_filter('admin_title', function ($adminTitle, $title) {
  $suffix = ' &#8212; WordPress';
  $suffixLength = strlen($suffix);

  $len = strlen($adminTitle);
  if ($len >= $suffixLength && strpos($adminTitle, $suffix) === ($len - $suffixLength)) {
    $adminTitle = substr($adminTitle, 0, -$suffixLength);
  }

  return $adminTitle;
}, 10, 2);

add_action('init', function () {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');

  add_action('wp_dashboard_setup', function () {
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
  });

  add_action('customize_register', function ($wp_customize) {
    $wp_customize->remove_control('site_icon');
  }, 20);
  add_filter('pings_open', '__return_false', 20, 2);

  $disableQuickEdit = function ($actions) {
    unset($actions['inline hide-if-no-js']);
    return $actions;
  };
  add_filter('page_row_actions', $disableQuickEdit, 10, 1);
  add_filter('post_raw_actions', $disableQuickEdit, 10, 1);

  // ----------

  add_action('admin_menu', function () {
    global $menu;
    if (!is_array($menu) || empty($menu)) {
      return;
    }

    $user = wp_get_current_user();
    if ($user instanceof WP_User && $user->exists()) {
      $menuPagesKey = null;
      foreach ($menu as $k => $v) {
        if (!is_scalar($k) || !is_array($v) || count($v) < 2
          || !isset($v[1]) || !is_string($v[1])) {
          continue;
        }

        if ($v[1] === 'edit_pages') {
          $menuPagesKey = $k;
          break;
        }
      }

      $homepagePost = NeaterWP\PageManager::wpFront();

      $homepageEditLink = '';
      if ($homepagePost instanceof WP_Post && isset($homepagePost->post_type)) {
        $postType = get_post_type_object($homepagePost->post_type);
        if ($postType && isset($postType->_edit_link) && !empty($postType->_edit_link)) {
          $action = '&action=edit';
          $homepageEditLink = admin_url(sprintf($postType->_edit_link . $action, $homepagePost->ID));
        }
      }

      if (!empty($homepageEditLink) && $user->has_cap('edit_post', $homepagePost->ID)) {
        if ($menuPagesKey !== null && is_numeric($menuPagesKey)) {
          $nextKey = (string) (floatval($menuPagesKey) + 0.5);
          if (!isset($menu[$nextKey])) {
            $menu[$nextKey] = [
              'Edit homepage',
              'edit_pages',
              $homepageEditLink,
              '',
              'menu-top menu-icon-page',
              'neaty-menu-homepage',
              'dashicons-admin-page',
            ];
          }
        }
      }
    }
  });
});

add_action('admin_init', function () {
  add_filter('admin_footer_text', function () {
    return '© Neaty (NeaterWP example theme)';
  });
});

add_action('wp_logout', function () {
  wp_redirect(home_url());
  die();
});

$printFooterScripts = function () {
  $themeData = [
    'system' => [],
  ];

  $user = wp_get_current_user();
  if ($user instanceof WP_User && $user->exists()) {
    $themeData['system']['on_admin_page'] = is_admin();

    if ($user->has_cap('edit_theme_options') || $user->has_cap('switch_themes')) {
      $loggedExceptions = NeaterWP\System::getLoggedExceptions();

      if (!empty($loggedExceptions)) {
        $es = [];

        foreach ($loggedExceptions as $e) {
          if (!($e instanceof \Exception)) {
            continue;
          }

          $level = 'error';
          if ($e instanceof NeaterWP\SystemPseudoException) {
            $level = $e->getLevel();
          }

          $es[] = [
            'level' => $level,
            'class' => get_class($e),
            'message' => (string) $e,
          ];
        }

        $themeData['system']['logged_exceptions'] = $es;
      }
    }
  }

  $encodedJson = '';
  try {
    $encodedJson = @json_encode($themeData);
  } catch (Exception $e) {}
  if (empty($encodedJson) || !is_string($encodedJson)) {
    $encodedJson = '{}';
  }

  $encoded = base64_encode($encodedJson);

  ?>
  <script type="text/javascript">window._ntrdata="<?php echo $encoded ?>";</script>
  <?php

  if (isset($themeData['system']['logged_exceptions'])):

  ?>
  <script type="text/javascript">
!function(){function n(e,r,o,n){if("string"!=typeof e||0===e.length)return null;null!==r&&"object"==typeof r||(r={}),null===o||"string"==typeof o||Array.isArray(o)||(o=null),null!==n&&"object"==typeof n||(n={});var t,i,a=document.createElement(e);for(t in r)a.setAttribute(t,r[t]);if(null!==o)if("string"==typeof o)a.innerText=o;else if(Array.isArray(o))for(var l of o)null!==l&&l instanceof Node&&a.appendChild(l);for(i in n)if(Array.isArray(typeof n[i]))for(var x of n[i])a.addEventListener(i,x);else a.addEventListener(i,n[i]);return a}var e,r={},o="_ntrdata",t=window&&"string"==typeof window[o]?window[o]:"",i=null;if(0<t.length){try{t=atob(t),i=JSON.parse(t)}catch(e){}null!==i&&"object"==typeof i&&(r=i),delete window[o]}null!==r[o="system"]&&"object"==typeof r[o]&&null!==(t=r[o])[o="logged_exceptions"]&&Array.isArray(t[o])&&0<(i=t[o]).length&&(r="ntrwp-exbox-style",null===document.getElementById(r)&&(r=n("style",{id:r},`
.ntrwp-exbox { display: none; position: absolute; top: 60px; left: 50%; transform: translateX(-50%); width: 600px; max-width: 95%; flex-direction: column; margin: 20px 0; padding: 0; background: #343a40; color: #fff; border-radius: 15px; z-index: 9999; overflow: hidden; box-shadow: 0 20px 40px rgba(16, 16, 16, 0.4); }
.ntrwp-exbox.is-open { display: flex; }
.ntrwp-exbox .ntrwp-exbox__header { max-width: 100%; font-size: 1.15em; font-weight: 600; display: flex; padding: 24px 38px 28px 38px; background: #212529; }
.ntrwp-exbox .ntrwp-exbox__header .ntrwp-exbox__actions { display: inline-block; margin-left: auto; }
.ntrwp-exbox .ntrwp-exbox__header .ntrwp-exbox__close { display: inline-block; padding: 4px; margin: 0; border: none; background: transparent; vertical-align: middle; line-height: 0; opacity: 0.5; transition: opacity 0.25s ease-in-out; }
.ntrwp-exbox .ntrwp-exbox__header .ntrwp-exbox__close > .icon { width: 12px; height: 12px; }
.ntrwp-exbox .ntrwp-exbox__header .ntrwp-exbox__close:hover { opacity: 0.8; }
.ntrwp-exbox .icon { display: inline-block; vertical-align: middle; width: 18px; height: 18px; background: transparent; background-repeat: no-repeat; background-size: 100% 100%; }
.ntrwp-exbox .icon.icon--error { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' xml:space='preserve' width='341.1' height='341.1' style='overflow:visible;enable-background:new 0 0 341.1 341.1'%3E%3Cstyle%3E.st0%7Bfill:%23fff%7D%3C/style%3E%3Cpath d='M170.6 0C76.4 0 0 76.4 0 170.6s76.4 170.6 170.6 170.6 170.6-76.4 170.6-170.6S264.7 0 170.6 0zm0 312.7c-78.5 0-142.1-63.6-142.1-142.1S92.1 28.4 170.6 28.4 312.7 92 312.7 170.5 249 312.7 170.6 312.7z' class='st0'/%3E%3Cpath d='M170.6 200c-10.2 0-18.5-8.3-18.5-18.5V96.2c0-10.2 8.3-18.5 18.5-18.5s18.5 8.3 18.5 18.5v85.3c-.1 10.2-8.3 18.5-18.5 18.5z' class='st0'/%3E%3Ccircle cx='169.8' cy='241.9' r='21.3' class='st0'/%3E%3C/svg%3E"); }
.ntrwp-exbox .icon.icon--close { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' xml:space='preserve' width='333.3' height='333.3' style='overflow:visible;enable-background:new 0 0 333.3 333.3'%3E%3Cpath d='M213.8 166.7 323.6 56.9c13-13 13-34.1 0-47.1s-34.1-13-47.1 0L166.7 119.5 56.9 9.8c-13-13-34.1-13-47.1 0s-13 34.1 0 47.1l109.8 109.8L9.8 276.4c-13 13-13 34.1 0 47.1s34.1 13 47.1 0l109.8-109.8 109.8 109.8c6.5 6.5 15 9.8 23.6 9.8 8.5 0 17.1-3.3 23.6-9.8 13-13 13-34.1 0-47.1L213.8 166.7z' style='fill:%23fff'/%3E%3C/svg%3E"); }
.ntrwp-exbox .ntrwp-exbox__title { cursor: default; }
.ntrwp-exbox .ntrwp-exbox__title > .icon { margin: 0 17px 0 0; }
.ntrwp-exbox .ntrwp-exbox__title > span { display: inline-block; vertical-align: middle; margin: -1px 0 0 0; }
.ntrwp-exbox .ntrwp-exbox__errors { max-width: 100%; display: flex; flex-direction: column; padding: 22px 34px 35px 32px; }
.ntrwp-exbox .ntrwp-exbox__error { display: flex; flex-direction: column; max-width: 100%; padding: 22px 25px 23px 26px; background: #3e4c57; color: #cbcfd2; border-left: 6px solid #567a92; border-top-right-radius: 14px; border-bottom-right-radius: 14px; overflow: hidden; cursor: default; font-family: var(--bs-font-monospace, monospace); line-height: 1.6; }
.ntrwp-exbox .ntrwp-exbox__error.ntrwp-exbox__error--error { background: #62403f; color: #beaead; border-left-color: #8e423e; }
.ntrwp-exbox .ntrwp-exbox__error.ntrwp-exbox__error--warning { background: #524b3f; color: #b6b2ad; border-left-color: #836a3c; }
.ntrwp-exbox .ntrwp-exbox__error + .ntrwp-exbox__error { margin-top: 13px; }
.ntrwp-exbox .ntrwp-exbox__error .ntrwp-exbox__error__class { font-size: 1em; font-weight: 600; display: block; margin: 0 0 12px 0; color: #ffffff; max-width: 100%; word-break: break-word; }
.ntrwp-exbox .ntrwp-exbox__error .ntrwp-exbox__error__message { font-size: 0.9em; font-weight: 500; display: block; margin: 0; padding: 0 0 0 11px; max-width: 100%; word-break: break-word; }
`),document.head.appendChild(r)),e=null,e=n("aside",{id:"ntrwp-exbox",class:"ntrwp-exbox"},[n("header",{class:"ntrwp-exbox__header"},[n("div",{class:"ntrwp-exbox__title"},[n("i",{class:"icon icon--error"}),n("span",{},"Theme messages")]),n("div",{class:"ntrwp-exbox__actions"},[n("button",{type:"button",class:"ntrwp-exbox__close"},[n("i",{class:"icon icon--close"})],{click:function(){e&&e.classList.remove("is-open")}})])]),n("section",{class:"ntrwp-exbox__errors"},i.map(function(e){var r,o;return null===e||"object"!=typeof e||(r="string"==typeof e.level?e.level:"",o="string"==typeof e.class?e.class:"",e="string"==typeof e.message?e.message:"",0===o.length&&0===e.length)?null:n("div",{class:"ntrwp-exbox__error"+(0<(r=["error","warning","info"].indexOf(r)<0?"error":r).length?" ntrwp-exbox__error--"+r:"")},[n("h4",{class:"ntrwp-exbox__error__class"},o),n("p",{class:"ntrwp-exbox__error__message"},e)])}))]),document.body.appendChild(e),(o="on_admin_page")in t)&&!t[o]&&e.classList.add("is-open")}();
  </script>
  <?php
endif;

  $encoded = '';
  $themeData = null;
};
add_action('admin_print_footer_scripts', $printFooterScripts);
add_action('wp_print_footer_scripts', $printFooterScripts);

// ==================================================

NeaterWP\System::checkRequirements([
  ['class', 'WP_Post'],

  ['class', 'ACF', 'ACF plugin'],
  ['class', 'acf_plugin_repeater', 'ACF Repeater plugin'],
]);
