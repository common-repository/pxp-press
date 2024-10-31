<?php
  $writeable = false;
  $htaccess_path = get_home_path() . ".htaccess";
  $file_exists = file_exists($htaccess_path);
  $file_is_writeable = is_writeable($htaccess_path);
  $setting_not_written = true;
  $setenvif_module = in_array("mod_setenvif", apache_get_modules());
  $xml_loaded = extension_loaded("xml");

  // let's check if we have access to write to .htaccess
  if ($file_exists) {
    // the file exists
    if ($file_is_writeable) {
      // and it's writeable!!!
      $writeable = true;
    }

    // Check to see if the Cloudfront Forwarded Proto Header setting is already set.
    $file = file_get_contents($htaccess_path);
    if (strpos($file, "SetEnvIf CloudFront-Forwarded-Proto \"^https$\" HTTPS") !== false) {
      $setting_not_written = false;
    }

    // check if the file has the SetEnvIf line set
  } else {
    // file doesn't exist.  Let's see if we can write it.
    $myfile = fopen($htaccess_path, "w");
    if ($myfile != false) {
      // Able to write .htaccess file
      $writeable = true;
      fclose($myfile); //close the file
      unlink($htaccess_path); //and delete it since we'll write to it later
    }
  }

  $code_snippet = "<IfModule mod_setenvif.c>\n  SetEnvIf CloudFront-Forwarded-Proto \"^https$\" HTTPS\n</IfModule>";

  // In case they did hit save and the htaccess file doesn't have that entry, let's actually do the save logic.
  if ( $setting_not_written && $writeable && 'POST' == $_SERVER['REQUEST_METHOD'] && check_admin_referer( 'pxp-press-options', 'save_settings_nonce') ) {
    $myfile = fopen($htaccess_path, "a");
    fwrite($myfile, "\n\n# BEGIN PXP Press\n" . $code_snippet . "\n# END PXP Press\n");
    fclose($myfile);
    $setting_not_written = false;
  }
  if ($setenvif_module == false) {
    ?>
    <div class="wrap">
      <p>You need to enable the apache module: <strong>setenvif</strong></p>
      <p>Via CLI: <code>sudo a2enmod setenvif</code></p>
      <p>Make sure to restart apache afterwards.</p>
    </div>
    <?php
  }
  if ($xml_loaded == false) {
    ?>
    <div class="wrap">
      <p>The PHP-XML module is required to invalidate Cloudfront.</p>
      <p>Via CLI: <code>sudo apt install php-xml</code></p>
      <p>Make sure to restart apache afterwards.</p>
    </div>
    <?php
  }
  if ($setting_not_written) { ?>
    <div class="wrap <?php if ($writeable) echo "htaccess_writeable" ?>">
      <?php if ($writeable) { ?>
        <p>We will add this to your <strong>.htaccess</strong> file when you click <strong>Save All Changes</strong>.</p>
      <?php } else { ?>
        Unable to write to your <strong>.htaccess</strong> file. Please add this line of code to it:
      <?php } ?>
        <pre><code><?php echo esc_html($code_snippet) ?></code></pre>
    </div>
  <?php } ?>
