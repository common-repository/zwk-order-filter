<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              zworthkey.com
 * @since             1.0.0
 * @package           Zwk_Order_Filter
 *
 * @wordpress-plugin
 * Plugin Name:       Zwk Order Filter
 * Plugin URI:        zworthkey.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Zworthkey
 * Author URI:        zworthkey.com/about-us
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zwk-order-filter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_action( 'admin_notices', 'general_admin_notice' );
	return;
}
function general_admin_notice() {
	echo '<div class="notice notice-error  ">
             <p>woocommerce is not activate! It should be active to Use the plugin.</p>
         </div>';
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ZWK_ORDER_FILTER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-zwk-order-filter-activator.php
 */
function activate_zwk_order_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zwk-order-filter-activator.php';
	Zwk_Order_Filter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-zwk-order-filter-deactivator.php
 */
function deactivate_zwk_order_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zwk-order-filter-deactivator.php';
	Zwk_Order_Filter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_zwk_order_filter' );
register_deactivation_hook( __FILE__, 'deactivate_zwk_order_filter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-zwk-order-filter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_zwk_order_filter() {

	$plugin = new Zwk_Order_Filter();
	$plugin->run();
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'zwkof_add_action_links' );
}
run_zwk_order_filter();
function zwkof_add_action_links( $actions ) {
	$mylinks = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=zwk_order_filter' ) . '">Settings</a>',
	);
	$actions = array_merge( $mylinks, $actions );
	return $actions;
}