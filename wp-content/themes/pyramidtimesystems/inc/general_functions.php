<?php
/**  
 * Remove woocommerce product-category slug 
 */
add_filter('request', function($vars){
	global $wpdb;
	if(!empty($vars['pagename']) || !empty($vars['category_name']) || !empty($vars['name']) || ! empty($vars['attachment'])){
		$slug = ! empty($vars['pagename']) ? $vars['pagename'] : (!empty($vars['name']) ? $vars['name'] : (!empty($vars['category_name']) ? $vars['category_name'] : $vars['attachment']));
		$exists = $wpdb->get_var($wpdb->prepare("SELECT t.term_id FROM $wpdb->terms t LEFT JOIN $wpdb->term_taxonomy tt ON tt.term_id = t.term_id WHERE tt.taxonomy = 'product_cat' AND t.slug = %s" ,array($slug)));
		if($exists){
			$old_vars = $vars;
			$vars = array('product_cat' => $slug);
			if(!empty($old_vars['paged']) || !empty($old_vars['page']))
				$vars['paged'] = !empty($old_vars['paged']) ? $old_vars['paged'] : $old_vars['page'];
			if(!empty($old_vars['orderby']))
	 	        	$vars['orderby'] = $old_vars['orderby'];
      			if(!empty($old_vars['order']))
 			        $vars['order'] = $old_vars['order'];	
		}
	}
	return $vars;
});
 
function term_link_filter( $url, $term, $taxonomy ){
    $url=str_replace("/./","/",$url);
    return $url;
}	
add_filter('term_link', 'term_link_filter', 10, 3);

/**  
 * Pagination for News CPT 
 */
if(!function_exists('wpex_pagination')){
	function wpex_pagination($wp_query){
		$prev_arrow = is_rtl() ? '>' : '<';
		$next_arrow = is_rtl() ? '<' : '>';

		$total = $wp_query->max_num_pages;
		$big = 999999999; // need an unlikely integer
		if($total > 1){
			if(!$current_page = get_query_var('paged'))
				$current_page = 1;
			if(get_option('permalink_structure')){
				$format = 'page/%#%/';
			} 
			else{
				$format = '&paged=%#%';
			}
			echo paginate_links(array(
				'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'    => $format,
				'current'   => max(1, get_query_var('paged')),
				'total'     => $total,
				'mid_size'    => 3,
				'type'      => 'list',
				'prev_text'   => $prev_arrow,
				'next_text'   => $next_arrow,
			));
		}
	}
}

/**  
 * add js script to footer 
 */
function wp_footer_script_code_func(){
?>
<!-- Share this buttons -->
<script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=61d2eb74a3f18600195680ae&product=inline-share-buttons' async='async'></script>

<!--Start of Zendesk Chat Script-->
<script type="text/javascript">
	window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=

	d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.

	_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");

	$.src="https://v2.zopim.com/?5TKQYIu832AYSfkxmjmVPty2PNmNSix6";z.t=+new Date;$.

	type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>
<!--End of Zendesk Chat Script-->

<!-- Start of pyramidtimesystems Zendesk Widget script -->
<!-- <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=e5556476-a8d0-4c19-87a8-025444b23c93"> </script> -->
<!-- End of pyramidtimesystems Zendesk Widget script -->

<?php
};
add_action('wp_footer', 'wp_footer_script_code_func');

/**  
 * Allow multi add to cart products to cart page 
 */ 
function wc_allow_multiple_products_to_cart(){
    if (!class_exists('WC_Form_Handler') || empty($_REQUEST['related_product_list'])){
        return;
    } 
    remove_action('wp_loaded', array('WC_Form_Handler', 'add_to_cart_action'), 20);
    
    $product_ids = explode(',', $_REQUEST['related_product_list']);
    $count = count($product_ids);
  
    $number = 0;
    foreach ($product_ids as $product_id){
        $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($product_id));
        $was_added_to_cart = false;
        $adding_to_cart = wc_get_product($product_id);
        if(!$adding_to_cart){
            continue;
        } 
        $add_to_cart_handler = apply_filters('woocommerce_add_to_cart_handler', $adding_to_cart->product_type, $adding_to_cart); 

        /* only add simple product to cart */ 
        if('simple' !== $add_to_cart_handler){
            continue;
        }
        $quantity = 1;
        $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
        if($passed_validation && false !== WC()->cart->add_to_cart($product_id, $quantity)){
            // wc_add_to_cart_message(array($product_id => $quantity), true);
        }

        if(++$number === $count){
            return WC_Form_Handler::add_to_cart_action();
        } 
    }
}

// Run before the WC_Form_Handler::add_to_cart_action callback 
add_action('wp_loaded', 'wc_allow_multiple_products_to_cart', 15);

/********************************************************************************************/
/************************** Custom phone number validation in cf7 ***************************/ 
function custom_filter_wpcf7_is_tel($result, $tel){
	$result = preg_match('/^\(?\+?([0-9]{1,5})?\)?[-\. ]?(\d{10})$/', $tel);
	return $result;
}
add_filter('wpcf7_is_tel', 'custom_filter_wpcf7_is_tel', 10, 2);
add_filter('wpcf7_is_tel*', 'custom_filter_wpcf7_is_tel', 10, 2);

/********************************************************************************************/
/************************** Custom name validation in cf7 ***********************************/ 
function custom_name_validation_filter($result, $tag){
	if("firstname" == $tag->name || "lastname" == $tag->name || "username" == $tag->name){
		$name = isset($_POST[$tag->name]) ? $_POST[$tag->name]  : '';

		if($name != "" && !preg_match("/^[a-zA-Z ]*$/", $name)){
			$result->invalidate($tag, "The name entered is invalid.");
		}
	}  
	return $result;
}
add_filter('wpcf7_validate_text', 'custom_name_validation_filter', 20, 2);
add_filter('wpcf7_validate_text*', 'custom_name_validation_filter', 20, 2);

/********************************************************************************************/
/************************** Custom zipcode validation in cf7 ********************************/ 
function custom_zip_validation_filter($result, $tag){ 
	if("company_postcode" == $tag->name){
		$zip = isset($_POST['company_postcode']) ? trim($_POST['company_postcode']) : '';
		$zipcodeLength = strlen($zip);

		if($zip != "" && ($zipcodeLength <= 4 || $zipcodeLength >= 12) || (!preg_match("/^[a-zA-Z0-9' ]*$/",$zip))){
			$result->invalidate($tag, "Please enter a valid zip code");
		}
	}
	return $result;
}
add_filter('wpcf7_validate_text', 'custom_zip_validation_filter', 21, 2);
add_filter('wpcf7_validate_text*', 'custom_zip_validation_filter', 21, 2);

/********************************************************************************************/
/************* Add nav menu items to account cart menu and mobile menu **********************/ 
function add_custom_menu_item($items, $args){
	$user = wp_get_current_user();
	$user_display_name = $user->display_name ? '<li class="welcome-msg">Welcome, '.$user->display_name.'! </li>' : '<li class="welcome-msg"></li>';

	// Get wishlist counter.
	$wishlist_counter = tinv_get_option('topline', 'show_counter') ? "<span class='wishlist_products_counter_number'></span>" : "";

	// Get wishlist for current user.
	$wl = tinv_wishlist_get();

	// Get share key for wishlist if exists.
	$share_key = ( $wl && isset( $wl['share_key'] ) ) ? $wl['share_key'] : '';

	// Get wishlist url with share key if exists.
	$wishlist_url = tinv_url_wishlist_by_key( $share_key );

	if(($args->theme_location == 'menu-2') || ($args->theme_location == 'menu-17')) // only for secondary menu and mobile menu
	{
		$list_menu_item = array();

		// Welcome message
		$list_menu_item[] = $user_display_name;

		// My account page url
		$myaccount_page_url = get_permalink(get_option('woocommerce_myaccount_page_id'));
		$list_menu_item[] = '<li class="menu-item"><a href="'. $myaccount_page_url .'" title="My Account">My Account</a></li>';

		// Wishlist page url
		$list_menu_item[] = '<li class="menu-item"><a href="'. $wishlist_url .'" title="My Wishlist">My Wishlist ('.$wishlist_counter.' items)
		 </a></li>';

		// Cart page url
		$list_menu_item[] = '<li class="menu-item"><a href="' .wc_get_cart_url() .'" title="My Cart ('.WC()->cart->get_cart_contents_count().' items)">My Cart ('.WC()->cart->get_cart_contents_count().' items)
		 </a></li>';

		// Checkout page url 
		$list_menu_item[] = '<li class="menu-item"><a href="'. wc_get_checkout_url() .'" title="My Checkout">My Checkout</a></li>';

		if(is_user_logged_in()){
			$list_item = '<li class="menu-item"><a href="'. wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ) .'" title="Log Out">Log Out</a></li>';
			$list_menu_item[] = $list_item;
		}
		else{
			$register_page = get_permalink(32565);
			$login_page = get_permalink(32584);

			$list_item1 = '<li class="class="menu-item menu-item-type-post_type wpmm-submenu-right"><a href="'.$register_page.'" title="Register">Register</a></li>';
			$list_menu_item[] = $list_item1; 

			$list_item2 = '<li class="menu-item"><a href="'.$login_page.'" title="Login">Login</a></li>';
			$list_menu_item[] = $list_item2;
		}

	    $items_array = array();
	    while(false !== ($item_pos = strpos($items, '<li', 3))){
	        $items_array[] = substr($items, 0, $item_pos);
	        $items = substr($items, $item_pos);
	    }

	    $items_array[] = $items;
	    array_splice($items_array, 1, 0, $list_menu_item); // add custom item to sub menu

	    $items = implode('', $items_array);
	}
	return $items;
}
add_filter('wp_nav_menu_items','add_custom_menu_item', 10, 2);

?>