<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.2
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>
	<div class="woocommerce-account-container">
		<div class="page-title">
		    <h1>Forgot Your Password?</h1>
		</div>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password">
			<h2>Retrieve your password here</h2>
			<p class="form-instructions">Please enter your email address below. You will receive a link to reset your password.</p>
			<p class="required">* Required Fields</p>

			<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
				<label for="user_login"><?php esc_html_e( 'Email Address', 'woocommerce' ); ?></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" autocapitalize="off" autocorrect="off" spellcheck="false" name="user_login" id="user_login" autocomplete="username" required />
			</p>

			<div class="clear"></div>

			<?php do_action( 'woocommerce_lostpassword_form' ); ?>

			<p class="back-link">
				<a href="<?php echo get_permalink(32584); ?>"><small>« </small>Back to Login</a>
			</p>

			<p class="woocommerce-form-row form-row">
				<input type="hidden" name="wc_reset_password" value="true" />
				<button type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Submit', 'woocommerce' ); ?>"><?php esc_html_e( 'Submit', 'woocommerce' ); ?></button>
			</p>

			<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

		</form>
	</div>	
<?php
do_action( 'woocommerce_after_lost_password_form' );
