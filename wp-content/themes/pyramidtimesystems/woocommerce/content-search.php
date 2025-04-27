<?php
	// WooCommerce Product search by category and keyword
	if($_GET['product_cat'] == 0){
		$args = array(
					'post_type'             => 'product',
					'post_status'           => 'publish',
					'posts_per_page'        => -1,
					'orderby'   => 'title',
        			'order' => 'ASC',
        			// 's' => $_GET['s'],
				);
	}
	else{
		$args = array(
				'post_type'             => 'product',
				'post_status'           => 'publish',
				'posts_per_page'        => -1,
				'orderby'   => 'title',
        		'order' => 'ASC',
				'tax_query' => array(
				    array(
				        'taxonomy'      => 'product_cat',
				        'field' => 'term_id', 
				        'terms'         => $_GET['product_cat'],
				        'operator'      => 'IN',
				        'include_children' => false
				    	),
				   
				),
				// 's' => $_GET['s'],
			);
	}

	$query = new WP_Query($args);

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );
	
	woocommerce_product_loop_start();

	$count_post = 0;
	if($query->have_posts()): 
	    while($query->have_posts()): 
	        $query->the_post();

	        if(preg_match("/".$_GET['s']."/i", $query->post->post_title)){
				wc_get_template_part('content', 'product');
				$count_post++;
	        }
	    endwhile;
	    $not_found = ($count_post > 0) ? "" : do_action( 'woocommerce_no_products_found' ); 	
	    wp_reset_postdata();
	else:
		do_action( 'woocommerce_no_products_found' );
	endif;

	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );

?>