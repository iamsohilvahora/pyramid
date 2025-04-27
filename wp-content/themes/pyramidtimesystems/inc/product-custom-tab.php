<?php
	// Back-end
	/*********************** Add tab to Product Data area of Edit Product page ***********************/
	// Add a new 'Specification' tab to Product Data area of Edit Product page.

	add_filter('woocommerce_product_data_tabs', 'specification_instruction_product_data_tab');
	function specification_instruction_product_data_tab($tabs){
		// Specification tab
		$tabs['spec'] = array(
						'label'    => 'Specification',
						'target'   => 'specification_product_data',
						'priority' => 90,
					);

		// Download tab
		$tabs['download'] = array(
						'label'    => 'Download',
						'target'   => 'download_product_pdf',
						'priority' => 91,
					);

		return $tabs;
	}

	// Add the 'Specification' tab contents
	add_action('woocommerce_product_data_panels', 'Specification_instruction_data_panel', 100);
	function Specification_instruction_data_panel(){
	?>
		<div id="specification_product_data" class="panel woocommerce_options_panel">
			<div class="options_group spec_instruction">
		<?php
			$style = "width: 100%;height: 15em;";
		    woocommerce_wp_textarea_input(array(
		    	'id' => '_specification_instruction', 
		    	'label' => 'Specs',
		    	'desc_tip' => 'true', 
		    	'name' => '_specification_instruction',
		    	'style' => $style,
		    ));
		?>
			</div>
		</div>
		<?php
	}

	// Add the 'Download' tab contents.
	add_action('woocommerce_product_data_panels', 'download_product_pdf_panel', 101);
	function download_product_pdf_panel(){
	?>
		<div id="download_product_pdf" class="options_group panel woocommerce_options_panel">
			<div class="form-field downloadable_files options_group pdf_instruction">
				<label>Downloadable files</label>
				<table class="">
					<thead>
						<tr>
							<th class="sort">&nbsp;</th>
							<th><?php esc_html_e( 'Name', 'woocommerce' ); ?> <?php echo wc_help_tip( __( 'This is the name of the download shown to the customer.', 'woocommerce' ) ); ?></th>
							<th colspan="2"><?php esc_html_e( 'File URL', 'woocommerce' ); ?> <?php echo wc_help_tip( __( 'This is the URL or absolute path to the file which customers will get access to. URLs entered here should already be encoded.', 'woocommerce' ) ); ?></th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$wc_pdf_names = get_post_meta(get_the_id(), '_wc_file_names_pdf', true);
						$wc_pdf_urls = get_post_meta(get_the_id(), '_wc_file_urls_pdf', true);
						
						array_map(function ($name_key, $name_value, $url_key, $url_value){
						    if($name_key == $url_key):
						    ?>
						    <tr>
						    	<td class="sort"></td>
						    	<td class="file_name">
						    		<input type="text" class="input_text" placeholder="<?php esc_attr_e( 'File name', 'woocommerce' ); ?>" name="_wc_file_names_pdf[]" value="<?php echo esc_attr( $name_value ); ?>" />
						    		<!-- <input type="hidden" name="_wc_file_hashes_pdf['name'][]" value="<?php // echo esc_attr( $key ); ?>" /> -->
						    	</td>
						    	<td class="file_url"><input type="text" class="input_text" placeholder="<?php esc_attr_e( 'http://', 'woocommerce' ); ?>" name="_wc_file_urls_pdf[]" value="<?php echo esc_attr( $url_value ); ?>" /></td>
						    	<td class="file_url_choose" width="1%"><a href="#" class="button upload_file_button" data-choose="<?php esc_attr_e( 'Choose file', 'woocommerce' ); ?>" data-update="<?php esc_attr_e( 'Insert file URL', 'woocommerce' ); ?>"><?php echo esc_html__( 'Choose file', 'woocommerce' ); ?></a></td>
						    	<td width="1%"><a href="#" class="delete"><?php esc_html_e( 'Delete', 'woocommerce' ); ?></a></td>
						    </tr>

						<?php 
							endif;
							},array_keys($wc_pdf_names), array_values($wc_pdf_names), array_keys($wc_pdf_urls), array_values($wc_pdf_urls));
						?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="5">
								<a href="#" class="button insert" data-row="
								<?php
									$key  = '';
									$file = array(
										'file' => '',
										'name' => '',
									);
									ob_start(); ?>
									<tr>
										<td class="sort"></td>
										<td class="file_name">
											<input type="text" class="input_text" placeholder="<?php esc_attr_e( 'File name', 'woocommerce' ); ?>" name="_wc_file_names_pdf[]" value="<?php echo esc_attr( $file['name'] ); ?>" />
											<!-- <input type="hidden" name="_wc_file_hashes_pdf[]" value="<?php // echo esc_attr( $key ); ?>" /> -->
										</td>
										<td class="file_url"><input type="text" class="input_text" placeholder="<?php esc_attr_e( 'http://', 'woocommerce' ); ?>" name="_wc_file_urls_pdf[]" value="<?php echo esc_attr( $file['file'] ); ?>" /></td>
										<td class="file_url_choose" width="1%"><a href="#" class="button upload_file_button" data-choose="<?php esc_attr_e( 'Choose file', 'woocommerce' ); ?>" data-update="<?php esc_attr_e( 'Insert file URL', 'woocommerce' ); ?>"><?php echo esc_html__( 'Choose file', 'woocommerce' ); ?></a></td>
										<td width="1%"><a href="#" class="delete"><?php esc_html_e( 'Delete', 'woocommerce' ); ?></a></td>
									</tr>

								<?php	echo esc_attr( ob_get_clean() );
									?>
								"><?php esc_html_e( 'Add File', 'woocommerce' ); ?></a>
							</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<?php
	}

	// Save 'Specification' and 'Download' data to post meta.
	add_action('save_post_product', 'specification_save_data');
	function specification_save_data($product_id){
	    global $pagenow, $typenow;
	    if ( 'post.php' !== $pagenow || 'product' !== $typenow ) return;
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	    // Save Specification data
	    if(isset( $_POST['_specification_instruction'])){
			if ( $_POST['_specification_instruction'] ){
					update_post_meta( $product_id, '_specification_instruction', $_POST['_specification_instruction'] );
			}
	    } 
	    else{
			delete_post_meta( $product_id, '_specification_instruction' );
		}

		// Save download pdf data 
		// check pdf file name
		$pdf_names = array_filter($_POST['_wc_file_names_pdf']);
		if(count($pdf_names)){
			update_post_meta($product_id, '_wc_file_names_pdf', $pdf_names);	
		}
		else{
			delete_post_meta($product_id, '_wc_file_names_pdf', $pdf_names);
		}

		// check pdf urls
		$pdf_url = array_filter($_POST['_wc_file_urls_pdf']);
		if(count($pdf_url)){
			update_post_meta($product_id, '_wc_file_urls_pdf', $pdf_url);	
		}
		else{
			delete_post_meta($product_id, '_wc_file_urls_pdf', $pdf_url);
		}
	}

?>