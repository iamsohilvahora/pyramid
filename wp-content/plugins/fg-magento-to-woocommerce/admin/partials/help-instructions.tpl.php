<div id="fgm2wc-help-instructions">

<h1>FG Magento to WooCommerce Instructions</h1>

<h2>Step 0:</h2>
<p>Before using the plugin, you must:
	<ul>
		<li>Define the WordPress permalinks on <a href="<?php echo admin_url('options-permalink.php'); ?>" target="_blank">the permalink screen</a><br />
		"Post name" is a good choice.</li>
		<li>Define the media sizes on <a href="<?php echo admin_url('options-media.php'); ?>" target="_blank">the media settings screen</a><br />
		The plugin will copy your Magento images to the WordPress media library and will resize them to all the sizes defined here.</li>
	</ul>
</p>

<h2>Step 1:</h2>
<h3>Empty the WordPress content</h3>
<p>This action is not mandatory the first time you run the import. But it is required if you have already ran an import and if you want to restart it from scratch. It will delete all the WordPress content (posts, pages, attachments, categories, tags, navigation menus, products, custom post types).</p>

<h2>Step 2:</h2>
<h3>Test the connection</h3>
<p>After having filled in the database parameters, you can test the connection to the Magento database. It will tell you how much data the plugin has found in the Magento database.</p>

<h2>Step 3:</h2>
<h3>Run the import</h3>
<p>After having chosen the different import options (see the options help tab), you click on this button to run the import. It can take a long time depending on the number of products and images in Magento.</p>
<p>If the screen becomes blank, let it turn until it finishes. Once the process is finished, it will display the import results.</p>
<p>If the process stops before having imported all the content, you can run it again and it will resume where it left off. This may happen if you have a timeout on your server or if the memory becomes low. In this case, ensure that the automatic removal checkbox is not checked.</p>

<h2>Import in command line by WP CLI <span class="fgm2wc_premium_feature">(Premium feature)</span></h2>
<p>The import in command line is much faster than the import by the browser.</p>
<p>You must first install WP CLI on your WordPress server. See the <a href="https://wp-cli.org/" target="_blank">WP CLI installation procedure</a>.</p>
<p>Before using the WP CLI commands, you must configure all the plugin settings in the WordPress backend.</p>
<p>Here are the WP CLI commands that you can use:
	<ul>
		<li><strong>wp import-magento empty</strong> : Empty the imported data</li>
		<li><strong>wp import-magento empty all</strong> : Empty all the WordPress data</li>
		<li><strong>wp import-magento test_database</strong> : Test the database connection</li>
		<li><strong>wp import-magento import</strong> : Import the data</li>
		<li><strong>wp import-magento update</strong> : Update the data</li>
	</ul>
</p>

<h2>Automatic import by cron <span class="fgm2wc_premium_feature">(Premium feature)</span></h2>
<p>If you want to update automatically the existing products and orders, and import the new data that may have changed in the Magento database, you can do it with a cron command.
	<ul>
		<li>First you need to set up correctly all the settings in the import screen. It is advised to run the first import manually to be sure that the settings are correct.</li>
		<li>Then define your crontab like:<br />
			<code>
				0 0 * * * php /path/to/wp/wp-content/plugins/fg-magento-to-woocommerce-premium/cron_import.php >>/dev/null
			</code><br />
			This will run the import once a day at 0:00.<br />
			You can of course change the frequency if you want.
		</li>
	</ul>
</p>

<?php do_action('fgm2wc_help_instructions'); ?>

</div>
