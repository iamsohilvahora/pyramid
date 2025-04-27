<?php
/**
 * This file is for setting
 *
 * @package woocommerce_refund_and_exchange_lite
 */

if ( ! class_exists( 'Mwb_Basic_Framework_Admin_Settings' ) ) {
	/** To register the setting */
	class Mwb_Basic_Framework_Admin_Settings {
		/** Constructor */
		public function __construct() {

			add_action( 'admin_head', array( $this, 'add_current_class_on_rma_menu' ) );
			add_submenu_page( '', '', '', 'manage_woocommerce', 'ced-rnx-notification', array( $this, 'ced_rnx_notification_callback' ) );
			add_meta_box( 'ced_rnx_order_refund', __( 'Refund Requested Products', 'woo-refund-and-exchange-lite' ), array( $this, 'ced_rnx_order_return' ), 'shop_order' );
			// Add order msg history.
			add_meta_box( 'ced_rnx_order_msg_history', __( 'Order Message History', 'woo-refund-and-exchange-lite' ), array( $this, 'ced_rnx_order_msg_history' ), 'shop_order' );

			$this->id = 'ced_rnx_setting';

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'ced_rnx_add_settings_tab' ), 50 );
			add_action( 'woocommerce_sections_' . $this->id, array( $this, 'ced_rnx_output_sections' ) );
			add_action( 'woocommerce_settings_tabs_' . $this->id, array( $this, 'ced_rnx_settings_tab' ) );
		}

		/**
		 * Active current tab.
		 *
		 * @return void
		 */
		public function add_current_class_on_rma_menu() {
			$get_tab     = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
			$get_section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : '';
			if ( 'ced_rnx_setting' === $get_tab ) {
				?>
				<script type="text/javascript">
					jQuery(document).ready( function($) { 
						$('.wp-submenu li.current').removeClass('current');  
						$('#mwb_wrma_config_menu').parent().addClass('current'); 
						$('#mwb_wrma_config_menu').parent().parent().addClass('current');
					});
				</script>
				<?php
			}
			if ( 'ced_rnx_setting' === $get_tab && '' === $get_section ) {
				?>
				<script type="text/javascript">
					jQuery(document).ready( function($) {   
						$('.subsubsub li a:first').addClass('current'); 
					});
				</script>
				<?php
			}
		}

		/**
		 * Add new tab to woocommerce setting
		 *
		 * @param array() $settings_tabs is settings.
		 * @author MakeWebBetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public static function ced_rnx_add_settings_tab( $settings_tabs ) {
			$settings_tabs['ced_rnx_setting'] = __( 'Refund-Exchange Lite Setting', 'woo-refund-and-exchange-lite' );
			return $settings_tabs;
		}

		/** Add some template for pro */
		public function ced_rnx_settings_tab() {
			global $current_section;
			woocommerce_admin_fields( self::ced_rnx_get_settings( $current_section ) );
			$get_tab     = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
			$get_section = '';
			if ( isset( $_GET['section'] ) ) {
				$get_section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : '';
				if ( 'refund' !== $get_section ) {
					include_once MWB_REFUND_N_EXCHANGE_LITE_DIRPATH . 'admin/partials/mwb-rnx-lite-pro-purchase-template.php';
					?>
					<style type="text/css">
						.button-primary.woocommerce-save-button {
							display: none;
						}
						.ced_rnx_help_wrapper {
							margin-top: 20px;
							width: 100%;
						}
					</style>
					<?php
				}
			}

			if ( 'ced_rnx_setting' === $get_tab && '' === $get_section ) {
				include_once MWB_REFUND_N_EXCHANGE_LITE_DIRPATH . 'admin/partials/mwb-rnx-lite-pro-purchase-template.php';
				?>
					<style type="text/css">
						.button-primary.woocommerce-save-button {
							display: none;
						}
						.ced_rnx_help_wrapper {
							margin-top: 20px;
							width: 100%;
						}
					</style>
					<?php
			}
			echo '</div>';
		}

		/**
		 * Output of section setting
		 *
		 * @author MakeWebBetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_output_sections() {

			global $current_section;
			echo '<div id="test">';
			$sections = $this->ced_rnx_get_sections();
			echo '<div id="mwb_lite_main_header" class="mwb_lite_main-header">
			 	  <div class="mwb_lite_header_content_left">
				    <h3 class="mwb_lite_setting_title">' . esc_html__( 'Refund-Exchange Lite Settings', 'woo-refund-and-exchange-lite' ) . '</h3>
			 	  </div>
			 	   <div class="mwb_lite_header_content_right">
				      <ul>
				      <li>
				      	  <a href="https://translate.wordpress.org/projects/wp-plugins/woo-refund-and-exchange-lite/" target="_blank"><b class="mwb_translate_link">' . esc_html__( 'Start translating this plugin in your language', 'woo-refund-and-exchange-lite' ) . '</b></a>
						</li>
				      	<li>
				      	  <a href="https://wpswings.com/submit-query/?utm_source=wpswings-rma-support&utm_medium=rma-org-backend&utm_campaign=support/" target="_blank">
						  <span class="dashicons dashicons-phone"></span></a>
						</li>
						<li>
						  <a href="https://docs.wpswings.com/woo-refund-and-exchange-lite/?utm_source=wpswings-rma-doc&utm_medium=rma-org-page&utm_campaign=rma-doc" target="_blank">
						   <span class="dashicons dashicons-media-document"></span></a>
						</li>
						<li class="mwb_lite_header_menu_button"><a href="https://wpswings.com/product/rma-return-refund-exchange-for-woocommerce-pro?utm_source=wpswings-rma-pro&utm_medium=rma-org-backend&utm_campaign=go-pro" class="" title="" target="_blank">GO PRO NOW</a></li>
				      </ul>
			 	  </div>
			</div>
			<div id="backup_notice_div">
			<span id="backup_notice">Important - On our customer\'s demand we will be updating plugin with some significant changes soon.So its highly recommended to take backup of your present version to <br>avoid any future inconvenience.</span>
			<span id="delete_notice">X</span></div>';
			echo '<div class="mwb_lte_nav_bar">';
			echo '<ul class="subsubsub">';

			$array_keys = array_keys( $sections );

			foreach ( $sections as $id => $label ) {
				echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . esc_html( $label ) . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';

			}
			echo '<li> | <a href="' . esc_url( admin_url() ) . 'admin.php?page=ced-rnx-notification">' . esc_html__( 'Mail Configuration', 'woo-refund-and-exchange-lite' ) . '</a></li>';
			echo '</div>';
			echo '</ul><br class="clear ced_rnx_clear"/>';
			echo '</div>';

			if ( ! defined( 'ONBOARD_PLUGIN_NAME' ) && ( ! empty( $_GET['tab'] ) && 'ced_rnx_setting' === $_GET['tab'] ) ) {
				define( 'ONBOARD_PLUGIN_NAME', 'Return Refund and Exchange for Woocommerce' );
			}

			if ( class_exists( 'Makewebbetter_Onboarding_Helper' ) ) {
				$this->onboard = new Makewebbetter_Onboarding_Helper();
			}
		}

		/**
		 * Create section setting
		 *
		 * @author MakeWebBetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_get_sections() {

			$sections = array(
				'overview'      => __( 'Overview', 'woo-refund-and-exchange-lite' ),
				'refund'        => __( 'Refund Products', 'woo-refund-and-exchange-lite' ),
				'exchange'      => __( 'Exchange Products', 'woo-refund-and-exchange-lite' ),
				'other'         => __( 'Common Setting', 'woo-refund-and-exchange-lite' ),
				'cancel'        => __( 'Cancel Order', 'woo-refund-and-exchange-lite' ),
				'text_setting'  => __( 'Text Settings', 'woo-refund-and-exchange-lite' ),
				'catalog_setting' => __( 'Catalog Settings', 'woo-refund-and-exchange-lite' ),

			);

			return apply_filters( 'ced_rnx_get_sections_' . $this->id, $sections );
		}

		/**
		 * Section setting
		 *
		 * @param string $current_section .
		 */
		public function ced_rnx_get_settings( $current_section ) {

			// get woocommerce categories.

			$all_cat = get_terms( 'product_cat', array( 'hide_empty' => 0 ) );
			$cat_name = array();
			if ( $all_cat ) {
				foreach ( $all_cat as $cat ) {

					$cat_name[ $cat->term_id ] = $cat->name;

				}
			}

			$statuses = wc_get_order_statuses();
			$status   = $statuses;
			unset( $status['wc-refund-approved'] );
			unset( $status['wc-refund-cancelled'] );
			unset( $status['wc-refund-requested'] );
			unset( $status['wc-refunded'] );

			$emai_url = admin_url() . 'admin.php?page=wc-settings&tab=email&section=wc_rma_messages_email';

			$button_view = array(
				'order-page' => __( 'Order Page', 'woo-refund-and-exchange-lite' ),
				'My account' => __( 'Order View Page', 'woo-refund-and-exchange-lite' ),
				'thank-you-page' => __( 'Thank You Page', 'woo-refund-and-exchange-lite' ),
			);

			if ( 'refund' == $current_section ) {

				$settings = array(

					array(
						'title' => __( 'Refund Products Setting', 'woo-refund-and-exchange-lite' ),
						'type'  => 'title',
					),

					array(
						'title'         => __( 'Enable', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'Enable Refund Request.', 'woo-refund-and-exchange-lite' ),
						'default'       => 'no',
						'type'          => 'checkbox',
						'id'        => 'mwb_wrma_return_enable',
					),

					array(
						'title'         => __( 'Include Tax', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'Include Tax with Product Refund Request.', 'woo-refund-and-exchange-lite' ),
						'default'       => 'no',
						'type'          => 'checkbox',
						'id'        => 'mwb_wrma_return_tax_enable',
					),

					array(
						'title'         => __( 'Maximum Number of Days', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'If days exceed the day of order placed then a Refund Request will not be sent. If the value is 0 or blank then the Refund button will not visible on the order detail page.', 'woo-refund-and-exchange-lite' ),
						'type'          => 'number',
						'custom_attributes'   => array( 'min' => '0' ),
						'id'        => 'mwb_wrma_return_days',
						'desc_tip' => true,
					),
					array(
						'title'         => __( 'Enable Attachment on Request Form', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'Enable this for user to send the attachment. User can attach <i>.png, .jpg, .jpeg</i> type files.', 'woo-refund-and-exchange-lite' ),
						'default'       => 'no',
						'type'          => 'checkbox',
						'id'        => 'mwb_wrma_return_attach_enable',
					),
					array(
						'title'         => __( 'Enter number Of Attachment to send', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'Enter the number of attachments the customer can add one time during refund.', 'woo-refund-and-exchange-lite' ),
						'type'          => 'number',
						'id'            => 'mwb_wrma_refund_attachment_limit',
						'default'       => __( 'Enter number Of Attachment to send', 'woo-refund-and-exchange-lite' ),
						'custom_attributes'   => array( 'min' => '0' ),
						'desc_tip' => true,
					),
					array(
						'title'         => __( 'Enable Refund Reason Description', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'Enable this for the user to send the detail description of Refund request.', 'woo-refund-and-exchange-lite' ),
						'default'       => 'no',
						'type'          => 'checkbox',
						'id'        => 'mwb_wrma_return_request_description',
					),
					array(
						'title'         => __( 'Enable Manage Stock', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'Enable this to increase product stock when the Refund request is accepted.', 'woo-refund-and-exchange-lite' ),
						'default'       => 'no',
						'type'          => 'checkbox',
						'id'        => 'mwb_wrma_return_request_manage_stock',
					),
					array(
						'title'    => __( 'Select the order status in which the order can be Refunded', 'woo-refund-and-exchange-lite' ),
						'desc'     => __( 'Select Order status on which you want Refund request user can submit.', 'woo-refund-and-exchange-lite' ),
						'class'    => 'wc-enhanced-select ',
						'css'      => 'min-width:300px;',
						'default'  => '',
						'type'     => 'multiselect',
						'options'  => $status,
						'desc_tip' => true,
						'id'        => 'mwb_wrma_return_order_status',
					),
					array(
						'title'         => __( 'Enable Refund Rules', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'Enable, if you want to show custom Refund Policy Rules on Refund Request Form.', 'woo-refund-and-exchange-lite' ),
						'default'       => 'no',
						'type'          => 'checkbox',
						'id'            => 'mwb_wrma_refund_rules_editor_enable',
					),
					array(

						'title'         => __( 'Refund Rules Editor ', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'Custom Refund Rules Editor (HTML and CSS allowed). Put your custom Refund rules here.', 'woo-refund-and-exchange-lite' ),
						'default'       => '',
						'type'          => 'textarea',
						'desc_tip'      => true,
						'id'            => 'mwb_wrma_return_request_rules_editor',
						'class'         => 'mwb_wrma_return_request_rules_editor',
					),
					array(
						'title'         => __( 'Refund Button Text', 'woo-refund-and-exchange-lite' ),
						'type'          => 'text',
						'id'            => 'mwb_wrma_return_button_text',
						'default'       => __( 'Refund', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'Change Refund button text on frontend', 'woo-refund-and-exchange-lite' ),
						'desc_tip' => true,
					),
					array(
						'title'         => __( 'Enable Order Messages', 'woo-refund-and-exchange-lite' ), /* translators: %s: search term */
						'desc'          => sprintf( __( 'Enable this if you want to allow your customers to message their order related query. To configure order message mails. %s', 'woo-refund-and-exchange-lite' ), '<a href="' . $emai_url . '">Click Here</a>' ),
						'default'       => 'no',
						'type'          => 'checkbox',
						'id'            => 'mwb_wrma_order_message_view',
					),
					array(
						'title'         => __( 'Enable attachment upload for order messages', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'Enable this if you want to allow your customers to upload attachment along with their order-related messages.', 'woo-refund-and-exchange-lite' ),
						'default'       => 'no',
						'type'          => 'checkbox',
						'id'            => 'mwb_wrma_order_message_attachment',
					),

					array(
						'title'         => __( 'View Order Messages Button text', 'woo-refund-and-exchange-lite' ),
						'type'          => 'text',
						'id'            => 'mwb_wrma_order_msg_text',
						'default'       => __( 'View Order Messages', 'woo-refund-and-exchange-lite' ),
						'desc'          => __( 'Change View Order Messages Button text on frontend', 'woo-refund-and-exchange-lite' ),
						'desc_tip' => true,
					),
					array(
						'title'    => __( 'Select to show refund button on pages', 'woo-refund-and-exchange-lite' ),
						'desc'     => __( 'Select the options to show the refund button on which page you want to show the button.', 'woo-refund-and-exchange-lite' ),
						'class'    => 'wc-enhanced-select ',
						'css'      => 'min-width:300px;',
						'default'  => '',
						'type'     => 'multiselect',
						'options'  => $button_view,
						'desc_tip' => true,
						'id'        => 'mwb_wrma_refund_button_view',
					),

					array(
						'type'  => 'sectionend',
					),

				);

			} else {
				$settings = array(
					array(
						'type'  => 'sectionend',
						'class' => 'ced_rnx_test',
					),
				);
			}

			return apply_filters( 'ced_rnx_get_settings_exchange' . $this->id, $settings );

		}

		 /**
		  * Save setting
		  *
		  * @author MakeWebBetter<webmaster@makewebbetter.com>
		  * @link http://www.makewebbetter.com/
		  */
		public function ced_rnx_setting_save() {
			global $current_section;
			$settings = $this->ced_rnx_get_settings( $current_section );
			WC_Admin_Settings::save_fields( $settings );
		}

		/**
		 * Add notification submenu in woocommerce
		 *
		 * @author MakeWebBetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_notification_callback() {
			include_once MWB_REFUND_N_EXCHANGE_LITE_DIRPATH . 'admin/partials/mwb-rnx-lite-notification.php';
		}

		/**
		 * This function is metabox template for Refund order product
		 *
		 * @author MakeWebBetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_order_return() {
			global $post, $thepostid, $theorder;
			include_once MWB_REFUND_N_EXCHANGE_LITE_DIRPATH . 'admin/partials/mwb-rnx-lite-return-product-meta.php';
		}

		/**
		 * This function is metabox template for order msg history.
		 *
		 * @name ced_rnx_order_msg_history.
		 */
		public function ced_rnx_order_msg_history() {
			global $post, $thepostid, $theorder;
			include_once MWB_REFUND_N_EXCHANGE_LITE_DIRPATH . 'admin/partials/mwb-rnx-lite-order-msg-history-meta.php';
		}
	}
}
