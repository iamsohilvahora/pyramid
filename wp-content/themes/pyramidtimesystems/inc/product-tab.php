<?php
	// Front-end
	// Callback function for display product specification
	function func_display_product_specs(){
		global $product;

	    if('variable' != $product->get_type() && $care_instruction = get_post_meta($product->get_id(), '_specification_instruction', true)){
	        echo $care_instruction;
	    }
	}

	// Calback function for display download pdf
	function func_download_template(){ 
		global $product;
		$downloads = $product->get_downloads();
	?>		
		<div id="pdf_box">
		    <div class="pdf_data">
		      <ul class="pdf_file">
		           <?php
		           		$wc_pdf_names = get_post_meta(get_the_id(), '_wc_file_names_pdf', true);
		           		$wc_pdf_urls = get_post_meta(get_the_id(), '_wc_file_urls_pdf', true);
		           		
		           		array_map(function($name_key, $name_value, $url_key, $url_value){
		           		    $ProductID = attachment_url_to_postid($url_value);
		           			$path = get_attached_file($ProductID);
		           			$size = size_format(filesize($path));
		           			
		           		    if($name_key == $url_key):
		           	?>
		           <li>
		            <table width="100%">
		                <tbody>
							<tr>
								<td width="80%"> 
									<img src="http://pyramidtimesystems.demo1.bytestechnolab.com/wp-content/uploads/2022/01/pdf-file.png">            
										<a align="absmiddle" href="<?php echo $url_value; ?>" target="_blank"><?php echo $name_value; ?>
										</a>
										<span>(<?php echo $size; ?>)</span>
								</td>
								<td width="20%" style="text-align:center;">
									<a align="absmiddle" class="download-file" download="<?php echo $name_value; ?>" title="<?php echo $name_value; ?>" href="<?php echo $url_value; ?>">Download
									</a>
								</td>
							</tr>
		                 </tbody>
		             </table>
		          </li>
		          <?php
		          endif;
		           			},array_keys($wc_pdf_names), array_values($wc_pdf_names), array_keys($wc_pdf_urls), array_values($wc_pdf_urls));

		       ?>                       
		        </ul>             
		    </div>
		</div>
	<?php }	

	// Callback function for display related products the in accessories tab
	function func_display_related_product_in_accessories_tab(){
		$related_product_ids = get_post_meta(get_the_id(), '_related_products_ids', true);
		
		if($related_product_ids): ?>
			<section class="related products">
		
				<!-- Add select all title -->
				<p class="block-subtitle">
					Check items to add to the cart or&nbsp;
					<a href="#" id="select_all_related">select all</a>
				</p>

				<?php
				    woocommerce_product_loop_start(); 
						foreach($related_product_ids as $related_product_id):

							// check product is in cart or not
							$product_cart_id = WC()->cart->generate_cart_id($related_product_id);
							$in_cart = WC()->cart->find_product_in_cart($product_cart_id);
						    if($in_cart){
						    	continue;
						    }

							$post_object = get_post($related_product_id);
							
							setup_postdata($GLOBALS['post'] =& $post_object);

							echo '<input type="checkbox" class="checkbox related-checkbox" id="related-checkbox'.$related_product_id.'" name="related_product_value[]" value="'.$related_product_id.'">';

							wc_get_template_part('content', 'product');
						endforeach;
					woocommerce_product_loop_end(); 
				?>
			</section>
		<?php
		endif;
		wp_reset_postdata();
	}


?>