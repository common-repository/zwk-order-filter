<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       zworthkey.com
 * @since      1.0.0
 *
 * @package    Zwk_Order_Filter
 * @subpackage Zwk_Order_Filter/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Zwk_Order_Filter
 * @subpackage Zwk_Order_Filter/includes
 * @author     Zworthkey Vaibhav <sales@zworthkey.com>
 */
class Zwk_Order_Filter_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'zwk-order-filter',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
