<?php
/**
 * Settings sidebar.
 *
 * @package WPDesk\FlexibleShippingUps
 */

namespace WPDesk\FlexibleShippingUps;

use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can display settings sidebar.
 */
class SettingsSidebar implements Hookable {

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'flexible_shipping_ups_settings_sidebar', [ $this, 'maybe_display_settings_sidebar' ] );
	}

	/**
	 * Maybe display settings sidebar.
	 */
	public function maybe_display_settings_sidebar() {
		if ( ! defined( 'FLEXIBLE_SHIPPING_UPS_PRO_VERSION' ) ) {
			$pro_url  = 'pl_PL' === get_locale() ? 'https://www.wpdesk.pl/sklep/ups-woocommerce/' : 'https://flexibleshipping.com/products/flexible-shipping-ups-pro/';
			$pro_url .= '?utm_source=ups-settings&utm_medium=link&utm_campaign=settings-upgrade-link';
			include __DIR__ . '/view/settings-sidebar-html.php';
		}
	}

}
