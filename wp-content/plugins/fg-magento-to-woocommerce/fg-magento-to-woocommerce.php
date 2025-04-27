<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wordpress.org/plugins/fg-magento-to-woocommerce/
 * @since             1.0.0
 * @package           FG_Magento_to_WooCommerce
 *
 * @wordpress-plugin
 * Plugin Name:       FG Magento to WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/fg-magento-to-woocommerce/
 * Description:       A plugin to migrate categories, products, images and CMS from Magento to WooCommerce
 * Version:           3.20.4
 * Author:            Frédéric GILLES
 * Author URI:        https://www.fredericgilles.net/
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fg-magento-to-woocommerce
 * Domain Path:       /languages
 * WC tested up to:   5.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'FGM2WC_PLUGIN_VERSION', '3.20.4' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fg-magento-to-woocommerce-activator.php
 */
function activate_fg_magento_to_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fg-magento-to-woocommerce-activator.php';
	FG_Magento_to_WooCommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fg-magento-to-woocommerce-deactivator.php
 */
function deactivate_fg_magento_to_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fg-magento-to-woocommerce-deactivator.php';
	FG_Magento_to_WooCommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_fg_magento_to_woocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_fg_magento_to_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fg-magento-to-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fg_magento_to_woocommerce() {

	$plugin = new FG_Magento_to_WooCommerce();
	$plugin->run();

}
run_fg_magento_to_woocommerce();
