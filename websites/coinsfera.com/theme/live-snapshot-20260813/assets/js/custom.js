jQuery(function () {
  // ==============================================================
  //Fix header while scroll
  // ==============================================================
  var wind = jQuery(window);
  wind.on("load", function () {
	  console.log("hey custom.js is working");
    var bodyScroll = wind.scrollTop(),
      navbar = jQuery(".topbar");
    if (bodyScroll > 40) {
      navbar.addClass("fixed-header");
    } else {
      navbar.removeClass("fixed-header");
    }
  });
  jQuery(window).scroll(function () {
    if (jQuery(window).scrollTop() >= 50) {
      jQuery(".topbar").addClass("fixed-header");
    } else {
      jQuery(".topbar").removeClass("fixed-header");
    }
  });

  var owl = jQuery(".banner-carousal");
  owl.owlCarousel({
    items: 1,
    loop: true,
    margin: 100,
    autoplay: false,
    autoplayTimeout: 1500,
    autoplayHoverPause: true,
    nav: true,
    navText: [jQuery(".prev"), jQuery(".next")],
  });

  // faqs

  // Add minus icon for collapse element which is open by default
  jQuery(".collapse.show").each(function () {
    jQuery(this)
      .prev(".acc-title")
      .find(".fas")
      .addClass("fa-minus-circle")
      .removeClass("fa-plus-circle")
      .parent()
      .parent()
      .parent()
      .addClass("active");
  });

  // Toggle plus minus icon on show hide of collapse element
  jQuery(".collapse")
    .on("show.bs.collapse", function () {
      jQuery(this)
        .prev(".acc-title")
        .find(".fas")
        .removeClass("fa-plus-circle")
        .addClass("fa-minus-circle")
        .parent()
        .parent()
        .parent()
        .addClass("active");
    })
    .on("hide.bs.collapse", function () {
      jQuery(this)
        .prev(".acc-title")
        .find(".fas")
        .removeClass("fa-minus-circle")
        .addClass("fa-plus-circle")
        .parent()
        .parent()
        .parent()
        .removeClass("active");
    });
});

//header menu submenu js
/*jQuery( '.primary-menu li.menu-item-has-children' ).each( function() {
  jQuery( '> a.nav-link', this ).addClass( 'dropdown-toggle' );
  jQuery( '> a.nav-link', this ).attr( 'data-toggle', 'dropdown' );
  jQuery( '> a.nav-link', this ).attr( 'aria-haspopup', 'true' );
  jQuery( '> a.nav-link', this ).attr( 'aria-expanded', 'false' );
  jQuery( '.sub-menu li', this ).addClass( 'dropdown-list-item' ).removeClass('nav-item');
  jQuery( '.sub-menu li a', this ).addClass( 'dropdown-item' ).removeClass('nav-link');
  jQuery( '> a.nav-link', this ).attr( 'href', 'javascript:void(0)' );
});*/

jQuery(function() {
    jQuery( 'ul.dropdown-menu [data-toggle="dropdown"]' ).on( 'click', function(event) {
      event.preventDefault();
      event.stopPropagation();
		
		jQuery(this).next().addClass("active-target");
		jQuery("ul.dropdown-submenu:not(.active-target)").hide("slow");
		jQuery(".active-target").toggle("slow");
		
		jQuery(".active-target").removeClass("active-target");
		
		
// 		jQuery(this).next().addClass("targeted");
// 		var submenus = jQuery('.dropdown-menu.dropdown-submenu');
// 		for(var i=0; i<submenus.length;i++){
// 			if(submenus[i].hasClass("targeted")){
// 				//do nothing
// 			}
// 			else{
// 				submenus[i].hide("slow");
// 			}
			
// 		}
		
// 		jQuery(this).parent().find('.dropdown-submenu').toggle("slow");
// 		jQuery(this).next().addClass("targeted");
	

//       jQuery( this ).siblings().toggleClass( 'show' );

		

//       if ( !jQuery( this ).next().hasClass( 'show' ) ) {
//         jQuery( this ).parents( '.dropdown-menu' ).first().find( '.show' ).removeClass( 'show' );
//       }

//       jQuery( this ).parents( 'li.nav-item.dropdown.show' ).on( 'hidden.bs.dropdown', function(e) {
//         jQuery( '.dropdown-submenu .show' ).removeClass( 'show' );
//       });
    });
  });
/* jQuery for scroll on btn click */

jQuery('.buy-btn').click(function () {
    jQuery('html, body').animate({
        scrollTop: jQuery(jQuery(this).attr('href')).offset().top - 80
    }, 1000);

    return false;
});

jQuery(window).on('load', function () {
    jQuery('.dropdown-menu .tab-content').addClass( 'd-none' );
});

/*jQuery('.custom-tab-menu').on('click', function () {
    jQuery('.dropdown-menu .tab-content').removeClass( 'd-none' );
});*/

jQuery('.dropdown-menu').on('mouseleave', function () {
    if (jQuery('.dropdown-menu').css('display') == 'none'){
      jQuery('.dropdown-menu .tab-content').addClass( 'd-none' );
      jQuery('.custom-tab-menu .nav-link').removeClass('active');
      jQuery('#custom-tabcontent .tab-pane').removeClass('active');
    }
});

jQuery('.custom-tab-menu .nav-link').hover(function () {
    
    var paneID = jQuery(this).attr('href');
    
    jQuery('.dropdown-menu .tab-content').removeClass( 'd-none' );
    jQuery('.custom-tab-menu .nav-link').removeClass('active');
    jQuery('#custom-tabcontent .tab-pane').removeClass('active');
    jQuery(this).addClass('active');
    jQuery('#custom-tabcontent .tab-pane' + paneID).addClass('active');
    
});


jQuery('.blog-heading').click(function () {
    jQuery('html, body').animate({
        scrollTop: jQuery(jQuery(this).attr('href')).offset().top - 150
    }, 1000);

    return false;
});

// ==============================================================
// ---> START OF OPTIMIZED CALCULATOR CODE <---
// ==============================================================

var calcTimeout;

// 1. When typing in the USD amount input field
jQuery(document).on('change input', '.price-value', function() {
    var $input = jQuery(this);
    var calcBox = $input.closest('.calc-box');
    var mode = calcBox.data('calc-mode') || 'buy';
    
    // Skip calculations if this change was triggered inside a 'sell' box calculation sequence
    if (mode === 'sell' && $input.is(':focus') === false) return;

    var price = parseFloat($input.val());
    var coin_name = $input.data('coin');
    
    if (isNaN(price) || price <= 0) {
        calcBox.find('.coin-value').val(0);
        return;
    }

    clearTimeout(calcTimeout);
    calcTimeout = setTimeout(function() {
        jQuery.ajax({
            url: coinsfera_ajax_url,
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'getSingleCoinPrice',
                fsym: coin_name,
                tsyms: 'USD'
            },
            success: function(response) {
                if (response && response.USD) {
                    var actualPrice = parseFloat(response.USD);
                    var coinValue = price / actualPrice;
                    calcBox.find('.coin-value').val(coinValue.toFixed(6));
                }
            }
        });
    }, 250);
});

// 2. When typing in the Crypto amount input field (Active on Sell layout setups)
jQuery(document).on('change input', '.coin-value', function() {
    var $input = jQuery(this);
    var calcBox = $input.closest('.calc-box');
    var mode = calcBox.data('calc-mode') || 'buy';

    // Only process top-down inputs if user is typing manually on a sell page
    if (mode === 'buy') return;

    var cryptoAmount = parseFloat($input.val());
    var coin_name = $input.data('coin');
    
    if (isNaN(cryptoAmount) || cryptoAmount <= 0) {
        calcBox.find('.price-value').val(0);
        return;
    }

    clearTimeout(calcTimeout);
    calcTimeout = setTimeout(function() {
        jQuery.ajax({
            url: coinsfera_ajax_url,
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'getSingleCoinPrice',
                fsym: coin_name,
                tsyms: 'USD'
            },
            success: function(response) {
                if (response && response.USD) {
                    var actualPrice = parseFloat(response.USD);
                    var usdValue = cryptoAmount * actualPrice;
                    calcBox.find('.price-value').val(usdValue.toFixed(2));
                }
            }
        });
    }, 250);
});

// ==============================================================
// ---> END OF OPTIMIZED CALCULATOR CODE <---
// ==============================================================