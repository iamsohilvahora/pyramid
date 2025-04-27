<?php

/**
 * Module to check the modules that are needed
 *
 * @link       https://wordpress.org/plugins/fg-magento-to-woocommerce/
 * @since      2.12.0
 *
 * @package    FG_Magento_to_WooCommerce
 * @subpackage FG_Magento_to_WooCommerce/admin
 */

if ( !class_exists('FG_Magento_to_WooCommerce_Modules_Check', false) ) {

	/**
	 * Class to check the modules that are needed
	 *
	 * @package    FG_Magento_to_WooCommerce
	 * @subpackage FG_Magento_to_WooCommerce/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Magento_to_WooCommerce_Modules_Check {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    2.12.0
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {

			$this->plugin = $plugin;

		}

		/**
		 * Check if some modules are needed
		 *
		 * @since    2.12.0
		 */
		public function check_modules() {
			$premium_url = 'https://www.fredericgilles.net/fg-magento-to-woocommerce/';
			$message_premium = __('Your Magento database contains %s. You need the <a href="%s" target="_blank">Premium version</a> to import them.', 'fg-magento-to-woocommerce');
			if ( defined('FGM2WCP_LOADED') ) {
				// Message for the Premium version
				$message_addon = __('Your Magento database contains %1$s. You need the <a href="%3$s" target="_blank">%4$s</a> to import them.', 'fg-magento-to-woocommerce');
			} else {
				// Message for the free version
				$message_addon = __('Your Magento database contains %1$s. You need the <a href="%2$s" target="_blank">Premium version</a> and the <a href="%3$s" target="_blank">%4$s</a> to import them.', 'fg-magento-to-woocommerce');
			}
			$modules = array(
				// Check if we need the Premium version: check the number of customers
				array(array($this, 'count'),
					array('customer_entity', 2),
					'fg-magento-to-woocommerce-premium/fg-magento-to-woocommerce-premium.php',
					sprintf($message_premium, __('several customers', 'fg-magento-to-woocommerce'), $premium_url)
				),
				
				// Check if we need the Customer Groups module
				array(array($this, 'count_used_customer_groups'),
					array(1),
					'fg-magento-to-woocommerce-premium-customer-groups-module/fgm2wc-customer-groups.php',
					sprintf($message_addon, __('customer groups', 'fg-magento-to-woocommerce'), $premium_url, $premium_url . 'customer-groups/', __('Customer Groups add-on', 'fg-magento-to-woocommerce'))
				),
				
				// Check if we need the WPML module
				array(array($this, 'count_languages'),
					array(1),
					'fg-magento-to-woocommerce-premium-wpml-module/fgm2wc-wpml.php',
					sprintf($message_addon, __('several languages', 'fg-magento-to-woocommerce'), $premium_url, $premium_url . 'wpml/', __('WPML add-on', 'fg-magento-to-woocommerce'))
				),
				
				// Check if we need the Brands module
				array(array($this, 'count_manufacturers'),
					array(1),
					'fg-magento-to-woocommerce-premium-brands-module/fgm2wc-brands.php',
					sprintf($message_addon, __('manufacturers', 'fg-magento-to-woocommerce'), $premium_url, $premium_url . 'brands/', __('Brands add-on', 'fg-magento-to-woocommerce'))
				),
				
				// Check if we need the Product Options module
				array(array($this, 'count_options'),
					array(1),
					'fg-magento-to-woocommerce-premium-product-options-module/fgm2wc-product-options.php',
					sprintf(__('Your Magento database contains some product options. If you have got many options combinations or if you get the message "Too many variations", you may need the <a href="%s" target="_blank">WooCommerce Product Add-Ons plugin</a> and the <a href="%s" target="_blank">Product Options add-on</a> to import the Magento options as add-ons instead of as variations.', 'fg-magento-to-woocommerce'), 'https://woocommerce.com/products/product-add-ons/?aff=3777', 'https://www.fredericgilles.net/fg-magento-to-woocommerce/product-options/'),
				),
				
				// Check if we need the Custom Order Statuses module
				array(array($this, 'count_custom_order_statuses'),
					array(0),
					'fg-magento-to-woocommerce-premium-custom-order-statuses-module/fgm2wc-custom-order-statuses.php',
					sprintf(__('Your Magento database contains some custom order statuses. To import them, you need the <a href="%s" target="_blank">WooCommerce Order Status Manager plugin</a> and the <a href="%s" target="_blank">Custom Order Statuses add-on</a>.', 'fg-magento-to-woocommerce'), 'https://woocommerce.com/products/woocommerce-order-status-manager/?aff=3777', 'https://www.fredericgilles.net/fg-magento-to-woocommerce/custom-order-statuses/'),
				),
				
				// Check if we need the Product Bundles module
				array(array($this, 'count'),
					array('catalog_product_bundle_selection', 0),
					'fg-magento-to-woocommerce-premium-product-bundles-module/fgm2wc-product-bundles.php',
					sprintf(__('Your Magento database contains some bundle products. To import them, you need the <a href="%s" target="_blank">WooCommerce Product Bundles plugin</a> and the <a href="%s" target="_blank">Product Bundles add-on</a>.', 'fg-magento-to-woocommerce'), 'https://woocommerce.com/products/product-bundles/?aff=3777', 'https://www.fredericgilles.net/fg-magento-to-woocommerce/product-bundles/'),
				),
				
				// Check if we need the Wish Lists module
				array(array($this, 'count'),
					array('wishlist', 0),
					'fg-magento-to-woocommerce-premium-wishlists-module/fgm2wc-wishlists.php',
					sprintf($message_addon, __('wish lists', 'fg-magento-to-woocommerce'), $premium_url, $premium_url . 'wishlists/', __('Wish Lists add-on', 'fg-magento-to-woocommerce')),
				),
				
				// Check if we need the Tiered Prices module
				array(array($this, 'count'),
					array('catalog_product_entity_tier_price', 0),
					'fg-magento-to-woocommerce-premium-tiered-prices-module/fgm2wc-tiered-prices.php',
					sprintf($message_addon, __('tiered prices', 'fg-magento-to-woocommerce'), $premium_url, $premium_url . 'tiered-prices/', __('Tiered Prices add-on', 'fg-magento-to-woocommerce')),
				),
				
				// Check if we need the AW Blog module
				array(array($this, 'count'),
					array('aw_blog', 0),
					'fg-magento-to-woocommerce-premium-awblog-module/fgm2wc-awblog.php',
					sprintf($message_addon, __('AW Blog data', 'fg-magento-to-woocommerce'), $premium_url, $premium_url . 'awblog/', __('AW Blog add-on', 'fg-magento-to-woocommerce')),
				),
				
			);
			foreach ( $modules as $module ) {
				list($callback, $params, $plugin, $message) = $module;
				if ( !is_plugin_active($plugin) ) {
					if ( call_user_func_array($callback, $params) ) {
						$this->plugin->display_admin_warning($message);
					}
				}
			}
		}

		/**
		 * Count the number of rows in the table
		 *
		 * @since    2.12.0
		 *
		 * @param string $table Table
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count($table, $min_value) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "SELECT COUNT(*) AS nb FROM ${prefix}${table}";
			return ($this->count_sql($sql) > $min_value);
		}

		/**
		 * Count the number of used customer groups
		 *
		 * @since    2.12.0
		 *
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count_used_customer_groups($min_value) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "SELECT COUNT(DISTINCT(group_id)) AS nb FROM ${prefix}customer_entity";
			return ($this->count_sql($sql) > $min_value);
		}

		/**
		 * Count the number of languages
		 *
		 * @since    2.12.0
		 *
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count_languages($min_value) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$store_table = version_compare($this->plugin->magento_version, '2', '<')? 'core_store' : 'store';
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}${store_table} AS nb
				WHERE code IN ('en', 'English', 'es', 'Spanish', 'de', 'German', 'fr', 'French', 'ar', 'Arabic', 'bs', 'Bosnian', 'bg', 'Bulgarian', 'ca', 'Catalan', 'cs', 'Czech', 'sk', 'Slovak', 'cy', 'Welsh', 'da', 'Danish', 'el', 'Greek', 'eo', 'Esperanto', 'et', 'Estonian', 'eu', 'Basque', 'fa', 'Persian', 'fi', 'Finnish', 'ga', 'Irish', 'he', 'Hebrew', 'hi', 'Hindi', 'hr', 'Croatian', 'hu', 'Hungarian', 'hy', 'Armenian', 'id', 'Indonesian', 'is', 'Icelandic', 'it', 'Italian', 'ja', 'Japanese', 'ko', 'Korean', 'ku', 'Kurdish', 'la', 'Latin', 'lv', 'Latvian', 'lt', 'Lithuanian', 'mk', 'Macedonian', 'mt', 'Maltese', 'mo', 'Moldavian', 'mn', 'Mongolian', 'ne', 'Nepali', 'nl', 'Dutch', 'nb', 'Norwegian Bokmål', 'pa', 'Punjabi', 'pl', 'Polish', 'pt-pt', 'Portuguese, Portugal', 'pt-br', 'Portuguese, Brazil', 'qu', 'Quechua', 'ro', 'Romanian', 'ru', 'Russian', 'sl', 'Slovenian', 'so', 'Somali', 'sq', 'Albanian', 'sr', 'Serbian', 'sv', 'Swedish', 'ta', 'Tamil', 'th', 'Thai', 'tr', 'Turkish', 'uk', 'Ukrainian', 'ur', 'Urdu', 'uz', 'Uzbek', 'vi', 'Vietnamese', 'yi', 'Yiddish', 'zh-hans', 'Chinese (Simplified)', 'zu', 'Zulu', 'zh-hant', 'Chinese (Traditional)', 'ms', 'Malay', 'my', 'Burmese')
			";
			return ($this->count_sql($sql) > $min_value);
		}

		/**
		 * Count the number of manufacturers
		 *
		 * @since    2.12.0
		 *
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count_manufacturers($min_value) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}eav_attribute_option o
				INNER JOIN ${prefix}eav_attribute a ON a.attribute_id = o.attribute_id
				WHERE a.attribute_code IN ('manufacturer', 'brand')
			";
			return ($this->count_sql($sql) > $min_value);
		}

		/**
		 * Count the number of product options
		 *
		 * @since    2.37.0
		 *
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count_options($min_value) {
			$prefix = $this->plugin->plugin_options['prefix'];
			
			$sql_options = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}catalog_product_option o
			";
			$options_count = $this->count_sql($sql_options);
			
			$sql_bundle_options = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}catalog_product_bundle_option o
			";
			$bundle_options_count = $this->count_sql($sql_bundle_options);
			
			return ($options_count + $bundle_options_count > $min_value);
		}

		/**
		 * Count the number of custom order statuses
		 *
		 * @since    2.42.0
		 *
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count_custom_order_statuses($min_value) {
			$result = false;
			$prefix = $this->plugin->plugin_options['prefix'];
			
			if ( $this->plugin->table_exists('sales_flat_order') ) {
				$orders_table = 'sales_flat_order';
			} else {
				$orders_table = 'sales_order';
			}
			if ( $this->plugin->table_exists('sales_order_status_state') ) {
				$sql = "
					SELECT COUNT(*) AS nb
					FROM ${prefix}$orders_table o
					INNER JOIN ${prefix}sales_order_status_state oss ON oss.status = o.status
					WHERE oss.is_default = 0
				";
				$result = $this->count_sql($sql) > $min_value;
			}
			
			return $result;
		}

		/**
		 * Execute the SQL request and return the nb value
		 *
		 * @since    2.12.0
		 *
		 * @param string $sql SQL request
		 * @return int Count
		 */
		private function count_sql($sql) {
			$count = 0;
			$result = $this->plugin->magento_query($sql, false);
			if ( isset($result[0]['nb']) ) {
				$count = $result[0]['nb'];
			}
			return $count;
		}

	}
}
