<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    woocommerce_refund_and_exchange_lite
 * @subpackage woocommerce_refund_and_exchange_lite/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    woocommerce_refund_and_exchange_lite
 * @subpackage woocommerce_refund_and_exchange_lite/includes
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Woocommerce_Refund_And_Exchange_Lite {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woocommerce_Refund_And_Exchange_Lite_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $woocommerce_refund_and_exchange_lite    The string used to uniquely identify this plugin.
	 */
	protected $woocommerce_refund_and_exchange_lite;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->woocommerce_refund_and_exchange_lite = 'woocommerce_refund_and_exchange_lite';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->init();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woocommerce_Refund_And_Exchange_Lite_Loader. Orchestrates the hooks of the plugin.
	 * - Woocommerce_Refund_And_Exchange_Lite_I18n. Defines internationalization functionality.
	 * - Woocommerce_Refund_And_Exchange_Lite_Admin. Defines all hooks for the admin area.
	 * - Woocommerce_Refund_And_Exchange_Lite_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		// The class responsible for orchestrating the actions and filters of the core plugin.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-refund-and-exchange-lite-loader.php';

		// The class responsible for defining internationalization functionality of the plugin.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-refund-and-exchange-lite-i18n.php';

		// The class responsible for defining all actions that occur in the admin area.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-refund-and-exchange-lite-admin.php';

		// The class responsible for defining all actions that occur in the public-facing side of the site.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-refund-and-exchange-lite-public.php';

		$this->loader = new Woocommerce_Refund_And_Exchange_Lite_Loader();

		// The class responsible for defining all actions that occur in the onboarding the site data in the admin side of the site.
		! class_exists( 'Makewebbetter_Onboarding_Helper' ) && require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-makewebbetter-onboarding-helper.php';

		if ( ! defined( 'ONBOARD_PLUGIN_NAME' ) && ( ! empty( $_GET['tab'] ) && 'ced_rnx_setting' === $_GET['tab'] ) ) {
			define( 'ONBOARD_PLUGIN_NAME', 'Return Refund and Exchange for Woocommerce' );
		}

		if ( class_exists( 'Makewebbetter_Onboarding_Helper' ) ) {
			$this->onboard = new Makewebbetter_Onboarding_Helper();
		}

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woocommerce_Refund_And_Exchange_Lite_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Woocommerce_Refund_And_Exchange_Lite_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Add woocommerce custom emails
	 *
	 * @since    1.0.0
	 */
	public function init() {
		add_filter( 'woocommerce_email_classes', array( $this, 'add_mwb_wrma_woocommerce_emails' ) );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Woocommerce_Refund_And_Exchange_Lite_Admin( $this->get_woocommerce_refund_and_exchange_lite(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menus' );
		$this->loader->add_action( 'init', $plugin_admin, 'ced_rnx_register_custom_order_status' );
		$this->loader->add_filter( 'wc_order_statuses', $plugin_admin, 'ced_rnx_add_custom_order_status' );
		$this->loader->add_action( 'wp_ajax_ced_return_req_approve', $plugin_admin, 'ced_rnx_return_req_approve_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_ced_return_req_approve', $plugin_admin, 'ced_rnx_return_req_approve_callback' );
		$this->loader->add_action( 'wp_ajax_ced_return_req_cancel', $plugin_admin, 'ced_rnx_return_req_cancel_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_ced_return_req_cancel', $plugin_admin, 'ced_rnx_return_req_cancel_callback' );
		$this->loader->add_action( 'wp_ajax_ced_rnx_manage_stock', $plugin_admin, 'ced_rnx_manage_stock' );
		$this->loader->add_action( 'wp_ajax_nopriv_ced_rnx_manage_stock', $plugin_admin, 'ced_rnx_manage_stock' );
		$this->loader->add_action( 'woocommerce_refund_created', $plugin_admin, 'ced_rnx_action_woocommerce_order_refunded', 10, 2 );
		$this->loader->add_action( 'wp_ajax_mwb_wrma_order_messages_save', $plugin_admin, 'mwb_wrma_order_messages_save' );
		// Add your screen.
		$this->loader->add_filter( 'mwb_helper_valid_frontend_screens', $plugin_admin, 'add_mwb_frontend_screens' );
		// Add Deactivation screen.
		$this->loader->add_filter( 'mwb_deactivation_supported_slug', $plugin_admin, 'add_mwb_deactivation_screens' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Woocommerce_Refund_And_Exchange_Lite_Public( $this->get_woocommerce_refund_and_exchange_lite(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'template_include', $plugin_public, 'ced_rnx_product_return_template' );
		$this->loader->add_filter( 'woocommerce_my_account_my_orders_actions', $plugin_public, 'ced_rnx_refund_exchange_button', 10, 2 );
		$this->loader->add_action( 'wp_ajax_ced_rnx_return_upload_files', $plugin_public, 'ced_rnx_order_return_attach_files' );
		$this->loader->add_action( 'wp_ajax_nopriv_ced_rnx_return_upload_files', $plugin_public, 'ced_rnx_order_return_attach_files' );
		$this->loader->add_action( 'wp_ajax_ced_rnx_return_product_info', $plugin_public, 'ced_rnx_return_product_info_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_ced_rnx_return_product_info', $plugin_public, 'ced_rnx_return_product_info_callback' );
		$this->loader->add_action( 'woocommerce_order_details_after_order_table', $plugin_public, 'ced_rnx_order_return_button' );

	}

	/**
	 * Add the email classes.
	 *
	 * @param array $email_classes email classes.
	 */
	public function add_mwb_wrma_woocommerce_emails( $email_classes ) {

		// include our custom email class.
		require_once MWB_REFUND_N_EXCHANGE_LITE_DIRPATH . 'emails/class-wc-rma-messages-email.php';

		// add the email class to the list of email classes that WooCommerce loads.
		$email_classes['wc_rma_messages_email'] = new WC_Rma_Messages_Email();
		return $email_classes;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_woocommerce_refund_and_exchange_lite() {
		return $this->woocommerce_refund_and_exchange_lite;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woocommerce_Refund_And_Exchange_Lite_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
