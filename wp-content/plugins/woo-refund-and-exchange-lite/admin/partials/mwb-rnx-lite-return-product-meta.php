<?php
/**
 * Exit if accessed directly
 *
 * @package woocommerce_refund_and_exchange_lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Show Return Product detail on Order Page on admin Side.

if ( ! is_int( $thepostid ) ) {
	$thepostid = $post->ID;
}
if ( ! is_object( $theorder ) ) {
	$theorder = wc_get_order( $thepostid );
}

$order_obj = $theorder;
if ( WC()->version < '3.0.0' ) {
	$order_id = $order_obj->id;
} else {
	$order_id = $order_obj->get_id();
}
$return_datas = get_post_meta( $order_id, 'ced_rnx_return_product', true );
$line_items   = $order_obj->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
if ( is_array( $line_items ) && ! empty( $line_items ) ) {
	update_post_meta( $order_id, 'ced_rnx_new_refund_line_items', $line_items );
}
$line_items = get_post_meta( $order_id, 'ced_rnx_new_refund_line_items', true );

if ( isset( $return_datas ) && ! empty( $return_datas ) ) {
	foreach ( $return_datas as $key => $return_data ) {
		$date        = date_create( $key );
		$date_format = get_option( 'date_format' );
		$date        = date_format( $date, $date_format );
		?>
		<p><?php esc_html_e( 'Following product refund request made on', 'woo-refund-and-exchange-lite' ); ?> <b><?php echo esc_html( $date ); ?>.</b></p>
		<div>
		<div id="ced_rnx_return_wrapper">
			<table>
				<thead>
					<tr>
						<th><?php esc_html_e( 'Item', 'woo-refund-and-exchange-lite' ); ?></th>
						<th><?php esc_html_e( 'Name', 'woo-refund-and-exchange-lite' ); ?></th>
						<th><?php esc_html_e( 'Cost', 'woo-refund-and-exchange-lite' ); ?></th>
						<th><?php esc_html_e( 'Qty', 'woo-refund-and-exchange-lite' ); ?></th>
						<th><?php esc_html_e( 'Total', 'woo-refund-and-exchange-lite' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$total = 0;

				$return_products = $return_data['products'];
				foreach ( $line_items as $item_id => $item ) {
					foreach ( $return_products as $returnkey => $return_product ) {
						if ( $item_id == $return_product['item_id'] ) {
							$refund_product_detail = $order_obj->get_meta_data();
							foreach ( $refund_product_detail as $rpd_value ) {
								$refund_product_data = $rpd_value->get_data();
								if ( 'ced_rnx_return_product' == $refund_product_data['key'] ) {
									$refund_product_values = $refund_product_data['value'];
									foreach ( $refund_product_values as $rpv_value ) {
										$refund_product_values1 = $rpv_value['products'];
										foreach ( $refund_product_values1 as $rpv1_value ) {
											$refund_product_id = $rpv1_value['product_id'];
											$get_return_product = wc_get_product( $refund_product_id );
											$new_refund_image = wp_get_attachment_image_src( get_post_thumbnail_id( $refund_product_id ), 'single-post-thumbnail' );
											$refund_product_new[] = array(
												'name'  => $get_return_product->get_name(),
												'sku'   => $get_return_product->get_sku(),
												'image' => $new_refund_image[0],
											);
										}
									}
								}
							}
							$_product  = $item->get_product();
							$item_meta = wc_get_order_item_meta( $item_id, $key );
							$thumbnail     = $_product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $_product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
							?>
							<tr>
								<td class="thumb">
								<?php
								if ( isset( $refund_product_new[ $returnkey ]['image'] ) && ! empty( $refund_product_new[ $returnkey ]['image'] ) ) {
									?>
									<div class="wc-order-item-thumbnail"><img src ="<?php echo esc_attr( $refund_product_new[ $returnkey ]['image'] ); ?>"></div>
									<?php
								}
								?>
								</td>
								<td class="name">
								<?php
								if ( isset( $refund_product_new[ $returnkey ]['name'] ) && ! empty( $refund_product_new[ $returnkey ]['name'] ) ) {
									echo esc_html( $refund_product_new[ $returnkey ]['name'] );
								}
								if ( isset( $refund_product_new[ $returnkey ]['sku'] ) && ! empty( $refund_product_new[ $returnkey ] ) ) {
									echo '<div class="wc-order-item-sku"><strong>' . esc_html_e( 'SKU:', 'woo-refund-and-exchange-lite' ) . '</strong> ' . esc_html( $refund_product_new[ $returnkey ]['sku'] ) . '</div>';
								}
								if ( ! empty( $item['variation_id'] ) ) {
									echo '<div class="wc-order-item-variation"><strong>' . esc_html_e( 'Variation ID:', 'woo-refund-and-exchange-lite' ) . '</strong> ';
									if ( ! empty( $item['variation_id'] ) && 'product_variation' === get_post_type( $item['variation_id'] ) ) {
										echo esc_html( $item['variation_id'] );
									} elseif ( ! empty( $item['variation_id'] ) ) {
										echo esc_html( $item['variation_id'] ) . ' (' . esc_html_e( 'No longer exists', 'woo-refund-and-exchange-lite' ) . ')';
									}
									echo '</div>';
								}
								if ( WC()->version < '3.1.0' ) {
									$item_meta      = new WC_Order_Item_Meta( $item, $_product );
									$item_meta->display();
								} else {
									$item_meta      = new WC_Order_Item_Product( $item, $_product );
									wc_display_item_meta( $item_meta );
								}
								?>
								</td>
								<td><?php echo wp_kses_post( wc_price( $return_product['price'] ) ); ?></td>
								<td><?php echo esc_html( $return_product['qty'] ); ?></td>
								<td><?php echo wp_kses_post( wc_price( $return_product['price'] * $return_product['qty'] ) ); ?></td>
							</tr>
							<?php
							$total += $return_product['price'] * $return_product['qty'];
						}
					}
				}
				?>
					<tr>
						<th colspan="4"><?php esc_html_e( 'Total Amount', 'woo-refund-and-exchange-lite' ); ?></th>
						<th><?php echo wp_kses_post( wc_price( $total ) ); ?></th>
					</tr>
				</tbody>
			</table>	
		</div>
		<div class="ced_rnx_extra_reason ced_rnx_extra_reason_for_refund">
		<?php


		if ( 'pending' === $return_data['status'] ) {
			?>
			<input type="hidden" value="<?php echo esc_attr( ced_rnx_lite_currency_seprator( $return_data['amount'] ) ); ?>" id="ced_rnx_refund_amount">
			<input type="hidden" value="<?php echo esc_attr( $return_data['subject'] ); ?>" id="ced_rnx_refund_reason">
			<?php
		}
		?>
		<p><strong>	
		<?php esc_html_e( 'Refund Amount', 'woo-refund-and-exchange-lite' ); ?> :</strong> <?php echo wp_kses_post( wc_price( $return_data['amount'] ) ); ?> <input type="hidden" name="ced_rnx_total_amount_for_refund" class="ced_rnx_total_amount_for_refund" value="<?php echo esc_attr( ced_rnx_lite_currency_seprator( $return_data['amount'] ) ); ?>"></p>
		<div class="ced_rnx_reason">	
			<p><strong><?php esc_html_e( 'Subject', 'woo-refund-and-exchange-lite' ); ?> :</strong><i> <?php echo esc_html( $return_data['subject'] ); ?></i></p></p>
			<p><b><?php esc_html_e( 'Reason', 'woo-refund-and-exchange-lite' ); ?> :</b></p>
			<p><?php echo esc_html( $return_data['reason'] ); ?></p>
			<?php
			$req_attachments = get_post_meta( $order_id, 'ced_rnx_return_attachment', true );

			if ( isset( $req_attachments ) && ! empty( $req_attachments ) ) {
				?>
				<p><b><?php esc_html_e( 'Attachment', 'woo-refund-and-exchange-lite' ); ?> :</b></p>
				<?php
				if ( is_array( $req_attachments ) ) {
					foreach ( $req_attachments as $da => $attachments ) {
						if ( $da == $key ) {
							$count = 1;
							foreach ( $attachments['files'] as $attachment ) {
								if ( $attachment != $order_id . '-' ) {
									?>
									<a href="<?php echo esc_url( content_url() ); ?>/attachment/<?php echo esc_html( $attachment ); ?>" target="_blank"><?php echo esc_html( 'Attachment', 'woo-refund-and-exchange-lite' ); ?>-<?php echo esc_html( $count ); ?></a>
									<?php
									$count++;
								}
							}
							break;
						}
					}
				}
			}
			if ( 'pending' == $return_data['status'] ) {
				?>
				<p id="ced_rnx_return_package">
				<input type="button" value="<?php esc_attr_e( 'Accept Request', 'woo-refund-and-exchange-lite' ); ?>" class="button" id="ced_rnx_accept_return" data-orderid="<?php echo esc_html( $order_id ); ?>" data-date="<?php echo esc_html( $key ); ?>">
				<input type="button" value="<?php esc_attr_e( 'Cancel Request', 'woo-refund-and-exchange-lite' ); ?>" class="button" id="ced_rnx_cancel_return" data-orderid="<?php echo esc_html( $order_id ); ?>" data-date="<?php echo esc_attr( $key ); ?>">
				</p>
				<?php
			}
			?>
		</div>
		<div class="ced_rnx_return_loader">
			<img src="<?php esc_url( admin_url() ); ?>images/spinner-2x.gif">
		</div>
		</div>	
		</div>
		<p>
		<?php
		if ( 'complete' == $return_data['status'] ) {
			?>
			<input type="hidden" value="<?php echo esc_html( ced_rnx_lite_currency_seprator( $return_data['amount'] ) ); ?>" id="ced_rnx_refund_amount">
			<input type="hidden" value="<?php echo esc_html( $return_data['subject'] ); ?>" id="ced_rnx_refund_reason">
			<?php

			$approve_date = date_create( $return_data['approve_date'] );
			$date_format = get_option( 'date_format' );
			$approve_date = date_format( $approve_date, $date_format );
			$ced_rnx_left_amount_done = get_post_meta( $order_id, 'ced_rnx_left_amount_done', true );

				esc_html_e( 'Following product refund request is approved on', 'woo-refund-and-exchange-lite' );
			?>
				<b><?php echo esc_html( $approve_date ); ?>.</b>
			<?php if ( 'yes' != $ced_rnx_left_amount_done ) { ?>
				<input type="button" name="ced_rnx_left_amount" class="button button-primary" data-orderid="<?php echo esc_html( $order_id ); ?>" id="ced_rnx_left_amount" Value="Refund Amount" > 
				<?php
			}

			$ced_rnx_manage_stock_for_return = get_post_meta( $order_id, 'ced_rnx_manage_stock_for_return', true );
			if ( '' == $ced_rnx_manage_stock_for_return ) {
				$ced_rnx_manage_stock_for_return = 'yes';
			}
			$manage_stock = get_option( 'mwb_wrma_return_request_manage_stock' );
			if ( 'yes' == $manage_stock && 'yes' == $ced_rnx_manage_stock_for_return ) {
				?>
				<div id="ced_rnx_stock_button_wrapper"><?php esc_html_e( 'When Product Back in stock then for stock management click on ', 'woo-refund-and-exchange-lite' ); ?> <input type="button" name="ced_rnx_stock_back" class="button button-primary" id="ced_rnx_stock_back" data-type="ced_rnx_return" data-orderid="<?php echo esc_html( $order_id ); ?>" Value="Manage Stock" ></div> 
				<?php
			}
		}
		if ( 'cancel' == $return_data['status'] ) {
			$approve_date = date_create( $return_data['cancel_date'] );
			$approve_date = date_format( $approve_date, 'F d, Y' );

			esc_html_e( 'Following product refund request is canceled on', 'woo-refund-and-exchange-lite' );
			?>
			<b><?php echo esc_html( $approve_date ); ?>.</b>
			<?php
		}
		?>
		</p>
		<hr/>
		<?php
	}
} else {
	?>
<p><?php esc_html_e( 'No request from customer', 'woo-refund-and-exchange-lite' ); ?></p>
	<?php
}
?>
