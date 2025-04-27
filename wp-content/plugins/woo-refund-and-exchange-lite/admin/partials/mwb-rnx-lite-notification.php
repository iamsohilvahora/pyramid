<?php
/**
 * Exit if accessed directly.
 *
 * @package woocommerce_refund_and_exchange_lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Notification setting page for Refund and Exchange Product on admin side.
$mwb_rnx_tab = 'basic';
if ( isset( $_GET['tab'] ) ) {
	$mwb_rnx_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
}
$refund_active = '';
$exchange_active = '';
$basic_active = '';
$ced_rnx_help_section = '';
$return_ship_label_setting_active = '';
$ced_rnx_addon_section = '';
if ( 'refund' == $mwb_rnx_tab ) {
	$refund_active = 'nav-tab-active';
} elseif ( 'exchange' == $mwb_rnx_tab ) {
	$exchange_active = 'nav-tab-active';
} elseif ( 'return_ship_label_setting' == $mwb_rnx_tab ) {
	$return_ship_label_setting_active = 'nav-tab-active';
} elseif ( 'ced_rnx_help_section' == $mwb_rnx_tab ) {
	$ced_rnx_help_section = 'nav-tab-active';
} elseif ( 'ced_rnx_addon_section' == $mwb_rnx_tab ) {
	$ced_rnx_addon_section = 'nav-tab-active';
} else {
	$basic_active = 'nav-tab-active';
}
if ( isset( $_POST['ced_rnx_noti_save_basic'] ) ) {

	$ced_nonce = isset( $_REQUEST['ced-rnx-nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ced-rnx-nonce'] ) ) : '';
	if ( wp_verify_nonce( $ced_nonce, 'ced-rnx-nonce' ) ) {
		?>
		<div class="notice notice-success is-dismissible">
			<p><strong><?php esc_html_e( 'Settings saved.', 'woo-refund-and-exchange-lite' ); ?></strong></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notices.', 'woo-refund-and-exchange-lite' ); ?></span>
			</button>
		</div>
		<?php

		unset( $_POST['ced_rnx_noti_save_basic'] );
		$mwb_post = $_POST;
		foreach ( $mwb_post as $k => $val ) {
			if ( is_array( $val ) ) {
				foreach ( $val as $a => $b ) {
					if ( empty( $b ) & 0 != $b ) {
						unset( $val[ $a ] );
					}
				}
			}
			if ( 'ced_rnx_notification_mail_header' == $k || 'ced_rnx_notification_mail_footer' == $k ) {
				$val = wpautop( $val );
			}
			update_option( $k, $val );
		}
	}
}
if ( isset( $_POST['ced_rnx_noti_save_return'] ) ) {
	$ced_nonce = isset( $_REQUEST['ced-rnx-nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ced-rnx-nonce'] ) ) : '';
	if ( wp_verify_nonce( $ced_nonce, 'ced-rnx-nonce' ) ) {
		?>
		<div class="notice notice-success is-dismissible">
			<p><strong><?php esc_html_e( 'Settings saved.', 'woo-refund-and-exchange-lite' ); ?></strong></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notices.', 'woo-refund-and-exchange-lite' ); ?></span>
			</button>
		</div>
		<?php
		unset( $_POST['ced_rnx_noti_save_return'] );
		$mwb_post = $_POST;
		foreach ( $mwb_post as $k => $val ) {
			if ( 'ced_rnx_notification_return_approve' == $k || 'ced_rnx_notification_return_rcv' == $k || 'ced_rnx_notification_return_cancel' == $k ) {
				$val = wpautop( $val );
			}
			update_option( $k, $val );
		}
	}
}
?>
<div class="wrap ced_rnx_notification">
	<h2><?php esc_html_e( 'Notification Setting', 'woo-refund-and-exchange-lite' ); ?></h2>
	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a class="nav-tab <?php echo esc_attr( $basic_active ); ?>" href="<?php esc_url( admin_url() ); ?>admin.php?page=ced-rnx-notification&amp;tab=basic"><?php esc_html_e( 'Basic', 'woo-refund-and-exchange-lite' ); ?></a>
		<a class="nav-tab <?php echo esc_html( $refund_active ); ?>" href="<?php esc_url( admin_url() ); ?>admin.php?page=ced-rnx-notification&amp;tab=refund"><?php esc_html_e( 'Refund', 'woo-refund-and-exchange-lite' ); ?></a>
		<a class="nav-tab <?php echo esc_html( $exchange_active ); ?>" href="<?php esc_url( admin_url() ); ?>
		admin.php?page=ced-rnx-notification&amp;tab=exchange"><?php esc_html_e( 'Exchange', 'woo-refund-and-exchange-lite' ); ?></a>
		<a class="nav-tab <?php echo esc_html( $return_ship_label_setting_active ); ?>" href="<?php esc_url( admin_url() ); ?>admin.php?page=ced-rnx-notification&amp;tab=return_ship_label_setting"><?php esc_html_e( 'Return Ship Label', 'woo-refund-and-exchange-lite' ); ?></a>
	</nav>
	<a href="<?php echo esc_html( admin_url( 'admin.php?page=wc-settings&tab=ced_rnx_setting&section=refund' ) ); ?>"><input type="button" value="<?php esc_html_e( 'GO TO SETTING', 'woo-refund-and-exchange-lite' ); ?>" class="ced-rnx-save-button button button-primary" style="float:right;"></a></div>
	<div class="clear ced-rnx-main-section">
		<?php
		// Basic Tab of Notification setting.
		if ( 'basic' == $mwb_rnx_tab ) {
			$predefined_return_reason = get_option( 'ced_rnx_return_predefined_reason', false );
			$predefined_exchange_reason = get_option( 'ced_rnx_exchange_predefined_reason', false );
			?>
			<form enctype="multipart/form-data" action="" class="ced-main-form" id="mainform" method="post">
				<h2 id="rnx_mail_setting" class="ced_rnx_basic_setting ced_rnx_slide_active"><?php esc_html_e( 'Mail Setting', 'woo-refund-and-exchange-lite' ); ?></h2>
				<input type="hidden" name="ced-rnx-nonce" name="ced-rnx-nonce" value="<?php echo esc_html( wp_create_nonce( 'ced-rnx-nonce' ) ); ?>">
				<div id="rnx_mail_setting_wrapper">
					<table class="form-table ced_rnx_notification_section">
						<tbody>
							<tr valign="top">
								<th class="titledesc" scope="row">
									<label for="ced_rnx_notification_from_name"><?php esc_html_e( 'From Name', 'woo-refund-and-exchange-lite' ); ?></label>
								</th>
								<td class="forminp forminp-text">
									<?php
									$admin_name = get_option( 'blogname' );
									$fname = get_option( 'ced_rnx_notification_from_name', false );
									if ( empty( $fname ) ) {
										$fname = $admin_name;
									}
									?>
									<input type="text" placeholder="" class="input-text" value="<?php echo esc_html( $fname ); ?>" style="" id="ced_rnx_notification_from_name" name="ced_rnx_notification_from_name">
								</td>
							</tr>
							<tr valign="top">
								<th class="titledesc" scope="row">
									<label for="ced_rnx_notification_from_mail"><?php esc_html_e( 'From Email', 'woo-refund-and-exchange-lite' ); ?></label>
								</th>
								<td class="forminp forminp-email">
									<?php
									$admin_email = get_option( 'admin_email' );
									$email = get_option( 'ced_rnx_notification_from_mail', false );
									if ( empty( $email ) ) {
										$email = $admin_email;
									}
									?>
									<input type="email" placeholder="" class="input-text" value="<?php echo esc_html( $email ); ?>" id="ced_rnx_notification_from_mail" name="ced_rnx_notification_from_mail">
								</td>
							</tr>
							<tr valign="top">
								<th class="titledesc" scope="row">
									<label for="ced_rnx_notification_auto_accept_return_rcv"><?php esc_html_e( 'Mail Header', 'woo-refund-and-exchange-lite' ); ?></label>
								</th>
								<td class="forminp forminp-textarea">
									<?php
									$content = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
									$editor_id = 'ced_rnx_notification_mail_header';
									$settings = array(
										'media_buttons'    => true,
										'drag_drop_upload' => true,
										'dfw'              => true,
										'teeny'            => true,
										'editor_height'    => 200,
										'editor_class'     => '',
										'textarea_name'    => 'ced_rnx_notification_mail_header',
									);
									wp_editor( $content, $editor_id, $settings );
									?>
								</td>
							</tr>
							<tr valign="top">
								<th class="titledesc" scope="row">
									<label for="ced_rnx_notification_auto_accept_return_rcv"><?php esc_html_e( 'Mail Footer', 'woo-refund-and-exchange-lite' ); ?></label>
								</th>
								<td class="forminp forminp-textarea">
									<?php
									$content = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
									$editor_id = 'ced_rnx_notification_mail_footer';
									$settings = array(
										'media_buttons'    => true,
										'drag_drop_upload' => true,
										'dfw'              => true,
										'teeny'            => true,
										'editor_height'    => 200,
										'editor_class'     => '',
										'textarea_name'    => 'ced_rnx_notification_mail_footer',
									);
									wp_editor( $content, $editor_id, $settings );
									?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>	
				<h2 id="rnx_return_reason" class="ced_rnx_basic_setting"><?php esc_html_e( 'Predefined Refund Reason', 'woo-refund-and-exchange-lite' ); ?></h2>
				<div id="rnx_return_reason_wrapper" class="ced_rnx_basic_wrapper">
					<table class="form-table ced_rnx_notification_section">
						<tbody>
							<tr valign="top">
								<td class="titledesc" scope="row" colspan="2">
									<div id="ced_rnx_return_predefined_reason_wrapper">
										<?php
										if ( isset( $predefined_return_reason ) && ! empty( $predefined_return_reason ) && is_array( $predefined_return_reason ) ) {
											foreach ( $predefined_return_reason as $predefine_reason ) {
												if ( ! empty( $predefine_reason ) ) {
													?>
													<input type="text" class="input-text" value="<?php echo esc_html( $predefine_reason ); ?>" class="ced_rnx_return_predefined_reason" name="ced_rnx_return_predefined_reason[]">
													<?php
												}
											}
										} else {
											?>
											<input type="text" class="input-text" name="ced_rnx_return_predefined_reason[]">
											<?php
										}
										?>
									</div>
									<input type="button" value="<?php esc_html_e( 'ADD MORE', 'woo-refund-and-exchange-lite' ); ?>" class="button" id="ced_rnx_return_predefined_reason_add">
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<p class="submit">
					<input type="submit" value="<?php esc_html_e( 'Save changes', 'woo-refund-and-exchange-lite' ); ?>" class="button-primary woocommerce-save-button ced-rnx-save-button" name="ced_rnx_noti_save_basic"> 
				</p>
			</form>
			<?php
		}
		// Refund Tab of Notification setting.
		if ( 'refund' == $mwb_rnx_tab ) {
			?>
			<form enctype="multipart/form-data" class="ced-main-form" action="" id="mainform" method="post">
				<div id="ced_rnx_accordion">
					<div class="ced_rnx_accord_sec_wrap">
						<h2 class="ced_rnx_slide_active"><?php esc_html_e( 'Merchant Setting', 'woo-refund-and-exchange-lite' ); ?></h2>
						<input type="hidden" name="ced-rnx-nonce" name="ced-rnx-nonce" value="<?php echo esc_html( wp_create_nonce( 'ced-rnx-nonce' ) ); ?>">
						<div class="ced_rnx_content_sec ced_rnx_notification_sec_active">
							<table class="form-table ced_rnx_notification_section">
								<tbody>
									<tr valign="top">
										<th class="titledesc" scope="row">
											<label for="ced_rnx_notification_merchant_return_subject"><?php esc_html_e( 'Merchant Refund Request Subject', 'woo-refund-and-exchange-lite' ); ?></label>
										</th>
										<td class="forminp forminp-text">
											<?php
											$merchant_subject = get_option( 'ced_rnx_notification_merchant_return_subject', false );
											?>
											<input type="text" placeholder="" class="input-text" value="<?php echo esc_html( $merchant_subject ); ?>" style="" id="ced_rnx_notification_merchant_return_subject" name="ced_rnx_notification_merchant_return_subject">
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div id="ced_rnx_accordion">
						<div class="ced_rnx_accord_sec_wrap">
							<h2><?php esc_html_e( 'Short-Codes ', 'woo-refund-and-exchange-lite' ); ?></h2>
							<div class="ced_rnx_content_sec ced_rnx_notification_section">
								<p><h3><?php esc_html_e( 'These are some shortcodes that you can use in EMAIL MESSAGES. It will be changed with its dynamic values.', 'woo-refund-and-exchange-lite' ); ?></h3></p>
								<p>
								<b> <?php esc_html_e( 'Note :', 'woo-refund-and-exchange-lite' ); ?></b><?php esc_html_e( 'Use', 'woo-refund-and-exchange-lite' ); ?><b> [order] </b> <?php esc_html_e( 'for Order Number', 'woo-refund-and-exchange-lite' ); ?><b> [siteurl] </b> <?php esc_html_e( 'for home page url and', 'woo-refund-and-exchange-lite' ); ?> <b> [username] </b> <?php esc_html_e( 'for user name.', 'woo-refund-and-exchange-lite' ); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="ced_rnx_accord_sec_wrap">
						<h2><?php esc_html_e( 'Refund Request', 'woo-refund-and-exchange-lite' ); ?></h2>
						<div class="ced_rnx_content_sec">
							<table class="form-table ced_rnx_notification_section">
								<tbody>
									<tr valign="top">
										<th class="titledesc" scope="row">
											<label for="ced_rnx_notification_return_subject"><?php esc_html_e( 'Refund Request Subject', 'woo-refund-and-exchange-lite' ); ?></label>
										</th>
										<td class="forminp forminp-text">
											<?php
											$return_cancel_subject = get_option( 'ced_rnx_notification_return_subject', false );
											?>
											<input type="text" placeholder="" class="input-text" value="<?php echo esc_html( $return_cancel_subject ); ?>" style="" id="ced_rnx_notification_return_subject" name="ced_rnx_notification_return_subject">
										</td>
									</tr>
									<tr valign="top">
										<th class="titledesc" scope="row">
											<label for="ced_rnx_notification_return_rcv"><?php esc_html_e( 'Received Refund Request Message', 'woo-refund-and-exchange-lite' ); ?></label>
										</th>
										<td class="forminp forminp-textarea">
											<?php
											$content = stripslashes( get_option( 'ced_rnx_notification_return_rcv', false ) );
											$editor_id = 'ced_rnx_notification_return_rcv';
											$settings = array(
												'media_buttons'    => false,
												'drag_drop_upload' => true,
												'dfw'              => true,
												'teeny'            => true,
												'editor_height'    => 200,
												'editor_class'     => '',
												'textarea_name'    => 'ced_rnx_notification_return_rcv',
											);
											wp_editor( $content, $editor_id, $settings );
											?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="ced_rnx_accord_sec_wrap">
						<h2><?php esc_html_e( 'Refund Approved', 'woo-refund-and-exchange-lite' ); ?></h2>
						<div class="ced_rnx_content_sec">
							<table class="form-table ced_rnx_notification_section">
								<tbody>
									<tr valign="top">
										<th class="titledesc" scope="row">
											<label for="ced_rnx_notification_return_approve_subject"><?php esc_html_e( 'Approved Refund Request Subject', 'woo-refund-and-exchange-lite' ); ?></label>
										</th>
										<td class="forminp forminp-text">
											<?php
											$return_subject = get_option( 'ced_rnx_notification_return_approve_subject', false );
											?>
											<input type="text" placeholder="" class="input-text" value="<?php echo esc_attr( $return_subject ); ?>" style="" id="ced_rnx_notification_return_approve_subject" name="ced_rnx_notification_return_approve_subject">
										</td>
									</tr>
									<tr valign="top">
										<th class="titledesc" scope="row">
											<label for="ced_rnx_notification_return_approve"><?php esc_html_e( 'Approved Refund Request Message', 'woo-refund-and-exchange-lite' ); ?></label>
										</th>
										<td class="forminp forminp-textarea">
											<?php
											$content = stripslashes( get_option( 'ced_rnx_notification_return_approve', false ) );
											$editor_id = 'ced_rnx_notification_return_approve';
											$settings = array(
												'media_buttons'    => false,
												'drag_drop_upload' => true,
												'dfw'              => true,
												'teeny'            => true,
												'editor_height'    => 200,
												'editor_class'     => '',
												'textarea_name'    => 'ced_rnx_notification_return_approve',
											);
											wp_editor( $content, $editor_id, $settings );
											?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="ced_rnx_accord_sec_wrap">
						<h2><?php esc_html_e( 'Refund Cancel', 'woo-refund-and-exchange-lite' ); ?></h2>
						<div class="ced_rnx_content_sec ">
							<table class="form-table ced_rnx_notification_section ">
								<tbody>
									<tr valign="top">
										<th class="titledesc" scope="row">
											<label for="ced_rnx_notification_return_cancel_subject"><?php esc_html_e( 'Cancelled Refund Request Subject', 'woo-refund-and-exchange-lite' ); ?></label>
										</th>
										<td class="forminp forminp-text">
											<?php
											$return_subject = get_option( 'ced_rnx_notification_return_cancel_subject', false );
											?>
											<input type="text" placeholder="" class="input-text" value="<?php echo esc_html( $return_subject ); ?>" style="" id="ced_rnx_notification_return_cancel_subject" name="ced_rnx_notification_return_cancel_subject">
										</td>
									</tr>
								</tr>
								<tr valign="top">
									<th class="titledesc" scope="row">
										<label for="ced_rnx_notification_return_cancel"><?php esc_html_e( 'Cancelled Refund Request Message', 'woo-refund-and-exchange-lite' ); ?></label>
									</th>
									<td class="forminp forminp-textarea">
										<?php
										$content = stripslashes( get_option( 'ced_rnx_notification_return_cancel', false ) );
										$editor_id = 'ced_rnx_notification_return_cancel';
										$settings = array(
											'media_buttons'    => false,
											'drag_drop_upload' => true,
											'dfw'              => true,
											'teeny'            => true,
											'editor_height'    => 200,
											'editor_class'     => '',
											'textarea_name'    => 'ced_rnx_notification_return_cancel',
										);
										wp_editor( $content, $editor_id, $settings );
										?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>		
			<p class="submit">
				<input type="submit" value="<?php esc_html_e( 'Save Settings', 'woo-refund-and-exchange-lite' ); ?>" class="ced-rnx-save-button button-primary woocommerce-save-button" name="ced_rnx_noti_save_return"> 
			</p>
		</form>
			<?php
		}
		if ( 'exchange' == $mwb_rnx_tab || 'return_ship_label_setting' == $mwb_rnx_tab ) {
			include_once MWB_REFUND_N_EXCHANGE_LITE_DIRPATH . 'admin/partials/mwb-rnx-lite-pro-purchase-template.php';
		}
		?>
	<div class="ced-rnx-sidebar">
		<div class="ced-rnx-sidebar-inner">
			<a href="https://wpswings.com/product/rma-return-refund-exchange-for-woocommerce-pro?utm_source=wpswings-rma-pro&utm_medium=rma-org-backend&utm_campaign=go-pro" target="_blank" class="ced0sidebar-button">
				<img src="<?php echo esc_url( MWB_REFUND_N_EXCHANGE_LITE_URL . '/admin/images/Side_Banner.png' ); ?>">
			</a>
		</div>
	</div>
</div>
