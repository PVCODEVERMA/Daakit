import { checkOptions, names } from './custom-switcher';
(function($) {
	"use strict";
	
	// ______________Active Class
	$(".app-sidebar .toggle-menu.side-menu a").each(function() {
		var pageUrl = window.location.href.split(/[?#]/)[0];
		if (this.href == pageUrl) {
			$(this).addClass("active");
			$(this).parent().addClass("active"); // add active to li of the current link
			$(this).parent().parent().prev().addClass("active"); // add active class to an anchor
			$(this).parent().parent().prev().click(); // click the item to make it drop
		}
	});
	
	// ______________ Modal
	// $("#myModal").modal('show');
	
		$("a[data-theme]").click(function() {
			$("head link#theme").attr("href", $(this).data("theme"));
			$(this).toggleClass('active').siblings().removeClass('active');
		});
		$("a[data-effect]").click(function() {
			$("script#effect").attr("src", $(this).data("effect"));
			$(this).toggleClass('active').siblings().removeClass('active');
		});
	
	// ______________Cover Image
	$(".cover-image").each(function() {
		var attr = $(this).attr('data-image-src');
		if (typeof attr !== typeof undefined && attr !== false) {
			$(this).css('background', 'url(' + attr + ') center center');
		}
	});
	
	// ______________Loader
	$(window).on("load", function(e) {
		$("#global-loader").fadeOut("slow");
	})
	
	
	// ______________Horizontal-menu Active Class
	$(document).ready(function() {
		$(".horizontalMenu-list li a").each(function() {
			var pageUrl = window.location.href.split(/[?#]/)[0];
			if (this.href == pageUrl) {
				$(this).addClass("active");
				$(this).parent().addClass("active"); // add active to li of the current link
				$(this).parent().parent().prev().addClass("active"); // add active class to an anchor
				$(this).parent().parent().prev().click(); // click the item to make it drop
			}
		});
	});
	
	// ______________ GLOBAL SEARCH
	$(document).on("click", "[data-bs-toggle='search']", function(e) {
		var body = $("body");

		if(body.hasClass('search-gone')) {
			body.addClass('search-gone');
			body.removeClass('search-show');
		}else{
			body.removeClass('search-gone');
			body.addClass('search-show');
		}
	});
	
	// ______________LEFTMENU
	var toggleSidebar = function() {
		var w = $(window);
		if(w.outerWidth() <= 991) {
			$("body").addClass("sidebar-gone");
			$(document).off("click", "body").on("click", "body", function(e) {
				if($(e.target).hasClass('sidebar-show') || $(e.target).hasClass('search-show')) {
					$("body").removeClass("sidebar-show");
					$("body").addClass("sidebar-gone");
					$("body").removeClass("search-show");
				}
			});
		}else{
			$("body").removeClass("sidebar-gone");
		}
	}
	toggleSidebar();
	$(window).resize(toggleSidebar);
	
	// ______________ Back to Top
	$(window).on("scroll", function(e) {
		if ($(this).scrollTop() > 130) {
			$('#back-to-top').fadeIn('slow');
		} else {
			$('#back-to-top').fadeOut('slow');
		}
	});

	// ______________Chart-circle
	if ($('.chart-circle').length) {
		$('.chart-circle').each(function() {
			let $this = $(this);
			$this.circleProgress({
				fill: {
					color: $this.attr('data-color')
				},
				size: $this.height(),
				startAngle: -Math.PI / 4 * 2,
				emptyFill: '#f6f6f6',
				lineCap: 'round'
			});
		});
	}
	
	
	// ______________Search
	$('body, .navbar-collapse form[role="search"] button[type="reset"]').on('click keyup', function(event) {
		if (event.which == 27 && $('.navbar-collapse form[role="search"]').hasClass('active') ||
		$(event.currentTarget).attr('type') == 'reset') {
		closeSearch();
		}
	});
	function closeSearch() {
		var $form = $('.navbar-collapse form[role="search"].active')
		$form.find('input').val('');
		$form.removeClass('active');
	}
	// Show Search if form is not active // event.preventDefault() is important, this prevents the form from submitting
	$(document).on('click', '.navbar-collapse form[role="search"]:not(.active) button[type="submit"]', function(event) {
		event.preventDefault();
		var $form = $(this).closest('form'),
		$input = $form.find('input');
		$form.addClass('active');
		$input.focus();
	});
	// if your form is ajax remember to call `closeSearch()` to close the search container
	$(document).on('click', '.navbar-collapse form[role="search"].active button[type="submit"]', function(event) {
		event.preventDefault();
		var $form = $(this).closest('form'),
		$input = $form.find('input');
		$('#showSearchTerm').text($input.val());
		closeSearch()
	});
	
	
	// ______________Quantity-right-plus
	$('.counter-plus').on('click',function(){
		var $qty=$(this).closest('div').find('.qty');
		var currentVal = parseInt($qty.val());
		if (!isNaN(currentVal)) {
		$qty.val(currentVal + 1);
		}
	});
	$('.counter-minus').on('click',function(){
		var $qty=$(this).closest('div').find('.qty');
		var currentVal = parseInt($qty.val());
		if (!isNaN(currentVal) && currentVal > 0) {
		$qty.val(currentVal - 1);
		}
	});
	
	// ______________Full screen
	$("#fullscreen-button").on("click", function toggleFullScreen() {

		$('html').addClass('window-fullscreen');
		if ((document.fullScreenElement !== undefined && document.fullScreenElement === null) || (document.msFullscreenElement !== undefined && document.msFullscreenElement === null) || (document.mozFullScreen !== undefined && !document.mozFullScreen) || (document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen)) {
			if (document.documentElement.requestFullScreen) {
			document.documentElement.requestFullScreen();
			} else if (document.documentElement.mozRequestFullScreen) {
			document.documentElement.mozRequestFullScreen();
			}
			else if (document.documentElement.webkitRequestFullScreen) {
			document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
			} 
			else if (document.documentElement.msRequestFullscreen) {
			document.documentElement.msRequestFullscreen();
			}
		} else {
			
			$('html').removeClass('window-fullscreen');
			if (document.cancelFullScreen) {
			document.cancelFullScreen();
			} else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
			} else if (document.webkitCancelFullScreen) {
			document.webkitCancelFullScreen();
			} else if (document.msExitFullscreen) {
			document.msExitFullscreen();
			}
		}
    })
	
	
	const DIV_CARD = 'div.card';
	
	// ___________TOOLTIP	
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})

	// __________POPOVER
	var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
	var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
		return new bootstrap.Popover(popoverTriggerEl)
	})

	$(function(e) {
		'use strict';
		$(document).on('click', function(e) {
			$('[data-bs-toggle="popover"]').each(function() {
				if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
					(($(this).popover('hide').data('bs.popover') || {}).inState || {}).click = false 
				}
			});
		});
	});
	
	// ______________Card Remove
	$('[data-bs-toggle="card-remove"]').on('click', function(e) {
		let $card = $(this).closest(DIV_CARD);
		$card.remove();
		e.preventDefault();
		return false;
	});
	
	// ______________Card Collapse
	$('[data-bs-toggle="card-collapse"]').on('click', function(e) {
		let $card = $(this).closest(DIV_CARD);
		$card.toggleClass('card-collapsed');
		e.preventDefault();
		return false;
	});
	
	// ______________Card Full Screen
	$('[data-bs-toggle="card-fullscreen"]').on('click', function(e) {
		let $card = $(this).closest(DIV_CARD);
		$card.toggleClass('card-fullscreen').removeClass('card-collapsed');
		e.preventDefault();
		return false;
	});
	
	
	// ______________Increment
	var quantitiy=0;
	$('.quantity-right-plus').on('click', function(e){
		e.preventDefault();
		var quantity = parseInt($('#quantity').val());
		$('#quantity').val(quantity + 1);

	});
	$('.quantity-left-minus').on('click', function(e){
		e.preventDefault();
		var quantity = parseInt($('#quantity').val());
		if(quantity>0){
			$('#quantity').val(quantity - 1);
		}
	});

	// ______________ SWITCHER-toggle ______________//
	$('.layout-setting').on("click", function (e) {
		let html = document.querySelector('html');
		if (html.getAttribute('data-theme-color') === "dark") {
			html.setAttribute('data-theme-color', 'light');
			html.setAttribute('data-header-style', 'color');
			html.setAttribute('data-menu-style', 'light');
			$('#switchbtn-lightmenu').prop('checked', true);
			$('#switchbtn-colorheader').prop('checked', true);
			
			$('#switchbtn-light').prop('checked', true);
			localStorage.setItem("soliclighttheme", true);
			localStorage.removeItem("solicdarktheme");
			localStorage.removeItem("solicbgColor");
			localStorage.removeItem("solicheaderbg");
			localStorage.removeItem("solicbgwhite");
			localStorage.removeItem("solicmenubg");

			localStorage.setItem("solicHeader", 'color');
        	localStorage.setItem("solicMenu", 'light');

			checkOptions();

			if (!document.body.classList.contains('auth-page')) {
				let mainHeader = document.querySelector('.app-header');
				mainHeader.style = "";
				let appSidebar = document.querySelector('.app-sidebar');
				appSidebar.style = "";
			}
			document.querySelector('html').style = '';
			names();

		} 
		else {
			html.setAttribute('data-theme-color', 'dark');
			html.setAttribute('data-header-style', 'dark');
			html.setAttribute('data-menu-style', 'dark');
			$('#switchbtn-darkmenu').prop('checked', true);
			$('#switchbtn-darkheader').prop('checked', true);
			
			$('#switchbtn-dark').prop('checked', true);
			localStorage.setItem("solicdarktheme", true);
			localStorage.removeItem("soliclighttheme");
			localStorage.removeItem("solicbgColor");
			localStorage.removeItem("solicheaderbg");
			localStorage.removeItem("solicbgwhite");
			localStorage.removeItem("solicmenubg");

			localStorage.setItem("solicHeader", 'dark');
        	localStorage.setItem("solicMenu", 'dark');
			
			checkOptions();

			if (!document.body.classList.contains('auth-page')) {
				let mainHeader = document.querySelector('.app-header');
				mainHeader.style = "";
				let appSidebar = document.querySelector('.app-sidebar');
				appSidebar.style = "";
			}
			document.querySelector('html').style = '';
			names();
		}
	});
	
})(jQuery);


