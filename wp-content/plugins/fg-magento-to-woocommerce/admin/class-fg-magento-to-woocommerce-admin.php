<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/fg-magento-to-woocommerce/
 * @since      1.0.0
 *
 * @package    FG_Magento_to_WooCommerce
 * @subpackage FG_Magento_to_WooCommerce/admin
 */

if ( !class_exists('FG_Magento_to_WooCommerce_Admin', false) ) {

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * @package    FG_Magento_to_WooCommerce
	 * @subpackage FG_Magento_to_WooCommerce/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Magento_to_WooCommerce_Admin extends WP_Importer {

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;						// Plugin version
		private $importer = 'fgm2wc';			// URL parameter

		public $magento_version;
		public $plugin_options;					// Plug-in options
		public $media_path;						// Media path
		public $default_language = 0;			// Default language ID
		public $progressbar;
		public $imported_categories = array();	// Imported product categories
		public $chunks_size = 10;
		public $imported_products = array();	// Imported products
		public $imported_products_count = 0;	// Number of imported products
		public $media_count = 0;				// Number of imported medias
		public $product_types = array();		// WooCommerce product types
		public $product_visibilities = array();	// WooCommerce product visibilities
		public $global_tax_rate = 0;
		public $attribute_types = array();
		public $website_id = 0;
		public $store_id = 0;
		public $product_type_id = 0;
		public $category_type_id = 0;
		public $customer_type_id = 0;
		public $customer_address_type_id = 0;
		public $global_manage_stock = false;
		
		protected $faq_url;						// URL of the FAQ page
		protected $post_type = 'post';			// post or page
		protected $notices = array();			// Error or success messages
		
		private $log_file;
		private $log_file_url;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param    string    $plugin_name       The name of this plugin.
		 * @param    string    $version           The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->version = $version;
			$this->faq_url = 'https://wordpress.org/plugins/fg-magento-to-woocommerce/faq/';
			$upload_dir = wp_upload_dir();
			$this->log_file = $upload_dir['basedir'] . '/' . $this->plugin_name . '.logs';
			$this->log_file_url = $upload_dir['baseurl'] . '/' . $this->plugin_name . '.logs';
			// Replace the protocol if the WordPress address is wrong in the WordPress General settings
			if ( is_ssl() ) {
				$this->log_file_url = preg_replace('/^https?/', 'https', $this->log_file_url);
			}

			// Progress bar
			$this->progressbar = new FG_Magento_to_Woocommerce_ProgressBar($this);

		}

		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since     1.0.0
		 * @return    string    The name of the plugin.
		 */
		public function get_plugin_name() {
			return $this->plugin_name;
		}

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fg-magento-to-woocommerce-admin.css', array(), $this->version, 'all' );

		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fg-magento-to-woocommerce-admin.js', array( 'jquery', 'jquery-ui-progressbar' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'objectL10n', array(
				'delete_imported_data_confirmation_message' => __( 'All new imported data will be deleted from WordPress.', 'fg-magento-to-woocommerce' ),
				'delete_all_confirmation_message' => __( 'All content will be deleted from WordPress.', 'fg-magento-to-woocommerce' ),
				'delete_no_answer_message' => __( 'Please select a remove option.', 'fg-magento-to-woocommerce' ),
				'import_completed' => __( 'IMPORT COMPLETED', 'fg-magento-to-woocommerce' ),
				'content_removed_from_wordpress' => __( 'Content removed from WordPress', 'fg-magento-to-woocommerce' ),
				'settings_saved' => __( 'Settings saved', 'fg-magento-to-woocommerce' ),
				'importing' => __( 'Importing…', 'fg-magento-to-woocommerce' ),
				'import_stopped_by_user' => __( 'IMPORT STOPPED BY USER', 'fg-magento-to-woocommerce' ),
			) );
			wp_localize_script( $this->plugin_name, 'objectPlugin', array(
				'log_file_url' => $this->log_file_url,
				'progress_url' => $this->progressbar->get_url(),
			));

		}

		/**
		 * Initialize the plugin
		 */
		public function init() {
			register_importer($this->importer, __('Magento', 'fg-magento-to-woocommerce'), __('Import Magento e-commerce solution to WooCommerce', 'fg-magento-to-woocommerce'), array($this, 'importer'));
		}

		/**
		 * Display the stored notices
		 * 
		 * @since 2.0.0
		 */
		public function display_notices() {
			foreach ( $this->notices as $notice ) {
				echo '<div class="' . $notice['level'] . '"><p>[' . $this->plugin_name . '] ' . $notice['message'] . "</p></div>\n";
			}
		}
		
		/**
		 * Write a message in the log file
		 * 
		 * @since 2.0.0
		 * 
		 * @param string $message
		 */
		public function log($message) {
			file_put_contents($this->log_file, "$message\n", FILE_APPEND);
		}
		
		/**
		 * Store an admin notice
		 */
		public function display_admin_notice( $message )	{
			$this->notices[] = array('level' => 'updated', 'message' => $message);
			error_log('[INFO] [' . $this->plugin_name . '] ' . $message);
			$this->log($message);
			if ( defined('WP_CLI') && WP_CLI ) {
				WP_CLI::log($message);
			}
		}

		/**
		 * Store an admin error
		 */
		public function display_admin_error( $message )	{
			$this->notices[] = array('level' => 'error', 'message' => $message);
			error_log('[ERROR] [' . $this->plugin_name . '] ' . $message);
			$this->log('[ERROR] ' . $message);
			if ( defined('WP_CLI') && WP_CLI ) {
				WP_CLI::error($message, false);
			}
		}

		/**
		 * Store an admin warning
		 */
		public function display_admin_warning( $message )	{
			$this->notices[] = array('level' => 'error', 'message' => $message);
			error_log('[WARNING] [' . $this->plugin_name . '] ' . $message);
			$this->log('[WARNING] ' . $message);
			if ( defined('WP_CLI') && WP_CLI ) {
				WP_CLI::warning($message);
			}
		}

		/**
		 * Run the importer
		 *
		 * @since    2.0.0
		 */
		public function importer() {
			$feasible_actions = array(
				'empty',
				'save',
				'test_database',
				'import',
			);
			$action = '';
			foreach ( $feasible_actions as $potential_action ) {
				if ( isset($_POST[$potential_action]) ) {
					$action = $potential_action;
					break;
				}
			}
			$this->set_local_timezone();
			$this->dispatch($action);
			$this->display_admin_page(); // Display the admin page
		}
		
		/**
		 * Import triggered by AJAX
		 *
		 * @since    2.0.0
		 */
		public function ajax_importer() {
			$current_user = wp_get_current_user();
			if ( !empty($current_user) && $current_user->has_cap('import') ) {
				$action = filter_input(INPUT_POST, 'plugin_action', FILTER_SANITIZE_STRING);

				if ( $action == 'update_wordpress_info') {
					// Update the WordPress database info
					echo $this->get_database_info();

				} else {
					ini_set('display_errors', true); // Display the errors that may happen (ex: Allowed memory size exhausted)

					// Empty the log file if we empty the WordPress content
					if ( ($action == 'empty') || (($action == 'import') && filter_input(INPUT_POST, 'automatic_empty', FILTER_VALIDATE_BOOLEAN)) ) {
						$this->empty_log_file();
					}

					$this->set_local_timezone();
					$time_start = date('Y-m-d H:i:s');
					$this->display_admin_notice("=== START $action $time_start ===");
					$result = $this->dispatch($action);
					if ( !empty($result) ) {
						echo json_encode($result); // Send the result to the AJAX caller
					}
					$time_end = date('Y-m-d H:i:s');
					$this->display_admin_notice("=== END $action $time_end ===\n");
				}
			}
			wp_die();
		}
		
		/**
		 * Empty the log file
		 * 
		 * @since 3.12.0
		 */
		public function empty_log_file() {
			file_put_contents($this->log_file, '');
		}
		
		/**
		 * Set the local timezone
		 * 
		 * @since 3.7.0
		 */
		public function set_local_timezone() {
			// Set the time zone
			$timezone = get_option('timezone_string');
			if ( !empty($timezone) ) {
				date_default_timezone_set($timezone);
			}
		}
		
		/**
		 * Dispatch the actions
		 * 
		 * @param string $action Action
		 * @return object Result to return to the caller
		 */
		public function dispatch($action) {
			$timeout = defined('IMPORT_TIMEOUT')? IMPORT_TIMEOUT : 7200; // 2 hours
			set_time_limit($timeout);

			// Suspend the cache during the migration to avoid exhausted memory problem
			wp_suspend_cache_addition(true);
			wp_suspend_cache_invalidation(true);

			// Default values
			$this->plugin_options = array(
				'automatic_empty'				=> 0,
				'url'							=> null,
				'hostname'						=> 'localhost',
				'port'							=> 3306,
				'database'						=> null,
				'username'						=> 'root',
				'password'						=> '',
				'prefix'						=> '',
				'skip_media'					=> 0,
				'first_image'					=> 'as_is_and_featured',
				'skip_thumbnails'				=> 0,
				'import_external'				=> 0,
				'import_duplicates'				=> 0,
				'force_media_import'			=> 0,
				'first_image_not_in_gallery'	=> false,
				'timeout'						=> 20,
				'price'							=> 'without_tax',
				'sale_price'					=> 'special',
				'stock_management'				=> true,
				'import_as_pages'				=> 0,
				'logger_autorefresh'			=> 1,
			);
			$options = get_option('fgm2wc_options');
			if ( is_array($options) ) {
				$this->plugin_options = array_merge($this->plugin_options, $options);
			}
			do_action('fgm2wc_post_get_plugin_options');

			// Check if the upload directory is writable
			$upload_dir = wp_upload_dir();
			if ( !is_writable($upload_dir['basedir']) ) {
				$this->display_admin_error(__('The wp-content directory must be writable.', 'fg-magento-to-woocommerce'));
			}

			// Requires at least WordPress 4.4
			if ( version_compare(get_bloginfo('version'), '4.4', '<') ) {
				$this->display_admin_error(sprintf(__('WordPress 4.4+ is required. Please <a href="%s">update WordPress</a>.', 'fg-magento-to-woocommerce'), admin_url('update-core.php')));
			}
			
			elseif ( !empty($action) ) {
				switch($action) {
					
					// Delete content
					case 'empty':
						if ( defined('WP_CLI') || check_admin_referer( 'empty', 'fgm2wc_nonce' ) ) { // Security check
							if ($this->empty_database($_POST['empty_action'])) { // Empty WP database
								$this->display_admin_notice(__('WordPress content removed', 'fg-magento-to-woocommerce'));
							} else {
								$this->display_admin_error(__('Couldn\'t remove content', 'fg-magento-to-woocommerce'));
							}
							wp_cache_flush();
						}
						break;
					
					// Save database options
					case 'save':
						if ( check_admin_referer( 'parameters_form', 'fgm2wc_nonce' ) ) { // Security check
							$this->save_plugin_options();
							$this->display_admin_notice(__('Settings saved', 'fg-magento-to-woocommerce'));
						}
						break;
					
					// Test the database connection
					case 'test_database':
						if ( defined('WP_CLI') || check_admin_referer( 'parameters_form', 'fgm2wc_nonce' ) ) { // Security check
							if ( !defined('WP_CLI') ) {
								// Save database options
								$this->save_plugin_options();
							}

							if ( $this->test_database_connection() ) {
								$result = array('status' => 'OK', 'message' => __('Connection successful', 'fg-magento-to-woocommerce'));
							} else {
								return array('status' => 'Error', 'message' => __('Connection failed', 'fg-magento-to-woocommerce') . '<br />' . __('See the errors in the log below', 'fg-magento-to-woocommerce'));
							}
							$result = apply_filters('fgm2wc_post_test_database_connection_click', $result);
							return $result;
						}
						break;
					
					// Run the import
					case 'import':
						if ( defined('WP_CLI') || defined('DOING_CRON') || check_admin_referer( 'parameters_form', 'fgm2wc_nonce' ) ) { // Security check
							if ( !defined('DOING_CRON') && !defined('WP_CLI') ) {
								// Save database options
								$this->save_plugin_options();
							} else {
								if ( defined('DOING_CRON') ) {
									// CRON triggered
									$this->plugin_options['automatic_empty'] = 0; // Don't delete the existing data when triggered by cron
								}
							}

							if ( $this->test_database_connection() ) {
								// Automatic empty
								if ( $this->plugin_options['automatic_empty'] ) {
									if ($this->empty_database('all')) {
										$this->display_admin_notice(__('WordPress content removed', 'fg-magento-to-woocommerce'));
									} else {
										$this->display_admin_error(__('Couldn\'t remove content', 'fg-magento-to-woocommerce'));
									}
									wp_cache_flush();
								}

								// Import content
								$this->import();
							}
						}
						break;
					
					// Stop the import
					case 'stop_import':
						if ( check_admin_referer( 'parameters_form', 'fgm2wc_nonce' ) ) { // Security check
							$this->stop_import();
						}
						break;
					
					default:
						// Do other actions
						do_action('fgm2wc_dispatch', $action);
				}
			}
		}

		/**
		 * Display the admin page
		 * 
		 */
		private function display_admin_page() {
			$data = $this->plugin_options;

			$data['title'] = __('Import Magento', 'fg-magento-to-woocommerce');
			$data['description'] = __('This plugin will import product categories, products, images and CMS from Magento to WooCommerce.<br />Compatible with Magento versions 1.3 to 2.3.', 'fg-magento-to-woocommerce');
			$data['description'] .= "<br />\n" . sprintf(__('For any issue, please read the <a href="%s" target="_blank">FAQ</a> first.', 'fg-magento-to-woocommerce'), $this->faq_url);
			$data['database_info'] = $this->get_database_info();
			$data['importer'] = $this->importer;
			$data['tab'] = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING);
			
			// Hook for modifying the admin page
			$data = apply_filters('fgm2wc_pre_display_admin_page', $data);

			// Load the CSS and Javascript
			$this->enqueue_styles();
			$this->enqueue_scripts();
			
			include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/admin-display.php';

			// Hook for doing other actions after displaying the admin page
			do_action('fgm2wc_post_display_admin_page');
		}

		/**
		 * Get the WP options name
		 * 
		 * @since 3.0.0
		 * 
		 * @param array $option_names Option names
		 * @return array Option names
		 */
		public function get_option_names($option_names) {
			$option_names[] = 'fgm2wc_options';
			return $option_names;
		}
		
		/**
		 * Get the WordPress database info
		 * 
		 * @since 2.0.0
		 * 
		 * @return string Database info
		 */
		private function get_database_info() {
			$posts_count = $this->count_posts('post');
			$pages_count = $this->count_posts('page');
			$products_count = $this->count_posts('product');
			$media_count = $this->count_posts('attachment');
			$product_cat_count = wp_count_terms('product_cat', array('hide_empty' => false));
			if ( is_wp_error($product_cat_count) ) {
				$product_cat_count = 0;
			}

			$database_info =
				sprintf(_n('%d post', '%d posts', $posts_count, 'fg-magento-to-woocommerce'), $posts_count) . "<br />" .
				sprintf(_n('%d page', '%d pages', $pages_count, 'fg-magento-to-woocommerce'), $pages_count) . "<br />" .
				sprintf(_n('%d product category', '%d product categories', $product_cat_count, 'fg-magento-to-woocommerce'), $product_cat_count) . "<br />" .
				sprintf(_n('%d product', '%d products', $products_count, 'fg-magento-to-woocommerce'), $products_count) . "<br />" .
				sprintf(_n('%d media', '%d medias', $media_count, 'fg-magento-to-woocommerce'), $media_count) . "<br />" ;
			$database_info = apply_filters('fgm2wc_get_database_info', $database_info);
			return $database_info;
		}
		
		/**
		 * Count the number of posts for a post type
		 * @param string $post_type
		 */
		public function count_posts($post_type) {
			$count = 0;
			$excluded_status = array('trash', 'auto-draft');
			$tab_count = wp_count_posts($post_type);
			foreach ( $tab_count as $key => $value ) {
				if ( !in_array($key, $excluded_status) ) {
					$count += $value;
				}
			}
			return $count;
		}

		/**
		 * Add an help tab
		 * 
		 */
		public function add_help_tab() {
			$screen = get_current_screen();
			$screen->add_help_tab(array(
				'id'	=> 'fgm2wc_help_instructions',
				'title'	=> __('Instructions'),
				'content'	=> '',
				'callback' => array($this, 'help_instructions'),
			));
			$screen->add_help_tab(array(
				'id'	=> 'fgm2wc_help_options',
				'title'	=> __('Options'),
				'content'	=> '',
				'callback' => array($this, 'help_options'),
			));
			$screen->set_help_sidebar('<a href="' . $this->faq_url . '" target="_blank">' . __('FAQ', 'fg-magento-to-woocommerce') . '</a>');
		}

		/**
		 * Instructions help screen
		 * 
		 * @return string Help content
		 */
		public function help_instructions() {
			include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/help-instructions.tpl.php';
		}

		/**
		 * Options help screen
		 * 
		 * @return string Help content
		 */
		public function help_options() {
			include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/help-options.tpl.php';
		}

		/**
		 * Open the connection on Magento database
		 *
		 * return boolean Connection successful or not
		 */
		protected function magento_connect() {
			global $magento_db;

			if ( !class_exists('PDO') ) {
				$this->display_admin_error(__('PDO is required. Please enable it.', 'fg-magento-to-woocommerce'));
				return false;
			}
			try {
				$magento_db = new PDO('mysql:host=' . $this->plugin_options['hostname'] . ';port=' . $this->plugin_options['port'] . ';dbname=' . $this->plugin_options['database'], $this->plugin_options['username'], $this->plugin_options['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
				if ( function_exists('wp_get_environment_type') && (wp_get_environment_type() == 'development') ) {
					$magento_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Display SQL errors
				}
			} catch ( PDOException $e ) {
				$this->display_admin_error(__('Couldn\'t connect to the Magento database. Please check your parameters. And be sure the WordPress server can access the Magento database.', 'fg-magento-to-woocommerce') . "<br />\n" . $e->getMessage() . "<br />\n" . sprintf(__('Please read the <a href="%s" target="_blank">FAQ for the solution</a>.', 'fg-magento-to-woocommerce'), $this->faq_url));
				return false;
			}
			$this->magento_version = $this->magento_version();
			$this->website_id = $this->get_default_website_id();
			return true;
		}

		/**
		 * Guess the Magento version
		 *
		 * @return string Magento version
		 */
		private function magento_version() {
			$version = '0.0';
			if ( $this->table_exists('core_resource') ) {
				// Magento 1
				$core_resource = $this->get_core_resource();
				$version = isset($core_resource['catalog_setup'])? $core_resource['catalog_setup'] : '1.0';
			} else {
				// Magento 2+
				$setup_module = $this->get_setup_module('Magento_Catalog');
				$version = isset($setup_module['schema_version'])? $setup_module['schema_version'] : '2.0';
			}
			return $version;
		}
		
		/**
		 * Get the Magento core_resource data
		 * 
		 * @since 1.12.1
		 * 
		 * @return array Core resource
		 */
		private function get_core_resource() {
			$core_resource = array();
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT c.code, c.version
				FROM ${prefix}core_resource c
			";
			$result = $this->magento_query($sql);
			foreach ( $result as $row ) {
				$core_resource[$row['code']] = $row['version'];
			}
			return $core_resource;
		}

		/**
		 * Get the Magento core_config data
		 * 
		 * @since 2.92.0
		 * 
		 * @param string $path Path
		 * @return string Core config
		 */
		public function get_core_config($path) {
			$core_config = '';
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT c.value
				FROM ${prefix}core_config_data c
				WHERE c.path = '$path'
			";
			$result = $this->magento_query($sql);
			if ( count($result) > 0 ) {
				$core_config = $result[0]['value'];
			}
			return $core_config;
		}

		/**
		 * Get the Magento setup_module data
		 * 
		 * @since 2.34.0
		 * 
		 * @param string $module Module
		 * @return array Setup module
		 */
		private function get_setup_module($module) {
			$setup_module = array();
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT s.schema_version, s.data_version
				FROM ${prefix}setup_module s
				WHERE s.module = '$module'
			";
			$result = $this->magento_query($sql);
			if ( isset($result[0]) ) {
				$setup_module = $result[0];
			}
			return $setup_module;
		}

		/**
		 * Get the Magento default website ID
		 *
		 * @since 2.16.0
		 * 
		 * @return int Web site ID
		 */
		protected function get_default_website_id() {
			$website_id = 0;
			$prefix = $this->plugin_options['prefix'];
			$website_table = version_compare($this->magento_version, '2', '<')? 'core_website' : 'store_website';
			$sql = "
				SELECT w.website_id
				FROM ${prefix}${website_table} w
				WHERE w.is_default = 1
			";
			$result = $this->magento_query($sql);
			if ( isset($result[0]['website_id']) ) {
				$website_id = $result[0]['website_id'];
			}
			return $website_id;
		}
		
		/**
		 * Execute a SQL query on the Magento database
		 * 
		 * @param string $sql SQL query
		 * @param bool $display_error Display the error?
		 * @return array Query result
		 */
		public function magento_query($sql, $display_error = true) {
			global $magento_db;
			$result = array();

			try {
				$query = $magento_db->query($sql, PDO::FETCH_ASSOC);
				if ( is_object($query) ) {
					foreach ( $query as $row ) {
						$result[] = $row;
					}
				}

			} catch ( PDOException $e ) {
				if ( $display_error ) {
					$this->display_admin_error(__('Error:', 'fg-magento-to-woocommerce') . $e->getMessage());
				}
			}
			return $result;
		}

		/**
		 * Delete all posts, medias and categories from the database
		 *
		 * @param string $action	imported = removes only new imported data
		 * 							all = removes all
		 * @return boolean
		 */
		private function empty_database($action) {
			global $wpdb;
			$result = true;

			$wpdb->show_errors();

			// Hook for doing other actions before emptying the database
			do_action('fgm2wc_pre_empty_database', $action);

			$sql_queries = array();

			if ( $action == 'all' ) {
				// Remove all content
				$sql_queries[] = "TRUNCATE $wpdb->commentmeta";
				$sql_queries[] = "TRUNCATE $wpdb->comments";
				$sql_queries[] = "TRUNCATE $wpdb->term_relationships";
				$sql_queries[] = "TRUNCATE $wpdb->termmeta";
				$sql_queries[] = "TRUNCATE $wpdb->postmeta";
				$sql_queries[] = "TRUNCATE $wpdb->posts";
				$sql_queries[] = <<<SQL
-- Delete Terms
DELETE FROM $wpdb->terms
WHERE term_id > 1 -- non-classe
SQL;
				$sql_queries[] = <<<SQL
-- Delete Terms taxonomies
DELETE FROM $wpdb->term_taxonomy
WHERE term_id > 1 -- non-classe
SQL;
				$sql_queries[] = "ALTER TABLE $wpdb->terms AUTO_INCREMENT = 2";
				$sql_queries[] = "ALTER TABLE $wpdb->term_taxonomy AUTO_INCREMENT = 2";
				
			} else {
				
				// (Re)create a temporary table with the IDs to delete
				$sql_queries[] = <<<SQL
DROP TEMPORARY TABLE IF EXISTS {$wpdb->prefix}fg_data_to_delete;
SQL;

				$sql_queries[] = <<<SQL
CREATE TEMPORARY TABLE IF NOT EXISTS {$wpdb->prefix}fg_data_to_delete (
`id` bigint(20) unsigned NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
SQL;
				
				// Insert the imported posts IDs in the temporary table
				$sql_queries[] = <<<SQL
INSERT IGNORE INTO {$wpdb->prefix}fg_data_to_delete (`id`)
SELECT post_id FROM $wpdb->postmeta
WHERE meta_key LIKE '_fgm2wc_%'
SQL;
				
				// Delete the imported posts and related data

				$sql_queries[] = <<<SQL
-- Delete Comments and Comment metas
DELETE c, cm
FROM $wpdb->comments c
LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = c.comment_ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE c.comment_post_ID = del.id;
SQL;

				$sql_queries[] = <<<SQL
-- Delete Term relashionships
DELETE tr
FROM $wpdb->term_relationships tr
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE tr.object_id = del.id;
SQL;

				$sql_queries[] = <<<SQL
-- Delete Posts Children and Post metas
DELETE p, pm
FROM $wpdb->posts p
LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE p.post_parent = del.id
AND p.post_type != 'attachment'; -- Don't remove the old medias attached to posts
SQL;

				$sql_queries[] = <<<SQL
-- Delete Posts and Post metas
DELETE p, pm
FROM $wpdb->posts p
LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE p.ID = del.id;
SQL;

				// Truncate the temporary table
				$sql_queries[] = <<<SQL
TRUNCATE {$wpdb->prefix}fg_data_to_delete;
SQL;
				
				// Insert the imported terms IDs in the temporary table
				$sql_queries[] = <<<SQL
INSERT IGNORE INTO {$wpdb->prefix}fg_data_to_delete (`id`)
SELECT term_id FROM $wpdb->termmeta
WHERE meta_key LIKE '_fgm2wc_%'
SQL;
				
				// Delete the imported terms and related data

				$sql_queries[] = <<<SQL
-- Delete Terms, Term taxonomies and Term metas
DELETE t, tt, tm
FROM $wpdb->termmeta tm
LEFT JOIN $wpdb->term_taxonomy tt ON tt.term_id = tm.term_id
LEFT JOIN $wpdb->terms t ON t.term_id = tm.term_id
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE tm.term_id = del.id;
SQL;

				// Truncate the temporary table
				$sql_queries[] = <<<SQL
TRUNCATE {$wpdb->prefix}fg_data_to_delete;
SQL;
				
				// Insert the imported comments IDs in the temporary table
				$sql_queries[] = <<<SQL
INSERT IGNORE INTO {$wpdb->prefix}fg_data_to_delete (`id`)
SELECT comment_id FROM $wpdb->commentmeta
WHERE meta_key LIKE '_fgm2wc_%'
SQL;
				
				// Delete the imported comments and related data
				$sql_queries[] = <<<SQL
-- Delete Comments and Comment metas
DELETE c, cm
FROM $wpdb->comments c
LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = c.comment_ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE c.comment_ID = del.id;
SQL;

			}

			// Delete WooCommerce transients
			$sql_queries[] = <<<SQL
-- Delete WooCommerce transients
DELETE o FROM $wpdb->options o
WHERE o.option_name LIKE '_transient_wc_%'
OR o.option_name LIKE '_transient_timeout_wc_%';
SQL;

			// Execute SQL queries
			if ( count($sql_queries) > 0 ) {
				foreach ( $sql_queries as $sql ) {
					$result &= $wpdb->query($sql);
				}
			}

			// Reset the Magento last imported IDs
			update_option('fgm2wc_last_magento_product_id', 0);
			update_option('fgm2wc_last_magento_cms_id', 0);
			delete_option('fgm2wc_last_update');

			// Hook for doing other actions after emptying the database
			do_action('fgm2wc_post_empty_database', $action);

			// Re-count categories and tags items
			$this->terms_count();

			// Clean the cache
			$this->clean_cache(array(), 'category');
			$this->clean_cache(array(), 'product_cat');
			delete_transient('wc_count_comments');

			$this->optimize_database();

			$this->progressbar->set_total_count(0);
			
			$wpdb->hide_errors();
			return ($result !== false);
		}

		/**
		 * Delete all woocommerce data
		 *
		 * @param string $action	imported = removes only new imported data
		 * 							all = removes all
		 */
		public function delete_woocommerce_data($action) {
			global $wpdb;
			global $wc_product_attributes;
			
			$wpdb->show_errors();
			
			$sql_queries = array();
			$sql_queries[] = <<<SQL
-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS=0;
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce attribute taxonomies
TRUNCATE {$wpdb->prefix}woocommerce_attribute_taxonomies
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce order items
TRUNCATE {$wpdb->prefix}woocommerce_order_items
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce order item metas
TRUNCATE {$wpdb->prefix}woocommerce_order_itemmeta
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce download logs
TRUNCATE {$wpdb->prefix}wc_download_log
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce downloadable product permissions
TRUNCATE {$wpdb->prefix}woocommerce_downloadable_product_permissions
SQL;

			// Delete the WooCommerce lookup and stats tables
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}wc_customer_lookup";
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}wc_order_coupon_lookup";
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}wc_order_product_lookup";
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}wc_order_tax_lookup";
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}wc_product_meta_lookup";
			$sql_queries[] = "TRUNCATE {$wpdb->prefix}wc_order_stats";
			
			$sql_queries[] = <<<SQL
-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;
SQL;

			// Execute SQL queries
			if ( count($sql_queries) > 0 ) {
				foreach ( $sql_queries as $sql ) {
					$wpdb->query($sql);
				}
			}
			
			if ( $action == 'all' ) {
				// Reset the WC pages flags
				$wc_pages = array('shop', 'cart', 'checkout', 'myaccount');
				foreach ( $wc_pages as $wc_page ) {
					update_option('woocommerce_' . $wc_page . '_page_id', 0);
				}
			}
			
			// Unset the default product category
			update_option('default_product_cat', 0);
			
			// Empty attribute taxonomies cache
			delete_transient('wc_attribute_taxonomies');
			$wc_product_attributes = array();
			$this->delete_var_prices_transient();
			
			// Delete the WooCommerce product category cache
			delete_option("product_cat_children");
			
			// Delete the default placeholder image
			delete_option('woocommerce_placeholder_image');
			
			$wpdb->hide_errors();
			
			$this->display_admin_notice(__('WooCommerce data deleted', 'fg-magento-to-woocommerce'));
			
			if ( $action == 'all' ) {
				// Recreate WooCommerce default data
				if ( class_exists('WC_Install') ) {
					WC_Install::create_pages();
					$this->display_admin_notice(__('WooCommerce default data created', 'fg-magento-to-woocommerce'));
				}
			}
		}
		
		/**
		 * Delete the wc_var_prices transient
		 * 
		 * @since 2.46.2
		 */
		public function delete_var_prices_transient() {
			$this->delete_transient('wc_var_prices_');
		}
		
		/**
		 * Delete the transient
		 * 
		 * @since 2.46.2
		 * 
		 * @param string $transient Transient
		 */
		public function delete_transient($transient) {
			global $wpdb;
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_$transient%') OR `option_name` LIKE ('_transient_timeout_$transient%')" );
		}
		
		/**
		 * Optimize the database
		 *
		 */
		protected function optimize_database() {
			global $wpdb;

			$sql = <<<SQL
OPTIMIZE TABLE 
`$wpdb->commentmeta` ,
`$wpdb->comments` ,
`$wpdb->options` ,
`$wpdb->postmeta` ,
`$wpdb->posts` ,
`$wpdb->terms` ,
`$wpdb->term_relationships` ,
`$wpdb->term_taxonomy`,
`$wpdb->termmeta`
SQL;
			$wpdb->query($sql);
		}

		/**
		 * Clean the cache
		 * 
		 */
		public function clean_cache($terms=array(), $taxonomy='category') {
			delete_option($taxonomy . '_children');
			clean_term_cache($terms, $taxonomy);
		}

		/**
		 * Test the database connection
		 * 
		 * @return boolean
		 */
		function test_database_connection() {
			global $magento_db;

			if ( $this->magento_connect() ) {
				try {
					$prefix = $this->plugin_options['prefix'];
					
					do_action('fgm2wc_pre_test_database_connection');
					
					// Test that the "catalog_product_entity" table exists
					$result = $magento_db->query("DESC ${prefix}catalog_product_entity");
					if ( !is_a($result, 'PDOStatement') ) {
						$errorInfo = $magento_db->errorInfo();
						throw new PDOException($errorInfo[2], $errorInfo[1]);
					}

					$this->display_admin_notice(__('Connected with success to the Magento database', 'fg-magento-to-woocommerce'));

					do_action('fgm2wc_post_test_database_connection');

					return true;

				} catch ( PDOException $e ) {
					$this->display_admin_error(__('Couldn\'t connect to the Magento database. Please check your parameters. And be sure the WordPress server can access the Magento database.', 'fg-magento-to-woocommerce') . "<br />\n" . $e->getMessage());
					return false;
				}
				$magento_db = null;
			}
			return false;
		}

		/**
		 * Test if the WooCommerce plugin is activated
		 *
		 * @return bool True if the WooCommerce plugin is activated
		 */
		public function test_woocommerce_activation() {
			if ( !class_exists('WooCommerce', false) ) {
				$this->display_admin_error(__('Error: the <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce plugin</a> must be installed and activated to import the products.', 'fg-magento-to-woocommerce'));
				return false;
			}
			return true;
		}

		/**
		 * Get some Magento information
		 *
		 */
		public function get_magento_info() {
			$message = __('Magento data found:', 'fg-magento-to-woocommerce') . "\n";

			// CMS pages
			$cms_count = $this->get_cms_count();
			$message .= sprintf(_n('%d CMS page', '%d CMS pages', $cms_count, 'fg-magento-to-woocommerce'), $cms_count) . "\n";

			// Product categories
			$cat_count = $this->get_all_product_categories_count();
			$message .= sprintf(_n('%d product category', '%d product categories', $cat_count, 'fg-magento-to-woocommerce'), $cat_count) . "\n";

			// Products
			$products_count = $this->get_products_count();
			$message .= sprintf(_n('%d product', '%d products', $products_count, 'fg-magento-to-woocommerce'), $products_count) . "\n";

			$message = apply_filters('fgm2wc_pre_display_magento_info', $message);

			$this->display_admin_notice($message);
		}

		/**
		 * Get the number of Magento categories
		 * 
		 * @return int Number of categories
		 */
		private function get_all_product_categories_count() {
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}catalog_category_entity c
				WHERE c.parent_id != 0 -- don't import the root category
			";
			$result = $this->magento_query($sql);
			$cat_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $cat_count;
		}

		/**
		 * Get the number of Magento products
		 * 
		 * @return int Number of products
		 */
		private function get_products_count() {
			$prefix = $this->plugin_options['prefix'];
			$website_criteria = '';
			if ( $this->website_id != 'all' ) {
				$website_criteria = "INNER JOIN ${prefix}catalog_product_website pw ON pw.product_id = p.entity_id AND pw.website_id = {$this->website_id}";
			}
			$sql = "
				SELECT COUNT(DISTINCT p.entity_id) AS nb
				FROM ${prefix}catalog_product_entity p
				INNER JOIN ${prefix}catalog_product_entity_int pei ON pei.entity_id = p.entity_id
				INNER JOIN ${prefix}eav_attribute a ON a.attribute_id = pei.attribute_id
				$website_criteria
				WHERE a.attribute_code = 'visibility'
				AND pei.value != 1 -- Different from 'Not visible individually'
			";
			$sql = apply_filters('fgm2wc_get_products_count_sql', $sql);
			$result = $this->magento_query($sql);
			$posts_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $posts_count;
		}

		/**
		 * Get the number of Magento CMS pages
		 * 
		 * @return int Number of pages
		 */
		private function get_cms_count() {
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(DISTINCT(a.page_id)) AS nb
				FROM ${prefix}cms_page a
				INNER JOIN ${prefix}cms_page_store s ON s.page_id = a.page_id AND s.store_id IN (0, {$this->store_id})
			";
			$sql = apply_filters('fgm2wc_get_posts_sql', $sql);
			$result = $this->magento_query($sql);
			$cms_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $cms_count;
		}
		
		/**
		 * Save the plugin options
		 *
		 */
		public function save_plugin_options() {
			$this->plugin_options = array_merge($this->plugin_options, $this->validate_form_info());
			update_option('fgm2wc_options', $this->plugin_options);

			// Hook for doing other actions after saving the options
			do_action('fgm2wc_post_save_plugin_options');
		}

		/**
		 * Validate POST info
		 *
		 * @return array Form parameters
		 */
		private function validate_form_info() {
			// Add http:// before the URL if it is missing
			$url = esc_url(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL));
			if ( !empty($url) && (preg_match('#^https?://#', $url) == 0) ) {
				$url = 'http://' . $url;
			}
			return array(
				'automatic_empty'				=> filter_input(INPUT_POST, 'automatic_empty', FILTER_VALIDATE_BOOLEAN),
				'url'							=> $url,
				'hostname'						=> filter_input(INPUT_POST, 'hostname', FILTER_SANITIZE_STRING),
				'port'							=> filter_input(INPUT_POST, 'port', FILTER_SANITIZE_NUMBER_INT),
				'database'						=> filter_input(INPUT_POST, 'database', FILTER_SANITIZE_STRING),
				'username'						=> filter_input(INPUT_POST, 'username'),
				'password'						=> filter_input(INPUT_POST, 'password'),
				'prefix'						=> filter_input(INPUT_POST, 'prefix', FILTER_SANITIZE_STRING),
				'skip_media'					=> filter_input(INPUT_POST, 'skip_media', FILTER_VALIDATE_BOOLEAN),
				'first_image'					=> filter_input(INPUT_POST, 'first_image', FILTER_SANITIZE_STRING),
				'skip_thumbnails'				=> filter_input(INPUT_POST, 'skip_thumbnails', FILTER_VALIDATE_BOOLEAN),
				'import_external'				=> filter_input(INPUT_POST, 'import_external', FILTER_VALIDATE_BOOLEAN),
				'import_duplicates'				=> filter_input(INPUT_POST, 'import_duplicates', FILTER_VALIDATE_BOOLEAN),
				'force_media_import'			=> filter_input(INPUT_POST, 'force_media_import', FILTER_VALIDATE_BOOLEAN),
				'first_image_not_in_gallery'	=> filter_input(INPUT_POST, 'first_image_not_in_gallery', FILTER_VALIDATE_BOOLEAN),
				'timeout'						=> filter_input(INPUT_POST, 'timeout', FILTER_SANITIZE_NUMBER_INT),
				'price'							=> filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING),
				'sale_price'					=> filter_input(INPUT_POST, 'sale_price', FILTER_SANITIZE_STRING),
				'stock_management'				=> filter_input(INPUT_POST, 'stock_management', FILTER_VALIDATE_BOOLEAN),
				'import_as_pages'				=> filter_input(INPUT_POST, 'import_as_pages', FILTER_VALIDATE_BOOLEAN),
				'logger_autorefresh'			=> filter_input(INPUT_POST, 'logger_autorefresh', FILTER_VALIDATE_BOOLEAN),
			);
		}

		/**
		 * Import
		 *
		 */
		private function import() {
			if ( $this->magento_connect() ) {

				$time_start = microtime(true);

				define('WP_IMPORTING', true);
				update_option('fgm2wc_stop_import', false, false); // Reset the stop import action
				
				// Check prerequesites before the import
				$do_import = apply_filters('fgm2wc_pre_import_check', true);
				if ( !$do_import) {
					return;
				}

				$total_elements_count = $this->get_total_elements_count();
				$this->progressbar->set_total_count($total_elements_count);
				
				$this->pre_import();
				
				// Hook for doing other actions before the import
				do_action('fgm2wc_pre_import');
				
				if ( !isset($this->premium_options['skip_cms']) || !$this->premium_options['skip_cms'] ) {
					$this->import_cms();
				}
				if ( !isset($this->premium_options['skip_products_categories']) || !$this->premium_options['skip_products_categories'] ) {
					$this->import_product_categories();
				}
				if ( !isset($this->premium_options['skip_products']) || !$this->premium_options['skip_products'] ) {
					$this->import_products();
					
					// Regenerate the WooCommerce product lookup tables
					if ( function_exists('wc_update_product_lookup_tables') ) {
						wc_update_product_lookup_tables();
					}
				}
				
				// Hook for doing other actions after the import
				do_action('fgm2wc_post_import');
				
				if ( !get_option('fgm2wc_last_update') ) {
					// Set the last update date if it was not already set
					update_option('fgm2wc_last_update', date('Y-m-d H:i:s'));
				}
				
				// Hook for other notices
				do_action('fgm2wc_import_notices');

				// Debug info
				if ( function_exists('wp_get_environment_type') && (wp_get_environment_type() == 'development') ) {
					$this->display_admin_notice(sprintf("Memory used: %s bytes<br />\n", number_format(memory_get_usage())));
					$time_end = microtime(true);
					$this->display_admin_notice(sprintf("Duration: %d sec<br />\n", $time_end - $time_start));
				}

				if ( $this->import_stopped() ) {
					
					// Import stopped by the user
					$this->display_admin_notice("IMPORT STOPPED BY USER");
					
				} else {
					// Import completed
					$this->display_admin_notice("IMPORT COMPLETED");
				}
				wp_cache_flush();
			}
		}
		
		/**
		 * Actions to do before the import/update
		 * 
		 * @since 2.99.1
		 */
		protected function pre_import() {
			kses_remove_filters(); // To solve the issue of links containing ":" in multisite mode
			
			global $wp_filter;
			unset($wp_filter['wp_insert_post']); // Remove the "wp_insert_post" that consumes a lot of CPU and memory
			
			$this->post_type = ($this->plugin_options['import_as_pages'] == 1) ? 'page' : 'post';
			
			$this->product_types = $this->create_woocommerce_product_types(); // (Re)create the WooCommerce product types
			$this->product_visibilities = $this->create_woocommerce_product_visibilities(); // (Re)create the WooCommerce product visibilities
			$this->attribute_types = $this->get_magento_attributes();
			$this->entity_type_codes = $this->get_magento_entity_type_codes();
			$this->set_entity_types($this->entity_type_codes);
			$this->default_backorders = $this->get_default_backorders();
			$this->global_tax_rate = $this->get_default_tax_rate();
			$this->set_media_path();
			$this->set_global_manage_stock();
		}

		/**
		 * Actions to do before the import
		 * 
		 * @param bool $import_doable Can we start the import?
		 * @return bool Can we start the import?
		 */
		public function pre_import_check($import_doable) {
			if ( $import_doable ) {
				if ( !$this->plugin_options['skip_media'] && empty($this->plugin_options['url']) ) {
					$this->display_admin_error(__('The URL field is required to import the media.', 'fg-magento-to-woocommerce'));
					$import_doable = false;
				}
			}
			return $import_doable;
		}

		/**
		 * Get the number of elements to import
		 * 
		 * @since 2.0.0
		 * 
		 * @return int Number of elements to import
		 */
		private function get_total_elements_count() {
			$count = 0;
			
			do_action('fgm2wc_pre_get_total_elements_count');
			
			// CMS articles
			if ( !isset($this->premium_options['skip_cms']) || !$this->premium_options['skip_cms'] ) {
				$count += $this->get_cms_count();
			}
			
			// Products categories
			if ( !isset($this->premium_options['skip_products_categories']) || !$this->premium_options['skip_products_categories'] ) {
				$count += $this->get_all_product_categories_count();
			}
			
			// Products
			if ( !isset($this->premium_options['skip_products']) || !$this->premium_options['skip_products'] ) {
				$count += $this->get_products_count();
			}
			
			$count = apply_filters('fgm2wc_get_total_elements_count', $count);
			
			return $count;
		}
		
		/**
		 * Create the WooCommerce product types
		 *
		 * @return array Product types
		 */
		protected function create_woocommerce_product_types() {
			return $this->create_unique_terms(
				array(
					'simple',
					'grouped',
					'variable',
					'external',
					'bundle',
				), 'product_type');
		}
		
		/**
		 * Create the WooCommerce visibilities
		 *
		 * @since 2.38.0
		 * 
		 * @return array Product visibilities
		 */
		protected function create_woocommerce_product_visibilities() {
			return $this->create_unique_terms(
				array(
					'exclude-from-search',
					'exclude-from-catalog',
					'featured',
					'outofstock',
					'rated-1',
					'rated-2',
					'rated-3',
					'rated-4',
					'rated-5',
				), 'product_visibility');
		}
		
		/**
		 * Create unique terms and get them
		 *
		 * @since 2.38.0
		 * 
		 * @param array $term_slugs Term slugs
		 * @param string $taxonomy Taxonomy
		 * @return array Terms
		 */
		private function create_unique_terms($term_slugs, $taxonomy) {
			$terms = array();
			foreach ( $term_slugs as $term_slug ) {
				$term = get_term_by('slug', $term_slug, $taxonomy);
				if ( !empty($term) ) {
					$terms[$term_slug] = $term->term_id;
				} else {
					$new_term = wp_insert_term($term_slug, $taxonomy);
					if ( !is_wp_error($new_term) ) {
						$terms[$term_slug] = $new_term['term_id'];
					}
				}
			}
			return $terms;
		}
		
		/**
		 * Import the Magento attributes (used for product categories and products)
		 * 
		 * @return array Attribute types
		 */
		protected function get_magento_attributes() {
			$attribute_types = array();
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT a.attribute_id, a.entity_type_id, a.attribute_code, a.backend_type
				FROM ${prefix}eav_attribute a
				WHERE a.backend_type != 'static'
				ORDER BY a.entity_type_id, a.backend_type
			";
			$attributes = $this->magento_query($sql);
			// Split the attributes by type
			foreach ( $attributes as $attribute ) {
				$attribute_types[$attribute['entity_type_id']][$attribute['backend_type']][$attribute['attribute_code']] = $attribute['attribute_id'];
			}
			return $attribute_types;
		}
		
		/**
		 * Get the entity type codes
		 * 
		 * @return array Entity type codes
		 */
		protected function get_magento_entity_type_codes() {
			$entity_type_codes = array();
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT t.entity_type_id, t.entity_type_code
				FROM ${prefix}eav_entity_type t
				ORDER BY t.entity_type_id
			";
			$result = $this->magento_query($sql);
			foreach ( $result as $row ) {
				$entity_type_codes[$row['entity_type_id']] = $row['entity_type_code'];
			}
			return $entity_type_codes;
		}

		/**
		 * Set the global entity types
		 * 
		 * @since 2.34.0
		 * 
		 * @param array $entity_type_codes
		 */
		protected function set_entity_types($entity_type_codes) {
			foreach ( $entity_type_codes as $entity_type_id => $entity_type_code ) {
				switch ( $entity_type_code ) {
					case 'catalog_product':
						$this->product_type_id = $entity_type_id;
						break;
					case 'catalog_category':
						$this->category_type_id = $entity_type_id;
						break;
					case 'customer':
						$this->customer_type_id = $entity_type_id;
						break;
					case 'customer_address':
						$this->customer_address_type_id = $entity_type_id;
						break;
				}
			}
		}
		
		/**
		 * Get the WooCommerce default tax rate
		 *
		 * @return float Tax rate
		 */
		private function get_default_tax_rate() {
			global $wpdb;
			$tax = 1;
			
			try {
				$sql = "
					SELECT tax_rate
					FROM {$wpdb->prefix}woocommerce_tax_rates
					WHERE tax_rate_priority = 1
					LIMIT 1
				";
				$tax_rate = $wpdb->get_var($sql);
				if ( !empty($tax_rate) ) {
					$tax = 1 + ($tax_rate / 100);
				}
			} catch ( PDOException $e ) {
				$this->plugin->display_admin_error(__('Error:', 'fg-magento-to-woocommerce') . $e->getMessage());
			}
			return $tax;
		}
		
		/**
		 * Set the media path
		 * 
		 * @since 2.34.0
		 */
		private function set_media_path() {
			// Get the home page content to see if the media are in the /pub/media directory
			$response = wp_remote_get($this->plugin_options['url'], array(
				'timeout' => $this->plugin_options['timeout'],
				'sslverify' => false,
				'cookies' => array('__cfduid' => 'xxx'), // Some sites block the request if this cookie doesn't exist
			)); // Uses WordPress HTTP API
			if ( !is_wp_error($response) && isset($response['body']) && preg_match('#' . preg_quote(trailingslashit($this->plugin_options['url']) . 'pub/media/') . '#', $response['body']) ) {
				$this->media_path = '/pub/media';
			} else {
				$this->media_path = '/media';
			}
		}
		
		/**
		 * Set the Global Manage Stock option
		 * 
		 * @since 3.18.0
		 */
		private function set_global_manage_stock() {
			$this->global_manage_stock = $this->get_core_config('cataloginventory/item_options/manage_stock');
			if ( $this->global_manage_stock === '' ) {
				$this->global_manage_stock = true; // Default system value = true
			}
		}
		
		/**
		 * Import CMS pages
		 */
		private function import_cms() {
			if ( $this->import_stopped() ) {
				return;
			}
			$imported_posts_count = 0;
			
			$message = __('Importing articles...', 'fg-magento-to-woocommerce');
			if ( defined('WP_CLI') ) {
				$progress_cli = \WP_CLI\Utils\make_progress_bar($message, $this->get_cms_count());
			} else {
				$this->log($message);
			}
			
			// Hook for doing other actions before the import
			do_action('fgm2wc_pre_import_posts');
			
			do {
				if ( $this->import_stopped() ) {
					return;
				}
				
				$posts = $this->get_cms_articles($this->chunks_size); // Get the CMS articles
				$posts_count = count($posts);
				
				if ( is_array($posts) ) {
					foreach ( $posts as $post ) {
						// Increment the CMS last imported post ID
						update_option('fgm2wc_last_magento_cms_id', $post['page_id']);
						
						$new_post_id = $this->import_cms_article($post);
						if ( $new_post_id ) {
							$imported_posts_count++;

							// Hook for doing other actions after inserting the post
							do_action('fgm2wc_post_import_cms_article', $new_post_id, $post);
						}
					}
				}
				$this->progressbar->increment_current_count($posts_count);
				
				if ( defined('WP_CLI') ) {
					$progress_cli->tick($this->chunks_size);
				}
			} while ( ($posts != null) && ($posts_count > 0) );
			
			if ( defined('WP_CLI') ) {
				$progress_cli->finish();
			}
			
			$this->display_admin_notice(sprintf(_n('%d article imported', '%d articles imported', $imported_posts_count, 'fg-magento-to-woocommerce'), $imported_posts_count));
			
			if ( !$this->import_stopped() ) {
				// Hook for doing other actions after the import
				do_action('fgm2wc_post_import_posts');
			}
			
			return array(
				'posts_count'	=> $imported_posts_count,
			);
		}
		
		/**
		 * Import a CMS page
		 * 
		 * @since 2.4.0
		 * 
		 * @param array $post CMS page data
		 * @return int New post ID
		 */
		public function import_cms_article($post) {
			// Hook for modifying the CMS post before processing
			$post = apply_filters('fgm2wc_pre_process_post', $post);

			// Date
			$post_date = $post['creation_time'];

			// Content
			$content = $post['content'];
			if ( !empty($post['content_heading']) ) {
				$content = '<h2>' . $post['content_heading'] . '</h2>' . $content;
			}

			// Replace Magento media URLs
			$content = preg_replace('/{{media url="(.*?)"}}/', "media/$1", $content);

			// Medias
			if ( !$this->plugin_options['skip_media'] ) {
				// Extra featured image
				$featured_image = '';
				list($featured_image, $post) = apply_filters('fgm2wc_pre_import_media', array($featured_image, $post));
				// Import media
				$result = $this->import_media_from_content($featured_image . $content, $post_date);
				$post_media = $result['media'];
				$this->media_count += $result['media_count'];
			} else {
				// Skip media
				$post_media = array();
			}

			// Process content
			$content = $this->process_content($content, $post_media);

			// Status
			$status = ($post['is_active'] == 1)? 'publish' : 'draft';

			// Insert the post
			$new_post = array(
				'post_content'		=> $content,
				'post_date'			=> $post_date,
				'post_status'		=> $status,
				'post_title'		=> $post['title'],
				'post_name'			=> $post['identifier'],
				'post_type'			=> $this->post_type,
				'menu_order'        => $post['sort_order'],
			);

			// Hook for modifying the WordPress post just before the insert
			$new_post = apply_filters('fgm2wc_pre_insert_post', $new_post, $post);

			$new_post_id = wp_insert_post($new_post);

			if ( $new_post_id ) {
				// Add links between the post and its medias
				$this->add_post_media($new_post_id, $this->get_attachment_ids($post_media), $post_date, $this->plugin_options['first_image'] != 'as_is');

				// Add the CMS ID as a post meta in order to modify links after
				add_post_meta($new_post_id, '_fgm2wc_old_cms_id', $post['page_id'], true);
				
				// Hook for doing other actions after inserting the post
				do_action('fgm2wc_post_insert_post', $new_post_id, $post);
			}
			
			return $new_post_id;
		}
		
		/**
		 * Import product categories
		 *
		 * @return int Number of product categories imported
		 */
		private function import_product_categories() {
			if ( $this->import_stopped() ) {
				return;
			}
			$message = __('Importing product categories...', 'fg-magento-to-woocommerce');
			if ( defined('WP_CLI') ) {
				$progress_cli = \WP_CLI\Utils\make_progress_bar($message, $this->get_all_product_categories_count());
			} else {
				$this->log($message);
			}
			
			// Allow HTML in term descriptions
			foreach ( array('pre_term_description') as $filter ) {
				remove_filter($filter, 'wp_filter_kses');
			}
			
			$imported_categories_count = 0;
			$terms = array();
			$taxonomy = 'product_cat';
			$this->used_slugs = array();
			
			// Set the list of previously imported categories
			$this->get_imported_categories($this->default_language);
			
			$categories = $this->get_all_product_categories();
			$categories_count = count($categories);
			foreach ( $categories as $category ) {
				
				// Check if the category is already imported
				if ( array_key_exists($category['entity_id'], $this->imported_categories[$this->default_language]) ) {
					$categories_count--;
					continue; // Do not import already imported category
				}
				
				$new_term = $this->import_product_category($category, $this->default_language);
				if ( !is_wp_error($new_term) ) {
					$imported_categories_count++;
					$terms[] = $new_term['term_id'];
					
					// Hook after inserting the category
					do_action('fgm2wc_post_import_product_category', $new_term['term_id'], $category);
				}
				if ( defined('WP_CLI') ) {
					$progress_cli->tick(1);
				}
			}
			
			// Set the list of imported categories
			$this->get_imported_categories($this->default_language);
			
			// Update the categories with their parent ids
			foreach ( $categories as $category ) {
				if ( array_key_exists($category['entity_id'], $this->imported_categories[$this->default_language]) && array_key_exists($category['parent_id'], $this->imported_categories[$this->default_language]) ) {
					$cat_id = $this->imported_categories[$this->default_language][$category['entity_id']];
					$parent_cat_id = $this->imported_categories[$this->default_language][$category['parent_id']];
					$cat = get_term_by('term_taxonomy_id', $cat_id, $taxonomy);
					$parent_cat = get_term_by('term_taxonomy_id', $parent_cat_id, $taxonomy);
					if ( $cat && $parent_cat ) {
						// Hook before editing the category
						$cat = apply_filters('fgm2wc_pre_edit_category', $cat, $parent_cat);
						wp_update_term($cat->term_id, $taxonomy, array('parent' => $parent_cat->term_id));
						// Hook after editing the category
						do_action('fgm2wc_post_edit_category', $cat);
					}
				}
			}
			
			if ( defined('WP_CLI') ) {
				$progress_cli->finish();
			}
			
			// Hook after importing all the categories
			do_action('fgm2wc_post_import_product_categories', $categories);
			
			// Update cache
			if ( !empty($terms) ) {
				wp_update_term_count_now($terms, $taxonomy);
				$this->clean_cache($terms, $taxonomy);
			}
			$this->progressbar->increment_current_count($categories_count);
			$this->display_admin_notice(sprintf(_n('%d product category imported', '%d product categories imported', $imported_categories_count, 'fg-magento-to-woocommerce'), $imported_categories_count));
		}
		
		/**
		 * Store the mapping of the imported product categories
		 * 
		 * @param int $language Language ID
		 */
		public function get_imported_categories($language) {
			$this->imported_categories[$language] = $this->get_term_metas_by_metakey('_fgm2wc_old_product_category_id' . '-lang' . $language);
		}
		
		/**
		 * Import a product category
		 *
		 * @since 2.4.0
		 * 
		 * @param array $category Category
		 * @param int $language Language ID
		 * @return WP_Term|WP_Error Inserted category
		 */
		public function import_product_category($category, $language) {
			$taxonomy = 'product_cat';
			
			// Other fields
			$category = array_merge($category, $this->get_attribute_values($category['entity_id'], $this->category_type_id, array(
				'name',
				'is_active',
				'description',
				'url_key',
				'url_path',
				'image',
				'sale_image',
				'thumbnail',
				'meta_title',
				'meta_description',
				'meta_keywords',
			)));
			
			if ( !isset($category['name']) ) {
				return new WP_Error();
			}
			
			// Don't import the disabled products categories
			if ( isset($this->premium_options['skip_disabled_products_categories']) && $this->premium_options['skip_disabled_products_categories'] ) {
				if ( $category['is_active'] == 0 ) {
					return new WP_Error();
				}
			}
			
			// Date
			$date = $category['created_at'];

			// Slug
			$slug = isset($category['url_key'])? $category['url_key']: sanitize_title($category['name']);
			$slug = $this->build_unique_slug($slug, $this->used_slugs);
			$this->used_slugs[] = $slug;

			// Parent
			$parent_id = isset($this->imported_categories[$language][$category['parent_id']])? $this->imported_categories[$language][$category['parent_id']] : 0;
			
			// Insert the category
			$new_category = array(
				'description'	=> isset($category['description'])? $category['description']: '',
				'slug'			=> $slug,
				'parent'		=> $parent_id,
			);

			// Hook before inserting the category
			$new_category = apply_filters('fgm2wc_pre_insert_product_category', $new_category, $category);
			
			$new_term = wp_insert_term($category['name'], $taxonomy, $new_category);
			if ( !is_wp_error($new_term) ) {
				// Store the product category ID
				add_term_meta($new_term['term_id'], '_fgm2wc_old_product_category_id' . '-lang' . $language, $category['entity_id'], true);
				
				// Category ordering
				if ( function_exists('wc_set_term_order') ) {
					wc_set_term_order($new_term['term_id'], $category['position'], $taxonomy);
				}

				// Category image
				if ( !$this->plugin_options['skip_media'] ) {
					$image_filename = $this->guess_image_filename($category);
					if ( !empty($image_filename) ) {
						$image_path = $this->media_path . '/catalog/category/' . $image_filename;
						$thumbnail_id = $this->import_media($category['name'], $image_path, $date);
						if ( !empty($thumbnail_id) ) {
							$this->media_count++;
							update_term_meta($new_term['term_id'], 'thumbnail_id', $thumbnail_id);
						}
					}
				}
				
				$this->imported_categories[$language][$category['entity_id']] = $new_term['term_id'];
				
				// Hook after inserting the category
				do_action('fgm2wc_post_insert_product_category', $new_term['term_id'], $category);
			}
			return $new_term;
		}
		
		/**
		 * Guess the image filename
		 * 
		 * @since 3.5.0
		 * 
		 * @param array $data data
		 * @return string Image filename
		 */
		private function guess_image_filename($data) {
			$filename = '';
			$potential_keys = array('image', 'sale_image', 'thumbnail');
			foreach ( $potential_keys as $key ) {
				if ( isset($data[$key]) && !empty($data[$key]) ) {
					$filename = $data[$key];
					break;
				}
			}
			return $filename;
		}
		
		/**
		 * Import products
		 *
		 * @return int Number of products imported
		 */
		private function import_products() {
			if ( !$this->test_woocommerce_activation() ) {
				return 0;
			}
			
			// Hook for doing other actions before importing the products
			do_action('fgm2wc_pre_import_products');
			
			if ( $this->import_stopped() ) {
				return;
			}
			$message = __('Importing products...', 'fg-magento-to-woocommerce');
			if ( defined('WP_CLI') ) {
				$progress_cli = \WP_CLI\Utils\make_progress_bar($message, $this->get_products_count());
			} else {
				$this->log($message);
			}
			$this->imported_products_count = 0;
			$this->imported_products = $this->get_imported_magento_products();
			
			do {
				if ( $this->import_stopped() ) {
					return;
				}
				$products = $this->get_products($this->chunks_size);
				$products_count = count($products);
				foreach ( $products as $product ) {
					// Increment the Magento last imported product ID
					update_option('fgm2wc_last_magento_product_id', $product['entity_id']);
					
					$new_post_id = $this->import_product($product, $this->default_language);
					if ( $new_post_id ) {
						$this->imported_products_count++;
						
						// Hook for doing other actions after importing the post
						do_action('fgm2wc_post_import_product', $new_post_id, $product);
					}
				}
				$this->progressbar->increment_current_count($products_count);
				
				if ( defined('WP_CLI') ) {
					$progress_cli->tick($this->chunks_size);
				}
			} while ( ($products != null) && ($products_count > 0) );
			
			if ( defined('WP_CLI') ) {
				$progress_cli->finish();
			}
			
			// Hook for doing other actions after all products are imported
			do_action('fgm2wc_post_import_products');
			
			$this->display_admin_notice(sprintf(_n('%d product imported', '%d products imported', $this->imported_products_count, 'fg-magento-to-woocommerce'), $this->imported_products_count));
		}
		
		/**
		 * Import a product
		 * 
		 * @since 2.4.0
		 * 
		 * @param array $product Product
		 * @param string $language Language
		 * @return int Product ID
		 */
		public function import_product($product, $language) {
			$product_id = $product['entity_id'];
			
			if ( ($language == $this->default_language) && isset($this->imported_products[$this->default_language]) && array_key_exists($product_id, $this->imported_products[$this->default_language]) ) {
				return 0; // Don't import a product already imported
			}
			
			// Other fields
			$product = array_merge($product, $this->get_other_product_fields($product_id, $this->product_type_id));
			
			// Don't import the disabled products
			if ( isset($this->premium_options['skip_disabled_products']) && $this->premium_options['skip_disabled_products'] ) {
				if ( $product['status'] != 1 ) {
					return 0;
				}
			}
			
			list($new_post, $product_medias, $post_media) = $this->build_product_post($product, $language);
			
			// Hook for modifying the WordPress post just before the insert
			$new_post = apply_filters('fgm2wc_pre_insert_product', $new_post, $product);

			$new_post_id = wp_insert_post($new_post);

			if ( $new_post_id ) {
				
				$this->imported_products[$language][$product_id] = $new_post_id;
				
				$prices = $this->import_product_details($new_post_id, $product, $product_medias, $post_media);
				
				// Add the Magento ID as a post meta
				if ( $language == $this->default_language ) {
					add_post_meta($new_post_id, '_fgm2wc_old_product_id', $product_id, true);
				} else {
					add_post_meta($new_post_id, '_fgm2wc_old_product_id' . '-lang' . $language, $product_id, true);
				}

				// Hook for doing other actions after importing the product details
				do_action('fgm2wc_post_import_product_details', $new_post_id, $product, $prices['regular_price'], $prices['sale_price']);

				// Hook for doing other actions after inserting the post
				do_action('fgm2wc_post_insert_product', $new_post_id, $product, $prices['regular_price'], $prices['sale_price']);
			}
			return $new_post_id;
		}
		
		/**
		 * Build the product post
		 * 
		 * @since 3.17.0
		 * 
		 * @param array $product Product
		 * @param string $language Language
		 * @return array WP Post, Product media, Post media
		 */
		protected function build_product_post(&$product, $language) {
			$product_id = $product['entity_id'];
			
			// Date
			$date = $product['created_at'];

			// Stock
			$stock = $this->get_stock($product_id, $this->website_id);
			if ( empty($stock) ) {
				$stock = $this->get_stock($product_id, 0); // Get the stock of the website 0
			}
			$product = array_merge($product, $stock);
			
			// Descriptions
			$content = isset($product['description'])? $product['description'] : '';
			$content = $this->replace_media_shortcodes($content);
			$product['description'] = $content;
			
			$excerpt = isset($product['short_description'])? $product['short_description'] : '';
			$excerpt = $this->replace_media_shortcodes($excerpt);
			$product['short_description'] = $excerpt;
			
			// Tags
			$tags = '';
			if ( isset($product['tags']) ) {
				$tag_options = $this->get_attribute_options_values($product['tags'], $language);
				$tags = implode(',', $tag_options);
			}
			
			// Hook for modifying the Magento product after we get all its data
			$product = apply_filters('fgm2wc_post_get_full_product', $product, $language);
			
			// Product images
			list($product_medias, $post_media) = $this->import_product_medias($product);

			// Product categories
			$categories_ids = array();
			$product_categories = $this->get_product_categories($product_id);
			$menu_order = 0;
			foreach ( $product_categories as $cat_id => $position ) {
				if ( isset($this->imported_categories[$language]) && array_key_exists($cat_id, $this->imported_categories[$language]) ) {
					$categories_ids[] = $this->imported_categories[$language][$cat_id];
				}
				if ( !empty($position) ) {
					$menu_order = $position;
				}
			}

			// Process content
			$content = $this->process_content($content, $post_media);
			$excerpt = $this->process_content($excerpt, $post_media);
			
			$title = isset($product['name'])? $product['name'] : $product['sku'];
			
			// Build the post data
			$new_post = array(
				'post_content'		=> $content,
				'post_date'			=> $date,
				'post_excerpt'		=> $excerpt,
				'post_status'		=> (!isset($product['status']) || ($product['status'] == 1))? 'publish': 'draft',
				'post_title'		=> $title,
				'post_name'			=> isset($product['url_key'])? $product['url_key'] : $title,
				'post_type'			=> 'product',
				'menu_order'		=> $menu_order,
				'tax_input'			=> array(
					'product_cat'	=> $categories_ids,
					'product_tag'	=> $tags,
				),
			);
			
			return array($new_post, $product_medias, $post_media);
		}
		
		/**
		 * Import the product details
		 * 
		 * @since 3.17.0
		 * 
		 * @param int $new_post_id WP Post ID
		 * @param array $product Product
		 * @param array $product_medias Product medias
		 * @param array $post_media Post media
		 * @return array Prices
		 */
		protected function import_product_details($new_post_id, $product, $product_medias, $post_media) {
			// Product type
			$product_type = $this->convert_product_type($product['type_id']);
			wp_set_object_terms($new_post_id, $product_type, 'product_type', true);

			// Product visibility
			$this->set_product_visibility($new_post_id, $product['visibility']);

			// Product galleries
			$medias_id = array();
			foreach ($product_medias as $media) {
				$medias_id[] = $media;
			}
			if ( $this->plugin_options['first_image_not_in_gallery'] ) {
				// Don't include the first image into the product gallery
				array_shift($medias_id);
			}
			$gallery = implode(',', $medias_id);

			// Prices
			if ( ($product['type_id'] == 'bundle') && ($product['price_type'] == 0) ) {
				$product['price'] = 0.0; // Price = 0 for bundle products
			}
			$prices = $this->calculate_prices($product);
			$special_from_date = isset($product['special_from_date'])? strtotime($product['special_from_date']): '';
			$special_to_date = isset($product['special_to_date'])? strtotime($product['special_to_date']): '';

			// Backorders
			$backorders = $this->allow_backorders($product['backorders'], $product['use_config_backorders']);

			// Stock
			$manage_stock = $this->set_manage_stock($product);
			if ( (($product['is_in_stock'] > 0) && ($product['qty'] > 0)) || ($manage_stock == 'no') ) {
				$stock_status = 'instock';
			} elseif ( $backorders != 'no' ) {
				$stock_status = 'onbackorder';
			} else {
				$stock_status = 'outofstock';
			}
			if ( $stock_status == 'outofstock' ) {
				wp_set_object_terms($new_post_id, $this->product_visibilities['outofstock'], 'product_visibility', true);
			}

			// Add the meta data
			update_post_meta($new_post_id, '_stock_status', $stock_status);
			update_post_meta($new_post_id, '_regular_price', $prices['regular_price']);
			update_post_meta($new_post_id, '_price', $prices['price']);
			update_post_meta($new_post_id, '_sale_price', $prices['sale_price']);
			update_post_meta($new_post_id, '_sale_price_dates_from', $special_from_date);
			update_post_meta($new_post_id, '_sale_price_dates_to', $special_to_date);
			if ( isset($product['weight']) ) {
				update_post_meta($new_post_id, '_weight', floatval($product['weight']));
			}
			if ( isset($product['length']) ) {
				update_post_meta($new_post_id, '_length', floatval($product['length']));
			}
			if ( isset($product['width']) ) {
				update_post_meta($new_post_id, '_width', floatval($product['width']));
			}
			if ( isset($product['height']) ) {
				update_post_meta($new_post_id, '_height', floatval($product['height']));
			}
			update_post_meta($new_post_id, '_sku', $product['sku']);
			update_post_meta($new_post_id, '_stock', $product['qty']);
			update_post_meta($new_post_id, '_manage_stock', $manage_stock);
			update_post_meta($new_post_id, '_backorders', $backorders);
			update_post_meta($new_post_id, '_product_image_gallery', $gallery);
			update_post_meta($new_post_id, '_wc_review_count', 0);
			update_post_meta($new_post_id, '_wc_rating_count', array());
			update_post_meta($new_post_id, '_wc_average_rating', 0);
			update_post_meta($new_post_id, 'total_sales', 0);

			// Add links between the post and its medias
			$date = $product['created_at'];
			$this->add_post_media($new_post_id, $product_medias, $date, true);
			$this->add_post_media($new_post_id, $this->get_attachment_ids($post_media), $date, false);
			
			return $prices;
		}
		
		/**
		 * Get the Magento attribute options values
		 *
		 * @since 2.73.0
		 * 
		 * @param string $option_ids Options IDs separated by commas
		 * @param int $store_id Store ID
		 * @return array of attributes options values
		 */
		private function get_attribute_options_values($option_ids, $store_id) {
			$attribute_options = array();
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT DISTINCT ov.option_id, ov.value
				FROM ${prefix}eav_attribute_option_value ov
				WHERE ov.option_id IN($option_ids)
				AND ov.store_id IN(0, $store_id)
				ORDER BY ov.store_id
			";
			$result = $this->magento_query($sql);
			foreach ( $result as $row ) {
				$attribute_options[$row['option_id']] = $row['value'];
			}
			
			return $attribute_options;
		}
		
		/**
		 * Replace the media shortcodes like {{media url="filename.jpg"}}
		 * 
		 * @since 2.45.0
		 * 
		 * @param string $content Content
		 * @return string Content
		 */
		private function replace_media_shortcodes($content) {
			$content = preg_replace('/{{media url="(.*?)"}}/', 'media/$1', $content);
			return $content;
		}
		
		/**
		 * Get the product stock information
		 * 
		 * @since 2.57.2
		 * 
		 * @param int $product_id Product ID
		 * @param int $website_id Website ID
		 * @return array Stock informations
		 */
		public function get_stock($product_id, $website_id) {
			$stock = array();
			$prefix = $this->plugin_options['prefix'];
			$stock_website_criteria = version_compare($this->magento_version, '2', '>=')? "AND s.website_id = '{$website_id}'" : '';
			
			$sql = "
				SELECT s.qty, s.manage_stock, s.use_config_manage_stock, s.is_in_stock, s.backorders, s.use_config_backorders
				FROM ${prefix}cataloginventory_stock_item s
				WHERE s.product_id = '$product_id'
				$stock_website_criteria
				LIMIT 1
			";
			$result = $this->magento_query($sql);
			if ( count($result) > 0 ) {
				$stock = $result[0];
				if ( $stock < 0 ) {
					$stock = 0; // Prevent negative value
				}
			}
			return $stock;
		}
		
		/**
		 * Set the "manage stock" option
		 * 
		 * @since 2.41.0
		 * 
		 * @param array $product Product data
		 * @return string yes|no
		 */
		public function set_manage_stock($product) {
			$manage_stock = 'no';
			if ( $this->plugin_options['stock_management'] ) {
				if ( $product['type_id'] != 'bundle' ) {
					if ( $product['manage_stock'] == 1 ) {
						$manage_stock = 'yes';
					} elseif ( $product['use_config_manage_stock'] == 1 ) {
						$manage_stock = $this->global_manage_stock? 'yes' : 'no';
					}
				}
			}
			return $manage_stock;
		}
		
		/**
		 * Get the product type
		 * 
		 * @since      1.10.0
		 * 
		 * @param string $type_id Magento Type ID (simple, grouped, …)
		 * @return int Product type term ID
		 */
		private function convert_product_type($type_id) {
			$product_type = 0;
			switch ($type_id) {
				case 'grouped':
					$product_type = $this->product_types['grouped'];
					break;
				default:
					$product_type = $this->product_types['simple'];
			}
			return intval($product_type);
		}
		
		/**
		 * Calculate the product prices
		 * 
		 * @since 2.3.0
		 * 
		 * @param array $product Product
		 * @return array Prices
		 */
		public function calculate_prices($product) {
			$regular_price = isset($product['price'])? floatval($product['price']): 0.0;
			$sale_price = isset($product['special_price'])? floatval($product['special_price']): '';
			if ( ($this->plugin_options['sale_price'] == 'msrp') && isset($product['msrp']) && !empty($product['msrp']) ) {
				// Manufacturer´s Suggested Retail Price
				$regular_price = floatval($product['msrp']);
				$sale_price = isset($product['price'])? floatval($product['price']): '';
			}
			if ( $this->plugin_options['price'] == 'with_tax' ) {
				$regular_price *= $this->global_tax_rate;
				if ( !empty($sale_price) ) {
					$sale_price *= $this->global_tax_rate;
				}
			}
			$special_from_date = isset($product['special_from_date'])? strtotime($product['special_from_date']): '';
			$special_to_date = isset($product['special_to_date'])? strtotime($product['special_to_date']): '';
			$now = time();
			if ( !empty($sale_price) && ($now > $special_from_date) && (($now < $special_to_date) || empty($special_to_date)) ) {
				$price = $sale_price;
			} else {
				$price = $regular_price;
			}
			$prices = array(
				'regular_price'	=> $regular_price,
				'sale_price'	=> $sale_price,
				'price'			=> $price,
			);
			$prices = apply_filters('fgm2wc_calculate_prices', $prices, $product);
			
			return $prices;
		}
		
		/**
		 * Get the other product fields
		 * 
		 * @param int $product_id Product ID
		 * @param int $product_entity_id Product Entity ID
		 * @return array Product data
		 */
		public function get_other_product_fields($product_id, $product_entity_id) {
			$fields = array(
				'name',
				'description',
				'short_description',
				'price',
				'price_type',
				'special_price',
				'special_from_date',
				'special_to_date',
				'msrp',
				'weight',
				'length',
				'width',
				'height',
				'meta_title',
				'meta_description',
				'meta_keyword',
				'meta_keywords',
				'image',
				'image_label',
				'status',
				'url_key',
				'url_path',
				'visibility',
				'links_purchased_separately',
				'tax_class_id',
				'tags',
			);
			$fields = apply_filters('fgm2wc_get_other_fields', $fields);
			return $this->get_attribute_values($product_id, $product_entity_id, $fields);
		}
		
		/**
		 * Import the product medias
		 * 
		 * @param array $product Product data
		 * @return array[array, array] = [product_medias, post_media]
		 */
		public function import_product_medias($product) {
			$product_medias = array();
			$post_media = array();
			
			if ( !$this->plugin_options['skip_media'] ) {
				$images = array();
				
				// Featured image
				if ( !empty($product['image']) ) {
					$featured_image = array(
						'value_id'	=> 0,
						'value'		=> $product['image'],
						'label'		=> isset($product['image_label'])? $product['image_label']: '',
					);
					$images[] = $featured_image;
				}
				
				// Gallery images
				$product_images = $this->get_product_images($product['entity_id'], $this->store_id);
				if ( empty($product_images) ) {
					$product_images = $this->get_product_images($product['entity_id'], 0);
				}
				foreach ( $product_images as $image ) {
					if ( $image['value'] != $product['image'] ) { // to avoid duplicate images
						$images[] = $image;
					}
				}
				foreach ( $images as $image ) {
					$image_name = !empty($image['label'])? $image['label'] : $product['name'] . '-' . $image['value_id'];
					if ( !preg_match('#^/#', $image['value']) ) {
						// Add a slash before the path
						$image['value'] = '/' . $image['value'];
					}
					$product_images_subdirectory = apply_filters('fgm2wc_product_images_subdirectory', '/catalog/product',  $image);
					$image_filename = $this->media_path . $product_images_subdirectory . $image['value'];
					$media_id = $this->import_media($image_name, $image_filename, $product['created_at']);
					if ( $media_id !== false ) {
						$product_medias[] = $media_id;
					}
				}
				$product_medias = array_unique($product_medias);
				$this->media_count += count($product_medias);

				// Import content media
				$content = isset($product['description'])? $product['description'] : '';
				if ( isset($product['short_description']) ) {
					$content .= $product['short_description'];
				}
				$result = $this->import_media_from_content($content, $product['created_at']);
				$post_media = $result['media'];
				$this->media_count += $result['media_count'];
			}
			return array($product_medias, $post_media);
		}
		
		/**
		 * Build a unique slug
		 * 
		 * @param string $slug Wished slug
		 * @param array $used_slugs Used slugs
		 * @return string New unique slug
		 */
		private function build_unique_slug($slug, $used_slugs) {
			$matches = array();
			$slug = apply_filters('fgm2wc_pre_build_unique_slug', $slug);
			$slug = sanitize_title($slug);
			if ( in_array($slug, $used_slugs) ) {
				// Get the slug suffix
				if ( preg_match('/(.*)\-(\d+)$/', $slug, $matches) ) {
					$inc = $matches[2] + 1;
					$slug = $matches[1];
				} else {
					$inc = 1;
				}
				// Add a suffix
				$slug .= '-' . $inc;
			}
			return $slug;
		}
		
		/**
		 * Get CMS articles
		 *
		 * @param int $limit Number of articles max
		 * @return array of Posts
		 */
		protected function get_cms_articles($limit=1000) {
			$articles = array();
			$prefix = $this->plugin_options['prefix'];
			
			$last_magento_cms_id = (int)get_option('fgm2wc_last_magento_cms_id'); // to restore the import where it left

			// Hooks for adding extra cols and extra joins
			$extra_cols = apply_filters('fgm2wc_get_posts_add_extra_cols', '');
			$extra_joins = apply_filters('fgm2wc_get_posts_add_extra_joins', '');

			if ( version_compare($this->magento_version, '1.4', '<') ) {
				// Magento 1.3 and less
				$content_heading_field = '"" AS content_heading';
			} else {
				// Magento 1.4+
				$content_heading_field = 'a.content_heading';
			}
			$sql = "
				SELECT DISTINCT a.page_id, a.title, a.meta_keywords, a.meta_description, a.identifier, $content_heading_field, a.content, a.creation_time, a.is_active, a.sort_order
				$extra_cols
				FROM ${prefix}cms_page a
				INNER JOIN ${prefix}cms_page_store s ON s.page_id = a.page_id AND s.store_id IN (0, {$this->store_id})
				$extra_joins
				WHERE a.page_id > '$last_magento_cms_id'
				ORDER BY a.page_id
				LIMIT $limit
			";
			$sql = apply_filters('fgm2wc_get_posts_sql', $sql, $extra_cols, $extra_joins, $last_magento_cms_id, $limit);
			$articles = $this->magento_query($sql);
			
			return $articles;
		}
		
		/**
		 * Get product categories
		 *
		 * @return array of Categories
		 */
		private function get_all_product_categories() {
			$categories = array();
			$prefix = $this->plugin_options['prefix'];

			$sql = "
				SELECT c.entity_id, c.parent_id, c.created_at, c.position
				FROM ${prefix}catalog_category_entity c
				WHERE c.parent_id != 0 -- don't import the root category
				ORDER BY c.level, c.position
			";
			$sql = apply_filters('fgm2wc_get_categories_sql', $sql);
			$categories = $this->magento_query($sql);
			
			$categories = apply_filters('fgm2wc_get_categories', $categories);
			
			return $categories;
		}
		
		/**
		 * Get attribute values
		 *
		 * @param int $entity_id Entity ID
		 * @param int $entity_type_id Entity type ID
		 * @param int $required_attributes Array of attribute keys (optional)
		 * @return array of values
		 */
		public function get_attribute_values($entity_id, $entity_type_id, $required_attributes=array()) {
			$values = array();
			$prefix = $this->plugin_options['prefix'];
			
			// Get the entity type code
			$entity_type_code = $this->entity_type_codes[$entity_type_id];
			if ( empty($entity_type_code) ) {
				return array();
			}
			
			// Get the values from the different tables
			if ( isset($this->attribute_types[$entity_type_id]) ) {
				foreach ( $this->attribute_types[$entity_type_id] as $attribute_type => $attributes ) {
					if ( empty($required_attributes) ) {
						$attributes_ids = $attributes;
					} else {
						$attributes_ids = array();
						// Get the required attributes IDs
						foreach ( $required_attributes as $required_attribute) {
							if ( array_key_exists($required_attribute, $attributes) ) {
								$attributes_ids[] = $attributes[$required_attribute];
							}
						}
					}
					if ( !empty($attributes_ids) ) {
						$attributes_ids_list = implode("', '", $attributes_ids);
						$sql = "
							SELECT a.attribute_code, e.value
							FROM ${prefix}${entity_type_code}_entity_$attribute_type e
							INNER JOIN ${prefix}eav_attribute a ON a.attribute_id = e.attribute_id
							WHERE e.entity_id = '$entity_id'
							AND e.attribute_id IN ('$attributes_ids_list')
						";
						if ( in_array($entity_type_code, array('catalog_product', 'catalog_category')) ) {
							$sql .= "
								AND e.store_id IN (0, {$this->store_id})
								ORDER BY e.store_id
							";
						}
						$result = $this->magento_query($sql);
						foreach ( $result as $row ) {
							$values[$row['attribute_code']] = $row['value'];
						}
					}
				}
			}
			return $values;
		}
		
		/**
		 * Get the products
		 * 
		 * @param int $limit Number of products max
		 * @return array of products
		 */
		private function get_products($limit=1000) {
			$products = array();
			$prefix = $this->plugin_options['prefix'];

			$last_magento_product_id = (int)get_option('fgm2wc_last_magento_product_id'); // to restore the import where it left
			
			$website_criteria = '';
			if ( $this->website_id != 'all' ) {
				$website_criteria = "INNER JOIN ${prefix}catalog_product_website pw ON pw.product_id = p.entity_id AND pw.website_id = {$this->website_id}";
			}
			$sql = "
				SELECT DISTINCT p.entity_id, p.type_id, p.sku, p.created_at
				FROM ${prefix}catalog_product_entity p
				INNER JOIN ${prefix}catalog_product_entity_int pei ON pei.entity_id = p.entity_id
				INNER JOIN ${prefix}eav_attribute a ON a.attribute_id = pei.attribute_id
				$website_criteria
				WHERE a.attribute_code = 'visibility'
				AND pei.value != 1 -- Different from 'Not visible individually'
				AND p.entity_id > '$last_magento_product_id'
				ORDER BY p.entity_id
				LIMIT $limit
			";
			$sql = apply_filters('fgm2wc_get_products_sql', $sql);
			$products = $this->magento_query($sql);
			
			$products = apply_filters('fgm2wc_get_products', $products);
			
			return $products;
		}
		
		/**
		 * Get a product
		 * 
		 * @since 3.18.0
		 * 
		 * @param int $product_id Product ID
		 * @return array Product data
		 */
		public function get_product($product_id) {
			$product = array();
			$prefix = $this->plugin_options['prefix'];

			$sql = "
				SELECT p.entity_id, p.type_id, p.sku, p.created_at
				FROM ${prefix}catalog_product_entity p
				WHERE p.entity_id = '$product_id'
				LIMIT 1
			";
			$results = $this->magento_query($sql);
			if ( count($results) > 0 ) {
				$product = $results[0];
			}
			
			return $product;
		}
		
		/**
		 * Get the product images
		 *
		 * @param int $product_id Product ID
		 * @param int $store_id Store ID
		 * @return array of images
		 */
		public function get_product_images($product_id, $store_id=0) {
			$images = array();
			$prefix = $this->plugin_options['prefix'];

			if ( version_compare($this->magento_version, '2', '<') ) {
				// Magento 1
				$sql = "
					SELECT DISTINCT g.value_id, g.value, gv.label, gv.position
					FROM ${prefix}catalog_product_entity_media_gallery g
					INNER JOIN ${prefix}catalog_product_entity_media_gallery_value gv ON gv.value_id = g.value_id
					WHERE g.entity_id = '$product_id'
					AND gv.store_id = '$store_id'
					AND gv.disabled = 0
					ORDER BY gv.position
				";
			} else {
				// Magento 2+
				$sql = "
					SELECT DISTINCT g.value_id, g.value, gv.label, gv.position
					FROM ${prefix}catalog_product_entity_media_gallery g
					INNER JOIN ${prefix}catalog_product_entity_media_gallery_value_to_entity gve ON gve.value_id = g.value_id
					INNER JOIN ${prefix}catalog_product_entity_media_gallery_value gv ON gv.value_id = g.value_id
					WHERE gve.entity_id = '$product_id'
					AND gv.store_id = '$store_id'
					AND gv.disabled = 0
					ORDER BY gv.position
				";
			}
			$images = $this->magento_query($sql);
			$images = apply_filters('fgm2wc_get_product_images', $images, $product_id, $store_id);
			
			return $images;
		}
		
		/**
		 * Get the categories from a product
		 *
		 * @param int $product_id Magento product ID
		 * @return array of categories
		 */
		private function get_product_categories($product_id) {
			$categories = array();
			$prefix = $this->plugin_options['prefix'];

			$sql = "
				SELECT cp.category_id, cp.position
				FROM ${prefix}catalog_category_product cp
				WHERE cp.product_id = '$product_id'
				ORDER BY cp.position
			";
			$result = $this->magento_query($sql);
			foreach ( $result as $row ) {
				$categories[$row['category_id']] = $row['position'];
			}
			return $categories;
		}
		
		/**
		 * Import post medias from content
		 *
		 * @param string $content post content
		 * @param date $post_date Post date (for storing media)
		 * @param array $options Options
		 * @return array:
		 * 		array media: Medias imported
		 * 		int media_count:   Medias count
		 */
		public function import_media_from_content($content, $post_date, $options=array()) {
			$media = array();
			$media_count = 0;
			$matches = array();
			$alt_matches = array();
			$title_matches = array();
			
			if ( preg_match_all('#<(img|a)(.*?)(src|href)="(.*?)"(.*?)>#', $content, $matches, PREG_SET_ORDER) > 0 ) {
				if ( is_array($matches) ) {
					foreach ($matches as $match ) {
						$filename = $match[4];
						$other_attributes = $match[2] . $match[5];
						// Image Alt
						$image_alt = '';
						if (preg_match('#alt="(.*?)"#', $other_attributes, $alt_matches) ) {
							$image_alt = wp_strip_all_tags(stripslashes($alt_matches[1]), true);
						}
						// Image caption
						$image_caption = '';
						if (preg_match('#title="(.*?)"#', $other_attributes, $title_matches) ) {
							$image_caption = $title_matches[1];
						}
						$attachment_id = $this->import_media($image_alt, $filename, $post_date, $options, $image_caption);
						if ( $attachment_id !== false ) {
							$media_count++;
							$media[$filename] = $attachment_id;
						}
					}
				}
			}
			return array(
				'media'			=> $media,
				'media_count'	=> $media_count
			);
		}
		
		/**
		 * Import a media
		 *
		 * @param string $name Image name
		 * @param string $filename Image URL
		 * @param date $date Date
		 * @param array $options Options
		 * @param string $image_caption Image caption
		 * @return int attachment ID or false
		 */
		public function import_media($name, $filename, $date='0000-00-00 00:00:00', $options=array(), $image_caption='') {
			if ( $date == '0000-00-00 00:00:00' ) {
				$date = date('Y-m-d H:i:s');
			}
			$import_external = ($this->plugin_options['import_external'] == 1) || (isset($options['force_external']) && $options['force_external'] );

			$filename = str_replace("%20", " ", $filename); // for filenames with spaces

			$filetype = wp_check_filetype($filename);
			if ( empty($filetype['type']) || ($filetype['type'] == 'text/html') ) { // Unrecognized file type
				return false;
			}

			// Upload the file from the Magento web site to WordPress upload dir
			if ( preg_match('/^http/', $filename) ) {
				if ( $import_external || // External file 
					preg_match('#^' . $this->plugin_options['url'] . '#', $filename) // Local file
				) {
					$old_filename = $filename;
				} else {
					return false;
				}
			} else {
				if ( strpos($filename, '/') === 0 ) { // Avoid a double slash
					$old_filename = untrailingslashit($this->plugin_options['url']) . $filename;
				} else {
					$old_filename = trailingslashit($this->plugin_options['url']) . $filename;
				}
			}
			$old_filename = str_replace(" ", "%20", $old_filename); // for filenames with spaces
			$img_dir = strftime('%Y/%m', strtotime($date));
			$uploads = wp_upload_dir($img_dir);
			$new_upload_dir = $uploads['path'];

			$new_filename = $filename;
			if ( $this->plugin_options['import_duplicates'] == 1 ) {
				// Images with duplicate names
				$new_filename = preg_replace('#.*media/catalog/#', '', $new_filename);
				$new_filename = str_replace('http://', '', $new_filename);
				$new_filename = str_replace('/', '_', $new_filename);
			}

			$basename = basename($new_filename);
			$basename = sanitize_file_name($basename);
			$new_full_filename = $new_upload_dir . '/' . $basename;

			// GUID
			$upload_dir = wp_upload_dir();
			$guid = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $new_full_filename);
			$attachment_id = $this->get_post_id_from_guid($guid);
			
			if ( empty($attachment_id) ) {
//				print "Copy \"$old_filename\" => $new_full_filename<br />";
				if ( $this->need_fix_image_orientation($old_filename) ) {
					$result_copy = $this->fix_image_orientation($old_filename, $new_full_filename);
				} else {
					$result_copy = @$this->remote_copy($old_filename, $new_full_filename);
				}
				if ( !$result_copy ) {
					$error = error_get_last();
					$error_message = $error['message'];
					$this->display_admin_error("Can't copy $old_filename to $new_full_filename : $error_message");
					return false;
				}

				$post_title = !empty($name)? $name : preg_replace('/\.[^.]+$/', '', $basename);

				// Image Alt
				$image_alt = '';
				if ( !empty($name) ) {
					$image_alt = wp_strip_all_tags(stripslashes($name), true);
				}

				$attachment_id = $this->insert_attachment($post_title, $basename, $new_full_filename, $guid, $date, $filetype['type'], $image_alt, $image_caption);
			}
			return $attachment_id;
		}
		
		/**
		 * Check if the image orientation needs to be fixed
		 * 
		 * @since 2.86.1
		 * 
		 * @param string $url URL of the source file
		 * @return boolean
		 */
		private function need_fix_image_orientation($url) {
			if ( function_exists('exif_read_data') ) {
				$exif = @exif_read_data($url);
				return $exif && ($exif['MimeType'] == 'image/jpeg') && isset($exif['Orientation']) && ($exif['Orientation'] !== 1);
			} else {
				return false;
			}
		}
		
		/**
		 * Fix the images that are displayed rotated
		 * 
		 * @since 2.86.1
		 * 
		 * @param string $url URL of the source file
		 * @param string $path destination file
		 * @return boolean
		 */
		private function fix_image_orientation($url, $path) {
			if ( !$this->plugin_options['force_media_import'] && file_exists($path) && (filesize($path) > 0) ) {
				// Don't download the file if already downloaded
				return true;
			}
			
			$img = imagecreatefromjpeg($url);
			$result = imagejpeg($img, $path, 100);
			imagedestroy($img);
			
			return $result;
		}
		
		/**
		 * Save the attachment and generates its metadata
		 * 
		 * @since 2.13.0
		 * 
		 * @param string $attachment_title Attachment name
		 * @param string $basename Original attachment filename
		 * @param string $new_full_filename New attachment filename with path
		 * @param string $guid GUID
		 * @param date $date Date
		 * @param string $filetype File type
		 * @param string $image_alt Image description
		 * @param string $image_caption Image caption
		 * @return int|false Attachment ID or false
		 */
		public function insert_attachment($attachment_title, $basename, $new_full_filename, $guid, $date, $filetype, $image_alt='', $image_caption='') {
			$post_name = sanitize_title($attachment_title);
			
			// If the attachment does not exist yet, insert it in the database
			$attachment_id = 0;
			$attachment = $this->get_attachment_from_name($post_name);
			if ( $attachment ) {
				$attached_file = basename(get_attached_file($attachment->ID));
				if ( $attached_file == $basename ) { // Check if the filename is the same (in case where the legend is not unique)
					$attachment_id = $attachment->ID;
				}
			}
			if ( $attachment_id == 0 ) {
				$attachment_data = array(
					'guid'				=> $guid, 
					'post_date'			=> $date,
					'post_mime_type'	=> $filetype,
					'post_name'			=> $post_name,
					'post_title'		=> $attachment_title,
					'post_status'		=> 'inherit',
					'post_content'		=> '',
					'post_excerpt'		=> $image_caption,
				);
				$attachment_id = wp_insert_attachment($attachment_data, $new_full_filename);
				add_post_meta($attachment_id, '_fgm2wc_imported', 1, true); // To delete the imported attachments
			}
			
			if ( !empty($attachment_id) ) {
				if ( preg_match('/(image|audio|video)/', $filetype) ) { // Image, audio or video
					if ( !$this->plugin_options['skip_thumbnails'] ) {
						// you must first include the image.php file
						// for the function wp_generate_attachment_metadata() to work
						require_once(ABSPATH . 'wp-admin/includes/image.php');
						$attach_data = wp_generate_attachment_metadata( $attachment_id, $new_full_filename );
						wp_update_attachment_metadata($attachment_id, $attach_data);
					}
					
					// Image Alt
					if ( !empty($image_alt) ) {
						update_post_meta($attachment_id, '_wp_attachment_image_alt', addslashes($image_alt)); // update_post_meta expects slashed
					}
				}
				return $attachment_id;
			} else {
				return false;
			}
		}
		
		/**
		 * Check if the attachment exists in the database
		 *
		 * @param string $name
		 * @return object Post
		 */
		private function get_attachment_from_name($name) {
			$name = preg_replace('/\.[^.]+$/', '', basename($name));
			$r = array(
				'name'			=> $name,
				'post_type'		=> 'attachment',
				'numberposts'	=> 1,
			);
			$posts_array = get_posts($r);
			if ( is_array($posts_array) && (count($posts_array) > 0) ) {
				return $posts_array[0];
			}
			else {
				return false;
			}
		}

		/**
		 * Process the post content
		 *
		 * @param string $content Post content
		 * @param array $post_media Post medias
		 * @return string Processed post content
		 */
		public function process_content($content, $post_media) {

			if ( !empty($content) ) {
				// Replace page breaks
				$content = preg_replace("#<hr([^>]*?)class=\"system-pagebreak\"(.*?)/>#", "<!--nextpage-->", $content);

				// Replace media URLs with the new URLs
				$content = $this->process_content_media_links($content, $post_media);

				// For importing backslashes
				$content = addslashes($content);
			}

			return $content;
		}

		/**
		 * Replace media URLs with the new URLs
		 *
		 * @param string $content Post content
		 * @param array $post_media Post medias
		 * @return string Processed post content
		 */
		private function process_content_media_links($content, $post_media) {
			$matches = array();
			$matches_caption = array();

			if ( is_array($post_media) ) {

				// Get the attachments attributes
				$attachments_found = false;
				$medias = array();
				foreach ( $post_media as $old_filename => $attachment_id ) {
					$media = array();
					$media['attachment_id'] = $attachment_id;
					$media['url_old_filename'] = urlencode($old_filename); // for filenames with spaces or accents
					if ( preg_match('/image/', get_post_mime_type($attachment_id)) ) {
						// Image
						$image_src = wp_get_attachment_image_src($attachment_id, 'full');
						$media['new_url'] = $image_src[0];
						$media['width'] = $image_src[1];
						$media['height'] = $image_src[2];
					} else {
						// Other media
						$media['new_url'] = wp_get_attachment_url($attachment_id);
					}
					$medias[$old_filename] = $media;
					$attachments_found = true;
				}
				if ( $attachments_found ) {

					// Remove the links from the content
					$this->post_link_count = 0;
					$this->post_link = array();
					$content = preg_replace_callback('#<(a) (.*?)(href)=(.*?)</a>#i', array($this, 'remove_links'), $content);
					$content = preg_replace_callback('#<(img) (.*?)(src)=(.*?)>#i', array($this, 'remove_links'), $content);

					// Process the stored medias links
					$first_image_removed = false;
					foreach ($this->post_link as &$link) {

						// Remove the first image from the content
						if ( ($this->plugin_options['first_image'] == 'as_featured') && !$first_image_removed && preg_match('#^<img#', $link['old_link']) ) {
							$link['new_link'] = '';
							$first_image_removed = true;
							continue;
						}
						$new_link = $link['old_link'];
						$alignment = '';
						if ( preg_match('/(align="|float: )(left|right)/', $new_link, $matches) ) {
							$alignment = 'align' . $matches[2];
						}
						if ( preg_match_all('#(src|href)="(.*?)"#i', $new_link, $matches, PREG_SET_ORDER) ) {
							$caption = '';
							foreach ( $matches as $match ) {
								$old_filename = $match[2];
								$link_type = ($match[1] == 'src')? 'img': 'a';
								if ( array_key_exists($old_filename, $medias) ) {
									$media = $medias[$old_filename];
									if ( array_key_exists('new_url', $media) ) {
										if ( (strpos($new_link, $old_filename) > 0) || (strpos($new_link, $media['url_old_filename']) > 0) ) {
											// URL encode the filename
											$new_filename = basename($media['new_url']);
											$encoded_new_filename = rawurlencode($new_filename);
											$new_url = str_replace($new_filename, $encoded_new_filename, $media['new_url']);
											$new_link = preg_replace('#(' . preg_quote($old_filename) . '|' . preg_quote($media['url_old_filename']) . ')#', $new_url, $new_link, 1);

											if ( $link_type == 'img' ) { // images only
												// Define the width and the height of the image if it isn't defined yet
												if ((strpos($new_link, 'width=') === false) && (strpos($new_link, 'height=') === false)) {
													$width_assertion = isset($media['width']) && !empty($media['width'])? ' width="' . $media['width'] . '"' : '';
													$height_assertion = isset($media['height']) && !empty($media['height'])? ' height="' . $media['height'] . '"' : '';
												} else {
													$width_assertion = '';
													$height_assertion = '';
												}

												// Caption shortcode
												if ( preg_match('/class=".*caption.*?"/', $link['old_link']) ) {
													if ( preg_match('/title="(.*?)"/', $link['old_link'], $matches_caption) ) {
														$caption_value = str_replace('%', '%%', $matches_caption[1]);
														$align_value = ($alignment != '')? $alignment : 'alignnone';
														$caption = '[caption id="attachment_' . $media['attachment_id'] . '" align="' . $align_value . '"' . $width_assertion . ']%s' . $caption_value . '[/caption]';
													}
												}

												$align_class = ($alignment != '')? $alignment . ' ' : '';
												$new_link = preg_replace('#<img(.*?)( class="(.*?)")?(.*) />#', "<img$1 class=\"$3 " . $align_class . 'size-full wp-image-' . $media['attachment_id'] . "\"$4" . $width_assertion . $height_assertion . ' />', $new_link);
											}
										}
									}
								}
							}

							// Add the caption
							if ( $caption != '' ) {
								$new_link = sprintf($caption, $new_link);
							}
						}
						$link['new_link'] = $new_link;
					}

					// Reinsert the converted medias links
					$content = preg_replace_callback('#__fg_link_(\d+)__#', array($this, 'restore_links'), $content);
				}
			}
			return $content;
		}

		/**
		 * Remove all the links from the content and replace them with a specific tag
		 * 
		 * @param array $matches Result of the preg_match
		 * @return string Replacement
		 */
		private function remove_links($matches) {
			$this->post_link[] = array('old_link' => $matches[0]);
			return '__fg_link_' . $this->post_link_count++ . '__';
		}

		/**
		 * Restore the links in the content and replace them with the new calculated link
		 * 
		 * @param array $matches Result of the preg_match
		 * @return string Replacement
		 */
		private function restore_links($matches) {
			$link = $this->post_link[$matches[1]];
			$new_link = array_key_exists('new_link', $link)? $link['new_link'] : $link['old_link'];
			return $new_link;
		}

		/**
		 * Add a link between a media and a post (parent id + thumbnail)
		 *
		 * @param int $post_id Post ID
		 * @param array $post_media Post medias
		 * @param array $date Date
		 * @param boolean $set_featured_image Set the featured image?
		 */
		public function add_post_media($post_id, $post_media, $date, $set_featured_image=true) {
			$thumbnail_is_set = false;
			if ( is_array($post_media) ) {
				foreach ( $post_media as $media ) {
					$attachment = get_post($media);
					if ( !empty($attachment) && ($attachment->post_type == 'attachment') ) {
						$attachment->post_parent = $post_id; // Attach the post to the media
						$attachment->post_date = $date ;// Define the media's date
						wp_update_post($attachment);

						// Set the featured image. If not defined, it is the first image of the content.
						if ( $set_featured_image && !$thumbnail_is_set ) {
							set_post_thumbnail($post_id, $attachment->ID);
							$thumbnail_is_set = true;
						}
					}
				}
			}
		}

		/**
		 * Get the IDs of the medias
		 *
		 * @param array $post_media Post medias
		 * @return array Array of attachment IDs
		 */
		public function get_attachment_ids($post_media) {
			$attachments_ids = array();
			if ( is_array($post_media) ) {
				foreach ( $post_media as $media ) {
					$attachments_ids[] = $media;
				}
			}
			return $attachments_ids;
		}
		
		/**
		 * Copy a remote file
		 * in replacement of the copy function
		 * 
		 * @param string $url URL of the source file
		 * @param string $path destination file
		 * @return boolean
		 */
		public function remote_copy($url, $path) {
			$result = false;
			
			if ( !$this->plugin_options['force_media_import'] && file_exists($path) && (filesize($path) > 0) ) {
				// Don't download the file if already downloaded
				return true;
			}
			
			$response = wp_remote_get($url, array(
				'timeout'		=> $this->plugin_options['timeout'],
				'sslverify'		=> false,
				'user-agent'	=> 'Mozilla/5.0 AppleWebKit (KHTML, like Gecko) Chrome/ Safari/', // the default "WordPress..." user agent is rejected with some NGINX config
			)); // Uses WordPress HTTP API
			
			if ( is_wp_error($response) ) {
				trigger_error($response->get_error_message(), E_USER_WARNING);
			} elseif ( $response['response']['code'] != 200 ) {
				trigger_error($response['response']['message'], E_USER_WARNING);
			} else {
				$content_type = wp_remote_retrieve_header($response, 'content-type');
				if ( preg_match('/^text/', $content_type) ) {
					// Not a media
					trigger_error('Not a media', E_USER_WARNING);
				} else {
					file_put_contents($path, wp_remote_retrieve_body($response));
					$result = true;
				}
			}
			return $result;
		}
		
		/**
		 * Get the Magento default backorders status
		 * 
		 * @since 2.96.0
		 * 
		 * @return string Default backorders status
		 */
		private function get_default_backorders() {
			$config_backorders = $this->get_core_config('cataloginventory/item_options/backorders');
			$backorders = $this->map_backorders_status($config_backorders, 'no');
			return $backorders;
		}
		
		/**
		 * Allow the backorders or not
		 * 
		 * @param int $out_of_stock_value Out of stock value 0|1|2
		 * @param bool $use_config_backorders Use the default backorder or not
		 * @return string Default backorders status
		 */
		public function allow_backorders($out_of_stock_value, $use_config_backorders) {
			if ( $use_config_backorders ) {
				$backorders = $this->default_backorders;
			} else {
				$backorders = $this->map_backorders_status($out_of_stock_value, $this->default_backorders);
			}
			return $backorders;
		}
		
		/**
		 * Map the Magento backorders status with the WooCommerce one
		 * 
		 * @since 2.96.0
		 * 
		 * @param int $backorders_value Magento backorders status
		 * @param string $default_status WooCommerce default backorders status
		 * @return string WooCommerce backorders status
		 */
		private function map_backorders_status($backorders_value, $default_status) {
			switch ( $backorders_value ) {
				case 0: $backorders = 'no'; break;
				case 1: $backorders = 'yes'; break;
				case 2: $backorders = 'notify'; break;
				default: $backorders = $default_status;
			}
			return $backorders;
		}
		
		/**
		 * Set the product visibility in WooCommerce
		 * 
		 * @since 2.38.0
		 * 
		 * @param int $new_post_id Post ID
		 * @param int $visibility Magento visibility
		 */
		protected function set_product_visibility($new_post_id, $visibility) {
			switch ( $visibility ) {
				case 1: // Hidden
					wp_set_object_terms($new_post_id, $this->product_visibilities['exclude-from-search'], 'product_visibility', true);
					wp_set_object_terms($new_post_id, $this->product_visibilities['exclude-from-catalog'], 'product_visibility', true);
					break;
					
				case 2: // Catalog
					wp_set_object_terms($new_post_id, $this->product_visibilities['exclude-from-search'], 'product_visibility', true);
					break;
					
				case 3: // Search
					wp_set_object_terms($new_post_id, $this->product_visibilities['exclude-from-catalog'], 'product_visibility', true);
					break;
			}
		}
		
		/**
		 * Recount the items for a taxonomy
		 * 
		 * @return boolean
		 */
		private function terms_tax_count($taxonomy) {
			$terms = get_terms(array($taxonomy));
			// Get the term taxonomies
			$terms_taxonomies = array();
			foreach ( $terms as $term ) {
				$terms_taxonomies[] = $term->term_taxonomy_id;
			}
			if ( !empty($terms_taxonomies) ) {
				return wp_update_term_count_now($terms_taxonomies, $taxonomy);
			} else {
				return true;
			}
		}

		/**
		 * Recount the items for each category and tag
		 * 
		 * @return boolean
		 */
		private function terms_count() {
			$result = $this->terms_tax_count('category');
			$result |= $this->terms_tax_count('post_tag');
		}

		/**
		 * Returns the imported posts mapped with their Magento ID
		 *
		 * @since 3.10.0
		 * 
		 * @param string $meta_key Meta key (default = _fgm2wc_old_cms_id)
		 * @return array of post IDs [magento_post_id => wordpress_post_id]
		 */
		public function get_imported_magento_posts($meta_key = '_fgm2wc_old_cms_id') {
			global $wpdb;
			$posts = array();

			$sql = "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '$meta_key'";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$posts[$result->meta_value] = $result->post_id;
			}
			ksort($posts);
			return $posts;
		}

		/**
		 * Returns the imported products mapped with their Magento ID
		 *
		 * @since      1.10.0
		 * 
		 * @param bool $with_variation_id If true, get the variation ID, else get the product ID
		 * @return array of post IDs [magento_product_id => wordpress_post_id]
		 */
		public function get_imported_magento_products($with_variation_id=false) {
			global $wpdb;
			$posts = array();
			$matches = array();

			$sql = "
				SELECT pm.post_id, pm.meta_key, pm.meta_value, p.post_type, p.post_parent
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id 
				WHERE pm.meta_key LIKE '_fgm2wc_old_product_id%'
			";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				if ( preg_match('/-lang(.*)/', $result->meta_key, $matches) ) {
					$language = $matches[1];
				} else {
					$language = $this->default_language;
				}
				if ( $result->post_type == 'product' ) {
					// Product
					$posts[$language][$result->meta_value] = $result->post_id;
				} else {
					// Product variation
					if ( $with_variation_id ) {
						$posts[$language][$result->meta_value] = $result->post_id; // Take the product variation ID
					} else {
						$posts[$language][$result->meta_value] = $result->post_parent; // Take the product ID (= parent of the product variation)
					}
				}
			}
			ksort($posts);
			return $posts;
		}

		/**
		 * Returns the imported users mapped with their Magento ID
		 *
		 * @return array of user IDs [magento_user_id => wordpress_user_id]
		 */
		public function get_imported_magento_users() {
			return $this->get_users_by_meta_key('magento_user_id');
		}

		/**
		 * Returns the imported customers mapped with their Magento ID
		 *
		 * @since 2.10.0
		 * 
		 * @return array of user IDs [magento_customer_id => wordpress_user_id]
		 */
		public function get_imported_magento_customers() {
			return $this->get_users_by_meta_key('magento_customer_id');
		}

		/**
		 * Returns users by meta key
		 *
		 * @since 2.10.0
		 * 
		 * @param string $meta_key Meta key
		 * @return array of user IDs [magento_user_id => wordpress_user_id]
		 */
		private function get_users_by_meta_key($meta_key) {
			global $wpdb;
			$users = array();

			$sql = "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = '$meta_key'";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$users[$result->meta_value] = $result->user_id;
			}
			ksort($users);
			return $users;
		}

		/**
		 * Display the number of imported media
		 * 
		 */
		public function display_media_count() {
			$this->display_admin_notice(sprintf(_n('%d media imported', '%d medias imported', $this->media_count, 'fg-magento-to-woocommerce'), $this->media_count));
		}

		/**
		 * Test if a column exists
		 *
		 * @param string $table Table name
		 * @param string $column Column name
		 * @return bool
		 */
		public function column_exists($table, $column) {
			global $magento_db;

			$cache_key = 'fgm2wc_column_exists:' . $table . '.' . $column;
			$found = false;
			$column_exists = wp_cache_get($cache_key, '', false, $found);
			if ( $found === false ) {
				$column_exists = false;
				try {
					$prefix = $this->plugin_options['prefix'];
					$sql = "SHOW COLUMNS FROM ${prefix}${table} LIKE '$column'";
					$query = $magento_db->query($sql, PDO::FETCH_ASSOC);
					if ( $query !== false ) {
						$result = $query->fetch();
						$column_exists = !empty($result);
					}
				} catch ( PDOException $e ) {}
				
				// Store the result in cache for the current request
				wp_cache_set($cache_key, $column_exists);
			}
			return $column_exists;
		}

		/**
		 * Test if a table exists
		 *
		 * @param string $table Table name
		 * @return bool
		 */
		public function table_exists($table) {
			global $magento_db;

			$cache_key = 'fgm2wc_table_exists:' . $table;
			$found = false;
			$table_exists = wp_cache_get($cache_key, '', false, $found);
			if ( $found === false ) {
				$table_exists = false;
				try {
					$prefix = $this->plugin_options['prefix'];
					$sql = "SHOW TABLES LIKE '${prefix}${table}'";
					$query = $magento_db->query($sql, PDO::FETCH_ASSOC);
					if ( $query !== false ) {
						$result = $query->fetch();
						$table_exists = !empty($result);
					}
				} catch ( PDOException $e ) {}
				
				// Store the result in cache for the current request
				wp_cache_set($cache_key, $table_exists);
			}
			return $table_exists;
		}

		/**
		 * Returns the imported product ID corresponding to a Magento ID
		 *
		 * @since 2.3.0
		 * 
		 * @param int $magento_id Magento product ID
		 * @return int WordPress product ID
		 */
		public function get_wp_product_id_from_magento_id($magento_id) {
			$product_id = $this->get_wp_post_id_from_meta('_fgm2wc_old_product_id', $magento_id);
			return $product_id;
		}

		/**
		 * Returns the imported post ID corresponding to a meta key and value
		 *
		 * @since 2.3.0
		 * 
		 * @param string $meta_key Meta key
		 * @param string $meta_value Meta value
		 * @return int WordPress post ID
		 */
		public function get_wp_post_id_from_meta($meta_key, $meta_value) {
			global $wpdb;

			$sql = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s LIMIT 1";
			$post_id = $wpdb->get_var($wpdb->prepare($sql, $meta_key, $meta_value));
			return $post_id;
		}

		/**
		 * Get a Post ID from its GUID
		 * 
		 * @since 3.18.3
		 * 
		 * @global object $wpdb
		 * @param string $guid GUID
		 * @return int Post ID
		 */
		public function get_post_id_from_guid($guid) {
			global $wpdb;
			return $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s", $guid));
		}
		
		/**
		 * Get all the term metas corresponding to a meta key
		 * 
		 * @param string $meta_key Meta key
		 * @return array List of term metas: term_id => meta_value
		 */
		public function get_term_metas_by_metakey($meta_key) {
			global $wpdb;
			$metas = array();
			
			$sql = "SELECT term_id, meta_value FROM {$wpdb->termmeta} WHERE meta_key = %s";
			$results = $wpdb->get_results($wpdb->prepare($sql, $meta_key));
			foreach ( $results as $result ) {
				$metas[$result->meta_value] = $result->term_id;
			}
			ksort($metas);
			return $metas;
		}
		
		/**
		 * Get all the term metas corresponding to a meta key
		 * 
		 * @param string $meta_key Meta key
		 * @return array List of term metas: term_id => meta_value
		 */
		public function get_term_metas_by_metakey_like($meta_key) {
			global $wpdb;
			$metas = array();
			
			$sql = "SELECT term_id, meta_value FROM {$wpdb->termmeta} WHERE meta_key LIKE %s";
			$results = $wpdb->get_results($wpdb->prepare($sql, $meta_key));
			foreach ( $results as $result ) {
				$metas[$result->meta_value] = $result->term_id;
			}
			ksort($metas);
			return $metas;
		}
		
		/**
		 * Search a term by its slug (LIKE search)
		 * 
		 * @param string $slug slug
		 * @return int Term id
		 */
		public function get_term_id_by_slug($slug) {
			global $wpdb;
			return $wpdb->get_var($wpdb->prepare("
				SELECT term_id FROM $wpdb->terms
				WHERE slug LIKE %s
			",
			$slug));
		}

		/**
		 * Stop the import
		 * 
		 * @since 2.0.0
		 */
		public function stop_import() {
			update_option('fgm2wc_stop_import', true);
		}
		
		/**
		 * Test if the import needs to stop
		 * 
		 * @since 2.0.0
		 * 
		 * @return boolean Import needs to stop or not
		 */
		public function import_stopped() {
			return get_option('fgm2wc_stop_import');
		}
		
	}
}
