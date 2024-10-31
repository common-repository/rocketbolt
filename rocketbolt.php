<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: RocketBolt for Wordpress
Plugin URI: http://wordpress.org/extend/plugins/rocketbolt/
Description: Enables <a href="http://rocketbolt.com">RocketBolt website tracking</a> on all pages.
Version: 1.0.0
Author: RocketBolt, Inc.
Author URI: http://rocketbolt.com/
*/

function activate_rocketbolt() {
  add_option('rocketbolt_organization_id', '0');
  add_option('rocketbolt_property_id', '0');

  // Enable activation redirect
  add_option('rocketbolt_do_activation_redirect', true);
}

function rocketbolt_activation_redirect() {
    if (get_option('rocketbolt_do_activation_redirect', false)) {
        delete_option('rocketbolt_do_activation_redirect');
         wp_redirect("options-general.php?page=rocketbolt");
         exit;
    }
}

function deactive_rocketbolt() {
  delete_option('rocketbolt_organization_id');
  delete_option('rocketbolt_property_id');
}

function admin_init_rocketbolt() {
  register_setting('rocketbolt', 'rocketbolt_organization_id');
  register_setting('rocketbolt', 'rocketbolt_property_id');
}

function admin_menu_rocketbolt() {
  add_options_page('RocketBolt', 'RocketBolt', 'manage_options', 'rocketbolt', 'options_page_rocketbolt');
}

function options_page_rocketbolt() {
  include( plugin_dir_path( __FILE__ ) . 'activate.php');  
}

function rocketbolt() {
  $rocketbolt_organization_id = get_option('rocketbolt_organization_id');
  $rocketbolt_property_id = get_option('rocketbolt_property_id');
?>
<script async src="//script.rocketbolt.com/rocket.js#oid=<?php echo $rocketbolt_organization_id ?>&pid=<?php echo $rocketbolt_property_id ?>"></script>
<?php
}

register_activation_hook(__FILE__, 'activate_rocketbolt');
// Add post-activation redirect
add_action('admin_init', 'rocketbolt_activation_redirect');
register_deactivation_hook(__FILE__, 'deactive_rocketbolt');

if (is_admin()) {
  add_action('admin_init', 'admin_init_rocketbolt');
  add_action('admin_menu', 'admin_menu_rocketbolt');
}

if (!is_admin()) {
  add_action('wp_footer', 'rocketbolt');
}

?>
