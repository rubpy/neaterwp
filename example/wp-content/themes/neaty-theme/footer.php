<?php

if (!defined('WP_IS_NEATER')) {
  die();
}

?>

  </main>

  <?php wp_footer();?>

  <script src="<?php echo NeaterWP\AssetManager::uri('/assets/js/lodash.min.js'); ?>"></script>
  <script src="<?php echo NeaterWP\AssetManager::uri('/assets/js/bootstrap.min.js'); ?>"></script>
  <script src="<?php echo NeaterWP\AssetManager::uri('/assets/js/theme.js'); ?>"></script>
</body>
</html>
