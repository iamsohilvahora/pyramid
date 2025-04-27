<?php
/**
 * Exit if accessed directly
 *
 * @package woocommerce_refund_and_exchange_lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

?>
<div class="mwb_admin_order_msg_wrapper">
	<div class="mwb_order_msg_reload_notice_wrapper">
		<p class="mwb_order_msg_sent_notice"><strong><?php esc_html_e( 'Messages Refreshed Successfully.', 'woo-refund-and-exchange-lite' ); ?></strong></p>
	</div>
	<div class="mwb_admin_order_msg_history_container">
		<div class="mwb_order_msg_history_title">
			<h4 class="mwb-order-heading">
				<?php esc_html_e( 'Message History', 'woo-refund-and-exchange-lite' ); ?>
			<a href="" class="mwb_wrma_reload_messages">
				<img src="<?php echo esc_url( MWB_REFUND_N_EXCHANGE_LITE_URL ) . '/public/images/reload-icon.png'; ?>" class="reload-icon">
			</a>
			</h4>
		</div>
		<div  class="mwb_admin_order_msg_sub_container">
			<?php
			$mwb_order_messages = get_option( $order_id . '-mwb_cutomer_order_msg', array() );
			if ( isset( $mwb_order_messages ) && is_array( $mwb_order_messages ) && ! empty( $mwb_order_messages ) ) {
				foreach ( array_reverse( $mwb_order_messages ) as $o_key => $o_val ) {
					foreach ( $o_val as $om_key => $om_val ) {
						?>
						<div class="mwb_order_msg_main_container mwb_order_messages">
							<div>
								<div class="mwb_order_msg_sender"><?php echo esc_html( ( 'Customer' === $om_val['sender'] ) ? __( 'Customer', 'woo-refund-and-exchange-lite' ) : __( 'Shop Manager', 'woo-refund-and-exchange-lite' ) ); ?></div>
								<span class="mwb_order_msg_date"><?php echo esc_html( get_date_from_gmt( gmdate( 'Y-m-d h:i a', $om_key ), 'Y-m-d h:i a' ) ); ?></span>
							</div>
							<div class="mwb_order_msg_detail_container">
								<span><?php echo esc_html( $om_val['msg'] ); ?></span>
							</div>
							<?php if ( isset( $om_val['files'] ) && ! empty( $om_val['files'] ) ) { ?>
								<hr>
								<div class="mwb_order_msg_attach_container">
									<div class="mwb_order_msg_attachments_title"><?php esc_html_e( 'Message attachments:', 'woo-refund-and-exchange-lite' ); ?></div>
									<?php
									foreach ( $om_val['files'] as $fkey => $fval ) {
										if ( ! empty( $fval['name'] ) ) {
											$is_image = $fval['img'];
											?>
											<div class="mwb_order_msg_single_attachment">
												<a target="_blank" href="<?php echo esc_url( get_home_url() ) . '/wp-content/attachment/' . esc_html( $order_id ) . '-' . esc_html( $fval['name'] ); ?>">
													<img class="mwb_order_msg_attachment_thumbnail" src="<?php echo $is_image ? esc_url( get_home_url() ) . '/wp-content/attachment/' . esc_html( $order_id ) . '-' . esc_html( $fval['name'] ) : esc_url( MWB_REFUND_N_EXCHANGE_LITE_URL ) . '/admin/images/attachment.png'; ?>">
													<span class="mwb_order_msg_attachment_file_name"><?php echo esc_html( $fval['name'] ); ?></span>
												</a>
											</div>
										<?php } ?>
									<?php } ?>
								</div>
							<?php } ?>
						</div>
						<?php
					}
				}
			}
			?>
		</div>
	</div>
	<div class="mwb_order_msg_notice_wrapper">
	</div>
	<div class="mwb_admin_order_msg_container">
		<form id="mwb_order_new_msg_form" method="post" enctype="multipart/form-data" action="">
			<div class="mwb_order_msg_title"><h4 class="mwb-order-heading"><?php esc_html_e( 'Add a message', 'woo-refund-and-exchange-lite' ); ?></h4></div>
			<textarea id="mwb_order_new_msg" name="mwb_order_new_msg" placeholder="<?php esc_html_e( 'Write a message you want to send to the Customer.', 'woo-refund-and-exchange-lite' ); ?>" maxlength="10000" rows="5"></textarea>
			<div>
				<label for="mwb_order_msg_attachment"> <?php esc_html_e( 'Attach files ', 'woo-refund-and-exchange-lite' ); ?></label>
			</div>
			<div class="mwb-order-msg-attachment-wrapper">
				<input type="file" id="mwb_order_msg_attachment" name="mwb_order_msg_attachment[]" multiple >
				<div class="mwb-order-msg-btn">
					<button type="submit" class="button button-primary" id="mwb_order_msg_submit" name="mwb_order_msg_submit" data-id="<?php echo esc_attr( $order_id ); ?>"><?php esc_html_e( 'Send', 'woo-refund-and-exchange-lite' ); ?> </button>
				</div>
			</div>	
		</form>
	</div>
</div>
