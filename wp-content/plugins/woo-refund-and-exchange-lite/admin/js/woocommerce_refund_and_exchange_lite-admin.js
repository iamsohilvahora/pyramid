(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write $ code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$( document ).ready(
		function(){
				
				$( document ).on(
					'click',
					'#ced_rnx_accept_return',
					function(){
						$( "#ced_rnx_return_package" ).hide();
						$( ".ced_rnx_return_loader" ).show();
						var orderid = $( this ).data( 'orderid' );
						var date = $( this ).data( 'date' );
						var data = {
							action:'ced_return_req_approve',
							orderid:orderid,
							date:date,
							security_check	: global_rnx.ced_rnx_nonce
						};
						$.ajax(
							{
								url: global_rnx.ajaxurl,
								type: "POST",
								data: data,
								dataType :'json',
								success: function(response)
							{
									$( ".ced_rnx_return_loader" ).hide();
									$( ".refund-actions .cancel-action" ).hide();

									window.location.reload();

								}
							}
						);
					}
				);

				$( document ).on(
					'click',
					'#ced_rnx_left_amount',
					function(){
						$( this ).attr( 'disabled','disabled' );

						var order_id = $( this ).data( 'orderid' );
						var refund_amount = $( ".ced_rnx_total_amount_for_refund" ).val();
						$( 'html, body' ).animate(
							{
								scrollTop: $( "#order_shipping_line_items" ).offset().top
							},
							2000
						);

						$( 'div.wc-order-refund-items' ).slideDown();
						$( 'div.wc-order-data-row-toggle' ).not( 'div.wc-order-refund-items' ).slideUp();
						$( 'div.wc-order-totals-items' ).slideUp();
						$( '#woocommerce-order-items' ).find( 'div.refund' ).show();
						$( '.wc-order-edit-line-item .wc-order-edit-line-item-actions' ).hide();
						var refund_amount = $( "#ced_rnx_refund_amount" ).val();
						var refund_reason = $( "#ced_rnx_refund_reason" ).val();
						$( "#refund_amount" ).val( refund_amount );
						$( "#refund_reason" ).val( refund_reason );

						var total = accounting.unformat( refund_amount, woocommerce_admin.mon_decimal_point );

						$( 'button .wc-order-refund-amount .amount' ).text(
							accounting.formatMoney(
								total,
								{
									symbol:    woocommerce_admin_meta_boxes.currency_format_symbol,
									decimal:   woocommerce_admin_meta_boxes.currency_format_decimal_sep,
									thousand:  woocommerce_admin_meta_boxes.currency_format_thousand_sep,
									precision: woocommerce_admin_meta_boxes.currency_format_num_decimals,
									format:    woocommerce_admin_meta_boxes.currency_format
								}
							)
						);

					}
				);

				$( document ).on(
					'click',
					'#ced_rnx_cancel_return',
					function(){
						$( ".ced_rnx_return_loader" ).show();
						var orderid = $( this ).data( 'orderid' );
						var date = $( this ).data( 'date' );
						var data = {
							action:'ced_return_req_cancel',
							orderid:orderid,
							date:date,
							security_check	:	global_rnx.ced_rnx_nonce
						};
						$.ajax(
							{
								url: global_rnx.ajaxurl,
								type: "POST",
								data: data,
								dataType :'json',
								success: function(response)
							{
									$( ".ced_rnx_return_loader" ).hide();
									window.location.reload();
								}
							}
						);
					}
				);
				jQuery( document ).on(
					'click',
					'#ced_rnx_stock_back',
					function(){
						jQuery( this ).attr( 'disabled','disabled' );
						var order_id = jQuery( this ).data( 'orderid' );
						var type = jQuery( this ).data( 'type' );
						var data = {
							action   : 'ced_rnx_manage_stock' ,
							order_id : order_id ,
							type     : type,
							security_check : global_rnx.ced_rnx_nonce
						};
						jQuery.ajax(
							{
								url: global_rnx.ajaxurl,
								type: "POST",
								data: data,
								dataType :'json',
								success: function(response)
							{
									jQuery( this ).removeAttr( 'disabled' );
									if (response.result) {
										jQuery( "#post" ).prepend( '<div class="updated notice notice-success is-dismissible" id="message"><p>' + response.msg + '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>' );
										jQuery( 'html, body' ).animate(
											{
												scrollTop: jQuery( "body" ).offset().top
											},
											2000,
											"linear",
											function(){
												window.setTimeout(
													function() {
														window.location.reload();
													},
													1000
												);
											}
										);
									} else {
										jQuery( "#post" ).prepend( '<div id="messege" class="notice notice-error is-dismissible" id="message"><p>' + response.msg + '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>' );
										jQuery( 'html, body' ).animate(
											{
												scrollTop: jQuery( "body" ).offset().top
											},
											2000,
											"linear",
											function(){
											}
										);
									}
								}
							}
						);
					}
				);
				
				$( document ).on(
					'click',
					'#ced_rnx_return_predefined_reason_add',
					function(){
							var html = '';
							html += '<input type="text" class="ced_rnx_return_predefined_reason" name="ced_rnx_return_predefined_reason[]" value="" class="input-text">';
							$( "#ced_rnx_return_predefined_reason_wrapper" ).append( html );
						
						
						
					}
				);
				$( '#ced_rnx_accordion h2' ).on(
					'click',
					function(){
						if ($( this ).next( '.ced_rnx_content_sec' ).is( ":visible" )) {
							  $( this ).removeClass( 'ced_rnx_slide_active' );
						} else {
							 $( this ).addClass( 'ced_rnx_slide_active' );
						}
						$( this ).next( '.ced_rnx_content_sec' ).slideToggle( 'slow' );

					}
				);
				$( document ).on(
					'click',
					'#rnx_mail_setting',
					function(){
						if ($( "#rnx_mail_setting_wrapper" ).is( ":visible" )) {
							  $( this ).removeClass( 'ced_rnx_slide_active' );
						} else {
							 $( this ).addClass( 'ced_rnx_slide_active' );
						}
						$( "#rnx_mail_setting_wrapper" ).slideToggle( 'slow' );
					}
				);
				$( document ).on(
					'click',
					'#rnx_return_reason',
					function(){
						if ($( "#rnx_return_reason_wrapper" ).is( ":visible" )) {
							  $( this ).removeClass( 'ced_rnx_slide_active' );
						} else {
							 $( this ).addClass( 'ced_rnx_slide_active' );
						}
						$( "#rnx_return_reason_wrapper" ).slideToggle( 'slow' );
					}
				);
				$( document ).on(
					'click',
					'#rnx_exchange_reason',
					function(){
						if ($( "#rnx_exchange_reason_wrapper" ).is( ":visible" )) {
							  $( this ).removeClass( 'ced_rnx_slide_active' );
						} else {
							 $( this ).addClass( 'ced_rnx_slide_active' );
						}
						$( "#rnx_exchange_reason_wrapper" ).slideToggle( 'slow' );
					}
				);
				$( "#rnx_refund_rules" ).on(
					'click',
					'#rnx_refund_rules',
					function(){
						if ($( "#rnx_refund_rules_wrapper" ).is( ":visible" )) {
							  $( this ).removeClass( 'ced_rnx_slide_active' );
						} else {
							 $( this ).addClass( 'ced_rnx_slide_active' );
						}
						$( "#rnx_refund_rules_wrapper" ).slideToggle( 'slow' );
					}
				);

				$( document ).ready(
					function(){
						$( ".ced-news-wrap" ).slick(
							{
								prevArrow: '<span class="ced-rnx-arrow left"><i class="dashicons dashicons-arrow-left-alt2"></i></span>',
								nextArrow: '<span class="ced-rnx-arrow right"><i class="dashicons dashicons-arrow-right-alt2"></i></span>',
								slidesToShow: 1,
								slidesToScroll: 1,
								autoplay: true,
								autoplaySpeed: 4000,
							}
						);
						$( document ).find( '#wpbody-content' ).addClass( 'mwb_custom_wrap' );

					}
				);
	$('.mwb_order_msg_notice_wrapper').hide();
	
	// Send order messages from admin.
    $( document ).on( 'click', '#mwb_order_msg_submit', function (e) {
    	e.preventDefault();
    	var up_files = $('#mwb_order_msg_attachment');
    	var msg = $('#mwb_order_new_msg').val();
    	var alerthtml = '';
    	if ( msg == '' ) {
    		alerthtml = '<p class="mwb_order_msg_sent_notice">'+  global_rnx.message_empty +'</p><a href="" class="mwb_remove_notice_msg">X</a>';
            $(".mwb_order_msg_notice_wrapper").addClass('mwb_msg_error');
            $('.mwb_order_msg_notice_wrapper').removeClass('mwb_msg_succuss_notice');
    		$(".mwb_order_msg_notice_wrapper").css('display', 'flex');
			$(".mwb_order_msg_notice_wrapper").html(alerthtml);
    		return false;
    	}
    	var order_id = $(this).data("id");

    	var form_data = new FormData();

		// Read selected files
		var totalfiles = up_files[0].files.length;
		for (var index = 0; index < totalfiles; index++) {
		   	form_data.append("mwb_order_msg_attachment[]", up_files[0].files[index]);
		}
		form_data.append( "action", 'mwb_wrma_order_messages_save' );
		form_data.append( "msg", msg );
		form_data.append( "order_id", order_id );
		form_data.append( "security_check", global_rnx.ced_rnx_nonce );

		   // AJAX request
		$.ajax({
		   	url: global_rnx.ajaxurl, 
		   	type: 'post',
		   	data: form_data,
		   	dataType: 'json',
		   	contentType: false,
		   	processData: false,
		   	success: function ( response ) {
		   		if( response ) {
			   		var html = 	'<p class="mwb_order_msg_sent_notice">'+  global_rnx.message_sent +'</p><a href="" class="mwb_remove_notice_msg">X</a>';
                    $('.mwb_order_msg_notice_wrapper').addClass('mwb_msg_succuss_notice');
					$('.mwb_order_msg_notice_wrapper').removeClass('mwb_msg_error');
			   		$('.mwb_order_msg_notice_wrapper').html( html );
			   		$('.mwb_order_msg_notice_wrapper').css('display', 'flex');
			   		$('.mwb_admin_order_msg_sub_container').load(document.URL +  ' .mwb_order_msg_main_container');
		   			$('#mwb_order_new_msg').val("");
		   			$('#mwb_order_msg_attachment').val('');
		   		}
		   	}
		});
	});

	$(document).on('click','.mwb_remove_notice_msg',function(e) {
		e.preventDefault();
		$('.mwb_order_msg_notice_wrapper').hide();
	});

	$(document).on('click','.mwb_wrma_reload_messages',function(e) {
		e.preventDefault();
		$(this).addClass('mwb-loader-icon');
		$('.mwb_admin_order_msg_sub_container').load(document.URL +  ' .mwb_order_msg_main_container');
		setTimeout(function() {
			$('.mwb_wrma_reload_messages').removeClass('mwb-loader-icon');
            $('.mwb_order_msg_reload_notice_wrapper').show();
		}, 2000);
         setTimeout(function() {
			 $('.mwb_order_msg_reload_notice_wrapper').hide();
		}, 3000);
	});

		}
	);
	$( document ).on( 'click', '#delete_notice', function(){
		$( '#backup_notice_div' ).remove();
	});
})( jQuery );