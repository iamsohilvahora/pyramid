<?php
/**
 * Exit if accessed directly
 *
 * @package  woocommerce_refund_and_exchange_lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.

}
/**
 * A custom Expedited Order WooCommerce Email class
 *
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Rma_Messages_Email extends WC_Email {

	/**
	 * Set email defaults
	 *
	 * @since 0.1
	 */
	public function __construct() {
		// set ID, this simply needs to be a unique name.
		$this->id = 'wc_rma_order_messages_email';

		// this is the title in WooCommerce Email settings.
		$this->title = 'RMA Order Messages';

		// this is the description in WooCommerce email settings.
		$this->description = 'Admin to customer order messages emails and viceversa';

		// these are the default heading and subject lines that can be overridden using the settings.
		$this->heading = 'RMA Message';
		$this->subject = 'New message has been received';

		// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar.
		$this->template_html  = 'mwb-rma-messages-email-template.php';
		$this->template_plain = 'mwb-rma-messages-email-template.php';
		$this->template_base  = MWB_REFUND_N_EXCHANGE_LITE_DIRPATH . 'emails/templates/';
		$this->placeholders   = array(
			'{site_title}'       => $this->get_blogname(),
			'{message_date}' => '',
			'{order_id}' => '',
		);

		// Call parent constructor to load any other defaults not explicity defined here.
		parent::__construct();

	}

	/**
	 * Determine if the email should actually be sent and setup email merge variables
	 *
	 * @param string $msg is message.
	 * @param array  $attachment is media attachment.
	 * @param string $to send to mail.
	 * @param array  $order_id is order.
	 */
	public function trigger( $msg, $attachment, $to, $order_id ) {
		if ( $to ) {
			$this->setup_locale();
			$this->receicer                       = $to;
			$this->msg                            = $msg;
			$this->placeholders['{message_date}'] = gmdate( 'M d, Y' );
			$this->placeholders['{order_id}']     = '#' . $order_id;
			$this->send( $this->receicer, $this->get_subject(), $this->get_content(), $this->get_headers(), $attachment );
		}
		$this->restore_locale();
	}

	/**
	 * Get_content_html function.
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template(
			$this->template_html,
			array(
				'order_id' => $this->order_id,
				'msg' => $this->msg,
				'email_heading'  => $this->get_heading(),
				'sent_to_admin'  => false,
				'plain_text'     => false,
				'email'          => $this,
			),
			'woo-refund-and-exchange-lite',
			$this->template_base
		);

		return ob_get_clean();
	}

	/**
	 * Get email subject.
	 */
	public function get_default_subject() {
		return esc_html__( 'Your {site_title} order {order_id} message from {message_date}', 'woo-refund-and-exchange-lite' );
	}

	/**
	 * Get email heading.
	 */
	public function get_default_heading() {
		return esc_html__( 'Thank you', 'woo-refund-and-exchange-lite' );
	}

	/**
	 * Get_content_plain function.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template(
			$this->template_plain,
			array(
				'msg' => $this->msg,
				'email_heading'  => $this->get_heading(),
				'sent_to_admin'  => false,
				'plain_text'     => false,
				'email'          => $this,
			),
			'woo-refund-and-exchange-lite',
			$this->template_base
		);
		return ob_get_clean();
	}

	/**
	 * Initialize Settings Form Fields
	 */
	public function init_form_fields() {
		// translators: %s: list of placeholders.
		$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'woo-refund-and-exchange-lite' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => 'Enable/Disable',
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'yes',
			),
			'subject'    => array(
				'title'       => esc_html__( 'Subject', 'woo-refund-and-exchange-lite' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading'    => array(
				'title'       => esc_html__( 'Heading', 'woo-refund-and-exchange-lite' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'email_type' => array(
				'title'       => 'Email type',
				'type'        => 'select',
				'description' => 'Choose which format of email to send.',
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => esc_html__( 'Plain text', 'woo-refund-and-exchange-lite' ),
					'html'      => esc_html__( 'HTML', 'woo-refund-and-exchange-lite' ),
					'multipart' => esc_html__( 'Multipart', 'woo-refund-and-exchange-lite' ),
				),
			),
		);
	}

}
