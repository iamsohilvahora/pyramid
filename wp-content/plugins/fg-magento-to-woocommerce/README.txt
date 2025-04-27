=== FG Magento to WooCommerce ===
Contributors: Kerfred
Plugin Uri: https://wordpress.org/plugins/fg-magento-to-woocommerce/
Tags: magento, woocommerce, import, importer, convert magento to wordpress, migrate magento to wordpress, migration, migrator, converter, wpml, dropshipping
Requires at least: 4.5
Tested up to: 5.8.2
Stable tag: 3.20.4
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=fred%2egilles%40free%2efr&lc=FR&item_name=fg-magento-to-woocommerce&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted

A plugin to migrate your Magento e-commerce store to WooCommerce

== Description ==

This plugin migrates your Magento products and CMS pages to WooCommerce.

It has been tested with **Magento versions 1.3 to 2.3** and **WordPress 5.8**. It is compatible with multisite installations.

The plugin migrates:

* the product categories
* the product categories images
* the products
* the products tags
* the product thumbnails
* the product images galleries
* the product stocks
* the CMS

No need to subscribe to an external web site.

= FG Magento to WooCommerce Premium version =

The **Premium version** includes these extra features:

* migrates the product attributes
* migrates the product variations
* migrates the grouped products
* migrates the products Up Sell and Cross Sell
* migrates the users
* migrates the customers
* authenticate the users and the customers in WordPress with their Magento passwords
* migrates the orders
* migrates the ratings and reviews
* migrates the discount coupons
* migrates the SEO meta data
* migrates the tax classes
* SEO: redirects the Magento URLs
* multisites/multistores: Option to choose which website/store to import
* update the already imported products stocks and orders
* compatible with Magento Enterprise Edition
* ability to run the import automatically from the cron (for dropshipping for example)
* WP CLI support

The Premium version can be purchased on: [https://www.fredericgilles.net/fg-magento-to-woocommerce/](https://www.fredericgilles.net/fg-magento-to-woocommerce/)

= Add-ons for FG Magento to WooCommerce Premium =

The Premium version allows the use of add-ons that enhance functionality:

* Multilingual Magento stores converted to WooCommerce + WPML
* Move Magento order numbers
* Move Magento customer groups
* Move Magento manufacturers
* Move Magento product options as add-ons
* Move Magento costs
* Move Magento custom order statuses
* Move Magento bundle products
* Move Magento wish lists
* Move Magento tiered prices
* Move Magento AW Blog

== Installation ==

1.  Install the plugin in the Admin => Plugins menu => Add New => Upload => Select the zip file => Install Now
2.  Activate the plugin in the Admin => Plugins menu
3.  Run the importer in Tools > Import > Magento
4.  Configure the plugin settings. You can find the Magento database parameters in the Magento file app/etc/local.xml<br />
    Hostname = host<br />
    Port     = 3306 (standard MySQL port)<br />
    Database = dbname<br />
    Username = username<br />
    Password = password<br />
    Table prefix = table_prefix<br />

== Frequently Asked Questions ==

= I get the message: "[fg-magento-to-woocommerce] Couldn't connect to the Magento database. Please check your parameters. And be sure the WordPress server can access the Magento database. SQLSTATE[28000] [1045] Access denied for user 'xxx'@'localhost' (using password: YES)" =

* First verify your login and password to your Magento database.
* If Magento and WordPress are not installed on the same host, you can do this:
- export the Magento database to a SQL file (with phpMyAdmin for example)
- import this SQL file on the same database as WordPress
- run the migration by using WordPress database credentials (host, user, password, database) instead of the Magento ones in the plugin settings.

= The migration stops and I get the message: "Fatal error: Allowed memory size of XXXXXX bytes exhausted" or I get the message: "Internal server error" =

* First, deactivate all the WordPress plugins except the ones used for the migration
* You can run the migration again. It will continue where it stopped.
* You can add: `define('WP_MEMORY_LIMIT', '1G');` in your wp-config.php file to increase the memory allowed by WordPress
* You can also increase the memory limit in php.ini if you have write access to this file (ie: memory_limit = 1G).

= I get a blank screen and the import seems to be stopped =

* Same as above

= The media are not imported =

* Check the URL field that you filled in the plugin settings. It must be your Magento home page URL and must start with http://

= The media are not imported and I get the error message: "Warning: copy() [function.copy]: URL file-access is disabled in the server configuration" =

* The PHP directive "Allow URL fopen" must be turned on in php.ini to copy the medias. If your remote host doesn't allow this directive, you will have to do the migration on localhost.

= I get the message: "Fatal error: Class 'PDO' not found" =

* PDO and PDO_MySQL libraries are needed. You must enable them in php.ini on the WordPress host.<br />
Or on Ubuntu:<br />
sudo php5enmod pdo<br />
sudo service apache2 reload

= I get this error: PHP Fatal error: Undefined class constant 'MYSQL_ATTR_INIT_COMMAND' =

* You have to enable PDO_MySQL in php.ini on the WordPress host. That means uncomment the line extension=pdo_mysql.so in php.ini

= Does the migration process modify the Magento site it migrates from? =

* No, it only reads the Magento database.

= Do I need to keep the plugin activated after the migration? =

* No, you can deactivate or even uninstall the plugin after the migration.

= Is there a log file to show the information from the import? =
* Yes. First you must put these lines in wp-config.php:<br />
define('WP_DEBUG', true);<br />
define('WP_DEBUG_LOG', true);<br />
And the messages will be logged to wp-content/debug.log.


Don't hesitate to let a comment on the [forum](https://wordpress.org/support/plugin/fg-magento-to-woocommerce) or to report bugs if you found some.

== Screenshots ==

1. Parameters screen

== Translations ==
* English (default)
* French (fr_FR)
* Brazilian (pt_BR)
* other can be translated

== Changelog ==

= 3.20.4 =
* Tested with WordPress 5.8.2

= 3.19.1 =
* Fixed: Extra parameter with the function update_post_meta()

= 3.18.3 =
* Fixed: Avoid duplicating the images if they are already imported

= 3.18.2 =
* Fixed: Some variables were not escaped before displaying
* Tested with WordPress 5.8

= 3.18.0 =
* Change: Manage the stock if "Manage stock" is not set in Magento
* Tested with WordPress 5.7.2

= 3.17.4 =
* Fixed: Images not imported for the Magento sites that require the cookie "__cfduid"

= 3.17.2 =
* Tested with WordPress 5.7.1

= 3.17.1 =
* Tested with WordPress 5.7

= 3.17.0 =
* Tweak: Remove the "wp_insert_post" that consumes a lot of CPU and memory
* Tweak: Refactoring

= 3.16.0 =
* New: Import the products sort order

= 3.15.0 =
* Tested with WordPress 5.6.2
* Tested with WooCommerce 5

= 3.14.0 =
* Tested with WordPress 5.6.1

= 3.13.1 =
* Fixed: Images inserted in the post content with width and height = 0 when the option "Don't generate the thumbnails" is selected

= 3.13.0 =
* New: Add documentation about WP CLI
* Fixed: Plugin and add-ons not displayed in the debug informations on Windows

= 3.12.0 =
* Fixed: Plugin and add-ons not shown on the Debug Info tab if the plugins are not installed in the standard plugins directory

= 3.11.2 =
* Fixed: JQuery Migrate warning: jQuery.fn.load() is deprecated
* Tested with WordPress 5.6

= 3.11.1 
* Fixed: Notice: Undefined index: description

= 3.11.0 =
* New: Ability to change the default import timeout by adding `define('IMPORT_TIMEOUT', 7200);` in the wp-config.php file
* Fixed: Character " not displayed in the settings
* Tested with WordPress 5.5.3

= 3.10.0 =
* New: Check if we need the AW Blog add-on
* New: Add the function get_imported_magento_posts()

= 3.9.0 =
* Fixed: Progress bar at 0% if the site is in https and the WordPress general settings are in http
* Tested with WordPress 5.5.1

= 3.7.0 =
* Compatible with WordPress 5.5
* Fixed: Timezone was not the same between the start and the end time in the logs

= 3.6.0 =
* New: Add an option to not generate the images thumbnails
* New: Make the max_allowed_packet human readable
* Change: Set the default media timeout to 20 seconds
* Fixed: Import counter exceeds 100% when we click multiple times on the import button

= 3.5.0 =
* New: Import the product category thumbnails

= 3.2.0 =
* New: Add the hook "fgm2wc_post_get_full_product"

= 3.1.2 =
* Fixed: Notice: Trying to access array offset on value of type int

= 3.1.0 =
* New: Display the PHP errors in the logs

= 3.0.0 =
* New: Add an help tab
* New: Add a debug info tab

= 2.99.1 =
* Tweak: Refactoring
* Tested with WordPress 5.4.2

= 2.98.1 =
* Fixed: Deprecated function update_woocommerce_term_meta

= 2.96.1 =
* Fixed: Sale price was set as current price even if the sale period is ended

= 2.96.0 =
* New: Use the config backorder status
* Fixed: Products on backorder had the status "out of stock"

= 2.95.2 =
* Fixed: Import hangs on PHP installations where the function "exif_read_data" is not available

= 2.95.0 =
* New: Check if we need the Tiered Prices add-on
* New: Add the hook "fgm2wc_product_images_subdirectory"
* Fixed: Wrong stock status

= 2.94.1 =
* Fixed: Images not imported (regression from 2.94.0)

= 2.94.0 =
* New: Add the hook "fgm2wc_get_product_images"
* Tweak: Add the subdirectory /catalog/product only for relative paths of images

= 2.93.1 =
* Tested with WordPress 5.4.1

= 2.93.0 =
* New: Check if we need the Wish Lists add-on

= 2.92.0 =
* Fixed: The stock management for each product was not managed exactly as in Magento

= 2.91.1 =
* Tested with WordPress 5.4

= 2.91.0 =
* Fixed: Logs not displayed

= 2.89.1 =
* Fixed: Images not imported
* Tested with WooCommerce 4.0

= 2.89.0 =
* Fixed: Warning: exif_read_data(): File not supported

= 2.86.1 =
* Fixed: Some JPEG images had a wrong orientation

= 2.85.1 =
* Fixed: Logs were not displayed due to mod_security

= 2.85.0 =
* Fixed: Notice: date_default_timezone_set(): Timezone ID '' is invalid
* Tested with WordPress 5.3.2

= 2.82.2 =
* Fixed: Product images were duplicated when importing a store different from the main one

= 2.82.0 =
* Tested with WordPress 5.3.1

= 2.81.0 =
* New: Add the hooks "fgm2wc_get_products_sql", "fgm2wc_get_products" and "fgm2wc_get_products_count_sql"
* Tested with WordPress 5.2.4

= 2.79.0 =
* Fixed: Logs were not displayed if the URL is wrong in the WordPress general settings

= 2.78.1 =
* Tested with Magento 2.3

= 2.78.0 =
* New: Download the media even if they are redirected

= 2.76.0 =
* Fixed: [ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'core_store' doesn't exist
* Tested with WordPress 5.2.3

= 2.73.0 =
* New: Translate the plugin in Brazilian (thanks to A. Costa)
* New: Import the product tags

= 2.72.0 =
* New: Add the hook "fgm2wc_pre_import_products"

= 2.71.0 =
* New: Update the WooCommerce product meta lookup table
* Tested with WordPress 5.2.2

= 2.70.0 =
* New: Add an option to manage the stock or not. Used if the stock is not managed in Magento but the products must be in stock in WooCommerce

= 2.68.0 =
* Tweak: Unset the default product category when emptying WordPress content
* Tested with WordPress 5.2.1

= 2.67.0 =
* Tested with WordPress 5.2

= 2.66.1 =
* Fixed: Price = 0 for some product bundles

= 2.66.0 =
* Tweak: Can manage the imported products in different languages (required for the WPML add-on)
* Tested with WordPress 5.1.1

= 2.65.1 =
* Fixed: Line breaks were removed in the product description
* Tested with WordPress 5.1

= 2.65.0 =
* New: Add the fgm2wc_get_other_fields hook

= 2.64.5 =
* Fixed: Prevent negative stock values
* Fixed: Notice: Undefined index: status

= 2.64.1 =
* Fixed: Product sorting by popularity didn't work

= 2.64.0 =
* New: Check if we need the Product Bundles add-on
* Tested with WordPress 5.0.3

= 2.63.1 =
* Fixed: Products without title were not imported

= 2.62.0 =
* Fixed: Some NGINX servers were blocking the images downloads
* Tested with WordPress 5.0.2

= 2.61.0 =
* Tested with WordPress 5.0

= 2.59.4 =
* Fixed: Images not imported because of a missing starting slash

= 2.59.3 =
* Fixed: Some category images were not imported

= 2.59.2 =
* Fixed: Avoid importing products as duplicates

= 2.58.0 =
* New: Generate the audio and video meta data (ID3 tag, featured image)
* Fixed: Set the price = 0 for the bundle products
* Fixed: Set manage_stock = no for the bundle products

= 2.57.2 =
* Fixed: Some Magento 2 product stocks were not imported
* Tweak: Cache some database results to increase import speed

= 2.57.1 =
* Fixed: Don't remove the WooCommerce pages associations when we delete only the imported data

= 2.57.0 =
* Fixed: Wrong products pagination with out of stock products

= 2.55.2 =
* Fixed: Regression from 2.55.1: products not imported on Magento < 2

= 2.55.1 =
* Fixed: Products may be imported as duplicates

= 2.52.0 =
* Tested with WordPress 4.9.8

= 2.50.1 =
* Fixed: WordPress database error: [Cannot truncate a table referenced in a foreign key constraint (`wp_wc_download_log`, CONSTRAINT `fk_wc_download_log_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `wp_woocommerce_downloadable_product_permission)]

= 2.50.0 =
* Fixed: Empty the WooCommerce wc_download_log and woocommerce_downloadable_product_permissions tables upon database emptying
* Change: Wording of the label "Remove only previously imported data"
* Tested with WordPress 4.9.7

= 2.46.2 =
* Tweak: Delete the wc_var_prices transient when emptying WordPress data
* Tested with WordPress 4.9.5

= 2.46.1 =
* Fixed: Fatal error: Uncaught Error: Cannot use object of type WP_Error as array

= 2.46.0 =
* New: Allow the import of the Magento 2 brands (with the Brands add-on)
* Fixed: Media path was wrong on some Magento 2 sites

= 2.45.4 =
* Fixed: Notice: Undefined index: short_description

= 2.45.3 =
* Fixed: Media not imported for some Magento 2 sites
* Fixed: [ERROR] Error:SQLSTATE[42S02]: Base table or view not found: 1146 Table 'sales_flat_order' doesn't exist

= 2.45.0 =
* New: Import the media shortcodes like {{media url="filename.jpg"}}
* Tested with WordPress 4.9.4

= 2.44.0 =
* Tweak: Use WP_IMPORTING

= 2.42.0 =
* New: Check if we need the Custom Order Statuses add-on

= 2.41.0 =
* New: Set the "Manage stock" checkbox according to the Magento "manage stock" value
* New: Put the stock status as "in stock" when the product stock is not managed
* Tested with WordPress 4.9.1

= 2.38.2 =
* Fixed: The variations were imported as simple products (Magento < 1.4)
* Tested with WordPress 4.9

= 2.38.0 =
* New: Make the products visibility compatible with WooCommerce 3

= 2.37.1 =
* Fixed: Categories imported in wrong language

= 2.37.0 =
* New: Check if we need the Product Options add-on
* New: Sanitize the media file names

= 2.36.0 =
* Tested with WordPress 4.8.2

= 2.35.1 =
* Fixed: Categories with duplicated names were not imported

= 2.35.0 =
* Fixed: Security cross-site scripting (XSS) vulnerability in the Ajax importer

= 2.34.0 =
* New: Compatible with Magento 2.x
* Fixed: CMS articles may be imported as duplicates
* Improvement: Import speed optimization
* Tested with WordPress 4.8.1

= 2.32.0 =
* New: Allow HTML in term descriptions

= 2.31.0 =
* New: Import the image caption in the media attachment page

= 2.29.0 =
* New: Block the import if the URL field is empty and if the media are not skipped
* New: Add error messages and information

= 2.28.0 =
* New: Add the percentage in the progress bar
* New: Display the progress and the log when returning to the import page
* Change: Restyling the progress bar
* Fixed: Typo - replace "complete" by "completed"
* Tested with WordPress 4.8

= 2.26.2 =
* Tested with WordPress 4.7.5

= 2.24.1 =
* Tweak: Code refactoring

= 2.24.0 =
* Fixed: Duplicated image in the product gallery

= 2.23.0 =
* New: Import the products visibility

= 2.22.0 =
* Tweak: Clear WooCommerce transients when emptying WordPress content
* Tested with WordPress 4.7.3

= 2.21.3 =
* Fixed: Notice: Undefined index: name

= 2.20.3 =
* Fixed: Term meta data not deleted when we delete the imported data only

= 2.20.0 =
* Tweak: Add a hook after testing the database connection

= 2.19.0 =
* Tested with WordPress 4.7.2

= 2.18.1 =
* Change: Import the Manufacturer's Suggested Retail Price as the regular price instead of the sale price

= 2.18.0 =
* New: Add an option to import the Special Price or the Manufacturer's Suggested Retail Price

= 2.17.0 =
* New: Import the length, width and height as shipping attributes
* Tweak: Code refactoring

= 2.16.1 =
* Tested with WordPress 4.7

= 2.16.0 =
* New: Import the default web site

= 2.15.1 =
* Fixed: Existing images attached to imported products were removed when deleting the imported data

= 2.15.0 =
* Fixed: The child products which are visible individually were not imported

= 2.14.8 =
* Fixed: Wrong progress bar color

= 2.14.7 =
* Fixed: The progress bar didn't move during the first import
* Fixed: The log window was empty during the first import

= 2.14.6 =
* Fixed: The "IMPORT COMPLETE" message was still displayed when the import was run again

= 2.14.5 =
* Fixed: Database passwords containing "<" were not accepted
* Tweak: Code refactoring

= 2.14.2 =
* Fixed: Wrong number of product categories displayed

= 2.14.1 =
* Tweak: If the import is blocked, stop sending AJAX requests

= 2.14.0 =
* New: Authorize the connections to Web sites that use invalid SSL certificates

= 2.13.0 =
* New: Option to delete only the new imported data

= 2.12.3 =
* Fixed: MySQL 5.7 incompatibility: [ERROR] Error:SQLSTATE[HY000]: General error: 3065 Expression #1 of ORDER BY clause is not in SELECT list, references column 'gv.position' which is not in SELECT list; this is incompatible with DISTINCT

= 2.12.2 =
* Fixed: Some images were duplicated in the product gallery

= 2.12.1 =
* Fixed: Review link broken

= 2.12.0 =
* New: Display the needed add-ons during the database testing and before importing
* Fixed: Wrong number of comments displayed
* Tested with WordPress 4.6.1

= 2.11.0 =
* New: Display the number of data found in the Magento database before importing

= 2.10.0 =
* Tweak: Code optimization

= 2.9.0 =
* Tested with WordPress 4.6

= 2.6.2 =
* Fixed: WordPress database error: [You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'xxx' yyy' LIMIT 1' at line 4

= 2.4.0 =
* Fixed: CMS pages from all languages were imported
* Fixed: Notice: Undefined index: name
* Tweak: Refactor some functions to allow multilingual import by the WPML addon

= 2.3.0 =
* Fixed: PHP Notice: Object of class WP_Error could not be converted to int
* Fixed: Notice: Undefined index: url_key

= 2.2.0 =
* New: Import the product featured images

= 2.1.0 =
* Fixed: Display an error message when the process hangs
* Tweak: Increase the speed of counting the terms
* Tested with WordPress 4.5.3

= 2.0.0 =
* New: Run the import in AJAX
* New: Add a progress bar
* New: Add a logger frame to see the logs in real time
* New: Ability to stop the import
* New: Compatible with PHP 7
* New: Compatible with WooCommerce 2.6.0

= 1.13.4 =
* Fixed: The products without stock were not imported

= 1.13.2 =
* Fixed: Products belonging to several bundles were imported as duplicates

= 1.13.1 =
* Fixed: Some descriptions were not imported correctly
* Tested with WordPress 4.5.2

= 1.12.1 =
* Fixed: Compatibility issues with Magento 1.3
* Tested with WordPress 4.5.1

= 1.12.0 =
* Tested with WordPress 4.5

= 1.10.2 =
* Fixed: Notice: Undefined index: short_description
* Fixed: Column 'post_excerpt' cannot be null

= 1.10.1 =
* Fixed: Products not imported. Error: "WordPress database error Column 'post_content' cannot be null"

= 1.9.1 =
* Tested with WordPress 4.4.2

= 1.8.4 =
* Tested with WordPress 4.4.1

= 1.8.2 =
* Fixed: Fatal error: Call to undefined function add_term_meta()

= 1.8.1 =
* Fixed: Better clean the taxonomies cache

= 1.8.0 =
* Tweak: Optimize the termmeta table

= 1.7.0 =
* Tweak: Use the WordPress 4.4 term metas

= 1.6.1 =
* Tested with WordPress 4.4

= 1.6.0 =
* New: Compatibility with Magento 1.3

= 1.5.0 =
* New: Add a link to the FAQ in the connection error message

= 1.4.0 =
* New: Add an Import link on the plugins list page
* New: Change the translation domain name to be compliant with the WordPress translation system
* Tweak: Code refactoring

= 1.3.1 =
* Fixed: Refresh the display of the product categories
* Fixed: Error: 1054 Unknown column 'e.store_id' in 'where clause'

= 1.2.1 =
* Fixed: Duplicate images
* Fixed: Avoid a double slash in the media filename
* Fixed: Import the original category name instead of the translation
* Fixed: Notice: Undefined index: url_key

= 1.2.0 =
* New: Compatible with Magento 1.4 to 1.9
* New: Support the table prefix
* Fixed: Don't import the child products as single products

= 1.1.0 =
* Tweak: Change the range of get_attribute_values()
* Tweak: Make the argument $required_attributes optional
* Premium version released

= 1.0.1 =
* Tested with WordPress 4.3.1

= 1.0.0 =
* Initial version: Import Magento product categories, products, images and CMS

== Upgrade Notice ==

= 3.20.4 =
Tested with WordPress 5.8.2
