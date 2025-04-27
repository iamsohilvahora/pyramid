<?php
/********************************************************************************************/
/*********************************** taxonomy archive page **********************************/
// WooCommerce support in theme
function pyramidtimesystems_wc_setup(){
	add_theme_support('woocommerce');
	add_theme_support( 'wc-product-gallery-lightbox');
	add_theme_support("wc-product-gallery-slider");
	add_theme_support("wc-product-gallery-zoom");
}
add_action('after_setup_theme', 'pyramidtimesystems_wc_setup' ,100);

// Remove taxonomy archive description
remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description');

/*******************************************************************************************/
/*********************************** Product archive page **********************************/
// Remove Woocommerce sidebar
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

// add product_short_description after product title 
add_action('woocommerce_after_shop_loop_item_title', 'product_short_description', 4);

// Remove result count
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);

// Remove catalog ordering
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

// Restrict product search to redirect on single page
add_filter('woocommerce_redirect_single_search_result', 'wc_remove_search_redirect', 10);

/********************************************************************** 
  *************  Load all hook according to template redirect to diiferent page *************/
function wc_load_custom_hook_function_code(){
	// archive product page or search page
	if(is_search() || is_shop()){
		// start main wrapper
		add_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);

		// product loop start wrapper
		add_action('woocommerce_before_shop_loop', 'product_wrapper_start', 11);
		add_action('woocommerce_after_shop_loop', 'product_wrapper_end', 11);

		// display sidebar after product loop
		add_action('woocommerce_after_shop_loop', 'sidebar_wrapper_start', 12);
		add_action('woocommerce_after_shop_loop', 'woocommerce_get_sidebar', 13);
		add_action('woocommerce_after_shop_loop', 'woocommerce_get_product_wishlist', 14);

		// product loop end wrapper
		add_action('woocommerce_after_shop_loop', 'product_wrapper_end', 15);

		// Close main wrapper
		add_action('woocommerce_after_main_content', 'product_wrapper_end', 10);
	}

	// Product detail page
	if(is_single()){
		add_action('woocommerce_shop_loop_item_title', 'wc_start_related_product_wrapper', 9);
		add_action('woocommerce_after_shop_loop_item_title', 'wc_end_related_product_wrapper', 11);
		add_filter('woocommerce_product_upsells_products_heading', 'wc_upsell_product_display_title');

		add_action('woocommerce_before_single_product_summary', 'wc_template_single_title', 9);
		add_action('woocommerce_single_product_summary', 'show_product_stock_availabity', 11);
		add_action('woocommerce_single_product_summary', 'wc_show_contact_number', 31);
		add_filter('woocommerce_get_stock_html', 'woocommerce_hide_stock_message', 10, 2);

		// Remove woocommerce single product meta
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

		add_action('woocommerce_single_product_summary', 'wc_show_product_wishlist', 31);
		add_action('woocommerce_single_product_summary', 'wc_close_wishlist_share_wrapper', 61);

		// Remove related product from single product page
		remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
		add_filter('woocommerce_product_tabs' ,'load_single_product_data_tabs');

		// add hidden field for related product in single product page
		add_action('woocommerce_before_add_to_cart_quantity', 'get_hidden_related_products_field', 29);
	}

	// Cart page
	if(is_cart()){
		add_filter('woocommerce_product_cross_sells_products_heading', 'wc_product_cross_sells_products_heading');

		// remove cross display
		remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');

		add_action('woocommerce_after_cart','woocommerce_cross_sell_display');

		add_action('woocommerce_before_cart', 'wc_show_cart_title', 7);
		add_action('woocommerce_before_cart', 'woocommerce_button_proceed_to_checkout', 8);
		add_action('woocommerce_before_cart', 'wc_close_title_checkout_wrapper' ,9);

		add_action('woocommerce_before_cart_collaterals', 'wc_show_shipping_content', 2);
		
		// remove default empty cart message
		remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
		add_action('woocommerce_cart_is_empty', 'wc_cart_is_empty_message', 11);
	}

	// apply except cart page
	if(!is_cart()){
		// remove add to cart button 
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

		// add Shop Now button
		add_action('woocommerce_after_shop_loop_item', 'woocommerce_shop_now_button', 10);
	}

	if(is_checkout()){
		add_action('woocommerce_before_checkout_form', 'wc_start_checkout_form_wrapper', 9);
		add_action('woocommerce_after_checkout_form', 'wc_end_checkout_form_wrapper');
	}

}
add_action('template_redirect', 'wc_load_custom_hook_function_code');

function woocommerce_output_content_wrapper(){
	echo '<div class="main-content-wrap">';
}

function woocommerce_output_content_wrapper_end(){
  	echo '</div>';
}

function product_wrapper_start(){
	echo '<div class="two_col">';
	echo '<div class="left-bar">';
}

function product_wrapper_end(){
	echo '</div>';
}

function woocommerce_get_product_wishlist(){
	echo do_shortcode('[ti_wishlistsview]');
}

function sidebar_wrapper_start(){
	echo '<div class="right-bar">';
}

// Add product short description after product title
function product_short_description(){
	the_excerpt();
}

// Add shop Now button after product price
function woocommerce_shop_now_button(){
	global $product;
	echo '<a href="'.get_permalink($product->ID).'">Shop Now <i class="fa fa-chevron-circle-right"></i></a>';
}

// Restrict product search to redirect on single page
function wc_remove_search_redirect(){
    return false;	 	 
}

/************************************************************************************/
/************************************ Single Product Page ***************************/
// Show product title in mobile above image in single product page
function wc_template_single_title(){
	global $product;

	echo '<div class="mobile_product_title">'.$product->get_title().'</div>';
}

// Show product is in stock or not in single product page
function show_product_stock_availabity(){
	global $product;
	$stock = $product->get_stock_quantity();

	if($product->is_in_stock() && $product->managing_stock()){
		echo '<div class="product_in_stock">IN STOCK</div>';
	} 
	else{
		echo '<div class="product_out_of_stock">OUT OF STOCK</div>';
	}
}

// Show contact number in single product page after add to cart button
function wc_show_contact_number(){
	echo '<div class="phone-order">
	    	<a href="tel:8884797264"><i class="fa fa-phone" aria-hidden="true"></i>888-479-7264</a>
	      </div>';
}

// Hide the "In stock" message on single product page.
function woocommerce_hide_stock_message($html, $product){
	if($product->is_in_stock()){
		return '';
	}
	return $html;
}

// Add wishlist in single product page
function wc_show_product_wishlist(){
	echo "<div class='wc_wishlist_share_wrapper'>";
	echo do_shortcode('[ti_wishlists_addtowishlist]');
}

// close wishlist social share wrapper
function wc_close_wishlist_share_wrapper(){
	echo "</div>";
}


// customize product data tabs of single product page
function load_single_product_data_tabs($tabs){
	global $product;

	// Description
	$tabs['description']['title'] = "Description";

	// Specs
	$tabs['additional_information']['title'] = "Specs";
	$tabs['additional_information']['priority'] = 11;
	$tabs['additional_information']['callback'] = "func_display_product_specs";

	// Accessories
	$related_product_ids = get_post_meta(get_the_id(), '_related_products_ids', true);
	if($related_product_ids):
		$tabs['accessories']['title'] = "Accessories";
		$tabs['accessories']['priority'] = 12;
		$tabs['accessories']['callback'] = "func_display_related_product_in_accessories_tab";
	endif;

	// Downloads
	$tabs['downloads']['title'] = "Downloads";
	$tabs['downloads']['priority'] = 13;
	$tabs['downloads']['callback'] = "func_download_template";

	return $tabs;
}

/**
 * load Product Data area of Edit Product page (admin)
 */
require get_template_directory().'/inc/product-custom-tab.php';

/**
 * load product data tabs of single product page (admin)
 */
require get_template_directory().'/inc/product-tab.php';

// display upsell product display title
function wc_upsell_product_display_title(){
	$title = "Customers also viewed";
	return $title; 
}

function get_hidden_related_products_field($product){
	global $product;
	?>
	<div class="no-display">
		<input type="hidden" name="main_product" value="<?php echo $product->get_id(); ?>">
		<input type="hidden" name="related_product_list" id="related-products-field" value="">
	</div>

<?php }

// show start div above related product title in single product page
function wc_start_related_product_wrapper(){
	echo '<div class="related_product_wrapper">';
}

// show end div after price in single product page
function wc_end_related_product_wrapper(){
	echo "</div>";
}

/********************************************************************************************/
/************************************ Cart Page *********************************************/
// Show cart title and proceed to checkout button above cart product table
function wc_show_cart_title(){
	echo '<div class="cart-page-title checkout-button">
	    	<h1>Shopping Cart</h1>';
}

// Close cart title and proceed to checkout button wrapper
function wc_close_title_checkout_wrapper(){
	echo "</div>";
}

// add shipping content besides cart product table
function  wc_show_shipping_content(){
	echo "<p>We ship our products to the continental US, Puerto Rico, and Canada only. For shipments to Puerto Rico, please choose either FedEx International or UPS Worldwide Express Saver. Delivery times are subject to product availability at the time of purchase. Orders placed after 12pm EST will be processed the next business day with the exception of TimeTrax Version 5 software and upgrade purchases.</p>";
}

// check for empty-cart get param to clear the cart

function wc_empty_cart_action(){	
	if(isset($_GET['empty_cart']) && 'yes' === $_GET['empty_cart']){
		WC()->cart->empty_cart();

		$referer = wp_get_referer() ? esc_url(remove_query_arg('empty_cart')) : wc_get_cart_url();
		wp_safe_redirect($referer);
	}
}
add_action('init', 'wc_empty_cart_action');

// add custom empty cart message in cart page
function wc_cart_is_empty_message(){ ?>
	<div class="col-main">
			<div class="page-title">
    			<h1>Shopping Cart is Empty</h1>
			</div>
			<div class="cart-empty">
            	<p>You have no items in your shopping cart.</p>
    			<p>Click <a href="<?php echo get_site_url(); ?>">here</a> to continue shopping.</p>
    		</div>
        </div> 
<?php } 

// display cross sells product heading in cart page
function wc_product_cross_sells_products_heading(){
	echo "<h2>Based on your selection, you may be interested in the following items:</h2>";
}

// display custom add to cart message in product detail and cart page
function wc_add_to_cart_message_filter($message, $product_id = null){
	$titles[] = get_the_title($product_id);
	 
	$titles = array_filter($titles);
	$added_text = sprintf(_n('%s has been added to your cart.', '%s have been added to your cart.', sizeof($titles), 'woocommerce'), wc_format_list_of_items($titles));
	 
	$message = $added_text; 
	// $message = sprintf( '%s <a href="%s" class="button">%s</a>&nbsp;<a href="%s" class="button">%s</a>',
	//                 esc_html( $added_text ),
	//                 esc_url( wc_get_page_permalink( 'checkout' ) ),
	//                 esc_html__( 'Checkout', 'woocommerce' ),
	//                 esc_url( wc_get_page_permalink( 'cart' ) ),
	//                 esc_html__( 'View Cart', 'woocommerce' ));
	return $message;
}
add_filter ('wc_add_to_cart_message', 'wc_add_to_cart_message_filter', 10, 2);

// Display the custom fields in the "Linked Products" section (admin)
add_action('woocommerce_product_options_related', 'wc_linked_products_data_custom_field');

// Save to custom fields (admin)
add_action('woocommerce_process_product_meta', 'wc_linked_products_data_custom_field_save');


// Display the custom fields
function wc_linked_products_data_custom_field(){
    global $woocommerce, $post;
?>
	<p class="form-field">
	    <label for="related_product_ids"><?php _e( 'Related Products', 'woocommerce' ); ?></label>
	    
	    <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="related_product_ids" name="related_product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $post->ID ); ?>">
	        <?php
	            $product_ids = get_post_meta($post->ID, '_related_products_ids', true);

	            foreach($product_ids as $product_id){
	                $product = wc_get_product($product_id);
	                if(is_object($product)){
	                    echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
	                }
	            }
	        ?>
	    </select> <?php echo wc_help_tip( __( 'Related are products which you recommend in the product detail page, based on the current product.', 'woocommerce' ) ); ?>
	</p>

<?php
}

// Save the custom fields
function wc_linked_products_data_custom_field_save($post_id){
    $product_field_type = $_POST['related_product_ids'];
    update_post_meta($post_id, '_related_products_ids', $product_field_type);
}

/********************************************************************************************/
/************************************ Checkout Page ******************************************/
// Start checkout form wrapper
function wc_start_checkout_form_wrapper(){
	echo "<div class='checkout-wrapper'>";
}

// End checkout form wrapper
function wc_end_checkout_form_wrapper(){
	echo "</div>";
}

/********************************************************************************************/
/************************************ My Account Page ****************************************/
add_action('woocommerce_account_navigation', 'start_my_account_wrapper', 1);
add_action('woocommerce_after_my_account', 'close_my_account_wrapper', 1);

function start_my_account_wrapper(){
	echo '<div class="woocommerce-account-container">';
}

function close_my_account_wrapper(){
	echo '</div>';
}

// Define the woocommerce_process_login_errors callback 
function wc_process_login_errors_func($validation_error, $post_username, $post_password){
    if(!filter_var($post_username, FILTER_VALIDATE_EMAIL)){
        throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . __( 'Please Enter a Valid Email Address.', 'woocommerce' ) );
    }
    return $validation_error;
}
add_filter('woocommerce_process_login_errors', 'wc_process_login_errors_func', 10, 3);

// Modify form error message in woocommerce
function wc_modify_error_msg($error){
	if($error == "Enter a username or email address."){
		$error = "Enter valid email address."; 
	}

	if($error == "Invalid username or email."){
		$error = "Invalid email address."; 
	}

	if($error == "Unknown email address. Check again or try your username."){
		$error = "Unknown email address. Check again."; 
	}

    // Check if that's the error you are looking for
    $pos = strpos($error, 'Lost');
    if(is_int($pos)){
    	$error = str_replace('Lost', 'Forgot', $error);
    }
    return $error;    
}
add_filter('woocommerce_add_error', 'wc_modify_error_msg');

/**
* register fields Validating.
*/
function wc_account_registration_field_validation($errors){
	// firstname, middlename, lastname validation
	if(isset($_POST['firstname']) && empty($_POST['firstname'])){ 
		$errors->add( 'firstname_error', __('Firstname is required!', 'woocommerce')); 
	}

	if(isset($_POST['lastname']) && empty($_POST['lastname'])){ 
		$errors->add( 'lastname_error', __('Lastname is required!', 'woocommerce')); 
	}

	if($_POST['firstname'] != "" && !preg_match("/^[a-zA-Z ]*$/", $_POST['firstname'])){
		$errors->add( 'firstname_error', __('The firstname entered is invalid.', 'woocommerce')); 
	}

	if($_POST['middlename'] != "" && !preg_match("/^[a-zA-Z ]*$/", $_POST['middlename'])){
		$errors->add( 'middlename_error', __('The middlename entered is invalid.', 'woocommerce')); 
	}

	if($_POST['lastname'] != "" && !preg_match("/^[a-zA-Z ]*$/", $_POST['lastname'])){
		$errors->add( 'lastname_error', __('The lastname entered is invalid.', 'woocommerce')); 
	}

	// email validation
	if(isset($_POST['email']) && empty($_POST['email'])){ 
		$errors->add( 'email_address_error', __('Email address is required!', 'woocommerce')); 
	} 

	// telephone number validation
	if(isset($_POST['telephone']) && empty($_POST['telephone'])){ 
		$errors->add( 'telephone_number_error', __('Telephone number is required!', 'woocommerce')); 
	} 

	if($_POST['telephone'] != "" && !preg_match('/^\(?\+?([0-9]{1,5})?\)?[-\. ]?(\d{10})$/', $_POST['telephone'])){
		$errors->add( 'telephone_number_error', __('Telephone number is invalid!', 'woocommerce'));
	}

	// street address, country, state, city validation
	if(isset($_POST['street']) && empty($_POST['street'])){ 
		$errors->add( 'street_address_error', __('Street address is required!', 'woocommerce')); 
	} 

	if(isset($_POST['country_id']) && empty($_POST['country_id'])){ 
		$errors->add( 'country_error', __('Please select country!', 'woocommerce')); 
	} 

	if(isset($_POST['region_id']) && empty($_POST['region_id'])){ 
		if(isset($_POST['region']) && empty($_POST['region'])){
			$errors->add( 'state_error', __('Please select state!', 'woocommerce')); 
		}
	}

	if(isset($_POST['city']) && empty($_POST['city'])){ 
		$errors->add( 'city_error', __('Enter your city name!', 'woocommerce')); 
	} 

	// zipcode validation
	if(isset($_POST['postcode']) && empty($_POST['postcode'])){ 
		$errors->add( 'postcode_error', __('Enter your postcode!', 'woocommerce')); 
	}

	$zipcodeLength = strlen($_POST['postcode']);
	if($_POST['postcode'] != "" && ($zipcodeLength <= 4 || $zipcodeLength >= 12) || (!preg_match("/^[a-zA-Z0-9' ]*$/", $_POST['postcode']))){
		$errors->add( 'postcode_error', __('Please enter a valid zip code!', 'woocommerce')); 
	}
	
	// Password validation
	if(isset($_POST['password']) && empty($_POST['password'])){ 
		$errors->add( 'password_error', __('Please enter password!', 'woocommerce')); 
	} 
	
	if(isset($_POST['confirmation']) && empty($_POST['confirmation'])){ 
		$errors->add( 'password_confirmation_error', __('Please re-enter password!', 'woocommerce')); 
	} 
	
	if(isset($_POST['password']) && isset($_POST['confirmation']) && !empty($_POST['password']) && !empty($_POST['confirmation'])){
		if($_POST['password'] !== $_POST['confirmation']){
			$errors->add( 'password_not_match_error', __('Password do not match!', 'woocommerce')); 
		}
	}

	// Validate password strength
	$uppercase = preg_match('@[A-Z]@', $_POST['password']);
	$lowercase = preg_match('@[a-z]@', $_POST['password']);
	$number    = preg_match('@[0-9]@', $_POST['password']);
	$specialChars = preg_match('@[^\w]@', $_POST['password']);

	if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($_POST['password']) < 8){
		$errors->add('password_not_match_error', __('Password should be at least 8 characters in length and should include at least one uppercase letter, lowercase letter, one number, and one special character!', 'woocommerce'));
	}

	return $errors;
}
add_filter('woocommerce_process_registration_errors', 'wc_account_registration_field_validation', 10, 1); 

// Save registration Field value 
function wc_save_account_registration_field($customer_id){ 
	if(isset($_POST['firstname'])){ 
		update_user_meta($customer_id, 'first_name', sanitize_text_field($_POST['firstname'])); 
		update_user_meta($customer_id, 'billing_first_name', sanitize_text_field($_POST['firstname'])); 
	}

	if(isset($_POST['middlename'])){ 
		// update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['lastname'])); 
		update_user_meta($customer_id, 'billing_middle_name', sanitize_text_field($_POST['middlename'])); 
	}

	if(isset($_POST['lastname'])){ 
		update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['lastname'])); 
		update_user_meta($customer_id, 'billing_last_name', sanitize_text_field($_POST['lastname'])); 
	}

	// if(isset($_POST['email'])){ 
	// 	update_user_meta($customer_id, 'billing_email', sanitize_text_field($_POST['email'])); 
	// }

	if(isset($_POST['company'])){ 
		update_user_meta($customer_id, 'billing_company', sanitize_text_field($_POST['company'])); 
	}

	if(isset($_POST['telephone'])){ 
		update_user_meta($customer_id, 'billing_phone', sanitize_text_field($_POST['telephone'])); 
	}

	if(isset($_POST['street'])){ 
		update_user_meta($customer_id, 'billing_address_1', sanitize_text_field($_POST['street']));

		if(isset($_POST['street_2'])){
			update_user_meta($customer_id, 'billing_address_2', sanitize_text_field($_POST['street_2']));
		}
	}

	if(isset($_POST['country_id'])){ 
		update_user_meta($customer_id, 'billing_country', sanitize_text_field($_POST['country_id'])); 
	}

	if(isset($_POST['region_id']) || isset($_POST['region'])){
		if(isset($_POST['region_id']) && !empty($_POST['region_id'])){
			update_user_meta($customer_id, 'billing_state', sanitize_text_field($_POST['region_id'])); 
		}
		else if(isset($_POST['region']) && !empty($_POST['region'])){
			update_user_meta($customer_id, 'billing_state', sanitize_text_field($_POST['region'])); 
		}
	}

	if(isset($_POST['city'])){ 
		update_user_meta($customer_id, 'billing_city', sanitize_text_field($_POST['city'])); 
	}

	if(isset($_POST['postcode'])){ 
		update_user_meta($customer_id, 'billing_postcode', sanitize_text_field($_POST['postcode'])); 
	}

	// if(isset($_POST['password'])){ 
	// 	update_user_meta($customer_id, 'billing_last_name', sanitize_text_field($_POST['lastname'])); 
	// }
}
add_action('woocommerce_created_customer', 'wc_save_account_registration_field');

// Save Field value in Edit account 
function wc_save_my_account_billing_account_number($user_id){ 
	if(isset($_POST['firstname'])){ 
		update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['firstname'])); 
		update_user_meta($user_id, 'billing_first_name', sanitize_text_field($_POST['firstname'])); 
	}

	if(isset($_POST['middlename'])){ 
		// update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['lastname'])); 
		update_user_meta($user_id, 'billing_middle_name', sanitize_text_field($_POST['middlename'])); 
	}

	if(isset($_POST['lastname'])){ 
		update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['lastname'])); 
		update_user_meta($user_id, 'billing_last_name', sanitize_text_field($_POST['lastname'])); 
	}

	// if(isset($_POST['email'])){ 
	// 	update_user_meta($user_id, 'billing_email', sanitize_text_field($_POST['email'])); 
	// }

	if(isset($_POST['company'])){ 
		update_user_meta($user_id, 'billing_company', sanitize_text_field($_POST['company'])); 
	}

	if(isset($_POST['telephone'])){ 
		update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['telephone'])); 
	}

	if(isset($_POST['street'])){ 
		update_user_meta($user_id, 'billing_address_1', sanitize_text_field($_POST['street']));

		if(isset($_POST['street_2'])){
			update_user_meta($user_id, 'billing_address_2', sanitize_text_field($_POST['street_2']));
		}
	}

	if(isset($_POST['country_id'])){ 
		update_user_meta($user_id, 'billing_country', sanitize_text_field($_POST['country_id'])); 
	}

	if(isset($_POST['region_id']) || isset($_POST['region'])){
		if(isset($_POST['region_id']) && !empty($_POST['region_id'])){
			update_user_meta($user_id, 'billing_state', sanitize_text_field($_POST['region_id'])); 
		}
		else if(isset($_POST['region']) && !empty($_POST['region'])){
			update_user_meta($user_id, 'billing_state', sanitize_text_field($_POST['region'])); 
		}
	}

	if(isset($_POST['city'])){ 
		update_user_meta($user_id, 'billing_city', sanitize_text_field($_POST['city'])); 
	}

	if(isset($_POST['postcode'])){ 
		update_user_meta($user_id, 'billing_postcode', sanitize_text_field($_POST['postcode'])); 
	}
}
add_action('woocommerce_save_account_details', 'wc_save_my_account_billing_account_number', 10, 1);

// Rename account menu items
function wc_account_menu_items_func($menu_links){
	// Change menu item's name
	$menu_links['dashboard'] = 'Account Dashboard';
	$menu_links['orders'] = 'My Orders';
	$menu_links['downloads'] = 'My Downloadable Products';
	$menu_links['edit-address'] = 'Address Book';
	$menu_links['edit-account'] = 'Account Information';
	$menu_links['customer-logout'] = 'Logout';

	$menu_links = array_slice($menu_links, 0, 5, true) 
	// + array('review-customer' => 'My Product Reviews')
	+ array('wishlist' => 'My Wishlist')
	+ array_slice($menu_links, 5, NULL, true);
	
	return $menu_links;
}
add_filter('woocommerce_account_menu_items', 'wc_account_menu_items_func');

/*
 * Register Permalink Endpoint
*/
function wc_add_endpoint_func(){
	add_rewrite_endpoint('review-customer', EP_PAGES);
	add_rewrite_endpoint('wishlist', EP_PAGES);
}
add_action('init', 'wc_add_endpoint_func');

/*
 * Content for the new page in My Account, woocommerce_account_{ENDPOINT NAME}_endpoint
*/
// Display customer product reviews
function wc_my_account_product_review_endpoint_content_func(){
	$user_id = get_current_user_id();
	$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;

	$recent_comments = get_comments(array(
			'user_id' => $user_id,
			'number'  => $limit,
			'status'    => 'approve',
	));

	global $wp;
	$current_url = home_url(add_query_arg(array(), $wp->request));
	?>
	<div class="page-title">
    	<h1>My Product Reviews</h1>
	</div>

	<div class="pager pager-no-toolbar">
	    <div class="count-container">
			<p class="amount amount--no-pages">
				<strong><?php echo count($recent_comments); ?> Item(s)</strong>
	        </p>
	                
			<div class="limiter">
	        	<label>Show</label>

	        	<select id="sort_rating" title="Results per page">
	        		<option value="<?php echo $current_url; ?>/?limit=10" <?php echo $_GET['limit'] == '10' ? 'selected' : ''; ?>>10</option>
	        		<option value="<?php echo $current_url; ?>/?limit=20" <?php echo $_GET['limit'] == '20' ? 'selected' : ''; ?>>20</option>
	        		<option value="<?php echo $current_url; ?>/?limit=50" <?php echo $_GET['limit'] == '50' ? 'selected' : ''; ?>>50</option>
	        	</select>
	    	</div>
		</div>
    </div>

	<table class="data-table" id="my-reviews-table">
            <colgroup>
        		<col width="1">
        		<col width="210">
        		<col width="1">
        		<col>
        		<col width="1">
        	</colgroup>

        	<tbody>
        	<?php foreach($recent_comments as $recent_comment): ?>	
                <tr>
                	<td><?php echo $recent_comment->comment_date; ?></td>
                	<td>
                		<h2 class="product-name">
                			<a href="<?php echo get_comment_link($recent_comment); ?>"><?php echo get_the_title($recent_comment->comment_post_ID); ?>
                			</a>
                		</h2>
                	</td>
                	<td></td>
                	<td><?php echo $recent_comment->comment_content; ?></td>
                	<td>
                		<a href="<?php echo wc_get_endpoint_url('review-customer'); ?>/?view_id=<?php echo $recent_comment->comment_ID; ?>">View Details</a>
                	</td>
            	</tr>
            <?php endforeach; ?>
			</tbody>
    </table>

    <div class="pager pager-no-toolbar">
	    <div class="count-container">
			<p class="amount amount--no-pages">
				<strong><?php echo count($recent_comments); ?> Item(s)</strong>
	        </p>
	                
			<div class="limiter">
	        	<label>Show</label>

	        	<select id="sort_rating" title="Results per page">
	        		<option value="<?php echo $current_url; ?>/?limit=10" <?php echo $_GET['limit'] == '10' ? 'selected' : ''; ?>>10</option>
	        		<option value="<?php echo $current_url; ?>/?limit=20" <?php echo $_GET['limit'] == '20' ? 'selected' : ''; ?>>20</option>
	        		<option value="<?php echo $current_url; ?>/?limit=50" <?php echo $_GET['limit'] == '50' ? 'selected' : ''; ?>>50</option>
	        	</select>
	    	</div>
		</div>
    </div>
<?php
}
if(!isset($_GET['view_id'])){
	add_action('woocommerce_account_review-customer_endpoint', 'wc_my_account_product_review_endpoint_content_func', 1);
}

// Display customer wishlist product
function wc_my_account_wishlist_endpoint_content_func(){	
	echo do_shortcode('[ti_wishlistsview]');
}
add_action('woocommerce_account_wishlist_endpoint', 'wc_my_account_wishlist_endpoint_content_func');

/*
 * Customer review page
*/ 
function wc_customer_rating_content_func(){
	$comment_ID = $_GET['view_id'];
	$recent_comment = get_comment($comment_ID);

	if(!empty($recent_comment)): ?>
		<div class="product-review">
		    <div class="page-title">
		        <h1>Review Details</h1>
		    </div>
		    <div class="product-img-box">
		        <a href="<?php echo get_comment_link($recent_comment); ?>" title="<?php echo get_the_title($recent_comment->comment_post_ID); ?>" class="product-image">
		        	<img src="<?php echo wp_get_attachment_url(get_post_thumbnail_id($recent_comment->comment_post_ID));  ?>" width="325" height="" alt="<?php echo get_the_title($recent_comment->comment_post_ID); ?>">
		        </a>
			</div>
		    <div class="product-details">
		        <h2 class="product-name"><?php echo get_the_title($recent_comment->comment_post_ID); ?></h2>
					<dl class="ratings-description">
		            <dt>Your Review (submitted on <?php echo date("F jS\, Y", strtotime($recent_comment->comment_date)); ?>):</dt>
		            <dd><?php echo $recent_comment->comment_content; ?></dd>
		        </dl>
		    </div>
		    <div class="buttons-set">
	        	<p class="back-link"><a href="<?php echo wc_get_endpoint_url('review-customer'); ?>"><small>« </small>Back to My Reviews</a></p>
	    	</div>
		</div>
<?php endif;
}
if(isset($_GET['view_id'])){
	add_action('woocommerce_account_review-customer_endpoint', 'wc_customer_rating_content_func', 2);
}

?>