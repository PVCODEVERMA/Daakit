$(function() {
	'use strict'
	if (window.matchMedia('(min-width: 992px)').matches) {
		const Chatmain = new PerfectScrollbar('.chat-main', {
			useBothWheelAxes:true,
			suppressScrollX:true,
		});
	}
	$('[data-bs-toggle="tooltip"]').tooltip();
});
