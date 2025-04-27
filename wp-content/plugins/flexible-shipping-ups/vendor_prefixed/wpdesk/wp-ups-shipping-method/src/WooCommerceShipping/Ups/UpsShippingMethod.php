<?php

/**
 * Ups Shipping Method.
 *
 * @package WPDesk\WooCommerceShipping\Ups
 */
namespace UpsFreeVendor\WPDesk\WooCommerceShipping\Ups;

use UpsFreeVendor\WPDesk\UpsShippingService\UpsServices;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsShippingService;
use UpsFreeVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax;
use UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingBuilder\AddressProvider;
use UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingBuilder\CustomOriginAddressSender;
use UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceAddressSender;
use UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingMethod;
/**
 * UPS Shipping Method.
 */
class UpsShippingMethod extends \UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingMethod implements \UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasFreeShipping, \UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasCustomOrigin
{
    /**
     * Supports.
     *
     * @var array
     */
    public $supports = array('settings', 'shipping-zones', 'instance-settings');
    /**
     * @var FieldApiStatusAjax
     */
    protected static $api_status_ajax_handler;
    /**
     * Set api status field AJAX handler.
     *
     * @param FieldApiStatusAjax $api_status_ajax_handler .
     */
    public static function set_api_status_ajax_handler(\UpsFreeVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax $api_status_ajax_handler)
    {
        static::$api_status_ajax_handler = $api_status_ajax_handler;
    }
    /**
     * Prepare description.
     * Description depends on current page.
     *
     * @return string
     */
    private function prepare_description()
    {
        if ('pl_PL' === \get_locale()) {
            $docs_link = 'https://www.wpdesk.pl/docs/ups-woocommerce-docs/';
        } else {
            $docs_link = 'https://docs.flexibleshipping.com/category/122-ups/';
        }
        $docs_link .= '?utm_source=ups-settings&utm_medium=link&utm_campaign=settings-docs-link';
        return \sprintf(
            // Translators: docs URL.
            \__('Dynamically calculated UPS live rates based on the established UPS API connection. %1$sLearn more →%2$s', 'flexible-shipping-ups'),
            '<a target="_blank" href="' . $docs_link . '">',
            '</a>'
        );
    }
    /**
     * Init method.
     */
    public function init()
    {
        parent::init();
        $this->method_description = $this->prepare_description();
    }
    /**
     * Init form fields.
     */
    public function build_form_fields()
    {
        $ups_settings_definition = new \UpsFreeVendor\WPDesk\WooCommerceShipping\Ups\UpsSettingsDefinitionWooCommerce($this->form_fields);
        $this->form_fields = $ups_settings_definition->get_form_fields();
        $this->instance_form_fields = $ups_settings_definition->get_instance_form_fields();
    }
    /**
     * Create meta data builder.
     *
     * @return UpsMetaDataBuilder
     */
    protected function create_metadata_builder()
    {
        return new \UpsFreeVendor\WPDesk\WooCommerceShipping\Ups\UpsMetaDataBuilder($this);
    }
    /**
     * Prepare settings fields for display.
     */
    private function prepare_settings_fields_for_display()
    {
        $this->instance_form_fields[\UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition::SERVICES]['options'] = \UpsFreeVendor\WPDesk\UpsShippingService\UpsServices::get_services_for_country($this->get_origin_country_code());
    }
    /**
     * Render shipping method settings.
     */
    public function admin_options()
    {
        $this->prepare_settings_fields_for_display();
        parent::admin_options();
        include __DIR__ . '/view/shipping-method-script.php';
    }
    /**
     * Is custom origin?
     *
     * @return bool
     */
    public function is_custom_origin()
    {
        return 'yes' === $this->get_option(\UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition::CUSTOM_ORIGIN, 'no');
    }
    /**
     * Get origin country code.
     *
     * @return string
     */
    public function get_origin_country_code()
    {
        $origin_country_code = '';
        if ($this->is_custom_origin()) {
            $country_state_code = \explode(':', $this->get_option(\UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition::ORIGIN_COUNTRY, ''));
            $origin_country_code = $country_state_code[0];
        } else {
            $woocommerce_default_country = \explode(':', \get_option('woocommerce_default_country', ''));
            if (!empty($woocommerce_default_country[0])) {
                $origin_country_code = $woocommerce_default_country[0];
            }
        }
        return $origin_country_code;
    }
    /**
     * Get enabled services.
     *
     * @return array
     */
    public function get_enabled_services()
    {
        $enabled_services = $this->get_available_services();
        foreach ($enabled_services as $service_code => $enabled_service) {
            if (!$enabled_service['enabled']) {
                unset($enabled_services[$service_code]);
            }
        }
        return $enabled_services;
    }
    /**
     * Get available UPS services.
     *
     * @param bool $get_current_services Get current services.
     *
     * @return array
     */
    private function get_available_services($get_current_services = \true)
    {
        $country_code = '';
        if ($this->is_custom_origin()) {
            $country_codes = \explode(':', $this->get_option(\UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition::ORIGIN_COUNTRY, ''));
            $country_code = $country_codes[0];
        } else {
            $woocommerce_default_country = \explode(':', \get_option('woocommerce_default_country', ''));
            if (!empty($woocommerce_default_country[0])) {
                $country_code = $woocommerce_default_country[0];
            }
        }
        $services_available = \UpsFreeVendor\WPDesk\UpsShippingService\UpsServices::get_services_for_country($country_code);
        $services = array();
        if ($get_current_services) {
            $current_services = $this->get_instance_option(\UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition::SERVICES, array());
            foreach ($current_services as $service_code => $service) {
                $services[$service_code] = $service;
            }
        }
        foreach ($services_available as $service_code => $service_name) {
            if (empty($services[$service_code])) {
                $services[$service_code] = array('name' => $service_name, 'enabled' => \true);
            }
        }
        return $services;
    }
    /**
     * Create sender address.
     *
     * @return AddressProvider
     */
    public function create_sender_address()
    {
        if ($this->is_custom_origin()) {
            $origin_country = \explode(':', $this->get_option(\UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition::ORIGIN_COUNTRY, ''));
            return new \UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingBuilder\CustomOriginAddressSender($this->get_option(\UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition::ORIGIN_ADDRESS, ''), '', $this->get_option(\UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition::ORIGIN_CITY, ''), $this->get_option(\UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition::ORIGIN_POSTCODE, ''), isset($origin_country[0]) ? $origin_country[0] : '', isset($origin_country[1]) ? $origin_country[1] : '');
        }
        return new \UpsFreeVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceAddressSender();
    }
}
