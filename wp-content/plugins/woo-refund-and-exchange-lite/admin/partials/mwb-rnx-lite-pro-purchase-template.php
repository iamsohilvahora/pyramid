<?php
/**
 * Exit if accessed directly
 *
 * @package woocommerce_refund_and_exchange_lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image_src       = '';
$mwb_rnx_section = '';
$mwb_rnx_tab     = '';
if ( isset( $_GET['section'] ) || isset( $_GET['section'] ) ) {
	$mwb_rnx_section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : '';
	$mwb_rnx_tab     = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
	if ( 'exchange' == $mwb_rnx_section ) {
		$image_src = MWB_REFUND_N_EXCHANGE_LITE_URL . '/admin/images/Order-Exchange-icon.png';
	} elseif ( 'other' == $mwb_rnx_section ) {
		$image_src = MWB_REFUND_N_EXCHANGE_LITE_URL . '/admin/images/Common-Setting-icon.png';
	} elseif ( 'text_setting' == $mwb_rnx_section ) {
		$image_src = MWB_REFUND_N_EXCHANGE_LITE_URL . '/admin/images/Text-Setting-icon.png';
	} elseif ( 'cancel' == $mwb_rnx_section ) {
		$image_src = MWB_REFUND_N_EXCHANGE_LITE_URL . '/admin/images/Order-Cancel-icon.png';
	} elseif ( 'catalog_setting' == $mwb_rnx_section ) {
		$image_src = MWB_REFUND_N_EXCHANGE_LITE_URL . '/admin/images/Product-Catalog-icon.png';
	}
} else {
	if ( isset( $_GET['tab'] ) ) {
		$mwb_rnx_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		if ( 'exchange' == $mwb_rnx_tab ) {
			$image_src = MWB_REFUND_N_EXCHANGE_LITE_URL . '/admin/images/Order-Exchange-icon.png';
		} elseif ( 'return_ship_label_setting' == $mwb_rnx_tab ) {
			$image_src = MWB_REFUND_N_EXCHANGE_LITE_URL . '/admin/images/Return-Slip-Label-icon.png';
		}
	}
}

if ( 'overview' == $mwb_rnx_section && 'ced_rnx_setting' == $mwb_rnx_tab || '' == $mwb_rnx_section && 'ced_rnx_setting' == $mwb_rnx_tab ) {
	?>
	<div class="mwb_wrma_table_wrapper mwb_wrma_overview-wrapper">
		<div class="mwb_wrma_overview_content">
			<h3 class="mwb_wrma_overview_heading"><?php esc_html_e( 'Connect With Us and Explore More About Return Refund and Exchange for Woocommerce', 'woo-refund-and-exchange-lite' ); ?></h3>
			<p><?php esc_html_e( 'Refund and Exchange plugin for WooCommerce gives your customers an easy and simple Refund Management system stuffed with organized Exchange, and Cancel features with a dedicated and streamlined mailing system.', 'woo-refund-and-exchange-lite' ); ?></p>
		</div>
		<div class="mwb_wrma_video_wrapper">
			<iframe width="80%" height="411" src="https://www.youtube.com/embed/z0kG75E60TI" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div>
	</div>
	<?php
} else {
	?>
	<div class="ced_rnx_help_wrapper">
		<div class="ced-purchase-main-plugin-wrap">
				<?php
				if ( '' !== $image_src ) {
					?>
				<img src="<?php echo esc_html( esc_url( $image_src ) ); ?>">
					<?php
				}
				?>
			<h2 class="ced-purchase-heading"><?php esc_html_e( 'Please Purchase Our Premium version for this feature and also for some more exciting feature.', 'woo-refund-and-exchange-lite' ); ?></h2>
			<div class="ced-rnx-purchase-conetnt">
				<a class="ced-rnx-news-button ced-prcchase-button" href="https://wpswings.com/product/rma-return-refund-exchange-for-woocommerce-pro?utm_source=wpswings-rma-pro&utm_medium=rma-org-backend&utm_campaign=go-pro" target="_blank"> <?php esc_html_e( 'Get it now', 'woo-refund-and-exchange-lite' ); ?>
			</a>
			</div>
		</div>
	</div>
	<?php
}
