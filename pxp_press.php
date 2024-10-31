<?php
/*
Plugin Name: PXP Press
Description: A plugin that invalidates all files in a Cloudfront CDN
Version: 1.5.3
Author: PXP
Author URI: http://pxp200.com
License: GPL2
*/

use Aws\CloudFront\CloudFrontClient;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// When plugin is deleted, remove settings
function remove_pxp_press_data() {
  delete_option('pxp-press-accessKeyID');
  delete_option('pxp-press-secretKey');
  delete_option('pxp-press-distributionID');
  delete_option('pxp-press-region');
  delete_option('pxp-press-publisher');
  delete_option('pxp-press-publisher_key');
}
register_uninstall_hook( __FILE__, 'remove_pxp_press_data' );

function pxp_press_add_settings_link( $links ) {
    $settings_link = '<a href="/wp-admin/admin.php?page=pxp_press">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'pxp_press_add_settings_link' );

// Logic to do the cloudfront invalidation on post save
function pxp_press_logic() {
  $xml_is_loaded = extension_loaded("xml");
  $access_key = get_option('pxp-press-accessKeyID');
  $secret_key = get_option('pxp-press-secretKey');
  $distribution = get_option('pxp-press-distributionID');
  $region = get_option('pxp-press-region');
  $epoch = date('U');

  if ($xml_is_loaded == true && $access_key != "" && $secret_key != "" & $distribution != "" && $region != "") {
    $sharedConfig = [
        'region'  => $region,
        'version' => 'latest',
        'credentials' => [
          'key'    => $access_key,
          'secret' => $secret_key,
        ],
    ];

    require_once( plugin_dir_path( __FILE__ ) . '/aws-sdk/aws-autoloader.php');

    $client = new CloudFrontClient($sharedConfig);

    try {
      $client->createInvalidation([
        'DistributionId' => $distribution,
        'InvalidationBatch' => [
          'CallerReference' => $distribution . $epoch,
          'Paths' => [
            'Items' => ['/*'],
            'Quantity' => 1,
          ],
        ],
      ]);
    } catch (Exception $err) {
    }
  }
}
add_action('save_post', 'pxp_press_logic');
add_action('wp_trash_post', 'pxp_press_logic');

// Add PXP Press to the Admin Menu
function pxp_press_menu() {
	add_menu_page('PXP Press', 'Performance', 'manage_options', 'pxp_press', 'pxp_press_options', 'dashicons-cloud', 90);
}
add_action('admin_menu', 'pxp_press_menu');

// Include styles
function pxp_press_styles_and_scripts($hook) {
  // Load only on ?page=pxp_press
  if($hook != 'toplevel_page_pxp_press') {
    return;
  }

  // Styles
  wp_enqueue_style( 'admin_google_font', 'https://fonts.googleapis.com/css?family=PT+Sans' );
  wp_enqueue_style( 'admin_css', plugins_url('styles/pxp_press.css', __FILE__), array(), filemtime(__dir__ . '/styles/pxp_press.css'));
  // Scripts
  wp_enqueue_script( 'pxp_obfuscate', plugins_url('scripts/obfuscate.js', __FILE__ ), array(), filemtime(__dir__ . '/scripts/obfuscate.js'), true );
  wp_enqueue_script( 'pxp_nav_menu', plugins_url('scripts/pxp_nav_menu.js', __FILE__ ), array(), filemtime(__dir__ . '/scripts/pxp_nav_menu.js'), true );
  wp_enqueue_script( 'pxp_press', plugins_url('scripts/pxp_press.js', __FILE__ ), array(), filemtime(__dir__ . '/scripts/pxp_press.js'), true );
}
add_action( 'admin_enqueue_scripts', 'pxp_press_styles_and_scripts' );

// Plugin setup callback
function pxp_press_options() {
  // Make sure they have rights
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

  if ( 'POST' == $_SERVER['REQUEST_METHOD'] && check_admin_referer( 'pxp-press-options', 'save_settings_nonce') ) {
    $save = false;
    // Save settings logic (on POST)
    if (isset($_POST['pxp-press-resetNow'])) {
      pxp_press_logic();
      ?>
      <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Invalidation Has Been Sent', 'invalidation_text' ); ?></p>
      </div>
      <?php
    }
    if (isset($_POST['pxp-press-accessKeyID'])) {
      update_option('pxp-press-accessKeyID', sanitize_text_field($_POST['pxp-press-accessKeyID']));
    }
    if (isset($_POST['pxp-press-secretKey'])) {
      update_option('pxp-press-secretKey', sanitize_text_field($_POST['pxp-press-secretKey']));
      $save = true;
    }
    if (isset($_POST['pxp-press-distributionID'])) {
      update_option('pxp-press-distributionID', sanitize_text_field($_POST['pxp-press-distributionID']));
      $save = true;
    }
    if (isset($_POST['pxp-press-region'])) {
      update_option('pxp-press-region', sanitize_text_field($_POST['pxp-press-region']));
      $save = true;
    }
    if ($save === true) {
      ?>
      <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Saved Cloudfront Settings', 'cloudfront_save_text' ); ?></p>
      </div>
      <?php
    }
  }

  // Load saved settings
  $accessKey = get_option('pxp-press-accessKeyID');
  $secretKey = get_option('pxp-press-secretKey');
  $distributionID = get_option('pxp-press-distributionID');
  $region = get_option('pxp-press-region');

  // Load the page content
  $plugin_dir = plugin_dir_url(__FILE__);
  include('partials/main.php');
}

function pxp_press_init() {
	if (isset($_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO']) === 'https') {
		$_SERVER['HTTPS'] = 'on';
	}
}
add_action('init', 'pxp_press_init');
