				<tr>
					<th scope="row" colspan="2"><h3><?php _e('Behavior', 'fg-magento-to-woocommerce'); ?></h3></th>
				</tr>
				<tr>
					<th scope="row"><?php _e('Media:', 'fg-magento-to-woocommerce'); ?></th>
					<td><input id="skip_media" name="skip_media" type="checkbox" value="1" <?php checked($data['skip_media'], 1); ?> /> <label for="skip_media" ><?php _e('Skip media', 'fg-magento-to-woocommerce'); ?></label>
					<br />
					<div id="media_import_box">
						<?php _e('Import first image:', 'fg-magento-to-woocommerce'); ?>&nbsp;
						<input id="first_image_as_is" name="first_image" type="radio" value="as_is" <?php checked($data['first_image'], 'as_is'); ?> /> <label for="first_image_as_is" title="<?php _e('The first image will be kept in the post content', 'fg-magento-to-woocommerce'); ?>"><?php _e('as is', 'fg-magento-to-woocommerce'); ?></label>&nbsp;&nbsp;
						<input id="first_image_as_featured" name="first_image" type="radio" value="as_featured" <?php checked($data['first_image'], 'as_featured'); ?> /> <label for="first_image_as_featured" title="<?php _e('The first image will be removed from the post content and imported as the featured image only', 'fg-magento-to-woocommerce'); ?>"><?php _e('as featured only', 'fg-magento-to-woocommerce'); ?></label>&nbsp;&nbsp;
						<input id="first_image_as_is_and_featured" name="first_image" type="radio" value="as_is_and_featured" <?php checked($data['first_image'], 'as_is_and_featured'); ?> /> <label for="first_image_as_is_and_featured" title="<?php _e('The first image will be kept in the post content and imported as the featured image', 'fg-magento-to-woocommerce'); ?>"><?php _e('as is and as featured', 'fg-magento-to-woocommerce'); ?></label>
						<br />
						<input id="skip_thumbnails" name="skip_thumbnails" type="checkbox" value="1" <?php checked($data['skip_thumbnails'], 1); ?> /> <label for="skip_thumbnails"><?php _e("Don't generate the thumbnails", 'fg-magento-to-woocommerce'); ?></label>
						<br />
						<input id="import_external" name="import_external" type="checkbox" value="1" <?php checked($data['import_external'], 1); ?> /> <label for="import_external"><?php _e('Import the external media stored outside your site', 'fg-magento-to-woocommerce'); ?></label>
						<br />
						<input id="import_duplicates" name="import_duplicates" type="checkbox" value="1" <?php checked($data['import_duplicates'], 1); ?> /> <label for="import_duplicates" title="<?php _e('Checked: download the media with their full path in order to import media with identical names.', 'fg-magento-to-woocommerce'); ?>"><?php _e('Import the media with duplicate names', 'fg-magento-to-woocommerce'); ?></label>
						<br />
						<input id="force_media_import" name="force_media_import" type="checkbox" value="1" <?php checked($data['force_media_import'], 1); ?> /> <label for="force_media_import" title="<?php _e('Checked: download the media even if it has already been imported. Unchecked: Download only media which were not already imported.', 'fg-magento-to-woocommerce'); ?>" ><?php _e('Force media import. <small>Keep unchecked except if you had previously some media download issues.</small>', 'fg-magento-to-woocommerce'); ?></label>
						<br />
						<input id="first_image_not_in_gallery" name="first_image_not_in_gallery" type="checkbox" value="1" <?php checked($data['first_image_not_in_gallery'], 1, 1); ?> /> <label for="first_image_not_in_gallery"><?php _e("Don't include the first image into the product gallery", 'fg-magento-to-woocommerce'); ?></label>
						<br />
						<?php _e('Timeout for each media:', 'fg-magento-to-woocommerce'); ?>&nbsp;
						<input id="timeout" name="timeout" type="text" size="5" value="<?php echo esc_attr($data['timeout']); ?>" /> <?php _e('seconds', 'fg-magento-to-woocommerce'); ?>
					</div></td>
				</tr>
				<tr><th><?php _e('Import prices:', 'fg-magento-to-woocommerce'); ?></th>
					<td>
						<input type="radio" name="price" id="price_without_tax" value="without_tax" <?php checked($data['price'], 'without_tax', 1); ?> /><label for="price_without_tax"><?php _e('excluding tax', 'fg-magento-to-woocommerce'); ?></label>&nbsp;
						<input type="radio" name="price" id="price_with_tax" value="with_tax" <?php checked($data['price'], 'with_tax', 1); ?> /><label for="price_with_tax"><?php _e('including tax <small>in this case, you must define a default tax rate before running the import</small>', 'fg-magento-to-woocommerce'); ?></label>
					</td>
				</tr>
				<tr><th><?php _e('Sale prices:', 'fg-magento-to-woocommerce'); ?></th>
					<td>
						<input type="radio" name="sale_price" id="sale_price_special" value="special" <?php checked($data['sale_price'], 'special', 1); ?> /><label for="sale_price_special"><?php _e('use the special price as sale price', 'fg-magento-to-woocommerce'); ?></label>&nbsp;
						<input type="radio" name="sale_price" id="sale_price_msrp" value="msrp" <?php checked($data['sale_price'], 'msrp', 1); ?> /><label for="sale_price_msrp"><?php _e("use the manufacturer's suggested retail price as regular price", 'fg-magento-to-woocommerce'); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Stock management:', 'fg-magento-to-woocommerce'); ?></th>
					<td>
						<input id="stock_management" name="stock_management" type="checkbox" value="1" <?php checked($data['stock_management'], 1); ?> /> <label for="stock_management" ><?php _e('Enable stock management', 'fg-magento-to-woocommerce'); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Create pages:', 'fg-magento-to-woocommerce'); ?></th>
					<td><input id="import_as_pages" name="import_as_pages" type="checkbox" value="1" <?php checked($data['import_as_pages'], 1); ?> /> <label for="import_as_pages" ><?php _e('Import as pages instead of blog posts (without categories)', 'fg-magento-to-woocommerce'); ?></label></td>
				</tr>
