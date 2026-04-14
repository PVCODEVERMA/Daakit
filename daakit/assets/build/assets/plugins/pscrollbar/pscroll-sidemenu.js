(function($) {
	"use strict";
	
	//P-scrolling
	const ps = new PerfectScrollbar('.sidebar-right', {
	  useBothWheelAxes:true,
	  suppressScrollX:true,
	});

	//P-scrolling
	const ps2 = new PerfectScrollbar('.app-sidebar', {
		useBothWheelAxes:true,
		suppressScrollX:true,
	});

	//P-scrolling
	const ps3 = new PerfectScrollbar('.header-dropdown-scroll1', {
		useBothWheelAxes:true,
		suppressScrollX:true,
	});

	//P-scrolling
	const ps4 = new PerfectScrollbar('.header-dropdown-scroll2', {
		useBothWheelAxes:true,
		suppressScrollX:true,
	});
		
})(jQuery);