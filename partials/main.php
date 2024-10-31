<?php
  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
  $xml_loaded = false; # default in case fileaccess.php isn't included or setting $xml_loaded
?>
<div class="pxp-header">
  <div class="pxp-header__background" style="background-image: url('<?php echo $plugin_dir . "assets/bg-md-res.png" ?>')">
    <div class="pxp-header__logo-background">
      <img class="pxp-header__logo" src="<?php echo $plugin_dir . "assets/pxp_logo.png" ?>" />
    </div>
  </div>
</div>
<div class="pxp-content-container">
  <?php include('navMenu.php') ?>
  <form id="cloudfrontConfigForm" method="post">
    <?php wp_nonce_field('pxp-press-options', 'save_settings_nonce') ?>
    <?php $dir = plugin_dir_path( __FILE__ ); ?>
    <?php include(__dir__ . '/cloudfront.php') ?>
    <?php include(__dir__ . '/fileaccess.php') ?>
    <?php include( __dir__ . '/about.php') ?>
    <div class="pxp-button-container">
      <input type="submit" value="Save All Changes" class="button button-primary button-large pxp-form-button" />
      <a href="/wp-admin/admin.php?page=pxp_press" class="button button-large pxp-form-button">Cancel</a>
      <a id="<?php echo $xml_loaded == true ? "invalidateNow" : "" ?>" href="javascript:;" class="button button-primary pxp-form-button pxp-form-button--right"<?php echo $xml_loaded != true ? " disabled=\"disabled\"" : "" ?>>Invalidate Now</a>
    </div>
  </form>
  <form id="cloudfrontInvalidateNow" method="post">
    <?php wp_nonce_field('pxp-press-options', 'save_settings_nonce') ?>
    <input type="hidden" name="pxp-press-resetNow" value="true" />
  </form>

</div>
