jQuery(document).ready(function($){
	jQuery(".btn-focus a").click(function(){
		jQuery(this).blur();
	});

	jQuery('li.my_account').on('click', function(){
		jQuery(this).toggleClass('user-account-open');
		jQuery('body').removeClass('woofc-show');
	});

	jQuery(document).click(function(e){
		if (!jQuery(e.target).parents().andSelf().is('.my_account')){
			jQuery('.my_account').removeClass('user-account-open');
			//   jQuery('body').removeClass('woofc-show');
		}
  	});

	if($(window).width() < 767){
		jQuery(".footer-wrap .widget.widget_nav_menu h2").on("click", function() {
			if(jQuery(this).hasClass("active")){
				jQuery(this).removeClass("active");
				jQuery(this).parent('.widget_nav_menu').find("ul.menu")
				  .slideUp(200);
			} 
			else{
				jQuery(".footer-wrap .widget.widget_nav_menu h2").removeClass("active");
				jQuery(this).addClass("active");
				jQuery("ul.menu").slideUp(200);
				jQuery(this).parent('.widget_nav_menu').find("ul.menu")
				  .slideDown(200);
			}
		});
	}

	$(".mob-nav > nav#site-navigation > .menu-mobile-menu-container > ul#mobile-menu > li > a").on('click', function(){
		$(this).removeAttr("href");	
		$(this).parents('.menu-item-has-children').find('.sub-menu').toggleClass("mob-sub-menu-open");
		$('.mob-nav > .form-wrap .search-form-desc').removeClass('search-form-open');
		$('wpmm_mobile_menu_btn show-close-icon').removeClass('menu-active');
			$('wpmm_mobile_menu_btn show-close-icon').removeClass('menu-active');
		$('#menu-main-menu').css({'display' : 'none'});
		return false;
	});

	$(".mob-nav > .form-wrap .search-toggle-menu").click(function(){
		$(this).next('.search-form-desc').toggleClass("search-form-open");
		$('.mob-nav > nav#site-navigation ul#mobile-menu > li .sub-menu').removeClass('mob-sub-menu-open');
		$('wpmm_mobile_menu_btn show-close-icon').removeClass('menu-active');
		$('#menu-main-menu').css({'display' : 'none'});
		return false;
	});

	$('li.wpmm-submenu-right > a ').on('click', function(){
			$(this).parents('.wpmm-submenu-right').toggleClass('sub-megamenu-open');				
			$(this).parents('.wpmm-submenu-right').find('.wpmm-strees-row-and-content-container').toggleClass('show').slideToggle(500);
			$(this).parents('.wpmm_mega_menu').siblings().find('.wpmm-strees-row-and-content-container').removeClass('show').slideUp(500);
			$('.wp-megamenu-sub-menu .sub-menu').addClass('hide').removeAttr("style");			   

	});		
	
	if($(window).width() < 770){
		$('.wp-megamenu-sub-menu li.menu-item.menu-item-has-children > a').on('click', function(){	
			// alert("ok");
			$(this).removeAttr("href");	
			$(this).parent().find('.sub-menu').toggleClass('show').removeClass('hide').slideToggle(500);
		});
	} 
	else{
		// change functionality for larger screens
	}
		$("#menu-main-menu .menu-item-has-children").append("<b class='fa fa-caret-right'></b>")
		$('#menu-main-menu .menu-item-has-children > a').on('click', function(){
		//removing the previous selected menu state
		$(this).parents('.menu-item-has-children').toggleClass('childsub-megamenu-open');
	});
	
	// On product dropdown select option, change width of input text field and option
	$('#product_cat').on('change', function(){
		//removing the previous selected menu state
		$(this).parents('.menu-item-has-children').toggleClass('childsub-megamenu-open');

		let arrowWidth = 2;
		let $this = $(this);
		$this.width(100);

		var text = $this.find('option:selected').text(); // selected category name
				
		// get font-weight, font-size, and font-family
		let style = window.getComputedStyle(this);
		let { fontWeight, fontSize, fontFamily } = style;

		// create test element
		let $test = $("<span>").html(text).css({
			"font-size": fontSize, 
			"font-weight": fontWeight, 
			"font-family": fontFamily,
			"visibility": "hidden" // prevents FOUC
		});

		// add to body, get width, and get out
		$test.appendTo($this.parent());
		let width = $test.width();
		$test.remove();

		// set select width
		$this.width(width + arrowWidth);
	}).change();
	

	
	// Product dropdown change width - Mobile
	$('#mob_product_cat').on('change', function(){
		//removing the previous selected menu state
		$(this).parents('.menu-item-has-children').toggleClass('childsub-megamenu-open');

		let arrowWidth = 18;
		let $this = $(this);
		$this.width(100);

		var text = $this.find('option:selected').text(); // selected category name
				
		// get font-weight, font-size, and font-family
		let style = window.getComputedStyle(this);
		let { fontWeight, fontSize, fontFamily } = style;

		// create test element
		let $test = $("<span>").html(text).css({
			"font-size": fontSize, 
			"font-weight": fontWeight, 
			"font-family": fontFamily,
			"visibility": "hidden" // prevents FOUC
		});

		// add to body, get width, and get out
		$test.appendTo($this.parent());
		let width = $test.width();
		$test.remove();

		// set select width
		$this.width(width + arrowWidth);
	}).change();
	

	// News sort encode uri
	$('#sort_news, #sort_rating').on('change', function(e){
		e.preventDefault();
		var val = $(this).val();
		window.location.href = encodeURI(val);
	});

	// Show and hide popup onclick of pdf
	$('.pop_pdf').on('click', function(event){			
		$('.success-popup').addClass("show-success-popup");
	});
		
	$('.close-popup').on('click', function(event){			
		$('.success-popup').removeClass("show-success-popup");
	});

	$('.success-popup').on('click', function(event){
		$('.success-popup').removeClass("show-success-popup");
	});

	// hide placeholder when user focus it and show when blur it
	$('input,textarea').focus(function(){
		$(this).data('placeholder',$(this).attr('placeholder'))
				.attr('placeholder','');
	}).blur(function(){
		$(this).attr('placeholder',$(this).data('placeholder'));
	});

	// Woocommerce mini cart
	$('ul.dropdown-menu.dropdown-menu-mini-cart').hide();
	$('a.dropdown-back').on('click', function(){
		$('ul.dropdown-menu.dropdown-menu-mini-cart').toggle();
	});
	
	if($(window).width() < 768){ 
		$(".woocommerce-account .woocommerce-MyAccount-navigation .block-title span").click(function(){
			$(this).parents('.woocommerce-account .woocommerce-MyAccount-navigation').find('ul').slideToggle(500);
		});
	};

	// news page accordion js
	if($(window).width() < 770){ 
		$(".right-section-news .widget.widget_block h2").click(function(){
			$(".right-section-news .widget.widget_block h2").toggleClass("icon-roted")
			$(".widget_recent_entries").slideToggle("slow");
			$(".widget_categories").slideToggle("slow");
			$("#block-17").slideToggle("slow");
			$(".widget_tag_cloud").slideToggle("slow");
		});
	};

	// product page accordion right side js
	if($(window).width() < 991){ 
		$(".main-content-wrap .right-bar .widget_recently_viewed_products h2").click(function(){
			$(".main-content-wrap .right-bar .widget_recently_viewed_products h2").toggleClass("icon-roted")
			$(".main-content-wrap .product_list_widget").slideToggle("slow");
		});

		$(".main-content-wrap .right-bar .tinv-wishlist h2").click(function(){
			$(".main-content-wrap .right-bar .tinv-wishlist h2").toggleClass("icon-roted")
			$(".main-content-wrap form").slideToggle("slow");
		});
	};

	// product detail page accordion design mobile view js
	if($(window).width() < 770){ 
		$('.tab_wrapper').champ();
		jQuery(".accordian_header").on("click", function(){
			setTimeout(function(){
				jQuery('html, body').animate({
					scrollTop: jQuery('.tab_wrapper').offset().top
				}, 300);
			},700);
		});
	};

	/*
	* add related product's id to hidden field value in single product page
	*/
	$('.related-checkbox').each(function(elem){
		$(this).on('click', addRelatedToProduct);
	});

	var relatedProductsCheckFlag = false;
	$("#select_all_related").on('click', function(e){
		e.preventDefault();
		selectAllRelated($(this));
	});

	function selectAllRelated(txt){
		if(relatedProductsCheckFlag == false){
			$('.related-checkbox').each(function(elem){
				$(this).prop('checked', true);
			});
			relatedProductsCheckFlag = true;
			txt.text('unselect all');
		}
		else{
			$('.related-checkbox').each(function(elem){
				$(this).removeAttr('checked');
			});
			relatedProductsCheckFlag = false;
			txt.text('select all');
		}
		addRelatedToProduct();
	}

	function addRelatedToProduct(){
		var checkboxes = $('.related-checkbox');
		var values = [];
		for(var i = 0;i < checkboxes.length;i++){
			if(checkboxes[i].checked) values.push(checkboxes[i].value);
		}
		var related_products_field = $('#related-products-field');
		if(related_products_field){
			related_products_field.val(values.join(','));
		}
	}

	/*
	* coupon form validation 
	*/
	$('#apply_coupon_button').on('click', discountForm);

	function discountForm(e){
		coupon_code = $('#coupon_code').val();

		if(!isEmptyOrSpaces(coupon_code)){
			$('#coupon_code').removeClass('required-entry');
			$('#advice-required-entry-coupon_code').hide();
		} 
		else{
			e.preventDefault();
			$('#coupon_code').addClass('required-entry');
			$('#advice-required-entry-coupon_code').show();
		}
	}

	// Check any field is contain space or not
	function isEmptyOrSpaces(str){
		return str === null || str.match(/^ *$/) !== null;
	}

	// Manual punch clocks - savings per employee (calculator)
	jQuery(".wage-change").keyup(function(){
		isFormChange();
	});

	jQuery(".wage-change").change(function(){
		isFormChange();
	});
	
	function isFormChange(){
		var wage = jQuery("#employee_wage1").val();
		var person = jQuery("#personnel_wage1").val();
		
		//check wage not empty
		if(!!wage && !!person && wage >= 0 && person >= 0){
			var over = ((wage/60) * 48 *261).toFixed(2);
			var error = ((wage*8 *261) * 0.07).toFixed(2);
			var audit = ((person/60) * 0.5 * 261).toFixed(2);
			var payroll = ((person/60) * 0.5 * 261).toFixed(2);
			var buddy = (wage*8*261 * 0.02).toFixed(2);
			var swipe = ((wage/60)*0.5*261).toFixed(2);
			var prox = ((wage/60)*261).toFixed(2);
			var bio = ((wage/60)*0.5*261).toFixed(2);
			
			jQuery("#saving-table-over").html("$"+over);
			jQuery("#saving-table-error").html("$"+error);
			jQuery("#saving-table-audit").html("$"+audit);
			jQuery("#saving-table-payroll").html("$"+payroll);
			jQuery("#saving-table-buddy").html("$"+buddy);
			jQuery("#saving-table-swipe").html("$"+swipe);
			jQuery("#saving-table-prox").html("$"+prox);
			jQuery("#saving-table-bio").html("$"+bio);
		}  

		//check wage is not nagative
		if(wage < 0 || person < 0){ 
			jQuery("#saving-table-over").html("$"+ 0);
			jQuery("#saving-table-error").html("$"+ 0);
			jQuery("#saving-table-audit").html("$"+ 0);
			jQuery("#saving-table-payroll").html("$"+ 0);
			jQuery("#saving-table-buddy").html("$"+ 0);
			jQuery("#saving-table-swipe").html("$"+ 0);
			jQuery("#saving-table-prox").html("$"+ 0);
			jQuery("#saving-table-bio").html("$"+ 0);
		} 
	}

	// Synchronized clock systems - savings per employee (calculator)
	jQuery(".wage-change").keyup(function(){
		isChange();
	});

	jQuery(".wage-change").change(function(){
		isChange();
	});

	function isChange(){
		var wage = jQuery("#employee_wage").val();
		var person = jQuery("#personnel_wage").val();
		var clocks = jQuery("#no_clocks").val();
		
		//check wage not empty
		if(!!wage && wage >= 0){
			var val = ((wage / 60) * 48 * 261).toFixed(2);
			jQuery("#wage-td").html("$"+val);
		}

		// check wage is not nagative
		if(wage < 0){
			jQuery("#wage-td").html("$"+ 0);
		}
		
		//check all fields aren't empty
		if(!!person && !!clocks && person >= 0 && clocks >= 0){
			var val = ((person / 60) * 15 * 2 * clocks).toFixed(2);
			jQuery("#daylight-td").html("$"+val);
		}

		//check all fields aren't nagative
		if(person < 0 || clocks < 0){
			jQuery("#daylight-td").html("$"+ 0);
		}
	}

	// State list of US and Canada
	var us_state = '<option value="">Please select region, state or province</option><option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District Of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option>';

	var canada_state = '<option value="">Please select region, state or province</option><option value="AB">Alberta</option><option value="BC">British Columbia</option><option value="MB">Manitoba</option><option value="NB">New Brunswick</option><option value="NL">Newfoundland and Labrador</option><option value="NT">Northwest Territories</option><option value="NS">Nova Scotia</option><option value="NU">Nunavut</option><option value="ON">Ontario</option><option value="PE">Prince Edward Island</option><option value="QC">Quebec</option><option value="SK">Saskatchewan</option><option value="YT">Yukon Territory</option>';

	// By default display US state
	jQuery('.customer_js_states').html(us_state);

	// Display US state and Canada state in contact form 7
	jQuery("#country, #country_id").change(function(){
		if((jQuery(this).val() == 'United States') || (jQuery(this).val() == 'US')){
			jQuery('.js_canada-states').css('display', 'none');
			jQuery('.js_us-states').css('display', 'block');

			jQuery('.customer_js_states').css('display', 'block');
			jQuery('.customer_js_states').html(us_state);
			jQuery('.js_other-states').css('display', 'none');
		}
		else if(jQuery(this).val() == 'Canada' || jQuery(this).val() == 'CA'){
			jQuery('.js_canada-states').css('display', 'block');
			jQuery('.js_us-states').css('display', 'none');

			jQuery('.customer_js_states').css('display', 'block');
			jQuery('.customer_js_states').html(canada_state);
			jQuery('.js_other-states').css('display', 'none');
		}
		else if(jQuery(this).val() == 'PR' || jQuery(this).val() == ''){
			jQuery('.js_canada-states').css('display', 'none');
			jQuery('.js_us-states').css('display', 'none');

			jQuery('.customer_js_states').css('display', 'none');
			jQuery('.js_other-states').css('display', 'block');
		}
	});

	// conatct form 7 - disable Select option
	var $option1 = jQuery('option:contains("Select option")');
	var $option2 = jQuery('option:contains("Select your product")');
	$option1.attr('disabled',true);
	$option2.attr('disabled',true);

	// Contact form 7 - datepicker
	var isTouchDevice = 'ontouchstart' in document.documentElement;
	if(isTouchDevice){
		// do mobile handling
		var date = $('#purchase_date');
		date.attr('type','date');
	}
	else{
		if(jQuery("#js-purchase-datepicker").length){
			jQuery("#purchase_date").datepicker();
		}
	}

	// Comment form validation for product detail page
	$("#submit").on("click",function(e){
		var comment = $("#comment").val();
		var author = $("#author").val();
		var email = $("#email").val();

		if(comment == '' || author == '' || email == ''){
			alert('Please fill all fields.');
			e.preventDefault();
		}
		else if(!isValidName(author)){
			alert('Please enter valid author.');
			e.preventDefault();
		}
		else if(!isValidEmailAddress(email)){
			alert('Please enter valid email address.');
			e.preventDefault();
		}
    });

    // check author validation in comment form
	function isValidName(author_name){
		var regex = /^[a-zA-Z ]+$/;
		let result = regex.test(author_name);
		return result;
	}

	// check email validation in comment form
    function isValidEmailAddress(emailAddress){
		var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
		return pattern.test(emailAddress);
	}

});
