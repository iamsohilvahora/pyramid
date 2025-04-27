<?php
/**
 * RMA Order Message email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/mwb-rma-messages-emial-template.php.
 *
 * @package    woocommerce_refund_and_exchange_lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$allowed_html = array(
	'p' => array(
		'class' => '',
	),
	'b' => array(
		'class' => '',
	),
);
do_action( 'woocommerce_email_header', $email_heading, $email );

$message = $msg;
echo wp_kses( $message, $allowed_html );

do_action( 'woocommerce_email_footer', $email );
