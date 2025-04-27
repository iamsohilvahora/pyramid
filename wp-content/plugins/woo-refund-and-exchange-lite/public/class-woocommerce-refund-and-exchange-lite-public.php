<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    woocommerce_refund_and_exchange_lite
 * @subpackage woocommerce_refund_and_exchange_lite/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    woocommerce_refund_and_exchange_lite
 * @subpackage woocommerce_refund_and_exchange_lite/public
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Woocommerce_Refund_And_Exchange_Lite_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $woocommerce_refund_and_exchange_lite    The ID of this plugin.
	 */
	private $woocommerce_refund_and_exchange_lite;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $woocommerce_refund_and_exchange_lite       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $woocommerce_refund_and_exchange_lite, $version ) {
		$this->woocommerce_refund_and_exchange_lite = $woocommerce_refund_and_exchange_lite;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Refund_And_Exchange_Lite_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Refund_And_Exchange_Lite_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->woocommerce_refund_and_exchange_lite, plugin_dir_url( __FILE__ ) . 'css/woocommerce_refund_and_exchange_lite-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Refund_And_Exchange_Lite_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Refund_And_Exchange_Lite_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script( $this->woocommerce_refund_and_exchange_lite, plugin_dir_url( __FILE__ ) . 'js/woocommerce_refund_and_exchange_lite-public.js', array( 'jquery' ), $this->version, false );
		$ajax_nonce = wp_create_nonce( 'ced-rnx-ajax-seurity-string' );

		$user_id = get_current_user_id();
		$myaccount_page = get_option( 'woocommerce_myaccount_page_id' );
		$myaccount_page_url = get_permalink( $myaccount_page );

		$translation_array = array(
			'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
			'ced_rnx_nonce' => $ajax_nonce,
			'myaccount_url' => $myaccount_page_url,
			'return_subject_msg' => esc_html__( 'Please enter refund subject.', 'woo-refund-and-exchange-lite' ),
			'return_reason_msg' => esc_html__( 'Please enter refund reason.', 'woo-refund-and-exchange-lite' ),
		);
		wp_localize_script( $this->woocommerce_refund_and_exchange_lite, 'global_rnx', $translation_array );
		wp_enqueue_script( $this->woocommerce_refund_and_exchange_lite );

	}

	/**
	 * Add template for refund request form.
	 *
	 * @param array $template is a current template.
	 * @since    1.0.0
	 */
	public function ced_rnx_product_return_template( $template ) {
		$ced_rnx_return_request_form_page_id = get_option( 'ced_rnx_return_request_form_page_id' );

		if ( has_filter( 'wpml_object_id' ) ) {
			$ro_pageid = apply_filters( 'wpml_object_id', $ced_rnx_return_request_form_page_id, 'page', false, ICL_LANGUAGE_CODE );
		}
		if ( ( ( '' !== $ced_rnx_return_request_form_page_id ) && is_page( $ced_rnx_return_request_form_page_id ) ) || ( isset( $ro_pageid ) && is_page( $ro_pageid ) ) ) {
			$located = locate_template( 'woo-refund-and-exchange-lite/public/partials/mwb-rnx-lite-refund-request-form.php' );
			if ( ! empty( $located ) ) {

				$new_template = wc_get_template( 'woo-refund-and-exchange-lite/public/partials/mwb-rnx-lite-refund-request-form.php' );
			} else {
				$new_template = MWB_REFUND_N_EXCHANGE_LITE_DIRPATH . 'public/partials/mwb-rnx-lite-refund-request-form.php';
			}
			$template = $new_template;
		}

		$ced_rnx_view_order_msg_page_id = get_option( 'ced_rnx_view_order_msg_page_id' );
		if ( has_filter( 'wpml_object_id' ) ) {
			$ro_pageid = apply_filters( 'wpml_object_id', $ced_rnx_view_order_msg_page_id, 'page', false, ICL_LANGUAGE_CODE );
		}
		if ( ( '' !== $ced_rnx_view_order_msg_page_id ) && ( is_page( $ced_rnx_view_order_msg_page_id ) ) || ( isset( $ro_pageid1 ) && is_page( $ro_pageid1 ) ) ) {
			$located = locate_template( 'woo-refund-and-exchange-lite/public/partials/mwb-rnx-lite-view-order-msg.php' );
			if ( ! empty( $located ) ) {

				$new_template = wc_get_template( 'woo-refund-and-exchange-lite/public/partials/mwb-rnx-lite-view-order-msg.php' );
			} else {
				$new_template = MWB_REFUND_N_EXCHANGE_LITE_DIRPATH . 'public/partials/mwb-rnx-lite-view-order-msg.php';
			}
			$template = $new_template;
		}

		return $template;
	}


	/**
	 * Add refund button on my-account order section.
	 *
	 * @param array $actions is current order action.
	 * @param array $order is a current order.
	 * @since    1.0.0
	 */
	public function ced_rnx_refund_exchange_button( $actions, $order ) {
		$order = new WC_Order( $order );
		$ced_rnx_next_return = true;
		$order_id = $order->get_id();

		$page_id = get_option( 'ced_rnx_view_order_msg_page_id', true );
		$view_order_msg_url = get_permalink( $page_id );
		$view_msg = get_option( 'mwb_wrma_order_message_view', 'no' );
		$ced_rnx_return = get_option( 'mwb_wrma_return_enable', false );
		$view_order_msg_text = get_option( 'mwb_wrma_order_msg_text', false );
		$refund_button_view = get_option( 'mwb_wrma_refund_button_view', false );

		if ( isset( $view_msg ) && 'yes' == $view_msg && isset( $ced_rnx_return ) && 'yes' == $ced_rnx_return ) {
			$view_order_msg_url = add_query_arg( 'order_id', $order_id, $view_order_msg_url );
			$view_order_msg_url = wp_nonce_url( $view_order_msg_url, 'ced-rnx-nonce', 'ced-rnx-nonce' );
			$actions['view_msg']['url'] = $view_order_msg_url;
			if ( isset( $view_order_msg_text ) && ! empty( $view_order_msg_text ) ) {
				$actions['view_msg']['name'] = $view_order_msg_text;
			} else {
				$actions['view_msg']['name'] = __( 'View Order Messages', 'woo-refund-and-exchange-lite' );
			}
		}

		$ced_rnx_made = get_post_meta( $order_id, 'ced_rnx_request_made', true );
		if ( isset( $ced_rnx_made ) && ! empty( $ced_rnx_made ) ) {
			$ced_rnx_next_return = false;
		}

		if ( $ced_rnx_next_return && in_array( 'order-page', $refund_button_view ) ) {
			// Return Request at order detail page.
			$ced_rnx_return = get_option( 'mwb_wrma_return_enable', false );
			if ( 'yes' === $ced_rnx_return ) {

				$statuses = get_option( 'mwb_wrma_return_order_status', array() );
				$order_status = 'wc-' . $order->get_status();

				if ( in_array( $order_status, $statuses ) ) {
					if ( WC()->version < '3.0.0' ) {
						$order_date = date_i18n( 'F d, Y', strtotime( $order->order_date ) );
					} else {
						$order_date = date_i18n( 'F d, Y', strtotime( $order->get_date_created() ) );
					}
					$today_date = date_i18n( 'F d, Y' );
					$order_date = strtotime( $order_date );
					$today_date = strtotime( $today_date );
					$days = $today_date - $order_date;
					$day_diff = floor( $days / ( 60 * 60 * 24 ) );

					$day_allowed = get_option( 'mwb_wrma_return_days', false );

					$return_button_text = get_option( 'mwb_wrma_return_button_text', false );
					if ( isset( $return_button_text ) && ! empty( $return_button_text ) ) {
						$return_button_text = $return_button_text;
					} else {
						$return_button_text = __( 'Refund', 'woo-refund-and-exchange-lite' );
					}

					if ( $day_allowed >= $day_diff && 0 !== $day_allowed ) {

						$ced_rnx_return_request_form_page_id = get_option( 'ced_rnx_return_request_form_page_id' );
						$return_url = get_permalink( $ced_rnx_return_request_form_page_id );
						if ( WC()->version < '3.0.0' ) {
							$order_id = $order->id;
						} else {
							$order_id = $order->get_id();
						}
						$return_url = add_query_arg( 'order_id', $order_id, $return_url );
						$return_url = wp_nonce_url( $return_url, 'ced-rnx-nonce', 'ced-rnx-nonce' );
						$actions['return']['url'] = $return_url;
						$actions['return']['name'] = $return_button_text;

					}
				}
			}
		}
		return $actions;
	}

	/**
	 * This function is to save return request Attachment
	 *
	 * @author MakeWebBetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */
	public function ced_rnx_order_return_attach_files() {

		$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
		if ( $check_ajax ) {
			if ( current_user_can( 'ced-rnx-refund-request' ) ) {
				if ( isset( $_FILES['ced_rnx_return_request_files'] ) ) {
					if ( isset( $_FILES['ced_rnx_return_request_files']['tmp_name'] ) ) {
						$filename = array();
						if ( isset( $_POST['ced_rnx_return_request_order'] ) ) {

							$order_id = sanitize_text_field( wp_unslash( $_POST['ced_rnx_return_request_order'] ) );
						}
						// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- Nonce already verified in 
						$count = count( $_FILES['ced_rnx_return_request_files']['tmp_name'] );
						for ( $i = 0;$i < $count;$i++ ) {
							if ( isset( $_FILES['ced_rnx_return_request_files']['tmp_name'][ $i ] ) ) {
								$directory = ABSPATH . 'wp-content/attachment';
								if ( ! file_exists( $directory ) ) {
									mkdir( $directory, 0755, true );
								}
								if ( isset( $_FILES['ced_rnx_return_request_files']['tmp_name'][ $i ] ) ) {

									$source_path = wc_clean( sanitize_text_field( wp_unslash( $_FILES['ced_rnx_return_request_files']['tmp_name'][ $i ] ) ) );
									if ( isset( $_FILES['ced_rnx_return_request_files']['name'][ $i ] ) ) {
										$target_path = $directory . '/' . $order_id . '-' . sanitize_text_field( wp_unslash( $_FILES['ced_rnx_return_request_files']['name'][ $i ] ) );

										$filename[] = $order_id . '-' . sanitize_text_field( wp_unslash( $_FILES['ced_rnx_return_request_files']['name'][ $i ] ) );
										move_uploaded_file( $source_path, $target_path );

									}
								}
							}
						}

						$request_files = get_post_meta( $order_id, 'ced_rnx_return_attachment', true );

						$pending = true;
						if ( isset( $request_files ) && ! empty( $request_files ) ) {
							foreach ( $request_files as $date => $request_file ) {
								if ( 'pending' === $request_file['status'] ) {
									unset( $request_files[ $date ][0] );
									$request_files[ $date ]['files'] = $filename;
									$request_files[ $date ]['status'] = 'pending';
									$pending = false;
									break;
								}
							}
						}

						if ( $pending ) {
							$request_files = array();
							$date = date_i18n( wc_date_format(), time() );
							$request_files[ $date ]['files'] = $filename;
							$request_files[ $date ]['status'] = 'pending';
						}
						// phpcs:enable
						update_post_meta( $order_id, 'ced_rnx_return_attachment', $request_files );
						echo 'success';
					}
				}
			}
			wp_die();
		}
	}
	/**
	 * This function is to save return request
	 *
	 * @author MakeWebBetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */
	public function ced_rnx_return_product_info_callback() {
		$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
		if ( $check_ajax ) {

			if ( current_user_can( 'ced-rnx-refund-request' ) ) {
				if ( isset( $_POST['orderid'] ) ) {
					$order_id = sanitize_text_field( wp_unslash( $_POST['orderid'] ) );
				} else {
					$order_id = ' ';
				}

				// custom code.
				$order = wc_get_order( $order_id );
				$items = $order->get_items();
				$gift_card_product = false;
				$item_id = '';
				$exp_flag = false;

				foreach ( $items as $key => $item ) {

					$product_id = $item['product_id'];

					$product_types = wp_get_object_terms( $product_id, 'product_type' );
					if ( isset( $product_types[0] ) ) {
						$product_type = $product_types[0]->slug;
						if ( 'wgm_gift_card' == $product_type || 'gw_gift_card' == $product_type ) {
							$gift_card_product = true;
							$item_id = $key;
						}
					}
				}
				if ( $gift_card_product && '' != $item_id ) {

					$coupon = get_post_meta( $order_id, $order_id . '#' . $item_id, true );

					$couponcode = $coupon[0];

					$coupons = new WC_Coupon( $couponcode );

					$usage_count = $coupons->usage_count;

					$exp_date = $coupons->get_data();
					if ( isset( $exp_date['date_expires'] ) && ! empty( $exp_date['date_expires'] ) ) {
						$expiry_date   = $exp_date['date_expires']->date( 'd M Y H:i:s' );
						$now_date      = date_i18n( wc_date_format(), time() ) . ' ' . date_i18n( wc_time_format(), time() );
						$todaydatetime = strtotime( $now_date );
						$expdatetime   = strtotime( $expiry_date );
						$diff          = $expdatetime - $todaydatetime;
						if ( $diff < 0 ) {
							$exp_flag = true;
						}
					}

					if ( $exp_flag ) {
						$response['flag'] = false;
						$response['msg'] = __( 'Your Giftcard has been expired so you can not proceed with the refund. Thanks', 'woo-refund-and-exchange-lite' );
						update_post_meta( $order_id, 'gift_card_hide_refund_button', 'true' );

						echo wp_json_encode( $response );
						wp_die();
					}

					if ( 0 != $usage_count ) {
						$response['flag'] = false;
						$response['msg'] = __( 'Your Giftcard has been used so you can not proceed with the refund. Thanks', 'woo-refund-and-exchange-lite' );
						update_post_meta( $order_id, 'gift_card_hide_refund_button', 'true' );

						echo wp_json_encode( $response );
						wp_die();
					}
				}
				if ( isset( $_POST['subject'] ) ) {
					$subject = sanitize_text_field( wp_unslash( $_POST['subject'] ) );
				} else {
					$subject = ' ';
				}
				if ( isset( $_POST['reason'] ) ) {
					$reason = sanitize_text_field( wp_unslash( $_POST['reason'] ) );
				} else {
					$reason = ' ';
				}
				$ced_post = $_POST;
				if ( is_array( $ced_post ) && ! empty( $ced_post ) ) {
					foreach ( $ced_post as $post_key => $post_value ) {
						if ( is_array( $post_value ) && ! empty( $post_value ) ) {
							foreach ( $post_value as $post_val_key => $post_val_value ) {
								sanitize_text_field( $ced_post[ $post_key ][ $post_key_value ] );
							}
						} else {
							sanitize_text_field( $ced_post[ $post_key ] );
						}
					}
				}

				$products = get_post_meta( $order_id, 'ced_rnx_return_product', true );
				$pending = true;
				if ( isset( $products ) && ! empty( $products ) ) {
					foreach ( $products as $date => $product ) {
						if ( 'pending' == $product['status'] ) {
							$products[ $date ] = $ced_post;
								$products[ $date ]['status'] = 'pending'; // update requested products.
								$pending = false;
								break;
						}
					}
				}
				if ( $pending ) {
					if ( ! is_array( $products ) ) {
						$products = array();
					}
					$products = array();
					$date = date_i18n( wc_date_format(), time() );
					$products[ $date ] = $ced_post;
					$products[ $date ]['status'] = 'pending';
				}

					update_post_meta( $order_id, 'ced_rnx_request_made', true );

					update_post_meta( $order_id, 'ced_rnx_return_product', $products );

					// Send mail to merchant.

					$reason_subject = $subject;

					$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
					$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );

					$message = '<html>
					<body>
						' . do_action( 'wrnx_return_request_before_mail_content', $order_id ) . '
						<style>
							body {
								box-shadow: 2px 2px 10px #ccc;
								color: #767676;
								font-family: Arial,sans-serif;
								margin: 80px auto;
								max-width: 700px;
								padding-bottom: 30px;
								width: 100%;
							}

							h2 {
								font-size: 30px;
								margin-top: 0;
								color: #fff;
								padding: 40px;
								background-color: #557da1;
							}

							h4 {
								color: #557da1;
								font-size: 20px;
								margin-bottom: 10px;
							}

							.content {
								padding: 0 40px;
							}

							.Customer-detail ul li p {
								margin: 0;
							}

							.details .Shipping-detail {
								width: 40%;
								float: right;
							}

							.details .Billing-detail {
								width: 60%;
								float: left;
							}

							.details .Shipping-detail ul li,.details .Billing-detail ul li {
								list-style-type: none;
								margin: 0;
							}

							.details .Billing-detail ul,.details .Shipping-detail ul {
								margin: 0;
								padding: 0;
							}

							.clear {
								clear: both;
							}

							table,td,th {
								border: 2px solid #ccc;
								padding: 15px;
								text-align: left;
							}

							table {
								border-collapse: collapse;
								width: 100%;
							}

							.info {
								display: inline-block;
							}

							.bold {
								font-weight: bold;
							}

							.footer {
								margin-top: 30px;
								text-align: center;
								color: #99B1D8;
								font-size: 12px;
							}
							dl.variation dd {
								font-size: 12px;
								margin: 0;
							}
							.return-request-mail-header{
								text-align:center;
								padding: 10px;
							}
							.return-request-mail-footer{
								text-align:center;
								padding: 10px;
							}
							
						</style>
						<div class="return-request-mail-header header">
							' . $mail_header . '
						</div>	
						<div class="header">
							<h2>' . $reason_subject . '</h2>
						</div>
						<div class="content">

							<div class="reason">
								<h4>' . __( 'Reason of Refund', 'woo-refund-and-exchange-lite' ) . '</h4>
								<p>' . $reason . '</p>
							</div>
							<div class="Order">
								<h4>Order #' . $order_id . '</h4>
								<table>
									<tbody>
										<tr>
											<th>' . __( 'Product', 'woo-refund-and-exchange-lite' ) . '</th>
											<th>' . __( 'Quantity', 'woo-refund-and-exchange-lite' ) . '</th>
											<th>' . __( 'Price', 'woo-refund-and-exchange-lite' ) . '</th>
										</tr>';
										$order = new WC_Order( $order_id );
										$requested_products = $products[ $date ]['products'];

				if ( isset( $requested_products ) && ! empty( $requested_products ) ) {
					$total = 0;
					foreach ( $order->get_items() as $item_id => $item ) {
						$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
						foreach ( $requested_products as $requested_product ) {
							if ( isset( $requested_product['item_id'] ) ) {
								if ( $item_id == $requested_product['item_id'] ) {
									if ( isset( $requested_product['variation_id'] ) && $requested_product['variation_id'] > 0 ) {
										$prod = wc_get_product( $requested_product['variation_id'] );

									} else {
										$prod = wc_get_product( $requested_product['product_id'] );
									}

									$subtotal = $requested_product['price'] * $item['qty'];
									$total += $subtotal;
									if ( WC()->version < '3.1.0' ) {
										$item_meta      = new WC_Order_Item_Meta( $item, $prod );
										$item_meta_html = $item_meta->display( true, true );
									} else {
										$item_meta      = new WC_Order_Item_Product( $item, $prod );
										$item_meta_html = wc_display_item_meta( $item_meta, array( 'echo' => false ) );
									}

									$message .= '<tr>
															<td>' . $item['name'] . '<br>';
										$message .= '<small>' . $item_meta_html . '</small>
																<td>' . $item['qty'] . '</td>
																<td>' . wc_price( $requested_product['price'] * $item['qty'] )/*phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped*/ . '</td>
															</tr>';
								}
							}
						}
					}
				}
										$message .= '<tr>
										<th colspan="2">' . __( 'Refund Total', 'woo-refund-and-exchange-lite' ) . ':</th>
										<td>' . wc_price( $total )/*phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped*/ . '</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="Customer-detail">
							<h4>' . __( 'Customer details', 'woo-refund-and-exchange-lite' ) . '</h4>
							<ul>
								<li><p class="info">
									<span class="bold">' . __( 'Email', 'woo-refund-and-exchange-lite' ) . ': </span>' . get_post_meta( $order_id, '_billing_email', true ) . '
								</p></li>
								<li><p class="info">
									<span class="bold">' . __( 'Tel', 'woo-refund-and-exchange-lite' ) . ': </span>' . get_post_meta( $order_id, '_billing_phone', true ) . '
								</p></li>
							</ul>
						</div>
						<div class="details">
							<div class="Shipping-detail">
								<h4>' . __( 'Shipping Address', 'woo-refund-and-exchange-lite' ) . '</h4>
								' . $order->get_formatted_shipping_address() . '
							</div>
							<div class="Billing-detail">
								<h4>' . __( 'Billing Address', 'woo-refund-and-exchange-lite' ) . '</h4>
								' . $order->get_formatted_billing_address() . '
							</div>
							<div class="clear"></div>
						</div>
						
					</div>
					<div class="return-request-mail-footer footer" style="text-align:center;padding: 10px;">
						' . $mail_footer . '
					</div>

				</body>
				</html>';

				$headers = array();
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$to        = get_option( 'ced_rnx_notification_from_mail' );
				$subject   = get_option( 'ced_rnx_notification_merchant_return_subject' );
				$subject   = str_replace( '[order]', '#' . $order_id, $subject );

				wc_mail( $to, $subject, $message, $headers );

				// Send mail to User that we recieved your request.

				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$to        = get_post_meta( $order_id, '_billing_email', true );
				$headers   = array();
				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$subject   = get_option( 'ced_rnx_notification_return_subject' );
				$subject   = str_replace( '[order]', '#' . $order_id, $subject );
				$message   = stripslashes( get_option( 'ced_rnx_notification_return_rcv' ) );
				$message   = str_replace( '[order]', '#' . $order_id, $message );
				$message   = str_replace( '[siteurl]', home_url(), $message );
				$firstname = get_post_meta( $order_id, '_billing_first_name', true );
				$lname     = get_post_meta( $order_id, '_billing_last_name', true );

				$fullname = $firstname . ' ' . $lname;
				$message  = str_replace( '[username]', $fullname, $message );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );

				$html_content = '<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
				</head>
				<body style="margin: 1% 0 0; padding: 0;">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td style="text-align: center; margin-top: 30px; padding: 10px; color: #99B1D8; font-size: 12px;">
								' . $mail_header . '
							</td>
						</tr>
						<tr>
							<td>
								<table align="center" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Open Sans; max-width: 600px; width: 100%;">
									<tr>
										<td style="padding: 36px 48px; width: 100%; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;">' . $subject . '</td>
									</tr>
									<tr>
										<td style="width:100%; padding: 36px 48px 10px; background-color:#fdfdfd; font-size: 14px; color: #737373;">' . $message . '</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="text-align: center; margin-top: 30px; color: #99B1D8; font-size: 12px;">
								' . $mail_footer . '
							</td>
						</tr>				
					</table>

				</body>
				</html>';

				wc_mail( $to, $subject, $html_content, $headers );

				$order = wc_get_order( $order_id );
				$order->update_status( 'wc-refund-requested', 'User Request to Refund Product' );
				$response['flag'] = true;
				$response['msg'] = __( 'Message send successfully. You have received a notification mail regarding this, Please check your mail. Soon You redirect to the My Account Page. Thanks', 'woo-refund-and-exchange-lite' );

				echo wp_json_encode( $response );
				wp_die();
			}
		}
	}

	/**
	 * This function is to add Return button and Show return products
	 *
	 * @param array $order is current order.
	 * @author MakeWebBetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */
	public function ced_rnx_order_return_button( $order ) {
		global $wp;
		$ced_rnx_return_request_form_page_id = get_option( 'ced_rnx_return_request_form_page_id', true );
		$ced_rnx_return_button_show = true;
		$ced_rnx_next_return = true;

		if ( ! is_user_logged_in() ) {
			$ced_rnx_next_return = false;
		}

		$return_button_text = get_option( 'mwb_wrma_return_button_text', false );
		$refund_button_view = get_option( 'mwb_wrma_refund_button_view', false );
		if ( isset( $return_button_text ) && ! empty( $return_button_text ) ) {
			$return_button_text = $return_button_text;
		} else {
			$return_button_text = __( 'Refund', 'woo-refund-and-exchange-lite' );
		}
		$order_id = $order->get_id();
		$page_id = get_option( 'ced_rnx_view_order_msg_page_id', true );
		$view_order_msg_url = get_permalink( $page_id );
		$ced_rnx_return = get_option( 'mwb_wrma_return_enable', false );
		$view_msg = get_option( 'mwb_wrma_order_message_view', 'no' );
		$redirect_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		if ( isset( $redirect_uri ) ) {
			if ( isset( $view_msg ) && 'yes' == $view_msg && isset( $ced_rnx_return ) && 'yes' == $ced_rnx_return && strpos( $redirect_uri, 'order-received' ) === false ) {
				?>
				<form action="<?php echo esc_html( add_query_arg( 'order_id', $order_id, $view_order_msg_url ) ); ?>" method="post">
					<input type="hidden" value="<?php echo esc_html( $order_id ); ?>" name="order_id">
					<p>
						<input type="submit" class="btn button" value="<?php esc_html_e( 'View Order Messages', 'woo-refund-and-exchange-lite' ); ?>">
					</p>
				</form>
				<?php
			}
		}
		$ced_rnx_enable = get_option( 'ced_rnx_return_exchange_enable', false );
		if ( 'yes' == $ced_rnx_enable ) {
			$order_id = $order->get_id();
			$ced_rnx_made = get_post_meta( $order_id, 'ced_rnx_request_made', true );
			if ( isset( $ced_rnx_made ) && ! empty( $ced_rnx_made ) ) {
				$ced_rnx_next_return = false;
			}
		}

		$order_total = $order->get_total();
		$return_min_amount = get_option( 'ced_rnx_return_minimum_amount', false );

		// Return Request at order detail page.
		$ced_rnx_return = get_option( 'mwb_wrma_return_enable', false );
		if ( 'yes' === $ced_rnx_return ) {
			if ( WC()->version < '3.0.0' ) {
				$order_id = $order->id;
			} else {
				$order_id = $order->get_id();
			}
			$statuses = get_option( 'mwb_wrma_return_order_status', array() );
			$order_status = 'wc-' . $order->get_status();
			$product_datas = get_post_meta( $order_id, 'ced_rnx_return_product', true );
			if ( isset( $product_datas ) && ! empty( $product_datas ) ) {
				?>
				<h2><?php esc_html_e( 'Refund Requested Product', 'woo-refund-and-exchange-lite' ); ?></h2>
				<?php

				$request_status = true;
				foreach ( $product_datas as $key => $product_data ) {
					$date = date_create( $key );
					$date_format = get_option( 'date_format' );
					$date = date_format( $date, $date_format );

					?>
					<p><?php esc_html_e( 'Following product Refund request made on', 'woo-refund-and-exchange-lite' ); ?> <b><?php echo esc_html( $date ); ?>.</b></p>
					<table class="shop_table order_details">
						<thead>
							<tr>
								<th class="product-name"><?php esc_html_e( 'Product', 'woo-refund-and-exchange-lite' ); ?></th>
								<th class="product-total"><?php esc_html_e( 'Total', 'woo-refund-and-exchange-lite' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$line_items  = $order->get_items();
							if ( is_array( $line_items ) && ! empty( $line_items ) ) {
								update_post_meta( $order_id, 'ced_rnx_refund_new_line_items', $line_items );
							}
							$line_items = get_post_meta( $order_id, 'ced_rnx_refund_new_line_items', true );

							$return_products = $product_data['products'];

							foreach ( $line_items as $item_id => $item ) {
								foreach ( $return_products as $return_product ) {
									if ( isset( $return_product['item_id'] ) ) {
										if ( $return_product['item_id'] == $item_id ) {
											?>
											<tr>
											<td class="product-name">
												<?php
												$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );

												$is_visible        = $product && $product->is_visible();
												$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

												echo esc_html( $product_permalink ) ? sprintf( '<a href="%s">%s</a>', esc_html( $product_permalink ), esc_html( $product->get_name() ) ) : esc_html( $product->get_name() );
												echo '<strong class="product-quantity">' . sprintf( '&times; %s', esc_html( $return_product['qty'] ) ) . '</strong>';

												do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

												if ( WC()->version < '3.0.0' ) {
													$order->display_item_meta( $item );
													$order->display_item_downloads( $item );
												} else {
													wc_display_item_meta( $item );
													wc_display_item_downloads( $item );
												}

												do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
												?>
											</td>
											<td class="product-total">
											<?php
												echo wp_kses_post( wc_price( $return_product['price'] * $return_product['qty'] ) );
											?>
												</td>
											</tr>
											<?php
										}
									}
								}
							}
							?>
							<tr>
								<th scope="row"><?php esc_html_e( 'Refund Amount', 'woo-refund-and-exchange-lite' ); ?></th>
								<th><?php echo wp_kses_post( wc_price( $product_data['amount'] ) ); ?></th>
							</tr>
							<?php
							$added_fees = get_post_meta( $order_id, 'ced_rnx_return_added_fee', true );
							if ( isset( $added_fees ) ) {
								if ( is_array( $added_fees ) ) {
									foreach ( $added_fees as $da => $added_fee ) {
										if ( ! empty( $added_fee ) ) {
											if ( $da == $key ) {
												?>
												<tr>
													<th colspan="2"><?php esc_html_e( 'Extra Cost', 'woo-refund-and-exchange-lite' ); ?></th>
												</tr>
												<?php
												foreach ( $added_fee as $fee ) {
													?>
													<tr>
														<th><?php echo esc_html( $fee['text'] ); ?></th>
														<td><?php echo esc_html( wc_price( $fee['val'] ) );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped; ?></td>
													</tr>
													<?php
													$product_data['amount'] -= $fee['val'];
												}
											}
										}
									}
									?>
									<tr>
										<th scope="row"><?php esc_html_e( 'Total Refund Amount', 'woo-refund-and-exchange-lite' ); ?></th>
										<th><?php echo esc_html( wc_price( $product_data['amount'] ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
									</tr>
									<?php
								}
							}
							?>
						</tbody>
					</table>	
					<?php

					if ( in_array( $order_status, $statuses ) ) {
						if ( 'pending' == $product_data['status'] ) {
							$request_status = false;
							if ( WC()->version < '3.0.0' ) {
								$order_id = $order->id;
								$order_date = date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) );
							} else {
								$order = new WC_Order( $order );
								$order_id = $order->get_id();
								$order_date = date_i18n( get_option( 'date_format' ), strtotime( $order->get_date_created() ) );
							}
								$today_date = time(); // or your date as well.
								$order_date = strtotime( $order_date );
								$days = $today_date - $order_date;
								$day_diff = floor( $days / ( 60 * 60 * 24 ) );
								$day_allowed = get_option( 'mwb_wrma_return_days', false );

							if ( $day_allowed >= $day_diff && 0 != $day_allowed ) {
								if ( $ced_rnx_return_button_show ) {
									$ced_rnx_return_button_show = false;

									$page_id = $ced_rnx_return_request_form_page_id;
									$return_url = get_permalink( $page_id );
									$return_url = add_query_arg( 'order_id', $order_id, $return_url );
									$return_url = wp_nonce_url( $return_url, 'ced-rnx-nonce', 'ced-rnx-nonce' );
									?>
										<form action="<?php echo esc_html( $return_url ); ?>" method="post">
											<input type="hidden" value="<?php echo esc_html( $order_id ); ?>" name="order_id">
											<p>
												<input type="submit" class="btn button" value="<?php esc_html_e( 'Update Request', 'woo-refund-and-exchange-lite' ); ?>" name="ced_update_return_request">
											</p>
										</form>
										<?php
								}
							}
						}
						if ( 'complete' === $product_data['status'] ) {
							$appdate = date_create( $product_data['approve_date'] );
							$format = get_option( 'date_format' );
							$appdate = date_format( $appdate, $format );
							?>
								<p><?php esc_html_e( 'Above product Refund request is approved on', 'woo-refund-and-exchange-lite' ); ?> <b><?php echo esc_html( $appdate ); ?>.</b></p>
								<?php
						}

						if ( 'cancel' == $product_data['status'] ) {
							$appdate = date_create( $product_data['cancel_date'] );
							$format = get_option( 'date_format' );
							$appdate = date_format( $appdate, $format );
							?>
								<p><?php esc_html_e( 'Above product Refund request is canceled on', 'woo-refund-and-exchange-lite' ); ?> <b><?php echo esc_html( $appdate ); ?>.</b></p>
								<?php
						}
					}

						$statuses = get_option( 'mwb_wrma_return_order_status', array() );
						$order_status = 'wc-' . $order->get_status();
					if ( in_array( $order_status, $statuses ) ) {
						if ( $request_status ) {
							if ( WC()->version < '3.0.0' ) {
								$order_id = $order->id;
								$order_date = date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) );
							} else {
								$order_id = $order->get_id();
								$order_date = date_i18n( get_option( 'date_format' ), strtotime( $order->get_date_created() ) );
							}
							$today_date = time(); // or your date as well.
							$order_date = strtotime( $order_date );
							$days = $today_date - $order_date;
							$day_diff = floor( $days / ( 60 * 60 * 24 ) );
							$day_allowed = get_option( 'mwb_wrma_return_days', false );

							if ( $day_allowed >= $day_diff && 0 != $day_allowed ) {
								$page_id = $ced_rnx_return_request_form_page_id;
								$return_url = get_permalink( $page_id );

								if ( $ced_rnx_next_return ) {
									if ( $ced_rnx_return_button_show ) {

										$ced_rnx_return_button_show = false;
										$return_url = add_query_arg( 'order_id', $order_id, $return_url );
										$return_url = wp_nonce_url( $return_url, 'ced-rnx-nonce', 'ced-rnx-nonce' );
										if ( isset( $refund_button_view ) && in_array( 'thank-you-page', $refund_button_view ) ) {
											if ( is_checkout() && ! empty( $wp->query_vars['order-received'] ) ) {
												?>
												<form action="<?php echo esc_html( $return_url ); ?>" method="post">
													<input type="hidden" value="<?php echo esc_html( $order_id ); ?>" name="order_id">
													<p><input type="submit" class="btn button" value="<?php echo esc_html( $return_button_text ); ?>" name="ced_new_return_request" ></p>
												</form>
												<?php
											} else if ( isset( $refund_button_view ) && in_array( get_the_title(), $refund_button_view ) ) {
												?>
												<form action="<?php echo esc_html( $return_url ); ?>" method="post">
													<input type="hidden" value="<?php echo esc_html( $order_id ); ?>" name="order_id">
													<p><input type="submit" class="btn button" value="<?php echo esc_html( $return_button_text ); ?>" name="ced_new_return_request" ></p>
												</form>
												<?php
											}
										}
									}
								}
							}
						}
					}
				}
			}
			if ( in_array( $order_status, $statuses ) ) {
				if ( WC()->version < '3.0.0' ) {
					$order_id = $order->id;
					$order_date = date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) );
				} else {
					$order_id = $order->get_id();
					$order_date = date_i18n( get_option( 'date_format' ), strtotime( $order->get_date_created() ) );
				}
				$today_date = date_i18n( get_option( 'date_format' ) );
				$order_date = strtotime( $order_date );
				$today_date = strtotime( $today_date );

				$days = $today_date - $order_date;
				$day_diff = floor( $days / ( 60 * 60 * 24 ) );
				$day_allowed = get_option( 'mwb_wrma_return_days', false );
				if ( $day_allowed >= $day_diff && 0 != $day_allowed ) {
					$page_id = $ced_rnx_return_request_form_page_id;
					$return_url = get_permalink( $page_id );

					if ( $ced_rnx_next_return ) {
						if ( $ced_rnx_return_button_show ) {
							$ced_rnx_return_button_show = false;
							$return_url = add_query_arg( 'order_id', $order_id, $return_url );
							$return_url = wp_nonce_url( $return_url, 'ced-rnx-nonce', 'ced-rnx-nonce' );
							if ( isset( $refund_button_view ) && in_array( 'thank-you-page', $refund_button_view ) ) {
								if ( is_checkout() && ! empty( $wp->query_vars['order-received'] ) ) {
									?>
									<form action="<?php echo esc_html( $return_url ); ?>" method="post">
										<input type="hidden" value="<?php echo esc_html( $order_id ); ?>" name="order_id">
										<p><input type="submit" class="btn button" value="<?php echo esc_html( $return_button_text ); ?>" name="ced_new_return_request"></p>
									</form>
									<?php
								} else if ( isset( $refund_button_view ) && in_array( get_the_title(), $refund_button_view ) ) {
									?>
									<form action="<?php echo esc_html( $return_url ); ?>" method="post">
										<input type="hidden" value="<?php echo esc_html( $order_id ); ?>" name="order_id">
										<p><input type="submit" class="btn button" value="<?php echo esc_html( $return_button_text ); ?>" name="ced_new_return_request"></p>
									</form>
									<?php
								}
							}
						}
					}
				}
			}
		}

	}


}
