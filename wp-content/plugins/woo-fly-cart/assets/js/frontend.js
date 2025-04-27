'use strict';

(function($) {
  var woofc_timeout = null;

  $(function() {
    // Reload the cart
    if (woofc_vars.reload == 'yes') {
      woofc_cart_reload();
    }
  });

  // Auto show
  $(document.body).on('added_to_cart', function() {
    if (woofc_vars.auto_show === 'yes') {
      setTimeout(function() {
        woofc_show_cart();
      }, 100);
    }
  });

  $(document.body).on('wc_fragments_loaded', function() {
    woofc_cart_loaded();
  });

  $(document.body).on('wc_fragments_refreshed', function() {
    woofc_cart_loaded();
  });

  // Reload cart/checkout
  $(document.body).on('woofc_cart_reload', function() {
    if ((woofc_vars.is_cart == '1') && $('form.woocommerce-cart-form').length) {
      $(document.body).trigger('wc_update_cart');
    }

    if ((woofc_vars.is_checkout == '1') &&
        $('form.woocommerce-checkout').length) {
      $(document.body).trigger('update_checkout');
    }
  });

  // Manual show
  if (woofc_vars.manual_show != '') {
    $(document).on('click touch', woofc_vars.manual_show, function(e) {
      woofc_toggle_cart();
      e.preventDefault();
    });
  }

  // Qty minus & plus
  $(document).
      on('click touch', '.woofc-item-qty-plus, .woofc-item-qty-minus',
          function() {
            // get values
            var $qty = $(this).
                    closest('.woofc-item-qty').
                    find('.qty'),
                qty_val = parseFloat($qty.val()),
                max = parseFloat($qty.attr('max')),
                min = parseFloat($qty.attr('min')),
                step = $qty.attr('step');

            // format values
            if (!qty_val || qty_val === '' || qty_val === 'NaN') {
              qty_val = 0;
            }

            if (max === '' || max === 'NaN') {
              max = '';
            }

            if (min === '' || min === 'NaN') {
              min = 0;
            }

            if (step === 'any' || step === '' || step === undefined ||
                parseFloat(step) === 'NaN') {
              step = 1;
            } else {
              step = parseFloat(step);
            }

            // change the value
            if ($(this).is('.woofc-item-qty-plus')) {
              if (max && (
                  max == qty_val || qty_val > max
              )) {
                $qty.val(max);
              } else {
                $qty.val((qty_val + step).toFixed(woofc_decimal_places(step)));
              }
            } else {
              if (min && (
                  min == qty_val || qty_val < min
              )) {
                $qty.val(min);
              } else if (qty_val > 0) {
                $qty.val((qty_val - step).toFixed(woofc_decimal_places(step)));
              }
            }

            // trigger change event
            $qty.trigger('change');
          });

  // Qty on change
  $(document).on('change', '.woofc-area input.qty', function() {
    var item_key = $(this).attr('name');
    var item_qty = $(this).val();

    woofc_update_qty(item_key, item_qty);
  });

  // Qty validate
  $(document).on('keyup', '.woofc-area .qty', function() {
    var $this = $(this);

    if ($this.closest('.woopq-quantity-input').length) {
      // checked in WPC Product Quantity
      return;
    }

    if (woofc_timeout != null) clearTimeout(woofc_timeout);
    woofc_timeout = setTimeout(woofc_check_qty, 1000, $this);
  });

  // Remove item
  $(document).
      on('click touch', '.woofc-area .woofc-item-remove', function() {
        var item_key = $(this).attr('data-key');
        var item_name = $(this).attr('data-name');

        if (item_key === undefined) {
          item_key = $(this).closest('.woofc-item').attr('data-key');
        }

        if (item_name === undefined) {
          item_name = $(this).closest('.woofc-item').attr('data-name');
        }

        $(this).closest('.woofc-item').addClass('woofc-item-removing');
        woofc_remove_item(item_key, item_name);
      });

  $(document).on('click touch', '.woofc-overlay', function() {
    woofc_hide_cart();
  });

  $(document).on('click touch', '.woofc-close', function() {
    woofc_hide_cart();
  });

  $(document).on('click touch', '.woofc-continue-url', function() {
    var url = $(this).attr('data-url');

    woofc_hide_cart();

    if (url != '') {
      window.location.href = url;
    }
  });

  $(document).on('click touch', '.woofc-empty-cart', function() {
    woofc_cart_loading();

    var data = {
      action: 'woofc_empty_cart',
      security: woofc_vars.nonce,
    };

    $.post(woofc_vars.ajaxurl, data, function(response) {
      woofc_cart_reload();
    });
  });

  // Count button
  $(document).on('click touch', '.woofc-count', function(e) {
    woofc_toggle_cart();

    e.preventDefault();
  });

  // Menu item
  $(document).on('click touch', '.woofc-menu-item a', function(e) {
    if (woofc_vars.cart_url != '') {
      window.location.href = woofc_vars.cart_url;
    } else {
      woofc_toggle_cart();
    }

    e.preventDefault();
  });

  // Cart
  $(document).on('click touch', '.woofc-cart, .woofc-btn', function(e) {
    woofc_toggle_cart();

    e.preventDefault();
  });

  // Cart link
  $(document).on('click touch', '.woofc-cart-link a', function(e) {
    if (woofc_vars.cart_url != '') {
      window.location.href = woofc_vars.cart_url;
    } else {
      woofc_toggle_cart();
    }

    e.preventDefault();
  });

  // Coupon
  $(document).on('click', '.woofc-coupon-check', function() {
    var coupon_code = $('.woofc-coupon-code').val();

    if (coupon_code == '') {
      $('.woofc-coupon-code').focus();
    } else {
      woofc_cart_loading();

      var data = {
        action: 'woofc_apply_coupon',
        coupon_code: coupon_code,
        security: woofc_vars.nonce,
      };

      $.post(woofc_vars.ajaxurl, data, function(response) {
        woofc_cart_reload();
      });
    }
  });

  $(document).on('keypress', '.woofc-coupon-code', function(e) {
    if (e.keyCode == 13) {
      var coupon_code = $('.woofc-coupon-code').val();

      if (coupon_code !== '') {
        woofc_cart_loading();

        var data = {
          action: 'woofc_apply_coupon',
          coupon_code: coupon_code,
          security: woofc_vars.nonce,
        };

        $.post(woofc_vars.ajaxurl, data, function(response) {
          woofc_cart_reload();
        });
      }
    }
  });

  $(document).on('click', '.woofc-coupon-remove', function() {
    woofc_cart_loading();

    var coupon_code = $(this).
        closest('.woofc-coupon-added').
        attr('data-code');
    var data = {
      action: 'woofc_remove_coupon',
      coupon_code: coupon_code,
      security: woofc_vars.nonce,
    };

    $.post(woofc_vars.ajaxurl, data, function(response) {
      woofc_cart_reload();
    });
  });

  $(document).on('click', '.woofc-undo a', function(e) {
    e.preventDefault();
    woofc_cart_loading();

    var undo_key = $('body').attr('woofc-undo-key');
    var data = {
      action: 'woofc_undo_remove',
      item_key: undo_key,
      security: woofc_vars.nonce,
    };

    $.post(woofc_vars.ajaxurl, data, function(response) {
      woofc_cart_reload();
    });

    $('body').attr('woofc-undo-key', '');
    $('body').attr('woofc-undo-name', '');
  });

  $(document).on('click', '.woofc-shipping', function(e) {
    $(this).toggleClass('woofc-shipping-open');
    $('.woofc-shipping-area').toggleClass('woofc-shipping-area-open');
  });

  $(document).on('change', 'input.woofc-shipping-method', function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    woofc_cart_loading();

    var shipping_methods = {};

    $('select.shipping_method, :input[name^=woofc_shipping_method][type=radio]:checked, :input[name^=shipping_method][type=hidden]').
        each(function() {
          shipping_methods[$(this).data('index')] = $(this).val();
        });

    var data = {
      action: 'woofc_update_shipping_method',
      shipping_methods: shipping_methods,
    };

    $.post(woofc_vars.ajaxurl, data, function(response) {
      woofc_cart_reload();
    });
  });

  $(document).
      on('submit', '.woofc-area form.woocommerce-shipping-calculator',
          function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            woofc_cart_loading();

            var $form = $(this);

            $('<input />').
                attr('type', 'hidden').
                attr('name', 'calc_shipping').
                attr('value', '1').
                appendTo($form);

            var formData = $form.serializeArray();
            var data = {
              action: 'woofc_calculate_shipping',
            };

            $(formData).each(function(index, obj) {
              data[obj.name] = obj.value;
            });

            $.post(woofc_vars.ajaxurl, data, function(response) {
              woofc_cart_reload();
            });
          });
})(jQuery);

function woofc_decimal_places(num) {
  var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);

  if (!match) {
    return 0;
  }

  return Math.max(
      0,
      // Number of digits right of decimal point.
      (match[1] ? match[1].length : 0)
      // Adjust for scientific notation.
      - (match[2] ? +match[2] : 0));
}

function woofc_update_qty(cart_item_key, cart_item_qty) {
  woofc_cart_loading();

  var data = {
    action: 'woofc_update_qty',
    cart_item_key: cart_item_key,
    cart_item_qty: cart_item_qty,
    security: woofc_vars.nonce,
  };

  jQuery.post(woofc_vars.ajaxurl, data, function(response) {
    woofc_cart_reload();

    jQuery(document.body).
        trigger('woofc_update_qty', [cart_item_key, cart_item_qty]);
  });
}

function woofc_remove_item(cart_item_key, cart_item_name) {
  woofc_cart_loading();

  var data = {
    action: 'woofc_remove_item',
    cart_item_key: cart_item_key,
    security: woofc_vars.nonce,
  };

  jQuery.post(woofc_vars.ajaxurl, data, function(response) {
    if (!response || !response.fragments) {
      return;
    }

    jQuery(document.body).
        trigger('removed_from_cart', [response.fragments, response.cart_hash]);

    jQuery('body').attr('woofc-undo-key', cart_item_key);
    jQuery('body').attr('woofc-undo-name', cart_item_name);
    woofc_cart_reload();

    jQuery(document.body).
        trigger('woofc_remove_item', [cart_item_key, cart_item_name, response]);
  });
}

function woofc_cart_loading() {
  jQuery('.woofc-area').addClass('woofc-area-loading');
  jQuery('.woofc-count').
      addClass('woofc-count-loading').
      removeClass('woofc-count-shake');

  jQuery(document.body).trigger('woofc_cart_loading');
}

function woofc_cart_reload() {
  woofc_unslick();
  jQuery(document.body).trigger('wc_fragment_refresh');
  jQuery(document.body).trigger('woofc_cart_reload');
  woofc_slick();
}

function woofc_cart_loaded() {
  jQuery('.woofc-area').removeClass('woofc-area-loading');
  jQuery('.woofc-count').
      removeClass('woofc-count-loading').
      addClass('woofc-count-shake');

  if ((
      woofc_vars.undo_remove == 'yes'
  ) && (
      jQuery('body').attr('woofc-undo-key') != undefined
  ) && (
      jQuery('body').attr('woofc-undo-key') != ''
  )) {
    var undo_name = 'Item';

    if ((
        jQuery('body').attr('woofc-undo-name') != undefined
    ) && (
        jQuery('body').attr('woofc-undo-name') != ''
    )) {
      undo_name = '"' + jQuery('body').attr('woofc-undo-name') + '"';
    }

    jQuery('.woofc-area-mid').
        prepend(
            '<div class="woofc-undo"><div class="woofc-undo-inner">' +
            woofc_vars.removed_text.replace('%s', undo_name) + ' <a href="#">' +
            woofc_vars.undo_text +
            '</a></div></div>');
  }

  woofc_slick();
  woofc_perfect_scrollbar();
  jQuery(document.body).trigger('woofc_cart_loaded');
}

function woofc_perfect_scrollbar() {
  if (woofc_vars.scrollbar === 'yes') {
    jQuery('.woofc-area .woofc-area-mid').
        perfectScrollbar({suppressScrollX: true, theme: 'wpc'});
  }
}

function woofc_unslick() {
  if ((jQuery('.woofc-cross-sells-product').length > 1) &&
      jQuery('.woofc-cross-sells-products').hasClass('slick-initialized')) {
    jQuery('.woofc-cross-sells-products').slick('unslick');
  }
}

function woofc_slick() {
  var is_rtl = woofc_vars.is_rtl === '1';

  if (jQuery('.woofc-cross-sells-product').length > 1) {
    jQuery('.woofc-cross-sells-products').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      dots: true,
      arrows: false,
      rtl: is_rtl,
    });
  }
}

function woofc_show_cart() {
  jQuery('body').addClass('woofc-show');

  jQuery(document.body).trigger('woofc_show_cart');
}

function woofc_hide_cart() {
  jQuery('body').removeClass('woofc-show');

  jQuery(document.body).trigger('woofc_hide_cart');
}

function woofc_toggle_cart() {
  if (jQuery('body').hasClass('woofc-show')) {
    woofc_hide_cart();
  } else {
    woofc_show_cart();
  }

  jQuery(document.body).trigger('woofc_toggle_cart');
}

function woofc_check_qty($qty) {
  var val = parseFloat($qty.val());
  var min = parseFloat($qty.attr('min'));
  var max = parseFloat($qty.attr('max'));
  var step = parseFloat($qty.attr('step'));
  var fix = Math.pow(10, Number(woofc_decimal_places(step)) + 1);

  if ((val === '') || isNaN(val)) {
    val = 0;
  }

  if ((step === '') || isNaN(step)) {
    step = 1;
  }

  var remainder = woofc_float_remainder(val, step);

  if (remainder >= 0) {
    val = Math.round((val - remainder) * fix) / fix;
  }

  if (!isNaN(min) && (
      val < min
  )) {
    val = min;
  }

  if (!isNaN(max) && (
      val > max
  )) {
    val = max;
  }

  $qty.val(val);
}

function woofc_decimal_places(num) {
  var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);

  if (!match) {
    return 0;
  }

  return Math.max(
      0,
      // Number of digits right of decimal point.
      (match[1] ? match[1].length : 0)
      // Adjust for scientific notation.
      - (match[2] ? +match[2] : 0));
}

function woofc_float_remainder(val, step) {
  var valDecCount = (val.toString().split('.')[1] || '').length;
  var stepDecCount = (step.toString().split('.')[1] || '').length;
  var decCount = valDecCount > stepDecCount ? valDecCount : stepDecCount;
  var valInt = parseInt(val.toFixed(decCount).replace('.', ''));
  var stepInt = parseInt(step.toFixed(decCount).replace('.', ''));
  return (valInt % stepInt) / Math.pow(10, decCount);
}