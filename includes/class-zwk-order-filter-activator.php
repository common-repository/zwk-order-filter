<?php

/**
 * Fired during plugin activation
 *
 * @link       zworthkey.com
 * @since      1.0.0
 *
 * @package    Zwk_Order_Filter
 * @subpackage Zwk_Order_Filter/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Zwk_Order_Filter
 * @subpackage Zwk_Order_Filter/includes
 * @author     Zworthkey Vaibhav <sales@zworthkey.com>
 */
class Zwk_Order_Filter_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$error .= '<div class="error notice">';
			$error .= '<p>Zwk Order Filters for WooCommerce Plugin requires WooCommerce to be installed and active.</p>';
			$error .= '</div>';
			echo $error;
		}

	}
}
