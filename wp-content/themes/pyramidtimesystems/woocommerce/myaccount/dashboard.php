<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>
<div class="page-title">
	<h1>My Dashboard</h1>
</div>

<p>
	<?php
	printf(
		/* translators: 1: user display name 2: logout url */
		wp_kses( __( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'woocommerce' ), $allowed_html ),
		'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
		esc_url( wc_logout_url() )
	);
	?>
</p>

<p>
	<?php
	/* translators: 1: Orders URL 2: Address URL 3: Account URL. */
	$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">billing address</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
	if ( wc_shipping_enabled() ) {
		/* translators: 1: Orders URL 2: Addresses URL 3: Account URL. */
		$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
	}
	printf(
		wp_kses( $dashboard_desc, $allowed_html ),
		esc_url( wc_get_endpoint_url( 'orders' ) ),
		esc_url( wc_get_endpoint_url( 'edit-address' ) ),
		esc_url( wc_get_endpoint_url( 'edit-account' ) )
	);
	?>
</p>

<?php
	// Get an instance of the WC_Customer Object from the user ID
	$customer = new WC_Customer($current_user->ID);

	// Customer account details
	$username     = $customer->get_username();
	$user_email   = $customer->get_email();
	$first_name   = $customer->get_first_name();
	$last_name    = $customer->get_last_name();
	$display_name = $customer->get_display_name();

	// Customer billing information details
	$billing_first_name = $customer->get_billing_first_name();
	$billing_last_name  = $customer->get_billing_last_name();
	$billing_company    = $customer->get_billing_company();
	$billing_address_1  = $customer->get_billing_address_1();
	$billing_address_2  = $customer->get_billing_address_2();
	$billing_city       = $customer->get_billing_city();
	$billing_state      = $customer->get_billing_state();
	$billing_postcode   = $customer->get_billing_postcode();
	$billing_country    = $customer->get_billing_country();
	$billing_telephone  = $customer->get_billing_phone();

	// Customer shipping information details
	$shipping_first_name = $customer->get_shipping_first_name();
	$shipping_last_name  = $customer->get_shipping_last_name();
	$shipping_company    = $customer->get_shipping_company();
	$shipping_address_1  = $customer->get_shipping_address_1();
	$shipping_address_2  = $customer->get_shipping_address_2();
	$shipping_city       = $customer->get_shipping_city();
	$shipping_state      = $customer->get_shipping_state();
	$shipping_postcode   = $customer->get_shipping_postcode();
	$shipping_country    = $customer->get_shipping_country();
	$shipping_telephone  = $customer->get_shipping_phone();
?>

<div class="box-account box-info">
    <div class="box-head">
        <h2>Account Information</h2>
    </div>
	<div class="col2-set">
	    <div class="col-1">
	        <div class="box">
	            <div class="box-title">
	                <h3>Contact Information</h3>
	                <a href="<?php echo wc_get_endpoint_url('edit-account'); ?>">Edit</a>
	            </div>
	            <div class="box-content">
	                <p>
	                    Firstname: <?php echo $first_name; ?><br>
	                    Lastname: <?php echo $last_name; ?><br>
	                    Email: <?php echo $user_email; ?>
	                </p>
	            </div>
	        </div>
	    </div>
        <div class="col-2">
		</div>
    </div>
</div>

<div class="box-account box-info">
    <div class="box-head">
        <h2>Address Book</h2>
        <a href="<?php echo wc_get_endpoint_url('edit-address'); ?>">Manage Addresses</a>
    </div>
    <div class="col2-set">
        <div class="col-1">
            <div class="box">
                <div class="box-title">
                    <h3>Default Billing Address</h3>
                    <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ); ?>">Edit Address</a>
                </div>
                <div class="box-content">
                    <address>
                    	Name: <?php echo $billing_first_name ." ". $billing_last_name; ?><br>
                    	Company: <?php echo $billing_company; ?><br>
						Address: <br /> 
						<?php echo $billing_address_1; ?><br>
						<?php echo $billing_address_2; ?><br>
						<?php echo $billing_city. ", ".$billing_state.", ",$billing_postcode; ?><br>
						<?php echo $billing_country; ?><br>
					</address>
                </div>
            </div>
        </div>
        <div class="col-2">
            <div class="box">
                <div class="box-title">
                    <h3>Default Shipping Address</h3>
                    <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'shipping' ) ); ?>">Edit Address</a>
                </div>
                <div class="box-content">
                    <address>
                    	Name: <?php echo $shipping_first_name ." ". $shipping_last_name; ?><br>
                    	Company: <?php echo $shipping_company; ?><br>
						Address: <br /> 
						<?php echo $shipping_address_1; ?><br>
						<?php echo $shipping_address_2; ?><br>
						<?php echo $shipping_city. ", ".$shipping_state.", ",$shipping_postcode; ?><br>
						<?php echo $shipping_country; ?><br>
					</address>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
