<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    woocommerce_refund_and_exchange_lite
 * @subpackage woocommerce_refund_and_exchange_lite/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    woocommerce_refund_and_exchange_lite
 * @subpackage woocommerce_refund_and_exchange_lite/includes
 * @author     MakeWebBetter<webmaster@makewebbetter.com>
 */
class Woocommerce_Refund_And_Exchange_Lite_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-refund-and-exchange-lite',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
